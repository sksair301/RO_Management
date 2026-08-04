<?php

namespace App\Http\Controllers;
use App\Models\Roles;
use Illuminate\Http\Request;

class RolePermissionController extends Controller
{
    public function assignPermission(Request $request, $roleId){

        $roles = Roles::find($roleId);

        if(!$roles){
            return response()->json([
                'success'=>False,
                'message'=>'Roles not find'
            ],404);
        }

        $request->validate([
            'permissions'=> 'required|array',
            'permissions.*'=>'exists:permissions,id'
        ]);

        $roles->permissions()->sync($request->permissions);

        $roles->load('permissions');

        return response()->json([
            'success'=>True,
            'message'=>'Permission assigned successfully',
            'data'=>[
                'roles_id'=>$roles->id,
                'roles_name'=>$roles->name,
                'permissions'=>$roles->permissions
            ]
        ],200);

    }

    public function removePermission(Request $request, $rolesId){

        $roles = Roles::find($rolesId);

        if(!$roles){
            return response()->json([
                'success'=>False,
                'message'=>'Roles not found'
            ],404);
        }

        $request->validate([
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        $roles->permissions()->detach($request->permissions);

        $roles->load('permissions');

        return response()->json([
            'success'=>True,
            'message'=>'Permission assigned successfully',
            'data'=>[
                'roles_id'=>$roles->id,
                'roles_name'=>$roles->name,
                'permissions'=>$roles->permissions
            ]
        ],200);
    }
}
