<?php
session_start();
include 'config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Busca el usuario
    $query = "SELECT * FROM usuarios WHERE correo = '$email'";
    $result = mysqli_query($conn, $query);
    $usuario = mysqli_fetch_assoc($result);

    if ($usuario && password_verify($password, $usuario['password'])) {
        // login exitoso
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_email'] = $usuario['correo'];
        $_SESSION['usuario_rol'] = $usuario['rol'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];

        header("Location: index.php");
        exit;
    } else {
        $mensaje = "Correo o contraseña incorrectos";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión | Mextium</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e3f0ff 0%, #fffbe7 100%) !important;
            min-height: 100vh;
        }
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 0 24px #0001;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 380px;
            width: 100%;
        }
        .logo-mex {
            color: #fff;
            background: #007bff;
            font-weight: bold;
            font-size: 2rem;
            border-radius: 8px 0 0 8px;
            padding: 0 12px;
        }
        .logo-tium {
            color: #212529;
            background: #ffc107;
            font-weight: bold;
            font-size: 2rem;
            border-radius: 0 8px 8px 0;
            margin-left: -5px;
            padding: 0 12px;
        }
        .form-control:focus {
            box-shadow: 0 0 0 2px #007bff33;
        }
        .btn-primary {
            background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
            border: none;
            font-weight: 600;
        }
        .btn-primary:hover {
            background: linear-gradient(90deg, #0056b3 0%, #e0a800 100%);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <div class="text-center mb-4">
                <span class="logo-mex">Mex</span><span class="logo-tium">tium</span>
                <div class="mt-2 mb-1 text-secondary" style="font-size:1.1rem;">Inicia sesión en tu cuenta</div>
            </div>
            <?php if (!empty($mensaje)): ?>
                <div class="alert alert-danger"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <div class="mb-3">
                    <label for="email" class="form-label">Correo electrónico</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="tucorreo@ejemplo.com" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password" class="form-control" id="password" name="password" placeholder="Contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary w-100 mb-2">Iniciar sesión</button>
            </form>
            <div class="text-center mt-3">
                <span>¿No tienes cuenta?</span>
                <a href="registro.php" class="text-primary text-decoration-none fw-bold">Regístrate</a>
            </div>
        </div>
    </div>
    <?php if (!isset($_SESSION['usuario_id'])): ?>
    <div id="auth-buttons" class="d-flex flex-column align-items-end mt-2 ms-4">
        <div class="d-flex gap-2">
            <a href="../cart.html" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-shopping-cart"></i> Carrito
            </a>
            <a href="login.php" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-sign-in-alt"></i> Ingreso
            </a>
            <a href="registro.php" class="btn btn-outline-primary btn-sm">
                <i class="fa fa-user-plus"></i> Crear cuenta
            </a>
        </div>
    </div>
    <?php endif; ?>
    <?php if (isset($_SESSION['usuario_id'])): ?>
    <div class="container mt-4">
        <a href="logout.php" class="btn btn-danger w-100 mt-3">
            <i class="fa fa-sign-out-alt me-2"></i> Cerrar sesión
        </a>
    </div>
    <?php endif; ?>
</body>
</html>