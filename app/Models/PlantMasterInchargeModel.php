<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlantMasterInchargeModel extends Model
{
    protected $table = 'plant_master_incharges';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'municipality_id',
        'plant_master_id',
        'incharge_name',
        'incharge_contact_no',
        'created_at',
        'updated_at',
        'status',
    ];

    // ✅ Parent Plant
    public function plant()
    {
        return $this->belongsTo(PlantMasterModel::class, 'plant_master_id', 'id');
    }
}

