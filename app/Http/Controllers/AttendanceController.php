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

        $file = $request->file('file');
        $path = $file->getRealPath();

        try {
            $spreadsheet = IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) {
                return back()->with('error', 'File Excel kosong atau format tidak dikenali.');
            }

            array_shift($data); // Remove header

            $importedCount = 0;
            $skippedCount = 0;

            DB::beginTransaction();
            $employees = Employee::all()->keyBy('nip');
            
            foreach ($data as $row) {
                if (empty($row[4])) continue;

                $nip = trim($row[4]);
                if (!isset($employees[$nip])) {
                    $skippedCount++;
                    continue;
                }

                $emp = $employees[$nip];
                $date = Carbon::parse($row[1])->format('Y-m-d');
                $time = Carbon::parse($row[2])->format('H:i:s');

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
                'details' => auth()->user()->name . " mengimpor $importedCount data absensi fingerprint"
            ]);

            // Success Flash Session
            session()->flash('success', "Sinkronisasi Berhasil! $importedCount data diproses, $skippedCount dilewati.");
            return redirect()->route('admin.attendance.index');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage());
        }
    }

    private function calculateAttendanceMetrics($attendance, $employee)
    {
        $schedule = Schedule::where('employee_id', $employee->id)->where('date', $attendance->date)->first();
        
        $shift = null;
        if ($schedule) {
            $shift = $schedule->shift;
        } elseif ($employee->employee_type === 'non_regu_jaga') {
            $shift = Shift::where('name', 'Kantor')->first();
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

        $attendance->allowance_amount = $this->getMealAllowance($employee->rank_class);
    }

    private function getMealAllowance($rankClass)
    {
        $class = strtoupper((string)$rankClass);
        if (str_contains($class, 'IV')) return (float) Setting::getValue('meal_allowance_iv', 41000);
        if (str_contains($class, 'III')) return (float) Setting::getValue('meal_allowance_iii', 37000);
        if (str_contains($class, 'II')) return (float) Setting::getValue('meal_allowance_ii', 35000);
        return 0;
    }

    public function export(Request $request)
    {
        $monthStr = $request->month ?? now()->format('Y-m');
        $date = Carbon::parse($monthStr);
        $type = $request->type ?? 'pdf';

        $attendances = Attendance::with('employee.work_unit')
            ->whereMonth('date', $date->month)
            ->whereYear('date', $date->year)
            ->orderBy('date', 'asc')
            ->get();

        if ($type === 'excel') {
            return $this->exportExcel($attendances, $date);
        }

        $pdf = Pdf::loadView('admin.attendance.pdf', compact('attendances', 'date'));
        return $pdf->download("rekap-absensi-{$monthStr}.pdf");
    }

    private function exportExcel($attendances, $date)
    {
        return Excel::download(new class($attendances, $date) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithDrawings, \Maatwebsite\Excel\Concerns\WithCustomStartCell {
            protected $data;
            protected $date;
            public function __construct($data, $date) { $this->data = $data; $this->date = $date; }
            public function collection() {
                return $this->data->map(function($a, $i) {
                    return [
                        $i + 1,
                        $a->date,
                        $a->employee->full_name,
                        $a->employee->nip,
                        $a->check_in,
                        $a->check_out,
                        $a->status,
                        $a->allowance_amount
                    ];
                });
            }
            public function headings(): array {
                return ['NO', 'TANGGAL', 'NAMA PEGAWAI', 'NIP', 'MASUK', 'PULANG', 'STATUS', 'UANG MAKAN'];
            }
            public function startCell(): string { return 'A7'; }
            public function drawings() {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Logo');
                $drawing->setPath(public_path('logo1.png'));
                $drawing->setHeight(80);
                $drawing->setCoordinates('A1');
                return $drawing;
            }
            public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet) {
                $kop1 = Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAM RI');
                $kop2 = Setting::getValue('kop_line_2', 'LAPAS KELAS IIB JOMBANG');
                $sheet->mergeCells('B1:H1'); $sheet->setCellValue('B1', $kop1);
                $sheet->mergeCells('B2:H2'); $sheet->setCellValue('B2', $kop2);
                $sheet->getStyle('B1:B2')->getFont()->setBold(true)->setSize(12);
                $sheet->mergeCells('A5:H5');
                $sheet->setCellValue('A5', 'REKAPITULASI ABSENSI & UANG MAKAN PERIODE ' . strtoupper($this->date->translatedFormat('F Y')));
                $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
                $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A7:H7')->getFont()->setBold(true);
                $sheet->getStyle('A7:H7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F1F5F9');
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle("A7:H$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                return [];
            }
        }, "rekap-absensi-{$date->format('Y-m')}.xlsx");
    }
}
