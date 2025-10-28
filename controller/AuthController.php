<?php
/**
 * Controlador de Autenticación
 * Maneja el login, logout y sesiones de usuarios
 */

require_once __DIR__ . '/../model/Usuario.php';

class AuthController {
    private $usuario;

    /**
     * Constructor
     */
    public function __construct() {
        $this->usuario = new Usuario();
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Procesa el login del usuario
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $clave = $_POST['clave'] ?? '';

            // Validar datos de entrada
            $errores = $this->validarDatosLogin($email, $clave);

            if (empty($errores)) {
                // Intentar autenticar al usuario
                $usuarioAutenticado = $this->usuario->autenticar($email, $clave);

                if ($usuarioAutenticado) {
                    // Guardar datos del usuario en la sesión
                    $_SESSION['usuario_id'] = $usuarioAutenticado->getId();
                    $_SESSION['usuario_nombre'] = $usuarioAutenticado->getNombre();
                    $_SESSION['usuario_email'] = $usuarioAutenticado->getEmail();
                    $_SESSION['usuario_role'] = $usuarioAutenticado->getRole();
                    $_SESSION['login_time'] = time();

                    // Redirigir según el rol
                    $this->redirigirSegunRol($usuarioAutenticado->getRole());
                } else {
                    $this->mostrarError("Email o contraseña incorrectos");
                    $this->mostrarFormularioLogin();
                }
            } else {
                $this->mostrarErrores($errores);
                $this->mostrarFormularioLogin();
            }
        } else {
            // Si ya está logueado, redirigir según su rol
            if ($this->estaLogueado()) {
                $this->redirigirSegunRol($_SESSION['usuario_role']);
            } else {
                $this->mostrarFormularioLogin();
            }
        }
    }

    /**
     * Procesa el registro del usuario
     */

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => trim($_POST['nombre'] ?? ''),
                'email' => trim($_POST['email'] ?? ''),
                'clave' => $_POST['clave'] ?? '',
                'confirmar_clave' => $_POST['confirmar_clave'] ?? '',
                'direccion' => trim($_POST['direccion'] ?? ''),
                'telefono' => trim($_POST['telefono'] ?? ''),
                'role' => 'cliente'
            ];

            // Validar datos usando el método del modelo
            $errores = $this->usuario->validarDatosRegistro($datos);

            if (empty($errores)) {
                $resultado = $this->usuario->crear($datos);

                if ($resultado['success']) {
                    $this->mostrarExito($resultado['message']);
                    $this->mostrarFormularioRegister();
                } else {
                    $this->mostrarError($resultado['message']);
                    $this->mostrarFormularioRegister();
                }
            } else {
                $this->mostrarErrores($errores);
                $this->mostrarFormularioRegister();
            }
        } else {
            $this->mostrarFormularioRegister();
        }
    }

    /**
     * Cierra la sesión del usuario
     */
    public function logout() {
        // Destruir todas las variables de sesión
        $_SESSION = array();

        // Destruir la cookie de sesión si existe
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        // Destruir la sesión
        session_destroy();

        // Redirigir al inicio
        echo "<script>window.location.href = 'index.php';</script>";
        exit;
    }

    /**
     * Verifica si el usuario está logueado
     * @return bool True si está logueado
     */
    public function estaLogueado() {
        return isset($_SESSION['usuario_id']) && 
               isset($_SESSION['usuario_role']) && 
               isset($_SESSION['login_time']);
    }

    /**
     * Verifica si el usuario tiene un rol específico
     * @param string $role Rol a verificar
     * @return bool True si tiene el rol
     */
    public function tieneRol($role) {
        return $this->estaLogueado() && $_SESSION['usuario_role'] === $role;
    }

    /**
     * Obtiene los datos del usuario actual
     * @return array|null Datos del usuario o null si no está logueado
     */
    public function obtenerUsuarioActual() {
        if (!$this->estaLogueado()) {
            return null;
        }

        return [
            'id' => $_SESSION['usuario_id'],
            'nombre' => $_SESSION['usuario_nombre'],
            'email' => $_SESSION['usuario_email'],
            'role' => $_SESSION['usuario_role'],
            'login_time' => $_SESSION['login_time']
        ];
    }

    /**
     * Requiere que el usuario esté logueado
     * Si no está logueado, redirige al login
     */
    public function requerirLogin() {
        if (!$this->estaLogueado()) {
            echo "<script>";
            echo "alert('Debes iniciar sesión para acceder a esta página');";
            echo "window.location.href = 'index.php?controller=auth&action=login';";
            echo "</script>";
            exit;
        }
    }

    /**
     * Requiere que el usuario tenga un rol específico
     * @param string $role Rol requerido
     */
    public function requerirRol($role) {
        $this->requerirLogin();
        
        if (!$this->tieneRol($role)) {
            echo "<script>";
            echo "alert('No tienes permisos para acceder a esta página');";
            echo "window.location.href = 'index.php';";
            echo "</script>";
            exit;
        }
    }

    /**
     * Redirige al usuario según su rol
     * @param string $role Rol del usuario
     */
    private function redirigirSegunRol($role) {
        switch ($role) {
            case 'administrador':
                echo "<script>window.location.href = 'index.php?controller=admin';</script>";
                break;
            case 'cliente':
                echo "<script>window.location.href = 'index.php?controller=cliente';</script>";
                break;
            default:
                echo "<script>window.location.href = 'index.php';</script>";
                break;
        }
        exit;
    }

    /**
     * Muestra el formulario de login
     */
    private function mostrarFormularioLogin() {
        $this->cargarVista('login');
    }

    /**
     * Muestra el formulario de registro
     */
    private function mostrarFormularioRegister() {
        $this->cargarVista('register');
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
     * Valida los datos del formulario de login
     * @param string $email Email del usuario
     * @param string $clave Contraseña del usuario
     * @return array Array de errores
     */
    private function validarDatosLogin($email, $clave) {
        $errores = [];

        if (empty($email)) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        if (empty($clave)) {
            $errores[] = "La contraseña es obligatoria";
        }

        return $errores;
    }

    /**
     * Muestra errores de validación
     * @param array $errores Array de errores
     */
    private function mostrarErrores($errores) {
        echo "<div class='alert alert-error'>";
        echo "<h3>Errores encontrados:</h3>";
        echo "<ul>";
        foreach ($errores as $error) {
            echo "<li>" . htmlspecialchars($error) . "</li>";
        }
        echo "</ul>";
        echo "</div>";
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
     * Carga una vista específica
     * @param string $vista Nombre de la vista
     * @param array $datos Datos para pasar a la vista
     */
    private function cargarVista($vista, $datos = []) {
        // Extraer variables del array de datos
        extract($datos);
        
        // Configurar título y CSS según la vista
        $titulo = ($vista === 'register') ? 'Registro - Producciones Angel' : 'Login - Producciones Angel';
        $css_file = 'view/' . $vista . '/' . $vista . '.css';
        
        // Incluir header común
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
     * Obtiene el tiempo de sesión restante en minutos
     * @return int Minutos restantes
     */
    public function obtenerTiempoSesionRestante() {
        if (!$this->estaLogueado()) {
            return 0;
        }

        $tiempoMaximo = 60 * 60; // 1 hora en segundos
        $tiempoTranscurrido = time() - $_SESSION['login_time'];
        $tiempoRestante = $tiempoMaximo - $tiempoTranscurrido;

        return max(0, floor($tiempoRestante / 60));
    }

    /**
     * Verifica si la sesión ha expirado
     * @return bool True si ha expirado
     */
    public function sesionExpirada() {
        if (!$this->estaLogueado()) {
            return true;
        }

        $tiempoMaximo = 60 * 60; // 1 hora en segundos
        $tiempoTranscurrido = time() - $_SESSION['login_time'];

        return $tiempoTranscurrido > $tiempoMaximo;
    }

    /**
     * Renueva la sesión del usuario
     */
    public function renovarSesion() {
        if ($this->estaLogueado()) {
            $_SESSION['login_time'] = time();
        }
    }
}
?>
