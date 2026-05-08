<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WhistleblowerReport;
use Illuminate\Http\Request;

class WbsController extends Controller
{
    public function index()
    {
        $reports = WhistleblowerReport::latest()->paginate(20);
        return view('admin.wbs.index', compact('reports'));
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
}
