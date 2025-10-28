<?php
/**
 * Modelo Producto
 * Maneja las operaciones CRUD para la tabla producto
 */

require_once __DIR__ . '/../config/conexion.php';

class Producto {
    private $id;
    private $nombre;
    private $descripcion;
    private $imagen;
    private $imagen_tipo;
    private $imagen_tamano;
    private $precio;
    private $stock;
    private $fecha_creacion;
    private $db;

    /**
     * Constructor
     */
    public function __construct() {
        $this->db = obtenerConexion();
    }

    // Getters
    public function getId() { return $this->id; }
    public function getNombre() { return $this->nombre; }
    public function getDescripcion() { return $this->descripcion; }
    public function getImagen() { 
        if ($this->imagen && file_exists($this->imagen)) {
            return $this->imagen;
        }
        return null;
    }
    
    /**
     * Obtiene la URL completa de la imagen
     * @return string|null URL de la imagen o null si no existe
     */
    public function getImagenUrl() {
        if ($this->imagen && $this->imagen_tipo) {
            // Generar URL de datos para imagen binaria
            $base64 = base64_encode($this->imagen);
            return "data:{$this->imagen_tipo};base64,{$base64}";
        }
        return null;
    }
    public function getImagenTipo() { return $this->imagen_tipo; }
    public function getImagenTamano() { return $this->imagen_tamano; }
    public function getPrecio() { return $this->precio; }
    public function getStock() { return $this->stock; }
    public function getFechaCreacion() { return $this->fecha_creacion; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setImagen($imagen) { $this->imagen = $imagen; }
    public function setImagenTipo($imagen_tipo) { $this->imagen_tipo = $imagen_tipo; }
    public function setImagenTamano($imagen_tamano) { $this->imagen_tamano = $imagen_tamano; }
    public function setPrecio($precio) { $this->precio = $precio; }
    public function setStock($stock) { $this->stock = $stock; }
    public function setFechaCreacion($fecha_creacion) { $this->fecha_creacion = $fecha_creacion; }

    /**
     * Obtiene todos los productos
     * @return array Array de productos
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM producto ORDER BY fecha_creacion DESC";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $productos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $producto = new Producto();
                $producto->setId($fila['id']);
                $producto->setNombre($fila['nombre']);
                $producto->setDescripcion($fila['descripcion']);
                $producto->setImagen($fila['imagen']);
                $producto->setImagenTipo($fila['imagen_tipo']);
                $producto->setImagenTamano($fila['imagen_tamano']);
                $producto->setPrecio($fila['precio']);
                $producto->setStock($fila['stock']);
                $producto->setFechaCreacion($fila['fecha_creacion']);
                $productos[] = $producto;
            }
        }
        
        return $productos;
    }

    /**
     * Obtiene un producto por ID
     * @param int $id ID del producto
     * @return Producto|null Producto encontrado o null
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM producto WHERE id = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$id]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->setId($fila['id']);
            $this->setNombre($fila['nombre']);
            $this->setDescripcion($fila['descripcion']);
            $this->setImagen($fila['imagen']);
            $this->setPrecio($fila['precio']);
            $this->setStock($fila['stock']);
            $this->setFechaCreacion($fila['fecha_creacion']);
            return $this;
        }
        
        return null;
    }

    /**
     * Obtiene productos con stock disponible
     * @return array Array de productos con stock > 0
     */
    public function obtenerConStock() {
        $sql = "SELECT * FROM producto WHERE stock > 0 ORDER BY fecha_creacion DESC";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $productos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $producto = new Producto();
                $producto->setId($fila['id']);
                $producto->setNombre($fila['nombre']);
                $producto->setDescripcion($fila['descripcion']);
                $producto->setImagen($fila['imagen']);
                $producto->setImagenTipo($fila['imagen_tipo']);
                $producto->setImagenTamano($fila['imagen_tamano']);
                $producto->setPrecio($fila['precio']);
                $producto->setStock($fila['stock']);
                $producto->setFechaCreacion($fila['fecha_creacion']);
                $productos[] = $producto;
            }
        }
        
