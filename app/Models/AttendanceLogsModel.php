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

    public function getAttendanceLogs($perPage, $filters = [])
    {
        $query = DB::table('attendance_logs')
            ->select(
                'attendance_logs.id',
                'attendance_logs.attendance_id',
                'attendance_logs.field_worker_id',
                'attendance_logs.created_by',
                'attendance_logs.date',
                DB::raw("DATE_FORMAT(attendance_logs.date, '%d/%m/%y') as formatted_date"),
                'attendance_logs.login_time',
                DB::raw("DATE_FORMAT(attendance_logs.login_time, '%d/%m/%y - %h:%i %p') as formatted_login_time"),
                'attendance_logs.login_lat',
                'attendance_logs.login_long',
                'attendance_logs.login_address',
                'attendance_logs.logout_time',
                DB::raw("DATE_FORMAT(attendance_logs.logout_time, '%d/%m/%y - %h:%i %p') as formatted_logout_time"),
                'attendance_logs.logout_lat',
                'attendance_logs.logout_long',
                'attendance_logs.logout_address',
                'attendance_logs.work_minutes',
                'attendance_logs.created_at',
                'attendance_logs.updated_at',
                'attendance_logs.status',
            );

        if (isset($filters['attendance_id']) && !empty($filters['attendance_id'])) {
            $query->where('attendance_logs.attendance_id', '=', $filters['attendance_id']);
        }

        if (isset($filters['field_worker_id']) && !empty($filters['field_worker_id'])) {
            $query->where('attendance_logs.field_worker_id', '=', $filters['field_worker_id']);
        }

        if (isset($filters['date']) && !empty($filters['date'])) {
            $query->whereDate('attendance_logs.date', '=', $filters['date']);
        }

        // Ordering
        $query->orderBy('attendance_logs.id', 'asc');

        if (isset($filters['id']) && !empty($filters['id'])) {
            $query->where('attendance_logs.id', '=', $filters['id']);
            return $query->first();
        }

        if($perPage!="")
        {
            return $query->paginate($perPage);
        }
        else
        {
            return $query->get();
        }
    }
}

