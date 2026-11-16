
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

    <main class="contenedor sombra">

        <h2>Iniciar sesión</h2>

        <p class="texto-centrado"> Ingresa tus datos para acceder al sistema. </p>

        <?php
        if (isset($_GET["mensaje"])) {

            if ($_GET["mensaje"] == "error") 
            {
                echo "<p>Usuario o contraseña incorrectos.</p>";
            } 
            elseif ($_GET["mensaje"] == "inactivo") 
            {

                echo "<p>Tu cuenta se encuentra inactiva.</p>";
            }
        }
        ?>

        <form action="procesos/validar_login.php" method="post" onsubmit="return validar()">

            <div>
                <label for="usuario">Nombre de usuario</label>
                <input type="text" id="usuario" name="usuario" placeholder="Escribe tu usuario" required>
            </div>

            <div>
                <label for="password">Contraseña</label>
                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Escribe tu contraseña"
                    required>
            </div>

            <p id="mensaje" style="color: red;"></p>

            <div>
                <input class="botonLogin" type="submit" value="Iniciar sesión">
            </div>

        </form>

        <p class="texto-centrado">
            ¿No tienes cuenta?
            <a href="registro.php">Regístrate aquí</a>
        </p>

    <style>

        .botonLogin{
            background-color: #1565c0;
            color: #ffffff;
            padding: 1.5rem 10rem;
            font-size: 1.6rem;
            font-weight: bold;
            text-align: center;
            border: none;
            border-radius: 0.5rem;
            cursor: pointer;
            display: flex;
            flex-direction: row;
            margin: 2rem 38rem 1rem;   
            transition: background-color 0.3s, transform 0.2s;
        }

    </style>

    </main>

</body>

    <script>

    function validar() {

        var usuario = document.getElementById("usuario").value;
        var password = document.getElementById("password").value;

        // Validar campos vacios

        if (usuario.trim() == "" || password == "") {

            document.getElementById("mensaje").innerHTML = "Error: Escribe tu usuario y contraseña";
            return false;

        }

        // Validar formato del usuario

        var expresionUsuario = /^[a-zA-Z0-9_]{4,20}$/;

        if (!expresionUsuario.test(usuario)) {

            document.getElementById("mensaje").innerHTML = "Error: El nombre de usuario no es valido";
            return false;

        }

        return true;

    }

</script>

</html>
