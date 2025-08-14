<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index()
    {
        $logs = Activity::latest()->paginate(20); // tampilkan 20 per halaman
        return view('log.index', compact('logs'));
    }

    public function destroy($id)
    {
        Activity::findOrFail($id)->delete();
        return redirect()->route('aktifitas-log')->with('message', 'Log berhasil dihapus.');
    }

    public function bulkDestroy(Request $request)
    {
        $ids = explode(',', $request->ids);
        if (!empty($ids)) {
            Activity::whereIn('id', $ids)->delete();
        }
        return redirect()->route('aktifitas-log')->with('message', 'Log terpilih berhasil dihapus.');
    }


    // public function clearAll()
    // {
    //     Activity::truncate();
    //     return redirect()->route('aktifitas-log')->with('message', 'Semua log berhasil dihapus.');
    // }
}

