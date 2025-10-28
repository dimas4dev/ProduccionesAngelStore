<?php
/**
 * Vista Cliente - Panel del cliente
 * Variables disponibles: $usuario, $productos, $carrito, $termino_busqueda (opcional)
 */

// Si no hay productos, mostrar array vacío
if (empty($productos)) {
    $productos = [];
}
if (empty($carrito)) {
    $carrito = [];
}
?>

<div class="cliente-dashboard">
    <!-- Header del Cliente -->
    <div class="cliente-header">
        <div class="welcome-section">
            <h1>🛒 Bienvenido, <?php echo htmlspecialchars($usuario['nombre']); ?></h1>
            <p>Explora nuestros productos y realiza tus compras</p>
        </div>
        <div class="cliente-actions">
            <button class="btn-cart" onclick="verCarrito()">
                🛒 Carrito (<span id="cart-count"><?php echo count($carrito); ?></span>)
            </button>
        </div>
    </div>

    <!-- Estadísticas del Cliente -->
    <div class="cliente-stats">
        <div class="stat-card">
            <div class="stat-icon">👤</div>
            <div class="stat-content">
                <h3>Cliente</h3>
                <p>Miembro desde <?php echo date('M Y', strtotime($usuario['login_time'])); ?></p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">🛍️</div>
            <div class="stat-content">
                <h3>0</h3>
                <p>Compras Realizadas</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">⭐</div>
            <div class="stat-content">
                <h3>VIP</h3>
                <p>Estado de Cliente</p>
            </div>
        </div>
    </div>

    <!-- Filtros de Productos -->
    <div class="filters-section">
        <h2>🔍 Buscar Productos</h2>
        <div id="resultados-info" class="resultados-info" style="margin-bottom: 15px; font-size: 0.9em; color: #666;">
            <!-- Se actualiza dinámicamente -->
        </div>
        <div class="filters">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Buscar productos... (Ctrl+K para enfocar)" 
                       onkeyup="filtrarProductos()" oninput="mostrarSugerencias()" onfocus="mostrarSugerencias()" onblur="ocultarSugerencias()"
                       value="<?php echo isset($termino_busqueda) ? htmlspecialchars($termino_busqueda) : ''; ?>">
                <button onclick="filtrarProductos()" title="Buscar">🔍</button>
                <div id="sugerencias" class="sugerencias-container" style="display: none;"></div>
            </div>
            <div class="filter-options">
                <select id="category-filter" onchange="filtrarProductos()">
                    <option value="">Todas las categorías</option>
                    <option value="laptop">Laptops</option>
                    <option value="smartphone">Smartphones</option>
                    <option value="accesorio">Accesorios</option>
                    <option value="gaming">Gaming</option>
                </select>
                <select id="price-filter" onchange="filtrarProductos()">
                    <option value="">Todos los precios</option>
                    <option value="0-100">$0 - $100</option>
                    <option value="100-500">$100 - $500</option>
                    <option value="500-1000">$500 - $1000</option>
                    <option value="1000+">$1000+</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Lista de Productos -->
    <div class="products-section">
        <h2>🛍️ Productos Disponibles</h2>
        <div class="products-grid" id="products-grid">
            <?php foreach ($productos as $producto): ?>
                <div class="product-card" data-id="<?php echo $producto->getId(); ?>" data-category="laptop" data-price="<?php echo $producto->getPrecio(); ?>">
                    <div class="product-image">
                        <?php if ($producto->getImagen() && file_exists($producto->getImagen())): ?>
                            <img src="<?php echo htmlspecialchars($producto->getImagen()); ?>" 
                                 alt="<?php echo htmlspecialchars($producto->getNombre()); ?>"
                                 onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2YjczODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlbjwvdGV4dD48L3N2Zz4='">
                        <?php else: ?>
                            <div class="imagen-placeholder">
                                <span>📦</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <div class="product-info">
                        <h3 class="product-name"><?php echo htmlspecialchars($producto->getNombre()); ?></h3>
                        <p class="product-description"><?php echo htmlspecialchars(substr($producto->getDescripcion(), 0, 80)); ?>...</p>
                        
                        <div class="product-price">
                            <span class="price"><?php echo $producto->getPrecioFormateado(); ?></span>
                        </div>
                        
                        <div class="product-stock">
                            <?php if ($producto->tieneStock()): ?>
                                <span class="stock-available">
                                    ✅ En stock (<?php echo $producto->getStock(); ?>)
                                </span>
                            <?php else: ?>
                                <span class="stock-unavailable">
                                    ❌ Sin stock
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="product-actions">
                            <?php if ($producto->tieneStock()): ?>
                                <button class="btn-add-cart" onclick="agregarAlCarrito(<?php echo $producto->getId(); ?>, '<?php echo htmlspecialchars($producto->getNombre()); ?>', <?php echo $producto->getPrecio(); ?>)">
                                    🛒 Agregar
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Mi Perfil -->
    <div class="profile-section">
        <h2>👤 Mi Perfil</h2>
        <div class="profile-card">
            <div class="profile-info">
                <div class="profile-item">
                    <strong>Nombre:</strong> <?php echo htmlspecialchars($usuario['nombre']); ?>
                </div>
                <div class="profile-item">
                    <strong>Email:</strong> <?php echo htmlspecialchars($usuario['email']); ?>
                </div>
                <div class="profile-item">
                    <strong>Estado:</strong> <span class="status-active">Activo</span>
                </div>
            </div>
            <div class="profile-actions">
                <button class="btn-edit-profile" onclick="editarPerfil()">✏️ Editar Perfil</button>
                <button class="btn-change-password" onclick="cambiarContrasena()">🔒 Cambiar Contraseña</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal del Carrito -->
