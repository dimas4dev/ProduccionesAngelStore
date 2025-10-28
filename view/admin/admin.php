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
    <!-- Mensajes de éxito/error -->
    <?php if (isset($_SESSION['mensaje'])): ?>
        <div class="alert alert-<?php echo $_SESSION['mensaje_tipo'] ?? 'info'; ?>">
            <?php echo htmlspecialchars($_SESSION['mensaje']); ?>
        </div>
        <?php unset($_SESSION['mensaje'], $_SESSION['mensaje_tipo']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['errores']) && !empty($_SESSION['errores'])): ?>
        <div class="alert alert-error">
            <h3>Errores encontrados:</h3>
            <ul>
                <?php foreach ($_SESSION['errores'] as $error): ?>
                    <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php unset($_SESSION['errores']); ?>
    <?php endif; ?>

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
        <button class="nav-btn" onclick="window.location.href='index.php?controller=admin&action=ventas'">💰 Ventas</button>
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
                        <?php if ($producto->getImagenUrl()): ?>
                            <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
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
                            <h4><?php echo htmlspecialchars($mensaje->getNombre()); ?></h4>
                            <span class="message-date"><?php echo $mensaje->getFechaFormateada(); ?></span>
                        </div>
                        <div class="message-content">
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($mensaje->getEmail()); ?></p>
                            <?php if ($mensaje->getTelefono()): ?>
                                <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($mensaje->getTelefono()); ?></p>
                            <?php endif; ?>
                            <?php if ($mensaje->getDireccion()): ?>
                                <p><strong>Asunto:</strong> <?php echo htmlspecialchars($mensaje->getDireccion()); ?></p>
                            <?php endif; ?>
                            <p><strong>Mensaje:</strong></p>
                            <p><?php echo nl2br(htmlspecialchars($mensaje->getMensaje())); ?></p>
                        </div>
                        <div class="message-actions">
                            <button class="btn-delete" onclick="eliminarMensaje(<?php echo $mensaje->getId(); ?>)">🗑️ Eliminar</button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<div id="modal-agregar-producto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Agregar Nuevo Producto</h2>
            <span class="close" onclick="cerrarModalProducto()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-producto" method="POST" action="index.php?controller=admin&action=gestionarProductos" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="crear">
                
                <div class="form-group">
                    <label for="nombre">📦 Nombre del Producto:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="descripcion">📝 Descripción:</label>
                    <textarea id="descripcion" name="descripcion" required></textarea>
                </div>

                <div class="form-group">
                    <label for="imagen">🖼️ Imagen:</label>
                    <input type="file" id="imagen" name="imagen" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    <small>Formatos permitidos: JPG, PNG, GIF, WEBP (máximo 10MB)</small>
                </div>

                <div class="form-group">
                    <label for="precio">💰 Precio:</label>
                    <input type="number" id="precio" name="precio" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="stock">📊 Stock:</label>
                    <input type="number" id="stock" name="stock" min="0" required>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalProducto()" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-submit">Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar producto -->
<div id="modal-editar-producto" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>✏️ Editar Producto</h2>
            <span class="close" onclick="cerrarModalEditarProducto()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-editar-producto" method="POST" action="index.php?controller=admin&action=gestionarProductos" enctype="multipart/form-data">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" id="editar-id" name="id">
                <input type="hidden" id="editar-imagen-actual" name="imagen_actual">
                
                <div class="form-group">
                    <label for="editar-nombre">📦 Nombre del Producto:</label>
                    <input type="text" id="editar-nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="editar-descripcion">📝 Descripción:</label>
                    <textarea id="editar-descripcion" name="descripcion" required></textarea>
                </div>

                <div class="form-group">
                    <label for="editar-imagen">🖼️ Imagen:</label>
                    <input type="file" id="editar-imagen" name="imagen" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                    <small>Deja vacío para mantener la imagen actual. Formatos permitidos: JPG, PNG, GIF, WEBP (máximo 5MB)</small>
                </div>

                <div class="form-group">
                    <label for="editar-precio">💰 Precio:</label>
                    <input type="number" id="editar-precio" name="precio" step="0.01" min="0" required>
                </div>

                <div class="form-group">
                    <label for="editar-stock">📊 Stock:</label>
                    <input type="number" id="editar-stock" name="stock" min="0" required>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalEditarProducto()" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-submit">Actualizar Producto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para agregar usuario -->
