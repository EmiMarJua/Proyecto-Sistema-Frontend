<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$nombre = trim($_POST["nombre"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");

if ($nombre === "") {
    header("Location: ../administrador/proveedores.php?mensaje=error");
    exit();
}

if ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../administrador/proveedores.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"INSERT INTO proveedores(nombre, telefono, correo, direccion, estado)VALUES (?, ?, ?, ?, 'activo')");

mysqli_stmt_bind_param($consulta,"ssss",$nombre,$telefono,$correo,$direccion);

if (mysqli_stmt_execute($consulta)) {
    header("Location: ../administrador/proveedores.php?mensaje=registrado");
    exit();
} 
else 
{
    if (mysqli_errno($conexion) == 1062) {
        header("Location: ../administrador/proveedores.php?mensaje=duplicado");
    } else {
        header("Location: ../administrador/proveedores.php?mensaje=error");
    }
    exit();
}
?>