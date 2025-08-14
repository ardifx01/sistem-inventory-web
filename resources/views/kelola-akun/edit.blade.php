@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Edit Akun') }}
</h2>
@endsection

@section('content')
<div class="py-6">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-transparent dark:bg-gray-800 shadow sm:rounded-lg p-6">

            <h2 class="text-2xl font-bold mb-4 text-center">Edit Akun</h2>

            <form action="{{ route('kelola-akun.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Name -->
                <div>
                    <x-input-label for="name" :value="__('Name')" />
                    <x-text-input id="name" class="block mt-1 w-full"
                                  type="text" name="name" value="{{ old('name', $user->name) }}"
                                  required autofocus />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <!-- Username -->
                <div class="mt-4">
                    <x-input-label for="username" :value="__('Username')" />
                    <x-text-input id="username" class="block mt-1 w-full"
                                  type="text" name="username" value="{{ old('username', $user->username) }}"
                                  required />
                    <x-input-error :messages="$errors->get('username')" class="mt-2" />
                </div>

                <!-- Email -->
                <!-- Email -->
                <div class="mt-4">
                    <x-input-label for="email" :value="__('Email')" />
                    <x-text-input id="email" class="block mt-1 w-full bg-gray-100 cursor-not-allowed"
                                type="email" name="email"
                                value=" {{ $user->email }}"
                                readonly />
                    {{-- <p class="text-gray-500 text-sm mt-1">Email tidak dapat diubah.</p>     --}}
                </div>


                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password (kosongkan jika tidak ingin diubah)')" />
                    <x-text-input id="password" class="block mt-1 w-full"
                                  type="password" name="password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-4">
                    <x-input-label for="password_confirmation" :value="__('Confirm Password')" />
                    <x-text-input id="password_confirmation" class="block mt-1 w-full"
                                  type="password" name="password_confirmation" />
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
                </div>

                <!-- Role -->
                <div class="mt-4">
                    <x-input-label for="role" :value="__('Role')" />
                    <select id="role" name="role" class="text-black block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="user" {{ $user->role=='user' ? 'selected' : '' }}>User</option>
                        <option value="admin" {{ $user->role=='admin' ? 'selected' : '' }}>Admin</option>
                    </select>
                    <x-input-error :messages="$errors->get('role')" class="mt-2" />
                </div>

                <!-- Status -->
                <div class="mt-4">
                    <x-input-label for="status" :value="__('Status')" />
                    <select id="status" name="status" class="text-black     block mt-1 w-full rounded-md border-gray-300 shadow-sm">
                        <option value="active" {{ $user->status=='active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ $user->status=='inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                    <x-input-error :messages="$errors->get('status')" class="mt-2" />
                </div>

                <div class="flex items-center justify-end mt-6">
                        <!-- Tombol Kembali -->
                    <a href="{{ url('/kelola-akun') }}"
                    class="inline-flex items-center px-4 py-2 bg-gray-500 border border-transparent rounded-md
                            font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-600
                            focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 mr-3 ">
                        Kembali
                    </a>
                    <x-primary-button>
                        {{ __('Simpan Perubahan') }}
                    </x-primary-button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection
