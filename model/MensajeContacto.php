<?php
/**
 * Modelo MensajeContacto
 * Maneja las operaciones CRUD para la tabla mensajes_contacto
 */

require_once __DIR__ . '/../config/conexion.php';

class MensajeContacto {
    private $id;
    private $nombre;
    private $email;
    private $direccion;
    private $telefono;
    private $mensaje;
    private $fecha;
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
    public function getEmail() { return $this->email; }
    public function getDireccion() { return $this->direccion; }
    public function getTelefono() { return $this->telefono; }
    public function getMensaje() { return $this->mensaje; }
    public function getFecha() { return $this->fecha; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setEmail($email) { $this->email = $email; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setMensaje($mensaje) { $this->mensaje = $mensaje; }
    public function setFecha($fecha) { $this->fecha = $fecha; }

    /**
     * Obtiene todos los mensajes de contacto
     * @return array Array de mensajes
     */
    public function obtenerTodos() {
        $sql = "SELECT * FROM mensajes_contacto ORDER BY fecha DESC";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $mensajes = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $mensaje = new MensajeContacto();
                $mensaje->setId($fila['id']);
                $mensaje->setNombre($fila['nombre']);
                $mensaje->setEmail($fila['email']);
                $mensaje->setDireccion($fila['direccion']);
                $mensaje->setTelefono($fila['telefono']);
                $mensaje->setMensaje($fila['mensaje']);
                $mensaje->setFecha($fila['fecha']);
                $mensajes[] = $mensaje;
            }
        }
        
        return $mensajes;
    }

    /**
     * Obtiene un mensaje por ID
     * @param int $id ID del mensaje
     * @return MensajeContacto|null Mensaje encontrado o null
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM mensajes_contacto WHERE id = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$id]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->setId($fila['id']);
            $this->setNombre($fila['nombre']);
            $this->setEmail($fila['email']);
            $this->setDireccion($fila['direccion']);
            $this->setTelefono($fila['telefono']);
            $this->setMensaje($fila['mensaje']);
            $this->setFecha($fila['fecha']);
            return $this;
        }
        
        return null;
    }

    /**
     * Crea un nuevo mensaje de contacto
     * @param array $datos Datos del mensaje
     * @return bool True si se creó correctamente
     */
    public function crear($datos) {
        try {
            $sql = "INSERT INTO mensajes_contacto (nombre, email, direccion, telefono, mensaje) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->getConexion()->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->getConexion()->error);
            }
            
            $nombre = $datos['nombre'];
            $email = $datos['email'];
            $direccion = $datos['direccion'] ?? null;
            $telefono = $datos['telefono'] ?? null;
            $mensaje = $datos['mensaje'];
            
            $stmt->bind_param("sssss", $nombre, $email, $direccion, $telefono, $mensaje);
            $resultado = $stmt->execute();
            $stmt->close();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al crear mensaje de contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un mensaje de contacto
     * @param int $id ID del mensaje
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM mensajes_contacto WHERE id = ?";
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
            error_log("Error al eliminar mensaje de contacto: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene el número total de mensajes
     * @return int Número total de mensajes
     */
    public function contar() {
        $sql = "SELECT COUNT(*) as total FROM mensajes_contacto";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return (int)$fila['total'];
        }
        
        return 0;
    }

    /**
     * Formatea la fecha para mostrar
     * @return string Fecha formateada
     */
    public function getFechaFormateada() {
        return date('d/m/Y H:i', strtotime($this->fecha));
    }

    /**
     * Obtiene mensajes recientes (últimos 30 días)
     * @return array Array de mensajes recientes
     */
    public function obtenerRecientes() {
        $sql = "SELECT * FROM mensajes_contacto WHERE fecha >= DATE_SUB(NOW(), INTERVAL 30 DAY) ORDER BY fecha DESC";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $mensajes = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $mensaje = new MensajeContacto();
                $mensaje->setId($fila['id']);
                $mensaje->setNombre($fila['nombre']);
                $mensaje->setEmail($fila['email']);
                $mensaje->setDireccion($fila['direccion']);
                $mensaje->setTelefono($fila['telefono']);
                $mensaje->setMensaje($fila['mensaje']);
                $mensaje->setFecha($fila['fecha']);
                $mensajes[] = $mensaje;
            }
        }
        
        return $mensajes;
    }
}
?>
