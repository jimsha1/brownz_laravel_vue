<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BookingService extends Model
{
    use HasFactory;

    /* service types */
    const SERVICE_TYPE_HOME = 'Home';
    const SERVICE_TYPE_HOTEL = 'Hotel';
    const SERVICE_TYPE_SALON = 'Salon';

    /* statuses */
    const STATUS_OPEN = 'open';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REJECTED = 'rejected';
    const STATUS_REOPENED = 'reopened';
    const STATUS_REFUNDED = 'refunded';
    const STATUS_DRAFT = 'draft';

    protected $primaryKey = 'booking_service_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['category_name_locale', 'service_name_locale'];

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
     * Get the category name in current locale using accessor.
     *
     * @return string
     */
    public function getCategoryNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['category_name_ar'])) ?
        //     $this->attributes['category_name_ar'] :
        //     $this->attributes['category_name'];

            return $this->attributes['category_name'] ?? null;
    }

    /**
     * Get the service name in current locale using accessor.
     *
     * @return string
     */
    public function getServiceNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['service_name_ar'])) ?
        //     $this->attributes['service_name_ar'] :
        //     $this->attributes['service_name'];

        return $this->attributes['service_name'] ?? null;
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

    public function service()
    {
        return $this->hasOne(\App\Models\Service::class, 'service_id', 'service_id');
    }

    public function usedProducts()
    {
        return $this->hasMany(BookingServiceProduct::class, 'booking_service_id', 'booking_service_id');
    }

    public function splitDraft()
    {
        return $this->hasMany(\App\Models\SplitDraft::class, 'booking_service_id', 'booking_service_id');
    }

    public function splitAmounts()
    {
        return $this->hasMany(\App\Models\SplitServiceAmount::class, 'booking_service_id', 'booking_service_id');
    }

    public function beautician()
    {
        return $this->hasOne(\App\Models\Beautician::class, 'beautician_id', 'beautician_id')
            ->join('users', 'beauticians.beautician_id', '=', 'users.id')
            ->select(
                'beauticians.beautician_id',
                'beauticians.beautician_code',
                'users.name',
                'users.shift_id',
                'users.profile_photo_path'
            );
    }

    public function reCalculateLineTotal(\App\Models\Booking $booking)
    {
        $global_discount_percent = $booking->applied_discount_percent;

        // Apply discount first
        $percent = $this->tax_percent;
        $old_price = $this->service_booking_price;

        $tax_value = ($old_price / 100) * $percent;
        $this->discount_percent = 0;
        $this->discount_amount = 0;

        // Price after discount
        $discounted_price = 0;

        // // Apply tax on discounted price
        $this->tax_amount =   $tax_value;

        // Item total
        $this->line_total = $this->service_booking_price + $tax_value;
    }
    public function reCalculateLineTotalExistsDiscount(\App\Models\Booking $booking, $total_price)
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


    public function user()
    {
        return $this->hasOne(User::class,'id', 'beautician_id');
    }

    public function booking()
    {
        return $this->hasOne(Booking::class,'booking_id', 'booking_id');
    }
}
