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
use PhpOffice\PhpSpreadsheet\IOFactory;

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
        
        $summary = [
            'total_present' => Attendance::whereMonth('date', now()->month)->where('status', 'present')->count(),
            'total_late' => Attendance::whereMonth('date', now()->month)->where('late_minutes', '>', 0)->count(),
            'total_allowance' => Attendance::whereMonth('date', now()->month)->sum('allowance_amount'),
        ];

        return view('admin.attendance.index', compact('attendances', $summary ? 'summary' : []));
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required']);

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            // Force load the file even if it has wrong extension (common in fingerprint machines)
            $spreadsheet = IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) {
                return back()->with('error', 'File Excel kosong atau format tidak dikenali.');
            }

            // Remove header
            array_shift($data);

            $importedCount = 0;
            $skippedCount = 0;
            $processedNips = [];

            DB::beginTransaction();
            
            // Pre-load employees to speed up lookup
            $employees = Employee::all()->keyBy('nip');
            
            foreach ($data as $row) {
                // Header Mapping: NIP at index 4, Date at index 1, Time at index 2
                if (empty($row[4])) continue;

                $nip = trim($row[4]);
                if (!isset($employees[$nip])) {
                    $skippedCount++;
                    continue;
                }

                $emp = $employees[$nip];
                $date = Carbon::parse($row[1])->format('Y-m-d');
                $time = Carbon::parse($row[2])->format('H:i:s');

                // Logic: Find or create record for this employee on this date
                $attendance = Attendance::firstOrNew(['employee_id' => $emp->id, 'date' => $date]);

                // Set earliest time as check_in, latest as check_out
                if (!$attendance->exists) {
                    $attendance->check_in = $time;
                    $attendance->check_out = $time;
                    $attendance->status = 'present';
                } else {
                    if ($time < $attendance->check_in) $attendance->check_in = $time;
                    if ($time > $attendance->check_out) $attendance->check_out = $time;
                }

                // Initial calculation
                $this->calculateAttendanceMetrics($attendance, $emp);
                $attendance->save();
                
                $importedCount++;
            }

            DB::commit();

            AuditLog::create([
                'user_id' => auth()->id(),
                'activity' => 'import_attendance',
                'ip_address' => $request->ip(),
                'details' => auth()->user()->name . " mengimpor $importedCount data absensi"
            ]);

            return back()->with('success', "Sinkronisasi Selesai! $importedCount data berhasil diproses.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Format file tidak didukung: ' . $e->getMessage());
        }
    }

    private function calculateAttendanceMetrics($attendance, $employee)
    {
        $schedule = Schedule::where('employee_id', $employee->id)->where('date', $attendance->date)->first();
        
        if (!$schedule) {
            $shift = ($employee->employee_type === 'non_regu_jaga') 
                ? Shift::where('name', 'Kantor')->first() 
                : null;
        } else {
            $shift = $schedule->shift;
        }

        if ($shift) {
            $startTime = Carbon::parse($shift->start_time);
            $checkIn = Carbon::parse($attendance->check_in);
            
            if ($checkIn->gt($startTime)) {
                $attendance->late_minutes = $checkIn->diffInMinutes($startTime);
                $attendance->status = 'late';
            } else {
                $attendance->late_minutes = 0;
                $attendance->status = 'present';
            }
        }

        // Meal Allowance
        $attendance->allowance_amount = $this->getMealAllowance($employee->rank_class);
    }

    private function getMealAllowance($rankClass)
    {
        $class = strtoupper($rankClass);
        if (str_contains($class, 'IV')) return (float) Setting::getValue('meal_allowance_iv', 41000);
        if (str_contains($class, 'III')) return (float) Setting::getValue('meal_allowance_iii', 37000);
        if (str_contains($class, 'II')) return (float) Setting::getValue('meal_allowance_ii', 35000);
        return 0;
    }

    public function export(Request $request)
    {
        return back()->with('info', 'Fitur ekspor rekapan sedang disiapkan.');
    }
}
