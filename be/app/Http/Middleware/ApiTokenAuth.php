<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;

class ApiTokenAuth
{
    public function handle(Request $request, Closure $next)
    {
        $token = $request->bearerToken() ?? $request->header('X-API-Token');

        if (!$token) {
            return response()->json(['message' => 'Thiếu token.'], 401);
        }

        $user = User::where('api_token', $token)->first();
        if (!$user) {
            return response()->json(['message' => 'Token không hợp lệ.'], 401);
        }

        $request->setUserResolver(fn () => $user);

        return $next($request);
    }
}
