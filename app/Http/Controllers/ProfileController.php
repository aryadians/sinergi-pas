<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Employee;
use App\Models\AuditLog;
use App\Models\ReportIssue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();
        $logs = AuditLog::where('user_id', $user->id)->with('document')->latest()->take(10)->get();
        $myIssues = ReportIssue::where('user_id', $user->id)->latest()->get();
        return view('profile.index', compact('user', 'employee', 'logs', 'myIssues'));
    }

    public function update(Request $request)
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:10240', // Relaxed to 10MB for server-side compression
            'password' => 'nullable|min:8|confirmed',
        ]);

        $changes = [];

        if ($request->name !== $user->name) {
            $user->update(['name' => $request->name]);
            $changes[] = 'nama';
        }

        if ($request->email !== $user->email) {
            $user->update(['email' => $request->email]);
            $changes[] = 'email';
        }

        if ($request->filled('password')) {
            $user->update(['password' => Hash::make($request->password)]);
            $changes[] = 'kata sandi';
        }

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            // Automatic Compression using Intervention Image V3 Manager
            $manager = new ImageManager(new Driver());
            $image = $manager->read($file->getRealPath());
            
            // Resize if too large while maintaining aspect ratio (800px max)
            $image->scaleDown(width: 800, height: 800);
            
            // Encode as JPG with 70% quality
            $encoded = $image->toJpeg(70);
            $base64 = 'data:image/jpeg;base64,' . base64_encode($encoded->toString());

            // Ensure employee record exists
            if (!$employee) {
                $employee = Employee::create([
                    'user_id' => $user->id,
                    'nip' => 'ADMIN-' . $user->id,
                    'full_name' => $user->name,
                    'position' => 'Administrator',
                ]);
            }

            // Delete old photo if it exists and is a file path (not base64)
            if ($employee->getRawOriginal('photo') && !str_starts_with($employee->getRawOriginal('photo'), 'data:image')) {
                Storage::disk('public')->delete($employee->getRawOriginal('photo'));
            }

            $employee->update(['photo' => $base64]);
            $changes[] = 'foto profil';
        }

        if (!empty($changes)) {
            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'update_profile',
                'ip_address' => $request->ip(),
                'details' => $user->name . ' memperbarui ' . implode(', ', $changes)
            ]);
        }

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    public function deletePhoto()
    {
        $user = auth()->user();
        $employee = Employee::where('user_id', $user->id)->first();

        if ($employee && $employee->getRawOriginal('photo')) {
            // Delete from storage if it's a path
            if (!str_starts_with($employee->getRawOriginal('photo'), 'data:image')) {
                Storage::disk('public')->delete($employee->getRawOriginal('photo'));
            }
            $employee->update(['photo' => null]);

            AuditLog::create([
                'user_id' => $user->id,
                'activity' => 'delete_profile_photo',
                'ip_address' => request()->ip(),
                'details' => $user->name . ' menghapus foto profil'
            ]);

            return back()->with('success', 'Foto profil berhasil dihapus.');
        }

        return back()->with('error', 'Tidak ada foto profil untuk dihapus.');
    }


    public function report(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $issue = ReportIssue::create([
            'user_id' => auth()->id(),
            'subject' => $request->subject,
            'message' => $request->message,
        ]);

        AuditLog::create([
            'user_id' => auth()->id(),
            'activity' => 'create_report_issue',
            'ip_address' => $request->ip(),
            'details' => auth()->user()->name . ' mengirim laporan masalah: ' . $issue->subject,
        ]);

        return back()->with('success', 'Laporan Anda telah terkirim ke Admin.');
    }
}
