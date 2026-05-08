<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WhistleblowerReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'is_anonymous',
        'category',
        'description',
        'status',
        'admin_response'
    ];

    protected $casts = [
        'is_anonymous' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function evidences(): HasMany
    {
        return $this->hasMany(WhistleblowerEvidence::class, 'report_id');
    }
}
