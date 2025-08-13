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
use App\Models\ActivityLog;

class RegisteredUserController extends Controller
{
    /**
     * Form register default Laravel Breeze (untuk public).
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Form register khusus tambah akun super admin.
     */
    public function createForSuperAdmin(): View
    {
        return view('auth.register-superadmin');
    }

    /**
     * Proses register default Laravel Breeze.
     */

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,superadmin',
            'status' => 'required|in:active,inactive',
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        // Simpan log aktivitas
        ActivityLog::create([
            'user_id' => auth()->id(),
            'activity' => 'Menambahkan akun ' . $request->name,
            'user_agent' => $request->header('User-Agent'),
        ]);


        return redirect()->route('livewire.manage-users')
                        ->with('success', 'Akun berhasil dibuat.');
    }


    /**
     * Proses register oleh super admin.
     */
    public function storeForSuperAdmin(Request $request): RedirectResponse
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:' . User::class],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:' . User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'in:user,admin,superadmin'],
            'status'   => ['required', 'in:active,inactive'],
        ]);

        $user = User::create([
            'name'     => $request->name,
            'username' => $request->username,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $request->role,
            'status'   => $request->status,
        ]);

        event(new Registered($user));

        return redirect()
            ->route('kelola-akun')
            ->with('success', 'Akun baru berhasil dibuat.');
    }
}
