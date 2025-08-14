<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Item;
use Illuminate\Support\Facades\DB;
use Spatie\Activitylog\Models\Activity;

class DashboardController extends Controller
{
    public function index()
    {
        // Total Barang
        $totalBarang = Item::count();

        // Total Kategori
        $totalKategori = Category::count();

        // Total Rak
        $totalRak = Item::whereNotNull('rack_location')
                        ->where('rack_location', '!=', '')
                        ->where('rack_location', '!=', 'ZIP')
                        ->selectRaw("DISTINCT SUBSTRING_INDEX(rack_location, '-', 1) as rak_prefix")
                        ->get()
                        ->count();

        // Barang Belum Masuk Rak
        $belumMasukRak = Item::where('rack_location', 'ZIP')->count();

        // Barang terbaru (4 item saja)
        $barangBaru = Item::select('name', 'item_code', 'rack_location')
                        ->orderBy('created_at', 'desc')
                        ->take(4)
                        ->get();

        return view('layouts.dashboard', compact(
            'totalBarang',
            'totalKategori',
            'totalRak',
            'belumMasukRak',
            'barangBaru'
        ));
    }


    public function getBarangBaru()
    {
        $barangBaru = Item::select('name', 'item_code', 'rack_location')
                        ->orderBy('created_at', 'desc')
                        ->take(8)
                        ->get();

        return response()->json($barangBaru);
    }

}
