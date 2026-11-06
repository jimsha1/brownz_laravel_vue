<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookingEditLog extends Model
{
    protected $guarded = [];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'booking_id', 'booking_id');
    }

    public function editedUser()
    {
        return $this->hasOne(User::class, 'id', 'edited_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }
}
