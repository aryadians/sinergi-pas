<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WhistleblowerEvidence extends Model
{
    use HasFactory;

    protected $table = 'whistleblower_evidences';

    protected $fillable = [
        'report_id',
        'file_path',
        'file_type',
        'original_name'
    ];

    public function report(): BelongsTo
    {
        return $this->belongsTo(WhistleblowerReport::class, 'report_id');
    }
}
