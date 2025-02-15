<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Twilio\Rest\Client;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    // خطوة التسجيل
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'longitude' => 'required|numeric',
            'location' =>'required|numeric',
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 400);
        }


        // رفع الصورة إذا كانت موجودة
        if ($request->hasFile('profile_image')) {
            $imagePath = $request->file('profile_image')->store('profile_images', 'public');
        }

        // إنشاء المستخدم الجديد
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'profile_image' => $imagePath ?? null,
        ]);

        // إرسال كود التحقق عبر SMS
//       $this->sendVerificationCode($user->phone);

        return response()->json(['message' => 'User registered successfully.'], 200);
    }

    // خطوة إرسال كود التحقق عبر Twilio
    private function sendVerificationCode($phone)
    {
        $sid = env('TWILIO_SID');
        $authToken = env('TWILIO_AUTH_TOKEN');
        $twilioNumber = env('TWILIO_PHONE_NUMBER');

        // تأكد من أن الرقم بصيغة E.164 الصحيحة
        $phone = '+20' . ltrim($phone, '0');

        $client = new Client($sid, $authToken);
        $code = rand(1000, 9999);

        $message = $client->messages->create(
            $phone,
            [
                'from' => $twilioNumber,
                'body' => "Your verification code is: {$code}",
            ]
        );
    }

    // خطوة تسجيل الدخول
    public function login(Request $request)
    {


        $request->validate([
            'phone' => 'required',
            'password' => 'required|string',
        ]);
        $credentials = $request->only('password', 'phone');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }
        $user = Auth::user();

        if ($token = JWTAuth::attempt($credentials)) {
            return response()->json([
                'status' => 'success',
                'user' => $user,
                'authorisation' => [
                    'token' => $token,
                    'type' => 'bearer',
                ]
            ]);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }

    // خطوة الحصول على بيانات المستخدم المتصل


    // خطوة تسجيل الخروج
    public function logout()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
        return response()->json(['message' => 'Successfully logged out']);
    }

}
