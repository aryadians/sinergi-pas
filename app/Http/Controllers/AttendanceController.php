<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Shift;
use App\Models\Schedule;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

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

        $monthStr = $request->filled('month') ? $request->month : now()->format('Y-m');
        $date = Carbon::parse($monthStr);
        $query->whereMonth('date', $date->month)->whereYear('date', $date->year);

        $attendances = $query->orderBy('date', 'desc')->paginate(20)->withQueryString();
        
        $summary = [
            'total_present' => Attendance::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('status', 'present')->count(),
            'total_late' => Attendance::whereMonth('date', $date->month)->whereYear('date', $date->year)->where('late_minutes', '>', 0)->count(),
            'total_allowance' => Attendance::whereMonth('date', $date->month)->whereYear('date', $date->year)->sum('allowance_amount'),
        ];

        return view('admin.attendance.index', compact('attendances', 'summary'));
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required']);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();

            // STEP 1: Identify file type explicitly
            $inputFileType = IOFactory::identify($path);
            $reader = IOFactory::createReader($inputFileType);
            
            // Allow reading HTML even if disguised as XLS
            if ($inputFileType === 'Html') {
                $reader->setReadDataOnly(true);
            }
            
            $spreadsheet = $reader->load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) {
                return back()->with('error', 'File terbaca namun tidak ada baris data di dalamnya.');
            }

            // STEP 2: Logic process data
            $importedCount = 0;
            $skippedCount = 0;
            $employees = Employee::all()->keyBy('nip');

            DB::beginTransaction();
            
            // Finding the start of data (skipping headers)
            $startRow = 0;
            foreach ($data as $index => $row) {
                // If column index 4 (NIP) contains a digit or numeric-like string, it's likely our data start
                if (isset($row[4]) && preg_match('/[0-9]/', (string)$row[4])) {
                    $startRow = $index;
                    break;
                }
            }

            for ($i = $startRow; $i < count($data); $i++) {
                $row = $data[$i];
                
                // Column Mapping (Based on machine format):
                // Tanggal scan (0), Tanggal (1), Jam (2), PIN (3), NIP (4)
                if (!isset($row[4]) || empty(trim((string)$row[4]))) continue;

                $nip = trim((string)$row[4]);
                if (!isset($employees[$nip])) {
                    $skippedCount++;
                    continue;
                }

                $emp = $employees[$nip];
                
                try {
                    // Normalize date & time strings
                    $rawDate = trim((string)$row[1]);
                    $rawTime = trim((string)$row[2]);
                    
                    $date = Carbon::parse($rawDate)->format('Y-m-d');
                    $time = Carbon::parse($rawTime)->format('H:i:s');
                } catch (\Exception $e) {
                    continue;
                }

                $attendance = Attendance::firstOrNew(['employee_id' => $emp->id, 'date' => $date]);

                if (!$attendance->exists) {
                    $attendance->check_in = $time;
                    $attendance->check_out = $time;
                    $attendance->status = 'present';
                } else {
                    if ($time < $attendance->check_in) $attendance->check_in = $time;
                    if ($time > $attendance->check_out) $attendance->check_out = $time;
                }

                $this->calculateAttendanceMetrics($attendance, $emp);
                $attendance->save();
                $importedCount++;
            }

            DB::commit();

            AuditLog::create([
                'user_id' => auth()->id(),
                'activity' => 'import_attendance',
                'ip_address' => $request->ip(),
                'details' => "Sinkronisasi fingerprint ($inputFileType): $importedCount data"
            ]);

            if ($importedCount === 0) {
                return back()->with('error', 'Gagal: Tidak ada NIP di file Excel yang cocok dengan data pegawai di sistem.');
            }

            return back()->with('success', "Sinkronisasi Berhasil! $importedCount baris data telah diproses.");

        } catch (\Exception $e) {
            if (isset($db_started)) DB::rollBack();
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage() . ' (Format: ' . ($inputFileType ?? 'Unknown') . ')');
        }
    }

    private function calculateAttendanceMetrics($attendance, $employee)
    {
        $schedule = Schedule::where('employee_id', $employee->id)->where('date', $attendance->date)->first();
        $shift = $schedule ? $schedule->shift : ($employee->employee_type === 'non_regu_jaga' ? Shift::where('name', 'Kantor')->first() : null);

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

        $class = strtoupper((string)$employee->rank_class);
        $rate = 0;
        if (str_contains($class, 'IV')) $rate = Setting::getValue('meal_allowance_iv', 41000);
        elseif (str_contains($class, 'III')) $rate = Setting::getValue('meal_allowance_iii', 37000);
        elseif (str_contains($class, 'II')) $rate = Setting::getValue('meal_allowance_ii', 35000);
        
        $attendance->allowance_amount = $rate;
    }

    public function export(Request $request)
    {
        $monthStr = $request->month ?? now()->format('Y-m');
        $date = Carbon::parse($monthStr);
        $attendances = Attendance::with('employee.work_unit')
            ->whereMonth('date', $date->month)->whereYear('date', $date->year)
            ->orderBy('date', 'asc')->get();

        if ($request->type === 'excel') return $this->exportExcel($attendances, $date);

        $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances', 'date'));
        return $pdf->download("rekap-absensi-{$monthStr}.pdf");
    }

    private function exportExcel($attendances, $date)
    {
        return Excel::download(new class($attendances, $date) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithDrawings, \Maatwebsite\Excel\Concerns\WithCustomStartCell {
            protected $data, $date;
            public function __construct($data, $date) { $this->data = $data; $this->date = $date; }
            public function collection() {
                return $this->data->map(fn($a, $i) => [$i+1, $a->date, $a->employee->full_name, $a->employee->nip, $a->check_in, $a->check_out, $a->status, $a->allowance_amount]);
            }
            public function headings(): array { return ['NO', 'TANGGAL', 'NAMA PEGAWAI', 'NIP', 'MASUK', 'PULANG', 'STATUS', 'UANG MAKAN']; }
            public function startCell(): string { return 'A7'; }
            public function drawings() {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath(public_path('logo1.png'))->setHeight(80)->setCoordinates('A1');
                return $drawing;
            }
            public function styles($sheet) {
                $sheet->mergeCells('B1:H1'); $sheet->setCellValue('B1', Setting::getValue('kop_line_1'));
                $sheet->mergeCells('B2:H2'); $sheet->setCellValue('B2', Setting::getValue('kop_line_2'));
                $sheet->getStyle('B1:B2')->getFont()->setBold(true)->setSize(12);
                $sheet->mergeCells('A5:H5'); $sheet->setCellValue('A5', 'REKAPITULASI ABSENSI PERIODE ' . strtoupper($this->date->translatedFormat('F Y')));
                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
                $sheet->getStyle('A7:H7')->getFont()->setBold(true);
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A7:H$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                return [];
            }
        }, "rekap-absensi-{$date->format('Y-m')}.xlsx");
    }
}
