<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{

        // Form tambah akun
    public function create()
    {
        return view('kelola-akun.create');
    }

    // Simpan akun baru
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|in:user,admin,superadmin',
            'status' => 'required|in:active,inactive',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->status,
        ]);

        return redirect()->route('kelola-akun')->with('message', 'Akun baru berhasil ditambahkan.');
    }

// Tampilkan daftar akun
public function index(Request $request)
{
    $query = User::orderBy('created_at', 'desc');

    // Filter pencarian jika ada input
    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('username', 'like', "%{$search}%");
        });
    }

    // Filter role
    if ($request->filled('role')) {
        $query->where('role', $request->role);
    }

    // Filter status
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Pagination, 10 data per halaman
    $users = $query->paginate(10);

    // Pastikan semua filter ikut saat pindah halaman
    $users->appends($request->only(['search', 'role', 'status']));

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
        $user->username = $request->username;

        // Hanya superadmin yang boleh mengubah email-nya
        if ($user->role === 'superadmin') {
            $user->email = $request->email;
        }

        // Superadmin tidak bisa ubah role atau status dirinya sendiri
        if (!($user->id === auth()->id() && $user->role === 'superadmin')) {
            $user->role = $request->role;
            $user->status = $request->status;
        }

        if ($request->filled('password')) {
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
