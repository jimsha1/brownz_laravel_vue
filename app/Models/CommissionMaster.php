<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommissionMaster extends Model
{
    protected $primaryKey = 'commission_master_id';
    protected $guarded = [];

    public function commissions()
    {
        return $this->hasMany(Commission::class, 'commission_master_id', 'commission_master_id');
    }
}
