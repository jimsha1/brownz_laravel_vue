<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmployeeAttendence extends Model
{
    protected $primaryKey = 'attendence_id';
    protected $guarded = [];


    public function employee()
    {
        return $this->hasOne(Employee::class,'emp_code', 'employee_code');
    }

    public function leave()
    {
        return $this->hasOne(LeaveManagement::class,'emp_id', 'employee_id');
    }

}

