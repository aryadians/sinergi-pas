<?php
// VERSION 6.0 - DOUBLE SHIFT SUPPORT

namespace App\Services;

use App\Models\Attendance;
use App\Models\Employee;
use App\Models\Setting;
use Carbon\Carbon;
use App\Services\ScheduleService;

class PayrollService
{
    protected $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Menghitung rincian Tukin dan Uang Makan untuk satu pegawai dalam satu bulan.
     * Mendukung Double Shift (2 jadwal sehari).
     */
    public function calculateMonthlyPayroll(Employee $employee, $monthStr)
    {
        $date = Carbon::parse($monthStr . '-01');
        $startDate = $date->copy()->startOfMonth()->format('Y-m-d');
        $endDate = $date->copy()->endOfMonth()->format('Y-m-d');
        $daysInMonth = $date->daysInMonth;

        $allSettings = Setting::where('key', 'like', 'payroll_%')->get()->pluck('value', 'key');
        
        $rules = [
            'tl_1' => (float)($allSettings['payroll_tl_1_percent'] ?? 0.5),
            'tl_2' => (float)($allSettings['payroll_tl_2_percent'] ?? 1.0),
            'tl_3' => (float)($allSettings['payroll_tl_3_percent'] ?? 1.25),
            'tl_4' => (float)($allSettings['payroll_tl_4_percent'] ?? 1.5),
            'max_late' => (int)($allSettings['payroll_max_late_count'] ?? 8),
            'mangkir' => (float)($allSettings['payroll_mangkir_percent'] ?? 5.0),
            'lupa_absen' => (float)($allSettings['payroll_lupa_absen_percent'] ?? 1.5),
            'sakit_3_6' => (float)($allSettings['payroll_sakit_3_6_percent'] ?? 2.5),
            'sakit_7' => (float)($allSettings['payroll_sakit_7_plus_percent'] ?? 10.0),
        ];
        
        $attendances = Attendance::where('employee_id', $employee->id)
            ->whereBetween('date', [$startDate, $endDate])
            ->get()
            ->keyBy(fn($item) => Carbon::parse($item->date)->format('Y-m-d'));

        $stats = [
            'total_present' => 0,
            'late_count' => 0,
            'compensation_count' => 0,
            'deduction_percentage' => 0.0,
            'meal_allowance_days' => 0,
            'details' => [],
            'processed_logs' => [],
            'violation_note' => null,
            'is_tubel' => (bool)$employee->is_tubel,
            'is_cpns' => (bool)$employee->is_cpns,
            'is_acting' => false
        ];
        
        $sickCounter = 0;
        $baseTunkin = (float)($employee->tunkin->nominal ?? 0);
        if ($employee->is_cpns) $baseTunkin = 0.8 * $baseTunkin;

        $actingBonus = 0;
        if ($employee->acting_tunkin_id && $employee->acting_start_date) {
            $startDateObj = Carbon::parse($employee->acting_start_date);
            if ($date->diffInMonths($startDateObj) >= 1) {
                $actingTunkin = $employee->actingTunkin->nominal ?? 0;
                $actingBonus = 0.2 * $actingTunkin;
                $stats['is_acting'] = true;
            }
        }

        if ($employee->is_tubel) {
            $stats['deduction_percentage'] = 100;
            $stats['details'][] = ['type' => 'Tugas Belajar', 'info' => 'Potong 100%', 'date' => null, 'percent' => 100, 'rupiah' => $baseTunkin];
            $baseTunkin = 0;
            $actingBonus = 0;
        }

        $mealRate = (float)($employee->rank_relation->meal_allowance ?? 0);
        $today = now()->startOfDay();

        for ($d = 1; $d <= $daysInMonth; $d++) {
            $currentDateObj = $date->copy()->day($d);
            $currentDate = $currentDateObj->format('Y-m-d');
            $isFuture = $currentDateObj->isAfter($today);

            if ($isFuture) {
                continue; // Do not process or log future dates
            }
            
            $attendance = $attendances->get($currentDate);
            $schedules = $this->scheduleService->getAllSchedulesForDay($employee, $currentDate);

            // Jika tidak ada jadwal, cek kalau dia absen (lembur/masuk libur)
            if (empty($schedules)) {
                if ($attendance && $attendance->check_in) {
                    $stats['processed_logs'][] = [
                        'date' => $currentDate, 
                        'status' => 'present', 
                        'check_in' => is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i:s'), 
                        'check_out' => $attendance->check_out ? (is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i:s')) : '--:--', 
                        'is_scheduled' => false, 
                        'meal_amount' => 0 // Umumnya tidak ada uang makan kalau tidak ada jadwal resmi
                    ];
                }
                continue;
            }

            // Loop untuk maksimal 2 jadwal (karena sistem support 2 shift)
            $shiftCount = min(2, count($schedules));
            $dayPresentCount = 0;
            $hasNightShiftPresence = false;
            
            $dayShiftResults = [];

            for ($i = 0; $i < $shiftCount; $i++) {
                $sched = $schedules[$i];
                $isShift2 = ($i === 1);
                
                $status = $sched['status'] ?? 'present';
                $isOff = $sched['is_off'] ?? false;
                $scheduledInTime = $sched['shift']->start_time ?? null;
                $scheduledOutTime = $sched['shift']->end_time ?? null;
                $shiftName = $sched['shift']->name ?? 'Unknown';
                $isNightShift = str_contains(strtoupper($shiftName), 'MALAM');
                $isDefaultOffice = ($sched['type'] ?? '') === 'office';

                // Ambil data check_in / check_out sesuai slot
                $checkInStr = null; $checkOutStr = null;
                if ($attendance) {
                    if ($isShift2) {
                        $checkInStr = $attendance->check_in_2 ? (is_string($attendance->check_in_2) ? $attendance->check_in_2 : $attendance->check_in_2->format('H:i:s')) : null;
                        $checkOutStr = $attendance->check_out_2 ? (is_string($attendance->check_out_2) ? $attendance->check_out_2 : $attendance->check_out_2->format('H:i:s')) : null;
                        $status = $attendance->status_2 !== 'absent' ? $attendance->status_2 : $status;
                    } else {
                        $checkInStr = $attendance->check_in ? (is_string($attendance->check_in) ? $attendance->check_in : $attendance->check_in->format('H:i:s')) : null;
                        $checkOutStr = $attendance->check_out ? (is_string($attendance->check_out) ? $attendance->check_out : $attendance->check_out->format('H:i:s')) : null;
                        $status = $attendance->status !== 'absent' ? $attendance->status : $status;
                    }
                }

                if ($isOff) {
                    if ($status === 'sick') {
                        $sickCounter++;
                        $p = ($sickCounter >= 3 && $sickCounter <= 6) ? $rules['sakit_3_6'] : (($sickCounter >= 7) ? $rules['sakit_7'] : 0);
                        if ($p > 0) { 
                            $stats['deduction_percentage'] += $p; 
                            $stats['details'][] = ['type' => 'Sakit Progresif', 'info' => "Hari ke-{$sickCounter}", 'date' => $currentDate, 'percent' => $p, 'rupiah' => ($p / 100) * $baseTunkin]; 
                        }
                    }
                    continue; 
                }

                $canReevaluate = in_array($status, ['absent', 'present', 'late', 'picket']);
                
                if ($checkInStr && $canReevaluate) {
                    if ($scheduledInTime) {
                        $actualTimestamp = strtotime($currentDate . ' ' . $checkInStr);
                        $targetTimestamp = strtotime($currentDate . ' ' . $scheduledInTime);
                        $diffMin = (int) ceil(($actualTimestamp - $targetTimestamp) / 60);

                        if ($diffMin >= -180) { 
                            if ($diffMin > 0) {
                                $status = 'late';
                            } else {
                                $status = ($sched['is_picket'] ?? false) ? 'picket' : 'present';
                            }
                        } else {
                            $status = 'absent';
                        }
                    }
                }

                $isEligibleMeal = in_array($status, ['present', 'late', 'duty_half', 'picket']);
                if ($isEligibleMeal) {
                    $dayPresentCount++;
                    if ($isNightShift) $hasNightShiftPresence = true;
                    $stats['total_present']++;
                }

                $dayShiftResults[] = [
                    'date' => $currentDate,
                    'status' => $status,
                    'check_in' => $checkInStr ?: '--:--',
                    'check_out' => $checkOutStr ?: '--:--',
                    'is_scheduled' => true,
                    'shift' => $shiftName,
                    'is_eligible' => $isEligibleMeal,
                    'is_night' => $isNightShift,
                    'is_default_office' => $isDefaultOffice,
                    'scheduled_in' => $scheduledInTime,
                    'scheduled_out' => $scheduledOutTime
                ];

                // Kalkulasi Potongan Tunkin
                $lateMin = 0;
                if ($checkInStr && $scheduledInTime) {
                    $actualTimestamp = strtotime($currentDate . ' ' . $checkInStr);
                    $targetTimestamp = strtotime($currentDate . ' ' . $scheduledInTime);
                    $diffMin = (int) ceil(($actualTimestamp - $targetTimestamp) / 60);
                    if ($diffMin >= -180 && $diffMin > 0) {
                        $lateMin = $diffMin;
                    }
                }

                if ($lateMin > 0) {
                    $p = $this->getLatePSWPercentage($lateMin, $rules);
                    $canCompensate = false;
                    
                    if ($lateMin <= 30 && $stats['compensation_count'] < 8 && $checkOutStr && $scheduledOutTime) {
                        $actualOut = strtotime($currentDate . ' ' . $checkOutStr);
                        $requiredOut = strtotime($currentDate . ' ' . $scheduledOutTime) + 1800; // +30 mins
                        if ($actualOut >= $requiredOut) $canCompensate = true;
                    }

                    if ($canCompensate) {
                        $stats['compensation_count']++;
                        $stats['details'][] = ['type' => 'TL Diganti', 'info' => "Telat {$lateMin}m, Ganti Pulang ({$shiftName})", 'date' => $currentDate, 'percent' => 0, 'rupiah' => 0];
                    } else {
                        $stats['late_count']++;
                        $stats['deduction_percentage'] += $p;
                        $label = $isDefaultOffice ? 'Terlambat (TL)' : 'Terlambat (TL Shift)';
                        $targetDisplay = $scheduledInTime ? date('H:i', strtotime($scheduledInTime)) : '--:--';
                        $stats['details'][] = [
                            'type' => $label, 
                            'info' => "{$lateMin}m (Jadwal: {$targetDisplay} - {$shiftName})", 
                            'date' => $currentDate, 
                            'percent' => $p, 
                            'rupiah' => ($p / 100) * $baseTunkin
                        ];
                    }
                }

                $earlyMin = 0;
                if ($isShift2 && $attendance) $earlyMin = abs((int)($attendance->early_minutes_2 ?? 0));
                elseif (!$isShift2 && $attendance) $earlyMin = abs((int)($attendance->early_minutes ?? 0));

                if ($earlyMin > 0) {
                    $p = $this->getLatePSWPercentage($earlyMin, $rules);
                    $stats['deduction_percentage'] += $p;
                    $stats['details'][] = ['type' => 'Pulang Cepat (PSW)', 'info' => "{$earlyMin}m ({$shiftName})", 'date' => $currentDate, 'percent' => $p, 'rupiah' => ($p / 100) * $baseTunkin];
                }

                if (in_array($status, ['present', 'late', 'picket']) && (!$checkInStr || !$checkOutStr || $checkInStr == $checkOutStr)) {
                    if ($isNightShift && $checkInStr && !$checkOutStr) {
                        // Bebas denda lupa absen pulang untuk shift malam
                    } else {
                        $stats['deduction_percentage'] += $rules['lupa_absen'];
                        $stats['details'][] = ['type' => 'Lupa Absen', 'info' => ((!$checkInStr) ? "Tanpa Masuk" : "Tanpa Pulang") . " ({$shiftName})", 'date' => $currentDate, 'percent' => $rules['lupa_absen'], 'rupiah' => ($rules['lupa_absen'] / 100) * $baseTunkin];
                    }
                }

                if ($status === 'absent' || (!$checkInStr && !$checkOutStr && !$isFuture)) {
                    $p = $rules['mangkir'];
                    $stats['deduction_percentage'] += $p;
                    $stats['details'][] = ['type' => 'Tanpa Keterangan', 'info' => "Tidak Hadir ({$shiftName})", 'date' => $currentDate, 'percent' => $p, 'rupiah' => ($p / 100) * $baseTunkin];
                }
            }

            // Hitung total Uang Makan harian
            // Aturan Baru: 2x jika lebih dari 1 shift (Double Shift), selain itu 1x.
            $dayMultiplier = ($dayPresentCount > 1) ? 2 : 1;
            $stats['meal_allowance_days'] += $dayMultiplier;
            $totalDailyMeal = $dayMultiplier * $mealRate;

            // Simpan ke log proses
            foreach ($dayShiftResults as $idx => $res) {
                // Tampilkan total di log pertama, 0 di log kedua untuk avoid double counting di tabel UI
                $res['meal_amount'] = ($idx === 0) ? $totalDailyMeal : 0;
                $stats['processed_logs'][] = $res;
            }
        }

        if ($stats['late_count'] > $rules['max_late']) $stats['violation_note'] = "PELANGGARAN: Telat {$stats['late_count']}x";
        $finalPercent = min(100, $stats['deduction_percentage']);
        $stats['total_potongan_rupiah'] = ($finalPercent / 100) * $baseTunkin;
        $stats['tunkin_final'] = max(0, ($baseTunkin + $actingBonus) - $stats['total_potongan_rupiah']);
        $stats['total_meal_allowance'] = $stats['meal_allowance_days'] * $mealRate;
        $stats['grand_total'] = $stats['tunkin_final'] + $stats['total_meal_allowance'];
        $stats['base_tunkin'] = $baseTunkin;

        return $stats;
    }

    private function getLatePSWPercentage($minutes, $rules)
    {
        if ($minutes <= 0) return 0;
        if ($minutes <= 30) return $rules['tl_1'];
        if ($minutes <= 60) return $rules['tl_2'];
        if ($minutes <= 90) return $rules['tl_3'];
        return $rules['tl_4'];
    }
}
