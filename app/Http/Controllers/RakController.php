<?php

namespace App\Http\Controllers;

use App\Models\Rak; // Asumsi ada model Rak

class RakController extends Controller
{
    public function index()
    {
        return view('rak.index', compact('raks'));
    }
}
