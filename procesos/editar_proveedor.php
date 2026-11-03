<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_proveedor = $_GET["id"] ?? "";

if ($id_proveedor === "" || !ctype_digit($id_proveedor)) {
    header("Location: proveedores.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"SELECT * FROM proveedores WHERE id_proveedor = ?");

mysqli_stmt_bind_param($consulta, "i", $id_proveedor);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$proveedor = mysqli_fetch_assoc($resultado);

if (!$proveedor) {
    header("Location: proveedores.php?mensaje=error");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Proveedor</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Editar Proveedor</h1>

        <form action="../procesos/actualizar_proveedor.php" method="POST">

            <input type="hidden"name="id_proveedor"value="<?php echo $proveedor["id_proveedor"]; ?>">

            <label for="nombre">Nombre:</label>
            <input type="text"id="nombre"name="nombre"maxlength="100"value="<?php echo htmlspecialchars($proveedor["nombre"]); ?>" required >

            <label for="telefono">Teléfono:</label>
            <input type="text"id="telefono"name="telefono"maxlength="20"value="<?php echo htmlspecialchars($proveedor["telefono"] ?? ""); ?>">

            <label for="correo">Correo electrónico:</label>
            <input type="email"id="correo"name="correo"maxlength="150"value="<?php echo htmlspecialchars($proveedor["correo"] ?? ""); ?>">

            <label for="direccion">Dirección:</label>
            <textarea id="direccion"name="direccion"rows="3"><?php echo htmlspecialchars($proveedor["direccion"] ?? ""); ?></textarea>

            <label for="estado">Estado:</label>
            
            <select id="estado" name="estado" required>

                <option value="activo"<?php echo $proveedor["estado"] === "activo" ? "selected" : ""; ?>>
                    Activo
                </option>

                <option value="inactivo" <?php echo $proveedor["estado"] === "inactivo" ? "selected" : ""; ?> >
                    Inactivo
                </option>
            </select>

            <button type="submit" class="boton">
                Actualizar proveedor
            </button>

        </form>

        <br>

        <a class="boton" href="proveedores.php">Regresar a proveedores</a>

    </main>

</body>
</html>