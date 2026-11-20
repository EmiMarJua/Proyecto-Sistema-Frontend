<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Recuperar Contraseña | Inventario</title>
</head>
<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
    </header>

    <style>
        .botonLogin {
            display: block;
            width: 100%;
            max-width: 40rem;
            margin: 2rem auto 1rem;
            padding: 1.5rem 2rem;
            background-color: #1565c0;
            color: #ffffff;
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            transition: background-color 0.3s, transform 0.2s;
        }

        .botonLogin:hover {
            background-color: #0d47a1;
            transform: translateY(-1px);
        }

        
    </style>

    <main class="contenedor sombra">
        <h2>Recuperar Contraseña</h2>
        <p class="texto-centrado">Ingresa tu correo y te enviaremos un enlace para restablecer tu contraseña.</p>

        <?php
        $mensaje = filter_input(INPUT_GET, 'mensaje', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($mensaje === 'enviado') {
            echo '<p class="mensaje-exito">Si el correo está registrado, se han enviado las instrucciones.</p>';
        } elseif ($mensaje === 'error') {
            echo '<p class="mensaje-error">Ocurrió un error. Intenta de nuevo.</p>';
        }
        ?>

        <form action="procesos/enviar_token.php" method="post">
            <div class="campo">
                <label for="correo">Correo Electrónico</label>
                <input type="email" id="correo" name="correo" placeholder="Escribe tu correo con el que registraste" required>
            </div>
            
            <div>
                <input class="botonLogin" type="submit" value="Enviar enlace">
            </div>
        </form>

        <p class="texto-centrado">
            <a href="login.php">Volver al inicio de sesión</a>
        </p>
    </main>
</body>
</html>