<?php

namespace App\Exports;

use App\Models\Queue;
use App\Models\TextStatus;
use App\Models\User;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Http\Request;

class QueuesExport implements FromQuery, WithHeadings, WithMapping, ShouldAutoSize
{
    use Exportable;

    protected $filters;
    protected $textID;

    public function __construct($filters, $textID)
    {
        $this->filters = $filters;
        $this->textID = $textID;
    }

    public function query()
    {


        $query = Queue::query()
            ->leftJoin('users', 'queues.created_by', '=', 'users.id')
            ->leftJoin('text_statuses', 'queues.status', '=', 'text_statuses.id')
            ->select(
                'queues.id',
                'queues.message',
                'queues.status',
                'text_statuses.text_status_name as status_name',
                'users.name as created_by',
                'queues.created_at'
            )


            ->where('queues.text_id', $this->textID);

        if (!empty($this->filters)) {
            $query->whereIn('queues.status', $this->filters);
        }

        return $query;
    }

    public function headings(): array
    {
        return ["ID", "Message", "Status ID", "Status", "Agent", "Date"];
    }

    public function map($queue): array
    {
        return [
            $queue->id,
            $queue->message,
            $queue->status,
            $queue->status_name,
            $queue->created_by,
            $queue->created_at->format('Y-m-d H:i:s'),
        ];
    }
}
