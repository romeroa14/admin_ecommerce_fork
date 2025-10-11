<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Limpiar datos existentes
        DB::table('role_user')->delete();
        User::query()->delete();
        Role::query()->delete();

        // Crear roles primero
        $adminRole = Role::create([
            'name' => 'Administrador',
            'slug' => 'admin',
            'description' => 'Acceso total al sistema',
            'is_active' => true,
        ]);

        $vendedorRole = Role::create([
            'name' => 'Vendedor',
            'slug' => 'vendedor',
            'description' => 'Puede gestionar productos y pedidos',
            'is_active' => true,
        ]);

        $clienteRole = Role::create([
            'name' => 'Cliente',
            'slug' => 'cliente',
            'description' => 'Usuario cliente del ecommerce',
            'is_active' => true,
        ]);

        // Usuario Administrador Principal
        $admin = User::create([
            'name' => 'Administrador',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'email_verified_at' => now(),
        ]);
        $admin->roles()->attach($adminRole->id);

        // Usuario Vendedor
        $vendedor = User::create([
            'name' => 'Juan Vendedor',
            'email' => 'vendedor@ecommerce.com',
            'password' => Hash::make('vendedor123'),
            'email_verified_at' => now(),
        ]);
        $vendedor->roles()->attach($vendedorRole->id);

        // Usuario Cliente 1
        $cliente1 = User::create([
            'name' => 'María García',
            'email' => 'maria@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now(),
        ]);
        $cliente1->roles()->attach($clienteRole->id);

        // Usuario Cliente 2
        $cliente2 = User::create([
            'name' => 'Carlos López',
            'email' => 'carlos@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now(),
        ]);
        $cliente2->roles()->attach($clienteRole->id);

        // Usuario Cliente 3
        $cliente3 = User::create([
            'name' => 'Ana Martínez',
            'email' => 'ana@example.com',
            'password' => Hash::make('cliente123'),
            'email_verified_at' => now(),
        ]);
        $cliente3->roles()->attach($clienteRole->id);

        $this->command->info('✅ Usuarios creados exitosamente!');
        $this->command->info('📧 Admin: admin@admin.com / admin123');
        $this->command->info('📧 Vendedor: vendedor@ecommerce.com / vendedor123');
        $this->command->info('📧 Clientes: maria@example.com, carlos@example.com, ana@example.com / cliente123');
    }
}
