<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PayrollMaster extends Model
{
    protected $primaryKey = 'payroll_master_id';

    protected $guarded = [];

    public function payrolls()
    {
        return $this->hasMany(Payroll::class, 'payroll_master_id', 'payroll_master_id');
    }
}
