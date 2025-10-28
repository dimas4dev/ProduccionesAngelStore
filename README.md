# 🏪 Producciones Angel - Tienda en Línea

Una tienda en línea desarrollada en PHP puro con arquitectura MVC (Modelo-Vista-Controlador).

## 🚀 Características

- **Sistema de Autenticación Completo**: Login y registro con roles (Administrador/Cliente)
- **Panel de Administración Avanzado**: Gestión completa de productos, usuarios, ventas y mensajes
- **Panel de Cliente Funcional**: Carrito de compras, perfil de usuario, historial de compras
- **Sistema de Ventas**: Procesamiento de pedidos, generación de facturas PDF
- **Formulario de Contacto**: Sistema de mensajes con gestión administrativa
- **Búsqueda de Productos**: Funcionalidad de búsqueda en tiempo real
- **Gestión de Imágenes**: Subida y manejo de imágenes de productos
- **Base de Datos Robusta**: MySQL con 5 tablas interrelacionadas y índices optimizados
- **Diseño Responsivo**: Interfaz moderna y adaptable a dispositivos móviles
- **Arquitectura MVC**: Código organizado, mantenible y escalable
- **Generación de PDFs**: Facturas automáticas para ventas
- **Sistema de Routing**: Manejo inteligente de URLs y controladores

## 📁 Estructura del Proyecto

```
/ProduccionesAngelStore/
├── index.php                 # Punto de entrada principal con routing
├── database.sql              # Script de base de datos
├── README.md                 # Documentación del proyecto
├── GuíaMVC.md               # Guía de arquitectura MVC
├── /config/
│   └── conexion.php          # Configuración de conexión a BD
├── /controller/
│   ├── AdminController.php   # Controlador del panel de administración
│   ├── AuthController.php     # Controlador de autenticación
│   ├── ClienteController.php # Controlador del panel de cliente
│   ├── ContactoController.php # Controlador de formulario de contacto
│   ├── ProductoController.php # Controlador de productos
│   └── VentasController.php   # Controlador de gestión de ventas
├── /model/
│   ├── MensajeContacto.php   # Modelo de mensajes de contacto
│   ├── Producto.php          # Modelo de productos
│   ├── Usuario.php           # Modelo de usuarios
│   └── Venta.php             # Modelo de ventas
├── /view/
│   ├── /admin/
│   │   ├── admin.css         # Estilos del panel de administración
│   │   └── admin.php         # Vista del panel de administración
│   ├── /cliente/
│   │   ├── cliente.css       # Estilos del panel de cliente
│   │   └── cliente.php       # Vista del panel de cliente
│   ├── /contacto/
│   │   ├── contacto.css      # Estilos del formulario de contacto
│   │   └── contacto.php      # Vista del formulario de contacto
│   ├── /home/
│   │   ├── home.css          # Estilos de la página principal
│   │   └── home.php          # Vista de la página principal
│   ├── /layouts/
│   │   ├── footer.php        # Pie de página común
│   │   └── header.php        # Cabecera común
│   ├── /login/
│   │   ├── login.css         # Estilos del formulario de login
│   │   └── login.php         # Vista del formulario de login
│   ├── /register/
│   │   ├── register.css      # Estilos del formulario de registro
│   │   └── register.php      # Vista del formulario de registro
│   └── /ventas/
│       └── ventas.php        # Vista de gestión de ventas
├── /lib/
│   └── PDFGenerator.php      # Generador de PDFs para facturas
└── /uploads/
    └── /productos/           # Directorio de imágenes de productos
```

## 🛠️ Instalación

### Requisitos del Sistema
- **XAMPP** (Apache + MySQL + PHP) o servidor web equivalente
- **PHP 7.4** o superior (recomendado PHP 8.0+)
- **MySQL 5.7** o superior (recomendado MySQL 8.0+)
- **Apache** con mod_rewrite habilitado
- **Navegador web moderno** (Chrome, Firefox, Safari, Edge)

