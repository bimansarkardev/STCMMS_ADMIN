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
use App\Models\MunicipalityModuleMaster;
use App\Models\Complaints;
use App\Models\LevelMaster;
use App\Models\ComplaintsTrail;
use App\Models\Notifications;
use App\Models\RatingsModel;
use App\Models\ModuleMaster;
use App\Models\ModuleMasterMunicipalitySettings;
use App\Models\BookingsModel;
use App\Models\PyementMethodModel;
use App\Models\PaymentsModel;
use App\Models\BookingsTrailModel;
use App\Models\ModuleMasterMunicipalitySettingsType;
use App\Models\BookingStates;
use App\Models\BookingsCancelRequestModel;
use App\Models\PaymentsShopModel;
use App\Models\RentPaymentModel;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class ApiShopPaymentController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function getShopTypes(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $types = ModuleMasterMunicipalitySettingsType::select('id', 'name', 'status')
            ->where('module_master', 6)
            ->get();

        $data = [
            'shop_types' => $types
        ];

        return response()->json([
            'status' => true,
            'message' => $types->isNotEmpty()
                ? 'Shop types list fetched successfully'
                : 'Shop types not found',
            'data' => $data
        ], 200);
    }

    function getShops(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'shop_type' => 'nullable|numeric|exists:module_master_municipality_settings_type,id',
            'ward' => 'nullable|numeric|exists:ward,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }

        $filters = [
	        'module_master_id' => 6,
	    ];
	    $module = (new MunicipalityModuleMaster)->getMunicipalityModuleMaster($filters);

        //dd($module);

		if (!$module) 
        {
            return response()->json([
                'status' => false,
                'message' => 'Invalid service ID',
            ], 400);
		}

        $filters = [
	        'module_master_id' => $module->module_master_id,
	        'ward' => $request->ward,
	        'type' => $request->shop_type,
	        'tenant_mobile' => $user->mobile,
	    ];

        //dd($filters);
		
	    $params = (new ModuleMasterMunicipalitySettings)->getModuleMasterMunicipalitySettings($filters);

        $data = [
            'tenant_mobile' => $user->mobile,
        ];

        if(count($params)>0)
        {
            $params = $params->map(function ($item) {
                return [
                    'id'                           => $item->id,
                    'municipality_id'              => $item->municipality_id,
                    'municipality_module_master_id'=> $item->municipality_module_master_id,
                    'name'                         => $item->name,
                    'type'                         => $item->type,
                    'type_name'                    => $item->type_name,
                    'ward'                         => $item->ward,
                    'address'                      => $item->address,
                    'tenant_name'                  => $item->tenant_name,
                    'tenant_mobile'                => $item->tenant_mobile,
                    'monthly_rent'                 => $item->monthly_rent,
                    'due_day'                      => $item->due_day,
                    'last_payment_date'            => $item->last_payment_date,
                    'created_at'                   => $item->created_at,
                    'updated_at'                   => $item->updated_at,
                    'status'                       => $item->status,
                    'active_stat'                  => $item->active_stat,
                ];
            });

            $data = [
                'shops' => $params,
            ];
            return response()->json([
                'status' => true,
                'message' => 'Shop list fetched successfully',
                'data' => $data
            ], 200);
        }
        else
        {
             return response()->json([
                'status' => false,
                'message' => 'We couldn’t find any shops linked to your account for rent payment. Please contact the municipality to add your shop details.',
                'data' => $data
            ], 400);
        }
    }

    public function generateTransactionIdShopPayment(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'shop_id' => 'required|numeric',
            'monthly_rent' => 'required|numeric',
            'from_month_year' => 'required|string',
            'to_month_year' => 'required|string',
            'total_month' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $checkExists = PaymentsShopModel::where([
            'shop_id' => $request->shop_id,
            'monthly_rent' => $request->monthly_rent,
            'from_month_year' => $request->from_month_year,
            'to_month_year' => $request->to_month_year,
            'total_month' => $request->total_month,
            'amount' => $request->amount,
            'user_id' => $user->user_id,
            'created_by' => $user->user_id,
            'municipality_id' => $user->created_by,
            'payment_method' => 1,
            'status' => 1,
        ])->first();

        if ($checkExists) {
            return response()->json([
                'status' => true,
                'message' => 'Payment already initiated',
                'data' => [
                    'transaction' => $checkExists
                ]
            ], 200);
        }
        
        do {
            $transaction_id = $this->commonService->getToken(15, 'UID');
            $exists = PaymentsShopModel::where('transaction_id', $transaction_id)->exists();
        } while ($exists);
        
        $payment = [
            'shop_id' => $request->shop_id,
            'monthly_rent' => $request->monthly_rent,
            'from_month_year' => $request->from_month_year,
            'to_month_year' => $request->to_month_year,
            'total_month' => $request->total_month,
            'amount' => $request->amount,
            'user_id' => $user->user_id,
            'created_by' => $user->user_id,
            'municipality_id' => $user->created_by,
            'transaction_id' => $transaction_id,
            'payment_method' => 1,
            'status' => 1,
        ];

        $transaction = PaymentsShopModel::create($payment);

        return response()->json([
            'status' => true,
            'message' => 'Shop payment transaction ID generated successfully',
            'data' => [
                'transaction' => $transaction
            ],
        ], 200);
    }

    public function makeShopPayment(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'transaction_id' => 'required|string|exists:payments_shop,transaction_id',
            'payment_date' => 'required|date',
            'ward' => 'required|numeric',
            'name' => 'required|string',
            'mobile' => [
                'required',
                'numeric',
                'regex:/^[1-9][0-9]{9}$/',
            ],
            'alternative_mobile' => [
                'nullable',
                'numeric',
                'regex:/^[1-9][0-9]{9}$/',
            ],
            'email' => 'required|email',
            'address' => 'required|string',
            'contact_person_name' => 'required|string',
            'shop_id' => 'required|numeric|exists:module_master_municipality_settings,id',
            'tenant_name' => 'required|string',
            'monthly_rent' => 'required|numeric',
            'from_month_year' => 'required|string',
            'to_month_year' => 'required|string',
            'total_month' => 'required|numeric',
            'amount' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->errors()->first(),
                'data' => []
            ], 422);
        }
        
        $from_month_year = $request->from_month_year;
        $to_month_year   = $request->to_month_year;
        $shop_id         = $request->shop_id;
        
        if ($from_month_year > $to_month_year) {
            return response()->json([
                'status'  => false,
                'message' => 'From Month-Year must be less than or equal to To Month-Year.',
                'data'    => []
            ], 422);
        }
        
        $check = RentPaymentModel::where('shop_id', $shop_id)
            ->where('from_month_year', '<=', $to_month_year)
            ->where('to_month_year', '>=', $from_month_year)
            ->first();

        if ($check) {
            return response()->json([
                'status'  => false,
                'message' => 'Already paid for your selected Month-Year.',
                'data'    => []
            ], 422);
        }

        //dd($check);

        $service_item = ModuleMasterMunicipalitySettings::find($request->shop_id);
        
        $filters = [
            'id' => $service_item->municipality_module_master_id,
        ];
        $service = (new MunicipalityModuleMaster)->getMunicipalityModuleMaster($filters);

        $transactionDetails = PaymentsShopModel::where('transaction_id',$request->transaction_id)->first();

        //dd($transactionDetails);

        if($transactionDetails->rent_payment_id!="")
        {
            return response()->json([
                'status' => false,
                'message' => 'This transaction id is already used for paymment',
                'data' => []
            ], 422);
        }
        
        $payment_status = $this->checkPaymentStatus($transactionDetails->id);
        if($payment_status=='SUCCESS')
        {
            do {
                $uid = $this->commonService->getToken(15, 'UID');
                $exists = RentPaymentModel::where('uid', $uid)->exists();
            } while ($exists);

            $rent_data = [
                'uid' => $uid,
                'municipality_id' => $user->created_by,
                'payment_id' => $transactionDetails->id,
                'payment_date' => $request->payment_date,
                'ward' => $request->ward,
                'name' => $request->name,
                'mobile' => $request->mobile,
                'alternative_mobile' => $request->alternative_mobile,
                'email' => $request->email,
                'address' => $request->address,
                'contact_person_name' => $request->contact_person_name,
                'shop_id' => $request->shop_id,
                'tenant_name' => $request->tenant_name,
                'monthly_rent' => $request->monthly_rent,
                'from_month_year' => $request->from_month_year,
                'to_month_year' => $request->to_month_year,
                'total_month' => $request->total_month,
                'amount' => $request->amount,
                'user_id' => $user->user_id,
                'cretead_by' => $user->user_id,
            ];

            $rent_payment = RentPaymentModel::create($rent_data);

            $transactionDetails->update([
                'rent_payment_id' => $rent_payment->id,
                'status' => 3,
            ]);

            /*NOTIFICATION AREA START*/

            $service_master_name = $this->commonService->getField('module_master', ['id'=>$service->module_master_id] ,'name');

            $user_notification_data = [
                'user_id' => $user->user_id,
                'title'   => "Paid: {$uid} - {$service_master_name}",
                'message' => "Your payment ({$uid}) regarding {$service_master_name} has been successfully completed."
            ];
            Notifications::create($user_notification_data);

            $admin_notification_data = [
                'user_id' => $user->created_by,
                'title'   => "New payment: {$uid} - {$service_master_name}",
                'message' => "A new payment ({$uid}) regarding {$service_master_name} has been paid by {$user->name}."
            ];
            Notifications::create($admin_notification_data);
            /*NOTIFICATION AREA END*/
            
            ModuleMasterMunicipalitySettings::where('id',$shop_id)->update(['last_payment_date'=>$request->payment_date]);

            $data = [
                'rent_payment'=>$rent_payment
            ];

            $filters = [
                'id' => $rent_payment->id,
                'municipality_id' => $user->created_by,
                'user_id' => $user->user_id,
            ];        
            
            $details = (new RentPaymentModel)->getShopPaymentList(null , $filters , false , null, null);

            //PDF generate and save
            $municipality = User::where('user_id',$service_item->municipality_id)->first();
			$municipality_det = UserDetails::where('user_id',$service_item->municipality_id)->first();

            

			$billData = [
				'title' => 'PUROSATHI - Shop Payment Details',
				'uid' => $details->uid,
				'service_master_title' => $details->service_master_name,
				'formatted_created_at' => $details->formatted_created_at,
				'user_name' => $details->name,
				'mobile' => $details->mobile,
				'email' => $details->email,
				'address' => $details->address,
				'user_ward_no' => $details->user_ward_no,
				'shop_name' => $details->shop_name,
				'shop_ward' => $details->shop_ward,
				'shop_address' => $details->shop_address,                
				'due_day' => $details->due_day,
				'tenant_name' => $details->tenant_name,
				'monthly_rent' => $details->monthly_rent,
				'from_month_year' => $details->from_month_year,
				'to_month_year' => $details->to_month_year,
				'total_month' => $details->total_month,
				'amount' => $details->amount,
				'last_payment_date' => $details->formatted_last_payment_date,
                
				'municipality_name' => $municipality->name ?? null,
				'municipality_address' => $municipality_det->address ?? null,
				'municipality_phone' => $municipality->mobile ?? null,
				'municipality_email' => $municipality->email ?? null,
			];

			// Create PDF from view
			$pdf = Pdf::loadView('PDF.bill_template_shop_payment', $billData);

			// Generate unique file name
			$fileName = 'PUROSATHI_SHOP_PAYMENT_BILL_' . $details->uid . '.pdf';

			// Relative path for DB
			$pdfPath = 'uploads/bills/' . $fileName;

			// Ensure folder exists
			if (!file_exists(public_path('uploads/bills'))) {
				mkdir(public_path('uploads/bills'), 0755, true);
			}

			// Save PDF to public folder
			$pdf->save(public_path($pdfPath));

			// Save path in DB
			$pdf_update = [
				'billPDF' => $pdfPath,
			];

            // Update parent booking
            $this->commonService->updateData("rent_payment", ['id' => $details->id], $pdf_update);
            //end PDF Section

            return response()->json([
                'status' => true,
                'message' => "Your payment ({$uid}) regarding {$service_master_name} has been successfully completed.",
                'data' => $data
            ], 200);
        }
        else
        {
            return response()->json([
                'status' => false,
                'message' => 'Payment failed, please try again.',
                'data' => []
            ], 400);
        }
    }

    public function checkPaymentStatus($id)
    {
        $transactionDetails = PaymentsShopModel::where('id',$id)->first();
        $payment_status = 'SUCCESS';
        return $payment_status;
    }

    public function shopPaymentList(Request $request)
    {
        $user = User::where('login_token', $request->bearerToken())->first();

        $validator = Validator::make($request->all(), [
            'shop_id' => 'nullable|numeric',
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
            'municipality_id' => $user->created_by,
            'from_date' => $request->from_date,
            'to_date' => $request->to_date,
            'shop_id' => $request->shop_id,
        ];        
        
        $params = (new RentPaymentModel)->getShopPaymentList(null , $filters , false , $request->per_page, $request->page);

        $total_records = (new RentPaymentModel)->getShopPaymentList(null , $filters , true , null, null);

        return response()->json([
            'status' => true,
            'message' => "Your Shop Rent Payment List.",
            'data' => $params,
            'total_records' => $total_records,
            'records_showing' => sizeof($params),
            'page' => $request->page,
            'per_page' => $request->per_page,
        ], 200);
    }

    public function shopPaymentDetails(Request $request)
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
            'municipality_id' => $user->created_by,
            'user_id' => $user->user_id,
        ];        
        
        $payment_details = (new RentPaymentModel)->getShopPaymentList(null , $filters , false , null, null);

        $data = [
            'payment_details' => $payment_details,
        ];

        return response()->json([
            'status' => true,
            'message' => "Your Rent Payments Details.",
            'data' => $data,
        ], 200);
    }


} //end class  
