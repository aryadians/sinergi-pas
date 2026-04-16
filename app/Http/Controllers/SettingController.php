<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\Announcement;
use App\Models\Position;
use App\Models\WorkUnit;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        $announcements = Announcement::with('user')->latest()->get();
        $positions = Position::orderBy('name')->get();
        $workUnits = WorkUnit::orderBy('name')->get();
        
        return view('settings.index', compact('settings', 'announcements', 'positions', 'workUnits'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'watermark_enabled' => ['nullable', 'in:on,off'],
            'running_text_speed' => ['nullable', 'integer', 'min:5', 'max:120'],
            'running_text_bg' => ['nullable', 'string', 'max:20'],
            'running_text_color' => ['nullable', 'string', 'max:20'],
            'compliance_whatsapp_number' => ['nullable', 'string', 'max:30'],
            'meal_allowance_ii' => ['nullable', 'numeric'],
            'meal_allowance_iii' => ['nullable', 'numeric'],
            'meal_allowance_iv' => ['nullable', 'numeric'],
        ]);

        foreach ($request->except('_token') as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Specifically track who updated the broadcast message
        if ($request->has('running_text_message')) {
            Setting::updateOrCreate(
                ['key' => 'running_text_author'],
                ['value' => auth()->user()->name]
            );
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'update_settings',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' memperbarui konfigurasi sistem'
        ]);

        return back()->with('success', 'Pengaturan berhasil diperbarui.');
    }

    public function storePosition(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:positions,name']);
        
        Position::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_position',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' menambahkan jabatan baru: ' . $request->name
        ]);

        return back()->with('success', 'Jabatan berhasil ditambahkan.');
    }

    public function destroyPosition(Position $position)
    {
        $name = $position->name;
        $position->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_position',
            'ip_address' => request()->ip(),
            'details' => auth()->user()->name . ' menghapus jabatan: ' . $name
        ]);

        return back()->with('success', 'Jabatan berhasil dihapus.');
    }

    public function bulkDestroyPosition(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Pilih data yang ingin dihapus.');

        $count = Position::whereIn('id', $ids)->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'bulk_delete_position',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' menghapus ' . $count . ' jabatan secara massal'
        ]);

        return back()->with('success', $count . ' jabatan berhasil dihapus.');
    }

    public function storeWorkUnit(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255|unique:work_units,name']);
        
        WorkUnit::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name)
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_work_unit',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' menambahkan unit kerja baru: ' . $request->name
        ]);

        return back()->with('success', 'Unit kerja berhasil ditambahkan.');
    }

    public function destroyWorkUnit(WorkUnit $workUnit)
    {
        $name = $workUnit->name;
        $workUnit->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_work_unit',
            'ip_address' => request()->ip(),
            'details' => auth()->user()->name . ' menghapus unit kerja: ' . $name
        ]);

        return back()->with('success', 'Unit kerja berhasil dihapus.');
    }

    public function bulkDestroyWorkUnit(Request $request)
    {
        $ids = $request->ids;
        if (!$ids) return back()->with('error', 'Pilih data yang ingin dihapus.');

        $count = WorkUnit::whereIn('id', $ids)->delete();

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'bulk_delete_work_unit',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' menghapus ' . $count . ' unit kerja secara massal'
        ]);

        return back()->with('success', $count . ' unit kerja berhasil dihapus.');
    }

    public function getRunningText()
    {
        $settings = Setting::whereIn('key', [
            'running_text_message',
            'broadcast_mode',
            'running_text_bg',
            'running_text_color',
            'running_text_speed',
            'running_text_size',
            'running_text_author'
        ])->pluck('value', 'key');

        return response()->json([
            'message' => $settings['running_text_message'] ?? null,
            'author' => $settings['running_text_author'] ?? 'Admin',
            'mode' => $settings['broadcast_mode'] ?? 'running_text',
            'bg' => $settings['running_text_bg'] ?? '#0F172A',
            'color' => $settings['running_text_color'] ?? '#FFFFFF',
            'speed' => $settings['running_text_speed'] ?? '20',
            'size' => $settings['running_text_size'] ?? '12',
        ]);
    }
}
