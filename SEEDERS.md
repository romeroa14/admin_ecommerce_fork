# 🌱 Seeders del Proyecto

## UserSeeder

Este seeder crea usuarios de ejemplo con diferentes roles para el sistema.

### 📋 Usuarios Creados

#### 👑 Administrador
- **Email**: `admin@admin.com`
- **Password**: `admin123`
- **Rol**: Administrador
- **Permisos**: Acceso total al sistema

#### 👨‍💼 Vendedor
- **Email**: `vendedor@ecommerce.com`
- **Password**: `vendedor123`
- **Rol**: Vendedor
- **Permisos**: Puede gestionar productos y pedidos

#### 👥 Clientes

**Cliente 1:**
- **Nombre**: María García
- **Email**: `maria@example.com`
- **Password**: `cliente123`
- **Rol**: Cliente

**Cliente 2:**
- **Nombre**: Carlos López
- **Email**: `carlos@example.com`
- **Password**: `cliente123`
- **Rol**: Cliente

**Cliente 3:**
- **Nombre**: Ana Martínez
- **Email**: `ana@example.com`
- **Password**: `cliente123`
- **Rol**: Cliente

### 🎭 Roles Creados

1. **Administrador** (`admin`)
   - Acceso total al sistema
   - Puede gestionar usuarios, productos, pedidos, configuraciones

2. **Vendedor** (`vendedor`)
   - Puede gestionar productos y pedidos
   - Acceso limitado a configuraciones

3. **Cliente** (`cliente`)
   - Usuario cliente del ecommerce
   - Puede realizar compras, ver pedidos, gestionar perfil

## 🚀 Cómo Ejecutar el Seeder

### Ejecutar solo UserSeeder
```bash
php artisan db:seed --class=UserSeeder
```

### Ejecutar todos los seeders
```bash
php artisan db:seed
```

### Resetear base de datos y ejecutar seeders
```bash
php artisan migrate:fresh --seed
```

## 📝 Notas

- El seeder **limpia automáticamente** los usuarios y roles existentes antes de crear nuevos
- Todos los usuarios tienen `email_verified_at` ya configurado (no necesitan verificar email)
- Las contraseñas están hasheadas con bcrypt
- Los usuarios están relacionados con sus respectivos roles mediante la tabla pivot `role_user`

## 🔐 Seguridad

⚠️ **IMPORTANTE**: Estos son datos de prueba. En producción:
- Cambiar todas las contraseñas
- Usar contraseñas seguras
- Eliminar usuarios de ejemplo
- Configurar verificación de email si es necesario

## 🎨 Personalización

Para agregar más usuarios, edita el archivo:
```
database/seeders/UserSeeder.php
```

Ejemplo de agregar un nuevo usuario:
```php
$nuevoUsuario = User::create([
    'name' => 'Nuevo Usuario',
    'email' => 'nuevo@example.com',
    'password' => Hash::make('password123'),
    'email_verified_at' => now(),
]);
$nuevoUsuario->roles()->attach($clienteRole->id);
```

## 📚 Próximos Seeders Recomendados

1. **CategorySeeder** - Categorías de productos
2. **BrandSeeder** - Marcas
3. **ProductSeeder** - Productos de ejemplo
4. **CouponSeeder** - Cupones de descuento
5. **SettingSeeder** - Configuraciones del sistema

---

**Última actualización**: Octubre 2025

