<?php
/**
 * Vista Admin - Panel de administración
 * Variables disponibles: $usuario, $productos, $usuarios, $ventas, $mensajes
 */

// Si no hay datos, mostrar arrays vacíos
if (empty($productos)) {
    $productos = [];
}
if (empty($usuarios)) {
    $usuarios = [];
}
if (empty($ventas)) {
    $ventas = [];
}
if (empty($mensajes)) {
    $mensajes = [];
}
?>

<div class="admin-dashboard">
    <!-- Header del Dashboard -->
    <div class="dashboard-header">
        <h1>👨‍💼 Panel de Administración</h1>
        <p>Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></p>
    </div>

    <!-- Estadísticas Generales -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📦</div>
            <div class="stat-content">
                <h3><?php echo count($productos); ?></h3>
                <p>Productos Total</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">👥</div>
            <div class="stat-content">
                <h3><?php echo count($usuarios); ?></h3>
                <p>Usuarios Registrados</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">💰</div>
            <div class="stat-content">
                <h3><?php echo count($ventas); ?></h3>
                <p>Ventas Realizadas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3>$0.00</h3>
                <p>Ingresos Totales</p>
            </div>
        </div>
    </div>

    <!-- Navegación del Panel -->
    <div class="admin-nav">
        <button class="nav-btn active" onclick="showSection('productos')">📦 Productos</button>
        <button class="nav-btn" onclick="showSection('usuarios')">👥 Usuarios</button>
        <button class="nav-btn" onclick="showSection('ventas')">💰 Ventas</button>
        <button class="nav-btn" onclick="showSection('mensajes')">📧 Mensajes</button>
    </div>

    <!-- Sección de Productos -->
    <div id="productos-section" class="admin-section active">
        <div class="section-header">
            <h2>📦 Gestión de Productos</h2>
            <button class="btn-add" onclick="agregarProducto()">➕ Agregar Producto</button>
        </div>
        
        <div class="products-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="product-admin-card">
                    <div class="product-image">
                        <?php if ($producto->getImagen()): ?>
                            <img src="<?php echo htmlspecialchars($producto->getImagen()); ?>" 
                                 alt="<?php echo htmlspecialchars($producto->getNombre()); ?>">
                        <?php else: ?>
                            <div class="no-image">📦</div>
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?php echo htmlspecialchars($producto->getNombre()); ?></h3>
                        <p class="product-price"><?php echo $producto->getPrecioFormateado(); ?></p>
                        <p class="product-stock">Stock: <?php echo $producto->getStock(); ?></p>
                        <div class="product-actions">
                            <button class="btn-edit" onclick="editarProducto(<?php echo $producto->getId(); ?>)">✏️ Editar</button>
                            <button class="btn-delete" onclick="eliminarProducto(<?php echo $producto->getId(); ?>)">🗑️ Eliminar</button>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Sección de Usuarios -->
    <div id="usuarios-section" class="admin-section">
        <div class="section-header">
            <h2>👥 Gestión de Usuarios</h2>
            <button class="btn-add" onclick="agregarUsuario()">➕ Agregar Usuario</button>
        </div>
        
        <div class="users-table">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Email</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?php echo $user->getId(); ?></td>
                            <td><?php echo htmlspecialchars($user->getNombre()); ?></td>
                            <td><?php echo htmlspecialchars($user->getEmail()); ?></td>
                            <td>
                                <span class="role-badge <?php echo $user->getRole(); ?>">
                                    <?php echo ucfirst($user->getRole()); ?>
                                </span>
                            </td>
                            <td><?php echo $user->getFechaRegistroFormateada(); ?></td>
                            <td>
                                <button class="btn-edit" onclick="editarUsuario(<?php echo $user->getId(); ?>)">✏️</button>
                                <button class="btn-delete" onclick="eliminarUsuario(<?php echo $user->getId(); ?>)">🗑️</button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sección de Ventas -->
    <div id="ventas-section" class="admin-section">
        <div class="section-header">
            <h2>💰 Historial de Ventas</h2>
        </div>
        
        <div class="sales-table">
            <table>
                <thead>
                    <tr>
                        <th>ID Venta</th>
                        <th>Cliente</th>
                        <th>Fecha</th>
                        <th>Total</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($ventas)): ?>
                        <tr>
                            <td colspan="6" class="no-data">No hay ventas registradas</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($ventas as $venta): ?>
                            <tr>
                                <td><?php echo $venta['id']; ?></td>
                                <td><?php echo htmlspecialchars($venta['cliente']); ?></td>
                                <td><?php echo $venta['fecha']; ?></td>
                                <td><?php echo $venta['total']; ?></td>
                                <td><span class="status-badge completed">Completada</span></td>
                                <td>
                                    <button class="btn-view" onclick="verDetalleVenta(<?php echo $venta['id']; ?>)">👁️ Ver</button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Sección de Mensajes -->
    <div id="mensajes-section" class="admin-section">
        <div class="section-header">
            <h2>📧 Mensajes de Contacto</h2>
        </div>
        
        <div class="messages-list">
            <?php if (empty($mensajes)): ?>
                <div class="no-data">No hay mensajes de contacto</div>
            <?php else: ?>
                <?php foreach ($mensajes as $mensaje): ?>
                    <div class="message-card">
                        <div class="message-header">
                            <h4><?php echo htmlspecialchars($mensaje['nombre']); ?></h4>
                            <span class="message-date"><?php echo $mensaje['fecha']; ?></span>
                        </div>
                        <div class="message-content">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($mensaje['email']); ?></p>
                            <?php if ($mensaje['telefono']): ?>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($mensaje['telefono']); ?></p>
                            <?php endif; ?>
                            <p><strong>Mensaje:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($mensaje['mensaje'])); ?></p>
                        </div>
                        <div class="message-actions">
                            <button class="btn-reply" onclick="responderMensaje(<?php echo $mensaje['id']; ?>)">📧 Responder</button>
                            <button class="btn-delete" onclick="eliminarMensaje(<?php echo $mensaje['id']; ?>)">🗑️ Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
function showSection(sectionName) {
    // Ocultar todas las secciones
    document.querySelectorAll('.admin-section').forEach(section => {
        section.classList.remove('active');
    });
    
    // Ocultar todos los botones activos
    document.querySelectorAll('.nav-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    
    // Mostrar la sección seleccionada
    document.getElementById(sectionName + '-section').classList.add('active');
    
    // Activar el botón correspondiente
    event.target.classList.add('active');
}

function agregarProducto() {
    alert('Funcionalidad de agregar producto próximamente disponible');
}

function editarProducto(id) {
    alert('Editar producto ID: ' + id + ' - Funcionalidad próximamente disponible');
}

function eliminarProducto(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        alert('Eliminar producto ID: ' + id + ' - Funcionalidad próximamente disponible');
    }
}

function agregarUsuario() {
    alert('Funcionalidad de agregar usuario próximamente disponible');
}

function editarUsuario(id) {
    alert('Editar usuario ID: ' + id + ' - Funcionalidad próximamente disponible');
}

function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        alert('Eliminar usuario ID: ' + id + ' - Funcionalidad próximamente disponible');
    }
}

function verDetalleVenta(id) {
    alert('Ver detalle de venta ID: ' + id + ' - Funcionalidad próximamente disponible');
}

function responderMensaje(id) {
    alert('Responder mensaje ID: ' + id + ' - Funcionalidad próximamente disponible');
}

function eliminarMensaje(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este mensaje?')) {
        alert('Eliminar mensaje ID: ' + id + ' - Funcionalidad próximamente disponible');
    }
}
</script>
