<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingServiceProduct extends Model
{
    protected $primaryKey = 'booking_service_product_id';

    protected $guarded = ['booking_service_product_id'];

    protected $casts = [
        'recipe_quantity' => 'float',
        'quantity' => 'float',
        'cost_price' => 'float',
        'pack_size' => 'float',
        'quantity_overridden' => 'boolean',
    ];

    public function bookingService()
    {
        return $this->belongsTo(BookingService::class, 'booking_service_id', 'booking_service_id');
    }

    public function item()
    {
        return $this->belongsTo(StockItem::class, 'item_id', 'item_id');
    }
}
