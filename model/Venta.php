<?php
/**
 * Modelo de Ventas
 * Maneja las operaciones CRUD para las ventas
 */

require_once __DIR__ . '/../config/conexion.php';

class Venta {
    private $db;
    private $id;
    private $producto_id;
    private $cliente_id;
    private $cantidad;
    private $precio_unitario;
    private $total;
    private $fecha_venta;
    private $estado;
    
    // Propiedades adicionales para información relacionada
    public $producto_nombre;
    public $cliente_nombre;
    public $cliente_email;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = new Database();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getProductoId() { return $this->producto_id; }
    public function getClienteId() { return $this->cliente_id; }
    public function getCantidad() { return $this->cantidad; }
    public function getPrecioUnitario() { return $this->precio_unitario; }
    public function getTotal() { return $this->total; }
    public function getFechaVenta() { return $this->fecha_venta; }
    public function getEstado() { return $this->estado; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setProductoId($producto_id) { $this->producto_id = $producto_id; }
    public function setClienteId($cliente_id) { $this->cliente_id = $cliente_id; }
    public function setCantidad($cantidad) { $this->cantidad = $cantidad; }
    public function setPrecioUnitario($precio_unitario) { $this->precio_unitario = $precio_unitario; }
    public function setTotal($total) { $this->total = $total; }
    public function setFechaVenta($fecha_venta) { $this->fecha_venta = $fecha_venta; }
    public function setEstado($estado) { $this->estado = $estado; }

    /**
     * Obtiene el ID de la última inserción
     * @return int ID de la última inserción
     */
    public function getLastInsertId() {
        return $this->db->getConexion()->insert_id;
    }

    /**
     * Obtiene todas las ventas con información de productos y clientes
     * @return array Array de ventas
     */
    public function obtenerTodas() {
        $sql = "SELECT v.*, p.nombre as producto_nombre, u.nombre as cliente_nombre, u.email as cliente_email 
                FROM venta v 
                LEFT JOIN producto p ON v.producto_id = p.id 
                LEFT JOIN usuarios u ON v.cliente_id = u.id 
                ORDER BY v.fecha_venta DESC";
        
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $ventas = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $venta = new Venta();
                $venta->setId($fila['id']);
                $venta->setProductoId($fila['producto_id']);
                $venta->setClienteId($fila['cliente_id']);
                $venta->setCantidad($fila['cantidad']);
                $venta->setPrecioUnitario($fila['precio_unitario']);
                $venta->setTotal($fila['total']);
                $venta->setFechaVenta($fila['fecha_venta']);
                $venta->setEstado($fila['estado']);
                
                // Agregar información adicional
                $venta->producto_nombre = $fila['producto_nombre'];
                $venta->cliente_nombre = $fila['cliente_nombre'];
                $venta->cliente_email = $fila['cliente_email'];
                
                $ventas[] = $venta;
            }
        }
        
