<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAccountLedger extends Model
{
    protected $fillable = [
        'employee_id',
        'ledger_id',
        'ledger_type',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'ledger_id', 'account_id');
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class, 'employee_id', 'emp_id');
    }
}
