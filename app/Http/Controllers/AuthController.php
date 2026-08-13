<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->validate(['email'=>'required|email','password'=>'required']);
        if (!Auth::attempt($credentials)) return response()->json(['message'=>'بيانات الدخول غير صحيحة'], 401);
        $user = Auth::user();
        $token = $user->createToken('nm-system')->plainTextToken;
        return response()->json(['token'=>$token,'user'=>$user]);
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['ok'=>true]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function sendResetLink(Request $request)
    {
        $request->validate(['email'=>'required|email']);
        return response()->json(['ok'=>true,'message'=>'تم إرسال رابط الاستعادة']);
    }

    public function resetPassword(Request $request)
    {
        $request->validate(['email'=>'required|email','password'=>'required|confirmed','token'=>'required']);
        return response()->json(['ok'=>true,'message'=>'تم تغيير كلمة المرور']);
    }
}
