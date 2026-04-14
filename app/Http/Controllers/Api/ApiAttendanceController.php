<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Ward;
use App\Models\UserDetails;
use App\Models\FieldWorkersModel;
use App\Models\VehiclesModel;
use App\Models\AttendanceModel;
use App\Models\AttendanceLogsModel;
use App\Models\AttendanceVehicleModel;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiAttendanceController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function getFieldWorkersForAttendance(Request $request)
    {
        $user_filters = [
            'login_token' => $request->bearerToken()
        ];
        $user = (new User)->getUser('', $user_filters);

        $filters = [
			'field_worker_user_id' => $user->user_id,
        ];
        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

        if($field_worker->role == 1)
        {
            $field_worker_roles = [2,3];
        }
        else if($field_worker->role == 4)
        {
            $field_worker_roles = [1,2,3];
        }

        $filters = [
			'field_worker_roles' => $field_worker_roles,
			'municipality_id' => $field_worker->municipality_id,
        ];
        $field_workers = (new FieldWorkersModel)->getFieldWorkers('', $filters);

        $data = [
            'field_workers'=>$field_workers,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Field Workers list For Attendance fetched successfully',
            'data' => $data
        ], 200);
    }

    public function getVehiclesForAttendance(Request $request)
    {
        $user_filters = [
            'login_token' => $request->bearerToken()
        ];
        $user = (new User)->getUser('', $user_filters);

        $filters = [
			'field_worker_user_id' => $user->user_id,
        ];
        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);        

        $filters = [
            'municipality_id' => $field_worker->municipality_id,
        ];
        $params = (new VehiclesModel)->getVehicles('', $filters);

        $data = [
            'vehicles'=>$params,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Vehicles list For Attendance fetched successfully',
            'data' => $data
        ], 200);
    }

    public function attendanceIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'vehicle_id' => 'required|exists:vehicles,id',
            'field_workers.*' => 'required',
            'login_lat' => 'required',
            'login_long' => 'required',
            'login_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        DB::beginTransaction();

        try {

            // 🔐 Get User
            $user_filters = [
                'login_token' => $request->bearerToken()
            ];
            $user = (new User)->getUser('', $user_filters);

            // 👷 Get Field Worker
            $filters = [
                'field_worker_user_id' => $user->user_id,
            ];
            $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

            $currentDate = Carbon::today()->toDateString();

            // ❗ Prevent double active session
            $activeSession = AttendanceLogsModel::where('field_worker_id', $field_worker->id)
                ->whereDate('date', $currentDate)
                ->whereNull('logout_time')
                ->exists();

            if ($activeSession) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Already Attendance-in',
                    'data' => []
                ], 422);
            }

            // ✅ Main Worker Attendance (Get or Create)
            $attendance = AttendanceModel::firstOrCreate(
                [
                    'field_worker_id' => $field_worker->id,
                    'date' => $currentDate,
                ],
                [
                    'created_by' => $user->user_id,
                    'total_sessions' => 0
                ]
            );

            // ✅ Create Login Log
            AttendanceLogsModel::create([
                'attendance_id' => $attendance->id,
                'field_worker_id' => $field_worker->id,
                'created_by' => $user->user_id,
                'date' => $currentDate,
                'login_lat' => $request->login_lat,
                'login_long' => $request->login_long,
                'login_address' => $request->login_address,
            ]);

            // 🔄 Update total sessions (main worker)
            $totalSessions = AttendanceLogsModel::where('attendance_id', $attendance->id)->count();

            $attendance->update([
                'total_sessions' => $totalSessions
            ]);

            // 👥 Other Field Workers
            $field_workers = $request->field_workers ?? [];

            foreach ($field_workers as $fw) {

                // ❗ Prevent double active session for others
                $active = AttendanceLogsModel::where('field_worker_id', $fw)
                    ->whereDate('date', $currentDate)
                    ->whereNull('logout_time')
                    ->exists();

                if ($active) {
                    continue; // skip instead of breaking whole flow
                }

                // ✅ Get or Create Attendance
                $otherAttendance = AttendanceModel::firstOrCreate(
                    [
                        'field_worker_id' => $fw,
                        'date' => $currentDate,
                    ],
                    [
                        'created_by' => $user->user_id,
                        'total_sessions' => 0
                    ]
                );

                // ✅ Create Log
                AttendanceLogsModel::create([
                    'attendance_id' => $otherAttendance->id,
                    'field_worker_id' => $fw,
                    'created_by' => $user->user_id,
                    'date' => $currentDate,
                    'login_lat' => $request->login_lat,
                    'login_long' => $request->login_long,
                    'login_address' => $request->login_address,
                ]);

                // 🔄 Update total sessions
                $totalSessions = AttendanceLogsModel::where('attendance_id', $otherAttendance->id)->count();

                $otherAttendance->update([
                    'total_sessions' => $totalSessions
                ]);
            }

            // 🚚 Vehicle Mapping
            $total_field_workers = count($field_workers) + 1;

            AttendanceVehicleModel::create([
                'attendance_id' => $attendance->id,
                'date' => $currentDate,
                'vehicle_id' => $request->vehicle_id,
                'total_field_workers' => $total_field_workers,
            ]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Attendance logged in successfully',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'total_sessions' => $totalSessions
                ]
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() . ' | Line: ' . $e->getLine(), // remove in production
                'data' => []
            ], 500);
        }
    }

    public function attendanceOut(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'logout_lat' => 'required',
            'logout_long' => 'required',
            'logout_address' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        DB::beginTransaction();

        try {

            // 🔐 Get User
            $user_filters = [
                'login_token' => $request->bearerToken()
            ];
            $user = (new User)->getUser('', $user_filters);

            if (!$user) {
                throw new \Exception("User not found");
            }

            // 👷 Get Field Worker
            $filters = [
                'field_worker_user_id' => $user->user_id,
            ];
            $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

            if (!$field_worker) {
                throw new \Exception("Field worker not found");
            }

            $currentDate = Carbon::today()->toDateString();

            // ❗ Get Active Session
            $attendance_log = AttendanceLogsModel::where('field_worker_id', $field_worker->id)
                ->whereDate('date', $currentDate)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first();

            if (!$attendance_log) {
                DB::rollBack();
                return response()->json([
                    'status' => false,
                    'message' => 'Already Attendance-out',
                    'data' => []
                ], 422);
            }

            // ⏱️ Calculate Session Work Minutes (CORRECT WAY)
            $loginTime = Carbon::parse($attendance_log->login_time);
            $logoutTime = Carbon::now();

            $this_session_work_minutes = $logoutTime->diffInMinutes($loginTime);

            // ✅ Update Log
            $attendance_log->update([
                'logout_time' => $logoutTime,
                'logout_lat' => $request->logout_lat,
                'logout_long' => $request->logout_long,   // ✅ FIXED
                'logout_address' => $request->logout_address, // ✅ FIXED
                'work_minutes' => $this_session_work_minutes,
            ]);

            // 📊 Update Attendance Total
            $attendance = AttendanceModel::where('id', $attendance_log->attendance_id)->first();

            $total_work_minutes = AttendanceLogsModel::where('attendance_id', $attendance->id)
                ->sum('work_minutes');

            $attendance->update([
                'total_work_minutes' => $total_work_minutes
            ]);

            // 👥 Logout Other Workers (Created by same user)
            $other_attendances = AttendanceModel::where('created_by', $user->user_id)
                ->where('date', $currentDate)
                ->where('field_worker_id', '!=', $field_worker->id)
                ->get();

            foreach ($other_attendances as $other) {

                $log = AttendanceLogsModel::where('attendance_id', $other->id)
                    ->whereNull('logout_time')
                    ->latest('login_time')
                    ->first();

                if (!$log) {
                    continue; // skip if already logged out
                }

                $loginTime = Carbon::parse($log->login_time);
                $logoutTime = Carbon::now();

                $minutes = $logoutTime->diffInMinutes($loginTime);

                $log->update([
                    'logout_time' => $logoutTime,
                    'logout_lat' => $request->logout_lat,
                    'logout_long' => $request->logout_long,
                    'logout_address' => $request->logout_address,
                    'work_minutes' => $minutes,
                ]);

                // update total
                $total = AttendanceLogsModel::where('attendance_id', $other->id)
                    ->sum('work_minutes');

                $other->update([
                    'total_work_minutes' => $total
                ]);
            }

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => 'Attendance logged out successfully',
                'data' => [
                    'attendance_id' => $attendance->id,
                    'this_session_minutes' => $this_session_work_minutes,
                    'total_work_minutes' => $total_work_minutes
                ]
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage() . ' | Line: ' . $e->getLine(),
                'data' => []
            ], 500);
        }
    }

} //end class getVehiclesForAttendance
