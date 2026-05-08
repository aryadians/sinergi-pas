<?php

namespace App\Http\Controllers;

use App\Models\WhistleblowerReport;
use App\Models\WhistleblowerEvidence;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class WbsController extends Controller
{
    public function create()
    {
        return view('wbs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'category' => 'required|string',
            'description' => 'required|string',
        ]);

        $ticketNumber = 'LP-JBG-' . date('Y') . '-' . strtoupper(Str::random(6));

        $report = WhistleblowerReport::create([
            'ticket_number' => $ticketNumber,
            'user_id' => auth()->check() ? auth()->id() : null,
            'is_anonymous' => $request->has('is_anonymous'),
            'category' => $request->category,
            'description' => $request->description,
        ]);

        if ($request->hasFile('evidences')) {
            foreach ($request->file('evidences') as $file) {
                $mimeType = $file->getMimeType();
                $originalName = $file->getClientOriginalName();
                
                // Konversi semua file ke Base64
                $content = file_get_contents($file->getRealPath());
                $base64 = 'data:' . $mimeType . ';base64,' . base64_encode($content);
                
                // Tentukan tipe
                $type = 'document';
                if (str_starts_with($mimeType, 'image/')) $type = 'image';
                elseif (str_starts_with($mimeType, 'video/')) $type = 'video';
                elseif (str_starts_with($mimeType, 'audio/')) $type = 'audio';

                WhistleblowerEvidence::create([
                    'report_id' => $report->id,
                    'file_path' => $base64,
                    'file_type' => $type,
                    'original_name' => $originalName,
                ]);
            }
        }

        return redirect()->route('wbs.track.show', $ticketNumber)->with('success', 'Laporan berhasil dikirim. Harap simpan nomor tiket ini untuk melacak status laporan Anda.');
    }

    public function track()
    {
        return view('wbs.track');
    }

    public function showTrack(Request $request, $ticket = null)
    {
        $ticketNumber = $ticket ?? $request->ticket_number;
        $report = WhistleblowerReport::with('evidences')->where('ticket_number', $ticketNumber)->first();

        if (!$report) {
            return back()->with('error', 'Nomor tiket tidak ditemukan.');
        }

        return view('wbs.track_result', compact('report'));
    }
}
