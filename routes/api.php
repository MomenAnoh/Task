<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DeliveryController;
use App\Http\Controllers\NotificationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::get('/send_notifications', [NotificationController::class, 'sendPushNotification']);


Route::post('register', [AuthController::class, 'register']);  // مسار التسجيل
Route::post('login', [AuthController::class, 'login']);        // مسار الدخول
Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');  // مسار تسجيل الخروج
Route::post('/nearest-delivery', [DeliveryController::class, 'getNearestDelivery'])->middleware('auth:api');
