<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AutoIncrementMaster extends Model
{
    use HasFactory;

    protected $primaryKey = 'auto_increment_id';
    public $timestamps = false;

    /* auto increment types */
    const AI_OUTLET_CODE = 1;
    const AI_ITEM_CODE = 2;
    const AI_SALON_BOOKING_CODE = 3;
    const AI_BEAUTICIAN_CODE = 4;
    const AI_SALES_ORDER_NUMBER = 5;
    const AI_WEBSITE_BOOKING_CODE = 6;
    const AI_PURCHASE_REF_NO = 7;
    const AI_EMPLOYEE_CODE = 8;
}
