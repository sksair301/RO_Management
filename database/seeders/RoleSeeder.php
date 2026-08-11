<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'admin',
            'lead',
            'user'
        ];

        foreach ($roles as $roles){

            Roles::firstOrCreate([
                'name'=> $roles
            ]);
        }
    }
}
