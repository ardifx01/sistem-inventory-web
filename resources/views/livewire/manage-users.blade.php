<div class="p-6 space-y-6">

    {{-- Form Tambah/Edit User --}}
    <div class="mb-4">
        <a href="{{ route('kelola-akun.create') }}" 
        class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
            + Tambah Akun
        </a>
    </div>



    {{-- Tabel Daftar User --}}
    <div class=" bg-white text-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Akun</h2>
        <table class="w-full border-collapse border border-gray-300">
            <thead class="bg-gray-100 dark:bg-gray-700  ">
                <tr>
                    <th class="border px-4 py-2">Nama</th>
                    <th class="border px-4 py-2">Email</th>
                    <th class="border px-4 py-2">Username</th>
                    <th class="border px-4 py-2">Role</th>
                    <th class="border px-4 py-2">Status</th>
                    <th class="border px-4 py-2">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @foreach($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="border px-4 py-2">{{ $user->name }}</td>
                        <td class="border px-4 py-2">{{ $user->email }}</td>
                        <td class="border px-4 py-2 text-center">{{ $user->username }}</td>
                        <td class="border px-4 py-2 text-center">{{ ucfirst($user->role) }}</td>
                        <td class="border px-4 py-2 text-center">
                            <span class=" px-6 py-1 rounded  text-white text-xs  
                                {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500 ' }}">
                                {{ ucfirst($user->status) }}
                            </span>
                        </td>
                        <td class="border px-4 py-2 space-x-2 text-center">
                            <a href="{{ route('kelola-akun.edit', $user->id) }}"class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">Edit</a>
                            <button wire:click="toggleStatus({{ $user->id }})"
                                class="px-3 py-1 {{ $user->status === 'active' ? 'bg-red-500 hover:bg-red-600' : 'bg-green-500 hover:bg-green-600' }} text-white rounded">
                                {{ $user->status === 'active' ? 'Nonaktifkan' : 'Aktifkan' }}
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
