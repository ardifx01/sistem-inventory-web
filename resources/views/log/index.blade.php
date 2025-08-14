@extends('layouts.app')

@section('header')
<h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
    {{ __('Aktifitas Log') }}
</h2>
@endsection

@section('content')
<div class="p-6 space-y-6 text-white">

    @if(session('message'))
        <div class="mb-4 px-4 py-2 bg-green-500 text-white rounded">
            {{ session('message') }}
        </div>
    @endif

    <div class="mb-4 flex gap-2">
        {{-- Hapus semua log --}}
        {{-- <form action="{{ route('aktifitas-log.clear') }}" method="POST" onsubmit="return confirm('Hapus semua log?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-red-700 shadow">
                Hapus Semua Log
            </button>
        </form> --}}

        {{-- Hapus yang dipilih --}}
        <form id="bulkDeleteForm" action="{{ route('aktifitas-log.bulk-destroy') }}" method="POST" onsubmit="return confirm('Hapus log yang dipilih?')">
            @csrf
            @method('DELETE')
            <input type="hidden" name="ids" id="bulkDeleteIds">
            <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded hover:bg-yellow-700 shadow">
                Hapus Log
            </button>
        </form>
    </div>

    <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 overflow-x-auto">
        <table class="w-full border-collapse border border-gray-300 text-sm">
            <thead class="bg-gray-100 dark:bg-gray-700 text-gray-900 dark:text-gray-200">
                <tr>
                    <th class="border px-4 py-2">
                        <input type="checkbox" id="selectAll">
                    </th>
                    <th class="border px-4 py-2">Tanggal & Waktu</th>
                    <th class="border px-4 py-2">User</th>
                    <th class="border px-4 py-2">Action</th>
                    <th class="border px-4 py-2">Properties</th>
                    <th class="border px-4 py-2 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                        <td class="border px-4 py-2 text-center">
                            <input type="checkbox" class="selectItem" value="{{ $log->id }}">
                        </td>
                        <td class="border px-4 py-2 text-center">{{ $log->created_at->format('d-m-Y H:i:s') }}</td>
                        <td class="border px-4 py-2 text-center">{{ $log->causer?->username ?? '-' }}</td>
                        <td class="border px-4 py-2 text-center">{{ $log->description }}</td>
                        <td class="border px-4 py-2 text-xs">
                            <pre class="whitespace-pre-wrap">{{ json_encode($log->properties, JSON_PRETTY_PRINT) }}</pre>
                        </td>
                        <td class="border px-4 py-2 text-center">
                            <form action="{{ route('aktifitas-log.destroy', $log->id) }}" method="POST" onsubmit="return confirm('Hapus log ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-gray-500">
                            Tidak ada data log.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="mt-4">
            {{ $logs->links() }}
        </div>
    </div>
</div>

{{-- Script untuk checkbox --}}
<script>
    document.getElementById('selectAll').addEventListener('change', function() {
        const checked = this.checked;
        document.querySelectorAll('.selectItem').forEach(cb => cb.checked = checked);
    });

    document.getElementById('bulkDeleteForm').addEventListener('submit', function(e) {
        const ids = Array.from(document.querySelectorAll('.selectItem:checked')).map(cb => cb.value);
        if (ids.length === 0) {
            e.preventDefault();
            alert('Tidak ada log yang dipilih.');
            return;
        }
        document.getElementById('bulkDeleteIds').value = ids.join(',');
    });
</script>
@endsection
