<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Roles;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
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
