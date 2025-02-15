<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FirebaseNotificationService
{
    protected $firebaseUrl = "https://fcm.googleapis.com/v1/projects/YOUR_PROJECT_ID/messages:send";
    
    public function sendNotification($deviceToken, $title, $body, $data = [])
    {
        $accessToken = $this->getAccessToken(); // جلب التوكن الصحيح من Google
        
        if (!$accessToken) {
            Log::error("فشل في جلب Access Token من Google.");
            return ['error' => 'Failed to get Access Token'];
        }

        $payload = [
            "message" => [
                "token" => $deviceToken,
                "notification" => [
                    "title" => $title,
                    "body"  => $body
                ],
                "data" => $data
            ]
        ];

        // تسجيل الطلب المرسل
        Log::info("إرسال طلب Firebase", ['payload' => $payload]);

        // إرسال الطلب إلى Firebase
        $response = Http::withHeaders([
            "Authorization" => "Bearer {$accessToken}",
            "Content-Type"  => "application/json",
        ])->post($this->firebaseUrl, $payload);

        $responseData = $response->json() ?? ['error' => 'Empty response from Firebase'];

        // تسجيل الاستجابة بشكل آمن
        Log::info("استجابة Firebase", ['response' => $responseData]);

        return $responseData;
    }

    private function getAccessToken()
{
    $keyFilePath = env('FIREBASE_CREDENTIALS'); // استخدم مسار ملف JSON

    if (!file_exists($keyFilePath)) {
        Log::error("🔥 ملف مفاتيح Firebase غير موجود في: {$keyFilePath}");
        return null;
    }

    $jsonKey = json_decode(file_get_contents($keyFilePath), true);
    $client = new \Google_Client();
    $client->setAuthConfig($jsonKey);
    $client->addScope('https://www.googleapis.com/auth/firebase.messaging');

    return $client->fetchAccessTokenWithAssertion()['access_token'] ?? null;
}

}
