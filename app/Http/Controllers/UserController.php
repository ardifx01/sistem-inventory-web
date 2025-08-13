<?php

namespace App\Http\Controllers;

use App\Models\User;

class UserController extends Controller
{
    public function manage()
    {
        $users = User::all();
        return view('livewire.manage-users', compact('users'));
    }
}
