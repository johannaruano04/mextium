<?php
session_start();
include '../Usuario/config.php';

// Validar ID de tienda recibido por GET
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: ../../index.php");
    exit;
}
$tienda_id = intval($_GET['id']);

// Obtener datos de la tienda
$sql = "SELECT nombre, descripcion, categoria, imagen FROM tiendas WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tienda_id);
$stmt->execute();
$stmt->bind_result($nombre_tienda, $descripcion_tienda, $categoria_tienda, $imagen_tienda);
$stmt->fetch();
$stmt->close();

if (empty($nombre_tienda)) {
    header("Location: ../../index.php");
    exit;
}

// Obtener productos de la tienda
$productos = [];
$sql = "SELECT nombre, descripcion, precio, stock, imagen, estado FROM productos WHERE tienda_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tienda_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();

// Asigna una clase de fondo según la categoría
switch (strtolower($categoria_tienda)) {
    case 'videojuegos':
    case 'consolas':
    case 'videojuegos y consolas':
        $bg_class = 'bg-videojuegos';
        break;
    case 'ropa de mujer':
        $bg_class = 'bg-ropa-mujer';
        break;
    case 'tecnología':
        $bg_class = 'bg-tecnologia';
        break;
    // Agrega más categorías si quieres
    default:
        $bg_class = 'bg-default';
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nombre_tienda); ?> - Mextium</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .tienda-header {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
            color: #fff;
            padding: 30px 0 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .tienda-header .logo-mex {
            color: #007bff;
            background: #212529;
            padding: 0 10px;
            border-radius: 5px 0 0 5px;
        }
        .tienda-header .logo-tium {
            color: #212529;
            background: #ffc107;
            padding: 0 10px;
            border-radius: 0 5px 5px 0;
            margin-left: -5px;
        }
        .tienda-img {
            width: 110px;
            height: 110px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #ffc107;
            background: #fff;
            margin-bottom: 10px;
        }
        .card-producto {
            transition: box-shadow .2s;
        }
        .card-producto:hover {
            box-shadow: 0 4px 20px rgba(0,0,0,0.12);
        }
        .preview-img {
            max-width: 180px;
            max-height: 180px;
            border-radius: 10px;
            margin: 0 auto 10px auto;
            display: block;
            object-fit: cover;
        }
        body.bg-videojuegos {
            background: linear-gradient(120deg, #1e3c72 0%, #2a5298 100%);
            color: #fff;
        }
        .tienda-header.bg-videojuegos {
            background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%);
        }
        body.bg-ropa-mujer {
            background: linear-gradient(120deg, #ffdde1 0%, #ee9ca7 100%);
        }
        .tienda-header.bg-ropa-mujer {
            background: linear-gradient(90deg, #ee9ca7 0%, #ffdde1 100%);
        }
        body.bg-tecnologia {
            background: linear-gradient(120deg, #e0eafc 0%, #cfdef3 100%);
        }
        .tienda-header.bg-tecnologia {
            background: linear-gradient(90deg, #2193b0 0%, #6dd5ed 100%);
        }
        body.bg-default {
            background: #f5f5f5;
        }
        .tienda-header.bg-default {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
        }
    </style>
</head>
<body class="<?php echo $bg_class; ?>">
    <div class="tienda-header <?php echo $bg_class; ?>">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">
            <?php if (!empty($imagen_tienda)): ?>
                <img src="../../img/tiendas/<?php echo htmlspecialchars($imagen_tienda); ?>" class="tienda-img" alt="Logo tienda">
            <?php endif; ?>
            <h2 class="fw-bold mt-2 mb-1"><?php echo htmlspecialchars($nombre_tienda); ?></h2>
            <div class="mb-2">
                <span class="badge bg-info text-dark"><?php echo htmlspecialchars($categoria_tienda); ?></span>
            </div>
            <p class="lead mb-0"><?php echo htmlspecialchars($descripcion_tienda); ?></p>
        </div>
    </div>
    <div class="container mb-5">
        <h3 class="mb-4 text-primary text-center">Productos de la tienda</h3>
        <div class="row g-4">
            <?php if (empty($productos)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center p-5 rounded shadow-sm">
                        <i class="fa fa-box-open fa-2x mb-2 text-primary"></i>
                        <h4 class="mb-2">¡Esta tienda aún no tiene productos publicados!</h4>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $prod): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-producto h-100 shadow-sm border-0">
                        <img src="<?php echo !empty($prod['imagen']) ? '../../img/productos/' . htmlspecialchars($prod['imagen']) : '../../img/no-image.png'; ?>"
                             class="preview-img mt-3"
                             alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                             onerror="this.onerror=null;this.src='../../img/no-image.png';">
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate mb-1"><?php echo htmlspecialchars($prod['nombre']); ?></h6>
                            <div class="mb-2">
                                <span class="badge bg-primary text-white">Stock: <?php echo htmlspecialchars($prod['stock']); ?></span>
                                <span class="badge bg-secondary text-white"><?php echo htmlspecialchars($prod['estado']); ?></span>
                            </div>
                            <h5 class="text-success mb-0">$<?php echo htmlspecialchars($prod['precio']); ?></h5>
                            <p class="small mt-2 text-muted"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="../../index.php" class="btn btn-warning px-4 py-2"><i class="fa fa-arrow-left me-2"></i>Volver al inicio</a>
        </div>
    </div>
    <footer class="text-center py-3 mt-4" style="background:#ffc107; color:#212529;">
        &copy; 2025 Mextium. Todos los derechos reservados.
    </footer>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>