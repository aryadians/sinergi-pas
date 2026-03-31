<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;

class EmployeesImport implements ToModel, WithHeadingRow, WithBatchInserts, WithChunkReading
{
    public function model(array $row)
    {
        $nip = $row['nip'] ?? $row['NIP'] ?? null;
        $email = $row['email'] ?? $row['Email'] ?? null;
        $nama = $row['nama_lengkap'] ?? $row['Nama Lengkap'] ?? $row['nama'] ?? null;
        $jabatan = $row['jabatan'] ?? $row['Jabatan'] ?? null;

        if (empty($nip) || empty($email)) return null;

        // Use updateOrCreate for performance and stability
        $user = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $nama ?? 'Pegawai Baru',
                'password' => Hash::make($row['password'] ?? 'password'),
                'role' => 'pegawai'
            ]
        );

        return Employee::updateOrCreate(
            ['nip' => $nip],
            [
                'user_id' => $user->id,
                'full_name' => $nama ?? 'Pegawai Baru',
                'position' => $jabatan ?? 'Staf'
            ]
        );
    }

    public function batchSize(): int
    {
        return 100;
    }

    public function chunkSize(): int
    {
        return 100;
    }
}
