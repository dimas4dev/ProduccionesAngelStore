<?php
/**
 * Controlador de Productos
 * Maneja la lógica de negocio para los productos
 */

require_once __DIR__ . '/../model/Producto.php';

class ProductoController {
    private $producto;

    /**
     * Constructor
     */
    public function __construct() {
        $this->producto = new Producto();
    }

    /**
     * Obtiene todos los productos y los envía a la vista
     */
    public function mostrarProductos() {
        try {
            $productos = $this->producto->obtenerTodos();
            $this->cargarVista('home', ['productos' => $productos]);
        } catch (Exception $e) {
            $this->manejarError("Error al obtener productos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene productos con stock disponible
     */
    public function mostrarProductosConStock() {
        try {
            $productos = $this->producto->obtenerConStock();
            $this->cargarVista('home', ['productos' => $productos]);
        } catch (Exception $e) {
            $this->manejarError("Error al obtener productos con stock: " . $e->getMessage());
        }
    }

    /**
     * Busca productos por término
     * @param string $termino Término de búsqueda
     */
    public function buscarProductos($termino) {
        try {
            if (empty(trim($termino))) {
                $productos = $this->producto->obtenerTodos();
            } else {
                $productos = $this->producto->buscarPorNombre($termino);
            }
            
            $this->cargarVista('home', [
                'productos' => $productos,
                'termino_busqueda' => $termino
            ]);
        } catch (Exception $e) {
            $this->manejarError("Error en la búsqueda: " . $e->getMessage());
        }
    }

    /**
     * Obtiene un producto específico por ID
     * @param int $id ID del producto
     */
    public function mostrarProducto($id) {
        try {
            $producto = $this->producto->obtenerPorId($id);
            
            if ($producto) {
                $this->cargarVista('detalle_producto', ['producto' => $producto]);
            } else {
                $this->manejarError("Producto no encontrado");
            }
        } catch (Exception $e) {
            $this->manejarError("Error al obtener producto: " . $e->getMessage());
        }
    }

    /**
     * Obtiene estadísticas de productos
     */
    public function obtenerEstadisticas() {
        try {
            $totalProductos = $this->producto->contar();
            $productosConStock = $this->producto->obtenerConStock();
            $productosSinStock = $totalProductos - count($productosConStock);
            
            return [
                'total' => $totalProductos,
                'con_stock' => count($productosConStock),
                'sin_stock' => $productosSinStock
            ];
        } catch (Exception $e) {
            return [
                'total' => 0,
                'con_stock' => 0,
                'sin_stock' => 0
            ];
        }
    }

    /**
     * Carga una vista específica
     * @param string $vista Nombre de la vista
     * @param array $datos Datos para pasar a la vista
     */
    private function cargarVista($vista, $datos = []) {
        // Extraer variables del array de datos
        extract($datos);
        
        $rutaVista = __DIR__ . '/../view/' . $vista . '.php';
        
        if (file_exists($rutaVista)) {
            include $rutaVista;
        } else {
            $this->manejarError("Vista no encontrada: " . $vista);
        }
    }

    /**
     * Maneja errores de la aplicación
     * @param string $mensaje Mensaje de error
     */
    private function manejarError($mensaje) {
        // Log del error (en un entorno de producción usarías un sistema de logs)
        error_log($mensaje);
        
        // Mostrar error amigable al usuario
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
        echo "<h3>Error</h3>";
        echo "<p>" . htmlspecialchars($mensaje) . "</p>";
        echo "<p>Por favor, intenta nuevamente o contacta al administrador.</p>";
        echo "</div>";
    }

    /**
     * Valida los datos de entrada
     * @param array $datos Datos a validar
     * @return array Array con errores de validación (vacío si no hay errores)
     */
    public function validarDatos($datos) {
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
     * Procesa la búsqueda desde formulario
     */
    public function procesarBusqueda() {
        $termino = $_GET['busqueda'] ?? '';
        $this->buscarProductos($termino);
    }

    /**
     * Obtiene productos para la página principal
     */
    public function obtenerProductosParaHome() {
        try {
            // Obtener productos con stock disponible para la página principal
            $productos = $this->producto->obtenerConStock();
            
            // Si no hay productos con stock, mostrar todos los productos
            if (empty($productos)) {
                $productos = $this->producto->obtenerTodos();
            }
            
            return $productos;
        } catch (Exception $e) {
            $this->manejarError("Error al cargar productos: " . $e->getMessage());
            return [];
        }
    }
}
?>
