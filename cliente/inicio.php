
<?php

session_start();

if (!isset($_SESSION["id_usuario"]) || $_SESSION["rol"] !== "cliente") {
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
    <title>Inicio del Cliente</title>
</head>

<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
        <p class="centrar-texto">Bienvenido, <?php echo htmlspecialchars($_SESSION["nombre"]); ?></p>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="productos.php">Productos</a>
            <a href="carrito.php">Mi Carrito</a>
            <a href="compras.php">Mis Compras</a>
            <a href="documentacion.php">Documentación</a>
            <a href="recibo.php">Recibos</a>
            <a href="../procesos/cerrar_sesion.php">Cerrar sesión</a>
        </nav>
    </div>

    <main class="contenedor sombra">

        <h2>Panel del Cliente</h2>

        <p>
            Desde aquí puedes consultar los productos disponibles,
            realizar compras y revisar la documentación del inventario.
        </p>

        <section class="servicios">

            <div class="servicio">
                <h3>Productos</h3>

                <p>Consulta los productos disponibles y sus existencias.</p>
                <a class="boton" href="productos.php">Ver productos</a>

            </div>

            <div class="servicio">

                <h3>Mis compras</h3>

                <p>Consulta tus compras y pedidos realizados.</p>
                <a class="boton" href="carrito.php">Ir a compras</a>

            </div>

            <div class="servicio">

                <h3>Documentación</h3>

                <p>Consulta los documentos del sistema de inventario.</p>
                <a class="boton" href="documentacion.php">Consultar</a>

            </div>

        </section>

    </main>

</body>
</html>
