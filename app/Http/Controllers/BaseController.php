<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public function __construct()
    {
        if (!Session::has('user')) 
        {
            abort(response()->redirectTo(route('admin.login')));
        }
    }
}
