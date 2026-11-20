<?php

require_once "../conexion.php";

session_start();

// Comprobar que el formulario se haya enviado
if ($_SERVER["REQUEST_METHOD"] !== "POST") {

    header("Location: ../registro.php");
    exit();
}

// Recibir los datos del formulario
$nombre = trim($_POST["nombre"] ?? "");
$correo = trim($_POST["correo"] ?? "");
$usuario = trim($_POST["usuario"] ?? "");
$password = $_POST["password"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

// Validar que no existan campos vacíos
if ($nombre === "" || $correo === "" || $usuario === "" || $password === "" || $confirmar === "") {

    header("Location: ../registro.php?mensaje=vacio");
    exit();
}

// Validar el nombre completo
$expresionNombre = "/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/u";

if (!preg_match($expresionNombre, $nombre)) {

    header("Location: ../registro.php?mensaje=nombre");
    exit();
}

// Validar el formato del correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {

    header("Location: ../registro.php?mensaje=correo");
    exit();
}

// Validar nombre de usuario
$expresionUsuario = "/^[a-zA-Z0-9_]{4,20}$/";

if (!preg_match($expresionUsuario, $usuario)) {

    header("Location: ../registro.php?mensaje=usuario");
    exit();
}

// Comprobar que las contraseñas coincidan
if ($password !== $confirmar) {

    header("Location: ../registro.php?mensaje=contrasenas");
    exit();
}

// Exigir una contraseña de al menos 8 caracteres
if (strlen($password) < 8) {

    header("Location: ../registro.php?mensaje=longitud");
    exit();
}

// Comprobar si el correo o usuario ya existen
$sql = "SELECT id_usuario FROM usuarios WHERE correo = ? OR usuario = ?";

$consulta = mysqli_prepare($conexion, $sql);

mysqli_stmt_bind_param($consulta, "ss", $correo, $usuario);

mysqli_stmt_execute($consulta);

$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) > 0) {

    mysqli_stmt_close($consulta);

    header("Location: ../registro.php?mensaje=duplicado");
    exit();
}

mysqli_stmt_close($consulta);

// Proteger la contraseña
$password_hash = password_hash($password, PASSWORD_DEFAULT);

// Registrar al usuario como cliente
$sql = "INSERT INTO usuarios (nombre, correo, usuario, password_hash, rol, estado) VALUES (?, ?, ?, ?, 'cliente', 'activo')";

$consulta = mysqli_prepare($conexion, $sql);

if (!$consulta) {

    die("Error al preparar el registro: " . mysqli_error($conexion));

}

mysqli_stmt_bind_param($consulta, "ssss", $nombre, $correo, $usuario, $password_hash);

if (mysqli_stmt_execute($consulta)) {

    mysqli_stmt_close($consulta);

    header("Location: ../registro.php?mensaje=exito");
    exit();

} else {

    mysqli_stmt_close($consulta);

    header("Location: ../registro.php?mensaje=error");
    exit();

}

?>
