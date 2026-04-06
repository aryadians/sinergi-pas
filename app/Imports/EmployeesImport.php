<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use App\Models\Position;
use App\Models\WorkUnit;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        // New Format Mapping based on user request:
        // Nama Lengkap, Jabatan, Unit Kerja, Email, NIK, No. WhatsApp, Gol, regu/nonregu, [Optional NIP]
        
        $nama = $row['nama_lengkap'] ?? null;
        $jabatanName = $row['jabatan'] ?? null;
        $unitName = $row['unit_kerja'] ?? null;
        $email = $row['email'] ?? null;
        $nik = $row['nik'] ?? null;
        $wa = $row['no_whatsapp'] ?? $row['whatsapp'] ?? null;
        $rankClass = $row['gol'] ?? $row['golongan'] ?? null;
        $typeRaw = $row['regunonregu'] ?? $row['tipe'] ?? null;
        $nip = $row['nip'] ?? $nik; // Fallback NIP to NIK if not provided

        if (empty($nip) || empty($email)) return null;

        // Auto-detect Employee Type based on Position
        $employeeType = 'non_regu_jaga';
        $picketRegu = $row['regu'] ?? null;

        $jagaKeywords = ['JAGA', 'RUMAH TAHANAN', 'PENGAMANAN', 'KOMANDAN'];
        foreach ($jagaKeywords as $key) {
            if (str_contains(strtoupper($jabatanName), $key)) {
                $employeeType = 'regu_jaga';
                break;
            }
        }

        // Override if explicitly mentioned in Excel
        if ($typeRaw) {
            if (str_contains(strtolower($typeRaw), 'regu')) $employeeType = 'regu_jaga';
            if (str_contains(strtolower($typeRaw), 'non')) $employeeType = 'non_regu_jaga';
        }

        // Resolve Position ID
        $positionId = null;
        if ($jabatanName) {
            $position = Position::firstOrCreate(
                ['name' => $jabatanName],
                ['slug' => Str::slug($jabatanName)]
            );
            $positionId = $position->id;
        }

        // Resolve Work Unit ID
        $workUnitId = null;
        if ($unitName) {
            $unit = WorkUnit::firstOrCreate(
                ['name' => $unitName],
                ['slug' => Str::slug($unitName)]
            );
            $workUnitId = $unit->id;
        }

        // 1. User Account
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $nama ?? 'Pegawai Baru',
                'password' => Hash::make($nip), // Default password is NIP
                'role' => 'pegawai'
            ]
        );

        // 2. Employee Profile
        Employee::updateOrCreate(
            ['nip' => (string)$nip],
            [
                'user_id' => $user->id,
                'nik' => (string)$nik,
                'full_name' => $nama,
                'phone_number' => $wa,
                'position' => $jabatanName,
                'position_id' => $positionId,
                'work_unit_id' => $workUnitId,
                'rank_class' => $rankClass,
                'employee_type' => $employeeType,
                'picket_regu' => $picketRegu,
            ]
        );

        return null; 
    }

    public function batchSize(): int { return 50; }
    public function chunkSize(): int { return 50; }
}
