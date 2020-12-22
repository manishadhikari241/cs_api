<?php

namespace App\Http\Middleware;

use App\Constants\ErrorCodes;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class IsAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if ( !(Auth::guard('api')->check() && Auth::guard('api')->user()->isAdmin()) ) {
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
