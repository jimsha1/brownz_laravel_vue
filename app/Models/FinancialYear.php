<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class FinancialYear extends Model
{
    const STATUS_OPEN = 'open';

    const STATUS_CLOSED = 'closed';

    protected $guarded = [];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_current' => 'boolean',
        'net_profit_loss' => 'decimal:2',
        'closed_at' => 'datetime',
        'opened_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
        });

        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    public function closingTransactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'closing_transaction_group_id');
    }

    public function openingTransactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'opening_transaction_group_id');
    }

    public function closedByUser()
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function openedByUser()
    {
        return $this->belongsTo(User::class, 'opened_by');
    }

    public function openingBalances()
    {
        return $this->hasMany(FinancialYearOpeningBalance::class);
    }

    public function isClosed(): bool
    {
        return $this->status === self::STATUS_CLOSED;
    }

    public function containsDate(string $date): bool
    {
        return $date >= $this->start_date->toDateString()
            && $date <= $this->end_date->toDateString();
    }
}
