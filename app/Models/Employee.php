<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $primaryKey = 'emp_id';
    protected $guarded = [];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function employeesDropdown($default = false)
    {
        $db_employees = self::join('users', 'employees.user_id', '=', 'users.id')
            ->where('employees.status', 'active')
            ->where('users.user_status', 'active')
            ->select('employees.emp_id', 'employees.emp_code', 'users.name', 'username', 'mobile')
            ->orderBy('users.name')
            ->get();

        $employees_array = [];
        foreach ($db_employees as $row) {
            $employees_array[$row->emp_id] = $row->name . ' (' . $row->emp_code . ')';
        }

        return voco_nvp_dropdown($employees_array, $default);
    }

    public function leave()
    {
        return $this->hasMany(LeaveManagement::class, 'emp_id', 'emp_id');
    }
    public function attendence()
    {
        return $this->hasMany(EmployeeAttendence::class, 'employee_id', 'emp_id');
    }
    public function salary()
    {
        return $this->hasOne(Salary::class, 'emp_id', 'emp_id');
    }
    public function currentAttendence()
    {
        return $this->hasOne(EmployeeAttendence::class, 'employee_id', 'emp_id')
            ->whereDate('date', now());
    }
    public function accountLedgers()
    {
        return $this->hasMany(
            EmployeeAccountLedger::class,
            'employee_id',
            'emp_id'
        );
    }

}
