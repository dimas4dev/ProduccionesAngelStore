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
        <div class="filters">
            <div class="search-box">
                <input type="text" id="search-input" placeholder="Buscar productos..." 
                       onkeyup="filtrarProductos()"
                       value="<?php echo isset($termino_busqueda) ? htmlspecialchars($termino_busqueda) : ''; ?>">
                <button onclick="filtrarProductos()">🔍</button>
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
                <div class="product-card" data-category="laptop" data-price="<?php echo $producto->getPrecio(); ?>">
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
                            <button class="btn-view" onclick="verDetalleProducto(<?php echo $producto->getId(); ?>)">
                                👁️ Ver Detalle
                            </button>
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

<script>
// Carrito de compras (simulado en localStorage)
let carrito = JSON.parse(localStorage.getItem('carrito')) || [];

function actualizarContadorCarrito() {
    document.getElementById('cart-count').textContent = carrito.length;
}

function agregarAlCarrito(id, nombre, precio) {
    const productoExistente = carrito.find(item => item.id === id);
    
    if (productoExistente) {
        productoExistente.cantidad += 1;
    } else {
        carrito.push({
            id: id,
            nombre: nombre,
            precio: precio,
            cantidad: 1
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
    
    alert('Funcionalidad de pago próximamente disponible');
}

function filtrarProductos() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const categoryFilter = document.getElementById('category-filter').value;
    const priceFilter = document.getElementById('price-filter').value;
    
    const products = document.querySelectorAll('.product-card');
    
    products.forEach(product => {
        const name = product.querySelector('.product-name').textContent.toLowerCase();
        const price = parseFloat(product.dataset.price);
        const category = product.dataset.category;
        
        let show = true;
        
        if (searchTerm && !name.includes(searchTerm)) {
            show = false;
        }
        
        if (categoryFilter && category !== categoryFilter) {
            show = false;
        }
        
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
        
        product.style.display = show ? 'block' : 'none';
    });
}

function verDetalleProducto(id) {
    alert('Ver detalle del producto ID: ' + id + ' - Funcionalidad próximamente disponible');
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
    const modal = document.getElementById('cart-modal');
    if (event.target === modal) {
        cerrarCarrito();
    }
}

actualizarContadorCarrito();
</script>
