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

$nombre = trim($_POST["nombre"] ?? "");
$descripcion = trim($_POST["descripcion"] ?? "");

// Validar nombre
if (
    $nombre === "" ||
    !preg_match("/^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,100}$/u", $nombre)
) {
    header("Location: ../administrador/categorias.php?mensaje=error");
    exit();
}

$consulta = mysqli_prepare($conexion,"INSERT INTO categorias (nombre, descripcion, estado) VALUES (?, ?, 'activo')");

mysqli_stmt_bind_param($consulta, "ss", $nombre, $descripcion);

if (mysqli_stmt_execute($consulta)) {
    mysqli_stmt_close($consulta);
    header("Location: ../administrador/categorias.php?mensaje=registrada");
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