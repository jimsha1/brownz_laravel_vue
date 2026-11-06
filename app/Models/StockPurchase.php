<?php

namespace App\Models;

use App\Service\FinancialYearService;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockPurchase extends Model
{
    use HasFactory;

    protected $primaryKey = 'purchase_id';
    protected $guarded = [];

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
            if (empty($entity->purchase_ref_no)) {
                $entity->purchase_ref_no = self::getNextPurchaseRefNo(true, $entity->purchase_date);
            }
        });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });
    }

    /**
     * Get next Purchase Ref.No for display or reserve the next number.
     *
     * Format: PR + financial-year letter + 3-digit sequence (e.g. PRA001).
     * The sequence resets when the financial year changes.
     *
     * @param  bool  $reserve  When true, allocate under row lock (used on save).
     * @param  mixed  $purchaseDate  Purchase date used to resolve the financial year.
     */
    public static function getNextPurchaseRefNo($reserve = true, $purchaseDate = null): string
    {
        $financialYearService = app(FinancialYearService::class);
        $date = $purchaseDate
            ? Carbon::parse($purchaseDate)->toDateString()
            : now()->toDateString();

        $financialYear = $financialYearService->findForDate($date)
            ?? $financialYearService->getCurrentFinancialYear();

        if (! $financialYear) {
            throw new \Exception(
                'No financial year exists for '.$date.'. Initialize or open a financial year before creating purchases.'
            );
        }

        $autoPurchaseRef = AutoIncrementMaster::where('auto_code', 'AI_PURCHASE_REF_NO')->first();
        $basePrefix = $autoPurchaseRef?->auto_prefix ?: 'PR';
        $prefix = $financialYearService->getRefPrefix($financialYear, $basePrefix);

        $generate = function () use ($prefix, $financialYear) {
            FinancialYear::where('id', $financialYear->id)->lockForUpdate()->first();

            $lastRecord = self::query()
                ->where('purchase_ref_no', 'LIKE', $prefix.'%')
                ->lockForUpdate()
                ->orderByRaw(
                    'CAST(SUBSTRING(purchase_ref_no, LENGTH(?) + 1) AS UNSIGNED) DESC',
                    [$prefix]
                )
                ->first();

            $nextNumber = 1;
            if ($lastRecord && $lastRecord->purchase_ref_no) {
                $numericPart = substr($lastRecord->purchase_ref_no, strlen($prefix));
                if (is_numeric($numericPart)) {
                    $nextNumber = (int) $numericPart + 1;
                }
            }

            return $prefix.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
        };

        if ($reserve) {
            return DB::transaction($generate);
        }

        $lastRecord = self::query()
            ->where('purchase_ref_no', 'LIKE', $prefix.'%')
            ->orderByRaw(
                'CAST(SUBSTRING(purchase_ref_no, LENGTH(?) + 1) AS UNSIGNED) DESC',
                [$prefix]
            )
            ->first();

        $nextNumber = 1;
        if ($lastRecord && $lastRecord->purchase_ref_no) {
            $numericPart = substr($lastRecord->purchase_ref_no, strlen($prefix));
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
        return $this->hasMany(StockPurchaseEditLog::class, 'purchase_id', 'purchase_id')
            ->orderByDesc('created_at');
    }
    public function product_rows()
    {
        return $this->hasMany(StockPurchaseItem::class, 'purchase_id');
    }
    public function payment_rows()
    {
        return $this->hasMany(PurchasePayment::class, 'purchase_id');
    }
    public function vendor()
    {
        return $this->hasOne(Vendor::class, 'vendor_id', 'vendor_id');
    }
}
