<?php
/**
 * Vista Home - Muestra los productos en formato de tarjetas
 * Variables disponibles: $productos, $termino_busqueda (opcional)
 */

// Si no hay productos, mostrar mensaje
if (empty($productos)) {
    echo "<div class='no-productos'>";
    echo "<h2>No se encontraron productos</h2>";
    if (isset($termino_busqueda) && !empty($termino_busqueda)) {
        echo "<p>No hay productos que coincidan con tu búsqueda: <strong>" . htmlspecialchars($termino_busqueda) . "</strong></p>";
    } else {
        echo "<p>No hay productos disponibles en este momento.</p>";
    }
    echo "</div>";
    return;
}
?>

<div class="productos-container">
    <div class="productos-grid">
        <?php foreach ($productos as $producto): ?>
            <div class="producto-card">
                <div class="producto-imagen">
                    <?php if ($producto->getImagenUrl()): ?>
                        <img src="<?php echo htmlspecialchars($producto->getImagenUrl()); ?>" 
                             alt="<?php echo htmlspecialchars($producto->getNombre()); ?>"
                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj48cmVjdCB3aWR0aD0iMjAwIiBoZWlnaHQ9IjIwMCIgZmlsbD0iI2Y4ZjlmYSIvPjx0ZXh0IHg9IjEwMCIgeT0iMTAwIiBmb250LWZhbWlseT0iQXJpYWwiIGZvbnQtc2l6ZT0iMTQiIGZpbGw9IiM2YjczODAiIHRleHQtYW5jaG9yPSJtaWRkbGUiPk5vIEltYWdlbjwvdGV4dD48L3N2Zz4='">
                    <?php else: ?>
                        <div class="imagen-placeholder">
                            <span>Sin imagen</span>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="producto-info">
                    <h3 class="producto-nombre"><?php echo htmlspecialchars($producto->getNombre()); ?></h3>
                    <p class="producto-descripcion"><?php echo htmlspecialchars(substr($producto->getDescripcion(), 0, 100)); ?>
                        <?php if (strlen($producto->getDescripcion()) > 100): ?>...<?php endif; ?>
                    </p>
                    
                    <div class="producto-precio">
                        <span class="precio"><?php echo $producto->getPrecioFormateado(); ?></span>
                    </div>
                    
                    <div class="producto-stock">
                        <?php if ($producto->tieneStock()): ?>
                            <span class="stock-disponible">
                                <i class="stock-icon">✓</i>
                                En stock (<?php echo $producto->getStock(); ?> unidades)
                            </span>
                        <?php else: ?>
                            <span class="stock-agotado">
                                <i class="stock-icon">✗</i>
                                Sin stock
                            </span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="producto-acciones">
                        <button class="btn-ver-detalle" onclick="verDetalle(<?php echo $producto->getId(); ?>)">
                            Ver detalles
                        </button>
                        <?php if ($producto->tieneStock()): ?>
                            <button class="btn-agregar-carrito" onclick="agregarAlCarrito(<?php echo $producto->getId(); ?>)">
                                Agregar al carrito
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<script>
function verDetalle(id) {
    alert('Ver detalles del producto ID: ' + id);
}

function agregarAlCarrito(id) {
    alert('Producto ID ' + id + ' agregado al carrito');
}
</script>
