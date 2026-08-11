<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
            DepartmentSeeder::class,
            PermissionSeeder::class,
            RolePermissionSeeder::class,
        ]);

        User::create([
            'username' => 'admin',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('password'),
            'first_name' => 'Admin',
            'last_name' => 'Admin',
            'roles_id' => 1,
            'departments_id' => 1,
            'status' => 'active',
        ]);
    }
}
