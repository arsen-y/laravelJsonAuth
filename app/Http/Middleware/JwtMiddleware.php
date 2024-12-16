<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

use Exception;
use Tymon\JWTAuth\Http\Middleware\BaseMiddleware;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;

class JwtMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
   public function handle($request, Closure $next)
   {
        try {
            $user = JWTAuth::parseToken()->authenticate();
        } catch (Exception $e) {
            if ($e instanceof TokenInvalidException){
                return response()->json(['error' => 'Токен недействителен'], 401);
            } else if ($e instanceof TokenExpiredException){
                return response()->json(['error' => 'Токен истек'], 401);
            } else {
                return response()->json(['error' => 'Авторизация отсутствует'], 401);
            }
        }
        return $next($request);
    }
}
