<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;

class RolesController extends Controller
{
    public static function middleware():array{
        return[
            new Middleware('permissions:view-roles',only:['index']),
            new Middleware('permission:create-permission',only:['store'])
        ];

    }
    public function index(){

        $roles = Roles::latest()->get();

        return response()->json([
            'success' => True,
            'message' => 'Successfully fetched',
            'data' => $roles
        ]);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(),[
            'name'=> 'required|string|max:30'
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => False,
                'message' => 'Validation Error',
                'error' => $valid->errors()
            ],422);
        }

        $data = $valid->Validated();

        $roles = Roles::create([
            'name' => $data['name']
        ]);

        return response()->json([
            'success' => TRUE,
            'message' => 'Created Successfully',
            'data' => $roles
        ],201);
    }
}
