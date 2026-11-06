<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockPurchaseEditLog extends Model
{
    protected $guarded = [];

    public function purchase()
    {
        return $this->belongsTo(StockPurchase::class, 'purchase_id', 'purchase_id');
    }

    public function editedUser()
    {
        return $this->hasOne(User::class, 'id', 'edited_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }
}
