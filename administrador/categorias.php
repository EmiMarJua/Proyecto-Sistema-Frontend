<?php
session_start();

if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "administrador") {
    header("Location: ../login.php");
    exit();
}

include("../conexion.php");

$consulta = mysqli_query($conexion, "SELECT * FROM categorias ORDER BY id_categoria DESC");

if (!$consulta) {
    die("Error al consultar categorías: " . mysqli_error($conexion));
}

$mensaje = $_GET["mensaje"] ?? "";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Categorías</title>
    <link rel="stylesheet" href="../css/normalize.css">
    <link rel="stylesheet" href="../css/style.css">
</head>
<body>

    <main class="contenedor sombra">

        <h1>Administración de Categorías</h1>

        <div id="mensajes"></div>

        <?php if ($mensaje === "registrada"): ?>

            <p>La categoría se registró correctamente.</p>

        <?php elseif ($mensaje === "actualizada"): ?>

            <p>La categoría se actualizó correctamente.</p>

        <?php elseif ($mensaje === "estado"): ?>

            <p>El estado de la categoría se cambió correctamente.</p>

        <?php elseif ($mensaje === "error"): ?>

            <p>Ocurrió un error al realizar la operación.</p>

        <?php elseif ($mensaje === "duplicada"): ?>

            <p>Ya existe una categoría con ese nombre.</p>

        <?php endif; ?>

        <h2>Registrar nueva categoría</h2>

        <form action="../procesos/guardar_categoria.php" method="POST" onsubmit="return validarCategoria()" novalidate>

            <label for="nombre" class="acomodarForm">Nombre de la categoría:</label>
            <input style="margin: -0.5rem -15rem 0 ; " type="text" id="nombre" name="nombre" required>

            <label class="acomodarForm" for="descripcion">Descripción:</label>
            <textarea style="margin: -0.5rem -15rem 0 ; " id="descripcion" name="descripcion"></textarea>

            <button type="submit" class="botonGuardar" >Guardar categoría</button>

        </form>

        <hr>

        <h2>Categorías registradas</h2>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Estado</th>
                    <th >Acciones</th>
                </tr>
            </thead>

            <Section>
                <?php while ($categoria = mysqli_fetch_assoc($consulta)): ?>
                    <tr>
                        <td class="acomodarForm">
                            <?php echo $categoria["id_categoria"]; ?>
                        </td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($categoria["nombre"]); ?>
                        </td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($categoria["descripcion"] ?? ""); ?>
                        </td>

                        <td class="acomodarForm">
                            <?php echo htmlspecialchars($categoria["estado"]); ?>
                        </td>

                        <td>
                            

                            <form class="nada" action="../procesos/cambiar_estado_categoria.php"method="POST" style="margin-left: 5rem; text-decoration: none; text-align:center; font-weight: bold;">
                                <input type="hidden" name="id_categoria" value="<?php echo $categoria["id_categoria"]; ?>">

                                <input type="hidden" name="estado" value="<?php echo $categoria["estado"] === "activo" ? "inactivo" : "activo"; ?>">

                                <button class="botonBonito" type="submit">
                                    <?php echo $categoria["estado"] === "activo" ? "Desactivar" : "Activar"; ?>
                                </button>

                               
                                
                            </form>

                            <a href="../procesos/editar_categoria.php?id=<?php echo $categoria["id_categoria"]; ?>" style="margin-left: 10rem; text-decoration: none; color: #1565c0; font-weight: bold;">
                                    Editar
                                </a>

                        </td>
                        
                    </tr>
                <?php endwhile; ?>
                </section>
        </table>

        <br>

        <a href="inicio.php" class="boton">Volver al panel principal</a>

    </main>

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
                        margin: 1rem auto auto;    
                        transition: background-color 0.3s, transform 0.2s;
                    }
                    </style>


</body>

<script>

function mostrarError(mensaje) {

    var contenedor = document.getElementById("mensajes");

    contenedor.innerHTML = "";

    var parrafo = document.createElement("p");

    parrafo.textContent = mensaje;

    parrafo.classList.add("error");

    contenedor.appendChild(parrafo);

}

function validarCategoria() {

    var nombre = document.getElementById("nombre").value.trim();

    if (nombre == "") {

        mostrarError("Escribe el nombre de la categoría.");

        return false;

    }

    var expresionNombre = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s.,()\/-]{3,100}$/;

    if (!expresionNombre.test(nombre)) {

        mostrarError("El nombre debe tener entre 3 y 100 caracteres y utilizar letras, números o signos permitidos.");

        return false;

    }

    return true;

}

</script>

</html>