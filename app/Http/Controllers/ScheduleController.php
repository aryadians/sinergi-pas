<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Models\Squad;
use App\Models\SquadSchedule;
use App\Models\AuditLog;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        // Get shifts (Pagi, Siang, Malam)
        $shifts = Shift::whereIn('name', ['Pagi', 'Siang', 'Malam'])->orderBy('id')->get();
        if ($shifts->isEmpty()) {
            return back()->with('error', 'Silakan inisialisasi data Shift terlebih dahulu.');
        }

        $squads = Squad::orderBy('name')->get();
        
        $monthStr = $request->input('month', now()->format('Y-m'));
        $month = Carbon::parse($monthStr);
        $daysInMonth = $month->daysInMonth;
        
        // Get all squad schedules for this month
        $schedules = SquadSchedule::with('squad', 'shift')
            ->whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->get()
            ->groupBy(function($item) {
                return $item->date . '_' . $item->shift_id;
            });

        return view('admin.schedules.index', compact(
            'shifts', 'month', 'daysInMonth', 'schedules', 'squads', 'monthStr'
        ));
    }

    public function store(Request $request)
    {
        $request->validate([
            'squad_id' => 'nullable|exists:squads,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
        ]);

        if (!$request->squad_id) {
            SquadSchedule::where('date', $request->date)
                ->where('shift_id', $request->shift_id)
                ->delete();
        } else {
            SquadSchedule::updateOrCreate(
                [
                    'date' => $request->date,
                    'shift_id' => $request->shift_id
                ],
                ['squad_id' => $request->squad_id]
            );
        }

        return response()->json(['success' => true]);
    }

    public function export(Request $request)
    {
        set_time_limit(0);
        $monthStr = $request->month ?? now()->format('Y-m');
        $date = Carbon::parse($monthStr);

        $shifts = Shift::whereIn('name', ['Pagi', 'Siang', 'Malam'])->orderBy('id')->get();
        $daysInMonth = $date->daysInMonth;
        
        $schedules = SquadSchedule::with('squad', 'shift')
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->get()
            ->groupBy(function($item) {
                return $item->date . '_' . $item->shift_id;
            });

        $kop1 = Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAM RI');
        $kop2 = Setting::getValue('kop_line_2', 'LAPAS KELAS IIB JOMBANG');

        $pdf = Pdf::loadView('admin.schedules.pdf-squad', compact(
            'shifts', 'schedules', 'date', 'daysInMonth', 'kop1', 'kop2'
        ))->setPaper('a4', 'landscape');

        return $pdf->download("jadwal-regu-{$monthStr}.pdf");
    }
}
