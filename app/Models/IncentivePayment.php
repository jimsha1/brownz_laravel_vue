<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncentivePayment extends Model
{
    protected $guarded = [];

    public function incentive()
    {
        return $this->belongsTo(Incentive::class, 'incentive_id', 'incentive_id');
    }

    public function transactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }
}
