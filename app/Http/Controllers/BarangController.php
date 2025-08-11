<?php

namespace App\Http\Controllers;

use App\Models\Barang; // Asumsi ada model Barang

class BarangController extends Controller
{
    public function index()
    {
        
        return view('barang.index', compact('barangs'));
    }
}
