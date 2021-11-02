<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\CheckUsersHelper;
use Closure;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class TeamSA
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
        return (Auth::check() && CheckUsersHelper::userIsTeamSA()) ? $next($request) : redirect()->route('login');
    }
}
