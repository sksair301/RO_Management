<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class DepartmentMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$departments): Response
    {
        $user = $request->attributes->get('user');

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 401);
        }

        $userRole = strtolower($user->roles?->name ?? '');

        // Admin role bypass
        if ($userRole === 'admin') {
            return $next($request);
        }

        $userDeptName = strtolower($user->departments?->name ?? '');
        $userDeptId = (string) $user->departments_id;

        $allowedDepts = array_map('strtolower', $departments);

        $hasAccess = in_array($userDeptName, $allowedDepts) || in_array($userDeptId, $allowedDepts);

        if (!$hasAccess) {
            return response()->json([
                'success' => false,
                'message' => 'Department Access Denied'
            ], 403);
        }

        return $next($request);
    }
}
