<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\checkUsersHelper;
use Closure;
use App\Helpers\Qs;
use Illuminate\Support\Facades\Auth;

class TeamSAT
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
        return (Auth::check() && checkUsersHelper::userIsTeamSAT()) ? $next($request) : redirect()->route('login');
    }
}
