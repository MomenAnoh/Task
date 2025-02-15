<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseNotificationService;

class NotificationController extends Controller
{
    public function sendPushNotification()
    {
        Log::info("إرسال إشعار بدأ...");

        $deviceToken = "your_device_token_here";  // استبدلها بتوكن الجهاز الصحيح
        $title = "مرحبا!";
        $body = "هذا إشعار تجريبي من Laravel بدون مكتبة Kreait";
        $data = ["order_id" => 1234];

        $firebaseService = new FirebaseNotificationService();
        $response = $firebaseService->sendNotification($deviceToken, $title, $body, $data);

        // تأكد من أن الاستجابة مصفوفة حتى يتم تسجيلها في اللوجات
        Log::info("استجابة Firebase:", $response ? (array) $response : []);

        return response()->json($response);
    }
}
