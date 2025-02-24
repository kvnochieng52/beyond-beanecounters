<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Illuminate\Http\Request;



class CalendarController extends Controller
{
    public function index()
    {
        $calendars = Calendar::all();
        return view('calendars.index', compact('calendars'));
    }

    public function create()
    {
        return view('calendars.create');
    }

    public function storeCalendar(Request $request)
    {
        $request->validate([
            'calendar_title' => 'required',
            'start_date_time' => 'required|date',
            'due_date_time' => 'required|date|after_or_equal:start_date_time',
        ]);

        Calendar::create($request->all());

        return redirect()->route('calendars.index')->with('success', 'Calendar event added successfully.');
    }
}
