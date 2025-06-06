<?php
include '../Usuario/config.php'; // Ajusta la ruta si tu config.php está en otra carpeta

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombreTienda = trim($_POST['nombreTienda']);
    $descripcionTienda = trim($_POST['descripcionTienda']);
    $direccionTienda = trim($_POST['direccionTienda']);
    $telefonoTienda = trim($_POST['telefonoTienda']);
    $correoTienda = trim($_POST['correoTienda']);
    $logoTienda = ""; // Aquí puedes guardar el nombre del archivo si lo subes

    // Validación básica
    if (
        empty($nombreTienda) || empty($descripcionTienda) || empty($direccionTienda) ||
        empty($telefonoTienda) || empty($correoTienda)
    ) {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
    } else {
        // Procesar el logo si se subió
        if (isset($_FILES['logoTienda']) && $_FILES['logoTienda']['error'] == 0) {
            $logoTienda = uniqid() . "_" . basename($_FILES['logoTienda']['name']);
            $rutaDestino = "../../img/tiendas/" . $logoTienda; // Ajusta la ruta según tu estructura
            move_uploaded_file($_FILES['logoTienda']['tmp_name'], $rutaDestino);
        }

        // Insertar en la base de datos
        $sql = "INSERT INTO tiendas (nombre, descripcion, direccion, telefono, correo, logo) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssss", $nombreTienda, $descripcionTienda, $direccionTienda, $telefonoTienda, $correoTienda, $logoTienda);

        if ($stmt->execute()) {
            header("Location: ../vista/vendedor/indextienda.html?registro=exitoso");
            exit;
        } else {
            $mensaje = "Error al guardar la tienda: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Configuración de Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .mextium-header { background: linear-gradient(90deg, #ffc107 0%, #007bff 100%); color: #fff; padding: 30px 0 20px 0; margin-bottom: 30px; }
        .mextium-header .logo-mex { color: #007bff; background: #212529; padding: 0 10px; border-radius: 5px 0 0 5px; }
        .mextium-header .logo-tium { color: #212529; background: #ffc107; padding: 0 10px; border-radius: 0 5px 5px 0; margin-left: -5px; }
        .mextium-form { background: #fff; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.07); padding: 30px; }
        .btn-mextium { background: #007bff; color: #fff; border: none; }
        .btn-mextium:hover { background: #ffc107; color: #212529; }
        .form-group label { color: #007bff; font-weight: 500; }
        footer { background: #ffc107; color: #212529; }
    </style>
</head>
<body>
    <div class="mextium-header text-center">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">Configuración de tu Tienda</div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-7 col-lg-6">
                <?php if (!empty($mensaje)): ?>
                    <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                <?php endif; ?>
                <form class="mextium-form" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="nombreTienda"><i class="fas fa-store"></i> Nombre de la Tienda</label>
                        <input type="text" class="form-control" id="nombreTienda" name="nombreTienda" placeholder="Ingresa el nombre de tu tienda" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcionTienda"><i class="fas fa-align-left"></i> Descripción</label>
                        <textarea class="form-control" id="descripcionTienda" name="descripcionTienda" rows="3" placeholder="Describe tu tienda" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="direccionTienda"><i class="fas fa-map-marker-alt"></i> Dirección</label>
                        <input type="text" class="form-control" id="direccionTienda" name="direccionTienda" placeholder="Dirección física o virtual" required>
                    </div>
                    <div class="form-group">
                        <label for="telefonoTienda"><i class="fas fa-phone"></i> Teléfono</label>
                        <input type="tel" class="form-control" id="telefonoTienda" name="telefonoTienda" placeholder="Número de contacto" required>
                    </div>
                    <div class="form-group">
                        <label for="correoTienda"><i class="fas fa-envelope"></i> Correo Electrónico</label>
                        <input type="email" class="form-control" id="correoTienda" name="correoTienda" placeholder="Correo de la tienda" required>
                    </div>
                    <div class="form-group">
                        <label for="logoTienda"><i class="fas fa-image"></i> Logo de la Tienda</label>
                        <input type="file" class="form-control-file" id="logoTienda" name="logoTienda">
                    </div>
                    <button type="submit" class="btn btn-mextium btn-block mt-4">Guardar Cambios</button>
                </form>
            </div>
        </div>
    </div>
    <footer class="text-center py-3 mt-4">
        &copy; 2025 Mextium. Todos los derechos reservados.
    </footer>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>