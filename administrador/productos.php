<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

require_once "../conexion.php";

// Consultar productos y categorías
$sql = "SELECT p.id_producto,p.codigo,p.nombre,p.descripcion,p.precio,p.stock,p.estado,c.nombre AS categoria
        FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id_categoria ORDER BY p.id_producto DESC";

$resultado = mysqli_query($conexion, $sql);

// Consultar categorías para el formulario
$categorias = mysqli_query($conexion,"SELECT id_categoria, nombre FROM categorias WHERE estado = 'activo'");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">

    <title>Administrar Productos</title>
</head>

<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
        <p>Administrador: <?php echo htmlspecialchars($_SESSION["nombre"]); ?></p>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="inicio.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="categorias.php">Categorías</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="ventas.php">Ventas</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="../procesos/cerrar_sesion.php">Cerrar sesión</a>
        </nav>
    </div>

    <main class="contenedor sombra">

        <h2>Administración de productos</h2>

        <?php

        if (isset($_GET["mensaje"])) {

            if ($_GET["mensaje"] == "actualizado") 
            {

                echo "<p>Producto actualizado correctamente</p>";

            } 
            elseif ($_GET["mensaje"] == "duplicado") 
            {

                echo "<p>El codigo del producto ya existe</p>";

            } 
            elseif ($_GET["mensaje"] == "datos") 
            {

                echo "<p>Verifica los datos del producto.</p>";

            } 
            elseif ($_GET["mensaje"] == "no_encontrado") 
            {

                echo "<p>El producto no existe.</p>";

            } 
            elseif ($_GET["mensaje"] == "error") 
            {

                echo "<p>Error al actualizar el producto.</p>";

            }

        }

        ?>

        <h3>Registrar producto</h3>

        <form action="../procesos/guardar_producto.php" method="post">

            <div>

                <label for="codigo">Código</label>
                <input type="text" id="codigo" name="codigo" required>

            </div>

            <div>

                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>

            </div>

            <div>
                <label for="descripcion">Descripción</label>
                <textarea id="descripcion" name="descripcion"></textarea>
            </div>

            <div>

                <label for="precio">Precio</label>
                <input type="number" id="precio" name="precio" min="0.01" step="0.01" required>

            </div>

            <div>

                <label for="stock">Existencias</label>
                <input type="number" id="stock" name="stock" min="0" required>

            </div>

            <div>

                <label for="id_categoria">Categoría</label>

                <select id="id_categoria" name="id_categoria" required>

                    <option value="">Selecciona una categoría</option>

                    <?php while ($categoria = mysqli_fetch_assoc($categorias)) { ?>

                        <option value="<?php echo $categoria["id_categoria"]; ?>">

                            <?php echo htmlspecialchars($categoria["nombre"]); ?>

                        </option>

                    <?php } ?>

                </select>
            </div>

            <div>

                <input class="botonGuardar" type="submit" value="Guardar producto">

            </div>

        </form>

        <hr>

        <h3>Productos registrados</h3>

        <div style="overflow-x:auto;">

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Código</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Stock</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php while ($producto = mysqli_fetch_assoc($resultado)) { ?>

                        <tr>
                            <td><?php echo $producto["id_producto"]; ?></td>

                            <td><?php echo htmlspecialchars($producto["codigo"]); ?></td>

                            <td><?php echo htmlspecialchars($producto["nombre"]); ?></td>

                            <td><?php echo htmlspecialchars($producto["categoria"]); ?></td>

                            <td>$<?php echo number_format($producto["precio"], 2); ?></td>

                            <td><?php echo $producto["stock"]; ?></td>

                            <td><?php echo htmlspecialchars($producto["estado"]); ?></td>

                            <td>
                                <a href="../procesos/editar_producto.php?id=<?php echo $producto["id_producto"]; ?>">
                                    Editar
                                </a>
                            </td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>



    </main>

    <style>

        .botonGuardar{
            background-color: var(--primario);
            color: var(--blanco);
            padding: 1rem 2.5rem;
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            justify-content: center;
            margin: 2rem 38rem 2rem;    
            transition: background-color 0.3s, transform 0.2s;
        }

    </style>


</body>
</html>
