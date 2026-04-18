<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\CollectionsModel;
use App\Models\FieldWorkersModel;
use App\Models\AttendanceLogsModel;
use App\Models\AttendanceVehicleModel;
use App\Models\PlantMasterModel;
use App\Models\DisposalModel;
use App\Models\DisposalDetailsModel;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApiCollectionController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function collection_submit(Request $request)
    {
        DB::beginTransaction();

        try {

            $user = User::where('login_token', $request->bearerToken())->first();

            $filters = [
                'field_worker_user_id' => $user->user_id,
            ];

            $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

            $currentDate = Carbon::today()->toDateString();

            // ✅ Active session check
            $attendance_log = AttendanceLogsModel::where('field_worker_id', $field_worker->id)
                ->whereDate('date', $currentDate)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first();

            if (!$attendance_log) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have to attendance-in before collection',
                    'data' => []
                ], 422);
            }

            // ✅ FIX: use first() instead of get()
            $vehicle = AttendanceVehicleModel::where('attendance_id', $attendance_log->attendance_id)->first();

            if (!$vehicle) {
                return response()->json([
                    'status' => false,
                    'message' => 'Vehicle not assigned',
                    'data' => []
                ], 422);
            }

            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'service_id' => 'required|numeric|exists:module_master,id',
                'service_boundary' => 'required',
                'ward_id' => 'nullable|numeric',
                'road_id' => 'nullable|numeric',
                'outside_boundary_details' => 'nullable|string',
                'nature_of_service' => 'required|numeric|exists:nature_of_services,id',
                'other_nature_of_service_details' => 'nullable|string',
                'type_of_building' => 'required|numeric|exists:types_of_buildings,id',
                'accessibility' => 'required|numeric|exists:accessibility_types,id',
                'accessibility_details' => 'nullable|string',
                'volume_quantity' => 'required|numeric',
                'tank_open_duration' => 'required|numeric|exists:tank_open_durations,id',
                'no_of_users' => 'required|numeric',
                'last_cleaned_date' => 'required|date',
                'beneficiary_name' => 'required|string', // ✅ FIXED
                'beneficiary_contact_number' => 'required|numeric',
                'beneficiary_lat' => 'required|string',
                'beneficiary_long' => 'required|string',
                'address' => 'required|string',
                'image' => 'required|file',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 422);
            }

            // ✅ Trip count
            $trip_count = CollectionsModel::whereDate('created_at', $currentDate)
                ->where('created_by', $user->user_id)
                ->count();

            $trip = $trip_count + 1;

            // ✅ Unique UID
            do {
                $uid = $this->commonService->getToken(15, 'UID');
            } while (CollectionsModel::where('uid', $uid)->exists());

            // ✅ File Upload
            $filePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/collection'), $filename);
                $filePath = 'uploads/collection/' . $filename;
            }

            // ✅ Insert
            $collection_data = [
                'service_id' => $request->service_id,
                'uid' => $uid,
                'trip_number' => $trip,
                'municipality_id' => $field_worker->municipality_id,
                'service_boundary' => $request->service_boundary,
                'ward_id' => $request->ward_id,
                'road_id' => $request->road_id,
                'outside_boundary_details' => $request->outside_boundary_details,
                'nature_of_service' => $request->nature_of_service,
                'other_nature_of_service_details' => $request->other_nature_of_service_details,
                'type_of_building' => $request->type_of_building,
                'accessibility' => $request->accessibility,
                'accessibility_details' => $request->accessibility_details,
                'volume_quantity' => $request->volume_quantity,
                'tank_open_duration' => $request->tank_open_duration,
                'no_of_users' => $request->no_of_users,
                'last_cleaned_date' => $request->last_cleaned_date,
                'beneficiary_name' => $request->beneficiary_name,
                'beneficiary_contact_number' => $request->beneficiary_contact_number,
                'beneficiary_lat' => $request->beneficiary_lat,
                'beneficiary_long' => $request->beneficiary_long,
                'address' => $request->address,
                'image' => $filePath,
                'created_by' => $user->user_id,
                'vehicle_id' => $vehicle->vehicle_id,
            ];

            $collection = CollectionsModel::create($collection_data);

            // ✅ FIX: use request value
            $service_master_name = $this->commonService->getField(
                'module_master',
                ['id' => $request->service_id],
                'name'
            );

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Collection ({$uid}) regarding {$service_master_name} has been successfully completed.",
                'data' => $collection
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(), // remove in production
                'data' => []
            ], 500);
        }
    }

    public function collection_list(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'service_id' => 'nullable|numeric',
            'from_date' => 'nullable|date',
            'to_date'   => 'nullable|date',
            'page'   => 'required|numeric',
            'per_page'   => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $filters = [
            'user_id'   => $user->user_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'service_id' => $request->service_id,
        ];        
        
        $params = (new CollectionsModel)->get_collection_list(null , $filters , false , $request->per_page, $request->page);

        $total_records = (new CollectionsModel)->get_collection_list(null , $filters , true , null, null);

        return response()->json([
            'status' => true,
            'message' => "Your Collection List.",
            'data' => $params,
            'total_records' => $total_records,
            'records_showing' => sizeof($params),
            'page' => $request->page,
            'per_page' => $request->per_page,
        ], 200);
    }

    public function collection_details(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $filters = [
            'id' => $request->id,
        ];        
        
        $details = (new CollectionsModel)->get_collection_list(null , $filters , false , null, null);

        $data = [
            'details' => $details,
        ];

        return response()->json([
            'status' => true,
            'message' => "Your Collection Details.",
            'data' => $data,
        ], 200);
    }

    function collection_summary(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $filters = [
            'field_worker_user_id' => $user->user_id,
        ];

        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);
        
        $filters = [
            'user_id'   => $user->user_id,
            //'from_date' => $request->from_date,
            //'to_date' => $request->to_date,
            //'service_id' => $request->service_id,
        ];
        
        $params = (new CollectionsModel)->get_collection_summary($filters);
        return response()->json([
            'status' => true,
            'message' => "Your Collection Summary.",
            'data' => $params,
        ], 200);
    }

    function plants_list(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $filters = [
            'field_worker_user_id' => $user->user_id,
        ];

        $field_worker = (new FieldWorkersModel)->getFieldWorkers('', $filters);

        $filters = [
            'municipality_id' => $field_worker->municipality_id,
            'tagged_municipality_id' => $field_worker->municipality_id,
        ];
        $params = (new PlantMasterModel)->getPlants(null, $filters);

        return response()->json([
            'status' => true,
            'message' => "Plant List.",
            'data' => $params,
        ], 200);
    }

    public function disposal_submit(Request $request)
    {
        DB::beginTransaction();

        try {

            $user = User::where('login_token', $request->bearerToken())->first();

            if (!$user) {
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid user',
                    'data' => []
                ], 401);
            }

            // ✅ Get field worker
            $field_worker = (new FieldWorkersModel)->getFieldWorkers('', [
                'field_worker_user_id' => $user->user_id,
            ]);

            $currentDate = Carbon::today()->toDateString();

            // ✅ Active attendance
            $attendance_log = AttendanceLogsModel::where('field_worker_id', $field_worker->id)
                ->whereDate('date', $currentDate)
                ->whereNull('logout_time')
                ->latest('login_time')
                ->first();

            if (!$attendance_log) {
                return response()->json([
                    'status' => false,
                    'message' => 'You have to attendance-in before disposal',
                    'data' => []
                ], 422);
            }

            // ✅ Vehicle check
            $vehicle = AttendanceVehicleModel::where('attendance_id', $attendance_log->attendance_id)->first();

            // ✅ Validation
            $validator = Validator::make($request->all(), [
                'plant_id' => 'required|numeric|exists:plant_masters,id',
                'incharge_id' => 'required|numeric|exists:plant_master_incharges,id',
                'disposal_lat' => 'required|string',
                'disposal_long' => 'required|string',
                'disposal_address' => 'required|string',
                'image' => 'required|file|mimes:jpg,jpeg,png|max:2048',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => false,
                    'message' => $validator->errors()->first(),
                    'data' => []
                ], 422);
            }

            // ✅ Get collections (ONLY active ones)
            $collection_summary = (new CollectionsModel)->get_collection_summary([
                'user_id' => $user->user_id,
            ]);

            $summary = $collection_summary['summary'];

            if (sizeof($summary) == 0) {
                return response()->json([
                    'status' => false,
                    'message' => 'No collections available for disposal',
                    'data' => []
                ], 422);
            }

            $total_volume_quantity = $collection_summary['total_volume_quantity'];

            // ✅ UID
            do {
                $uid = $this->commonService->getToken(15, 'UID');
            } while (DisposalModel::where('uid', $uid)->exists());

            // ✅ Upload
            $filePath = null;
            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/disposal'), $filename);
                $filePath = 'uploads/disposal/' . $filename;
            }

            // ✅ Create disposal
            $disposal = DisposalModel::create([
                'uid' => $uid,
                'municipality_id' => $field_worker->municipality_id,
                'plant_id' => $request->plant_id,
                'quantity' => $total_volume_quantity, // numeric
                'incharge_id' => $request->incharge_id,
                'created_by' => $user->user_id,
                'vehicle_id' => $vehicle->vehicle_id,
                'disposal_lat' => $request->disposal_lat,
                'disposal_long' => $request->disposal_long,
                'disposal_address' => $request->disposal_address,
                'image' => $filePath,
            ]);

            // ✅ Prepare bulk insert + update
            $collection_ids = [];
            $details_data = [];

            foreach ($summary as $item) {
                $collection_ids[] = $item->id;

                $details_data[] = [
                    'disposal_id' => $disposal->id,
                    'collection_id' => $item->id,
                ];
            }

            // ✅ Bulk insert
            DisposalDetailsModel::insert($details_data);

            // ✅ Bulk update (IMPORTANT)
            CollectionsModel::whereIn('id', $collection_ids)
                ->update(['status' => 2]);

            DB::commit();

            return response()->json([
                'status' => true,
                'message' => "Disposal ({$uid}) has been successfully completed.",
                'data' => $disposal
            ], 200);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status' => false,
                'message' => 'Something went wrong',
                'error' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    function disposal_list(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'from_date' => 'nullable|date',
            'to_date'   => 'nullable|date',
            'page'   => 'required|numeric',
            'per_page'   => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $filters = [
            'user_id'   => $user->user_id,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
        ];        
        
        $params = (new DisposalModel)->get_disposal_list(null , $filters , false , $request->per_page, $request->page);

        $total_records = (new DisposalModel)->get_disposal_list(null , $filters , true , null, null);

        return response()->json([
            'status' => true,
            'message' => "Your Disposal List.",
            'data' => $params,
            'total_records' => $total_records,
            'records_showing' => sizeof($params),
            'page' => $request->page,
            'per_page' => $request->per_page,
        ], 200);
    }

    public function disposal_details(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $filters = [
            'id' => $request->id,
        ];        
        
        $details = (new DisposalModel)->get_disposal_list(null , $filters , false , null, null);

        $data = [
            'details' => $details,
        ];

        return response()->json([
            'status' => true,
            'message' => "Your Disposal Details.",
            'data' => $data,
        ], 200);
    }


} //end class  
