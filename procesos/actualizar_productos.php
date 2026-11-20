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

// Recibir datos
$id_producto = $_POST["id_producto"] ?? "";
$codigo = trim($_POST["codigo"] ?? "");
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$precio = $_POST["precio"] ?? "";
$stock = $_POST["stock"] ?? "";
$id_categoria = $_POST["id_categoria"] ?? "";
$estado = $_POST["estado"] ?? "";

// Validar campos obligatorios y el ID
if (
    !ctype_digit($id_producto) ||
    (int)$id_producto <= 0 ||
    $codigo === "" ||
    $nombre === "" ||
    $precio === "" ||
    $stock === "" ||
    $id_categoria === "" ||
    $estado === ""
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

// Validar precio
if (
    !is_numeric($precio) ||
    (float)$precio <= 0 ||
    !preg_match("/^[0-9]+(\.[0-9]{1,2})?$/", $precio)
) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Validar existencias
if (!ctype_digit($stock)) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Validar categoría
if (!ctype_digit($id_categoria) || (int)$id_categoria <= 0) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Validar estado
if (!in_array($estado, ["activo", "inactivo"])) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

// Comprobar que el producto exista
$consulta_producto = mysqli_prepare($conexion,"SELECT id_producto FROM productos WHERE id_producto = ?");

mysqli_stmt_bind_param($consulta_producto, "i", $id_producto);
mysqli_stmt_execute($consulta_producto);

$resultado_producto = mysqli_stmt_get_result($consulta_producto);
$producto_existente = mysqli_fetch_assoc($resultado_producto);

mysqli_stmt_close($consulta_producto);

if (!$producto_existente) {
    header("Location: ../administrador/productos.php?mensaje=no_encontrado");
    exit();
}

$consulta_codigo = mysqli_prepare($conexion,"SELECT id_producto FROM productos WHERE codigo = ? AND id_producto != ?");

mysqli_stmt_bind_param($consulta_codigo, "si", $codigo, $id_producto);
mysqli_stmt_execute($consulta_codigo);

$resultado_codigo = mysqli_stmt_get_result($consulta_codigo);
$codigo_existente = mysqli_fetch_assoc($resultado_codigo);

mysqli_stmt_close($consulta_codigo);

if ($codigo_existente) {
    header("Location: ../administrador/productos.php?mensaje=duplicado");
    exit();
}

$consulta_categoria = mysqli_prepare($conexion,"SELECT id_categoria FROM categorias WHERE id_categoria = ? AND estado = 'activo'");

mysqli_stmt_bind_param($consulta_categoria, "i", $id_categoria);
mysqli_stmt_execute($consulta_categoria);

$resultado_categoria = mysqli_stmt_get_result($consulta_categoria);
$categoria = mysqli_fetch_assoc($resultado_categoria);

mysqli_stmt_close($consulta_categoria);

if (!$categoria) {
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

$sql = "UPDATE productos SET codigo = ?, nombre = ?, descripcion = ?, precio = ?, stock = ?, id_categoria = ?, estado = ? WHERE id_producto = ?";

$consulta = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($consulta,"sssd iisi",$codigo,$nombre,$descripcion,$precio,$stock,$id_categoria,$estado,$id_producto);

if (mysqli_stmt_execute($consulta)) {
    mysqli_stmt_close($consulta);
    header("Location: ../administrador/productos.php?mensaje=actualizado");
    exit();
}

mysqli_stmt_close($consulta);

header("Location: ../administrador/productos.php?mensaje=error");
exit();

?>