<?php
session_start();
include '../Usuario/config.php';

// Si no hay sesión de tienda, redirige al login de tienda
if (!isset($_SESSION['tienda_id'])) {
    header("Location: ingretienda.php");
    exit;
}

// Eliminar producto si se recibe el parámetro GET
if (isset($_GET['eliminar']) && is_numeric($_GET['eliminar'])) {
    $idEliminar = intval($_GET['eliminar']);
    // Opcional: eliminar imagen del servidor
    $sqlImg = "SELECT imagen FROM productos WHERE id = ? AND tienda_id = ?";
    $stmtImg = $conn->prepare($sqlImg);
    $stmtImg->bind_param("ii", $idEliminar, $_SESSION['tienda_id']);
    $stmtImg->execute();
    $stmtImg->bind_result($imgFile);
    if ($stmtImg->fetch() && $imgFile && file_exists("../../img/productos/" . $imgFile)) {
        unlink("../../img/productos/" . $imgFile);
    }
    $stmtImg->close();

    // Eliminar producto de la base de datos
    $sqlDel = "DELETE FROM productos WHERE id = ? AND tienda_id = ?";
    $stmtDel = $conn->prepare($sqlDel);
    $stmtDel->bind_param("ii", $idEliminar, $_SESSION['tienda_id']);
    $stmtDel->execute();
    $stmtDel->close();

    header("Location: productos.php?eliminado=ok");
    exit;
}

// Obtener productos de la base de datos con el nombre de la categoría
$productos = [];
$tienda_id = $_SESSION['tienda_id'];
$sql = "SELECT p.id, p.nombre, p.descripcion, p.precio, p.stock, p.imagen, c.nombre AS categoria
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.tienda_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $tienda_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mis Productos - Mextium</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        body { background: #f5f5f5; }
        .cart-header {
            background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
            color: #fff;
            padding: 30px 0 20px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .cart-header .logo-mex {
            color: #007bff;
            background: #212529;
            padding: 0 10px;
            border-radius: 5px 0 0 5px;
        }
        .cart-header .logo-tium {
            color: #212529;
            background: #ffc107;
            padding: 0 10px;
            border-radius: 0 5px 5px 0;
            margin-left: -5px;
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
    </style>
</head>
<body>
    <div class="cart-header">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">Mis Productos</div>
    </div>
    <div class="container mb-5">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <a href="public.php" class="btn btn-success mb-3">
                    <i class="fa fa-plus"></i> Publicar nuevo producto
                </a>
            </div>
        </div>
        <div class="row" id="productosLista">
            <?php if (empty($productos)): ?>
                <div class="col-12">
                    <div class="alert alert-info text-center p-5 rounded shadow-sm">
                        <i class="fa fa-box-open fa-2x mb-2 text-primary"></i>
                        <h4 class="mb-2">¡Aún no tienes productos publicados!</h4>
                        <p>Publica tu primer producto y comienza a vender en Mextium.</p>
                        <a href="public.php" class="btn btn-success mt-2">
                            <i class="fa fa-plus"></i> Publicar producto
                        </a>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($productos as $prod): ?>
                <div class="col-lg-3 col-md-4 col-sm-6 pb-4">
                    <div class="card card-producto h-100 shadow-sm border-0">
                        <?php
                        $ruta_img = '/img/no-image.png';
                        if (!empty($prod['imagen'])) {
                            $ruta_absoluta = $_SERVER['DOCUMENT_ROOT'] . '/img/productos/' . $prod['imagen'];
                            if (file_exists($ruta_absoluta)) {
                                $ruta_img = '/img/productos/' . htmlspecialchars($prod['imagen']);
                            }
                        }
                        ?>
                        <img src="<?php echo $ruta_img; ?>"
                             class="preview-img mt-3"
                             alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                             onerror="this.onerror=null;this.src='/img/no-image.png';">
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate mb-1"><?php echo htmlspecialchars($prod['nombre']); ?></h6>
                            <div class="mb-2">
                                <span class="badge bg-primary text-white">Stock: <?php echo htmlspecialchars($prod['stock']); ?></span>
                                <span class="badge bg-secondary text-white"><?php echo htmlspecialchars($prod['categoria']); ?></span>
                            </div>
                            <h5 class="text-success mb-0">$<?php echo htmlspecialchars($prod['precio']); ?></h5>
                            <p class="small mt-2 text-muted"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
                        </div>
                        <div class="card-footer bg-white border-0 text-center">
                            <a href="editprod.php?id=<?php echo $prod['id']; ?>" class="btn btn-outline-warning btn-sm me-2">
                                <i class="fa fa-edit"></i> Editar
                            </a>
                            <a href="productos.php?eliminar=<?php echo $prod['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('¿Seguro que deseas eliminar este producto?');">
                                <i class="fa fa-trash"></i> Eliminar
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    <footer class="text-center py-3 mt-4" style="background:#ffc107; color:#212529;">
        &copy; 2025 Mextium. Todos los derechos reservados.
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- FontAwesome para los íconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css">
</body>
</html>