<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

if (!isset($_SESSION["carrito"])) {
    $_SESSION["carrito"] = [];
}

$carrito = $_SESSION["carrito"];
$productos_carrito = [];
$total = 0;

if (!empty($carrito)) {

    $ids = array_map("intval", array_keys($carrito));
    $ids_sql = implode(",", $ids);

    $consulta = mysqli_query($conexion,"SELECT id_producto, codigo, nombre, precio, stock, estado FROM productos WHERE id_producto IN ($ids_sql)");

    if (!$consulta) {
        die("Error al consultar el carrito: " . mysqli_error($conexion));
    }

    while ($producto = mysqli_fetch_assoc($consulta)) {

        $id = (int)$producto["id_producto"];
        $cantidad = (int)$carrito[$id];

        $subtotal = (float)$producto["precio"] * $cantidad;

        $producto["cantidad"] = $cantidad;
        $producto["subtotal"] = $subtotal;

        $productos_carrito[] = $producto;

        $total += $subtotal;
    }
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Carrito</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="contenedor sombra">

    <h1>Mi Carrito de Compras</h1>

    <?php if ($mensaje === "actualizado"): ?>

        <p>El carrito se actualizó correctamente.</p>

    <?php elseif ($mensaje === "eliminado"): ?>

        <p>El producto se eliminó del carrito.</p>

    <?php elseif ($mensaje === "stock"): ?>

        <p>La cantidad solicitada supera las existencias disponibles.</p>

    <?php endif; ?>

    <?php if (empty($productos_carrito)): ?>

        <p>Tu carrito está vacío.</p>

        <a href="productos.php">Ir al catálogo</a>

    <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Producto</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                    <th>Subtotal</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($productos_carrito as $producto): ?>

                    <tr>
                        <td><?php echo htmlspecialchars($producto["codigo"]); ?></td>

                        <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>

                        <td>
                            $<?php echo number_format((float)$producto["precio"], 2); ?>
                        </td>

                        <td>

                            <form  action="../procesos/actualizar_carrito.php" method="POST">

                                <input type="hidden"name="id_producto"value="<?php echo $producto["id_producto"]; ?>">

                                <input type="number"name="cantidad"min="1"max="<?php echo (int)$producto["stock"]; ?>"value="<?php echo $producto["cantidad"]; ?>"required>

                                <button  type="submit">Actualizar</button>

                            </form>

                        </td>

                        <td>
                            $<?php echo number_format($producto["subtotal"], 2); ?>
                        </td>

                        <td>

                            <form class="nada" action="../procesos/eliminar_carrito.php" method="POST">

                                <input type="hidden"name="id_producto"value="<?php echo $producto["id_producto"]; ?>">
                                <button class="botonBonito" type="submit">Eliminar</button>

                            </form>

                        </td>
                    </tr>

                <?php endforeach; ?>

            </tbody>
        </table>

        <h2>
            Total: $<?php echo number_format($total, 2); ?>
        </h2>

        <a class="boton" href="productos.php">Seguir comprando</a>

        <br><br>

        <a class="boton" href="compras.php">Continuar con la compra</a>

    <?php endif; ?>

    <br><br>

    <a class="boton" href="inicio.php">Volver al panel</a>

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
                        padding: 2rem 20rem;
                        font-size: 1.6rem;
                        font-weight: bold;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        display: flex;
                        justify-content: center;
                        margin: 3rem 35rem 0;    
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
                        padding: 1rem 2rem;
                        font-size: 1.6rem;
                        font-weight: bold;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        margin: 1rem -3rem 0;    
                        transition: background-color 0.3s, transform 0.2s;
                    }

                    </style>
</body>
</html>