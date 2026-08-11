<?php

namespace App\Http\Controllers;

use App\Models\Roles;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RolesController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permission:view-roles', only: ['index']),
            new Middleware('permission:create-role', only: ['store']),
            new Middleware('permission:update-role', only: ['update']),
            new Middleware('permission:delete-role', only: ['destroy']),
        ];
    }

    public function index()
    {
        return response()->json([
            'success' => true,
            'message' => 'Roles fetched successfully',
            'data' => Roles::with('permissions')->latest()->get()
        ]);
    }

    public function show($id)
    {
        $role = Roles::with('permissions')->find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Role fetched successfully',
            'data' => $role
        ]);
    }

    public function store(Request $request)
    {
        $valid = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name',
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $role = Roles::create([
            'name' => $data['name']
        ]);

        $role->permissions()->sync($data['permissions']);
        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Role created successfully',
            'data' => $role
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $role = Roles::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        $valid = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'required|array',
            'permissions.*' => 'exists:permissions,id'
        ]);

        if ($valid->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'errors' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $role->update([
            'name' => $data['name']
        ]);

        $role->permissions()->sync($data['permissions']);
        $role->load('permissions');

        return response()->json([
            'success' => true,
            'message' => 'Role updated successfully',
            'data' => $role
        ]);
    }

    public function destroy($id)
    {
        $role = Roles::find($id);

        if (!$role) {
            return response()->json([
                'success' => false,
                'message' => 'Role not found'
            ], 404);
        }

        $role->delete();

        return response()->json([
            'success' => true,
            'message' => 'Role deleted successfully'
        ]);
    }
}
