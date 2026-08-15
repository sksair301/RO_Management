<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Permissions extends Model
{
    protected $fillable = [
        'name',
    ];

    public function roles(){

        return $this->belongsToMany(Roles::class, 'role_permissions', 'roles_id', 'permissions_id')->withTimestamps();
    }
}
