<?php

namespace App\Http\Controllers;

use Spatie\Activitylog\Models\Activity;
use Illuminate\Http\Request;

class LogController extends Controller
{
    public function index(Request $request)
    {
        $logs = Activity::with('causer')
            ->when($request->tanggal, function ($query) use ($request) {
                $query->whereDate('created_at', $request->tanggal);
            })
            ->when($request->username, function ($query) use ($request) {
                $query->whereHas('causer', function ($subQuery) use ($request) {
                    $subQuery->where('username', 'like', '%' . $request->username . '%');
                });
            })
            ->latest()
            ->paginate(20);

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
}
