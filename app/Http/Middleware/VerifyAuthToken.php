<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Models\User;

class VerifyAuthToken
{
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->bearerToken();
        
        if($token=="none" || $token=="")
        {
            return response()->json([
                'status'=>false,
                'message'=>'You have to send authorization token with this request'
            ], 401);
        }
        
        $user = User::where('login_token',$token)->first();
        
        if($user)
        {
            return $next($request);
        }
        else
        {
            return response()->json([
                'status'=>false,
                'message'=>'Invalid Authorization Token'
            ], 401);
        }        
    }
}
