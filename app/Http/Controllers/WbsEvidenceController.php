<?php

namespace App\Http\Controllers;

use App\Models\WhistleblowerEvidence;
use Illuminate\Support\Facades\Storage;

class WbsEvidenceController extends Controller
{
    public function download($id)
    {
        $evidence = WhistleblowerEvidence::findOrFail($id);
        
        // Cek apakah file ada di storage
        if (!Storage::disk('public')->exists($evidence->file_path)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return Storage::disk('public')->download($evidence->file_path, $evidence->original_name);
    }
}
