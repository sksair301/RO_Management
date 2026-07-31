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
                'sucess'=> False,
                'message' => 'Invalid emial or password'
            ],401);
        }

        $token = $jwt->generateToken($user);

        return response()->json([
            'success'=>True,
            'message'=> 'Login Successfully',
            'token'=>$token,
            'data'=>[
                'id'=>$user->id,
                'username'=>$user->username,
                'email'=>$user->email,
                'password'=>$user->password,
                'roles_id'=>$user->roles_id,
                'departments_id'=>$user->departments_id

            ]
        ]);

    }
}
