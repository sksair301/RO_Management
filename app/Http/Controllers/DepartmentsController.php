<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Validator;
use App\Models\Departments;
use Illuminate\Http\Request;

class DepartmentsController extends Controller
{
    public function index(){
        $departments = Departments::latest()->get();

        return response()->json([
            'success' => TRUE,
            'message' => 'Successfully fetched',
            'data' => $departments
        ]);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(),[
            'name' => 'required|string|max:20'
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => FALSE,
                'message' => 'Validation Error',
                'error' => $valid->errors()
            ],422);
        }

        $data = $valid->Validated();

        $departments = Departments::create([
            'name' => $data['name']
        ]);

        return response()->json([
            'success' => TRUE,
            'message' => 'Successfully created',
            'data' => $departments
        ],201);
    }
}
