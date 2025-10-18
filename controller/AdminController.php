<?php
/**
 * Controlador de Administración
 * Maneja toda la lógica del panel de administración
 */

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/AuthController.php';

class AdminController {
    private $authController;
    private $productoModel;
    private $usuarioModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->authController = new AuthController();
        $this->productoModel = new Producto();
        $this->usuarioModel = new Usuario();
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

            // Datos de ejemplo para mensajes (en una implementación real vendrían de la base de datos)
            $mensajes = [
                [
                    'id' => 1,
                    'nombre' => 'Ana Martínez',
                    'email' => 'ana.martinez@email.com',
                    'telefono' => '+1-555-0101',
                    'fecha' => '2024-01-15 09:20:00',
                    'mensaje' => 'Hola, me interesa saber más sobre los productos de gaming que tienen disponibles. ¿Podrían contactarme?'
                ],
                [
                    'id' => 2,
                    'nombre' => 'Roberto Silva',
                    'email' => 'roberto.silva@email.com',
                    'telefono' => '+1-555-0202',
                    'fecha' => '2024-01-14 14:30:00',
                    'mensaje' => 'Buenos días, quisiera información sobre el servicio técnico para laptops. Gracias.'
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
            }
        }

        // Redirigir de vuelta al dashboard
        header('Location: index.php?controller=admin');
        exit;
    }

    /**
     * Crea un nuevo producto
     */
    private function crearProducto() {
        try {
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => $_POST['imagen'] ?? null,
                'precio' => $_POST['precio'] ?? 0,
                'stock' => $_POST['stock'] ?? 0
            ];

            $errores = $this->validarProducto($datos);
            if (empty($errores)) {
                if ($this->productoModel->crear($datos)) {
                    $this->mostrarExito("Producto creado exitosamente");
                } else {
                    $this->mostrarError("Error al crear el producto");
                }
            } else {
                $this->mostrarErrores($errores);
            }
        } catch (Exception $e) {
            $this->mostrarError("Error al crear producto: " . $e->getMessage());
        }
    }

    /**
     * Actualiza un producto existente
     */
    private function actualizarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen' => $_POST['imagen'] ?? null,
                'precio' => $_POST['precio'] ?? 0,
                'stock' => $_POST['stock'] ?? 0
            ];

            $errores = $this->validarProducto($datos);
            if (empty($errores)) {
                if ($this->productoModel->actualizar($id, $datos)) {
                    $this->mostrarExito("Producto actualizado exitosamente");
                } else {
                    $this->mostrarError("Error al actualizar el producto");
                }
            } else {
                $this->mostrarErrores($errores);
            }
        } catch (Exception $e) {
            $this->mostrarError("Error al actualizar producto: " . $e->getMessage());
        }
    }

    /**
     * Elimina un producto
     */
    private function eliminarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->productoModel->eliminar($id)) {
                $this->mostrarExito("Producto eliminado exitosamente");
            } else {
                $this->mostrarError("Error al eliminar el producto");
            }
        } catch (Exception $e) {
            $this->mostrarError("Error al eliminar producto: " . $e->getMessage());
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
        $rutaVista = __DIR__ . '/../view/' . $vista . '/' . $vista . '.php';
        
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
