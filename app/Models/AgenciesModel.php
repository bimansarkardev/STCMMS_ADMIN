<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class AgenciesModel extends Model
{
    protected $table = 'agencies';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'agency_name',
        'agency_address',
        'contact_person',
        'contact_person_contact_number',
        'created_at',
        'updated_at',
        'status',
    ];

    public function getAgencies($perPage, $filters = [])
    {
        $query = AgenciesModel::query()
        ->select(
            'agencies.id',
            'agencies.agency_name',
            'agencies.agency_address',
            'agencies.contact_person',
            'agencies.contact_person_contact_number'
        )
        ->with([
            'agenciesMunicipalities' => function ($q) {
                $q->join('user', 'user.user_id', '=', 'agencies_municipalities.municipality_id')
                  ->select(
                      'agencies_municipalities.id',
                      'agencies_municipalities.agency_id',
                      'agencies_municipalities.municipality_id',
                      'agencies_municipalities.contract_from_date',
                      DB::raw("DATE_FORMAT(agencies_municipalities.contract_from_date, '%d/%m/%Y') as formatted_contract_from_date"),
                      'agencies_municipalities.contract_to_date',
                      DB::raw("DATE_FORMAT(agencies_municipalities.contract_to_date, '%d/%m/%Y') as formatted_contract_to_date"),
                      'agencies_municipalities.contract_file',
                        DB::raw("
                            CONCAT(
                                '" . rtrim(config('app.url'), '/') . "/',
                                agencies_municipalities.contract_file
                            ) AS contract_file_full_filepath
                        "),
                      'user.name as municipality_name'
                  );
            }
        ])
        ->withCount('agenciesMunicipalities');

        // Filters
        if (!empty($filters['user_type_id']) && $filters['user_type_id'] == 2) {
            if (!empty($filters['user_id'])) {
                $query->where('agencies.municipality_id', $filters['user_id']);
            }
        }

        if (isset($filters['municipality_id']) && !empty($filters['municipality_id'])) {
            $query->where('agencies.municipality_id', '=', $filters['municipality_id']);
        }

        if (!empty($filters['status'])) {
            $query->where('agencies.status', $filters['status']);
        }

        if (!empty($filters['id'])) {
            $query->where('agencies.id', $filters['id']);
            return $query->first();
        }

        // Ordering
        $query->orderBy('agencies.id', 'desc');

        return $perPage ? $query->paginate($perPage) : $query->get();
    }

    // ✅ municipalities
    public function agenciesMunicipalities()
    {
        return $this->hasMany(AgenciesMunicipalitiesModel::class, 'agency_id', 'id');
    }
}

