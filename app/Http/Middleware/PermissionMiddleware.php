<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\Permissions;
use App\Models\RolePermissions;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$permissions): Response
    {
        $user = $request->attributes->get('user');

        if(!$user){
            return response()->json([
                'success'=>false,
                'message'=>'Unauthorized'
            ],401);
        }

        // Admin role bypass
        if (strtolower($user->roles?->name ?? '') === 'admin') {
            return $next($request);
        }

        $permissionsIds = Permissions::whereIn('name',$permissions)->pluck('id');

        $hasPermission = RolePermissions::where('roles_id',$user->roles_id)
         ->whereIn('permissions_id',$permissionsIds)->exists();

        if(!$hasPermission){
            return response()->json([
                'success'=>false,
                'message'=>'Permission Denied'
            ],403);
        }

        return $next($request);
    }
}
