<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CheckUser
{
    public function handle(Request $request, Closure $next)
    {
        // Cek apakah pengguna sudah terautentikasi
        if (Auth::check()) {
            // Alihkan pengguna ke halaman lain jika sudah terautentikasi
            return redirect('/home'); // Ganti dengan rute yang sesuai
        }

        return $next($request);
    }
}

