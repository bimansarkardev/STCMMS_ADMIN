<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AgenciesMunicipalitiesModel extends Model
{
    protected $table = 'agencies_municipalities';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'agency_id',
        'municipality_id',
        'contract_from_date',
        'contract_to_date',
        'contract_file',
        'created_at',
        'updated_at',
        'status',
    ];

    // ✅Agency
    public function agency()
    {
        return $this->belongsTo(AgenciesModel::class, 'agency_id', 'id');
    }
}

