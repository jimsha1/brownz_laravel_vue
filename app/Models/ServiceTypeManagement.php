<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ServiceTypeManagement extends Model
{
    use HasFactory;

    protected $primaryKey = 'service_type_management_id';
    protected $table = 'service_type_managements';

    protected $appends = ['service_type_arabic'];

    public function getServiceTypeArabicAttribute()
    {
        // return (app()->getLocale() === 'ar' && !empty($this->attributes['name_arabic'])) ?
        //     $this->attributes['name_arabic'] :
        //     $this->attributes['service_type_name'] ?? null;

        return $this->attributes['service_type_name'] ?? null;
    }

    public function priceServices(){
    	return $this->hasMany(\App\Models\PriceService::class,'service_type_id','service_type_management_id');
	}
}
