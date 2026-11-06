<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $primaryKey = 'salary_id';
    protected $guarded = [];

    public function employee()
    {
        return $this->hasOne(Employee::class, 'emp_id', 'emp_id');
    }


}
