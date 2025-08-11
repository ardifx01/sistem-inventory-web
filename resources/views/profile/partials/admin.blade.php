<div>
    <h3>Profil Admin</h3>
    <p>Nama: {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
    <p>Role: Admin</p>
    <p>Status: {{ auth()->user()->status }}</p>
</div>
