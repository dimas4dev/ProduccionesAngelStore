# 🧭 GuíaMVC — Introducción al Patrón Modelo‑Vista‑Controlador en PHP
## Proyecto "Producciones Angel" - Tienda en Línea

Bienvenido/a a esta guía específica para el proyecto **"Producciones Angel"**.  
Este documento te ayudará a **entender, modificar y ampliar** este proyecto de tienda en línea que sigue el patrón **MVC (Modelo‑Vista‑Controlador)** con sistema de autenticación y paneles diferenciados.  
Su propósito es que puedas trabajar de manera **organizada, secuencial y profesional** con este sistema completo.

---

## 📘 1. ¿Qué es el patrón MVC en "Producciones Angel"?

El patrón **MVC** en nuestro proyecto divide la aplicación en tres partes especializadas:

| Componente | Función principal | Ejemplo en Producciones Angel |
|-------------|------------------|------------------------------|
| **Modelo (Model)** | Contiene la lógica de datos. Se conecta con la base de datos y realiza consultas (SELECT, INSERT, UPDATE, DELETE). | `Producto.php`, `Usuario.php` |
| **Vista (View)** | Es la parte visual del proyecto: lo que ve el usuario. Muestra información y formularios. | `home.php`, `admin.php`, `cliente.php` |
| **Controlador (Controller)** | Es el "puente" entre el modelo y la vista. Recibe peticiones, llama al modelo, y pasa los datos a la vista. | `ProductoController.php`, `AuthController.php`, `AdminController.php` |

---

## 🧱 2. Estructura del proyecto "Producciones Angel"

La carpeta del proyecto PHP está organizada así:

```
/ProduccionesAngelStore/
│
├── index.php                    # Punto de entrada único con routing
├── /config/
│   └── conexion.php             # Conexión a la base de datos
├── /model/                      # Modelos: manejo de datos
│   ├── Producto.php             # Gestión de productos
│   └── Usuario.php              # Gestión de usuarios y autenticación
├── /controller/                 # Controladores: lógica de negocio
│   ├── AuthController.php       # Autenticación y sesiones
│   ├── AdminController.php      # Panel de administración
│   ├── ClienteController.php    # Panel de cliente
│   └── ProductoController.php   # Gestión de productos
└── /view/                       # Vistas: interfaz del usuario
    ├── /home/                   # Vista principal
    │   ├── home.php
    │   └── home.css
    ├── /login/                  # Vista de login
    │   ├── login.php
    │   └── login.css
    ├── /admin/                  # Vista de administración
    │   ├── admin.php
    │   └── admin.css
    ├── /cliente/                # Vista de cliente
    │   ├── cliente.php
    │   └── cliente.css
    └── /layouts/                # Layouts reutilizables
        ├── header.php
        └── footer.php
```

Cada carpeta tiene una función específica. Si sabes **en qué carpeta trabajar**, podrás modificar tu proyecto sin perderte.

---

## 🧩 3. Cómo trabajar paso a paso en "Producciones Angel"

Cuando necesites **agregar o modificar algo**, sigue siempre este orden:

### 🔹 Paso 1. Define qué deseas hacer
Ejemplo: "Quiero agregar un nuevo producto desde el panel de administración."  
→ Esto implica crear un formulario, validar datos, insertar en la base de datos y mostrar confirmación.

### 🔹 Paso 2. Crea o modifica el **Modelo**
- Ubícate en `/model/` y abre `Producto.php`.
- Agrega una nueva función que interactúe con la base de datos.

```php
// model/Producto.php
class Producto {
    private $db;

    public function __construct() {
        $this->db = obtenerConexion();
    }

    public function crear($datos) {
        $sql = "INSERT INTO producto (nombre, descripcion, imagen, precio, stock) VALUES (?, ?, ?, ?, ?)";
        $resultado = $this->db->ejecutarConsultaPreparada($sql, [
            $datos['nombre'],
            $datos['descripcion'],
            $datos['imagen'] ?? null,
            $datos['precio'],
            $datos['stock']
        ]);
        
        return $resultado !== false;
    }
}
```

### 🔹 Paso 3. Crea o modifica el **Controlador**
- En `/controller/`, abre `AdminController.php`.
- Llama al modelo y maneja la lógica de negocio.

```php
// controller/AdminController.php
class AdminController {
    private $productoModel;

    public function __construct() {
        $this->productoModel = new Producto();
    }

    public function crearProducto() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $datos = [
                'nombre' => $_POST['nombre'],
                'descripcion' => $_POST['descripcion'],
                'precio' => $_POST['precio'],
                'stock' => $_POST['stock']
            ];
            
            if ($this->productoModel->crear($datos)) {
                $this->mostrarExito("Producto creado exitosamente");
            } else {
                $this->mostrarError("Error al crear el producto");
            }
        }
        
        $this->cargarVista('admin', ['productos' => $this->productoModel->obtenerTodos()]);
    }
}
```

### 🔹 Paso 4. Crea o edita la **Vista**
- En `/view/admin/`, abre `admin.php`.
- Usa los datos del controlador para mostrarlos en pantalla.