<div id="cart-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>🛒 Mi Carrito</h2>
            <span class="close" onclick="cerrarCarrito()">&times;</span>
        </div>
        <div class="modal-body">
            <div id="cart-items">
                <!-- Los items del carrito se cargarán aquí dinámicamente -->
            </div>
            <div class="cart-total">
                <strong>Total: $<span id="cart-total">0.00</span></strong>
            </div>
        </div>
        <div class="modal-footer">
            <button class="btn-clear-cart" onclick="limpiarCarrito()">🗑️ Limpiar Carrito</button>
            <button class="btn-checkout" onclick="procederPago()">💳 Proceder al Pago</button>
        </div>
    </div>
</div>

<!-- Modal Checkout -->
<div id="modal-checkout" class="modal">
    <div class="modal-content checkout-modal">
        <div class="modal-header">
            <h2>💳 Proceder al Pago</h2>
            <span class="close" onclick="cerrarModalCheckout()">&times;</span>
        </div>
        <div class="modal-body">
            <div class="checkout-container">
                <!-- Resumen del pedido -->
                <div class="order-summary">
                    <h3>📋 Resumen del Pedido</h3>
                    <div id="checkout-items">
                        <!-- Items se cargan dinámicamente -->
                    </div>
                    <div class="order-total">
                        <strong>Total: $<span id="checkout-total">0.00</span></strong>
                    </div>
                </div>
                
                <!-- Información del cliente -->
                <div class="customer-info">
                    <h3>👤 Información de Contacto</h3>
                    <form id="checkout-form">
                        <div class="form-group">
                            <label for="customer-name">Nombre completo:</label>
                            <input type="text" id="customer-name" name="customer_name" required>
                        </div>
                        <div class="form-group">
                            <label for="customer-email">Email:</label>
                            <input type="email" id="customer-email" name="customer_email" required>
                        </div>
                        <div class="form-group">
                            <label for="customer-phone">Teléfono:</label>
                            <input type="tel" id="customer-phone" name="customer_phone" required>
                        </div>
                        <div class="form-group">
                            <label for="delivery-address">Dirección de entrega:</label>
                            <textarea id="delivery-address" name="delivery_address" rows="3" required></textarea>
                        </div>
                        <div class="form-group">
                            <label for="payment-method">Método de pago:</label>
                            <select id="payment-method" name="payment_method" required>
                                <option value="">Seleccionar método de pago</option>
                                <option value="efectivo">💵 Efectivo contra entrega</option>
                                <option value="transferencia">🏦 Transferencia bancaria</option>
                                <option value="tarjeta">💳 Tarjeta de crédito/débito</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="notes">Notas adicionales (opcional):</label>
                            <textarea id="notes" name="notes" rows="2" placeholder="Instrucciones especiales para la entrega..."></textarea>
                        </div>
                    </form>
                </div>
            </div>
        </div>
               <div class="modal-footer">
                   <button class="btn-cancel" onclick="cerrarModalCheckout()">❌ Cancelar</button>
                   <button class="btn-confirm-order" onclick="confirmarPedido()">✅ Confirmar Pedido</button>
               </div>
    </div>
