<?php

namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;

class BelioSms //here
{
    /**
     * Send SMS to one or multiple recipients
     */
    public function send($recipients, string $message): array
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        try {
            $token = $this->getAccessToken();
            return $this->sendMessages($token, $recipients, $message);
        } catch (Exception $e) {
            Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'recipients' => $recipients
            ]);
            throw $e;
        }
    }

    /**
     * Get OAuth access token
     */
    private function getAccessToken(): string
    {
        $clientId = config('services.belio.client_id');
        $clientSecret = config('services.belio.client_secret');

        $postData = http_build_query([
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'grant_type' => 'client_credentials'
        ]);

        $response = $this->makeHttpRequest(
            'https://account.belio.co.ke/realms/api/protocol/openid-connect/token',
            'POST',
            $postData,
            ['Content-Type: application/x-www-form-urlencoded']
        );

        if (empty($response['access_token'])) {
            throw new Exception('Failed to get access token: ' . json_encode($response));
        }

        return $response['access_token'];
    }

    /**
     * Send messages via Belio API
     */
    private function sendMessages(string $token, array $recipients, string $message): array
    {
        $serviceId = config('services.belio.service_id');

        $messages = array_map(fn($phone) => [
            'text' => $message,
            'phone' => $phone
        ], $recipients);

        $payload = json_encode([
            'type' => 'SendToEach',
            'messages' => $messages
        ]);

        return $this->makeHttpRequest(
            "https://api.belio.co.ke/message/" . urlencode($serviceId),
            'POST',
            $payload,
            [
                "Authorization: Bearer " . $token,
                "Content-Type: application/json"
            ]
        );
    }

    /**
     * Make HTTP request
     */
    private function makeHttpRequest(
        string $url,
        string $method,
        $data,
        array $headers = []
    ): array {
        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
        ];

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            throw new Exception("HTTP request failed: " . $error);
        }

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            throw new Exception(
                "API request failed with status {$httpCode}: " .
                    ($decoded['error_description'] ?? $response)
            );
        }

        return $decoded;
    }
}
