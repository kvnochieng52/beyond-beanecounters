<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReminderProcess extends Model
{
    use HasFactory;

    protected $fillable = [
        'process_type',
        'process_date',
        'start_time',
        'end_time',
        'total_customers',
        'successful_reminders',
        'failed_reminders',
        'status',
        'error_message',
        'processed_customers',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'process_date' => 'date',
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'processed_customers' => 'array',
    ];
}
