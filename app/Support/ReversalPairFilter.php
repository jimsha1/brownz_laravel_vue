<?php

namespace App\Support;

use App\Models\Employee;
use Illuminate\Support\Collection;

class ReversalPairFilter
{
    /**
     * Return transaction_group ids for reversals and their matched originals.
     * Orphan reversals (no original found) are still excluded.
     *
     * @param  Collection<int, object|array>  $groups  items with id + description
     */
    public static function excludedGroupIds(Collection $groups): Collection
    {
        $normalized = $groups
            ->map(function ($group) {
                return (object) [
                    'id' => (int) data_get($group, 'id'),
                    'description' => (string) data_get($group, 'description'),
                ];
            })
            ->filter(fn ($group) => $group->id > 0)
            ->unique('id')
            ->sortBy('id')
            ->values();

        $excluded = collect();
        $pairedOriginalIds = collect();

        $reversals = $normalized->filter(function ($group) {
            return self::isReversalDescription($group->description);
        });

        foreach ($reversals as $reversal) {
            $excluded->push($reversal->id);

            $originalId = self::findOriginalGroupId($reversal, $normalized, $pairedOriginalIds);
            if ($originalId) {
                $excluded->push($originalId);
                $pairedOriginalIds->push($originalId);
            }
        }

        return $excluded->unique()->values();
    }

    public static function isReversalDescription($description): bool
    {
        return (bool) preg_match('/^Reversal\b/i', trim((string) $description));
    }

    private static function findOriginalGroupId($reversal, Collection $groups, Collection $pairedOriginalIds): ?int
    {
        $description = trim((string) $reversal->description);

        // reverseTransactionGroup(): "Reversal of: {original description}"
        if (preg_match('/^Reversal of:\s*(.+)$/i', $description, $matches)) {
            $originalDescription = trim($matches[1]);

            $match = $groups
                ->filter(function ($group) use ($originalDescription, $reversal, $pairedOriginalIds) {
                    if ($group->id >= $reversal->id || $pairedOriginalIds->contains($group->id)) {
                        return false;
                    }

                    if (self::isReversalDescription($group->description)) {
                        return false;
                    }

                    return trim((string) $group->description) === $originalDescription;
                })
                ->sortByDesc('id')
                ->first();

            return $match?->id;
        }

        // Booking reversals: pair with nearest preceding posting for the same booking code
        if (preg_match('/Booking Code:\s*([^)]+)\)?/i', $description, $matches)) {
            $bookingCode = trim($matches[1]);

            $match = $groups
                ->filter(function ($group) use ($bookingCode, $reversal, $pairedOriginalIds) {
                    if ($group->id >= $reversal->id || $pairedOriginalIds->contains($group->id)) {
                        return false;
                    }

                    if (self::isReversalDescription($group->description)) {
                        return false;
                    }

                    return stripos((string) $group->description, 'Booking Code: '.$bookingCode) !== false;
                })
                ->sortByDesc('id')
                ->first();

            return $match?->id;
        }

        // Manual voucher edit: "Reversal of Voucher Transaction #{id}"
        if (preg_match('/Reversal of Voucher Transaction #(\d+)/i', $description, $matches)) {
            return (int) $matches[1];
        }

        // Legacy payroll advance edit: "Reversal of salary advance edit for employee #{id}"
        if (preg_match('/^Reversal of salary advance edit for employee #(\d+)/i', $description, $matches)) {
            return self::findNearestPrecedingByDescription(
                $groups,
                $reversal,
                $pairedOriginalIds,
                'Salary advance for employee #'.$matches[1]
            );
        }

        // Legacy payroll payment edit: "Reversal of salary payment edit for employee #{id}"
        if (preg_match('/^Reversal of salary payment edit for employee #(\d+)/i', $description, $matches)) {
            $employeeName = optional(
                Employee::where('emp_id', (int) $matches[1])->first()
            )->name;

            if ($employeeName) {
                return self::findNearestPrecedingByDescription(
                    $groups,
                    $reversal,
                    $pairedOriginalIds,
                    'Salary payment for '.$employeeName
                );
            }
        }

        // Expense edits: "Reversal: {short note}" → match prior group with same note
        if (preg_match('/^Reversal:\s*(.+)$/i', $description, $matches)) {
            return self::findNearestPrecedingByDescription(
                $groups,
                $reversal,
                $pairedOriginalIds,
                trim($matches[1])
            );
        }

        return null;
    }

    private static function findNearestPrecedingByDescription(
        Collection $groups,
        $reversal,
        Collection $pairedOriginalIds,
        string $originalDescription
    ): ?int {
        $match = $groups
            ->filter(function ($group) use ($originalDescription, $reversal, $pairedOriginalIds) {
                if ($group->id >= $reversal->id || $pairedOriginalIds->contains($group->id)) {
                    return false;
                }

                if (self::isReversalDescription($group->description)) {
                    return false;
                }

                return trim((string) $group->description) === $originalDescription;
            })
            ->sortByDesc('id')
            ->first();

        return $match?->id;
    }
}
