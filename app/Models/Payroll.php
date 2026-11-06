<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payroll extends Model
{
    protected $primaryKey = 'payroll_id';
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class,'emp_id', 'emp_id');
    }

    public function master()
    {
        return $this->belongsTo(PayrollMaster::class, 'payroll_master_id', 'payroll_master_id');
    }

    public function payments()
    {
        return $this->hasMany(PayrollPayments::class, 'payroll_id', 'payroll_id');
    }

    public function paymentVouchers()
    {
        return $this->hasMany(PayrollPayments::class, 'payroll_id', 'payroll_id')
            ->whereNotNull('transaction_group_id');
    }
}
