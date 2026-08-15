<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Departments;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            'Accounts',
            'HR',
            'Tech',
            'Marketing',
            'Sales'
        ];

        foreach ($departments as $department){

            Departments::firstOrCreate([
                'name' => $department
            ]);
        }
    }
}
