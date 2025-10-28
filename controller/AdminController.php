<?php
/**
 * Controlador de Administración
 * Maneja toda la lógica del panel de administración
 */

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/MensajeContacto.php';
require_once __DIR__ . '/../model/Venta.php';
require_once __DIR__ . '/../lib/PDFGenerator.php';
require_once __DIR__ . '/AuthController.php';

class AdminController {
    private $authController;
    private $productoModel;
    private $usuarioModel;
    private $mensajeModel;
    private $ventaModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->authController = new AuthController();
        $this->productoModel = new Producto();
        $this->usuarioModel = new Usuario();
        $this->mensajeModel = new MensajeContacto();
        $this->ventaModel = new Venta();
    }

    /**
     * Muestra el panel de administración
     */
    public function index() {
        // Verificar que el usuario esté logueado y sea administrador
        $this->authController->requerirRol('administrador');

        // Obtener datos del usuario actual
        $usuario = $this->authController->obtenerUsuarioActual();

        // Obtener datos para el dashboard
        $datos = $this->obtenerDatosDashboard();

        // Cargar la vista
        $this->cargarVista('admin', [
            'usuario' => $usuario,
            'productos' => $datos['productos'],
            'usuarios' => $datos['usuarios'],
            'ventas' => $datos['ventas'],
            'mensajes' => $datos['mensajes']
        ]);
    }

    /**
     * Obtiene los datos necesarios para el dashboard
     * @return array Datos del dashboard
     */
    private function obtenerDatosDashboard() {
        try {
            $productos = $this->productoModel->obtenerTodos();
            $usuarios = $this->usuarioModel->obtenerTodos();
            $mensajes = $this->mensajeModel->obtenerTodos();

            // Datos de ejemplo para ventas (en una implementación real vendrían de la base de datos)
            $ventas = [
                [
                    'id' => 1,
                    'cliente' => 'Juan Pérez',
                    'fecha' => '2024-01-15 10:30:00',
                    'total' => '$899.99'
                ],
                [
                    'id' => 2,
                    'cliente' => 'María García',
                    'fecha' => '2024-01-14 15:45:00',
                    'total' => '$349.99'
                ]
            ];

            return [
                'productos' => $productos,
                'usuarios' => $usuarios,
                'ventas' => $ventas,
                'mensajes' => $mensajes
            ];

        } catch (Exception $e) {
            $this->manejarError("Error al cargar datos del dashboard: " . $e->getMessage());
            return [
                'productos' => [],
                'usuarios' => [],
                'ventas' => [],
                'mensajes' => []
            ];
        }
    }

    /**
     * Gestiona productos (CRUD)
     */
    public function gestionarProductos() {
        $this->authController->requerirRol('administrador');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            switch ($accion) {
                case 'crear':
                    $this->crearProducto();
                    break;
                case 'actualizar':
                    $this->actualizarProducto();
                    break;
                case 'eliminar':
                    $this->eliminarProducto();
                    break;
                default:
                    $this->mostrarError("Acción no válida");
                    break;
            }
        }

        // Redirigir de vuelta al dashboard
        header('Location: index.php?controller=admin');
        exit;
    }

    /**
     * Gestiona mensajes de contacto
     */
    public function gestionarMensajes() {
        $this->authController->requerirRol('administrador');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            switch ($accion) {
                case 'eliminar':
                    $this->eliminarMensaje();
                    break;
                default:
                    $_SESSION['mensaje'] = "Acción no válida";
                    $_SESSION['mensaje_tipo'] = 'error';
                    break;
            }
        }

        // Redirigir de vuelta al dashboard
        header('Location: index.php?controller=admin');
        exit;
    }

    /**
     * Elimina un mensaje de contacto
     */
    private function eliminarMensaje() {
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->mensajeModel->eliminar($id)) {
                $_SESSION['mensaje'] = "Mensaje eliminado exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar el mensaje";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarMensaje: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al eliminar mensaje: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Gestiona usuarios (CRUD)
     */
    public function gestionarUsuarios() {
        $this->authController->requerirRol('administrador');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $accion = $_POST['accion'] ?? '';

            switch ($accion) {
                case 'crear':
                    $this->crearUsuario();
                    break;
                case 'actualizar':
                    $this->actualizarUsuario();
                    break;
                case 'eliminar':
                    $this->eliminarUsuario();
                    break;
                default:
                    $_SESSION['mensaje'] = "Acción no válida";
                    $_SESSION['mensaje_tipo'] = 'error';
                    break;
            }
        }

        // Redirigir de vuelta al dashboard
        header('Location: index.php?controller=admin');
        exit;
    }

    /**
     * Crea un nuevo usuario
     */
    private function crearUsuario() {
        try {
            // Recoger datos del formulario
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'clave' => $_POST['clave'] ?? '',
                'confirmar_clave' => $_POST['confirmar_clave'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'role' => $_POST['role'] ?? 'cliente'
            ];

            // Validar datos
            $errores = $this->validarUsuario($datos);
            
            if (empty($errores)) {
                $resultado = $this->usuarioModel->crear($datos);
                
                if ($resultado['success']) {
                    $_SESSION['mensaje'] = $resultado['message'];
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = $resultado['message'];
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en crearUsuario: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al crear el usuario: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Actualiza un usuario existente
     */
    private function actualizarUsuario() {
        try {
            $id = $_POST['id'] ?? 0;
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'email' => $_POST['email'] ?? '',
                'direccion' => $_POST['direccion'] ?? '',
                'telefono' => $_POST['telefono'] ?? '',
                'role' => $_POST['role'] ?? 'cliente'
            ];

            // Si se proporciona una nueva contraseña, incluirla
            if (!empty($_POST['clave'])) {
                $datos['clave'] = $_POST['clave'];
            }

            $errores = $this->validarUsuarioActualizacion($datos);
            
            if (empty($errores)) {
                if ($this->usuarioModel->actualizar($id, $datos)) {
                    $_SESSION['mensaje'] = "Usuario actualizado exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar el usuario";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en actualizarUsuario: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al actualizar usuario: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Elimina un usuario
     */
    private function eliminarUsuario() {
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->usuarioModel->eliminar($id)) {
                $_SESSION['mensaje'] = "Usuario eliminado exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar el usuario";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarUsuario: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al eliminar usuario: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Valida los datos de un usuario
     * @param array $datos Datos del usuario
     * @return array Array de errores
     */
    private function validarUsuario($datos) {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($datos['email'])) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        if (empty($datos['clave'])) {
            $errores[] = "La contraseña es obligatoria";
        } elseif (strlen($datos['clave']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        if ($datos['clave'] !== $datos['confirmar_clave']) {
            $errores[] = "Las contraseñas no coinciden";
        }

        return $errores;
    }

    /**
     * Valida los datos para actualizar un usuario
     * @param array $datos Datos del usuario
     * @return array Array de errores
     */
    private function validarUsuarioActualizacion($datos) {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = "El nombre es obligatorio";
        }

        if (empty($datos['email'])) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        // Validar contraseña solo si se proporciona
        if (!empty($datos['clave']) && strlen($datos['clave']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        return $errores;
    }

    /**
     * Crea un nuevo producto
     */
    private function crearProducto() {
        try {
            // Manejar la carga de imagen
            $imagen_binaria = null;
            $imagen_tipo = null;
            $imagen_tamano = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                // Obtener información del archivo
                $archivoTmp = $_FILES['imagen']['tmp_name'];
                $nombreOriginal = $_FILES['imagen']['name'];
                $tipoArchivo = $_FILES['imagen']['type'];
                $tamanoArchivo = $_FILES['imagen']['size'];
                
                // Validar tipo de archivo
                $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP");
                }
                
                // Validar tamaño (máximo 10MB)
                if ($tamanoArchivo > 10 * 1024 * 1024) {
                    throw new Exception("El archivo es demasiado grande. Máximo 10MB permitido");
                }
                
                // Leer el archivo como binario
                $imagen_binaria = file_get_contents($archivoTmp);
                if ($imagen_binaria === false) {
                    throw new Exception("Error al leer el archivo: $nombreOriginal");
                }
                
                $imagen_tipo = $tipoArchivo;
                $imagen_tamano = $tamanoArchivo;
            }

            // Recoger datos del formulario
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => floatval($_POST['precio']),
                'stock' => intval($_POST['stock']),
                'imagen_binaria' => $imagen_binaria,
                'imagen_tipo' => $imagen_tipo,
                'imagen_tamano' => $imagen_tamano
            ];

            // Validar datos
            $errores = $this->validarProducto($datos);
            
            if (empty($errores)) {
                if ($this->productoModel->crear($datos)) {
                    $_SESSION['mensaje'] = "Producto creado exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                    header('Location: index.php?controller=admin');
                    exit;
                } else {
                    throw new Exception("Error al crear el producto en la base de datos. Verifica los logs para más detalles.");
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
                header('Location: index.php?controller=admin');
                exit;
            }
        } catch (Exception $e) {
            error_log("Error en crearProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al crear el producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Actualiza un producto existente
     */
    private function actualizarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            
            // Manejar la carga de imagen si se proporciona una nueva
            $imagen_binaria = null;
            $imagen_tipo = null;
            $imagen_tamano = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                // Obtener información del archivo
                $archivoTmp = $_FILES['imagen']['tmp_name'];
                $nombreOriginal = $_FILES['imagen']['name'];
                $tipoArchivo = $_FILES['imagen']['type'];
                $tamanoArchivo = $_FILES['imagen']['size'];
                
                // Validar tipo de archivo
                $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP");
                }
                
                // Validar tamaño (máximo 10MB)
                if ($tamanoArchivo > 10 * 1024 * 1024) {
                    throw new Exception("El archivo es demasiado grande. Máximo 10MB permitido");
                }
                
                // Leer el archivo como binario
                $imagen_binaria = file_get_contents($archivoTmp);
                if ($imagen_binaria === false) {
                    throw new Exception("Error al leer el archivo: $nombreOriginal");
                }
                
                $imagen_tipo = $tipoArchivo;
                $imagen_tamano = $tamanoArchivo;
            }
            
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen_binaria' => $imagen_binaria,
                'imagen_tipo' => $imagen_tipo,
                'imagen_tamano' => $imagen_tamano,
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0)
            ];

            $errores = $this->validarProducto($datos);
            
            if (empty($errores)) {
                if ($this->productoModel->actualizar($id, $datos)) {
                    $_SESSION['mensaje'] = "Producto actualizado exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar el producto";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en actualizarProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al actualizar producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Elimina un producto
     */
    private function eliminarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->productoModel->eliminar($id)) {
                $_SESSION['mensaje'] = "Producto eliminado exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar el producto";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin');
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al eliminar producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin');
            exit;
        }
    }

    /**
     * Valida los datos de un producto
     * @param array $datos Datos del producto
     * @return array Array de errores
     */
    private function validarProducto($datos) {
        $errores = [];

        if (empty($datos['nombre'])) {
            $errores[] = "El nombre del producto es obligatorio";
        }

        if (empty($datos['descripcion'])) {
            $errores[] = "La descripción del producto es obligatoria";
        }

        if (!isset($datos['precio']) || $datos['precio'] <= 0) {
            $errores[] = "El precio debe ser mayor a 0";
        }

        if (!isset($datos['stock']) || $datos['stock'] < 0) {
            $errores[] = "El stock no puede ser negativo";
        }

        return $errores;
    }

    /**
     * Carga una vista específica
     * @param string $vista Nombre de la vista
     * @param array $datos Datos para pasar a la vista
     */
    private function cargarVista($vista, $datos = []) {
        // Extraer variables del array de datos
        extract($datos);
        
        // Incluir header común
        $titulo = 'Panel de Administración - Producciones Angel';
        $css_file = 'view/admin/admin.css';
        include __DIR__ . '/../view/layouts/header.php';
        
        // Cargar la vista específica
        if ($vista === 'ventas') {
            // Vista especial para ventas (está en view/ventas/ventas.php)
            $rutaVista = __DIR__ . '/../view/' . $vista . '/' . $vista . '.php';
        } else {
            // Vistas normales del admin (están en view/admin/admin.php)
            $rutaVista = __DIR__ . '/../view/' . $vista . '/' . $vista . '.php';
        }
        
        if (file_exists($rutaVista)) {
            include $rutaVista;
        } else {
            $this->mostrarError("Vista no encontrada: " . $vista);
        }
        
        // Incluir footer común
        include __DIR__ . '/../view/layouts/footer.php';
    }

    /**
     * Muestra un mensaje de error
     * @param string $mensaje Mensaje de error
     */
    private function mostrarError($mensaje) {
        echo "<div class='alert alert-error'>";
        echo "<strong>Error:</strong> " . htmlspecialchars($mensaje);
        echo "</div>";
    }

    /**
     * Muestra un mensaje de éxito
     * @param string $mensaje Mensaje de éxito
     */
    private function mostrarExito($mensaje) {
        echo "<div class='alert alert-success'>";
        echo "<strong>Éxito:</strong> " . htmlspecialchars($mensaje);
        echo "</div>";
    }

    /**
     * Muestra la vista de ventas
     */
    public function verVentas() {
        $this->authController->requerirRol('administrador');
        
        try {
            $ventas = $this->ventaModel->obtenerTodas();
            $estadisticas = $this->ventaModel->obtenerEstadisticas();
            
            $this->cargarVista('ventas', [
                'ventas' => $ventas,
                'estadisticas' => $estadisticas
            ]);
        } catch (Exception $e) {
            error_log("Error en verVentas: " . $e->getMessage());
            $this->manejarError("Error al obtener ventas: " . $e->getMessage());
        }
    }

    /**
     * Elimina una venta
     */
    public function eliminarVenta() {
        $this->authController->requerirRol('administrador');
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->ventaModel->eliminar($id)) {
                $_SESSION['mensaje'] = "Venta eliminada exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar la venta";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=ventas&t=' + Date.now();";
            echo "</script>";
            exit;
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al eliminar venta: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=ventas&t=' + Date.now();";
            echo "</script>";
            exit;
        }
    }

    /**
     * Crea una nueva venta
     */
    public function crearVenta() {
        $this->authController->requerirRol('administrador');
        
        try {
            // Validar datos
            $errores = $this->validarVenta($_POST);
            
            if (empty($errores)) {
                // Verificar que el producto tenga stock suficiente
                $producto = $this->productoModel->obtenerPorId($_POST['producto_id']);
                if (!$producto || $producto->getStock() < $_POST['cantidad']) {
                    throw new Exception("Stock insuficiente para este producto");
                }
                
                // Crear la venta
                $datos = [
                    'producto_id' => $_POST['producto_id'],
                    'cliente_id' => $_POST['cliente_id'],
                    'cantidad' => $_POST['cantidad'],
                    'precio_unitario' => $producto->getPrecio(),
                    'estado' => $_POST['estado'] ?? 'pendiente'
                ];
                
                if ($this->ventaModel->crear($datos)) {
                    // Actualizar stock del producto
                    $nuevoStock = $producto->getStock() - $_POST['cantidad'];
                    $this->productoModel->actualizar($_POST['producto_id'], [
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'precio' => $producto->getPrecio(),
                        'stock' => $nuevoStock
                    ]);
                    
                    $_SESSION['mensaje'] = "Venta creada exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                    
                    // Generar PDF de la factura
                    $ventaCreada = $this->ventaModel->obtenerPorId($this->ventaModel->getLastInsertId());
                    $cliente = $this->usuarioModel->obtenerPorId($_POST['cliente_id']);
                    
                    $datosFactura = [
                        'id' => $ventaCreada->getId(),
                        'fecha_venta' => $ventaCreada->getFechaVenta(),
                        'precio_unitario' => $ventaCreada->getPrecioUnitario(),
                        'cantidad' => $ventaCreada->getCantidad(),
                        'total' => $ventaCreada->getTotal(),
                        'estado' => $ventaCreada->getEstado()
                    ];
                    
                    $datosProducto = [
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'imagen_url' => $producto->getImagenUrl()
                    ];
                    
                    $datosCliente = [
                        'id' => $cliente->getId(),
                        'nombre' => $cliente->getNombre(),
                        'email' => $cliente->getEmail()
                    ];
                    
                    $htmlFactura = PDFGenerator::generarFacturaVenta($datosFactura, $datosProducto, $datosCliente);
                    $nombreArchivo = 'factura_venta_' . $ventaCreada->getId() . '_' . date('Y-m-d_H-i-s');
                    
                    // Mostrar PDF en nueva ventana
                    echo "<script>";
                    echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
                    echo "window.open('data:text/html;charset=utf-8,' + encodeURIComponent(`" . addslashes($htmlFactura) . "`), '_blank');";
                    echo "window.location.href = 'index.php?controller=admin&action=ventas&t=' + Date.now();";
                    echo "</script>";
                    exit;
                } else {
                    $_SESSION['mensaje'] = "Error al crear la venta";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['mensaje'] = "Errores en los datos: " . implode(', ', $errores);
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            // Redirigir a la página de ventas
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=ventas&t=' + Date.now();";
            echo "</script>";
            exit;
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al crear venta: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=ventas&t=' + Date.now();";
            echo "</script>";
            exit;
        }
    }

    /**
     * Procesa un pedido del checkout del cliente
     */
    public function procesarPedidoCheckout() {
        // Limpiar cualquier output previo
        if (ob_get_level()) {
            ob_clean();
        }
        
        // Deshabilitar display_errors para evitar HTML en la respuesta JSON
        ini_set('display_errors', '0');
        error_reporting(E_ALL);
        
        // Verificar que el usuario esté logueado
        if (!isset($_SESSION['usuario_id'])) {
            $response = [
                'success' => false,
                'error' => 'Usuario no autenticado. Por favor, inicia sesión nuevamente.'
            ];
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, must-revalidate');
            echo json_encode($response);
            exit;
        }
        
        // Verificar que el usuario tenga rol de cliente
        if (!isset($_SESSION['usuario_role']) || $_SESSION['usuario_role'] !== 'cliente') {
            $response = [
                'success' => false,
                'error' => 'Acceso denegado. Solo los clientes pueden realizar pedidos.'
            ];
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, must-revalidate');
            echo json_encode($response);
            exit;
        }
        
        try {
            // Obtener datos del POST
            if (!isset($_POST['pedido_data'])) {
                throw new Exception("No se recibieron datos del pedido");
            }
            
            $pedidoData = json_decode($_POST['pedido_data'], true);
            
            if (!$pedidoData || empty($pedidoData['items'])) {
                throw new Exception("Datos del pedido inválidos");
            }
            
            $ventasCreadas = [];
            $errores = [];
            
            // Crear una venta por cada item del carrito
            foreach ($pedidoData['items'] as $item) {
                // Verificar que el item tenga ID
                if (!isset($item['id']) || empty($item['id'])) {
                    $errores[] = "Item sin ID válido: " . ($item['nombre'] ?? 'Desconocido');
                    continue;
                }
                
                // Verificar que el producto tenga stock suficiente
                $producto = $this->productoModel->obtenerPorId($item['id']);
                if (!$producto || $producto->getStock() < $item['cantidad']) {
                    $errores[] = "Stock insuficiente para el producto: " . ($item['nombre'] ?? 'ID: ' . $item['id']);
                    continue;
                }
                
                // Crear la venta
                $datosVenta = [
                    'producto_id' => $item['id'],
                    'cliente_id' => $_SESSION['usuario_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $item['precio'],
                    'estado' => 'pendiente'
                ];
                
                if ($this->ventaModel->crear($datosVenta)) {
                    // Actualizar stock del producto
                    $nuevoStock = $producto->getStock() - $item['cantidad'];
                    $this->productoModel->actualizar($item['id'], [
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'precio' => $producto->getPrecio(),
                        'stock' => $nuevoStock
                    ]);
                    
                    $ventasCreadas[] = $item['nombre'];
                } else {
                    $errores[] = "Error al crear venta para: " . $item['nombre'];
                }
            }
            
            // Preparar respuesta
            $response = [
                'success' => count($ventasCreadas) > 0,
                'ventas_creadas' => $ventasCreadas,
                'errores' => $errores,
                'total_procesado' => count($ventasCreadas)
            ];
            
            // Si se procesaron ventas exitosamente, generar PDF del pedido
            if (count($ventasCreadas) > 0) {
                $cliente = $this->usuarioModel->obtenerPorId($_SESSION['usuario_id']);
                $productos = [];
                
                // Obtener información de productos para el PDF
                foreach ($pedidoData['items'] as $item) {
                    if (isset($item['id'])) {
                        $producto = $this->productoModel->obtenerPorId($item['id']);
                        if ($producto) {
                            $productos[] = [
                                'id' => $producto->getId(),
                                'nombre' => $producto->getNombre(),
                                'precio' => $producto->getPrecio(),
                                'imagen_url' => $producto->getImagenUrl()
                            ];
                        }
                    }
                }
                
                $datosCliente = [
                    'id' => $cliente->getId(),
                    'nombre' => $cliente->getNombre(),
                    'email' => $cliente->getEmail()
                ];
                
                // Agregar ID temporal al pedido si no existe
                if (!isset($pedidoData['id'])) {
                    $pedidoData['id'] = time();
                }
                
                $htmlFactura = PDFGenerator::generarFacturaPedido($pedidoData, $productos, $datosCliente);
                $response['pdf_html'] = $htmlFactura;
                $response['pdf_filename'] = 'factura_pedido_' . $pedidoData['id'] . '_' . date('Y-m-d_H-i-s');
            }
            
            // Enviar respuesta JSON
            header('Content-Type: application/json');
            header('Cache-Control: no-cache, must-revalidate');
            echo json_encode($response);
            exit;
            
        } catch (Exception $e) {
            $response = [
                'success' => false,
                'error' => $e->getMessage()
            ];
            
            header('Content-Type: application/json');
            echo json_encode($response);
            exit;
        }
    }


    /**
     * Genera PDF de una venta existente
     */
    public function generarPDFVenta() {
        $this->authController->requerirRol('administrador');
        
        try {
            $ventaId = $_GET['id'] ?? null;
            
            if (!$ventaId) {
                throw new Exception("ID de venta no proporcionado");
            }
            
            // Obtener datos de la venta
            $venta = $this->ventaModel->obtenerPorId($ventaId);
            if (!$venta) {
                throw new Exception("Venta no encontrada");
            }
            
            // Obtener datos del producto
            $producto = $this->productoModel->obtenerPorId($venta->getProductoId());
            if (!$producto) {
                throw new Exception("Producto no encontrado");
            }
            
            // Obtener datos del cliente
            $cliente = $this->usuarioModel->obtenerPorId($venta->getClienteId());
            if (!$cliente) {
                throw new Exception("Cliente no encontrado");
            }
            
            // Preparar datos para el PDF
            $datosFactura = [
                'id' => $venta->getId(),
                'fecha_venta' => $venta->getFechaVenta(),
                'precio_unitario' => $venta->getPrecioUnitario(),
                'cantidad' => $venta->getCantidad(),
                'total' => $venta->getTotal(),
                'estado' => $venta->getEstado()
            ];
            
            $datosProducto = [
                'nombre' => $producto->getNombre(),
                'descripcion' => $producto->getDescripcion(),
                'imagen_url' => $producto->getImagenUrl()
            ];
            
            $datosCliente = [
                'id' => $cliente->getId(),
                'nombre' => $cliente->getNombre(),
                'email' => $cliente->getEmail()
            ];
            
            // Generar PDF
            $htmlFactura = PDFGenerator::generarFacturaVenta($datosFactura, $datosProducto, $datosCliente);
            $nombreArchivo = 'factura_venta_' . $venta->getId() . '_' . date('Y-m-d_H-i-s');
            
            // Mostrar PDF
            PDFGenerator::mostrarPDF($htmlFactura, $nombreArchivo);
            
        } catch (Exception $e) {
            $_SESSION['mensaje'] = "Error al generar PDF: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=ventas';";
            echo "</script>";
            exit;
        }
    }

    /**
     * Valida los datos de una venta
     * @param array $datos Datos a validar
     * @return array Array de errores
     */
    private function validarVenta($datos) {
        $errores = [];
        
        if (empty($datos['producto_id'])) {
            $errores[] = "Debe seleccionar un producto";
        }
        
        if (empty($datos['cliente_id'])) {
            $errores[] = "Debe seleccionar un cliente";
        }
        
        if (empty($datos['cantidad']) || $datos['cantidad'] <= 0) {
            $errores[] = "La cantidad debe ser mayor a 0";
        }
        
        return $errores;
    }

    /**
     * Muestra errores de validación
     * @param array $errores Array de errores
     */
    private function mostrarErrores($errores) {
        echo "<div class='alert alert-error'>";
        echo "<h3>Errores encontrados:</h3>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
    }

    /**
     * Maneja errores generales
     * @param string $mensaje Mensaje de error
     */
    private function manejarError($mensaje) {
        error_log($mensaje);
        $this->mostrarError($mensaje);
    }
}
?>
