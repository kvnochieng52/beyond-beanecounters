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
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Illuminate\Support\Collection;

class AdminAgentPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $reportData;

    public function __construct($data)
    {
        // Handle both full data array and just report_data
        if (is_array($data) && isset($data['report_data'])) {
            $this->reportData = $data['report_data'];
        } else {
            $this->reportData = $data;
        }
    }

    public function collection()
    {
        // Convert report data to collection and add totals row
        $collection = collect($this->reportData);

        // Calculate totals
        $totals = [
            'agent_name' => 'TOTALS',
            'leads_worked' => $collection->sum('leads_worked'),
            'negotiation_in_progress' => $collection->sum('negotiation_in_progress'),
            'ptp_created_today' => $collection->sum('ptp_created_today'),
            'good_leads' => $collection->sum('good_leads'),
            'right_party_ptp_count' => $collection->sum('right_party_ptp_count'),
            'right_party_ptp_value' => $collection->sum('right_party_ptp_value'),
            'ptp_month_count' => $collection->sum('ptp_month_count'),
            'ptp_month_value' => $collection->sum('ptp_month_value'),
            'mtd_today_count' => $collection->sum('mtd_today_count'),
            'mtd_today_value' => $collection->sum('mtd_today_value'),
            'mtd_month_value' => $collection->sum('mtd_month_value'),
            'payments_posted_value' => $collection->sum('payments_posted_value'),
            'is_totals_row' => true
        ];

        $collection->push($totals);

        return $collection;
    }

    public function headings(): array
    {
        return [
            'Agent Name',
            'Leads Worked',
            'Negotiation in Progress',
            'PTP Created (Today)',
            'Good Leads',
            'Right Party PTP (Count)',
            'Right Party PTP (Value KSH)',
            'Monthly PTP (Count)',
            'Monthly PTP (Value KSH)',
            'MTD Today (Count)',
            'MTD Today (Value KSH)',
            'MTD Monthly (Value KSH)',
            'Payments Posted (Month) KSH'
        ];
    }

    public function map($row): array
    {
        return [
            $row['agent_name'],
            $row['leads_worked'] ?? 0,
            $row['negotiation_in_progress'] ?? 0,
            $row['ptp_created_today'] ?? 0,
            $row['good_leads'] ?? 0,
            $row['right_party_ptp_count'] ?? 0,
            $row['right_party_ptp_value'] ?? 0,
            $row['ptp_month_count'] ?? 0,
            $row['ptp_month_value'] ?? 0,
            $row['mtd_today_count'] ?? 0,
            $row['mtd_today_value'] ?? 0,
            $row['mtd_month_value'] ?? 0,
            $row['payments_posted_value'] ?? 0
        ];
    }

    public function title(): string
    {
        return 'Admin Agent Performance';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 22,
            'B' => 15,
            'C' => 18,
            'D' => 18,
            'E' => 15,
            'F' => 20,
            'G' => 22,
            'H' => 18,
            'I' => 22,
            'J' => 18,
            'K' => 22,
            'L' => 22,
            'M' => 26
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $sheet->getStyle('1:1')->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '1f4e78'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER,
                'wrapText' => true
            ]
        ]);

        // Find the row count to identify the totals row
        $rowCount = $sheet->getHighestRow();

        // Style the totals row
        $sheet->getStyle($rowCount . ':' . $rowCount)->applyFromArray([
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '366092'],
            ],
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
                'size' => 11
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ]
        ]);

        // Right-align numeric columns (except agent name)
        for ($row = 2; $row <= $rowCount; $row++) {
            for ($col = 'B'; $col <= 'M'; $col++) {
                $sheet->getStyle($col . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
            }
        }

        // Format currency columns
        $currencyCols = ['G', 'I', 'K', 'L', 'M'];
        for ($row = 2; $row <= $rowCount; $row++) {
            foreach ($currencyCols as $col) {
                $sheet->getStyle($col . $row)->getNumberFormat()->setFormatCode('#,##0.00');
            }
        }

        // Set minimum row height for header
        $sheet->getRowDimension(1)->setRowHeight(30);

        return [];
    }
}
