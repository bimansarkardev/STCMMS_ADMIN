<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisposalModel extends Model
{
    public $timestamps = true;
    protected $table = 'disposals';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'uid',
        'municipality_id',
        'plant_id',
        'quantity',
        'incharge_id',
        'created_by',
        'vehicle_id',
        'disposal_lat',
        'disposal_long',
        'disposal_address',
        'image',
        'created_at',
        'updated_at',
        'status',
    ];

    public function get_disposal_list($perPage = null, $filters = [], $returnCount = false, $perPageApi = null, $pageApi = null, )
    {
        $query = DB::table('disposals')
            ->leftJoin('user as ulb', "ulb.user_id", '=', 'disposals.municipality_id')
            ->leftJoin('vehicles', "vehicles.id", '=', "disposals.vehicle_id")
            ->leftJoin('plant_masters', "plant_masters.id", '=', "disposals.plant_id")
            ->leftJoin('plant_categories', 'plant_categories.id', '=', 'plant_masters.category_id')
            ->leftJoin('plant_master_incharges', "plant_master_incharges.id", '=', "disposals.incharge_id")
            ->leftJoin('user as created_by_user', "created_by_user.user_id", '=', 'disposals.created_by')
            ->leftJoin('ward', 'ward.id', '=', 'plant_masters.ward_id')
            ->select(
                'disposals.id',
                'disposals.uid',
                'disposals.municipality_id',
                'ulb.name as municipality_name',
                'disposals.plant_id',
                'plant_masters.location as plant_location',
                'plant_categories.name as plant_category_name',
                DB::raw("CONCAT(ward.ward_no , ' No. Ward') as plant_ward_no"),
                'disposals.quantity',
                DB::raw("CONCAT(disposals.quantity , ' ltr.') as quantity_mod"),
                'disposals.incharge_id',
                'plant_master_incharges.incharge_name',                
                'disposals.disposal_lat',
                'disposals.disposal_long',
                'disposals.disposal_address',
                'disposals.image',
                DB::raw("
                    CONCAT(
                        '" . rtrim(config('app.url'), '/') . "/',
                        disposals.image
                    ) AS image_filepath
                "),
                'disposals.created_by',
                'created_by_user.name as created_by_name',
                'disposals.vehicle_id',
                'vehicles.vehicle_type',
                'vehicles.vehicle_reg_no',                
                'disposals.created_at',
                DB::raw("DATE_FORMAT(disposals.created_at, '%d/%m/%y - %h:%i %p') as formatted_created_at"),
                'disposals.updated_at',
                DB::raw("DATE_FORMAT(disposals.updated_at, '%d/%m/%y - %h:%i %p') as formatted_updated_at"),
                'disposals.status',
            );

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) 
        {
            $query->where("disposals.municipality_id", '=', $filters['municipality_id']);
        }

        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (isset($filters['user_id']) && !empty($filters['user_id'])) {
                $query->where('disposals.municipality_id', '=', $filters['user_id']);
            }
        }

        if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
            $from = $filters['from_date'] . ' 00:00:00';
            $to   = $filters['to_date'] . ' 23:59:59';
            $query->whereBetween('disposals.created_at', [$from, $to]);
        }

        if(isset($filters['user_id']) && !empty($filters['user_id']) && empty($filters['user_type_id']))
        {
            $query->where("disposals.created_by", '=', $filters['user_id']);
        }

        $query->orderBy("disposals.created_at" , 'desc');

        if ($returnCount)
        {
            return $query->count();
        }

        if (isset($filters['id']) && !empty($filters['id'])) 
        {
            $query->where("disposals.id", '=', $filters['id']);
            return $query->first();
        }

        if (!is_null($perPageApi) && !is_null($pageApi)) 
        {
            $offset = ($pageApi - 1) * $perPageApi;
            $query->limit($perPageApi)->offset($offset);
        }

        if(!is_null($perPage))
        {
            return $query->paginate($perPage);
        }        
        else
        {
            return $query->get();
        }        
    }
}

