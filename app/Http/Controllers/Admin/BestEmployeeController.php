<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Employee;
use App\Services\PayrollService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class BestEmployeeController extends Controller
{
    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request)
    {
        $monthStr = $request->month ?? now()->format('Y-m');
        $date = Carbon::parse($monthStr . '-01');
        
        $employees = Employee::with(['rank_relation', 'work_unit', 'user'])
            ->whereHas('user')
            ->get();

        $rankedEmployees = [];

        foreach ($employees as $employee) {
            // Get stats from PayrollService
            $stats = $this->payrollService->calculateMonthlyPayroll($employee, $monthStr);

            $score = 0;
            
            // Formula for score:
            // Base points for attendance: +10 per valid day
            // Penalty for late: -5 per late
            // Penalty for deductions: - (deduction_percentage * 2)
            $score += ($stats['total_present'] ?? 0) * 10;
            $score -= ($stats['late_count'] ?? 0) * 5;
            $score -= ($stats['deduction_percentage'] ?? 0) * 2;

            // Only consider employees who have some attendance
            if (($stats['total_present'] ?? 0) > 0) {
                $rankedEmployees[] = (object)[
                    'employee' => $employee,
                    'total_present' => $stats['total_present'] ?? 0,
                    'late_count' => $stats['late_count'] ?? 0,
                    'deduction_percentage' => $stats['deduction_percentage'] ?? 0,
                    'score' => $score,
                    'total_meal_allowance' => $stats['total_meal_allowance'] ?? 0,
                ];
            }
        }

        // Sort by score descending, then by total present descending, then by late ascending
        usort($rankedEmployees, function($a, $b) {
            if ($a->score !== $b->score) {
                return $b->score <=> $a->score;
            }
            if ($a->total_present !== $b->total_present) {
                return $b->total_present <=> $a->total_present;
            }
            return $a->late_count <=> $b->late_count;
        });

        // Take top 10
        $topEmployees = array_slice($rankedEmployees, 0, 10);

        return view('admin.best-employee.index', compact('topEmployees', 'monthStr', 'date'));
    }
}
