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

        // Base Query untuk filter unit kerja
        $employeeQuery = Employee::query();
        if ($workUnitId) {
            $employeeQuery->where('work_unit_id', $workUnitId);
        }

        // Stats Dasar
        $totalEmployees = $employeeQuery->count();
        
        // Stats Dokumen (Jika difilter unit kerja, hitung dokumen milik pegawai di unit tersebut)
        if ($workUnitId) {
            $totalDocuments = Document::whereIn('employee_id', $employeeQuery->pluck('id'))->count();
            $docsToday = Document::whereIn('employee_id', $employeeQuery->pluck('id'))
                ->whereDate('created_at', now())->count();
        } else {
            $totalDocuments = Document::count();
            $docsToday = Document::whereDate('created_at', now())->count();
        }

        // Atensi SKP
        $skpCategoryId = DocumentCategory::where('slug', 'skp')->first()?->id;
        $atensiQuery = clone $employeeQuery;
        $employeesWithoutSkp = $atensiQuery->whereDoesntHave('documents', function($q) use ($skpCategoryId) {
            $q->where('document_category_id', $skpCategoryId);
        })->count();

        // Data Lainnya
        $latestEmployees = (clone $employeeQuery)->with('user')->latest()->take(5)->get();
        $chartData = DocumentCategory::withCount(['documents' => function($q) use ($workUnitId, $employeeQuery) {
            if ($workUnitId) {
                $q->whereIn('employee_id', (clone $employeeQuery)->pluck('id'));
            }
        }])->get();

        $workUnits = WorkUnit::all();

        // Statistik khusus pegawai
        $myDocumentsCount = 0;
        if ($user->role === 'pegawai') {
            $employee = Employee::where('user_id', $user->id)->first();
            $myDocumentsCount = Document::where('employee_id', $employee?->id)->count();
        }

        return view('dashboard', compact(
            'totalEmployees', 
            'totalDocuments', 
            'latestEmployees',
            'myDocumentsCount',
            'chartData',
            'docsToday',
            'employeesWithoutSkp',
            'workUnits'
        ));
    }
}
