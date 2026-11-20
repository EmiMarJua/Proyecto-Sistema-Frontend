<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Iniciar sesión | Inventario de Almacenamiento</title>

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

        .mensaje-error {
            background-color: #ffebee;
            color: #b71c1c;
            border: 1px solid #ef9a9a;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            font-size: 1.5rem;
        }

        .mensaje-aviso {
            background-color: #fff8e1;
            color: #8d6e63;
            border: 1px solid #ffe082;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            font-size: 1.5rem;
        }

        .mensaje-exito {
            background-color: #e8f5e9;
            color: #1b5e20;
            border: 1px solid #a5d6a7;
            padding: 1rem 1.5rem;
            border-radius: 0.5rem;
            margin: 1rem 0;
            font-size: 1.5rem;
        }

        .campo {
            margin-bottom: 1.5rem;
        }

        .campo label {
            display: block;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .campo input {
            width: 100%;
            padding: 1rem;
            font-size: 1.6rem;
            border: 1px solid #cfd8dc;
            border-radius: 0.5rem;
            box-sizing: border-box;
        }

        .campo input:focus {
            outline: none;
            border-color: #1565c0;
            box-shadow: 0 0 0 2px rgba(21, 101, 192, 0.2);
        }
    </style>
</head>
<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
    </header>

    <main class="contenedor sombra">

        <h2>Iniciar sesión</h2>

        <p class="texto-centrado">
            Ingresa tus datos para acceder al sistema.
        </p>

        <?php
        // Saneamos el parámetro GET para evitar warnings/XSS
        $mensaje = filter_input(INPUT_GET, 'mensaje', FILTER_SANITIZE_SPECIAL_CHARS);

        if ($mensaje !== null) {
            switch ($mensaje) {
                case 'error':
                    echo '<p class="mensaje-error">Usuario o contraseña incorrectos.</p>';
                    break;
                case 'inactivo':
                    echo '<p class="mensaje-aviso">Tu cuenta se encuentra inactiva.</p>';
                    break;
                case 'vacio':
                    echo '<p class="mensaje-error">Todos los campos son obligatorios.</p>';
                    break;
                case 'usuario':
                    echo '<p class="mensaje-error">El nombre de usuario no es válido.</p>';
                    break;
                case 'registrado':
                    echo '<p class="mensaje-exito">Registro exitoso. Ya puedes iniciar sesión.</p>';
                    break;
            }
        }
        ?>

        <form action="procesos/validar_login.php" method="post" onsubmit="return validar()" novalidate>

            <div class="campo">
                <label for="usuario">Nombre de usuario</label>
                <input type="text"id="usuario"name="usuario"placeholder="Escribe tu usuario"autocomplete="username"aria-required="true"aria-describedby="mensajes"required>
            </div>

            <div class="campo">
                <label for="password">Contraseña</label>
                <input type="password"id="password"name="password"placeholder="Escribe tu contraseña"autocomplete="current-password"aria-required="true"aria-describedby="mensajes"required>
            </div>

            <!-- Contenedor único para mensajes (PHP ya imprimió los suyos arriba;
                 este se usa para los errores de JS) -->
            <div id="mensajes" aria-live="polite"></div>

            <div>
                <input class="botonLogin" type="submit" name="enviar" value="Iniciar sesión">
            </div>

        </form>

        <p class="texto-centrado">
            ¿No tienes cuenta?
            <a href="registro.php">Regístrate aquí</a>
        </p>
        <p class="texto-centrado">
            <a href="recuperar.php">¿Olvidaste tu contraseña?</a>
        </p>

    </main>

    <script>

        // Mostrar mensajes de error en el contenedor #mensajes
        function mostrarError(mensaje) {
            var contenedor = document.getElementById("mensajes");
            contenedor.innerHTML = "";

            var alerta = document.createElement("p");
            alerta.textContent = mensaje;
            alerta.classList.add("mensaje-error");

            contenedor.appendChild(alerta);
        }

        // Validar el formulario de inicio de sesión
        function validar() {
            var usuario  = document.getElementById("usuario").value.trim();
            var password = document.getElementById("password").value.trim();

            // Campos vacíos
            if (usuario === "" || password === "") {
                mostrarError("Error: Escribe tu usuario y contraseña.");
                return false;
            }

            var expresionUsuario = /^[a-zA-Z0-9_]{4,20}$/;
            if (!expresionUsuario.test(usuario)) {
                mostrarError("Error: El nombre de usuario no es válido (4-20 caracteres, solo letras, números o _).");
                document.getElementById("usuario").focus();
                return false;
            }

            if (password.length < 6) {
                mostrarError("Error: La contraseña debe tener al menos 6 caracteres.");
                document.getElementById("password").focus();
                return false;
            }

            return true;
        }
    </script>

</body>
</html>