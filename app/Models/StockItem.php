<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_id';
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['item_name_locale', 'short_description_locale'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_sales_item' => 'boolean',
        'commission_status' => 'boolean',
        'pack_size' => 'float',
        'current_stock' => 'float',
    ];

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;

            // Generate item code upon creation if not provided
            if (empty($entity->item_code)) {
                $entity->item_code = next_reference_code(\App\Models\AutoIncrementMaster::AI_ITEM_CODE);
            }
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    /**
     * Get the item name in current locale using accessor.
     *
     * @return string
     */
    public function getItemNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['name_arabic'])) ?
        //     $this->attributes['name_arabic'] :
        //     $this->attributes['name'] ?? null;

        return $this->attributes['item_name'] ?? null;
    }

    /**
     * Get the short description in current locale using accessor.
     *
     * @return string
     */
    public function getShortDescriptionLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['short_description_ar'])) ?
        //     $this->attributes['short_description_ar'] :
        //     $this->attributes['short_description_en'] ?? null;

        return $this->attributes['short_description_en'] ?? null;
    }

    /**
     * Get record for created_by user.
     */
    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * Get record for updated_by user.
     */
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * Get record for Item Group.
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\StockItemGroup::class, 'item_group_id', 'item_group_id');
    }

    public function parent()
    {
        return $this->belongsTo(StockItemGroup::class, 'parent_item_group_id');
    }
    public function children()
    {
        return $this->hasMany(StockItemGroup::class, 'parent_item_group_id');
    }

    public function brand()
    {
        return $this->belongsTo(\App\Models\StockBrand::class, 'brand_id', 'brand_id');
    }

    public function stockUom()
    {
        return $this->belongsTo(UnitOfMeasurement::class, 'stock_uom_id', 'uom_id');
    }

    /**
     * Fewer than this many full packs is treated as low stock.
     */
    public const LOW_STOCK_PACKS = 3;

    /**
     * Items that have at least one full pack on hand.
     */
    public function scopeAvailableForSale($query)
    {
        return $query->whereRaw('current_stock >= '.self::packSizeSql());
    }

    public static function packSizeSql(string $column = 'pack_size'): string
    {
        return "IF(COALESCE({$column}, 0) <= 0, 1, {$column})";
    }

    /**
     * out: cannot sell one pack, low: under LOW_STOCK_PACKS, ok: otherwise.
     */
    public function stockLevelKey(): string
    {
        $pack = $this->packSize();
        $qty = (float) $this->current_stock;

        if ($qty + 0.0001 < $pack) {
            return 'out';
        }

        if ($qty + 0.0001 < ($pack * self::LOW_STOCK_PACKS)) {
            return 'low';
        }

        return 'ok';
    }

    public function packsAvailable(): float
    {
        return round(((float) $this->current_stock) / $this->packSize(), 2);
    }

    public function inventoryValue(): float
    {
        return round(((float) $this->current_stock) * ((float) ($this->cost_price ?? 0)) / $this->packSize(), 2);
    }

    public function stockMovements()
    {
        return $this->hasMany(StockMovement::class, 'item_id', 'item_id');
    }

    public static function normalizePackSize($packSize): float
    {
        $size = round((float) $packSize, 3);

        return $size > 0 ? $size : 1.0;
    }

    public function packSize(): float
    {
        return self::normalizePackSize($this->pack_size ?? 1);
    }

    /**
     * Convert a purchase or sale quantity (packs) into the stock unit.
     */
    public function baseQuantity($packQuantity): float
    {
        return round(((float) $packQuantity) * $this->packSize(), 3);
    }

    /**
     * Inventory money value: on-hand stock is in the stock unit, cost is per pack.
     */
    public static function inventoryValueSql(string $qty = 'current_stock', string $cost = 'cost_price', string $pack = 'pack_size'): string
    {
        return "({$qty} * {$cost} / IF({$pack} IS NULL OR {$pack} <= 0, 1, {$pack}))";
    }

    public static function sqlAdd(string $column, float $delta)
    {
        $deltaSql = number_format($delta, 3, '.', '');
        if ($delta >= 0) {
            return DB::raw($column.' + '.$deltaSql);
        }

        return DB::raw('GREATEST(0, '.$column.' - '.number_format(abs($delta), 3, '.', '').')');
    }

    /**
     * Keep the number of packs the same when pack size changes.
     * 2 packs at size 1 become 1000 when the pack size becomes 500.
     */
    public function rescaleStockUnits(float $oldPack, float $newPack): void
    {
        $oldPack = self::normalizePackSize($oldPack);
        $newPack = self::normalizePackSize($newPack);
        $factor = $newPack / $oldPack;
        $factorSql = number_format($factor, 6, '.', '');

        $this->current_stock = round(((float) $this->current_stock) * $factor, 3);

        if (! $this->item_id) {
            return;
        }

        StockItemPrice::where('item_id', $this->item_id)->update([
            'quantity' => DB::raw('ROUND(quantity * '.$factorSql.', 3)'),
        ]);

        StockMovement::where('item_id', $this->item_id)->update([
            'stock_in' => DB::raw('ROUND(stock_in * '.$factorSql.', 3)'),
            'stock_out' => DB::raw('ROUND(stock_out * '.$factorSql.', 3)'),
        ]);
    }

    public function stockItemDropdownJson()
    {
        return self::query()
            ->leftJoin('unit_of_measurements', 'unit_of_measurements.uom_id', '=', 'stock_items.stock_uom_id')
            ->where('stock_items.is_active', 1)
            ->select(
                'stock_items.item_name as text',
                'stock_items.item_id as id',
                'stock_items.cost_price as cost_price',
                'stock_items.tax_percent as tax',
                'stock_items.hsn_code as hsn_code',
                'stock_items.pack_size as pack_size',
                'unit_of_measurements.uom_name as uom_name'
            )
            ->get();
    }

    public function itemPrice()
    {
        return $this->hasOne(StockItemPrice::class, 'item_id', 'item_id');
    }

    
}
