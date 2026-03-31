<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class EmployeesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip if NIP or Email is missing
        if (!isset($row['nip']) || !isset($row['email'])) {
            return null;
        }

        // Check if user already exists
        $user = User::where('email', $row['email'])->first();
        
        if (!$user) {
            $user = User::create([
                'name'     => $row['nama_lengkap'] ?? $row['name'],
                'email'    => $row['email'],
                'password' => Hash::make($row['password'] ?? 'password'),
                'role'     => 'pegawai',
            ]);
        }

        // Check if employee record already exists
        $employee = Employee::where('nip', $row['nip'])->first();

        if (!$employee) {
            return new Employee([
                'user_id'   => $user->id,
                'nip'       => $row['nip'],
                'full_name' => $row['nama_lengkap'] ?? $row['name'],
                'position'  => $row['jabatan'] ?? $row['position'],
                'rank'      => $row['pangkat'] ?? $row['rank'] ?? null,
            ]);
        }

        return null;
    }
}
