<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserDevice extends Model
{
    public $timestamps = true;
    protected $table = 'user_device';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_id',
        'device_type',
        'login_token',
        'fcm_token',
        'created_at',
        'updated_at',
    ];
}

