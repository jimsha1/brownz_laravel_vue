<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceUsedProduct extends Model
{
    protected $primaryKey = 'service_used_product_id';

    protected $guarded = ['service_used_product_id'];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id', 'service_id');
    }

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'item_id', 'item_id');
    }
}
