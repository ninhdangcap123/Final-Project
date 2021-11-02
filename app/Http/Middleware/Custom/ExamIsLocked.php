<?php

namespace App\Http\Middleware\Custom;

use App\Helpers\CheckExamInfoHelper;
use App\Helpers\Mk;
use Closure;
use Illuminate\Support\Facades\Auth;

class ExamIsLocked
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
        return (CheckExamInfoHelper::examIsLocked()) ? $next($request) : redirect()->route('dashboard');
    }
}
