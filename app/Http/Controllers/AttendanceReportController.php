<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request;

use App\Models\FieldWorkersModel;
use App\Models\User;
use App\Models\AttendanceLogsModel;

use Illuminate\Support\Str;
use App\Services\CommonService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AttendanceReportController extends BaseController
{
	protected $commonService;

	public function __construct(CommonService $commonService)
	{
		parent::__construct();
		$this->commonService = $commonService;
	}

    public function attendanceList(Request $request)
	{
		if (session('user')->user_type_id == 1 || session('user')->user_type_id == 2) {

			$params = [];
			$title = "Field Workers Attendance";

            $municipalities = User::where('user_type_id', 2)
                ->select('user_id', 'name')
                ->orderBy('name', 'asc')
                ->get();
            
            $currentDate = Carbon::today()->toDateString();

            $municipality_id = $request->m ?? null;

			$filters = [
				'user_type_id' => session('user')->user_type_id,
				'user_id' => session('user')->user_id,
				'municipality_id' => $municipality_id,
				'currentDate' => $request->d ?? $currentDate,
			];
			$params = (new FieldWorkersModel)->getAttendanceList(10, $filters);

			//dd($params);

			return view('attendanceList' , compact(
                'title',
                'params',
                'municipalities',
                'currentDate',
                'municipality_id',
            ));

		} else {
			return redirect()->route('admin.dashboard')->with('error', "Permission Denied!");
		}
	}

    function getAttendanceSessions(Request $request)
    {
        $filters = [
            'field_worker_id' => $request->field_worker_id,
            'date' => $request->date,
        ];
        $params = (new AttendanceLogsModel)->getAttendanceLogs(null, $filters);
        return response()->json(['data' => $params]);
    }

}//end class  
