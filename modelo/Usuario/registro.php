<?php
require_once 'config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibe y limpia los datos
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $cedula = trim($_POST['cedula']); // <-- NUEVO
    $correo = trim($_POST['email']);
    $telefono = trim($_POST['telefono']);
    $direccion = trim($_POST['direccion']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $rol_id = $_POST['rol_id'];

    // Validación básica
    if (
        empty($nombre) || empty($apellido) || empty($cedula) || empty($correo) ||
        empty($telefono) || empty($direccion) ||
        empty($password) || empty($confirm_password) ||
        empty($rol_id)
    ) {
        $mensaje = "Por favor, completa todos los campos.";
    } elseif ($password !== $confirm_password) {
        $mensaje = "Las contraseñas no coinciden.";
    } else {
        // Verifica si el correo ya existe
        $sql = "SELECT id FROM usuario WHERE correo = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $correo);
        $stmt->execute();
        $stmt->store_result();
        if ($stmt->num_rows > 0) {
            $mensaje = "El correo ya está registrado.";
        } else {
            // Inserta el usuario
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $nombreCompleto = $nombre . " " . $apellido;
            $sql = "INSERT INTO usuarios (nombre, correo, telefono, direccion, password, rol_id, cedula) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssssis", $nombreCompleto, $correo, $telefono, $direccion, $password_hash, $rol_id, $cedula);
            if ($stmt->execute()) {
                header("Location: login.php?registro=exitoso");
                exit;
            } else {
                $mensaje = "Error al registrar: " . $conn->error;
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e3f0ff 0%, #fffbe7 100%) !important;
        }
        .registro-header {
            background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
            color: #fff;
            border-radius: 0 0 24px 24px;
            padding: 2rem 0 1rem 0;
            text-align: center;
            margin-bottom: 2rem;
        }
        .registro-header .logo-mex {
            color: #fff;
            background: #007bff;
            font-weight: bold;
            font-size: 2rem;
            border-radius: 8px 0 0 8px;
            padding: 0 12px;
        }
        .registro-header .logo-tium {
            color: #212529;
            background: #ffc107;
            font-weight: bold;
            font-size: 2rem;
            border-radius: 0 8px 8px 0;
            margin-left: -5px;
            padding: 0 12px;
        }
        .registro-header .slogan {
            color: #fffbe7;
            font-size: 1.1rem;
            margin-top: 0.5rem;
        }
        .form-registro {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 4px 24px rgba(0,123,255,0.10);
            padding: 2rem 2rem 1.5rem 2rem;
            margin: 0 auto;
            max-width: 500px;
        }
        .form-registro .btn-primary {
            background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
            border: none;
            color: #fff;
            font-weight: bold;
        }
        .form-registro .btn-primary:hover {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
            color: #fff;
        }
        .btn-volver {
            background: #ffc107;
            color: #212529;
            border: none;
            font-weight: bold;
        }
        .btn-volver:hover {
            background: #007bff;
            color: #fff;
        }
        .footer-mex {
            background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
            color: #fff;
            border-radius: 18px 18px 0 0;
            margin-top: 50px;
            padding: 30px 0 10px 0;
        }
        .footer-mex a { color: #fffbe7; }
        .footer-mex a:hover { color: #fff; text-decoration: underline; }
    </style>
</head>
<body>
    <!-- Encabezado personalizado -->
    <div class="registro-header">
        <span class="logo-mex">Mex</span>
        <span class="logo-tium">tium</span>
        <div class="slogan">¡Crea tu cuenta y comienza a disfrutar!</div>
    </div>

    <!-- Formulario de registro -->
    <div class="container">
        <form action="registro.php" method="POST" class="form-registro" id="registro-form">
            <h2 class="text-center mb-4 text-primary">Registro de Usuario</h2>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-warning"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            <div class="mb-3">
                <label for="nombre" class="form-label">Nombre</label>
                <input type="text" id="nombre" name="nombre" class="form-control" placeholder="Ingresa tu nombre" required>
            </div>
            <div class="mb-3">
                <label for="apellido" class="form-label">Apellido</label>
                <input type="text" id="apellido" name="apellido" class="form-control" placeholder="Ingresa tu apellido" required>
            </div>
            <div class="mb-3">
                <label for="telefono" class="form-label">Teléfono</label>
                <input type="text" id="telefono" name="telefono" class="form-control" placeholder="Ingresa tu teléfono" required>
            </div>
            <div class="mb-3">
                <label for="direccion" class="form-label">Dirección</label>
                <input type="text" id="direccion" name="direccion" class="form-control" placeholder="Ingresa tu dirección" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Correo Electrónico</label>
                <input type="email" id="email" name="email" class="form-control" placeholder="Ingresa tu correo electrónico" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" id="password" name="password" class="form-control" placeholder="Crea una contraseña" required>
            </div>
            <div class="mb-3">
                <label for="confirm-password" class="form-label">Confirmar Contraseña</label>
                <input type="password" id="confirm-password" name="confirm_password" class="form-control" placeholder="Confirma tu contraseña" required>
            </div>
            <div class="mb-3">
                <label for="rol_id" class="form-label">Rol</label>
                <select name="rol_id" class="form-control" required>
                    <option value="">Selecciona un rol</option>
                    <option value="1">Usuario</option>
                    <option value="2">Vendedor</option>
                    <option value="3">Administrador</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" id="cedula" name="cedula" class="form-control" placeholder="Ingresa tu cédula" required>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-2">Registrarse</button>
            <a href="../index.html" class="btn btn-volver w-100">Ir Atrás</a>
        </form>
    </div>

    <!-- Footer moderno -->
    <footer class="footer-mex text-center mt-5">
        <div class="container">
            <div class="mb-2">
                <a href="../../index.html" class="text-white me-3">Inicio</a>
                <a href="../shop.html" class="text-white me-3">Tienda</a>
                <a href="../cart.html" class="text-white me-3">Carrito</a>
                <a href="../productos/mis_productos.html" class="text-white me-3">Mis Productos</a>
                <a href="../productos/publicar_producto.html" class="text-white me-3">Publicar Producto</a>
                <a href="../Usuario/tienda.html" class="text-white me-3">Mi Tienda</a>
                <a href="../encabezados/acerca.html" class="text-white me-3">Acerca de</a>
                <a href="../encabezados/contactos.html" class="text-white me-3">Contactos</a>
                <a href="../encabezados/ayuda.html" class="text-white me-3">Ayuda</a>
                <a href="../encabezados/FAQs.html" class="text-white">FAQs</a>
            </div>
            <div>
                &copy; 2025 <b>Mextium</b>. Todos los derechos reservados.
            </div>
        </div>
    </footer>
</body>
</html>