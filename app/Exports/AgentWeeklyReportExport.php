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
        $agentsCollection = $this->data['agents'];

        // Calculate totals
        $totals = [
            'agent_name' => 'TOTALS',
            'agent_code' => '',
            'calls_made' => 0,
            'ptp_count' => 0,
            'ptp_value' => 0,
            'total_collected' => 0,
            'mtd_collected' => 0,
        ];

        // Sum all numeric columns
        foreach ($agentsCollection as $agent) {
            $totals['calls_made'] += $agent['calls_made'];
            $totals['ptp_count'] += $agent['ptp_count'];
            $totals['ptp_value'] += $agent['ptp_value'];
            $totals['total_collected'] += $agent['total_collected'];
            $totals['mtd_collected'] += $agent['mtd_collected'];
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
        $agentsCollection->push($totals);

        return $agentsCollection;
    }

    public function headings(): array
    {
        $headings = [
            'Agent Name',
            'Agent Code',
            'Calls Made',
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
            $agent['calls_made'],
            $agent['ptp_count'],
            number_format($agent['ptp_value'], 2),
            number_format($agent['total_collected'], 2),
            number_format($agent['mtd_collected'], 2),
        ];

        // Add institution collections in the same order as headings
        if (!empty($this->data['institutions'])) {
            foreach ($this->data['institutions'] as $instId => $instName) {
                $amount = $agent['inst_' . $instId] ?? 0;
                $row[] = number_format($amount, 2);
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

        // Add totals row styling (last row)
        $lastRow = $sheet->getHighestRow();
        $styles['A' . $lastRow . ':Z' . $lastRow] = [
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
            'C' => 12,  // Calls Made
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
