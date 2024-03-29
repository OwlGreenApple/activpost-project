<?php

namespace Celebpost\Http\Middleware;

use Closure;

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
      if (auth()->check() && ($request->user()->is_admin==1)){
        return $next($request);
      }

      return redirect()->guest('/');
    }
}
