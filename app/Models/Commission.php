<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commission extends Model
{
    protected $primaryKey = 'commission_id';
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class,'emp_id', 'emp_id');
    }

    public function payments()
    {
        return $this->hasMany(CommissionPayment::class, 'commission_id', 'commission_id');
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
