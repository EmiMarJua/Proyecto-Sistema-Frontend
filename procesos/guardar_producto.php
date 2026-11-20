<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

require_once "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../administrador/productos.php");
    exit();
}

$codigo = trim($_POST["codigo"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$precio = $_POST["precio"] ?? "";
$stock = $_POST["stock"] ?? "";
$id_categoria = $_POST["id_categoria"] ?? "";

// Validar campos obligatorios
if (
    $codigo === "" ||
    $nombre === "" ||
    $precio === "" ||
    $stock === "" ||
    $id_categoria === ""
) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Validar código
if (!preg_match("/^[a-zA-Z0-9-]{3,20}$/", $codigo)) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Validar nombre
if (!preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,60}$/u", $nombre)) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

if (!is_numeric($precio) || (float)$precio <= 0 || !preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $precio)) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

if (!ctype_digit($stock)) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

if (!ctype_digit($id_categoria) || (int)$id_categoria <= 0) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

$consulta_categoria = mysqli_prepare( $conexion, "SELECT id_categoria FROM categorias WHERE id_categoria = ? AND estado = 'activo'");

mysqli_stmt_bind_param($consulta_categoria, "i", $id_categoria);
mysqli_stmt_execute($consulta_categoria);

$resultado_categoria = mysqli_stmt_get_result($consulta_categoria);
$categoria = mysqli_fetch_assoc($resultado_categoria);

mysqli_stmt_close($consulta_categoria);

if (!$categoria) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

$consulta_codigo = mysqli_prepare( $conexion, "SELECT id_producto FROM productos WHERE codigo = ?");

mysqli_stmt_bind_param($consulta_codigo, "s", $codigo);
mysqli_stmt_execute($consulta_codigo);

$resultado_codigo = mysqli_stmt_get_result($consulta_codigo);
$producto_existente = mysqli_fetch_assoc($resultado_codigo);

mysqli_stmt_close($consulta_codigo);

if ($producto_existente) {
    header("Location: ../administrador/productos.php?mensaje=duplicado");
    exit();
}

// Registrar producto
$sql = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, id_categoria, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')";

$consulta = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($consulta,"sssdii",$codigo,$nombre,$descripcion,$precio,$stock,$id_categoria);

if (mysqli_stmt_execute($consulta)) {
    mysqli_stmt_close($consulta);
    header("Location: ../administrador/productos.php?mensaje=exito");
    exit();
}

mysqli_stmt_close($consulta);

header("Location: ../administrador/productos.php?mensaje=error");
exit();

?>