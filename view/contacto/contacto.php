<?php
/**
 * Vista Contacto - Página de contacto
 * Variables disponibles: $titulo, $usuario (opcional)
 */

// Si no hay datos, mostrar arrays vacíos
$mensaje_exito = $_SESSION['mensaje'] ?? '';
$mensaje_tipo = $_SESSION['mensaje_tipo'] ?? '';
$errores = $_SESSION['errores'] ?? [];

// Limpiar mensajes de sesión después de mostrarlos
unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo'], $_SESSION['errores']);
?>

<div class="contacto-container">
    <!-- Header del Contacto -->
    <div class="contacto-header">
        <h1>📞 Contáctanos</h1>
        <p>Estamos aquí para ayudarte. Envíanos un mensaje y te responderemos pronto.</p>
    </div>

    <!-- Estadísticas de Contacto -->
    <div class="contacto-stats">
        <div class="stat-card">
            <div class="stat-icon">📧</div>
            <div class="stat-number">24h</div>
            <div class="stat-label">Tiempo de Respuesta</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-number">100%</div>
            <div class="stat-label">Satisfacción del Cliente</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛡️</div>
            <div class="stat-number">24/7</div>
            <div class="stat-label">Soporte Disponible</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-number">5.0</div>
            <div class="stat-label">Calificación Promedio</div>
        </div>
    </div>

    <!-- Contenido Principal -->
    <div class="contacto-content">
        <!-- Información de Contacto -->
        <div class="contacto-info">
            <h2>📍 Información de Contacto</h2>
            
            <div class="info-item">
                <div class="info-icon">🏢</div>
                <div class="info-content">
                    <h3>Dirección</h3>
                    <p>Av. Principal 123, Centro Comercial<br>Ciudad, Estado 12345</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">📞</div>
                <div class="info-content">
                    <h3>Teléfono</h3>
                    <p>+1 (555) 123-4567<br>+1 (555) 987-6543</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">📧</div>
                <div class="info-content">
                    <h3>Email</h3>
                    <p>info@produccionesangel.com<br>ventas@produccionesangel.com</p>
                </div>
            </div>
            
            <div class="info-item">
                <div class="info-icon">🕒</div>
                <div class="info-content">
                    <h3>Horarios</h3>
                    <p>Lunes - Viernes: 9:00 AM - 6:00 PM<br>Sábados: 10:00 AM - 4:00 PM</p>
                </div>
            </div>
        </div>

        <!-- Formulario de Contacto -->
        <div class="contacto-form">
            <h2>✉️ Envíanos un Mensaje</h2>
            
            <?php if ($mensaje_exito): ?>
                <div class="alert alert-<?php echo $mensaje_tipo; ?>">
                    <?php echo htmlspecialchars($mensaje_exito); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errores)): ?>
                <div class="alert alert-error">
                    <h3>Errores encontrados:</h3>
                    <ul style="margin: 10px 0 0 20px;">
                        <?php foreach ($errores as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="index.php?controller=contacto&action=enviar">
                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">👤 Nombre Completo</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre completo" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">📧 Correo Electrónico</label>
                        <input type="email" id="email" name="email" placeholder="tu@email.com" required>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="telefono">📞 Teléfono</label>
                        <input type="tel" id="telefono" name="telefono" placeholder="+1 (555) 123-4567">
                    </div>
                    
                    <div class="form-group">
                        <label for="asunto">📋 Asunto</label>
                        <select id="asunto" name="asunto" required>
                            <option value="">Selecciona un asunto</option>
                            <option value="consulta">Consulta General</option>
                            <option value="ventas">Información de Ventas</option>
                            <option value="soporte">Soporte Técnico</option>
                            <option value="reclamo">Reclamo</option>
                            <option value="sugerencia">Sugerencia</option>
                            <option value="otro">Otro</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="mensaje">💬 Mensaje</label>
                    <textarea id="mensaje" name="mensaje" placeholder="Escribe tu mensaje aquí..." required></textarea>
                </div>
                
                <button type="submit" class="btn-enviar">
                    📤 Enviar Mensaje
                </button>
            </form>
        </div>
    </div>

    <!-- Mapa de Ubicación -->
    <div class="contacto-map">
        <h2>🗺️ Nuestra Ubicación</h2>
        <div class="map-placeholder">
            📍 Mapa interactivo próximamente disponible
        </div>
    </div>
</div>

<script>
// Validación del formulario
document.querySelector('form').addEventListener('submit', function(e) {
    const nombre = document.getElementById('nombre').value.trim();
    const email = document.getElementById('email').value.trim();
    const mensaje = document.getElementById('mensaje').value.trim();
    
    if (!nombre || !email || !mensaje) {
        e.preventDefault();
        alert('Por favor, completa todos los campos obligatorios.');
        return false;
    }
    
    // Validar email
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailRegex.test(email)) {
        e.preventDefault();
        alert('Por favor, ingresa un email válido.');
        return false;
    }
    
    // Mostrar mensaje de envío
    const btnEnviar = document.querySelector('.btn-enviar');
    btnEnviar.innerHTML = '⏳ Enviando...';
    btnEnviar.disabled = true;
});

// Animación de entrada para los elementos
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.style.opacity = '1';
            entry.target.style.transform = 'translateY(0)';
        }
    });
}, observerOptions);

// Observar elementos para animación
document.querySelectorAll('.info-item').forEach((item, index) => {
    item.style.opacity = '0';
    item.style.transform = 'translateY(20px)';
    item.style.transition = `all 0.6s ease ${index * 0.1}s`;
    observer.observe(item);
});
</script>