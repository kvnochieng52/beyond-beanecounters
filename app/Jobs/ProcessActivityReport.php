<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BackgroundReport;
use App\Models\Activity;
use App\Exports\ActivityReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProcessActivityReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $backgroundReport;

    /**
     * Create a new job instance.
     */
    public function __construct(BackgroundReport $backgroundReport)
    {
        $this->backgroundReport = $backgroundReport;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            // Mark as processing
            $this->backgroundReport->update([
                'status' => 'processing',
                'started_at' => now(),
            ]);

            $filters = $this->backgroundReport->filters;

            // Build the same query as in ActivityReportController
            $query = Activity::select([
                'activities.*',
                'lead_priorities.lead_priority_name',
                'lead_priorities.color_code as lead_priority_color_code',
                'activity_types.activity_type_title',
                'activity_types.icon as activity_type_icon',
                'activity_statuses.activity_status_name',
                'activity_statuses.color_code as activity_status_color_code',
                'departments.department_name',
                'AGENT_JOIN.name AS assigned_agent_name',
                'AGENT_JOIN.id_number AS assigned_agent_id_number',
                'AGENT_JOIN.agent_code AS assigned_agent_code',
                'CREATED_BY_JOIN.name AS created_by_name',
                'CREATED_BY_JOIN.id_number AS created_by_id_number',
                'CREATED_BY_JOIN.agent_code AS created_by_code',
                'CREATED_BY_JOIN.telephone AS created_by_telephone',
                'CREATED_BY_JOIN.email AS created_by_email',
                DB::raw('DATE(activities.created_at) as created_date'),
                DB::raw('DATE_FORMAT(activities.created_at, "%l:%i %p") as created_time'),
                'leads.title as lead_title',
                'leads.id as ticket_number',
                'institutions.institution_name',
                'leads.amount as lead_amount',
                'leads.balance as lead_balance',
                'leads.waiver_discount as lead_waiver_discount',
                'call_dispositions.call_disposition_name',
                'text_statuses.text_status_name',
                'text_statuses.color_code as text_status_color_code',
                'payment_methods.method_name',
            ])
                ->leftJoin('lead_priorities', 'activities.priority_id', 'lead_priorities.id')
                ->leftJoin('activity_statuses', 'activities.status_id', 'activity_statuses.id')
                ->leftJoin('activity_types', 'activities.activity_type_id', 'activity_types.id')
                ->leftJoin('departments', 'activities.assigned_department_id', 'departments.id')
                ->leftJoin('users AS AGENT_JOIN', 'activities.assigned_user_id', '=', 'AGENT_JOIN.id')
                ->leftJoin('users AS CREATED_BY_JOIN', 'activities.created_by', '=', 'CREATED_BY_JOIN.id')
                ->leftJoin('leads', 'activities.lead_id', 'leads.id')
                ->leftJoin('institutions', 'leads.institution_id', 'institutions.id')
                ->leftJoin('call_dispositions', 'activities.act_call_disposition_id', 'call_dispositions.id')
                ->leftJoin('texts', 'activities.ref_text_id', 'texts.id')
                ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id')
                ->leftJoin('payment_methods', 'activities.act_payment_method', '=', 'payment_methods.id');

            // Apply filters
            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $fromDate = Carbon::createFromFormat('Y-m-d', $filters['from_date'])->startOfDay();
                $toDate = Carbon::createFromFormat('Y-m-d', $filters['to_date'])->endOfDay();
                $query->whereBetween('activities.created_at', [$fromDate, $toDate]);
            }

            if (!empty($filters['ticket_numbers'])) {
                $query->whereIn('activities.lead_id', $filters['ticket_numbers']);
            }

            if (!empty($filters['activity_type_ids'])) {
                $query->whereIn('activities.activity_type_id', $filters['activity_type_ids']);
            }

            if (!empty($filters['agent_ids'])) {
                $query->whereIn('activities.assigned_user_id', $filters['agent_ids']);
            }

            if (!empty($filters['institution_ids'])) {
                $query->whereIn('leads.institution_id', $filters['institution_ids']);
            }

            if (!empty($filters['disposition_ids'])) {
                $query->whereIn('activities.act_call_disposition_id', $filters['disposition_ids']);
            }

            if (!empty($filters['ptp_due_from_date'])) {
                $query->where('activities.act_ptp_date', '>=', $filters['ptp_due_from_date']);
            }

            if (!empty($filters['ptp_due_to_date'])) {
                $query->where('activities.act_ptp_date', '<=', $filters['ptp_due_to_date']);
            }

            // Order by created date
            $query->orderBy('activities.created_at', 'desc');

            // Get the filtered data
            $activities = $query->get();

            // Generate filename
            $filename = 'activity_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filepath = 'reports/' . $filename;

            // Create the export
            Excel::store(new ActivityReportExport($activities, $filters), $filepath);

            // Update background report with completion
            $this->backgroundReport->update([
                'status' => 'completed',
                'file_path' => $filepath,
                'completed_at' => now(),
            ]);

        } catch (\Exception $e) {
            // Mark as failed and store error
            $this->backgroundReport->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
                'completed_at' => now(),
            ]);

            throw $e;
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        $this->backgroundReport->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
            'completed_at' => now(),
        ]);
    }
}
