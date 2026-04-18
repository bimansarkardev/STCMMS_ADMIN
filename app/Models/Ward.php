<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Ward extends Model
{
    protected $table = 'ward';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality',
        'ward_no',
        'created_at',
        'updated_at'
    ];

    public function getWard($perPage, $filters = [])
    {
        //dd($filters);
        $query = DB::table('ward')
            ->leftJoin('user', 'user.user_id', '=', 'ward.municipality')
            ->select(
                'ward.id',
                'ward.municipality',
                'user.name as municipality_name',
                DB::raw("CONCAT(ward.ward_no , ' No. Ward') as ward_no"),
                DB::raw("(SELECT COUNT(*) FROM road WHERE road.ward = ward.id) as road_count")
            );

        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (isset($filters['user_id']) && !empty($filters['user_id'])) {
                $query->where('ward.municipality', '=', $filters['user_id']);
            }
        }

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) {
            $query->where('ward.municipality', '=', $filters['municipality_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('ward.status', $filters['status']);
        }

        if (!empty($filters['id'])) {
            $query->where('ward.id', $filters['id']);
            return $query->first();
        }

        // Ordering
        $query->orderBy('user.name', 'asc')
            ->orderBy('ward.ward_no', 'asc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}

