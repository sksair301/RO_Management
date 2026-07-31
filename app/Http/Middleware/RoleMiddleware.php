<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, $roles, $department=null): Response
    {
        $user = $request->attributes->get('user');

        if(!$user){
            return response()->json([
                'success'=>FALSE,
                'message'=>'Unauthorized'
            ],401);
        }

        if($user['roles_id'] != $roles){
            return response()->json([
                'success'=>False,
                'message'=>'Access Denied'
            ],403);
        }

        if($department && $user['departments_id'] != $department){
            return response()->json([
                'success'=>False,
                'message'=>'Access Denied'
            ],403);
        }
        return $next($request);
    }
}
