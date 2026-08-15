<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Services\JwtService;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request, JwtService $jwt){

        $valid = Validator::make($request->all(),[
            'email' => 'required|email',
            'password'=> 'required'
        ]);

        if($valid->fails()){
            return response()->json([
                'success'=>False,
                'message'=>'Validation Error',
                'error'=> $valid->errors()
            ],422);
        }

        $user = User::where('email', $request->email)->first();

        if(!$user){
            return response()->json([
                'success' => False,
                'message'=>'Invalid email or password'
            ],401);
        }

        if(!Hash::check($request->password, $user->password)){
            return response()->json([
                'success'=> false,
                'message' => 'Invalid email or password'
            ],401);
        }

        if ($user->status !== 'active') {
            return response()->json([
                'success' => false,
                'message' => 'Your account is inactive. Please contact the administrator.'
            ], 403);
        }

        $user->load(['roles', 'departments']);
        $token = $jwt->generateToken($user);

        return response()->json([
            'success'=>true,
            'message'=> 'Login Successfully',
            'token'=>$token,
            'data'=>[
                'id'=>$user->id,
                'username'=>$user->username,
                'email'=>$user->email,
                'roles_id'=>$user->roles_id,
                'roles_name'=>$user->roles?->name,
                'departments_id'=>$user->departments_id,
                'departments_name'=>$user->departments?->name,
            ]
        ]);

    }

    public function profile(Request $request, JwtService $jwt){

        $token = $request->bearerToken();

        if(!$token){
            return response()->json([
                'success'=> false,
                'message'=>'Invalid token'
            ],401);
        }

        $payload =$jwt->verifyToken($token);

        if(!$payload){
            return response()->json([
                'success'=> false,
                'message'=>'Invalid Token'
            ],401);
        }

        $user = User::with(['roles', 'departments'])->find($payload['id']);

        if(!$user){
            return response()->json([
                'success'=> false,
                'message'=>'User not found'
            ],404);
        }

        return response()->json([
            'success'=>true,
            'message'=>'Profile fetched Successfully',
            'data'=>$user
        ],200);

    }

    public function refresh(Request $request, JwtService $jwt){
        $token = $request->bearerToken();

        if(!$token){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid Token'
            ],401);
        }

        $payload = $jwt->verifyToken($token);

        if(!$payload){
            return response()->json([
                'success'=>false,
                'message'=>'Invalid Token'
            ],401);
        }

        $user = User::find($payload['id']);

        if(!$user){
            return response()->json([
                'success'=>False,
                'message'=>'User not found'
            ],404);
        }

        $newToken = $jwt->generateToken($user);

        return response()->json([
            'success'=>True,
            'message'=>'Refresh token',
            'token'=>$newToken
        ],200);
    }

    public function logout(Request $request){
        return response()->json([
            'success'=>True,
            'message'=>'Successfully logout'
        ],200);
    }
}
