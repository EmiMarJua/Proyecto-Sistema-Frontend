<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$consulta = mysqli_query($conexion,"SELECT * FROM proveedores ORDER BY id_proveedor DESC");

if (!$consulta) {
    die("Error al consultar proveedores: " . mysqli_error($conexion));
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Proveedores</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Administración de Proveedores</h1>

        <?php if ($mensaje === "registrado"): ?>

            <p>El proveedor se registró correctamente.</p>

        <?php elseif ($mensaje === "actualizado"): ?>

            <p>El proveedor se actualizó correctamente.</p>

        <?php elseif ($mensaje === "estado"): ?>

            <p>El estado del proveedor se cambió correctamente.</p>

        <?php elseif ($mensaje === "duplicado"): ?>

            <p>Ya existe un proveedor con esos datos únicos.</p>
            
        <?php elseif ($mensaje === "error"): ?>

            <p>Ocurrió un error al realizar la operación.</p>

        <?php endif; ?>

        <h2>Registrar proveedor</h2>

        <form action="../procesos/guardar_proveedor.php" method="POST">

            <label class="acomodarForm" for="nombre">Nombre del proveedor:</label>
            <input style="margin: -0.5rem -15rem 0 ; type="text"id="nombre"name="nombre"maxlength="100" required>

            <label class="acomodarForm" for="telefono">Teléfono:</label>
            <input style="margin: -0.5rem -15rem 0 ; type="text"id="telefono"name="telefono"maxlength="20">

            <label class="acomodarForm" for="correo">Correo electrónico:</label>
            <input style="margin: -0.5rem -15rem 0 ; type="email"id="correo"name="correo"maxlength="150">

            <label class="acomodarForm" for="direccion">Dirección:</label>
            <textarea style="margin: -0.5rem -15rem 0 ; id="direccion"name="direccion"rows="3"></textarea>

            <button href="proovedores.php"  type="submit" class="botonGuardar">
                Guardar proveedor
            </button>

        </form>

        <hr>

        <h2>Proveedores registrados</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Teléfono</th>
                    <th>Correo</th>
                    <th>Dirección</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <tbody>
                <?php while ($proveedor = mysqli_fetch_assoc($consulta)): ?>
                    <tr>
                        <td>
                            <?php echo $proveedor["id_proveedor"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($proveedor["nombre"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($proveedor["telefono"] ?? ""); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($proveedor["correo"] ?? ""); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($proveedor["direccion"] ?? ""); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($proveedor["estado"]); ?>
                        </td>

                        <td>
                            <a href="editar_proveedor.php?id=<?php echo $proveedor["id_proveedor"]; ?>">
                                Editar
                            </a>

                            <form class="nada" action="../procesos/cambiar_estado_proveedor.php"method="POST"style="display:inline;">
                                <input type="hidden"name="id_proveedor"value="<?php echo $proveedor["id_proveedor"]; ?>">

                                <input type="hidden"name="estado"value="<?php echo $proveedor["estado"] === "activo" ? "inactivo" : "activo"; ?>">

                                <button type="submit" class="botonBonito">
                                    <?php echo $proveedor["estado"] === "activo" ? "Desactivar" : "Activar"; ?>
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <br>

        <a class="boton" href="inicio.php">Volver al panel principal</a>

                        <style>
                            .acomodarForm{
                                margin-left: 5rem; 
                                text-decoration: none; 
                                text-align:center; 
                                font-weight: bold;
                            }
                    .botonGuardar{
                        background-color: var(--primario);
                        color: var(--blanco);
                        padding: 2rem 20rem;
                        font-size: 1.6rem;
                        font-weight: bold;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        display: flex;
                        justify-content: center;
                        margin: 3rem 35rem 0;    
                        transition: background-color 0.3s, transform 0.2s;
                    }
                    .nada {
                            background-color: transparent ;
                            padding: 0 ;
                            margin:0 5rem 0;
                            border: none ;
                            box-shadow: none ;
                            display: inline-block ;
                        }
                    .botonBonito{
                        background-color: var(--primario);
                        color: var(--blanco);
                        padding: 1rem 2rem;
                        font-size: 1.6rem;
                        font-weight: bold;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        margin: 1rem -3rem 0;    
                        transition: background-color 0.3s, transform 0.2s;
                    }

                    </style>

    </main>

</body>
</html>