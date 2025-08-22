<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans text-gray-900 antialiased bg-gray-100 dark:bg-gray-900">

    <!-- Wrapper background -->
    <div class="min-h-screen flex flex-col bg-cover bg-center relative"
         style="background-image: url('{{ asset('images/background.png') }}');">

        <!-- Overlay hitam transparan -->
        <div class="absolute inset-0 bg-black/50"></div>

        <!-- Wrapper isi -->
        <div class="flex flex-1 items-center 
                    justify-center md:justify-end 
                    md:pr-8 lg:pr-16 xl:pr-24 relative z-10">
            
            <!-- Form Card -->
            <div class="relative w-full max-w-md px-6 py-8 
                bg-white/80 dark:bg-gray-800/80 backdrop-blur-md 
                shadow-xl rounded-2xl
                md:-translate-x-8 lg:-translate-x-16 xl:-translate-x-24">
                
                <!-- Logo + Toggle -->
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <img src="{{ asset('images/logo-light.png') }}" alt="Logo" class="block dark:hidden w-32">
                        <img src="{{ asset('images/logo-dark.png') }}" alt="Logo" class="hidden dark:block w-32">
                    </div>

                    <!-- Tombol Toggle Dark/Light -->
                    <button id="theme-toggle-login" 
                        class="p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg id="theme-toggle-light-icon-login" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3.75a6.25 6.25 0 100 12.5 6.25 6.25 0 000-12.5zM10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5a.75.75 0 01.75-.75zM3.22 3.22a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06L3.22 4.28a.75.75 0 010-1.06zM14.66 14.66a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06l-1.06-1.06a.75.75 0 010-1.06zM2 10a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5A.75.75 0 012 10zM15.75 9.25h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 010-1.5zM4.28 15.72a.75.75 0 010-1.06l1.06-1.06a.75.75 0 111.06 1.06L5.34 15.72a.75.75 0 01-1.06 0zM14.66 5.34a.75.75 0 010-1.06l1.06-1.06a.75.75 0 011.06 1.06l-1.06 1.06a.75.75 0 01-1.06 0z"/>
                        </svg>
                        <svg id="theme-toggle-dark-icon-login" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z"/>
                        </svg>
                    </button>
                </div>

                <!-- Slot untuk isi form -->
                {{ $slot }}
            </div>
        </div>

       <!-- Footer -->
        <footer class="relative z-10 w-full text-center py-4 text-xs 
                       text-gray-300 dark:text-gray-500 bg-transparent">
            <p>&copy; {{ date('Y') }} Copy Right. By Telkom University Surabaya</p>
        </footer>
    </div>

    <!-- Script Toggle Dark/Light -->
    <script>
    const themeToggleLogin = document.getElementById('theme-toggle-login');
    const darkIconLogin = document.getElementById('theme-toggle-dark-icon-login');
    const lightIconLogin = document.getElementById('theme-toggle-light-icon-login');

    function setThemeIconsLogin() {
        if (document.documentElement.classList.contains('dark')) {
            lightIconLogin.classList.remove('hidden');
            darkIconLogin.classList.add('hidden');
        } else {
            darkIconLogin.classList.remove('hidden');
            lightIconLogin.classList.add('hidden');
        }
    }

    if (localStorage.getItem('color-theme') === 'dark' ||
        (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }

    setThemeIconsLogin();

    themeToggleLogin.addEventListener('click', function() {
        document.documentElement.classList.toggle('dark');
        localStorage.setItem('color-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
        setThemeIconsLogin();
    });
    </script>
</body>
</html>
