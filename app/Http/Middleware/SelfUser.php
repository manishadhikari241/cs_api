<?php

namespace App\Http\Middleware;

use App\Constants\ErrorCodes;
use Closure;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class SelfUser
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
        if (!(Auth::guard('api')->check() && Auth::guard('api')->id() == $request->route()->parameter('id')))
            return respondError(ErrorCodes::UNAUTHORIZED, Response::HTTP_UNAUTHORIZED);

        return $next($request);
    }
}
