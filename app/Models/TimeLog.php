<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'project_id',
        'subproject_id',
        'department_id',
        'date',
        'start_time',
        'end_time',
        'total_hours',
    ];
}
