<?php
/**
 * Vista de Ventas - Panel de administración
 * Variables disponibles: $ventas, $estadisticas, $fecha_inicio, $fecha_fin
 */

require_once __DIR__ . '/../../model/Usuario.php';
require_once __DIR__ . '/../../model/Producto.php';
?>

<div class="admin-container">
    <div class="admin-header">
        <h1>📊 Gestión de Ventas</h1>
        <div class="admin-actions">
            <button class="btn-primary" onclick="abrirModalVenta()">➕ Nueva Venta</button>
            <button class="btn-secondary" onclick="filtrarPorFechas()">📅 Filtrar por Fechas</button>
        </div>
    </div>

    <!-- Estadísticas -->
    <!-- Navegación del Panel -->
    <div class="admin-nav">
        <button class="nav-btn" onclick="window.location.href='index.php?controller=admin'">📦 Productos</button>
        <button class="nav-btn active" onclick="window.location.href='index.php?controller=admin&action=ventas'">💰 Ventas</button>
        <button class="nav-btn" onclick="window.location.href='index.php?controller=admin&action=gestionarUsuarios'">👥 Usuarios</button>
        <button class="nav-btn" onclick="window.location.href='index.php?controller=admin&action=gestionarMensajes'">📧 Mensajes</button>
    </div>

    <!-- Filtro de fechas -->
    <div id="filtro-fechas" class="filtro-container" style="display: none;">
        <form method="GET" action="index.php">
            <input type="hidden" name="controller" value="ventas">
            <input type="hidden" name="action" value="obtenerPorFechas">
            
            <div class="form-row">
                <div class="form-group">
                    <label for="fecha_inicio">Fecha Inicio:</label>
                    <input type="date" id="fecha_inicio" name="fecha_inicio" value="<?php echo $fecha_inicio ?? date('Y-m-01'); ?>">
                </div>
                <div class="form-group">
                    <label for="fecha_fin">Fecha Fin:</label>
                    <input type="date" id="fecha_fin" name="fecha_fin" value="<?php echo $fecha_fin ?? date('Y-m-t'); ?>">
                </div>
                <div class="form-group">
                    <button type="submit" class="btn-primary">🔍 Filtrar</button>
                    <button type="button" class="btn-secondary" onclick="cerrarFiltro()">❌ Cancelar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Ventas -->
    <div class="ventas-section">
        <div class="section-header">
            <h2>📋 Lista de Ventas</h2>
        </div>
        
        <div class="ventas-list">
            <?php if (empty($ventas)): ?>
                <div class="no-data">No hay ventas registradas</div>
            <?php else: ?>
                <div class="table-container">
                    <table class="ventas-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Producto</th>
                                <th>Cliente</th>
                                <th>Cantidad</th>
                                <th>Precio Unit.</th>
                                <th>Total</th>
                                <th>Fecha</th>
                                <th>Acciones</th>
                        </thead>
                        <tbody>
                            <?php foreach ($ventas as $venta): ?>
                                <tr>
                                    <td><?php echo $venta->getId(); ?></td>
                                    <td><?php echo htmlspecialchars($venta->producto_nombre ?? 'N/A'); ?></td>
                                    <td>
                                        <div class="cliente-info">
                                            <strong><?php echo htmlspecialchars($venta->cliente_nombre ?? 'N/A'); ?></strong>
                                            <small><?php echo htmlspecialchars($venta->cliente_email ?? ''); ?></small>
                                        </div>
                                    </td>
                                    <td><?php echo $venta->getCantidad(); ?></td>
                                    <td><?php echo '$' . number_format($venta->getPrecioUnitario(), 2); ?></td>
                                    <td><strong><?php echo $venta->getTotalFormateado(); ?></strong></td>
                                    <td><?php echo $venta->getFechaFormateada(); ?></td>
                                    <td>
                                        <span class="estado-badge estado-<?php echo $venta->getEstado(); ?>">
                                            <?php echo $venta->getEstadoConEmoji(); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-pdf" onclick="verPDFVenta(<?php echo $venta->getId(); ?>)" 
                                                    title="Ver PDF de la venta" style="background: #17a2b8; color: white; border: none; padding: 8px 12px; border-radius: 4px; cursor: pointer; margin-right: 5px;">
                                                📄 PDF
                                            </button>
                                            <button class="btn-delete" onclick="eliminarVenta(<?php echo $venta->getId(); ?>)">🗑️ Eliminar</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

</div>

<!-- Formulario oculto para eliminar -->
<form id="form-eliminar" method="POST" action="index.php" style="display: none;">
    <input type="hidden" name="controller" value="admin">
    <input type="hidden" name="action" value="eliminarVenta">
    <input type="hidden" name="id" id="eliminar-id">
</form>

