<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

require_once "../conexion.php";

if (!isset($_GET["id"]) || !ctype_digit($_GET["id"]) || (int)$_GET["id"] <= 0) {
    header("Location: productos.php");
    exit();
}

$id_producto = (int)$_GET["id"];

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

mysqli_stmt_close($consulta);

// Consultar categorías
$categorias = mysqli_query(   $conexion,"SELECT id_categoria, nombre FROM categorias WHERE estado = 'activo'");

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

        <div id="mensajes"></div>

        <form action="../procesos/actualizar_productos.php" method="post" onsubmit="return validarProducto()" novalidate>

            <input type="hidden" name="id_producto" value="<?php echo $producto["id_producto"]; ?>">

            <div>

                <label for="codigo">Código</label>
                <input type="text"id="codigo"name="codigo"value="<?php echo htmlspecialchars($producto["codigo"]); ?>"required>

            </div>

            <div>

                <label for="nombre">Nombre</label>
                <input type="text"id="nombre"name="nombre" value="<?php echo htmlspecialchars($producto["nombre"]); ?>"required>

            </div>

            <div>

                <label for="descripcion">Descripción</label>

                <textarea id="descripcion" name="descripcion"><?php echo htmlspecialchars($producto["descripcion"]); ?></textarea>

            </div>

            <div>

                <label for="precio">Precio</label>
                <input type="number"id="precio"name="precio"min="0.01"step="0.01"value="<?php echo $producto["precio"]; ?>"required>

            </div>

            <div>

                <label for="stock">Existencias</label>
                <input type="number"id="stock"name="stock"min="0"value="<?php echo $producto["stock"]; ?>"required>

            </div>

            <div>

                <label for="id_categoria">Categoría</label>

                <select id="id_categoria" name="id_categoria" required>

                    <option value="">Selecciona una categoría</option>

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

                    <option value="activo" <?php if ($producto["estado"] == "activo") echo "selected"; ?>>
                        Activo
                    </option>

                    <option value="inactivo" <?php if ($producto["estado"] == "inactivo") echo "selected"; ?>>
                        Inactivo
                    </option>

                </select>

            </div>

            <div>
                <input class="botonCentrado" type="submit" value="Actualizar producto">
            </div>

        </form>

        <p>
            <a class="boton" href="productos.php">Regresar a productos</a>
        </p>

    </main>

    <script>

    // Mostrar mensajes de validación utilizando el DOM

    function mostrarError(mensaje) {

        var contenedor = document.getElementById("mensajes");

        contenedor.innerHTML = "";

        var parrafo = document.createElement("p");

        parrafo.textContent = mensaje;

        parrafo.classList.add("error");

        contenedor.appendChild(parrafo);

    }


    // Validar los datos del producto

    function validarProducto() {

        var codigo = document.getElementById("codigo").value.trim();
        var nombre = document.getElementById("nombre").value.trim();
        var precio = document.getElementById("precio").value;
        var stock = document.getElementById("stock").value;
        var categoria = document.getElementById("id_categoria").value;
        var estado = document.getElementById("estado").value;

        // Validar campos vacíos

        if (codigo == "" || nombre == "" || precio == "" || stock == "" || categoria == "" || estado == "") {

            mostrarError("Completa todos los campos obligatorios.");

            return false;

        }

        // Validar código

        var expresionCodigo = /^[a-zA-Z0-9-]{3,20}$/;

        if (!expresionCodigo.test(codigo)) {

            mostrarError("El código debe tener entre 3 y 20 caracteres y utilizar solamente letras, números o guiones.");

            return false;

        }

        // Validar nombre

        var expresionNombre = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,60}$/;

        if (!expresionNombre.test(nombre)) {

            mostrarError("El nombre debe tener entre 3 y 60 caracteres y utilizar solamente letras, números y signos permitidos.");

            return false;

        }

        // Validar precio

        var expresionPrecio = /^[0-9]+(\.[0-9]{1,2})?$/;

        if (!expresionPrecio.test(precio) || parseFloat(precio) <= 0) {

            mostrarError("El precio debe ser mayor que cero y tener como máximo dos decimales.");

            return false;

        }

        // Validar existencias

        var expresionStock = /^[0-9]+$/;

        if (!expresionStock.test(stock)) {

            mostrarError("Las existencias deben ser un número entero igual o mayor que cero.");

            return false;

        }

        // Validar categoría

        if (categoria == "") {

            mostrarError("Selecciona una categoría.");

            return false;

        }

        // Validar estado

        if (estado != "activo" && estado != "inactivo") {

            mostrarError("Selecciona un estado válido.");

            return false;

        }

        return true;

    }

    </script>

</body>
</html>