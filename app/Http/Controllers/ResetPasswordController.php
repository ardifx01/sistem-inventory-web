<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Notifications\PasswordResetRequestNotification;

class ResetPasswordController extends Controller
{
    public function submitRequest(Request $request)
    {
        $request->validate([
            'username_or_email' => 'required|string',
        ]);

        $usernameOrEmail = $request->input('username_or_email');

        // Ambil semua superadmin
        $superadmins = User::where('role', 'superadmin')->get();

        foreach ($superadmins as $admin) {
            $admin->notify(new PasswordResetRequestNotification($usernameOrEmail));
        }

        return redirect()->back()->with('status', 'Permintaan reset password telah dikirim ke Superadmin.');
    }

    
}
