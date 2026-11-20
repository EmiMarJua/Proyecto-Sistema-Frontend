<?php

session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_categoria = $_GET["id"] ?? "";

if ($id_categoria === "" || !ctype_digit($id_categoria) || (int)$id_categoria <= 0) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"SELECT * FROM categorias WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta, "i", $id_categoria);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$categoria = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($consulta);

if (!$categoria) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar Categoría</title>

    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>

    <main class="contenedor sombra">

        <h1>Editar Categoría</h1>

        <div id="mensajes"></div>

        <form action="actualizar_categoria.php" method="POST" onsubmit="return validarCategoria()" novalidate>

            <input type="hidden"name="id_categoria"value="<?php echo $categoria["id_categoria"]; ?>">
           
            <label for="nombre">Nombre:</label>

            <input type="text"id="nombre"name="nombre"maxlength="100"value="<?php echo htmlspecialchars($categoria["nombre"]); ?>"required>

            <label for="descripcion">Descripción:</label>

            <textarea id="descripcion"name="descripcion"rows="4"><?php echo htmlspecialchars($categoria["descripcion"] ?? ""); ?></textarea>

            <label for="estado">Estado:</label>

            <select id="estado" name="estado" required>

                <option
                    value="activo"
                    <?php echo $categoria["estado"] === "activo" ? "selected" : ""; ?>
                >
                    Activo
                </option>

                <option
                    value="inactivo"
                    <?php echo $categoria["estado"] === "inactivo" ? "selected" : ""; ?>
                >
                    Inactivo
                </option>

            </select>

            <button type="submit" class="boton">Actualizar categoría</button>

        </form>

        <br>

        <a class="boton" href="../administrador/categorias.php">Regresar a categorías</a>

    </main>

    <script>

    function mostrarError(mensaje) {

        var contenedor = document.getElementById("mensajes");

        contenedor.innerHTML = "";

        var parrafo = document.createElement("p");

        parrafo.textContent = mensaje;

        parrafo.classList.add("error");

        contenedor.appendChild(parrafo);

    }

    function validarCategoria() {

        var nombre = document.getElementById("nombre").value.trim();
        var estado = document.getElementById("estado").value;

        if (nombre == "") {

            mostrarError("Escribe el nombre de la categoría.");

            return false;

        }

        var expresionNombre = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,100}$/;

        if (!expresionNombre.test(nombre)) {

            mostrarError("El nombre debe tener entre 3 y 100 caracteres y utilizar letras, números o signos permitidos.");

            return false;

        }

        if (estado != "activo" && estado != "inactivo") {

            mostrarError("Selecciona un estado válido.");

            return false;

        }

        return true;

    }

    </script>

</body>
</html>