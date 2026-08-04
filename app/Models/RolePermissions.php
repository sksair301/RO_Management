<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RolePermissions extends Model
{
    protected $fillable = [
        'role_permissions',
        'roles_id',
        'permissions_id'
    ];

    public function permissions(){

        return $this->belongsTo(Permission::class, 'role_permissions', 'roles_id', 'permissions_id');
    }
}
