<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FixedPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_name', 'day', 'period', 'shift', 'auto_assign_homeroom',
    ];

    protected $casts = [
        'day' => 'integer',
        'period' => 'integer',
        'auto_assign_homeroom' => 'boolean',
    ];
}
