<?php
session_start();
include '../Usuario/config.php';

// Si no hay sesión de tienda, redirige al login de tienda
if (!isset($_SESSION['tienda_id'])) {
    header("Location: ingretienda.php");
    exit;
}

// CATEGORÍAS PREDETERMINADAS
$categorias_predeterminadas = [
    ['id' => 1, 'nombre' => 'Ropa'],
    ['id' => 2, 'nombre' => 'Electrónica'],
    ['id' => 3, 'nombre' => 'Hogar'],
    ['id' => 4, 'nombre' => 'Juguetes'],
    ['id' => 5, 'nombre' => 'Deportes'],
    ['id' => 6, 'nombre' => 'Belleza'],
    ['id' => 7, 'nombre' => 'Libros'],
    ['id' => 8, 'nombre' => 'Mascotas'],
    ['id' => 9, 'nombre' => 'Consolas y Videojuegos'],
    ['id' => 10, 'nombre' => 'Herramientas'],
    ['id' => 11, 'nombre' => 'Alimentos y Bebidas'],
    ['id' => 12, 'nombre' => 'Salud y Cuidado Personal'],
    ['id' => 13, 'nombre' => 'Accesorios para Vehículos'],
    ['id' => 14, 'nombre' => 'Jardinería'],
    ['id' => 15, 'nombre' => 'Artículos de Oficina'],
    ['id' => 16, 'nombre' => 'Productos para Bebés']

];

// Puedes comentar o eliminar el bloque de agregar categorías nuevas si no lo necesitas aún

$categorias = $categorias_predeterminadas;

$mensaje = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_POST['nueva_categoria'])) {
    $nombre = trim($_POST['nombreProducto']);
    $descripcion = trim($_POST['descripcionProducto']);
    $precio = floatval($_POST['precioProducto']);
    $stock = intval($_POST['stockProducto']);
    $estado = $_POST['estadoProducto'];
    $categoria_id = intval($_POST['categoriaProducto']);
    $tienda_id = $_SESSION['tienda_id'];
    $imagen = "";

    // Validación básica
    if (
        empty($nombre) || empty($descripcion) || empty($precio) || empty($stock) ||
        empty($estado) || empty($categoria_id)
    ) {
        $mensaje = "Por favor, completa todos los campos obligatorios.";
    } else {
        // Procesar imagen si se subió
        if (isset($_FILES['imagenProducto']) && $_FILES['imagenProducto']['error'] == 0) {
            $imagen = uniqid() . "_" . basename($_FILES['imagenProducto']['name']);
            $rutaDestino = "../../img/productos/" . $imagen;
            move_uploaded_file($_FILES['imagenProducto']['tmp_name'], $rutaDestino);
        }

        // Insertar producto en la base de datos
        $sql = "INSERT INTO productos (nombre, descripcion, precio, stock, imagen, estado, categoria_id, tienda_id)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssdisiii", $nombre, $descripcion, $precio, $stock, $imagen, $estado, $categoria_id, $tienda_id);

        if ($stmt->execute()) {
            header("Location: productos.php?publicado=ok");
            exit;
        } else {
            $mensaje = "Error al publicar el producto: " . $conn->error;
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Publicar Producto - Mextium</title>
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
            display: none;
        }
    </style>
</head>
<body>
    <div class="cart-header">
        <span class="h1 text-uppercase logo-mex">Mex</span>
        <span class="h1 text-uppercase logo-tium">tium</span>
        <div class="mt-2">Publicar Nuevo Producto</div>
    </div>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-7">
                <div class="card shadow border-0">
                    <div class="card-body p-4">
                        <?php if (!empty($mensaje)): ?>
                            <div class="alert alert-danger"><?php echo $mensaje; ?></div>
                        <?php endif; ?>
                        <form id="formPublicarProducto" method="POST" enctype="multipart/form-data" autocomplete="off">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="form-floating">
                                        <input type="text" class="form-control" id="nombreProducto" name="nombreProducto" placeholder="Nombre del producto" required>
                                        <label for="nombreProducto">Nombre del producto</label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating d-flex align-items-center">
                                        <select class="form-select" id="categoriaProducto" name="categoriaProducto" required>
                                            <option value="">Selecciona una categoría</option>
                                            <?php foreach ($categorias as $categoria): ?>
                                                <option value="<?php echo $categoria['id']; ?>"><?php echo htmlspecialchars($categoria['nombre']); ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <label for="categoriaProducto">Categoría</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating">
                                        <textarea class="form-control" id="descripcionProducto" name="descripcionProducto" placeholder="Descripción" style="height: 100px" required></textarea>
                                        <label for="descripcionProducto">Descripción</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="precioProducto" name="precioProducto" min="0" step="0.01" placeholder="Precio" required>
                                        <label for="precioProducto">Precio ($)</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <input type="number" class="form-control" id="stockProducto" name="stockProducto" min="1" placeholder="Stock" required>
                                        <label for="stockProducto">Stock disponible</label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating">
                                        <select class="form-select" id="estadoProducto" name="estadoProducto" required>
                                            <option value="Nuevo">Nuevo</option>
                                            <option value="Usado">Usado</option>
                                        </select>
                                        <label for="estadoProducto">Estado</label>
                                    </div>
                                </div>
                                <div class="col-12">
                                    <label class="form-label">Imagen principal</label>
                                    <input type="file" class="form-control" id="imagenProducto" name="imagenProducto" accept="image/*" required>
                                    <img id="previewImagenProducto" class="preview-img" src="" alt="Vista previa">
                                </div>
                                <div class="col-12 text-center mt-3">
                                    <button type="submit" class="btn btn-success px-5 py-2">Publicar Producto</button>
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