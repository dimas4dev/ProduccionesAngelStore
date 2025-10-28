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
     * Crea un nuevo producto
     */
    public function crearProducto() {
        try {
            // Manejar la carga de imagen
            $imagen_binaria = null;
            $imagen_tipo = null;
            $imagen_tamano = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                // Obtener información del archivo
                $archivoTmp = $_FILES['imagen']['tmp_name'];
                $nombreOriginal = $_FILES['imagen']['name'];
                $tipoArchivo = $_FILES['imagen']['type'];
                $tamanoArchivo = $_FILES['imagen']['size'];
                
                // Validar tipo de archivo
                $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP");
                }
                
                // Validar tamaño (máximo 10MB)
                if ($tamanoArchivo > 10 * 1024 * 1024) {
                    throw new Exception("El archivo es demasiado grande. Máximo 10MB permitido");
                }
                
                // Leer el archivo como binario
                $imagen_binaria = file_get_contents($archivoTmp);
                if ($imagen_binaria === false) {
                    throw new Exception("Error al leer el archivo: $nombreOriginal");
                }
                
                $imagen_tipo = $tipoArchivo;
                $imagen_tamano = $tamanoArchivo;
            }

            // Recoger datos del formulario
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0),
                'imagen_binaria' => $imagen_binaria,
                'imagen_tipo' => isset($_FILES['imagen']) ? $_FILES['imagen']['type'] : null,
                'imagen_tamano' => isset($_FILES['imagen']) ? $_FILES['imagen']['size'] : null
            ];

            // Validar datos
            $errores = $this->validarProducto($datos);
            
            if (empty($errores)) {
                if ($this->producto->crear($datos)) {
                    $_SESSION['mensaje'] = "Producto creado exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al crear el producto";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            error_log("Error en crearProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al crear el producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Actualiza un producto existente
     */
    public function actualizarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            
            // Manejar la carga de imagen si se proporciona una nueva
            $imagen_binaria = null;
            $imagen_tipo = null;
            $imagen_tamano = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                // Obtener información del archivo
                $archivoTmp = $_FILES['imagen']['tmp_name'];
                $nombreOriginal = $_FILES['imagen']['name'];
                $tipoArchivo = $_FILES['imagen']['type'];
                $tamanoArchivo = $_FILES['imagen']['size'];
                
                // Validar tipo de archivo
                $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
                if (!in_array($tipoArchivo, $tiposPermitidos)) {
                    throw new Exception("Tipo de archivo no permitido. Solo se permiten: JPG, PNG, GIF, WEBP");
                }
                
                // Validar tamaño (máximo 10MB)
                if ($tamanoArchivo > 10 * 1024 * 1024) {
                    throw new Exception("El archivo es demasiado grande. Máximo 10MB permitido");
                }
                
                // Leer el archivo como binario
                $imagen_binaria = file_get_contents($archivoTmp);
                if ($imagen_binaria === false) {
                    throw new Exception("Error al leer el archivo: $nombreOriginal");
                }
                
                $imagen_tipo = $tipoArchivo;
                $imagen_tamano = $tamanoArchivo;
            }
            
            $datos = [
                'nombre' => $_POST['nombre'] ?? '',
                'descripcion' => $_POST['descripcion'] ?? '',
                'imagen_binaria' => $imagen_binaria,
                'imagen_tipo' => $imagen_tipo,
                'imagen_tamano' => $imagen_tamano,
                'precio' => floatval($_POST['precio'] ?? 0),
                'stock' => intval($_POST['stock'] ?? 0)
            ];

            $errores = $this->validarProducto($datos);
            
            if (empty($errores)) {
                if ($this->producto->actualizar($id, $datos)) {
                    $_SESSION['mensaje'] = "Producto actualizado exitosamente";
                    $_SESSION['mensaje_tipo'] = 'success';
                } else {
                    $_SESSION['mensaje'] = "Error al actualizar el producto";
                    $_SESSION['mensaje_tipo'] = 'error';
                }
            } else {
                $_SESSION['errores'] = $errores;
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            error_log("Error en actualizarProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al actualizar producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php');
            exit;
        }
    }

    /**
     * Elimina un producto
     */
    public function eliminarProducto() {
        try {
            $id = $_POST['id'] ?? 0;
            
            if ($this->producto->eliminar($id)) {
                $_SESSION['mensaje'] = "Producto eliminado exitosamente";
                $_SESSION['mensaje_tipo'] = 'success';
            } else {
                $_SESSION['mensaje'] = "Error al eliminar el producto";
                $_SESSION['mensaje_tipo'] = 'error';
            }
            
            header('Location: index.php');
            exit;
        } catch (Exception $e) {
            error_log("Error en eliminarProducto: " . $e->getMessage());
            $_SESSION['mensaje'] = "Error al eliminar producto: " . $e->getMessage();
            $_SESSION['mensaje_tipo'] = 'error';
            header('Location: index.php');
            exit;
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
