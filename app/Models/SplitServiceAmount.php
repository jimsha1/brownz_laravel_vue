<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SplitServiceAmount extends Model
{

    protected $primaryKey = 'split_service_amount_id';
    protected $guarded = [];
    protected $appends = ['line_type', 'line_type_label'];

    /**
     * Service vs Product line for commission reports.
     */
    public function getLineTypeAttribute(): string
    {
        if (! empty($this->booking_item_id) || ! empty($this->item_id)) {
            return 'product';
        }

        return 'service';
    }

    public function getLineTypeLabelAttribute(): string
    {
        return $this->line_type === 'product' ? 'Product' : 'Service';
    }

    public function booking()
    {
        return $this->hasOne(Booking::class, 'booking_id', 'booking_id');
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
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'beautician_id');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class, 'user_id', 'beautician_id');
    }
}
