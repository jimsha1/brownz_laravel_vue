<?php

// function next_reference_code($auto_increment_id)
// {

//     // return \App\Repositories\AutoIncrementMasterRepository::getNextReferenceCode($auto_increment_id);

//     $lastBooking = \App\Models\Booking::latest('booking_code')->first();
//     $lastBookingCodeValue = $lastBooking ? $lastBooking->booking_code : null;

//     $autoBookingCode = \App\Models\AutoIncrementMaster::where('auto_code','AI_SALON_BOOKING_CODE')->first();

//     if ($lastBookingCodeValue) {
//         $numericPart = (int) substr($lastBookingCodeValue, strlen($autoBookingCode->auto_prefix));
//         $newBookingCodeValue = $numericPart + 1;

//     } else {
//         $newBookingCodeValue = $autoBookingCode->auto_increment_general_value;
//     }

//     $formattedBookingCode = str_pad($newBookingCodeValue, 4, '0', STR_PAD_LEFT);

//     $autoBookingCode->auto_increment_general_value = $newBookingCodeValue;
//     $autoBookingCode->save();

//     return $autoBookingCode->auto_prefix . $formattedBookingCode;

// }

function next_reference_code($auto_increment_id)
{
    if ($auto_increment_id === \App\Models\AutoIncrementMaster::AI_SALON_BOOKING_CODE) {
        return \App\Models\Booking::getNextBookingCode($auto_increment_id);
    }

    // Get the auto increment master configuration
    $autoIncrementMaster = \App\Models\AutoIncrementMaster::find($auto_increment_id);

    if (!$autoIncrementMaster) {
        throw new \Exception("Auto increment config not found for ID: {$auto_increment_id}");
    }

    // Map auto increment types to their models and field names
    $modelMapping = [
        \App\Models\AutoIncrementMaster::AI_OUTLET_CODE => [
            'model' => \App\Models\Outlet::class,
            'field' => 'outlet_code'
        ],
        \App\Models\AutoIncrementMaster::AI_ITEM_CODE => [
            'model' => \App\Models\StockItem::class,
            'field' => 'item_code'
        ],
        \App\Models\AutoIncrementMaster::AI_SALON_BOOKING_CODE => [
            'model' => \App\Models\Booking::class,
            'field' => 'booking_code'
        ],
        \App\Models\AutoIncrementMaster::AI_BEAUTICIAN_CODE => [
            'model' => \App\Models\Beautician::class,
            'field' => 'beautician_code'
        ],
        \App\Models\AutoIncrementMaster::AI_SALES_ORDER_NUMBER => [
            'model' => \App\Models\SalesOrder::class,
            'field' => 'order_number'
        ],
        \App\Models\AutoIncrementMaster::AI_PURCHASE_REF_NO => [
            'model' => \App\Models\StockPurchase::class,
            'field' => 'purchase_ref_no'
        ],
        \App\Models\AutoIncrementMaster::AI_WEBSITE_BOOKING_CODE => [
            'model' => \App\Models\Booking::class,
            'field' => 'booking_code'
        ],
    ];

    // Get model and field for this auto increment type
    if (!isset($modelMapping[$auto_increment_id])) {
        throw new \Exception("Model mapping not found for auto increment ID: {$auto_increment_id}");
    }

    $modelClass = $modelMapping[$auto_increment_id]['model'];
    $fieldName = $modelMapping[$auto_increment_id]['field'];

    // Check if model class exists
    if (!class_exists($modelClass)) {
        throw new \Exception("Model class not found: {$modelClass}");
    }

    // Get the last record with the prefix.
    // Include soft-deleted rows: unique indexes still hold those codes, so skipping
    // them makes next_reference_code reuse codes and fail on insert/update.
    $prefix = $autoIncrementMaster->auto_prefix;
    $query = $modelClass::query();
    if (in_array(\Illuminate\Database\Eloquent\SoftDeletes::class, class_uses_recursive($modelClass), true)) {
        $query->withTrashed();
    }
    $lastRecord = $query->where($fieldName, 'LIKE', $prefix . '%')
        ->orderByRaw("CAST(SUBSTRING({$fieldName}, LENGTH(?) + 1) AS UNSIGNED) DESC", [$prefix])
        ->first();

    $lastCodeValue = $lastRecord ? $lastRecord->$fieldName : null;

    if ($lastCodeValue) {
        // Extract numeric part and increment
        $numericPart = (int) substr($lastCodeValue, strlen($prefix));
        $newCodeValue = $numericPart + 1;
    } else {
        // Fallback if no record exists
        $newCodeValue = $autoIncrementMaster->auto_increment_general_value ?: 1;
    }

    // Pad the number to 4 digits: 0001, 0456, 1234, etc.
    $formattedCode = str_pad($newCodeValue, 4, '0', STR_PAD_LEFT);

    // Save new value back to the master table
    $autoIncrementMaster->auto_increment_general_value = $newCodeValue;
    $autoIncrementMaster->save();

    return $prefix . $formattedCode;
}


function barcode_unique()
{
    // @TODO - use a better number generator
    return hexdec(uniqid());
}

function coupon_code_unique()
{
    // @TODO - use a better number generator
    return uniqid();
}

function sms_otp()
{
    return mt_rand(100000, 999999);
}

function voco_uniqid($prefix = '', $length = 32)
{
    $string = $prefix . hexdec(uniqid());
    $remaining = $length - strlen($string);
    $maxRandomSize = 10;

    while ($remaining > 0) {
        $digits = min($remaining, $maxRandomSize);
        $string .= mt_rand((int) pow(10, $digits - 1), (int) (pow(10, $digits) - 1));

        $remaining -= $digits;
    }

    return str_replace('+', '', $string);
}
