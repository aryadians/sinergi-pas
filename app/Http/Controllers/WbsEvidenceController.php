<?php

namespace App\Http\Controllers;

use App\Models\WhistleblowerEvidence;
use Illuminate\Support\Facades\Storage;

class WbsEvidenceController extends Controller
{
    public function download(Request $request, $id)
    {
        $evidence = WhistleblowerEvidence::findOrFail($id);
        $base64 = $evidence->file_path;
        
        $data = explode(',', $base64);
        $fileData = base64_decode($data[1]);
        $mimeType = explode(';', explode(':', $data[0])[1])[0];
        
        $disposition = $request->has('preview') ? 'inline' : 'attachment';

        return response($fileData)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', $disposition . '; filename="' . $evidence->original_name . '"');
    }
}
