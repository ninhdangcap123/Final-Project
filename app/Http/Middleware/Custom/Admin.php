<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\getUserTypeHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Qs;

class Admin
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
        return (Auth::check() && getUserTypeHelper::userIsAdmin()) ? $next($request) : redirect()->route('login');
    }
}
