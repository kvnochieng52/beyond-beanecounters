<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class DispositionsReportExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
        $rows = [];

        // Add header info
        $rows[] = ['Dispositions Report'];
        $rows[] = ['Date Range', $this->data['start_date'] . ' to ' . $this->data['end_date']];
        $rows[] = [];

        // Add institution headers
        $headers = ['Disposition'];
        foreach ($this->data['institutions'] as $institution) {
            $headers[] = $institution->institution_name;
        }
        $headers[] = 'Total';
        $rows[] = $headers;

        // Add data rows
        foreach ($this->data['report_data'] as $row) {
            $dataRow = [$row['disposition_name']];
            foreach ($this->data['institutions'] as $institution) {
                $dataRow[] = $row[$institution->id] ?? 0;
            }
            $dataRow[] = $row['total'];
            $rows[] = $dataRow;
        }

        // Add totals row
        $rows[] = [];
        $totalRow = ['Total Leads'];
        foreach ($this->data['institutions'] as $institution) {
            $institutionTotal = collect($this->data['report_data'])->sum(function($row) use ($institution) {
                return $row[$institution->id] ?? 0;
            });
            $totalRow[] = $institutionTotal;
        }
        $grandTotal = collect($this->data['report_data'])->sum('total');
        $totalRow[] = $grandTotal;
        $rows[] = $totalRow;

        return collect($rows);
    }

    public function headings(): array
    {
        return [];
    }

    public function title(): string
    {
        return 'Dispositions Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'size' => 14]],
            3 => ['font' => ['bold' => true]],
            'A:A' => ['alignment' => ['horizontal' => 'left']],
        ];
    }
}
