<?php

namespace App\Http\Controllers;

use App\Models\WhistleblowerEvidence;
use Illuminate\Support\Facades\Storage;

class WbsEvidenceController extends Controller
{
    public function download($id)
    {
        $evidence = WhistleblowerEvidence::findOrFail($id);
        
        // Karena semua file sudah base64, kita langsung render data URL tersebut
        // Untuk memaksa download, kita bisa menggunakan base64_decode di server, 
        // tapi untuk kemudahan tampilan di browser, kita bisa langsung redirect atau stream.
        
        $base64 = $evidence->file_path;
        
        // Memisahkan data dari header (data:mime/type;base64,...)
        $data = explode(',', $base64);
        $fileData = base64_decode($data[1]);
        $mimeType = explode(';', explode(':', $data[0])[1])[0];

        return response($fileData)
            ->header('Content-Type', $mimeType)
            ->header('Content-Disposition', 'attachment; filename="' . $evidence->original_name . '"');
    }
}
