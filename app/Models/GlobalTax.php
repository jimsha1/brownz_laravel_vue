<?php

namespace App\Models;

use Facades\App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GlobalTax extends Model
{
    use HasFactory;

    protected $primaryKey = 'tax_id';

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
        });
    }

    public function globalTaxesDropdown($default = false)
    {
        return voco_nvp_dropdown(self::pluck('name', 'tax')->toArray(), $default);
    }

    public function globalTaxesDropdownJson()
    {
        return self::select('name as text', 'tax as id')->where('is_active', 1)->get();
    }

    /**
     * Get record for created_by user.
     */
    public function createdUser()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }
}
