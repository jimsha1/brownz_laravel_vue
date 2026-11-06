<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\BookingItem;
use App\Models\StockItem;
use App\Models\StockMovement;
use App\Service\FinancialYearService;
use Carbon\Carbon;

class Booking extends Model
{
    use HasFactory, SoftDeletes;

    /* booking sources */
    const BOOKING_SOURCE_WALK_IN = 'Walk-in';
    const BOOKING_SOURCE_WEBSITE = 'Website';
    const BOOKING_SOURCE_ANDROID = 'Android';
    const BOOKING_SOURCE_IOS = 'iOS';

    /* booking types */
    const BOOKING_TYPE_SALE = 'Sale';
    const BOOKING_TYPE_REFUND = 'Refund';
    const BOOKING_TYPE_CANCEL = 'Cancel';
    const BOOKING_TYPE_DRAFT = 'Draft';
    /* payment statuses */
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_INITIATED = 'initiated';
    const PAYMENT_STATUS_FAILED = 'failed';
    const PAYMENT_STATUS_COMPLETED = 'completed';

    protected $primaryKey = 'booking_id';
    protected $guarded = [];

    /**
     * Computed for Vue only — not a DB column.
     */
    protected $appends = [
        'has_client_credit_applied',
    ];

    public static function boot()
    {
        parent::boot();

        /* Execute when creating a record */
        static::creating(function ($entity) {
            $entity->created_by = Auth::id() ?? $entity->created_by;
            $entity->outlet_id = $entity->outlet_id ?? session_outlet_id();
            $entity->booking_barcode = barcode_unique();
        });

        // static::saved(function($entity){
        //     if($entity->booking_code ){
        //         Mail::to($entity->client_email)->send(new OrderPlaced($entity));
        //     }

        // });

        /* Execute when creating/updating a record */
        static::saving(function ($entity) {
            $entity->updated_by = Auth::id() ?? $entity->updated_by;
        });

        /* Execute when deleting a record */
        static::deleting(function ($entity) {
            $entity->deleted_by = Auth::id() ?? $entity->deleted_by;
            $entity->save();
            
            // Manage stock when booking is soft-deleted (if not already canceled)
            // Stock is already managed in CancelBookingController when booking is canceled
            if (!$entity->canceled_at) {
                static::manageStockOnBookingDelete($entity);
            }
        });
    }

