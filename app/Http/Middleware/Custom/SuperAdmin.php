<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\GetUserTypeHelper;
use Closure;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class SuperAdmin
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
        return (Auth::check() && GetUserTypeHelper::userIsSuperAdmin()) ? $next($request) : redirect()->route('login');
    }
}
