<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FieldWorkersModel extends Model 
{
    public $timestamps = true;
    protected $table = 'field_workers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality_id',
        'role',
        'field_worker_name',
        'field_worker_mobile_no',
        'address',
        'is_user',
        'user_id',
        'operate_by',
        'agency_id',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getFieldWorkers($perPage, $filters = [])
    {
        $query = FieldWorkersModel::query()
        ->leftJoin('user', 'user.user_id', '=', 'field_workers.user_id')
        ->leftJoin('user as municipality', 'municipality.user_id', '=', 'field_workers.municipality_id')
        ->leftJoin('roles', 'roles.id', '=', 'field_workers.role')
        ->leftJoin('agencies', 'agencies.id', '=', 'field_workers.agency_id')
        ->select(
            'field_workers.id',
            'field_workers.is_user',
            'field_workers.user_id',
            'field_workers.field_worker_name',
            'field_workers.field_worker_mobile_no',
            'field_workers.address',
            'user.username',
            'field_workers.municipality_id',
            'municipality.name as municipality_name',
            'field_workers.role',
            'roles.name as role_name',
            'field_workers.operate_by',
            DB::raw("
                CASE 
                    WHEN field_workers.operate_by = 1 THEN 'Municipality'
                    WHEN field_workers.operate_by = 2 THEN 'Agency'
                    ELSE 'N/A'
                END as operate_by_label
            "),
            DB::raw("
                CASE 
                    WHEN field_workers.operate_by = 1 THEN municipality.name
                    WHEN field_workers.operate_by = 2 THEN agencies.agency_name
                    ELSE '-'
                END as working_under_name
            "),
            'field_workers.agency_id',
            'agencies.agency_name',
        );

        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            $query->where('field_workers.municipality_id', '=', $filters['user_id']);
        }

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) {
            $query->where('field_workers.municipality_id', '=', $filters['municipality_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('field_workers.status', $filters['status']);
        }

       if (!empty($filters['field_worker_roles']) && is_array($filters['field_worker_roles'])) {
            $query->whereIn('field_workers.role', $filters['field_worker_roles']);
        }

        if (!empty($filters['id'])) {
            $query->where('field_workers.id', $filters['id']);
            return $query->first();
        }

        if (!empty($filters['field_worker_user_id'])) {
            $query->where('field_workers.user_id', $filters['field_worker_user_id']);
            return $query->first();
        }        

        // Ordering
        $query->orderBy('municipality.name', 'asc');
        $query->orderBy('roles.ordering', 'asc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

} //getFieldWorkers

