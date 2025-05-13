<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CollectionProgressExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
                'Collection Progress Report',
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
            ],
            [
                'Total Leads',
                $this->data['total_leads'],
                '',
                '',
                '',
                '',
            ],
            [
                'Total Debt Amount',
                number_format($this->data['total_debt'], 2),
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
            ],
            [
                'Overall Collection Rate',
                number_format($this->data['collection_rate'], 2) . '%',
                '',
                '',
                '',
                '',
            ],
            [
                '', '', '', '', '', '',
            ],
            [
                'Monthly Collection Progress',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Month',
                'Target Amount',
                'Collected Amount',
                'Collection Rate',
                '',
                '',
            ],
        ];

        // Add monthly data
        foreach ($this->data['monthly_data'] as $month) {
            $rows[] = [
                $month['month'],
                number_format($month['target'], 2),
                number_format($month['collected'], 2),
                number_format($month['percentage'], 2) . '%',
                '',
                '',
            ];
        }

        $rows[] = ['', '', '', '', '', ''];
        $rows[] = [
            'Weekly Collection Progress',
            '',
            '',
            '',
            '',
            '',
        ];
        $rows[] = [
            'Week',
            'Target Amount',
            'Collected Amount',
            'Collection Rate',
            '',
            '',
        ];

        // Add weekly data
        foreach ($this->data['weekly_data'] as $week) {
            $rows[] = [
                $week['week'],
                number_format($week['target'], 2),
                number_format($week['collected'], 2),
                number_format($week['percentage'], 2) . '%',
                '',
                '',
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
        ];
    }

    public function title(): string
    {
        return 'Collection Progress Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true]],
            // Style the monthly progress header
            8 => ['font' => ['bold' => true]],
            // Style the monthly columns header
            9 => ['font' => ['bold' => true]],
            // Style the weekly progress header
            12 + count($this->data['monthly_data']) => ['font' => ['bold' => true]],
            // Style the weekly columns header
            13 + count($this->data['monthly_data']) => ['font' => ['bold' => true]],
        ];
    }
}
