<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RoomCategory extends Model
{
    protected $fillable = ['name'];

    public function rooms()
    {
        return $this->hasMany(Room::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }
}
