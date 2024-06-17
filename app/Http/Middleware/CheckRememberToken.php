<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class CheckRememberToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $rememberToken = $request->input('remember_token');

        if (!$rememberToken) {
            return response()->json(['error' => 'Token is required'], 400);
        }

        $user = DB::table('users')->where('remember_token', $rememberToken)->first();

        if (!$user) {
            return response()->json(['error' => 'Invalid Token, Please Login Again'], 401);
        }

        return $next($request);
    }
}
