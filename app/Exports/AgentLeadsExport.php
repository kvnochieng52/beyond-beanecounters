<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AgentLeadsExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
                'Agent Leads Report',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Agent Name',
                $this->data['agent']->name,
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Date Range',
                $this->data['start_date'] . ' to ' . $this->data['end_date'],
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Total Leads',
                $this->data['total_leads'],
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Total Assigned Amount',
                number_format($this->data['total_assigned'], 2),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Total Collected Amount',
                number_format($this->data['total_collected'], 2),
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Collection Rate',
                number_format($this->data['collection_rate'], 2) . '%',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Closed Leads',
                $this->data['closed_leads'],
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Overdue Leads',
                $this->data['overdue_leads'],
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                '', '', '', '', '', '', '', '', '', '', '',
            ],
            [
                'Detailed Lead Information',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Lead ID',
                'Title',
                'Institution',
                'Amount',
                'Balance',
                'Collected',
                'Due Date',
                'Status',
                'Closed',
                'Overdue',
                'Created Date',
            ],
        ];

        // Add lead data
        foreach ($this->data['leads'] as $lead) {
            $rows[] = [
                $lead['id'],
                $lead['title'],
                $lead['institution'],
                number_format($lead['amount'], 2),
                number_format($lead['balance'], 2),
                number_format($lead['collected'], 2),
                $lead['due_date'],
                $lead['status'],
                $lead['is_closed'] ? 'Yes' : 'No',
                $lead['is_overdue'] ? 'Yes' : 'No',
                $lead['created_at'],
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
            '',
            '',
            '',
            '',
        ];
    }

    public function title(): string
    {
        return 'Agent Leads Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true]],
            // Style the agent name row
            2 => ['font' => ['bold' => true]],
            // Style the detailed lead header
            11 => ['font' => ['bold' => true]],
            // Style the detailed lead columns header
            12 => ['font' => ['bold' => true]],
        ];
    }
}
