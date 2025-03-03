<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TextStatus extends Model
{
    use HasFactory;

    const PENDING = 1;
    const QUEUED = 2;
    const SENT = 3;
    const FAILED = 4;
    const CANCELLED = 5;
}