### Pasos de Instalación Detallados

1. **Preparar el Entorno**:
   ```bash
   # Descargar e instalar XAMPP desde https://www.apachefriends.org/
   # O usar un entorno de desarrollo como MAMP, WAMP, o Laragon
   ```

2. **Clonar/Descargar el Proyecto**:
   ```bash
   # Opción 1: Clonar desde Git
   git clone [URL_DEL_REPOSITORIO] ProduccionesAngelStore
   
   # Opción 2: Descargar ZIP y extraer
   # Colocar en la carpeta htdocs de XAMPP:
   /Applications/XAMPP/xamppfiles/htdocs/ProduccionesAngelStore/
   # O en Windows:
   C:\xampp\htdocs\ProduccionesAngelStore\
   ```

3. **Iniciar Servicios**:
   - Abrir **XAMPP Control Panel**
   - Iniciar **Apache** y **MySQL**
   - Verificar que ambos servicios estén funcionando (estado "Running")

4. **Configurar la Base de Datos**:
   ```sql
   -- Opción 1: Usar phpMyAdmin
   -- Acceder a: http://localhost/phpmyadmin
   -- Crear nueva base de datos: produccionesAngel
   -- Importar el archivo database.sql
   
   -- Opción 2: Línea de comandos MySQL
   mysql -u root -p < database.sql
   ```

5. **Configurar Permisos**:
   ```bash
   # Asegurar permisos de escritura para uploads
   chmod 755 uploads/
   chmod 755 uploads/productos/
   ```

6. **Verificar Configuración**:
   - Editar `config/conexion.php` si es necesario
   - Verificar credenciales de MySQL (por defecto: usuario `root`, sin contraseña)
   - Ajustar configuración según tu entorno

7. **Acceder a la Aplicación**:
   ```
   URL Principal: http://localhost/ProduccionesAngelStore/
   Panel Admin: http://localhost/ProduccionesAngelStore/?controller=admin
   Login: http://localhost/ProduccionesAngelStore/?controller=auth&action=login
   ```

### Configuración Adicional

#### Para Producción
- Cambiar credenciales de base de datos
- Configurar HTTPS
- Ajustar permisos de archivos
- Configurar backup automático de BD

#### Para Desarrollo
- Habilitar logs de PHP
- Configurar debug mode
- Usar herramientas de desarrollo

## 👤 Usuarios de Prueba

### Administradores
- **Email**: `admin@produccionesangel.com`
- **Contraseña**: `admin123`
- **Acceso**: Panel de administración completo con todas las funcionalidades

- **Email**: `add@example.com`
- **Contraseña**: `add123`
- **Acceso**: Panel de administración alternativo

### Clientes de Prueba
- **Email**: `juan.perez@email.com`
- **Contraseña**: `password123`
- **Perfil**: Cliente estándar con acceso a carrito y compras

- **Email**: `maria.garcia@email.com`
- **Contraseña**: `password123`
- **Perfil**: Cliente estándar con acceso a carrito y compras

- **Email**: `carlos.lopez@email.com`
- **Contraseña**: `password123`
- **Perfil**: Cliente estándar con acceso a carrito y compras

> **Nota**: Todos los usuarios de prueba tienen datos de ejemplo incluidos en el script `database.sql`

## 🎯 Funcionalidades

### Para Visitantes (No Logueados)
- **Navegación Libre**: Ver productos disponibles sin restricciones
- **Búsqueda Avanzada**: Buscar productos por nombre con resultados en tiempo real
- **Información Detallada**: Ver descripciones, precios y disponibilidad de productos
- **Formulario de Contacto**: Enviar consultas y mensajes a la empresa
- **Diseño Responsivo**: Experiencia optimizada en móviles y tablets

