<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Http;

class WhatsAppHelper 
{
    public static function sendMessage($phone, $message) 
    {
        $instanceId = config('services.ultramsg.instance_id');
        $token = config('services.ultramsg.token');
        
        if (!$instanceId || !$token) {
            return false; // لم يتم إعداد الواتساب بعد
        }

        $url = "https://api.ultramsg.com/{$instanceId}/messages/chat";
        $countryCode = config('services.ultramsg.country_code', '+20');
        $formattedPhone = $countryCode . ltrim($phone, '0'); 

        $response = Http::post($url, [
            'token' => $token,
            'to' => $formattedPhone,
            'body' => $message
        ]);

        return $response->json();
    }
}