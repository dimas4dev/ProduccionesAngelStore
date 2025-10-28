<?php
/**
 * Archivo principal - Punto de entrada de la aplicación
 * Maneja el routing hacia los controladores apropiados
 */

// Limpiar cualquier output previo para endpoints JSON
if (isset($_GET['controller']) && $_GET['controller'] === 'admin' && 
    isset($_GET['action']) && $_GET['action'] === 'procesarPedidoCheckout') {
    if (ob_get_level()) {
        ob_clean();
    }
}

// Incluir controladores
require_once __DIR__ . '/controller/AuthController.php';
require_once __DIR__ . '/controller/ProductoController.php';
require_once __DIR__ . '/controller/AdminController.php';
require_once __DIR__ . '/controller/ClienteController.php';
require_once __DIR__ . '/controller/ContactoController.php';
require_once __DIR__ . '/controller/VentasController.php';

// Obtener parámetros de la URL o POST
$controller = $_POST['controller'] ?? $_GET['controller'] ?? 'home';
$action = $_POST['action'] ?? $_GET['action'] ?? 'index';

// Inicializar variables para el layout
$titulo = 'Producciones Angel';
$css_file = null;
$usuario = null;

// Verificar si hay un usuario logueado
$authController = new AuthController();
if ($authController->estaLogueado()) {
    $usuario = $authController->obtenerUsuarioActual();
}


try {
    // Routing principal
    switch ($controller) {
        case 'auth':
            $authController = new AuthController();
            switch ($action) {
                case 'login':
                    $titulo = 'Login - Producciones Angel';
                    $css_file = 'view/login/login.css';
                    $authController->login();
                    break;
                case 'logout':
                    $authController->logout();
                    break;
                case 'register':
                    $titulo = 'Register - Producciones Angel';
                    $css_file = 'view/register/register.css';
                    $authController->register();
                    break;
                default:
                    header('Location: index.php?controller=auth&action=login');
                    exit;
            }
            break;

        case 'admin':
            $adminController = new AdminController();
            $titulo = 'Panel de Administración - Producciones Angel';
            $css_file = 'view/admin/admin.css';
            
            if ($action === 'gestionarProductos') {
                $adminController->gestionarProductos();
            } elseif ($action === 'gestionarUsuarios') {
                $adminController->gestionarUsuarios();
            } elseif ($action === 'gestionarMensajes') {
                $adminController->gestionarMensajes();
            } elseif ($action === 'ventas') {
                $adminController->verVentas();
            } elseif ($action === 'eliminarVenta') {
                $adminController->eliminarVenta();
            } elseif ($action === 'crearVenta') {
                $adminController->crearVenta();
            } elseif ($action === 'procesarPedidoCheckout') {
                $adminController->procesarPedidoCheckout();
            } elseif ($action === 'generarPDFVenta') {
                $adminController->generarPDFVenta();
            } else {
                $adminController->index();
            }
            break;

        case 'ventas':
            $ventasController = new VentasController();
            $titulo = 'Gestión de Ventas - Producciones Angel';
            $css_file = 'view/admin/admin.css';
            
            switch ($action) {
                case 'mostrarVentas':
                    $ventasController->mostrarVentas();
                    break;
                case 'crearVenta':
                    $ventasController->crearVenta();
                    break;
                case 'actualizarVenta':
                    $ventasController->actualizarVenta();
                    break;
                case 'eliminarVenta':
                    $ventasController->eliminarVenta();
                    break;
                case 'obtenerPorFechas':
                    $ventasController->obtenerVentasPorFechas();
                    break;
                default:
                    $ventasController->mostrarVentas();
                    break;
            }
            break;

        case 'cliente':
            $clienteController = new ClienteController();
            $titulo = 'Mi Cuenta - Producciones Angel';
            $css_file = 'view/cliente/cliente.css';
            
            switch ($action) {
                case 'index':
                    $clienteController->index();
                    break;
                case 'carrito':
                    $clienteController->carrito();
                    break;
                case 'checkout':
                    $clienteController->checkout();
                    break;
                case 'buscar':
                    $clienteController->buscar();
                    break;
                default:
                    $clienteController->index();
            }
            break;

        case 'contacto':
            $contactoController = new ContactoController();
            $titulo = 'Contacto - Producciones Angel';
            $css_file = 'view/contacto/contacto.css';
            
            if ($action === 'enviar') {
                $contactoController->enviar();
            } else {
                $contactoController->contacto();
            }
            break;

        case 'producto':
            $productoController = new ProductoController();
            $titulo = 'Gestión de Productos - Producciones Angel';
            $css_file = 'view/home/home.css';
            
            if ($action === 'crear') {
                $productoController->crearProducto();
            } elseif ($action === 'actualizar') {
                $productoController->actualizarProducto();
            } elseif ($action === 'eliminar') {
                $productoController->eliminarProducto();
            } else {
                $productoController->mostrarProductos();
            }
            break;

        case 'home':
        default:
            // Página principal - mostrar productos
            $productoController = new ProductoController();
            $titulo = 'Producciones Angel - Tienda en Línea';
            
            // Verificar si hay búsqueda
            if (isset($_GET['busqueda']) && !empty($_GET['busqueda'])) {
                $productos = $productoController->buscarProductos($_GET['busqueda']);
                $termino_busqueda = $_GET['busqueda'];
            } else {
                $productos = $productoController->obtenerProductosParaHome();
                $termino_busqueda = null;
            }
            
            // Incluir header común
            include __DIR__ . '/view/layouts/header.php';
            
            // Sección de búsqueda
            ?>
            <section class="search-container">
                <form method="GET" action="index.php" class="search-box">
                    <input type="hidden" name="controller" value="home">
                    <input type="text" name="busqueda" class="search-input" 
                           placeholder="Buscar productos..." 
                           value="<?php echo isset($termino_busqueda) ? htmlspecialchars($termino_busqueda) : ''; ?>">
                    <button type="submit" class="search-btn">🔍 Buscar</button>
                </form>
            </section>

            <!-- Main Content -->
            <main class="main-content">
                <!-- Welcome Section -->
                <section class="welcome-section">
                    <h1 class="welcome-title">Bienvenido a Producciones Angel</h1>
                    <p class="welcome-subtitle">Tu tienda de confianza con los mejores productos</p>
                    
                    <div class="stats-container">
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count($productos); ?></span>
                            <span class="stat-label">Productos Disponibles</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number"><?php echo count(array_filter($productos, function($p) { return $p->tieneStock(); })); ?></span>
                            <span class="stat-label">En Stock</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">100%</span>
                            <span class="stat-label">Calidad Garantizada</span>
                        </div>
                    </div>
                </section>

                <!-- Products Section -->
                <section id="productos">
                    <?php 
                    // Cargar la vista de productos
                    $css_file = 'view/home/home.css';
                    include __DIR__ . '/view/home/home.php'; 
                    ?>
                </section>
            </main>
            
            <!-- Incluir footer común -->
            <?php include __DIR__ . '/view/layouts/footer.php'; ?>
            <?php
            break;
    }

} catch (Exception $e) {
    // Manejo de errores
    error_log("Error en la aplicación: " . $e->getMessage());
    
    // Mostrar página de error
    $titulo = 'Error - Producciones Angel';
    include __DIR__ . '/view/layouts/header.php';
    ?>
    <div class="error-container">
        <h1>❌ Error</h1>
        <p>Ha ocurrido un error inesperado. Por favor, intenta nuevamente.</p>
        <a href="index.php" class="btn-home">🏠 Volver al Inicio</a>
    </div>
    
    <style>
        .error-container {
            text-align: center;
            padding: 60px 20px;
            max-width: 600px;
            margin: 0 auto;
        }
        
        .error-container h1 {
            color: #dc3545;
            margin-bottom: 20px;
        }
        
        .btn-home {
            display: inline-block;
            padding: 12px 24px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            margin-top: 20px;
            transition: all 0.3s ease;
        }
        
        .btn-home:hover {
            background: #0056b3;
            transform: translateY(-2px);
        }
    </style>
    <?php
    include __DIR__ . '/view/layouts/footer.php';
}
?>

