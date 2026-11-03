
<?php
session_start();

header("Content-Type: application/json; charset=utf-8");

require_once("../conexion.php");
require_once("../paypal_config.php");

function responder($success, $mensaje, $datos = []) {
    echo json_encode(array_merge([
        "success" => $success,
        "mensaje" => $mensaje
    ], $datos));
    exit();
}

function obtenerTokenPayPal() {
    $url = PAYPAL_API . "/v1/oauth2/token";

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_USERPWD => PAYPAL_CLIENT_ID . ":" . PAYPAL_CLIENT_SECRET,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => "grant_type=client_credentials",
        CURLOPT_HTTPHEADER => [
            "Accept: application/json",
            "Accept-Language: es_MX"
        ]
    ]);

    $respuesta = curl_exec($ch);
    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($respuesta === false || $codigo !== 200) {
        return false;
    }

    $datos = json_decode($respuesta, true);

    return $datos["access_token"] ?? false;
}

function llamarPayPal($url, $token, $metodo = "GET", $datos = null, $requestId = null) {
    $encabezados = [
        "Content-Type: application/json",
        "Authorization: Bearer " . $token
    ];

    if ($requestId !== null) {
        $encabezados[] = "PayPal-Request-Id: " . $requestId;
    }

    $ch = curl_init($url);

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_CUSTOMREQUEST => $metodo,
        CURLOPT_HTTPHEADER => $encabezados
    ]);

    if ($datos !== null) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($datos));
    }

    $respuesta = curl_exec($ch);
    $codigo = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    curl_close($ch);

    if ($respuesta === false) {
        return false;
    }

    return [
        "codigo" => $codigo,
        "datos" => json_decode($respuesta, true)
    ];
}


// Verificar sesión
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    responder(false, "Debes iniciar sesión como cliente.");
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    responder(false, "Solicitud no permitida.");
}


// Recibir datos JSON
$datos_recibidos = json_decode(file_get_contents("php://input"), true);

if (!$datos_recibidos) {
    responder(false, "No se recibieron datos válidos.");
}

$accion = $datos_recibidos["accion"] ?? "";
$id_venta = $datos_recibidos["id_venta"] ?? "";

if (!ctype_digit((string)$id_venta) || (int)$id_venta <= 0) {
    responder(false, "Número de venta inválido.");
}

$id_venta = (int)$id_venta;
$id_usuario = (int)$_SESSION["id_usuario"];


// Consultar venta y pago del cliente
$consulta = mysqli_prepare(
    $conexion,
    "SELECT v.id_venta, v.id_usuario, v.total, v.estado,p.monto, p.estado AS estado_pago,p.paypal_order_id, p.paypal_capture_id
     FROM ventas v INNER JOIN pagos p ON v.id_venta = p.id_venta WHERE v.id_venta = ? AND v.id_usuario = ?"
);

mysqli_stmt_bind_param($consulta, "ii", $id_venta, $id_usuario);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$venta = mysqli_fetch_assoc($resultado);

if (!$venta) {
    responder(false, "No se encontró la venta o no tienes permiso para pagarla.");
}


//  CREAR ORDEN DE PAYPAL

