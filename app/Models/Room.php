<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity', 'category', 'status'];

    protected $casts = [
        'capacity' => 'integer',
        'status' => 'boolean',
    ];

    public function roomCategory()
    {
        return $this->belongsTo(RoomCategory::class);
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
