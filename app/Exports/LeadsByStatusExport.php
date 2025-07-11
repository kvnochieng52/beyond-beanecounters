<?php

namespace App\Exports;

use App\Models\Lead;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsByStatusExport implements FromQuery, WithHeadings, WithMapping
{
    protected $status;

    public function __construct($status)
    {
        $this->status = $status;
    }

    public function query()
    {
        return Lead::query()
            ->where('status_id', $this->status)
            ->orderBy('id', 'DESC');
    }

    public function headings(): array
    {
        return [
            '#',
            'T/No.',
            'Names/Title',
            'Defaulter Type',
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
            $lead->defaulter_type_name,
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
