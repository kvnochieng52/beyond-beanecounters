<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Lead extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'id_passport_number',
        'account_number',
        'defaulter_type_id',
        'gender_id',
        'telephone',
        'alternate_telephone',
        'email',
        'alternate_email',
        'country_id',
        'town',
        'address',
        'occupation',
        'company_name',
        'description',
        'kin_full_names',
        'kin_telephone',
        'kin_email',
        'kin_relationship',
        'assigned_agent',
        'institution_id',
        'amount',
        'additional_charges',
        'balance',
        'currency_id',
        'status_id',
        'stage_id',
        'priority_id',
        'due_date',
        'created_by',
        'updated_by',
    ];


    public static function query()
    {






        $query = self::select([
            'leads.*',
            'defaulter_types.defaulter_type_name',
            'genders.gender_name',
            'countries.country_name',
            'AGENT_JOIN.name AS assigned_agent_name',
            'AGENT_JOIN.id_number AS assigned_agent_id_number',
            'AGENT_JOIN.agent_code AS assigned_agent_code',
            'AGENT_JOIN.telephone AS assigned_agent_telephone',
            'AGENT_JOIN.email AS assigned_agent_email',

            'CREATED_BY_JOIN.name AS created_by_name',
            'CREATED_BY_JOIN.id_number AS created_by_id_number',
            'CREATED_BY_JOIN.agent_code AS created_by_code',
            'CREATED_BY_JOIN.telephone AS created_by_telephone',
            'CREATED_BY_JOIN.email AS created_by_email',

            'institutions.institution_name',
            'currencies.currency_name',
            'lead_statuses.lead_status_name',
            'lead_statuses.color_code AS lead_status_color_code',
            'lead_stages.lead_stage_name',
            'lead_categories.lead_category_name',
            'lead_priorities.lead_priority_name',
            'lead_priorities.color_code AS lead_priority_color_code',
            'lead_priorities.description AS lead_priority_description',
            'lead_industries.lead_industry_name',
            'departments.department_name',
            'lead_conversion_statuses.lead_conversion_name',
            'lead_engagement_levels.lead_engagement_level_name',
            'ptps.ptp_date',
            'ptps.ptp_amount',
            'ptps.ptp_expiry_date',
            'call_dispositions.call_disposition_name',
            // 'leads.amount as amount'

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
            ->leftJoin('lead_industries', 'leads.industry_id', 'lead_industries.id')
            ->leftJoin('departments', 'leads.assigned_department', 'departments.id')
            ->leftJoin('lead_conversion_statuses', 'leads.conversion_status_id', 'lead_conversion_statuses.id')
            ->leftJoin('lead_engagement_levels', 'leads.engagement_level_id', 'lead_engagement_levels.id')
            ->leftJoin('ptps', 'leads.last_ptp_id', 'ptps.id')
            ->leftJoin('call_dispositions', 'leads.call_disposition_id', 'call_dispositions.id');



        return $query;
    }






    public static function getLeadByID($leadID)
    {
        return self::query()->where('leads.id', $leadID)->first();
    }



    public static function getLeads()
    {

        $query = self::query()
            ->orderBy('id', 'DESC')
            ->paginate(10);

        return $query;
    }
}
