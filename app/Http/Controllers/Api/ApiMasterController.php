<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\CommonService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Models\User;
use App\Models\ModuleMaster;

class ApiMasterController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function services(Request $request)
    {
        $params = ModuleMaster::where('status', 1)
				->orderBy('ordering', 'asc')
				->get();

        $data = [
            'services'=>$params
        ];

        return response()->json([
            'status' => true,
            'message' => 'Service list fetched successfully',
            'data' => $data
        ], 200);
    }

    public function accessibilityTypes(Request $request)
    {
        $accessibility_types = DB::table('accessibility_types')
                ->select('id','name')
                ->where('status', 1)
                ->orderBy('ordering' , 'asc')
                ->get();

        $data = [
            'accessibilityTypes'=>$accessibility_types
        ];

        return response()->json([
            'status' => true,
            'message' => 'Accessibility types list fetched successfully',
            'data' => $data
        ], 200);
    }

    public function natureOfServices(Request $request)
    {
        $natureOfServices = DB::table('nature_of_services')
                ->select('id','name')
                ->where('status', 1)
                ->orderBy('ordering' , 'asc')
                ->get();

        $data = [
            'natureOfServices'=>$natureOfServices
        ];

        return response()->json([
            'status' => true,
            'message' => 'Nature Of Services list fetched successfully',
            'data' => $data
        ], 200);
    }

    public function serviceBoundaryTypes(Request $request)
    {
        $serviceBoundaryTypes = DB::table('service_boundary_types')
                ->select('id','name')
                ->where('status', 1)
                ->orderBy('ordering' , 'asc')
                ->get();

        $data = [
            'serviceBoundaryTypes'=>$serviceBoundaryTypes
        ];

        return response()->json([
            'status' => true,
            'message' => 'Service Boundary Types list fetched successfully',
            'data' => $data
        ], 200);
    }

    public function tankOpenDurations(Request $request)
    {
        $tankOpenDurations = DB::table('tank_open_durations')
                ->select('id','name')
                ->where('status', 1)
                ->orderBy('ordering' , 'asc')
                ->get();

        $data = [
            'tankOpenDurations'=>$tankOpenDurations
        ];

        return response()->json([
            'status' => true,
            'message' => 'Tank Open Durations list fetched successfully',
            'data' => $data
        ], 200);
    }

    public function typesOfBuildings(Request $request)
    {
        $typesOfBuildings = DB::table('types_of_buildings')
                ->select('id','name')
                ->where('status', 1)
                ->orderBy('ordering' , 'asc')
                ->get();

        $data = [
            'typesOfBuildings'=>$typesOfBuildings
        ];

        return response()->json([
            'status' => true,
            'message' => 'Types Of Buildings list fetched successfully',
            'data' => $data
        ], 200);
    }

} //end class 
