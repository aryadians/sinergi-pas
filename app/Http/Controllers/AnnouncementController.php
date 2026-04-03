<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'message' => 'required|string',
            'type' => 'required|in:banner,popup',
        ]);

        $ann = Announcement::create([
            'user_id' => auth()->id(),
            'message' => $request->message,
            'type' => $request->type,
            'is_active' => true,
        ]);

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_announcement',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' menyiarkan pengumuman baru: ' . $ann->message
        ]);

        return back()->with('success', 'Pengumuman berhasil disiarkan.');
    }

    public function toggle(Announcement $announcement)
    {
        $announcement->update(['is_active' => !$announcement->is_active]);
        
        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'toggle_announcement',
            'ip_address' => request()->ip(),
            'details' => auth()->user()->name . ' mengubah status pengumuman: ' . $announcement->message
        ]);

        return back()->with('success', 'Status pengumuman diperbarui.');
    }

    public function destroy(Announcement $announcement)
    {
        $msg = $announcement->message;
        $announcement->delete();

        \App\Models\AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'delete_announcement',
            'ip_address' => request()->ip(),
            'details' => auth()->user()->name . ' menghapus pengumuman: ' . $msg
        ]);

        return back()->with('success', 'Pengumuman dihapus.');
    }
}
