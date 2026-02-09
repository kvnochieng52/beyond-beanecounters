<?php

namespace App\Jobs;

use App\Models\Activity;
use App\Models\ReminderProcess;
use App\Models\Lead;
use App\Models\BSms;
use App\Facades\RmsSms;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class PTPReminderJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $reminderProcess;
    protected $processDate;

    public function __construct($processDate = null)
    {
        $this->processDate = $processDate ?? Carbon::today()->toDateString();
    }

    public function handle()
    {
        Log::info("Starting PTP Reminder Job for date: {$this->processDate}");

        // Create reminder process record
        $this->reminderProcess = ReminderProcess::create([
            'process_type' => 'ptp_reminder',
            'process_date' => $this->processDate,
            'start_time' => now(),
            'status' => 'running',
            'created_by' => 1, // System user
            'updated_by' => 1,
        ]);

        try {
            // Get PTP activities due today (activity_type_id = 4) for leads with institution client_contract_type_id = 1 or 3 (Direct or RMS)
            $ptpActivities = Activity::where('activity_type_id', 4)
                ->whereDate('act_ptp_date', $this->processDate)
                ->whereHas('lead', function ($query) {
                    $query->whereHas('institution', function ($institutionQuery) {
                        $institutionQuery->whereIn('client_contract_type_id', [1, 3]);
                    });
                })
                ->with(['lead'])
                ->get();

            $totalCustomers = $ptpActivities->count();
            $successfulReminders = 0;
            $failedReminders = 0;
            $processedCustomers = [];

            Log::info("Found {$totalCustomers} PTP customers for date: {$this->processDate} (filtered by act_ptp_date and client_contract_type_id = 1 or 3)");

            foreach ($ptpActivities as $activity) {
                try {
                    $lead = $activity->lead;

                    // Debug logging to see what data we have
                    $leadData = Lead::query()->where('leads.id', $lead->id)->first();
                    Log::info("Processing Lead ID: {$lead->id}, Account: " . ($leadData->account_number ?? 'NULL') . ", Paybill: " . ($leadData->how_to_pay_instructions ?? 'NULL'));

                    if (!$lead || !$lead->telephone) {
                        Log::warning("No lead or telephone found for activity ID: {$activity->id}");
                        $failedReminders++;
                        continue;
                    }

                    // Generate reminder message
                    $message = $this->generateReminderMessage($lead, $activity);

                    // Determine contract type and route to appropriate SMS service
                    $institution = $lead->institution;
                    $isDirectContract = $institution && $institution->client_contract_type_id == 1;
                    $isRmsClient = $institution && $institution->client_contract_type_id == 3;

                    // Send SMS via appropriate gateway
                    if ($isDirectContract) {
                        // Use existing BSms for direct contracts
                        $sms = new BSms();
                        $response = $sms->send([$lead->telephone], $message);
                        $apiSuccess = isset($response['desc']) && $response['desc'] === 'OK';
                    } elseif ($isRmsClient) {
                        // Use RMS SMS gateway for RMS clients
                        $response = RmsSms::send($lead->telephone, $message);
                        $apiSuccess = isset($response['success']) && $response['success'] === true;
                    } else {
                        // Unknown contract type
                        $apiSuccess = false;
                        $response = ['desc' => 'Unknown contract type'];
                    }

                    if ($apiSuccess) {
                        $successfulReminders++;
                        $processedCustomers[] = [
                            'lead_id' => $lead->id,
                            'activity_id' => $activity->id,
                            'phone' => $lead->telephone,
                            'status' => 'success',
                            'message' => $message
                        ];

                        // Create activity record for the reminder
                        $reminderActivity = new Activity();
                        $reminderActivity->activity_type_id = 8; // SMS
                        $reminderActivity->activity_title = "PTP Reminder Sent";
                        $reminderActivity->description = $message;
                        $reminderActivity->lead_id = $lead->id;
                        $reminderActivity->assigned_user_id = $activity->assigned_user_id;
                        $reminderActivity->status_id = 2; // Completed
                        $reminderActivity->created_by = 1; // System
                        $reminderActivity->updated_by = 1; // System
                        $reminderActivity->save();

                        Log::info("PTP reminder sent successfully to lead ID: {$lead->id}, Phone: {$lead->telephone}");
                    } else {
                        $failedReminders++;
                        $processedCustomers[] = [
                            'lead_id' => $lead->id,
                            'activity_id' => $activity->id,
                            'phone' => $lead->telephone,
                            'status' => 'failed',
                            'error' => $response['desc'] ?? 'Unknown error'
                        ];
                        Log::error("Failed to send PTP reminder to lead ID: {$lead->id}, Error: " . ($response['desc'] ?? 'Unknown error'));
                    }
                } catch (\Exception $e) {
                    $failedReminders++;
                    Log::error("Error processing PTP reminder for activity ID: {$activity->id}, Error: " . $e->getMessage());
                }
            }

            // Update reminder process record
            $this->reminderProcess->update([
                'end_time' => now(),
                'total_customers' => $totalCustomers,
                'successful_reminders' => $successfulReminders,
                'failed_reminders' => $failedReminders,
                'status' => 'completed',
                'processed_customers' => $processedCustomers,
                'updated_by' => 1,
            ]);

            Log::info("PTP Reminder Job completed. Total: {$totalCustomers}, Success: {$successfulReminders}, Failed: {$failedReminders}");
        } catch (\Exception $e) {
            Log::error("PTP Reminder Job failed: " . $e->getMessage());

            $this->reminderProcess->update([
                'end_time' => now(),
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'updated_by' => 1,
            ]);
        }
    }

    private function generateReminderMessage($lead, $activity)
    {
        $amount = $activity->act_ptp_amount ?? $lead->balance;

        // Use Lead's custom query method to get proper account_number and institution data
        $leadData = Lead::query()->where('leads.id', $lead->id)->first();
        $institutionName = $leadData->institution_name ?? 'your institution';
        $accountNumber = $leadData->account_number ?? 'your account';
        $paybillNumber = $leadData->how_to_pay_instructions ?? 'your paybill';

        // Format amount without decimal places
        $formattedAmount = number_format($amount, 0, '.', ',');

        // Log for debugging if fields are missing
        if (!$leadData->account_number) {
            Log::warning("Missing account_number for lead ID: {$lead->id}");
        }
        if (!$leadData->how_to_pay_instructions) {
            Log::warning("Missing how_to_pay_instructions for lead ID: {$lead->id}");
        }

        return "Dear {$lead->title}, Remember to make your {$institutionName} debt payment of KES {$formattedAmount} today. Paybill {$paybillNumber}, account {$accountNumber}.";
    }
}
