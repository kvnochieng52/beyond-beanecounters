<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Carbon\Carbon;

class PaymentReportExport implements WithMultipleSheets
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function sheets(): array
    {
        return [
            new PaymentSheetExport($this->data),
            new MTDSheetExport($this->data),
        ];
    }
}

// Payment Sheet Export Class
class PaymentSheetExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
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
        return "Payments ({$fromDate} to {$toDate})";
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
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
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

            // All cells alignment
            'A:M' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

            // Number columns right alignment
            'G:I' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
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

// MTD Sheet Export Class
class MTDSheetExport implements \Maatwebsite\Excel\Concerns\FromCollection, \Maatwebsite\Excel\Concerns\WithHeadings, \Maatwebsite\Excel\Concerns\WithMapping, \Maatwebsite\Excel\Concerns\WithTitle, \Maatwebsite\Excel\Concerns\WithStyles, \Maatwebsite\Excel\Concerns\WithColumnWidths
{
    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return collect($this->data['mtd_records']);
    }

    public function headings(): array
    {
        return [
            'Date Paid',
            'Ticket No',
            'Lead Name',
            'Institution',
            'Agent Name',
            'Agent Code',
            'Amount Paid (KSH)',
            'Payment Channel',
            'Description',
            'Created By'
        ];
    }

    public function map($mtd): array
    {
        return [
            Carbon::parse($mtd->date_paid)->format('d-m-Y'),
            '#' . $mtd->ticket_number,
            $mtd->lead_name ?? 'N/A',
            $mtd->institution_name ?? 'N/A',
            $mtd->agent_name ?? 'Unassigned',
            $mtd->agent_code ?? '-',
            number_format($mtd->amount_paid, 2),
            $mtd->payment_channel ?? '-',
            $mtd->description ?? '-',
            $mtd->created_by_name ?? 'N/A'
        ];
    }

    public function title(): string
    {
        $fromDate = $this->data['filters']['from_date'];
        $toDate = $this->data['filters']['to_date'];
        return "MTD Records ({$fromDate} to {$toDate})";
    }

    public function styles(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $sheet)
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
                        'argb' => 'FF6F42C1',
                    ],
                ],
                'font' => [
                    'color' => [
                        'argb' => 'FFFFFFFF',
                    ],
                    'bold' => true,
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

            // All cells alignment
            'A:J' => [
                'alignment' => [
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],

            // Number columns right alignment
            'G' => [
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_RIGHT,
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 12,  // Date Paid
            'B' => 12,  // Ticket No
            'C' => 20,  // Lead Name
            'D' => 20,  // Institution
            'E' => 18,  // Agent Name
            'F' => 12,  // Agent Code
            'G' => 15,  // Amount Paid
            'H' => 18,  // Payment Channel
            'I' => 25,  // Description
            'J' => 15,  // Created By
        ];
    }
}