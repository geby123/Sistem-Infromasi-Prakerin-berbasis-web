<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckRoles
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah login dan memiliki salah satu dari 4 role
        if (Auth::check() && (
            Auth::user()->role === 'admin' ||
            Auth::user()->role === 'student' ||
            Auth::user()->role === 'humas' ||
            Auth::user()->role === 'guru'
        )) {
            return $next($request);
        }

        // Jika pengguna tidak memiliki peran yang diizinkan, redirect atau tampilkan error
        return redirect('/403'); // atau gunakan abort(403)
    }
}
