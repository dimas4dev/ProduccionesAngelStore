<?php
/**
 * Controlador de Cliente
 * Maneja toda la lógica del panel de cliente
 */

require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/AuthController.php';

class ClienteController {
    private $authController;
    private $productoModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->authController = new AuthController();
        $this->productoModel = new Producto();
    }

    /**
     * Muestra el panel del cliente
     */
    public function index() {
        // Verificar que el usuario esté logueado y sea cliente
        $this->authController->requerirRol('cliente');

        // Obtener datos del usuario actual
        $usuario = $this->authController->obtenerUsuarioActual();

        // Obtener productos para mostrar
        $productos = $this->productoModel->obtenerConStock();

        // Carrito de ejemplo (en una implementación real vendría de la base de datos o sesión)
        $carrito = $this->obtenerCarritoUsuario($usuario['id']);

        // Cargar la vista
        $this->cargarVista('cliente', [
            'usuario' => $usuario,
            'productos' => $productos,
            'carrito' => $carrito
        ]);
    }

    /**
     * Maneja las acciones del carrito de compras
     */
    public function carrito() {
        $this->authController->requerirRol('cliente');

        $accion = $_POST['accion'] ?? $_GET['accion'] ?? '';

        switch ($accion) {
            case 'agregar':
                $this->agregarAlCarrito();
                break;
            case 'eliminar':
                $this->eliminarDelCarrito();
                break;
            case 'actualizar':
                $this->actualizarCarrito();
                break;
            case 'limpiar':
                $this->limpiarCarrito();
                break;
            default:
                $this->mostrarError("Acción no válida");
        }
    }

    /**
     * Agrega un producto al carrito
     */
    private function agregarAlCarrito() {
        try {
            $productoId = $_POST['producto_id'] ?? 0;
            $cantidad = $_POST['cantidad'] ?? 1;

            $producto = $this->productoModel->obtenerPorId($productoId);
            if (!$producto) {
                $this->mostrarError("Producto no encontrado");
                return;
            }

            if (!$producto->tieneStock()) {
                $this->mostrarError("El producto no tiene stock disponible");
                return;
            }

            // En una implementación real, esto se guardaría en la base de datos
            $this->mostrarExito("Producto agregado al carrito exitosamente");
            
            // Redirigir de vuelta al panel del cliente
            header('Location: index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            $this->mostrarError("Error al agregar producto al carrito: " . $e->getMessage());
        }
    }

    /**
     * Elimina un producto del carrito
     */
    private function eliminarDelCarrito() {
        try {
            $productoId = $_POST['producto_id'] ?? 0;
            
            // En una implementación real, esto se eliminaría de la base de datos
            $this->mostrarExito("Producto eliminado del carrito");
            
            header('Location: index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            $this->mostrarError("Error al eliminar producto del carrito: " . $e->getMessage());
        }
    }

    /**
     * Actualiza la cantidad de un producto en el carrito
     */
    private function actualizarCarrito() {
        try {
            $productoId = $_POST['producto_id'] ?? 0;
            $cantidad = $_POST['cantidad'] ?? 1;

            if ($cantidad <= 0) {
                $this->eliminarDelCarrito();
                return;
            }

            // En una implementación real, esto se actualizaría en la base de datos
            $this->mostrarExito("Carrito actualizado");
            
            header('Location: index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            $this->mostrarError("Error al actualizar carrito: " . $e->getMessage());
        }
    }

    /**
     * Limpia el carrito completo
     */
    private function limpiarCarrito() {
        try {
            // En una implementación real, esto se limpiaría en la base de datos
            $this->mostrarExito("Carrito limpiado");
            
            header('Location: index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            $this->mostrarError("Error al limpiar carrito: " . $e->getMessage());
        }
    }

    /**
     * Procesa el checkout/pago
     */
    public function checkout() {
        $this->authController->requerirRol('cliente');

        $usuario = $this->authController->obtenerUsuarioActual();
        $carrito = $this->obtenerCarritoUsuario($usuario['id']);

        if (empty($carrito)) {
            $this->mostrarError("El carrito está vacío");
            header('Location: index.php?controller=cliente');
            exit;
        }

        try {
            // En una implementación real, aquí se procesaría el pago
            // y se crearían los registros de venta y detalle_venta
            
            $this->mostrarExito("¡Compra realizada exitosamente!");
            
            // Limpiar carrito después de la compra
            $this->limpiarCarrito();
            
            header('Location: index.php?controller=cliente');
            exit;

        } catch (Exception $e) {
            $this->mostrarError("Error al procesar el pago: " . $e->getMessage());
        }
    }

    /**
     * Busca productos
     */
    public function buscar() {
        $this->authController->requerirRol('cliente');

        $termino = $_GET['q'] ?? '';
        $usuario = $this->authController->obtenerUsuarioActual();

        try {
            if (empty($termino)) {
                $productos = $this->productoModel->obtenerConStock();
            } else {
                $productos = $this->productoModel->buscarPorNombre($termino);
            }

            $carrito = $this->obtenerCarritoUsuario($usuario['id']);

            $this->cargarVista('cliente', [
                'usuario' => $usuario,
                'productos' => $productos,
                'carrito' => $carrito,
                'termino_busqueda' => $termino
            ]);

        } catch (Exception $e) {
            $this->mostrarError("Error en la búsqueda: " . $e->getMessage());
        }
    }

    /**
     * Obtiene el carrito del usuario (simulado)
     * @param int $usuarioId ID del usuario
     * @return array Carrito del usuario
     */
    private function obtenerCarritoUsuario($usuarioId) {
        // En una implementación real, esto consultaría la base de datos
        // Por ahora retornamos un array vacío
        return [];
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
        $titulo = 'Mi Cuenta - Producciones Angel';
        $css_file = 'view/cliente/cliente.css';
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
     * Maneja errores generales
     * @param string $mensaje Mensaje de error
     */
    private function manejarError($mensaje) {
        error_log($mensaje);
        $this->mostrarError($mensaje);
    }
}
?>
