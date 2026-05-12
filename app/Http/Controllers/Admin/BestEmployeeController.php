<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
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
        
        // Get all employees, excluding superadmins
        $employees = Employee::with(['rank_relation', 'work_unit', 'user'])
            ->whereHas('user', function($q) {
                $q->where('role', '!=', 'superadmin');
            })
            ->get();

        $rankedEmployees = [];

        foreach ($employees as $employee) {
            // Get stats from PayrollService
            $stats = $this->payrollService->calculateMonthlyPayroll($employee, $monthStr);

            $score = 0;
            
            // Formula for score
            $score += ($stats['total_present'] ?? 0) * 10;
            $score -= ($stats['late_count'] ?? 0) * 5;
            $score -= ($stats['deduction_percentage'] ?? 0) * 2;

            // Include ALL employees in the list, even if score is 0,
            // as long as they are not superadmins.
            $rankedEmployees[] = (object)[
                'employee' => $employee,
                'total_present' => $stats['total_present'] ?? 0,
                'late_count' => $stats['late_count'] ?? 0,
                'deduction_percentage' => $stats['deduction_percentage'] ?? 0,
                'score' => $score,
                'total_meal_allowance' => $stats['total_meal_allowance'] ?? 0,
            ];
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

        // Take all instead of top 10
        $topEmployees = $rankedEmployees;

        return view('admin.best-employee.index', compact('topEmployees', 'monthStr', 'date'));
    }
}
