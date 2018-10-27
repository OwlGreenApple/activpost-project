<?php

namespace Celebpost\Http\Middleware;

use Closure;

class APIMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    private $apikey = '8ILryMCw08WxwgXOP1MUwKXBjQ7wiE28JSWuYVfC';

    public function handle($request, Closure $next)
    {

      /*$apikey = AuthAPI::where('secret',$request->apikey)->first();

        if(!is_null($apikey)){
          return $next($request);
        }*/

        if($request->apikey == $this->apikey){
          return $next($request);
        }

        return redirect()->guest('/');
    }
}
