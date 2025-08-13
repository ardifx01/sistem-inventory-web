<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Rack;

class RackController extends Controller
{
    /**
     * Tampilkan daftar rack.
     */
    public function index()
    {
        return view('rack.index');
    }
}

