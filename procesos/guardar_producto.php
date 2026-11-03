
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

$codigo = trim($_POST["codigo"]);
$nombre = trim($_POST["nombre"]);
$descripcion = trim($_POST["descripcion"]);
$precio = $_POST["precio"];
$stock = $_POST["stock"];
$id_categoria = $_POST["id_categoria"];

if ($codigo === "" ||$nombre === "" ||$precio <= 0 ||!is_numeric($precio) ||!ctype_digit($stock) ||!ctype_digit($id_categoria))
{
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();
}

$sql = "INSERT INTO productos (codigo, nombre, descripcion, precio, stock, id_categoria, estado) VALUES (?, ?, ?, ?, ?, ?, 'activo')";

    $consulta = mysqli_prepare($conexion, $sql);

    mysqli_stmt_bind_param(
        $consulta,
        "sssdii",
        $codigo,
        $nombre,
        $descripcion,
        $precio,
        $stock,
        $id_categoria
    );

    mysqli_stmt_execute($consulta);

    mysqli_stmt_close($consulta);

    header("Location: ../administrador/productos.php?mensaje=exito");
    exit();


?>
