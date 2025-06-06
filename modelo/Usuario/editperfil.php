<?php
session_start();
include 'config.php';

// Verifica sesión de usuario
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../login.php");
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$mensaje = "";

// Obtener datos actuales del usuario
$sql = "SELECT nombre, correo, telefono, foto FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$stmt->bind_result($nombre, $correo, $telefono, $foto);
$stmt->fetch();
$stmt->close();

// Procesar formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nuevo_nombre = trim($_POST['nombre']);
    $nuevo_correo = trim($_POST['email']);
    $nuevo_telefono = trim($_POST['telefono']);
    $nueva_foto = $foto;

    // Procesar nueva foto si se subió
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $nombreArchivo = uniqid() . "_" . basename($_FILES['foto']['name']);
        $rutaDestino = "../../img/perfiles/" . $nombreArchivo;
        if (!is_dir("../../img/perfiles/")) {
            mkdir("../../img/perfiles/", 0777, true);
        }
        if (move_uploaded_file($_FILES['foto']['tmp_name'], $rutaDestino)) {
            // Elimina la foto anterior si existe
            if ($foto && file_exists("../../img/perfiles/" . $foto)) {
                unlink("../../img/perfiles/" . $foto);
            }
            $nueva_foto = $nombreArchivo;
        }
    }

    $sql = "UPDATE usuarios SET nombre=?, correo=?, telefono=?, foto=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $nuevo_nombre, $nuevo_correo, $nuevo_telefono, $nueva_foto, $usuario_id);

    if ($stmt->execute()) {
        $mensaje = "Perfil actualizado correctamente.";
        $nombre = $nuevo_nombre;
        $correo = $nuevo_correo;
        $telefono = $nuevo_telefono;
        $foto = $nueva_foto;
    } else {
        $mensaje = "Error al actualizar el perfil: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Perfil - Mextium</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .perfil-header {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
            color: #fff;
            padding: 30px 0 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .perfil-header .logo-mex {
            color: #007bff;
            background: #212529;
            padding: 0 10px;
            border-radius: 5px 0 0 5px;
        }
        .perfil-header .logo-tium {
            color: #212529;
            background: #ffc107;
            padding: 0 10px;
            border-radius: 0 5px 5px 0;
            margin-left: -5px;
        }
        .perfil-foto {
            width: 120px;
            height: 120px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ffc107;
            margin-bottom: 15px;
            background: #fff;
        }
    </style>
</head>
<body>
    <div class="perfil-header">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">Editar Perfil</div>
    </div>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-info"><?php echo $mensaje; ?></div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data" autocomplete="off">
                            <div class="text-center mb-3">
                                <img src="<?php echo !empty($foto) ? '../../img/perfiles/' . htmlspecialchars($foto) : '../../img/no-image.png'; ?>" class="perfil-foto" id="previewFoto" alt="Foto de perfil">
                            </div>
                            <div class="mb-3">
                                <label for="foto" class="form-label">Foto de perfil</label>
                                <input type="file" class="form-control" id="foto" name="foto" accept="image/*">
                            </div>
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre completo</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($nombre); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Correo electrónico</label>
                                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($correo); ?>" required>
                            </div>
                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($telefono); ?>">
                            </div>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary px-5">Guardar Cambios</button>
                                <a href="../../index.php" class="btn btn-secondary ms-2">Cancelar</a>
                                <a href="index.php" class="btn btn-warning ms-2">Volver a inicio</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <footer class="text-center py-3 mt-4" style="background:#ffc107; color:#212529;">
        &copy; 2025 Mextium. Todos los derechos reservados.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Previsualizar foto seleccionada
    document.getElementById('foto').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                document.getElementById('previewFoto').src = ev.target.result;
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    </script>
</body>
</html>