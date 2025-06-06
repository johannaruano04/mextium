<?php
include '../Usuario/config.php';

// Validar ID recibido
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    echo "<div class='alert alert-danger text-center mt-5'>Producto no válido.</div>";
    exit;
}
$id = intval($_GET['id']);

var_dump($_GET['id']);

// Consulta para obtener los detalles del producto y su categoría
$sql = "SELECT p.nombre, p.descripcion, p.precio, p.stock, p.imagen, c.nombre AS categoria
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$stmt->bind_result($nombre, $descripcion, $precio, $stock, $imagen, $categoria);
$stmt->fetch();
$stmt->close();

if (empty($nombre)) {
    echo "<div class='alert alert-warning text-center mt-5'>Producto no encontrado.</div>";
    exit;
}

$ruta_img = (!empty($imagen) && file_exists($_SERVER['DOCUMENT_ROOT']."/img/productos/".$imagen))
    ? "/img/productos/" . htmlspecialchars($imagen)
    : "/img/no-image.png";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($nombre); ?> | Detalle de producto</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container py-5">
    <div class="row justify-content-center align-items-center">
        <div class="col-md-5 text-center mb-4 mb-md-0">
            <img src="<?php echo $ruta_img; ?>" alt="<?php echo htmlspecialchars($nombre); ?>" class="img-fluid rounded shadow" style="max-height:320px;">
        </div>
        <div class="col-md-7">
            <h2 class="mb-3"><?php echo htmlspecialchars($nombre); ?></h2>
            <span class="badge bg-info mb-2"><?php echo htmlspecialchars($categoria); ?></span>
            <h3 class="text-success mb-3">$<?php echo htmlspecialchars($precio); ?></h3>
            <p class="mb-4"><?php echo nl2br(htmlspecialchars($descripcion)); ?></p>
            <p><strong>Stock:</strong> <?php echo htmlspecialchars($stock); ?></p>
            <a href="#" class="btn btn-gradient-blue btn-lg">
                <i class="fa fa-shopping-cart me-2"></i> Comprar ahora
            </a>
            <a href="../Usuario/index.php" class="btn btn-outline-secondary btn-lg ms-2">Volver</a>
        </div>
    </div>
</div>
</body>
</html>