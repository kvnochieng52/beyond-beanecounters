<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Text extends Model
{
    use HasFactory;

    protected $fillable = [
        'status',
        
    ];

    public static function query()
    {
        return  Text::select(
            'texts.*',
            'text_statuses.text_status_name',
            'text_statuses.color_code',
            'users.name as created_by_name'
        )
            ->leftJoin('text_statuses', 'texts.status', '=', 'text_statuses.id')
            ->leftJoin('users', 'texts.created_by', '=', 'users.id')
            ->orderBy('texts.id', 'DESC');
    }



    public static function getTextByID($textID)
    {
        return self::query()->where('texts.id', $textID)->first();
    }


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
