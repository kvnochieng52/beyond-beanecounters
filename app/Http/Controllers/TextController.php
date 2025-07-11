<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Models\BelioSms; //here
use App\Models\BSms;
use App\Models\Contact;
use App\Models\ContactList;
use App\Models\Queue;
use App\Models\Text;
use App\Models\TextStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;

class TextController extends Controller
{
    /**
     * Display a listing of the resource.
     */


    public function index(Request $request)
    {

        if ($request->ajax()) {
            $query = Text::select(
                'texts.*',
                'text_statuses.text_status_name',
                'text_statuses.color_code',
                'users.name as created_by_name'
            )
                ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id')
                ->leftJoin('users', 'texts.created_by', '=', 'users.id')
                ->orderBy('texts.id', 'DESC');

            return DataTables::of($query)
                ->addIndexColumn()
                ->addColumn('status', function ($row) {
                    return '<span class="badge badge-' . $row->color_code . '">' . $row->text_status_name . '</span>';
                })
                ->filterColumn('text_statuses.text_status_name', function ($query, $keyword) {
                    $query->where('text_statuses.text_status_name', 'like', "%{$keyword}%");
                })
                ->rawColumns(['status'])
                ->make(true);
        }

        return view('text.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {


        // try {
        //     $sms = new BSms();
        //     $response = $sms->send(['254713295853'], 'Hello from our app!');

        //     return response()->json([
        //         'success' => true,
        //         'response' => $response
        //     ]);
        // } catch (\Exception $e) {
        //     return response()->json([
        //         'success' => false,
        //         'message' => $e->getMessage()
        //     ], 500);
        // }


        // exit;
        return view('text.create')->with([
            'contactLists' => Contact::where('is_active', 1)->pluck('title', 'id')
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {


        $text = new Text();
        $text->text_title = $request->title;
        $text->contact_type = $request->contact_source;
        $text->message = $request->message;
        $text->contacts_count = $request->sms_contacts_count;
        $text->created_by = auth()->id(); // Assuming authentication is used
        $text->updated_by = auth()->id();

        // Handle contact sources
        if ($request->contact_source === 'manual') {
            $text->recepient_contacts = $request->contacts;
        } elseif ($request->contact_source === 'csv') {
            $text->csv_file_name = $request->csv_file_name;
            $text->csv_file_path = $request->csv_file_path;
            $text->csv_file_columns = $request->csv_file_columns;
        } elseif ($request->contact_source === 'saved') {
            $text->contact_list = json_encode($request->contact_list);
        }

        // Handle scheduling
        if ($request->has('schedule')) {
            $text->scheduled = 1;
            if ($request->schedule_date && $request->schedule_time) {
                $datetime = \DateTime::createFromFormat('d/m/Y h:i A', $request->schedule_date . ' ' . $request->schedule_time);
                $text->schedule_date = $datetime ? $datetime->format('Y-m-d H:i:s') : null;
            }
        } else {
            $text->scheduled = 0;
        }

        $text->status = TextStatus::PENDING; // Default status, can be updated later
        $text->save();


        SendSmsJob::dispatch($text);

        return redirect()->route('text.index')->with('success', 'SMS Campaign saved successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Text $text)
    {

        $sentMessagesCount = Queue::where('text_id', $text->id)->where('status', TextStatus::SENT)->count();
        $undeliveredMessagesCount = Queue::where('text_id', $text->id)->where('status', TextStatus::FAILED)->count();
        $blackListedMessagesCount = 0;
        $queuedMessagesCount = $text->contacts_count - ($sentMessagesCount + $undeliveredMessagesCount);

        $totalContacts = $text->contacts_count;
        $percentage = ceil(($sentMessagesCount + $undeliveredMessagesCount + $blackListedMessagesCount) / $totalContacts * 100);


        return view('text.show')->with([
            'text' => Text::getTextByID($text->id),
            'sentMessagesCount' => $sentMessagesCount,
            'undeliveredMessagesCount' => $undeliveredMessagesCount,
            'blackListedMessagesCount' => $blackListedMessagesCount,
            'queuedMessagesCount' => $queuedMessagesCount,
            'percentage' => $percentage
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {

        $text = Text::findOrFail($id);

        // dd($text);

        return view('text.edit', [
            'text' => $text,
            'contactLists' => Contact::where('is_active', 1)->pluck('title', 'id')
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $text = Text::findOrFail($id);

        // Handle contact sources
        if ($request->contact_source_edit === 'manual') {
            $text->recepient_contacts = $request->contacts;
        } elseif ($request->contact_source_edit === 'csv') {
            $text->csv_file_name = $request->csv_file_name;
            $text->csv_file_path = $request->csv_file_path;
            $text->csv_file_columns = $request->csv_file_columns;
        } elseif ($request->contact_source_edit === 'saved') {
            $text->contact_list = json_encode($request->contact_list);
        }


        $text->text_title = $request->title;
        $text->contact_type = $request->contact_source_edit;
        $text->message = $request->message;
        $text->contacts_count = $request->sms_contacts_count;
        $text->updated_by = auth()->id();


        // Handle scheduling
        if ($request->has('schedule')) {
            $text->scheduled = 1;
            if ($request->schedule_date && $request->schedule_time) {
                $datetime = \DateTime::createFromFormat('d/m/Y h:i A', $request->schedule_date . ' ' . $request->schedule_time);
                $text->schedule_date = $datetime ? $datetime->format('Y-m-d H:i:s') : null;
            }
        } else {
            $text->scheduled = 0;
        }

        $text->status = TextStatus::PENDING; // Keep status unchanged unless required
        $text->save();

        return redirect()->route('text.index')->with('success', 'SMS Campaign updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Text $text)
    {
        //
    }


    public function uploadCsv(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|mimes:csv,txt|max:2048', // Max 2MB
        ]);

        $file = $request->file('csv_file');
        $originalName = $file->getClientOriginalName();
        $fileName = time() . '_' . Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.csv';

        // Store CSV in public/csv_file_uploads
        $destinationPath = public_path('csv_file_uploads');
        if (!file_exists($destinationPath)) {
            mkdir($destinationPath, 0755, true); // Create the directory if it doesn't exist
        }

        $file->move($destinationPath, $fileName);

        $relativePath = 'csv_file_uploads/' . $fileName; // Relative path without base URL
        $csvPath = public_path($relativePath);

        if (!file_exists($csvPath)) {
            return response()->json(['error' => 'CSV file not found.'], 404);
        }

        $handle = fopen($csvPath, 'r');
        if (!$handle) {
            return response()->json(['error' => 'Unable to open CSV file.'], 500);
        }

        $columns = fgetcsv($handle); // Get header row
        fclose($handle);

        if (!$columns) {
            return response()->json(['error' => 'CSV file is empty or invalid.'], 400);
        }

        $columns = array_map('trim', $columns); // Clean column names

        $validPhoneColumns = [
            'contact',
            'contacts',
            'telephone',
            'mobile',
            'phone number',
            'phone',
            'mobile number'
        ];

        // Convert columns to lowercase for case-insensitive check
        $columnsLower = array_map('strtolower', $columns);

        $hasPhoneColumn = collect($columnsLower)->contains(fn($column) => in_array($column, $validPhoneColumns));

        if (!$hasPhoneColumn) {
            return response()->json([
                'error' => 'CSV has no phone number column. Please ensure your contacts column is named one of: ' . implode(', ', $validPhoneColumns)
            ], 422);
        }

        return response()->json([
            'message' => 'CSV uploaded successfully.',
            'original_name' => $originalName,
            'path' => $relativePath, // Return relative path only
            'columns' => $columns
        ]);
    }




    public function previewSms(Request $request)
    {
        $validContacts = 0;
        $invalidContacts = 0;
        $totalContacts = 0;

        $message = $request['message'];
        $messageTotalChars = mb_strlen($message);
        $recipientMethod = $request['recipientMethod'];

        $validPhoneColumns = [
            'contact',
            'contacts',
            'telephone',
            'mobile',
            'phone number',
            'phone',
            'mobile number'
        ];

        $contactList = [];

        if ($recipientMethod === 'manual') {
            $contactList = array_filter(array_map('trim', explode(',', $request['contacts'])));
            foreach ($contactList as $contact) {
                Text::isValidPhoneNumber($contact) ? $validContacts++ : $invalidContacts++;
            }
        }

        if ($recipientMethod === 'csv') {
            $csvPath = public_path(ltrim($request['csvFilePath'], '/'));

            if (!file_exists($csvPath)) {
                return response()->json(['message' => 'CSV file not found.'], 404);
            }

            if (($handle = fopen($csvPath, 'r')) !== false) {
                $headers = fgetcsv($handle);

                if (!$headers) {
                    return response()->json(['message' => 'Invalid CSV file. No headers found.'], 400);
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
                    return response()->json(['message' => 'No valid contact column found in the CSV.'], 400);
                }

                $personalizedMessage = $message;
                $firstRowProcessed = false;

                while (($row = fgetcsv($handle)) !== false) {
                    $contact = trim($row[$phoneColumnIndex] ?? '');

                    if ($contact) {
                        Text::isValidPhoneNumber($contact) ? $validContacts++ : $invalidContacts++;
                        $contactList[] = $contact;
                    }

                    if (!$firstRowProcessed && preg_match_all('/\{(\w+)\}/', $message, $matches)) {
                        foreach ($matches[1] as $placeholder) {
                            $columnIndex = array_search(strtolower($placeholder), $headerMap);
                            $replacement = $columnIndex !== false ? ($row[$columnIndex] ?? '') : '';
                            $personalizedMessage = str_replace('{' . $placeholder . '}', $replacement, $personalizedMessage);
                        }
                        $firstRowProcessed = true;
                    }
                }

                fclose($handle);

                $totalContacts = count($contactList);
                $message = $personalizedMessage;
            } else {
                return response()->json(['message' => 'Unable to open CSV file.'], 500);
            }
        }



        if ($recipientMethod === 'saved') {
            $contactBatch = $request['contactList'];

            foreach ($contactBatch as $batch) {
                $contactList = DB::table('contact_lists')->where('contact_id', $batch)->get();
                foreach ($contactList as $list) {
                    Text::isValidPhoneNumber($list->telephone) ? $validContacts++ : $invalidContacts++;
                    $totalContacts++;
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'validContacts' => $validContacts,
            'invalidContacts' => $invalidContacts,
            'totalContacts' => $totalContacts,
            'messageTotalChars' => $messageTotalChars,
            'personalizedMessage' => $message,
        ]);
    }

    public function previewSmsEdit(Request $request)
    {
        $validContacts = 0;
        $invalidContacts = 0;
        $totalContacts = 0;

        $message = $request['message'];
        $messageTotalChars = mb_strlen($message);
        $recipientMethod = $request['recipientMethod'];

        $validPhoneColumns = [
            'contact',
            'contacts',
            'telephone',
            'mobile',
            'phone number',
            'phone',
            'mobile number'
        ];

        $contactList = [];

        if ($recipientMethod === 'manual') {
            $contactList = array_filter(array_map('trim', explode(',', $request['contacts'])));
            foreach ($contactList as $contact) {
                Text::isValidPhoneNumber($contact) ? $validContacts++ : $invalidContacts++;
            }
        }

        if ($recipientMethod === 'csv') {
            $csvPath = public_path(ltrim($request['csvFilePath'], '/'));

            if (!file_exists($csvPath)) {
                return response()->json(['message' => 'CSV file not found.'], 404);
            }

            if (($handle = fopen($csvPath, 'r')) !== false) {
                $headers = fgetcsv($handle);

                if (!$headers) {
                    return response()->json(['message' => 'Invalid CSV file. No headers found.'], 400);
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
                    return response()->json(['message' => 'No valid contact column found in the CSV.'], 400);
                }

                $personalizedMessage = $message;
                $firstRowProcessed = false;

                while (($row = fgetcsv($handle)) !== false) {
                    $contact = trim($row[$phoneColumnIndex] ?? '');

                    if ($contact) {
                        Text::isValidPhoneNumber($contact) ? $validContacts++ : $invalidContacts++;
                        $contactList[] = $contact;
                    }

                    if (!$firstRowProcessed && preg_match_all('/\{(\w+)\}/', $message, $matches)) {
                        foreach ($matches[1] as $placeholder) {
                            $columnIndex = array_search(strtolower($placeholder), $headerMap);
                            $replacement = $columnIndex !== false ? ($row[$columnIndex] ?? '') : '';
                            $personalizedMessage = str_replace('{' . $placeholder . '}', $replacement, $personalizedMessage);
                        }
                        $firstRowProcessed = true;
                    }
                }

                fclose($handle);

                $totalContacts = count($contactList);
                $message = $personalizedMessage;
            } else {
                return response()->json(['message' => 'Unable to open CSV file.'], 500);
            }
        }



        if ($recipientMethod === 'saved') {
            $contactBatch = $request['contactList'];

            foreach ($contactBatch as $batch) {
                $contactList = DB::table('contact_lists')->where('contact_id', $batch)->get();
                foreach ($contactList as $list) {
                    Text::isValidPhoneNumber($list->telephone) ? $validContacts++ : $invalidContacts++;
                    $totalContacts++;
                }
            }
        }

        return response()->json([
            'message' => 'Success',
            'validContacts' => $validContacts,
            'invalidContacts' => $invalidContacts,
            'totalContacts' => $totalContacts,
            'messageTotalChars' => $messageTotalChars,
            'personalizedMessage' => $message,
        ]);
    }

    public function cancel($id)
    {
        $text = Text::find($id);

        if (!$text) {
            return response()->json(['success' => false, 'message' => 'SMS not found'], 404);
        }

        // Update status to "Cancelled"
        $text->update(['status' => TextStatus::CANCELLED]);

        return response()->json(['success' => true, 'message' => 'SMS canceled successfully']);
    }
}