<div id="modal-agregar-usuario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Agregar Nuevo Usuario</h2>
            <span class="close" onclick="cerrarModalUsuario()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-usuario" method="POST" action="index.php?controller=admin&action=gestionarUsuarios">
                <input type="hidden" name="accion" value="crear">
                
                <div class="form-group">
                    <label for="nombre">👤 Nombre Completo:</label>
                    <input type="text" id="nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="email">📧 Email:</label>
                    <input type="email" id="email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="clave">🔒 Contraseña:</label>
                    <input type="password" id="clave" name="clave" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="confirmar_clave">🔒 Confirmar Contraseña:</label>
                    <input type="password" id="confirmar_clave" name="confirmar_clave" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="direccion">📍 Dirección:</label>
                    <input type="text" id="direccion" name="direccion">
                </div>

                <div class="form-group">
                    <label for="telefono">📞 Teléfono:</label>
                    <input type="tel" id="telefono" name="telefono">
                </div>

                <div class="form-group">
                    <label for="role">👥 Rol:</label>
                    <select id="role" name="role" required>
                        <option value="cliente">Cliente</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalUsuario()" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-submit">Guardar Usuario</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal para editar usuario -->
<div id="modal-editar-usuario" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>✏️ Editar Usuario</h2>
            <span class="close" onclick="cerrarModalEditarUsuario()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-editar-usuario" method="POST" action="index.php?controller=admin&action=gestionarUsuarios">
                <input type="hidden" name="accion" value="actualizar">
                <input type="hidden" id="editar-usuario-id" name="id">
                
                <div class="form-group">
                    <label for="editar-usuario-nombre">👤 Nombre Completo:</label>
                    <input type="text" id="editar-usuario-nombre" name="nombre" required>
                </div>

                <div class="form-group">
                    <label for="editar-usuario-email">📧 Email:</label>
                    <input type="email" id="editar-usuario-email" name="email" required>
                </div>

                <div class="form-group">
                    <label for="editar-usuario-clave">🔒 Nueva Contraseña:</label>
                    <input type="password" id="editar-usuario-clave" name="clave" minlength="6">
                    <small>Deja vacío para mantener la contraseña actual</small>
                </div>

                <div class="form-group">
                    <label for="editar-usuario-direccion">📍 Dirección:</label>
                    <input type="text" id="editar-usuario-direccion" name="direccion">
                </div>

                <div class="form-group">
                    <label for="editar-usuario-telefono">📞 Teléfono:</label>
                    <input type="tel" id="editar-usuario-telefono" name="telefono">
                </div>

                <div class="form-group">
                    <label for="editar-usuario-role">👥 Rol:</label>
                    <select id="editar-usuario-role" name="role" required>
                        <option value="cliente">Cliente</option>
                        <option value="administrador">Administrador</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="button" onclick="cerrarModalEditarUsuario()" class="btn-cancel">Cancelar</button>
                    <button type="submit" class="btn-submit">Actualizar Usuario</button>
                </div>
            </form>
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
    document.getElementById('modal-agregar-producto').style.display = 'block';
}

function cerrarModalProducto() {
    document.getElementById('modal-agregar-producto').style.display = 'none';
    document.getElementById('form-producto').reset();
}

