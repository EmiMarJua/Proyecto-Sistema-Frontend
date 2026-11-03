<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

// Productos disponibles para registrar movimientos
$productos = mysqli_query(
    $conexion,
    "SELECT id_producto, codigo, nombre, stock
     FROM productos
     WHERE estado = 'activo'
     ORDER BY nombre ASC"
);

// Historial de movimientos
$movimientos = mysqli_query(
    $conexion,
    "SELECT m.id_movimiento, m.tipo, m.cantidad, m.descripcion,
            m.fecha_movimiento, p.nombre AS producto,
            p.codigo, u.usuario
     FROM movimientos_inventario m
     INNER JOIN productos p ON m.id_producto = p.id_producto
     INNER JOIN usuarios u ON m.id_usuario = u.id_usuario
     ORDER BY m.fecha_movimiento DESC, m.id_movimiento DESC"
);

if (!$productos || !$movimientos) {
    die("Error al consultar movimientos: " . mysqli_error($conexion));
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos de Inventario</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Movimientos de Inventario</h1>

        <?php if ($mensaje === "registrado"): ?>

            <p>El movimiento se registró correctamente y el stock fue actualizado.</p>

        <?php elseif ($mensaje === "error"): ?>

            <p>No fue posible registrar el movimiento.</p>

        <?php elseif ($mensaje === "stock"): ?>

            <p>No hay existencias suficientes para realizar esa salida.</p>

        <?php elseif ($mensaje === "datos"): ?>

            <p>Verifica los datos ingresados.</p>

        <?php elseif ($mensaje === "sin_cambios"): ?>

            <p>El ajuste indicado no cambia el stock actual.</p>

        <?php endif; ?>

        <h2>Registrar movimiento</h2>

        <form action="../procesos/guardar_movimiento.php" method="POST" class="formulario-ancho">

        <label for="id_producto">Producto:</label>
        <select id="id_producto" name="id_producto" required>
            <option value="">Selecciona un producto</option>
            <?php while ($producto = mysqli_fetch_assoc($productos)): ?>
                <option value="<?php echo $producto["id_producto"]; ?>">
                    <?php echo htmlspecialchars($producto["codigo"] . " - " .$producto["nombre"] ." (Stock: " . $producto["stock"] . ")"); ?>
                </option>
            <?php endwhile; ?>
        </select>

        <label for="tipo">Tipo de movimiento:</label>
        <select id="tipo" name="tipo" required>
            <option value="">Selecciona el tipo</option>
            <option value="entrada">Entrada</option>
            <option value="salida">Salida</option>
            <option value="ajuste">Ajuste</option>
        </select>

        <label for="cantidad">Cantidad:</label>
        <input type="number" id="cantidad" name="cantidad" min="0" step="1" required>

        <p class="texto-ayuda">
            Para entrada y salida, indica cuántas unidades se mueven.
            Para ajuste, indica cuál debe ser el stock final del producto.
        </p>

        <label for="descripcion">Descripción:</label>
        <textarea id="descripcion" name="descripcion" maxlength="250" required></textarea>

        <button type="submit" class="boton">
            Registrar movimiento
        </button>

</form>

        <hr>

        <h2>Historial de movimientos</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Producto</th>
                    <th>Tipo</th>
                    <th>Cantidad</th>
                    <th>Descripción</th>
                    <th>Usuario</th>
                    <th>Fecha</th>
                </tr>
            </thead>

            <section>
                <?php while ($movimiento = mysqli_fetch_assoc($movimientos)): ?>
                    <tr>
                        <td><?php echo $movimiento["id_movimiento"]; ?></td>

                        <td>
                            <?php
                            echo htmlspecialchars(
                                $movimiento["codigo"] . " - " .
                                $movimiento["producto"]
                            );
                            ?>
                        </td>

                        <td><?php echo htmlspecialchars($movimiento["tipo"]); ?></td>

                        <td><?php echo $movimiento["cantidad"]; ?></td>

                        <td><?php echo htmlspecialchars($movimiento["descripcion"] ?? ""); ?></td>

                        <td><?php echo htmlspecialchars($movimiento["usuario"]); ?></td>

                        <td><?php echo htmlspecialchars($movimiento["fecha_movimiento"]); ?></td>
                    </tr>
                <?php endwhile; ?>
                </section>
        </table>

        <br>

        <a class="boton" href="inicio.php">Volver al panel principal</a>

    </main>
    
                  <style>
                    form.formulario-ancho {
                        display: flex !important;
                        flex-direction: column !important;
                        gap: 1.5rem !important;
                    }

                    /* Ajustar el texto de ayuda para que se vea ordenado */
                    .texto-ayuda {
                        font-size: 1.4rem;
                        color: var(--gris);
                        background-color: var(--blanco);
                        padding: 1rem;
                        border-radius: 0.5rem;
                        border-left: 0.4rem solid var(--primario);
                        margin: 0;
                    }

                    /* Ajustar el botón para que quede centrado y no ocupe todo el ancho */
                    form.formulario-ancho .boton {
                        margin: 2rem auto 0 auto !important; /* Centrado horizontalmente */
                        width: auto !important;
                        min-width: 20rem; /* Un poco más ancho para que se vea bien */
                    }

                    </style>

</body>
</html>