```php
<!-- view/admin/admin.php -->
<div class="admin-dashboard">
    <h1>👨‍💼 Panel de Administración</h1>
    
    <!-- Formulario para agregar producto -->
    <form method="POST" action="index.php?controller=admin&action=crear">
        <input type="text" name="nombre" placeholder="Nombre del producto" required>
        <textarea name="descripcion" placeholder="Descripción" required></textarea>
        <input type="number" name="precio" placeholder="Precio" step="0.01" required>
        <input type="number" name="stock" placeholder="Stock" required>
        <button type="submit">Crear Producto</button>
    </form>
    
    <!-- Lista de productos existentes -->
    <div class="products-grid">
        <?php foreach ($productos as $producto): ?>
            <div class="product-card">
                <h3><?php echo htmlspecialchars($producto->getNombre()); ?></h3>
                <p><?php echo $producto->getPrecioFormateado(); ?></p>
                <p>Stock: <?php echo $producto->getStock(); ?></p>
            </div>
        <?php endforeach; ?>
    </div>
</div>
```

### 🔹 Paso 5. Conecta todo en `index.php`
- Este archivo es la **puerta de entrada** de tu aplicación con routing.
- Maneja las rutas y llama al controlador apropiado.

```php
// index.php
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

switch ($controller) {
    case 'admin':
        $adminController = new AdminController();
        $adminController->$action();
        break;
    case 'cliente':
        $clienteController = new ClienteController();
        $clienteController->$action();
        break;
    case 'auth':
        $authController = new AuthController();
        $authController->$action();
        break;
    default:
        // Mostrar página principal
        $productoController = new ProductoController();
        $productoController->mostrarProductos();
}
```

---

## 🧠 4. Ejemplo completo (Resumen del flujo)

### Flujo para mostrar productos:
```
index.php → routing → ProductoController
ProductoController → usa el → Producto (modelo)
Producto → obtiene datos de la base de datos
ProductoController → pasa los datos → home.php (vista)
home.php → muestra los datos en pantalla
```

### Flujo para autenticación:
```
index.php?controller=auth&action=login → AuthController
AuthController → valida credenciales → Usuario (modelo)
Usuario → verifica en base de datos
AuthController → redirige según rol → admin.php o cliente.php
```

### Flujo para panel de administración:
```
index.php?controller=admin → AdminController
AdminController → obtiene datos → Producto + Usuario (modelos)
AdminController → pasa datos → admin.php (vista)
admin.php → muestra dashboard con estadísticas
```

---

## 🔐 5. Sistema de Autenticación

### Roles y Permisos:
- **Administrador**: Acceso completo al panel de administración
- **Cliente**: Acceso al panel de cliente con carrito de compras
- **Visitante**: Solo puede ver productos sin autenticación

### URLs del Sistema:
- **Inicio**: `index.php` o `index.php?controller=home`
- **Login**: `index.php?controller=auth&action=login`
- **Panel Admin**: `index.php?controller=admin`
- **Panel Cliente**: `index.php?controller=cliente`
- **Logout**: `index.php?controller=auth&action=logout`

### Usuarios de Prueba:
- **Admin**: `admin@produccionesangel.com` / `admin123`
- **Cliente**: `juan.perez@email.com` / `password123`

---

## 💡 6. Buenas prácticas específicas para "Producciones Angel"

### Seguridad:
- Usa `htmlspecialchars()` para escapar datos en las vistas
- Valida todos los datos de entrada en los controladores
- Usa consultas preparadas para prevenir SQL injection
- Verifica permisos antes de mostrar contenido sensible

### Organización:
- Cada vista tiene su propio CSS en su carpeta
- Los layouts (header/footer) son reutilizables
- Los controladores manejan toda la lógica de negocio
- Los modelos solo acceden a datos, sin lógica compleja

### Mantenimiento:
- Comenta tu código para recordar qué hace cada parte
- Mantén la separación MVC estricta
- Usa nombres descriptivos para métodos y variables
- Guarda siempre tu archivo antes de probar

---

## 🚀 7. Retos sugeridos para "Producciones Angel"

1. **Agregar categorías de productos** y filtros por categoría
2. **Implementar sistema de carrito persistente** en base de datos
3. **Crear sistema de pedidos** con estados (pendiente, procesando, enviado)
4. **Agregar sistema de notificaciones** por email
5. **Implementar búsqueda avanzada** con múltiples filtros
6. **Crear sistema de reseñas** para productos
7. **Agregar dashboard de estadísticas** más detallado
8. **Implementar sistema de cupones** y descuentos
9. **Crear API REST** para integraciones externas
10. **Agregar sistema de reportes** en PDF

---

## 🎓 8. Conclusión

El patrón **MVC** en "Producciones Angel" te permite:
- **Organizar** el código de manera profesional
- **Escalar** el proyecto fácilmente
- **Mantener** la seguridad y separación de responsabilidades
- **Trabajar en equipo** sin conflictos

Cada vez que necesites hacer un cambio, recuerda:

> **Primero piensa qué quieres lograr, luego identifica si eso pertenece al Modelo, Controlador o Vista, y finalmente considera el sistema de autenticación y roles.**

Así te asegurarás de trabajar de forma **limpia, modular, segura y profesional** en este sistema completo de tienda en línea.

---

## 📚 9. Recursos Adicionales

- **Base de datos**: Ejecuta `database.sql` para crear las tablas
- **Configuración**: Revisa `config/conexion.php` para ajustar la conexión
- **Documentación**: Consulta `README.md` para instrucciones detalladas
- **Prompt original**: Revisa `prompt_produccionesAngel.md` para el contexto completo