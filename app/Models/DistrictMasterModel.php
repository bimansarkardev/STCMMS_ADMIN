<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DistrictMasterModel extends Model
{
    public $timestamps = true;
    protected $table = 'district_master';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'zone_id',
        'district_name',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getDistrictMaster($filters = [],$returnCount = false)
    {
        $query = DB::table('district_master')
            ->leftJoin('zone_master', 'zone_master.id', '=', 'district_master.zone_id')
            ->select(
                'district_master.id',
                'district_master.district_name',
                'district_master.zone_id',
                'zone_master.zone_name',
                'district_master.created_at',
                'district_master.updated_at',
                'district_master.status',
            );

        if (isset($filters['searchKey']) && !empty($filters['searchKey'])) 
        {
            $query->where('district_master.district_name', 'like', '%' . $filters['searchKey'] . '%');
        }

        $query->orderBy('district_master.district_name', 'asc');

        /* ---------------- Count only ---------------- */
        if ($returnCount) {
            return $query->count();
        }

        /* ---------------- Single record ---------------- */
        if (!empty($filters['id'])) {

            $record = $query
                ->where('district_master.id', $filters['id'])
                ->first();

            return $record;
        }

        /* ---------------- API Pagination ---------------- */

        if (!is_null($filters['per_page']) && !is_null($filters['page'])) {
            $offset = ($filters['page'] - 1) * $filters['per_page'];
            $query->limit($filters['per_page'])->offset($offset);
        }

        $data = $query->get();

        return $data;
    }
}

