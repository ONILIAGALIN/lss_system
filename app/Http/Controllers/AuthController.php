<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;
use App\Models\Activity_log;

class AuthController extends Controller
{
    public function login(Request $request){
        $validator = Validator::make($request->all(), [
            'username' => 'required',
            'password' => 'required|min:6'
        ]);
   
        if ($validator->fails()) {
            return response()->json([
                'ok' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $login = $request->input('username');
        $password = $request->input('password');
        if (Auth::attempt(['email' => $login, 'password' => $password]) ||
            Auth::attempt(['username' => $login, 'password' => $password])) {
            
          // $user = Auth::user();
            $user = User::where('id',Auth::id())->first();
            $token = $user->createToken('auth_token')->plainTextToken;

        Activity_log::create([
            'user_id' => $user->id,
            'action' => 'Login',
            'description' => 'User logged in successfully',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);    
            return response()->json([
                'ok' => true,
                'message' => 'Login successful',
                'user' => $user,
                'token' => $token
            ], 200);
        }else{
             return response()->json(['error']);
        }

       /* $apiDatad = Http::get('https://teahub.depedcalabarzon.ph/api/login-request/$2y$10$cLeGKQPtcL1mXbaAGp6NDeKml4EEN0468YrdSSLnjlMfZNxLgC/' . $request->email . '/' . $request->password);
        $result = json_decode($apiDatad);
        return response()->json([
            'success' => false,
            'message' => 'Invalid credentials',
            "data" => $result
        ], 400); */
    }

    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logged out successfully'
        ], 200);
    }

    public function checkToken(Request $request){
        $user = $request->user();
        $user->profile;
        return response()->json([
            "ok" => true,
            "message" => "UserInfo has been retrieve!",
            "data" => $user
        ],200);
    }
}