    /**
     * Get next salon booking code for display or reserve the next number.
     *
     * Format: prefix + financial-year letter + 3-digit sequence (e.g. BSA001).
     * The sequence resets when the financial year changes.
     *
     * @param  int  $autoIncrementId  Auto increment type (salon: AI_SALON_BOOKING_CODE).
     * @param  mixed  $bookingDate  Date used to resolve the financial year.
     * @param  bool  $reserve  When true, allocate under row lock (used on save).
     */
    public static function getNextBookingCode(
        int $autoIncrementId,
        $bookingDate = null,
        bool $reserve = true
    ): string {
        $autoIncrementMaster = AutoIncrementMaster::find($autoIncrementId);

        if (! $autoIncrementMaster) {
            throw new \Exception("Auto increment config not found for ID: {$autoIncrementId}");
        }

        $financialYearService = app(FinancialYearService::class);
        $date = $bookingDate
            ? Carbon::parse($bookingDate)->toDateString()
            : now()->toDateString();

        $financialYear = $financialYearService->findForDate($date)
            ?? $financialYearService->getCurrentFinancialYear();

        if (! $financialYear) {
            throw new \Exception(
                'No financial year exists for '.$date.'. Initialize or open a financial year before creating bookings.'
            );
        }

        $basePrefix = $autoIncrementMaster->auto_prefix ?: 'BS';
        $prefix = $financialYearService->getRefPrefix($financialYear, $basePrefix);

        $generate = function () use ($prefix, $financialYear) {
            FinancialYear::where('id', $financialYear->id)->lockForUpdate()->first();

            $lastRecord = self::query()
                ->withTrashed()
                ->where('booking_code', 'LIKE', $prefix.'%')
                ->lockForUpdate()
                ->orderByRaw(
                    'CAST(SUBSTRING(booking_code, LENGTH(?) + 1) AS UNSIGNED) DESC',
                    [$prefix]
                )
                ->first();

            $nextNumber = 1;
            if ($lastRecord && $lastRecord->booking_code) {
                $numericPart = substr($lastRecord->booking_code, strlen($prefix));
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
            ->withTrashed()
            ->where('booking_code', 'LIKE', $prefix.'%')
            ->orderByRaw(
                'CAST(SUBSTRING(booking_code, LENGTH(?) + 1) AS UNSIGNED) DESC',
                [$prefix]
            )
            ->first();

        $nextNumber = 1;
        if ($lastRecord && $lastRecord->booking_code) {
            $numericPart = substr($lastRecord->booking_code, strlen($prefix));
            if (is_numeric($numericPart)) {
                $nextNumber = (int) $numericPart + 1;
            }
        }

        return $prefix.str_pad((string) $nextNumber, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Refund document code — mirrors stock purchase returns (RET-{original}-{Hisu}).
     */
    public static function generateRefundBookingCode(string $originalBookingCode, $at = null): string
    {
        $suffix = Carbon::parse($at ?? now())->format('Hisu');

        return 'RET-'.$originalBookingCode.'-'.$suffix;
    }

    /**
     * Assign the next sale or refund booking code based on booking type.
     */
    public static function assignNextBookingCode(Booking $booking): string
    {
        if (in_array($booking->booking_type, [self::BOOKING_TYPE_REFUND, 'Refunded'], true)) {
            $originalCode = $booking->refund_invoice_no;
            if (empty($originalCode) && ! empty($booking->parent_id)) {
                $originalCode = self::query()
                    ->where('booking_id', $booking->parent_id)
                    ->value('booking_code');
            }
            if (empty($originalCode)) {
                throw new \RuntimeException('Cannot assign refund invoice number without the original sale reference.');
            }
            if (empty($booking->refund_invoice_no)) {
                $booking->refund_invoice_no = $originalCode;
            }

            return self::generateRefundBookingCode(
                $originalCode,
                $booking->created_at ?? now()
            );
        }

        $autoIncrId = $booking->booking_source === self::BOOKING_SOURCE_WEBSITE
            ? AutoIncrementMaster::AI_WEBSITE_BOOKING_CODE
            : AutoIncrementMaster::AI_SALON_BOOKING_CODE;

        return self::getNextBookingCode($autoIncrId, $booking->created_at ?? null);
    }

    public function orderPlacedMail()
    {
        // Mail::to($this->client_email)->send(new OrderPlaced($this));
    }

    public function getBookingSources()
    {
        return [
            self::BOOKING_SOURCE_WALK_IN,
            self::BOOKING_SOURCE_WEBSITE,
            self::BOOKING_SOURCE_ANDROID,
            self::BOOKING_SOURCE_IOS,
        ];
    }

    public function bookingSourceDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getBookingSources(), $default);
    }

    public function getPaymentStatuses()
    {
        return [
            self::PAYMENT_STATUS_PENDING,
            self::PAYMENT_STATUS_INITIATED,
            self::PAYMENT_STATUS_FAILED,
            self::PAYMENT_STATUS_COMPLETED,
        ];
    }

    public function paymentStatusDropdown($default = false)
    {
        return voco_scalar_dropdown($this->getPaymentStatuses(), $default);
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
        return $this->hasMany(BookingEditLog::class, 'booking_id', 'booking_id')
            ->orderByDesc('created_at');
    }

    public function bookingOnlyServices()
    {
        return $this->hasMany(BookingService::class, 'booking_id', 'booking_id');
    }

    /**
     * Parent sale booking for refund documents (bookings.parent_id).
     */
    public function parentBooking()
    {
        return $this->belongsTo(self::class, 'parent_id', 'booking_id');
    }

    public function bookingServices()
    {
        return $this->hasOne(BookingService::class, 'booking_id', 'booking_id');
    }

    public function bookingPayment()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id', 'booking_id');
    }

    public function bookingItems()
    {
        return $this->hasMany(BookingItem::class, 'booking_id', 'booking_id');
    }

    public function resetBookingAmount()
    {
        $service_base_total = $this->bookingOnlyServices->pluck('line_total')->sum();
        $item_total = $this->bookingItems->pluck('line_total')->sum();

        // Match UI grand total: line totals + booking-level discount/roundoff adjustments
        $this->booking_total = round(
            $service_base_total
            + $item_total
            - (float) ($this->applied_discount_price ?? 0)
            - (float) ($this->applied_roundoff_price ?? 0),
            2
        );

        // Keep payment status in sync when cart totals change
        if (abs((float) $this->payment_total - (float) $this->booking_total) < 0.01 && (float) $this->payment_total > 0) {
            $this->payment_status = self::PAYMENT_STATUS_COMPLETED;
        } else {
            $this->payment_status = self::PAYMENT_STATUS_PENDING;
        }

        $this->save();
    }


    public function resetPaymentTotal()
    {
        $total = 0;
        foreach ($this->payments as $payment) {
            $total += ($payment->amount_taken - $payment->amount_given);
        }

        // Total payment
        $this->payment_total = round($total, 2);

        // Make sure to update payment status
        $this->payment_status = ($this->payment_total == $this->booking_total) ? self::PAYMENT_STATUS_COMPLETED : self::PAYMENT_STATUS_PENDING;

        $this->save();
    }

    public function payments()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id', 'booking_id');
    }

