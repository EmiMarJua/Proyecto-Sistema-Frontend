<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_categoria = $_GET["id"] ?? "";

if (!ctype_digit($id_categoria) || $id_categoria === "") {
    header("Location: categorias.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"SELECT * FROM categorias WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta, "i", $id_categoria);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$categoria = mysqli_fetch_assoc($resultado);

if (!$categoria) {
    header("Location: categorias.php?mensaje=error");
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

        <form action="../procesos/actualizar_categoria.php" method="POST">

            <input type="hidden"name="id_categoria"value="<?php echo $categoria["id_categoria"]; ?>">

            <label for="nombre">Nombre:</label>
            <input type="text"id="nombre"name="nombre"maxlength="100"value="<?php echo htmlspecialchars($categoria["nombre"]); ?>" required>

            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion"name="descripcion"rows="4"><?php echo htmlspecialchars($categoria["descripcion"] ?? ""); ?></textarea>

            <label for="estado">Estado:</label>
            <select id="estado" name="estado" required>

                <option
                    value="activo"
                    <?php echo $categoria["estado"] === "activo" ? "selected" : ""; ?>>
                    Activo
                </option>

                <option
                    value="inactivo"
                    <?php echo $categoria["estado"] === "inactivo" ? "selected" : ""; ?>>
                    Inactivo
                </option>
            </select>

            <button type="submit" class="boton">Actualizar categoría</button>

        </form>

        <br>

        <a class="boton" href="../administrador/categorias.php">Regresar a categorías</a>

    </main>

</body>
</html>