<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    public function index()
    {
        $shifts = Shift::all();
        return view('admin.shifts.index', compact('shifts'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        Shift::create([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_next_day' => $request->has('is_next_day'),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_shift',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . " menambahkan shift baru: $request->name"
        ]);

        return back()->with('success', 'Shift berhasil ditambahkan.');
    }

    public function update(Request $request, Shift $shift)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'start_time' => 'required',
            'end_time' => 'required',
        ]);

        $shift->update([
            'name' => $request->name,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_next_day' => $request->has('is_next_day'),
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'update_shift',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . " memperbarui konfigurasi shift: $shift->name"
        ]);

        return back()->with('success', 'Konfigurasi shift berhasil diperbarui.');
    }

    public function destroy(Shift $shift)
    {
        $name = $shift->name;
        $shift->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_shift',
            'ip_address' => request()->ip(),
            'details' => auth()->user()->name . " menghapus shift: $name"
        ]);

        return back()->with('success', 'Shift berhasil dihapus.');
    }
}
