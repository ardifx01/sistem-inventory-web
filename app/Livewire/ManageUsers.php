<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class ManageUsers extends Component
{
    public $name, $email, $username, $password, $role = 'user', $status = 'active';
    public $userId;
    public $isEdit = false;

    public function render()
    {
        $users = User::orderBy('created_at', 'desc')->get();
        return view('livewire.manage-users', compact('users'))
            ->layout('layouts.app');
    }

    public function resetForm()
    {
        $this->name = $this->email = $this->username = $this->password = '';
        $this->role = 'user';
        $this->status = 'active';
        $this->userId = null;
        $this->isEdit = false;
    }

    public function save()
    {
        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'username' => $this->username,
            'role' => $this->role,
            'status' => $this->status,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->isEdit) {
            User::findOrFail($this->userId)->update($data);
            session()->flash('message', 'Akun berhasil diperbarui.');
        } else {
            $data['password'] = Hash::make($this->password);
            User::create($data);
            session()->flash('message', 'Akun baru berhasil ditambahkan.');
        }

        $this->resetForm();
    }

    public function edit($id)
    {
        $user = User::findOrFail($id);
        $this->userId = $user->id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->username = $user->username;
        $this->role = $user->role;
        $this->status = $user->status;
        $this->isEdit = true;
    }

    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);
        $user->status = $user->status === 'active' ? 'inactive' : 'active';
        $user->save();
    }
}
