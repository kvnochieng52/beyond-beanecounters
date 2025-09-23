<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStatus extends Model
{
    use HasFactory;

    const PENDING = 1;
    const PAID = 2;
    const PARTIALLY_PAID = 3;
    const OPEN = 7;
}
