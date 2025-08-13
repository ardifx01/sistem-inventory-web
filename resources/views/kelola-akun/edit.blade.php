@include('kelola-akun.form', [
    'action' => route('kelola-akun.update', $user->id),
    'method' => 'PUT',
    'title' => 'Edit Akun',
    'button' => 'Update Akun',
    'user' => $user
])