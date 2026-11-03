
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

$id_producto = $_POST["id_producto"];
$codigo = trim($_POST["codigo"]);
$nombre = trim($_POST["nombre"]);
$descripcion = trim($_POST["descripcion"]);
$precio = $_POST["precio"];
$stock = $_POST["stock"];
$id_categoria = $_POST["id_categoria"];
$estado = $_POST["estado"];

// Validar los datos
if (!ctype_digit($id_producto) ||$codigo === "" ||$nombre === "" ||!is_numeric($precio) ||$precio <= 0 ||!ctype_digit($stock) ||!ctype_digit($id_categoria) ||!in_array($estado, ["activo", "inactivo"])
) 
{
    header("Location: ../administrador/productos.php?mensaje=datos");
    exit();

}

// Actualizar producto
$sql = "UPDATE productos SET codigo = ?,nombre = ?,descripcion = ?,precio = ?,stock = ?,id_categoria = ?,estado = ? WHERE id_producto = ?";

    $consulta = mysqli_prepare($conexion, $sql);

    mysqli_stmt_bind_param($consulta,"sssdiisi",  $codigo,$nombre,$descripcion,$precio,$stock,$id_categoria,$estado,$id_producto);

    mysqli_stmt_execute($consulta);

    mysqli_stmt_close($consulta);

    header("Location: ../administrador/productos.php?mensaje=actualizado");
    exit();



?>
