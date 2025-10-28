<?php
/**
 * Vista de Login
 * Formulario para autenticación de usuarios
 */
?>

<div class="login-container">
    <div class="login-header">
        <h1>🏪 Producciones Angel</h1>
        <p>Inicia sesión para continuar</p>
    </div>

    <div class="login-form">
        <form method="POST" action="index.php?controller=auth&action=login" id="loginForm">
            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" required 
                       placeholder="tu@email.com"
                       value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="clave">🔒 Contraseña:</label>
                <input type="password" id="clave" name="clave" required 
                       placeholder="Tu contraseña">
            </div>

            <button type="submit" class="login-btn" id="submitBtn">
                Iniciar Sesión
            </button>
        </form>

        <div class="login-links">
            <a href="#" onclick="alert('Funcionalidad de recuperación de contraseña próximamente disponible')">
                ¿Olvidaste tu contraseña?
            </a>
        </div>

        <div class="back-to-home">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </div>
</div>

<script>
// Manejar el envío del formulario
document.getElementById('loginForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});

// Auto-focus en el campo de email
document.getElementById('email').focus();

// Validación en tiempo real
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('clave');

emailInput.addEventListener('input', function() {
    const email = this.value;
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (email && !emailRegex.test(email)) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

passwordInput.addEventListener('input', function() {
    if (this.value.length < 6 && this.value.length > 0) {
        this.style.borderColor = '#ffc107';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

// Mostrar/ocultar contraseña
const togglePassword = document.createElement('button');
togglePassword.type = 'button';
togglePassword.innerHTML = '👁️';
togglePassword.style.cssText = `
    position: absolute;
    right: 10px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    font-size: 1.2rem;
    padding: 5px;
`;

const passwordGroup = passwordInput.parentElement;
passwordGroup.style.position = 'relative';
passwordGroup.appendChild(togglePassword);

togglePassword.addEventListener('click', function() {
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        this.innerHTML = '🙈';
    } else {
        passwordInput.type = 'password';
        this.innerHTML = '👁️';
    }
});
</script>
