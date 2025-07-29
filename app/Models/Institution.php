<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Institution extends Model
{
    use HasFactory;

    protected $fillable = [
        'institution_name',
        'address',
        'email',
        'website',
        'telephone',
        'contact_person',
        'is_active',
        'description',
        'created_by',
        'updated_by',
        'how_to_pay_instructions'
    ];
}
