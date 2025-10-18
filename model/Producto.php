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
    public function getImagen() { return $this->imagen; }
    public function getPrecio() { return $this->precio; }
    public function getStock() { return $this->stock; }
    public function getFechaCreacion() { return $this->fecha_creacion; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setDescripcion($descripcion) { $this->descripcion = $descripcion; }
    public function setImagen($imagen) { $this->imagen = $imagen; }
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
        $sql = "INSERT INTO producto (nombre, descripcion, imagen, precio, stock) VALUES (?, ?, ?, ?, ?)";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [
            $datos['nombre'],
            $datos['descripcion'],
            $datos['imagen'] ?? null,
            $datos['precio'],
            $datos['stock']
        ]);
        
        return $resultado !== false;
    }

    /**
     * Actualiza un producto existente
     * @param int $id ID del producto
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        $sql = "UPDATE producto SET nombre = ?, descripcion = ?, imagen = ?, precio = ?, stock = ? WHERE id = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [
            $datos['nombre'],
            $datos['descripcion'],
            $datos['imagen'] ?? null,
            $datos['precio'],
            $datos['stock'],
            $id
        ]);
        
        return $resultado !== false;
    }

    /**
     * Elimina un producto
     * @param int $id ID del producto
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        $sql = "DELETE FROM producto WHERE id = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$id]);
        
        return $resultado !== false;
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
