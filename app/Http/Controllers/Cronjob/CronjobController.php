<?php

namespace App\Http\Controllers\Cronjob;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\User;
use App\Models\Notifications;
use App\Models\BookingsModel;
use App\Models\BookingsTrailModel;


use App\Services\CommonService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CronjobController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function bookingMarkAsCompleteCron()
    {
        $bookings = (new BookingsModel)->getBookingsForMarkAsCompleteCron();
        //dd($bookings);
        if($bookings)
        {        
            DB::beginTransaction();

            try {

                foreach ($bookings as $booking) 
                {
                    $municipality_id = $booking->municipality_id;
                    $user_id = $booking->user_id;
                    $parent_id = $booking->id;
                    $from_state = $booking->status;
                    $to_state = 4; //completed                

                    //booking_update
                    $booking_update = [
                        'status' => $to_state,
                    ];

                    // Update parent booking
                    $this->commonService->updateData("bookings", ['id' => $parent_id], $booking_update);

                    // Update partition table
                    $bookings_partitions_table = "bookings_partitions_" . $municipality_id;
                    $this->commonService->updateData($bookings_partitions_table, ['parent_id' => $parent_id], $booking_update);

                    // Insert trail into master trail
                    $booking_trail_data = [
                        'booking_id' => $parent_id,
                        'from_state' => $from_state,
                        'to_state' => $to_state,
                        'action_by' => $municipality_id,
                        'assign_from' => 0,
                        'assign_to' => 0,
                        'remarks' => 'Booking completed',
                    ];
                    BookingsTrailModel::create($booking_trail_data);

                    $bookings_partitions_table_id = $this->commonService->getField($bookings_partitions_table, ['parent_id' => $parent_id] , 'id');

                    // Insert trail into partition trail
                    $booking_trail_data['booking_id'] = $bookings_partitions_table_id;

                    $bookings_trail_partitions_table = "bookings_trail_partitions_" . $municipality_id;
                    $this->commonService->insertData($bookings_trail_partitions_table, $booking_trail_data);

                    //after booking_update
                    $filters = [
                        'municipality_id' => $municipality_id,
                        'id' => $bookings_partitions_table_id,
                        'user_id' => $user_id,
                    ];
                    $details = (new BookingsModel)->getBookings('', $filters);

                    $user_notification_data = [
                        'user_id' => $user_id,
                        'title'   => "Booking {$details->booking_status_name}: {$details->uid} - {$details->service_master_name}",
                        'message' => "Your booking ({$details->uid}) regarding {$details->service_master_name} has been {$details->booking_status_name} by system"
                    ];
                    Notifications::create($user_notification_data);
                }

                DB::commit(); // Commit the transaction after all updates

                return response()->json([
                    'status' => true,
                    'message' => 'Auto booking complete cron ran successfully.',
                    'data' => []
                ], 200);

            } catch (\Exception $e) {
                DB::rollBack(); // Roll back on any failure

            return response()->json([
                    'status' => false,
                    'message' => $e->getMessage(),
                    'exception' => get_class($e),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'code' => $e->getCode(),
                    'trace' => $e->getTrace(),  // full trace array
                ], 500);
            }
        }
        else{
            return response()->json([
                    'status' => true,
                    'message' => 'No booking pending booking found for run cron.',
                    'data' => []
                ], 200);
        }
    }
}
