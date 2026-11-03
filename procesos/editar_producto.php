
<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

require_once "../conexion.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"])) {
    header("Location: productos.php");
    exit();
}

$id_producto = $_GET["id"];

// Consultar producto
$sql = "SELECT * FROM productos WHERE id_producto = ?";

$consulta = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($consulta, "i", $id_producto);

mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) === 0) {
    header("Location: productos.php?mensaje=no_encontrado");
    exit();
}

$producto = mysqli_fetch_assoc($resultado);

// Consultar categorías
$categorias = mysqli_query($conexion,"SELECT id_categoria, nombre FROM categorias WHERE estado = 'activo'");

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">

    <title>Editar producto</title>
</head>

<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="../administrador/inicio.php">Inicio</a>
            <a href="../administrador/productos.php">Productos</a>
            <a href="../cerrar_sesion.php">Cerrar sesión</a>
        </nav>
    </div>

    <main class="contenedor sombra">

        <h2>Editar producto</h2>

        <form action="../procesos/actualizar_productos.php" method="post">

            <input type="hidden"name="id_producto"value="<?php echo $producto["id_producto"]; ?>">

            <div>

                <label for="codigo">Código</label>
                <input type="text"id="codigo"name="codigo"value="<?php echo htmlspecialchars($producto["codigo"]); ?>"required>
            
            </div>

            <div>

                <label for="nombre">Nombre</label>
                <input type="text"id="nombre"name="nombre"value="<?php echo htmlspecialchars($producto["nombre"]); ?>"required>
            
            </div>

            <div>

                <label for="descripcion">Descripción</label>

                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto["descripcion"]);?></textarea>

            </div>

            <div>

                <label for="precio">Precio</label>
                <input type="number"id="precio"name="precio"min="0.01"step="0.01"value="<?php echo $producto["precio"]; ?>" required>
           
            </div>

            <div>

                <label for="stock">Existencias</label>
                <input type="number" id="stock"name="stock"min="0" value="<?php echo $producto["stock"]; ?>"required>
            
            </div>

            <div>
                <label for="id_categoria">Categoría</label>

                <select id="id_categoria" name="id_categoria" required>

                    <?php while ($categoria = mysqli_fetch_assoc($categorias)) { ?>

                        <option
                            value="<?php echo $categoria["id_categoria"]; ?>"

                            <?php
                            if ($categoria["id_categoria"] == $producto["id_categoria"]) {
                                echo "selected";
                            }

                            ?>
                            >
                            <?php echo htmlspecialchars($categoria["nombre"]); ?>

                        </option>

                    <?php } ?>

                </select>
            </div>

            <div>
                <label for="estado">Estado</label>

                <select id="estado" name="estado" required>

                    <option value="activo"

                        <?php if ($producto["estado"] == "activo") echo "selected"; ?>>

                        Activo

                    </option>

                    <option value="inactivo"

                        <?php if ($producto["estado"] == "inactivo") echo "selected"; ?>>
                        Inactivo

                    </option>

                </select>
            </div>

            <div>
                <input class="botonCentrado" type="submit" value="Actualizar producto" href="../administrador/producto.php">
            </div>

        </form>

        <p>
            <a class="boton" href="productos.php">Regresar a productos</a>
        </p>

    </main>

</body>
</html>
