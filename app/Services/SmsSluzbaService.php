<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsSluzbaService
{
    public function send(string $phoneNumber, string $message): bool
    {
        $login = config('services.sms_sluzba.login');
        $password = config('services.sms_sluzba.password');

        if (!$login || !$password) {
            Log::warning('SMS: Credentials not configured, logging instead', [
                'to' => $phoneNumber,
                'message' => $message,
            ]);
            return false;
        }

        $phone = preg_replace('/[^0-9+]/', '', $phoneNumber);

        try {
            $response = Http::get(config('services.sms_sluzba.api_url'), [
                'login' => $login,
                'password' => $password,
                'act' => 'send',
                'msisdn' => $phone,
                'msg' => $message,
            ]);

            if ($response->successful()) {
                Log::info('SMS sent successfully', ['to' => $phone]);
                return true;
            }

            Log::error('SMS sending failed', ['response' => $response->body()]);
            return false;
        } catch (\Exception $e) {
            Log::error('SMS sending error', ['error' => $e->getMessage()]);
            return false;
        }
    }
}
