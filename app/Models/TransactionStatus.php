<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionStatus extends Model
{
    use HasFactory;


    const PENDING = 1;
    const PAID = 2;
    const POSTED = 3;
    const CANCELLED = 4;
    const FAILED = 5;
}
