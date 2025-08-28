<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600 dark:text-gray-400">
        Masukkan username atau email Anda untuk mengajukan permintaan reset password.
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('request.reset') }}">
        @csrf

        <!-- Username/Email -->
        <div>
            <x-input-label for="username_or_email" :value="__('Email atau Username')" />
            <x-text-input id="username_or_email" class="block mt-1 w-full" type="text" name="username_or_email" :value="old('username_or_email')" required autofocus />
            <x-input-error :messages="$errors->get('username_or_email')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-4">
            <a href="{{ route('login') }}" class="inline-flex items-center px-4 py-2 bg-gray-800 dark:bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-white dark:text-gray-800 uppercase tracking-widest hover:bg-gray-700 dark:hover:bg-white focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                Kembali
            </a>

            <x-primary-button>
                Kirim Permintaan Reset
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
