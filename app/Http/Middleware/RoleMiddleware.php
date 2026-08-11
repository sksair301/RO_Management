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
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->attributes->get('user');

        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'Unauthorized'
            ],401);
        }

        $userRoleName = strtolower($user->roles?->name ?? '');
        
        // Admin role bypass
        if ($userRoleName === 'admin') {
            return $next($request);
        }

        $allowedRoles = array_map('strtolower', $roles);

        $hasRole = in_array((string)$user->roles_id, $allowedRoles) || 
                   in_array($userRoleName, $allowedRoles);

        if(!$hasRole){
            return response()->json([
                'success'=>false,
                'message'=>'Access Denied'
            ],403);
        }
        return $next($request);
    }
}
