<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlantMasterMunicipalityTaggedModel extends Model
{
    protected $table = 'plant_masters_municipality_tagged';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality_id',
        'plant_master_id',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getPlantsWithTags($perPage, $filters = [])
    {
        // 🔹 Step 1: Get tagged municipalities (subquery data)
        $taggedData = DB::table('plant_masters_municipality_tagged')
            ->leftJoin('user', 'user.user_id', '=', 'plant_masters_municipality_tagged.municipality_id')
            ->select(
                'plant_masters_municipality_tagged.plant_master_id',
                'plant_masters_municipality_tagged.municipality_id',
                'user.name'
            )
            ->get()
            ->groupBy('plant_master_id');


        // 🔹 Step 2: Main query (NO GROUP BY)
        $query = DB::table('plant_masters')
            ->leftJoin('user', 'user.user_id', '=', 'plant_masters.municipality_id')
            ->leftJoin('ward', 'ward.id', '=', 'plant_masters.ward_id')
            ->leftJoin('plant_categories', 'plant_categories.id', '=', 'plant_masters.category_id')
            ->select(
                'plant_masters.*',
                'plant_masters.municipality_id',
                'user.name as municipality_name',
                DB::raw("CONCAT(ward.ward_no , ' No. Ward') as ward_no"),
                'plant_categories.name as category_name',
                DB::raw("CONCAT(plant_masters.capacity , ' ltr.') as capacity_mod")
            );

        // 🔹 Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (!empty($filters['user_id'])) {
                $query->where('plant_masters.municipality_id', $filters['user_id']);
            }
        }

        if (!empty($filters['municipality_id'])) {
            $query->where('plant_masters.municipality_id', $filters['municipality_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('plant_masters.status', $filters['status']);
        }

        // 🔹 Single record
        if (!empty($filters['id'])) {
            $query->where('plant_masters.id', $filters['id']);
            $row = $query->first();

            if ($row) {
                $row->tagged_municipalities = [];

                if (isset($taggedData[$row->id])) {
                    foreach ($taggedData[$row->id] as $tag) {
                        $row->tagged_municipalities[] = [
                            'municipality_id' => $tag->municipality_id,
                            'municipality_name' => $tag->name
                        ];
                    }
                }
            }

            return $row;
        }

        // 🔹 Ordering
        $query->orderBy('plant_masters.id', 'desc');

        // 🔹 Get result
        $result = $perPage ? $query->paginate($perPage) : $query->get();

        // 🔹 Merge tagged data
        $items = $perPage ? $result->items() : $result;

        foreach ($items as $row) {
            $row->tagged_municipalities = [];

            if (isset($taggedData[$row->id])) {
                foreach ($taggedData[$row->id] as $tag) {
                    $row->tagged_municipalities[] = [
                        'municipality_id' => $tag->municipality_id,
                        'municipality_name' => $tag->name
                    ];
                }
            }
        }

        return $result;
    }

    // ✅ Tagged Plant
    public function tagged_plant()
    {
        return $this->belongsTo(PlantMasterModel::class, 'plant_master_id', 'id');
    }
}

