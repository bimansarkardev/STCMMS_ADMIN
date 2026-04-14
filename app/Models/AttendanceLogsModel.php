<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AttendanceLogsModel extends Model
{
    public $timestamps = true;
    protected $table = 'attendance_logs';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'attendance_id',
        'field_worker_id',
        'created_by',
        'date',
        'login_time',
        'login_lat',
        'login_long',
        'login_address',
        'logout_time',
        'logout_lat',
        'logout_long',
        'logout_address',
        'work_minutes',
        'created_at',
        'updated_at',
        'status',
    ];
}

