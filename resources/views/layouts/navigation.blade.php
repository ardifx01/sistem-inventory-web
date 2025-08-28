<nav x-data="{ open: false }" class="bg-white dark:bg-gray-800 border-b border-gray-100 dark:border-gray-700">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <!-- Logo untuk Light Mode -->
                        <img src="{{ asset('images/logo-light.png') }}" 
                             alt="Logo" 
                             class="block h-12 w-auto dark:hidden" />

                        <!-- Logo untuk Dark Mode -->
                        <img src="{{ asset('images/logo-dark.png') }}" 
                             alt="Logo" 
                             class="hidden h-12 w-auto dark:block" />
                    </a>
                </div>

                <!-- Navigation Links -->
                @php $role = Auth::user()->role; @endphp

                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('items.index')" :active="request()->routeIs('items.*')">
                        {{ __('Daftar Barang') }}
                    </x-nav-link>
                </div>
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('tatanan-rack')" :active="request()->routeIs('tatanan-rack')">
                        {{ __('Tatanan Rak') }}
                    </x-nav-link>
                </div>

                @if($role === 'superadmin')
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('aktifitas-log')" :active="request()->routeIs('aktifitas-log')">
                            {{ __('Aktifitas Log') }}
                        </x-nav-link>
                    </div>
                    <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                        <x-nav-link :href="route('kelola-akun')" :active="request()->routeIs('kelola-akun')">
                            {{ __('Kelola Akun') }}
                        </x-nav-link>
                    </div>
                @endif
            </div>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <!-- Notifikasi Superadmin -->
@if(auth()->user() && auth()->user()->role === 'superadmin')
    @php
        $notifList = auth()->user()->unreadNotifications()->latest()->take(5)->get();
        $notifCount = auth()->user()->unreadNotifications()->count();
    @endphp

    <div class="relative">
        <button id="notifBtn" class="relative mr-2 p-2 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700">
            <!-- Ikon lonceng -->
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-gray-700 dark:text-gray-200" viewBox="0 0 24 24" fill="currentColor">
                <path d="M14.243 20.01A2.998 2.998 0 0 1 12 21.5a2.998 2.998 0 0 1-2.243-1.49h4.486zM12 2a7 7 0 0 0-7 7v3.382l-1.553 2.59A1 1 0 0 0 4.29 16h15.42a1 1 0 0 0 .843-1.528L19 12.382V9a7 7 0 0 0-7-7z"/>
            </svg>
            @if($notifCount > 0)
                <span class="absolute -top-0.5 -right-0.5 bg-red-600 text-white text-[10px] leading-none rounded-full px-1.5 py-0.5">
                    {{ $notifCount }}
                </span>
            @endif
        </button>

        <!-- Popover -->
        <div id="notifPopup" class="hidden absolute right-0 mt-2 w-100 bg-white dark:bg-gray-800 shadow-lg rounded-lg z-50">
            <div class="p-4">
                <h3 class="font-semibold text-gray-800 dark:text-gray-100 mb-2">Notifikasi</h3>
                <ul class="max-h-60 overflow-y-auto divide-y divide-gray-200 dark:divide-gray-700">
                    @forelse($notifList as $notif)
<li class="py-2 flex justify-between items-center">
    <!-- Pesan notif -->
    <div class="text-sm text-gray-700 dark:text-gray-200">
        {{ $notif->data['message'] }}
        <div class="text-xs text-gray-400">{{ $notif->created_at->diffForHumans() }}</div>
    </div>

    <!-- Aksi (sejajar kanan) -->
    <div class="flex space-x-2">
        <!-- Tombol tandai dibaca -->
        <button onclick="markAsRead('{{ $notif->id }}')" 
                class="flex flex-col items-center text-blue-500 hover:text-blue-700">
            <div class="p-1.5 ml-2 bg-blue-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-4 w-4" fill="none" viewBox="0 0 24 24" 
                     stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <span class="text-[10px] ml-2 mt-0.5">Dibaca</span>
        </button>

        <!-- Tombol edit profil -->
        <a href="{{ route('kelola-akun.edit', $notif->data['user_id']) }}" 
           class="flex flex-col items-center text-gray-500 hover:text-blue-600"
           title="Edit Profil User">
            <div class="p-1.5 bg-gray-100 rounded-full">
                <svg xmlns="http://www.w3.org/2000/svg" 
                     class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M17.414 2.586a2 2 0 010 2.828L8.414 14.414a2 2 0 01-1.414.586H5a1 1 0 01-1-1v-2a2 2 0 01.586-1.414l9-9a2 2 0 012.828 0z"/>
                    <path fill-rule="evenodd" 
                          d="M4 16h12a1 1 0 110 2H4a1 1 0 110-2z" 
                          clip-rule="evenodd"/>
                </svg>
            </div>
            <span class="text-[10px] mt-0.5">Edit</span>
        </a>
    </div>
