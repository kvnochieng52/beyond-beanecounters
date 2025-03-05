<?php

namespace App\Http\Controllers;

use App\Models\DueNotification;
use App\Models\Lead;
use Illuminate\Http\Request;

use Yajra\DataTables\Facades\DataTables;

class DueNotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            return datatables()->of(DueNotification::query())
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('due-notifications.edit', $row->id) . '" class="btn btn-sm btn-primary">Edit</a>
                        <form action="' . route('due-notifications.destroy', $row->id) . '" method="POST" style="display:inline;">
                            ' . csrf_field() . '
                            ' . method_field('DELETE') . '
                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                        </form>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('due-notifications.index');
    }


    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $query = Lead::query(); // Your existing query function
        $columns = array_keys($query->first()->getAttributes()); // Get column names

        return view('due-notifications.create', compact('columns'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'moment' => 'required|string',
            'days' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);



        $data = $request->all();


        // Handle unchecked checkboxes (default to 0)
        $data['send_sms'] = $request->has('send_sms') ? 1 : 0;
        $data['send_email'] = $request->has('send_email') ? 1 : 0;

        DueNotification::create($data);

        return redirect()->route('due-notifications.index')->with('success', 'Notification created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(DueNotification $dueNotification)
    {


        $query = Lead::query(); // Your existing query function
        $columns = array_keys($query->first()->getAttributes()); // Get column names

        return view('due-notifications.edit', compact('dueNotification', 'columns'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, DueNotification $dueNotification)
    {
        $request->validate([
            'title' => 'required|string',
            'message' => 'required|string',
            'moment' => 'required|string',
            'days' => 'required|integer',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->all();

        // Handle unchecked checkboxes (default to 0)
        $data['send_sms'] = $request->has('send_sms') ? 1 : 0;
        $data['send_email'] = $request->has('send_email') ? 1 : 0;

        $dueNotification->update($data);

        return redirect()->route('due-notifications.index')->with('success', 'Notification updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(DueNotification $dueNotification)
    {
        $dueNotification->delete();
        return redirect()->route('due-notifications.index')->with('success', 'Notification deleted successfully.');
    }
}