    public function voidPayments()
    {
        return $this->hasMany(BookingPayment::class, 'booking_id', 'booking_id')
            ->onlyTrashed();
    }

    public function realClient()
    {
        return $this->hasOne(Client::class, 'client_id', 'client_id')
            ->join('users', 'clients.client_id', '=', 'users.id')
            ->select(
                'clients.client_id',
                'clients.balance',
                'clients.ledger_id',
                'users.name',
                'users.email',
                'users.mobile',
                'users.gender',
                'users.date_of_birth',
                'users.telephone',
                'users.address',
                'users.city',
                'users.state',
                'users.zip'
            );
    }

    /**
     * When a split was reduced to one beautician, sync the line beautician for display
     * and drop the redundant single draft row.
     */
    protected function reconcileSingleBeauticianSplits(): void
    {
        foreach ($this->bookingOnlyServices as $bookingService) {
            if (! $bookingService->relationLoaded('splitDraft')) {
                continue;
            }

            $drafts = $bookingService->splitDraft;
            if ($drafts->count() !== 1) {
                continue;
            }

            $draftBeauticianId = (int) $drafts->first()->beautician_id;
            if ($draftBeauticianId <= 0) {
                continue;
            }

            if ((int) $bookingService->beautician_id === $draftBeauticianId && (int) $bookingService->split_draft_status === 0) {
                continue;
            }

            $user = User::find($draftBeauticianId);
            $bookingService->beautician_id = $draftBeauticianId;
            if ($user && $user->shift_id) {
                $bookingService->shift_id = $user->shift_id;
            }
            if ($bookingService->status === BookingService::STATUS_COMPLETED) {
                $bookingService->service_started_by = $draftBeauticianId;
                $bookingService->service_finished_by = $draftBeauticianId;
            }
            $bookingService->split_draft_status = 0;
            $bookingService->save();

            SplitDraft::where('booking_service_id', $bookingService->booking_service_id)->delete();
            $bookingService->setRelation('splitDraft', collect());
            $bookingService->load('beautician');
        }
    }

