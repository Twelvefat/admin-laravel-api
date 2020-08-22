<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email','password');
        try{
            if(!$token = JWTAuth::attempt($credentials)){
                return response()->json([
                    'message' => 'Invalid Email or Password'
                ], 400);
            }
        }catch(JWTException $e){
            return response()->json([
                'message' => 'Could not create a token, try again please'
            ], 500);
        }

        $user = auth()->user();

        return response()->json([
            'message' => 'Successfully Login !',
            'token' => $token,
            'user' => $user
        ], 200);
    }

    public function logout(){
        $getToken = JWTAuth::getToken();
        JWTAuth::invalidate($getToken);
        return response()->json([
            'message' => 'Successfully Logout !'
        ], 200);
    }
}
