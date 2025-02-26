<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;


    public static function isValidPhoneNumber(string $number)
    {
        // Remove spaces and non-digit characters
        $number = preg_replace('/\D/', '', $number);

        // Check for:
        // - 10-digit number starting with '0'
        // - 13-digit number starting with '254'
        return (preg_match('/^0\d{9}$/', $number) || preg_match('/^254\d{9}$/', $number));
    }
}
