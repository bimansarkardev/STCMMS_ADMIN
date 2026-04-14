<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use App\Services\CommonService;

class AuthController extends Controller
{
    protected $commonService;

    public function __construct(CommonService $commonService)
    {
        $this->commonService = $commonService;
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required',
            'password' => 'required'
        ]);

        $user = User::where('username', $request->email)->first();
        // dd($user);

        if ($user && Hash::check($request->password, $user->password)) 
        {
            do {
                $login_token = $this->commonService->getToken(20, 'GENERAL');
                $exists = User::where('login_token', $login_token)->exists();
            } while ($exists);

            $user->login_token = $login_token;
            $user->save();
            if($user->user_type_id==2)
            {
                $user['municipality_id'] =  $user->user_id;
            }
            else if($user->user_type_id==3)
            {
                $user['municipality_id'] =  $user->created_by;
            }
            Session::put('user', $user);
            return redirect()->route('admin.dashboard');
        }

        return back()->with('error', 'Invalid credentials');
    }

    public function logout()
    {
        $user = session('user');
        if ($user) {
            $dbUser = User::find($user->user_id);
            if ($dbUser) {
                $dbUser->login_token = "";
                $dbUser->save();
            }
        }
        Session::forget('user');
        return redirect()->route('admin.login');
    }

    public function forgotPassword()
    {
        return view('auth.forgotPassword');
    }

    public function forgotPasswordOtp(Request $request)
    {
        $data = $request->json()->all();

        $email = $data['email'] ?? null;

        if (!$email) 
        {
            return response()->json(['error' => 'Email is required'], 400);
        }

        $user = User::where('email', $email)->first();

        if (!$user) 
        {
            return response()->json(['error' => 'Email not found'], 404);
        }
        else
        {
            //$otp = $this->commonService->getToken(4, 'OTP');
            $otp = 1234;
            $user->forget_pass_otp = $otp;
            $user->save();
        }

        // Simulate success response
        return response()->json(['success' => true, 'message' => 'OTP sent to your registered email address for reset password']);
    }

    public function resetPassword(Request $request)
    {
        if (!$request->email) 
        {
            return response()->json(['error' => 'Email is required'], 400);
        }

        $user = User::where('email', $request->email)->first();

        if (!$user) 
        {
            return response()->json(['error' => 'Email not found'], 404);
        }
        else
        {
            $request->validate([
            'password' => 'required|min:6',
            'confirm_password' => 'required|same:password',
            'otp' => 'required',
            ], [
                'confirm_password.same' => 'The new password confirmation does not match.',
            ]);

            if($request->otp!=$user->forget_pass_otp)
            {
                return response()->json(['error' => 'OTP mismatched'], 400);
            }

            $user = User::where('email', $request->email)->first();
            $user->password = Hash::make($request->confirm_password);
            $user->save();
        }

        // Simulate success response
        return response()->json(['success' => true, 'message' => 'Password successfully reset please login with it']);
    }

}
