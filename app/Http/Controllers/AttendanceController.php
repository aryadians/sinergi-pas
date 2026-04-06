<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $query = Attendance::with('employee.work_unit');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('employee', function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%");
            });
        }

        if ($request->filled('month')) {
            $date = Carbon::parse($request->month);
            $query->whereMonth('date', $date->month)->whereYear('date', $date->year);
        } else {
            $query->whereMonth('date', now()->month)->whereYear('date', now()->year);
        }

        $attendances = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();
        
        // Summary Data for Dashboard
        $summary = [
            'total_present' => Attendance::whereMonth('date', now()->month)->where('status', 'present')->count(),
            'total_late' => Attendance::whereMonth('date', now()->month)->where('late_minutes', '>', 0)->count(),
            'total_allowance' => Attendance::whereMonth('date', now()->month)->sum('allowance_amount'),
        ];

        return view('admin.attendance.index', compact('attendances', 'summary'));
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,xls,csv']);

        $file = $request->file('file');
        $data = Excel::toArray([], $file)[0];

        // Header mapping check
        // Format: Tanggal scan, Tanggal, Jam, PIN, NIP, Nama, ...
        // We assume NIP is at index 4, Date at index 1, Time at index 2
        
        $importedCount = 0;
        $skippedCount = 0;

        // Skip header row
        array_shift($data);

        DB::beginTransaction();
        try {
            foreach ($data as $row) {
                if (empty($row[4])) continue; // Skip if NIP is empty

                $nip = trim($row[4]);
                $dateString = $row[1]; // Tanggal
                $timeString = $row[2]; // Jam

                $employee = Employee::where('nip', $nip)->first();
                if (!$employee) {
                    $skippedCount++;
                    continue;
                }

                $date = Carbon::parse($dateString)->format('Y-m-d');
                $time = Carbon::parse($timeString)->format('H:i:s');

                $attendance = Attendance::firstOrCreate(
                    ['employee_id' => $employee->id, 'date' => $date],
                    ['status' => 'present']
                );

                // Update check-in (earliest time) and check-out (latest time)
                if (!$attendance->check_in || $time < $attendance->check_in) {
                    $attendance->check_in = $time;
                }
                
                if (!$attendance->check_out || $time > $attendance->check_out) {
                    $attendance->check_out = $time;
                }

                // Recalculate late minutes & allowance
                $this->calculateAttendanceMetrics($attendance, $employee);
                $attendance->save();
                
                $importedCount++;
            }
            DB::commit();

            AuditLog::create([
                'user_id' => auth()->id(),
                'activity' => 'import_attendance',
                'ip_address' => $request->ip(),
                'details' => auth()->user()->name . " mengimpor $importedCount data absensi dari mesin fingerprint"
            ]);

            return back()->with('success', "Berhasil mengimpor $importedCount data. (Skipped: $skippedCount)");
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', "Gagal mengimpor data: " . $e->getMessage());
        }
    }

    private function calculateAttendanceMetrics($attendance, $employee)
    {
        // 1. Get Schedule for this date
        $schedule = Schedule::where('employee_id', $employee->id)->where('date', $attendance->date)->first();
        
        // 2. Fallback to default if no schedule
        if (!$schedule) {
            if ($employee->employee_type === 'non_regu_jaga') {
                $shift = Shift::where('name', 'Kantor')->first();
            } else {
                // If regu jaga but no schedule, we can't calculate properly, just set as present
                $attendance->allowance_amount = $this->getMealAllowance($employee->rank_class);
                return;
            }
        } else {
            $shift = $schedule->shift;
        }

        if (!$shift) {
            $attendance->allowance_amount = $this->getMealAllowance($employee->rank_class);
            return;
        }

        // 3. Calculate Lateness
        $startTime = Carbon::parse($shift->start_time);
        $checkIn = Carbon::parse($attendance->check_in);
        
        if ($checkIn->gt($startTime)) {
            $attendance->late_minutes = $checkIn->diffInMinutes($startTime);
            $attendance->status = 'late';
        } else {
            $attendance->late_minutes = 0;
            $attendance->status = 'present';
        }

        // 4. Calculate Meal Allowance
        // Allowance is given if they checked in
        if ($attendance->check_in) {
            $attendance->allowance_amount = $this->getMealAllowance($employee->rank_class);
        }
    }

    private function getMealAllowance($rankClass)
    {
        $class = strtoupper($rankClass);
        if (str_contains($class, 'IV')) {
            return (float) Setting::getValue('meal_allowance_iv', 41000);
        } elseif (str_contains($class, 'III')) {
            return (float) Setting::getValue('meal_allowance_iii', 37000);
        } elseif (str_contains($class, 'II')) {
            return (float) Setting::getValue('meal_allowance_ii', 35000);
        }
        return 0;
    }

    public function export(Request $request)
    {
        // Logic for exporting recap to Excel
        // For now, we'll return back. Implementation will follow in Step 3.
        return back()->with('info', 'Fitur ekspor rekapan sedang disiapkan.');
    }
}
