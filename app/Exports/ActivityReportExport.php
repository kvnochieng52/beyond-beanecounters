<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Carbon\Carbon;

class ActivityReportExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle
{
    protected $activities;
    protected $filters;

    public function __construct($activities, $filters = [])
    {
        $this->activities = $activities;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->activities;
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Activity Title',
            'Description',
            'Activity Type',
            'Priority',
            'Status',
            'Lead Title',
            'Institution',
            'Lead Amount',
            'Lead Balance',
            'Lead Waiver/Discount',
            'Assigned Agent',
            'Agent Code',
            'Department',
            'Created By',
            'Created Date',
            'Created Time',
            'Call Disposition',
            'PTP Check',
            'PTP Amount',
            'PTP Date',
            'PTP Retire Date',
            'Payment Check',
            'Payment Amount',
            'Payment Trans ID',
            'Payment Method',
            'Payment Date',
            'Text Status',
        ];
    }

    public function map($activity): array
    {
        return [
            $activity->lead_id,
            $activity->activity_title,
            $activity->description,
            $activity->activity_type_title,
            $activity->lead_priority_name,
            $activity->activity_status_name,
            $activity->lead_title,
            $activity->institution_name,
            $activity->lead_amount ? number_format($activity->lead_amount, 2) : '',
            $activity->lead_balance ? number_format($activity->lead_balance, 2) : '',
            $activity->lead_waiver_discount ? number_format($activity->lead_waiver_discount, 2) : '',
            $activity->assigned_agent_name,
            $activity->assigned_agent_code,
            $activity->department_name,
            $activity->created_by_name,
            $activity->created_date,
            $activity->created_time,
            $activity->call_disposition_name,
            $activity->ptp_check ? 'Yes' : 'No',
            $activity->act_ptp_amount ? number_format($activity->act_ptp_amount, 2) : '',
            $activity->act_ptp_date,
            $activity->act_ptp_retire_date,
            $activity->payment_check ? 'Yes' : 'No',
            $activity->act_payment_amount ? number_format($activity->act_payment_amount, 2) : '',
            $activity->act_payment_transid,
            $activity->method_name,
            $activity->created_at,

            //$this->getPaymentMethod($activity->act_payment_method),
            $activity->text_status_name,
        ];
    }

    private function getPaymentMethod($methodId)
    {
        // Assuming payment methods mapping - adjust as per your system
        $methods = [
            1 => 'Cash',
            2 => 'Bank Transfer',
            3 => 'Credit Card',
            4 => 'Mobile Money',
            5 => 'Cheque'
        ];

        return isset($methods[$methodId]) ? $methods[$methodId] : '';
    }

    public function styles(Worksheet $sheet)
    {
        // Style the header row
        $sheet->getStyle('A1:AA1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF']
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '366092']
            ],
            'alignment' => [
                'horizontal' => Alignment::HORIZONTAL_CENTER,
                'vertical' => Alignment::VERTICAL_CENTER
            ],
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => '000000']
                ]
            ]
        ]);

        // Style data rows
        $lastRow = $this->activities->count() + 1;
        $sheet->getStyle('A2:AA' . $lastRow)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color' => ['rgb' => 'CCCCCC']
                ]
            ],
            'alignment' => [
                'vertical' => Alignment::VERTICAL_TOP,
                'wrapText' => true
            ]
        ]);

        // Freeze the header row
        $sheet->freezePane('A2');

        // Auto-fit row heights
        for ($i = 1; $i <= $lastRow; $i++) {
            $sheet->getRowDimension($i)->setRowHeight(-1);
        }

        return [];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 8,   // ID
            'B' => 25,  // Activity Title
            'C' => 30,  // Description
            'D' => 20,  // Activity Type
            'E' => 15,  // Priority
            'F' => 15,  // Status
            'G' => 25,  // Lead Title
            'H' => 20,  // Institution
            'I' => 15,  // Lead Amount
            'J' => 15,  // Lead Balance
            'K' => 18,  // Lead Waiver/Discount
            'L' => 20,  // Assigned Agent
            'M' => 15,  // Agent Code
            'N' => 20,  // Department
            'O' => 20,  // Created By
            'P' => 12,  // Created Date
            'Q' => 12,  // Created Time
            'R' => 20,  // Call Disposition
            'S' => 12,  // PTP Check
            'T' => 15,  // PTP Amount
            'U' => 12,  // PTP Date
            'V' => 15,  // PTP Retire Date
            'W' => 15,  // Payment Check
            'X' => 15,  // Payment Amount
            'Y' => 20,  // Payment Trans ID
            'Z' => 15,  // Payment Method
            'AA' => 15,  // Text Status
        ];
    }

    public function title(): string
    {
        $fromDate = isset($this->filters['from_date']) ? $this->filters['from_date'] : '';
        $toDate = isset($this->filters['to_date']) ? $this->filters['to_date'] : '';

        return 'Activity Report ' . $fromDate . ' to ' . $toDate;
    }
}
