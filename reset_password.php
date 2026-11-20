<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Nueva Contraseña | Inventario</title>
</head>
<body>
    <header>
        <h1>Inventario Almacenamiento</h1>
    </header>

    <main class="contenedor sombra">
        <h2>Crear nueva contraseña</h2>

        <?php
        // Recibimos el token de la URL
        $token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_SPECIAL_CHARS);
        
        if (!$token) {
            echo '<p class="mensaje-error">Token no válido o ausente.</p>';
            echo '<p><a href="login.php">Volver al login</a></p>';
            exit();
        }
        ?>

        <form action="procesos/actualizar_password.php" method="post">
            <!-- Enviamos el token oculto -->
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

            <div class="campo">
                <label for="password">Nueva Contraseña</label>
                <input type="password" id="password" name="password" placeholder="Mínimo 8 caracteres" required>
            </div>

            <div class="campo">
                <label for="confirmar">Confirmar Contraseña</label>
                <input type="password" id="confirmar" name="confirmar" placeholder="Repite la contraseña" required>
            </div>
            
            <div>
                <input class="botonLogin" type="submit" value="Actualizar Contraseña">
            </div>
        </form>
    </main>
</body>
</html>