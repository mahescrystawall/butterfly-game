<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlyingHistory extends Model
{
    use HasFactory;

    // Ensure datetime fields are cast to Carbon instances
    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'final_multiplier' => 'float',
    ];

    // Specify which fields are mass-assignable
    protected $fillable = [
        'final_multiplier',
        'end_time',
        'start_time',
        'is_active',
    ];

}
