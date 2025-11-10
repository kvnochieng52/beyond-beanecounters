<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PTPTodayExport implements FromCollection, WithHeadings, WithMapping
{
    protected $ptps;

    public function __construct($ptps)
    {
        $this->ptps = $ptps;
    }

    public function collection()
    {
        return $this->ptps;
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Lead Name',
            'Institution',
            'Assigned Agent',
            'Agent Code',
            'PTP Amount',
            'Email',
            'Telephone',
            'PTP Date'
        ];
    }

    public function map($ptp): array
    {
        return [
            $ptp->lead_id,
            $ptp->lead_name,
            $ptp->institution_name,
            $ptp->assigned_agent_name,
            $ptp->assigned_agent_code,
            number_format($ptp->act_ptp_amount, 2),
            $ptp->lead_email,
            $ptp->lead_telephone,
            Carbon::parse($ptp->act_ptp_date)->format('Y-m-d')
        ];
    }
}