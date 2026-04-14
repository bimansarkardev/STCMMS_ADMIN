<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class VehiclesModel extends Model
{
    protected $table = 'vehicles';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality_id',
        'vehicle_type',
        'vehicle_reg_no',
        'capacity',
        'vehicle_category_id',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getVehicles($perPage, $filters = [])
    {
        $query = VehiclesModel::query()
        ->leftJoin('user', 'user.user_id', '=', 'vehicles.municipality_id')
        ->leftJoin('vehicle_categories', 'vehicle_categories.id', '=', 'vehicles.vehicle_category_id')
        ->select(
            'vehicles.id',
            'vehicles.municipality_id',
            'user.name as municipality_name',
            'vehicles.vehicle_type',
            'vehicles.vehicle_reg_no',
            'vehicles.vehicle_category_id',
            'vehicle_categories.name as vehicle_category_name',
            DB::raw("CONCAT(vehicles.capacity , ' ltr.') as capacity_mod")
        );

        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (!empty($filters['user_id'])) {
                $query->where('vehicles.municipality_id', $filters['user_id']);
            }
        }

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) {
            $query->where('vehicles.municipality_id', '=', $filters['municipality_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('vehicles.status', $filters['status']);
        }

        if (!empty($filters['id'])) {
            $query->where('vehicles.id', $filters['id']);
            return $query->first();
        }

        // Ordering
        $query->orderBy('vehicles.id', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }
}

