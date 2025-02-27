<?php

namespace App\Jobs;

use App\Models\ContactList;
use App\Models\Text;
use App\Models\TextStatus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
            $placeholder = '{' . trim($key) . '}'; // Ensure it matches {title}, {currency}, etc.
            $message = str_replace($placeholder, $value, $message);
        }
        return $message;
    }


    private function sendSmsToContacts(array $contacts)
    {
        foreach ($contacts as $contact) {
            Log::info("Sending SMS to: {$contact['phone']} - Message: {$contact['message']}");
            // Implement actual SMS sending API here.
        }

        $this->text->status = TextStatus::SENT;
        $this->text->save();
    }
}
