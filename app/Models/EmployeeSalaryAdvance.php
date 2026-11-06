<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeSalaryAdvance extends Model
{
    protected $primaryKey = 'emp_salary_advance_id';
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class,'emp_id', 'emp_id');
    }
}
