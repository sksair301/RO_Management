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

    public function permission(){
        return $this->belongsTo(Permissions::class, 'permissions_id');
    }

    public function role(){
        return $this->belongsTo(Roles::class, 'roles_id');
    }
}
