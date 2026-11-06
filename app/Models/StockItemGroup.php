<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class StockItemGroup extends Model
{
    use HasFactory;

    protected $primaryKey = 'item_group_id';
    protected $guarded = [];

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

    public function stockItemGroupsDropdownJson()
    {
        return self::select('group_name as text', 'item_group_id as id')->get();
    }

    public function parentStockItemGroupsDropdownJson()
    {
        return self::select('group_name as text', 'item_group_id as id')->whereNull('parent_id')->where('is_active', 1)->get();
    }

    public static function getStockItemSubGroups($parent_id)
    {
        return self::select('group_name as text', 'item_group_id as id')
            ->where('parent_id', $parent_id)
            ->get();
    }

    /**
     * Get record for parent service category.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\StockItemGroup::class, 'parent_id', 'item_group_id');
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
}
