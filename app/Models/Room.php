<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'capacity'];

    protected $casts = [
        'capacity' => 'integer',
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
