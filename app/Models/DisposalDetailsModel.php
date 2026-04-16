<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class DisposalDetailsModel extends Model
{
    public $timestamps = true;
    protected $table = 'disposal_details';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'disposal_id',
        'collection_id',
        'created_at',
        'updated_at',
        'status',
    ];
}