    public function loadEssentialsForVueJs($booking_date = null)
    {
        app(\App\Service\ServiceUsedProductService::class)->ensureSnapshotsForBooking($this);

        $this->load([
            'bookingOnlyServices.service.beauticians',
            'bookingOnlyServices.beautician',
            'bookingOnlyServices.usedProducts.item:item_id,current_stock',
            'payments',
            'voidPayments',
            'bookingItems.beautician',
            'realClient',
            'bookingOnlyServices.splitDraft',
            'bookingOnlyServices.splitDraft.beautician',
        ]);

        foreach ($this->bookingOnlyServices as $bookingService) {
            foreach ($bookingService->usedProducts as $usedLine) {
                $usedLine->setAttribute(
                    'current_stock',
                    $usedLine->item ? round((float) $usedLine->item->current_stock, 3) : null
                );
                $usedLine->setAttribute(
                    'stock_accounted_qty',
                    $usedLine->posted_at ? round((float) $usedLine->quantity, 3) : 0
                );
                $usedLine->unsetRelation('item');
            }
        }

        $this->reconcileSingleBeauticianSplits();

        $booked_service_slots = $this->getBookedServiceSlots();

        $beauticianIds = [];
        $leaveDates = [];
        foreach ($this->bookingOnlyServices as $db_booking_service) {
            if (! $db_booking_service->service) {
                continue;
            }
            $leaveDates[] = $db_booking_service->appointment_date;
            foreach ($db_booking_service->service->beauticians as $db_beautician) {
                $beauticianIds[] = $db_beautician->beautician_id;
            }
        }

        $leavesByKey = collect();
        if (! empty($beauticianIds) && ! empty($leaveDates)) {
            $leavesByKey = \App\Models\LeaveManagement::whereIn('beautician_id', array_unique($beauticianIds))
                ->whereIn('leave_date', array_unique($leaveDates))
                ->where('leave_status', 'approved')
                ->get()
                ->keyBy(function ($leave) {
                    return $leave->beautician_id.'|'.$leave->leave_date;
                });
        }

        foreach ($this->bookingOnlyServices as $db_booking_service) {
            if (! $db_booking_service->service) {
                continue;
            }

            $bk_date = $db_booking_service->appointment_date;
            $bk_service_id = $db_booking_service->service_id;
            $duration_seconds = max(1, (int) $db_booking_service->duration_minutes) * 60;

            foreach ($db_booking_service->service->beauticians as $db_beautician) {
                $beaut_leave = $leavesByKey->get($db_beautician->beautician_id.'|'.$bk_date);

                // Always show beautician, even if on leave - but hide time slots for full leave
                if (empty($beaut_leave) || $beaut_leave->leave_type != \App\Models\LeaveManagement::LEAVE_TYPE_FULL) {
                    if (empty($beaut_leave)) {
                        $beautician_start_time = \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                        $beautician_end_time = \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                    } else {
                        $beautician_start_time = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_FIRST_HALF
                            ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME
                            : \App\Models\Beautician::DEFAULT_SHIFT_START_TIME;
                        $beautician_end_time = $beaut_leave->leave_type == \App\Models\LeaveManagement::LEAVE_TYPE_SECOND_HALF
                            ? \App\Models\Beautician::DEFAULT_SHIFT_HALF_TIME
                            : \App\Models\Beautician::DEFAULT_SHIFT_END_TIME;
                    }

                    $start_ts = strtotime($beautician_start_time);
                    $end_ts = strtotime($beautician_end_time);
                    $bk_beautician_id = $db_beautician->beautician_id;
                    $existing_bookings = $booked_service_slots[$bk_date][$bk_service_id][$bk_beautician_id] ?? [];

                    $slots = [];
                    for ($i = $start_ts; $i <= $end_ts; $i += $duration_seconds) {
                        $temp_slot = date('H:i:s', $i);
                        $available = true;

                        foreach ($existing_bookings as $bk_row) {
                            if ($bk_row->slot_start == $temp_slot) {
                                $temp_next_slot = date('H:i:s', $i + $duration_seconds);
                                if (! $bk_row->slot_end || ($bk_row->slot_end >= $temp_next_slot)) {
                                    $available = false;
                                }
                                break;
                            }
                        }

                        $slots[$temp_slot] = [
                            'slot_time' => $temp_slot,
                            'not_in_use' => false,
                            'available' => $available,
                        ];
                    }

                    $db_beautician->slots = $slots;
                } else {
                    $db_beautician->slots = [];
                }

                $db_beautician->photo_src_url = beautician_photo_url($db_beautician->profile_photo_path, 50, 50, 0);

                if ($db_booking_service->beautician_id == $db_beautician->beautician_id) {
                    $db_beautician->selected_slot_hms = $db_booking_service->slot_start;
                    $db_beautician->selected_slot_hm = date('H:i', strtotime($db_booking_service->slot_start));
                }
            }
        }

        $this->available_credit = $this->resolveAvailableClientCredit();
    }

