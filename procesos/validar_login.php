<?php

session_start();

require_once "../conexion.php";

// Comprobar que el formulario se haya enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../login.php");
    exit();

}

// Recibir los datos
$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";

// Validar campos vacíos
if ($usuario === "" || $password === "") {

    header("Location: ../login.php?mensaje=vacio");
    exit();

}

// Validar formato del usuario
$expresionUsuario = "/^[a-zA-Z0-9_]{4,20}$/";

if (!preg_match($expresionUsuario, $usuario)) {

    header("Location: ../login.php?mensaje=usuario");
    exit();

}

// Consultar usuario
$sql = "SELECT id_usuario, nombre, usuario, password_hash, rol, estado FROM usuarios WHERE usuario = ? LIMIT 1";

$consulta = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($consulta, "s", $usuario);

mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) === 1) {

    $datos = mysqli_fetch_assoc($resultado);

    // Comprobar si la cuenta está activa
    if ($datos["estado"] !== "activo") {

        header("Location: ../login.php?mensaje=inactivo");
        exit();

    }

    // Comprobar contraseña
    if (password_verify($password, $datos["password_hash"])) {

        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $datos["id_usuario"];
        $_SESSION["nombre"] = $datos["nombre"];
        $_SESSION["usuario"] = $datos["usuario"];
        $_SESSION["rol"] = $datos["rol"];

        // Redireccionar según el rol
        if ($datos["rol"] === "administrador") {

            header("Location: ../administrador/inicio.php");

        } else {

            header("Location: ../cliente/inicio.php");

        }

        exit();

    }

}

// Si los datos son incorrectos
header("Location: ../login.php?mensaje=error");
exit();

?>