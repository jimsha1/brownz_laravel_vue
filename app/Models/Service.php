<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Service extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_id';
    protected $guarded = [];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['service_name_locale', 'short_description_locale'];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'allow_online_booking' => 'boolean',
        'show_on_website' => 'boolean',
        'is_active' => 'boolean',
    ];

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;

            $entity->outlet_id = $entity->outlet_id ?? session_outlet_id();
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    /**
     * Get the service name in current locale using accessor.
     *
     * @return string
     */
    public function getServiceNameLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['name_arabic'])) ?
        //     $this->attributes['name_arabic'] :
        //     $this->attributes['name'] ?? null;

        return $this->attributes['name'] ?? null;
    }

    /**
     * Get the short description in current locale using accessor.
     *
     * @return string
     */
    public function getShortDescriptionLocaleAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['short_description_ar'])) ?
        //     $this->attributes['short_description_ar'] :
        //     $this->attributes['short_description_en'] ?? null;

        return $this->attributes['short_description_en'] ?? null;
    }

    public function servicesDropdown($default = false)
    {
        return voco_nvp_dropdown(self::pluck('name', 'service_id')->toArray(), $default);
    }

    public function servicesDropdownJson()
    {
        return self::select('name as text', 'service_id as id')->get();
    }

    public function durationStaticData()
    {
        return [10, 15, 20, 30, 45, 60, 75, 90, 105, 120, 150, 180, 210, 240, 300, 360, 420];
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

    /**
     * Get record for service category.
     */
    public function category()
    {
        return $this->belongsTo(\App\Models\ServiceCategory::class, 'category_id', 'category_id');
    }

    public function parent()
    {
        return $this->belongsTo(ServiceCategory::class, 'parent_id');
    }
    public function children()
    {
        return $this->hasMany(ServiceCategory::class, 'parent_id');
    }

    public function beauticians()
    {
        return $this->hasManyThrough(
            \App\Models\Beautician::class,
            \App\Models\BeauticianService::class,
            'service_id', // Foreign key on users table...
            'beautician_id', // Foreign key on posts table...
            'service_id', // Local key on countries table...
            'beautician_id' // Local key on users table...
        )
        ->join('users', 'beauticians.beautician_id', '=', 'users.id')
        ->where('beautician_services.status', 1) //1 = Active
        ->where('beauticians.status', 'A') //A = Active
        ->whereExists(function ($q) {
            $q->select(\DB::raw(1))
                ->from('employees')
                ->whereColumn('employees.user_id', 'beauticians.beautician_id')
                ->where('employees.status', 'active');
        })
        ->where('users.user_status', 'active')
        ->select(
            'beauticians.beautician_id',
            'beauticians.beautician_code',
            'beautician_services.*',
            'users.name',
            'users.shift_id',
            'users.profile_photo_path'
        );
    }

    // public function serviceDurations(){
    //     return $this->hasMany(\App\Models\ServiceDuration::class,'service_id','service_id');
    // }
    // public function serviceShifts(){
    //     return $this->hasMany(\App\Models\ServiceShift::class,'service_id','service_id');
    // }
    // public function serviceTypes(){
    //     return $this->hasMany(\App\Models\ServiceType::class,'service_id','service_id');
    // }
    // public function servicePriceCombinations(){
    //     return $this->hasMany(\App\Models\ServicePriceCombination::class,'service_id','service_id');
    // }

    public function servicePrices(){
        return $this->hasMany(\App\Models\PriceService::class,'service_id','service_id');
    }

    public function usedProducts()
    {
        return $this->hasMany(ServiceUsedProduct::class, 'service_id', 'service_id');
    }
}
