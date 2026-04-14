<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDetails extends Model
{
    public $timestamps = true;
    protected $table = 'user_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'municipality_id',
        'district_id',
        'address',
        'created_at',
        'updated_at',
        'status',
    ];
}

