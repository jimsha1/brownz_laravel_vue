<?php

namespace App\Models;

use App\Service\FinancialYearService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class TransactionGroup extends Model
{
    // protected $fillable = ['description', 'date'];
    protected $guarded = [];

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
    }

    /**
     * @return array<string, string>
     */
    public static function voucherTypePrefixes(): array
    {
        return [
            'Payment' => 'PV',
            'Receipt' => 'RV',
            'Journal' => 'JV',
            'Contra' => 'CV',
        ];
    }

    public static function resolveVoucherBasePrefix(?string $voucherType): string
    {
        return self::voucherTypePrefixes()[$voucherType] ?? 'VN';
    }

    /**
     * Get next voucher number for a financial year.
     *
     * Format: type prefix + financial-year letter + 3-digit sequence (e.g. PVA001).
     * The sequence resets when the financial year changes.
     *
     * Must be called inside an active DB transaction when allocating numbers.
     */
    public static function getNextVoucherNo(
        string $voucherType,
        $transactionDate,
        ?int $financialYearId = null
    ): string {
        $financialYearService = app(FinancialYearService::class);
        $date = Carbon::parse($transactionDate)->toDateString();

        $financialYear = $financialYearId
            ? FinancialYear::find($financialYearId)
            : ($financialYearService->findForDate($date) ?? $financialYearService->getCurrentFinancialYear());

        if (! $financialYear) {
            throw new \Exception(
                'No financial year exists for '.$date.'. Initialize or open a financial year before creating vouchers.'
            );
        }

        $prefix = $financialYearService->getRefPrefix(
            $financialYear,
            self::resolveVoucherBasePrefix($voucherType)
        );

        FinancialYear::where('id', $financialYear->id)->lockForUpdate()->first();

        $lastRecord = self::query()
            ->where('voucher_no', 'LIKE', $prefix.'%')
            ->where('voucher_no', 'NOT LIKE', '%-REV')
            ->lockForUpdate()
            ->orderByRaw(
                'CAST(SUBSTRING(voucher_no, LENGTH(?) + 1) AS UNSIGNED) DESC',
                [$prefix]
            )
            ->first();

        $nextNumber = 1;
        if ($lastRecord && $lastRecord->voucher_no) {
            $numericPart = substr($lastRecord->voucher_no, strlen($prefix));
            if (is_numeric($numericPart)) {
                $nextNumber = (int) $numericPart + 1;
            }
        }

        return $prefix.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
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

    public function editLogs()
    {
        return $this->hasMany(StockVoucherEditLog::class, 'transaction_group_id')
            ->orderByDesc('created_at');
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * System-generated salon booking vouchers (not manually created).
     * Matched by narration from booking payment/update accounting posts.
     */
    public function isBookingVoucher(): bool
    {
        $description = (string) ($this->description ?? '');

        return $description !== ''
            && (
                str_contains($description, 'Booking Code:')
                || stripos($description, 'Salon booking') !== false
                || stripos($description, 'Reversal of booking') !== false
            );
    }
}
