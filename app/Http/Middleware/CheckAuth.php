<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckAuth
{
    public function handle($request, Closure $next, ...$guards)
    {
        if (Auth::check()) return $next($request);
        else return redirect('/');
    }
}
