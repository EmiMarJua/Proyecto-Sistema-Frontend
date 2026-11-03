
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

        <form action="procesos/validar_login.php" method="post">

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

</html>