</div>

<style>
/* Estilos para el modal de checkout */
.modal {
    display: none;
    position: fixed;
    z-index: 1000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
}

.modal-content {
    background-color: #fefefe;
    margin: 2% auto;
    padding: 0;
    border: none;
    border-radius: 10px;
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 4px 20px rgba(0,0,0,0.3);
}

.modal-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    border-radius: 10px 10px 0 0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.5em;
}

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    transition: opacity 0.3s;
}

.close:hover {
    opacity: 0.7;
}

.modal-body {
    padding: 20px;
}

.modal-footer {
    padding: 20px;
    border-top: 1px solid #e9ecef;
    display: flex;
    justify-content: space-between;
    gap: 10px;
}

/* Estilos específicos para checkout */
.checkout-container {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 30px;
}

.order-summary, .customer-info {
    background: #f8f9fa;
    padding: 20px;
    border-radius: 10px;
    border: 1px solid #e9ecef;
}

.order-summary h3, .customer-info h3 {
    margin: 0 0 20px 0;
    color: #333;
    font-size: 1.3em;
}

.checkout-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 10px 0;
    border-bottom: 1px solid #e9ecef;
}

.checkout-item:last-child {
    border-bottom: none;
}

.item-info {
    display: flex;
    flex-direction: column;
}

.item-info strong {
    color: #333;
    margin-bottom: 5px;
}

.item-info span {
    color: #666;
    font-size: 0.9em;
}

.item-total {
    font-weight: bold;
    color: #28a745;
}

.order-total {
    margin-top: 20px;
    padding-top: 20px;
    border-top: 2px solid #28a745;
    text-align: right;
    font-size: 1.2em;
}

/* Formulario */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
    color: #333;
}

.form-group input,
.form-group select,
.form-group textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1em;
    transition: border-color 0.3s;
}

.form-group input:focus,
.form-group select:focus,
.form-group textarea:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 2px rgba(102, 126, 234, 0.2);
}

/* Botones */
.btn-cancel {
    background: #6c757d;
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    transition: background-color 0.3s;
}

.btn-cancel:hover {
    background: #5a6268;
}

.btn-confirm-order {
    background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: 5px;
    cursor: pointer;
    font-size: 1em;
    font-weight: bold;
    transition: transform 0.2s, box-shadow 0.2s;
}

.btn-confirm-order:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(40, 167, 69, 0.3);
}

.btn-confirm-order:disabled {
    background: #6c757d;
    cursor: not-allowed;
    transform: none;
    box-shadow: none;
}

/* Responsive */
@media (max-width: 768px) {
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .checkout-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .modal-footer {
        flex-direction: column;
    }
    
    .btn-cancel,
    .btn-confirm-order {
        width: 100%;
    }
}

/* Estilos para sugerencias de búsqueda */
.sugerencias-container {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: white;
    border: 1px solid #ddd;
    border-top: none;
    border-radius: 0 0 8px 8px;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
}

.sugerencia-item {
    padding: 12px 15px;
    cursor: pointer;
    border-bottom: 1px solid #f0f0f0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    transition: background-color 0.2s;
}

.sugerencia-item:hover {
    background-color: #f8f9fa;
}

.sugerencia-item:last-child {
    border-bottom: none;
}

.sugerencia-nombre {
    font-weight: 500;
    color: #333;
    flex: 1;
}

.sugerencia-precio {
    color: #28a745;
    font-weight: bold;
    font-size: 0.9em;
}

.search-box {
    position: relative;
}
</style>

<script>
// Carrito de compras (simulado en localStorage)
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

function actualizarContadorCarrito() {
    document.getElementById('cart-count').textContent = carrito.length;
}

function agregarAlCarrito(id, nombre, precio, imagen = null) {
    const productoExistente = carrito.find(item => item.id === id);
    
    if (productoExistente) {
        productoExistente.cantidad += 1;
    } else {
        // Obtener la imagen del producto si no se proporcionó
        if (!imagen) {
            const productoCard = document.querySelector(`.product-card[data-id="${id}"] img`);
            imagen = productoCard ? productoCard.src : 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik0yMCAyMEg0MFY0MEgyMFYyMFoiIGZpbGw9IiNjY2MiLz4KPC9zdmc+';
        }
        
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1,
            imagen: imagen
        });
    }
    
    localStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarContadorCarrito();
    
    mostrarNotificacion(`${nombre} agregado al carrito`);
}

