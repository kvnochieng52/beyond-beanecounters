<?php

namespace App\Http\Middleware;

use App\Exceptions\UserInActiveException;
use Closure;
use Illuminate\Support\Facades\Auth;

class UserIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (Auth::user()->is_active != 1) {
            throw new UserInActiveException(403, "User is not activated. Please Contact Admin");
        }
        return $next($request);
    }
}
