<?php

session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../administrador/categorias.php");
    exit();
}

$id_categoria = $_POST["id_categoria"] ?? "";
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$estado = $_POST["estado"] ?? "";

// Validar datos
if ($id_categoria === "" ||!ctype_digit($id_categoria) ||(int)$id_categoria <= 0 ||!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,100}$/u", $nombre) ||!in_array($estado, ["activo", "inactivo"])) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

// Comprobar que la categoría exista
$consulta_existe = mysqli_prepare($conexion, "SELECT id_categoria FROM categorias WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta_existe, "i", $id_categoria);
mysqli_stmt_execute($consulta_existe);

$resultado = mysqli_stmt_get_result($consulta_existe);
$categoria = mysqli_fetch_assoc($resultado);

mysqli_stmt_close($consulta_existe);

if (!$categoria) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

// Actualizar categoría
$consulta = mysqli_prepare($conexion,"UPDATE categorias SET nombre = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta,"sssi",$nombre,$descripcion,$estado,$id_categoria);

if (mysqli_stmt_execute($consulta)) {
    mysqli_stmt_close($consulta);
    header("Location: ../administrador/categorias.php?mensaje=actualizada");
    exit();
} else {

    if (mysqli_errno($conexion) == 1062) {
        header("Location: ../administrador/categorias.php?mensaje=duplicada");
    } else {
        header("Location: ../administrador/categorias.php?mensaje=error");
    }

    mysqli_stmt_close($consulta);
    exit();
}

?>