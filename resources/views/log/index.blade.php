@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Aktifitas Log') }}
</h2>
@endsection

@section('content')
<div class="p-6 space-y-6 ">

    @if(session('message'))
        <div class="mb-4 px-4 py-2 bg-green-500 text-white rounded">
            {{ session('message') }}
        </div>
    @endif

    {{-- Filter Form (di luar kotak) --}}
    <form action="{{ route('aktifitas-log') }}" method="GET" class="flex flex-col sm:flex-row gap-3 mb-4 bg-white dark:bg-gray-800 p-4 rounded-lg shadow">
        {{-- Filter tanggal --}}
        <div class="flex items-center gap-2">
            <label for="tanggal" class="text-gray-700 dark:text-gray-300 text-sm">Tanggal</label>
            <input type="date" name="tanggal" id="tanggal"
                value="{{ request('tanggal') }}"
                class="px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-900 dark:text-gray-200">
        </div>

        {{-- Search username --}}
        <div class="flex items-center gap-2 flex-1">
            <input type="text" name="username" id="username"
                placeholder="Cari username..."
                value="{{ request('username') }}"
                class="w-full px-3 py-2 border border-gray-300 rounded-lg dark:bg-gray-900 dark:text-gray-200">
        </div>

        {{-- Tombol filter --}}
        <div class="flex gap-2">
            <button type="submit"
                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                Filter
            </button>
            <a href="{{ route('aktifitas-log') }}"
                class="px-4 py-3 bg-gray-500 text-white rounded-lg hover:bg-gray-600">
                Reset
            </a>
        </div>
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 overflow-x-auto">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight mb-4">
            {{ __('Aktifitas Log') }}
        </h2>
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                <tr>
                    <th class="border px-4 py-2">Tanggal & Waktu</th>
                    <th class="border px-4 py-2">User</th>
                    <th class="border px-4 py-2">Action</th>
                    <th class="border px-4 py-2">Properties</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="border px-4 py-2 text-center">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                        <td class="border px-4 py-2 text-center">{{ $log->causer?->username ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $log->description }}</td>
                        <td class="border px-4 py-2 text-xs">
                            <pre class="whitespace-pre-wrap">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="text-center py-4 text-gray-500">
                            Tidak ada data log.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    </div>
</div>
@endsection
    