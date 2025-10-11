# Panel Administrativo E-commerce - Laravel 12 + Filament 4

Sistema completo de administración para ecommerce construido con Laravel 12 y Filament 4, diseñado para conectarse a una API del frontend.

## 🚀 Características

### 📦 Módulo de Catálogo de Productos
- **Productos**: Gestión completa con SKU, precios, stock, y variantes
- **Categorías**: Jerarquía multinivel con categorías padre e hijas
- **Marcas**: Gestión de fabricantes y marcas
- **Variantes**: Productos con múltiples opciones (tallas, colores, etc.)
- **Imágenes**: Galería de imágenes por producto/variante
- **Etiquetas**: Sistema de tags para filtros y SEO
- **Inventario**: Control de stock por ubicación y producto
- **Descuentos**: Promociones y descuentos configurables

### 👥 Módulo de Usuarios y Clientes
- **Roles y Permisos**: Sistema de control de acceso
- **Direcciones**: Múltiples direcciones de envío y facturación
- **Carritos**: Gestión de carritos activos
- **Lista de Deseos**: Productos favoritos por usuario

### 📋 Módulo de Pedidos, Pagos y Envíos
- **Pedidos**: Gestión completa del flujo de órdenes
- **Items de Pedido**: Detalle de productos en cada orden
- **Pagos**: Integración con múltiples métodos de pago
- **Envíos**: Tracking y gestión de entregas
- **Facturas**: Generación de facturas
- **Reembolsos**: Gestión de devoluciones
- **Impuestos**: Configuración de tasas impositivas

### ⚙️ Módulo de Administración y Marketing
- **Configuraciones**: Settings globales del sistema
- **Cupones**: Códigos de descuento y promociones
- **Newsletter**: Gestión de suscriptores
- **Banners**: Promociones visuales para el frontend
- **Reseñas**: Sistema de reviews de productos
- **Logs**: Auditoría de acciones del sistema

## 🛠️ Tecnologías

- **Laravel 12**: Framework PHP moderno
- **Filament 4**: Panel de administración elegante y potente
- **PostgreSQL**: Base de datos relacional
- **PHP 8.2+**: Lenguaje de programación

## 📋 Requisitos

- PHP >= 8.2
- PostgreSQL >= 14
- Composer
- Node.js y NPM (para assets)

## 🔧 Instalación

1. **Clonar el repositorio**
```bash
cd /var/www/html/admin_ecommerce_fork
```

2. **Instalar dependencias**
```bash
composer install
```

3. **Configurar base de datos**
Editar `.env`:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=admin_ecommerce
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

4. **Ejecutar migraciones**
```bash
php artisan migrate
```

5. **Crear usuario administrador**
```bash
php artisan make:filament-user
```

## 🚀 Uso

### Iniciar el servidor de desarrollo
```bash
php artisan serve
```

### Acceder al panel de administración
```
http://localhost/admin
```

**Credenciales por defecto:**
- Email: `admin@admin.com`
- Password: `admin123`

## 📊 Estructura de la Base de Datos

### Tablas Principales

#### Catálogo
- `categories` - Categorías de productos
- `brands` - Marcas
- `products` - Productos principales
- `product_variants` - Variantes de productos
- `product_images` - Imágenes
- `tags` - Etiquetas
- `product_tag` - Relación productos-etiquetas
- `inventories` - Control de inventario
- `discounts` - Descuentos

#### Usuarios
- `users` - Usuarios del sistema
- `roles` - Roles de usuario
- `permissions` - Permisos
- `role_user` - Relación roles-usuarios
- `addresses` - Direcciones
- `carts` - Carritos de compra
- `cart_items` - Items del carrito
- `wishlists` - Lista de deseos

#### Pedidos
- `orders` - Pedidos
- `order_items` - Items de pedidos
- `payments` - Pagos
- `shipments` - Envíos
- `invoices` - Facturas
- `refunds` - Reembolsos
- `taxes` - Impuestos

#### Administración
- `settings` - Configuraciones
- `coupons` - Cupones de descuento
- `newsletter_subscribers` - Suscriptores
- `banners` - Banners promocionales
- `reviews` - Reseñas de productos
- `logs` - Registros del sistema

## 🔐 Seguridad

- Sistema de roles y permisos
- Autenticación robusta con Filament
- Logs de auditoría
- Protección contra SQL Injection
- Validación de datos en todos los formularios

## 📱 API para Frontend

Este panel está diseñado para conectarse con un frontend a través de API. 

### Próximos pasos:
1. Implementar API REST con Laravel Sanctum
2. Configurar rutas API para cada módulo
3. Documentación con Swagger/OpenAPI

## 🤝 Contribución

Este proyecto está en desarrollo activo. 

## 📝 Licencia

[Especificar licencia]

## 👨‍💻 Autor

Desarrollado para administración de ecommerce moderno.

## 📞 Contacto

Para soporte o consultas sobre el proyecto.

---

**Versión**: 1.0.0  
**Fecha**: Octubre 2025  
**Framework**: Laravel 12  
**Panel Admin**: Filament 4
# admin_ecommerce_fork
