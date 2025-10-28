<?php
/**
 * Archivo de conexión a la base de datos
 * Utiliza mysqli para conectar con la base de datos produccionesAngel
 */

class Database {
    private $host = 'localhost';
    private $usuario = 'root';
    private $clave = '';
    private $base_datos = 'produccionesAngel';
    private $conexion;

    /**
     * Constructor - establece la conexión con la base de datos
     */
    public function __construct() {
        try {
            $this->conexion = new mysqli($this->host, $this->usuario, $this->clave, $this->base_datos);
            
            // Verificar la conexión
            if ($this->conexion->connect_error) {
                throw new Exception("Error de conexión: " . $this->conexion->connect_error);
            }
            
            // Establecer charset
            $this->conexion->set_charset("utf8");
            
        } catch (Exception $e) {
            die("Error de conexión a la base de datos: " . $e->getMessage());
        }
    }

    /**
     * Obtiene la conexión mysqli
     * @return mysqli
     */
    public function getConexion() {
        return $this->conexion;
    }

    /**
     * Ejecuta una consulta SQL
     * @param string $sql La consulta SQL a ejecutar
     * @return mysqli_result|bool Resultado de la consulta
     */
    public function ejecutarConsulta($sql) {
        try {
            $resultado = $this->conexion->query($sql);
            
            if (!$resultado) {
                throw new Exception("Error en la consulta: " . $this->conexion->error);
            }
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en consulta SQL: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Ejecuta una consulta preparada
     * @param string $sql La consulta SQL con placeholders
     * @param array $parametros Array de parámetros para la consulta
     * @return mysqli_result|bool Resultado de la consulta
     */
    public function ejecutarConsultaPreparada($sql, $parametros = []) {
        try {
            $stmt = $this->conexion->prepare($sql);
            
            if (!$stmt) {
                throw new Exception("Error preparando consulta: " . $this->conexion->error);
            }
            
            if (!empty($parametros)) {
                $tipos = '';
                $valores = [];
                
                foreach ($parametros as $parametro) {
                    if (is_int($parametro)) {
                        $tipos .= 'i';
                    } elseif (is_double($parametro)) {
                        $tipos .= 'd';
                    } else {
                        $tipos .= 's';
                    }
                    $valores[] = $parametro;
                }
                
                $stmt->bind_param($tipos, ...$valores);
            }
            
            $stmt->execute();
            $resultado = $stmt->get_result();
            
            return $resultado;
            
        } catch (Exception $e) {
            error_log("Error en consulta preparada: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Cierra la conexión a la base de datos
     */
    public function cerrarConexion() {
        if ($this->conexion) {
            $this->conexion->close();
        }
    }

    /**
     * Destructor - cierra la conexión automáticamente
     */
    public function __destruct() {
        $this->cerrarConexion();
    }
}

// Función global para obtener una instancia de la base de datos
function obtenerConexion() {
    static $instancia = null;
    if ($instancia === null) {
        $instancia = new Database();
    }
    return $instancia;
}
?>
