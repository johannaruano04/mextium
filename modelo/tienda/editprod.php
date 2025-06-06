<?php
session_start();
include '../Usuario/config.php';

// Verifica sesión de tienda
if (!isset($_SESSION['tienda_id'])) {
    header("Location: ingretienda.php");
    exit;
}

// Obtiene el ID del producto
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header("Location: productos.php");
    exit;
}
$producto_id = intval($_GET['id']);
$tienda_id = $_SESSION['tienda_id'];

// Obtiene categorías predeterminadas
$categorias = [];
$sql = "SELECT id, nombre FROM categorias";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $categorias[] = $row;
}

// Obtiene datos del producto
$sql = "SELECT * FROM productos WHERE id = ? AND tienda_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ii", $producto_id, $tienda_id);
$stmt->execute();
$result = $stmt->get_result();
$producto = $result->fetch_assoc();
$stmt->close();

if (!$producto) {
    header("Location: productos.php");
    exit;
}

$mensaje = "";

// Procesa el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombreProducto']);
    $descripcion = trim($_POST['descripcionProducto']);
    $precio = floatval($_POST['precioProducto']);
    $stock = intval($_POST['stockProducto']);
    $estado = $_POST['estadoProducto'];
    $categoria_id = intval($_POST['categoriaProducto']);
    $imagen = $producto['imagen'];

    // Procesar nueva imagen si se subió
    if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] == 0) {
        $imagen = uniqid() . "_" . basename($_FILES['imagenProducto']['name']);
        $rutaDestino = "../../img/productos/" . $imagen;
        move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $rutaDestino);
        // Elimina la imagen anterior si existe
        if ($producto['imagen'] && file_exists("../../img/productos/" . $producto['imagen'])) {
            unlink("../../img/productos/" . $producto['imagen']);
        }
    }

    // Actualiza el producto
    $sql = "UPDATE productos SET nombre=?, descripcion=?, precio=?, stock=?, imagen=?, estado=?, categoria_id=? WHERE id=? AND tienda_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssdisiiii", $nombre, $descripcion, $precio, $stock, $imagen, $estado, $categoria_id, $producto_id, $tienda_id);

    if ($stmt->execute()) {
        header("Location: productos.php?editado=ok");
        exit;
    } else {
        $mensaje = "Error al actualizar el producto: " . $conn->error;
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Editar Producto - Mextium</title>
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
        .form-floating > label { left: 1.2rem; }
        .preview-img {
            max-width: 180px;
            max-height: 180px;
            border-radius: 10px;
            margin-top: 10px;
            display: block;
            object-fit: cover;
        }
    </style>
</head>
<body>
    <div class="cart-header">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">Editar Producto</div>
    </div>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                        <?php endif; ?>
                        <form method="POST" enctype="multipart/form-data" autocomplete="off">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Nombre del producto" value="<?php echo htmlspecialchars($producto['nombre']); ?>" required>
                                        <label for="nombreProducto">Nombre del producto</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <select class="form-select" id="categoriaProducto" name="categoriaProducto" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php foreach ($categorias as $cat): ?>
                                                <option value="<?php echo $cat['id']; ?>" <?php if ($producto['categoria_id'] == $cat['id']) echo 'selected'; ?>>
                                                    <?php echo htmlspecialchars($cat['nombre']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="categoriaProducto">Categoría</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="descripcionProducto" name="descripcionProducto" placeholder="Descripción" style="height: 100px" required><?php echo htmlspecialchars($producto['descripcion']); ?></textarea>
                                        <label for="descripcionProducto">Descripción</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="precioProducto" name="precioProducto" min="0" step="0.01" placeholder="Precio" value="<?php echo htmlspecialchars($producto['precio']); ?>" required>
                                        <label for="precioProducto">Precio ($)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="stockProducto" name="stockProducto" min="1" placeholder="Stock" value="<?php echo htmlspecialchars($producto['stock']); ?>" required>
                                        <label for="stockProducto">Stock disponible</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="estadoProducto" name="estadoProducto" required>
                                            <option value="Nuevo" <?php if ($producto['estado'] == 'Nuevo') echo 'selected'; ?>>Nuevo</option>
                                            <option value="Usado" <?php if ($producto['estado'] == 'Usado') echo 'selected'; ?>>Usado</option>
                                        </select>
                                        <label for="estadoProducto">Estado</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Imagen principal</label>
                                    <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" accept="image/*">
                                    <img id="previewImagenProducto" class="preview-img" src="<?php echo !empty($producto['imagen']) ? '/img/productos/' . htmlspecialchars($producto['imagen']) : '/img/no-image.png'; ?>" alt="Vista previa">
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-primary px-5 py-2">Guardar Cambios</button>
                                    <a href="productos.php" class="btn btn-secondary ms-2 px-4 py-2">Cancelar</a>
                                </div>
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
    // Previsualizar imagen seleccionada
    document.getElementById('imagenProducto').addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            var reader = new FileReader();
            reader.onload = function(ev) {
                var preview = document.getElementById('previewImagenProducto');
                preview.src = ev.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(e.target.files[0]);
        }
    });
    </script>
</body>
</html>