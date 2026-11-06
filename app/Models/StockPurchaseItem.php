<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockPurchaseItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_item_id';
    protected $guarded = [];

    public function stockItem()
    {
        return $this->belongsTo(StockItem::class, 'item_id', 'item_id');
    }

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
}
