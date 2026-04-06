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
        $nip = $row['nip'] ?? null;
        $email = $row['email'] ?? null;
        $nama = $row['full_name'] ?? $row['nama_lengkap'] ?? $row['nama'] ?? null;
        $jabatanName = $row['position'] ?? $row['jabatan'] ?? null;
        $unitName = $row['work_unit'] ?? $row['unit_kerja'] ?? null;
        $rankClass = $row['rank_class'] ?? $row['golongan'] ?? null;
        $type = $row['employee_type'] ?? 'non_regu_jaga';
        $regu = $row['picket_regu'] ?? $row['regu'] ?? null;

        if (empty($nip) || empty($email)) return null;

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

        // 1. Update atau Create User
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $nama ?? 'Pegawai Baru',
                'password' => Hash::make($nip), // Default password is NIP
                'role' => 'pegawai'
            ]
        );

        // 2. Update atau Create Employee
        Employee::updateOrCreate(
            ['nip' => (string)$nip],
            [
                'user_id' => $user->id,
                'full_name' => $nama ?? 'Pegawai Baru',
                'position' => $jabatanName ?? 'Staf',
                'position_id' => $positionId,
                'work_unit_id' => $workUnitId,
                'rank_class' => $rankClass,
                'employee_type' => $type,
                'picket_regu' => $regu,
            ]
        );

        return null; 
    }

    public function batchSize(): int { return 50; }
    public function chunkSize(): int { return 50; }
}
