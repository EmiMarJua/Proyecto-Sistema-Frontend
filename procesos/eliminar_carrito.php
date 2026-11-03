<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

$id_producto = $_POST["id_producto"] ?? "";

if ($id_producto !== "" && ctype_digit($id_producto)) {

    $id_producto = (int)$id_producto;

    if (isset($_SESSION["carrito"][$id_producto])) {
        unset($_SESSION["carrito"][$id_producto]);
    }
}

header("Location: ../cliente/carrito.php?mensaje=eliminado");
exit();
?>