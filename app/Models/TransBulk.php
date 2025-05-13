<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransBulk extends Model
{
    use HasFactory;

    protected $fillable = ['csv_file', 'rules', 'created_by', 'updated_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
