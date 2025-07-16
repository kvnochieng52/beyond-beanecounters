<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CalendarController extends Controller
{
    public function index()
    {
        $calendars = Calendar::where('created_by', 14)->get();
        return view('calendars.index', compact('calendars'));
    }

    public function create()
    {
        return view('calendars.create');
    }

    public function storeCalendar(Request $request)
    {

        if (!empty($request['id'])) {
            $calendar =  Calendar::find($request['id']);
            $calendar->calendar_title = $request['title'];
            $calendar->start_date_time = Carbon::parse($request['start'])->format('Y-m-d H:i:s');
            $calendar->due_date_time = Carbon::parse($request['end'])->format('Y-m-d H:i:s');
            $calendar->description = $request['description'];
            $calendar->updated_by = Auth::id();
            $calendar->save();
        } else {
            $calendar = new Calendar();
            $calendar->calendar_title = $request['title'];
            $calendar->start_date_time = Carbon::parse($request['start'])->format('Y-m-d H:i:s');
            $calendar->due_date_time = Carbon::parse($request['end'])->format('Y-m-d H:i:s');
            $calendar->description = $request['description'];
            $calendar->created_by = Auth::id();
            $calendar->updated_by = Auth::id();
            $calendar->save();
        }


        return response()->json([
            'id' => $calendar->id,
            'title' => $calendar->calendar_title,
            'start' => $calendar->start_date_time,
            'end' => $calendar->due_date_time,
            'description' => $calendar->description
        ]);
    }


    public function deleteCalendar(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:calendars,id',
        ]);

        $calendar = Calendar::find($request->id);

        if (!$calendar) {
            return response()->json(['success' => false, 'message' => 'Event not found'], 404);
        }

        $calendar->delete();

        return response()->json([
            'success' => true,
            'id' => $request->id,
            'message' => 'Event deleted successfully',
        ]);
    }
}
