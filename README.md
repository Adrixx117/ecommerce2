# E-commerce con PHP, JavaScript y MySQL

Sistema completo de comercio electrónico con todas las funcionalidades requeridas.

## 📋 Características

✅ **Autenticación**
- Sistema de registro de usuarios
- Login/Logout
- Sesiones persistentes

✅ **Catálogo de Productos**
- Landing page con productos destacados
- Catálogo completo con paginación
- Búsqueda de productos
- Filtros por categoría
- Ordenamiento por precio y nombre

✅ **Carrito de Compras**
- Carrito persistente (guardado en base de datos)
- Funciona para usuarios logueados e invitados
- Agregar/eliminar productos
- Actualizar cantidades
- Cálculo automático de totales

✅ **Sistema de Pedidos**
- Checkout con formulario completo
- Validación de datos
- Actualización automática de stock
- Generación de factura

✅ **Envío de Emails**
- Factura en HTML enviada por correo
- Detalles completos del pedido

## 🚀 Instalación

### 1. Requisitos Previos

- **XAMPP** o **WAMP** instalado (incluye Apache, PHP y MySQL)
- **HeidiSQL** (o phpMyAdmin)
- Navegador web moderno

### 2. Configurar la Base de Datos

1. Abre **HeidiSQL** y conéctate a tu servidor MySQL
2. Crea una nueva base de datos o usa una existente
3. Ejecuta el archivo `ecommerce.sql` para crear todas las tablas
4. Las tablas se crearán automáticamente con datos de ejemplo

### 3. Configurar los Archivos

1. Crea una carpeta en tu servidor web:
   - XAMPP: `C:/xampp/htdocs/ecommerce/`
   - WAMP: `C:/wamp64/www/ecommerce/`

2. Coloca todos los archivos PHP en esta carpeta:
   - `config.php`
   - `auth.php`
   - `productos.php`
   - `carrito.php`
   - `pedidos.php`
   - `index.html`
   - `app.js`

3. **IMPORTANTE**: Edita `config.php` y configura tus credenciales:

```php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'ecommerce_db');  // Tu nombre de BD
define('DB_USER', 'root');          // Tu usuario
define('DB_PASS', '');              // Tu contraseña

// Configuración de email (para envío de facturas)
define('SMTP_USER', 'tu_email@gmail.com');
define('SMTP_PASS', 'tu_password_app');
define('SMTP_FROM', 'tu_email@gmail.com');
```

### 4. Configurar Email (Gmail)

Para que funcione el envío de facturas por email:

1. Ve a tu cuenta de Google
2. Activa la **verificación en 2 pasos**
3. Genera una **contraseña de aplicación**:
   - Ve a: https://myaccount.google.com/apppasswords
   - Genera una nueva contraseña
   - Usa esa contraseña en `SMTP_PASS` del `config.php`

**Alternativa sin Gmail**: Puedes usar la función `mail()` de PHP nativa modificando la función `enviar_factura()` en `pedidos.php`.

### 5. Iniciar el Servidor

1. Inicia Apache y MySQL desde XAMPP/WAMP
2. Abre tu navegador
3. Visita: `http://localhost/ecommerce/`

## 📁 Estructura de Archivos

```
ecommerce/
│
├── config.php          # Configuración y conexión a BD
├── auth.php           # API de autenticación
├── productos.php      # API de productos
├── carrito.php        # API de carrito
├── pedidos.php        # API de pedidos
├── index.html         # Frontend HTML
├── app.js            # Lógica JavaScript
└── ecommerce.sql     # Base de datos
```

## 🎯 Uso del Sistema

### Para Usuarios

1. **Navegar sin registro**: Puedes ver productos y agregar al carrito
2. **Registrarse**: Crea una cuenta para guardar tu información
3. **Buscar productos**: Usa la barra de búsqueda y filtros
4. **Agregar al carrito**: Click en "Agregar al Carrito"
5. **Ver carrito**: Click en el ícono del carrito
6. **Checkout**: Completa el formulario y confirma
7. **Recibir factura**: Se envía automáticamente a tu email

### Productos de Ejemplo

La base de datos incluye 8 productos de ejemplo en 4 categorías:
- Electrónica
- Ropa
- Hogar
- Deportes

## 🔧 Funcionalidades Técnicas

### Backend (PHP)

- **PDO** para consultas seguras (prevención de SQL injection)
- **Sesiones** para autenticación
- **Transacciones** para garantizar integridad de datos
- **Password hashing** con bcrypt
- **Validación** de datos de entrada
- **API RESTful** con JSON

### Frontend (JavaScript)

- **Vanilla JavaScript** puro (sin frameworks)
- **Fetch API** para peticiones asíncronas
- **DOM Manipulation** dinámica
- **Responsive Design** con CSS moderno
- **Animaciones** CSS
- **Modales** para login, registro, carrito y checkout

### Base de Datos (MySQL)

- **Normalización** correcta
- **Foreign Keys** para integridad referencial
- **Índices** para optimización
- **Timestamps** automáticos
- **Transacciones** ACID

## 🛠️ Personalización

### Cambiar Colores

Edita las variables CSS en `index.html`:
```css
/* Busca estos colores y cámbialos */
background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
```

### Agregar Más Productos

```sql
INSERT INTO productos (nombre, descripcion, precio, stock, categoria_id, destacado) 
VALUES ('Nuevo Producto', 'Descripción', 99.99, 50, 1, FALSE);
```

### Modificar Imágenes

Los productos usan emojis por defecto (🎁). Para usar imágenes reales:

1. Agrega una columna en la BD o usa la existente
2. Modifica la función `renderizarProductos()` en `app.js`
3. Guarda imágenes en `/img/productos/`

## 🐛 Resolución de Problemas

### Error de Conexión a BD
- Verifica que MySQL esté corriendo
- Revisa credenciales en `config.php`
- Confirma que la BD existe

### No se envían emails
- Verifica configuración SMTP en `config.php`
- Usa contraseña de aplicación de Google
- Revisa logs de PHP para errores

### El carrito no persiste
- Verifica que las sesiones de PHP estén activas
- Revisa que la tabla `carritos` exista
- Comprueba permisos de archivos de sesión

### Productos no se muestran
- Ejecuta el SQL completo con los INSERT de ejemplo
- Verifica que Apache y MySQL estén activos
- Revisa la consola del navegador para errores JS

## 📝 Notas de Seguridad

- ✅ Contraseñas hasheadas con bcrypt
- ✅ Consultas preparadas (PDO)
- ✅ Validación de entrada
- ✅ Protección XSS con htmlspecialchars
- ✅ Sesiones seguras

**Para producción**:
- Usa HTTPS
- Configura variables de entorno
- Activa error logging (no mostrar en pantalla)
- Implementa rate limiting
- Añade CAPTCHA en formularios

## 📄 Licencia

Proyecto de código abierto para fines educativos.

## 🤝 Soporte

Si encuentras algún error o tienes preguntas:
1. Revisa la sección de Resolución de Problemas
2. Verifica los logs de error de PHP
3. Inspecciona la consola del navegador

---

**¡Disfruta tu e-commerce! 🎉**