### Para Clientes Registrados
- **Gestión de Perfil**: Actualizar información personal y datos de contacto
- **Carrito de Compras**: Agregar, modificar y eliminar productos del carrito
- **Proceso de Checkout**: Completar compras de forma segura
- **Historial de Compras**: Ver todas las transacciones realizadas
- **Facturas PDF**: Descargar facturas de compras anteriores
- **Búsqueda Personalizada**: Acceso completo a la funcionalidad de búsqueda
- **Sesión Persistente**: Mantener carrito y preferencias entre sesiones

### Para Administradores
- **Dashboard Completo**: Estadísticas generales de la tienda
- **Gestión de Productos**: 
  - Crear, editar y eliminar productos
  - Subir y gestionar imágenes de productos
  - Control de stock e inventario
- **Gestión de Usuarios**: 
  - Ver, editar y eliminar usuarios
  - Asignar roles y permisos
  - Monitorear actividad de usuarios
- **Gestión de Ventas**:
  - Ver historial completo de ventas
  - Generar reportes por fechas
  - Procesar pedidos manualmente
  - Generar facturas PDF
- **Gestión de Mensajes**:
  - Ver mensajes de contacto
  - Responder consultas de clientes
  - Archivar mensajes procesados
- **Panel de Estadísticas**:
  - Métricas de ventas y productos
  - Análisis de usuarios activos
  - Reportes de inventario

## 🗄️ Base de Datos

### Estructura de Tablas

1. **usuarios**: Gestión de usuarios y autenticación
   - Campos: id, nombre, email, clave, direccion, telefono, role, fecha_registro
   - Roles: `cliente`, `administrador`
   - Seguridad: Contraseñas hasheadas con `password_hash()`

2. **producto**: Catálogo de productos de la tienda
   - Campos: id, nombre, descripcion, imagen, precio, stock, fecha_creacion
   - Características: Control de inventario, gestión de imágenes

3. **venta**: Registro principal de transacciones
   - Campos: id, usuario_id, fecha, total
   - Relaciones: Conecta usuarios con detalles de venta

4. **detalle_venta**: Items específicos de cada venta
   - Campos: id, venta_id, producto_id, cantidad, precio
   - Funcionalidad: Tracking detallado de productos vendidos

5. **mensajes_contacto**: Sistema de comunicación con clientes
   - Campos: id, nombre, email, direccion, telefono, mensaje, fecha
   - Uso: Formulario de contacto y gestión administrativa

### Características de Seguridad
- **Contraseñas Seguras**: Hash con `password_hash()` y `password_verify()`
- **Consultas Preparadas**: Prevención de SQL injection en todas las operaciones
- **Validación de Roles**: Control de acceso basado en roles de usuario
- **Manejo Seguro de Sesiones**: Gestión robusta de autenticación
- **Sanitización de Datos**: Limpieza de inputs del usuario
- **Índices Optimizados**: Mejora del rendimiento en consultas frecuentes

### Datos de Ejemplo Incluidos
- **10 productos** con información completa (laptops, smartphones, auriculares, etc.)
- **5 usuarios** (2 administradores + 3 clientes)
- **3 mensajes de contacto** de ejemplo
- **Índices** para optimizar consultas por nombre, precio, email y fechas

## 🎨 Diseño y UX

- **Diseño Responsivo**: Adaptable a móviles y tablets
- **Interfaz Moderna**: Gradientes, sombras y animaciones
- **Navegación Intuitiva**: Menús claros y accesibles
- **Feedback Visual**: Notificaciones y estados de carga
- **Accesibilidad**: Contraste adecuado y navegación por teclado

## 🔧 Tecnologías Utilizadas

### Backend
- **PHP 7.4+**: Lenguaje principal del servidor (compatible con PHP 8.0+)
- **MySQL 5.7+**: Base de datos relacional para almacenamiento
- **Apache**: Servidor web con soporte para mod_rewrite
- **PDO**: Interfaz de acceso a datos para consultas seguras

