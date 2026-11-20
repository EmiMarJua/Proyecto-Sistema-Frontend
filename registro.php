
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">
    <title>Inventario de Almacenamiento</title>
</head>

<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
    </header>

    <div class="nav-bg">

        <nav class="navegacion-principal contenedor">
            <a href="index.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="documentacion.php">Documentación</a>
            <a href="registro.php">Registro</a>
            <a href="compras.php">Compras</a>
            <a href="login.php">Iniciar Sesion</a>
        </nav>

    </div>

    <main class="contenedor sombra">

        <h2>Registro de Usuarios</h2>

        <?php

            if (isset($_GET["mensaje"])) {

                switch ($_GET["mensaje"]) {

                    case "exito":
                        echo "<p>¡Registro exitoso! Ya puedes iniciar sesión.</p>";
                        break;

                    case "vacio":
                        echo "<p>Todos los campos son obligatorios.</p>";
                        break;

                    case "correo":
                        echo "<p>El correo electronico no es valido.</p>";
                        break;

                    case "contrasenas":
                        echo "<p>Las contraseñas no coinciden.</p>";
                        break;

                    case "longitud":
                        echo "<p>La contraseña debe tener al menos 8 caracteres.</p>";
                        break;

                    case "duplicado":
                        echo "<p>El correo o nombre de usuario ya esta registrado.</p>";
                        break;

                    case "nombre":
                        echo "<p>El nombre solo debe contener letras y espacios.</p>";
                        break;

                    case "usuario":
                        echo "<p>El usuario debe tener entre 4 y 20 caracteres, solo letras, numeros y guion bajo.</p>";
                        break;

                    default:
                        echo "<p>Error al registrar el usuario.</p>";
                        break;
                }

            }

        ?>

        <p class="texto-centrado">
            Registra los datos del usuario que utilizará
            el sistema de inventario.
        </p>

        <section>

            <h3>Datos del usuario</h3>

            <form action="procesos/registrar_usuario.php" method="POST" onsubmit="return validar()" novalidate>

                <div>

                    <label for="nombre">Nombre completo</label>
                    <input type="text" id="nombre" name="nombre" placeholder="Escribe tu nombre completo" required>

                </div>

                <div>

                    <label for="correo">Correo electrónico</label>
                    <input type="email" id="correo" name="correo" placeholder="correo@ejemplo.com" required>

                </div>

                <div>

                    <label for="usuario">Nombre de usuario</label>
                    <input type="text" id="usuario" name="usuario" placeholder="Escribe un nombre de usuario" required>

                </div>

                <div>

                    <label for="password">Contraseña</label>
                    <input type="password" id="password" name="password" placeholder="Escribe una contraseña" required>

                </div>

                <div>

                    <label for="confirmar">Confirmar contraseña</label>
                    <input type="password" id="confirmar" name="confirmar" placeholder="Repite la contraseña" required>

                </div>

                <div id="mensajes"></div>

                <div>
                    <input class="botonCentrado" type="submit" value="Registrarse">
                </div>

            </form>

        </section>

    </main>

    <script>

        function mostrarError(mensaje) {

            var contenedor = document.getElementById("mensajes");

            contenedor.innerHTML = "";

            var alerta = document.createElement("p");

            alerta.textContent = mensaje;

            alerta.classList.add("error");

            contenedor.appendChild(alerta);

        }


        function validar() {

            var nombre = document.getElementById("nombre").value;
            var correo = document.getElementById("correo").value;
            var usuario = document.getElementById("usuario").value;
            var password = document.getElementById("password").value;
            var confirmar = document.getElementById("confirmar").value;

            // Validar si hay campos vacíos

            if (nombre.trim() == "" || correo.trim() == "" || usuario.trim() == "" || password == "" || confirmar == "") {

                mostrarError("Error: Todos los campos son obligatorios");
                return false;

            }

            // Validar nombre completo con expresión regular

            var expresionNombre = /^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]+$/;

            if (!expresionNombre.test(nombre.trim())) {

                mostrarError("Error: El nombre solo debe contener letras y espacios");
                return false;

            }

            // Validar correo electrónico

            var expresionCorreo = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

            if (!expresionCorreo.test(correo.trim())) {

                mostrarError("Error: El correo electrónico no es válido");
                return false;

            }

            // Validar nombre de usuario

            var expresionUsuario = /^[a-zA-Z0-9_]{4,20}$/;

            if (!expresionUsuario.test(usuario.trim())) {

                mostrarError("Error: El usuario debe tener entre 4 y 20 caracteres, solo letras, números y guion bajo");
                return false;

            }

            // Validar longitud de contraseña

            if (password.length < 8) {

                mostrarError("Error: La contraseña debe tener al menos 8 caracteres");
                return false;

            }

            // Comprobar que las contraseñas coincidan

            if (password != confirmar) {

                mostrarError("Error: Las contraseñas no coinciden");
                return false;

            }

            return true;

        }

    </script>

</body>

</html>
