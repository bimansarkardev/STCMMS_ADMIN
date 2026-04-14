<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Models\AgencyRegistrationRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use App\Services\CommonService;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Ward;
use App\Models\Road;
use App\Models\UserDetails;
use App\Models\UserDevice;
use App\Models\FieldWorkersModel;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class ApiAuthController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        //parent::__construct();
        $this->commonService = $commonService;
    }

    public function getCities()
    {
        $cities = $this->commonService->getListData("cities", ['state_id'=>41], ['id','name'], 'id', 'asc');
        return response()->json([
            'status' => true,
            'message' => 'Cities fetched successfully',
            'data' => $cities
        ], 200);
    }

    public function getMunicipalities(Request $request)
    {
        // $validator = Validator::make($request->all(), [
        //     'city' => 'nullable|numeric',
        // ]);

        // if ($validator->fails()) {
        //     return response()->json([
        //         'status' => false,
        //         'message' => $validator->errors()->first(),
        //         'data' => []
        //     ], 422);
        // }

        $filters = [
            'user_type' => 2,
            //'city' => $request->city,
        ];

        $users = (new User)->getMunicipalities($filters);
        // dd($users);

        if ($users && count($users) > 0) {
            return response()->json([
                'status' => true,
                'message' => 'Municipalities fetched successfully',
                'data' => $users
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'No Municipality found under this city',
                'data' => []
            ], 404);
        }
    }

    public function getWard(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'municipality' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $filters = [
            'status'=>0,
            'municipality'=>$request->municipality,
            'orderBy'=>'asc',
            'orderField'=>'ward_no',
        ];

        $params = (new Ward)->getWard("", $filters);


        if ($params && count($params) > 0) 
        {
            return response()->json([
                'status' => true,
                'message' => 'Ward numbers fetched successfully',
                'data' => $params
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'No Ward found under this municipality',
                'data' => []
            ], 404);
        }
    }

    function getRoads(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'ward' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $filters = [
            'status'=>0,
            'ward'=>$request->ward,
            'orderBy'=>'asc',
            'orderField'=>'road_name',
        ];

        $params = (new Road)->getRoad("", $filters);

        if ($params && count($params) > 0) 
        {
            return response()->json([
                'status' => true,
                'message' => 'Roads fetched successfully',
                'data' => $params
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'No Roads found under this ward',
                'data' => []
            ], 404);
        }
    }

    public function registration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'nullable',
                'required',
                'regex:/^[1-9][0-9]{9}$/',
                'unique:user,mobile',
            ],
            'name' => 'required|string',
            'municipality' => 'required|exists:user,user_id',
            'ward' => 'nullable|numeric|exists:ward,id',
            'password' => [
                'required',
                'string',
                'min:6',
            ],

            'confirm_password' => [
                'required',
                'same:password',
            ],
        ], [
            'confirm_password.same' => 'The password confirmation does not match.',
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
            $data = [
                'user_type_id' => 5,
                'name' => $request->name,
                //'email' => $request->email,
                'mobile' => $request->mobile,
                'created_by' => $request->municipality,
                //'otp' => $this->commonService->getToken(4, 'OTP')
                'otp' => 1234,
                'password' => Hash::make($request->password),
            ];

            $user = User::create($data);

            $municipalityDetails = UserDetails::where('user_id', $request->municipality)->first();

            $details_data = [
                'user_id' => $user->user_id,
                'city' => $municipalityDetails->city,
                'municipality' => $request->municipality,
                'ward' => $request->ward,
            ];

            UserDetails::create($details_data);

            DB::commit();

            $message = "An OTP has been sent to the provided mobile number. Please verify it to complete your registration.";

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'user_id' => $user->user_id,
                    'resend_after' => Carbon::now()->addMinutes(2)->toDateTimeString()
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Registration failed. Please try again.',
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function verifyRegistrationOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('user_id', $request->user_id)->first();

        if($request->otp == $user->otp)
        {
            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully. Registration completed. Please log in with your mobile number and password.',
                'data' => [
                    'user_id' => $user->user_id
                ]
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please check the OTP and try again. If you haven’t received it, you can request a new one by clicking on Resend.',
                'data' => []
            ], 404);
        }
    }

    public function resendOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::findOrFail($request->user_id);

        $data = [
            //'otp' => $this->commonService->getToken(4, 'OTP'),
            'otp' => 3456
        ];

        $user->update($data);

        return response()->json([
            'status' => true,
            'message' => 'Another OTP has been sent to the provided mobile number. Please verify it to continue.',
            'data' => [
                'user_id' => $user->user_id,
                'resend_after' => Carbon::now()->addMinutes(2)->toDateTimeString()
            ]
        ], 200);
    }

    public function login_old(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('mobile', $request->mobile)->first();

        if(!empty($user))
        {
            $data = [
                //'otp' => $this->commonService->getToken(4, 'OTP')
                'otp' => 1234
            ];

            $user->update($data);

            $message = "An OTP has been sent to the provided mobile number. Please verify it to login.";
            //$edit_text = "Edit mobile number";

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'user_id' => $user->user_id
                ]
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'No account found with the provided mobile number. Please register to create a new account.',
                'data' => [],
            ], 404);
        }
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile' => 'required|exists:user,username',
            'password' => 'required|string',
            'device_type'=>'required',
            'fcm_token'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('username', $request->mobile)->where('user_type_id', 3)->first();

        if (Hash::check($request->password, $user->password)) 
        {
            do {
                $login_token = $this->commonService->getToken(60, 'GENERAL');
                $exists = User::where('login_token', $login_token)->exists();
            } while ($exists);

            $data = [
                'login_token' => $login_token
            ];

            $user->update($data);

            $userDevice = UserDevice::where('user_id', $user->user_id)
                        ->where('fcm_token', $request->fcm_token)
                        ->where('device_type', $request->device_type)
                        ->first();

            if ($userDevice) {
                $userDevice->login_token = $login_token;
            } else {
                $userDevice = new UserDevice();
                $userDevice->user_id = $user->user_id;
                $userDevice->device_type = $request->device_type ?? 'unknown';
                $userDevice->login_token = $login_token;
                $userDevice->fcm_token = $request->fcm_token;               
            }
            $userDevice->save();

            $filters = [
                'user_id' => $user->user_id,
            ];
            $userDetails = (new User)->getUser('', $filters);

            $filters = [
				'field_worker_user_id' => $user->user_id,
			];
			$field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

            $currentDate = Carbon::today()->toDateString();
            $attendance_data = $this->commonService->getAttendanceDetails($field_worker->id,$currentDate);

            return response()->json([
                'status' => true,
                'message' => 'Successfully loggedin.',
                'data' => [
                    'user_id' => $user->user_id,
                    'user' => $userDetails,
                    'field_worker' => $field_worker,
                    'login_token' => $login_token,
                    'attendance_data' => $attendance_data
                ]
            ], 200);
        
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password. Please check your credentials and try again.',
                'data' => []
            ], 401);
        }
    }

    function login_new(Request $request) //reg and login at once
    {
        $validator = Validator::make($request->all(), [
            'mobile' => [
                'required',
                'regex:/^[1-9][0-9]{9}$/',
                //'exists:user,mobile',
            ],
            //'municipality' => 'required|exists:user,user_id',
            //'ward' => 'required|numeric|exists:ward,id',
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

            $user = User::where('mobile', $request->mobile)->first();

            if(!empty($user))
            {
                $data = [
                    //'otp' => $this->commonService->getToken(4, 'OTP')
                    'otp' => 1234
                ];

                $user->update($data);

                $message = "An OTP has been sent to the provided mobile number. Please verify it to login.";

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => [
                        'user_id' => $user->user_id
                    ]
                ], 200);
            }
            else
            {
                $data = [
                    'user_type_id' => 5,
                    'name' => $request->name,
                    //'email' => $request->email,
                    'mobile' => $request->mobile,
                    'created_by' => $request->municipality,
                    //'otp' => $this->commonService->getToken(4, 'OTP')
                    'otp' => 1234
                ];

                $user = User::create($data);

                $municipalityDetails = UserDetails::where('user_id', $request->municipality)->first();

                $details_data = [
                    'user_id' => $user->user_id,
                    'city' => $municipalityDetails->city,
                    'municipality' => $request->municipality,
                    //'ward' => $request->ward,
                ];

                UserDetails::create($details_data);

                DB::commit();

                $message = "Registration successful. An OTP has been sent to the provided mobile number. Please verify it to login.";

                return response()->json([
                    'status' => true,
                    'message' => $message,
                    'data' => [
                        'user_id' => $user->user_id,
                        'resend_after' => Carbon::now()->addMinutes(2)->toDateTimeString()
                    ]
                ], 200);
            }            

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Login failed. Please try again.',
                'data' => [],
                'error' => $e->getMessage(),
            ], 500);
        }
        
    }

    public function verifyLoginOtp(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id',
            'otp' => 'required|numeric',
            'device_type'=>'required',
            'fcm_token'=>'required'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('user_id', $request->user_id)->first();

        if($request->otp == $user->otp)
        {
            do {
                $login_token = $this->commonService->getToken(60, 'GENERAL');
                $exists = User::where('login_token', $login_token)->exists();
            } while ($exists);

            $data = [
                'login_token' => $login_token
            ];

            $user->update($data);

            $userDevice = UserDevice::where('user_id', $user->user_id)
                        ->where('fcm_token', $request->fcm_token)
                        ->where('device_type', $request->device_type)
                        ->first();
            if ($userDevice) {
                $userDevice->login_token = $login_token;
            } else {
                $userDevice = new UserDevice();
                $userDevice->user_id = $user->user_id;
                $userDevice->device_type = $request->device_type ?? 'unknown';
                $userDevice->login_token = $login_token;
                $userDevice->fcm_token = $request->fcm_token;               
            }
            $userDevice->save();

            return response()->json([
                'status' => true,
                'message' => 'OTP verified, successfully loggedin.',
                'data' => [
                    'user_id' => $user->user_id,
                    'login_token' => $login_token,
                ]
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please check the OTP and try again. If you haven’t received it, you can request a new one by clicking on Resend.',
                'data' => []
            ], 404);
        }
    }

    public function agencyRegistration(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'municipality_id' => 'required|exists:user,user_id',
            'agency_name' => 'required|string',
            'contact_person' => 'required|string',
            'email' => 'required|email|unique:user,email',
            'mobile' => 'required|regex:/^[1-9][0-9]{9}$/|unique:user,mobile',
            'registration_no' => 'required|string',
            'address' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $data = [
            'municipality_id' => $request->municipality_id,
            'agency_type' => 1,
            'agency_name' => $request->agency_name,
            'contact_person' => $request->contact_person,
            'email' => $request->email,
            'mobile' => $request->mobile,
            'registration_no' => $request->registration_no,
            'address' => $request->address,
            // 'password' => bcrypt($request->password),
        ];

        $agencyRequest = AgencyRegistrationRequest::create($data);

        return response()->json([
            'status' => true,
            'message' => 'Agency registration request submitted successfully. It will be reviewed by the municipality and you will be notified about the status.',
            'data' => [
                'data' => $agencyRequest
            ]
        ], 200);
    }

    public function agencyLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:user,email',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('email', $request->email)->first();

        if (Hash::check($request->password, $user->password)) {
              do {
                $login_token = $this->commonService->getToken(20, 'GENERAL');
                $exists = User::where('login_token', $login_token)->exists();
            } while ($exists);

            $user->login_token = $login_token;
            $user->save();

            $agency_details = [
                'user_id' => $user->user_id,            ];
            $det = (new AgencyRegistrationRequest)->getAgencyRegistrationRequests($agency_details , '');

            $data = [
                'user' => $user,
                'agency_details' => $det,
                'login_token' => $login_token,
            ];

            return response()->json([
                'status' => true,
                'message' => 'Login successful.',
                'data' => $data
            ], 200);
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Invalid password. Please check your credentials and try again.',
                'data' => []
            ], 401);
        }
    }

    public function forgetPasswordGetOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'mobile_no' => 'required|min:10',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('username', $request->mobile_no)->first();

        if(!empty($user))
        {
            $data = [
                //'otp' => $this->commonService->getToken(4, 'OTP')
                'otp' => 1234
            ];

            $user->update($data);

            $message = "An OTP has been sent to your registered mobile number. Please verify it to reset your password.";

            return response()->json([
                'status' => true,
                'message' => $message,
                'data' => [
                    'user_id' => $user->user_id
                ]
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'No account found with the provided mobile number. Please register to create a new account.',
                'data' => [],
            ], 404);
        }
    }

    public function forgetPasswordVerifyOTP(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:user,user_id',
            'otp' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $user = User::where('user_id', $request->user_id)->first();

        if($request->otp == $user->otp)
        {
            return response()->json([
                'status' => true,
                'message' => 'OTP verified successfully. You can now reset your password.',
                'data' => [
                    'user_id' => $user->user_id,
                ]
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'OTP verification failed. Please check the OTP and try again. If you haven’t received it, you can request a new one by clicking on Resend.',
                'data' => []
            ], 404);
        }
    }

    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|numeric|exists:user,user_id',
            'password' => [
                'required',
                'string',
                'min:6',
            ],
            'confirm_password' => [
                'required',
                'same:password',
            ],
        ], [
            'confirm_password.same' => 'The password confirmation does not match.',
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

            $user = User::where('user_id', $request->user_id)->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'User not found.',
                    'data' => []
                ], 404);
            }

            $user->password = Hash::make($request->password);
            $user->save();

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Password successfully changed. Please login with your new password.",
                'data' => [
                    
                ]
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Reset password failed. Please try again.',
                'data' => [],
            ], 500);
        }
    }

} //end class 
