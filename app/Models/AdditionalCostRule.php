<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCostRule extends Model
{
    use HasFactory;


    protected $fillable = [
        'title',
        'type',
        'cost_type',
        'value',
        'apply_due_date',
        'days',
        'description',
        'is_active',
        'created_by',
    ];
}