        return $productos;
    }

    /**
     * Busca productos por nombre
     * @param string $termino Término de búsqueda
     * @return array Array de productos encontrados
     */
    public function buscarPorNombre($termino) {
        $sql = "SELECT * FROM producto WHERE nombre LIKE ? OR descripcion LIKE ? ORDER BY nombre ASC";
        $terminoBusqueda = "%{$termino}%";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$terminoBusqueda, $terminoBusqueda]);
        
        $productos = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $producto = new Producto();
                $producto->setId($fila['id']);
                $producto->setNombre($fila['nombre']);
                $producto->setDescripcion($fila['descripcion']);
                $producto->setImagen($fila['imagen']);
                $producto->setImagenTipo($fila['imagen_tipo']);
                $producto->setImagenTamano($fila['imagen_tamano']);
                $producto->setPrecio($fila['precio']);
                $producto->setStock($fila['stock']);
                $producto->setFechaCreacion($fila['fecha_creacion']);
                $productos[] = $producto;
            }
        }
        
        return $productos;
    }

    /**
     * Crea un nuevo producto
     * @param array $datos Datos del producto
     * @return bool True si se creó correctamente
     */
    public function crear($datos) {
        try {
            $conexion = $this->db->getConexion();
            
            $nombre = $conexion->real_escape_string($datos['nombre']);
            $descripcion = $conexion->real_escape_string($datos['descripcion']);
            $imagen_tipo = $conexion->real_escape_string($datos['imagen_tipo'] ?? '');
            $imagen_tamano = (int)($datos['imagen_tamano'] ?? 0);
            $precio = (float)$datos['precio'];
            $stock = (int)$datos['stock'];
            
            // Para LONGBLOB, usar una consulta directa con escape de datos binarios
            if (isset($datos['imagen_binaria']) && $datos['imagen_binaria'] !== null) {
                $imagen_binaria = $conexion->real_escape_string($datos['imagen_binaria']);
                $sql = "INSERT INTO producto (nombre, descripcion, imagen, imagen_tipo, imagen_tamano, precio, stock) VALUES ('$nombre', '$descripcion', '$imagen_binaria', '$imagen_tipo', $imagen_tamano, $precio, $stock)";
            } else {
                $sql = "INSERT INTO producto (nombre, descripcion, imagen, imagen_tipo, imagen_tamano, precio, stock) VALUES ('$nombre', '$descripcion', NULL, '$imagen_tipo', $imagen_tamano, $precio, $stock)";
            }
            
            $resultado = $conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $conexion->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al crear producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Actualiza un producto existente
     * @param int $id ID del producto
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            $conexion = $this->db->getConexion();
            
            $nombre = $conexion->real_escape_string($datos['nombre']);
            $descripcion = $conexion->real_escape_string($datos['descripcion']);
            $imagen_tipo = $conexion->real_escape_string($datos['imagen_tipo'] ?? '');
            $imagen_tamano = (int)($datos['imagen_tamano'] ?? 0);
            $precio = (float)$datos['precio'];
            $stock = (int)$datos['stock'];
            $id_int = (int)$id;
            
            // Para LONGBLOB, usar una consulta directa con escape de datos binarios
            if (isset($datos['imagen_binaria']) && $datos['imagen_binaria'] !== null) {
                $imagen_binaria = $conexion->real_escape_string($datos['imagen_binaria']);
                $sql = "UPDATE producto SET nombre = '$nombre', descripcion = '$descripcion', imagen = '$imagen_binaria', imagen_tipo = '$imagen_tipo', imagen_tamano = $imagen_tamano, precio = $precio, stock = $stock WHERE id = $id_int";
            } else {
                $sql = "UPDATE producto SET nombre = '$nombre', descripcion = '$descripcion', imagen_tipo = '$imagen_tipo', imagen_tamano = $imagen_tamano, precio = $precio, stock = $stock WHERE id = $id_int";
            }
            
            $resultado = $conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en consulta: " . $conexion->error);
            }
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al actualizar producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un producto
     * @param int $id ID del producto
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM producto WHERE id = ?";
            $stmt = $this->db->getConexion()->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->getConexion()->error);
            }
            
            $id_int = (int)$id;
            $stmt->bind_param("i", $id_int);
            $resultado = $stmt->execute();
            $stmt->close();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al eliminar producto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el número total de productos
     * @return int Número total de productos
     */
    public function contar() {
        $sql = "SELECT COUNT(*) as total FROM producto";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return (int)$fila['total'];
        }
        
        return 0;
    }

    /**
     * Formatea el precio para mostrar
     * @return string Precio formateado
     */
    public function getPrecioFormateado() {
        return '$' . number_format($this->precio, 2);
    }

    /**
     * Verifica si el producto tiene stock disponible
     * @return bool True si tiene stock
     */
    public function tieneStock() {
        return $this->stock > 0;
    }
}
?>
