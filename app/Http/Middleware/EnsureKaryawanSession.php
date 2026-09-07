<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureKaryawanSession
{
    public function handle(Request $request, Closure $next)
    {
        if (! $request->session()->has('absen_karyawan.id')) {
            return redirect()->route('absen.login')->with('error', 'Silakan login PIN dahulu.');
        }

        return $next($request);
    }
}
