<?php
/**
 * Header común para todas las páginas
 * Variables disponibles: $titulo, $usuario (opcional)
 */
$titulo = $titulo ?? 'Producciones Angel';
$usuario = $usuario ?? null;
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo); ?></title>
    
    <!-- CSS específico de la vista -->
    <?php if (isset($css_file)): ?>
        <link rel="stylesheet" href="<?php echo htmlspecialchars($css_file); ?>">
    <?php endif; ?>
    
    <!-- CSS común -->
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background: #f8f9fa;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            grid-column-start: 2;
            grid-column-end: 12;
        }

        .header-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 2rem;
            font-weight: bold;
            text-decoration: none;
            color: white;
        }

        .logo:hover {
            text-shadow: 0 0 10px rgba(255,255,255,0.5);
        }

        /* Navigation */
        .nav {
            display: flex;
            list-style: none;
            gap: 2rem;
        }

        .nav a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: all 0.3s ease;
            padding: 0.5rem 1rem;
            border-radius: 5px;
        }

        .nav a:hover {
            background: rgba(255,255,255,0.2);
            transform: translateY(-2px);
        }

        /* User Info */
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
            color: white;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .header-container {
                flex-direction: column;
                gap: 20px;
            }

            .nav {
                gap: 1rem;
            }

            .user-info {
                flex-direction: column;
                gap: 10px;
            }
        }

        @media (max-width: 480px) {
            .nav {
                flex-direction: column;
                gap: 10px;
                text-align: center;
            }
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-container">
            <a href="index.php" class="logo">🏪 Producciones Angel</a>
            <nav>
                <ul class="nav">
                    <li><a href="index.php">Inicio</a></li>
                    <li><a href="index.php?controller=contacto">📞 Contacto</a></li>
                    <?php if ($usuario): ?>
                        <?php if ($usuario['role'] === 'administrador'): ?>
                            <li><a href="index.php?controller=admin">Panel Admin</a></li>
                        <?php else: ?>
                            <li><a href="index.php?controller=cliente">Mi Cuenta</a></li>
                        <?php endif; ?>
                        <li><a href="index.php?controller=auth&action=logout">Cerrar Sesión</a></li>
                    <?php else: ?>
                        <li><a href="index.php?controller=auth&action=login">🔐 Iniciar Sesión</a></li>
                    <?php endif; ?>
                    <li><a href="index.php?controller=auth&action=register">👤 Registrate</a></li>
                </ul>
            </nav>
            <?php if ($usuario): ?>
                <div class="user-info">
                    <div class="user-avatar">
                        <?php echo strtoupper(substr($usuario['nombre'], 0, 1)); ?>
                    </div>
                    <div>
                        <div style="font-weight: 600;"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                        <div style="font-size: 0.8rem; opacity: 0.8;"><?php echo ucfirst($usuario['role']); ?></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </header>

    <!-- Main Content -->
    <main>
