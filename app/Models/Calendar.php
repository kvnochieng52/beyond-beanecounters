<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Calendar extends Model
{
    use HasFactory;


    protected $fillable = [
        'calendar_title',
        'start_date_time',
        'due_date_time',
        'description',
        'priority_id',
        'lead_id',
        'assigned_team_id',
        'assigned_user_id',
        'status_id',
        'created_by',
        'updated_by'
    ];
}
