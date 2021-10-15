<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\getUserTypeHelper;
use Closure;
use Illuminate\Support\Facades\Auth;
use App\Helpers\Qs;

class Student
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
        return (Auth::check() && getUserTypeHelper::userIsStudent()) ? $next($request) : redirect()->route('login');
    }

}
