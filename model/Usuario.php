<?php
/**
 * Modelo Usuario
 * Maneja las operaciones CRUD y autenticación para la tabla usuarios
 */

require_once __DIR__ . '/../config/conexion.php';

class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $clave;
    private $direccion;
    private $telefono;
    private $role;
    private $fecha_registro;
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
    public function getClave() { return $this->clave; }
    public function getDireccion() { return $this->direccion; }
    public function getTelefono() { return $this->telefono; }
    public function getRole() { return $this->role; }
    public function getFechaRegistro() { return $this->fecha_registro; }

    // Setters
    public function setId($id) { $this->id = $id; }
    public function setNombre($nombre) { $this->nombre = $nombre; }
    public function setEmail($email) { $this->email = $email; }
    public function setClave($clave) { $this->clave = $clave; }
    public function setDireccion($direccion) { $this->direccion = $direccion; }
    public function setTelefono($telefono) { $this->telefono = $telefono; }
    public function setRole($role) { $this->role = $role; }
    public function setFechaRegistro($fecha_registro) { $this->fecha_registro = $fecha_registro; }

    /**
     * Autentica un usuario por email y contraseña
     * @param string $email Email del usuario
     * @param string $clave Contraseña del usuario
     * @return Usuario|null Usuario autenticado o null si falla
     */
    public function autenticar($email, $clave) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$email]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            
            // Verificar la contraseña (asumiendo que está hasheada con password_hash)
            if (password_verify($clave, $fila['clave'])) {
                $this->setId($fila['id']);
                $this->setNombre($fila['nombre']);
                $this->setEmail($fila['email']);
                $this->setClave($fila['clave']);
                $this->setDireccion($fila['direccion']);
                $this->setTelefono($fila['telefono']);
                $this->setRole($fila['role']);
                $this->setFechaRegistro($fila['fecha_registro']);
                return $this;
            }
        }
        
        return null;
    }

    /**
     * Obtiene un usuario por ID
     * @param int $id ID del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function obtenerPorId($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$id]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->setId($fila['id']);
            $this->setNombre($fila['nombre']);
            $this->setEmail($fila['email']);
            $this->setClave($fila['clave']);
            $this->setDireccion($fila['direccion']);
            $this->setTelefono($fila['telefono']);
            $this->setRole($fila['role']);
            $this->setFechaRegistro($fila['fecha_registro']);
            return $this;
        }
        
        return null;
    }

    /**
     * Obtiene un usuario por email
     * @param string $email Email del usuario
     * @return Usuario|null Usuario encontrado o null
     */
    public function obtenerPorEmail($email) {
        $sql = "SELECT * FROM usuarios WHERE email = ?";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$email]);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            $this->setId($fila['id']);
            $this->setNombre($fila['nombre']);
            $this->setEmail($fila['email']);
            $this->setClave($fila['clave']);
            $this->setDireccion($fila['direccion']);
            $this->setTelefono($fila['telefono']);
            $this->setRole($fila['role']);
            $this->setFechaRegistro($fila['fecha_registro']);
            return $this;
        }
        
        return null;
    }

    /**
     * Crea un nuevo usuario
     * @param array $datos Datos del usuario
     * @return array Resultado con 'success' y 'message'
     */
    public function crear($datos) {
        try {
            // Validar que el email no exista
            if ($this->emailExiste($datos['email'])) {
                return [
                    'success' => false,
                    'message' => 'El email ya está registrado. Por favor, usa otro email.'
                ];
            }

            // Hash de la contraseña
            $claveHasheada = password_hash($datos['clave'], PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO usuarios (nombre, email, clave, direccion, telefono, role) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt = $this->db->getConexion()->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->db->getConexion()->error);
            }
            
            $nombre = $datos['nombre'];
            $email = $datos['email'];
            $direccion = $datos['direccion'] ?? null;
            $telefono = $datos['telefono'] ?? null;
            $role = $datos['role'] ?? 'cliente';
            
            $stmt->bind_param("ssssss", $nombre, $email, $claveHasheada, $direccion, $telefono, $role);
            $resultado = $stmt->execute();
            $stmt->close();
            
            if ($resultado) {
                return [
                    'success' => true,
                    'message' => 'Usuario registrado exitosamente. Ya puedes iniciar sesión.'
                ];
            } else {
                return [
                    'success' => false,
                    'message' => 'Error al crear el usuario. Por favor, intenta nuevamente.'
                ];
            }
            
        } catch (Exception $e) {
            error_log("Error al crear usuario: " . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error interno del servidor. Por favor, intenta más tarde.'
            ];
        }
    }

    /**
     * Actualiza un usuario existente
     * @param int $id ID del usuario
     * @param array $datos Datos a actualizar
     * @return bool True si se actualizó correctamente
     */
    public function actualizar($id, $datos) {
        try {
            // Si se proporciona una nueva contraseña, actualizarla
            if (!empty($datos['clave'])) {
                $sql = "UPDATE usuarios SET nombre = ?, email = ?, clave = ?, direccion = ?, telefono = ?, role = ? WHERE id = ?";
                $claveHasheada = password_hash($datos['clave'], PASSWORD_DEFAULT);
                
                $stmt = $this->db->getConexion()->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparando consulta: " . $this->db->getConexion()->error);
                }
                
                $nombre = $datos['nombre'];
                $email = $datos['email'];
                $direccion = $datos['direccion'] ?? null;
                $telefono = $datos['telefono'] ?? null;
                $role = $datos['role'];
                $id_int = (int)$id;
                
                $stmt->bind_param("ssssssi", $nombre, $email, $claveHasheada, $direccion, $telefono, $role, $id_int);
            } else {
                $sql = "UPDATE usuarios SET nombre = ?, email = ?, direccion = ?, telefono = ?, role = ? WHERE id = ?";
                
                $stmt = $this->db->getConexion()->prepare($sql);
                if (!$stmt) {
                    throw new Exception("Error preparando consulta: " . $this->db->getConexion()->error);
                }
                
                $nombre = $datos['nombre'];
                $email = $datos['email'];
                $direccion = $datos['direccion'] ?? null;
                $telefono = $datos['telefono'] ?? null;
                $role = $datos['role'];
                $id_int = (int)$id;
                
                $stmt->bind_param("sssssi", $nombre, $email, $direccion, $telefono, $role, $id_int);
            }
            
            $resultado = $stmt->execute();
            $stmt->close();
            
            return $resultado;
        } catch (Exception $e) {
            error_log("Error al actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Elimina un usuario
     * @param int $id ID del usuario
     * @return bool True si se eliminó correctamente
     */
    public function eliminar($id) {
        try {
            $sql = "DELETE FROM usuarios WHERE id = ?";
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
            error_log("Error al eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtiene todos los usuarios
     * @return array Array de usuarios
     */
    public function obtenerTodos() {
        $sql = "SELECT id, nombre, email, direccion, telefono, role, fecha_registro FROM usuarios ORDER BY fecha_registro DESC";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        $usuarios = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuario = new Usuario();
                $usuario->setId($fila['id']);
                $usuario->setNombre($fila['nombre']);
                $usuario->setEmail($fila['email']);
                $usuario->setDireccion($fila['direccion']);
                $usuario->setTelefono($fila['telefono']);
                $usuario->setRole($fila['role']);
                $usuario->setFechaRegistro($fila['fecha_registro']);
                $usuarios[] = $usuario;
            }
        }
        
        return $usuarios;
    }

    /**
     * Obtiene usuarios por rol
     * @param string $role Rol del usuario
     * @return array Array de usuarios
     */
    public function obtenerPorRol($role) {
        $sql = "SELECT id, nombre, email, direccion, telefono, role, fecha_registro FROM usuarios WHERE role = ? ORDER BY fecha_registro DESC";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [$role]);
        
        $usuarios = [];
        if ($resultado && $resultado->num_rows > 0) {
            while ($fila = $resultado->fetch_assoc()) {
                $usuario = new Usuario();
                $usuario->setId($fila['id']);
                $usuario->setNombre($fila['nombre']);
                $usuario->setEmail($fila['email']);
                $usuario->setDireccion($fila['direccion']);
                $usuario->setTelefono($fila['telefono']);
                $usuario->setRole($fila['role']);
                $usuario->setFechaRegistro($fila['fecha_registro']);
                $usuarios[] = $usuario;
            }
        }
        
        return $usuarios;
    }

    /**
     * Verifica si un email ya existe
     * @param string $email Email a verificar
     * @param int $idExcluir ID del usuario a excluir de la verificación (para actualizaciones)
     * @return bool True si el email existe
     */
    public function emailExiste($email, $idExcluir = null) {
        if ($idExcluir) {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ? AND id != ?";
            $resultado = $this->db->ejecutarConsultaPreparada($sql, [$email, $idExcluir]);
        } else {
            $sql = "SELECT COUNT(*) as total FROM usuarios WHERE email = ?";
            $resultado = $this->db->ejecutarConsultaPreparada($sql, [$email]);
        }
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return $fila['total'] > 0;
        }
        
        return false;
    }

    /**
     * Obtiene el número total de usuarios
     * @return int Número total de usuarios
     */
    public function contar() {
        $sql = "SELECT COUNT(*) as total FROM usuarios";
        $resultado = $this->db->ejecutarConsulta($sql);
        
        if ($resultado && $resultado->num_rows > 0) {
            $fila = $resultado->fetch_assoc();
            return (int)$fila['total'];
        }
        
        return 0;
    }

    /**
     * Verifica si el usuario es administrador
     * @return bool True si es administrador
     */
    public function esAdministrador() {
        return $this->role === 'administrador';
    }

    /**
     * Verifica si el usuario es cliente
     * @return bool True si es cliente
     */
    public function esCliente() {
        return $this->role === 'cliente';
    }

    /**
     * Valida los datos de registro
     * @param array $datos Datos a validar
     * @return array Array con errores de validación (vacío si no hay errores)
     */
    public function validarDatosRegistro($datos) {
        $errores = [];

        // Validar nombre
        if (empty(trim($datos['nombre']))) {
            $errores[] = "El nombre es obligatorio";
        } elseif (strlen(trim($datos['nombre'])) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        }

        // Validar email
        if (empty(trim($datos['email']))) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        // Validar contraseña
        if (empty($datos['clave'])) {
            $errores[] = "La contraseña es obligatoria";
        } elseif (strlen($datos['clave']) < 6) {
            $errores[] = "La contraseña debe tener al menos 6 caracteres";
        }

        // Validar confirmación de contraseña
        if (empty($datos['confirmar_clave'])) {
            $errores[] = "Debes confirmar tu contraseña";
        } elseif ($datos['clave'] !== $datos['confirmar_clave']) {
            $errores[] = "Las contraseñas no coinciden";
        }

        // Validar teléfono (opcional pero si se proporciona debe ser válido)
        if (!empty($datos['telefono'])) {
            $telefono = preg_replace('/[^0-9+\-\(\)\s]/', '', $datos['telefono']);
            if (strlen($telefono) < 7) {
                $errores[] = "El teléfono debe tener al menos 7 dígitos";
            }
        }

        return $errores;
    }

    /**
     * Formatea la fecha de registro para mostrar
     * @return string Fecha formateada
     */
    public function getFechaRegistroFormateada() {
        return date('d/m/Y H:i', strtotime($this->fecha_registro));
    }
}
?>
