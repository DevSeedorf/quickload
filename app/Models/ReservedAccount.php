<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReservedAccount extends Model
{
    protected $fillable = [
        'user_id',
        'contract_code',
        'account_reference',
        'account_name',
        'currency_code',
        'customer_email',
        'customer_name',
        'bank_code',
        'bank_name',
        'account_number',
        'reservation_reference',
        'reserved_account_type',
        'status',
    ];
}
