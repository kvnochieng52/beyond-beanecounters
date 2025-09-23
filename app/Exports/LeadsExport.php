<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsExport implements FromQuery, WithHeadings, WithMapping
{
    protected $user;

    public function __construct($user = null)
    {
        $this->user = $user;
    }

    public function query()
    {
        $query = Lead::query()->orderBy('id', 'DESC');

        // If user is provided and is not an admin, filter by assigned agent
        if ($this->user && !$this->user->hasRole('Admin')) {
            $query->where('assigned_agent', $this->user->id);
        }

        return $query;
    }

    public function headings(): array
    {
        return [
            '#',
            'T/No.',
            'Names/Title',
            'Institution',
            'ID Number',
            'Telephone',
            'Amount',
            'Balance',
            'PTP',
            'PTP Retire Date',
            'Assigned To',
            'Priority',
            'Status',
            'Stage',
            'Call Disposition'
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->id,
            $lead->title,
            $lead->institution_name,
            $lead->id_passport_number,
            $lead->telephone,
            $lead->currency_name . ' ' . number_format($lead->amount, 2),
            $lead->currency_name . ' ' . number_format($lead->balance, 2),
            $lead->currency_name . ' ' . number_format($lead->ptp_amount, 2),
            $lead->ptp_expiry_date ? date('d-m-Y', strtotime($lead->ptp_expiry_date)) : '',
            $lead->assigned_agent_name,
            $lead->lead_priority_name,
            $lead->lead_status_name,
            $lead->lead_stage_name,
            $lead->call_disposition_name,
        ];
    }
}
