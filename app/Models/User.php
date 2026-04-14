<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use App\Models\AgencyRegistrationRequest;


class User extends Authenticatable
{
    use HasFactory, Notifiable;
    public $timestamps = true;
    protected $table = 'user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id',
        'username',
        'user_type_id',
        'role',
        'name',
        'email',
        'mobile',
        'filepath',
        'password',
        'login_token',        
        'forget_pass_otp',
        'otp',
        'created_by',
        'created_at',        
        'updated_at',
        'active_stat',
        'status',
    ];

    protected $hidden = [
        'password',
        'login_token',
        'forget_pass_otp',
        'otp',
    ];

    public function getUser($perPage = 10, $filters = [])
    {
        $query = DB::table('user')
            ->leftJoin('user_type', 'user.user_type_id',    '=', 'user_type.id')
            ->leftJoin('user_details', 'user.user_id',         '=', 'user_details.user_id')
            ->leftJoin('district_master', 'district_master.id',         '=', 'user_details.district_id')
            ->leftJoin('roles', 'roles.id', '=', 'user.role')
            ->leftJoin('user as municipality', 'municipality.user_id', '=', 'user_details.municipality_id')
            ->select([
                'user.user_id',
                'user.username',
                'user_type.user_type',
                'user.created_by',
                'user.name',
                'user.email',
                'user.mobile',
                'user_details.district_id',
                'district_master.district_name',
                'user_details.address',
                'user.role',
                'roles.name as role_name',
                'user_details.municipality_id',
                'municipality.name as municipality_name',
            ]);


        if (isset($filters['user_type']) && !empty($filters['user_type']))
        {
            $query->where('user.user_type_id', '=', $filters['user_type']);
        }

        if (isset($filters['name']) && !empty($filters['name'])) 
        {
            $query->where('user.name', 'like', '%' . $filters['name'] . '%');
        }

        if (isset($filters['user_id']) && !empty($filters['user_id'])) 
        {
            $query->where('user.user_id', '=', $filters['user_id']);
            return $query->first();
        }

        if (isset($filters['login_token']) && !empty($filters['login_token'])) 
        {
            $query->where('user.login_token', '=', $filters['login_token']);
            return $query->first();
        }

        if (isset($filters['created_by']) && !empty($filters['created_by']))
        {
            $query->where('user.created_by', '=', Session::get('user')->user_id);
        }

        $query->orderBy('user.name' , 'asc');

        if($perPage!="")
        {
            return $query->paginate($perPage);
        }
        else
        {
            return $query->get();
        }
    }

    public function getMunicipalities($filters = [])
    {
        $query = DB::table('user')
            ->Join('user_details', 'user.user_id', '=', 'user_details.user_id')
            //->Join('cities', 'user_details.city', '=', 'cities.id')
            ->select('user.user_id', 'user.name');

        if (isset($filters['user_type']) && !empty($filters['user_type'])) 
        {
            $query->where('user.user_type_id', '=', $filters['user_type']);
        }

        if (isset($filters['city']) && !empty($filters['city'])) 
        {
            $query->where('user_details.city', '=', $filters['city']);
        }

        return $query->get();
    }

    public function getLevelWiseGrievanceAdmin($filters = [])
    {
        $query = DB::table('user')
            ->Join('level_master', 'user.level', '=', 'level_master.id')
            ->select('user.user_id', 'user.name');

        $query->where('user.user_type_id', '=', 3);

        if (isset($filters['created_by']) && !empty($filters['created_by'])) 
        {
            $query->where('user.created_by', '=', $filters['created_by']);
        }

        if (isset($filters['level']) && !empty($filters['level'])) 
        {
            $query->where('user.level', '=', $filters['level']);
        }

        return $query->first();
    }
}

