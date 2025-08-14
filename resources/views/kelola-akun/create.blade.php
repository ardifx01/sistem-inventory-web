@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Tambah Akun Baru') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">
            
            <h2 class="text-2xl font-bold mb-4 text-center">Tambah Akun</h2>

            <form method="POST" action="{{ route('kelola-akun.store') }}">
                @csrf

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Nama')" />
                    <x-text-input id="name" class="block mt-1 w-full"
                                  type="text" name="name" value="{{ old('name') }}"
                                  required autofocus autocomplete="name" />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Username -->
                <div class="mt-4">
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" class="block mt-1 w-full"
                                  type="text" name="username" value="{{ old('username') }}"
                                  required autocomplete="username" />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full"
                                  type="email" name="email" value="{{ old('email') }}"
                                  required autocomplete="email" />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password" name="password" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Konfirmasi Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                  type="password" name="password_confirmation" required autocomplete="new-password" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <select id="role" name="role"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700
                                   dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500
                                   focus:ring-indigo-500">
                        <option value="user" {{ old('role') == 'user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="superadmin" {{ old('role') == 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status"
                            class="block mt-1 w-full rounded-md border-gray-300 dark:border-gray-700
                                   dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500
                                   focus:ring-indigo-500">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6 space-x-2">
                    <a href="{{ url('/kelola-akun') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md
                            font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600
                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 mr-3 ">
                        Kembali
                    </a>
                    <x-primary-button>
                        {{ __('Simpan Akun') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
