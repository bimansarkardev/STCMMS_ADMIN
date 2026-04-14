<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserType;
use App\Models\UserDetails;
use App\Models\ModuleMaster;
use App\Models\Road;
use App\Models\Ward;
use App\Models\DistrictMasterModel;

use App\Services\CommonService;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UserController extends BaseController
{
	protected $commonService;

    public function __construct(CommonService $commonService)
    {
    	parent::__construct();
        $this->commonService = $commonService;
    }

    public function users($type)
	{
		if(session('user')->user_type_id == 1)
		{
			$access = ['municipality-admin'];
		}
		if(session('user')->user_type_id == 2)
		{
			$access = ['municipality-sub-admin'];
		}

		if(in_array($type, $access))
		{
			$user_type = UserType::where('slug', $type)->first();
			// dd($user_type->toArray());

			if($user_type)
			{
				$filters = [
			        'user_type' => $user_type->id,
			        'created_by' => session('user')->user_id,
			    ];
			    $users = (new User)->getUser(10, $filters);
				// dd($users->toArray());

				return view('users' , compact('user_type' , 'users'));
			}
			abort(404, 'User type not found');
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

	public function addUser($type)
	{
		if(session('user')->user_type_id == 1)
		{
			$access = ['municipality-admin'];
		}
		if(session('user')->user_type_id == 2)
		{
			$access = ['municipality-sub-admin'];
		}

		// dd($access);
		if(in_array($type, $access))
		{
			$user_type = UserType::where('slug', $type)->first();
			if($user_type)
			{
				$districts = DistrictMasterModel::where('status', 1)
					->orderBy('district_name' , 'asc')->get();

				return view('addUser' , compact('user_type' , 'districts'));
			}
			abort(404, 'User type not found');
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}		
	}

	public function addUserProcess(Request $request)
	{
		//dd($request->all());
		$userType = $request->user_type_id;

		$emailLabel = ($userType == 2) ? 'Municipality Code' : 'Email ID';

		$request->validate([
			'name' => 'required',
			'email' => 'required|string|unique:user,username',
			'mobile' => 'nullable|min:10',
			'newPass' => 'required|min:6',
			'newPassConfirm' => 'required|same:newPass',
			'district' => 'required_if:user_type_id,2',
			'full_address' => 'nullable|string',
			
		], [
			'email.required' => "$emailLabel is required.",
			'email.unique' => "This $emailLabel already exists.",
			'email.string' => "$emailLabel must be a valid string.",
			//'mobile.required' => 'Contact Number is required.',
			'mobile.min' => 'Contact Number must be at least 10 digits.',
			'newPassConfirm.same' => 'The new password confirmation does not match.',
		]);

	    $data = [
	        'user_type_id' => $request->user_type_id,
	        'name' => $request->name,
	        'username' => $request->email,
	        'mobile' => $request->mobile,
	        'password' => Hash::make($request->newPass),
	        'created_by' => session('user')->user_id,
	    ];

	    $user = User::create($data);

	    $details_data = [
	        'user_id' => $user->user_id,
	        'address' => $request->full_address,
	        'district_id' => $request->district,
	    ];

	    $userDetails = UserDetails::create($details_data);

	    $user_type = UserType::where('id', $request->user_type_id)->first();

	    return redirect()->route('admin.users.param', $user_type->slug)->with('success', $user_type->menu_title.' added successfully.');
	}

	public function editUser($type,$user_id)
	{
		if(session('user')->user_type_id == 1)
		{
			$access = ['municipality-admin'];
		}
		if(session('user')->user_type_id == 2)
		{
			$access = ['municipality-sub-admin'];
		}
		if(in_array($type, $access))
		{
			$user_type = UserType::where('slug', $type)->first();
			$filters = [
		        'user_id' => base64_decode($user_id),
		    ];
		    $user = (new User)->getUser('', $filters);
			//dd($user);
			if($user_type && $user)
			{
				$districts = DistrictMasterModel::where('status', 1)
					->orderBy('district_name' , 'asc')->get();

				return view('addUser' , compact('user_type','user','districts'));
			}
			abort(404, 'User not found');
		}
		else
		{
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}		
	}

	public function editUserProcess(Request $request)
	{
		$userType = $request->user_type_id;

		// ✅ Dynamic label
		$emailLabel = ($userType == 2) ? 'Municipality Code' : 'Email ID';

		// ✅ Base rules
		$rules = [
			'user_id' => 'required',
			'name' => 'required',
			'email' => 'required|string|unique:user,username,' . $request->user_id . ',user_id',
			'mobile' => 'nullable|digits:10',
			'district' => 'required_if:user_type_id,2',
			'full_address' => 'nullable|string',
		];

		// ✅ Password validation (ONLY if user enters it)
		if ($request->filled('newPass') || $request->filled('newPassConfirm')) {
			$rules['newPass'] = 'required|min:6';
			$rules['newPassConfirm'] = 'required|same:newPass';
		}

		$request->validate($rules, [
			'email.required' => "$emailLabel is required.",
			'email.unique' => "This $emailLabel already exists.",
			//'mobile.required' => 'Contact Number is required.',
			'mobile.digits' => 'Contact Number must be 10 digits.',
			'newPassConfirm.same' => 'The new password confirmation does not match.',
		], [
			'email' => $emailLabel,
			'mobile' => 'Contact Number',
		]);

	    $user = User::findOrFail($request->user_id);

	    $data = [
	        'user_type_id' => $request->user_type_id,
	        'name' => $request->name,
	        'username' => $request->email,
	        'mobile' => $request->mobile,
	    ];

	    if (!empty($request->newPass)) {
	        $data['password'] = Hash::make($request->newPass);
	    }

	    $user->update($data);

	    $details_data = [
	        'address' => $request->full_address,
	        'district_id' => $request->district,
	    ];

	    UserDetails::updateOrCreate(['user_id' => $request->user_id],$details_data);

	    $user_type = UserType::where('id', $request->user_type_id)->first();

	    return redirect()->route('admin.users.param', $user_type->slug)->with('success', 'Updated successfully.');
	}

	public function deleteUser($user_id)
	{
		$user_id = base64_decode($user_id);
	    $user = User::where('user_id', $user_id)->first();
	    if ($user) 
	    {
	    	if ($user->user_type_id == 2) 
	    	{
			    $module_master_count = 0;
			    $road_count = 0;
			    $ward_count = 0;
			    $grievance_admin_count = 0;

			    $associatedItems = [];

			    if ($module_master_count > 0) {
			        $associatedItems[] = 'Level Master records';
			    }
			    if ($road_count > 0) {
			        $associatedItems[] = 'Roads';
			    }
			    if ($ward_count > 0) {
			        $associatedItems[] = 'Wards';
			    }
			    if ($grievance_admin_count > 0) {
			        $associatedItems[] = 'Grievance Admins';
			    }

			    if (!empty($associatedItems)) {
			        $message = 'Cannot delete this municipality. Associated items exist: ' . implode(', ', $associatedItems) . ' — to maintain system integrity.';
			        return redirect()->route('admin.users.param', 'municipality-admin')->with('error', $message);
			    }
			}

			if($user->user_type_id == 3)
			{
				$complaints_trail_partitions = 'complaints_trail_partitions_' . $user->created_by;
				$complaints1 = $this->commonService->getNumRows($complaints_trail_partitions, ['assign_from'=>$user_id]);
				$complaints2 = $this->commonService->getNumRows($complaints_trail_partitions, ['assign_to'=>$user_id]);
				$complaints = $complaints1+$complaints2;

				$associatedItems = [];

			    if ($complaints > 0) {
			        $associatedItems[] = 'Complaints records';
			    }

				if (!empty($associatedItems)) {
			        $message = 'Cannot delete this grievance admin. Associated items exist: ' . implode(', ', $associatedItems) . ' — to maintain system integrity.';
			        return redirect()->route('admin.users.param', 'municipality-sub-admin')->with('error', $message);
			    }
			}

	    	$user_type_id = $user->user_type_id;
	    	$slug = UserType::where('id', $user_type_id)->value('slug');
	        $user->delete();
	        $userDetails = UserDetails::where('user_id', $user_id)->first();
	        if ($userDetails)
	        {
	        	$userDetails->delete();
	        }

	        return redirect()->route('admin.users.param', $slug)->with('success', 'User deleted successfully.');
	    } 
	    else 
	    {
	        return redirect()->route('admin.users.param', $slug)->with('error', 'User not found.');
	    }
	}

}//end class
