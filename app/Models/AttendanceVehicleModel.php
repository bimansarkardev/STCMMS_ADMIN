<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AttendanceVehicleModel extends Model
{
    public $timestamps = true;
    protected $table = 'attendance_vehicle';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'attendance_id',
        'date',
        'vehicle_id',
        'total_field_workers',
        'is_changed',
        'changed_reason',
        'created_at',
        'updated_at',
        'status',
    ];
}

