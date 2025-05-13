<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Activity;
use App\Models\Calendar;
use App\Models\Department;
use App\Models\ActivityType;
use App\Models\LeadPriority;
use Illuminate\Http\Request;
use App\Models\ActivityStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class ActivityController extends Controller
{
    public function allActivity(Request $request)
    {
        if ($request->ajax()) {
            $query = Activity::query()->orderBy('created_at', 'desc');


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
        ->select('activities.*', 'leads.id as LeadID','leads.assigned_agent',
        'AGENT_JOIN.name AS assigned_agent_name','AGENT_JOIN.telephone AS assigned_agent_telephone',
        'AGENT_JOIN.id_number AS assigned_agent_id_number','AGENT_JOIN.agent_code AS assigned_agent_code',
        'lead_priorities.lead_priority_name','AGENT_JOIN.email AS assigned_agent_email',
        'activity_types.activity_type_title','activity_types.icon as activity_type_icon',
        'activity_statuses.activity_status_name','activity_statuses.color_code as activity_status_color_code',
        'departments.department_name','CREATED_BY_JOIN.name AS created_by_name',
        'CREATED_BY_JOIN.id_number AS created_by_id_number',
        'CREATED_BY_JOIN.agent_code AS created_by_code',
        'CREATED_BY_JOIN.telephone AS created_by_telephone',
        'CREATED_BY_JOIN.email AS created_by_email',)
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
            'activity_statuses' => ActivityStatus::where('is_active', 1)->select('id','activity_status_name')->get(),
         ]));
    }

    public function storeActivity(Request $request)
    {
        $activity = new Activity();

        $activity->activity_title = $request['activity_title'];
        $activity->description = $request['description'];
        $activity->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
        $activity->activity_type_id = $request['activityType'];
        $activity->lead_id = $request['leadID'];
        $activity->assigned_department_id = $request['department'];
        $activity->assigned_user_id = !empty($request['agent']) ? $request['agent'] : Auth::user()->id;
        $activity->status_id = $request['status'];
        $activity->calendar_add = $request['addToCalendar'];

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
        $activity->save();


        if ($request['addToCalendar'] == 1) {
            $calendar = new Calendar();
            $calendar->calendar_title = $request['activity_title'];
            $calendar->start_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date'] . ' ' . $startTime);
            $calendar->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date'] . ' ' . $endTime);
            $calendar->description = $request['description'];
            $calendar->lead_id = $request['leadID'];
            $calendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
            $calendar->assigned_team_id = $request['department'];
            $calendar->assigned_user_id = $request['assigned_user_id'];
            $calendar->created_by = Auth::id();
            $calendar->updated_by = Auth::id();

            $calendar->save();
        }

        return redirect('/lead/' . $request['leadID'] . '?section=activities')->with('success', 'Activity Saved Successfully');
    }

    public function editActivity(Request $request, $id)
    {

       dd($id, $request->all());

        $activity = Activity::findOrFail($id);
        // Update fields
        $activity->activity_title = $request['activity_title_edit'];
        $activity->description = $request['description_edit'];
        $activity->priority_id = !empty($request['priority_edit']) ? $request['priority_edit'] : 1;
        $activity->activity_type_id = $request['activityType_edit'];
        $activity->lead_id = $request['leadID'];
        $activity->assigned_department_id = $request['department_edit'];
        $activity->assigned_user_id = !empty($request['agent_edit']) ? $request['agent_edit'] : Auth::user()->id;
        $activity->status_id = $request['status_edit'];
        // $activity->calendar_add = $request['addToCalendar'];
        if ($request->has('addToCalendar_edit')) {
            $activity->calendar_add = 1; // Convert 'on' to 1
        } else {
            $activity->calendar_add = 0; // Checkbox not checked
        }

        // Handle start_date_time
        if (!empty($request['start_date_edit'])) {
            $startTime = !empty($request['start_time_edit']) ? $request['start_time_edit'] : '12:00 AM';
            $activity->start_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date_edit'] . ' ' . $startTime);
        } else {
            $activity->start_date_time = null;
        }

        // Handle due_date_time
        if (!empty($request['end_date_edit'])) {
            $endTime = !empty($request['end_time_edit']) ? $request['end_time_edit'] : '12:00 AM';
            $activity->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date_edit'] . ' ' . $endTime);
        } else {
            $activity->due_date_time = null;
        }
        $activity->updated_by = Auth::user()->id;
        $activity->save();

        dd( $activity);

        // Optional: Update Calendar entry if needed (if already exists or if required to be added on update)
        if ($request['addToCalendar_edit'] == 1) {
            $calendar = new Calendar();
            $calendar->calendar_title = $request['activity_title_edit'];
            $calendar->start_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['start_date_edit'] . ' ' . $startTime);
            $calendar->due_date_time = Carbon::createFromFormat('d-m-Y h:i A', $request['end_date_edit'] . ' ' . $endTime);
            $calendar->description = $request['description_edit'];
            $calendar->lead_id = $request['leadID'];
            $calendar->priority_id = !empty($request['priority_edit']) ? $request['priority_edit'] : 1;
            $calendar->assigned_team_id = $request['department_edit'];
            $calendar->assigned_user_id = $request['assigned_user_id'];
            $calendar->created_by = Auth::id();
            $calendar->updated_by = Auth::id();
            $calendar->save();
        }

        return redirect('/lead/' . $request['leadID']. '?section=activities')->with('success', 'Activity Updated Successfully');
    }

    public function updateAllActivity(Request $request, $id)
    {
        $activity = Activity::findOrFail($id);
        $activity->activity_title = $request['activity_title'];
        $activity->description = $request['description'];
        $activity->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
        $activity->activity_type_id = $request['activityType'];
        $activity->assigned_department_id = $request['department'];
        $activity->assigned_user_id = !empty($request['agent']) ? $request['agent'] : Auth::user()->id;
        $activity->status_id = $request['status'];
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
        if ($request['addToCalendar'] == 1) {
            $calendar = new Calendar();
            $calendar->calendar_title = $request['activity_title'];
            $calendar->start_date_time = Carbon::createFromFormat('Y-m-d h:i A', $request['start_date'] . ' ' . $startTime);
            $calendar->due_date_time = Carbon::createFromFormat('Y-m-d h:i A', $request['end_date'] . ' ' . $endTime);
            $calendar->description = $request['description'];
            $calendar->lead_id = $request['leadID'];
            $calendar->priority_id = !empty($request['priority']) ? $request['priority'] : 1;
            $calendar->assigned_team_id = $request['department'];
            $calendar->assigned_user_id = $request['assigned_user_id'];
            $calendar->created_by = Auth::id();
            $calendar->updated_by = Auth::id();
            $calendar->save();
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
