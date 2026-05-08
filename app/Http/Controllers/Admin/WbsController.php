<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhistleblowerReport;
use Illuminate\Http\Request;

class WbsController extends Controller
{
    public function index(Request $request)
    {
        $query = WhistleblowerReport::query();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('ticket_number', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
        }

        if ($request->filled('status') && $request->status !== 'all') {
            $query->where('status', $request->status);
        }

        $reports = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => WhistleblowerReport::count(),
            'pending' => WhistleblowerReport::where('status', 'pending')->count(),
            'investigating' => WhistleblowerReport::where('status', 'investigating')->count(),
            'resolved' => WhistleblowerReport::where('status', 'resolved')->count(),
            'rejected' => WhistleblowerReport::where('status', 'rejected')->count(),
        ];

        return view('admin.wbs.index', compact('reports', 'stats'));
    }

    public function show($id)
    {
        $report = WhistleblowerReport::with(['user', 'evidences'])->findOrFail($id);
        return view('admin.wbs.show', compact('report'));
    }

    public function update(Request $request, $id)
    {
        $report = WhistleblowerReport::findOrFail($id);

        $request->validate([
            'status' => 'required|in:pending,investigating,resolved,rejected',
        ]);

        $report->update([
            'status' => $request->status,
            'admin_response' => $request->admin_response,
        ]);

        return back()->with('success', 'Status laporan berhasil diperbarui.');
    }

    public function bulkDestroy(Request $request)
    {
        $request->validate([
            'report_ids' => 'required|array',
            'report_ids.*' => 'exists:whistleblower_reports,id'
        ]);

        WhistleblowerReport::whereIn('id', $request->report_ids)->delete();

        return back()->with('success', 'Laporan terpilih berhasil dihapus.');
    }
}
