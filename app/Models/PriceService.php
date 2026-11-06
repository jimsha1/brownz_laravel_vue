<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceService extends Model
{
    use HasFactory;

    protected $primaryKey = 'price_service_id';

    protected $fillable = ['service_id','shift_id', 'duration', 'price','service_type_id']; 

    public function shift(){
    	return $this->belongsTo(\App\Models\Shift::class, 'shift_id', 'shift_id');
	}

	public function serviceType(){
    	return $this->belongsTo(\App\Models\ServiceTypeManagement::class, 'service_type_id', 'service_type_management_id');
	}
}
