<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class CollectionRateExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
                'Collection Rates Report',
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
                'Institution Breakdown',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                'Institution Name',
                'Total Debt',
                'Total Collected',
                'Collection Rate',
                '',
                '',
            ],
        ];

        // Add institution data
        foreach ($this->data['institutions'] as $institution) {
            $rows[] = [
                $institution['name'],
                number_format($institution['total_debt'], 2),
                number_format($institution['total_collected'], 2),
                number_format($institution['collection_rate'], 2) . '%',
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
        return 'Collection Rates Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true]],
            // Style the institution breakdown header
            8 => ['font' => ['bold' => true]],
            // Style the institution columns header
            9 => ['font' => ['bold' => true]],
        ];
    }
}
