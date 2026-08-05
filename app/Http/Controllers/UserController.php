<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;

class UserController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('permissions:view-user', only: ['index', 'show']),
            new Middleware('permissions:create-user', only: ['store']),
            new Middleware('permissions:edit-user', only: ['update']),
            new Middleware('permissions:delete-user', only: ['destroy']),
        ];
    }
    public function index(Request $request){
        $query = User::with('departments','roles','primaryLead','secondaryLead');

        $query->orderBy('created_at');

        $user = $query->paginate();

        return response()->json([
            'success'=>True,
            'message'=>'Successfully fetched',
            'data'=>$user
        ],200);
    }

    public function store(Request $request){

        $valid = Validator::make($request->all(),[
            'username' => 'required|string|max:50',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|max:10',
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'roles_id' => 'required|exists:roles,id',
            'departments_id' => 'required|exists:departments,id',
            'primary_lead_id' => 'nullable|exists:users,id',
            'secondary_lead_id' => 'nullable|exists:users,id|different:primary_lead_id',
            'status' => 'nullable|string'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>False,
                'message'=>'Validation Error',
                'error'=> $valid->errors()
            ]);
        }

        $validated = $valid->Validated();

        $plainPassword = $validated['password'];

        $user = User::create([
            'username' =>$validated['username'],
            'email' => $validated['email'],
            'password' => bcrypt($validated['password']),
            'first_name' => $validated['first_name'],
            'last_name' => $validated['last_name'],
            'roles_id' => $validated['roles_id'],
            'departments_id' => $validated['departments_id'],
            'primary_lead_id' => $validated['primary_lead_id'] ?? null,
            'secondary_lead_id' => $validated['secondary_lead_id'] ?? null,
            'status' => $validated['status'] ?? 'active'
        ]);

        Mail::to($user->email)->send(new UserCreatedMail($user, $plainPassword));

        return response()->json([
            'success'=>True,
            'message'=>'Successfully created',
            'data' => $user
        ],201);
    }

    public function show($id){

        $user = User::with('roles','departments','primaryLead','secondaryLead')->find($id);

        if(!$user){
            return response()->json([
                'success'=>False,
                'message'=>'Not found'
            ],404);
        }

        return response()->json([
            'success'=>True,
            'message'=>'Successfully fetched',
            'data'=> $user
        ],200);
    }

    public function update(Request $request, $id){

        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found'
            ], 404);
        }

        $valid = Validator::make($request->all(),[
            'username' => 'sometimes|string|max:50',
            'email' => 'sometimes|email|unique:users,email,' .$id,
            'password' => 'sometimes|string|max:10',
            'first_name' => 'sometimes|string|max:50',
            'last_name' => 'sometimes|string|max:50',
            'roles_id' => 'sometimes|exists:roles,id',
            'departments_id' => 'sometimes|exists:departments,id',
            'primary_lead_id' => 'sometimes|nullable|exists:users,id',
            'secondary_lead_id' => 'sometimes|nullable|exists:users,id|different:primary_lead_id',
            'status' => 'sometimes|in:active,inactive'
        ]);

        if($valid->fails()){
            return response()->json([
                'success' => False,
                'message' => 'Validation Error',
                'error' => $valid->errors()
            ],422);
        }

        $data = $valid->Validated();

        if (
            isset($data['primary_lead_id']) &&
            $data['primary_lead_id'] == $user->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'User cannot be their own primary lead.'
            ], 422);
        }

        if (
            isset($data['secondary_lead_id']) &&
            $data['secondary_lead_id'] == $user->id
        ) {
            return response()->json([
                'success' => false,
                'message' => 'User cannot be their own secondary lead.'
            ], 422);
        }

        if(isset($data['password'])){
            $data['password'] = bcrypt($data['password']);
        }

        $user->update($data);

        return response()->json([
            'success' => True,
            'message' => 'Data updated successfully',
            'data' => $user->fresh()
        ],200);
    }

    public function destroy($id){

        $user = User::find($id);

        if(!$user){
            return response()->json([
                'success'=>False,
                'message'=>'user not found'
            ],404);
        }

        $user->delete();

        return response()->json([
            'success'=>True,
            'message'=>'User deleted successfully'
        ],200);
    }
}
