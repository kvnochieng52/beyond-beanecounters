<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactList extends Model
{
    use HasFactory;

    protected $fillable = [
        'contact_id',
        'name',
        'telephone',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function contact()
    {
        return $this->belongsTo(Contact::class);
    }
}
