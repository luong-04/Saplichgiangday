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

    public function subjects()
    {
        return $this->belongsToMany(Subject::class , 'room_subject');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
