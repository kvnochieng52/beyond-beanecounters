<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class OutstandingDebtExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $rows = [
            [
                'Report Type',
                'Outstanding Debts Report',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'As of Date',
                $this->data['as_of_date'],
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Total Outstanding Amount',
                number_format($this->data['total_outstanding'], 2),
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Total Leads with Outstanding Balances',
                $this->data['total_leads'],
                '',
                '',
                '',
                '',
                '',
            ],
            [
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Aging Analysis',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Days Overdue',
                'Number of Leads',
                'Outstanding Amount',
                'Percentage of Total',
                '',
                '',
                '',
            ],
        ];

        // Add aging analysis data
        foreach ($this->data['overdue_groups'] as $range => $group) {
            $percentage = $this->data['total_outstanding'] > 0
                ? ($group['amount'] / $this->data['total_outstanding']) * 100
                : 0;

            $rows[] = [
                $range . ' days',
                $group['count'],
                number_format($group['amount'], 2),
                number_format($percentage, 2) . '%',
                '',
                '',
                '',
            ];
        }

        $rows[] = ['', '', '', '', '', '', ''];
        $rows[] = [
            'Detailed Lead Information',
            '',
            '',
            '',
            '',
            '',
            '',
        ];
        $rows[] = [
            'Lead ID',
            'Lead Title',
            'Institution',
            'Original Amount',
            'Outstanding Balance',
            'Due Date',
            'Days Overdue',
            'assigned_agent'
        ];

        // Add detailed lead data
        foreach ($this->data['leads'] as $lead) {
            $rows[] = [
                $lead['id'],
                $lead['title'],
                $lead['institution'],
                number_format($lead['amount'], 2),
                number_format($lead['balance'], 2),
                $lead['due_date'],
                $lead['days_overdue'],
                $lead['assigned_agent'],
            ];
        }

        return new Collection($rows);
    }

    public function headings(): array
    {
        return [
            'Report Generated on',
            date('Y-m-d H:i:s'),
            '',
            '',
            '',
            '',
            '',
        ];
    }

    public function title(): string
    {
        return 'Outstanding Debts Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true]],
            // Style the aging analysis header
            6 => ['font' => ['bold' => true]],
            // Style the aging columns header
            7 => ['font' => ['bold' => true]],
            // Style the detailed lead header
            12 => ['font' => ['bold' => true]],
            // Style the detailed lead columns header
            13 => ['font' => ['bold' => true]],
        ];
    }
}
