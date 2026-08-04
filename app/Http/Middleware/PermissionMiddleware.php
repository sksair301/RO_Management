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
                'success'=>False,
                'message'=>'Unauthorized'
            ],401);
        }

        $permissionsIds = Permissions::whereIn('name',$permissions)->pluck('id');

        $hasPermisssion = RolePermissions::where('roles_id',$user['roles_id'])
         ->whereIn('permissions_id',$permissionsIds)->exists();

        if(!$hasPermisssion){
            return response()->json([
                'success'=>False,
                'message'=>'Permission Denied'
            ],403);
        }

        return $next($request);
    }
}
