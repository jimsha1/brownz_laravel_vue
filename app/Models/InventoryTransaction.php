<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventoryTransaction extends Model
{
    // protected $fillable = ['inventory_item_id', 'transaction_group_id', 'type', 'quantity', 'unit_cost', 'date'];
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

    public function item() {
        return $this->belongsTo(InventoryItem::class, 'inventory_item_id');
    }

    public function transactionGroup() {
        return $this->belongsTo(TransactionGroup::class);
    }
}
