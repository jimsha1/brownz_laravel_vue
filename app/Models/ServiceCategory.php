<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class ServiceCategory extends Model
{
    use HasFactory;

    const CATEGORY_IMAGE_UPLOAD_PATH = 'services';
    protected $guarded = [];

    protected $primaryKey = 'category_id';

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['name_locale', 'tagline_locale', 'description_locale', 'long_description_locale'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'show_on_website' => 'boolean',
    ];

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

    /**
     * Get the name in current locale using accessor.
     *
     * @return string
     */
    public function getNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['name_arabic'])) ?
        //     $this->attributes['name_arabic'] :
        //     $this->attributes['name'] ?? null;
        return $this->attributes['name'] ?? null;
    }

    /**
     * Get the tagline in current locale using accessor.
     *
     * @return string
     */
    public function getTaglineLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['tagline_ar'])) ?
        //     $this->attributes['tagline_ar'] :
        //     $this->attributes['tagline_en'] ?? null;

        return $this->attributes['tagline_en'] ?? null;
    }

    /**
     * Get the description in current locale using accessor.
     *
     * @return string
     */
    public function getDescriptionLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['description_ar'])) ?
        //     $this->attributes['description_ar'] :
        //     $this->attributes['description_en'] ?? null;

        return $this->attributes['description_en'] ?? null;
    }

    /**
     * Get the long description in current locale using accessor.
     *
     * @return string
     */
    public function getLongDescriptionLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['long_description_ar'])) ?
        //     $this->attributes['long_description_ar'] :
        //     $this->attributes['long_description_en'] ?? null;

        return $this->attributes['long_description_en'] ?? null;
    }

    // public function serviceCategoriesDropdownJson()
    // {
    //     return self::select('name as text', 'category_id as id')->whereNotNull('parent_id')->get();
    // }

    public function parentCategoriesDropdownJson()
    {
        return self::select('name as text', 'category_id as id')->whereNull('parent_id')->where('is_active', 1 )->get();
    }

    public static function getSubcategories($parent_id)
    {
        return self::select('name as text', 'category_id as id')
            ->where('parent_id', $parent_id)->where('is_active', 1 )
            ->get();
    }

    /**
     * Get record for parent service category.
     */
    public function parent()
    {
        return $this->belongsTo(\App\Models\ServiceCategory::class, 'parent_id', 'category_id');
    }

    /**
     * Get records for child service categories to be shown on website
     */
    public function webChildren()
    {
        return $this->hasMany(\App\Models\ServiceCategory::class, 'parent_id', 'category_id')
            ->where('show_on_website', 1);
    }

    /**
     * Get record for created_by user.
     */
    public function createdUser()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'created_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * Get record for updated_by user.
     */
    public function updatedUser()
    {
        return $this->hasOne(\App\Models\User::class, 'id', 'updated_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }
}
