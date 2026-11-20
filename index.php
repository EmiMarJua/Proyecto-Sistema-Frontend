<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="css/normalize.css">
    <link rel="stylesheet" href="css/style.css">

    <title>Inventario de Almacenamiento</title>

    <style>
        /* ===== Carrusel ===== */
        .carrusel {
            max-width: 120rem;
            width: 90%;
            margin: 3rem auto;
            position: relative;
            overflow: hidden;
            border-radius: 1rem;
        }

        .diapositiva {
            display: none;
            min-height: 28rem;
            padding: 5rem 3rem;
            text-align: center;
            color: #ffffff;
        }

        .diapositiva-uno  { background-color: #1565c0; }
        .diapositiva-dos  { background-color: #243b46; }
        .diapositiva-tres { background-color: #455a64; }

        .diapositiva h2 {
            color: #ffffff;
            font-size: 3rem;
            margin-bottom: 2rem;
        }

        .diapositiva p {
            color: #ffffff;
            font-size: 1.8rem;
            max-width: 70rem;
            margin: 0 auto 2rem;
            line-height: 1.6;
        }

        /* ===== Controles ===== */
        .controles-carrusel {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 2rem;
            padding: 1.5rem;
            background-color: #f2f5f9;
        }

        .controles-carrusel button {
            background-color: #1565c0;
            color: white;
            border: none;
            padding: 1rem 2rem;
            border-radius: .5rem;
            cursor: pointer;
            font-size: 1.6rem;
        }

        .controles-carrusel button:hover {
            background-color: #0d47a1;
        }

        .controles-carrusel span {
            font-size: 1.5rem;
            font-weight: bold;
            color: #212121;
        }
    </style>
</head>

<body>

    <header>
        <h1>Inventario Almacenamiento</h1>
        <p>Control y administración de productos y almacenes</p>
    </header>

    <div class="nav-bg">
        <nav class="navegacion-principal contenedor">
            <a href="index.php">Inicio</a>
            <a href="cliente/productos.php">Productos</a>
            <a href="administrador/movimientos.php">Movimientos</a>
            <a href="cliente/documentacion.php">Documentación</a>
            <a href="registro.php">Registro</a>
            <a href="cliente/compras.php">Pagos</a>
            <a href="login.php">Iniciar Sesión</a>
        </nav>
    </div>

    <section class="carrusel" id="carrusel">

        <div class="diapositiva diapositiva-uno">
            <h2>Sistema de Gestión de Inventario</h2>
            <p>
                Consulta productos, revisa existencias y mantén
                organizada la información de tu inventario.
            </p>
            <a class="botonPrincipal" href="cliente/productos.php">
                Consultar productos
            </a>
        </div>

        <div class="diapositiva diapositiva-dos">
            <h2>Control de productos</h2>
            <p>
                Consulta nombres, códigos, categorías, precios
                y existencias de los productos almacenados.
            </p>
            <a class="botonPrincipal" href="cliente/productos.php">
                Ver productos
            </a>
        </div>

        <div class="diapositiva diapositiva-tres">
            <h2>Administración de almacenes</h2>
            <p>
                Revisa las entradas y salidas de mercancía
                para mantener un mejor control del inventario.
            </p>
            <a class="botonPrincipal" href="login.php">
                Iniciar sesión
            </a>
        </div>

        <div class="controles-carrusel">
            <button type="button" id="anterior" aria-label="Diapositiva anterior">Anterior</button>
            <span id="numero-diapositiva" aria-live="polite">1 / 3</span>
            <button type="button" id="siguiente" aria-label="Diapositiva siguiente">Siguiente</button>
        </div>

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
                <a class="boton" href="registro.php">Ir al registro</a>
                <a class="boton" href="cliente/compras.php">Consultar pagos</a>
            </div>

            <div class="texto-centrado">
                <p>Medio de pago de referencia:</p>
                <a href="cliente/compras.php">
                    <img src="img/paypal.png.webp" alt="Imagen de PayPal" width="180">
                </a>
            </div>
        </section>

    </main>

    <!-- Biblioteca jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <script>
        $(document).ready(function () {

            var actual = 0;
            var diapositivas = $(".diapositiva");
            var total = diapositivas.length;
            var intervalo = null;
            var TIEMPO = 5000;

            // Mostrar la primera diapositiva
            diapositivas.hide().eq(actual).show();

            function mostrarDiapositiva(numero) {
                // Normaliza el índice (por si acaso)
                if (numero < 0) numero = total - 1;
                if (numero >= total) numero = 0;

                if (numero === actual && diapositivas.eq(actual).is(":visible")) {
                    return; // evita parpadeo innecesario
                }

                diapositivas.eq(actual).fadeOut(400);
                diapositivas.eq(numero).fadeIn(400);

                actual = numero;
                $("#numero-diapositiva").text((actual + 1) + " / " + total);
            }

            // Reinicia el auto-avance
            function reiniciarIntervalo() {
                if (intervalo) clearInterval(intervalo);
                intervalo = setInterval(function () {
                    mostrarDiapositiva(actual + 1);
                }, TIEMPO);
            }

            $("#siguiente").on("click", function () {
                mostrarDiapositiva(actual + 1);
                reiniciarIntervalo(); // reinicia el temporizador al interactuar
            });

            $("#anterior").on("click", function () {
                mostrarDiapositiva(actual - 1);
                reiniciarIntervalo();
            });

            // Pausar al pasar el mouse (mejora de accesibilidad)
            $("#carrusel").on("mouseenter", function () {
                if (intervalo) clearInterval(intervalo);
                intervalo = null;
            }).on("mouseleave", function () {
                reiniciarIntervalo();
            });

            // Iniciar auto-avance
            reiniciarIntervalo();
        });
    </script>

</body>
</html>