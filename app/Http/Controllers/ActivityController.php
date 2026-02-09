<?php

namespace App\Http\Controllers;

use App\Jobs\SendSmsJob;
use App\Facades\RmsSms;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use App\Models\Calendar;
use App\Models\Department;
use App\Models\ActivityType;
use App\Models\LeadPriority;
use Illuminate\Http\Request;
use App\Models\ActivityStatus;
use App\Models\Institution;
use App\Models\Lead;
use App\Models\LeadStatus;
use App\Models\PaymentMethod;
use App\Models\Ptp;
use App\Models\Text;
use App\Models\TextStatus;
use App\Models\Transaction;
use App\Models\TransactionStatus;
use App\Models\TransactionType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    public function allActivity(Request $request)
    {
        if ($request->ajax()) {
            $query = Activity::query()->orderBy('created_at', 'desc');


            $user = User::find(Auth::user()->id);


            if ($user->hasRole('Agent')) {
                $query->where('activities.created_by', $user->id);
            }


            return DataTables::of($query)
                ->addIndexColumn()
                ->editColumn('due', function ($row) {
                    if (!empty($row->due)) {
                        $dueDate = \Carbon\Carbon::parse($row->due);
                        $dueTimestamp = $dueDate->timestamp;
                        return '<span id="countdown-' . $row->id . '" data-duetime="' . $dueTimestamp . '" class="badge">Calculating...</span>';
                    }
                    return '<span class="badge bg-secondary">No Due Date</span>';
                })
                ->addColumn('action', function ($row) {
                    return '<a href="/activity/' . $row->id . '/edit" class="btn btn-warning btn-xs edit-btn">
                                Edit
                            </a>
                            <form action="' . route('activity.destroy', $row->id) . '" method="POST" style="display:inline-block;">
                                ' . csrf_field() . '
                                ' . method_field("DELETE") . '
                                <button type="submit" class="btn btn-danger btn-xs delete-btn"
                                        onclick="return confirm(\'Are you sure you want to delete this activity?\')">
                                    Delete
                                </button>
                            </form>';
                })
                ->rawColumns(['due', 'action'])
                ->make(true);
        }

        return view('all-activities.index');
    }

    public function allEditActivity($id)
    {
        $editActivity = Activity::where('activities.id', $id)
            ->leftJoin('leads', 'leads.id', '=', 'activities.lead_id')
            ->leftJoin('users AS AGENT_JOIN', 'leads.assigned_agent', '=', 'AGENT_JOIN.id')
            ->leftJoin('lead_priorities', 'activities.priority_id', 'lead_priorities.id')
            ->leftJoin('activity_statuses', 'activities.status_id', 'activity_statuses.id')
            ->leftJoin('activity_types', 'activities.activity_type_id', 'activity_types.id')
            ->leftJoin('departments', 'activities.assigned_department_id', 'departments.id')
            ->leftJoin('users AS CREATED_BY_JOIN', 'activities.created_by', '=', 'CREATED_BY_JOIN.id')
            ->select(
                'activities.*',
                'leads.id as LeadID',
                'leads.assigned_agent',
                'AGENT_JOIN.name AS assigned_agent_name',
                'AGENT_JOIN.telephone AS assigned_agent_telephone',
                'AGENT_JOIN.id_number AS assigned_agent_id_number',
                'AGENT_JOIN.agent_code AS assigned_agent_code',
                'lead_priorities.lead_priority_name',
                'AGENT_JOIN.email AS assigned_agent_email',
                'activity_types.activity_type_title',
                'activity_types.icon as activity_type_icon',
                'activity_statuses.activity_status_name',
                'activity_statuses.color_code as activity_status_color_code',
                'departments.department_name',
                'CREATED_BY_JOIN.name AS created_by_name',
                'CREATED_BY_JOIN.id_number AS created_by_id_number',
                'CREATED_BY_JOIN.agent_code AS created_by_code',
                'CREATED_BY_JOIN.telephone AS created_by_telephone',
                'CREATED_BY_JOIN.email AS created_by_email',
            )
            ->firstOrFail();

        return view('all-activities.edit', with([
            'editActivity' => $editActivity,
            'departments' => Department::where('is_active', 1)->select('department_name', 'id')->get(),
            'agentsLists' => User::where('is_active', 1)
                ->select(DB::raw("CONCAT(name, ' - ', agent_code) as name"), 'id')
                ->pluck('name', 'id'),
            'priorities' => LeadPriority::where('is_active', 1)
                ->select(DB::raw("CONCAT(lead_priority_name, ' - ', description) as name"), 'id')
                ->pluck('name', 'id'),
            'activityTypes' => ActivityType::where('is_active', 1)
                ->select('id', 'activity_type_title', 'icon')
                ->get(),
            'activity_statuses' => ActivityStatus::where('is_active', 1)->select('id', 'activity_status_name')->get(),
        ]));
    }

    public function storeActivity(Request $request)
    {
        // Validate the request
        $request->validate([
            'description' => 'required|string',
            'activityType' => 'required|exists:activity_types,id',
            'leadID' => 'required|exists:leads,id',
            //'call_disposition' => 'required|exists:call_dispositions,id',
        ]);

        // Check for duplicate activities within the last 5 minutes
        // if (Activity::hasSimilarActivity($request['leadID'], $request['activityType'], Auth::user()->id)) {
        //     return redirect()->back()
        //         ->with('warning', 'A similar activity was recently created for this lead. Please wait a few minutes before creating another.')
        //         ->withInput();
        // }

        // Check for duplicate PTP if PTP is being added
        // if ($request['addPTP'] == 1 && !empty($request['ptp_payment_date'])) {
        //     $ptpDate = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date'])->format('Y-m-d');
        //     if (Activity::hasPTPForDate($request['leadID'], $ptpDate)) {
        //         return redirect()->back()
        //             ->with('warning', 'A PTP already exists for this lead on the selected date.')
        //             ->withInput();
        //     }
        // }

        // Check for duplicate payment if payment is being added
        // if (($request['activityType'] == 19 || $request['activityType'] == 16 || $request['activityType'] == 5 || $request['activityType'] == 28)
        //     && !empty($request['payment_transID'])
        // ) {
        //     if (Activity::hasPaymentWithTransactionId($request['leadID'], $request['payment_transID'])) {
        //         return redirect()->back()
        //             ->with('warning', 'A payment with this transaction ID already exists for this lead.')
        //             ->withInput();
        //     }
        // }

        // Wrap the entire activity creation in a database transaction
        try {
            return DB::transaction(function () use ($request) {
                $activity = new Activity();

                $activity->activity_type_id = $request['activityType'];
                $activity->activity_title = ActivityType::find($request['activityType'])->activity_type_title;
                $activity->description = $request['description'];
                $activity->priority_id = !empty($request['priority']) ? $request['priority'] : 1;

                $activity->lead_id = $request['leadID'];
                $activity->assigned_department_id = null;
                $activity->assigned_user_id = Auth::user()->id;
                $activity->status_id = 2; // Default to closed status
                $activity->calendar_add = $request['addToCalendar'] ?? 0;


                $activity->ptp_check = $request['addPTP'] ?? 0;
                $activity->act_ptp_amount  = $request['ptp_amount'];
                if (!empty($request['ptp_payment_date']) && \DateTime::createFromFormat('d-m-Y', $request['ptp_payment_date'])) {
                    $date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date']);
                    $activity->act_ptp_date = $date;
                    $activity->act_ptp_retire_date = $date->copy()->addDay();
                } else {
                    $activity->act_ptp_date = null;
                    $activity->act_ptp_retire_date = null;
                }

                // $activity->act_ptp_date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date']) ?? $request['ptp_payment_date'];
                // $activity->act_ptp_retire_date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date'])->addDay() ?? $request['ptp_payment_date'];

                $activity->ptp_check = $request['addPayment'] ?? 0;
                $activity->act_payment_amount  = $request['payment_amount'];
                $activity->act_payment_transid  = $request['payment_transID'];
                $activity->act_payment_method  = $request['payment_method'];

                $activity->act_call_disposition_id  = $request['call_disposition'];

                // Handle start_date_time
                if (!empty($request['start_date'])) {
                    $startTime = !empty($request['start_time']) ? $request['start_time'] : '12:00 AM';
                    $activity->start_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date'] . ' ' . $startTime);
                } else {
                    $activity->start_date_time = null;
                }

                // Handle due_date_time
                if (!empty($request['end_date'])) {
                    $endTime = !empty($request['end_time']) ? $request['end_time'] : '12:00 AM';
                    $activity->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
                } else {
                    $activity->due_date_time = null;
                }

                $activity->created_by = Auth::user()->id;
                $activity->updated_by = Auth::user()->id;

                // Only add to calendar if checkbox is checked AND we have both start and end dates
                if ($request['addToCalendar'] == 1 && !empty($request['start_date']) && !empty($request['end_date'])) {
                    try {
                        $startDateTime = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date'] . ' ' . $startTime);

                        // Check if a calendar entry already exists for this lead, user, and start time
                        $existingCalendar = Calendar::where('lead_id', $request['leadID'])
                            ->where('created_by', Auth::id())
                            ->where('start_date_time', $startDateTime)
                            ->first();

                        if (!$existingCalendar) {
                            $calendar = new Calendar();
                            // Use the proper activity title from the activity type
                            $activityType = ActivityType::find($request['activityType']);
                            $calendar->calendar_title = $activityType ? $activityType->activity_type_title : 'Activity';
                            $calendar->start_date_time = $startDateTime;
                            $calendar->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
                            $calendar->description = $request['description'];
                            $calendar->lead_id = $request['leadID'];
                            $calendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
                            $calendar->assigned_team_id = null;
                            $calendar->assigned_user_id = Auth::user()->id;
                            $calendar->created_by = Auth::id();
                            $calendar->updated_by = Auth::id();

                            $calendar->save();
                            \Log::info('Calendar entry created successfully for activity', ['calendar_id' => $calendar->id, 'activity_title' => $calendar->calendar_title]);
                        } else {
                            \Log::info('Calendar entry already exists, skipping creation', ['existing_calendar_id' => $existingCalendar->id]);
                        }
                    } catch (\Exception $e) {
                        \Log::error('Failed to create calendar entry for activity', [
                            'error' => $e->getMessage(),
                            'start_date' => $request['start_date'],
                            'end_date' => $request['end_date'],
                            'start_time' => $startTime,
                            'end_time' => $endTime
                        ]);
                    }
                }


                if ($request['activityType'] == 8) {
                    $lead = Lead::find($request['leadID']);
                    $institution = $lead->institution;

                    // Check client contract type
                    $isDirectContract = $institution && $institution->client_contract_type_id == 1;
                    $isRmsClient = $institution && $institution->client_contract_type_id == 3;

                    $text = new Text();
                    $text->text_title = "ACTIVITY: " . $request['sms_template'];
                    $text->contact_type = 'manual';
                    $text->message = $request['description'];
                    $text->contacts_count = 1;
                    $text->recepient_contacts = $lead->telephone;
                    $text->created_by = auth()->id();
                    $text->updated_by = auth()->id();

                    if ($request->has('setStartDate')) {
                        $text->scheduled = 1;
                        if ($request->start_date && $request->start_time) {
                            $passTime = Carbon::parse($request->start_date . " " . $request->start_time);
                            $text->schedule_date = $passTime->format('Y-m-d H:i:s');
                        }
                    } else {
                        $text->scheduled = 0;
                    }

                    // Set status based on contract type
                    $text->status = ($isDirectContract || $isRmsClient) ? TextStatus::SENT : TextStatus::PENDING_APPROVAL;
                    $text->save();

                    // Send SMS immediately if direct or RMS contract
                    if ($isDirectContract) {
                        SendSmsJob::dispatch($text);
                    } elseif ($isRmsClient) {
                        // Use RMS SMS gateway for RMS clients
                        RmsSms::send($lead->telephone, $request['description']);
                    }

                    $activity->ref_text_id = $text->id;
                }

                $activity->save();





                if ($request['activityType'] == 19 || $request['activityType'] == 16 || $request['activityType'] == 5 || $request['activityType'] == 28) {

                    $transTypeDetails = TransactionType::where('id', TransactionType::PAYMENT)->first();
                    $paymentMethodDetails = PaymentMethod::where('id', $request['payment_method'])->first();

                    $desc = $transTypeDetails->transaction_type_title . "/"  . $paymentMethodDetails->method_name . " -Manual- " . $request['description'];
                    $transaction = new Transaction();
                    $transaction->lead_id = $request['leadID'];
                    $transaction->transaction_type =  TransactionType::PAYMENT;
                    $transaction->amount = $request['payment_amount'] * -1;
                    $transaction->description = $desc;
                    $transaction->transaction_id = $request['payment_transID'];
                    $transaction->status_id = TransactionStatus::PAID;
                    $transaction->payment_method = $request['payment_method'];
                    $transaction->created_by = Auth::user()->id;
                    $transaction->updated_by = Auth::user()->id;
                    $transaction->save();

                    $leadDetails = Lead::where('id', $request['leadID'])->first();
                    $leadDetails->balance = $leadDetails->balance - $request['payment_amount'];

                    $leadDetails->call_disposition_id = $request['call_disposition'];

                    if ($request['addPTP'] == 1) {
                        // $leadDetails->last_ptp_amount = $request['ptp_amount'];
                        // $leadDetails->last_ptp_date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date']) ?? $request['ptp_payment_date'];;
                        // $leadDetails->last_retire_date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date'])->addDay() ?? $request['ptp_payment_date'];


                        if (!empty($request['ptp_payment_date']) && \DateTime::createFromFormat('d-m-Y', $request['ptp_payment_date'])) {
                            $date = Carbon::createFromFormat('d-m-Y', $request['ptp_payment_date']);
                            $activity->act_ptp_date = $date;
                            $activity->act_ptp_retire_date = $date->copy()->addDay();
                        } else {
                            $activity->act_ptp_date = null;
                            $activity->act_ptp_retire_date = null;
                        }
                    }


                    // $leadDetails = Lead::where('id', $request['leadID'])->first();


                    if ($leadDetails->balance <= 0) {
                        $leadDetails->status_id = LeadStatus::PAID;
                        //$leadDetails->save();
                    } else {
                        $leadDetails->status_id = LeadStatus::PARTIALLY_PAID;
                        // $leadDetails->save();
                    }




                    $leadDetails->save();
                }



                if ($request['activityType'] == 4) {
                    $ptp = new Ptp();
                    $ptp->lead_id = $request['leadID'];
                    $ptp->ptp_date = Carbon::parse($request['ptp_payment_date'])->format('Y-m-d');
                    $ptp->ptp_amount = $request['ptp_amount'];
                    $ptp->ptp_expiry_date = Carbon::parse($request['ptp_payment_date'])->addDay()->format('Y-m-d');
                    $ptp->description = $request['description'];
                    $ptp->created_by = Auth::user()->id;
                    $ptp->updated_by = Auth::user()->id;
                    $ptp->save();
                }



                if ($request['activityType'] == 23) {
                    $leadDetails = Lead::where('id', $request['leadID'])->first();
                    $leadDetails->status_id = LeadStatus::PAID;
                    $leadDetails->save();
                }

                if ($request['activityType'] == 24) {
                    $leadDetails = Lead::where('id', $request['leadID'])->first();
                    $leadDetails->status_id = 5;
                    $leadDetails->save();
                }

                if ($request['activityType'] == 26) {
                    $leadDetails = Lead::where('id', $request['leadID'])->first();
                    $leadDetails->status_id = 6;
                    $leadDetails->save();
                }

                $leadDetails = Lead::where('id', $request['leadID'])->first();
                $leadDetails->call_disposition_id = $request['call_disposition'];
                $leadDetails->updated_by = Auth::user()->id;
                $leadDetails->updated_at = Carbon::now();
                $leadDetails->save();

                // Send automatic SMS for Call Disposition ID 6 (Ringing No Response) - for direct or RMS contract institutions
                if ($request['call_disposition'] == 6) {
                    $lead = Lead::select('leads.*', 'institutions.institution_name', 'institutions.how_to_pay_instructions', 'institutions.client_contract_type_id')
                        ->join('institutions', 'leads.institution_id', '=', 'institutions.id')
                        ->where('leads.id', $request['leadID'])
                        ->first();

                    $isDirectContract = $lead && $lead->client_contract_type_id == 1;
                    $isRmsClient = $lead && $lead->client_contract_type_id == 3;

                    if ($lead && ($isDirectContract || $isRmsClient)) {
                        // Build the message using the template
                        $messageTemplate = "{name}, we have tried calling you without success. Kindly but urgently get in touch with us to discuss your debt with {institution_name} of KES {amount}. The debt ought to be settled to avoid additional penalties and other charges. Pay through {paybill_no}, account number {account_number}. Notify us on 0701967176.";

                        $message = str_replace(
                            ['{name}', '{institution_name}', '{amount}', '{paybill_no}', '{account_number}'],
                            [$lead->title, $lead->institution_name, number_format($lead->balance, 2), $lead->how_to_pay_instructions, $lead->account_number],
                            $messageTemplate
                        );

                        // Create Text record
                        $text = new Text();
                        $text->text_title = "AUTOMATIC: Ringing No Response";
                        $text->contact_type = 'manual';
                        $text->message = $message;
                        $text->contacts_count = 1;
                        $text->recepient_contacts = $lead->telephone;
                        $text->created_by = Auth::id();
                        $text->updated_by = Auth::id();
                        $text->scheduled = 0;
                        $text->status = TextStatus::SENT;
                        $text->save();

                        // Link the text to the activity
                        $activity->ref_text_id = $text->id;
                        $activity->save();

                        // Create Activity record with type 8 (SMS Activity) to record the automatic SMS
                        $smsActivity = new Activity();
                        $smsActivity->activity_type_id = 8; // SMS Activity type
                        $smsActivity->activity_title = "Ringing No Response";
                        $smsActivity->description = $message;
                        $smsActivity->priority_id = 1;
                        $smsActivity->lead_id = $request['leadID'];
                        $smsActivity->assigned_department_id = null;
                        $smsActivity->assigned_user_id = Auth::user()->id;
                        $smsActivity->status_id = 2; // Closed status
                        $smsActivity->calendar_add = 0;
                        $smsActivity->ptp_check = 0;
                        $smsActivity->act_call_disposition_id = 6; // Ringing No Response
                        $smsActivity->ref_text_id = $text->id;
                        $smsActivity->created_by = Auth::user()->id;
                        $smsActivity->updated_by = Auth::user()->id;
                        $smsActivity->save();

                        // Send SMS via appropriate gateway
                        if ($isDirectContract) {
                            SendSmsJob::dispatch($text);
                        } elseif ($isRmsClient) {
                            // Use RMS SMS gateway for RMS clients
                            RmsSms::send($lead->telephone, $message);
                        }
                    }
                }

                return redirect('/lead/' . $request['leadID'] . '?section=activities')->with('success', 'Activity Saved Successfully');
            });
        } catch (\Exception $e) {
            \Log::error('Error storing activity', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'leadID' => $request['leadID'],
                'activityType' => $request['activityType'],
            ]);
            return redirect()->back()
                ->with('error', 'Error saving activity: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editActivity(Request $request, $id)
    {

        $activity = Activity::findOrFail($id);
        // Update fields
        $activity->activity_title = $request['activity_title'];
        $activity->description = $request['description'];
        $activity->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
        $activity->activity_type_id = $request['activityType'];
        $activity->lead_id = $request['leadID'];
        // Keep existing assignments, don't change them in edit
        // $activity->calendar_add = $request['addToCalendar'];
        if ($request->has('addToCalendar')) {
            $activity->calendar_add = 1; // Convert 'on' to 1
        } else {
            $activity->calendar_add = 0; // Checkbox not checked
        }

        // Handle start_date_time
        if (!empty($request['start_date'])) {
            $startTime = !empty($request['start_time']) ? $request['start_time'] : '12:00 AM';
            $activity->start_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date'] . ' ' . $startTime);
        } else {
            $activity->start_date_time = null;
        }

        // Handle due_date_time
        if (!empty($request['end_date'])) {
            $endTime = !empty($request['end_time']) ? $request['end_time'] : '12:00 AM';
            $activity->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
        } else {
            $activity->due_date_time = null;
        }
        $activity->updated_by = Auth::user()->id;
        $activity->save();

        // Optional: Update Calendar entry if needed (if already exists or if required to be added on update)
        if ($request['addToCalendar'] == 1 && !empty($request['start_date']) && !empty($request['end_date'])) {
            try {
                $startDateTime = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date'] . ' ' . $startTime);

                // Check if a calendar entry already exists for this lead, user, and start time
                $existingCalendar = Calendar::where('lead_id', $request['leadID'])
                    ->where('created_by', Auth::id())
                    ->where('start_date_time', $startDateTime)
                    ->first();

                if ($existingCalendar) {
                    // Update existing calendar entry
                    $existingCalendar->calendar_title = $request['activity_title'] ?: $activity->activity_title;
                    $existingCalendar->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
                    $existingCalendar->description = $request['description'];
                    $existingCalendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
                    $existingCalendar->updated_by = Auth::id();
                    $existingCalendar->save();
                    \Log::info('Calendar entry updated successfully for activity', ['calendar_id' => $existingCalendar->id, 'activity_title' => $existingCalendar->calendar_title]);
                } else {
                    // Create new calendar entry
                    $calendar = new Calendar();
                    $calendar->calendar_title = $request['activity_title'] ?: $activity->activity_title;
                    $calendar->start_date_time = $startDateTime;
                    $calendar->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
                    $calendar->description = $request['description'];
                    $calendar->lead_id = $request['leadID'];
                    $calendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
                    $calendar->assigned_team_id = null;
                    $calendar->assigned_user_id = Auth::user()->id;
                    $calendar->created_by = Auth::id();
                    $calendar->updated_by = Auth::id();
                    $calendar->save();
                    \Log::info('Calendar entry created successfully for activity', ['calendar_id' => $calendar->id, 'activity_title' => $calendar->calendar_title]);
                }
            } catch (\Exception $e) {
                \Log::error('Failed to create/update calendar entry for activity', [
                    'error' => $e->getMessage(),
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
            }
        }

        return redirect('/lead/' . $request['leadID'] . '?section=activities')->with('success', 'Activity Updated Successfully');
    }

    public function updateAllActivity(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->activity_title = $request['activity_title'];
        $activity->description = $request['description'];
        $activity->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
        $activity->activity_type_id = $request['activityType'];
        // Keep existing assignments, don't change them in edit
        // $activity->calendar_add = $request['addToCalendar'];
        if ($request->has('addToCalendar')) {
            $activity->calendar_add = 1; // Convert 'on' to 1
        } else {
            $activity->calendar_add = 0; // Checkbox not checked
        }

        // Handle start_date_time
        if (!empty($request['start_date'])) {
            $startTime = !empty($request['start_time']) ? $request['start_time'] : '12:00 AM';

            // dd($request['start_date'], $startTime, $request['start_date'] . ' ' . $startTime );

            $activity->start_date_time = Carbon::createFromFormat('Y-m-d h:i A', trim($request['start_date'] . ' ' . $startTime));
        } else {
            $activity->start_date_time = null;
        }


        // Handle due_date_time
        if (!empty($request['end_date'])) {
            $endTime = !empty($request['end_time']) ? $request['end_time'] : '12:00 AM';
            $activity->due_date_time = Carbon::createFromFormat('Y-m-d h:i A', trim($request['end_date'] . ' ' . $endTime));
        } else {
            $activity->due_date_time = null;
        }

        $activity->updated_by = Auth::user()->id;
        $activity->save();

        // Optional: Update Calendar entry if needed (if already exists or if required to be added on update)
        if ($request['addToCalendar'] == 1 && !empty($request['start_date']) && !empty($request['end_date'])) {
            try {
                $calendar = new Calendar();
                // Use the activity title from the request or the existing activity
                $calendar->calendar_title = $request['activity_title'] ?: $activity->activity_title;
                $calendar->start_date_time = Carbon::createFromFormat('Y-m-d h:i A', $request['start_date'] . ' ' . $startTime);
                $calendar->due_date_time = Carbon::createFromFormat('Y-m-d h:i A', $request['end_date'] . ' ' . $endTime);
                $calendar->description = $request['description'];
                $calendar->lead_id = $request['leadID'];
                $calendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
                $calendar->assigned_team_id = null;
                $calendar->assigned_user_id = Auth::user()->id;
                $calendar->created_by = Auth::id();
                $calendar->updated_by = Auth::id();
                $calendar->save();
                \Log::info('Calendar entry created/updated successfully for all-activity', ['calendar_id' => $calendar->id, 'activity_title' => $calendar->calendar_title]);
            } catch (\Exception $e) {
                \Log::error('Failed to create/update calendar entry for all-activity', [
                    'error' => $e->getMessage(),
                    'start_date' => $request['start_date'],
                    'end_date' => $request['end_date'],
                    'start_time' => $startTime,
                    'end_time' => $endTime
                ]);
            }
        }

        return redirect('/activity')->with('success', 'Activity Updated Successfully');
    }

    public function destroy($id)
    {
        $activity = Activity::find($id);

        if (!$activity) {
            return redirect()->back()->with('error', 'Activity not found.');
        }

        $activity->delete();

        return redirect()->back()->with('success', 'Activity deleted successfully.');
    }
}