function verCarrito() {
    const modal = document.getElementById('cart-modal');
    const cartItems = document.getElementById('cart-items');
    const cartTotal = document.getElementById('cart-total');
    
    if (carrito.length === 0) {
        cartItems.innerHTML = '<p style="text-align: center; color: #6c757d;">Tu carrito está vacío</p>';
        cartTotal.textContent = '0.00';
    } else {
        cartItems.innerHTML = '';
        let total = 0;
        
        carrito.forEach(item => {
            const itemTotal = item.precio * item.cantidad;
            total += itemTotal;
            
            const itemElement = document.createElement('div');
            itemElement.className = 'cart-item';
            itemElement.style.cssText = `
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px;
                border-bottom: 1px solid #e9ecef;
            `;
            
            itemElement.innerHTML = `
                <div>
                    <strong>${item.nombre}</strong><br>
                    <small>$${item.precio} x ${item.cantidad}</small>
                </div>
                <div>
                    <span style="font-weight: bold; color: #28a745;">$${itemTotal.toFixed(2)}</span>
                    <button onclick="eliminarDelCarrito(${item.id})" style="margin-left: 10px; background: #dc3545; color: white; border: none; border-radius: 4px; padding: 4px 8px; cursor: pointer;">🗑️</button>
                </div>
            `;
            
            cartItems.appendChild(itemElement);
        });
        
        cartTotal.textContent = total.toFixed(2);
    }
    
    modal.style.display = 'block';
}

function cerrarCarrito() {
    document.getElementById('cart-modal').style.display = 'none';
}

function eliminarDelCarrito(id) {
    carrito = carrito.filter(item => item.id !== id);
    localStorage.setItem('carrito', JSON.stringify(carrito));
    actualizarContadorCarrito();
    verCarrito();
}

function limpiarCarrito() {
    if (confirm('¿Estás seguro de que quieres limpiar el carrito?')) {
        carrito = [];
        localStorage.setItem('carrito', JSON.stringify(carrito));
        actualizarContadorCarrito();
        cerrarCarrito();
        mostrarNotificacion('Carrito limpiado');
    }
}

function procederPago() {
    if (carrito.length === 0) {
        alert('Tu carrito está vacío');
        return;
    }
    
    // Cerrar el modal del carrito
    cerrarCarrito();
    
    // Mostrar el modal de checkout
    mostrarModalCheckout();
}

function mostrarModalCheckout() {
    const modal = document.getElementById('modal-checkout');
    const checkoutItems = document.getElementById('checkout-items');
    const checkoutTotal = document.getElementById('checkout-total');
    
    // Limpiar items anteriores
    checkoutItems.innerHTML = '';
    
    let total = 0;
    
    // Cargar items del carrito
    carrito.forEach(item => {
        const itemTotal = item.precio * item.cantidad;
        total += itemTotal;
        
        const itemElement = document.createElement('div');
        itemElement.className = 'checkout-item';
        itemElement.innerHTML = `
            <div class="item-info">
                <strong>${item.nombre}</strong>
                <span>$${item.precio} x ${item.cantidad}</span>
            </div>
            <div class="item-total">
                $${itemTotal.toFixed(2)}
            </div>
        `;
        
        checkoutItems.appendChild(itemElement);
    });
    
    checkoutTotal.textContent = total.toFixed(2);
    
    // Pre-llenar información del usuario si está disponible
    const usuario = <?php echo json_encode($usuario); ?>;
    if (usuario) {
        document.getElementById('customer-name').value = usuario.nombre || '';
        document.getElementById('customer-email').value = usuario.email || '';
    }
    
    modal.style.display = 'block';
}

function cerrarModalCheckout() {
    document.getElementById('modal-checkout').style.display = 'none';
}


