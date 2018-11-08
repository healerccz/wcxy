<?php

namespace App\Http\Middleware;

use Closure;

class CheckLogin
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
        if (!($request->session()->has('openid') &&
            $request->session()->has('userId') &&
            $request->session()->has('permission'))) {
            return response()->json([
                'code' => 4000,
                'msg' => '没有登录'
            ]);
        }

        return $next($request);
    }
}
