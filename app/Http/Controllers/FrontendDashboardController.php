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

use App\Services\CommonService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;

class FrontendDashboardController extends Controller
{
	protected $commonService;

    public function __construct(CommonService $commonService)
    {
    	//parent::__construct();
        $this->commonService = $commonService;
    }
    
    public function index()
    {
        $filters = [
            'user_type' => 2,
        ];
        $municipalities = (new User)->getUser('', $filters);
    	return view('frontend/dashboard' , compact('municipalities'));
    }
}
