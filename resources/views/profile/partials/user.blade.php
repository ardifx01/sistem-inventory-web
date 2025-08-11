<div>
    <h3>Profil User</h3>
    <p>Nama: {{ auth()->user()->name }}</p>
    <p>Email: {{ auth()->user()->email }}</p>
    <p>Role: User</p>
    <p>Status: {{ auth()->user()->status }}</p>
</div>
