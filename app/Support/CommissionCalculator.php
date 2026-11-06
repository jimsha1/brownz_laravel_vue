<?php

namespace App\Support;

use DateTime;
use Exception;

class CommissionCalculator
{
    /**
     * Whether commission should apply for a service performance timestamp.
     * If start/end dates are empty, an active commission status is enough.
     */
    public static function isApplicable($commissionStatus, $serviceStartedAt, $startDate = null, $endDate = null): bool
    {
        if ((int) $commissionStatus !== 1) {
            return false;
        }

        if (empty($startDate) || empty($endDate)) {
            return true;
        }

        try {
            $started = $serviceStartedAt instanceof DateTime
                ? clone $serviceStartedAt
                : new DateTime((string) $serviceStartedAt);

            $from = new DateTime((string) $startDate);
            $from->setTime(0, 0, 0);

            $to = new DateTime((string) $endDate);
            $to->setTime(23, 59, 59);

            return $started >= $from && $started <= $to;
        } catch (Exception $e) {
            // Invalid dates with active status: treat as applicable
            return true;
        }
    }

    public static function amount(float $baseAmount, float $percentage, bool $applicable): float
    {
        if (! $applicable || $percentage <= 0 || $baseAmount <= 0) {
            return 0.0;
        }

        return round(($baseAmount * $percentage) / 100, 2);
    }
}
