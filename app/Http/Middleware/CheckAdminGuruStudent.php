<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckAdminGuruStudent
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
        // Cek apakah pengguna memiliki salah satu dari role yang diizinkan
        if (Auth::check() && (
            Auth::user()->role === 'admin' ||
            Auth::user()->role === 'guru' ||
            Auth::user()->role === 'student'
        )) {
            return $next($request);
        }

        // Jika tidak memiliki role yang diizinkan, redirect atau tampilkan error
        return redirect('/unauthorized'); // atau bisa menggunakan abort(403);
    }
}
