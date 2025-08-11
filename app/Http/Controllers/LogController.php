<?php

namespace App\Http\Controllers;

use App\Models\AktifitasLog; // Misal ada model AktifitasLog

class LogController extends Controller
{
    public function index()
    {
        // $logs = AktifitasLog::latest()->paginate(20);
        return view('log.index', compact('logs'));
    }
}
