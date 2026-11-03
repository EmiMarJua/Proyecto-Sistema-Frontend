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
        <p>Control y administración de productos y almacenes</p>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="index.php">Inicio</a>
            <a href="administrador/productos.php">Productos</a>
            <a href="administrador/movimientos.php">Movimientos</a>
            <a href="cliente/documentacion.php">Documentación</a>
            <a href="registro.php">Registro</a>
            <a href="cliente/compras.php">Pagos</a>
            <a href="login.php">Iniciar Sesion</a>
        </nav>
    </div>

    <section class="hero">

        <h2 >Sistema de Gestión de Inventario</h2>
        
        <p>
            Consulta productos, revisa existencias, conoce los movimientos
            del almacén y mantén organizada la información de tu inventario.
        </p>

        <a class="botonPrincipal" href="clientes/productos.php">Consultar productos</a>

    </section>

    <main class="contenedor sombra">

        <section>

            <h2>Información general</h2>

            <div class="servicios">

                <article class="servicio">

                    <h3>Productos</h3>

                    <p>
                        Consulta nombres, códigos, categorías,
                        precios y existencias de los productos.
                    </p>

                    <a class="boton" href="cliente/productos.php">Ver productos</a>

                </article>

                <article class="servicio">

                    <h3>Movimientos</h3>

                    <p>
                        Revisa las entradas y salidas de mercancía
                        registradas en el almacén.
                    </p>

                    <a class="boton" href="administrador/movimientos.php">Ver movimientos</a>

                </article>

                <article class="servicio">

                    <h3>Documentación</h3>

                    <p>
                        Consulta manuales, catálogos y reportes
                        relacionados con el inventario.
                    </p>

                    <a class="boton" href="cliente/documentacion.php">Ver documentos</a>

                </article>

            </div>
        </section>

        <section class="texto-centrado">

            <h2>Información del sistema</h2>

            <p><strong>Nombre:</strong> Inventario Almacenamiento</p>
            <p><strong>Área:</strong> Control de productos y almacenes</p>
            <p><strong>Modalidad:</strong> Consulta y administración de inventario</p>
            <p><strong>Periodo:</strong> 2026</p>
            <p><strong>Sede:</strong> Área de almacenamiento principal</p>

        </section>

        <section>

            <h2>Registro y pagos</h2>

            <p class="texto-centrado">
                Consulta las opciones de registro y los precios
                de acceso a los servicios del sistema.
            </p>

            <div class="texto-centrado">

                <a class="boton" href="registro.html">Ir al registro</a>
                <a class="boton" href="compras.html">Consultar pagos</a>

            </div>

            <div class="texto-centrado">

                <p>Medio de pago de referencia:</p>

                <a href="compras.html">

                    <img src="img/paypal.png.webp" alt="Imagen de PayPal" width="180">

                </a>

            </div>

        </section>

    </main>

</body>
</html>