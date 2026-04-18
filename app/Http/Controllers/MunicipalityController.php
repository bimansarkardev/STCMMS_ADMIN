<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\Ward;
use App\Models\Road;
use App\Models\User;
use App\Models\UserDetails;
use App\Models\PlantCategoriesModel;
use App\Models\PlantMasterModel;
use App\Models\PlantMasterInchargeModel;
use App\Models\PlantMasterMunicipalityTaggedModel;
use App\Models\UserType;
use App\Models\FieldWorkersModel;
use App\Models\RolesModel;
use App\Models\VehiclesModel;
use App\Models\AgenciesModel;
use App\Models\AgenciesMunicipalitiesModel;


use Illuminate\Validation\Rule;
use App\Services\CommonService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class MunicipalityController extends BaseController
{
	protected $commonService;

    public function __construct(CommonService $commonService)
    {
    	parent::__construct();
        $this->commonService = $commonService;
    }

    public function ward()
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
            $filters = [
				'user_type_id'=>session('user')->user_type_id,
				'user_id'=>session('user')->user_id,
			];
			$params = (new Ward)->getWard(10, $filters);
			$title = "Municipality Ward";
			return view('wards' , compact('title','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}
	
	function getMunicipalityWards(Request $request)
	{
		$filters = [
			'municipality_id'=>$request->municipality_id,
		];
		$params = (new Ward)->getWard('', $filters);

		return response()->json($params);
	}

	public function addWard(Request $request)
	{
	    $from = (int) $request->from;
	    $to = (int) $request->to;

	    if ($from > $to) {
	        return back()->with('error', 'Invalid ward range: "from" must be less than or equal to "to".')->withInput();
	    }

	    $municipalityId = session('user')->user_id;

	    $existingWards = Ward::where('municipality', $municipalityId)
	                        ->whereIn('ward_no', range($from, $to))
	                        ->pluck('ward_no')
	                        ->toArray();

	    if (!empty($existingWards)) {
	        return back()->with('error', 'Ward number(s) already exist: ' . implode(', ', $existingWards))->withInput();
	    }

	    for ($i = $from; $i <= $to; $i++) {
	        Ward::create([
	            'ward_no' => $i,
	            'municipality' => $municipalityId,
	        ]);
	    }

	    return redirect()->route('admin.ward')->with('success', 'Wards from ' . $from . ' to ' . $to . ' added successfully.');
	}

	public function deleteWard($id)
	{
		if(session('user')->user_type_id == 2)
		{
			$ward = Ward::where('id', base64_decode($id))->first();
		    if ($ward) 
		    {
		    	$road = Road::where('ward', base64_decode($id))->count();
		    	$user_details = UserDetails::where('ward', base64_decode($id))->count();

		    	$associatedItems = [];

			    if ($road > 0) {
			        $associatedItems[] = 'Road records';
			    }

			    if ($user_details > 0) {
			        $associatedItems[] = 'Citizen records';
			    }

				if (!empty($associatedItems)) 
				{
			        $message = 'Cannot delete this ward. Associated items exist: ' . implode(', ', $associatedItems) . ' — to maintain system integrity.';
			        return redirect()->route('admin.ward')->with('error', $message);
			    }
			    else
			    {
			    	$ward->delete();
		        	return redirect()->route('admin.ward')->with('success', 'Ward deleted successfully.');
			    }		    	
		    }
		    else
		    {
		        return redirect()->route('admin.ward')->with('error', 'Ward not found.');
		    }
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}	    
	}

	public function road(Request $request)
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
			$filters = [
				'user_type_id'=>session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality' => $request->m ?? null,
            	'ward' => $request->w ?? null,
			];

			$perpage = 10;

			if ($request->filled(['m', 'w'])) {
				$perpage = 200;
			}

			$params = (new Road)->getRoad($perpage, $filters);

			$title = "Municipality Roads";
			$addUrl = "admin.addRoad";

			return view('roads' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addRoad()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Municipality Road";
			$listUrl = "admin.road";
			$wards = Ward::where('status', 0)
                ->where('municipality', session('user')->user_id)
                ->orderBy('ward_no', 'asc')
                ->get();
			return view('add_road' , compact('title','listUrl','wards'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addRoadProcess(Request $request)
	{
		$municipality = session('user')->user_id;

		$request->validate([
		    'road_name' => [
		        'required',
		        Rule::unique('road')->where(function ($query) use ($municipality, $request) {
	                return $query->where('municipality', $municipality)
	                             ->where('ward', $request->ward);
	            }),
		    ],
		    'ward' => 'required',
		]);

	    $data = [
	        'road_name' => $request->road_name,
	        'ward' => $request->ward,
	        'municipality' => $municipality,
	    ];

	    Road::create($data);

	    return redirect()->route('admin.road')->with('success', 'Municipality road added successfully.');
	}

	public function editRoad($id)
	{
		if(session('user')->user_type_id == 2)
		{
			$details = Road::where('id', base64_decode($id))->first();
			if($details)
			{
				$title = "Edit Municipality Road";
				$listUrl = "road";
				$wards = Ward::where('status', 0)
                ->where('municipality', session('user')->user_id)
                ->orderBy('ward_no', 'asc')
                ->get();
				return view('add_road' , compact('title','details','listUrl','wards'));
			}
			abort(404, 'Road not found');
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function editRoadProcess(Request $request)
	{
		$municipality = session('user')->user_id;

		$request->validate([
		    'road_name' => [
		        'required',
		        Rule::unique('road')->ignore($request->id)->where(function ($query) use ($municipality, $request) {
	                return $query->where('municipality', $municipality)
	                             ->where('ward', $request->ward);
	            }),
		    ],
		    'ward' => 'required',
		]);

	    $road = Road::find($request->id);

	    $road->road_name = $request->road_name;
	    $road->ward = $request->ward;
	    $road->save();

	    return redirect()->route('admin.road')->with('success', 'Municipality road updated successfully.');
	}

	public function deleteRoad($id)
	{
		if(session('user')->user_type_id == 2)
		{
			$road = Road::where('id', base64_decode($id))->first();
		    if ($road) 
		    {
		    	//$check_count = Road::where('ward', base64_decode($id))->count();
		    	$check_count = 0;
		    	if($check_count==0)
		    	{
		    		$road->delete();
			        return redirect()->route('admin.road')->with('success', 'Road deleted successfully.');
		    	}
		    	else
		    	{
		    		return redirect()->route('admin.road')->with('error', 'This Road is associated with Complaint(s) and it cannot be deleted.');
		    	}		        
		    }
		    else
		    {
		        return redirect()->route('admin.road')->with('error', 'Road not found.');
		    }
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addStpFstp()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Plants Master";
			$listUrl = "admin.stpFstps";

			$municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();

			$plant_categories = PlantCategoriesModel::where('status',1)
				->select('id','name')
				->orderBy('ordering', 'asc')
                ->get();
			
			$agencies = AgenciesModel::where('status',1)
				->select('id','agency_name')
				->orderBy('id', 'desc')
                ->get();

			return view('add_stp_fstp' , compact(
				'title',
				'listUrl',
				'municipalities',
				'plant_categories',
				'agencies',
			));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addStpFstpProcess(Request $request)
	{
		$rules = [
			'municipality' => 'required',
			'ward' => 'required',
			'location' => 'required',
			'category' => 'required',
			'capacity' => 'required',
			'operate_by' => 'required',
			'incharge_name.*' => 'required',
			'incharge_contact_no.*' => 'required',
		];

		$messages = [
			'agency.required_if' => 'Agency is required when plant operate by agency.',
		];

		if ($request->operate_by == 2) {
			$rules['agency'] = 'required';
		}

		$request->validate($rules, $messages);

		DB::beginTransaction();

		try {

			// Generate UID
			do {
				$uid = $this->commonService->getToken(10, 'UID');
				$exists = PlantMasterModel::where('uid', $uid)->exists();
			} while ($exists);

			// Save Plant
			$plantMaster = PlantMasterModel::create([
				'uid' => $uid,
				'municipality_id' => $request->municipality,
				'ward_id' => $request->ward,
				'location' => $request->location,
				'category_id' => $request->category,
				'capacity' => $request->capacity,
				'operate_by' => $request->operate_by,
				'agency_id' => $request->agency,
			]);

			// Incharge Data
			$names = $request->incharge_name;
			$contacts = $request->incharge_contact_no;

			foreach ($names as $key => $name) {

				if (!empty($name) && !empty($contacts[$key])) {

					PlantMasterInchargeModel::create([
						'municipality_id' => $request->municipality,
						'plant_master_id' => $plantMaster->id,
						'incharge_name' => $name,
						'incharge_contact_no' => $contacts[$key],
					]);
				}
			}

			DB::commit();

			return redirect()->route('admin.stpFstps')->with('success', 'Added successfully.');

		} catch (\Exception $e) {

			DB::rollBack();

			return back()->with('error', 'Something went wrong!');
		}
	}

	function stpFstps(Request $request)
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
			$title = "Plants Master";
			$addUrl = "admin.addStpFstp";

			$filters = [
				'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $request->m ?? null,
			];
			$params = (new PlantMasterModel)->getPlants(10, $filters);

			//dd($params);

			return view('stp_fstps' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function taggedStpFstps()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Plants Tagged with Municipalities";
			$addUrl = "admin.tagStpFstp";

			$filters = [
				'user_type' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $request->m ?? null,
			];
			$params = (new PlantMasterMunicipalityTaggedModel)->getPlantsWithTags(10, $filters);

			//dd($params);

			return view('taggedStpFstps' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function tagStpFstp()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Tag Municipality(s) with Plant";
			$listUrl = "admin.taggedStpFstps";

			$municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();

			return view('tagStpFstp' , compact(
				'title',
				'listUrl',
				'municipalities',
			));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function getMunicipalityPlants(Request $request)
	{
		$filters = [
			'municipality_id'=>$request->municipality_id,
		];
		$params = (new Ward)->getWard('', $filters);

		$filters = [
			'municipality_id' => $request->municipality_id,
		];
		$params = (new PlantMasterModel)->getPlants('', $filters);

		return response()->json($params);
	}

	function tagStpFstpAddProcess(Request $request)
	{
		//dd($request->all());
		$request->validate([
			'plant' => 'required',
			'taggedMunicipalities.*' => 'required',
		]);

		DB::beginTransaction();

		try {

			$plant = $request->plant;
			$taggedMunicipalities = $request->taggedMunicipalities;

			foreach ($taggedMunicipalities as $municipalityId) {
				if (!empty($municipalityId)) {
					PlantMasterMunicipalityTaggedModel::create([
						'municipality_id' => $municipalityId,
						'plant_master_id' => $plant,
					]);
				}
			}

			DB::commit();

			return redirect()->route('admin.taggedStpFstps')->with('success', 'Tagged successfully.');

		} catch (\Exception $e) {

			DB::rollBack();

			return back()->with('error', $e->getMessage() . ' | Line: ' . $e->getLine());
		}
	}

	function addVehicle()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Vehicle Master";
			$listUrl = "admin.vehicles";
			$v_cats = $this->commonService->getListData('vehicle_categories', ['status'=>1], ['id','name']);
			$municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
			return view('add_vehicle' , compact('title','listUrl','v_cats','municipalities'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function addVehicleProcess(Request $request)
	{
		$request->validate([
			'municipality' => 'required',
			'vehicle_type' => 'required',
			'vehicle_reg_no' => 'required|string|unique:vehicles,vehicle_reg_no',
			'capacity' => 'required',
			'category' => 'required',
		]);

		DB::beginTransaction();

		try {

			$vehicle = VehiclesModel::create([
				'municipality_id' => $request->municipality,
				'vehicle_type' => $request->vehicle_type,
				'vehicle_reg_no' => $request->vehicle_reg_no,
				'capacity' => $request->capacity,
				'vehicle_category_id' => $request->category,
			]);

			DB::commit();

			return redirect()->route('admin.vehicles')->with('success', 'Added successfully.');

		} catch (\Exception $e) {

			DB::rollBack();

			return back()->with('error', 'Something went wrong!');
		}
	}

	function vehicles()
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
			$title = "Vehicle Master";
			$addUrl = "admin.addVehicle";

			$filters = [
				'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $request->m ?? null,
			];
			$params = (new VehiclesModel)->getVehicles(10, $filters);

			//dd($params);
			return view('vehicles' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function addFieldWorkers()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Add New Field Worker";
			$listUrl = "admin.fieldWorkers";
			$user_type = UserType::where('slug', 'field-worker')->first();
			$municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
			$roles = RolesModel::where('status', 1)
                ->select('id', 'name')
                ->orderBy('ordering', 'asc')
                ->get();
			$agencies = AgenciesModel::where('status',1)
				->select('id','agency_name')
				->orderBy('id', 'desc')
                ->get();
			return view('add_field_workers' , compact('title','listUrl','user_type','municipalities','roles','agencies'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function fieldWorkers()
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
			$params = [];
			$title = "Field Workers";
			$addUrl = "admin.addFieldWorkers";

			$filters = [
				'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $request->m ?? null,
			];
			$params = (new FieldWorkersModel)->getFieldWorkers(10, $filters);

			//dd($params);

			return view('fieldWorkers' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addFieldWorkersProcess(Request $request)
	{
		DB::beginTransaction();

		try {

			$userType = $request->user_type_id;
			$emailLabel = ($userType == 3) ? 'Mobile Number/User ID' : 'User ID';

			$rules = [
				'municipality' => 'required',
				'role' => 'required',
				'name' => 'required',
				'full_address' => 'nullable|string',
			];

			$messages = [
				'mobile.required_if' => 'Contact Number is required.',
				'mobile.min' => 'Contact Number must be at least 10 digits.',
				'newPassConfirm.same' => 'The new password confirmation does not match.',
			];

			if ($request->is_user == 1) {
				$rules['email'] = 'required|string|unique:user,username';
				$rules['mobile'] = 'required|min:10';
				$rules['newPass'] = 'required|min:6';
				$rules['newPassConfirm'] = 'required|same:newPass';

				$messages['email.required'] = "$emailLabel is required.";
				$messages['email.unique'] = "This $emailLabel already exists.";
				$messages['email.string'] = "$emailLabel must be a valid string.";
			}

			if ($request->operate_by == 2) {
				$rules['agency'] = 'required';
				$messages['agency.required'] = "Agency is required when field worker work with agency.";
			}

			$request->validate($rules, $messages);

			$is_user = $request->is_user;
			$user_id = null;

			if ($is_user == 1) {

				$user = User::create([
					'user_type_id' => $request->user_type_id,
					'role' => $request->role,
					'name' => $request->name,
					'username' => $request->email,
					'mobile' => $request->mobile,
					'password' => Hash::make($request->newPass),
					'created_by' => session('user')->user_id,
				]);

				UserDetails::create([
					'user_id' => $user->user_id,
					'address' => $request->full_address,
					'municipality_id' => $request->municipality
				]);

				$user_id = $user->user_id;
			}

			FieldWorkersModel::create([
				'municipality_id' => $request->municipality,
				'role' => $request->role,
				'field_worker_name' => $request->name,
				'field_worker_mobile_no' => $request->mobile,
				'address' => $request->full_address,
				'is_user' => $request->is_user,
				'user_id' => $user_id,
				'operate_by' => $request->operate_by,
				'agency_id' => $request->agency,
			]);

			DB::commit();

			return redirect()->route('admin.fieldWorkers')
				->with('success', 'Added successfully.');

		} catch (\Exception $e) {
			DB::rollBack();
			return redirect()->back()->withInput()->with('error', $e->getMessage() . ' | Line: ' . $e->getLine());
		}
	}

	function addAgency()
	{
		if(session('user')->user_type_id == 1)
		{
			$title = "Agency Master";
			$listUrl = "admin.agencies";
			$municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
			return view('add_agency' , compact('title','listUrl','municipalities'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	function addAgencyProcess(Request $request)
	{
		//dd($request->all());
		$request->validate([
			'agency_name' => 'required|string|max:255',
			'agency_address' => 'nullable|string|max:500',

			'contact_person' => 'nullable|string|max:255',
			'contact_person_contact_number' => 'nullable|digits:10',

			'municipality_id' => 'required|array',
			'municipality_id.*' => 'required|integer',

			'contract_from_date' => 'required|array',
			'contract_from_date.*' => 'required|date',

			'contract_to_date' => 'required|array',
			'contract_to_date.*' => 'required|date',

			'contract_file' => 'required|array',
			'contract_file.*' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
		]);

		DB::beginTransaction();

		try {
			// ✅ Create Agency
			$agency = AgenciesModel::create([
				'agency_name' => $request->agency_name, // optional uppercase
				'agency_address' => $request->agency_address,
				'contact_person' => $request->contact_person,
				'contact_person_contact_number' => $request->contact_person_contact_number,
			]);

			$municipality_ids = $request->municipality_id;
			$contract_from_dates = $request->contract_from_date;
			$contract_to_dates = $request->contract_to_date;
			$contract_files = $request->file('contract_file'); // IMPORTANT

			foreach ($municipality_ids as $index => $municipality_id) {

				$filePath = null;

				// ✅ Handle file per index
				if (isset($contract_files[$index])) {
					$file = $contract_files[$index];
					$filename = time() . '_' . $index . '_' . $file->getClientOriginalName();
					$destinationPath = public_path('uploads/amc');
					$file->move($destinationPath, $filename);
					$filePath = 'uploads/amc/' . $filename;
				}

				// ✅ Insert row
				AgenciesMunicipalitiesModel::create([
					'agency_id' => $agency->id,
					'municipality_id' => $municipality_id,
					'contract_from_date' => $contract_from_dates[$index],
					'contract_to_date' => $contract_to_dates[$index],
					'contract_file' => $filePath,
				]);
			}

			DB::commit();

			return redirect()->route('admin.agencies')
				->with('success', 'Agency added successfully.');

		} catch (\Exception $e) {

			DB::rollBack();

			return back()->with('error', 'Something went wrong! ' . $e->getMessage());
		}
	}

	function agencies()
	{
		if(session('user')->user_type_id == 1 || session('user')->user_type_id == 2)
		{
			$title = "Agency Master";
			$addUrl = "admin.addAgency";

			$filters = [
				'user_type' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $request->m ?? null,
			];
			$params = (new AgenciesModel)->getAgencies(10, $filters);
			//dd($params);
			return view('agencies' , compact('title','addUrl','params'));
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

}//end class 
