<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\BackgroundReport;
use App\Models\Lead;
use App\Exports\LeadsReportExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class ProcessLeadsReport implements ShouldQueue
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

            // Build the leads query using the same structure as Lead::query()
            $query = Lead::select([
                'leads.*',
                'defaulter_types.defaulter_type_name',
                'genders.gender_name',
                'countries.country_name',
                'AGENT_JOIN.name AS assigned_agent_name',
                'AGENT_JOIN.id_number AS assigned_agent_id_number',
                'AGENT_JOIN.agent_code AS assigned_agent_code',
                'CREATED_BY_JOIN.name AS created_by_name',
                'CREATED_BY_JOIN.id_number AS created_by_id_number',
                'CREATED_BY_JOIN.agent_code AS created_by_code',
                'institutions.institution_name',
                'currencies.currency_name',
                'lead_statuses.lead_status_name as status_name',
                'lead_stages.lead_stage_name as stage_name',
                'lead_categories.lead_category_name as category_name',
                'lead_priorities.lead_priority_name as priority_name',
                'lead_priorities.color_code AS lead_priority_color_code',
                'lead_priorities.description AS lead_priority_description',
                'departments.department_name',
                DB::raw('DATE(leads.created_at) as created_date'),
            ])
                ->leftJoin('defaulter_types', 'leads.defaulter_type_id', 'defaulter_types.id')
                ->leftJoin('genders', 'leads.gender_id', 'genders.id')
                ->leftJoin('countries', 'leads.country_id', 'countries.id')
                ->leftJoin('users AS AGENT_JOIN', 'leads.assigned_agent', '=', 'AGENT_JOIN.id')
                ->leftJoin('users AS CREATED_BY_JOIN', 'leads.created_by', '=', 'CREATED_BY_JOIN.id')
                ->leftJoin('institutions', 'leads.institution_id', 'institutions.id')
                ->leftJoin('currencies', 'leads.currency_id', 'currencies.id')
                ->leftJoin('lead_statuses', 'leads.status_id', 'lead_statuses.id')
                ->leftJoin('lead_stages', 'leads.stage_id', 'lead_stages.id')
                ->leftJoin('lead_categories', 'leads.category_id', 'lead_categories.id')
                ->leftJoin('lead_priorities', 'leads.priority_id', 'lead_priorities.id')
                ->leftJoin('departments', 'leads.assigned_department', 'departments.id');

            // Apply filters
            if (!empty($filters['from_date']) && !empty($filters['to_date'])) {
                $fromDate = Carbon::createFromFormat('Y-m-d', $filters['from_date'])->startOfDay();
                $toDate = Carbon::createFromFormat('Y-m-d', $filters['to_date'])->endOfDay();
                $query->whereBetween('leads.created_at', [$fromDate, $toDate]);
            }

            if (!empty($filters['institution_ids'])) {
                $query->whereIn('leads.institution_id', $filters['institution_ids']);
            }

            if (!empty($filters['status_ids'])) {
                $query->whereIn('leads.status_id', $filters['status_ids']);
            }

            if (!empty($filters['priority_ids'])) {
                $query->whereIn('leads.priority_id', $filters['priority_ids']);
            }

            if (!empty($filters['category_ids'])) {
                $query->whereIn('leads.category_id', $filters['category_ids']);
            }

            if (!empty($filters['min_amount'])) {
                $query->where('leads.amount', '>=', $filters['min_amount']);
            }

            if (!empty($filters['max_amount'])) {
                $query->where('leads.amount', '<=', $filters['max_amount']);
            }

            // Order by created date
            $query->orderBy('leads.created_at', 'desc');

            // Get the filtered data
            $leads = $query->get();

            // Generate filename
            $filename = 'leads_report_' . now()->format('Y_m_d_H_i_s') . '.xlsx';
            $filepath = 'reports/' . $filename;

            // Create the export
            Excel::store(new LeadsReportExport($leads, $filters), $filepath);

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