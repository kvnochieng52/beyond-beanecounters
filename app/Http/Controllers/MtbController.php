<?php

namespace App\Http\Controllers;

use App\Models\Mtb;
use App\Models\MtbAttachment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class MtbController extends Controller
{
    public function getAgents(Request $request)
    {
        $agents = \App\Models\User::where('is_active', 1)
            ->select('id', 'name', 'agent_code')
            ->orderBy('name')
            ->get()
            ->map(function ($agent) {
                return [
                    'id' => $agent->id,
                    'text' => $agent->name . ' (' . ($agent->agent_code ?? '-') . ')'
                ];
            });

        return response()->json(['results' => $agents]);
    }

    public function getMtbs(Request $request)
    {
        if ($request->ajax()) {
            $mtbs = Mtb::leftJoin('users', 'mtbs.created_by', '=', 'users.id')
                ->select(
                    'mtbs.id',
                    'mtbs.lead_id',
                    'mtbs.amount_paid',
                    'mtbs.date_paid',
                    'mtbs.payment_channel',
                    'mtbs.description',
                    'users.name as created_by_name',
                    'mtbs.created_at'
                )
                ->where('mtbs.lead_id', $request['lead_id'])
                ->withCount('attachments')
                ->orderBy('id', 'DESC');

            return DataTables::of($mtbs)
                ->addIndexColumn()
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->make(true);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'lead_id' => 'required|exists:leads,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
            'payment_channel' => 'required|in:Mpesa,CASH,CHEQUE',
            'description' => 'nullable|string',
            'agent_id' => 'nullable|exists:users,id',
            'attachments.*' => 'nullable|file|max:5120' // Max 5MB per file
        ]);

        $mtb = new Mtb();
        $mtb->lead_id = $request->lead_id;
        $mtb->amount_paid = $request->amount_paid;
        $mtb->date_paid = $request->date_paid;
        $mtb->payment_channel = $request->payment_channel;
        $mtb->description = $request->description;

        // Use selected agent if provided, otherwise use current user
        $createdBy = $request->filled('agent_id') ? $request->agent_id : Auth::user()->id;
        $mtb->created_by = $createdBy;
        $mtb->updated_by = $createdBy;
        $mtb->save();

        // Handle file attachments
        if ($request->hasFile('attachments')) {
            $this->storeAttachments($request->file('attachments'), $mtb->id);
        }

        return redirect()->back()->with('success', 'MTB record created successfully!');
    }

    public function edit($id)
    {
        $mtb = Mtb::findOrFail($id);
        return response()->json([
            'id' => $mtb->id,
            'lead_id' => $mtb->lead_id,
            'amount_paid' => $mtb->amount_paid,
            'date_paid' => $mtb->date_paid,
            'payment_channel' => $mtb->payment_channel,
            'description' => $mtb->description
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'mtb_id' => 'required|exists:mtbs,id',
            'lead_id' => 'required|exists:leads,id',
            'amount_paid' => 'required|numeric|min:0',
            'date_paid' => 'required|date',
            'payment_channel' => 'required|in:Mpesa,CASH,CHEQUE',
            'description' => 'nullable|string'
        ]);

        $mtb = Mtb::findOrFail($request->mtb_id);
        $mtb->amount_paid = $request->amount_paid;
        $mtb->date_paid = $request->date_paid;
        $mtb->payment_channel = $request->payment_channel;
        $mtb->description = $request->description;
        $mtb->updated_by = Auth::user()->id;
        $mtb->save();

        return redirect()->back()->with('success', 'MTB record updated successfully!');
    }

    public function destroy($id)
    {
        $mtb = Mtb::findOrFail($id);
        $mtb->delete();

        return response()->json(['success' => 'MTB record deleted successfully!']);
    }

    public function getMtbAttachments(Request $request)
    {
        if ($request->ajax()) {
            $attachments = MtbAttachment::where('mtb_id', $request['mtb_id'])
                ->leftJoin('users', 'mtb_attachments.created_by', '=', 'users.id')
                ->select(
                    'mtb_attachments.id',
                    'mtb_attachments.original_name',
                    'mtb_attachments.file_size',
                    'mtb_attachments.file_type',
                    'users.name as created_by_name',
                    'mtb_attachments.created_at'
                )
                ->orderBy('mtb_attachments.id', 'DESC');

            return DataTables::of($attachments)
                ->addIndexColumn()
                ->editColumn('file_type', function ($row) {
                    // Map MIME types to simple names
                    $mimeMap = [
                        'application/pdf' => 'PDF',
                        'application/msword' => 'Word',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document' => 'Word',
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.template' => 'Word',
                        'application/vnd.ms-excel' => 'Excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' => 'Excel',
                        'application/vnd.openxmlformats-officedocument.spreadsheetml.template' => 'Excel',
                        'image/jpeg' => 'Image',
                        'image/jpg' => 'Image',
                        'image/png' => 'Image',
                        'image/gif' => 'Image',
                        'image/webp' => 'Image',
                        'image/bmp' => 'Image',
                        'image/svg+xml' => 'Image',
                        'text/plain' => 'Text',
                        'text/csv' => 'CSV',
                        'application/vnd.ms-powerpoint' => 'PowerPoint',
                        'application/vnd.openxmlformats-officedocument.presentationml.presentation' => 'PowerPoint',
                    ];

                    return $mimeMap[$row->file_type] ?? ucfirst(str_replace('application/', '', $row->file_type));
                })
                ->editColumn('created_at', function ($row) {
                    return $row->created_at->format('Y-m-d H:i:s');
                })
                ->addColumn('file_size_formatted', function ($row) {
                    $bytes = $row->file_size;
                    $units = ['B', 'KB', 'MB', 'GB'];
                    $bytes = max($bytes, 0);
                    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
                    $pow = min($pow, count($units) - 1);
                    $bytes /= (1 << (10 * $pow));
                    return round($bytes, 2) . ' ' . $units[$pow];
                })
                ->addColumn('action', function ($row) {
                    return '<a href="' . route('mtb.download-attachment', $row->id) . '" class="btn btn-sm btn-info" title="Download"><i class="fa fa-download"></i></a>
                            <button class="btn btn-sm btn-danger" onclick="deleteAttachment(' . $row->id . ')" title="Delete"><i class="fa fa-trash"></i></button>';
                })
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    public function downloadAttachment($id)
    {
        $attachment = MtbAttachment::findOrFail($id);
        $filePath = storage_path('app/' . $attachment->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File not found!');
        }

        return response()->download($filePath, $attachment->original_name);
    }

    public function deleteAttachment($id)
    {
        $attachment = MtbAttachment::findOrFail($id);
        $filePath = storage_path('app/' . $attachment->file_path);

        // Delete file from storage
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $attachment->delete();

        return response()->json(['success' => 'Attachment deleted successfully!']);
    }

    private function storeAttachments($files, $mtbId)
    {
        foreach ($files as $file) {
            $originalName = $file->getClientOriginalName();
            $fileName = time() . '_' . uniqid() . '_' . $originalName;
            $relativePath = 'mtb-attachments/' . $mtbId;

            // Store file
            $path = $file->storeAs($relativePath, $fileName, 'local');

            // Create attachment record
            MtbAttachment::create([
                'mtb_id' => $mtbId,
                'file_name' => $fileName,
                'original_name' => $originalName,
                'file_size' => $file->getSize(),
                'file_type' => $file->getMimeType(),
                'file_path' => $path,
                'created_by' => Auth::user()->id
            ]);
        }
    }
}