function confirmarPedido() {
    const form = document.getElementById('checkout-form');
    
    // Validar formulario
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    // Recopilar datos del pedido
    const formData = new FormData(form);
    const pedidoData = {
        items: carrito,
        total: parseFloat(document.getElementById('checkout-total').textContent),
        customer_name: formData.get('customer_name'),
        customer_email: formData.get('customer_email'),
        customer_phone: formData.get('customer_phone'),
        delivery_address: formData.get('delivery_address'),
        payment_method: formData.get('payment_method'),
        notes: formData.get('notes'),
        fecha: new Date().toISOString(),
        estado: 'pendiente'
    };
    
    // Mostrar notificación de procesamiento
    mostrarNotificacion('Procesando pedido...');
    
    // Deshabilitar botón para evitar doble envío
    const confirmButton = document.querySelector('.btn-confirm-order');
    confirmButton.disabled = true;
    confirmButton.textContent = 'Procesando...';
    
    // Enviar datos al backend
    const formDataToSend = new FormData();
    formDataToSend.append('pedido_data', JSON.stringify(pedidoData));
    
    fetch('index.php?controller=admin&action=procesarPedidoCheckout', {
        method: 'POST',
        body: formDataToSend
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Guardar pedido en localStorage para referencia
            const pedidos = JSON.parse(localStorage.getItem('pedidos')) || [];
            pedidoData.id = Date.now(); // ID único
            pedidos.push(pedidoData);
            localStorage.setItem('pedidos', JSON.stringify(pedidos));
            
            // Limpiar carrito
            carrito = [];
            localStorage.setItem('carrito', JSON.stringify(carrito));
            actualizarContadorCarrito();
            
            // Cerrar modal
            cerrarModalCheckout();
            
            // Mostrar PDF si está disponible
            if (data.pdf_html) {
                mostrarPDFPedido(data.pdf_html, data.pdf_filename);
            }
            
            // Mostrar confirmación exitosa
            mostrarConfirmacionPedido(pedidoData, data);
            
        } else {
            // Mostrar errores detallados
            let mensajeError = 'Error al procesar el pedido:\n';
            if (data.error) {
                mensajeError += 'Error: ' + data.error + '\n';
            }
            if (data.errores && data.errores.length > 0) {
                mensajeError += 'Errores específicos:\n' + data.errores.join('\n');
            }
            if (data.debug_info) {
                mensajeError += '\n\nInformación de debug:\n';
                mensajeError += 'Usuario ID: ' + data.debug_info.usuario_id + '\n';
                mensajeError += 'Datos POST: ' + data.debug_info.post_data;
            }
            
            console.error('Error del servidor:', data);
            alert(mensajeError);
            
            // Rehabilitar botón
            confirmButton.disabled = false;
            confirmButton.textContent = '✅ Confirmar Pedido';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        
        // Mostrar información detallada del error
        let mensajeError = 'Error de conexión:\n';
        mensajeError += 'Tipo: ' + error.name + '\n';
        mensajeError += 'Mensaje: ' + error.message + '\n';
        mensajeError += 'Intenta nuevamente o verifica tu conexión.';
        
        alert(mensajeError);
        
        // Rehabilitar botón
        confirmButton.disabled = false;
        confirmButton.textContent = '✅ Confirmar Pedido';
    });
}

function mostrarConfirmacionPedido(pedido, backendData = null) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'block';
    
    let ventasInfo = '';
    if (backendData && backendData.ventas_creadas) {
        ventasInfo = `
            <p><strong>Ventas creadas:</strong> ${backendData.total_procesado}</p>
            <p><strong>Productos procesados:</strong></p>
            <ul style="text-align: left; margin: 10px 0;">
                ${backendData.ventas_creadas.map(producto => `<li>${producto}</li>`).join('')}
            </ul>
        `;
    }
    
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 500px;">
            <div class="modal-header" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%);">
                <h2>✅ Pedido Confirmado</h2>
                <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
            </div>
            <div class="modal-body">
                <div style="text-align: center; padding: 20px;">
                    <div style="font-size: 4em; margin-bottom: 20px;">🎉</div>
                    <h3>¡Gracias por tu compra!</h3>
                    <p><strong>Número de pedido:</strong> #${pedido.id}</p>
                    <p><strong>Total:</strong> $${pedido.total.toFixed(2)}</p>
                    <p><strong>Método de pago:</strong> ${getPaymentMethodText(pedido.payment_method)}</p>
                    ${ventasInfo}
                    <hr style="margin: 20px 0;">
                    <p>Te contactaremos pronto para coordinar la entrega.</p>
                    <p>Recibirás un email de confirmación en: <strong>${pedido.customer_email}</strong></p>
                </div>
            </div>
            <div class="modal-footer" style="text-align: center;">
                <button class="btn-primary" onclick="this.closest('.modal').remove()" style="background: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer;">
                    Continuar Comprando
                </button>
            </div>
        </div>
    `;
    
    document.body.appendChild(modal);
}

function getPaymentMethodText(method) {
    const methods = {
        'efectivo': '💵 Efectivo contra entrega',
        'transferencia': '🏦 Transferencia bancaria',
        'tarjeta': '💳 Tarjeta de crédito/débito'
    };
    return methods[method] || method;
}

function mostrarPDFPedido(htmlFactura, nombreArchivo) {
    // Crear una nueva ventana para mostrar el PDF
    const ventanaPDF = window.open('', '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
    
    if (ventanaPDF) {
        ventanaPDF.document.write(htmlFactura);
        ventanaPDF.document.close();
        
        // Agregar botón de descarga
        ventanaPDF.document.addEventListener('DOMContentLoaded', function() {
            const botonDescarga = ventanaPDF.document.createElement('div');
            botonDescarga.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                z-index: 1000;
                background: #007bff;
                color: white;
                padding: 10px 20px;
                border-radius: 5px;
                cursor: pointer;
                font-weight: bold;
                box-shadow: 0 2px 10px rgba(0,0,0,0.3);
            `;
            botonDescarga.innerHTML = '📄 Descargar PDF';
            botonDescarga.onclick = function() {
                ventanaPDF.print();
            };
            ventanaPDF.document.body.appendChild(botonDescarga);
        });
    } else {
        // Si no se puede abrir ventana nueva, mostrar en modal
        mostrarPDFEnModal(htmlFactura);
    }
}

