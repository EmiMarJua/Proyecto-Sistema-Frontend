<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$carrito = $_SESSION["carrito"] ?? [];

if (empty($carrito)) {
    header("Location: ../cliente/carrito.php");
    exit();
}

try {

    mysqli_begin_transaction($conexion);

    $id_usuario = (int)$_SESSION["id_usuario"];
    $total = 0;
    $detalles = [];

    // Volver a consultar y bloquear cada producto
    foreach ($carrito as $id => $cantidad) {

        $id_producto = (int)$id;
        $cantidad = (int)$cantidad;

        if ($id_producto <= 0 || $cantidad <= 0) {
            throw new Exception("Datos del carrito incorrectos.");
        }

        $consulta = mysqli_prepare($conexion,"SELECT nombre, precio, stock FROM productos WHERE id_producto = ?
               AND estado = 'activo' FOR UPDATE");

        mysqli_stmt_bind_param($consulta, "i", $id_producto);
        mysqli_stmt_execute($consulta);

        $resultado = mysqli_stmt_get_result($consulta);
        $producto = mysqli_fetch_assoc($resultado);

        if (!$producto || $cantidad > (int)$producto["stock"]) {
            throw new Exception("Existencias insuficientes.");
        }

        $precio = (float)$producto["precio"];
        $subtotal = $precio * $cantidad;

        $total += $subtotal;

        $detalles[] = [
            "id_producto" => $id_producto,
            "cantidad" => $cantidad,
            "precio" => $precio,
            "subtotal" => $subtotal
        ];
    }

    // Crear la venta pendiente
    $estado_venta = "pendiente";

    $insertar_venta = mysqli_prepare($conexion,"INSERT INTO ventas (id_usuario, total, estado)VALUES (?, ?, ?)");

    mysqli_stmt_bind_param($insertar_venta,"ids",$id_usuario,$total,$estado_venta);

    if (!mysqli_stmt_execute($insertar_venta)) {
        throw new Exception("No se pudo crear la venta.");
    }

    $id_venta = mysqli_insert_id($conexion);

    // Guardar los productos de la venta
    $insertar_detalle = mysqli_prepare($conexion,"INSERT INTO detalle_venta(id_venta, id_producto, cantidad, precio_unitario, subtotal)VALUES (?, ?, ?, ?, ?)");

    foreach ($detalles as $detalle) {

        mysqli_stmt_bind_param($insertar_detalle,"iiidd",$id_venta,$detalle["id_producto"],$detalle["cantidad"],$detalle["precio"],$detalle["subtotal"]);

        if (!mysqli_stmt_execute($insertar_detalle)) {
            throw new Exception("No se pudo guardar un detalle de la venta.");
        }
    }

    // Crear registro de pago pendiente
    $metodo_pago = "PayPal";
    $estado_pago = "pendiente";

    $insertar_pago = mysqli_prepare($conexion,"INSERT INTO pagos(id_venta, metodo_pago, monto, estado)VALUES (?, ?, ?, ?)");

    mysqli_stmt_bind_param($insertar_pago,"isds",$id_venta,$metodo_pago,$total,$estado_pago);

    if (!mysqli_stmt_execute($insertar_pago)) {
        throw new Exception("No se pudo crear el registro del pago.");
    }

    mysqli_commit($conexion);

    // Guardar la venta para consultarla después
    $_SESSION["ultima_venta"] = $id_venta;

    // Vaciar el carrito después de crear la venta
    unset($_SESSION["carrito"]);

    header("Location: ../cliente/recibo.php?id=" . $id_venta . "&pendiente=1");
    exit();

} catch (Throwable $error) {

    mysqli_rollback($conexion);

    error_log($error->getMessage());

    if (strpos($error->getMessage(), "Existencias insuficientes") !== false) {
        header("Location: ../cliente/compras.php?mensaje=stock");
    } else {
        header("Location: ../cliente/compras.php?mensaje=error");
    }

    exit();
}
?>