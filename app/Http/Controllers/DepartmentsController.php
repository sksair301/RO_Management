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
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $departments
        ], 200);
    }

    public function store(Request $request){
        $valid = Validator::make($request->all(),[
            'name' => 'required|string|max:50|unique:departments,name'
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'error' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $department = Departments::create([
            'name' => $data['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully created',
            'data' => $department
        ], 201);
    }

    public function show($id){
        $department = Departments::find($id);

        if(!$department){
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Successfully fetched',
            'data' => $department
        ], 200);
    }

    public function update(Request $request, $id){
        $department = Departments::find($id);

        if(!$department){
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $valid = Validator::make($request->all(),[
            'name' => 'required|string|max:50|unique:departments,name,' . $id
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => false,
                'message' => 'Validation Error',
                'error' => $valid->errors()
            ], 422);
        }

        $data = $valid->validated();

        $department->update([
            'name' => $data['name']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Successfully updated',
            'data' => $department
        ], 200);
    }

    public function destroy($id){
        $department = Departments::find($id);

        if(!$department){
            return response()->json([
                'success' => false,
                'message' => 'Department not found'
            ], 404);
        }

        $department->delete();

        return response()->json([
            'success' => true,
            'message' => 'Department deleted successfully'
        ], 200);
    }
}