<!-- Modal Nueva Venta -->
<div id="modal-venta" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>➕ Nueva Venta</h2>
            <span class="close" onclick="cerrarModalVenta()">&times;</span>
        </div>
        <div class="modal-body">
            <form id="form-venta" method="POST" action="index.php">
                <input type="hidden" name="controller" value="admin">
                <input type="hidden" name="action" value="crearVenta">
                
                <div class="form-group">
                    <label for="producto_id">🛍️ Producto:</label>
                    <select id="producto_id" name="producto_id" required>
                        <option value="">Seleccionar producto...</option>
                        <?php
                        $productos = (new Producto())->obtenerConStock();
                        foreach ($productos as $producto): ?>
                            <option value="<?php echo $producto->getId(); ?>" 
                                    data-precio="<?php echo $producto->getPrecio(); ?>"
                                    data-stock="<?php echo $producto->getStock(); ?>">
                                <?php echo htmlspecialchars($producto->getNombre()); ?> 
                                (Stock: <?php echo $producto->getStock(); ?>) - 
                                <?php echo $producto->getPrecioFormateado(); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cliente_id">👤 Cliente:</label>
                    <select id="cliente_id" name="cliente_id" required>
                        <option value="">Seleccionar cliente...</option>
                        <?php
                        $usuarios = (new Usuario())->obtenerTodos();
                        foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario->getId(); ?>">
                                <?php echo htmlspecialchars($usuario->getNombre()); ?> 
                                (<?php echo htmlspecialchars($usuario->getEmail()); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="cantidad">📦 Cantidad:</label>
                    <input type="number" id="cantidad" name="cantidad" min="1" required>
                    <small id="stock-info"></small>
                </div>
                
                <!-- Campo oculto para precio unitario -->
                <input type="hidden" id="precio_unitario" name="precio_unitario" value="">
                
                <div class="form-group">
                    <label for="estado">📊 Estado:</label>
                    <select id="estado" name="estado">
                        <option value="pendiente">⏳ Pendiente</option>
                        <option value="completada">✅ Completada</option>
                        <option value="cancelada">❌ Cancelada</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>💵 Total:</label>
                    <div id="total-venta" class="total-display">$0.00</div>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn-primary">💾 Guardar Venta</button>
                    <button type="button" class="btn-secondary" onclick="cerrarModalVenta()">❌ Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Variables globales
let ventas = <?php echo json_encode($ventas); ?>;

// Funciones de filtro
function filtrarPorFechas() {
    document.getElementById('filtro-fechas').style.display = 'block';
}

function cerrarFiltro() {
    document.getElementById('filtro-fechas').style.display = 'none';
}

// Función para eliminar venta
function verPDFVenta(ventaId) {
    // Abrir PDF en nueva ventana
    window.open('index.php?controller=admin&action=generarPDFVenta&id=' + ventaId, '_blank', 'width=800,height=600,scrollbars=yes,resizable=yes');
}

function eliminarVenta(id) {
    if (confirm('¿Estás seguro de que quieres eliminar esta venta?')) {
        document.getElementById('eliminar-id').value = id;
        document.getElementById('form-eliminar').submit();
    }
}

// Funciones del modal de nueva venta
function abrirModalVenta() {
    document.getElementById('modal-venta').style.display = 'block';
    document.getElementById('form-venta').reset();
    document.getElementById('total-venta').textContent = '$0.00';
}

function cerrarModalVenta() {
    document.getElementById('modal-venta').style.display = 'none';
}

// Función para calcular total
function calcularTotal() {
    const cantidad = parseInt(document.getElementById('cantidad').value) || 0;
    const precio = parseFloat(document.getElementById('precio_unitario').value) || 0;
    const total = cantidad * precio;
    document.getElementById('total-venta').textContent = '$' + total.toFixed(2);
}

// Event listeners
document.getElementById('producto_id').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.value) {
        // Actualizar campo oculto para precio unitario
        document.getElementById('precio_unitario').value = option.dataset.precio;
        document.getElementById('stock-info').textContent = 'Stock disponible: ' + option.dataset.stock;
        calcularTotal();
    }
});

document.getElementById('cantidad').addEventListener('input', calcularTotal);

// Cerrar modal al hacer clic fuera
window.onclick = function(event) {
    const modal = document.getElementById('modal-venta');
    if (event.target == modal) {
        cerrarModalVenta();
    }
}
</script>

<style>
/* Estilos específicos para ventas */
.stats-container {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    margin-bottom: 30px;
}

.stat-card {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    font-size: 2.5em;
    opacity: 0.7;
}

.stat-info h3 {
    margin: 0;
    font-size: 1.8em;
    color: #2c3e50;
}

.stat-info p {
    margin: 5px 0 0 0;
    color: #7f8c8d;
    font-size: 0.9em;
}

.filtro-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.form-row {
    display: flex;
    gap: 20px;
    align-items: end;
}

.ventas-table {
    width: 100%;
    border-collapse: collapse;
    background: white;
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.ventas-table th,
.ventas-table td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #ecf0f1;
}

.ventas-table th {
    background: #34495e;
    color: white;
    font-weight: 600;
}

.cliente-info {
    display: flex;
    flex-direction: column;
}

.cliente-info small {
    color: #7f8c8d;
    font-size: 0.8em;
}

.estado-badge {
    padding: 4px 8px;
    border-radius: 15px;
    font-size: 0.8em;
    font-weight: 600;
}

.estado-pendiente {
    background: #fff3cd;
    color: #856404;
}

.estado-completada {
    background: #d4edda;
    color: #155724;
}

.estado-cancelada {
    background: #f8d7da;
    color: #721c24;
}

.action-buttons {
    display: flex;
    gap: 5px;
}

.btn-delete {
    background: #dc3545;
    color: white;
    border: none;
    padding: 5px 10px;
    border-radius: 4px;
    cursor: pointer;
    font-size: 0.9em;
}

.btn-delete:hover {
    background: #c82333;
}

.total-display {
    font-size: 1.2em;
    font-weight: bold;
    color: #27ae60;
    padding: 10px;
    background: #ecf0f1;
    border-radius: 5px;
    text-align: center;
}

#stock-info {
    color: #7f8c8d;
    font-size: 0.8em;
}
</style>
