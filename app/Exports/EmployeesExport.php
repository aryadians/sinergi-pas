<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Employee::with(['user', 'position_relation', 'work_unit'])->get();
    }

    public function headings(): array
    {
        return [
            'NIP',
            'Nama Lengkap',
            'Jabatan',
            'Unit Kerja',
            'Email',
            'Pangkat/Golongan'
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->nip,
            $employee->full_name,
            $employee->position, // This is still stored as string for fallback
            $employee->work_unit->name ?? 'N/A',
            $employee->user->email ?? 'N/A',
            $employee->rank ?? 'N/A'
        ];
    }
}
