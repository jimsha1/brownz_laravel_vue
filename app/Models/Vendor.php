<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Vendor extends Model
{
    use HasFactory;

    protected $primaryKey = 'vendor_id';
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

    public function journal()
    {
        return $this->belongsTo(Journal::class);
    }

    public function transactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchasePayment()
    {
        return $this->hasMany(PurchasePayment::class, 'vendor_id', 'vendor_id');
    }

    public function stockPurchases()
    {
        return $this->hasMany(StockPurchase::class, 'vendor_id', 'vendor_id');
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

    public function vendorsDropdownJson()
    {
        return self::select('name as text', 'vendor_id as id')
        ->where('is_active', 1)
        ->get();
    }
}
