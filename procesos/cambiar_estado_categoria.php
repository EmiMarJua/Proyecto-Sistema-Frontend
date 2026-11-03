<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_categoria = $_POST["id_categoria"] ?? "";
$estado = $_POST["estado"] ?? "";

if ($id_categoria === "" ||!ctype_digit($id_categoria) ||!in_array($estado, ["activo", "inactivo"])) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"UPDATE categorias SET estado = ? WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta,"si",$estado,$id_categoria);

if (mysqli_stmt_execute($consulta)) {
    header("Location: ../administrador/categorias.php?mensaje=estado");
    exit();
} 
else
{
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}
?>