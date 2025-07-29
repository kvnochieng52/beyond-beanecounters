<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Activity extends Model
{
    use HasFactory;


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
            ->leftJoin('ptps', 'leads.last_ptp_id', 'ptps.id')
            ->leftJoin('call_dispositions', 'leads.call_disposition_id', 'call_dispositions.id')
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
}
