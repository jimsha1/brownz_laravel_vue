<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookingItem extends Model
{
    use HasFactory;

    protected $primaryKey = 'booking_item_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['item_name_locale'];

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

    /**
     * Get the item name in current locale using accessor.
     *
     * @return string
     */
    public function getItemNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['item_name_ar'])) ?
        //     $this->attributes['item_name_ar'] :
        //     $this->attributes['item_name'];

        return $this->attributes['item_name'] ?? null;
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

    //
    public function beautician()
    {
        return $this->hasOne(\App\Models\Beautician::class, 'beautician_id', 'beautician_id')
            ->join('users', 'beauticians.beautician_id', '=', 'users.id')
            ->select(
                'beauticians.beautician_id',
                'beauticians.beautician_code',
                'users.name',
                'users.shift_id',
                // 'users.name_arabic',
                'users.profile_photo_path'
            );
    }
    //

    public function reCalculateLineTotal(\App\Models\Booking $booking)
    {
        $global_discount_percent = 0; //$booking->applied_discount_percent;

        // Apply discount first
        $this->discount_percent = $global_discount_percent;
        $this->discount_amount = round($this->selling_price * $global_discount_percent / 100, 2);

        // Price after discount
        $discounted_price = $this->selling_price - $this->discount_amount;

        // Apply tax on discounted price
        $this->tax_amount = round($discounted_price * $this->tax_percent / 100, 2);

        // Item total
        $this->line_total = round($discounted_price + $this->tax_amount, 2);
        $this->discount_percent = 0;
        $this->discount_amount = 0;
    }

    public function reCalculateLineTotalExistsDiscount(\App\Models\Booking $booking,$total_price)
    {
        $global_discount_percent = $booking->applied_discount_percent;

        // Apply discount first

        $percent = $this->tax_percent;

        $old_price = $total_price;

        $tax_value = ($old_price / 100) * $percent;
        $this->discount_percent = 0;
        $this->discount_amount = 0;

        // Price after discount
        $discounted_price = 0;

        // // Apply tax on discounted price
         $this->tax_amount =   $tax_value;

        // Item total
        $this->line_total = $total_price + $tax_value;

    }

}
