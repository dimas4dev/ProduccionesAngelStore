<?php
/**
 * Controlador de Contacto
 * Maneja el envío de mensajes de contacto
 */

require_once __DIR__ . '/../model/Usuario.php';
require_once __DIR__ . '/../model/MensajeContacto.php';

class ContactoController {
    private $usuarioModel;
    private $mensajeModel;

    /**
     * Constructor
     */
    public function __construct() {
        $this->usuarioModel = new Usuario();
        $this->mensajeModel = new MensajeContacto();
        
        // Iniciar sesión si no está iniciada
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Muestra la página de contacto
     */
    public function contacto() {
        // Configurar título y CSS
        $titulo = 'Contacto - Producciones Angel';
        $css_file = 'view/contacto/contacto.css';
        
        // Incluir header común
        include __DIR__ . '/../view/layouts/header.php';
        
        // Cargar la vista de contacto
        include __DIR__ . '/../view/contacto/contacto.php';
        
        // Incluir footer común
        include __DIR__ . '/../view/layouts/footer.php';
    }

    /**
     * Procesa el envío del formulario de contacto
     */
    public function enviar() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                // Recoger datos del formulario
                $datos = [
                    'nombre' => trim($_POST['nombre'] ?? ''),
                    'email' => trim($_POST['email'] ?? ''),
                    'telefono' => trim($_POST['telefono'] ?? ''),
                    'asunto' => $_POST['asunto'] ?? '',
                    'mensaje' => trim($_POST['mensaje'] ?? '')
                ];

                // Validar datos
                $errores = $this->validarDatosContacto($datos);
                
                if (empty($errores)) {
                    // Guardar mensaje en la base de datos
                    if ($this->mensajeModel->crear($datos)) {
                        $_SESSION['mensaje'] = "¡Mensaje enviado exitosamente! Te responderemos pronto.";
                        $_SESSION['mensaje_tipo'] = 'success';
                    } else {
                        $_SESSION['mensaje'] = "Error al enviar el mensaje. Por favor, intenta nuevamente.";
                        $_SESSION['mensaje_tipo'] = 'error';
                    }
                } else {
                    $_SESSION['errores'] = $errores;
                    $_SESSION['mensaje_tipo'] = 'error';
                }
                
                // Redirigir de vuelta al formulario
                header('Location: index.php?controller=contacto&action=contacto');
                exit;
                
            } catch (Exception $e) {
                error_log("Error en enviar mensaje de contacto: " . $e->getMessage());
                $_SESSION['mensaje'] = "Error interno del servidor. Por favor, intenta más tarde.";
                $_SESSION['mensaje_tipo'] = 'error';
                header('Location: index.php?controller=contacto&action=contacto');
                exit;
            }
        } else {
            // Si no es POST, redirigir al formulario
            header('Location: index.php?controller=contacto&action=contacto');
            exit;
        }
    }

    /**
     * Valida los datos del formulario de contacto
     * @param array $datos Datos del formulario
     * @return array Array de errores
     */
    private function validarDatosContacto($datos) {
        $errores = [];

        // Validar nombre
        if (empty($datos['nombre'])) {
            $errores[] = "El nombre es obligatorio";
        } elseif (strlen($datos['nombre']) < 2) {
            $errores[] = "El nombre debe tener al menos 2 caracteres";
        }

        // Validar email
        if (empty($datos['email'])) {
            $errores[] = "El email es obligatorio";
        } elseif (!filter_var($datos['email'], FILTER_VALIDATE_EMAIL)) {
            $errores[] = "El formato del email no es válido";
        }

        // Validar asunto
        if (empty($datos['asunto'])) {
            $errores[] = "Debes seleccionar un asunto";
        }

        // Validar mensaje
        if (empty($datos['mensaje'])) {
            $errores[] = "El mensaje es obligatorio";
        } elseif (strlen($datos['mensaje']) < 10) {
            $errores[] = "El mensaje debe tener al menos 10 caracteres";
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
}
?>
