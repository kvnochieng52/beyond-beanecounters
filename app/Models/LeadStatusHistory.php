<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatusHistory extends Model
{
    use HasFactory;



    public static function getLeadHistory($leadID)
    {
        return self::select([
            'lead_status_histories.*',
            'lead_statuses.lead_status_name',
            'lead_statuses.color_code as lead_status_color_code',
            'lead_stages.lead_stage_name',
            'lead_conversion_statuses.lead_conversion_name',
            'lead_engagement_levels.lead_engagement_level_name',
            'CREATED_BY_JOIN.name as agent_name',

        ])
            ->leftJoin('lead_statuses', 'lead_status_histories.lead_status_id', 'lead_statuses.id')
            ->leftJoin('lead_stages', 'lead_status_histories.lead_stage_id', 'lead_stages.id')
            ->leftJoin('lead_conversion_statuses', 'lead_status_histories.lead_conversion_id', 'lead_conversion_statuses.id')
            ->leftJoin('lead_engagement_levels', 'lead_status_histories.lead_engagement_level', 'lead_engagement_levels.id')
            ->leftJoin('users AS CREATED_BY_JOIN', 'lead_status_histories.created_by', '=', 'CREATED_BY_JOIN.id')
            ->where('lead_status_histories.lead_id', $leadID)
            ->orderBy('id', 'DESC')
            ->paginate(10);
    }
}
