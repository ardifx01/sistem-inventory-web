<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class KelolaAkunController extends Controller
{
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

        // Pagination, 10 data per halaman
        $users = $query->paginate(10);

        // Pastikan pencarian ikut saat pindah halaman
        $users->appends(['search' => $request->search]);

        return view('kelola-akun.index', compact('users'));
    }

    public function create()
    {
        return view('kelola-akun.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|string|max:255',
            'email'=>'required|email|unique:users,email',
            'username'=>'required|string|unique:users,username',
            'password'=>'required|string|min:8|confirmed',
            'role'=>'required|in:user,admin,superadmin',
            'status'=>'required|in:active,inactive',
        ]);

        User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'username'=>$request->username,
            'password'=>Hash::make($request->password),
            'role'=>$request->role,
            'status'=>$request->status,
        ]);

        return redirect()->route('kelola-akun')->with('message','Akun baru berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        return view('kelola-akun.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        if(auth()->user()->role === 'superadmin') {
            // superadmin tidak bisa ubah email
            $request->validate([
                'name'=>'required|string|max:255',
                'username'=>'required|string|unique:users,username,'.$user->id,
                'role'=>'required|in:user,admin,superadmin',
                'status'=>'required|in:active,inactive',
                'password'=>'nullable|string|min:8|confirmed',
            ]);
        } else {
            // user biasa bisa ubah email
            $request->validate([
                'name'=>'required|string|max:255',
                'email'=>'required|email|unique:users,email,'.$user->id,
                'username'=>'required|string|unique:users,username,'.$user->id,
                'role'=>'required|in:user,admin,superadmin',
                'status'=>'required|in:active,inactive',
                'password'=>'nullable|string|min:8|confirmed',
            ]);
        }

        $user->name = $request->name;
        $user->username = $request->username;
        $user->role = $request->role;
        $user->status = $request->status;

        if(auth()->user()->role !== 'superadmin') {
            $user->email = $request->email; // hanya user biasa bisa update email
        }

        if($request->password){
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return redirect()->route('kelola-akun')->with('message','Akun berhasil diperbarui.');
    }
}
