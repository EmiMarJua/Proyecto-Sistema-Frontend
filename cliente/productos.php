<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$consulta = mysqli_query(
    $conexion,
    "SELECT p.id_producto, p.codigo, p.nombre, p.descripcion, p.precio, p.stock, p.imagen, c.nombre AS categoria FROM productos p
     INNER JOIN categorias c ON p.id_categoria = c.id_categoria  WHERE p.estado = 'activo' AND c.estado = 'activo' ORDER BY p.nombre ASC"
);

if (!$consulta) {
    die("Error al consultar productos: " . mysqli_error($conexion));
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos | Inventario de Almacenamiento</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

<main class="contenedor sombra">

    <h1>Catálogo de Productos</h1>

    <?php if ($mensaje === "agregado"): ?>

        <p>Producto agregado al carrito correctamente.</p>

    <?php elseif ($mensaje === "stock"): ?>

        <p>No hay suficientes existencias para esa cantidad.</p>

    <?php elseif ($mensaje === "error"): ?>

        <p>No fue posible agregar el producto.</p>

    <?php endif; ?>

    <p>Consulta nuestros productos disponibles y agrégalos a tu carrito.</p>

    <div class="servicio" data-stock="<?php echo (int)$producto["stock"]; ?>">

        <?php while ($producto = mysqli_fetch_assoc($consulta)): ?>

            <div class="servicio">

                <?php if (!empty($producto["imagen"])): ?>
                    <img
                        src="../img/ <?php echo htmlspecialchars($producto["imagen"]); ?>"
                        alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
                        style="max-width:100%; height:160px; object-fit:contain;"
                    >
                <?php endif; ?>

                <h2><?php echo htmlspecialchars($producto["nombre"]); ?></h2>

                <p>
                    <strong>Código:</strong>
                    <?php echo htmlspecialchars($producto["codigo"]); ?>
                </p>

                <p>
                    <strong>Categoría:</strong>
                    <?php echo htmlspecialchars($producto["categoria"]); ?>
                </p>

                <p>
                    <?php echo htmlspecialchars($producto["descripcion"] ?? ""); ?>
                </p>

                <p>
                    <strong>Precio:</strong>
                    $<?php echo number_format((float)$producto["precio"], 2); ?>
                </p>

                <p>
                    <strong>Existencias:</strong>
                    <?php echo (int)$producto["stock"]; ?>
                </p>

                <?php if ((int)$producto["stock"] > 0): ?>

                    <form action="../procesos/agregar_carrito.php" method="POST" onsubmit="return validarCantidad(this)">

                        <input type="hidden"name="id_producto"value="<?php echo $producto["id_producto"]; ?>">

                        <label for="cantidad_<?php echo $producto["id_producto"]; ?>">
                            Cantidad:
                        </label>

                        <input type="number" id="cantidad_<?php echo $producto["id_producto"]; ?>"
                              name="cantidad" min="1" max="<?php echo (int)$producto["stock"]; ?>" value="1" required>

                        <button type="submit" class="boton">
                            Agregar al carrito
                        </button>

                    </form>

                <?php else: ?>

                    <p>Producto agotado.</p>

                <?php endif; ?>

            </div>

        <?php endwhile; ?>

    </div>

    <br>

    <a class="boton" href="carrito.php">Ver carrito</a>
    <br>
    <a class="boton" href="inicio.php">Volver al panel</a>

</main>

</body>

    <script>

    function validarCantidad(formulario) {

        var cantidad = formulario.cantidad.value;

        // Validar que no este vacia

        if (cantidad.trim() == "") {

            alert("Escribe una cantidad");
            return false;

        }

        // Validar que solamente sean numeros

        var expresionCantidad = /^[0-9]+$/;

        if (!expresionCantidad.test(cantidad)) {

            alert("La cantidad debe contener solamente numeros");
            return false;

        }

        // Validar que sea mayor a cero

        if (parseInt(cantidad) < 1) {

            alert("La cantidad debe ser mayor a cero");
            return false;

        }

        // Validar que no supere el stock

        if (parseInt(cantidad) > parseInt(formulario.cantidad.max)) {

            alert("No hay suficientes existencias");
            return false;

        }

        return true;

        // Obtener todas las tarjetas de productos
        var tarjetas = document.getElementsByClassName("servicio");

        // Recorrer las tarjetas
        for (var i = 0; i < tarjetas.length; i++) {

            var stock = parseInt(tarjetas[i].getAttribute("data-stock"));

            // Destacar productos con poco stock
            if (stock <= 5) {

                tarjetas[i].style.border = "2px solid orange";
                tarjetas[i].style.backgroundColor = "#fff3e0";

            }

        }

    }

</script>

</html>