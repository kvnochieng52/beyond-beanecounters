<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

class AgentPerformanceExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
                'Agent Performance Report',
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
            ],
            [
                'Overall Collection Rate',
                number_format($this->data['overall_collection_rate'], 2) . '%',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
            ],
            [
                '', '', '', '', '', '', '', '', '',
            ],
            [
                'Agent Performance Breakdown',
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
                'Total Leads',
                'Assigned Amount',
                'Collected Amount',
                'Collection Rate',
                'Closed Leads',
                'Overdue Cases',
                'Avg Days to Close',
                'Performance Rating',
            ],
        ];

        // Add agent data
        foreach ($this->data['agents'] as $agent) {
            // Calculate performance rating
            $performanceRating = $this->calculatePerformanceRating($agent);
            
            $rows[] = [
                $agent['name'],
                $agent['total_leads'],
                number_format($agent['assigned_amount'], 2),
                number_format($agent['collected_amount'], 2),
                number_format($agent['collection_rate'], 2) . '%',
                $agent['closed_leads'],
                $agent['overdue_cases'],
                number_format($agent['avg_days_to_close'], 1),
                $performanceRating,
            ];
        }

        return new Collection($rows);
    }

    /**
     * Calculate performance rating based on metrics
     */
    private function calculatePerformanceRating($agent)
    {
        // Simple rating algorithm based on collection rate
        $rate = $agent['collection_rate'];
        
        if ($rate >= 90) return 'Excellent';
        if ($rate >= 75) return 'Very Good';
        if ($rate >= 60) return 'Good';
        if ($rate >= 40) return 'Average';
        if ($rate >= 20) return 'Below Average';
        return 'Poor';
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
        ];
    }

    public function title(): string
    {
        return 'Agent Performance Report';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style the first row (headings)
            1 => ['font' => ['bold' => true]],
            // Style the agent performance breakdown header
            7 => ['font' => ['bold' => true]],
            // Style the agent columns header
            8 => ['font' => ['bold' => true]],
        ];
    }
}
