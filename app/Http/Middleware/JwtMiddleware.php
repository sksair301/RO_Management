<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JwtService;
use App\Models\User;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        $token = $request->bearerToken();

        if(!$token){
            return response()->json([
                'success'=>False,
                'message'=>'Inavlid Token'
            ],401);
        }

        $jwt = New JwtService();

        $payload = $jwt->verifyToken($token);

        if($payload === null){
            return response()->json([
                'success'=>False,
                'message'=>'Inavlid Token'
            ],401);
        }

        $user = User::find($payload['id']);

        if(!$user){
            return response()->json([
                'success'=> False,
                'message'=>'User not found'
            ],404);
        }

        $request->attributes->set('user',$user);

        return $next($request);
    }
}
