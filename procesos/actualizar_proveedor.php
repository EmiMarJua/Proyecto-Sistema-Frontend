<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_proveedor = $_POST["id_proveedor"] ?? "";
$nombre = trim($_POST["nombre"] ?? "");
$telefono = trim($_POST["telefono"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$direccion = trim($_POST["direccion"] ?? "");
$estado = $_POST["estado"] ?? "";

if ($id_proveedor === "" ||!ctype_digit($id_proveedor) ||$nombre === "" ||!in_array($estado, ["activo", "inactivo"])) {
    header("Location: ../administrador/proveedores.php?mensaje=error");
    exit();
}

if ($correo !== "" && !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../administrador/proveedores.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"UPDATE proveedores SET nombre = ?, telefono = ?, correo = ?, direccion = ?, estado = ? WHERE id_proveedor = ?");

mysqli_stmt_bind_param($consulta,"sssssi",$nombre,$telefono,$correo,$direccion,$estado,$id_proveedor);

if (mysqli_stmt_execute($consulta)) {
    header("Location: ../administrador/proveedores.php?mensaje=actualizado");
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