    /**
     * Whether previous client credit was already applied once on this booking.
     * Exposed to Vue via $appends (not a database column).
     */
    public function getHasClientCreditAppliedAttribute(): bool
    {
        return $this->hasClientCreditApplied();
    }

    /**
     * Prepaid / overpayment credit available to apply on this booking.
     * Uses clients.balance (negative = prepaid) and client ledger balance when negative.
     * Deducts credit already applied on this booking so the same credit cannot be reused.
     */
    public function resolveAvailableClientCredit(): float
    {
        if (empty($this->client_id)) {
            return 0.0;
        }

        $walletCredit = 0.0;
        $clientBalance = null;

        if ($this->relationLoaded('realClient') && $this->realClient) {
            $clientBalance = (float) ($this->realClient->balance ?? 0);
        } else {
            $clientBalance = (float) (DB::table('clients')->where('client_id', $this->client_id)->value('balance') ?? 0);
        }

        if ($clientBalance < 0) {
            $walletCredit = abs($clientBalance);
        }

        $ledgerCredit = 0.0;
        $ledgerId = null;
        if ($this->relationLoaded('realClient') && $this->realClient) {
            $ledgerId = $this->realClient->ledger_id ?? null;
        } else {
            $ledgerId = DB::table('clients')->where('client_id', $this->client_id)->value('ledger_id');
        }

        if (! empty($ledgerId)) {
            $ledgerBalance = (float) (DB::table('accounts')->where('account_id', $ledgerId)->value('balance') ?? 0);
            if ($ledgerBalance < 0) {
                $ledgerCredit = abs($ledgerBalance);
            }
        }

        // Prefer wallet; fall back to ledger. Never allow applying more than remaining prepaid.
        $available = max($walletCredit, $ledgerCredit);

        // Credit already consumed on this booking must not remain "available"
        $alreadyAppliedOnBooking = $this->clientCreditAlreadyAppliedAmount();
        $available = max(0, $available - $alreadyAppliedOnBooking);

        return round($available, 2);
    }

    /**
     * Whether previous client credit was already applied once on this booking.
     */
    public function hasClientCreditApplied(): bool
    {
        return $this->clientCreditAlreadyAppliedAmount() > 0.009;
    }

    /**
     * Sum of prepaid/credit payment rows already applied on this booking.
     */
    public function clientCreditAlreadyAppliedAmount(): float
    {
        $payments = $this->relationLoaded('payments')
            ? $this->payments
            : $this->payments()->get();

        $applied = 0.0;
        foreach ($payments as $payment) {
            $isPrepaid = (int) ($payment->prepaid_applied ?? 0) === 1;
            $isCreditMode = ($payment->payment_mode === \App\Models\BookingPayment::PAYMENT_MODE_CREDIT);
            if ($isPrepaid || $isCreditMode) {
                $applied += max(0, (float) $payment->amount_taken - (float) ($payment->amount_given ?? 0));
            }
        }

        return round($applied, 2);
    }