if ($accion === "crear_orden") {

    if ($venta["estado"] !== "pendiente" || $venta["estado_pago"] !== "pendiente") {
        responder(false, "Esta venta ya no se encuentra pendiente de pago.");
    }

    if ((float)$venta["total"] <= 0 ||
        abs((float)$venta["total"] - (float)$venta["monto"]) > 0.01) {
        responder(false, "El importe de la venta no coincide con el registro de pago.");
    }

    // Si ya existe una orden, reutilizarla
    if (!empty($venta["paypal_order_id"])) {
        responder(true, "La orden ya existe.", [
            "orderID" => $venta["paypal_order_id"]
        ]);
    }

    $token = obtenerTokenPayPal();

    if (!$token) {
        responder(false, "No fue posible autenticar con PayPal.");
    }

    $importe = number_format((float)$venta["total"], 2, ".", "");

    $orden = [
        "intent" => "CAPTURE",
        "purchase_units" => [
            [
                "custom_id" => (string)$id_venta,
                "description" => "Compra de Inventario de Almacenamiento",
                "amount" => [
                    "currency_code" => "MXN",
                    "value" => $importe
                ]
            ]
        ]
    ];

    $respuesta_paypal = llamarPayPal(
        PAYPAL_API . "/v2/checkout/orders",
        $token,
        "POST",
        $orden
    );

    if (!$respuesta_paypal ||$respuesta_paypal["codigo"] < 200 ||$respuesta_paypal["codigo"] >= 300 ||
        empty($respuesta_paypal["datos"]["id"])) {

        responder(false, "PayPal no pudo crear la orden.");
    }

    $orderID = $respuesta_paypal["datos"]["id"];

    // Guardar el identificador de PayPal
    $actualizar = mysqli_prepare($conexion,"UPDATE pagos SET paypal_order_id = ?
         WHERE id_venta = ? AND estado = 'pendiente' AND paypal_order_id IS NULL");

    mysqli_stmt_bind_param($actualizar, "si", $orderID, $id_venta);
    mysqli_stmt_execute($actualizar);

    // Consultar el identificador guardado por si hubo otra solicitud simultánea
    $consulta_orden = mysqli_prepare(
        $conexion,
        "SELECT paypal_order_id FROM pagos WHERE id_venta = ?"
    );

    mysqli_stmt_bind_param($consulta_orden, "i", $id_venta);
    mysqli_stmt_execute($consulta_orden);

    $resultado_orden = mysqli_stmt_get_result($consulta_orden);
    $pago_actual = mysqli_fetch_assoc($resultado_orden);

    if (!$pago_actual || empty($pago_actual["paypal_order_id"])) {
        responder(false, "No se pudo guardar la orden de PayPal.");
    }

    responder(true, "Orden creada correctamente.", [
        "orderID" => $pago_actual["paypal_order_id"]
    ]);
}


// CAPTURAR EL PAGO

if ($accion === "capturar") {

    $orderID = $datos_recibidos["orderID"] ?? "";

    if ($orderID === "" || !is_string($orderID)) {
        responder(false, "Identificador de orden inválido.");
    }

    if ($venta["estado_pago"] === "completado") {
        responder(true, "Este pago ya había sido confirmado.", [
            "captureID" => $venta["paypal_capture_id"]
        ]);
    }

    if ($venta["estado"] !== "pendiente" || $venta["estado_pago"] !== "pendiente") {
        responder(false, "La venta ya no se encuentra pendiente.");
    }

    if (empty($venta["paypal_order_id"]) ||
        !hash_equals($venta["paypal_order_id"], $orderID)) {
        responder(false, "La orden de PayPal no coincide con esta venta.");
    }

    // Revisar que todavía exista inventario suficiente antes de capturar
    $consulta_stock = mysqli_prepare($conexion,
        "SELECT d.id_producto, d.cantidad, p.stock FROM detalle_venta d
         INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_venta = ? ORDER BY d.id_producto");

    mysqli_stmt_bind_param($consulta_stock, "i", $id_venta);
    mysqli_stmt_execute($consulta_stock);

    $resultado_stock = mysqli_stmt_get_result($consulta_stock);

    while ($producto = mysqli_fetch_assoc($resultado_stock)) {
        if ((int)$producto["stock"] < (int)$producto["cantidad"]) {
            responder(false, "No hay inventario suficiente para completar esta compra.");
        }
    }

    $token = obtenerTokenPayPal();

    if (!$token) {
        responder(false, "No fue posible autenticar con PayPal.");
    }

    // Identificador estable para evitar capturas duplicadas en reintentos
    $requestID = substr(hash("sha256", $orderID), 0, 32);

    $respuesta_paypal = llamarPayPal(
        PAYPAL_API . "/v2/checkout/orders/" . rawurlencode($orderID) . "/capture",
        $token,
        "POST",
        new stdClass(),
        $requestID
    );

    if (!$respuesta_paypal ||
        $respuesta_paypal["codigo"] < 200 ||
        $respuesta_paypal["codigo"] >= 300) {

        responder(false, "PayPal no pudo confirmar el pago. Puedes intentar nuevamente.");
    }

    $datos_paypal = $respuesta_paypal["datos"];

    if (($datos_paypal["status"] ?? "") !== "COMPLETED") {
        responder(false, "PayPal todavía no ha confirmado el pago.");
    }

    $captura = $datos_paypal["purchase_units"][0]["payments"]["captures"][0] ?? null;

    if (!$captura || ($captura["status"] ?? "") !== "COMPLETED") {
        responder(false, "No se encontró una captura de pago completada.");
    }

    $captureID = $captura["id"] ?? "";
    $importe_paypal = $captura["amount"]["value"] ?? "";
    $moneda_paypal = $captura["amount"]["currency_code"] ?? "";

    $importe_esperado = number_format((float)$venta["total"], 2, ".", "");

    if ($captureID === "" ||
        $moneda_paypal !== "MXN" ||
        $importe_paypal !== $importe_esperado) {

        responder(false, "El importe o la moneda confirmados por PayPal no coinciden con la venta.");
    }


    // ACTUALIZAR BASE DE DATOS

    mysqli_begin_transaction($conexion);

    try {

        // Bloquear venta y pago para evitar actualizaciones simultáneas
        $bloqueo = mysqli_prepare($conexion,
            "SELECT v.estado, p.estado AS estado_pago, p.paypal_order_id FROM ventas v INNER JOIN pagos p ON v.id_venta = p.id_venta
             WHERE v.id_venta = ? AND v.id_usuario = ? FOR UPDATE"
        );

        mysqli_stmt_bind_param($bloqueo, "ii", $id_venta, $id_usuario);
        mysqli_stmt_execute($bloqueo);

        $resultado_bloqueo = mysqli_stmt_get_result($bloqueo);
        $estado_actual = mysqli_fetch_assoc($resultado_bloqueo);

        if (!$estado_actual) {
            throw new Exception("No se encontró la venta.");
        }

        // Si otra solicitud ya terminó el proceso, no volver a descontar stock
        if ($estado_actual["estado_pago"] === "completado") {
            mysqli_commit($conexion);

            responder(true, "El pago ya estaba confirmado.", [
                "captureID" => $captureID
            ]);
        }

        if ($estado_actual["estado"] !== "pendiente" ||
            $estado_actual["estado_pago"] !== "pendiente" ||
            $estado_actual["paypal_order_id"] !== $orderID) {

            throw new Exception("El estado de la venta cambió durante el pago.");
        }

        // Bloquear los productos y comprobar nuevamente el inventario
        $productos = mysqli_prepare($conexion,
            "SELECT d.id_producto, d.cantidad, p.stock FROM detalle_venta d INNER JOIN productos p ON d.id_producto = p.id_producto
             WHERE d.id_venta = ? ORDER BY d.id_producto FOR UPDATE");

        mysqli_stmt_bind_param($productos, "i", $id_venta);
        mysqli_stmt_execute($productos);

        $resultado_productos = mysqli_stmt_get_result($productos);

        $lista_productos = [];

        while ($producto = mysqli_fetch_assoc($resultado_productos)) {

            if ((int)$producto["stock"] < (int)$producto["cantidad"]) {
                throw new Exception("El inventario cambió y ya no es suficiente. El pago fue capturado por PayPal; se requiere revisar esta operación.");
            }

            $lista_productos[] = $producto;
        }

        if (count($lista_productos) === 0) {
            throw new Exception("La venta no contiene productos.");
        }

        // Registrar pago completado
        $actualizar_pago = mysqli_prepare($conexion,"UPDATE pagos SET estado = 'completado',paypal_capture_id = ?,fecha_pago = NOW() WHERE id_venta = ? AND estado = 'pendiente'");

        mysqli_stmt_bind_param($actualizar_pago, "si", $captureID, $id_venta);

        if (!mysqli_stmt_execute($actualizar_pago) ||
            mysqli_stmt_affected_rows($actualizar_pago) !== 1) {
            throw new Exception("No se pudo actualizar el pago.");
        }

        // Marcar venta como pagada
        $actualizar_venta = mysqli_prepare($conexion,"UPDATE ventas SET estado = 'pagada' WHERE id_venta = ? AND estado = 'pendiente'");

        mysqli_stmt_bind_param($actualizar_venta, "i", $id_venta);

        if (!mysqli_stmt_execute($actualizar_venta) ||
            mysqli_stmt_affected_rows($actualizar_venta) !== 1) {
            throw new Exception("No se pudo actualizar la venta.");
        }

        // Descontar inventario y registrar movimientos
        foreach ($lista_productos as $producto) {

            $id_producto = (int)$producto["id_producto"];
            $cantidad = (int)$producto["cantidad"];

            $descontar = mysqli_prepare($conexion,"UPDATE productos SET stock = stock - ? WHERE id_producto = ? AND stock >= ?");

            mysqli_stmt_bind_param($descontar,"iii",$cantidad,$id_producto,$cantidad);

            if (!mysqli_stmt_execute($descontar) ||
                mysqli_stmt_affected_rows($descontar) !== 1) {
                throw new Exception("No se pudo descontar el inventario.");
            }

            $descripcion = "Salida de inventario por venta #" . $id_venta;

            $movimiento = mysqli_prepare($conexion,"INSERT INTO movimientos_inventario(id_producto, id_usuario, id_venta, tipo, cantidad, descripcion)VALUES (?, ?, ?, 'salida', ?, ?)");

            mysqli_stmt_bind_param($movimiento,"iiiis",$id_producto,$id_usuario,$id_venta,$cantidad,$descripcion);

            if (!mysqli_stmt_execute($movimiento)) {
                throw new Exception("No se pudo registrar el movimiento de inventario.");
            }
        }

        mysqli_commit($conexion);

        responder(true, "Pago confirmado correctamente.", [
            "captureID" => $captureID
        ]);

    } catch (Exception $e) {

        mysqli_rollback($conexion);

        responder(false, $e->getMessage());
    }
}


// Acción no reconocida
responder(false, "La acción solicitada no es válida.");

?>
