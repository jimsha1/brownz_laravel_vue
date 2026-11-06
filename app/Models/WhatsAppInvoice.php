<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WhatsAppInvoice extends Model
{
    protected $guarded = [];
    protected $table = 'whatsapp_invoices';

    public function booking()
    {
        return $this->hasOne(Booking::class, 'booking_id', 'booking_id');
    }
}
