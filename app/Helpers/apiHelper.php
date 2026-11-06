<?php

function apiResponse($errorcode, $errormessage, $data = null, $http_status_code = 200)
{
    $response = [
        'errorcode' => $errorcode,
        'errormessage' => $errormessage,
    ];

    if ($data !== null) {
        $response['data'] = $data;
    }

    return response()->json($response, $http_status_code);
}

function generatePaginationData($result)
{
    return [
        'total' => $result->total(),
        'per_page' => $result->perPage(),
        'current_page' => $result->currentPage(),
        'last_page' => $result->lastPage(),
        'from' => $result->firstItem(),
        'to' => $result->lastItem()
    ];
}

function successResponse($data, $message, $errorcode, $code = 200)
{
    return response()->json([
        'status' => 'Success',
        'errormessage' => $message,
        'errorcode' => $errorcode,
        'result' => $data
    ], $code);
}

function calculateDistance($lat1, $lon1, $lat2, $lon2)
{
    $earthRadius = 6371000; // Earth radius in meters

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat / 2) * sin($dLat / 2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon / 2) * sin($dLon / 2);

    $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

    $distance = $earthRadius * $c; // Distance in meters

    return $distance;
}


function errorResponse($message, $errorcode, $data = null)
{
    return response()->json([
        'status' => 'Error',
        'errormessage' => $message,
        'errorcode' => $errorcode,
        'result' => $data
    ], 200);
}

if (!function_exists('getMonths')) {
    function getMonths()
    {

        return $months = [
            "January" => "January",
            "February" => "February",
            "March" => "March",
            "April" => "April",
            "May" => "May",
            "June" => "June",
            "July" => "July",
            "August" => "August",
            "September" => "September",
            "October" => "October",
            "November" => "November",
            "December" => "December"
        ];
    }
}

if (!function_exists('getYears')) {
    function getYears()
    {
        return range(2024, (int)date("Y"));
    }
}

/**
 * Active (open) till session for a cashier.
 * Allows overnight floats: added_date can be on or before today.
 */
if (!function_exists('openFloatAmountQuery')) {
    function openFloatAmountQuery($userId = null)
    {
        $userId = $userId ?? auth()->id();

        return \Illuminate\Support\Facades\DB::table('float_amount')
            ->where('user_id', $userId)
            ->where('is_closed', 0)
            ->where('print_bill', 0)
            ->where('amonut', '>', 0)
            ->whereDate('added_date', '<=', date('Y-m-d'))
            ->orderBy('float_amt_id', 'desc');
    }
}

if (!function_exists('hasOpenFloatAmount')) {
    function hasOpenFloatAmount($userId = null)
    {
        return openFloatAmountQuery($userId)->exists();
    }
}

if (!function_exists('getOpenFloatAmount')) {
    function getOpenFloatAmount($userId = null)
    {
        return openFloatAmountQuery($userId)->first();
    }
}

/**
 * Load a specific open float session for a cashier (force-close / close by id).
 */
if (!function_exists('getOpenFloatAmountById')) {
    function getOpenFloatAmountById($floatAmtId, $userId = null)
    {
        if (empty($floatAmtId)) {
            return null;
        }

        $query = openFloatAmountQuery($userId)->where('float_amt_id', $floatAmtId);

        return $query->first();
    }
}
