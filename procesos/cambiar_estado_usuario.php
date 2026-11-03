<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_usuario = $_POST["id_usuario"] ?? "";
$estado = $_POST["estado"] ?? "";

if ($id_usuario === "" ||!ctype_digit($id_usuario) ||!in_array($estado, ["activo", "inactivo"])) 
{
    header("Location: ../administrador/usuarios.php?mensaje=error");
    exit();
}

// Consultar el rol del usuario
$consulta = mysqli_prepare($conexion,"SELECT rol FROM usuarios WHERE id_usuario = ?");

mysqli_stmt_bind_param($consulta, "i", $id_usuario);
mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);
$usuario = mysqli_fetch_assoc($resultado);

if (!$usuario) {
    header("Location: ../administrador/usuarios.php?mensaje=error");
    exit();
}

// Proteger las cuentas de administrador
if ($usuario["rol"] !== "cliente") {
    header("Location: ../administrador/usuarios.php?mensaje=protegido");
    exit();
}

// Actualizar el estado del cliente
$actualizar = mysqli_prepare($conexion,"UPDATE usuarios SET estado = ? WHERE id_usuario = ?");

mysqli_stmt_bind_param($actualizar,"si",$estado,$id_usuario);

if (mysqli_stmt_execute($actualizar)) {
    header("Location: ../administrador/usuarios.php?mensaje=estado");
    exit();
} else {
    header("Location: ../administrador/usuarios.php?mensaje=error");
    exit();
}
?>