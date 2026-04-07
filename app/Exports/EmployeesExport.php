<?php

namespace App\Exports;

use App\Models\Employee;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use App\Models\Setting;

class EmployeesExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithDrawings, WithCustomStartCell
{
    public function collection()
    {
        return Employee::with(['user', 'position_relation', 'work_unit'])->get();
    }

    public function startCell(): string
    {
        return 'A7';
    }

    public function drawings()
    {
        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Instansi Logo');
        $drawing->setPath(public_path('logo1.png'));
        $drawing->setHeight(80);
        $drawing->setCoordinates('A1');

        return $drawing;
    }

    public function headings(): array
    {
        return [
            'NIP',
            'NAMA LENGKAP',
            'JABATAN',
            'UNIT KERJA',
            'EMAIL',
            'NIK',
            'NO. WHATSAPP',
            'GOLONGAN',
            'REGU'
        ];
    }

    public function map($employee): array
    {
        return [
            $employee->nip,
            $employee->full_name,
            $employee->position,
            $employee->work_unit->name ?? '-',
            $employee->user->email ?? '-',
            $employee->nik ?? '-',
            $employee->phone_number ?? '-',
            $employee->rank_class ?? '-',
            $employee->picket_regu ?? '-'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $kop1 = Setting::getValue('kop_line_1', 'KEMENTERIAN HUKUM DAN HAK ASASI MANUSIA RI');
        $kop2 = Setting::getValue('kop_line_2', 'LEMBAGA PEMASYARAKATAN KELAS IIB JOMBANG');
        
        $sheet->mergeCells('B1:I1');
        $sheet->setCellValue('B1', $kop1);
        $sheet->mergeCells('B2:I2');
        $sheet->setCellValue('B2', $kop2);
        
        $sheet->mergeCells('A5:I5');
        $sheet->setCellValue('A5', 'DAFTAR NOMINATIF PEGAWAI');
        
        $sheet->getStyle('B1:I2')->getFont()->setBold(true)->setSize(12);
        $sheet->getStyle('A5')->getFont()->setBold(true)->setSize(14)->setUnderline(true);
        $sheet->getStyle('A5')->getAlignment()->setHorizontal('center');
        
        $sheet->getStyle('A7:I7')->getFont()->setBold(true);
        $sheet->getStyle('A7:I7')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('F1F5F9');
        
        $lastRow = $sheet->getHighestRow();
        $sheet->getStyle("A7:I$lastRow")->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        foreach (range('A', 'I') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        return [];
    }
}
