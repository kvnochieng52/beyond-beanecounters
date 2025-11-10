<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mtb extends Model
{
    use HasFactory;

    protected $fillable = [
        'lead_id',
        'amount_paid',
        'date_paid',
        'payment_channel',
        'description',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date_paid' => 'date',
        'amount_paid' => 'decimal:2'
    ];

    public function lead()
    {
        return $this->belongsTo(Lead::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
