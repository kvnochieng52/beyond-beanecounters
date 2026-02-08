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

class AgentPerformanceExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data['agents'];
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
        return 'Agent Performance ' . Carbon::parse($this->data['date'])->format('d M Y');
    }

    public function styles(Worksheet $sheet)
    {
        return [
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
