<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MakeupBooking extends Model
{
    protected $guarded = [];

    public function getMakeupServiceNameAttribute($value)
    {
        return json_decode($value, true);
    }

    /**
     * Always return beautician names as an array (supports legacy single-string values).
     */
    public function getBeauticianNameAttribute($value)
    {
        if (is_array($value)) {
            return array_values(array_filter($value, fn ($name) => $name !== null && $name !== ''));
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            return array_values(array_filter($decoded, fn ($name) => $name !== null && $name !== ''));
        }

        return [$raw];
    }

    public function setBeauticianNameAttribute($value): void
    {
        if (is_array($value)) {
            $names = array_values(array_filter($value, fn ($name) => $name !== null && trim((string) $name) !== ''));
            $this->attributes['beautician_name'] = json_encode($names);

            return;
        }

        $raw = trim((string) $value);
        if ($raw === '') {
            $this->attributes['beautician_name'] = json_encode([]);

            return;
        }

        $decoded = json_decode($raw, true);
        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
            $this->attributes['beautician_name'] = json_encode(array_values(array_filter($decoded)));

            return;
        }

        $this->attributes['beautician_name'] = json_encode([$raw]);
    }

    public function service()
    {
        return $this->belongsTo(Service::class, 'service_id');
    }

    public function services()
    {
        return $this->hasMany(Service::class, 'service_id', 'makeup_service_name');
    }
}
