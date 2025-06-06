<?php
session_start();
$usuario_nombre = isset($_SESSION['usuario_nombre']) ? $_SESSION['usuario_nombre'] : '';

// Conexión a la base de datos (ajusta según tu configuración)
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "mextium";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Redirigir a la tienda correspondiente si el usuario ya tiene una tienda registrada
$irTiendaUrl = "../tienda/conftienda.php"; // Por defecto, formulario de registro de tienda
if (isset($_SESSION['usuario_email']) || isset($_SESSION['usuario_telefono'])) {
    $email = $_SESSION['usuario_email'] ?? '';
    $telefono = $_SESSION['usuario_telefono'] ?? '';
    $sql = "SELECT id FROM tiendas WHERE correo = ? OR telefono = ? LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $email, $telefono);
    $stmt->execute();
    $stmt->bind_result($tienda_id);
    if ($stmt->fetch()) {
        // Si existe tienda, ir al panel de tienda
        $irTiendaUrl = "../tienda/indextienda.php?tienda_id=" . $tienda_id;
    } else {
        // Si NO existe tienda, ir al registro de tienda
        $irTiendaUrl = "../tienda/conftienda.php";
    }
    $stmt->close();
}

// Obtener productos reales
$productos_destacados = [];
$sql = "SELECT p.nombre, p.precio, p.stock, c.nombre AS categoria, p.estado, p.descripcion, p.imagen
        FROM productos p
        LEFT JOIN categorias c ON p.categoria_id = c.id
        ORDER BY p.id DESC LIMIT 12";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $productos_destacados[] = $row;
}

