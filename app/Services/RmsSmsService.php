<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class RmsSmsService
{
    protected $username;
    protected $apiKey;
    protected $senderId;
    protected $apiEndpoint;
    protected $timeout;

    public function __construct()
    {
        $this->username = config('rms_sms.username');
        $this->apiKey = config('rms_sms.api_key');
        $this->senderId = config('rms_sms.sender_id');
        $this->apiEndpoint = config('rms_sms.api_endpoint');
        $this->timeout = config('rms_sms.timeout');
    }

    /**
     * Send SMS to single or multiple recipients
     * 
     * @param string|array $recipient Phone number(s) - can be single string or comma-separated string or array
     * @param string $message Message content to send
     * @param string|null $senderId Custom sender ID (optional, defaults to config)
     * @return array Response from API
     * @throws Exception
     */
    public function send($recipient, $message, $senderId = null)
    {
        try {
            // Prepare recipient(s)
            $recipients = $this->formatRecipients($recipient);

            if (empty($recipients)) {
                throw new Exception('No valid recipients provided');
            }

            // Use custom sender ID if provided, otherwise use config
            $sender = $senderId ?? $this->senderId;

            // Build request payload
            $payload = [
                'username' => $this->username,
                'senderId' => $sender,
                'recipient' => $recipients,
                'message' => $message,
            ];

            // Send request
            $response = Http::timeout($this->timeout)
                ->withHeaders([
                    'apikey' => $this->apiKey,
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ])
                ->post($this->apiEndpoint, $payload);

            // Parse response
            $result = $response->json();

            // Log successful send
            Log::info('SMS sent successfully', [
                'recipients' => $recipients,
                'sender_id' => $sender,
                'response' => $result,
            ]);

            return [
                'success' => true,
                'data' => $result,
                'message' => 'SMS sent successfully',
            ];
        } catch (Exception $e) {
            Log::error('Failed to send SMS', [
                'recipient' => $recipient,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send SMS: ' . $e->getMessage(),
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Send SMS with automatic retry on failure
     * 
     * @param string|array $recipient Phone number(s)
     * @param string $message Message content
     * @param int $retries Number of retry attempts
     * @param string|null $senderId Custom sender ID
     * @return array Response from API
     */
    public function sendWithRetry($recipient, $message, $retries = 3, $senderId = null)
    {
        $attempt = 0;

        while ($attempt < $retries) {
            $result = $this->send($recipient, $message, $senderId);

            if ($result['success']) {
                return $result;
            }

            $attempt++;

            if ($attempt < $retries) {
                // Wait before retry (exponential backoff)
                sleep(pow(2, $attempt - 1));
            }
        }

        return $result;
    }

    /**
     * Format recipients to comma-separated string
     * 
     * @param string|array $recipient Phone number(s)
     * @return string Formatted recipient string
     */
    protected function formatRecipients($recipient)
    {
        if (is_array($recipient)) {
            // Filter empty values and join with comma
            $formatted = implode(',', array_filter($recipient));
        } else {
            // If already string, just trim spaces
            $formatted = trim($recipient);
        }

        return $formatted;
    }

    /**
     * Check SMS balance/account status
     * This is a placeholder for future implementation if API supports it
     * 
     * @return array Account status
     */
    public function getBalance()
    {
        Log::info('Balance inquiry - this feature may not be supported by RMS SMS API');

        return [
            'success' => false,
            'message' => 'Balance inquiry not yet implemented for this API',
        ];
    }

    /**
     * Parse and validate API response
     * 
     * @param array $response Raw API response
     * @return array Parsed response with success status
     */
    public function parseResponse($response)
    {
        if (!isset($response['message'])) {
            return [
                'success' => false,
                'message' => 'Invalid API response format',
            ];
        }

        $messages = (array)$response['message'];
        $allSuccessful = true;
        $failedCount = 0;

        foreach ($messages as $msg) {
            // Status 111 means success
            if (($msg['status'] ?? null) !== 111) {
                $allSuccessful = false;
                $failedCount++;
            }
        }

        return [
            'success' => $allSuccessful,
            'total_messages' => count($messages),
            'failed_count' => $failedCount,
            'messages' => $messages,
        ];
    }
}
