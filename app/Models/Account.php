<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class Account extends Model
{
    protected $guarded = [];
    
    protected $primaryKey = 'account_id';

    /* Account Type */
    // '','','','',''
    const ACCOUNT_TYPE_ASSET = 'asset';
    const ACCOUNT_TYPE_LIABILITY = 'liability';
    const ACCOUNT_TYPE_EQUITY = 'equity';
    const ACCOUNT_TYPE_REVENUE = 'revenue';
    const ACCOUNT_TYPE_EXPENSE = 'expense';


    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });

        static::saved(function ($entity) {
            AccountCategory::syncFromAccount($entity);
        });
    }

    public function accountTypeDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getType(), $default);
    }

    public function getType()
    {
        return [
            self::ACCOUNT_TYPE_ASSET,
            self::ACCOUNT_TYPE_LIABILITY,
            self::ACCOUNT_TYPE_EQUITY,
            self::ACCOUNT_TYPE_REVENUE,
            self::ACCOUNT_TYPE_EXPENSE,
        ];
    }

    /**
     * Get record for created_by user.
     */
    public function createdUser()
    {
        return $this->hasOne(User::class, 'id', 'created_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    /**
     * Get record for updated_by user.
     */
    public function updatedUser()
    {
        return $this->hasOne(User::class, 'id', 'updated_by')
            ->select('id', 'name', 'username', 'email', 'profile_photo_path', 'user_status');
    }

    public function journals()
    {
        return $this->hasMany(Journal::class, 'account_id', 'account_id');
    }

    public static function generateNextCode(string $type): string
    {
        $prefixes = [
            'Asset' => '1',
            'Liability' => '2',
            'Equity' => '3',
            'Revenue' => '4',
            'Expense' => '6',
        ];

        $prefix = $prefixes[$type] ?? '0';  // Fallback for unknown types

        // Asset ledgers outgrew 1000–1990. Keep those codes, then continue
        // as 10000, 10010, ... and add another digit when that series fills.
        if (strcasecmp($type, 'Asset') === 0) {
            return self::generateNextExtendedNumericCode($prefix);
        }

        // Fetch the highest code starting with the prefix (e.g., '2%')
        $maxCode = self::where('code', 'like', $prefix . '%')
            ->orderBy('code', 'desc')
            ->value('code');

        if (!$maxCode) {
            // No existing codes for this type: start with base (e.g., 2000 for Liability)
            return $prefix . '000';
        }

        // Extract suffix (last 3 digits) and increment by 10
        if (preg_match('/^' . $prefix . '(\d{3})$/', $maxCode, $matches)) {
            $suffix = (int) $matches[1];
            $nextSuffix = $suffix + 10;

            // If suffix exceeds 999, wrap to 000 and use fallback (e.g., 2A00)
            if ($nextSuffix >= 1000) {
                return self::generateFallbackCode($type);
            }

            return $prefix . str_pad($nextSuffix, 3, '0', STR_PAD_LEFT);
        }

        // Fallback if pattern doesn't match
        return self::generateFallbackCode($type);
    }

    /**
     * Next unused numeric code for a type prefix.
     * 1990 is followed by 10000, and 19990 is followed by 100000.
     */
    private static function generateNextExtendedNumericCode(string $prefix): string
    {
        $codes = self::query()
            ->where('code', 'regexp', '^'.$prefix.'[0-9]+$')
            ->pluck('code');

        $best = null;
        foreach ($codes as $code) {
            if ($best === null
                || strlen($code) > strlen($best)
                || (strlen($code) === strlen($best) && strcmp($code, $best) > 0)) {
                $best = $code;
            }
        }

        if ($best === null) {
            return $prefix.'000';
        }

        $next = self::stepNumericCode($best, $prefix);

        while (self::query()->where('code', $next)->exists()) {
            $next = self::stepNumericCode($next, $prefix);
        }

        return $next;
    }

    private static function stepNumericCode(string $code, string $prefix): string
    {
        $length = strlen($code);
        $next = (string) ((int) $code + 10);

        if (strlen($next) !== $length || ! str_starts_with($next, $prefix)) {
            $length++;

            return $prefix.str_repeat('0', $length - strlen($prefix));
        }

        return $next;
    }

    /**
     * Used once the numeric series is full (for example 1990).
     * Continues as 1A00, 1A10, ... 1Z90, then 1AA00, 1AA10, ... 1AZ90, 1BA00.
     */
    private static function generateFallbackCode(string $type): string
    {
        $prefixes = [
            'Asset' => '1',
            'Liability' => '2',
            'Equity' => '3',
            'Revenue' => '4',
            'Expense' => '6',
        ];

        $prefix = $prefixes[$type] ?? '0';

        $codes = self::query()
            ->where('code', 'regexp', '^'.$prefix.'[A-Z]+[0-9]{2}$')
            ->pluck('code');

        $bestLetters = '';
        $bestNumber = -1;

        foreach ($codes as $code) {
            if (! preg_match('/^'.preg_quote($prefix, '/').'([A-Z]+)(\d{2})$/', $code, $matches)) {
                continue;
            }

            $letters = $matches[1];
            $number = (int) $matches[2];

            if (self::compareLetterSequences($letters, $bestLetters) > 0
                || ($letters === $bestLetters && $number > $bestNumber)) {
                $bestLetters = $letters;
                $bestNumber = $number;
            }
        }

        if ($bestLetters === '') {
            return $prefix.'A00';
        }

        $letters = $bestLetters;
        $number = $bestNumber + 10;

        if ($number >= 100) {
            $number = 0;
            $letters = self::incrementLetters($letters);
        }

        $next = $prefix.$letters.str_pad((string) $number, 2, '0', STR_PAD_LEFT);

        while (self::query()->where('code', $next)->exists()) {
            $number += 10;
            if ($number >= 100) {
                $number = 0;
                $letters = self::incrementLetters($letters);
            }
            $next = $prefix.$letters.str_pad((string) $number, 2, '0', STR_PAD_LEFT);
        }

        return $next;
    }

    /**
     * A..Z come before AA..AZ, which come before BA, and so on.
     */
    private static function compareLetterSequences(string $left, string $right): int
    {
        if ($right === '') {
            return 1;
        }

        if (strlen($left) !== strlen($right)) {
            return strlen($left) <=> strlen($right);
        }

        return strcmp($left, $right);
    }

    /**
     * Z becomes AA. PHP's string increment is not used here because 'Z'++ is 'AA'
     * and 'AA' > 'Z' is false, which reused the first two-letter code.
     */
    private static function incrementLetters(string $letters): string
    {
        $chars = str_split($letters);
        $index = count($chars) - 1;

        while ($index >= 0) {
            if ($chars[$index] !== 'Z') {
                $chars[$index] = chr(ord($chars[$index]) + 1);

                return implode('', $chars);
            }

            $chars[$index] = 'A';
            $index--;
        }

        return 'A'.implode('', $chars);
    }

    public function accountsDropdownJson()
    {
        return self::select('name as text', 'account_id as id')
            ->where('is_active', 1)
            ->get();
    }
}
