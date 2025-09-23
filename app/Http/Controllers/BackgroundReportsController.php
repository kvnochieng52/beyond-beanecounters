<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\BackgroundReport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class BackgroundReportsController extends Controller
{
    public function index()
    {
        $reports = BackgroundReport::with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('background_reports.index', compact('reports'));
    }

    public function download($id)
    {
        $report = BackgroundReport::findOrFail($id);

        // Check if user can download this report
        if (!Auth::user()->hasRole('Admin') && $report->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized to download this report.');
        }

        if ($report->status !== 'completed' || !$report->file_path) {
            return back()->with('error', 'Report is not ready for download.');
        }

        $filePath = storage_path('app/' . $report->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'Report file not found.');
        }

        return response()->download($filePath, basename($report->file_path));
    }

    public function destroy($id)
    {
        $report = BackgroundReport::findOrFail($id);

        // Check if user can delete this report
        if (!Auth::user()->hasRole('Admin') && $report->requested_by !== Auth::id()) {
            abort(403, 'Unauthorized to delete this report.');
        }

        // Delete the file if it exists
        if ($report->file_path && Storage::exists($report->file_path)) {
            Storage::delete($report->file_path);
        }

        $report->delete();

        return back()->with('success', 'Background report deleted successfully.');
    }

    public function getData()
    {
        $query = BackgroundReport::with('user');

        // If user is not admin, only show their reports
        if (!Auth::user()->hasRole('Admin')) {
            $query->where('requested_by', Auth::id());
        }

        $reports = $query->orderBy('created_at', 'desc')->get();

        return datatables($reports)
            ->addColumn('user_name', function ($report) {
                return $report->user->name ?? 'Unknown';
            })
            ->addColumn('status_badge', function ($report) {
                $color = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'completed' => 'success',
                    'failed' => 'danger'
                ][$report->status] ?? 'secondary';

                return '<span class="badge bg-' . $color . '">' . ucfirst($report->status) . '</span>';
            })
            ->addColumn('duration', function ($report) {
                return $report->duration;
            })
            ->addColumn('file_size', function ($report) {
                return $report->file_size ?: 'N/A';
            })
            ->addColumn('actions', function ($report) {
                $actions = '';

                if ($report->status === 'completed' && $report->file_path) {
                    $actions .= '<a href="' . route('background-reports.download', $report->id) . '" class="btn btn-sm btn-success me-1">
                        <i class="fas fa-download"></i> Download
                    </a>';
                }

                if (Auth::user()->hasRole('Admin') || $report->requested_by === Auth::id()) {
                    $actions .= '<button onclick="deleteReport(' . $report->id . ')" class="btn btn-sm btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </button>';
                }

                return $actions;
            })
            ->addColumn('created_at_formatted', function ($report) {
                return $report->created_at->format('d-m-Y H:i:s');
            })
            ->rawColumns(['status_badge', 'actions'])
            ->make(true);
    }
}
