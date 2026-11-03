<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$consulta = mysqli_query($conexion,"SELECT v.id_venta, v.fecha_venta, v.total, v.estado,u.nombre AS cliente, u.correo FROM ventas v 
    INNER JOIN usuarios u ON v.id_usuario = u.id_usuario ORDER BY v.fecha_venta DESC, v.id_venta DESC");

if (!$consulta) {
    die("Error al consultar ventas: " . mysqli_error($conexion));
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Ventas</title>

    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>
                        <style>
                            .acomodarForm{
                                margin: 0rem 5rem 0; 
                                text-decoration: none; 
                                text-align:center; 
                                font-weight: bold;
                                margin:1rem 20rem auto;
                            }
                            .nada {
                            background-color: transparent ;
                            padding: 0 ;
                            margin:0 5rem 0;
                            border: none ;
                            box-shadow: none ;
                            display: inline-block ;
                        }
                    </style>
    <main class="contenedor sombra">

        <h1>Administración de Ventas</h1>

        <h2>Historial de ventas</h2>

        <table class="acomodarForm" border=1>
            <thead>
                <tr>
                    <th >ID Venta</th>
                    <th>Cliente</th>
                    <th>Correo</th>
                    <th>Fecha</th>
                    <th>Total</th>
                    <th>Estado</th>
                    <th>Detalles</th>
                </tr>
            </thead>

            <section>
                <?php while ($venta = mysqli_fetch_assoc($consulta)): ?>
                    <tr>
                        <td class="acomodarForm"><?php echo $venta["id_venta"]; ?></td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($venta["cliente"]); ?>
                        </td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($venta["correo"]); ?>
                        </td class="acomodarForm">

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($venta["fecha_venta"]); ?>
                        </td>

                        <td class="acomodarForm">
                            $<?php echo number_format((float)$venta["total"], 2); ?>
                        </td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($venta["estado"]); ?>
                        </td>

                        <td class="acomodarForm">
                            <a href="detalle_venta.php?id=<?php echo $venta["id_venta"]; ?>">
                                Ver detalles
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </section>
        </table>

        <br>

        <a class="boton" href="inicio.php">Volver al panel principal</a>

    </main>

</body>
</html>