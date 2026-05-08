<?php

namespace App\Http\Controllers;

use App\Models\WhistleblowerEvidence;
use Illuminate\Support\Facades\Storage;

class WbsEvidenceController extends Controller
{
    public function download($id)
    {
        $evidence = WhistleblowerEvidence::findOrFail($id);
        
        // Cek jika base64 (image)
        if (str_starts_with($evidence->file_path, 'data:')) {
            return response($evidence->file_path);
        }

        // Cek file fisik di folder public
        $filePath = public_path($evidence->file_path);
        
        if (!file_exists($filePath)) {
            return back()->with('error', 'File tidak ditemukan di server.');
        }

        return response()->download($filePath, $evidence->original_name);
    }
}
