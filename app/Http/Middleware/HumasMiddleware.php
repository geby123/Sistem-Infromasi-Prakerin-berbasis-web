<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HumasMiddleware
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
        // Asumsi bahwa ada field 'role' di tabel users yang menyimpan tipe pengguna (humas, guru, siswa, dll)
        if (Auth::check() && Auth::user()->role === 'humas') {
            return $next($request);
        }

        // Jika bukan Humas, arahkan ke halaman 403 (Forbidden)
        return response()->view('index', [], 403);
    }
}
