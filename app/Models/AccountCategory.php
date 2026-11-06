<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Schema;

class AccountCategory extends Model
{
    protected $primaryKey = 'category_id';

    protected $guarded = [];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public static function normalizeName(string $name): string
    {
        $name = preg_replace('/\s+/', ' ', trim($name)) ?? '';

        return trim($name);
    }

    public static function findByTypeAndName(string $type, string $name): ?self
    {
        $name = self::normalizeName($name);
        if ($name === '' || $type === '') {
            return null;
        }

        return static::query()
            ->where('type', $type)
            ->whereRaw('LOWER(name) = ?', [mb_strtolower($name)])
            ->first();
    }

    /**
     * Keep the category list in step with ledgers created outside this form.
     */
    public static function syncFromAccount(Account $account): void
    {
        static $tableReady = null;
        if ($tableReady === null) {
            $tableReady = Schema::hasTable('account_categories');
        }
        if (! $tableReady) {
            return;
        }

        $name = self::normalizeName((string) ($account->category ?? ''));
        $type = (string) ($account->type ?? '');
        if ($name === '' || $type === '') {
            return;
        }

        $existing = self::findByTypeAndName($type, $name);
        if ($existing) {
            if ((int) $account->is_system_defined === 1 && ! $existing->is_system) {
                $existing->is_system = true;
                $existing->save();
            }

            return;
        }

        static::create([
            'name' => $name,
            'type' => $type,
            'is_system' => (int) $account->is_system_defined === 1,
        ]);
    }
}
