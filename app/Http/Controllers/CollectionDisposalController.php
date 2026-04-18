<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\FieldWorkersModel;
use App\Models\User;
use App\Models\CollectionsModel;
use App\Models\ModuleMaster;
use App\Models\DisposalModel;

use Illuminate\Support\Str;
use App\Services\CommonService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CollectionDisposalController extends BaseController
{
	protected $commonService;

	public function __construct(CommonService $commonService)
	{
		parent::__construct();
		$this->commonService = $commonService;
	}

    public function collectionsList(Request $request)
	{
		if (session('user')->user_type_id == 1 || session('user')->user_type_id == 2) {

			$params = [];
			$title = "Collections";

            $municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            
            $services = ModuleMaster::where('status', 1)
				->orderBy('ordering', 'asc')
				->get();
            
            $currentDate = Carbon::today()->toDateString();

            $from_date = $request->fd ?? $currentDate;
            $to_date = $request->td ?? $currentDate;

            $municipality_id = $request->m ?? null;

            $filters = [
                'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
                'municipality_id' => $municipality_id,
                'from_date' => $from_date,
                'to_date' => $to_date,
                'service_id' => $request->s ?? null,
            ];
            
            //dd($filters);
            
            $params = (new CollectionsModel)->get_collection_list(10 , $filters , false , null, null);

			//dd($params);

			return view('collectionsList' , compact(
                'title',
                'params',
                'municipalities',
                'from_date',
                'to_date',
                'municipality_id',
                'services',
            ));

		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}
    
    public function disposalsList(Request $request)
	{
		if (session('user')->user_type_id == 1 || session('user')->user_type_id == 2) {

			$params = [];
			$title = "Disposals";

            $municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            
            $currentDate = Carbon::today()->toDateString();

            $from_date = $request->fd ?? $currentDate;
            $to_date = $request->td ?? $currentDate;

            $municipality_id = $request->m ?? null;

            $filters = [
                'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
                'municipality_id' => $municipality_id,
                'from_date' => $from_date,
                'to_date' => $to_date,
            ];
            
            $params = (new DisposalModel)->get_disposal_list(10 , $filters , false , null, null);

			//dd($params);

			return view('disposalsList' , compact(
                'title',
                'params',
                'municipalities',
                'from_date',
                'to_date',
                'municipality_id',
            ));

		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

}//end class  
