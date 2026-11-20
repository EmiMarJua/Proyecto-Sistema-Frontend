<?php
require_once "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../login.php");
    exit();
}

$token = $_POST["token"] ?? "";
$password = $_POST["password"] ?? "";
$confirmar = $_POST["confirmar"] ?? "";

if (empty($token) || empty($password) || empty($confirmar)) {
    die("Todos los campos son obligatorios. <a href='../login.php'>Volver</a>");
}

if ($password !== $confirmar) {
    die("Las contraseñas no coinciden. <a href='javascript:history.back()'>Volver a intentar</a>");
}

if (strlen($password) < 8) {
    die("La contraseña debe tener al menos 8 caracteres. <a href='javascript:history.back()'>Volver a intentar</a>");
}

$sql = "SELECT id_usuario FROM usuarios WHERE token_recuperacion = ? AND expiracion_token > NOW()";
$consulta = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($consulta, "s", $token);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    
    // 2. Encriptar la nueva contraseña
    $nuevo_hash = password_hash($password, PASSWORD_DEFAULT);
    
    $sql_update = "UPDATE usuarios SET password_hash = ?, token_recuperacion = NULL, expiracion_token = NULL WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($conexion, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "si", $nuevo_hash, $usuario['id_usuario']);
    
    if (mysqli_stmt_execute($stmt_update)) {
        echo "<div style='font-family: Arial; padding: 20px; text-align: center;'>";
        echo "<h2>Contraseña actualizada con éxito</h2>";
        echo "<p>Ya puedes iniciar sesión con tu nueva contraseña.</p>";
        echo "<a href='../login.php' style='padding: 10px 20px; background: #1565c0; color: white; text-decoration: none; border-radius: 5px;'>Ir al Login</a>";
        echo "</div>";
    } else {
        echo "Error al actualizar la contraseña.";
    }
    mysqli_stmt_close($stmt_update);
} else {
    echo "<div style='font-family: Arial; padding: 20px; text-align: center;'>";
    echo "<h2>Token inválido o expirado</h2>";
    echo "<p>Por favor, solicita un nuevo enlace de recuperación.</p>";
    echo "<a href='../recuperar.php'>Volver a intentar</a>";
    echo "</div>";
}

mysqli_stmt_close($consulta);
?>