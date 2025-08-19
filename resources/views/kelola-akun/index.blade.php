@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Kelola Akun') }}
</h2>
@endsection

@section('content')
<div class="p-6 space-y-6 text-white">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <a href="{{ route('kelola-akun.create') }}" 
           class="px-4 py-3 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
            + Tambah Akun
        </a>

        
    <!-- Form Pencarian + Dropdown -->
    <form action="{{ route('kelola-akun') }}" method="GET" class="flex sm:w-auto gap-2 ">
        
{{-- Dropdown Role --}}
<div class="relative">
    <select name="role" id="role"
        class="appearance-none px-3 py-2.5 pr-10 rounded-lg border border-gray-300 
            focus:ring-indigo-500 focus:border-indigo-500 
            dark:bg-gray-900 dark:text-gray-200"
        onchange="this.form.submit()">
        <option value="">Semua Role</option>
        <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>Admin</option>
        <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>User</option>
    </select>
    <!-- Ikon panah bawah -->
</div>

{{-- Dropdown Status --}}
<div class="relative">
    <select name="status" id="status"
        class="appearance-none px-3 py-2.5 pr-10 rounded-lg border border-gray-300 
            focus:ring-indigo-500 focus:border-indigo-500 
            dark:bg-gray-900 dark:text-gray-200"
        onchange="this.form.submit()">
        <option value="">Semua Status</option>
        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
    </select>
</div>



        {{-- Input pencarian --}}
        <input type="text" name="search" id="search"
            value="{{ request('search') }}"
            placeholder="Cari nama atau username…"
            class="px-4 py-2.5 rounded-l-lg border border-gray-300 rounded-lg
                focus:ring-indigo-500 focus:border-indigo-500 
                dark:bg-gray-900 dark:text-gray-200 w-64" />

        {{-- Tombol cari --}}
        <button type="submit" id="searchBtn"
                class="px-4 border border-gray-300 rounded-lg 
                    flex items-center justify-center 
                    dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>  
        </button>

        {{-- Tombol clear --}}
        @if(request('search') || request('role') || request('status'))
            <a href="{{ route('kelola-akun') }}"
            class="px-4 rounded-r-lg border border-gray-300 rounded-lg 
                    flex items-center justify-center 
                    dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                    stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        d="M6 18L18 6M6 6l12 12" />
                </svg>
            </a>
        @else
            <style>
                #searchBtn { border-top-right-radius: 0.5rem; border-bottom-right-radius: 0.5rem; }
            </style>
        @endif
    </form>


    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6">
        <h2 class="text-lg font-semibold mb-4">Daftar Akun</h2>

        @if(session('message'))
            <div class="mb-4 px-4 py-2 bg-green-500 text-white rounded">
                {{ session('message') }}
            </div>
        @endif

        <!-- Wrapper untuk scroll di mobile -->
        <div class="overflow-x-auto">
            <table class="w-full border-collapse border border-gray-300 text-sm min-w-[640px]">
                <thead class="bg-gray-100 dark:bg-gray-700">
                    <tr>
                        <th class="border px-4 py-2">Nama</th>
                        <th class="border px-4 py-2">Email</th>
                        <th class="border px-4 py-2">Username</th>
                        <th class="border px-4 py-2">Role</th>
                        <th class="border px-4 py-2">Status</th>
                        <th class="border px-4 py-2 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                            <td class="border px-4 py-2">{{ $user->name }}</td>
                            <td class="border px-4 py-2">{{ $user->email }}</td>
                            <td class="border px-4 py-2 text-center">{{ $user->username }}</td>
                            <td class="border px-4 py-2 text-center">{{ ucfirst($user->role) }}</td>
                            <td class="border px-4 py-2 text-center">
                                <span class="px-3 py-1 rounded text-white text-xs  
                                    {{ $user->status === 'active' ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ ucfirst($user->status) }}
                                </span>
                            </td>
                            <td class="border px-4 py-2 text-center space-x-2">
                                <a href="{{ route('kelola-akun.edit', $user->id) }}" 
                                   class="px-3 py-1 bg-yellow-500 text-white rounded hover:bg-yellow-600">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-gray-500">
                                Tidak ada data pengguna.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $users->appends(['search' => request('search')])->links() }}
        </div>
    </div>
</div>
@endsection
