<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class CollectionsModel extends Model
{
    public $timestamps = true;
    protected $table = 'collections';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'service_id',
        'uid',
        'trip_number',
        'municipality_id',
        'service_boundary',
        'ward_id',
        'road_id',
        'outside_boundary_details',
        'nature_of_service',
        'other_nature_of_service_details',
        'type_of_building',
        'accessibility',
        'accessibility_details',
        'volume_quantity',
        'tank_open_duration',
        'no_of_users',
        'last_cleaned_date',
        'beneficiary_name',
        'beneficiary_contact_number',
        'beneficiary_lat',
        'beneficiary_long',
        'address',
        'image',
        'created_by',
        'vehicle_id',
        'created_at',
        'updated_at',
        'status',
    ];

    public function get_collection_list($perPage = null, $filters = [], $returnCount = false, $perPageApi = null, $pageApi = null, )
    {
        $query = DB::table('collections')           
            ->Join('module_master', "module_master.id", '=', 'collections.service_id')            
            ->leftJoin('user as ulb', "ulb.user_id", '=', 'collections.municipality_id')
            ->leftJoin('service_boundary_types', "service_boundary_types.id", '=', 'collections.service_boundary')
            ->leftJoin('ward', "ward.id", '=', "collections.ward_id")
            ->leftJoin('road', "ward.id", '=', "collections.road_id")
            ->leftJoin('nature_of_services', "nature_of_services.id", '=', "collections.nature_of_service")
            ->leftJoin('types_of_buildings', "types_of_buildings.id", '=', "collections.type_of_building")
            ->leftJoin('accessibility_types', "accessibility_types.id", '=', "collections.accessibility")
            ->leftJoin('tank_open_durations', "tank_open_durations.id", '=', "collections.tank_open_duration")
            ->leftJoin('vehicles', "vehicles.id", '=', "collections.vehicle_id")
            ->leftJoin('collection_status', "collection_status.id", '=', "collections.status")
            
            ->select(
                'collections.id',
                'collections.service_id',
                'module_master.name as service_name',
                'collections.uid',
                'collections.trip_number',
                'collections.municipality_id',
                'ulb.name as municipality_name',
                'collections.service_boundary',
                'collections.ward_id',
                DB::raw("CONCAT(ward.ward_no , ' No. Ward') as ward_no"),
                'collections.road_id',
                'road.road_name',                
                'collections.outside_boundary_details',
                'collections.nature_of_service',
                'nature_of_services.name as nature_of_service_name',
                'collections.other_nature_of_service_details',
                'collections.type_of_building',
                'types_of_buildings.name as type_of_building_name',

                'collections.accessibility',
                'accessibility_types.name as accessibility_name',
                'collections.accessibility_details',
                'collections.volume_quantity',
                'collections.tank_open_duration',
                'tank_open_durations.name as tank_open_duration_name',
                'collections.no_of_users',
                'collections.last_cleaned_date',
                'collections.beneficiary_name',
                'collections.beneficiary_contact_number',
                'collections.beneficiary_lat',
                'collections.beneficiary_long',
                'collections.address',
                'collections.image',
                DB::raw("
                    CONCAT(
                        '" . rtrim(config('app.url'), '/') . "/',
                        collections.image
                    ) AS image_filepath
                "),
                'collections.created_by',
                'collections.vehicle_id',
                'vehicles.vehicle_type',
                'vehicles.vehicle_reg_no',                
                'collections.created_at',
                DB::raw("DATE_FORMAT(collections.created_at, '%d/%m/%y - %h:%i %p') as formatted_created_at"),
                'collections.updated_at',
                DB::raw("DATE_FORMAT(collections.updated_at, '%d/%m/%y - %h:%i %p') as formatted_updated_at"),
                'collections.status',
                'collection_status.name as status_name',
            );

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) 
        {
            $query->where("collections.municipality_id", '=', $filters['municipality_id']);
        }

        if (isset($filters['service_id']) && !empty($filters['service_id'])) 
        {
            $query->where("module_master.id", '=', $filters['service_id']);
        }        

        if(isset($filters['ward']) && !empty($filters['ward']) && $filters['ward']!="all")
        {
            $query->where("collections.ward", '=', $filters['ward']);
        }

        if (isset($filters['from_date']) && !empty($filters['from_date']) && isset($filters['to_date']) && !empty($filters['to_date']))
        {
            $query->whereBetween("collections.created_at", [$filters['from_date'], $filters['to_date']]);
        }

        if(isset($filters['user_id']) && !empty($filters['user_id']))
        {
            $query->where("collections.created_by", '=', $filters['user_id']);
        }

        $query->orderBy("collections.updated_at" , 'desc');

        if ($returnCount)
        {
            return $query->count();
        }

        if (isset($filters['id']) && !empty($filters['id'])) 
        {
            $query->where("collections.id", '=', $filters['id']);
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