        return $ventas;
    }

    /**
     * Obtiene una venta por ID
     * @param int $id ID de la venta
     * @return Venta|null Objeto venta o null si no existe
     */
    public function obtenerPorId($id) {
        $sql = "SELECT v.*, p.nombre as producto_nombre, u.nombre as cliente_nombre, u.email as cliente_email 
                FROM venta v 
                LEFT JOIN producto p ON v.producto_id = p.id 
                LEFT JOIN usuarios u ON v.cliente_id = u.id 
                WHERE v.id = ?";
        
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$id]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->setId($fila['id']);
            $this->setProductoId($fila['producto_id']);
            $this->setClienteId($fila['cliente_id']);
            $this->setCantidad($fila['cantidad']);
            $this->setPrecioUnitario($fila['precio_unitario']);
            $this->setTotal($fila['total']);
            $this->setFechaVenta($fila['fecha_venta']);
            $this->setEstado($fila['estado']);
            
            // Agregar información adicional
            $this->producto_nombre = $fila['producto_nombre'];
            $this->cliente_nombre = $fila['cliente_nombre'];
            $this->cliente_email = $fila['cliente_email'];
            
            return $this;
        }
        
        return null;
    }

    /**
     * Crea una nueva venta
     * @param array $datos Datos de la venta
     * @return bool True si se creó correctamente
     */
    public function crear($datos) {
        try {
            $conexion = $this->db->getConexion();
            
            $producto_id = (int)$datos['producto_id'];
            $cliente_id = (int)$datos['cliente_id'];
            $cantidad = (int)$datos['cantidad'];
            $precio_unitario = (float)$datos['precio_unitario'];
            $total = $cantidad * $precio_unitario;
            $estado = $conexion->real_escape_string($datos['estado'] ?? 'pendiente');
            
            $sql = "INSERT INTO venta (producto_id, cliente_id, cantidad, precio_unitario, total, estado) 
                    VALUES ($producto_id, $cliente_id, $cantidad, $precio_unitario, $total, '$estado')";
            
            $resultado = $conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $conexion->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al crear venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza una venta existente
     * @param int $id ID de la venta
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            $conexion = $this->db->getConexion();
            
            $cantidad = (int)$datos['cantidad'];
            $precio_unitario = (float)$datos['precio_unitario'];
            $total = $cantidad * $precio_unitario;
            $estado = $conexion->real_escape_string($datos['estado']);
            $id_int = (int)$id;
            
            $sql = "UPDATE venta SET cantidad = $cantidad, precio_unitario = $precio_unitario, 
                    total = $total, estado = '$estado' WHERE id = $id_int";
            
            $resultado = $conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $conexion->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al actualizar venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina una venta
     * @param int $id ID de la venta
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            $conexion = $this->db->getConexion();
            $sql = "DELETE FROM venta WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();
            
            return $stmt->affected_rows > 0;
        } catch (Exception $e) {
            error_log("Error al eliminar venta: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene estadísticas de ventas
     * @return array Estadísticas de ventas
     */
    public function obtenerEstadisticas() {
        try {
            $sql = "SELECT 
                        COUNT(*) as total_ventas,
                        SUM(total) as total_ingresos,
                        AVG(total) as promedio_venta,
                        COUNT(DISTINCT cliente_id) as clientes_unicos
                    FROM venta 
                    WHERE estado = 'completada'";
            
            $resultado = $this->db->ejecutarConsulta($sql);
            
            if ($resultado && $resultado->num_rows > 0) {
                return $resultado->fetch_assoc();
            }
            
            return [
                'total_ventas' => 0,
                'total_ingresos' => 0,
                'promedio_venta' => 0,
                'clientes_unicos' => 0
            ];
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [
                'total_ventas' => 0,
                'total_ingresos' => 0,
                'promedio_venta' => 0,
                'clientes_unicos' => 0
            ];
        }
    }

    /**
     * Obtiene ventas por rango de fechas
     * @param string $fecha_inicio Fecha de inicio
     * @param string $fecha_fin Fecha de fin
     * @return array Array de ventas
     */
    public function obtenerPorRangoFechas($fecha_inicio, $fecha_fin) {
        $sql = "SELECT v.*, p.nombre as producto_nombre, u.nombre as cliente_nombre, u.email as cliente_email 
                FROM venta v 
                LEFT JOIN producto p ON v.producto_id = p.id 
                LEFT JOIN usuarios u ON v.cliente_id = u.id 
                WHERE v.fecha_venta BETWEEN ? AND ? 
                ORDER BY v.fecha_venta DESC";
        
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$fecha_inicio, $fecha_fin]);
        
        $ventas = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $venta = new Venta();
                $venta->setId($fila['id']);
                $venta->setProductoId($fila['producto_id']);
                $venta->setClienteId($fila['cliente_id']);
                $venta->setCantidad($fila['cantidad']);
                $venta->setPrecioUnitario($fila['precio_unitario']);
                $venta->setTotal($fila['total']);
                $venta->setFechaVenta($fila['fecha_venta']);
                $venta->setEstado($fila['estado']);
                
                // Agregar información adicional
                $venta->producto_nombre = $fila['producto_nombre'];
                $venta->cliente_nombre = $fila['cliente_nombre'];
                $venta->cliente_email = $fila['cliente_email'];
                
                $ventas[] = $venta;
            }
        }
        
        return $ventas;
    }

    /**
     * Formatea el total para mostrar
     * @return string Total formateado
     */
    public function getTotalFormateado() {
        return '$' . number_format($this->total, 2);
    }

    /**
     * Formatea la fecha para mostrar
     * @return string Fecha formateada
     */
    public function getFechaFormateada() {
        return date('d/m/Y H:i', strtotime($this->fecha_venta));
    }

    /**
     * Obtiene el estado con emoji
     * @return string Estado con emoji
     */
    public function getEstadoConEmoji() {
        switch ($this->estado) {
            case 'pendiente':
                return '⏳ Pendiente';
            case 'completada':
                return '✅ Completada';
            case 'cancelada':
                return '❌ Cancelada';
            default:
                return $this->estado;
        }
    }
}
?>
