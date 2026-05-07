<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class PayrollSettingController extends Controller
{
    public function index()
    {
        $settings = [
            'payroll_tl_1_percent' => Setting::getValue('payroll_tl_1_percent', 0.5),
            'payroll_tl_2_percent' => Setting::getValue('payroll_tl_2_percent', 1.0),
            'payroll_tl_3_percent' => Setting::getValue('payroll_tl_3_percent', 1.25),
            'payroll_tl_4_percent' => Setting::getValue('payroll_tl_4_percent', 1.5),
            'payroll_max_late_count' => Setting::getValue('payroll_max_late_count', 8),
            'payroll_mangkir_percent' => Setting::getValue('payroll_mangkir_percent', 5.0),
            'payroll_lupa_absen_percent' => Setting::getValue('payroll_lupa_absen_percent', 1.5),
            'payroll_sakit_3_6_percent' => Setting::getValue('payroll_sakit_3_6_percent', 2.5),
            'payroll_sakit_7_plus_percent' => Setting::getValue('payroll_sakit_7_plus_percent', 10.0),
            'payroll_apel_percent' => Setting::getValue('payroll_apel_percent', 0.5),
            
            // Jam Kerja Staff (Reguler)
            'payroll_staff_in' => Setting::getValue('payroll_staff_in', '07:30'),
            'payroll_staff_out_mon_thu' => Setting::getValue('payroll_staff_out_mon_thu', '16:00'),
            'payroll_staff_out_fri' => Setting::getValue('payroll_staff_out_fri', '16:30'),

            // Jam Kerja Shift (Garda/Piket Regu)
            'payroll_shift_pagi_in' => Setting::getValue('payroll_shift_pagi_in', '06:00'),
            'payroll_shift_siang_in' => Setting::getValue('payroll_shift_siang_in', '13:00'),
            'payroll_shift_malam_in' => Setting::getValue('payroll_shift_malam_in', '20:00'),

            // Jam Kerja Shift (Piket Individu)
            'payroll_picket_pagi_in' => Setting::getValue('payroll_picket_pagi_in', '07:30'),
            'payroll_picket_siang_in' => Setting::getValue('payroll_picket_siang_in', '13:00'),
            'payroll_picket_malam_in' => Setting::getValue('payroll_picket_malam_in', '20:00'),

            // Jam Kerja Staff (Sabtu - Opsional)
            'payroll_staff_saturday_enabled' => Setting::getValue('payroll_staff_saturday_enabled', 'off'),
            'payroll_staff_saturday_in' => Setting::getValue('payroll_staff_saturday_in', '07:30'),
            'payroll_staff_saturday_out' => Setting::getValue('payroll_staff_saturday_out', '12:00'),

            // Jam Kerja Staff (Bulan Puasa - Opsional)
            'payroll_ramadan_enabled' => Setting::getValue('payroll_ramadan_enabled', 'off'),
            'payroll_ramadan_start' => Setting::getValue('payroll_ramadan_start', date('Y-m-d')),
            'payroll_ramadan_end' => Setting::getValue('payroll_ramadan_end', date('Y-m-d')),
            'payroll_ramadan_staff_in' => Setting::getValue('payroll_ramadan_staff_in', '08:00'),
            'payroll_ramadan_staff_out_mon_thu' => Setting::getValue('payroll_ramadan_staff_out_mon_thu', '15:00'),
            'payroll_ramadan_staff_out_fri' => Setting::getValue('payroll_ramadan_staff_out_fri', '15:30'),

            // Jam Kerja Staff (Bulan Puasa - Sabtu)
            'payroll_ramadan_saturday_enabled' => Setting::getValue('payroll_ramadan_saturday_enabled', 'off'),
            'payroll_ramadan_saturday_in' => Setting::getValue('payroll_ramadan_saturday_in', '08:00'),
            'payroll_ramadan_saturday_out' => Setting::getValue('payroll_ramadan_saturday_out', '12:00'),
        ];

        return view('admin.settings.payroll', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'payroll_tl_1_percent' => 'required|numeric|min:0',
            'payroll_tl_2_percent' => 'required|numeric|min:0',
            'payroll_tl_3_percent' => 'required|numeric|min:0',
            'payroll_tl_4_percent' => 'required|numeric|min:0',
            'payroll_max_late_count' => 'required|integer|min:0',
            'payroll_mangkir_percent' => 'required|numeric|min:0',
            'payroll_lupa_absen_percent' => 'required|numeric|min:0',
            'payroll_sakit_3_6_percent' => 'required|numeric|min:0',
            'payroll_sakit_7_plus_percent' => 'required|numeric|min:0',
            'payroll_apel_percent' => 'required|numeric|min:0',
            'payroll_staff_in' => 'required|string',
            'payroll_staff_out_mon_thu' => 'required|string',
            'payroll_staff_out_fri' => 'required|string',
            'payroll_shift_pagi_in' => 'required|string',
            'payroll_shift_siang_in' => 'required|string',
            'payroll_shift_malam_in' => 'required|string',
            'payroll_staff_saturday_enabled' => 'nullable|string',
            'payroll_staff_saturday_in' => 'required_if:payroll_staff_saturday_enabled,on|string',
            'payroll_staff_saturday_out' => 'required_if:payroll_staff_saturday_enabled,on|string',
            
            'payroll_ramadan_enabled' => 'nullable|string',
            'payroll_ramadan_start' => 'required_if:payroll_ramadan_enabled,on|date',
            'payroll_ramadan_end' => 'required_if:payroll_ramadan_enabled,on|date',
            'payroll_ramadan_staff_in' => 'required_if:payroll_ramadan_enabled,on|string',
            'payroll_ramadan_staff_out_mon_thu' => 'required_if:payroll_ramadan_enabled,on|string',
            'payroll_ramadan_staff_out_fri' => 'required_if:payroll_ramadan_enabled,on|string',

            'payroll_ramadan_saturday_enabled' => 'nullable|string',
            'payroll_ramadan_saturday_in' => 'required_if:payroll_ramadan_saturday_enabled,on|string',
            'payroll_ramadan_saturday_out' => 'required_if:payroll_ramadan_saturday_enabled,on|string',
        ]);

        // Handle checkboxes
        if (!isset($data['payroll_staff_saturday_enabled'])) {
            $data['payroll_staff_saturday_enabled'] = 'off';
        }
        if (!isset($data['payroll_ramadan_enabled'])) {
            $data['payroll_ramadan_enabled'] = 'off';
        }
        if (!isset($data['payroll_ramadan_saturday_enabled'])) {
            $data['payroll_ramadan_saturday_enabled'] = 'off';
        }

        foreach ($data as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('success', 'Aturan perhitungan payroll berhasil diperbarui.');
    }
}
