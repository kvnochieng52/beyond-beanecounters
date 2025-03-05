<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;
    const PENALTY = 1;
    const PAYMENT = 2;
    const DISCOUNT = 3;
}
