<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\JwtService;
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

        if($payload == null){
            return response()->json([
                'success'=>False,
                'message'=>'Inavlid Token'
            ],401);
        }

        $request->attributes->set('user',$payload);

        return $next($request);
    }
}
