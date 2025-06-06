<?php
include '../Usuario/config.php';

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $correo = trim($_POST['correo']);
    $numero = trim($_POST['numero']);

    // Validación básica
    if (empty($correo) || empty($numero)) {
        $mensaje = "Por favor, ingresa tu correo y número de contacto.";
    } else {
        // Buscar tienda por correo y teléfono
        $sql = "SELECT * FROM tiendas WHERE correo = ? AND telefono = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $correo, $numero);
        $stmt->execute();
        $result = $stmt->get_result();
        $tienda = $result->fetch_assoc();

        if ($tienda) {
            session_start();
            $_SESSION['tienda_id'] = $tienda['id'];
            $_SESSION['tienda_nombre'] = $tienda['nombre'];
            header("Location: indextienda.php");
            exit;
        } else {
            $mensaje = "Datos incorrectos. Verifica tu correo y número.";
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Ingreso a Tienda</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(120deg, #e3f0ff 0%, #fffbe7 100%) !important;
        }
        .ingreso-box {
            background: #fff;
            border-radius: 18px;
            box-shadow: 0 0 24px #0001;
            padding: 2.5rem 2rem 2rem 2rem;
            max-width: 400px;
            margin: 60px auto;
        }
        .mextium-header {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
            color: #fff;
            padding: 24px 0 16px 0;
            margin-bottom: 24px;
            border-radius: 0 0 18px 18px;
            text-align: center;
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
        .btn-mextium {
            background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
            color: #fff;
            border: none;
            font-weight: 600;
        }
        .btn-mextium:hover {
            background: linear-gradient(90deg, #0056b3 0%, #e0a800 100%);
            color: #212529;
        }
        .form-group label {
            color: #007bff;
            font-weight: 500;
        }
    </style>
</head>
<body>
    <div class="mextium-header">
        <span class="logo-mex">Mex</span><span class="logo-tium">tium</span>
        <div class="mt-2" style="font-size:1.1rem;">Ingreso a tu Tienda</div>
    </div>
    <div class="ingreso-box">
        <h4 class="text-center mb-4 text-primary"><i class="fas fa-store"></i> Accede a tu Tienda</h4>
        <?php if (!empty($mensaje)): ?>
            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
        <?php endif; ?>
        <form method="POST">
            <div class="form-group">
                <label for="correo"><i class="fas fa-envelope"></i> Correo de la tienda</label>
                <input type="email" class="form-control" id="correo" name="correo" placeholder="Correo registrado" required>
            </div>
            <div class="form-group">
                <label for="numero"><i class="fas fa-phone"></i> Número de contacto</label>
                <input type="text" class="form-control" id="numero" name="numero" placeholder="Número registrado" required>
            </div>
            <button type="submit" class="btn btn-mextium btn-block mt-3">Ingresar</button>
        </form>
    </div>
</body>
</html>