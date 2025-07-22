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
        // if (Auth::user()->is_active != 1) {
        //     throw new UserInActiveException(403, "User is not activated. Please Contact Admin");
        // }
        // return $next($request);

        if (Auth::user()->is_active != 1) {
            $logoutUrl = route('logout');
            $message = "User is not activated. Please Contact Admin. <a href='{$logoutUrl}' onclick='event.preventDefault(); document.getElementById(\"logout-form\").submit();'>Logout</a>";

            throw new UserInActiveException(403, $message);
        }
        return $next($request);
    }
}
