<?php

namespace Celebpost\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

use Carbon;

class RedirectIfAuthenticated
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->check()) {
          /* dipindah saat add akun
            $dt = Carbon::now();
            $user = Auth::user();
            $user->running_time = $dt->toDateTimeString();
            $user->is_started = 1;
            $user->save();
          */
            return redirect('/home');
        }

        return $next($request);
    }
}
