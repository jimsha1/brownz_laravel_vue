<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Transaction extends Model
{
    // protected $fillable = ['transaction_group_id', 'journal_id', 'debit', 'credit', 'date', 'customer_id'];
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    public function transactionGroup() {
        return $this->belongsTo(TransactionGroup::class);
    }

    public function journal() {
        return $this->belongsTo(Journal::class);
    }

    public function scopeForReporting($query)
    {
        return $query->whereHas('transactionGroup', function ($groupQuery) {
            $groupQuery->where('is_audit_only', false);
        });
    }

    public function scopeForProfitLoss($query)
    {
        return $query->forReporting()->whereHas('transactionGroup', function ($groupQuery) {
            $groupQuery->where('description', 'not like', 'Financial Year Close%');
        });
    }

}
