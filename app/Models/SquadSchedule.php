<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SquadSchedule extends Model
{
    use HasFactory;

    protected $fillable = ['squad_id', 'shift_id', 'date'];

    protected $casts = [
        'date' => 'date',
    ];

    public function squad()
    {
        return $this->belongsTo(Squad::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }
}
