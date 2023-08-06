<?php

namespace App\Helpers;
use Illuminate\Support\Facades\Http;

class VerificationHelper
{
    protected $apiKey;
    protected $apiSecret;

    public function __construct()
    {
        $this->apiKey = config('services.sms_service.api_key');
        $this->apiSecret = config('services.sms_service.api_secret');
    }
    public function sendVerificationCode($phoneNumber)
    {
        $code = random_int(100000, 999999);

        $response = Http::post('https://api.sms-service.com/send', [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'phone_number' => $phoneNumber,
            'message' => "Your verification code is $code",
        ]);

        if ($response->ok()) {
            return $code;
        } else {
            return false;
        }
    }
    public function verifyCode($phoneNumber, $code)
    {
        $response = Http::post('https://api.sms-service.com/verify', [
            'api_key' => $this->apiKey,
            'api_secret' => $this->apiSecret,
            'phone_number' => $phoneNumber,
            'code' => $code,
        ]);
    
        if ($response->ok() && $response['status'] === 'success') {
            return true;
        } else {
            return false;
        }
    }
}