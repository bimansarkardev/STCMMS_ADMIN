<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Models\AttendanceLogsModel;
use App\Models\AttendanceModel;
use App\Models\AttendanceVehicleModel;

class CommonService
{
    public function insertData($table, $data)
    {
        return DB::table($table)->insertGetId($data);
    }

    public function updateData($table, $where, $updateData)
    {
        return DB::table($table)->where($where)->update($updateData);
    }

    public function deleteData($table, $where)
    {
        return DB::table($table)->where($where)->delete();
    }

    public function getField($table, $where, $field)
    {
        return DB::table($table)->where($where)->value($field);
    }

    public function getSingle($table, $where, $selectFields = ['*'])
    {
        return DB::table($table)->select($selectFields)->where($where)->first();
    }

    public function getListData($table, $where = [], $selectFields = ['*'], $orderField = null, $orderBy = 'asc', $whereIn = [], $whereInField = null)
    {
        $query = DB::table($table)->select($selectFields);

        if (!empty($where)) {
            $query->where($where);
        }

        if (!empty($whereIn) && $whereInField) {
            $query->whereIn($whereInField, $whereIn);
        }

        if ($orderField) {
            $query->orderBy($orderField, $orderBy);
        }

        return $query->get()->toArray();
    }

    public function getNumRows($table, array $whereArray = [], $exceptionField = null, $exceptionVal = null)
    {
        $query = DB::table($table);

        if (!empty($whereArray)) {
            $query->where($whereArray);
        }

        if ($exceptionField && $exceptionVal) {
            $query->where($exceptionField, '!=', $exceptionVal);
        }

        return $query->count();
    }

    public function getDropdown($selected, $table, $where, $value, $text, $orderField = null, $orderBy = null)
    {
        $query = DB::table($table)->where($where);

        if ($orderField) {
            $query->orderBy($orderField, $orderBy);
        }

        $items = $query->get();

        $options = '';
        foreach ($items as $item) {
            $isSelected = ($selected == $item->$value) ? ' selected="selected"' : '';
            $options .= "<option {$isSelected} value='{$item->$value}'>{$item->$text}</option>";
        }

        return $options;
    }

    public function getTotalNumRows($table)
    {
        return DB::table($table)->count();
    }

    public function sendEmail($to, $from, $subject, $message)
    {
        try {
            Mail::raw($message, function ($mail) use ($to, $from, $subject) {
                $mail->to($to)
                     ->from($from, 'Haque Electric')
                     ->subject($subject);
            });

            return [
                'success' => true,
                'message' => 'Email sent successfully!'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Failed to send email: ' . $e->getMessage()
            ];
        }
    }

    public function getAdminIds()
    {
        return DB::table('user')->select('user_id')->get()->toArray();
    }

    public function getToken($length, $for = 'GENERAL')
    {
        $token = '';
        if ($for === 'OTP')
        {
            $codeAlphabet = '0123456789';
        }
        else if ($for === 'UID')
        {
            $codeAlphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }
        else
        {
            $codeAlphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        }

        $max = strlen($codeAlphabet);
        for ($i = 0; $i < $length; $i++) {
            $token .= $codeAlphabet[$this->cryptoRandSecure(0, $max - 1)];
        }

        return $token;
    }

    private function cryptoRandSecure($min, $max)
    {
        $range = $max - $min;
        if ($range < 1) return $min;

        $log = ceil(log($range, 2));
        $bytes = (int) ($log / 8) + 1;
        $bits = (int) $log + 1;
        $filter = (1 << $bits) - 1;

        do {
            $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
            $rnd = $rnd & $filter;
        } while ($rnd > $range);

        return $min + $rnd;
    }

    function getAttendanceDetails($field_worker_id,$date) 
    {
        //attendance data
        $currentDate = $date;

        $attendance = AttendanceModel::where('field_worker_id', $field_worker_id)
            ->whereDate('date', $currentDate)
            ->first();

        $attendance_data = [
            'is_attendance_in_today' => false,
            'is_currently_working' => false,
            'last_session' => null,
            'total_sessions' => 0,
            'all_sessions' => null,
        ];

        if ($attendance) {

            // total sessions
            $total_sessions = AttendanceLogsModel::where('attendance_id', $attendance->id)->count();

            // active session (IMPORTANT FIX)
            $active_session = AttendanceLogsModel::where('attendance_id', $attendance->id)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first();

            // last session (for info)
            $last_session = AttendanceLogsModel::where('attendance_id', $attendance->id)
                ->latest('login_time')
                ->first();

            // all session (for info)
            $all_sessions = AttendanceLogsModel::where('attendance_id', $attendance->id)
                ->get();

            $attendance_data = [
                'is_attendance_in_today' => true,
                'is_currently_working' => $active_session ? true : false,
                'last_session' => $last_session ? : null,
                'total_sessions' => $total_sessions,
                'all_sessions' => $all_sessions,
            ];
        }
        //attendance data

        return $attendance_data;
    }

}
