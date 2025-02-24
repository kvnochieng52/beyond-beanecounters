<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ActivityController extends Controller
{
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

        return redirect('/lead/' . $request['leadID'] . '?section=activities')->with('success', 'Activity Saved Successfully');
    }
}
