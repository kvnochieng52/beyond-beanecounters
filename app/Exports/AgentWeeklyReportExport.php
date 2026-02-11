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
use Carbon\Carbon;

class AgentWeeklyReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        $agentsCollection = collect($this->data['agents']);

        // Calculate totals
        $totals = [
            'agent_name' => 'TOTALS',
            'agent_code' => '',
            'average_dispositions' => 0,
            'ptp_count' => 0,
            'ptp_value' => 0,
            'total_collected' => 0,
            'mtd_collected' => 0,
        ];

        // Sum all numeric columns
        foreach ($agentsCollection as $agent) {
            $totals['average_dispositions'] += $agent['average_dispositions'];
            $totals['ptp_count'] += $agent['ptp_count'];
            $totals['ptp_value'] += $agent['ptp_value'];
            $totals['total_collected'] += $agent['total_collected'];
            $totals['mtd_collected'] += $agent['mtd_collected'];
        }

        // Calculate average for dispositions instead of sum
        if ($agentsCollection->count() > 0) {
            $totals['average_dispositions'] = round($totals['average_dispositions'] / $agentsCollection->count());
        }

        // Sum institution columns
        if (!empty($this->data['institutions'])) {
            foreach ($this->data['institutions'] as $instId => $instName) {
                $totals['inst_' . $instId] = 0;
                foreach ($agentsCollection as $agent) {
                    $totals['inst_' . $instId] += $agent['inst_' . $instId] ?? 0;
                }
            }
        }

        // Add the totals row with a marker
        $totals['is_total_row'] = true;

        // Create a new collection with all agents plus totals
        $exportCollection = $agentsCollection->values()->push($totals);

        return $exportCollection;
    }

    public function headings(): array
    {
        $headings = [
            'Agent Name',
            'Agent Code',
            'Avg. Dispositions/Day',
            'PTP Count',
            'PTP Value (KSH)',
            'Total Collected (KSH)',
            'MTD Collected (KSH)',
        ];

        // Add institution columns
        if (!empty($this->data['institutions'])) {
            foreach ($this->data['institutions'] as $institutionName) {
                $headings[] = $institutionName . ' (KSH)';
            }
        }

        return $headings;
    }

    public function map($agent): array
    {
        $row = [
            $agent['agent_name'],
            $agent['agent_code'],
            $agent['average_dispositions'],
            $agent['ptp_count'],
            $agent['ptp_value'], // Remove number_format for PTP Value
            $agent['total_collected'], // Remove number_format for Total Collected  
            $agent['mtd_collected'], // Remove number_format for MTD Collected
        ];

        // Add institution collections in the same order as headings
        if (!empty($this->data['institutions'])) {
            foreach ($this->data['institutions'] as $instId => $instName) {
                $amount = $agent['inst_' . $instId] ?? 0;
                $row[] = $amount; // Remove number_format for institution amounts
            }
        }

        return $row;
    }

    public function title(): string
    {
        return 'Weekly Report - ' . $this->data['period'];
    }

    public function styles(Worksheet $sheet)
    {
        $styles = [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 11,
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF1F4E78',
                    ],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                    'wrapText' => true,
                ],
            ],

            // All cells alignment
            'A:Z' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Number columns right alignment
            'C:Z' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],

            // Currency columns formatting (columns E onwards, starting from row 2)
            'E2:Z999' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];

        // Add totals row styling (last row) - extend to all columns
        $lastRow = $sheet->getHighestRow();
        $lastCol = $sheet->getHighestColumn();

        $styles['A' . $lastRow . ':' . $lastCol . $lastRow] = [
            'font' => [
                'bold' => true,
                'size' => 11,
                'color' => [
                    'argb' => 'FFFFFFFF',
                ],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'argb' => 'FF366092',
                ],
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_RIGHT,
                'vertical' => Alignment::VERTICAL_CENTER,
            ],
        ];

        return $styles;
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 25,  // Agent Name
            'B' => 15,  // Agent Code
            'C' => 20,  // Avg. Dispositions/Day
            'D' => 12,  // PTP Count
            'E' => 18,  // PTP Value
            'F' => 18,  // Total Collected
            'G' => 18,  // MTD Collected
        ];

        // Add width for institution columns
        if (!empty($this->data['institutions'])) {
            $col = 'H';
            foreach ($this->data['institutions'] as $instName) {
                $widths[$col] = 18;
                $col++;
            }
        }

        return $widths;
    }
}