function mostrarPDFEnModal(htmlFactura) {
    const modal = document.createElement('div');
    modal.className = 'modal';
    modal.style.display = 'block';
    modal.innerHTML = `
        <div class="modal-content" style="max-width: 90%; max-height: 90%; overflow-y: auto;">
            <div class="modal-header" style="background: #007bff; color: white;">
                <h2>📄 Factura del Pedido</h2>
                <span class="close" onclick="this.closest('.modal').remove()">&times;</span>
            </div>
            <div class="modal-body" style="padding: 0;">
                <div style="padding: 20px;">
                    ${htmlFactura}
                </div>
            </div>
            <div class="modal-footer" style="text-align: center; padding: 20px;">
                <button onclick="window.print(); this.closest('.modal').remove();" 
                        style="background: #28a745; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer; margin-right: 10px;">
                    🖨️ Imprimir PDF
                </button>
                <button onclick="this.closest('.modal').remove()" 
                        style="background: #6c757d; color: white; border: none; padding: 12px 24px; border-radius: 5px; cursor: pointer;">
                    ❌ Cerrar
                </button>
            </div>
        </div>
    `;
    document.body.appendChild(modal);
}

function filtrarProductos() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
    const categoryFilter = document.getElementById('category-filter').value;
    const priceFilter = document.getElementById('price-filter').value;
    
    const products = document.querySelectorAll('.product-card');
    let productosVisibles = 0;
    
    products.forEach(product => {
        const name = product.querySelector('.product-name').textContent.toLowerCase();
        const description = product.querySelector('.product-description') ? 
                           product.querySelector('.product-description').textContent.toLowerCase() : '';
        const price = parseFloat(product.dataset.price);
        const category = product.dataset.category;
        
        let show = true;
        
        // Filtrar por término de búsqueda (nombre y descripción)
        if (searchTerm) {
            const searchInName = name.includes(searchTerm);
            const searchInDescription = description.includes(searchTerm);
            
            if (!searchInName && !searchInDescription) {
            show = false;
            }
        }
        
        // Filtrar por categoría
        if (categoryFilter && category !== categoryFilter) {
            show = false;
        }
        
        // Filtrar por precio
        if (priceFilter) {
            switch (priceFilter) {
                case '0-100':
                    if (price >= 100) show = false;
                    break;
                case '100-500':
                    if (price < 100 || price >= 500) show = false;
                    break;
                case '500-1000':
                    if (price < 500 || price >= 1000) show = false;
                    break;
                case '1000+':
                    if (price < 1000) show = false;
                    break;
            }
        }
        
        // Mostrar/ocultar producto con animación
        if (show) {
            product.style.display = 'block';
            product.style.opacity = '0';
            product.style.transform = 'translateY(20px)';
            
            setTimeout(() => {
                product.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                product.style.opacity = '1';
                product.style.transform = 'translateY(0)';
            }, productosVisibles * 50); // Animación escalonada
            
            productosVisibles++;
        } else {
            product.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
            product.style.opacity = '0';
            product.style.transform = 'translateY(-20px)';
            
            setTimeout(() => {
                product.style.display = 'none';
            }, 300);
        }
    });
    
    // Mostrar mensaje si no hay resultados
    mostrarMensajeResultados(productosVisibles, searchTerm);
    
    // Actualizar contador de resultados
    actualizarContadorResultados(productosVisibles, searchTerm);
    
    // Destacar términos de búsqueda en los resultados
    if (searchTerm) {
        destacarTerminos(searchTerm);
    }
}

