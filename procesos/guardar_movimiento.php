<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$id_producto = $_POST["id_producto"] ?? "";
$tipo = $_POST["tipo"] ?? "";
$cantidad = $_POST["cantidad"] ?? "";
$descripcion = trim($_POST["descripcion"] ?? "");

if ($id_producto === "" ||!ctype_digit($id_producto) ||$cantidad === "" ||!ctype_digit($cantidad) ||!in_array($tipo, ["entrada", "salida", "ajuste"]) ||$descripcion === "") 
{
    header("Location: ../administrador/movimientos.php?mensaje=datos");
    exit();
}

$cantidad = (int)$cantidad;

if (($tipo === "entrada" || $tipo === "salida") && $cantidad <= 0) {
    header("Location: ../administrador/movimientos.php?mensaje=datos");
    exit();
}

if ($tipo === "ajuste" && $cantidad < 0) {
    header("Location: ../administrador/movimientos.php?mensaje=datos");
    exit();
}

try {
    mysqli_begin_transaction($conexion);

    // Bloquear el producto mientras se actualiza su stock
    $consulta = mysqli_prepare($conexion,"SELECT stock FROM productos WHERE id_producto = ? FOR UPDATE");

    mysqli_stmt_bind_param($consulta, "i", $id_producto);
    mysqli_stmt_execute($consulta);

    $resultado = mysqli_stmt_get_result($consulta);
    $producto = mysqli_fetch_assoc($resultado);

    if (!$producto) {
        throw new Exception("Producto no encontrado.");
    }

    $stock_anterior = (int)$producto["stock"];

    // Calcular el nuevo stock
    if ($tipo === "entrada") {
        $nuevo_stock = $stock_anterior + $cantidad;
        $cantidad_movimiento = $cantidad;

    } elseif ($tipo === "salida") {

        if ($cantidad > $stock_anterior) {
            mysqli_rollback($conexion);
            header("Location: ../administrador/movimientos.php?mensaje=stock");
            exit();
        }

        $nuevo_stock = $stock_anterior - $cantidad;
        $cantidad_movimiento = $cantidad;

    } else {

        // En un ajuste, la cantidad recibida es el stock final deseado
        $nuevo_stock = $cantidad;
        $cantidad_movimiento = abs($nuevo_stock - $stock_anterior);

        if ($nuevo_stock === $stock_anterior) {
            mysqli_rollback($conexion);
            header("Location: ../administrador/movimientos.php?mensaje=sin_cambios");
            exit();
        }

        $descripcion .= " | Ajuste de stock: " .
                        $stock_anterior . " a " . $nuevo_stock;
    }

    // Actualizar existencias del producto
    $actualizar = mysqli_prepare($conexion,"UPDATE productos SET stock = ? WHERE id_producto = ?");

    mysqli_stmt_bind_param($actualizar,"ii",$nuevo_stock,$id_producto);

    if (!mysqli_stmt_execute($actualizar)) {
        throw new Exception("No se pudo actualizar el stock.");
    }

    // Registrar el movimiento
    $id_usuario = (int)$_SESSION["id_usuario"];

    $insertar = mysqli_prepare($conexion,"INSERT INTO movimientos_inventario(id_producto, id_usuario, id_venta, tipo, cantidad, descripcion)VALUES (?, ?, NULL, ?, ?, ?)");

    mysqli_stmt_bind_param($insertar,"iisis",$id_producto,$id_usuario,$tipo,$cantidad_movimiento,$descripcion);

    if (!mysqli_stmt_execute($insertar)) {
        throw new Exception("No se pudo guardar el movimiento.");
    }

    mysqli_commit($conexion);

    header("Location: ../administrador/movimientos.php?mensaje=registrado");
    exit();

} catch (Throwable $error) {

    mysqli_rollback($conexion);

    error_log($error->getMessage());

    header("Location: ../administrador/movimientos.php?mensaje=error");
    exit();
}
?>