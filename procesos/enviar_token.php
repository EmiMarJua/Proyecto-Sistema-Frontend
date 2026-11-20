<?php
require_once "../conexion.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../recuperar.php");
    exit();
}

$correo = trim($_POST["correo"] ?? "");

if (empty($correo) || !filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../recuperar.php?mensaje=error");
    exit();
}

// 1. Buscar si el correo existe en la base de datos
$sql = "SELECT id_usuario FROM usuarios WHERE correo = ?";
$consulta = mysqli_prepare($conexion, $sql);
mysqli_stmt_bind_param($consulta, "s", $correo);
mysqli_stmt_execute($consulta);
$resultado = mysqli_stmt_get_result($consulta);

if (mysqli_num_rows($resultado) > 0) {
    $usuario = mysqli_fetch_assoc($resultado);
    
    $token = bin2hex(random_bytes(16)); 
    $expiracion = date('Y-m-d H:i:s', strtotime('+1 hour'));

    // 3. Guardar en la BD
    $sql_update = "UPDATE usuarios SET token_recuperacion = ?, expiracion_token = ? WHERE id_usuario = ?";
    $stmt_update = mysqli_prepare($conexion, $sql_update);
    mysqli_stmt_bind_param($stmt_update, "ssi", $token, $expiracion, $usuario['id_usuario']);
    mysqli_stmt_execute($stmt_update);
    mysqli_stmt_close($stmt_update);

    $enlace = "http://localhost/pw/Proyecto%20PHP/ProyectoFrontend-InventarioAlmacenamiento/reset_password.php?token=" . $token;
    
    echo "<div style='font-family: Arial; padding: 20px; text-align: center;'>";
    echo "<h2>Simulación de Correo </h2>";
    echo "<p>ve a este enace para cambiar la contraseña:</p>";
    echo "<a href='$enlace' style='color: blue; font-weight: bold;'>$enlace</a>";
    echo "<br><br><a href='../login.php'>Volver al Login</a>";
    echo "</div>";
    exit();
} else {
    // Por seguridad, se usa el mismo mensaje (enviado) para no revelar si el correo existe
    header("Location: ../recuperar.php?mensaje=enviado"); 
    exit();
}
mysqli_stmt_close($consulta);
?>