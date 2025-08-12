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
        // Tentukan apakah input login berupa email atau username
        $loginType = filter_var($request->login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        // Ambil user berdasarkan email atau username
        $user = \App\Models\User::where($loginType, $request->login)->first();

        // Cek apakah user ada
        if (!$user) {
            return back()->withErrors([
                'login' => 'Email/Username atau password salah.',
            ]);
        }

        // Cek status akun
        if ($user->status === 'inactive') {
            return back()->withErrors([
                'login' => 'Akun Anda sedang nonaktif. Silakan hubungi Super Admin.',
            ]);
        }

        // Proses autentikasi
        $request->authenticate();
        $request->session()->regenerate();

        // Redirect sesuai role
        return redirect()->route('dashboard');
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

