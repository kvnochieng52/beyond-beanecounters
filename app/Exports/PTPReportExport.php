<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class PTPReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['ptps']);
    }

    public function headings(): array
    {
        return [
            'Institution',
            'Ticket No.',
            'Lead Title',
            'PTP Created By',
            'PTP Created Date',
            'PTP Due Date',
            'PTP Amount (KSH)',
            'Assigned Agent',
            'Agent Code',
            'Lead Amount (KSH)',
            'Lead Balance (KSH)',
            'Days Until Due',
            'Status',
            'Creation Date & Time'
        ];
    }

    public function map($ptp): array
    {
        $ptpDueDate = Carbon::parse($ptp->act_ptp_date);
        $today = Carbon::today();
        $daysUntilDue = $today->diffInDays($ptpDueDate, false); // false to get negative for overdue

        $status = '';
        if ($ptpDueDate->lt($today)) {
            $status = 'Overdue';
        } elseif ($ptpDueDate->isToday()) {
            $status = 'Due Today';
        } elseif ($ptpDueDate->isTomorrow()) {
            $status = 'Due Tomorrow';
        } else {
            $status = 'Active';
        }

        return [
            $ptp->institution_name ?? 'N/A',
            '#' . $ptp->ticket_number,
            $ptp->lead_title ?? 'N/A',
            $ptp->created_by_name ?? 'Unknown',
            Carbon::parse($ptp->ptp_created_date)->format('d-m-Y'),
            $ptpDueDate->format('d-m-Y'),
            number_format($ptp->act_ptp_amount, 2),
            $ptp->assigned_agent_name ?? 'Unassigned',
            $ptp->assigned_agent_code ?? '-',
            number_format($ptp->lead_amount ?? 0, 2),
            number_format($ptp->lead_balance ?? 0, 2),
            $daysUntilDue,
            $status,
            Carbon::parse($ptp->ptp_created_date)->format('d-m-Y H:i:s')
        ];
    }

    public function title(): string
    {
        $fromDate = $this->data['filters']['from_date'];
        $toDate = $this->data['filters']['to_date'];
        $filterType = $this->data['filters']['date_filter_type'];
        $filterTypeText = $filterType === 'due' ? 'Due Date' : 'Created Date';

        return "PTP Report ({$filterTypeText}: {$fromDate} to {$toDate})";
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF28a745',
                    ],
                ],
                'font' => [
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // All cells alignment
            'A:N' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Number columns right alignment
            'G:G' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
            'J:L' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Center alignment for specific columns
            'E:F' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
            'L:M' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20,  // Institution
            'B' => 12,  // Ticket No
            'C' => 25,  // Lead Title
            'D' => 18,  // PTP Created By
            'E' => 15,  // PTP Created Date
            'F' => 15,  // PTP Due Date
            'G' => 18,  // PTP Amount
            'H' => 18,  // Assigned Agent
            'I' => 12,  // Agent Code
            'J' => 15,  // Lead Amount
            'K' => 15,  // Lead Balance
            'L' => 12,  // Days Until Due
            'M' => 12,  // Status
            'N' => 18,  // Creation DateTime
        ];
    }
}