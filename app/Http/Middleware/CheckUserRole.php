<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string  $role
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $role)
    {
        $user = $request->user();

        if ($role === 'superAdmin' && $user->id != 1) {
            return response(['message' => 'Unauthorized'], 403);
        }

        if ($role === 'admin' && $user->id != 1 && !in_array($user->id, [2, 3])) {
            return response(['message' => 'Unauthorized'], 403);
        }

        if ($role === 'member' && ($user->id == 1 || in_array($user->id, [2, 3]))) {
            return response(['message' => 'Unauthorized'], 403);
        }

        return $next($request);
    }
}
