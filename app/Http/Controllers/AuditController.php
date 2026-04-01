<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditController extends Controller
{
    public function index()
    {
        // Auto-cleanup
        AuditLog::where('created_at', '<', now()->subDays(30))->delete();

        $logs = AuditLog::with(['user', 'document'])->latest()->paginate(20);
        
        $topDownloaders = AuditLog::select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->with('user')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get();

        return view('audit.index', compact('logs', 'topDownloaders'));
    }

    public function destroyAll()
    {
        AuditLog::truncate();
        return back()->with('success', 'Seluruh log audit telah dibersihkan.');
    }
}
