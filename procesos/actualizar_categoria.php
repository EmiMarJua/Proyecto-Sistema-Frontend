<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_categoria = $_POST["id_categoria"] ?? "";
$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");
$estado = $_POST["estado"] ?? "";

if ($id_categoria === "" ||!ctype_digit($id_categoria) ||$nombre === "" ||!in_array($estado, ["activo", "inactivo"])) 
{
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"UPDATE categorias SET nombre = ?, descripcion = ?, estado = ? WHERE id_categoria = ?");

mysqli_stmt_bind_param($consulta,"sssi",$nombre,$descripcion,$estado,$id_categoria);

if (mysqli_stmt_execute($consulta)) 
{
    header("Location: ../administrador/categorias.php?mensaje=actualizada");
    exit();
} 
else 
{
    if (mysqli_errno($conexion) == 1062) 
    {
        header("Location: ../administrador/categorias.php?mensaje=duplicada");
    } 
    else 
    {
        header("Location: ../administrador/categorias.php?mensaje=error");
    }
    exit();
}
?>