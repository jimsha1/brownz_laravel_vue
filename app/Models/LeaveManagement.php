<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class LeaveManagement extends Model
{
    use HasFactory;

    protected $primaryKey = 'leave_id';
    protected $table = 'leave_managements';
    protected $guarded = [];


    /* leave types */
    const LEAVE_TYPE_FULL = 'full';
    const LEAVE_TYPE_FIRST_HALF = 'first_half';
    const LEAVE_TYPE_SECOND_HALF = 'second_half';

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

    public function beautician()
    {
        return $this->hasOne(\App\Models\Beautician::class, 'beautician_id', 'beautician_id')
                    ->select( 'beautician_id','beautician_code');
    }
    public function details()
    {
        return $this->hasOne(User::class, 'id', 'beautician_id')
                    ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }


}
