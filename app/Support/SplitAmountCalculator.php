<?php

namespace App\Support;

class SplitAmountCalculator
{
    /**
     * Normalize per-beautician split prices so they add up to the service total.
     * Equal-split when prices are missing, zero, or clearly wrong (e.g. each row has the full total).
     *
     * @param  array<int, float|int|string>  $prices
     * @return array<int, float>
     */
    public static function normalizePrices(array $prices, float $lineTotal, int $beauticianCount): array
    {
        $beauticianCount = max($beauticianCount, count($prices), 1);
        $lineTotal = round(max(0, $lineTotal), 2);

        $normalized = [];
        foreach (array_values($prices) as $price) {
            $normalized[] = round((float) $price, 2);
        }

        while (count($normalized) < $beauticianCount) {
            $normalized[] = 0.0;
        }
        if (count($normalized) > $beauticianCount) {
            $normalized = array_slice($normalized, 0, $beauticianCount);
        }

        $sum = round(array_sum($normalized), 2);
        $allMatchTotal = $beauticianCount > 1
            && $lineTotal > 0
            && collect($normalized)->every(function ($price) use ($lineTotal) {
                return abs($price - $lineTotal) < 0.009;
            });

        $needsEqualSplit = $beauticianCount > 1 && (
            $allMatchTotal
            || $sum <= 0
            || ($lineTotal > 0 && abs($sum - $lineTotal) > 0.05)
        );

        if ($needsEqualSplit || ($beauticianCount === 1 && ($normalized[0] ?? 0) <= 0 && $lineTotal > 0)) {
            return self::equalSplit($lineTotal, $beauticianCount);
        }

        return $normalized;
    }

    /**
     * @return array<int, float>
     */
    public static function equalSplit(float $lineTotal, int $count): array
    {
        $count = max(1, $count);
        $lineTotal = round(max(0, $lineTotal), 2);

        if ($count === 1) {
            return [$lineTotal];
        }

        $base = round($lineTotal / $count, 2);
        $prices = array_fill(0, $count, $base);
        // Absorb rounding remainder on the last share
        $prices[$count - 1] = round($lineTotal - ($base * ($count - 1)), 2);

        return $prices;
    }
}
