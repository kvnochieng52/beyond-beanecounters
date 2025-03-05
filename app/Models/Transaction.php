<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'transaction_type',
        'amount',
        'description',
        'rule_id',
        'created_by',
        'updated_by'
    ];
}
