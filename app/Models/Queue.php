<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Queue extends Model
{
    use HasFactory;

    protected $fillable = [
        'text_id',
        'message',
        'status',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'phone',
        'api_response',

    ];
}
