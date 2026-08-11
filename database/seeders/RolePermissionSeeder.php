<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Roles;
use App\Models\Permissions;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = Roles::where('name', 'admin')->first();
        $lead = Roles::where('name', 'lead')->first();
        $user = Roles::where('name', 'user')->first();

        if($admin){
            $admin->permissions()->sync(Permissions::pluck('id')->toArray());
        }

        if($lead){
            $permissions = Permissions::whereIn('name', [

                // Vendors
                'view-vendor',
                'create-vendor',
                'edit-vendor',
                'delete-vendor',

                // RO Forms
                'view-ro-form',
                'create-ro-form',
                'edit-ro-form',
                'delete-ro-form',

                // Accounts
                'view-accounts',
                'manage-accounts',
            ])->pluck('id')->toArray();

            $lead->permissions()->sync($permissions);
        }

        if($user){
            $permissions = Permissions::whereIn('name',[

                'view-vendor',
                'create-vendor',
                'edit-vendor',

                // RO Forms
                'view-ro-form',
                'create-ro-form',
                'edit-ro-form',

                // Accounts
                'view-accounts',
                'manage-accounts',
            ])->pluck('id')->toArray();

            $user->permissions()->sync($permissions);
        }
    }
}
