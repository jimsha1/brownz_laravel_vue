<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollPayments extends Model
{
    protected $primaryKey = 'payroll_payment_id';
    protected $guarded = [];

    public function payroll()
    {
        return $this->hasOne(Payroll::class, 'payroll_id', 'payroll_id');
    }

    public function transactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }
}
