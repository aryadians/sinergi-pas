<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $employees = Employee::with('work_unit')->orderBy('full_name')->get();
        $shifts = Shift::all();
        
        $month = $request->filled('month') ? Carbon::parse($request->month) : now();
        $daysInMonth = $month->daysInMonth;
        
        $schedules = Schedule::whereMonth('date', $month->month)
            ->whereYear('date', $month->year)
            ->get()
            ->groupBy('employee_id');

        return view('admin.schedules.index', compact('employees', 'shifts', 'month', 'daysInMonth', 'schedules'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|exists:employees,id',
            'shift_id' => 'required|exists:shifts,id',
            'date' => 'required|date',
        ]);

        Schedule::updateOrCreate(
            ['employee_id' => $request->employee_id, 'date' => $request->date],
            ['shift_id' => $request->shift_id]
        );

        return response()->json(['success' => true]);
    }

    public function generateRoster(Request $request)
    {
        $request->validate([
            'regu' => 'required|string',
            'month' => 'required|string',
            'pattern' => 'required|array', // Array of shift IDs: [Pagi, Siang, Malam, Libur]
        ]);

        $month = Carbon::parse($request->month);
        $employees = Employee::where('picket_regu', $request->regu)->get();
        $pattern = $request->pattern;
        $patternCount = count($pattern);

        foreach ($employees as $employee) {
            for ($day = 1; $day <= $month->daysInMonth; $day++) {
                $date = $month->copy()->day($day);
                
                // Simple rotating pattern based on day of month
                $patternIndex = ($day - 1) % $patternCount;
                $shiftId = $pattern[$patternIndex];

                if ($shiftId) { // If not 'Libur' (null/0)
                    Schedule::updateOrCreate(
                        ['employee_id' => $employee->id, 'date' => $date->format('Y-m-d')],
                        ['shift_id' => $shiftId]
                    );
                } else {
                    // If Libur, delete existing schedule for that day
                    Schedule::where('employee_id', $employee->id)->where('date', $date->format('Y-m-d'))->delete();
                }
            }
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'generate_roster',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . " men-generate roster otomatis untuk Regu $request->regu bulan " . $month->format('F Y')
        ]);

        return back()->with('success', "Roster untuk Regu $request->regu berhasil di-generate.");
    }
}
