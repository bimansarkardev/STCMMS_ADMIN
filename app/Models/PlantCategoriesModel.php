<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class PlantCategoriesModel extends Model
{
    protected $table = 'plant_categories';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'name',
        'ordering',
        'status'
    ];
}

