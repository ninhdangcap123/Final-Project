<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\GetUserTypeHelper;
use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Closure $next
     * @return mixed
     */
    public function handle(\Illuminate\Http\Request $request, Closure $next)
    {
        return ( Auth::check() && GetUserTypeHelper::userIsAdmin() ) ? $next($request) : redirect()->route('login');
    }
}