<style>
/* Estilos comunes para la página principal */
.search-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 30px 20px;
    text-align: center;
}

.search-box {
    display: flex;
    max-width: 500px;
    margin: 0 auto;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    border-radius: 25px;
    overflow: hidden;
}

.search-input {
    flex: 1;
    padding: 15px 20px;
    border: none;
    font-size: 1rem;
    outline: none;
}

.search-btn {
    background: #28a745;
    color: white;
    border: none;
    padding: 15px 25px;
    cursor: pointer;
    font-size: 1rem;
    transition: background 0.3s ease;
}

.search-btn:hover {
    background: #218838;
}

.main-content {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px 40px;
}

.welcome-section {
    text-align: center;
    margin-bottom: 40px;
    padding: 40px 20px;
    background: white;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.welcome-title {
    font-size: 2.5rem;
    color: #2c3e50;
    margin-bottom: 15px;
    font-weight: 700;
}

.welcome-subtitle {
    font-size: 1.2rem;
    color: #6c757d;
    margin-bottom: 20px;
}

.stats-container {
    display: flex;
    justify-content: center;
    gap: 40px;
    margin-top: 30px;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    border-radius: 15px;
    min-width: 150px;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    opacity: 0.9;
}

/* Responsive */
@media (max-width: 768px) {
    .stats-container {
        gap: 20px;
    }
    
    .stat-item {
        min-width: 120px;
    }
    
    .welcome-title {
        font-size: 2rem;
    }
}

@media (max-width: 480px) {
    .search-box {
        flex-direction: column;
        border-radius: 15px;
    }
    
    .search-btn {
        border-radius: 0 0 15px 15px;
    }
}
</style>