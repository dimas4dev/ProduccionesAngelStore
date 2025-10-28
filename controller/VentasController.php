<?php
/**
 * Controlador de Ventas
 * Maneja la lógica de negocio para las ventas
 */

require_once __DIR__ . '/../model/Venta.php';
require_once __DIR__ . '/../model/Producto.php';
require_once __DIR__ . '/../model/Usuario.php';

class VentasController {
    private $venta;
    private $producto;
    private $usuario;

    /**
     * Constructor
     */
    public function __construct() {
        $this->venta = new Venta();
        $this->producto = new Producto();
        $this->usuario = new Usuario();
    }

    /**
     * Muestra todas las ventas
     */
    public function mostrarVentas() {
        try {
            $ventas = $this->venta->obtenerTodas();
            $estadisticas = $this->venta->obtenerEstadisticas();
            
            $this->cargarVista('ventas', [
                'ventas' => $ventas,
                'estadisticas' => $estadisticas
            ]);
        } catch (Exception $e) {
            $this->manejarError("Error al obtener ventas: " . $e->getMessage());
        }
    }

    /**
     * Crea una nueva venta
     */
    public function crearVenta() {
        try {
            // Validar datos
            $errores = $this->validarVenta($_POST);
            
            if (empty($errores)) {
                // Verificar que el producto tenga stock suficiente
                $producto = $this->producto->obtenerPorId($_POST['producto_id']);
                if (!$producto || $producto->getStock() < $_POST['cantidad']) {
                    throw new Exception("Stock insuficiente para este producto");
                }
                
                // Crear la venta
                $datos = [
                    'producto_id' => $_POST['producto_id'],
                    'cliente_id' => $_POST['cliente_id'],
                    'cantidad' => $_POST['cantidad'],
                    'precio_unitario' => $producto->getPrecio(),
                    'estado' => 'pendiente'
                ];
                
                if ($this->venta->crear($datos)) {
                    // Actualizar stock del producto
                    $nuevoStock = $producto->getStock() - $_POST['cantidad'];
                    $this->producto->actualizar($_POST['producto_id'], [
                        'nombre' => $producto->getNombre(),
                        'descripcion' => $producto->getDescripcion(),
                        'precio' => $producto->getPrecio(),
                        'stock' => $nuevoStock
                    ]);
                    
                    $_SESSION['mensaje'] = "Venta creada exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al crear la venta";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            // En lugar de redirección, mostrar mensaje y recargar con JavaScript
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=verVentas';";
            echo "</script>";
            exit;
        } catch (Exception $e) {
            error_log("Error en crearVenta: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al crear venta: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            
            // En lugar de redirección, mostrar mensaje y recargar con JavaScript
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=verVentas';";
            echo "</script>";
            exit;
        }
    }

    /**
     * Actualiza una venta existente
     */
    public function actualizarVenta() {
        try {
            $id = $_POST['id'] ?? 0;
            
            $errores = $this->validarVenta($_POST);
            
            if (empty($errores)) {
                $datos = [
                    'cantidad' => $_POST['cantidad'],
                    'precio_unitario' => $_POST['precio_unitario'],
                    'estado' => $_POST['estado']
                ];
                
                if ($this->venta->actualizar($id, $datos)) {
                    $_SESSION['mensaje'] = "Venta actualizada exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar la venta";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php?controller=admin&action=verVentas');
            exit;
        } catch (Exception $e) {
            error_log("Error en actualizarVenta: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al actualizar venta: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin&action=verVentas');
            exit;
        }
    }

    /**
     * Elimina una venta
     */
    public function eliminarVenta() {
        // Verificar autenticación
        if (!isset($_SESSION['usuario_id']) || $_SESSION['rol'] !== 'administrador') {
            $_SESSION['mensaje'] = "Acceso denegado";
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php?controller=admin&action=verVentas');
            exit;
        }
        
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->venta->eliminar($id)) {
                $_SESSION['mensaje'] = "Venta eliminada exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar la venta";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            // En lugar de redirección, mostrar mensaje y recargar con JavaScript
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=verVentas';";
            echo "</script>";
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarVenta: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al eliminar venta: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            
            // En lugar de redirección, mostrar mensaje y recargar con JavaScript
            echo "<script>";
            echo "alert('" . addslashes($_SESSION['mensaje']) . "');";
            echo "window.location.href = 'index.php?controller=admin&action=verVentas';";
            echo "</script>";
            exit;
        }
    }

    /**
     * Obtiene ventas por rango de fechas
     */
    public function obtenerVentasPorFechas() {
        try {
            $fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
            $fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-t');
            
            $ventas = $this->venta->obtenerPorRangoFechas($fecha_inicio, $fecha_fin);
            
            $this->cargarVista('ventas', [
                'ventas' => $ventas,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin' => $fecha_fin
            ]);
        } catch (Exception $e) {
            $this->manejarError("Error al obtener ventas por fechas: " . $e->getMessage());
        }
    }

    /**
     * Valida los datos de una venta
     * @param array $datos Datos de la venta
     * @return array Array de errores
     */
    private function validarVenta($datos) {
        $errores = [];

        if (empty($datos['producto_id'])) {
            $errores[] = "El producto es obligatorio";
        }

        if (empty($datos['cliente_id'])) {
            $errores[] = "El cliente es obligatorio";
        }

        if (!isset($datos['cantidad']) || $datos['cantidad'] <= 0) {
            $errores[] = "La cantidad debe ser mayor a 0";
        }

        if (isset($datos['precio_unitario']) && $datos['precio_unitario'] <= 0) {
            $errores[] = "El precio unitario debe ser mayor a 0";
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
        // Log del error
        error_log($mensaje);
        
        // Mostrar error amigable al usuario
        echo "<div style='background-color: #f8d7da; color: #721c24; padding: 15px; margin: 20px; border-radius: 5px; border: 1px solid #f5c6cb;'>";
        echo "<h3>Error</h3>";
        echo "<p>" . htmlspecialchars($mensaje) . "</p>";
        echo "<p>Por favor, intenta nuevamente o contacta al administrador.</p>";
        echo "</div>";
    }
}
?>
