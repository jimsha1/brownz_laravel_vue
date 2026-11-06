<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionPayment extends Model
{
    protected $guarded = [];

    public function commission()
    {
        return $this->belongsTo(Commission::class, 'commission_id', 'commission_id');
    }

    public function transactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }
}
