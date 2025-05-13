<?php

namespace App\Http\Controllers;

use App\Exports\QueuesExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class QueueController extends Controller
{
    public function export(Request $request)
    {
        $filters = [];



        // dd("here");

        if ($request->has('delivered')) $filters[] = 3; // SENT
        if ($request->has('undelivered')) $filters[] = 4; // FAILED
        if ($request->has('cancelled')) $filters[] = 5; // CANCELLED
        if ($request->has('blacklisted')) $filters[] = 6; // BLACKLISTED

        return Excel::download(new QueuesExport($filters, $request['text_id']), 'queues_report.xlsx');
    }
}
