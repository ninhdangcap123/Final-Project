<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\GetUserTypeHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Qs;

class MyParent
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        return (Auth::check() && GetUserTypeHelper::userIsParent()) ? $next($request) : redirect()->route('login');
    }
}
