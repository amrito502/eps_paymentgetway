<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EpsPayment extends Model
{
      protected $fillable = ['invoice_id', 'amount', 'status', 'response'];

    protected $casts = [
        'response' => 'array',
    ];
}
