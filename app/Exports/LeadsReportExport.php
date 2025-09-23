<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class LeadsReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $leads;
    protected $filters;

    public function __construct($leads, $filters = [])
    {
        $this->leads = $leads;
        $this->filters = $filters;
    }

    public function collection()
    {
        return $this->leads;
    }

    public function headings(): array
    {
        return [
            'Ticket No',
            'Lead Title',
            'ID/Passport Number',
            'Account Number',
            'Gender',
            'Telephone',
            'Alternate Telephone',
            'Email',
            'Alternate Email',
            'Country',
            'Town',
            'Address',
            'Occupation',
            'Company Name',
            'Institution',
            'Amount',
            'Additional Charges',
            'Balance',
            'Waiver/Discount',
            'Currency',
            'Status',
            'Stage',
            'Priority',
            'Category',
            'Due Date',
            'Assigned Agent',
            'Created By',
            'Created Date',
            'Updated Date',
        ];
    }

    public function map($lead): array
    {
        return [
            $lead->id,
            $lead->title,
            $lead->id_passport_number,
            $lead->account_number,
            $lead->gender_name ?? 'N/A',
            $lead->telephone,
            $lead->alternate_telephone,
            $lead->email,
            $lead->alternate_email,
            $lead->country_name ?? 'N/A',
            $lead->town,
            $lead->address,
            $lead->occupation,
            $lead->company_name,
            $lead->institution_name ?? 'N/A',
            number_format($lead->amount ?? 0, 2),
            number_format($lead->additional_charges ?? 0, 2),
            number_format($lead->balance ?? 0, 2),
            number_format($lead->waiver_discount ?? 0, 2),
            $lead->currency_name ?? 'N/A',
            $lead->status_name ?? 'N/A',
            $lead->stage_name ?? 'N/A',
            $lead->priority_name ?? 'N/A',
            $lead->category_name ?? 'N/A',
            $lead->due_date ? date('d-m-Y', strtotime($lead->due_date)) : 'N/A',
            $lead->assigned_agent_name ?? 'N/A',
            $lead->created_by_name ?? 'N/A',
            $lead->created_at ? date('d-m-Y H:i:s', strtotime($lead->created_at)) : 'N/A',
            $lead->updated_at ? date('d-m-Y H:i:s', strtotime($lead->updated_at)) : 'N/A',
        ];
    }
}