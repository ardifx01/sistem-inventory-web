<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
        public function store(LoginRequest $request): RedirectResponse
    {
        // Ambil user berdasarkan email
        $user = \App\Models\User::where('email', $request->email)->first();

        // Cek apakah user ada
        if (!$user) {
            return back()->withErrors([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Cek status akun
        if ($user->status === 'inactive') {
            return back()->withErrors([
                'email' => 'Akun Anda sedang nonaktif. Silakan hubungi Super Admin.',
            ]);
        }

        // Proses autentikasi (pakai method bawaan Breeze)
        $request->authenticate();
        $request->session()->regenerate();

        // Redirect sesuai role
        if ($user->role === 'superadmin') {
            return redirect()->route('dashboard');
        } elseif ($user->role === 'admin') {
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('dashboard');
        }
    }


    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }

        
}

