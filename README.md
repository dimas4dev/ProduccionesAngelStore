# 🏪 Producciones Angel - Tienda en Línea

Una tienda en línea desarrollada en PHP puro con arquitectura MVC (Modelo-Vista-Controlador).

## 🚀 Características

- **Sistema de Autenticación**: Login con roles (Administrador/Cliente)
- **Panel de Administración**: Gestión completa de productos, usuarios y ventas
- **Panel de Cliente**: Carrito de compras, perfil de usuario y compras
- **Base de Datos**: MySQL con 5 tablas interrelacionadas
- **Diseño Responsivo**: Interfaz moderna y adaptable a dispositivos móviles
- **Arquitectura MVC**: Código organizado y mantenible

## 📁 Estructura del Proyecto

```
/produccionesAngel/
├── index.php                 # Página principal
├── login.php                 # Sistema de login
├── admin.php                 # Panel de administración
├── cliente.php               # Panel de cliente
├── logout.php                # Cerrar sesión
├── database.sql              # Script de base de datos
├── /config/
│   └── conexion.php          # Conexión a base de datos
├── /model/
│   ├── Producto.php          # Modelo de productos
│   └── Usuario.php           # Modelo de usuarios
├── /controller/
│   ├── ProductoController.php # Controlador de productos
│   └── AuthController.php     # Controlador de autenticación
└── /view/
    ├── home.php              # Vista de productos
    ├── login.php             # Vista de login
    ├── admin.php             # Vista de administración
    └── cliente.php           # Vista de cliente
```

## 🛠️ Instalación

### Requisitos
- XAMPP (Apache + MySQL)
- PHP 7.4 o superior
- Navegador web moderno

### Pasos de Instalación

1. **Clonar/Descargar el proyecto** en la carpeta `htdocs` de XAMPP:
   ```
   /Applications/XAMPP/xamppfiles/htdocs/ProduccionesAngelStore/
   ```

2. **Iniciar XAMPP**:
   - Abrir XAMPP Control Panel
   - Iniciar Apache y MySQL

3. **Crear la Base de Datos**:
   - Abrir phpMyAdmin: `http://localhost/phpmyadmin`
   - Importar el archivo `database.sql`
   - O ejecutar el script SQL manualmente

4. **Configurar la Conexión**:
   - Editar `config/conexion.php` si es necesario
   - Verificar credenciales de MySQL (por defecto: root, sin contraseña)

5. **Acceder a la Aplicación**:
   - Abrir navegador: `http://localhost/ProduccionesAngelStore/`

## 👤 Usuarios de Prueba

### Administrador
- **Email**: `admin@produccionesangel.com`
- **Contraseña**: `admin123`
- **Acceso**: Panel de administración completo

### Clientes
- **Email**: `juan.perez@email.com`
- **Contraseña**: `password123`

- **Email**: `maria.garcia@email.com`
- **Contraseña**: `password123`

- **Email**: `carlos.lopez@email.com`
- **Contraseña**: `password123`

## 🎯 Funcionalidades

### Para Visitantes (No Logueados)
- Ver productos disponibles
- Buscar productos
- Navegar por la tienda

### Para Clientes Logueados
- Ver todos los productos
- Agregar productos al carrito
- Gestionar perfil de usuario
- Filtrar productos por categoría y precio
- Carrito de compras persistente

### Para Administradores
- **Gestión de Productos**: CRUD completo
- **Gestión de Usuarios**: Ver, editar, eliminar usuarios
- **Panel de Estadísticas**: Métricas de la tienda
- **Gestión de Mensajes**: Ver mensajes de contacto
- **Historial de Ventas**: Seguimiento de transacciones

## 🗄️ Base de Datos

### Tablas Principales

1. **usuarios**: Información de usuarios y autenticación
2. **producto**: Catálogo de productos
3. **venta**: Registro de ventas
4. **detalle_venta**: Detalles de cada venta
5. **mensajes_contacto**: Formularios de contacto

### Características de Seguridad
- Contraseñas hasheadas con `password_hash()`
- Consultas preparadas para prevenir SQL injection
- Validación de roles y permisos
- Manejo seguro de sesiones

## 🎨 Diseño y UX

- **Diseño Responsivo**: Adaptable a móviles y tablets
- **Interfaz Moderna**: Gradientes, sombras y animaciones
- **Navegación Intuitiva**: Menús claros y accesibles
- **Feedback Visual**: Notificaciones y estados de carga
- **Accesibilidad**: Contraste adecuado y navegación por teclado

## 🔧 Tecnologías Utilizadas

- **Backend**: PHP 7.4+ (Sin frameworks)
- **Base de Datos**: MySQL 8.0+
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Servidor**: Apache (XAMPP)
- **Arquitectura**: MVC (Modelo-Vista-Controlador)

## 📱 Características Técnicas

- **Sesiones Seguras**: Manejo robusto de autenticación
- **Validación de Datos**: Frontend y backend
- **Manejo de Errores**: Logs y mensajes informativos
- **Optimización**: Consultas eficientes y código limpio
- **Escalabilidad**: Estructura preparada para crecimiento

## 🚀 Próximas Funcionalidades

- [ ] Sistema de pagos integrado
- [ ] Notificaciones por email
- [ ] Sistema de reseñas y calificaciones
- [ ] Panel de reportes avanzado
- [ ] API REST para integraciones
- [ ] Sistema de cupones y descuentos

## 📞 Soporte

Para soporte técnico o consultas sobre el proyecto, contactar al desarrollador.

---

**Desarrollado con ❤️ usando PHP puro y arquitectura MVC**