</li>

                    @empty
                        <li class="py-4 text-sm text-gray-500 text-center">Tidak ada notifikasi baru</li>
                    @endforelse
                </ul>
            </div>
        </div>
    </div>

    <script>
        const notifBtn = document.getElementById('notifBtn');
        const notifPopup = document.getElementById('notifPopup');

        notifBtn.addEventListener('click', () => {
            notifPopup.classList.toggle('hidden');
        });

        document.addEventListener('click', (e) => {
            if (!notifBtn.contains(e.target) && !notifPopup.contains(e.target)) {
                notifPopup.classList.add('hidden');
            }
        });

        async function markAsRead(id) {
            fetch(`/notifications/${id}/read`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            }).then(res => {
                if(res.ok) location.reload(); // refresh biar notif count update
            });
        }
    </script>
@endif
  



                <!-- Tombol Toggle Dark/Light -->
                <button id="theme-toggle" class="mr-4 p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                    <svg id="theme-toggle-light-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 3.75a6.25 6.25 0 100 12.5 6.25 6.25 0 000-12.5zM10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5a.75.75 0 01.75-.75zM3.22 3.22a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06L3.22 4.28a.75.75 0 010-1.06zM14.66 14.66a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06l-1.06-1.06a.75.75 0 010-1.06zM2 10a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5A.75.75 0 012 10zM15.75 9.25h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 010-1.5zM4.28 15.72a.75.75 0 010-1.06l1.06-1.06a.75.75 0 111.06 1.06L5.34 15.72a.75.75 0 01-1.06 0zM14.66 5.34a.75.75 0 010-1.06l1.06-1.06a.75.75 0 011.06 1.06l-1.06 1.06a.75.75 0 01-1.06 0z"/>
                    </svg>
                    <svg id="theme-toggle-dark-icon" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z"/>
                    </svg>
                </button>

                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md
                        text-gray-500 dark:text-gray-400 bg-white dark:bg-gray-800 hover:text-gray-700 dark:hover:text-gray-300 focus:outline-none
                        transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>
                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd"
                                        d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                                        111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"
                                        clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <x-dropdown-link :href="route('logout')"
                                onclick="event.preventDefault();
                                    this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger for mobile -->
            <div class="-me-2 flex items-center sm:hidden">
                <div class="">
                    <button id="theme-toggle-mobile" class="w-full flex items-center justify-center p-2 rounded-md text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                        <svg id="theme-toggle-light-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10 3.75a6.25 6.25 0 100 12.5 6.25 6.25 0 000-12.5zM10 2a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5A.75.75 0 0110 2zM10 15.75a.75.75 0 01.75.75v1.5a.75.75 0 01-1.5 0v-1.5a.75.75 0 01.75-.75zM3.22 3.22a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06L3.22 4.28a.75.75 0 010-1.06zM14.66 14.66a.75.75 0 011.06 0l1.06 1.06a.75.75 0 11-1.06 1.06l-1.06-1.06a.75.75 0 010-1.06zM2 10a.75.75 0 01.75-.75h1.5a.75.75 0 010 1.5h-1.5A.75.75 0 012 10zM15.75 9.25h1.5a.75.75 0 010 1.5h-1.5a.75.75 0 010-1.5zM4.28 15.72a.75.75 0 010-1.06l1.06-1.06a.75.75 0 111.06 1.06L5.34 15.72a.75.75 0 01-1.06 0zM14.66 5.34a.75.75 0 010-1.06l1.06-1.06a.75.75 0 011.06 1.06l-1.06 1.06a.75.75 0 01-1.06 0z"/>
                        </svg>
                        <svg id="theme-toggle-dark-icon-mobile" class="hidden w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M17.293 13.293A8 8 0 016.707 2.707a8 8 0 1010.586 10.586z"/>
                        </svg>
                    </button>
                </div>
                <button @click="open = ! open"
                    class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 dark:text-gray-500
                    hover:text-gray-500 dark:hover:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-900
                    focus:outline-none focus:bg-gray-100 dark:focus:bg-gray-900 focus:text-gray-500 dark:focus:text-gray-400
                    transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden"
                            stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            @if(in_array($role, ['admin', 'user', 'superadmin']))
                <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                    {{ __('Dashboard') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('items.index')" :active="request()->routeIs('daftar-barang')">
                    {{ __('Daftar Barang') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('tatanan-rack')" :active="request()->routeIs('tatanan-rak')">
                    {{ __('Tatanan Rak') }}
                </x-responsive-nav-link>
            @endif
            @if($role === 'superadmin')
                <x-responsive-nav-link :href="route('aktifitas-log')" :active="request()->routeIs('aktifitas-log')">
                    {{ __('Aktifitas Log') }}
                </x-responsive-nav-link>
                <x-responsive-nav-link :href="route('kelola-akun')" :active="request()->routeIs('kelola-akun')">
                    {{ __('Kelola Akun') }}
                </x-responsive-nav-link>
            @endif
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200 dark:border-gray-600">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800 dark:text-gray-200">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <x-responsive-nav-link :href="route('logout')"
                        onclick="event.preventDefault();
                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>

<script>
const themeToggleBtn = document.getElementById('theme-toggle');
const darkIcon = document.getElementById('theme-toggle-dark-icon');
const lightIcon = document.getElementById('theme-toggle-light-icon');
const themeToggleBtnMobile = document.getElementById('theme-toggle-mobile');
const darkIconMobile = document.getElementById('theme-toggle-dark-icon-mobile');
const lightIconMobile = document.getElementById('theme-toggle-light-icon-mobile');

function setThemeIcons() {
    if (document.documentElement.classList.contains('dark')) {
        lightIcon.classList.remove('hidden');
        lightIconMobile.classList.remove('hidden');
        darkIcon.classList.add('hidden');
        darkIconMobile.classList.add('hidden');
    } else {
        darkIcon.classList.remove('hidden');
        darkIconMobile.classList.remove('hidden');
        lightIcon.classList.add('hidden');
        lightIconMobile.classList.add('hidden');
    }
}

if (localStorage.getItem('color-theme') === 'dark' ||
    (!localStorage.getItem('color-theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
    document.documentElement.classList.add('dark');
} else {
    document.documentElement.classList.remove('dark');
}
setThemeIcons();

themeToggleBtn.addEventListener('click', function() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('color-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    setThemeIcons();
});

themeToggleBtnMobile.addEventListener('click', function() {
    document.documentElement.classList.toggle('dark');
    localStorage.setItem('color-theme', document.documentElement.classList.contains('dark') ? 'dark' : 'light');
    setThemeIcons();
});

document.addEventListener("DOMContentLoaded", () => {
    const notifBtn = document.getElementById("notif-btn");
    const notifPopup = document.getElementById("notif-popup");

    notifBtn?.addEventListener("click", () => {
        notifPopup.classList.toggle("hidden");
    });

    // Klik di luar popup -> tutup
    document.addEventListener("click", function(e) {
        if (!notifBtn.contains(e.target) && !notifPopup.contains(e.target)) {
            notifPopup.classList.add("hidden");
        }
    });
});

function openResetPopup() {
    document.getElementById('resetModal').classList.remove('hidden');
    document.getElementById('resetModal').classList.add('flex');
}
function closeResetPopup() {
    document.getElementById('resetModal').classList.remove('flex');
    document.getElementById('resetModal').classList.add('hidden');
}
</script>
