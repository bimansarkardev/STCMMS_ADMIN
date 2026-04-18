<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlantMasterModel extends Model
{
    protected $table = 'plant_masters';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality_id',
        'uid',
        'ward_id',
        'location',
        'category_id',
        'capacity',
        'operate_by',
        'agency_id',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getPlants($perPage, $filters = [])
    {
        $query = PlantMasterModel::query()
        ->leftJoin('user', 'user.user_id', '=', 'plant_masters.municipality_id')
        ->leftJoin('ward', 'ward.id', '=', 'plant_masters.ward_id')
        ->leftJoin('plant_categories', 'plant_categories.id', '=', 'plant_masters.category_id')
        ->leftJoin('agencies', 'agencies.id', '=', 'plant_masters.agency_id')
        ->leftJoin('plant_masters_municipality_tagged', 'plant_masters_municipality_tagged.plant_master_id', '=', 'plant_masters.id')
        ->select(
            'plant_masters.id',
            'plant_masters.municipality_id',
            'plant_masters.uid',
            'plant_masters.ward_id',
            'plant_masters.location',
            'plant_masters.category_id',
            'plant_masters.capacity',
            'plant_masters.operate_by',
            'plant_masters.agency_id',
            'plant_masters.created_at',
            'plant_masters.updated_at',
            'plant_masters.status',
            'user.name as municipality_name',
            DB::raw("CONCAT(ward.ward_no , ' No. Ward') as ward_no"),
            'plant_categories.name as category_name',
            'agencies.agency_name',
            DB::raw("CONCAT(plant_masters.capacity , ' ltr.') as capacity_mod")
        )
        ->with([
            'incharges' => function ($q) {
                $q->select('id', 'plant_master_id', 'incharge_name', 'incharge_contact_no');
            }
        ])
        ->withCount('incharges');

        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (!empty($filters['user_id'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('plant_masters.municipality_id', $filters['user_id'])
                    ->orWhere('plant_masters_municipality_tagged.municipality_id', $filters['user_id']);
                });
            }
        }

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) {
            $query->where('plant_masters.municipality_id', '=', $filters['municipality_id']);
        }

        if (isset($filters['tagged_municipality_id']) && !empty($filters['tagged_municipality_id'])) {
            $query->where('plant_masters_municipality_tagged.municipality_id', '=', $filters['tagged_municipality_id']); //is this ok ?
        }

        if (!empty($filters['status'])) {
            $query->where('plant_masters.status', $filters['status']);
        }

        if (!empty($filters['id'])) {
            $query->where('plant_masters.id', $filters['id']);
            return $query->first();
        }

        // Ordering
        $query->orderBy('plant_masters.id', 'desc');
        $query->groupBy(
            'plant_masters.id',
            'plant_masters.municipality_id',
            'plant_masters.uid',
            'plant_masters.ward_id',
            'plant_masters.location',
            'plant_masters.category_id',
            'plant_masters.capacity',
            'plant_masters.operate_by',
            'plant_masters.agency_id',
            'plant_masters.created_at',
            'plant_masters.updated_at',
            'plant_masters.status',
            'municipality_name',
            'ward_no',
            'category_name',
            'agency_name',
            'capacity_mod',
        );

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    // ✅ Incharges
    public function incharges()
    {
        return $this->hasMany(PlantMasterInchargeModel::class, 'plant_master_id', 'id');
    }

    // ✅ Tagged Municipalities
    public function taggedMunicipalities()
    {
        return $this->hasMany(PlantMasterMunicipalityTaggedModel::class, 'plant_master_id', 'id');
    }
}

