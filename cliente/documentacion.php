
<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "cliente") {
    header("Location: ../login.php");
    exit();
}

// Documentos disponibles
$documentos = [
    [
        "titulo" => "Manual del Sistema",
        "descripcion" => "Guía general para utilizar el sistema de inventario de almacenamiento.",
        "archivo" => "manual_inventario.pdf"
    ],
    [
        "titulo" => "Catálogo de Productos",
        "descripcion" => "Documento de referencia para consultar los productos del inventario.",
        "archivo" => "catalogo_productos.pdf"
    ],
    [
        "titulo" => "Guía del Usuario",
        "descripcion" => "Instrucciones para consultar productos, realizar compras y revisar pedidos.",
        "archivo" => "guia_usuario.pdf"
    ]
];

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Documentación</title>

    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">

    <style>
        .documento {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.10);
            text-align: center;
        }

        .documento h2 {
            color: #1565c0;
        }

        .documento p {
            min-height: 55px;
        }

        .documento a {
            display: inline-block;
            margin-top: 15px;
            text-decoration: none;
        }

        .documento .disponible {
            color: #198754;
            font-weight: bold;
        }

        .documento .pendiente {
            color: #b45309;
            font-weight: bold;
        }
    </style>
</head>

<body>

<main class="contenedor sombra">

    <h1>Documentación del Inventario</h1>

    <p>
        Consulta los documentos y manuales relacionados con el sistema
        de inventario de almacenamiento.
    </p>

    <section class="servicios">

        <?php foreach ($documentos as $documento): ?>

            <?php
            $ruta = "../documentos/" . $documento["archivo"];
            $ruta_fisica = __DIR__ . "/../documentos/" . $documento["archivo"];

            $existe = file_exists($ruta_fisica);
            ?>

            <div class="servicio documento">

                <h2>
                    <?php echo htmlspecialchars($documento["titulo"]); ?>
                </h2>

                <p>
                    <?php echo htmlspecialchars($documento["descripcion"]); ?>
                </p>

                <?php if ($existe): ?>

                    <p class="disponible">Documento disponible</p>

                    <a href="<?php echo htmlspecialchars($ruta); ?>" target="_blank" rel="noopener noreferrer" class="boton">Consultar PDF</a>

                <?php else: ?>

                    <p class="pendiente">Documento pendiente de agregar</p>

                <?php endif; ?>

            </div>

        <?php endforeach; ?>

    </section>

    <br>

    <a class="boton" href="inicio.php">Volver al panel</a>

</main>

</body>
</html>
