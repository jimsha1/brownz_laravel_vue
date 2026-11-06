<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Incentive extends Model
{
    protected $primaryKey = 'incentive_id';
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class,'emp_id', 'emp_id');
    }

    public function payments()
    {
        return $this->hasMany(IncentivePayment::class, 'incentive_id', 'incentive_id');
    }

    public function paymentVoucher()
    {
        return $this->belongsTo(TransactionGroup::class, 'payment_transaction_group_id');
    }

    public function accrualVoucher()
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }
}