    private function getBookedServiceSlots()
    {

        // All services in this booking (date-wise)
        $date_wise_services = [];
        $booking_service_ids = [];
        foreach ($this->bookingOnlyServices as $db_booking_service) {
            $date_wise_services[$db_booking_service->appointment_date][] = $db_booking_service->service_id;
            $booking_service_ids[] = $db_booking_service->booking_service_id;
        }

        if (count($booking_service_ids) === 0) {
            return [];
        }

        // Fetch other bookings for above services
        $db_booked_slots = BookingService::where(function ($query) use ($date_wise_services) {
            foreach ($date_wise_services as $date => $service_ids) {
                $query->orWhere(function ($where) use ($date_wise_services, $date, $service_ids) {
                    $where->where('appointment_date', $date)
                        ->whereIn('service_id', $service_ids);
                });
            }
        })
            ->whereNotIn('booking_service_id', $booking_service_ids)
            ->whereNotNull('slot_start')
            ->select('booking_service_id', 'appointment_date', 'service_id', 'beautician_id', 'slot_start', 'slot_end')
            ->get();
        $booked_service_slots = [];
        foreach ($db_booked_slots as $row) {
            $booked_service_slots[$row->appointment_date][$row->service_id][$row->beautician_id][] = $row;
        }

        return $booked_service_slots;
    }

    /**
     * @var $booking_source "web"|"salon"
     */
    public function cancelBooking($booking_source)
    {

        if ($booking_source !== 'web' && $booking_source !== 'salon') {
            throw new \Exception('Invalid booking source specified!');
        }

        DB::transaction(function () use ($booking_source) {
            $this->canceled_at = new \DateTime();
            $this->canceled_by = Auth::id();

            $this->save();

            $booking_info = new Booking();

            $booking_info->refund_invoice_no = $this->booking_code;
            $booking_info->booking_code = self::generateRefundBookingCode($this->booking_code);
            $booking_info->booking_barcode = $booking_info->booking_code;

            $booking_info->client_id = Auth::user()->id;
            $booking_info->outlet_id = $this->outlet_id;
            $booking_info->client_id = $this->client_id;

            $booking_info->client_name = $this->client_name;
            $booking_info->client_mobile = $this->client_mobile;
            $booking_info->client_telephone = $this->client_telephone;
            $booking_info->client_email = $this->client_email;

            $booking_info->booking_total = $this->booking_total;
            $booking_info->payment_total = $this->payment_total;
            $booking_info->payment_status = $this->payment_status;
            $booking_info->applied_discount_percent = $this->applied_discount_percent;

            $booking_info->booking_source = $this->booking_source;
            $booking_info->booking_type = Booking::BOOKING_TYPE_REFUND;
            $booking_info->parent_id = $this->booking_id;
            $booking_info->save();


            foreach ($this->bookingOnlyServices as $booking_service_model) {
                $booking_service = new BookingService();
                $booking_service->booking_id = $booking_info->booking_id;
                $booking_service->service_id = $booking_service_model->service_id;
                $booking_service->category_name = $booking_service_model->category_name;
                $booking_service->service_name = $booking_service_model->service_name;
                $booking_service->duration_minutes = $booking_service_model->duration_minutes;

                $booking_service->service_booking_price = $booking_service_model->service_booking_price;
                $booking_service->tax_percent = $booking_service_model->tax_percent;
                $booking_service->tax_amount = $booking_service_model->tax_amount;
                $booking_service->line_total = $booking_service_model->line_total;

                $booking_service->appointment_date = $booking_service_model->appointment_date;
                $booking_service->slot_start = $booking_service_model->slot_start;

                $booking_service->status = BookingService::STATUS_REFUNDED;;
                // $booking_service->slot_end = $request->time;
                $booking_service->beautician_id = $booking_service_model->beautician_id;
                $booking_service->save();
            }

            foreach ($this->bookingItems as $booking_item_model) {
                $booking_item = new BookingItem();
                $booking_item->booking_id = $booking_info->booking_id;
                $booking_item->item_id = $booking_item_model->item_id;
                $booking_item->price_id = $booking_item_model->price_id;
                $booking_item->item_code = $booking_item_model->item_code;
                $booking_item->item_name = $booking_item_model->item_name;
                $booking_item->quantity = $booking_item_model->quantity;

                $booking_item->unit_price = $booking_item_model->unit_price;
                $booking_item->discount_percent = $booking_item_model->discount_percent;
                $booking_item->discount_amount = $booking_item_model->discount_amount;
                $booking_item->tax_percent = $booking_item_model->tax_percent;
                $booking_item->tax_amount = $booking_item_model->tax_amount;
                $booking_item->line_total = $booking_item_model->line_total;



                // $booking_item->status = BookingService::STATUS_REFUNDED;;
                // $booking_item->beautician_id = $booking_item_model->beautician_id;
                $booking_item->save();
            }

            return $this;
        });
    }


