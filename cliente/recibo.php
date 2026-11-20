<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");
require_once("../paypal_config.php");

$id_venta = $_GET["id"] ?? "";

if ($id_venta === "" || !ctype_digit($id_venta)) {
    header("Location: compras.php");
    exit();
}

// Consultar venta del cliente que inició sesión
$consulta = mysqli_prepare($conexion,"SELECT v.id_venta, v.fecha_venta, v.total, v.estado,u.nombre, u.correo FROM ventas v
     INNER JOIN usuarios u ON v.id_usuario = u.id_usuario WHERE v.id_venta = ? AND v.id_usuario = ?");

mysqli_stmt_bind_param($consulta,"ii",$id_venta,$_SESSION["id_usuario"]);

mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$venta = mysqli_fetch_assoc($resultado);

if (!$venta) {
    header("Location: compras.php");
    exit();
}

// Consultar detalles
$consulta_detalles = mysqli_prepare($conexion,"SELECT d.cantidad, d.precio_unitario, d.subtotal,p.codigo, p.nombre FROM detalle_venta d 
    INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_venta = ?");

mysqli_stmt_bind_param($consulta_detalles, "i", $id_venta);
mysqli_stmt_execute($consulta_detalles);

$detalles = mysqli_stmt_get_result($consulta_detalles);

// Consultar pago
$consulta_pago = mysqli_prepare($conexion,"SELECT metodo_pago, monto, estado,paypal_order_id, paypal_capture_id, fecha_pago FROM pagos WHERE id_venta = ?");

mysqli_stmt_bind_param($consulta_pago, "i", $id_venta);
mysqli_stmt_execute($consulta_pago);

$resultado_pago = mysqli_stmt_get_result($consulta_pago);
$pago = mysqli_fetch_assoc($resultado_pago);

$pagado = $pago && $pago["estado"] === "completado";
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo de Compra</title>

    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .recibo {
            max-width: 800px;
            margin: 30px auto;
            padding: 30px;
            background: white;
        }

        .recibo table {
            width: 100%;
            border-collapse: collapse;
        }

        .recibo th,
        .recibo td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }

        @media print {
            .no-imprimir {
                display: none;
            }

            body {
                background: white;
            }

            .recibo {
                box-shadow: none;
            }
        }
    </style>
</head>
<body>

<main class="recibo">

    <h1>Inventario de Almacenamiento</h1>

    <h2>Comprobante de Compra</h2>

    <p>
        <strong>Número de venta:</strong>
        <?php echo $venta["id_venta"]; ?>
    </p>

    <p>
        <strong>Fecha:</strong>
        <?php echo htmlspecialchars($venta["fecha_venta"]); ?>
    </p>

    <hr>

    <h2>Datos del cliente</h2>

    <p>
        <strong>Nombre:</strong>
        <?php echo htmlspecialchars($venta["nombre"]); ?>
    </p>

    <p>
        <strong>Correo:</strong>
        <?php echo htmlspecialchars($venta["correo"]); ?>
    </p>

    <hr>

    <h2>Productos adquiridos</h2>

    <table>
        <thead>
            <tr>
                <th>Código</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio</th>
                <th>Subtotal</th>
            </tr>
        </thead>

        <tbody>

            <?php while ($detalle = mysqli_fetch_assoc($detalles)): ?>

                <tr>
                    <td><?php echo htmlspecialchars($detalle["codigo"]); ?></td>

                    <td><?php echo htmlspecialchars($detalle["nombre"]); ?></td>

                    <td><?php echo $detalle["cantidad"]; ?></td>

                    <td>
                        $<?php echo number_format((float)$detalle["precio_unitario"], 2); ?>
                    </td>

                    <td>
                        $<?php echo number_format((float)$detalle["subtotal"], 2); ?>
                    </td>
                </tr>

            <?php endwhile; ?>

        </tbody>
    </table>

    <h2>
        Total: $<?php echo number_format((float)$venta["total"], 2); ?>
    </h2>

    <hr>

    <h2>Información del pago</h2>

    <p>
        <strong>Estado de la venta:</strong>
        <?php echo htmlspecialchars($venta["estado"]); ?>
    </p>

    <?php if ($pago): ?>

        <p>
            <strong>Método de pago:</strong>
            <?php echo htmlspecialchars($pago["metodo_pago"]); ?>
        </p>

        <p>
            <strong>Estado del pago:</strong>
            <?php echo htmlspecialchars($pago["estado"]); ?>
        </p>

        <?php if ($pagado): ?>

            <p>
                <strong>Identificador de pago:</strong>
                <?php echo htmlspecialchars($pago["paypal_capture_id"] ?? ""); ?>
            </p>

            <p>
                <strong>Fecha de pago:</strong>
                <?php echo htmlspecialchars($pago["fecha_pago"] ?? ""); ?>
            </p>

            <p>Pago confirmado. Este comprobante corresponde a una compra pagada.</p>

        <?php else: ?>

            <p>Este documento es provisional. El pago todavía no está confirmado.</p>

        <?php endif; ?>

    <?php else: ?>

        <p>No existe un registro de pago para esta venta.</p>

    <?php endif; ?>

    <div class="no-imprimir">

        <br>

        <?php if ($pagado): ?>

            <button onclick="window.print()" class="boton">
                Imprimir o guardar como PDF
            </button>

       <?php else: ?>

             <p>El recibo definitivo estará disponible después de confirmar el pago.</p>

            <?php if ($pago &&
                $pago["estado"] === "pendiente" &&
                $venta["estado"] === "pendiente"): ?>

                <div id="paypal-button-container"></div>

                <p id="paypal-mensaje"></p>

            <?php endif; ?>

        <?php endif; ?>

        <br><br>

        <a class="boton" href="compras.php">Volver a mis compras</a>

        <br>

        <a class="boton" href="inicio.php">Volver al panel</a>

    </div>

</main>


<?php if (
    $pago &&
    $pago["estado"] === "pendiente" &&
    $venta["estado"] === "pendiente"
): ?>

<script src="https://www.paypal.com/sdk/js?client-id=<?php echo urlencode(PAYPAL_CLIENT_ID); ?>&currency=MXN&intent=capture"></script>

<script>
// Guarda el ID de la venta para enviarlo al proceso de pago.
const idVenta = <?php echo json_encode((int)$id_venta); ?>;

// Configura los botones de PayPal.
paypal.Buttons({

    // Esta función solicita crear una orden de pago.
    createOrder: function() {

        // Envía los datos al archivo PHP que procesa los pagos.
        return fetch("../procesos/procesar_pago.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                accion: "crear_orden",
                id_venta: idVenta
            })
        })

        // Convierte la respuesta del servidor a formato JSON.
        .then(response => response.json())

        // Comprueba si la orden se creó correctamente.
        .then(datos => {

            if (!datos.success) {
                throw new Error(datos.mensaje);
            }

            // Devuelve el identificador de la orden a PayPal.
            return datos.orderID;
        });
    },

    // Esta función se ejecuta cuando el cliente aprueba el pago.
    onApprove: function(data) {

        // Muestra un mensaje mientras se confirma el pago.
        document.getElementById("paypal-mensaje").textContent =
            "Confirmando el pago...";

        // Envía la solicitud para capturar el pago aprobado.
        return fetch("../procesos/procesar_pago.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                accion: "capturar",
                id_venta: idVenta,
                orderID: data.orderID
            })
        })

        // Convierte la respuesta del servidor a JSON.
        .then(response => response.json())

        // Comprueba el resultado de la operación.
        .then(datos => {

            if (datos.success) {

                // Informa que el pago fue confirmado.
                document.getElementById("paypal-mensaje").textContent =
                    "¡Pago confirmado! Actualizando comprobante...";

                // Recarga la página para mostrar el estado actualizado.
                window.location.reload();

            } else {

                // Muestra el error que devolvió el servidor.
                document.getElementById("paypal-mensaje").textContent =
                    datos.mensaje;
            }
        });
    },

    // Esta función se ejecuta si ocurre un error con PayPal.
    onError: function(error) {

        // Muestra el error en la consola del navegador.
        console.error(error);

        // Informa al cliente que ocurrió un problema.
        document.getElementById("paypal-mensaje").textContent =
            "Ocurrió un error al procesar el pago. Intenta nuevamente.";
    }

}).render("#paypal-button-container");
</script>

<?php endif; ?>
</body>
</html>