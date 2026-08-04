<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Permissions;
use Illuminate\Support\Facades\Validator;

class PermissionController extends Controller
{
    public function index(){
        $permission = Permissions::all();

        return response()->json([
            'success'=>True,
            'messsage'=>'Successfully fetched',
            'data'=>$permission
        ],200);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(),[
            'name'=> 'required|string|max:220|unique:permissions,name'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>False,
                'message'=>'Validation error',
                'error'=>$valid->errors()
            ],422);
        }

        $data = $valid->Validated();

        $permission = Permissions::create([
            'name'=> $data['name']
        ]);

        $permission->save();

        return response()->json([
            'success'=>True,
            'message'=>'Created successfully',
            'data'=>$permission
        ],200);
    }

    public function update(Request $request, $id){

        $permission = Permission::find($id);

        $valid = Validated::make($request->all(),[
            'name'=> 'sometimes|string|max:220|unique:permissions,name'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>False,
                'message'=>'Validation error',
                'error'=> $valid->errors()
            ],422);
        }

        $data = $valid->Validated();

        $permission->update($data);

        return response()->json([
            'success'=>True,
            'message'=>'Successfully updated',
            'data'=>$permission
        ],200);
    }

    public function destroy($id){
        $permission = Permission::find($id);

        if(!$permission){
            return response()->json([
                'success'->False,
                'message'=>'Permission not found'
            ],404);
        }

        $permission->delete();

        return response()->json([
            'success'=>True,
            'message'=>'Deleted Successfully'
        ],404);
    }
}
