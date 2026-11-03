<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_producto = $_POST["id_producto"] ?? "";
$cantidad = $_POST["cantidad"] ?? "";

if ($id_producto === "" ||!ctype_digit($id_producto) ||$cantidad === "" ||!ctype_digit($cantidad) ||(int)$cantidad <= 0) {
    header("Location: ../cliente/carrito.php");
    exit();
}

$id_producto = (int)$id_producto;
$cantidad = (int)$cantidad;

$consulta = mysqli_prepare($conexion,"SELECT stock FROM productos WHERE id_producto = ? AND estado = 'activo'");

mysqli_stmt_bind_param($consulta, "i", $id_producto);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$producto = mysqli_fetch_assoc($resultado);

if (!$producto || $cantidad > (int)$producto["stock"]) {
    header("Location: ../cliente/carrito.php?mensaje=stock");
    exit();
}

$_SESSION["carrito"][$id_producto] = $cantidad;

header("Location: ../cliente/carrito.php?mensaje=actualizado");
exit();
?>