<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Document;
use App\Models\WorkUnit;
use App\Models\DocumentCategory;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $workUnitId = $request->work_unit_id;

        // Base Query terpaku pada Database Pusat Pegawai
        $employeeQuery = Employee::query();
        if ($workUnitId) {
            $employeeQuery->where('work_unit_id', $workUnitId);
        }

        // Stats Dasar Real-time
        $totalEmployees = $employeeQuery->count();
        $totalDocuments = Document::count();
        $docsToday = Document::whereDate('created_at', now())->count();

        // Atensi SKP (Berdasarkan Database Pegawai)
        $skpCategory = DocumentCategory::where('slug', 'skp')->first();
        $employeesWithoutSkp = 0;
        if ($skpCategory) {
            $employeesWithoutSkp = (clone $employeeQuery)->whereDoesntHave('documents', function($q) use ($skpCategory) {
                $q->where('document_category_id', $skpCategory->id);
            })->count();
        }

        // Chart Data (Real-time Categories)
        $chartData = DocumentCategory::withCount(['documents' => function($q) use ($workUnitId) {
            if ($workUnitId) {
                $q->whereHas('employee', function($eq) use ($workUnitId) {
                    $eq->where('work_unit_id', $workUnitId);
                });
            }
        }])->get();

        $workUnits = WorkUnit::all();
        $latestEmployees = (clone $employeeQuery)->with('user')->latest()->take(5)->get();

        // Stats Pegawai
        $myDocumentsCount = 0;
        if ($user->role === 'pegawai') {
            $employee = Employee::where('user_id', $user->id)->first();
            $myDocumentsCount = Document::where('employee_id', $employee?->id)->count();
        }

        return view('dashboard', compact(
            'totalEmployees', 
            'totalDocuments', 
            'latestEmployees', // Needs definition if used
            'myDocumentsCount',
            'chartData',
            'docsToday',
            'employeesWithoutSkp',
            'workUnits'
        ));
    }
}