    public function beauticians()
    {
        return $this->hasOne(BookingService::class, 'booking_id', 'booking_id');
    }


    public function calculateServiceBookingPrice()
    {
        return $this->bookingServices->sum('service_booking_price');
    }

    /**
     * Restore product stock for a single booking line.
     */
    public static function restoreStockForBookingItem(BookingItem $bookingItem, $booking = null): float
    {
        $booking = $booking ?: static::find($bookingItem->booking_id);

        $stockOut = (float) StockMovement::where('booking_item_id', $bookingItem->booking_item_id)
            ->sum('stock_out');
        $stockIn = (float) StockMovement::where('booking_item_id', $bookingItem->booking_item_id)
            ->sum('stock_in');

        $quantityToReturn = max(0, $stockOut - $stockIn);

        if ($quantityToReturn <= 0) {
            if ($booking && $booking->booking_type === self::BOOKING_TYPE_REFUND) {
                StockMovement::where('booking_item_id', $bookingItem->booking_item_id)->delete();

                return 0;
            }

            $refundedQty = (float) ($bookingItem->refunded_qty ?? 0);
            $packQty = max(0, (float) $bookingItem->quantity - $refundedQty);
            $stockItem = StockItem::find($bookingItem->item_id);
            $quantityToReturn = $stockItem ? $stockItem->baseQuantity($packQty) : $packQty;
        }

        if ($quantityToReturn > 0) {
            $stockItem = StockItem::find($bookingItem->item_id);
            if ($stockItem) {
                $stockItem->increment('current_stock', $quantityToReturn);
            }

            if (! empty($bookingItem->price_id)) {
                DB::table('stock_item_prices')
                    ->where('price_id', $bookingItem->price_id)
                    ->increment('quantity', $quantityToReturn);
            } else {
                $priceRows = DB::table('stock_item_prices')
                    ->where('item_id', $bookingItem->item_id)
                    ->count();
                if ($priceRows === 1) {
                    DB::table('stock_item_prices')
                        ->where('item_id', $bookingItem->item_id)
                        ->increment('quantity', $quantityToReturn);
                }
            }
        }

        StockMovement::where('booking_item_id', $bookingItem->booking_item_id)->delete();

        return $quantityToReturn;
    }

    /**
     * Restore product stock for a booking that previously deducted inventory.
     * Uses stock movements (when present) and restores by price_id to match sell path.
     */
    public static function restoreStockForBooking($booking): void
    {
        $bookingItems = BookingItem::where('booking_id', $booking->booking_id)->get();

        foreach ($bookingItems as $bookingItem) {
            static::restoreStockForBookingItem($bookingItem, $booking);
        }

        app(\App\Service\ServiceUsedProductService::class)->restoreForBooking($booking);
    }

    /**
     * Manage stock when booking is soft-deleted
     * Returns all stock items back to inventory
     */
    protected static function manageStockOnBookingDelete($booking)
    {
        static::restoreStockForBooking($booking);
    }
}
