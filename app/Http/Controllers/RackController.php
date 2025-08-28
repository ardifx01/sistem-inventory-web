<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class RackController extends Controller
{
    /**
     * Display the rack layout.
     */
    public function index(Request $request)
    {
        $areas        = ['PO1', 'PO2'];
        $selectedArea = $request->query('area', 'PO1'); // ambil dari query string ?area=PO1
        $levels       = 5;
        $positions    = 10;
        $columns      = 5;

        // Contoh data slot yang biasanya berasal dari database
        $slots = [
            'PO1-03-03-04' => ['role' => 'pieces', 'sku' => 'SKU-001', 'qty' => 12],
            'PO1-01-05-01' => ['role' => 'bulky',  'sku' => 'SKU-XL',  'qty' => 3],
            'PO1-02-01-07' => ['role' => 'lower',  'sku' => 'SKU-LWR', 'qty' => 9],
        ];

        return view('rack.index', compact(
            'areas',
            'selectedArea',
            'levels',
            'positions',
            'columns',
            'slots'
        ));
    }
}
