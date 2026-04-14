<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserType extends Model
{
    protected $table = 'user_type';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'user_type',
        'menu_title',
        'menu_name',
        'add_menu_name',
        'edit_menu_name',
        'slug',
        'status'
    ];
}

