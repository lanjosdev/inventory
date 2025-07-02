<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionRoleSeeder extends Seeder
{
    public function run()
    {
        // Tabelas para CRUD
        $tables = ['users', 'companies', 'contact_companies', 'sectors'];
        $actions = ['create', 'read', 'update', 'delete'];
        $permissions = [];

        // Cria permissões para cada tabela
        foreach ($tables as $table) {
            foreach ($actions as $action) {
                $permissions[] = Permission::firstOrCreate([
                    'name' => $action . ' ' . $table
                ]);
            }
        }

        // Cria roles
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $user = Role::firstOrCreate(['name' => 'user']);

        // Admin: todas permissões
        $admin->syncPermissions(Permission::all());

        // Manager: CRUD em companies, contact_companies, sectors
        $manager->syncPermissions(Permission::whereIn('name', [
            'create companies',
            'read companies',
            'update companies',
            'delete companies',
            'create contact_companies',
            'read contact_companies',
            'update contact_companies',
            'delete contact_companies',
            'create sectors',
            'read sectors',
            'update sectors',
            'delete sectors',
        ])->get());

        // User: apenas read em todas
        $user->syncPermissions(Permission::whereIn('name', [
            'read users',
            'read companies',
            'read contact_companies',
            'read sectors',
        ])->get());

        // Atribui admin ao usuário admin padrão
        $adminUser = User::where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            $adminUser->assignRole('admin');
        }
    }
}
