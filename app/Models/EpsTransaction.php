<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpsTransaction extends Model
{
    protected $fillable = [
        'merchant_id', 'store_id', 'username',
        'trx_id', 'amount', 'status', 'reference'
    ];
}
