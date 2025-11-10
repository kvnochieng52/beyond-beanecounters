<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Carbon\Carbon;

class PTPThisWeekExport implements FromCollection, WithHeadings, WithMapping
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
            'PTP Date',
            'Days Until PTP'
        ];
    }

    public function map($ptp): array
    {
        $ptpDate = Carbon::parse($ptp->act_ptp_date);
        $today = Carbon::today();
        $daysUntil = $today->diffInDays($ptpDate, false);

        return [
            $ptp->lead_id,
            $ptp->lead_name,
            $ptp->institution_name,
            $ptp->assigned_agent_name,
            $ptp->assigned_agent_code,
            number_format($ptp->act_ptp_amount, 2),
            $ptp->lead_email,
            $ptp->lead_telephone,
            $ptpDate->format('Y-m-d'),
            $daysUntil == 0 ? 'Today' : ($daysUntil == 1 ? 'Tomorrow' : $daysUntil . ' days')
        ];
    }
}