function mostrarMensajeResultados(cantidad, terminoBusqueda) {
    // Remover mensaje anterior si existe
    const mensajeAnterior = document.getElementById('mensaje-resultados');
    if (mensajeAnterior) {
        mensajeAnterior.remove();
    }
    
    if (cantidad === 0) {
        const productosContainer = document.querySelector('.products-grid');
        const mensaje = document.createElement('div');
        mensaje.id = 'mensaje-resultados';
        mensaje.style.cssText = `
            grid-column: 1 / -1;
            text-align: center;
            padding: 40px 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 2px dashed #dee2e6;
            margin: 20px 0;
        `;
        
        if (terminoBusqueda) {
            mensaje.innerHTML = `
                <div style="font-size: 3em; margin-bottom: 20px;">🔍</div>
                <h3>No se encontraron productos</h3>
                <p>No hay productos que coincidan con "<strong>${terminoBusqueda}</strong>"</p>
                <p style="color: #6c757d; margin-top: 10px;">Intenta con otros términos de búsqueda o ajusta los filtros</p>
                <button onclick="limpiarFiltros()" style="margin-top: 15px; background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    Limpiar Filtros
                </button>
            `;
        } else {
            mensaje.innerHTML = `
                <div style="font-size: 3em; margin-bottom: 20px;">📦</div>
                <h3>No hay productos disponibles</h3>
                <p>No hay productos que coincidan con los filtros seleccionados</p>
                <button onclick="limpiarFiltros()" style="margin-top: 15px; background: #007bff; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer;">
                    Limpiar Filtros
                </button>
            `;
        }
        
        productosContainer.appendChild(mensaje);
    }
}

function limpiarFiltros() {
    document.getElementById('search-input').value = '';
    document.getElementById('category-filter').value = '';
    document.getElementById('price-filter').value = '';
    filtrarProductos();
}

function actualizarContadorResultados(cantidad, terminoBusqueda) {
    const contador = document.getElementById('resultados-info');
    const totalProductos = document.querySelectorAll('.product-card').length;
    
    if (terminoBusqueda) {
        contador.innerHTML = `
            <span style="color: #007bff;">🔍</span> 
            Mostrando <strong>${cantidad}</strong> de <strong>${totalProductos}</strong> productos 
            para "<strong>${terminoBusqueda}</strong>"
        `;
    } else {
        contador.innerHTML = `
            <span style="color: #28a745;">📦</span> 
            Mostrando <strong>${cantidad}</strong> de <strong>${totalProductos}</strong> productos
        `;
    }
}

function mostrarSugerencias() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase().trim();
    const sugerenciasContainer = document.getElementById('sugerencias');
    
    if (searchTerm.length < 2) {
        sugerenciasContainer.style.display = 'none';
        return;
    }
    
    // Obtener todos los productos
    const productos = <?php echo json_encode($productos); ?>;
    const sugerencias = [];
    
    // Buscar coincidencias en nombres y descripciones
    productos.forEach(producto => {
        const nombre = producto.nombre.toLowerCase();
        const descripcion = producto.descripcion ? producto.descripcion.toLowerCase() : '';
        
        if (nombre.includes(searchTerm) || descripcion.includes(searchTerm)) {
            sugerencias.push({
                nombre: producto.nombre,
                descripcion: producto.descripcion,
                precio: producto.precio
            });
        }
    });
    
    // Mostrar máximo 5 sugerencias
    const sugerenciasLimitadas = sugerencias.slice(0, 5);
    
    if (sugerenciasLimitadas.length > 0) {
        let html = '';
        sugerenciasLimitadas.forEach(sugerencia => {
            html += `
                <div class="sugerencia-item" onclick="seleccionarSugerencia('${sugerencia.nombre}')">
                    <div class="sugerencia-nombre">${sugerencia.nombre}</div>
                    <div class="sugerencia-precio">$${parseFloat(sugerencia.precio).toFixed(2)}</div>
                </div>
            `;
        });
        
        sugerenciasContainer.innerHTML = html;
        sugerenciasContainer.style.display = 'block';
    } else {
        sugerenciasContainer.style.display = 'none';
    }
}

