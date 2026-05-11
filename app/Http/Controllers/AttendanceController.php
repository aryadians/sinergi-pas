<?php
// VERSION 6.0 - DOUBLE SHIFT SUPPORT

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Attendance;
use App\Models\Schedule;
use App\Models\SquadSchedule;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\IOFactory;
use Barryvdh\DomPDF\Facade\Pdf;
use Maatwebsite\Excel\Facades\Excel;

use App\Services\ScheduleService;

class AttendanceController extends Controller
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $search = $request->search;
        
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');
        $monthStr = Carbon::parse($startDate)->format('Y-m');

        $employees = Employee::with(['work_unit', 'squad', 'rank_relation'])
            ->whereHas('user', function($q) { $q->where('role', '!=', 'superadmin'); })
            ->when($search, function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%");
            })
            ->with(['attendances' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }])
            ->orderBy('full_name')
            ->paginate(50)->withQueryString();

        $allFilteredEmployees = Employee::whereHas('user', function($q) { $q->where('role', '!=', 'superadmin'); })
            ->when($search, function($q) use ($search) {
                $q->where('full_name', 'like', "%$search%")
                  ->orWhere('nip', 'like', "%$search%");
            })
            ->with(['attendances' => function($q) use ($startDate, $endDate) {
                $q->whereBetween('date', [$startDate, $endDate]);
            }, 'rank_relation'])
            ->get();

        $totalPresent = 0; $totalValidDays = 0; $totalLate = 0; $totalAllowance = 0;

        foreach ($allFilteredEmployees as $emp) {
            $empRate = (float)($emp->rank_relation->meal_allowance ?? 0);
            $empValidDays = 0; $empTotalAllowance = 0;

            foreach ($emp->attendances as $att) {
                $schedules = $this->scheduleService->getAllSchedulesForDay($emp, $att->date);
                
                if (empty($schedules)) {
                    if ($att->check_in) $totalPresent++;
                    continue;
                }

                $shiftCount = min(2, count($schedules));
                $dayPresentCount = 0;
                $hasNightShift = false;

                for ($i = 0; $i < $shiftCount; $i++) {
                    $sched = $schedules[$i];
                    $isShift2 = ($i === 1);
                    $status = $isShift2 ? $att->status_2 : $att->status;
                    $checkIn = $isShift2 ? $att->check_in_2 : $att->check_in;
                    $checkOut = $isShift2 ? $att->check_out_2 : $att->check_out;
                    $isOff = $sched['is_off'] ?? false;

                    if ($isOff) continue;

                    $canReevaluate = in_array($status, ['absent', 'present', 'late', 'picket']);
                    $effectiveStart = $sched['shift']->start_time ?? null;

                    if (($checkIn || $checkOut) && $effectiveStart && $canReevaluate) {
                        if ($checkIn) {
                            $checkInTime = date('H:i', strtotime($checkIn));
                            $targetInTime = date('H:i', strtotime($effectiveStart));
                            $dateStr = Carbon::parse($att->date)->format('Y-m-d');
                            
                            $actualTs = strtotime($dateStr.' '.$checkInTime);
                            $targetTs = strtotime($dateStr.' '.$targetInTime);
                            $diffMinutes = (int) ceil(($actualTs - $targetTs) / 60);
                            
                            if ($diffMinutes >= -180) {
                                $status = ($diffMinutes > 0) ? 'late' : ($sched['is_picket'] ? 'picket' : 'present');
                            } else {
                                $status = 'absent';
                            }
                        } else {
                            $status = ($sched['is_picket'] ?? false) ? 'picket' : 'present';
                        }
                    } elseif (($checkIn || $checkOut) && !$effectiveStart && $canReevaluate) {
                        $status = 'present';
                    }

                    if ($status !== 'absent') {
                        $dayPresentCount++;
                        if ($status === 'late') $totalLate++;
                        
                        $shiftName = strtoupper($sched['shift']->name ?? '');
                        if (str_contains($shiftName, 'MALAM')) $hasNightShift = true;
                    }
                }

                if ($dayPresentCount > 0) {
                    $totalPresent++;
                    // 2x jika double shift, selain itu 1x
                    $dayMultiplier = ($dayPresentCount > 1) ? 2 : 1;
                    
                    $empValidDays += $dayMultiplier;
                    $empTotalAllowance += ($empRate * $dayMultiplier);
                }
            }
            $totalValidDays += $empValidDays;
            $totalAllowance += $empTotalAllowance;
            
            $paginatedEmp = $employees->getCollection()->where('id', $emp->id)->first();
            if ($paginatedEmp) {
                $paginatedEmp->setAttribute('valid_attendance_count', $empValidDays);
                $paginatedEmp->setAttribute('corrected_total_allowance', $empTotalAllowance);
            }
        }

        $summary = (object)['total_present' => $totalPresent, 'total_valid_days' => $totalValidDays, 'total_late' => $totalLate, 'total_allowance' => $totalAllowance];

        $allEmployees = Employee::whereHas('user', function($q) { $q->where('role', '!=', 'superadmin'); })->orderBy('full_name')->get();

        $attendanceLogs = Attendance::whereHas('employee')->with(['employee.rank_relation'])
            ->whereBetween('date', [$startDate, $endDate])
            ->when($search, function($q) use ($search) {
                $q->whereHas('employee', fn($eq) => $eq->where('full_name', 'like', "%$search%")->orWhere('nip', 'like', "%$search%"));
            })
            ->orderBy('date', 'desc')->orderBy('check_in', 'asc')
            ->paginate(50, ['*'], 'log_page')->withQueryString();

        $attendanceLogs->getCollection()->transform(function($log) {
            $emp = $log->employee;
            $schedules = $this->scheduleService->getAllSchedulesForDay($emp, $log->date);
            $empRate = (float)($emp->rank_relation->meal_allowance ?? 0);
            
            $log->allowance_amount = 0;
            $log->allowance_amount_2 = 0;
            
            if (empty($schedules)) {
                if ($log->check_in) $log->status = 'present';
                if ($log->check_in_2) $log->status_2 = 'present';
                return $log;
            }

            $shiftCount = min(2, count($schedules));
            $dayPresentCount = 0;
            
            for ($i = 0; $i < $shiftCount; $i++) {
                $sched = $schedules[$i];
                $isShift2 = ($i === 1);
                
                $status = $isShift2 ? $log->status_2 : $log->status;
                $checkIn = $isShift2 ? $log->check_in_2 : $log->check_in;
                $checkOut = $isShift2 ? $log->check_out_2 : $log->check_out;
                $isOff = $sched['is_off'] ?? false;
                $effectiveStart = $sched['shift']->start_time ?? null;

                if ($isOff) continue;

                $canReevaluate = in_array($status, ['absent', 'present', 'late', 'picket']);
                
                if (($checkIn || $checkOut) && $effectiveStart && $canReevaluate) {
                    if ($checkIn) {
                        $checkInTime = date('H:i', strtotime($checkIn));
                        $targetInTime = date('H:i', strtotime($effectiveStart));
                        
                        $dateStr = Carbon::parse($log->date)->format('Y-m-d');
                        $actualTs = strtotime($dateStr.' '.$checkInTime);
                        $targetTs = strtotime($dateStr.' '.$targetInTime);
                        $diffMinutes = (int) ceil(($actualTs - $targetTs) / 60);

                        if ($diffMinutes >= -180) {
                            if ($diffMinutes > 0) {
                                $status = 'late';
                                if ($isShift2) $log->late_minutes_2 = $diffMinutes;
                                else $log->late_minutes = $diffMinutes;
                            } else {
                                $status = ($sched['is_picket'] ?? false) ? 'picket' : 'present';
                                if ($isShift2) $log->late_minutes_2 = 0;
                                else $log->late_minutes = 0;
                            }
                        } else {
                            $status = 'absent';
                        }
                    } else {
                        // Jika tidak ada check_in tapi ada check_out, anggap hadir
                        $status = ($sched['is_picket'] ? 'picket' : 'present');
                        if ($isShift2) $log->late_minutes_2 = 0;
                        else $log->late_minutes = 0;
                    }
                } elseif (($checkIn || $checkOut) && !$effectiveStart && $canReevaluate) {
                    $status = 'present';
                    if ($isShift2) $log->late_minutes_2 = 0;
                    else $log->late_minutes = 0;
                }

                $hasMeal = !in_array($status, ['absent', 'duty_full', 'tubel', 'on_leave', 'sick']);
                if ($hasMeal) {
                    $dayPresentCount++;
                }
            }
            
            // Total allowance for the day
            $dayMultiplier = ($dayPresentCount > 1) ? 2 : 1;
            $totalDailyAllowance = $empRate * $dayMultiplier;

            // Assign allowance to first log entry only
            if ($dayPresentCount > 0) {
                $log->allowance_amount = $totalDailyAllowance;
                $log->allowance_amount_2 = 0;
            } else {
                $log->allowance_amount = 0;
                $log->allowance_amount_2 = 0;
            }
            
            return $log;
        });

        $maxLateCount = (int)\App\Models\Setting::getValue('payroll_max_late_count', 8);
        $rangeTitle = Carbon::parse($startDate)->translatedFormat('d M') . ' - ' . Carbon::parse($endDate)->translatedFormat('d M Y');

        return view('admin.attendance.index', compact('employees', 'allEmployees', 'attendanceLogs', 'summary', 'startDate', 'endDate', 'rangeTitle', 'monthStr', 'maxLateCount'));
    }

    public function import(Request $request)
    {
        set_time_limit(0);
        ini_set('memory_limit', '1024M');
        $request->validate(['file' => 'required']);

        try {
            $file = $request->file('file');
            $path = $file->getRealPath();
            $spreadsheet = IOFactory::load($path);
            $data = $spreadsheet->getActiveSheet()->toArray();

            if (count($data) < 2) return back()->with('error', 'File terbaca namun kosong.');

            $scansByNip = []; $allDates = [];
            $map = ['nip' => 4, 'date' => 1, 'time' => 2, 'datetime' => 0];
            $dataStarted = false;

            foreach ($data as $index => $row) {
                if (!$dataStarted) {
                    $rowNormalized = array_map(fn($v) => strtoupper(str_replace(' ', '', trim((string)$v))), $row);
                    if (in_array('NIP', $rowNormalized) || in_array('TANGGALSCAN', $rowNormalized)) {
                        foreach ($rowNormalized as $colIdx => $h) {
                            if ($h === 'NIP') $map['nip'] = $colIdx;
                            if ($h === 'TANGGAL' || $h === 'TANGGALSCAN') $map['date'] = $colIdx;
                            if ($h === 'JAM') $map['time'] = $colIdx;
                            if ($h === 'TANGGALSCAN') $map['datetime'] = $colIdx;
                        }
                        $dataStarted = true; continue;
                    }
                    if ($index >= 10) $dataStarted = true; else continue;
                }
                $nipRaw = trim((string)($row[$map['nip']] ?? ''));
                if (empty($nipRaw)) continue;
                $nip = preg_replace('/[^0-9]/', '', $nipRaw);
                if (empty($nip)) continue;

                try {
                    $d = trim((string)($row[$map['date']] ?? ''));
                    $t = trim((string)($row[$map['time']] ?? ''));
                    
                    // Gunakan format DD-MM-YYYY untuk tanggal jika perlu
                    $scanTime = Carbon::createFromFormat('d-m-Y H:i:s', $d . ' ' . $t);
                    
                    $scansByNip[$nip][] = $scanTime;
                    $allDates[] = $scanTime->format('Y-m-d');
                } catch (\Exception $e) { 
                    try {
                        // Fallback jika format berbeda
                        $scanTime = Carbon::parse($d . ' ' . $t);
                        $scansByNip[$nip][] = $scanTime;
                        $allDates[] = $scanTime->format('Y-m-d');
                    } catch (\Exception $e2) { continue; }
                }
            }

            if (empty($scansByNip)) return back()->with('error', 'Gagal membaca data scan.');

            $excelNips = array_keys($scansByNip);
            $employees = Employee::with(['rank_relation', 'squad'])->whereIn('nip', $excelNips)->get()->keyBy('nip');
            
            if ($employees->isEmpty()) return back()->with('error', 'NIP di Excel tidak cocok dengan Database.');

            $minDate = collect($allDates)->min(); $maxDate = collect($allDates)->max();
            $empIds = $employees->pluck('id')->toArray();

            Attendance::whereIn('employee_id', $empIds)->whereBetween('date', [$minDate, $maxDate])->delete();

            $now = now(); $insertData = []; $importedCount = 0;

            foreach ($employees as $dbNip => $emp) {
                $excelKey = null;
                foreach (array_keys($scansByNip) as $k) { if (str_ends_with($dbNip, $k) || str_ends_with($k, $dbNip)) { $excelKey = $k; break; } }
                if (!$excelKey) continue;

                $allScans = collect($scansByNip[$excelKey])->sort()->values();
                $dates = $allScans->map(fn($s) => $s->format('Y-m-d'))->unique()->values();
                $usedScans = [];

                foreach ($dates as $date) {
                    $schedules = $this->scheduleService->getAllSchedulesForDay($emp, $date);
                    
                    $attData = [
                        'employee_id' => $emp->id,
                        'date' => $date,
                        'check_in' => null,
                        'check_out' => null,
                        'status' => 'absent',
                        'late_minutes' => 0,
                        'early_minutes' => 0,
                        'allowance_amount' => 0,
                        'check_in_2' => null,
                        'check_out_2' => null,
                        'status_2' => 'absent',
                        'late_minutes_2' => 0,
                        'early_minutes_2' => 0,
                        'allowance_amount_2' => 0,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    $empRate = (float)($emp->rank_relation->meal_allowance ?? 0);

                    if (empty($schedules)) {
                        // Tidak ada jadwal resmi, ambil sisa scan di hari itu
                        $dayScans = $allScans->filter(fn($s, $idx) => $s->format('Y-m-d') === $date && !in_array($idx, $usedScans))->values();
                        if ($dayScans->count() > 0) {
                            $attData['check_in'] = $dayScans->first()->format('H:i:s');
                            if ($dayScans->count() > 1) $attData['check_out'] = $dayScans->last()->format('H:i:s');
                            $attData['status'] = 'present';
                        }
                    } else {
                        // Pencocokan pintar scan ke shift
                        $shiftCount = min(2, count($schedules));
                        
                        if (isset($schedules[0]) && $schedules[0]['is_off']) $attData['status'] = $schedules[0]['status'];
                        if (isset($schedules[1]) && $schedules[1]['is_off']) $attData['status_2'] = $schedules[1]['status'];

                        $targets = [];
                        for ($i = 0; $i < $shiftCount; $i++) {
                            if ($schedules[$i]['is_off']) continue;
                            $st = Carbon::parse($date . ' ' . $schedules[$i]['shift']->start_time);
                            $et = Carbon::parse($date . ' ' . $schedules[$i]['shift']->end_time);
                            if ($et <= $st) $et->addDay(); // Shift melintasi hari
                            
                            $targets[] = ['shift' => $i, 'type' => 'in', 'time' => $st];
                            $targets[] = ['shift' => $i, 'type' => 'out', 'time' => $et];
                        }

                        $matches = [];
                        foreach ($targets as $eIdx => $target) {
                            foreach ($allScans as $sIdx => $scan) {
                                if (in_array($sIdx, $usedScans)) continue;
                                $diffMins = ($scan->timestamp - $target['time']->timestamp) / 60;
                                
                                // Toleransi Masuk: 4 jam lebih awal s/d 4 jam telat
                                if ($target['type'] === 'in' && ($diffMins < -240 || $diffMins > 240)) continue;
                                // Toleransi Pulang: 4 jam lebih awal s/d 8 jam telat
                                if ($target['type'] === 'out' && ($diffMins < -240 || $diffMins > 480)) continue;
                                
                                $matches[] = [
                                    'eIdx' => $eIdx,
                                    'sIdx' => $sIdx,
                                    'absDiff' => abs($diffMins),
                                    'diff' => $diffMins
                                ];
                            }
                        }

                        // Mengurutkan berdasarkan selisih waktu terkecil (paling cocok)
                        usort($matches, function($a, $b) {
                            return $a['absDiff'] <=> $b['absDiff'];
                        });

                        $matchedEvents = [];
                        $matchedScans = [];

                        foreach ($matches as $match) {
                            if (isset($matchedEvents[$match['eIdx']]) || isset($matchedScans[$match['sIdx']])) continue;
                            
                            $matchedEvents[$match['eIdx']] = [
                                'scan' => $allScans[$match['sIdx']],
                                'diff' => $match['diff']
                            ];
                            $matchedScans[$match['sIdx']] = true;
                            $usedScans[] = $match['sIdx'];
                        }

                        for ($i = 0; $i < $shiftCount; $i++) {
                            $sched = $schedules[$i];
                            if ($sched['is_off']) continue;

                            $isShift2 = ($i === 1);
                            $assignedCheckIn = null;
                            $assignedCheckOut = null;
                            $late = 0;
                            
                            foreach ($targets as $eIdx => $target) {
                                if ($target['shift'] === $i && isset($matchedEvents[$eIdx])) {
                                    if ($target['type'] === 'in') {
                                        $assignedCheckIn = $matchedEvents[$eIdx]['scan'];
                                        $late = $matchedEvents[$eIdx]['diff'] > 0 ? (int)$matchedEvents[$eIdx]['diff'] : 0;
                                    } elseif ($target['type'] === 'out') {
                                        $assignedCheckOut = $matchedEvents[$eIdx]['scan'];
                                    }
                                }
                            }
                            
                            $status = 'absent';
                            $allowance = 0;

                            if ($assignedCheckIn) {
                                if ($late > 0) {
                                    $status = 'late';
                                } else {
                                    $status = $sched['is_picket'] ? 'picket' : 'present';
                                }
                                
                                $shiftName = strtoupper($sched['shift']->name ?? '');
                                $isNightShift = str_contains($shiftName, 'MALAM');
                                $mealMultiplier = $isNightShift ? 2 : 1;
                                
                                // Jika ada assignedCheckIn (berhasil match), berikan uang makan.
                                // Logika match di atas sudah punya filter toleransi -240 (4 jam sebelum).
                                // Jadi jika 04:05 masuk jadwal 07:30 (selisih ~205m), dia lolos match dan lolos allowance.
                                $allowance = $empRate * $mealMultiplier;
                            }

                            if ($isShift2) {
                                $attData['check_in_2'] = $assignedCheckIn ? $assignedCheckIn->format('H:i:s') : null;
                                $attData['check_out_2'] = $assignedCheckOut ? $assignedCheckOut->format('H:i:s') : null;
                                $attData['status_2'] = $status;
                                $attData['late_minutes_2'] = $late;
                                $attData['allowance_amount_2'] = $allowance;
                            } else {
                                $attData['check_in'] = $assignedCheckIn ? $assignedCheckIn->format('H:i:s') : null;
                                $attData['check_out'] = $assignedCheckOut ? $assignedCheckOut->format('H:i:s') : null;
                                $attData['status'] = $status;
                                $attData['late_minutes'] = $late;
                                $attData['allowance_amount'] = $allowance;
                            }
                        }
                    }

                    $insertData[] = $attData;
                    $importedCount++;
                }
            }
            if (!empty($insertData)) { foreach (array_chunk($insertData, 500) as $chunk) { Attendance::insert($chunk); } }
            AuditLog::create(['user_id' => auth()->id(), 'activity' => 'import_attendance', 'ip_address' => $request->ip(), 'details' => "Import Replace periode $minDate - $maxDate. Total $importedCount data."]);
            return back()->with('success', "Berhasil mereplace $importedCount data absensi.");
        } catch (\Exception $e) { return back()->with('error', 'Gagal: ' . $e->getMessage()); }
    }

    public function storeManual(Request $request)
    {
        $request->validate([
            'log_id' => 'required|exists:attendances,id',
            'status' => 'required|string',
            'status_2' => 'required|string',
        ]);

        $log = Attendance::with('employee.rank_relation')->findOrFail($request->log_id);
        $empRate = (float)($log->employee->rank_relation->meal_allowance ?? 0);
        
        $eligibleStatuses = ['present', 'late', 'picket'];
        
        // Multiplier check (Shift Malam)
        $multiplier1 = 1;
        $multiplier2 = 1;
        
        // Deteksi shift malam dari jam check-in manual jika ada
        if ($request->check_in) {
            $hour = (int)explode(':', $request->check_in)[0];
            if ($hour >= 18 || $hour < 5) $multiplier1 = 2;
        }
        if ($request->check_in_2) {
            $hour = (int)explode(':', $request->check_in_2)[0];
            if ($hour >= 18 || $hour < 5) $multiplier2 = 2;
        }

        $allowance1 = in_array($request->status, $eligibleStatuses) ? ($empRate * $multiplier1) : 0;
        $allowance2 = in_array($request->status_2, $eligibleStatuses) ? ($empRate * $multiplier2) : 0;

        // Cap 2x rate
        if (($allowance1 + $allowance2) > ($empRate * 2)) {
            $allowance2 = ($empRate * 2) - $allowance1;
        }

        $log->update([
            'check_in' => $request->check_in,
            'check_out' => $request->check_out,
            'check_in_2' => $request->check_in_2,
            'check_out_2' => $request->check_out_2,
            'status' => $request->status,
            'status_2' => $request->status_2,
            'allowance_amount' => $allowance1,
            'allowance_amount_2' => $allowance2,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'update_attendance_manual',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . " mengoreksi absensi pegawai secara manual untuk tanggal " . $log->date->format('Y-m-d'),
        ]);

        return back()->with('success', 'Koreksi absensi berhasil disimpan.');
    }

    public function export(Request $request)
    {
        set_time_limit(600);
        ini_set('memory_limit', '2048M');
        $filter = $request->filter ?? 'range'; $type = $request->type ?? 'pdf';
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');
        
        $query = Employee::whereHas('user', function($q) { $q->where('role', '!=', 'superadmin'); })->orderBy('full_name');
        if ($request->filled('employee_id')) $query->where('id', $request->employee_id);
        if ($request->filled('work_unit_id')) $query->where('work_unit_id', $request->work_unit_id);

        if ($filter === 'daily') {
            $exactDate = Carbon::parse($request->exact_date ?? now()); $dateStr = $exactDate->format('Y-m-d');
            $employees = $query->with('rank_relation')->get();
            $attendances = Attendance::whereDate('date', $exactDate)->get()->keyBy('employee_id');
            $data = $employees->map(function($emp) use ($attendances, $dateStr) {
                $att = $attendances->get($emp->id);
                // Simplify sum of allowances for daily PDF
                $allowance = 0;
                if ($att) {
                    $allowance = $att->allowance_amount + $att->allowance_amount_2;
                }
                return (object)['employee' => $emp, 'check_in' => $att?->check_in, 'check_out' => $att?->check_out, 'status' => $att?->status ?? 'absent', 'late_minutes' => $att?->late_minutes ?? 0, 'allowance_amount' => $allowance];
            });
            $reportTitle = "ABSENSI HARIAN - " . strtoupper($exactDate->translatedFormat('d F Y'));
            if ($type === 'excel') return $this->exportExcelDaily($data, $reportTitle, "absensi-harian-{$dateStr}.xlsx");
            if (ob_get_length()) ob_end_clean();
            return Pdf::loadView('admin.attendance.pdf-daily', ['logs' => $data, 'reportTitle' => $reportTitle, 'date' => $dateStr])->setPaper('a4', 'landscape')->download("absensi-harian-{$dateStr}.pdf");
        } elseif ($filter === 'range' || $filter === 'weekly' || $filter === 'monthly') {
            $start = Carbon::parse($startDate); $end = Carbon::parse($endDate);
            $employees = $query->with('rank_relation')->get();
            $attendances = Attendance::whereBetween('date', [$startDate, $endDate])->get()->groupBy('employee_id');
            $data = $employees->map(function($emp) use ($attendances) {
                $atts = $attendances->get($emp->id) ?? collect();
                
                $presentCount = 0;
                $lateCount = 0;
                $totalAllowance = 0;
                
                foreach ($atts as $att) {
                    if (!in_array($att->status, ['absent'])) $presentCount++;
                    if (!in_array($att->status_2, ['absent']) && $att->check_in_2) $presentCount++;
                    
                    if ($att->status === 'late') $lateCount++;
                    if ($att->status_2 === 'late') $lateCount++;
                    
                    $totalAllowance += $att->allowance_amount + $att->allowance_amount_2;
                }
                
                return (object)['full_name' => strtoupper($emp->full_name), 'nip' => $emp->nip, 'present_count' => $presentCount, 'late_count' => $lateCount, 'total_allowance' => $totalAllowance];
            });
            $reportTitle = "REKAP ABSENSI (" . $start->format('d/m/Y') . " - " . $end->format('d/m/Y') . ")";
            if ($type === 'excel') return $this->exportExcelMonthly($data, $reportTitle, "rekap-absensi.xlsx");
            if (ob_get_length()) ob_end_clean();
            return Pdf::loadView('admin.attendance.pdf-monthly', ['logs' => $data, 'reportTitle' => $reportTitle, 'startDate' => $startDate, 'endDate' => $endDate])->setPaper('a4', 'landscape')->download("rekap-absensi.pdf");
        }
    }

    private function exportExcelIndividual($emp, $logs, $title, $filename)
    {
        return Excel::download(new class($emp, $logs, $title) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithDrawings, \Maatwebsite\Excel\Concerns\WithCustomStartCell {
            protected $emp, $logs, $title;
            public function __construct($e, $l, $t) { $this->emp = $e; $this->logs = $l; $this->title = $t; }
            public function collection() {
                return $this->logs->map(fn($log, $i) => [
                    $i+1, Carbon::parse($log->date)->format('d/m/Y'),
                    $log->check_in ?? '--:--', $log->check_out ?? '--:--',
                    strtoupper($log->status), $log->late_minutes . 'm', $log->allowance_amount + $log->allowance_amount_2
                ]);
            }
            public function headings(): array { return ['NO', 'TANGGAL', 'MASUK', 'PULANG', 'STATUS', 'TELAT', 'UANG MAKAN']; }
            public function startCell(): string { return 'A7'; }
            public function drawings() {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath(public_path('logo1.png'))->setHeight(80)->setCoordinates('A1');
                return $drawing;
            }
            public function styles($sheet) {
                $sheet->mergeCells('B1:G1'); $sheet->setCellValue('B1', Setting::getValue('kop_line_1'));
                $sheet->mergeCells('B2:G2'); $sheet->setCellValue('B2', Setting::getValue('kop_line_2'));
                $sheet->mergeCells('A5:G5'); $sheet->setCellValue('A5', $this->title);
                $sheet->getStyle('A7:G7')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F172A']]]);
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) $sheet->getStyle("A7:G$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                foreach (range('A', 'G') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
                return [];
            }
        }, $filename);
    }

    private function exportExcelDaily($data, $title, $filename)
    {
        return Excel::download(new class($data, $title) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithDrawings, \Maatwebsite\Excel\Concerns\WithCustomStartCell {
            protected $data, $title;
            public function __construct($data, $title) { $this->data = $data; $this->title = $title; }
            public function collection() {
                return $this->data->map(fn($item, $i) => [$i+1, $item->employee->full_name, "'" . $item->employee->nip, $item->check_in, $item->check_out, strtoupper($item->status), $item->allowance_amount]);
            }
            public function headings(): array { return ['NO', 'NAMA PEGAWAI', 'NIP', 'MASUK', 'PULANG', 'STATUS', 'UANG MAKAN']; }
            public function startCell(): string { return 'A7'; }
            public function drawings() {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath(public_path('logo1.png'))->setHeight(80)->setCoordinates('A1');
                return $drawing;
            }
            public function styles($sheet) {
                $sheet->mergeCells('B1:H1'); $sheet->setCellValue('B1', Setting::getValue('kop_line_1'));
                $sheet->mergeCells('B2:H2'); $sheet->setCellValue('B2', Setting::getValue('kop_line_2'));
                $sheet->mergeCells('A5:H5'); $sheet->setCellValue('A5', $this->title);
                $sheet->getStyle('A7:H7')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F172A']]]);
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) $sheet->getStyle("A7:H$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                foreach (range('A', 'H') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
                return [];
            }
        }, $filename);
    }

    private function exportExcelMonthly($data, $title, $filename)
    {
        return Excel::download(new class($data, $title) implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithDrawings, \Maatwebsite\Excel\Concerns\WithCustomStartCell {
            protected $data, $title;
            public function __construct($data, $title) { $this->data = $data; $this->title = $title; }
            public function collection() {
                return $this->data->map(fn($item, $i) => [$i+1, $item->full_name, "'" . $item->nip, $item->present_count, $item->late_count, $item->total_allowance]);
            }
            public function headings(): array { return ['NO', 'NAMA PEGAWAI', 'NIP', 'HADIR', 'TELAT', 'TOTAL UANG MAKAN']; }
            public function startCell(): string { return 'A7'; }
            public function drawings() {
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setPath(public_path('logo1.png'))->setHeight(80)->setCoordinates('A1');
                return $drawing;
            }
            public function styles($sheet) {
                $sheet->mergeCells('B1:F1'); $sheet->setCellValue('B1', Setting::getValue('kop_line_1'));
                $sheet->mergeCells('B2:F2'); $sheet->setCellValue('B2', Setting::getValue('kop_line_2'));
                $sheet->mergeCells('A5:F5'); $sheet->setCellValue('A5', $this->title);
                $sheet->getStyle('A7:F7')->applyFromArray(['font'=>['bold'=>true,'color'=>['rgb'=>'FFFFFF']],'fill'=>['fillType'=>\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,'startColor'=>['rgb'=>'0F172A']]]);
                $lastRow = $sheet->getHighestRow();
                if ($lastRow >= 7) $sheet->getStyle("A7:F$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
                foreach (range('A', 'F') as $col) $sheet->getColumnDimension($col)->setAutoSize(true);
                return [];
            }
        }, $filename);
    }
}
