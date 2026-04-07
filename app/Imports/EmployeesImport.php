<?php

namespace App\Imports;

use App\Models\Employee;
use App\Models\User;
use App\Models\Position;
use App\Models\WorkUnit;
use App\Models\Squad;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Illuminate\Support\Facades\Log;

class EmployeesImport implements ToCollection
{
    public $importedCount = 0;

    public function collection(Collection $rows)
    {
        $positions = Position::all()->pluck('id', 'name')->toArray();
        $workUnits = WorkUnit::all()->pluck('id', 'name')->toArray();
        $squads = Squad::all()->pluck('id', 'name')->toArray();

        // Default mappings
        $map = [
            'nip' => 0,
            'nama' => 1,
            'jabatan' => 2,
            'unit' => 3,
            'email' => 4,
            'nik' => 5,
            'wa' => 6,
            'golongan' => 7,
            'regu' => 8
        ];

        DB::beginTransaction();
        try {
            $dataStarted = false;

            foreach ($rows as $index => $row) {
                // 1. Detect Header Row
                $firstCell = strtolower(trim((string)($row[0] ?? '')));
                if (!$dataStarted && (str_contains($firstCell, 'nip') || str_contains($firstCell, 'no') || str_contains(strtolower(trim((string)($row[1] ?? ''))), 'nip'))) {
                    // This looks like a header row, try to map columns
                    foreach ($row as $colIdx => $cellValue) {
                        $cellValue = strtolower(trim((string)$cellValue));
                        if (str_contains($cellValue, 'nip')) $map['nip'] = $colIdx;
                        if (str_contains($cellValue, 'nama')) $map['nama'] = $colIdx;
                        if (str_contains($cellValue, 'jabatan')) $map['jabatan'] = $colIdx;
                        if (str_contains($cellValue, 'unit')) $map['unit'] = $colIdx;
                        if (str_contains($cellValue, 'email')) $map['email'] = $colIdx;
                        if (str_contains($cellValue, 'nik')) $map['nik'] = $colIdx;
                        if (str_contains($cellValue, 'wa') || str_contains($cellValue, 'phone')) $map['wa'] = $colIdx;
                        if (str_contains($cellValue, 'gol')) $map['golongan'] = $colIdx;
                        if (str_contains($cellValue, 'regu') || str_contains($cellValue, 'squad')) $map['regu'] = $colIdx;
                    }
                    
                    if (str_contains($firstCell, 'nip') || str_contains(strtolower(trim((string)($row[1] ?? ''))), 'nip')) {
                        $dataStarted = true;
                        continue;
                    }
                }

                // 2. Extract NIP
                $nipRaw = trim((string)($row[$map['nip']] ?? ''));
                
                if (empty($nipRaw) || !is_numeric(preg_replace('/[^0-9]/', '', $nipRaw))) {
                    if (!$dataStarted && is_numeric(preg_replace('/[^0-9]/', '', (string)($row[1] ?? ''))) && strlen(preg_replace('/[^0-9]/', '', (string)($row[1] ?? ''))) > 5) {
                        $map['nip'] = 1;
                        $map['nama'] = 2;
                        $map['jabatan'] = 3;
                        $map['unit'] = 4;
                        $map['email'] = 5;
                        $map['nik'] = 6;
                        $map['wa'] = 7;
                        $map['golongan'] = 8;
                        $map['regu'] = 9;
                        $nipRaw = trim((string)($row[$map['nip']] ?? ''));
                        $dataStarted = true;
                    } else {
                        continue;
                    }
                }

                $dataStarted = true;

                // Clean NIP
                if (str_contains(strtoupper($nipRaw), 'E+')) {
                    $nip = number_format((float)$nipRaw, 0, '', '');
                } else {
                    $nip = preg_replace('/[^0-9]/', '', $nipRaw);
                }

                if (empty($nip) || strlen($nip) < 5) continue;

                $nama = $this->cleanValue($row[$map['nama']] ?? '');
                $jabatanName = $this->cleanValue($row[$map['jabatan']] ?? '');
                $unitName = $this->cleanValue($row[$map['unit']] ?? '');
                $email = $this->cleanValue($row[$map['email']] ?? '');
                $nik = $this->cleanValue($row[$map['nik']] ?? '');
                $wa = $this->cleanValue($row[$map['wa']] ?? '');
                $rankClass = $this->cleanValue($row[$map['golongan']] ?? '');
                $reguValue = $this->cleanValue($row[$map['regu']] ?? '');

                if (empty($nama)) continue;

                if (empty($email)) {
                    $email = $nip . '@sinergipas.id';
                }

                // Resolve Master Data
                $positionId = $this->resolveId($jabatanName, $positions, Position::class);
                $workUnitId = $this->resolveId($unitName, $workUnits, WorkUnit::class);
                
                // Specific Classification Logic
                $jNameUpper = strtoupper($jabatanName);
                $isJaga = str_contains($jNameUpper, 'PETUGAS JAGA') || 
                          str_contains($jNameUpper, 'ANGGOTA JAGA') || 
                          str_contains($jNameUpper, 'KOMANDAN JAGA') ||
                          str_contains($jNameUpper, 'PENJAGA');

                $employeeType = $isJaga ? 'regu_jaga' : 'non_regu_jaga';
                
                $squadId = null;
                $picketRegu = null;
                if ($isJaga && !empty($reguValue) && !in_array(strtolower($reguValue), ['non', 'staf', 'staff', '-', ''])) {
                    // Clean regu value from common "Petugas Jaga" prefix if present in the cell
                    $cleanRegu = trim(str_ireplace(['Petugas Jaga', 'Regu'], '', $reguValue));
                    if (!empty($cleanRegu)) {
                        $squadId = $this->resolveId($cleanRegu, $squads, Squad::class);
                        $picketRegu = $cleanRegu;
                    }
                }

                $employee = Employee::where('nip', $nip)->first();

                if ($employee) {
                    if ($employee->user) {
                        $employee->user->update([
                            'name' => $nama,
                            'email' => $email,
                        ]);
                    }

                    $employee->update([
                        'nik' => $nik,
                        'full_name' => $nama,
                        'phone_number' => $wa,
                        'position' => $jabatanName,
                        'position_id' => $positionId,
                        'work_unit_id' => $workUnitId,
                        'rank_class' => $rankClass,
                        'employee_type' => $employeeType,
                        'squad_id' => $squadId,
                        'picket_regu' => $picketRegu
                    ]);
                } else {
                    $user = User::create([
                        'name' => $nama,
                        'email' => $email,
                        'password' => Hash::make($nip),
                        'role' => 'pegawai'
                    ]);

                    Employee::create([
                        'user_id' => $user->id,
                        'nip' => $nip,
                        'nik' => $nik,
                        'full_name' => $nama,
                        'phone_number' => $wa,
                        'position' => $jabatanName,
                        'position_id' => $positionId,
                        'work_unit_id' => $workUnitId,
                        'rank_class' => $rankClass,
                        'employee_type' => $employeeType,
                        'squad_id' => $squadId,
                        'picket_regu' => $picketRegu
                    ]);
                }
                $this->importedCount++;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Import Error: ' . $e->getMessage());
            throw $e;
        }
    }

    private function cleanValue($value)
    {
        $val = trim((string)$value);
        // If it starts with =, it might be a formula that failed to evaluate or was copied as string
        if (str_starts_with($val, '=')) {
            // For this specific system, we usually want the literal intended result if possible, 
            // but if we get the formula string, it's garbage. 
            // However, often users have "=IF(ISNUMBER...)" which results in "Petugas Jaga" or "Non".
            // If we only have the formula string, we can't evaluate it here easily without a parser.
            // But we can try to see if the value contains keywords.
            if (str_contains(strtoupper($val), 'PETUGAS JAGA')) return 'Petugas Jaga';
            if (str_contains(strtoupper($val), 'NON')) return 'Non';
            return ''; // Strip the formula
        }
        return $val;
    }

    private function resolveId($name, &$cache, $modelClass)
    {
        if (empty($name)) return null;
        if (isset($cache[$name])) return $cache[$name];

        $record = $modelClass::firstOrCreate(
            ['name' => $name],
            ['slug' => Str::slug($name)]
        );

        $cache[$name] = $record->id;
        return $record->id;
    }
}
