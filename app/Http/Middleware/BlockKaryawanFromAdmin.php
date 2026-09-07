<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BlockKaryawanFromAdmin
{
    public function handle(Request $request, Closure $next)
    {
        // Karyawan yang login lewat PIN (absen_karyawan) TANPA login admin
        // dilarang mengakses halaman admin/dashboard.
        if ($request->session()->has('absen_karyawan.id') && ! Auth::check()) {
            return redirect()->route('absen.index')->with('error', 'Halaman hanya untuk admin.');
        }

        return $next($request);
    }
}
