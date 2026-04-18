<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Road extends Model
{
    protected $table = 'road';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality',
        'ward',
        'road_name',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getRoad($perPage, $filters = [])
    {
        $query = DB::table('road')
            ->Join('ward', 'road.ward', '=', 'ward.id')
            ->leftJoin('user', 'user.user_id', '=', 'ward.municipality')
            ->select(
                'road.id',
                'road.road_name',
                'road.ward',
                DB::raw("CONCAT(ward.ward_no , ' No. Ward') as ward_no"),
                'user.name as municipality_name',
            );
        
        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (isset($filters['user_id']) && !empty($filters['user_id'])) {
                $query->where('road.municipality', '=', $filters['user_id']);
            }
        }

        if (isset($filters['municipality']) && !empty($filters['municipality'])) {
            $query->where('road.municipality', '=', $filters['municipality']);
        }

        if (isset($filters['ward']) && !empty($filters['ward'])) {
            $query->where('road.ward', '=', $filters['ward']);
        }

        if (isset($filters['status']) && !empty($filters['status'])) {
            $query->where('road.status', '=', $filters['status']);
        }

        // Ordering
        $query->orderBy('user.name', 'asc')
            ->orderBy('ward.ward_no', 'asc')
            ->orderBy('road.road_name', 'asc')
            ;

        if (isset($filters['id']) && !empty($filters['id'])) {
            $query->where('road.id', '=', $filters['id']);
            return $query->first();
        }

        if($perPage!="")
        {
            return $query->paginate($perPage);
        }
        else
        {
            return $query->get();
        }
    }
}

