<?php

namespace App\Models;

use Facades\App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class BusinessConfig extends Model
{
    use HasFactory;

    protected $primaryKey = 'config_key';
    public $incrementing = false;

    public static function boot()
    {
        parent::boot();

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    /**
     * Get record for updated_by user.
     */
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by')
                    ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }    
}
