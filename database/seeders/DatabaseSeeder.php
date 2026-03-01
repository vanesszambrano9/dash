<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create permissions
        $permissions = [
            'acceder-dashboard',
            'acceder-userlist_Iac',
            'acceder-administrar-roles',
            'acceder-permissions-list',
            'acceder-sessionlist_p6D',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin_general']);
        
        // Assign all permissions to admin role
        $adminRole->syncPermissions(Permission::all());

        // Create admin user
        $user = User::firstOrCreate(
            ['email' => 'admin@dash.com'],
            [
                'name' => 'Admin User',
                'password' => bcrypt('12345678'),
            ]
        );

        // Assign admin role to user
        if (!$user->hasRole('admin_general')) {
            $user->assignRole('admin_general');
        }
    }
}
