
<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
    <title>Panel del Administrador</title>
</head>

<body>

    <header>
        
        <h1>Inventario Almacenamiento</h1>

        <p class="texto-centrado">
            Bienvenido, administrador: <?php echo htmlspecialchars($_SESSION["nombre"]); ?>
        
        </p>

    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="inicio.php">Inicio</a>
            <a href="productos.php">Productos</a>
            <a href="categorias.php">Categorías</a>
            <a href="usuarios.php">Usuarios</a>
            <a href="ventas.php">Ventas</a>
            <a href="movimientos.php">Movimientos</a>
            <a href="usuarios.php">Administrar Usuarios</a>
            <a href="../procesos/cerrar_sesion.php">Cerrar sesión</a>
        </nav>
    </div>

    <main class="contenedor sombra">

        <h2>Panel de Administración</h2>

        <p>
            Desde este apartado puedes administrar los recursos
            y las operaciones del sistema de inventario.
        </p>

        <section class="servicios">

            <div class="servicio">

                <h3>Productos</h3>

                <p>Administra los productos y sus existencias.</p>
                <a class="boton" href="productos.php">Administrar</a>

            </div>

            <div class="servicio">

                <h3>Categorías</h3>

                <p>Gestiona las categorías de los productos.</p>
                <a class="boton" href="categorias.php">Administrar</a>

            </div>

            <div class="servicio">

                <h3>Usuarios</h3>

                <p>Consulta y administra las cuentas registradas.</p>
                <a class="boton" href="usuarios.php">Administrar</a>

            </div>

            <div class="servicio">

                <h3>Ventas</h3>

                <p>Consulta las ventas realizadas en el sistema.</p>
                <a class="boton" href="ventas.php">Consultar</a>

            </div>

            <div class="servicio">

                <h3>Movimientos</h3>

                <p>Revisa las entradas y salidas de inventario.</p>
                <a class="boton" href="movimientos.php">Consultar</a>

            </div>
            <div class="servicio">

                <h3>Proovedores</h3>

                <p>Revisa las entradas y salidas de inventario.</p>
                <a class="boton" href="proovedores.php">Consultar</a>

            </div>

        </section>

    </main>
                    <style>
                            .acomodarForm{
                                margin-left: 5rem; 
                                text-decoration: none; 
                                text-align:center; 
                                font-weight: bold;
                            }
                    </style>
</body>

    <script>

    // Obtener todos los títulos h3
    var titulos = document.getElementsByTagName("h3");

    // Recorrer los títulos
    for (var i = 0; i < titulos.length; i++) {

        titulos[i].style.color = "#1565c0";

    }

    </script>

</html>
