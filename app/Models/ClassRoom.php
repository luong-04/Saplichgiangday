<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    use HasFactory;
    
    // Khai báo rõ bảng classes (vì tên Model là ClassRoom)
    protected $table = 'classes'; 

    protected $fillable = [
        'name',
        'grade',
        'lookup_code',
    ];
}