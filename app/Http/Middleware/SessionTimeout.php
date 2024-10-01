<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SessionTimeout
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle($request, Closure $next)
    {
        // Memeriksa apakah session ada
        if (Session::has('lastActivity')) {
            // Periksa waktu terakhir aktivitas
            $lastActivity = Session::get('lastActivity');

            // Jika sudah lebih dari 30 menit
            if (now()->diffInMinutes($lastActivity) > 30) {
                // Hapus session dan redirect sesuai kondisi
                Session::flush();

                // Cek apakah user sudah login
                if (Auth::check()) {
                    // Jika sudah login, redirect ke halaman login
                    return redirect()->route('');
                }

                // Jika tamu, redirect ke view cashier
                return redirect(route('GuestCashierView'));
            }
        }

        // Perbarui timestamp terakhir aktivitas
        Session::put('lastActivity', now());

        return $next($request);
    }
}