// Obtener tiendas reales
$tiendas_mas_buscadas = [];
$sql = "SELECT id, nombre, categoria, descripcion, imagen FROM tiendas ORDER BY id DESC LIMIT 12";
$res = $conn->query($sql);
while ($row = $res->fetch_assoc()) {
    $tiendas_mas_buscadas[] = $row;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mextium | Inicio</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Bootstrap y FontAwesome -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.10.0/css/all.min.css" rel="stylesheet">
    <style>
body {
    background: linear-gradient(120deg, #e3f0ff 0%, #fffbe7 100%) !important;
    font-family: 'Nunito', Arial, sans-serif;
    position: relative;
    min-height: 100vh;
    overflow-x: hidden;
}
body::before {
    content: "";
    position: fixed;
    top: -100px; left: -100px;
    width: 140vw; height: 140vh;
    background: radial-gradient(circle at 20% 30%, #ffc10755 0, #fff0 60%),
                radial-gradient(circle at 80% 70%, #007bff33 0, #fff0 60%);
    z-index: 0;
    pointer-events: none;
    animation: fondoAnimado 12s linear infinite alternate;
}
@keyframes fondoAnimado {
    0% { transform: scale(1) translateY(0);}
    100% { transform: scale(1.1) translateY(-30px);}
}
.main-header {
    background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
    color: #fff;
    border-radius: 0 0 24px 24px;
    margin-bottom: 2rem;
    box-shadow: 0 8px 32px rgba(0,0,0,0.10);
    position: relative;
    overflow: hidden;
}
.main-header::after {
    content: "";
    position: absolute;
    right: -80px; top: -60px;
    width: 200px; height: 200px;
    background: radial-gradient(circle, #fffbe7 0 60%, #fff0 100%);
    opacity: .25;
    z-index: 0;
    animation: headerGlow 6s ease-in-out infinite alternate;
}
@keyframes headerGlow {
    0% { filter: blur(0px);}
    100% { filter: blur(8px);}
}
.logo-mex, .logo-tium {
    font-weight: bold;
    font-size: 2rem;
    letter-spacing: 2px;
    box-shadow: 0 2px 8px rgba(0,123,255,0.08);
}
.logo-mex {
    color: #fff;
    background: linear-gradient(90deg,#007bff 60%,#0056b3 100%);
    border-radius: 8px 0 0 8px;
    padding: 0 12px;
    text-shadow: 0 2px 8px #007bff44;
    animation: mexGlow 2.5s infinite alternate;
}
@keyframes mexGlow {
    0% { filter: brightness(1);}
    100% { filter: brightness(1.15);}
}
.logo-tium {
    color: #212529;
    background: linear-gradient(90deg,#ffc107 60%,#fffbe7 100%);
    border-radius: 0 8px 8px 0;
    margin-left: -5px;
    padding: 0 12px;
    text-shadow: 0 2px 8px #ffc10744;
    animation: tiumGlow 2.5s infinite alternate-reverse;
}
@keyframes tiumGlow {
    0% { filter: brightness(1);}
    100% { filter: brightness(1.12);}
}
.section-title {
    font-weight: bold;
    color: #007bff;
    letter-spacing: 2px;
    text-shadow: 0 2px 8px #007bff22;
    position: relative;
    z-index: 1;
}
.card-producto {
    background: rgba(255,255,255,0.85);
    border-radius: 18px;
    box-shadow: 0 8px 32px rgba(0,0,0,0.13);
    border: 1px solid rgba(255,255,255,0.25);
    position: relative;
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.card-producto:hover {
    box-shadow: 0 12px 40px 0 #ffc10755, 0 2px 16px rgba(0,0,0,0.18);
    transform: translateY(-6px) scale(1.03) rotate(-1deg);
    border-color: #ffc10799;
    z-index: 2;
}
.card-producto .card-img-top {
    transition: transform .3s cubic-bezier(.4,2,.6,1), box-shadow .3s;
    box-shadow: 0 2px 16px rgba(0,0,0,0.13);
}
.card-producto:hover .card-img-top {
    transform: scale(1.09) rotate(-2deg);
    box-shadow: 0 8px 32px #ffc10755;
}
.badge, .ribbon span {
    animation: badgePulse 2s infinite alternate;
}
@keyframes badgePulse {
    0% { filter: brightness(1);}
    100% { filter: brightness(1.18);}
}
.ribbon {
    width: 90px;
    height: 90px;
    overflow: hidden;
    position: absolute;
    z-index: 2;
    pointer-events: none;
}
.ribbon-top-right {
    top: -10px;
    right: -10px;
}
.ribbon-top-left {
    top: -10px;
    left: -10px;
}
.ribbon span {
    position: absolute;
    display: block;
    width: 120px;
    padding: 6px 0;
    background: linear-gradient(90deg,#ff9800 0%,#ffc107 100%);
    color: #fff;
    font-weight: bold;
    text-align: center;
    transform: rotate(45deg);
    box-shadow: 0 2px 8px rgba(0,0,0,0.13);
    font-size: 0.95rem;
    left: -30px;
    top: 20px;
    letter-spacing: 1px;
    text-shadow: 0 2px 8px #ff980044;
}
.fa-bounce, .fa-beat, .fa-fade, .fa-spin, .fa-shake {
    animation-duration: 1.2s;
    animation-iteration-count: infinite;
}
.promo-banner {
    background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
    color: #212529;
    border-radius: 12px;
    padding: 1.5rem 1rem;
    margin-bottom: 2rem;
    text-align: center;
    font-size: 1.25rem;
    font-weight: 500;
    box-shadow: 0 2px 8px rgba(0,123,255,0.08);
    animation: bannerGlow 3s infinite alternate;
}
@keyframes bannerGlow {
    0% { filter: brightness(1);}
    100% { filter: brightness(1.08);}
}
.btn-gradient-blue {
    background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
    color: #fff;
    border: none;
    transition: box-shadow .2s, background .2s;
    box-shadow: 0 2px 8px rgba(0,123,255,0.08);
}
.btn-gradient-blue:hover {
    background: linear-gradient(90deg, #ffc107 0%, #007bff 100%);
    color: #fff;
    box-shadow: 0 4px 16px #ffc10755;
}
.footer-mex {
    background: linear-gradient(90deg, #007bff 0%, #ffc107 100%);
    color: #fff;
    border-radius: 18px 18px 0 0;
    margin-top: 50px;
    padding: 30px 0 10px 0;
    box-shadow: 0 -2px 16px #007bff22;
}
.footer-mex a { color: #fffbe7; }
.footer-mex a:hover { color: #fff; text-decoration: underline; }
.testimonial {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
}
.testimonial .fa-quote-left {
    color: #ffc107;
    font-size: 1.5rem;
    animation: quoteGlow 2s infinite alternate;
}
@keyframes quoteGlow {
    0% { filter: drop-shadow(0 0 0 #ffc107);}
    100% { filter: drop-shadow(0 0 8px #ffc107);}
}
.gif-hover-zoom {
    transition: transform 0.25s cubic-bezier(.4,2,.6,1);
    will-change: transform;
}
.gif-hover-zoom:hover {
    transform: scale(1.13) rotate(-2deg);
    z-index: 2;
    box-shadow: 0 8px 32px rgba(0,0,0,0.18);
}
.social-bar a {
    transition: transform .2s, filter .2s;
}
.social-bar a:hover {
    transform: scale(1.18) rotate(-6deg);
    filter: brightness(1.2) drop-shadow(0 2px 8px #ffc10788);
}
::-webkit-scrollbar-thumb {
    background: linear-gradient(90deg,#ffc107 0,#007bff 100%);
    border-radius: 8px;
}
::-webkit-scrollbar {
    width: 8px;
    background: #fffbe7;
}
    </style>
</head>
<body>
    <!-- Encabezado principal -->
    <header class="main-header py-5 mb-4 position-relative">
        <div class="d-flex justify-content-between align-items-start">
            <div class="d-flex align-items-center">
                <!-- Botón de menú tipo hamburguesa -->
                <button class="btn btn-link p-0 me-3 d-flex flex-column justify-content-center align-items-center" id="menu-toggle" style="border:none;outline:none;background:transparent;">
                    <span style="display:block;width:28px;height:4px;background:#fff;border-radius:2px;margin-bottom:5px;"></span>
                    <span style="display:block;width:28px;height:4px;background:#fff;border-radius:2px;margin-bottom:5px;"></span>
                    <span style="display:block;width:28px;height:4px;background:#fff;border-radius:2px;"></span>
                </button>
                <span class="logo-mex px-3 py-1 rounded-start">Mex</span>
                <span class="logo-tium px-3 py-1 rounded-end">tium</span>
            </div>
            <!-- Barra de búsqueda entre el logo y el selector de ciudad -->
            <form class="mx-4 flex-grow-1" style="max-width:700px; min-width:250px;" role="search">
                <div class="input-group input-group-lg">
                    <input type="search" class="form-control" id="busqueda-mextium" placeholder="Buscar productos, tiendas, categorías..." aria-label="Buscar">
                    <button class="btn btn-warning" type="submit">
                        <i class="fa fa-search"></i>
                    </button>
                </div>
                <ul id="historial-busqueda" class="list-group position-absolute w-100" style="z-index:999; display:none; max-height:200px; overflow-y:auto;"></ul>
            </form>
        </div>
        <!-- Nombre de usuario centrado, grande y blanco -->
        <div id="usuario-nombre" class="text-center fw-bold" style="color: #fff; font-size: 2rem; margin-top: 1.5rem;">
            <?php if (isset($_SESSION['usuario_nombre'])) echo htmlspecialchars($_SESSION['usuario_nombre']); ?>
        </div>
    </header>

    <!-- Sidebar lateral oculto -->
    <div id="sidebar-menu" class="position-fixed top-0 start-0 h-100 bg-white shadow" style="width:320px;z-index:1050;transform:translateX(-100%);transition:transform .3s;">
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-user-circle me-2"></i>Ingreso</h5>
            <button class="btn btn-sm btn-light" id="sidebar-close" aria-label="Cerrar"><i class="fa fa-times"></i></button>
        </div>
        <div class="p-4">
            <?php if (!isset($_SESSION['usuario_id'])): ?>
                <p class="mb-4">Accede a tu cuenta para comprar, vender y ver tus pedidos.</p>
                <a href="login.php" class="btn btn-gradient-blue w-100 mb-3">
                    <i class="fa fa-sign-in-alt me-2"></i> Iniciar sesión
                </a>
                <a href="registro.php" class="btn btn-outline-primary w-100 mb-3">
                    <i class="fa fa-user-plus me-2"></i> Crear cuenta
                </a>
            <?php else: ?>
                <?php
                    // Obtener la foto de perfil del usuario
                    $foto_perfil = null;
                    if (isset($_SESSION['usuario_id'])) {
                        $sql_foto = "SELECT foto FROM usuarios WHERE id = ?";
                        $stmt_foto = $conn->prepare($sql_foto);
                        $stmt_foto->bind_param("i", $_SESSION['usuario_id']);
                        $stmt_foto->execute();
                        $stmt_foto->bind_result($foto_perfil);
                        $stmt_foto->fetch();
                        $stmt_foto->close();
                    }
                    $ruta_foto = (!empty($foto_perfil) && file_exists("../../img/perfiles/" . $foto_perfil))
                        ? "../../img/perfiles/" . htmlspecialchars($foto_perfil)
                        : "../../img/no-image.png";
                ?>
                <div class="mb-4 text-center">
                    <img src="<?php echo $ruta_foto; ?>" alt="Foto de perfil" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:2px solid #ffc107;margin-bottom:10px;">
                    <div class="fw-bold fs-5"><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></div>
                    <div class="text-muted small"><?php echo htmlspecialchars($_SESSION['usuario_email']); ?></div>
                </div>
            <?php endif; ?>
            <hr>
            <h6 class="fw-bold text-primary mt-4">Tu cuenta</h6>
            <div id="cuenta-info" class="mb-3">
                <!-- Información de la cuenta del usuario (nombre, email, etc.) -->
            </div>
            <a href="../Usuario/editperfil.php" class="btn btn-link text-primary w-100 text-start">
                <i class="fa fa-user-edit me-2"></i> Editar perfil
            </a>
            <a href="mis_pedidos.html" class="btn btn-link text-primary w-100 text-start">
                <i class="fa fa-box-open me-2"></i> Mis pedidos
            </a>
            <a href="../tienda/productos.php" class="btn btn-link text-primary w-100 text-start">
                <i class="fa fa-tags me-2"></i> Mis productos
            </a>
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <a href="logout.php" class="btn btn-danger w-100 mt-3">
                    <i class="fa fa-sign-out-alt me-2"></i> Cerrar sesión
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Banner promocional -->
    <div class="container">
        <div class="promo-banner mb-4">
            <i class="fa fa-gift me-2"></i>
            ¡Envío gratis en compras mayores a $199.900! Solo por tiempo limitado.
        </div>
    </div>

    <!-- Sección de unirse como vendedor -->
<section class="container mb-5">
    <h2 class="section-title text-center mb-4">ÚNETE</h2>
    <div class="row justify-content-center align-items-end g-4 mb-3">
        <div class="col-12 col-md-4 text-center">
            <img src="../img/entrega.gif" alt="Marca 1" style="max-width: 100px;" class="gif-hover-zoom">
            <div class="card shadow-sm mt-3 py-4 h-100 border-0">
                <h5 class="fw-bold mb-3">Únete como vendedor</h5>
                <a href="<?php echo $irTiendaUrl; ?>" class="btn btn-gradient-blue">Ingresar</a>
            </div>
        </div>
        <div class="col-12 col-md-4 text-center">
            <img src="../img/muy-pronto.gif" alt="Marca 2" style="max-width: 100px;" class="gif-hover-zoom">
            <div class="card shadow-sm mt-3 py-4 h-100 border-0">
                <h5 class="fw-bold mb-3 text-muted">Próximamente</h5>
                <a href="#" class="btn btn-gradient-blue disabled" tabindex="-1" aria-disabled="true">Ingresar</a>
            </div>
        </div>
        <div class="col-12 col-md-4 text-center">
            <img src="../img/muy-pronto.gif" alt="Marca 3" style="max-width: 100px;" class="gif-hover-zoom">
            <div class="card shadow-sm mt-3 py-4 h-100 border-0">
                <h5 class="fw-bold mb-3 text-muted">Próximamente</h5>
                <a href="#" class="btn btn-gradient-blue disabled" tabindex="-1" aria-disabled="true">Ingresar</a>
            </div>
        </div>
    </div>
</section>


<!-- Navegación rápida -->
<nav class="container mb-5">
    <div class="card shadow-sm p-4">
        <h5 class="mb-3 text-primary"><i class="fa fa-th-large"></i> Categorías</h5>
        <div class="row row-cols-1 row-cols-md-3 g-3">
            <div class="col">
                <a href="categorias/ropa.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-tshirt"></i> Ropa
                </a>
            </div>
            <div class="col">
                <a href="categorias/tecnologia.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-laptop"></i> Tecnología
                </a>
            </div>
            <div class="col">
                <a href="categorias/hogar.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-couch"></i> Hogar y Muebles
                </a>
            </div>
            <div class="col">
                <a href="categorias/deportes.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-futbol"></i> Deportes
                </a>
            </div>
            <div class="col">
                <a href="categorias/electrodomesticos.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-blender"></i> Electrodomésticos
                </a>
            </div>
            <div class="col">
                <a href="categorias/juegos_juguetes.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-puzzle-piece"></i> Juegos y Juguetes
                </a>
            </div>
            <div class="col">
                <a href="categorias/mascotas.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-paw"></i> Mascotas
                </a>
            </div>
            <div class="col">
                <a href="categorias/papeleria.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-pencil-alt"></i> Papelería
                </a>
            </div>
            <div class="col">
                <a href="categorias/salud.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-heartbeat"></i> Salud y Belleza
                </a>
            </div>
            <div class="col">
                <a href="categorias/supermercado.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-shopping-basket"></i> Supermercado
                </a>
            </div>
            <div class="col">
                <a href="categorias/.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-store-alt"></i> pronto...
                </a>
            </div>
            <div class="col">
                <a href="categorias.html" class="btn btn-outline-primary w-100 mb-2">
                    <i class="fa fa-store-alt"></i> pronto...
                </a>
            </div>
            <!-- Agrega aquí más categorías si existen en tu carpeta "categorias" -->
        </div>
    </div>
</nav>

    <!-- Productos Destacados con glassmorphism y cintas -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4" style="letter-spacing:2px; position:relative;">
            <i class="fa fa-star text-warning me-2 fa-bounce"></i>Productos Destacados
                <i class="fa fa-magic fa-spin text-primary"></i>
            </span>
        </h2>
        <div class="row g-4" id="productosDestacados">
            <?php if (empty($productos_destacados)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center p-5 rounded shadow-sm glass-card">
                    <i class="fa fa-box-open fa-2x mb-2 text-warning fa-shake"></i>
                    <h4 class="mb-2">No hay productos para mostrar</h4>
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($productos_destacados as $prod): ?>
    <div class="col-lg-3 col-md-4 col-sm-6">
        <div class="card shadow-lg border-0 h-100 card-producto position-relative overflow-hidden glass-card" style="transition:transform .2s;">
            <?php
                $ruta_img = (!empty($prod['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT']."/img/productos/".$prod['imagen']))
                    ? "/img/productos/" . htmlspecialchars($prod['imagen'])
                    : "/img/no-image.png";
                $es_nuevo = (strtotime($prod['estado'] ?? '') > strtotime('-7 days')) ? true : false;
            ?>
            <?php if ($es_nuevo): ?>
                <span class="ribbon ribbon-top-right"><span>Nuevo</span></span>
            <?php endif; ?>
            <div class="bg-light d-flex align-items-center justify-content-center" style="height:200px;">
                <img class="card-img-top gif-hover-zoom" src="<?php echo $ruta_img; ?>"
                     alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                     style="max-width:160px;max-height:160px;object-fit:cover;border-radius:18px;box-shadow:0 2px 16px rgba(0,0,0,0.13);"
                     onerror="this.onerror=null;this.src='/img/no-image.png';">
            </div>
            <div class="card-body text-center">
                <h6 class="card-title text-truncate mb-2" style="font-weight:700;font-size:1.1rem;">
                    <?php echo htmlspecialchars($prod['nombre']); ?>
                    <?php if(rand(0,10) > 8): // Easter egg ?>
                        <i class="fa fa-rocket text-danger fa-beat"></i>
                    <?php endif; ?>
                </h6>
                <div class="mb-2">
                    <span class="badge bg-gradient bg-warning text-dark me-1"><i class="fa fa-box"></i> Stock: <?php echo htmlspecialchars($prod['stock']); ?></span>
                    <span class="badge bg-gradient bg-info text-white"><i class="fa fa-tag"></i> <?php echo htmlspecialchars($prod['categoria']); ?></span>
                </div>
                <h5 class="text-success mb-0 fw-bold">$<?php echo htmlspecialchars($prod['precio']); ?></h5>
                <p class="small mt-2 text-muted"><?php echo htmlspecialchars($prod['descripcion']); ?></p>
            </div>
            <div class="position-absolute top-0 end-0 m-2">
                <span class="badge bg-primary text-white shadow-sm" style="font-size:0.9rem;">
                    <i class="fa fa-bolt fa-fade"></i> <?php echo htmlspecialchars($prod['estado']); ?>
                </span>
            </div>
            <!-- Botón Comprar en cada producto -->
            <div class="card-footer bg-white border-0 text-center">
                <a href="../tienda/detalle.php?id=<?php echo urlencode($prod['id'] ?? ''); ?>" class="btn btn-gradient-blue w-100">
                    <i class="fa fa-shopping-cart me-2"></i> Comprar
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Tiendas Más Buscadas with glassmorphism and animations -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4" style="letter-spacing:2px; position:relative;">
            <i class="fa fa-store text-primary me-2 fa-beat"></i>Tiendas Más Buscadas
            <span style="position:absolute;left:0;top:-10px;">
                <i class="fa fa-fire text-danger fa-fade"></i>
            </span>
        </h2>
        <div class="row g-4" id="tiendasMasBuscadas">
            <?php if (empty($tiendas_mas_buscadas)): ?>
            <div class="col-12">
                <div class="alert alert-warning text-center p-5 rounded shadow-sm glass-card">
                    <i class="fa fa-store fa-2x mb-2 text-warning fa-shake"></i>
                    <h4 class="mb-2">No hay tiendas para mostrar</h4>
                </div>
            </div>
            <?php else: ?>
                <?php foreach ($tiendas_mas_buscadas as $tienda): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card shadow-lg border-0 h-100 card-producto position-relative overflow-hidden glass-card" style="transition:transform .2s;">
                        <?php
                            $ruta_tienda = (!empty($tienda['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT']."/img/tiendas/".$tienda['imagen']))
                                ? '/img/tiendas/' . htmlspecialchars($tienda['imagen'])
                                : '/img/no-image.png';
                        ?>
                        <?php if(rand(0,10) > 8): // Easter egg ?>
                            <span class="ribbon ribbon-top-left"><span>🔥 Top</span></span>
                        <?php endif; ?>
                        <div class="d-flex align-items-center justify-content-center" style="height:140px;">
                            <img class="card-img-top gif-hover-zoom"
                                 src="<?php echo $ruta_tienda; ?>"
                                 alt="Logo tienda"
                                 style="width:90px;height:90px;object-fit:cover;border-radius:50%;border:3px solid #ffc107;box-shadow:0 2px 8px rgba(0,0,0,0.13);">
                        </div>
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate mb-2" style="font-weight:700;font-size:1.1rem;">
                                <?php echo htmlspecialchars($tienda['nombre']); ?>
                                <?php if(rand(0,10) > 8): ?>
                                    <i class="fa fa-crown text-warning fa-beat"></i>
                                <?php endif; ?>
                            </h6>
                            <div class="mb-2">
                                <span class="badge bg-info text-white"><i class="fa fa-tag"></i> <?php echo htmlspecialchars($tienda['categoria']); ?></span>
                            </div>
                            <p class="small mt-2 text-muted"><?php echo htmlspecialchars($tienda['descripcion']); ?></p>
                        </div>
                        <div class="card-footer bg-white border-0 text-center">
                            <a href="../tienda/vertienda.php?id=<?php echo $tienda['id']; ?>" class="btn btn-outline-primary btn-sm px-3 rounded-pill shadow-sm">
                                <i class="fa fa-store"></i> Ver tienda
                            </a>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>

    <!-- Testimonios de clientes -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">Lo que opinan nuestros clientes</h2>
        <div class="row justify-content-center">
            <div class="col-md-4">
                <div class="testimonial">
                    <i class="fa fa-quote-left"></i>
                    <p class="mt-3 mb-2">¡Excelente plataforma! Vendí mis productos rápido y sin complicaciones.</p>
                    <div class="user">- Ana G.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial">
                    <i class="fa fa-quote-left"></i>
                    <p class="mt-3 mb-2">La atención al cliente es muy buena y el envío fue rápido.</p>
                    <div class="user">- Luis P.</div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="testimonial">
                    <i class="fa fa-quote-left"></i>
                    <p class="mt-3 mb-2">Gran variedad de productos y ofertas. ¡Recomendado!</p>
                    <div class="user">- Sofía R.</div>
                </div>
            </div>
        </div>
    </section>
        <!-- Productos vistos recientemente -->
<section class="container mb-5">
    <h2 class="section-title text-center mb-4">
        <i class="fa fa-history text-secondary me-2"></i>Productos vistos recientemente
    </h2>
    <div class="row g-4" id="productosRecientes">
        <?php
        // Ejemplo: puedes reemplazar esto por tu lógica real de productos vistos
        $productos_recientes = []; // Aquí deberías cargar los productos vistos por el usuario
        // Ejemplo de productos de prueba (elimina esto si tienes tu propia lógica)
        // $productos_recientes = array_slice($productos_destacados, 0, 4);

        if (empty($productos_recientes)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-4 rounded shadow-sm">
                    <i class="fa fa-eye-slash fa-2x mb-2 text-secondary"></i>
                    <h5 class="mb-2">Aún no has visto productos recientemente</h5>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($productos_recientes as $prod): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-producto h-100 shadow-sm border-0">
                        <?php
                        $ruta_img = (!empty($prod['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT']."/img/productos/".$prod['imagen']))
                            ? "/img/productos/" . htmlspecialchars($prod['imagen'])
                            : "/img/no-image.png";
                        ?>
                        <img src="<?php echo $ruta_img; ?>"
                             class="card-img-top gif-hover-zoom"
                             alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                             style="max-width:160px;max-height:160px;object-fit:cover;border-radius:12px;margin:0 auto 10px auto;"
                             onerror="this.onerror=null;this.src='/img/no-image.png';">
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate mb-2"><?php echo htmlspecialchars($prod['nombre']); ?></h6>
                            <h5 class="text-success mb-0">$<?php echo htmlspecialchars($prod['precio']); ?></h5>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Productos que podrían interesarte -->
<section class="container mb-5">
    <h2 class="section-title text-center mb-4">
        <i class="fa fa-lightbulb text-warning me-2"></i>Productos que podrían interesarte
    </h2>
    <div class="row g-4" id="productosInteres">
        <?php
        // Ejemplo: puedes reemplazar esto por tu lógica real de recomendaciones
        $productos_interes = []; // Aquí deberías cargar productos recomendados para el usuario
        // Ejemplo de productos de prueba (elimina esto si tienes tu propia lógica)
        // $productos_interes = array_slice($productos_destacados, 4, 4);

        if (empty($productos_interes)): ?>
            <div class="col-12">
                <div class="alert alert-info text-center p-4 rounded shadow-sm">
                    <i class="fa fa-lightbulb fa-2x mb-2 text-warning"></i>
                    <h5 class="mb-2">¡Pronto verás recomendaciones personalizadas aquí!</h5>
                </div>
            </div>
        <?php else: ?>
            <?php foreach ($productos_interes as $prod): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-producto h-100 shadow-sm border-0">
                        <?php
                        $ruta_img = (!empty($prod['imagen']) && file_exists($_SERVER['DOCUMENT_ROOT']."/img/productos/".$prod['imagen']))
                            ? "/img/productos/" . htmlspecialchars($prod['imagen'])
                            : "/img/no-image.png";
                        ?>
                        <img src="<?php echo $ruta_img; ?>"
                             class="card-img-top gif-hover-zoom"
                             alt="<?php echo htmlspecialchars($prod['nombre']); ?>"
                             style="max-width:160px;max-height:160px;object-fit:cover;border-radius:12px;margin:0 auto 10px auto;"
                             onerror="this.onerror=null;this.src='/img/no-image.png';">
                        <div class="card-body text-center">
                            <h6 class="card-title text-truncate mb-2"><?php echo htmlspecialchars($prod['nombre']); ?></h6>
                            <h5 class="text-success mb-0">$<?php echo htmlspecialchars($prod['precio']); ?></h5>
                        </div>
                        <div class="card-footer bg-white border-0 text-center d-flex flex-column gap-2">
                            <a href="detalle_producto.php?id=<?php echo urlencode($prod['id'] ?? ''); ?>" class="btn btn-outline-primary btn-sm w-100 mb-1">
                                <i class="fa fa-eye"></i> Ver detalles
                            </a>
                            <button class="btn btn-success btn-sm w-100 mb-1" onclick="agregarAlCarrito('<?php echo htmlspecialchars($prod['id'] ?? ''); ?>')">
                                <i class="fa fa-cart-plus"></i> Agregar al carrito
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

    <!-- NUEVO: Sección de categorías populares -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">Categorías Populares</h2>
        <div class="row justify-content-center g-4">
            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center py-4 h-100">
                    <i class="fa fa-tshirt fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-0">Ropa</h6>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center py-4 h-100">
                    <i class="fa fa-laptop fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-0">Tecnología</h6>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center py-4 h-100">
                    <i class="fa fa-couch fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-0">Hogar</h6>
                </div>
            </div>
            <div class="col-6 col-md-3">
                <div class="card shadow-sm text-center py-4 h-100">
                    <i class="fa fa-basketball-ball fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-0">Deportes</h6>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de preguntas frecuentes destacadas -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">Preguntas Frecuentes</h2>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq1">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1">
                        ¿Cómo puedo vender un producto?
                    </button>
                </h2>
                <div id="faqCollapse1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Solo debes registrarte, ir a "Publicar Producto" y llenar el formulario con los datos de tu producto.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq2">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2">
                        ¿Cuáles son los métodos de pago aceptados?
                    </button>
                </h2>
                <div id="faqCollapse2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Puedes pagar con tarjeta de crédito, débito y transferencias bancarias.
                    </div>
                </div>
            </div>
            <div class="accordion-item">
                <h2 class="accordion-header" id="faq3">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3">
                        ¿Cómo funciona el envío?
                    </button>
                </h2>
                <div id="faqCollapse3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        El envío es gestionado por Mextium y llega a todo México. ¡Envío gratis en compras mayores a $999!
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de beneficios de usar Mextium -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">¿Por qué elegir Mextium?</h2>
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-shipping-fast fa-2x mb-2 text-success"></i>
                    <h6 class="fw-bold mb-2">Envíos Rápidos</h6>
                    <p class="small text-muted">Recibe tus compras en tiempo récord en todo México.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-lock fa-2x mb-2 text-danger"></i>
                    <h6 class="fw-bold mb-2">Pago Seguro</h6>
                    <p class="small text-muted">Tus datos y pagos están protegidos con la mejor tecnología.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-tags fa-2x mb-2 text-warning"></i>
                    <h6 class="fw-bold mb-2">Ofertas Exclusivas</h6>
                    <p class="small text-muted">Aprovecha descuentos y promociones únicas cada semana.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-headset fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-2">Soporte 24/7</h6>
                    <p class="small text-muted">Nuestro equipo te ayuda en todo momento, todos los días.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de newsletter -->
    <section class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm p-4 text-center">
                    <h4 class="mb-3"><i class="fa fa-envelope-open-text text-primary me-2"></i>Suscríbete a nuestro Newsletter</h4>
                    <p class="mb-3 text-muted">Recibe novedades, ofertas y tips exclusivos directamente en tu correo.</p>
                    <form id="newsletter-form" class="row g-2 justify-content-center">
                        <div class="col-12 col-md-8">
                            <input type="email" class="form-control" id="newsletter-email" placeholder="Tu correo electrónico" required>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-gradient-blue w-100">Suscribirme</button>
                        </div>
                    </form>
                    <div id="newsletter-msg" class="mt-2"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de apps móviles -->
    <section class="container mb-5">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start mb-4 mb-md-0">
                <h3 class="mb-3">¡Lleva Mextium contigo!</h3>
                <p class="mb-4">Descarga nuestra app y compra o vende desde cualquier lugar.</p>
                <a href="#" class="btn btn-dark me-2 mb-2"><i class="fab fa-apple me-1"></i> App Store</a>
                <a href="#" class="btn btn-success mb-2"><i class="fab fa-google-play me-1"></i> Google Play</a>
            </div>
            <div class="col-md-6 text-center">
                <img src="img/app-movil.png" alt="App Mextium" style="max-width: 250px;">
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de marcas aliadas -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">Marcas Aliadas</h2>
        <div class="row justify-content-center align-items-center g-4">
            <div class="col-4 col-md-2 text-center">
                <img src="img/marca1.png" alt="Marca 1" style="max-width: 100px;">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="img/marca2.png" alt="Marca 2" style="max-width: 100px;">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="img/marca3.png" alt="Marca 3" style="max-width: 100px;">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="img/marca4.png" alt="Marca 4" style="max-width: 100px;">
            </div>
            <div class="col-4 col-md-2 text-center">
                <img src="img/marca5.png" alt="Marca 5" style="max-width: 100px;">
            </div>
        </div>
    </section>

    <!-- NUEVO: Sección de pasos para comprar y vender -->
    <section class="container mb-5">
        <h2 class="section-title text-center mb-4">¿Cómo funciona Mextium?</h2>
        <div class="row text-center g-4">
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-user-plus fa-2x mb-2 text-primary"></i>
                    <h6 class="fw-bold mb-2">1. Regístrate</h6>
                    <p class="small text-muted">Crea tu cuenta gratis en minutos.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-search fa-2x mb-2 text-success"></i>
                    <h6 class="fw-bold mb-2">2. Explora</h6>
                    <p class="small text-muted">Busca productos o publica los tuyos fácilmente.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-credit-card fa-2x mb-2 text-warning"></i>
                    <h6 class="fw-bold mb-2">3. Compra o Vende</h6>
                    <p class="small text-muted">Realiza transacciones seguras y rápidas.</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm py-4 h-100">
                    <i class="fa fa-smile fa-2x mb-2 text-danger"></i>
                    <h6 class="fw-bold mb-2">4. Disfruta</h6>
                    <p class="small text-muted">Recibe tus productos o tus ganancias sin complicaciones.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- NUEVO: Barra de redes sociales -->
    <div class="container mb-5">
        <h2 class="section-title text-center mb-4">Síguenos en redes sociales</h2>
        <div class="d-flex justify-content-center gap-4">
            <a href="https://facebook.com" target="_blank" class="text-primary fs-2"><i class="fab fa-facebook"></i></a>
            <a href="https://twitter.com" target="_blank" class="text-info fs-2"><i class="fab fa-twitter"></i></a>
            <a href="https://instagram.com" target="_blank" class="text-danger fs-2"><i class="fab fa-instagram"></i></a>
            <a href="https://tiktok.com" target="_blank" class="text-dark fs-2"><i class="fab fa-tiktok"></i></a>
            <a href="mailto:soporte@mextium.com" class="text-success fs-2"><i class="fa fa-envelope"></i></a>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer-mex text-center mt-5 py-4">
        <div class="container">
            <div class="mb-2">
                <a href="encabezados/acerca.html" class="text-white me-3">Acerca de</a>
                <a href="encabezados/contactos.html" class="text-white me-3">Contactos</a>
                <a href="encabezados/ayuda.html" class="text-white me-3">Ayuda</a>
                <a href="encabezados/FAQs.html" class="text-white">FAQs</a>
            </div>
            <div>
                &copy; 2025 <b>Mextium</b>. Todos los derechos reservados.
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/bienvenida.js"></script>
    <script>


    // Newsletter
    document.getElementById('newsletter-form').addEventListener('submit', function(e) {
        e.preventDefault();
        var email = document.getElementById('newsletter-email').value.trim();
        var msg = document.getElementById('newsletter-msg');
        if(email && /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){
            msg.innerHTML = '<span class="text-success">¡Gracias por suscribirte!</span>';
            document.getElementById('newsletter-form').reset();
        } else {
            msg.innerHTML = '<span class="text-danger">Por favor, ingresa un correo válido.</span>';
        }
    });

    // Sidebar lateral
    const sidebar = document.getElementById('sidebar-menu');
    const openBtn = document.getElementById('menu-toggle');
    const closeBtn = document.getElementById('sidebar-close');

    openBtn.addEventListener('click', function(e) {
        e.preventDefault();
        sidebar.style.transform = 'translateX(0)';
    });

    closeBtn.addEventListener('click', function() {
        sidebar.style.transform = 'translateX(-100%)';
    });

    // Cambiar ciudad al hacer clic en el menú desplegable
    document.querySelectorAll('.dropdown-menu .dropdown-item').forEach(function(item) {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            document.getElementById('ciudadDropdown').innerHTML = this.textContent + ' <i class="fa fa-chevron-down ms-1"></i>';
        });
    });

    // Selección de elementos
const inputBusqueda = document.getElementById('busqueda-mextium');
const historialBox = document.getElementById('historial-busqueda');

// Cargar historial desde localStorage
function cargarHistorial() {
    const historial = JSON.parse(localStorage.getItem('historialBusquedaMextium') || '[]');
    historialBox.innerHTML = '';
    if (historial.length > 0) {
        historial.forEach(item => {
            const li = document.createElement('li');
            li.className = 'list-group-item list-group-item-action';
            li.textContent = item;
            li.onclick = () => {
                inputBusqueda.value = item;
                historialBox.style.display = 'none';
            };
            historialBox.appendChild(li);
        });
        historialBox.style.display = 'block';
    } else {
        historialBox.style.display = 'none';
    }
}

// Guardar búsqueda al enviar el formulario
inputBusqueda.form.addEventListener('submit', function(e) {
    const valor = inputBusqueda.value.trim();
    if (valor) {
        let historial = JSON.parse(localStorage.getItem('historialBusquedaMextium') || '[]');
        historial = historial.filter(item => item !== valor); // Evita duplicados
        historial.unshift(valor);
        if (historial.length > 8) historial = historial.slice(0,8); // Máximo 8
        localStorage.setItem('historialBusquedaMextium', JSON.stringify(historial));
    }
});

// Mostrar historial al enfocar el input
inputBusqueda.addEventListener('focus', cargarHistorial);

// Ocultar historial al salir del input (con pequeño delay para permitir click)
inputBusqueda.addEventListener('blur', () => setTimeout(()=>{ historialBox.style.display = 'none'; }, 150));

// Opcional: limpiar historial con doble click
inputBusqueda.addEventListener('dblclick', () => {
    localStorage.removeItem('historialBusquedaMextium');
    cargarHistorial();
});
    </script>

    <!-- NUEVO: Botón flotante de WhatsApp -->
    <a href="https://wa.me/5215555555555" target="_blank" style="position:fixed;bottom:30px;right:30px;z-index:999;">
        <i class="fab fa-whatsapp fa-4x text-success" style="background:none;"></i>
    </a>

    <!-- NUEVO: Botón flotante de ayuda -->
    <a href="encabezados/ayuda.html" title="¿Necesitas ayuda?" style="position:fixed;bottom:100px;right:30px;z-index=999;">
        <i class="fa fa-question-circle fa-3x text-warning"></i>
    </a>

</body>
</html>