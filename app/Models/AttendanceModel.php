<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AttendanceModel extends Model
{
    public $timestamps = true;
    protected $table = 'attendance';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'field_worker_id',
        'created_by',
        'date',
        'total_work_minutes',
        'total_sessions',
        'created_at',
        'updated_at',
        'status',
    ];
}

