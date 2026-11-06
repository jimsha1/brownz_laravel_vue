<?php

function seconds_to_time($seconds)
{
    return sprintf('%02d:%02d:%02d', ...seconds_to_time_array($seconds));
}

function seconds_to_time_array($seconds)
{
    return [
        (int) floor($seconds / 3600),
        (int) floor(($seconds / 60) % 60),
        $seconds % 60
    ];
}

function time_to_seconds($time)
{
    if (is_string($time) && strpos($time, ':') !== false) {
        $time_parts = explode(':', $time);
    } elseif (is_array($time)) {
        $time_parts = $time;
    } else {
        throw new \Exception('Time format mismatch');
    }

    return ($time_parts[0] * 3600) + ($time_parts[1] * 60) + $time_parts[2];
}
