<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Activity extends Model
{
    use HasFactory;

    protected $fillable = [
        'activity_title',
        'description',
        'priority_id',
        'start_date_time',
        'due_date_time',
        'activity_type_id',
        'lead_id',
        'assigned_department_id',
        'assigned_user_id',
        'status_id',
        'calendar_add',
        'ptp_check',
        'act_ptp_amount',
        'act_ptp_date',
        'act_ptp_retire_date',
        'act_payment_amount',
        'act_payment_transid',
        'act_payment_method',
        'act_call_disposition_id',
        'ref_text_id',
        'created_by',
        'updated_by'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class, 'lead_id');
    }


    public static function query()
    {
        return Activity::select([
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
            'institutions.institution_name',
            'leads.amount as lead_amount',
            'leads.balance as lead_balance',
            'ptps.ptp_date',
            'ptps.ptp_amount',
            'ptps.ptp_expiry_date',
            'ptps.created_at as ptp_created_at',
            'call_dispositions.call_disposition_name',
            'text_statuses.text_status_name',
            'text_statuses.color_code as text_status_color_code',
        ])
            ->leftJoin('lead_priorities', 'activities.priority_id', 'lead_priorities.id')
            ->leftJoin('activity_statuses', 'activities.status_id', 'activity_statuses.id')
            ->leftJoin('activity_types', 'activities.activity_type_id', 'activity_types.id')
            ->leftJoin('departments', 'activities.assigned_department_id', 'departments.id')
            ->leftJoin('users AS AGENT_JOIN', 'activities.assigned_user_id', '=', 'AGENT_JOIN.id')
            ->leftJoin('users AS CREATED_BY_JOIN', 'activities.created_by', '=', 'CREATED_BY_JOIN.id')
            ->leftJoin('leads', 'activities.lead_id', 'leads.id')
            ->leftJoin('institutions', 'leads.institution_id', 'institutions.id')
            // Only join PTPs created on the same date as the activity
            ->leftJoin('ptps', function ($join) {
                $join->on('activities.lead_id', '=', 'ptps.lead_id')
                    ->whereRaw('DATE(ptps.created_at) = DATE(activities.created_at)');
            })
            // Only join Call Dispositions created on the same date as the activity
            ->leftJoin('call_disposition_histories', function ($join) {
                $join->on('activities.lead_id', '=', 'call_disposition_histories.lead_id')
                    ->whereRaw('DATE(call_disposition_histories.created_at) = DATE(activities.created_at)');
            })

            ->leftJoin('call_dispositions', 'call_disposition_histories.call_disposition_id', 'call_dispositions.id')
            ->leftJoin('texts', 'activities.ref_text_id', 'texts.id')
            ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id');
    }


    public static function getLeadActivities($leadID)
    {
        $query = self::query()->where('activities.lead_id', $leadID)->orderBy('activities.created_at', 'desc')->paginate(10);

        return $query;
    }



    public static function getLeadInTimeLine($leadID)
    {
        $query = self::query()->where('activities.lead_id', $leadID)
            ->orderBy('activities.created_at', 'desc')
            ->get()
            ->groupBy('created_date');
        //  ->paginate(3);
        return $query;
    }

    /**
     * Check if a similar activity already exists for a lead within a timeframe
     */
    public static function hasSimilarActivity($leadId, $activityTypeId, $createdBy, $minutesWindow = 5)
    {
        $timeThreshold = now()->subMinutes($minutesWindow);

        return self::where('lead_id', $leadId)
            ->where('activity_type_id', $activityTypeId)
            ->where('created_by', $createdBy)
            ->where('created_at', '>=', $timeThreshold)
            ->exists();
    }

    /**
     * Check if a PTP activity already exists for a lead on the same date
     */
    public static function hasPTPForDate($leadId, $ptpDate, $excludeId = null)
    {
        $query = self::where('lead_id', $leadId)
            ->whereNotNull('act_ptp_date')
            ->whereDate('act_ptp_date', $ptpDate);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Check if a payment activity already exists for a lead with same transaction ID
     */
    public static function hasPaymentWithTransactionId($leadId, $transactionId, $excludeId = null)
    {
        if (empty($transactionId)) {
            return false;
        }

        $query = self::where('lead_id', $leadId)
            ->where('act_payment_transid', $transactionId)
            ->whereNotNull('act_payment_amount');

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Get the latest activity for a lead by type
     */
    public static function getLatestActivityByType($leadId, $activityTypeId)
    {
        return self::where('lead_id', $leadId)
            ->where('activity_type_id', $activityTypeId)
            ->orderBy('created_at', 'desc')
            ->first();
    }
}
