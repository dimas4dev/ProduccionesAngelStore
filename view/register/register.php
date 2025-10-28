<div class="register-container">
    <div class="register-header">
        <h1>🏪 Producciones Angel</h1>
        <p>Regístrate para continuar</p>
    </div>

    <div class="register-form">
        <form method="POST" action="index.php?controller=auth&action=register" id="registerForm">
            <div class="form-group">
                <label for="nombre">📧 Nombre:</label>
                <input type="text" id="nombre" name="nombre" required 
                       placeholder="Tu nombre"
                       value="<?php echo isset($_POST['nombre']) ? htmlspecialchars($_POST['nombre']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="direccion">📍 Dirección:</label>
                <input type="text" id="direccion" name="direccion" required 
                       placeholder="Tu dirección"
                       value="<?php echo isset($_POST['direccion']) ? htmlspecialchars($_POST['direccion']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="telefono">📱 Teléfono:</label>
                <input type="tel" id="telefono" name="telefono" required 
                       placeholder="Tu teléfono"
                       value="<?php echo isset($_POST['telefono']) ? htmlspecialchars($_POST['telefono']) : ''; ?>">
            </div>

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

            <div class="form-group">
                <label for="confirmar_clave">🔒 Confirmar contraseña:</label>
                <input type="password" id="confirmar_clave" name="confirmar_clave" required 
                       placeholder="Confirmar tu contraseña">
            </div>

            <button type="submit" class="register-btn" id="submitBtn">
                Registrarse
            </button>
        </form>

        <div class="back-to-home">
            <a href="index.php">← Volver al inicio</a>
        </div>
    </div>
</div>

<script>
// Manejar el envío del formulario
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.classList.add('loading');
    submitBtn.disabled = true;
});

// Auto-focus en el campo de email
    document.getElementById('nombre').focus();

// Validación en tiempo real
const nombreInput = document.getElementById('nombre');
const direccionInput = document.getElementById('direccion');
const telefonoInput = document.getElementById('telefono');
const emailInput = document.getElementById('email');
const passwordInput = document.getElementById('clave');
const confirmarPasswordInput = document.getElementById('confirmar_clave');

confirmarPasswordInput.addEventListener('input', function() {
    const confirmarPassword = this.value;
    const password = passwordInput.value;
    
    if (confirmarPassword !== password) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

nombreInput.addEventListener('input', function() {
    const nombre = this.value;
    const nombreRegex = /^[a-zA-Z]+$/;
    
    if (nombre && !nombreRegex.test(nombre)) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

direccionInput.addEventListener('input', function() {
    const direccion = this.value;
    const direccionRegex = /^[a-zA-Z0-9\s]+$/;
    
    if (direccion && !direccionRegex.test(direccion)) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

telefonoInput.addEventListener('input', function() {
    const telefono = this.value;
    const telefonoRegex = /^[0-9]+$/;
    
    if (telefono && !telefonoRegex.test(telefono)) {
        this.style.borderColor = '#dc3545';
    } else {
        this.style.borderColor = '#e1e5e9';
    }
});

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