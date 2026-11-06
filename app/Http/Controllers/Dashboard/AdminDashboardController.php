<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\BookingService;
use App\Models\MakeupBooking;
use App\Models\MakeupService;
use App\Models\Service;
use App\Models\SplitServiceAmount;
use App\Service\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminDashboardController extends Controller
{
    public function __construct(private ReportService $reportService)
    {
    }

    public function __invoke(Request $request)
    {
        $events = $this->buildCalendarEvents();
        $salonEvents = $this->buildSalonCalendarEvents();
        [$salesByDay, $months, $currentYearData, $previousYearData, $currentYear, $previousYear] = $this->buildSalesCharts();
        $bestPerformers = $this->buildBestPerformers();

        return view('dashboard.admin-dashboard', compact(
            'events',
            'salonEvents',
            'salesByDay',
            'months',
            'currentYearData',
            'previousYearData',
            'currentYear',
            'previousYear',
            'bestPerformers'
        ));
    }

    private function buildSalonCalendarEvents()
    {
        $today = Carbon::now()->toDateString();
        $rangeStart = Carbon::now()->subMonth()->startOfDay()->toDateString();
        $rangeEnd = Carbon::now()->addMonths(3)->endOfMonth()->toDateString();

        $services = BookingService::query()
            ->join('bookings', 'bookings.booking_id', '=', 'booking_services.booking_id')
            ->leftJoin('users as beauticians', 'beauticians.id', '=', 'booking_services.beautician_id')
            ->whereNull('bookings.deleted_at')
            ->whereNull('bookings.canceled_at')
            ->whereNotIn('bookings.booking_type', [
                Booking::BOOKING_TYPE_CANCEL,
                Booking::BOOKING_TYPE_REFUND,
                'Refunded',
            ])
            ->whereDate('booking_services.appointment_date', '>=', $rangeStart)
            ->whereDate('booking_services.appointment_date', '<=', $rangeEnd)
            ->whereNotIn('booking_services.status', [
                BookingService::STATUS_REJECTED,
                BookingService::STATUS_REFUNDED,
            ])
            ->where(function ($query) use ($today) {
                // Advance bookings (marked Adv / future paid)
                $query->where('bookings.is_booking', 1)
                    // Upcoming: today/future open or not yet started
                    ->orWhere(function ($inner) use ($today) {
                        $inner->whereDate('booking_services.appointment_date', '>=', $today)
                            ->where(function ($status) {
                                $status->whereIn('booking_services.status', [
                                    BookingService::STATUS_OPEN,
                                    BookingService::STATUS_REOPENED,
                                    'start',
                                ])->orWhereNull('booking_services.service_started_at');
                            });
                    })
                    // Expired: past appointment, service never started
                    ->orWhere(function ($inner) use ($today) {
                        $inner->whereDate('booking_services.appointment_date', '<', $today)
                            ->whereNull('booking_services.service_started_at');
                    })
                    // Booking code not generated (Draft)
                    ->orWhere(function ($inner) {
                        $inner->where(function ($code) {
                            $code->whereNull('bookings.booking_code')
                                ->orWhere('bookings.booking_code', 'draft');
                        })->orWhere('booking_services.status', BookingService::STATUS_DRAFT);
                    });
            })
            ->where('booking_services.line_total', '>=', 0)
            ->orderBy('booking_services.appointment_date')
            ->orderBy('booking_services.slot_start')
            ->select(
                'booking_services.booking_service_id',
                'booking_services.booking_id',
                'booking_services.service_name',
                'booking_services.appointment_date',
                'booking_services.slot_start',
                'booking_services.slot_end',
                'booking_services.status',
                'booking_services.beautician_id',
                'booking_services.service_started_at',
                'bookings.booking_code',
                'bookings.is_booking',
                'bookings.client_name',
                'bookings.client_mobile',
                'bookings.booking_source',
                'beauticians.name as beautician_name',
                'beauticians.profile_photo_path as beautician_photo_path'
            )
            ->get();

        return $services->map(function ($service) use ($today) {
            $date = Carbon::parse($service->appointment_date)->toDateString();
            $slotStart = $this->normalizeSlotTime($service->slot_start);
            $slotEnd = $this->normalizeSlotTime($service->slot_end);

            $start = $slotStart ? $date.'T'.$slotStart : $date;
            $end = $slotEnd ? $date.'T'.$slotEnd : $start;

            $hasNoCode = empty($service->booking_code) || $service->booking_code === 'draft';
            $isDraft = $hasNoCode || strtolower((string) $service->status) === BookingService::STATUS_DRAFT;
            $isAdvance = (int) $service->is_booking === 1
                || ($date > $today && strtolower((string) $service->status) === BookingService::STATUS_COMPLETED);
            $isExpired = $date < $today && empty($service->service_started_at);
            $isCompleted = ! $isAdvance && ! $isDraft && ! $isExpired && (
                strtolower((string) $service->status) === BookingService::STATUS_COMPLETED
                || (! empty($service->service_started_at)
                    && ! in_array(strtolower((string) $service->status), [
                        BookingService::STATUS_OPEN,
                        BookingService::STATUS_REOPENED,
                        'start',
                        BookingService::STATUS_DRAFT,
                    ], true))
            );
            $isUpcoming = ! $isAdvance && ! $isExpired && ! $isDraft && ! $isCompleted && (
                $date >= $today
                && (
                    in_array(strtolower((string) $service->status), [
                        BookingService::STATUS_OPEN,
                        BookingService::STATUS_REOPENED,
                        'start',
                    ], true)
                    || empty($service->service_started_at)
                )
            );

            $eventType = 'upcoming';
            if ($isAdvance) {
                $eventType = 'advance';
            } elseif ($isDraft) {
                $eventType = 'draft';
            } elseif ($isExpired) {
                $eventType = 'expired';
            } elseif ($isCompleted) {
                $eventType = 'completed';
            } elseif (strtolower((string) $service->status) === 'start') {
                $eventType = 'started';
            }

            $backgroundColor = $this->salonEventColor($eventType);
            $clientName = trim((string) $service->client_name) ?: 'Client';
            $serviceName = trim((string) $service->service_name) ?: 'Service';
            $timeLabel = $this->formatSlotDisplay($service->slot_start, $service->slot_end);
            $bookingCode = match ($eventType) {
                'advance' => 'Adv'.($service->booking_code ? ' ('.$service->booking_code.')' : ''),
                'draft' => 'Draft',
                default => ($service->booking_code ?: 'Draft'),
            };
            $dateLabel = Carbon::parse($service->appointment_date)->format('d M Y');
            $titleTime = ($timeLabel && $timeLabel !== '—') ? $timeLabel : '';
            $statusLabel = match ($eventType) {
                'advance' => 'Advance',
                'draft' => 'Draft',
                'expired' => 'Expired',
                'started' => 'Started',
                'completed' => 'Completed',
                default => 'Upcoming',
            };

            return [
                'id' => 'salon-'.$service->booking_service_id,
                'title' => $titleTime ? $clientName.' - '.$titleTime : $clientName,
                'client_name' => $clientName,
                'mobile' => $service->client_mobile,
                'booking_code' => $bookingCode,
                'booking_date' => $dateLabel,
                'time_slot' => $timeLabel,
                'services' => $serviceName,
                'status' => $statusLabel,
                'booking_source' => $service->booking_source,
                'start' => $start,
                'end' => $end,
                'allDay' => empty($slotStart),
                'backgroundColor' => $backgroundColor,
                'borderColor' => $backgroundColor,
                'textColor' => '#FFFFFF',
                'beautician_name' => $service->beautician_name,
                'beautician_photo' => $service->beautician_photo_path
                    ? asset('storage/'.$service->beautician_photo_path)
                    : null,
                'url' => route('tab.salon.booking.details', [
                    'booking_id' => $service->booking_id,
                    'tab_name' => 'edit',
                ]),
                'event_type' => $eventType,
            ];
        })->filter(fn ($event) => ($event['event_type'] ?? '') !== 'completed')
            ->map(function ($event) {
                unset($event['event_type']);

                return $event;
            })
            ->values();
    }

    private function normalizeSlotTime($time): ?string
    {
        $time = trim((string) $time);
        if ($time === '') {
            return null;
        }

        try {
            if (preg_match('/AM|PM/i', $time)) {
                return Carbon::parse($time)->format('H:i:s');
            }

            if (preg_match('/^\d{2}:\d{2}(:\d{2})?$/', $time)) {
                return strlen($time) === 5 ? $time.':00' : $time;
            }

            return Carbon::parse($time)->format('H:i:s');
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function formatSlotDisplay($start, $end): string
    {
        $startLabel = $this->formatSingleSlot($start);
        $endLabel = $this->formatSingleSlot($end);

        if ($startLabel && $endLabel) {
            return $startLabel.' - '.$endLabel;
        }

        return $startLabel ?: ($endLabel ?: '—');
    }

    private function formatSingleSlot($time): ?string
    {
        $normalized = $this->normalizeSlotTime($time);
        if (! $normalized) {
            return null;
        }

        try {
            return Carbon::createFromFormat('H:i:s', $normalized)->format('h:i A');
        } catch (\Throwable $e) {
            return $time;
        }
    }

    private function salonEventColor(string $eventType): string
    {
        return match ($eventType) {
            'advance' => '#4CAF50',
            'started' => '#FF9800',
            'expired' => '#F44336',
            'draft' => '#9E9E9E',
            'completed' => '#00897B',
            default => '#2196F3', // upcoming
        };
    }

    private function salonStatusColor(?string $status, bool $isAdvance = false): string
    {
        if ($isAdvance) {
            return '#4CAF50';
        }

        return match (strtolower((string) $status)) {
            'start' => '#FF9800',
            'reopened' => '#9C27B0',
            'completed' => '#607D8B',
            'draft' => '#9E9E9E',
            default => '#2196F3',
        };
    }

    private function buildCalendarEvents()
    {
        $now = Carbon::now();

        $bookings = MakeupBooking::where('status', 'active')
            ->whereDate('makeup_date_booked', '>=', $now->toDateString())
            ->orderBy('makeup_date_booked')
            ->get()
            ->filter(function ($booking) use ($now) {
                try {
                    $bookingDateTime = $this->parseMakeupDateTime($booking);

                    // Keep all of today's bookings on the calendar; only hide past days' slots
                    if ($bookingDateTime->isSameDay($now)) {
                        return true;
                    }

                    return $bookingDateTime->gt($now);
                } catch (\Throwable $e) {
                    Log::warning('Dashboard calendar skipped booking '.$booking->id.': '.$e->getMessage());

                    return false;
                }
            })
            ->values();

        return $bookings->map(function ($booking) {
            $serviceNames = $this->resolveMakeupServiceNames($booking->makeup_service_name);
            $type = $this->normalizeMakeupType($booking->booking_type);
            $backgroundColor = $this->makeupTypeColor($type);

            $start = $booking->makeup_date_booked;
            try {
                $dt = $this->parseMakeupDateTime($booking);
                // Include time so agenda views show the slot correctly
                $start = $dt->format('Y-m-d\TH:i:s');
            } catch (\Throwable $e) {
                // fall back to date-only
            }

            $dateDisplay = $booking->makeup_date_booked;
            try {
                $dateDisplay = Carbon::parse($booking->makeup_date_booked)->format('d M Y');
            } catch (\Throwable $e) {
                // keep raw date
            }

            return [
                'id' => $booking->id,
                'title1' => 'Type: '.$type.' - '.$serviceNames,
                'title' => trim((string) $booking->makeup_client_name),
                'client_name' => $booking->makeup_client_name,
                'mobile' => $booking->makeup_contact_number,
                'place' => $booking->makeup_place,
                'booking_date' => $dateDisplay,
                'time_slot' => $booking->makeup_time_slot,
                'important_details' => $booking->makeup_clent_address,
                'services' => 'Services: '.($serviceNames ?: 'N/A'),
                'start' => $start,
                'end' => $start,
                'allDay' => false,
                'backgroundColor' => $backgroundColor,
                'borderColor' => $backgroundColor,
                'textColor' => '#FFFFFF',
                'beautician_name' => $booking->beautician_name,
                'beautician_photo' => $this->resolveBeauticianPhoto($booking->beautician_id),
                'booking_type' => ucfirst($type),
            ];
        });
    }

    private function resolveBeauticianPhoto($beauticianId): ?string
    {
        if (! $beauticianId) {
            return null;
        }

        $path = DB::table('users')->where('id', $beauticianId)->value('profile_photo_path');
        if (! $path) {
            return null;
        }

        return asset('storage/'.$path);
    }

    private function parseMakeupDateTime(MakeupBooking $booking): Carbon
    {
        $time = trim((string) $booking->makeup_time_slot);
        $date = trim((string) $booking->makeup_date_booked);

        if ($time === '') {
            return Carbon::parse($date)->startOfDay();
        }

        if (preg_match('/AM|PM/i', $time)) {
            $normalized = strtoupper($time);
            try {
                $parsed = Carbon::createFromFormat('g:i A', $normalized);
            } catch (\Throwable $e) {
                $parsed = Carbon::createFromFormat('h:i A', $normalized);
            }
            $time = $parsed->format('H:i:s');
        } elseif (strlen($time) === 5) {
            $time .= ':00';
        }

        return Carbon::parse($date.' '.$time);
    }

    private function normalizeMakeupType(?string $type): string
    {
        $type = strtolower(trim((string) $type));

        // Legacy bridal rows were stored as "all"
        if ($type === 'all' || $type === '') {
            return 'bridal';
        }

        return in_array($type, ['bridal', 'groom', 'party'], true) ? $type : 'bridal';
    }

    private function makeupTypeColor(string $type): string
    {
        return match ($type) {
            'groom' => '#F44336',
            'party' => '#4CAF50',
            default => '#2196F3', // bridal
        };
    }

    /**
     * Bridal uses MakeupService ids; groom/party historically used Service ids.
     */
    private function resolveMakeupServiceNames($serviceIds): string
    {
        if (! is_array($serviceIds) || empty($serviceIds)) {
            return '';
        }

        $names = MakeupService::whereIn('id', $serviceIds)->pluck('service_name');
        if ($names->isNotEmpty()) {
            return $names->implode(', ');
        }

        return Service::whereIn('service_id', $serviceIds)->pluck('name')->implode(', ');
    }

    private function buildSalesCharts(): array
    {
        $startOfWeek = Carbon::now()->startOfWeek();
        $endOfToday = Carbon::now()->endOfDay();

        // Daily chart: group by payment date (created_at), net of refunds on the sale day.
        // Appointment-date rules from the sales report do not match a Mon–Sun week widget.
        $salesData = $this->sumCompletedSalesByDayName($startOfWeek, $endOfToday);
        $refundAdjustments = $this->sumRefundAdjustmentsByDayName($startOfWeek, $endOfToday);

        $dayMap = [
            'Monday' => 'Mon',
            'Tuesday' => 'Tue',
            'Wednesday' => 'Wed',
            'Thursday' => 'Thu',
            'Friday' => 'Fri',
            'Saturday' => 'Sat',
            'Sunday' => 'Sun',
        ];

        $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];
        $salesByDay = [];
        foreach ($daysOfWeek as $shortDay) {
            $fullDay = array_search($shortDay, $dayMap, true);
            $gross = (float) ($salesData[$fullDay] ?? 0);
            $refunded = (float) ($refundAdjustments[$fullDay] ?? 0);
            $salesByDay[$shortDay] = round(max(0, $gross - $refunded), 2);
        }

        $currentYear = Carbon::now()->year;
        $previousYear = $currentYear - 1;

        $currentYearSales = $this->reportService->netSalesByMonth($currentYear);
        $previousYearSales = $this->reportService->netSalesByMonth($previousYear);

        $months = [
            1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
            5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
            9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
        ];

        $currentYearData = [];
        $previousYearData = [];
        foreach ($months as $num => $month) {
            $currentYearData[] = round(max(0, (float) ($currentYearSales[$num] ?? 0)), 2);
            $previousYearData[] = round(max(0, (float) ($previousYearSales[$num] ?? 0)), 2);
        }

        return [$salesByDay, $months, $currentYearData, $previousYearData, $currentYear, $previousYear];
    }

    private function completedSaleBookingsQuery()
    {
        return Booking::query()
            ->where('payment_status', 'completed')
            ->where('booking_type', Booking::BOOKING_TYPE_SALE)
            ->whereNotNull('booking_code')
            ->where('payment_total', '>', 0);
    }

    private function completedRefundBookingsQuery()
    {
        return Booking::query()
            ->where('payment_status', 'completed')
            ->whereIn('booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded'])
            ->whereNotNull('booking_code');
    }

    private function sumCompletedSalesByDayName(Carbon $from, Carbon $to)
    {
        return $this->completedSaleBookingsQuery()
            ->whereBetween('created_at', [$from, $to])
            ->selectRaw('DAYNAME(created_at) as day, SUM(payment_total) as total')
            ->groupBy('day')
            ->pluck('total', 'day');
    }

    private function sumRefundAdjustmentsByDayName(Carbon $from, Carbon $to)
    {
        $refunds = $this->completedRefundBookingsQuery()
            ->where(function ($q) use ($from, $to) {
                $q->whereBetween('created_at', [$from, $to])
                    ->orWhereHas('parentBooking', function ($parent) use ($from, $to) {
                        $parent->whereBetween('created_at', [$from, $to]);
                    });
            })
            ->with(['parentBooking:booking_id,created_at'])
            ->get(['booking_id', 'parent_id', 'payment_total', 'booking_total', 'created_at']);

        $byDay = [];
        foreach ($refunds as $refund) {
            $amount = abs((float) ($refund->payment_total ?: $refund->booking_total ?: 0));
            if ($amount <= 0) {
                continue;
            }

            $anchor = optional($refund->parentBooking)->created_at ?? $refund->created_at;
            if (! $anchor || $anchor->lt($from) || $anchor->gt($to)) {
                continue;
            }

            $dayName = $anchor->format('l');
            $byDay[$dayName] = ($byDay[$dayName] ?? 0) + $amount;
        }

        return $byDay;
    }

    private function buildBestPerformers()
    {
        $monthStart = Carbon::now()->startOfMonth();
        $monthEnd = Carbon::now()->endOfDay();

        $monthlySalesByBeautician = SplitServiceAmount::with(['beautician'])
            ->whereHas('booking', function ($query) use ($monthStart, $monthEnd) {
                $query->where('payment_status', 'completed')
                    ->where('booking_type', Booking::BOOKING_TYPE_SALE)
                    ->whereNotNull('booking_code')
                    ->whereBetween('created_at', [$monthStart, $monthEnd]);
            })
            ->select(
                'beautician_id',
                DB::raw('SUM(price) as total_sales'),
                DB::raw('COUNT(*) as total_services')
            )
            ->groupBy('beautician_id')
            ->get()
            ->map(function ($item) {
                return (object) [
                    'beautician_name' => $item->beautician->name ?? '',
                    'profile_photo_path' => $item->beautician->profile_photo_path ?? '',
                    'total_sales' => round((float) $item->total_sales, 2),
                    'total_services' => (int) $item->total_services,
                ];
            })
            ->filter(function ($item) {
                return $item->total_sales > 0 && $item->beautician_name !== '';
            })
            ->values();

        if ($monthlySalesByBeautician->isEmpty()) {
            return collect();
        }

        $maxSales = $monthlySalesByBeautician->max('total_sales');

        return $monthlySalesByBeautician
            ->filter(function ($item) use ($maxSales) {
                return abs($item->total_sales - $maxSales) < 0.009;
            })
            ->values();
    }

    public function privacy()
    {
        return view('privacy_policy');
    }

    public function removedAccount()
    {
        return view('remove_my_account');
    }

    public function removedAccountPost(Request $request)
    {
        Log::info($request->all());
    }
}
