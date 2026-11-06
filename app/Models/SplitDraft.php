<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SplitDraft extends Model
{
    protected $primaryKey = 'split_draft_id';
    protected $guarded = [];

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
}
