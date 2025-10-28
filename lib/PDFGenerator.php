<?php

class PDFGenerator {
    
    /**
     * Genera un PDF de factura para una venta
     * @param array $venta Datos de la venta
     * @param array $producto Datos del producto
     * @param array $cliente Datos del cliente
     * @return string HTML del PDF
     */
    public static function generarFacturaVenta($venta, $producto, $cliente) {
        $fecha = date('d/m/Y H:i:s', strtotime($venta['fecha_venta']));
        $numeroFactura = 'FAC-' . str_pad($venta['id'], 6, '0', STR_PAD_LEFT);
        
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura de Venta - ' . $numeroFactura . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .factura-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 2.5em;
                    font-weight: bold;
                    color: #007bff;
                    margin-bottom: 10px;
                }
                .empresa-info {
                    color: #666;
                    font-size: 0.9em;
                }
                .factura-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                }
                .factura-datos {
                    flex: 1;
                }
                .cliente-datos {
                    flex: 1;
                    text-align: right;
                }
                .datos-titulo {
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 10px;
                    font-size: 1.1em;
                }
                .datos-valor {
                    color: #666;
                    margin-bottom: 5px;
                }
                .productos-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                .productos-table th {
                    background: #007bff;
                    color: white;
                    padding: 15px;
                    text-align: left;
                    font-weight: bold;
                }
                .productos-table td {
                    padding: 15px;
                    border-bottom: 1px solid #ddd;
                }
                .productos-table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .producto-imagen {
                    width: 60px;
                    height: 60px;
                    object-fit: cover;
                    border-radius: 5px;
                }
                .total-section {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: right;
                }
                .total-line {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    font-size: 1.1em;
                }
                .total-final {
                    font-size: 1.5em;
                    font-weight: bold;
                    color: #28a745;
                    border-top: 2px solid #28a745;
                    padding-top: 10px;
                    margin-top: 10px;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #666;
                    font-size: 0.9em;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .estado-badge {
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: bold;
                    text-transform: uppercase;
                    font-size: 0.8em;
                }
                .estado-pendiente {
                    background: #fff3cd;
                    color: #856404;
                }
                .estado-completado {
                    background: #d4edda;
                    color: #155724;
                }
                .estado-cancelado {
                    background: #f8d7da;
                    color: #721c24;
                }
                @media print {
                    body { background: white; }
                    .factura-container { box-shadow: none; }
                    .print-buttons { display: none !important; }
                }
                .print-buttons {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 1000;
                    display: flex;
                    gap: 10px;
                }
                .btn-print {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                }
                .btn-print:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="print-buttons">
                <button class="btn-print" onclick="window.print()">🖨️ Imprimir/Guardar PDF</button>
                <button class="btn-print" onclick="window.close()" style="background: #6c757d;">❌ Cerrar</button>
            </div>
            <div class="factura-container">
                <!-- Header -->
                <div class="header">
                    <div class="logo">🏪 Producciones Angel Store</div>
                    <div class="empresa-info">
                        <div>Tienda de Tecnología y Electrónicos</div>
                        <div>📧 contacto@produccionesangel.com | 📞 +1 (555) 123-4567</div>
                        <div>🌐 www.produccionesangel.com</div>
                    </div>
                </div>
                
                <!-- Información de Factura y Cliente -->
                <div class="factura-info">
                    <div class="factura-datos">
                        <div class="datos-titulo">📄 Información de Factura</div>
                        <div class="datos-valor"><strong>Número:</strong> ' . $numeroFactura . '</div>
                        <div class="datos-valor"><strong>Fecha:</strong> ' . $fecha . '</div>
                        <div class="datos-valor"><strong>Estado:</strong> <span class="estado-badge estado-' . $venta['estado'] . '">' . ucfirst($venta['estado']) . '</span></div>
                    </div>
                    <div class="cliente-datos">
                        <div class="datos-titulo">👤 Datos del Cliente</div>
                        <div class="datos-valor"><strong>Nombre:</strong> ' . htmlspecialchars($cliente['nombre']) . '</div>
                        <div class="datos-valor"><strong>Email:</strong> ' . htmlspecialchars($cliente['email']) . '</div>
                        <div class="datos-valor"><strong>ID Cliente:</strong> #' . $cliente['id'] . '</div>
                    </div>
                </div>
                
                <!-- Tabla de Productos -->
                <table class="productos-table">
                    <thead>
                        <tr>
                            <th>🖼️ Imagen</th>
                            <th>📦 Producto</th>
                            <th>📝 Descripción</th>
                            <th>💰 Precio Unit.</th>
                            <th>🔢 Cantidad</th>
                            <th>💵 Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <img src="' . $producto['imagen_url'] . '" alt="' . htmlspecialchars($producto['nombre']) . '" class="producto-imagen">
                            </td>
                            <td><strong>' . htmlspecialchars($producto['nombre']) . '</strong></td>
                            <td>' . htmlspecialchars(substr($producto['descripcion'], 0, 100)) . (strlen($producto['descripcion']) > 100 ? '...' : '') . '</td>
                            <td>$' . number_format($venta['precio_unitario'], 2) . '</td>
                            <td>' . $venta['cantidad'] . '</td>
                            <td>$' . number_format($venta['total'], 2) . '</td>
                        </tr>
                    </tbody>
                </table>
                
                <!-- Totales -->
                <div class="total-section">
                    <div class="total-line">
                        <span>Subtotal:</span>
                        <span>$' . number_format($venta['total'], 2) . '</span>
                    </div>
                    <div class="total-line">
                        <span>Impuestos (0%):</span>
                        <span>$0.00</span>
                    </div>
                    <div class="total-line">
                        <span>Descuentos:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="total-line total-final">
                        <span>💰 TOTAL:</span>
                        <span>$' . number_format($venta['total'], 2) . '</span>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <p><strong>¡Gracias por tu compra!</strong></p>
                    <p>Este documento es una factura válida para efectos fiscales.</p>
                    <p>Para consultas o soporte técnico, contacta a nuestro equipo de atención al cliente.</p>
                    <p>Generado el ' . date('d/m/Y H:i:s') . ' - Sistema Producciones Angel Store</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Genera un PDF de factura para múltiples productos (checkout)
     * @param array $pedidoData Datos del pedido completo
     * @param array $productos Array de productos
     * @param array $cliente Datos del cliente
     * @return string HTML del PDF
     */
    public static function generarFacturaPedido($pedidoData, $productos, $cliente) {
        $fecha = date('d/m/Y H:i:s', strtotime($pedidoData['fecha'] ?? date('Y-m-d H:i:s')));
        $pedidoId = $pedidoData['id'] ?? time();
        $numeroFactura = 'PED-' . str_pad($pedidoId, 8, '0', STR_PAD_LEFT);
        
        $html = '
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Factura de Pedido - ' . $numeroFactura . '</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    margin: 0;
                    padding: 20px;
                    background-color: #f5f5f5;
                }
                .factura-container {
                    max-width: 800px;
                    margin: 0 auto;
                    background: white;
                    padding: 30px;
                    border-radius: 10px;
                    box-shadow: 0 0 20px rgba(0,0,0,0.1);
                }
                .header {
                    text-align: center;
                    border-bottom: 3px solid #007bff;
                    padding-bottom: 20px;
                    margin-bottom: 30px;
                }
                .logo {
                    font-size: 2.5em;
                    font-weight: bold;
                    color: #007bff;
                    margin-bottom: 10px;
                }
                .empresa-info {
                    color: #666;
                    font-size: 0.9em;
                }
                .factura-info {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 30px;
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                }
                .factura-datos {
                    flex: 1;
                }
                .cliente-datos {
                    flex: 1;
                    text-align: right;
                }
                .datos-titulo {
                    font-weight: bold;
                    color: #333;
                    margin-bottom: 10px;
                    font-size: 1.1em;
                }
                .datos-valor {
                    color: #666;
                    margin-bottom: 5px;
                }
                .productos-table {
                    width: 100%;
                    border-collapse: collapse;
                    margin-bottom: 30px;
                }
                .productos-table th {
                    background: #007bff;
                    color: white;
                    padding: 15px;
                    text-align: left;
                    font-weight: bold;
                }
                .productos-table td {
                    padding: 15px;
                    border-bottom: 1px solid #ddd;
                }
                .productos-table tr:nth-child(even) {
                    background: #f8f9fa;
                }
                .producto-imagen {
                    width: 60px;
                    height: 60px;
                    object-fit: cover;
                    border-radius: 5px;
                }
                .total-section {
                    background: #f8f9fa;
                    padding: 20px;
                    border-radius: 8px;
                    text-align: right;
                }
                .total-line {
                    display: flex;
                    justify-content: space-between;
                    margin-bottom: 10px;
                    font-size: 1.1em;
                }
                .total-final {
                    font-size: 1.5em;
                    font-weight: bold;
                    color: #28a745;
                    border-top: 2px solid #28a745;
                    padding-top: 10px;
                    margin-top: 10px;
                }
                .footer {
                    margin-top: 40px;
                    text-align: center;
                    color: #666;
                    font-size: 0.9em;
                    border-top: 1px solid #ddd;
                    padding-top: 20px;
                }
                .estado-badge {
                    display: inline-block;
                    padding: 5px 15px;
                    border-radius: 20px;
                    font-weight: bold;
                    text-transform: uppercase;
                    font-size: 0.8em;
                }
                .estado-pendiente {
                    background: #fff3cd;
                    color: #856404;
                }
                .metodo-pago {
                    background: #e7f3ff;
                    padding: 15px;
                    border-radius: 8px;
                    margin-bottom: 20px;
                    border-left: 4px solid #007bff;
                }
                @media print {
                    body { background: white; }
                    .factura-container { box-shadow: none; }
                    .print-buttons { display: none !important; }
                }
                .print-buttons {
                    position: fixed;
                    top: 20px;
                    right: 20px;
                    z-index: 1000;
                    display: flex;
                    gap: 10px;
                }
                .btn-print {
                    background: #007bff;
                    color: white;
                    border: none;
                    padding: 10px 20px;
                    border-radius: 5px;
                    cursor: pointer;
                    font-weight: bold;
                    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
                }
                .btn-print:hover {
                    background: #0056b3;
                }
            </style>
        </head>
        <body>
            <div class="print-buttons">
                <button class="btn-print" onclick="window.print()">🖨️ Imprimir/Guardar PDF</button>
                <button class="btn-print" onclick="window.close()" style="background: #6c757d;">❌ Cerrar</button>
            </div>
            <div class="factura-container">
                <!-- Header -->
                <div class="header">
                    <div class="logo">🏪 Producciones Angel Store</div>
                    <div class="empresa-info">
                        <div>Tienda de Tecnología y Electrónicos</div>
                        <div>📧 contacto@produccionesangel.com | 📞 +1 (555) 123-4567</div>
                        <div>🌐 www.produccionesangel.com</div>
                    </div>
                </div>
                
                <!-- Información de Factura y Cliente -->
                <div class="factura-info">
                    <div class="factura-datos">
                        <div class="datos-titulo">📄 Información de Pedido</div>
                        <div class="datos-valor"><strong>Número:</strong> ' . $numeroFactura . '</div>
                        <div class="datos-valor"><strong>Fecha:</strong> ' . $fecha . '</div>
                        <div class="datos-valor"><strong>Estado:</strong> <span class="estado-badge estado-pendiente">Pendiente</span></div>
                    </div>
                    <div class="cliente-datos">
                        <div class="datos-titulo">👤 Datos del Cliente</div>
                        <div class="datos-valor"><strong>Nombre:</strong> ' . htmlspecialchars($pedidoData['customer_name']) . '</div>
                        <div class="datos-valor"><strong>Email:</strong> ' . htmlspecialchars($pedidoData['customer_email']) . '</div>
                        <div class="datos-valor"><strong>Teléfono:</strong> ' . htmlspecialchars($pedidoData['customer_phone']) . '</div>
                    </div>
                </div>
                
                <!-- Información de Entrega y Pago -->
                <div class="metodo-pago">
                    <div class="datos-titulo">🚚 Información de Entrega y Pago</div>
                    <div class="datos-valor"><strong>Dirección:</strong> ' . htmlspecialchars($pedidoData['delivery_address']) . '</div>
                    <div class="datos-valor"><strong>Método de Pago:</strong> ' . self::getPaymentMethodText($pedidoData['payment_method']) . '</div>
                    ' . ($pedidoData['notes'] ? '<div class="datos-valor"><strong>Notas:</strong> ' . htmlspecialchars($pedidoData['notes']) . '</div>' : '') . '
                </div>
                
                <!-- Tabla de Productos -->
                <table class="productos-table">
                    <thead>
                        <tr>
                            <th>🖼️ Imagen</th>
                            <th>📦 Producto</th>
                            <th>💰 Precio Unit.</th>
                            <th>🔢 Cantidad</th>
                            <th>💵 Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>';
        
        $subtotal = 0;
        foreach ($pedidoData['items'] as $item) {
            $itemTotal = $item['precio'] * $item['cantidad'];
            $subtotal += $itemTotal;
            
            $html .= '
                        <tr>
                            <td>
                                <img src="' . ($item['imagen'] ?? 'data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNjAiIGhlaWdodD0iNjAiIHZpZXdCb3g9IjAgMCA2MCA2MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjYwIiBoZWlnaHQ9IjYwIiBmaWxsPSIjZjhmOWZhIi8+CjxwYXRoIGQ9Ik0yMCAyMEg0MFY0MEgyMFYyMFoiIGZpbGw9IiNjY2MiLz4KPC9zdmc+') . '" alt="' . htmlspecialchars($item['nombre']) . '" class="producto-imagen">
                            </td>
                            <td><strong>' . htmlspecialchars($item['nombre']) . '</strong></td>
                            <td>$' . number_format($item['precio'], 2) . '</td>
                            <td>' . $item['cantidad'] . '</td>
                            <td>$' . number_format($itemTotal, 2) . '</td>
                        </tr>';
        }
        
        $html .= '
                    </tbody>
                </table>
                
                <!-- Totales -->
                <div class="total-section">
                    <div class="total-line">
                        <span>Subtotal:</span>
                        <span>$' . number_format($subtotal, 2) . '</span>
                    </div>
                    <div class="total-line">
                        <span>Impuestos (0%):</span>
                        <span>$0.00</span>
                    </div>
                    <div class="total-line">
                        <span>Descuentos:</span>
                        <span>$0.00</span>
                    </div>
                    <div class="total-line total-final">
                        <span>💰 TOTAL:</span>
                        <span>$' . number_format($pedidoData['total'], 2) . '</span>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="footer">
                    <p><strong>¡Gracias por tu compra!</strong></p>
                    <p>Este documento es una factura válida para efectos fiscales.</p>
                    <p>Para consultas o soporte técnico, contacta a nuestro equipo de atención al cliente.</p>
                    <p>Generado el ' . date('d/m/Y H:i:s') . ' - Sistema Producciones Angel Store</p>
                </div>
            </div>
        </body>
        </html>';
        
        return $html;
    }
    
    /**
     * Obtiene el texto del método de pago
     */
    private static function getPaymentMethodText($method) {
        $methods = [
            'efectivo' => '💵 Efectivo contra entrega',
            'transferencia' => '🏦 Transferencia bancaria',
            'tarjeta' => '💳 Tarjeta de crédito/débito'
        ];
        return $methods[$method] ?? $method;
    }
    
    /**
     * Genera y descarga el PDF
     * @param string $html Contenido HTML
     * @param string $filename Nombre del archivo
     */
    public static function descargarPDF($html, $filename) {
        // Configurar headers para descarga
        header('Content-Type: text/html; charset=UTF-8');
        header('Content-Disposition: inline; filename="' . $filename . '.html"');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $html;
        exit;
    }
    
    /**
     * Genera y muestra el PDF en el navegador
     * @param string $html Contenido HTML
     * @param string $filename Nombre del archivo
     */
    public static function mostrarPDF($html, $filename) {
        // Configurar headers para mostrar en navegador
        header('Content-Type: text/html; charset=UTF-8');
        header('Cache-Control: private, max-age=0, must-revalidate');
        header('Pragma: public');
        
        echo $html;
        exit;
    }
}
