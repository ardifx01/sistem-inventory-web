<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Notifikasi Reset Password') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow sm:rounded-lg p-6">
                
                @if($notifications->isEmpty())
                    <p class="text-gray-600 dark:text-gray-300">
                        ✅ Tidak ada notifikasi.
                    </p>
                @else
                    <ul class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($superadmin as $notif)
                            <li class="py-3 flex justify-between items-center">
                                <span class="text-gray-800 dark:text-gray-200">
                                    {{ $notif->data['message'] }}
                                </span>
                                <span class="text-xs text-gray-500">
                                    {{ $notif->created_at->diffForHumans() }}
                                </span>
                            </li>
                        @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
