<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeadStage extends Model
{
    use HasFactory;

    const NEW_LEAD = 1;
}
