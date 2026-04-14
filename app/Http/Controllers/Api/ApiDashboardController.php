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
use App\Models\AttendanceLogsModel;

use Illuminate\Support\Facades\Hash;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ApiDashboardController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function dashboard(Request $request)
    {
        $user_filters = [
            'login_token' => $request->bearerToken()
        ];
        $user = (new User)->getUser('', $user_filters);

        $municipality_filters = [
            'user_id' => $user->municipality_id
        ];
        $municipality = (new User)->getUser('', $municipality_filters);

        $filters = [
				'field_worker_user_id' => $user->user_id,
        ];
        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

        $currentDate = Carbon::today()->toDateString();
        $attendance_data = $this->commonService->getAttendanceDetails($field_worker->id,$currentDate);

        $data = [
            'municipality'=>$municipality,
            'user'=>$user,
            'field_worker'=>$field_worker,
            'attendance_data'=>$attendance_data,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Dashboard data fetched successfully',
            'data' => $data
        ], 200);
    }

    public function profile(Request $request)
    {
        $user_filters = [
            'login_token' => $request->bearerToken()
        ];
        $user = (new User)->getUser('', $user_filters);

        $filters = [
            'field_worker_user_id' => $user->user_id,
        ];
        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);
        
        $currentDate = Carbon::today()->toDateString();
        $attendance_data = $this->commonService->getAttendanceDetails($field_worker->id,$currentDate);

        $data = [
            'profile'=>$user,
            'field_worker'=>$field_worker,
            'attendance_data'=>$attendance_data,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Profile data fetched successfully',
            'data' => $data
        ], 200);
    }

    public function updateProfile(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'name'        => 'required|string',
            // 'email'       => [
            //     'required', 'email',
            //     Rule::unique('user', 'email')
            //         ->ignore($user->user_id, 'user_id'),
            // ],
            'profile_pic' => 'nullable|mimes:jpg,jpeg,png|max:2048',
            'address'        => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $update_user = User::find($user->user_id);

        if ($request->hasFile('profile_pic')) {
            $file = $request->file('profile_pic');
            $filename = time() . '_' . $file->getClientOriginalName();
            $filePath = 'uploads/citizen/' . $filename;

            if ($update_user->filepath && file_exists(public_path($update_user->filepath))) {
                unlink(public_path($update_user->filepath));
            }

            $file->move(public_path('uploads/citizen'), $filename);
            $update_user->filepath = $filePath;
        }

        $update_user->name = $request->name;
        $update_user->save();

        $user_details = UserDetails::where('user_id',$user->user_id)->first();
        $user_details->address = $request->address;
        $user_details->save();

        $field_worker = FieldWorkersModel::where('user_id',$user->user_id)->first();
        $field_worker->field_worker_name = $request->name;
        $field_worker->address = $request->address;
        $field_worker->save();

        $user_filters = [
            'login_token' => $request->bearerToken()
        ];
        $user = (new User)->getUser('', $user_filters);

        $filters = [
				'field_worker_user_id' => $user->user_id,
        ];
        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

        $data = [
            'profile'=>$user,
            'field_worker'=>$field_worker,
        ];

        return response()->json([
            'status' => true,
            'message' => 'Profile data updated successfully',
            'data' => $data
        ], 200);
    }

    public function changePassword(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        if (!$user) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid token.',
                'data' => []
            ], 401);
        }

        $validator = Validator::make($request->all(), [
            'current_password' => 'required|string',
            'new_password' => [
                'required',
                'string',
                'min:6',
            ],
            'confirm_new_password' => [
                'required',
                'same:new_password',
            ],
        ], [
            'confirm_new_password.same' => 'The new password confirmation does not match.',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        if (Hash::check($request->current_password, $user->password)) {

            User::where('user_id', $user->user_id)->update([
                'password' => Hash::make($request->new_password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Password successfully changed.',
                'data' => []
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password. Please check your current password and try again.',
                'data' => []
            ], 401);
        }
    }

    public function logoutFromApplication(Request $request)
    {
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

            if (!$field_worker) {
                return response()->json([
                    'status' => false,
                    'message' => 'Field worker not found',
                    'data' => []
                ], 404);
            }

            // ❗ Check active session (NO NEED date filter)
            $activeSession = AttendanceLogsModel::where('field_worker_id', $field_worker->id)
                ->whereNull('logout_time')
                ->exists();

            if ($activeSession) {
                return response()->json([
                    'status' => false,
                    'message' => 'Please complete attendance out before logging out',
                    'data' => []
                ], 422);
            }

            // 🔓 Logout User
            $dbUser = User::find($user->user_id);
            if ($dbUser) {
                $dbUser->login_token = null; // better than ""
                $dbUser->save();
            }

            return response()->json([
                'status' => true,
                'message' => 'Successfully logged out',
                'data' => []
            ], 200);

        } catch (\Exception $e) {

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

} //end class 
