<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncentiveMaster extends Model
{
    protected $primaryKey = 'incentive_master_id';
    protected $guarded = [];

    public function incentives()
    {
        return $this->hasMany(Incentive::class, 'incentive_master_id', 'incentive_master_id');
    }
}
