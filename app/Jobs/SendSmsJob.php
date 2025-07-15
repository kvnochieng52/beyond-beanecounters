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

    private function processCsvContacts()
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


        //     try {
        //         $sms = new BSms();
        //         $response = $sms->send(['254713295853'], 'Hello from our app!');

        //         return response()->json([
        //             'success' => true,
        //             'response' => $response
        //         ]);
        //     } catch (\Exception $e) {
        //         return response()->json([
        //             'success' => false,
        //             'message' => $e->getMessage()
        //         ], 500);
        //     }



        //     $username = 'sandbox';
        //     $apiKey = 'atsk_4a781f01f2993900998a885155ef6f6eae81b012b43da9e715473f59f1025a7473ad18d0'; // Replace with actual sandbox API key
        //     $AT = new AfricasTalking($username, $apiKey);
        //     $sms = $AT->sms();

        //     foreach ($contacts as $contact) {
        //         try {
        //             $response = $sms->send([
        //                 'to' => $contact['phone'],
        //                 'message' => $contact['message'],
        //                 'from' => 'BEYOND_SMS', // Replace with sender ID if applicable
        //             ]);

        //             Log::info("SMS Response: " . json_encode($response));

        //             Queue::create([
        //                 'text_id' => $this->text->id,
        //                 'message' => $contact['message'],
        //                 'status' => TextStatus::SENT,
        //                 'created_by' => $this->text->created_by,
        //                 'updated_by' => $this->text->updated_by,
        //             ]);
        //         } catch (\Exception $e) {
        //             Log::error("Failed to send SMS to {$contact['phone']}: " . $e->getMessage());
        //         }
        //     }

        //     $this->text->status = TextStatus::SENT;
        //     $this->text->save();

    }
}
