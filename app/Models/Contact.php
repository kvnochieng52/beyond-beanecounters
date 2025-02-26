<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'is_active',
        'created_by',
        'updated_by'
    ];

    public function contactLists()
    {
        return $this->hasMany(ContactList::class);
    }
}
