<?php

namespace App\Jobs;

use App\Models\ContactList;
use App\Models\Queue;
use App\Models\Text;
use App\Models\TextStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use AfricasTalking\SDK\AfricasTalking;
use App\Models\BSms;
use App\Models\Lead;
use Exception;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $text;

    public function __construct(Text $text)
    {
        $this->text = $text;
    }

    public function handle()
    {
        Log::info("Processing SMS Campaign ID: {$this->text->id} - Contact Type: {$this->text->contact_type}");

        $contacts = match ($this->text->contact_type) {
            'manual' => $this->processManualContacts(),
            'saved' => $this->processSavedContacts(),
            'csv' => $this->processCsvContacts(),
            default => [],
        };

        $this->sendSmsToContacts($contacts);
    }

    private function processManualContacts()
    {
        return array_map(fn($contact) => ['phone' => trim($contact), 'message' => $this->text->message], explode(',', $this->text->recepient_contacts));
    }

    private function processSavedContacts()
    {
        $contactIds = json_decode($this->text->contact_list, true) ?? [];
        Log::info("Processing Saved Contacts: " . json_encode($contactIds));

        $phones = ContactList::whereIn('contact_id', $contactIds)->pluck('telephone')->toArray();
        return array_map(fn($phone) => ['phone' => $phone, 'message' => $this->text->message], $phones);
    }


    private function processTemplateCsvContacts()
    {
        // Initialize contacts array at the start
        $contacts = [];

        try {
            $csvPath = public_path(ltrim($this->text->csv_file_path, '/'));
            if (!file_exists($csvPath)) {
                Log::error("CSV file not found: {$csvPath}");
                return $contacts; // Return empty array instead of []
            }

            $handle = fopen($csvPath, 'r');
            if ($handle === false) {
                Log::error("Could not open CSV file: {$csvPath}");
                return $contacts;
            }

            $headers = fgetcsv($handle);
            if (!$headers) {
                Log::error("Invalid CSV file. No headers found.");
                fclose($handle);
                return $contacts;
            }

            $headerMap = array_map(fn($header) => strtolower(trim($header)), $headers);

            // Find the "Ticket No" column (case insensitive)
            $ticketNoColumnIndex = null;
            foreach ($headerMap as $index => $header) {
                if ($header === 'ticket no') {
                    $ticketNoColumnIndex = $index;
                    break;
                }
            }

            if ($ticketNoColumnIndex === null) {
                Log::error("CSV file does not contain 'Ticket No' column.");
                fclose($handle);
                return $contacts;
            }

            $processedCount = 0;
            $errorCount = 0;

            while (($row = fgetcsv($handle)) !== false) {
                try {
                    $ticketNo = trim($row[$ticketNoColumnIndex] ?? '');

                    if (empty($ticketNo)) {
                        continue; // Skip empty ticket numbers
                    }

                    // Get lead details using ticket number
                    $leadDetails = Lead::getLeadByID($ticketNo);

                    if (!$leadDetails) {
                        Log::warning("Lead not found for ticket number: {$ticketNo}");
                        $errorCount++;
                        continue;
                    }

                    // Get template from texts table
                    $template = $this->text->template ?? '';

                    if (empty($template)) {
                        Log::warning("No template specified for ticket number: {$ticketNo}");
                        $errorCount++;
                        continue;
                    }

                    // Generate message based on template
                    $sms_message = $this->generateTemplateMessage($template, $leadDetails);

                    if (empty($sms_message)) {
                        Log::warning("Could not generate message for ticket number: {$ticketNo} with template: {$template}");
                        $errorCount++;
                        continue;
                    }

                    // Use telephone from lead details for SMS contact
                    $phone = trim($leadDetails->telephone ?? '');

                    if (empty($phone)) {
                        Log::warning("No phone number found for ticket number: {$ticketNo}");
                        $errorCount++;
                        continue;
                    }

                    $contacts[] = [
                        'phone' => $phone,
                        'message' => $sms_message,
                        'ticket_no' => $ticketNo // Add ticket number for reference
                    ];
                    $processedCount++;
                } catch (Exception $e) {
                    Log::error("Error processing row for ticket {$ticketNo}: " . $e->getMessage());
                    $errorCount++;
                    continue;
                }
            }

            fclose($handle);

            Log::info("CSV processing completed. Processed: {$processedCount}, Errors: {$errorCount}, Total contacts: " . count($contacts));
        } catch (Exception $e) {
            Log::error("Fatal error in processTemplateCsvContacts: " . $e->getMessage());
            return $contacts; // Return empty array even on fatal error
        }

        return $contacts;
    }



    private function generateTemplateMessage($template, $leadDetails)
    {
        $sms_message = '';

        switch ($template) {
            case 'introduction':
                $sms_message = "Dear {$leadDetails->title}, Your debt for {$leadDetails->institution_name}, of {$leadDetails->currency_name} {$leadDetails->balance} has been forwarded to Beyond BeanCounters for recovery. Urgently pay via {$leadDetails->how_to_pay_instructions}, account: {$leadDetails->account_number}, or reach out to us to discuss a repayment plan, 0701967176.";
                break;

            case 'no_anwser':
                $sms_message = "{$leadDetails->title}, we have tried calling you without success. Kindly but urgently get in touch with us to discuss your debt with {$leadDetails->institution_name} of {$leadDetails->currency_name} {$leadDetails->balance}. The debt ought to be settled to avoid additional penalties and other charges. Pay through {$leadDetails->how_to_pay_instructions}, account number {$leadDetails->account_number}. Notify us on 0701967176.";
                break;

            case 'ptp_reminder':
                $sms_message = "Dear {$leadDetails->title}, remember to make payment for Your debt for {$leadDetails->institution_name}, of {$leadDetails->currency_name} {$leadDetails->balance} today. {$leadDetails->how_to_pay_instructions}, account: {$leadDetails->account_number}. Notify us on 0701967176";
                break;

            case 'refusal_to_pay':
                $sms_message = "{$leadDetails->title}, Despite previous reminders, your {$leadDetails->institution_name} debt for {$leadDetails->currency_name} {$leadDetails->balance}, remains uncleared. Be strongly advised that failure to do so will force us to recover the debt at your cost, using our Field Collectors. Pay through {$leadDetails->how_to_pay_instructions}, account {$leadDetails->account_number}. Notify us on 0701967176.";
                break;

            case 'broken_ptp_follow_up':
                $sms_message = "Greetings, we have not yet received your {$leadDetails->institution_name} payment. Urgently pay. {$leadDetails->how_to_pay_instructions}, Acc: {$leadDetails->account_number}. Notify us on 0701967176";
                break;

            default:
                Log::warning("Unknown template: {$template}");
                break;
        }

        return $sms_message;
    }


    private function processCsvContacts()
    {

        $checkTemplate = $this->text->template;
        if (!$checkTemplate) {
            return $this->processBulkCsvContacts();
        } else {
            return $this->processTemplateCsvContacts();
        }
    }

    private function processBulkCsvContacts()
    {



        $csvPath = public_path(ltrim($this->text->csv_file_path, '/'));
        if (!file_exists($csvPath)) {
            Log::error("CSV file not found: {$csvPath}");
            return [];
        }

        $validPhoneColumns = [
            'contact',
            'contacts',
            'telephone',
            'mobile',
            'phone number',
            'phone',
            'mobile number'
        ];

        $contacts = [];
        if (($handle = fopen($csvPath, 'r')) !== false) {
            $headers = fgetcsv($handle);
            if (!$headers) {
                Log::error("Invalid CSV file. No headers found.");
                return [];
            }

            $headerMap = array_map(fn($header) => strtolower(trim($header)), $headers);
            $phoneColumnIndex = null;

            foreach ($headerMap as $index => $header) {
                if (in_array($header, $validPhoneColumns)) {
                    $phoneColumnIndex = $index;
                    break;
                }
            }

            if ($phoneColumnIndex === null) {
                Log::error("No valid contact column found in the CSV.");
                return [];
            }

            while (($row = fgetcsv($handle)) !== false) {
                $contactData = array_combine($headers, $row);
                $phone = trim($row[$phoneColumnIndex] ?? '');
                if (!empty($phone)) {
                    $message = $this->replacePlaceholders($this->text->message, $contactData);
                    $contacts[] = ['phone' => $phone, 'message' => $message];
                }
            }
            fclose($handle);
        }
        return $contacts;
    }

    private function replacePlaceholders($message, $contactData)
    {
        foreach ($contactData as $key => $value) {
            $placeholder = '{' . trim($key) . '}';
            $message = str_replace($placeholder, $value, $message);
        }
        return $message;
    }

    private function sendSmsToContacts(array $contacts)
    {


        try {
            $sms = new BSms();
            $overallSuccess = true;
            $failedContacts = [];
            $lastSuccessfulResponse = null;

            foreach ($contacts as $contact) {
                try {
                    $response = $sms->send([$contact['phone']], $contact['message']);

                    // Determine if the message was successfully sent based on response
                    $apiSuccess = isset($response['desc']) && $response['desc'] === 'OK';
                    $status = $apiSuccess ? TextStatus::SENT : TextStatus::FAILED;

                    Log::info("SMS Response for {$contact['phone']}: " . json_encode($response));

                    if ($apiSuccess) {
                        $lastSuccessfulResponse = $response;
                    } else {
                        $overallSuccess = false;
                        $failedContacts[] = $contact['phone'];
                        $errorMessage = $response['desc'] ?? 'Message sending failed';
                        Log::error("API reported failure for {$contact['phone']}: {$errorMessage}");
                    }

                    // Store response in queue
                    Queue::create([
                        'text_id' => $this->text->id,
                        'message' => $contact['message'],
                        'phone' => $contact['phone'],
                        'status' => $status,
                        'api_response' => json_encode([
                            'success' => $apiSuccess,
                            'response' => $response
                        ]),
                        'created_by' => $this->text->created_by,
                        'updated_by' => $this->text->updated_by,
                    ]);
                } catch (\Exception $e) {
                    Log::error("Failed to send SMS to {$contact['phone']}: " . $e->getMessage());
                    $overallSuccess = false;
                    $failedContacts[] = $contact['phone'];

                    // Store failed response in queue
                    Queue::create([
                        'text_id' => $this->text->id,
                        'message' => $contact['message'],
                        'phone' => $contact['phone'],
                        'status' => TextStatus::FAILED,
                        'api_response' => json_encode([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]),
                        'created_by' => $this->text->created_by,
                        'updated_by' => $this->text->updated_by,
                    ]);
                }
            }

            // Update text status based on overall success
            $this->text->status = $overallSuccess ? TextStatus::SENT : TextStatus::SENT;
            $this->text->save();

            $responseData = [
                'success' => $overallSuccess,
                'message' => $overallSuccess ? 'All messages sent successfully' : count($failedContacts) . ' messages failed to send',
                'failed_contacts' => $failedContacts,
                'sent_count' => count($contacts) - count($failedContacts),
                'failed_count' => count($failedContacts)
            ];

            if ($overallSuccess && $lastSuccessfulResponse) {
                $responseData['response'] = $lastSuccessfulResponse;
            }

            return response()->json($responseData);
        } catch (\Exception $e) {
            Log::error("SMS sending process failed: " . $e->getMessage());
            $this->text->status = TextStatus::SENT;
            $this->text->save();

            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
