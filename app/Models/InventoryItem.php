<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class InventoryItem extends Model
{
    // protected $fillable = ['name', 'sku', 'unit_cost', 'quantity', 'costing_method'];
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

    public function transactions() {
        return $this->hasMany(InventoryTransaction::class);
    }

    public function item() {
        return $this->hasOne(StockItem::class);
    }

    public function calculateCost($quantitySold) {
        if ($this->costing_method === 'weighted_average') {
            return $this->unit_cost * $quantitySold;
        } elseif ($this->costing_method === 'fifo') {
            $remaining = $quantitySold;
            $totalCost = 0;
            $purchases = $this->transactions()
                ->where('type', 'purchase')
                ->where('quantity', '>', 0)
                ->orderBy('date', 'asc')
                ->get();

            foreach ($purchases as $purchase) {
                if ($remaining <= 0) break;
                $qtyToUse = min($remaining, $purchase->quantity);
                $totalCost += $qtyToUse * $purchase->unit_cost;
                $remaining -= $qtyToUse;
            }
            return $totalCost;
        }
        return 0; // Fallback
    }
}
