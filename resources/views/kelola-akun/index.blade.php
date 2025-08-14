@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Kelola Akun') }}
</h2>
@endsection

@section('content')
<div class="p-6 space-y-6 text-white">
    <div>
        <a href="{{ route('kelola-akun.create') }}" 
           class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 shadow">
            + Tambah Akun
        </a>
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
    </div>
</div>
@endsection
