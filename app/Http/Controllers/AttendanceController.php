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

            // Robust reader detection
            $spreadsheet = null;
            $readers = ['Xlsx', 'Xls', 'Html', 'Csv'];
            
            foreach ($readers as $readerName) {
                try {
                    $reader = IOFactory::createReader($readerName);
                    if ($reader->canRead($path)) {
                        $spreadsheet = $reader->load($path);
                        break;
                    }
                } catch (\Exception $e) {
                    continue;
                }
            }

            if (!$spreadsheet) {
                // Last ditch effort: Try auto-detection again with different approach
                $spreadsheet = IOFactory::load($path);
            }

            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) return back()->with('error', 'File Excel kosong atau format tidak didukung.');

            $importedCount = 0;
            $employees = Employee::all()->keyBy('nip');

            DB::beginTransaction();
            
            foreach ($data as $index => $row) {
                // Skip header logic: Look for NIP at index 4
                if ($index === 0 || !isset($row[4]) || !is_numeric($row[4])) {
                    if (isset($row[4]) && strtolower((string)$row[4]) === 'nip') continue;
                    if ($index < 5) continue; // Safety skip for some machine formats with long headers
                }

                $nip = trim((string)$row[4]);
                if (empty($nip) || !isset($employees[$nip])) continue;

                $emp = $employees[$nip];
                
                try {
                    $date = Carbon::parse($row[1])->format('Y-m-d');
                    $time = Carbon::parse($row[2])->format('H:i:s');
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
                'details' => "Sinkronisasi fingerprint: $importedCount data"
            ]);

            return back()->with('success', "Berhasil! $importedCount data absensi telah disinkronkan.");

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Gagal memproses file: ' . $e->getMessage());
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
