<div>
    <h3>Profil Superadmin</h3>
    <p>Nama: {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
    <p>Role: Superadmin</p>
    <p>Status: {{ auth()->user()->status }}</p>
    <a href="#">Kelola Semua Akun</a>
</div>
