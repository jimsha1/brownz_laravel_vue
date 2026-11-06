<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialYearOpeningBalance extends Model
{
    protected $guarded = [];

    protected $casts = [
        'debit' => 'decimal:2',
        'credit' => 'decimal:2',
    ];

    public function financialYear()
    {
        return $this->belongsTo(FinancialYear::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id', 'account_id');
    }
}
