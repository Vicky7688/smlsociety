<?php

namespace App\Http\Middleware;

use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;


class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Attempt to authenticate using the JWT token
        try {
            $user = JWTAuth::parseToken()->authenticate();

            // Attach user information to the request (if needed in later parts of the app)
            $request->attributes->set('user', $user);
        } catch (JWTException $e) {
            // Check specific error types
            if ($e instanceof \Tymon\JWTAuth\Exceptions\TokenExpiredException) {
                return response()->json(['error' => 'Token has expired'], 401);
            } elseif ($e instanceof \Tymon\JWTAuth\Exceptions\TokenInvalidException) {
                return response()->json(['error' => 'Token is invalid'], 401);
            } else {
                return response()->json(['error' => 'Token not provided'], 401);
            }
        }

        return $next($request);
    }
}

