
<?php

$servidor = "localhost";
$usuario = "root";
$password = "";
$baseDatos = "inventario_almacenamiento";

// Crear conexión
$conexion = mysqli_connect(
    $servidor,
    $usuario,
    $password,
    $baseDatos
);

// Comprobar conexión
if (!$conexion) {
    die("Error al conectar con MySQL: " . mysqli_connect_error());
}

// Configurar caracteres
mysqli_set_charset($conexion, "utf8mb4");

?>
