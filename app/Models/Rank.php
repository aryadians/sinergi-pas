<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Rank extends Model
{
    protected $fillable = ['name', 'description', 'meal_allowance'];

    public function employees(): HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
