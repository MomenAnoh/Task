<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class DeliveryController extends Controller
{
    public function getNearestDelivery(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $userLatitude = $request->latitude;
        $userLongitude = $request->longitude;

        // جلب جميع مندوبي التوصيل المسجلين
        $deliveryAgents = User::where('user_type', 'delivery')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $apiKey = env('GOOGLE_MAPS_API_KEY');
        $origins = "{$userLatitude},{$userLongitude}";

        $nearestAgents = [];

        foreach ($deliveryAgents as $agent) {
            $destinations = "{$agent->latitude},{$agent->longitude}";

            $response = Http::get("https://maps.googleapis.com/maps/api/distancematrix/json", [
                'origins' => $origins,
                'destinations' => $destinations,
                'key' => $apiKey,
                'units' => 'metric',
            ]);

            $data = $response->json();

            if (!empty($data['rows'][0]['elements'][0]['distance']['value'])) {
                $distance = $data['rows'][0]['elements'][0]['distance']['value']; // المسافة بالمتر
                $nearestAgents[] = [
                    'id' => $agent->id,
                    'name' => $agent->name,
                    'phone' => $agent->phone,
                    'distance' => $distance,
                ];
            }
        }

        // ترتيب المندوبين حسب المسافة
        usort($nearestAgents, function ($a, $b) {
            return $a['distance'] <=> $b['distance'];
        });

        return response()->json([
            'status' => 'success',
            'nearest_agents' => array_slice($nearestAgents, 0, 5) // إرجاع أقرب 5 مندوبي توصيل
        ]);
    }
}
