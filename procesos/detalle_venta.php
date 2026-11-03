<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_venta = $_GET["id"] ?? "";

if ($id_venta === "" || !ctype_digit($id_venta)) {
    header("Location: ventas.php");
    exit();
}

// Consultar información general de la venta
$consulta_venta = mysqli_prepare(
    $conexion,
    "SELECT v.id_venta, v.fecha_venta, v.total, v.estado,
            u.nombre AS cliente, u.correo
     FROM ventas v
     INNER JOIN usuarios u ON v.id_usuario = u.id_usuario
     WHERE v.id_venta = ?"
);

mysqli_stmt_bind_param($consulta_venta, "i", $id_venta);
mysqli_stmt_execute($consulta_venta);

$resultado_venta = mysqli_stmt_get_result($consulta_venta);
$venta = mysqli_fetch_assoc($resultado_venta);

if (!$venta) {
    header("Location: ventas.php");
    exit();
}

// Consultar los productos de la venta
$consulta_detalles = mysqli_prepare($conexion,"SELECT d.cantidad, d.precio_unitario, d.subtotal,p.codigo, p.nombre
     FROM detalle_venta d INNER JOIN productos p ON d.id_producto = p.id_producto WHERE d.id_venta = ?");

mysqli_stmt_bind_param($consulta_detalles, "i", $id_venta);
mysqli_stmt_execute($consulta_detalles);

$detalles = mysqli_stmt_get_result($consulta_detalles);

// Consultar el pago, si existe
$consulta_pago = mysqli_prepare($conexion,"SELECT metodo_pago, paypal_order_id, paypal_capture_id,monto, estado, fecha_pago FROM pagos WHERE id_venta = ?");

mysqli_stmt_bind_param($consulta_pago, "i", $id_venta);
mysqli_stmt_execute($consulta_pago);

$resultado_pago = mysqli_stmt_get_result($consulta_pago);
$pago = mysqli_fetch_assoc($resultado_pago);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle de Venta</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Detalle de Venta #<?php echo $venta["id_venta"]; ?></h1>

        <h2>Información del cliente</h2>

        <p>
            <strong>Nombre:</strong>
            <?php echo htmlspecialchars($venta["cliente"]); ?>
        </p>

        <p>
            <strong>Correo:</strong>
            <?php echo htmlspecialchars($venta["correo"]); ?>
        </p>

        <p>
            <strong>Fecha:</strong>
            <?php echo htmlspecialchars($venta["fecha_venta"]); ?>
        </p>

        <p>
            <strong>Estado:</strong>
            <?php echo htmlspecialchars($venta["estado"]); ?>
        </p>

        <hr>

        <h2>Productos de la venta</h2>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Cantidad</th>
                    <th>Precio unitario</th>
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

        <?php if ($pago): ?>

            <p>
                <strong>Método:</strong>
                <?php echo htmlspecialchars($pago["metodo_pago"]); ?>
            </p>

            <p>
                <strong>Estado del pago:</strong>
                <?php echo htmlspecialchars($pago["estado"]); ?>
            </p>

            <p>
                <strong>Monto:</strong>
                $<?php echo number_format((float)$pago["monto"], 2); ?>
            </p>

            <p>
                <strong>Orden PayPal:</strong>
                <?php echo htmlspecialchars($pago["paypal_order_id"] ?? "Pendiente"); ?>
            </p>

            <p>
                <strong>Captura PayPal:</strong>
                <?php echo htmlspecialchars($pago["paypal_capture_id"] ?? "Pendiente"); ?>
            </p>

            <p>
                <strong>Fecha del pago:</strong>
                <?php echo htmlspecialchars($pago["fecha_pago"] ?? "Pendiente"); ?>
            </p>

        <?php else: ?>

            <p>Esta venta todavía no tiene un registro de pago.</p>

        <?php endif; ?>

        <br>

        <a class="boton" href="ventas.php">Regresar a ventas</a>

    </main>

</body>
</html>