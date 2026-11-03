
<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

// Consultar historial de compras
$historial = mysqli_prepare($conexion,"SELECT id_venta, fecha_venta, total, estado FROM ventas WHERE id_usuario = ? ORDER BY fecha_venta DESC");

mysqli_stmt_bind_param($historial, "i", $_SESSION["id_usuario"]);
mysqli_stmt_execute($historial);

$resultado_historial = mysqli_stmt_get_result($historial);

// Consultar carrito
$carrito = $_SESSION["carrito"] ?? [];
$productos = [];
$total = 0;

if (!empty($carrito)) {

    $ids = array_map("intval", array_keys($carrito));
    $ids_sql = implode(",", $ids);

    $consulta = mysqli_query($conexion,"SELECT id_producto, codigo, nombre, precio, stock FROM productos WHERE id_producto IN ($ids_sql) AND estado = 'activo'");

    if (!$consulta) {
        die("Error al consultar productos: " . mysqli_error($conexion));
    }

    while ($producto = mysqli_fetch_assoc($consulta)) {

        $id = (int)$producto["id_producto"];
        $cantidad = (int)$carrito[$id];

        $producto["cantidad"] = $cantidad;
        $producto["subtotal"] = (float)$producto["precio"] * $cantidad;

        $productos[] = $producto;
        $total += $producto["subtotal"];
    }
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Confirmar Compra</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

<main class="contenedor sombra">

    <h1>Confirmar Compra</h1>

    <?php if ($mensaje === "carrito_vacio"): ?>

        <p>No tienes productos en tu carrito.</p>

    <?php elseif ($mensaje === "error"): ?>

        <p>No fue posible generar tu compra. Inténtalo nuevamente.</p>

    <?php elseif ($mensaje === "stock"): ?>

        <p>Uno de los productos ya no tiene suficiente existencia.</p>

    <?php endif; ?>

    <h2>Mis compras anteriores</h2>

    <table>
        <thead>
            <tr>
                <th>Venta</th>
                <th>Fecha</th>
                <th>Total</th>
                <th>Estado</th>
                <th>Recibo</th>
            </tr>
        </thead>

        <tbody>

            <?php if (mysqli_num_rows($resultado_historial) > 0): ?>

                <?php while ($compra = mysqli_fetch_assoc($resultado_historial)): ?>

                    <tr>
                        <td><?php echo $compra["id_venta"]; ?></td>

                        <td>
                            <?php echo htmlspecialchars($compra["fecha_venta"]); ?>
                        </td>

                        <td>
                            $<?php echo number_format((float)$compra["total"], 2); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($compra["estado"]); ?>
                        </td>

                        <td>
                            <a href="recibo.php?id=<?php echo (int)$compra["id_venta"]; ?>">
                                Consultar recibo
                            </a>
                        </td>
                    </tr>

                <?php endwhile; ?>

            <?php else: ?>

                <tr>
                    <td colspan="5">Todavía no tienes compras registradas.</td>
                </tr>

            <?php endif; ?>

        </tbody>
    </table>

    <hr>

    <h2>Resumen de tu compra</h2>

    <?php if (!empty($productos)): ?>

        <table>
            <thead>
                <tr>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Stock disponible</th>
                </tr>
            </thead>

            <section>

                <?php foreach ($productos as $producto): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>

                        <td>
                            $<?php echo number_format((float)$producto["precio"], 2); ?>
                        </td>

                        <td><?php echo $producto["cantidad"]; ?></td>

                        <td>
                            $<?php echo number_format($producto["subtotal"], 2); ?>
                        </td>

                        <td><?php echo $producto["stock"]; ?></td>
                    </tr>

                <?php endforeach; ?>

                </section>
        </table>

        <h2>
            Total de la compra: $<?php echo number_format($total, 2); ?>
        </h2>

        <p>
            Al continuar se generará una venta pendiente de pago.
            El inventario no se descontará hasta confirmar el pago.
        </p>

        <form action="../procesos/crear_compra.php" method="POST">

            <button type="submit" class="botonBonito">Generar compra</button>

        </form>

    <?php else: ?>

        <p>Tu carrito está vacío.</p>

        <a class="botonGuardar" href="productos.php" class="boton">Ver productos</a>

    <?php endif; ?>

    <br><br>

    <a class="botonGuardar" href="carrito.php">Regresar al carrito</a>

    <br><br>

    <a class="botonGuardar" href="inicio.php">Volver al panel</a>

</main>
                <style>
                            .acomodarForm{
                                margin-left: 5rem; 
                                text-decoration: none; 
                                text-align:center; 
                                font-weight: bold;
                            }
                    .botonGuardar{
                        background-color: var(--primario);
                        color: var(--blanco);
                        padding: 1rem 0rem;
                        font-size: 1.6rem;
                        font-weight: bold;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        display: flex;
                        justify-content: center;
                        margin: 1rem 35rem auto;    
                        transition: background-color 0.3s, transform 0.2s;
                    }
                    .nada {
                            background-color: transparent ;
                            padding: 0 ;
                            margin:0 5rem 0;
                            border: none ;
                            box-shadow: none ;
                            display: inline-block ;
                        }
                    .botonBonito{
                        background-color: var(--primario);
                        color: var(--blanco);
                        padding: 1rem 20rem;
                        font-size: 1.7rem;
                        font-weight: bold;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        display:flex;
                        justify-content:center;
                        margin: auto auto auto;    
                        transition: background-color 0.3s, transform 0.2s;
                    }

                    </style>
</body>
</html>
