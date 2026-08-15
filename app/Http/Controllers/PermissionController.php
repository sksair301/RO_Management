<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissions;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(){
        $permissions = Permissions::all();

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $permissions
        ], 200);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(),[
            'name'=> 'required|string|max:220|unique:permissions,name'
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $permission = Permissions::create([
            'name' => $data['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Created successfully',
            'data' => $permission
        ], 201);
    }

    public function show($id){
        $permission = Permissions::find($id);

        if(!$permission){
            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $permission
        ], 200);
    }

    public function update(Request $request, $id){

        $permission = Permissions::find($id);

        if(!$permission){
            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        $valid = Validator::make($request->all(),[
            'name'=> 'sometimes|string|max:220|unique:permissions,name,' . $id
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'error' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $permission->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated',
            'data' => $permission
        ], 200);
    }

    public function destroy($id){
        $permission = Permissions::find($id);

        if(!$permission){
            return response()->json([
                'success' => false,
                'message' => 'Permission not found'
            ], 404);
        }

        $permission->delete();

        return response()->json([
            'success' => true,
            'message' => 'Deleted successfully'
        ], 200);
    }
}