function editarProducto(id) {
    // Obtener datos del producto desde el DOM
    const productoCard = document.querySelector(`[onclick*="editarProducto(${id})"]`).closest('.product-admin-card');
    const nombre = productoCard.querySelector('h3').textContent;
    const precio = productoCard.querySelector('.product-price').textContent.replace('$', '').replace(',', '');
    const stock = productoCard.querySelector('.product-stock').textContent.replace('Stock: ', '');
    
    // Obtener imagen actual si existe
    const imagenElement = productoCard.querySelector('img');
    const imagenActual = imagenElement ? imagenElement.src : '';
    
    // Llenar el formulario de edición
    document.getElementById('editar-id').value = id;
    document.getElementById('editar-nombre').value = nombre;
    document.getElementById('editar-precio').value = precio;
    document.getElementById('editar-stock').value = stock;
    document.getElementById('editar-imagen-actual').value = imagenActual;
    
    // Mostrar el modal
    document.getElementById('modal-editar-producto').style.display = 'block';
}

function cerrarModalEditarProducto() {
    document.getElementById('modal-editar-producto').style.display = 'none';
    document.getElementById('form-editar-producto').reset();
}

function eliminarProducto(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        // Crear un formulario temporal para enviar la solicitud de eliminación
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=admin&action=gestionarProductos';
        
        let accion = document.createElement('input');
        accion.type = 'hidden';
        accion.name = 'accion';
        accion.value = 'eliminar';
        
        let idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(accion);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}

function agregarUsuario() {
    document.getElementById('modal-agregar-usuario').style.display = 'block';
}

function cerrarModalUsuario() {
    document.getElementById('modal-agregar-usuario').style.display = 'none';
    document.getElementById('form-usuario').reset();
}

// Cerrar modales al hacer clic fuera
window.onclick = function(event) {
    let modalProducto = document.getElementById('modal-agregar-producto');
    let modalEditarProducto = document.getElementById('modal-editar-producto');
    let modalUsuario = document.getElementById('modal-agregar-usuario');
    let modalEditarUsuario = document.getElementById('modal-editar-usuario');
    
    if (event.target == modalProducto) {
        cerrarModalProducto();
    }
    
    if (event.target == modalEditarProducto) {
        cerrarModalEditarProducto();
    }
    
    if (event.target == modalUsuario) {
        cerrarModalUsuario();
    }
    
    if (event.target == modalEditarUsuario) {
        cerrarModalEditarUsuario();
    }
}

function editarUsuario(id) {
    // Obtener datos del usuario desde la tabla
    const fila = document.querySelector(`[onclick*="editarUsuario(${id})"]`).closest('tr');
    const celdas = fila.querySelectorAll('td');
    
    const nombre = celdas[1].textContent.trim();
    const email = celdas[2].textContent.trim();
    const role = celdas[3].textContent.trim().toLowerCase();
    
    // Llenar el formulario de edición
    document.getElementById('editar-usuario-id').value = id;
    document.getElementById('editar-usuario-nombre').value = nombre;
    document.getElementById('editar-usuario-email').value = email;
    document.getElementById('editar-usuario-role').value = role;
    
    // Mostrar el modal
    document.getElementById('modal-editar-usuario').style.display = 'block';
}

function cerrarModalEditarUsuario() {
    document.getElementById('modal-editar-usuario').style.display = 'none';
    document.getElementById('form-editar-usuario').reset();
}

function eliminarUsuario(id) {
    if (confirm('¿Estás seguro de que quieres eliminar este usuario?')) {
        // Crear un formulario temporal para enviar la solicitud de eliminación
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=admin&action=gestionarUsuarios';
        
        let accion = document.createElement('input');
        accion.type = 'hidden';
        accion.name = 'accion';
        accion.value = 'eliminar';
        
        let idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(accion);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
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
        // Crear un formulario temporal para enviar la solicitud de eliminación
        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'index.php?controller=admin&action=gestionarMensajes';
        
        let accion = document.createElement('input');
        accion.type = 'hidden';
        accion.name = 'accion';
        accion.value = 'eliminar';
        
        let idInput = document.createElement('input');
        idInput.type = 'hidden';
        idInput.name = 'id';
        idInput.value = id;
        
        form.appendChild(accion);
        form.appendChild(idInput);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
