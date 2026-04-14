<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ModuleMaster extends Model
{
    public $timestamps = true;
    protected $table = 'module_master';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'slug',
        'type',
        'filepath',
        'details',
        'tags',
        'created_at',
        'updated_at',
        'status',
    ];
}

