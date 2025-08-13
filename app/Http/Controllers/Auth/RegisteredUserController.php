<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register-superadmin');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];

        // Kalau admin bisa set role & status
        if (auth()->check() && in_array(auth()->user()->role, ['admin', 'superadmin'])) {
            $rules['role'] = ['required', 'in:user,admin,superadmin'];
            $rules['status'] = ['required', 'in:active,inactive'];
        }

        $validated = $request->validate($rules);

        $data = [
            'name' => $validated['name'],
            'username' => $validated['username'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ];

        if (isset($validated['role'])) {
            $data['role'] = $validated['role'];
            $data['status'] = $validated['status'];
        }

        $user = User::create($data);
        event(new Registered($user));

        // Kalau admin yang bikin akun, jangan login ke akun baru
        if (Auth::check() && in_array(Auth::user()->role, ['admin', 'superadmin'])) {
            return redirect()->route('kelola-akun')->with('message', 'Akun berhasil ditambahkan.');
        }
        
        // Kalau user register sendiri
        Auth::login($user);
        return redirect()->route('dashboard');
    }

}
