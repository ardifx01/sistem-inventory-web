<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    // Tampilkan daftar akun
    public function index()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('kelola-akun.index', compact('users'));
    }

    // Tampilkan form edit
    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('kelola-akun.edit', compact('user'));
    }

    // Update data user
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$user->id,
            'username' => 'required|string|unique:users,username,'.$user->id,
            'role' => 'required|in:user,admin,superadmin',
            'status' => 'required|in:active,inactive',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $user->name = $request->name;
        $user->email = $request->email;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->status = $request->status;

        if($request->password) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('kelola-akun')->with('message', 'Akun berhasil diperbarui.');
    }

    // Toggle status aktif/inaktif
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();

        return redirect()->route('kelola-akun')->with('message', 'Status akun berhasil diperbarui.');
    }
}
