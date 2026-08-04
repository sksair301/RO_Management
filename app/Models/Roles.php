<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $fillable =[
        'name'
    ];

    public function user(){

        return $this->hasMany(User::class);
    }

    public function permissions(){

        return $this->belongsToMany(Permissions::class, 'role_permissions', 'roles_id', 'permissions_id')->withTimestamps();
    }
}
