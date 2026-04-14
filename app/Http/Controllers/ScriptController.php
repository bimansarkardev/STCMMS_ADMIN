<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Services\CommonService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use App\Models\User;
use App\Models\Ward;


use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ScriptController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function runWardScripts()
    {
        set_time_limit(0);

        DB::beginTransaction();

        try {

            // ✅ Step 1: Load all base data
            $users = DB::table('user')
                ->select('user_id','name')
                ->where('user_type_id', 2)
                ->get();

            $municipalities = DB::table('municipality')
                ->select('id','name')
                ->get();

            $wards = DB::table('ward')
                ->select('id','name','muni_id')
                ->whereNotNull('name')
                ->where('name', '!=', '')
                ->get()
                ->groupBy('muni_id');

            $roads = DB::table('road')
                ->select('ward_id','name')
                ->get()
                ->groupBy('ward_id');

            // ✅ Step 2: Process users
            foreach ($users as $user)
            {
                // 🔍 Find matching municipality (in-memory)
                $municipality = $municipalities->first(function ($m) use ($user) {
                    return stripos($m->name, $user->name) !== false;
                });

                if (!$municipality) continue;

                $wardList = $wards[$municipality->id] ?? collect();

                $wardInsertData = [];
                $wardMap = []; // map: ward_name => inserted_id

                // ✅ Step 3: Prepare ward inserts
                foreach ($wardList as $ward)
                {
                    $wardInsertData[] = [
                        'municipality' => $user->user_id,
                        'ward_no'      => $ward->name,
                    ];
                }

                // ✅ Bulk insert wards (ignore duplicates)
                DB::table('ward_old')->insertOrIgnore($wardInsertData);

                // ✅ Fetch inserted wards for mapping
                $insertedWards = DB::table('ward_old')
                    ->where('municipality', $user->user_id)
                    ->pluck('id', 'ward_no'); // ward_no => id

                // ✅ Step 4: Prepare road inserts
                $roadInsertData = [];

                foreach ($wardList as $ward)
                {
                    $wardOldId = $insertedWards[$ward->name] ?? null;
                    if (!$wardOldId) continue;

                    $roadList = $roads[$ward->id] ?? collect();

                    foreach ($roadList as $road)
                    {
                        $roadInsertData[] = [
                            'municipality' => $user->user_id,
                            'ward'         => $wardOldId,
                            'road_name'    => $road->name,
                        ];
                    }
                }

                // ✅ Bulk insert roads
                if (!empty($roadInsertData)) {
                    DB::table('road_old')->insertOrIgnore($roadInsertData);
                }
            }

            DB::commit();

            return response()->json([
                'status'  => true,
                'message' => '🚀 Optimized script executed successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();

            return response()->json([
                'status'  => false,
                'message' => '❌ Error: ' . $e->getMessage(),
            ]);
        }
    }

} //end class 
