<?php

namespace App\Http\Controllers\Examples;

use App\Facades\RmsSms;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * Example controller showing how to use the RMS SMS Service
 * 
 * This is a reference implementation. Copy patterns as needed to your actual controllers.
 */
class RmsSmsExampleController
{
    /**
     * Test SMS: Send a test message to your number
     */
    public function testSms()
    {
        $testPhone = '0713295853';
        $testMessage = 'Test SMS from Beyond Debt - RMS SMS Service is working!';

        Log::info('Testing RMS SMS Service', [
            'phone' => $testPhone,
            'message' => $testMessage,
        ]);

        $response = RmsSms::send($testPhone, $testMessage);

        if ($response['success']) {
            Log::info('Test SMS sent successfully', [
                'response' => $response['data'],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Test SMS sent successfully to ' . $testPhone,
                'phone' => $testPhone,
                'data' => $response['data'],
            ]);
        }

        Log::error('Test SMS failed', [
            'error' => $response['message'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send test SMS: ' . $response['message'],
            'error' => $response['message'],
        ], 400);
    }

    /**
     * Example: Send SMS to single recipient
     */
    public function sendSingleSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $response = RmsSms::send(
            $request->phone,
            $request->message
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'SMS sent successfully',
                'data' => $response['data'],
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'],
        ], 400);
    }

    /**
     * Example: Send SMS to multiple recipients
     */
    public function sendBulkSms(Request $request)
    {
        $request->validate([
            'phones' => 'required|array|min:1',
            'phones.*' => 'required|string',
            'message' => 'required|string|max:160',
        ]);

        $response = RmsSms::send(
            $request->phones,
            $request->message
        );

        if ($response['success']) {
            $parsed = RmsSms::parseResponse($response['data']);

            return response()->json([
                'success' => true,
                'message' => "SMS sent to {$parsed['total_messages']} recipients",
                'failed' => $parsed['failed_count'],
                'data' => $parsed,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'],
        ], 400);
    }

    /**
     * Example: Send OTP SMS
     */
    public function sendOtpSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
        ]);

        // Generate OTP
        $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        // Send OTP
        $response = RmsSms::send(
            $request->phone,
            "Your verification code is: $otp. Valid for 5 minutes."
        );

        if ($response['success']) {
            // Store OTP in cache/database for verification later
            cache()->put("otp_{$request->phone}", $otp, now()->addMinutes(5));

            return response()->json([
                'success' => true,
                'message' => 'OTP sent to your phone',
                // Don't return OTP in response in production!
            ]);
        }

        Log::error('Failed to send OTP', [
            'phone' => $request->phone,
            'error' => $response['message'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send OTP. Please try again.',
        ], 400);
    }

    /**
     * Example: Send notification SMS with retry
     */
    public function sendNotificationSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
            'critical' => 'boolean', // If critical, retry 5 times instead of 3
        ]);

        $retries = $request->boolean('critical') ? 5 : 3;

        $response = RmsSms::sendWithRetry(
            $request->phone,
            $request->message,
            $retries
        );

        if ($response['success']) {
            return response()->json([
                'success' => true,
                'message' => 'Notification sent successfully',
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $response['message'],
        ], 400);
    }

    /**
     * Example: Send SMS to lead with business context
     */
    public function sendLeadSms(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'message' => 'required|string|max:160',
        ]);

        // Get lead
        $lead = \App\Models\Lead::find($request->lead_id);

        // Send SMS
        $response = RmsSms::send(
            $lead->phone,
            $request->message,
            'DEBT' // Custom sender ID for context
        );

        if ($response['success']) {
            // Log SMS activity
            \App\Models\Activity::create([
                'lead_id' => $lead->id,
                'activity_type' => 'sms_sent',
                'description' => $request->message,
                'created_by' => auth()->id(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'SMS sent to lead',
            ]);
        }

        Log::error('Failed to send SMS to lead', [
            'lead_id' => $lead->id,
            'phone' => $lead->phone,
            'error' => $response['message'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send SMS',
        ], 400);
    }

    /**
     * Example: Send reminder SMS (maybe from a scheduled job)
     */
    public function sendReminderSms()
    {
        // Get all leads with upcoming PTPs
        $leads = \App\Models\Lead::with(['activities'])
            ->where('balance', '>', 0)
            ->get()
            ->filter(function ($lead) {
                return $lead->activities->whereDate('act_ptp_date', today())->count() > 0;
            });

        $phones = $leads->pluck('phone')->toArray();

        if (empty($phones)) {
            return response()->json([
                'success' => true,
                'message' => 'No reminders to send',
                'count' => 0,
            ]);
        }

        $message = 'Remember: You have a payment commitment scheduled for today. ' .
            'Please ensure funds are available.';

        $response = RmsSms::send($phones, $message, 'REMINDER');

        if ($response['success']) {
            $parsed = RmsSms::parseResponse($response['data']);

            Log::info('Reminders sent', [
                'total' => $parsed['total_messages'],
                'failed' => $parsed['failed_count'],
            ]);

            return response()->json([
                'success' => true,
                'message' => "Reminders sent to {$parsed['total_messages']} leads",
                'failed' => $parsed['failed_count'],
            ]);
        }

        Log::error('Failed to send reminders', [
            'error' => $response['message'],
        ]);

        return response()->json([
            'success' => false,
            'message' => 'Failed to send reminders',
        ], 400);
    }
}
