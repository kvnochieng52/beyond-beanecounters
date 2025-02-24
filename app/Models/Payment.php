<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;



    public static function query()
    {
        $query = self::select([
            'payments.*',
            'payment_statuses.payment_status_name',
            'payment_statuses.color_code',
            'CREATED_BY_JOIN.name as agent_name',
        ])
            ->leftJoin('payment_statuses', 'payments.status_id', 'payment_statuses.id')
            ->leftJoin('users AS CREATED_BY_JOIN', 'payments.created_by', '=', 'CREATED_BY_JOIN.id');

        return $query;
    }


    public static function getLeadPayments($leadID)
    {
        $query = self::query()
            ->where('payments.lead_id', $leadID)
            ->orderBy('id', 'DESC')
            ->paginate(2);


        return $query;
    }
}
