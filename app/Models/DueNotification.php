<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DueNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'message',
        'moment',
        'days',
        'is_active',
        'created_by',
        'updated_by'
    ];
}
