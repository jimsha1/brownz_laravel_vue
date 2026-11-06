<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockVoucherEditLog extends Model
{
    protected $guarded = [];

    public function transactionGroup()
    {
        return $this->belongsTo(TransactionGroup::class, 'transaction_group_id');
    }

    public function editedUser()
    {
        return $this->hasOne(User::class, 'id', 'edited_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }
}