### Frontend
- **HTML5**: Estructura semántica y moderna
- **CSS3**: Estilos avanzados con flexbox, grid y animaciones
- **JavaScript (Vanilla)**: Interactividad sin dependencias externas
- **Responsive Design**: Adaptación automática a diferentes dispositivos

### Arquitectura y Patrones
- **MVC (Modelo-Vista-Controlador)**: Separación clara de responsabilidades
- **Routing Manual**: Sistema de enrutamiento personalizado
- **Singleton Pattern**: Para conexiones de base de datos
- **Factory Pattern**: Para creación de objetos de modelos

### Librerías y Herramientas
- **PDFGenerator**: Generación de facturas en formato PDF
- **File Upload**: Sistema de subida de imágenes de productos
- **Session Management**: Gestión segura de sesiones de usuario
- **Password Hashing**: Seguridad avanzada para contraseñas

## 📱 Características Técnicas

### Seguridad
- **Autenticación Robusta**: Sistema de login con hash de contraseñas
- **Autorización por Roles**: Control de acceso granular (admin/cliente)
- **Prevención SQL Injection**: Consultas preparadas en todas las operaciones
- **Sanitización de Inputs**: Limpieza automática de datos del usuario
- **Manejo Seguro de Sesiones**: Protección contra ataques de sesión

### Rendimiento
- **Consultas Optimizadas**: Índices en campos críticos de la base de datos
- **Caching de Sesiones**: Reducción de consultas repetitivas
- **Lazy Loading**: Carga eficiente de datos según necesidad
- **Compresión de Imágenes**: Optimización automática de archivos subidos

### Escalabilidad
- **Arquitectura Modular**: Fácil extensión de funcionalidades
- **Separación de Responsabilidades**: Código mantenible y testeable
- **Configuración Centralizada**: Gestión fácil de parámetros del sistema
- **API-Ready**: Estructura preparada para futuras integraciones REST

### Mantenibilidad
- **Código Limpio**: Estándares de codificación consistentes
- **Documentación Inline**: Comentarios detallados en funciones críticas
- **Manejo de Errores**: Sistema robusto de logging y recuperación
- **Testing Ready**: Estructura preparada para implementar tests unitarios

## 🚀 Próximas Funcionalidades

### Funcionalidades en Desarrollo
- [ ] **Sistema de Pagos**: Integración con pasarelas de pago (PayPal, Stripe)
- [ ] **Notificaciones Email**: Sistema de emails automáticos para ventas y contactos
- [ ] **Sistema de Reseñas**: Calificaciones y comentarios de productos
- [ ] **Panel de Reportes**: Dashboard avanzado con gráficos y métricas
- [ ] **API REST**: Endpoints para integraciones externas
- [ ] **Sistema de Cupones**: Descuentos y promociones automáticas

### Mejoras Planificadas
- [ ] **Carrito Persistente**: Guardar carrito en base de datos
- [ ] **Wishlist**: Lista de deseos para usuarios
- [ ] **Comparador de Productos**: Comparar características de productos
- [ ] **Sistema de Inventario**: Alertas de stock bajo
- [ ] **Multi-idioma**: Soporte para múltiples idiomas
- [ ] **Tema Oscuro**: Modo oscuro para la interfaz

### Optimizaciones Técnicas
- [ ] **Caching Avanzado**: Redis para sesiones y consultas frecuentes
- [ ] **CDN**: Distribución de contenido estático
- [ ] **Compresión**: Optimización de imágenes y recursos
- [ ] **Testing**: Suite completa de tests unitarios y de integración
- [ ] **Docker**: Containerización para despliegue fácil
- [ ] **CI/CD**: Pipeline de integración continua

## 📞 Soporte

Para soporte técnico o consultas sobre el proyecto, contactar al desarrollador.

---

**Desarrollado con ❤️ usando PHP puro y arquitectura MVC**
