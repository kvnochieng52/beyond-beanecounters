<?php



namespace App\Models;

use Exception;
use Illuminate\Support\Facades\Log;

class BSms
{
    private $apiBaseUrl = 'https://api.belio.co.ke';
    private $authUrl = 'https://account.belio.co.ke/realms/api/protocol/openid-connect/token';

    public function send($recipients, string $message): array
    {
        $recipients = is_array($recipients) ? $recipients : [$recipients];

        try {
            $token = $this->getAccessToken();
            return $this->sendMessages($token, $recipients, $message);
        } catch (Exception $e) {
            Log::error('Belio SMS Failed', [
                'error' => $e->getMessage(),
                'recipients' => $recipients,
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }

    private function getAccessToken(): string
    {
        $response = $this->makeRequest(
            $this->authUrl,
            'POST',
            http_build_query([
                'client_id' => config('services.belio.client_id'),
                'client_secret' => config('services.belio.client_secret'),
                'grant_type' => 'client_credentials'
            ]),
            ['Content-Type: application/x-www-form-urlencoded']
        );

        return $response['access_token'] ?? throw new Exception(
            'Token request failed: ' . json_encode($response)
        );
    }

    private function sendMessages(string $token, array $recipients, string $message): array
    {
        $payload = [
            'type' => 'SendToEach',
            'messages' => array_map(fn($phone) => [
                'text' => $message,
                'phone' => $phone
            ], $recipients)
        ];


        //  $senederID = config('services.belio.service_id');

        $senderID = "4532a3b3-cf06-413c-8c8c-2ef1109dddf9";

        return $this->makeRequest(
            $this->apiBaseUrl . '/message/' . $senderID,
            'POST',
            json_encode($payload),
            [
                'Authorization: Bearer ' . $token,
                'Content-Type: application/json',
                //'Origin: ' . config('app.url'),
                //'Origin: https://bapp.co.ke'
            ]
        );
    }

    private function makeRequest(string $url, string $method, $data, array $headers): array
    {


        $curl = curl_init();

        $options = [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $method,
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_ENCODING => '',
        ];

        curl_setopt_array($curl, $options);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($error) {
            curl_close($curl);
            throw new Exception("cURL error: $error");
        }

        curl_close($curl);

        $decoded = json_decode($response, true) ?? [];

        if ($httpCode >= 400) {
            throw new Exception(
                "API error {$httpCode}: " . ($decoded['desc'] ?? $response)
            );
        }

        return $decoded;
    }
}