function ocultarSugerencias() {
    // Delay para permitir clicks en sugerencias
    setTimeout(() => {
        document.getElementById('sugerencias').style.display = 'none';
    }, 200);
}

function seleccionarSugerencia(nombre) {
    document.getElementById('search-input').value = nombre;
    document.getElementById('sugerencias').style.display = 'none';
    filtrarProductos();
}

// Funcionalidad de atajos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + K para enfocar la búsqueda
    if (e.ctrlKey && e.key === 'k') {
        e.preventDefault();
        document.getElementById('search-input').focus();
    }
    
    // Escape para limpiar búsqueda
    if (e.key === 'Escape') {
        if (document.getElementById('search-input') === document.activeElement) {
            limpiarFiltros();
        }
    }
});

// Búsqueda avanzada con múltiples términos
function buscarAvanzado(termino) {
    const terminos = termino.toLowerCase().split(' ').filter(t => t.length > 0);
    const productos = document.querySelectorAll('.product-card');
    
    productos.forEach(producto => {
        const name = producto.querySelector('.product-name').textContent.toLowerCase();
        const description = producto.querySelector('.product-description') ? 
                           producto.querySelector('.product-description').textContent.toLowerCase() : '';
        const textoCompleto = name + ' ' + description;
        
        // Verificar si todos los términos están presentes
        const todosLosTerminosPresentes = terminos.every(termino => 
            textoCompleto.includes(termino)
        );
        
        if (todosLosTerminosPresentes) {
            producto.style.display = 'block';
            producto.style.opacity = '1';
        } else {
            producto.style.display = 'none';
        }
    });
}

// Función para destacar términos de búsqueda
function destacarTerminos(termino) {
    const productos = document.querySelectorAll('.product-card');
    const terminos = termino.toLowerCase().split(' ').filter(t => t.length > 0);
    
    productos.forEach(producto => {
        const nombre = producto.querySelector('.product-name');
        const descripcion = producto.querySelector('.product-description');
        
        if (nombre && termino) {
            let textoNombre = nombre.textContent;
            terminos.forEach(termino => {
                const regex = new RegExp(`(${termino})`, 'gi');
                textoNombre = textoNombre.replace(regex, '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>');
            });
            nombre.innerHTML = textoNombre;
        }
        
        if (descripcion && termino) {
            let textoDescripcion = descripcion.textContent;
            terminos.forEach(termino => {
                const regex = new RegExp(`(${termino})`, 'gi');
                textoDescripcion = textoDescripcion.replace(regex, '<mark style="background: #ffeb3b; padding: 2px 4px; border-radius: 3px;">$1</mark>');
            });
            descripcion.innerHTML = textoDescripcion;
        }
    });
}

function editarPerfil() {
    alert('Editar perfil - Funcionalidad próximamente disponible');
}

function cambiarContrasena() {
    alert('Cambiar contraseña - Funcionalidad próximamente disponible');
}

function mostrarNotificacion(mensaje) {
    const notification = document.createElement('div');
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        background: #28a745;
        color: white;
        padding: 15px 20px;
        border-radius: 8px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        z-index: 1001;
        animation: slideInRight 0.3s ease-out;
    `;
    notification.textContent = mensaje;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease-out';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

window.onclick = function(event) {
    const modalCarrito = document.getElementById('cart-modal');
    const modalCheckout = document.getElementById('modal-checkout');
    
    if (event.target === modalCarrito) {
        cerrarCarrito();
    }
    
    if (event.target === modalCheckout) {
        cerrarModalCheckout();
    }
}

    // Limpiar carrito antiguo sin imágenes y forzar regeneración
    if (carrito.length > 0 && !carrito[0].imagen) {
        carrito = [];
        localStorage.setItem('carrito', JSON.stringify(carrito));
}

actualizarContadorCarrito();

// Inicializar contador de resultados
const totalProductos = document.querySelectorAll('.product-card').length;
document.getElementById('resultados-info').innerHTML = `
    <span style="color: #28a745;">📦</span> 
    Mostrando <strong>${totalProductos}</strong> productos disponibles
`;
</script>
