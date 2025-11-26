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
use PhpOffice\PhpSpreadsheet\Style\Font;
use Carbon\Carbon;

class PaymentReportExport implements FromCollection, WithHeadings, WithMapping, WithTitle, WithStyles, WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['payments']);
    }

    public function headings(): array
    {
        return [
            'Date',
            'Ticket No',
            'Lead Name',
            'Institution',
            'Agent Name',
            'Agent Code',
            'Payment Amount (KSH)',
            'Lead Amount (KSH)',
            'Lead Balance (KSH)',
            'Transaction ID',
            'Payment Status',
            'Description',
            'Created At'
        ];
    }

    public function map($payment): array
    {
        return [
            Carbon::parse($payment->created_at)->format('d-m-Y'),
            '#' . $payment->ticket_number,
            $payment->lead_name ?? 'N/A',
            $payment->institution_name ?? 'N/A',
            $payment->agent_name ?? 'Unassigned',
            $payment->agent_code ?? '-',
            number_format($payment->amount, 2),
            number_format($payment->lead_amount ?? 0, 2),
            number_format($payment->lead_balance ?? 0, 2),
            $payment->transaction_id ?? '-',
            $payment->payment_status_name ?? 'Unknown',
            $payment->description ?? '-',
            Carbon::parse($payment->created_at)->format('d-m-Y H:i:s')
        ];
    }

    public function title(): string
    {
        $fromDate = $this->data['filters']['from_date'];
        $toDate = $this->data['filters']['to_date'];
        return "Payment Report ({$fromDate} to {$toDate})";
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => [
                        'argb' => 'FF4472C4',
                    ],
                ],
                'font' => [
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // All cells alignment
            'A:M' => [
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],

            // Number columns right alignment
            'G:I' => [
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Date
            'B' => 12,  // Ticket No
            'C' => 20,  // Lead Name
            'D' => 20,  // Institution
            'E' => 18,  // Agent Name
            'F' => 12,  // Agent Code
            'G' => 15,  // Payment Amount
            'H' => 15,  // Lead Amount
            'I' => 15,  // Lead Balance
            'J' => 18,  // Transaction ID
            'K' => 15,  // Payment Status
            'L' => 25,  // Description
            'M' => 18,  // Created At
        ];
    }
}