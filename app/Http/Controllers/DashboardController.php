<?php 
namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\UserDevice;
use App\Models\MunicipalityModuleMaster;
use App\Models\LevelMaster;
use App\Models\Ward;
use App\Models\Road;
use App\Models\Complaints;
use App\Models\BookingsModel;
use App\Models\RentPaymentModel;

use App\Services\CommonService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class DashboardController extends BaseController
{
	protected $commonService;

    public function __construct(CommonService $commonService)
    {
    	parent::__construct();
        $this->commonService = $commonService;
    }
    
    public function index()
    {
    	// if(session('user')->user_type_id==2)
    	// {
    	// 	$data['module_master_count'] = MunicipalityModuleMaster::where('municipality_id', session('user')->user_id)->count();
    	// 	$data['level_master_count'] = LevelMaster::where('municipality', session('user')->user_id)->count();
    	// 	$data['executors_count'] = User::where('created_by', session('user')->user_id)->where('user_type_id', 3)->count();
    	// 	$data['ward_count'] = Ward::where('municipality', session('user')->user_id)->count();
    	// 	$data['road_count'] = Road::where('municipality', session('user')->user_id)->count();

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Pending',
		// 	];
		// 	$data['pending_booking_count'] = (new BookingsModel)->getBookings('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Completed',
		// 	];
		// 	$data['completed_booking_count'] = (new BookingsModel)->getBookings('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Pending',
		// 	];
		// 	$data['pending_application_count'] = (new BookingsModel)->getApplications('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Accepted',
		// 	];
		// 	$data['accepted_application_count'] = (new BookingsModel)->getApplications('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Rejected',
		// 	];
		// 	$data['rejected_application_count'] = (new BookingsModel)->getApplications('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 		'type' 				=> 'Visit-Scheduled',
		// 	];
		// 	$data['visit_scheduled_application_count'] = (new BookingsModel)->getApplications('' , $filters , true);

		// 	$filters = [
		// 		'municipality_id' 	=> session('user')->municipality_id,
		// 	];
		// 	$data['paid_rent_count'] = (new RentPaymentModel)->getShopPaymentList('' , $filters , true);			

		// 	// dd($data);
    	// }
    	// if(session('user')->user_type_id==3 || session('user')->user_type_id==2)
    	// {
    	// 	$pending_filters = [
		//         'municipality_id' 	=> session('user')->municipality_id,
		//         'type' 				=> 'Pending',
		//     ];
    	// 	//$data['pending_count'] = (new Complaints)->getComplaints('' , $pending_filters , true);
    	// 	$data['pending_count'] = 0;

    	// 	$completed_filters = [
		//         'municipality_id' 	=> session('user')->municipality_id,
		//         'type' 				=> 'Completed',
		//     ];
    	// 	//$data['completed_count'] = (new Complaints)->getComplaints('' , $completed_filters , true);
    	// 	$data['completed_count'] = 0;
    	// }

		$data['user'] = Auth::user();
		return view('dashboard' , compact('data'));
    }

    public function changePassword()
    {
    	return view('changePassword');
    }

    public function changePasswordProcess(Request $request)
	{
	    $request->validate([
	        'email' => 'required|email',
	        'currentPass' => 'required',
	        'newPass' => 'required|min:6',
	        'newPassConfirm' => 'required|same:newPass',
	    ], [
	        'newPassConfirm.same' => 'The new password confirmation does not match.',
	    ]);

	    $user = User::where('email', $request->email)->first();

	    if (!$user || !Hash::check($request->currentPass, $user->password)) {
	        return back()->with('error', 'Wrong current password or email');
	    }

	    $user->password = Hash::make($request->newPass);
	    $user->save();
	    Auth::logout();
	    return redirect()->route('admin.login')->with('success', 'Password changed successfully.');
	}

	public function saveToken(Request $request)
	{
		$user = session('user');
        if ($user) {
            $userDevice = UserDevice::where('user_id', $user->user_id)
                        ->where('fcm_token', $request->token)
                        ->where('device_type', $request->deviceType)
                        ->first();
	        if ($userDevice) {
	            $userDevice->login_token = $user->login_token;
	        } else {
	            $userDevice = new UserDevice();
	            $userDevice->user_id = $user->user_id;
	            $userDevice->device_type = $request->deviceType ?? 'unknown';
	            $userDevice->login_token = $user->login_token;
	            $userDevice->fcm_token = $request->token;	            
	        }
	        $userDevice->save();
	        return response()->json(['message' => 'Token saved successfully']);
        }
	}

}
