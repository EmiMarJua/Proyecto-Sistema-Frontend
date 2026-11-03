<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$consulta = mysqli_query($conexion,"SELECT id_usuario, nombre, correo, usuario, rol, estado, fecha_registro FROM usuarios ORDER BY id_usuario DESC");

if (!$consulta) {
    die("Error al consultar usuarios: " . mysqli_error($conexion));
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Administración de Usuarios</h1>

        <?php if ($mensaje === "estado"): ?>

            <p>El estado del usuario se actualizó correctamente.</p>

        <?php elseif ($mensaje === "protegido"): ?>

            <p>No se permite cambiar el estado de una cuenta de administrador.</p>

        <?php elseif ($mensaje === "error"): ?>

            <p>Ocurrió un error al realizar la operación.</p>

        <?php endif; ?>

        <h2>Usuarios registrados</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Correo</th>
                    <th>Usuario</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Fecha de registro</th>
                    <th>Acciones</th>
                </tr>
            </thead>

            <section>
                <?php while ($usuario = mysqli_fetch_assoc($consulta)): ?>
                    <tr>
                        <td>
                            <?php echo $usuario["id_usuario"]; ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["nombre"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["correo"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["usuario"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["rol"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["estado"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($usuario["fecha_registro"]); ?>
                        </td>

                        <td>
                            <?php if ($usuario["rol"] === "cliente"): ?>

                                <form class="nada" action="../procesos/cambiar_estado_usuario.php" method="POST" >
                                    <input type="hidden"name="id_usuario"value="<?php echo $usuario["id_usuario"]; ?>">

                                    <input type="hidden"name="estado"value="<?php echo $usuario["estado"] === "activo" ? "inactivo" : "activo"; ?>">

                                    <button  type="submit" class="botonCentrado">
                                        <?php echo $usuario["estado"] === "activo" ? "Desactivar" : "Activar"; ?>
                                    </button>
                                </form>

                            <?php else: ?>

                                <span class="acomodarForm">Cuenta protegida</span>

                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                            </section>
        </table>

        <br>

        <a class="boton" href="inicio.php">Volver al panel principal</a>

    </main>
       
                        <style>
                            .acomodarForm{
                                margin: 0rem 5rem 0; 
                                text-decoration: none; 
                                text-align:center; 
                                font-weight: bold;
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

</body>
</html>