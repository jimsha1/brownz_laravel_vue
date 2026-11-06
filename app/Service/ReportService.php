<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\BookingService;
use App\Models\Client;
use App\Models\Vendor;
use App\Models\PurchasePayment;
use App\Models\Consultation;
use App\Models\SplitServiceAmount;
use App\Models\StockPurchase;
use App\Models\User;
use App\Models\WhatsAppInvoice;
use Carbon\Carbon;
use Exception;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ReportService
{

    public function salesReportListAdminApi($data = null)
    {
        $result = Booking::with(['beauticians.user']);



        $startDate = $data->start_date;
        $endDate = $data->end_date;
        if ($endDate != null && $startDate != null) {
            $result->whereBetween('appointment_date', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);
        } else if ($endDate == null && $startDate != null) {
            $result->where('appointment_date', '>=', $startDate . ' 00:00:00');
        } else if ($endDate != null && $startDate == null) {
            $result->where('appointment_date', '<=', $endDate . ' 23:59:59');
        } else {
        }

        if (!empty($data->beautician_name)) {
            $result->whereHas('beauticians.user', function ($query) use ($data) {
                $query->where('name', 'LIKE', '%' . $data->beautician_name . '%');
            });
        }
        if (!empty($data->service_name)) {
            $result->whereHas('beauticians', function ($query) use ($data) {
                $query->where('service_name', 'LIKE', '%' . $data->service_name . '%');
            });
        }

        $result = $result->orderBy('created_at', 'asc')->where('payment_status', 'completed')->get();

        $response = [
            'errorcode' => 0,
            'errormessage' => 'Success',
            'data' => $result
        ];


        return response()->json($response);
    }
    public function salesReportListApi($data = null, $platform = 'web')
    {

        try {
            $result = Booking::whereNotNull('booking_code')
                ->when(!empty($data->booking_code), function ($query) use ($data) {
                    $query->where('booking_code', 'LIKE', '%' . trim($data->booking_code) . '%');
                })
                ->where('payment_status', 'completed');

            $this->applyDateFilters($result, $data);
            $this->applyClientFilter($result, $data);
            $this->applyPaymentModeFilter($result, $data);
            $this->applyBookingTypeFilter($result, $data);
            $this->applyLineTypeFilter($result, $data);

            // Aggregate in SQL — never load all bookings just to sum payments.
            // Refunds subtract from net sales (except Refund-only drill-down).
            $sum_of_amount = $this->sumSalesPaymentAmount((clone $result)->select('bookings.booking_id'), $data);

            $result->with([
                'bookingServices',
                'bookingOnlyServices',
                'bookingItems',
                'bookingPayment' => function ($query) use ($data) {
                    if (!empty($data->payment_mode)) {
                        $query->where('payment_mode', $data->payment_mode);
                    }
                },
            ])->orderBy('created_at', 'DESC');

            if ($platform === 'web') {
                $pageLength = max(1, (int) ($data->page_length ?? 100));
                $page = max(1, (int) ($data->page ?? 1));
                $result = $result->paginate($pageLength, ['*'], 'page', $page);
                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'sum_of_amount' => $sum_of_amount,
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ];
            } else {
                $result = $result->get();

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'sum_of_amount' => $sum_of_amount,
                    'data' => $result,
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function vendorReportListApi($data = null, $platform = 'web')
    {
        try {
            $result = Vendor::query()
                ->withSum('stockPurchases', 'total_purchase_amount')
                ->orderBy('name', 'desc');

            if ($data) {
                $this->applyVendorFilters($result, $data);
            }

            $totalsQuery = clone $result;
            $allVendors = $totalsQuery->get();
            $totals = [
                'total_purchase_amount' => 0,
                'total_credit_amount' => 0,
            ];
            foreach ($allVendors as $v) {
                $totals['total_purchase_amount'] += (float) ($v->stock_purchases_sum_total_purchase_amount ?? 0);
                $totals['total_credit_amount'] += $this->getVendorCreditAmount($v->vendor_id);
            }

            if ($platform === 'web') {
                $pageLength = (int) ($data->page_length ?? 100);
                if ($pageLength <= 0) {
                    $pageLength = 100;
                }

                $result = $result->paginate($pageLength);

                $result->getCollection()->transform(function ($vendor) {
                    $vendor->total_purchase_amount = (float) ($vendor->stock_purchases_sum_total_purchase_amount ?? 0);
                    $vendor->credit_amount = $this->getVendorCreditAmount($vendor->vendor_id);

                    return $vendor;
                });

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'totals' => $totals,
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ];
            } else {
                $result = $result->get();
                $result->transform(function ($vendor) {
                    $vendor->total_purchase_amount = (float) ($vendor->stock_purchases_sum_total_purchase_amount ?? 0);
                    $vendor->credit_amount = $this->getVendorCreditAmount($vendor->vendor_id);

                    return $vendor;
                });
                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'totals' => $totals,
                    'data' => $result,
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    // public function whatsappReportListApi($data = null, $platform = 'web')
    // {
    //     $result = WhatsAppInvoice::with('booking');

    //     if (!empty($data->filter_code)) {
    //         $result->where('booking_code', 'LIKE', '%' . $data->filter_code . '%');
    //     }
    //     if (!empty($data->filter_client_name)) {
    //         $result->whereHas('booking', function ($query) use ($data) {
    //             $query->where('client_name', 'LIKE', '%' . $data->filter_client_name . '%');
    //         });
    //     }
    //     if (!empty($data->filter_status)) {
    //         $result->where('status', $data->filter_status);
    //     }

    //     $result = $result->orderBy('created_at', 'DESC')->get();

    //     if ($platform === 'web') {
    //         $pageLength = max(1, (int) ($data->page_length ?? 100));
    //         $page = max(1, (int) ($data->page ?? 1));
    //         $result = $result->paginate($pageLength, ['*'], 'page', $page);
    //         $response = [
    //             'errorcode' => 0,
    //             'errormessage' => 'Success',
    //             'pagination' => [
    //                 'total' => $result->total(),
    //                 'per_page' => $result->perPage(),
    //                 'current_page' => $result->currentPage(),
    //                 'last_page' => $result->lastPage(),
    //                 'from' => $result->firstItem(),
    //                 'to' => $result->lastItem(),
    //             ],
    //             'data' => $result,
    //         ];
    //     } else {
    //         $result = $result->get();

    //         $response = [
    //             'errorcode' => 0,
    //             'errormessage' => 'Success',
    //             'data' => $result,
    //         ];
    //     }
    // }

    public function whatsappReportListApi($data = null, $platform = 'web')
    {

        try {
            $result = WhatsAppInvoice::with('booking');

            if (!empty($data->filter_code)) {
                $result->where('booking_code', 'LIKE', '%' . $data->filter_code . '%');
            }
            if (!empty($data->filter_client_name)) {
                $result->whereHas('booking', function ($query) use ($data) {
                    $query->where('client_name', 'LIKE', '%' . $data->filter_client_name . '%');
                });
            }
            if (!empty($data->filter_status)) {
                $result->where('status', $data->filter_status);
            }

            $result = $result->orderBy('created_at', 'DESC');

            if ($platform === 'web') {
                $pageLength = max(1, (int) ($data->page_length ?? 100));
                $page = max(1, (int) ($data->page ?? 1));
                $result = $result->paginate($pageLength, ['*'], 'page', $page);
                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ];
            } else {
                $result = $result->get();

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'data' => $result,
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function getVendorCreditAmount($vendorId)
    {
        $totalCredit = PurchasePayment::where('vendor_id', $vendorId)
            ->where('payment_mode', PurchasePayment::PAYMENT_MODE_CREDIT)
            ->sum('amount_taken');

        $manualPaid = PurchasePayment::where('vendor_id', $vendorId)
            ->whereNull('purchase_id')
            ->whereIn('payment_mode', [PurchasePayment::PAYMENT_MODE_CASH, PurchasePayment::PAYMENT_MODE_BANK])
            ->sum('amount_taken');

        return max(0, (float) $totalCredit - (float) $manualPaid);
    }

    public function clientReportListApi($data = null, $platform = 'web')
    {
        try {
            $result = Client::with('user')
                ->leftJoin('users', 'users.id', '=', 'clients.client_id')
                ->select('clients.*')
                ->orderBy('users.name', 'desc');

            $this->applyFilters($result, $data);

            if ($platform === 'web') {
                $pageLength = (int) ($data->page_length ?? 100);
                if ($pageLength <= 0) {
                    $pageLength = 100;
                }

                $result = $result->paginate($pageLength);

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ];
            } else {
                $result = $result->get();

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'data' => $result,
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function taxReportListApi($data = null, $platform = 'web')
    {
        try {
            $taxType = strtolower((string) ($data->tax_type ?? 'output'));
            if (! in_array($taxType, ['output', 'input'], true)) {
                $taxType = 'output';
            }

            $summary = $this->buildTaxReportSummary($data);
            $pageLength = (int) ($data->page_length ?? 100);
            if ($pageLength <= 0) {
                $pageLength = 100;
            }

            if ($taxType === 'input') {
                $query = $this->buildInputTaxQuery($data);
                $totals = $this->sumInputTaxRows((clone $query)->with(['product_rows', 'vendor'])->get());

                if ($platform === 'web') {
                    $result = $query->with(['product_rows', 'vendor'])->paginate($pageLength);
                    $rows = $result->getCollection()->map(fn($purchase) => $this->mapInputTaxPurchase($purchase))->values();
                    $result->setCollection($rows);

                    return response()->json([
                        'errorcode' => 0,
                        'errormessage' => 'Success',
                        'tax_type' => 'input',
                        'summary' => $summary,
                        'totals' => $totals,
                        'pagination' => [
                            'total' => $result->total(),
                            'per_page' => $result->perPage(),
                            'current_page' => $result->currentPage(),
                            'last_page' => $result->lastPage(),
                            'from' => $result->firstItem(),
                            'to' => $result->lastItem(),
                        ],
                        'data' => $result,
                    ]);
                }

                $rows = $query->with(['product_rows', 'vendor'])->get()
                    ->map(fn($purchase) => $this->mapInputTaxPurchase($purchase))
                    ->values();

                return response()->json([
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'tax_type' => 'input',
                    'summary' => $summary,
                    'totals' => $totals,
                    'data' => $rows,
                ]);
            }

            $query = $this->buildOutputTaxQuery($data);
            $totals = $this->sumOutputTaxRows(
                (clone $query)->with(['bookingOnlyServices', 'bookingItems'])->get()
            );

            if ($platform === 'web') {
                $result = $query->with(['bookingOnlyServices', 'bookingItems'])->paginate($pageLength);
                $rows = $result->getCollection()->map(fn($booking) => $this->mapOutputTaxBooking($booking))->values();
                $result->setCollection($rows);

                return response()->json([
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'tax_type' => 'output',
                    'summary' => $summary,
                    'totals' => $totals,
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ]);
            }

            $rows = $query->with(['bookingOnlyServices', 'bookingItems'])->get()
                ->map(fn($booking) => $this->mapOutputTaxBooking($booking))
                ->values();

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'tax_type' => 'output',
                'summary' => $summary,
                'totals' => $totals,
                'data' => $rows,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    private function buildOutputTaxQuery($data)
    {
        $query = Booking::query()
            ->whereNotNull('booking_code')
            ->where('booking_code', '!=', '')
            ->where('payment_status', 'completed')
            ->orderByDesc('created_at');

        $this->applyBookingTypeFilter($query, $data);
        $this->applyDateFilters($query, $data);

        if (! empty($data->booking_code)) {
            $query->where('booking_code', 'LIKE', '%' . $data->booking_code . '%');
        }

        return $query;
    }

    private function buildInputTaxQuery($data)
    {
        $query = StockPurchase::query()
            ->orderByDesc('purchase_date')
            ->orderByDesc('purchase_id');

        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        if ($startDate && $endDate) {
            $query->whereBetween('purchase_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('purchase_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('purchase_date', '<=', $endDate);
        }

        if (! empty($data->purchase_ref)) {
            $ref = trim((string) $data->purchase_ref);
            $query->where(function ($q) use ($ref) {
                $q->where('purchase_ref_no', 'LIKE', '%' . $ref . '%')
                    ->orWhere('purchase_invoice_no', 'LIKE', '%' . $ref . '%');
            });
        }

        if (! empty($data->vendor_name)) {
            $vendorName = trim((string) $data->vendor_name);
            $query->whereHas('vendor', function ($q) use ($vendorName) {
                $q->where('name', 'LIKE', '%' . $vendorName . '%');
            });
        }

        $this->applyInputTaxTypeFilter($query, $data);

        return $query;
    }

    /**
     * Input tax type filter.
     * All (default) → purchases + returns.
     * Purchase → normal purchase entries only.
     * Return / Refund → purchase return entries only.
     */
    private function applyInputTaxTypeFilter($query, $data): void
    {
        $type = $data->type ?? null;
        $type = is_string($type) ? trim($type) : $type;

        if (empty($type) || strcasecmp((string) $type, 'All') === 0) {
            return;
        }

        if (strcasecmp((string) $type, 'Purchase') === 0) {
            $query->where(function ($q) {
                $q->whereNull('parent_id')
                    ->where(function ($inner) {
                        $inner->whereNull('purchase_type')
                            ->orWhere('purchase_type', '!=', 'refund');
                    });
            });

            return;
        }

        if (in_array(strtolower((string) $type), ['return', 'refund'], true)) {
            $query->where(function ($q) {
                $q->whereNotNull('parent_id')
                    ->orWhere('purchase_type', 'refund');
            });
        }
    }

    private function buildTaxReportSummary($data)
    {
        $periodFilters = (object) [
            'start_date' => $data->start_date ?? null,
            'end_date' => $data->end_date ?? null,
        ];

        $outputRows = $this->buildOutputTaxQuery($periodFilters)
            ->with(['bookingOnlyServices', 'bookingItems'])
            ->get();
        $outputTotals = $this->sumOutputTaxRows($outputRows);

        $inputRows = $this->buildInputTaxQuery($periodFilters)
            ->with(['product_rows', 'vendor'])
            ->get();
        $inputTotals = $this->sumInputTaxRows($inputRows);

        $outputTax = (float) ($outputTotals['tax_amount'] ?? 0);
        $inputTax = (float) ($inputTotals['tax_amount'] ?? 0);

        return [
            'output_tax' => round($outputTax, 2),
            'input_tax' => round($inputTax, 2),
            'net_tax' => round($outputTax - $inputTax, 2),
        ];
    }

    private function mapOutputTaxBooking(Booking $booking): array
    {
        $isRefund = in_array($booking->booking_type, [Booking::BOOKING_TYPE_REFUND, 'Refunded'], true);
        $sign = $isRefund ? -1 : 1;

        $serviceTax = (float) $booking->bookingOnlyServices->sum('tax_amount');
        $itemTax = (float) $booking->bookingItems->sum('tax_amount');

        $taxPercent = optional($booking->bookingOnlyServices->first())->tax_percent;
        if ($taxPercent === null || $taxPercent === '') {
            $taxPercent = optional($booking->bookingItems->first())->tax_percent;
        }

        $appointmentDate = optional(
            $booking->bookingOnlyServices->sortBy('appointment_date')->first()
        )->appointment_date;

        if (! $appointmentDate) {
            $appointmentDate = Carbon::parse($booking->created_at)->toDateString();
        } else {
            $appointmentDate = Carbon::parse($appointmentDate)->toDateString();
        }

        return [
            'id' => $booking->booking_id,
            'ref_no' => $booking->booking_code,
            'date' => $appointmentDate,
            'party' => $booking->client_name,
            'booking_type' => $booking->booking_type,
            'tax_percent' => $taxPercent,
            'service_tax' => round($sign * $serviceTax, 2),
            'item_tax' => round($sign * $itemTax, 2),
            'tax_amount' => round($sign * ($serviceTax + $itemTax), 2),
            'is_refund' => $isRefund,
        ];
    }

    private function mapInputTaxPurchase(StockPurchase $purchase): array
    {
        $isReturn = ! empty($purchase->parent_id) || $purchase->purchase_type === 'refund';
        $sign = $isReturn ? -1 : 1;

        $taxAmount = (float) $purchase->product_rows->sum('tax_amount');
        $taxableBase = (float) $purchase->product_rows->sum(function ($row) {
            return ((float) $row->quantity) * ((float) $row->cost_price);
        });

        $taxPercent = optional($purchase->product_rows->first())->tax_percent;

        return [
            'id' => $purchase->purchase_id,
            'ref_no' => $purchase->purchase_ref_no,
            'invoice_no' => $purchase->purchase_invoice_no,
            'date' => $purchase->purchase_date
                ? Carbon::parse($purchase->purchase_date)->toDateString()
                : null,
            'party' => optional($purchase->vendor)->name,
            'tax_percent' => $taxPercent,
            'taxable_base' => round($sign * $taxableBase, 2),
            'tax_amount' => round($sign * $taxAmount, 2),
            'is_return' => $isReturn,
        ];
    }

    private function sumOutputTaxRows($bookings): array
    {
        $serviceTax = 0.0;
        $itemTax = 0.0;
        $taxAmount = 0.0;

        foreach ($bookings as $booking) {
            $row = $booking instanceof Booking
                ? $this->mapOutputTaxBooking($booking)
                : $booking;

            $serviceTax += (float) ($row['service_tax'] ?? 0);
            $itemTax += (float) ($row['item_tax'] ?? 0);
            $taxAmount += (float) ($row['tax_amount'] ?? 0);
        }

        return [
            'service_tax' => round($serviceTax, 2),
            'item_tax' => round($itemTax, 2),
            'tax_amount' => round($taxAmount, 2),
        ];
    }

    private function sumInputTaxRows($purchases): array
    {
        $taxableBase = 0.0;
        $taxAmount = 0.0;

        foreach ($purchases as $purchase) {
            $row = $purchase instanceof StockPurchase
                ? $this->mapInputTaxPurchase($purchase)
                : $purchase;

            $taxableBase += (float) ($row['taxable_base'] ?? 0);
            $taxAmount += (float) ($row['tax_amount'] ?? 0);
        }

        return [
            'taxable_base' => round($taxableBase, 2),
            'tax_amount' => round($taxAmount, 2),
        ];
    }

    public function taxexportToPDF($data = null, $platform = 'web')
    {
        $taxType = strtolower((string) ($data->tax_type ?? 'output'));
        if (! in_array($taxType, ['output', 'input'], true)) {
            $taxType = 'output';
        }

        $summary = $this->buildTaxReportSummary($data);

        if ($taxType === 'input') {
            $rows = $this->buildInputTaxQuery($data)
                ->with(['product_rows', 'vendor'])
                ->get()
                ->map(fn($purchase) => $this->mapInputTaxPurchase($purchase))
                ->values();

            return [
                'tax_type' => 'input',
                'records' => $rows,
                'totals' => $this->sumInputTaxRows($rows),
                'summary' => $summary,
            ];
        }

        $rows = $this->buildOutputTaxQuery($data)
            ->with(['bookingOnlyServices', 'bookingItems'])
            ->get()
            ->map(fn($booking) => $this->mapOutputTaxBooking($booking))
            ->values();

        return [
            'tax_type' => 'output',
            'records' => $rows,
            'totals' => $this->sumOutputTaxRows($rows),
            'summary' => $summary,
        ];
    }

    public function serviceReportListApi($data = null, $platform = 'web')
    {
        try {
            if ($platform !== 'web') {
                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'Platform not supported',
                ]);
            }

            $pageLength = (int) ($data->page_length ?? 100);
            if ($pageLength < 1) {
                $pageLength = 100;
            }
            if ($pageLength > 200) {
                $pageLength = 200;
            }

            $baseQuery = $this->buildServiceReportQuery($data);
            $filteredTotal = (clone $baseQuery)->sum('booking_services.line_total');

            $result = (clone $baseQuery)
                ->with([
                    'booking:booking_id,booking_code',
                    'beautician',
                ])
                ->orderByDesc('booking_services.appointment_date')
                ->orderByDesc('booking_services.booking_service_id')
                ->paginate($pageLength);

            // Skip unused locale accessors on list payload
            $result->getCollection()->each->setAppends([]);

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'pagination' => [
                    'total' => $result->total(),
                    'per_page' => $result->perPage(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ],
                'filtered_total' => round((float) $filteredTotal, 2),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Shared service-report query: lean joins + indexed columns, no heavy whereHas.
     */
    private function buildServiceReportQuery($data)
    {
        $query = BookingService::query()
            ->select('booking_services.*')
            ->join('bookings', 'booking_services.booking_id', '=', 'bookings.booking_id')
            ->whereNull('bookings.deleted_at')
            ->where('booking_services.status', BookingService::STATUS_COMPLETED)
            ->whereNotNull('bookings.booking_code')
            ->where('bookings.booking_code', '!=', '')
            // Exclude refund bookings and negative/refund service lines
            ->whereNotIn('bookings.booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded'])
            ->where(function ($q) {
                $q->whereNull('booking_services.line_total')
                    ->orWhere('booking_services.line_total', '>=', 0);
            });

        $startDate = ! empty($data->start_date) ? $data->start_date : null;
        $endDate = ! empty($data->end_date) ? $data->end_date : null;

        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        // appointment_date is a DATE column — compare as dates (index-friendly)
        if ($startDate && $endDate) {
            $query->whereBetween('booking_services.appointment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('booking_services.appointment_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('booking_services.appointment_date', '<=', $endDate);
        }

        if (! empty($data->beautician_id)) {
            $query->where('booking_services.beautician_id', $data->beautician_id);
        }

        if (! empty($data->service_name)) {
            $serviceName = trim($data->service_name);
            $query->where('booking_services.service_name', 'LIKE', '%' . $serviceName . '%');
        }

        if (! empty($data->booking_code)) {
            $bookingCode = trim($data->booking_code);
            $query->where('bookings.booking_code', 'LIKE', '%' . $bookingCode . '%');
        }

        return $query;
    }

    public function beauticianReportListApi($data = null, $platform = 'web')
    {
        try {
            if ($platform !== 'web') {
                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'Platform not supported',
                ]);
            }

            $pageLength = (int) ($data->page_length ?? 100);
            if ($pageLength < 1) {
                $pageLength = 100;
            }
            if ($pageLength > 200) {
                $pageLength = 200;
            }

            $baseQuery = $this->buildBeauticianReportQuery($data);
            $filteredTotal = (clone $baseQuery)->sum('split_service_amounts.price');
            $filteredCommissionTotal = (clone $baseQuery)->sum('split_service_amounts.commission_amount');

            $result = (clone $baseQuery)
                ->addSelect([
                    DB::raw('COALESCE(booking_services.appointment_date, DATE(split_service_amounts.created_at)) as report_date'),
                ])
                ->with([
                    'booking:booking_id,booking_code',
                    'beautician',
                ])
                ->orderByDesc('report_date')
                ->orderByDesc('split_service_amounts.split_service_amount_id')
                ->paginate($pageLength);

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'pagination' => [
                    'total' => $result->total(),
                    'per_page' => $result->perPage(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ],
                'filtered_total' => round((float) $filteredTotal, 2),
                'filtered_commission_total' => round((float) $filteredCommissionTotal, 2),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Shared beautician/split-amount report query.
     * Filters by the related booking-service appointment date (fallback: split created_at).
     */
    private function buildBeauticianReportQuery($data)
    {
        $query = SplitServiceAmount::query()
            ->select('split_service_amounts.*')
            ->join('bookings', 'split_service_amounts.booking_id', '=', 'bookings.booking_id')
            ->leftJoin(
                'booking_services',
                'split_service_amounts.booking_service_id',
                '=',
                'booking_services.booking_service_id'
            )
            // Raw joins bypass SoftDeletes; exclude trashed bookings so eager-loaded
            // relations (and Vue templates) never see null booking rows.
            ->whereNull('bookings.deleted_at')
            ->whereNotNull('bookings.booking_code')
            ->where('bookings.booking_code', '!=', '');

        $startDate = ! empty($data->start_date) ? $data->start_date : null;
        $endDate = ! empty($data->end_date) ? $data->end_date : null;

        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        if ($startDate && $endDate) {
            $query->whereRaw(
                'COALESCE(booking_services.appointment_date, DATE(split_service_amounts.created_at)) BETWEEN ? AND ?',
                [$startDate, $endDate]
            );
        } elseif ($startDate) {
            $query->whereRaw(
                'COALESCE(booking_services.appointment_date, DATE(split_service_amounts.created_at)) >= ?',
                [$startDate]
            );
        } elseif ($endDate) {
            $query->whereRaw(
                'COALESCE(booking_services.appointment_date, DATE(split_service_amounts.created_at)) <= ?',
                [$endDate]
            );
        }

        if (! empty($data->beautician_id)) {
            $query->where('split_service_amounts.beautician_id', $data->beautician_id);
        }

        if (! empty($data->booking_code)) {
            $query->where('bookings.booking_code', 'LIKE', '%' . trim($data->booking_code) . '%');
        }

        $lineType = strtolower(trim((string) ($data->line_type ?? '')));
        if ($lineType === 'product') {
            $query->where(function ($q) {
                $q->whereNotNull('split_service_amounts.booking_item_id')
                    ->orWhereNotNull('split_service_amounts.item_id');
            });
        } elseif ($lineType === 'service') {
            $query->whereNull('split_service_amounts.booking_item_id')
                ->whereNull('split_service_amounts.item_id');
        }

        return $query;
    }



    /**
     * Date filter for sales report.
     * Service bookings: match booking_services.appointment_date.
     * Item-only bookings (e.g. product refunds with no services): match bookings.created_at.
     * Empty dates default to the current calendar month.
     */
    private function applyDateFilters($query, $data)
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        $startBound = $startDate ? $startDate . ' 00:00:00' : null;
        $endBound = $endDate ? $endDate . ' 23:59:59' : null;

        $query->where(function ($outer) use ($startDate, $endDate, $startBound, $endBound) {
            // Bookings with services dated in range
            $outer->whereHas('bookingOnlyServices', function ($q) use ($startDate, $endDate, $startBound, $endBound) {
                if ($startDate && $endDate) {
                    $q->whereBetween('appointment_date', [$startBound, $endBound]);
                } elseif ($startDate) {
                    $q->where('appointment_date', '>=', $startBound);
                } else {
                    $q->where('appointment_date', '<=', $endBound);
                }
            })
                // Product/item-only bookings (no services) — e.g. product refunds
                ->orWhere(function ($q) use ($startBound, $endBound) {
                    $q->whereDoesntHave('bookingOnlyServices')
                        ->whereHas('bookingItems');

                    if ($startBound && $endBound) {
                        $q->whereBetween('bookings.created_at', [$startBound, $endBound]);
                    } elseif ($startBound) {
                        $q->where('bookings.created_at', '>=', $startBound);
                    } elseif ($endBound) {
                        $q->where('bookings.created_at', '<=', $endBound);
                    }
                });
        });
    }


    private function applyClientFilter($query, $data)
    {
        if (!empty($data->client_name)) {
            $query->where('client_name', 'LIKE', '%' . trim($data->client_name) . '%');
        }
    }

    private function applyPaymentModeFilter($query, $data)
    {
        if (!empty($data->payment_mode)) {
            $query->whereHas('bookingPayment', function ($subQuery) use ($data) {
                $subQuery->where('payment_mode', $data->payment_mode);
            });
        }
    }

    /**
     * Sales report type filter.
     * All (default when empty) → sales + refunds.
     * Refund → refund bookings (+ legacy Refunded).
     * Sale → sales only.
     */
    private function applyBookingTypeFilter($query, $data)
    {
        $type = $data->type ?? $data->booking_type ?? null;
        $type = is_string($type) ? trim($type) : $type;

        if (!empty($type) && strcasecmp((string) $type, 'Sale') === 0) {
            $query->where('booking_type', Booking::BOOKING_TYPE_SALE);

            return;
        }

        if (!empty($type) && strcasecmp((string) $type, 'Sales') === 0) {
            $query->where('booking_type', Booking::BOOKING_TYPE_SALE);

            return;
        }

        if (!empty($type) && strcasecmp((string) $type, 'Refund') === 0) {
            $query->whereIn('booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded']);

            return;
        }

        // Empty / All → sales + refunds (net sales default)
        $query->whereIn('booking_type', [
            Booking::BOOKING_TYPE_SALE,
            Booking::BOOKING_TYPE_REFUND,
            'Refunded',
        ]);
    }

    /**
     * Sales report line filter: Service / Item / All.
     * Shows bookings that have at least one matching line.
     */
    private function applyLineTypeFilter($query, $data)
    {
        $lineType = strtolower(trim((string) ($data->line_type ?? '')));

        if ($lineType === 'service') {
            $query->whereHas('bookingOnlyServices');

            return;
        }

        if ($lineType === 'item' || $lineType === 'product') {
            $query->whereHas('bookingItems');

            return;
        }
    }

    /**
     * Whether a booking is a refund document.
     */
    public function isRefundBooking($booking): bool
    {
        $type = is_object($booking)
            ? ($booking->booking_type ?? '')
            : (is_array($booking) ? ($booking['booking_type'] ?? '') : '');

        return in_array(strtolower(trim((string) $type)), ['refund', 'refunded'], true);
    }

    /**
     * Sign for report amounts: refunds reduce net sales.
     * Refund-only drill-down keeps positive refund volume.
     */
    public function salesReportAmountSign($booking, $data = null): float
    {
        if (! $this->isRefundBooking($booking)) {
            return 1.0;
        }

        $type = $data->type ?? $data->booking_type ?? null;
        if (is_string($type) && strcasecmp(trim($type), 'Refund') === 0) {
            return 1.0;
        }

        return -1.0;
    }

    /**
     * Net payment total for filtered sales report bookings.
     */
    private function sumSalesPaymentAmount($bookingIdQuery, $data = null): float
    {
        $sumQuery = DB::table('booking_payments as bp')
            ->join('bookings', 'bookings.booking_id', '=', 'bp.booking_id')
            ->whereIn('bp.booking_id', $bookingIdQuery);

        if (! empty($data->payment_mode)) {
            $sumQuery->where('bp.payment_mode', $data->payment_mode);
        }

        $type = $data->type ?? $data->booking_type ?? null;
        $refundOnly = is_string($type) && strcasecmp(trim($type), 'Refund') === 0;

        if ($refundOnly) {
            return (float) $sumQuery->sum(DB::raw('ABS(bp.amount_taken - COALESCE(bp.amount_given, 0))'));
        }

        $net = $sumQuery->selectRaw(
            "SUM(CASE WHEN bookings.booking_type IN ('Refund', 'Refunded')
                THEN -ABS(bp.amount_taken - COALESCE(bp.amount_given, 0))
                ELSE ABS(bp.amount_taken - COALESCE(bp.amount_given, 0))
            END) as net_amount"
        )->value('net_amount');

        return (float) ($net ?? 0);
    }

    private function applyFilters($query, $data)
    {
        if (! $data) {
            return;
        }

        if (! empty($data->client_name)) {
            $query->whereHas('user', function ($subQuery) use ($data) {
                $subQuery->where('name', 'LIKE', '%' . $data->client_name . '%');
            });
        }

        if (! empty($data->mobile)) {
            $query->whereHas('user', function ($subQuery) use ($data) {
                $subQuery->where('mobile', 'LIKE', '%' . $data->mobile . '%');
            });
        }

        $status = trim((string) ($data->status ?? $data->user_status ?? ''));
        if ($status !== '' && strtolower($status) !== 'all') {
            $query->whereHas('user', function ($subQuery) use ($status) {
                $subQuery->where('user_status', $status);
            });
        }
    }

    private function applyVendorFilters($query, $data)
    {
        if (!$data) {
            return;
        }
        if (!empty($data->vendor_name)) {
            $query->where('name', 'LIKE', '%' . $data->vendor_name . '%');
        }
        if (!empty($data->vendor_email)) {
            $query->where('email', 'LIKE', '%' . $data->vendor_email . '%');
        }
        if (!empty($data->vendor_phone)) {
            $query->where('phone', 'LIKE', '%' . $data->vendor_phone . '%');
        }
    }


    // Sales Report Export To Pdf
    public function salesexportToPDF($data = null, $platform = 'web')
    {

        $records = Booking::whereNotNull('booking_code')->with('bookingServices.user', 'bookingServices.service', 'bookingPayment', 'bookingOnlyServices', 'bookingItems');

        $vs = $data->search;

        if ($platform == 'web') {

            $this->applyDateFilters($records, $data);

            if (!empty($data->client_name)) {
                $records->where('client_name', $data->client_name);
            }

            if (!empty($data->booking_code)) {
                $records->where('booking_code', $data->booking_code);
            }

            if (!empty($data->payment_mode)) {
                $records->whereHas('bookingPayment', function ($subQuery) use ($data) {
                    $subQuery->where('payment_mode', $data->payment_mode);
                });
            }

            $this->applyBookingTypeFilter($records, $data);
            $this->applyLineTypeFilter($records, $data);
        } else {
        }

        $records = $records->where('payment_status', 'completed')
            ->orderBy('created_at', 'DESC')
            ->get();

        foreach ($records as $record) {
            $sign = $this->salesReportAmountSign($record, $data);

            // Service totals (signed for net sales / All)
            $record->service_sales_total = abs((float) $record->bookingOnlyServices->sum('line_total')) * $sign;
            $record->service_booking_price_total = abs((float) $record->bookingOnlyServices->sum('service_booking_price')) * $sign;
            $record->service_tax_amount = abs((float) $record->bookingOnlyServices->sum('tax_amount')) * $sign;
            $record->service_discount_amount = abs((float) $record->bookingOnlyServices->sum('discount_amount')) * $sign;
            $record->individual_discount_amt = abs((float) $record->bookingOnlyServices->sum('individual_discount_amt')) * $sign;

            // Item totals
            $record->item_sales_total = abs((float) $record->bookingItems->sum('line_total')) * $sign;
            $record->item_tax_amount = abs((float) $record->bookingItems->sum('tax_amount')) * $sign;
            $record->item_discount_amount = abs((float) $record->bookingItems->sum('discount_amount')) * $sign;

            // Combined totals
            $record->tax_amount_total = $record->service_tax_amount + $record->item_tax_amount;
            $record->discount_amount = $record->service_discount_amount + $record->item_discount_amount;
            $record->applied_discount_price = abs((float) ($record->applied_discount_price ?? 0)) * $sign;
            $record->signed_payment_total = abs((float) ($record->payment_total ?? 0)) * $sign;
        }
        return $records;
    }

    public function clientsexportToPDF($data = null, $platform = 'web')
    {
        $records = Client::with('user')
            ->leftJoin('users', 'users.id', '=', 'clients.client_id')
            ->select('clients.*')
            ->orderBy('users.name', 'desc');

        $this->applyFilters($records, $data);

        return [
            'records' => $records->get(),
        ];
    }

    public function updateClientDetails($data)
    {
        try {
            $client = Client::with('user')->findOrFail($data->client_id);
            $user = $client->user;

            if (! $user) {
                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => __('Client user record not found.'),
                ], 404);
            }

            $data->validate([
                'name' => 'required|string|max:255',
                'username' => 'required|string|max:255|unique:users,username,' . $user->id,
                'email' => 'nullable|email|max:255|unique:users,email,' . $user->id,
                'mobile' => ['required', 'digits:10'],
                'status' => 'required|in:active,inactive,pending,blocked',
            ], [
                'mobile.digits' => __('The mobile number must be exactly 10 digits.'),
            ]);

            $mobile = '+91' . $data->mobile;

            $mobileExists = User::where('mobile', $mobile)
                ->where('id', '!=', $user->id)
                ->exists();

            if ($mobileExists) {
                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => __('This mobile number is already in use.'),
                ], 422);
            }

            $user->name = $data->name;
            $user->username = $data->username;
            $user->email = $data->email ?: null;
            $user->mobile = $mobile;
            $user->user_status = $data->status;
            $user->save();

            return response()->json([
                'errorcode' => 0,
                'errormessage' => __('Client details updated successfully.'),
                'data' => $client->fresh('user'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $firstError = collect($e->errors())->flatten()->first();

            return response()->json([
                'errorcode' => 1,
                'errormessage' => $firstError ?: __('Validation failed.'),
                'errors' => $e->errors(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function vendorsexportToPDF($data = null, $platform = 'web')
    {
        $records = Vendor::query()
            ->withSum('stockPurchases', 'total_purchase_amount')
            ->orderBy('name', 'desc');

        if ($data) {
            $this->applyVendorFilters($records, $data);
        }
        $records = $records->get();

        $totals = ['total_purchase_amount' => 0, 'total_credit_amount' => 0];
        $records->transform(function ($vendor) use (&$totals) {
            $vendor->total_purchase_amount = (float) ($vendor->stock_purchases_sum_total_purchase_amount ?? 0);
            $vendor->credit_amount = $this->getVendorCreditAmount($vendor->vendor_id);
            $totals['total_purchase_amount'] += $vendor->total_purchase_amount;
            $totals['total_credit_amount'] += $vendor->credit_amount;

            return $vendor;
        });

        return [
            'records' => $records,
            'totals' => $totals,
        ];
    }

    public function serviceexportToPDF($data = null, $platform = 'web')
    {
        $records = $this->buildServiceReportQuery($data)
            ->with([
                'booking:booking_id,booking_code',
                'beautician',
            ])
            ->orderByDesc('booking_services.appointment_date')
            ->orderByDesc('booking_services.booking_service_id')
            ->get();

        $records->each->setAppends([]);

        return $records;
    }

    public function beauticianexportToPDF($data = null, $platform = 'web')
    {
        return $this->buildBeauticianReportQuery($data)
            ->addSelect([
                DB::raw('COALESCE(booking_services.appointment_date, DATE(split_service_amounts.created_at)) as report_date'),
            ])
            ->with([
                'booking:booking_id,booking_code',
                'beautician',
            ])
            ->orderByDesc('report_date')
            ->orderByDesc('split_service_amounts.split_service_amount_id')
            ->get();
    }
    public function getBarGraphData($request)
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $today = Carbon::now();
            $salesData = Booking::where('payment_status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $today])
                ->selectRaw('DAYNAME(created_at) as day, SUM(payment_total) as total')
                ->groupBy('day')
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->pluck('total', 'day');

            $daysOfWeek = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];

            $salesByDay = array_replace(array_fill_keys($daysOfWeek, 0), $salesData->toArray());

            $monthlySales = Booking::where('payment_status', 'completed')
                ->selectRaw('MONTH(created_at) as month, SUM(payment_total) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            $months = ["Jan", "Feb", "Mar", "Apr", "May", "June", "July", "Aug", "Sep", "Oct", "Nov", "Dec"];
            $salesByMonth = array_replace(array_fill_keys($months, 0), $monthlySales->toArray());

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'data' => $salesByDay,
                'data_monthly' => $salesByMonth,
            ]);
        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function salesSummary($request, $returnRawData = false)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
            $request->merge([
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);
        }
        $paymentMode = $request->input('payment_mode');

        $query = Booking::whereNotNull('booking_code')
            ->where('payment_status', 'completed');

        $this->applyBookingTypeFilter($query, $request);
        $this->applyDateFilters($query, $request);
        $this->applyLineTypeFilter($query, $request);

        $bookings = $query->with('bookingPayment', 'bookingServices', 'bookingOnlyServices', 'bookingItems')->get();

        $totalSales = 0;
        $cashSales = 0;
        $onlineSales = 0;
        $cardSales = 0;
        $creditSales = 0;
        $totalTaxAmount = 0;

        foreach ($bookings as $booking) {
            $sign = $this->salesReportAmountSign($booking, $request);

            $paymentSum = 0;
            foreach ($booking->bookingPayment as $payment) {
                $netTaken = (float) $payment->amount_taken - (float) ($payment->amount_given ?? 0);
                $signedTaken = abs($netTaken) * $sign;
                $paymentSum += $signedTaken;

                switch ($payment->payment_mode) {
                    case 'cash':
                        $cashSales += $signedTaken;
                        break;
                    case 'online':
                        $onlineSales += $signedTaken;
                        break;
                    case 'card':
                        $cardSales += $signedTaken;
                        break;
                    case 'credit':
                        $creditSales += $signedTaken;
                        break;
                }
            }
            $totalSales += $paymentSum;

            $taxSum = abs((float) (
                $booking->bookingOnlyServices->sum('tax_amount')
                + $booking->bookingItems->sum('tax_amount')
            )) * $sign;
            $totalTaxAmount += $taxSum;
        }

        $validModes = ['cash', 'online', 'card', 'credit'];
        if ($paymentMode) {
            if (!in_array($paymentMode, $validModes)) {
                if ($returnRawData) {
                    throw new \Exception('Invalid payment_mode. Use cash, online, card, or credit.');
                }

                return response()->json([
                    'errorcode' => 1,
                    'errormessage' => 'Invalid payment_mode. Use cash, online, card, or credit.'
                ], 400);
            }
            $totalSales = ${$paymentMode . 'Sales'};
            foreach ($validModes as $mode) {
                if ($mode !== $paymentMode) {
                    ${$mode . 'Sales'} = 0;
                }
            }
        }

        $summary = [
            'start_date' => $startDate,
            'end_date' => $endDate,
            'payment_mode' => $paymentMode,
            'total_sales' => number_format($totalSales, 2, '.', ''),
            'cash_sales' => number_format($cashSales, 2, '.', ''),
            'online_sales' => number_format($onlineSales, 2, '.', ''),
            'card_sales' => number_format($cardSales, 2, '.', ''),
            'credit_sales' => number_format($creditSales, 2, '.', ''),
            'total_tax_amount' => number_format($totalTaxAmount, 2, '.', ''),
        ];

        if ($returnRawData) {
            return $summary;
        }

        return response()->json([
            'errorcode' => 0,
            'errormessage' => 'Success',
            'summary' => $summary
        ]);
    }

    /**
     * Net sales (sales − refunds) for dashboard charts, keyed by English day name.
     * Uses the same date rules as the sales report (appointment_date / item created_at).
     *
     * @return array<string, float>
     */
    public function netSalesByDayName(Carbon $from, Carbon $to): array
    {
        $request = new \Illuminate\Http\Request([
            'start_date' => $from->toDateString(),
            'end_date' => $to->toDateString(),
            'type' => 'All',
        ]);

        $byDay = [];
        foreach ($this->salesReportBookings($request) as $booking) {
            $anchor = $this->resolveSalesReportAnchorDate($booking);
            if (! $anchor || $anchor->lt($from->copy()->startOfDay()) || $anchor->gt($to->copy()->endOfDay())) {
                continue;
            }

            $amount = $this->signedNetPaymentAmount($booking, $request);
            $dayName = $anchor->format('l');
            $byDay[$dayName] = round(($byDay[$dayName] ?? 0) + $amount, 2);
        }

        return $byDay;
    }

    /**
     * Net sales (sales − refunds) for dashboard charts, keyed by month number (1–12).
     *
     * @return array<int, float>
     */
    public function netSalesByMonth(int $year): array
    {
        $request = new \Illuminate\Http\Request([
            'start_date' => sprintf('%d-01-01', $year),
            'end_date' => sprintf('%d-12-31', $year),
            'type' => 'All',
        ]);

        $byMonth = [];
        foreach ($this->salesReportBookings($request) as $booking) {
            $anchor = $this->resolveSalesReportAnchorDate($booking);
            if (! $anchor || (int) $anchor->format('Y') !== $year) {
                continue;
            }

            $amount = $this->signedNetPaymentAmount($booking, $request);
            $month = (int) $anchor->format('n');
            $byMonth[$month] = round(($byMonth[$month] ?? 0) + $amount, 2);
        }

        return $byMonth;
    }

    /**
     * Bookings for sales report filters (completed Sale / Refund).
     */
    private function salesReportBookings($data)
    {
        $query = Booking::whereNotNull('booking_code')
            ->where('payment_status', 'completed');

        $this->applyBookingTypeFilter($query, $data);
        $this->applyDateFilters($query, $data);
        $this->applyLineTypeFilter($query, $data);

        return $query->with(['bookingPayment', 'bookingOnlyServices', 'bookingItems'])->get();
    }

    /**
     * Date used to place a booking on sales charts (matches report listing).
     */
    private function resolveSalesReportAnchorDate(Booking $booking): ?Carbon
    {
        $appointment = $booking->bookingOnlyServices
            ->pluck('appointment_date')
            ->filter()
            ->sort()
            ->first();

        if ($appointment) {
            return Carbon::parse($appointment);
        }

        return $booking->created_at ? Carbon::parse($booking->created_at) : null;
    }

    /**
     * Net payment for one booking (taken − given), signed for refunds.
     */
    private function signedNetPaymentAmount(Booking $booking, $data = null): float
    {
        $sign = $this->salesReportAmountSign($booking, $data);
        $net = 0.0;
        foreach ($booking->bookingPayment as $payment) {
            $net += abs((float) $payment->amount_taken - (float) ($payment->amount_given ?? 0));
        }

        if ($net <= 0 && $booking->payment_total !== null) {
            $net = abs((float) $booking->payment_total);
        }

        return $net * $sign;
    }

    /**
     * One row per day for the selected range. Uses the same date and refund rules as the sales report.
     *
     * @return array{start_date:string,end_date:string,days:array<int,array<string,mixed>>,totals:array<string,mixed>}
     */
    public function dailySalesRows($data): array
    {
        $startDate = trim((string) ($data->start_date ?? ''));
        $endDate = trim((string) ($data->end_date ?? ''));

        if ($startDate === '') {
            $startDate = now()->startOfMonth()->toDateString();
        }
        if ($endDate === '') {
            $endDate = now()->toDateString();
        }

        try {
            $start = Carbon::parse($startDate)->startOfDay();
            $end = Carbon::parse($endDate)->startOfDay();
        } catch (\Exception $e) {
            throw new \InvalidArgumentException('Enter a valid start and end date.');
        }

        if ($end->lt($start)) {
            throw new \InvalidArgumentException('End date must be on or after the start date.');
        }

        if ($start->diffInDays($end) > 92) {
            throw new \InvalidArgumentException('Choose a range of 92 days or less.');
        }

        $request = new \Illuminate\Http\Request([
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'type' => 'All',
        ]);

        $days = [];
        for ($cursor = $start->copy(); $cursor->lte($end); $cursor->addDay()) {
            $days[$cursor->toDateString()] = $this->emptyDailySalesRow($cursor);
        }

        foreach ($this->salesReportBookings($request) as $booking) {
            $anchor = $this->resolveSalesReportAnchorDate($booking);
            if (! $anchor) {
                continue;
            }

            $key = $anchor->copy()->startOfDay()->toDateString();
            if (! isset($days[$key])) {
                continue;
            }

            $sign = $this->salesReportAmountSign($booking, $request);
            $isRefund = $this->isRefundBooking($booking);

            $days[$key]['bookings']++;
            if ($isRefund) {
                $days[$key]['refund_count']++;
            }

            $days[$key]['service'] += $this->signedRelationAmount($booking, 'bookingOnlyServices', 'line_total', $sign);
            $days[$key]['item'] += $this->signedRelationAmount($booking, 'bookingItems', 'line_total', $sign);
            $days[$key]['tax'] += $this->signedRelationAmount($booking, 'bookingOnlyServices', 'tax_amount', $sign)
                + $this->signedRelationAmount($booking, 'bookingItems', 'tax_amount', $sign);
            $days[$key]['discount'] += $this->signedDiscountAmount($booking, $sign);

            $paymentNet = 0.0;
            foreach ($booking->bookingPayment as $payment) {
                $taken = abs((float) $payment->amount_taken - (float) ($payment->amount_given ?? 0)) * $sign;
                $paymentNet += $taken;
                $mode = strtolower(trim((string) $payment->payment_mode));
                if (isset($days[$key][$mode])) {
                    $days[$key][$mode] += $taken;
                }
                if ($isRefund) {
                    $days[$key]['refund'] += abs($taken);
                }
            }

            if ($booking->bookingPayment->isEmpty() && $booking->payment_total !== null) {
                $paymentNet = abs((float) $booking->payment_total) * $sign;
                if ($isRefund) {
                    $days[$key]['refund'] += abs($paymentNet);
                }
            }

            $days[$key]['net'] += $paymentNet;
        }

        $rows = [];
        foreach ($days as $row) {
            $rows[] = $this->formatDailySalesRow($row);
        }

        return [
            'start_date' => $start->toDateString(),
            'end_date' => $end->toDateString(),
            'days' => $rows,
            'totals' => $this->dailySalesTotals($rows),
        ];
    }

    public function dailySalesReport($data)
    {
        try {
            $payload = $this->dailySalesRows($data);

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'start_date' => $payload['start_date'],
                'end_date' => $payload['end_date'],
                'days' => $payload['days'],
                'totals' => $payload['totals'],
            ]);
        } catch (\InvalidArgumentException $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    private function emptyDailySalesRow(Carbon $date): array
    {
        return [
            'date' => $date->toDateString(),
            'date_label' => $date->format('d-m-Y'),
            'weekday' => $date->format('l'),
            'bookings' => 0,
            'refund_count' => 0,
            'service' => 0.0,
            'item' => 0.0,
            'tax' => 0.0,
            'discount' => 0.0,
            'cash' => 0.0,
            'card' => 0.0,
            'online' => 0.0,
            'credit' => 0.0,
            'refund' => 0.0,
            'net' => 0.0,
        ];
    }

    private function signedRelationAmount(Booking $booking, string $relation, string $field, float $sign): float
    {
        $lines = $booking->{$relation} ?? [];
        $total = 0.0;
        foreach ($lines as $line) {
            $total += abs((float) ($line->{$field} ?? 0));
        }

        return $total * $sign;
    }

    private function signedDiscountAmount(Booking $booking, float $sign): float
    {
        $discount = abs((float) ($booking->applied_discount_price ?? 0));

        foreach ($booking->bookingOnlyServices as $service) {
            $discount += abs((float) ($service->discount_amount ?? 0));
            $discount += abs((float) ($service->individual_discount_amt ?? 0));
        }

        foreach ($booking->bookingItems as $item) {
            $discount += abs((float) ($item->discount_amount ?? 0));
        }

        return $discount * $sign;
    }

    private function formatDailySalesRow(array $row): array
    {
        foreach (['service', 'item', 'tax', 'discount', 'cash', 'card', 'online', 'credit', 'refund', 'net'] as $field) {
            $row[$field] = number_format((float) $row[$field], 2, '.', '');
        }

        $row['bookings'] = (int) $row['bookings'];
        $row['refund_count'] = (int) $row['refund_count'];

        return $row;
    }

    private function dailySalesTotals(array $rows): array
    {
        $totals = [
            'bookings' => 0,
            'refund_count' => 0,
            'service' => 0.0,
            'item' => 0.0,
            'tax' => 0.0,
            'discount' => 0.0,
            'cash' => 0.0,
            'card' => 0.0,
            'online' => 0.0,
            'credit' => 0.0,
            'refund' => 0.0,
            'net' => 0.0,
        ];

        foreach ($rows as $row) {
            foreach ($totals as $field => $value) {
                $totals[$field] += (float) $row[$field];
            }
        }

        foreach (['service', 'item', 'tax', 'discount', 'cash', 'card', 'online', 'credit', 'refund', 'net'] as $field) {
            $totals[$field] = number_format((float) $totals[$field], 2, '.', '');
        }

        $totals['bookings'] = (int) $totals['bookings'];
        $totals['refund_count'] = (int) $totals['refund_count'];

        return $totals;
    }

    public function serviceSummary($data, $returnRawData = false)
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        // Aggregate in SQL instead of loading every row into PHP
        $summary = $this->buildServiceReportQuery($data)
            ->select([
                DB::raw('booking_services.service_name as service_name'),
                DB::raw('SUM(COALESCE(booking_services.quantity, 1)) as count'),
                DB::raw('ROUND(SUM(COALESCE(booking_services.line_total, 0)), 2) as total_service_price'),
            ])
            ->groupBy('booking_services.service_name')
            ->orderBy('booking_services.service_name')
            ->get()
            ->map(function ($row) {
                return [
                    'service_name' => $row->service_name,
                    'count' => (int) $row->count,
                    'total_service_price' => (float) $row->total_service_price,
                ];
            })
            ->values()
            ->toArray();

        $totalCount = array_sum(array_column($summary, 'count'));
        $totalPrice = array_sum(array_column($summary, 'total_service_price'));

        if ($returnRawData) {
            return [
                'summary' => $summary,
                'total_count' => $totalCount,
                'total_service_price' => round($totalPrice, 2),
            ];
        }

        return response()->json([
            'errorcode' => 0,
            'errormessage' => 'Success',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $summary,
            'total_count' => $totalCount,
            'total_service_price' => round($totalPrice, 2),
        ]);
    }

    public function beauticianSummary($data, $returnRawData = false)
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        $rows = $this->buildBeauticianReportQuery($data)
            ->with([
                'user:id,name',
                'employee.salary',
                'employee.attendence',
            ])
            ->orderByDesc('split_service_amounts.created_at')
            ->get();

        $summary = $rows->groupBy('beautician_id')->map(function ($group) {
            $sumTotalAmount = $group->sum('price');
            $sumCommissionAmount = round((float) $group->sum(function ($row) {
                return (float) ($row->commission_amount ?? 0);
            }), 2);
            $employee = optional($group->first())->employee;
            $targetValue = optional(optional($employee)->salary)->target_value ?? 0.00;

            // Count each attendance row once per beautician (not once per split row)
            $totalOvertimeInSeconds = 0;
            $attendances = optional($employee)->attendence ?? collect();
            foreach ($attendances as $attendance) {
                if (empty($attendance->overtime)) {
                    continue;
                }
                $parts = explode(':', $attendance->overtime);
                if (count($parts) < 3) {
                    continue;
                }
                [$h, $m, $s] = $parts;
                $totalOvertimeInSeconds += ((int) $h * 3600) + ((int) $m * 60) + (int) $s;
            }

            $hours = floor($totalOvertimeInSeconds / 3600);
            $minutes = floor(($totalOvertimeInSeconds % 3600) / 60);
            $seconds = $totalOvertimeInSeconds % 60;
            $formattedOvertime = sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);

            $balanceAmount = $targetValue > $sumTotalAmount
                ? $targetValue - $sumTotalAmount
                : 0.00;

            return [
                'beautician_name' => optional(optional($group->first())->user)->name
                    ?? optional(optional($group->first())->beautician)->name
                    ?? 'N/A',
                'number_of_services' => $group->count(),
                'sum_total_amount' => $sumTotalAmount,
                'sum_commission_amount' => $sumCommissionAmount,
                'target_value' => $targetValue,
                'balance_amount' => number_format($balanceAmount, 2, '.', ''),
                'total_overtime' => $formattedOvertime,
            ];
        })->values()->toArray();

        if ($returnRawData) {
            return $summary;
        }

        return response()->json([
            'errorcode' => 0,
            'errormessage' => 'Success',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $summary,
            'total_beauticians' => count($summary),
            'total_services' => array_sum(array_column($summary, 'number_of_services')),
            'total_amount' => round(array_sum(array_column($summary, 'sum_total_amount')), 2),
            'total_commission' => round(array_sum(array_column($summary, 'sum_commission_amount')), 2),
        ]);
    }

    public function consultationReportList($data = null, $platform = 'web')
    {
        try {
            $result = $this->consultationReportBaseQuery($data);

            if ($platform === 'web') {
                $pageLength = (int) ($data->page_length ?? 100);
                $pageLength = $pageLength > 0 ? $pageLength : 100;
                $result = $result->orderBy('created_at', 'DESC')->paginate($pageLength);

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'pagination' => [
                        'total' => $result->total(),
                        'per_page' => $result->perPage(),
                        'current_page' => $result->currentPage(),
                        'last_page' => $result->lastPage(),
                        'from' => $result->firstItem(),
                        'to' => $result->lastItem(),
                    ],
                    'data' => $result,
                ];
            } else {
                $result = $result->orderBy('created_at', 'DESC')->get();

                $response = [
                    'errorcode' => 0,
                    'errormessage' => 'Success',
                    'data' => $result,
                ];
            }

            return response()->json($response);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Non-paginated consultation rows for PDF export.
     */
    public function consultationReportExport($data = null)
    {
        return $this->consultationReportBaseQuery($data)
            ->orderBy('created_at', 'DESC')
            ->get();
    }

    private function consultationReportBaseQuery($data = null)
    {
        $result = Consultation::with(
            'skin_analyses',
            'skin_lifestyles',
            'skin_histories',
            'skin_conclusions',
            'hair_analyses',
            'hair_histories',
            'hair_client_desires'
        )->where('status', 'completed');

        if ($data) {
            if (! empty($data->client_name)) {
                $result->where('customer_name', 'like', '%' . $data->client_name . '%');
            }

            if (! empty($data->mobile)) {
                $result->where('customer_mobile', 'like', '%' . $data->mobile . '%');
            }

            if (! empty($data->type)) {
                $result->where('type', $data->type);
            }

            $approveStatus = strtolower(trim((string) ($data->approve_status ?? '')));
            if ($approveStatus !== '' && $approveStatus !== 'all') {
                if ($approveStatus === 'approved') {
                    $result->where('approve_status', 'approved');
                } elseif ($approveStatus === 'pending') {
                    $result->where(function ($query) {
                        $query->where('approve_status', 'pending')
                            ->orWhereNull('approve_status');
                    });
                }
            }

            $startDate = $data->start_date ?? null;
            $endDate = $data->end_date ?? null;
            if ($startDate && $endDate) {
                $result->whereBetween('date', [$startDate, $endDate]);
            } elseif ($startDate) {
                $result->where('date', '>=', $startDate);
            } elseif ($endDate) {
                $result->where('date', '<=', $endDate);
            }
        }

        return $result;
    }

    public function skinConsultationSummary($data, $returnRawData = false)
    {
        $startDate = $data->start_date ?? null;
        $endDate = $data->end_date ?? null;

        $result = BookingService::with('booking', 'beautician', 'user');

        $result = $result->where('status', 'completed')
            ->whereHas('booking', function ($query) {
                $query->whereNotNull('booking_code');
            });

        if ($startDate && $endDate) {
            $result->whereHas('booking', function ($query) use ($startDate, $endDate) {
                $query->whereBetween('booking_date', [$startDate, $endDate]);
            });
        }

        $result = $result->get();

        $summary = $result->groupBy('service_name')->map(function ($group) {
            return [
                'service_name' => $group->first()->service_name,
                'count' => $group->sum('quantity'),
                'total_service_price' => round($group->sum(function ($item) {
                    return $item->line_total ?? 0;
                }), 2),
            ];
        })->values()->toArray();

        $totalCount = array_sum(array_column($summary, 'count'));
        $totalPrice = array_sum(array_column($summary, 'total_service_price'));

        if ($returnRawData) {
            return [
                'summary' => $summary,
                'total_count' => $totalCount,
                'total_service_price' => round($totalPrice, 2),
            ];
        }

        return response()->json([
            'errorcode' => 0,
            'errormessage' => 'Success',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'data' => $summary,
            'total_count' => $totalCount,
            'total_service_price' => round($totalPrice, 2),
        ]);
    }

    public function editLogEditors()
    {
        $ids = collect();

        if (Schema::hasTable('booking_edit_logs')) {
            $ids = $ids->merge(
                DB::table('booking_edit_logs')->whereNotNull('edited_by')->distinct()->pluck('edited_by')
            );
        }
        if (Schema::hasTable('stock_purchase_edit_logs')) {
            $ids = $ids->merge(
                DB::table('stock_purchase_edit_logs')->whereNotNull('edited_by')->distinct()->pluck('edited_by')
            );
        }
        if (Schema::hasTable('stock_voucher_edit_logs')) {
            $ids = $ids->merge(
                DB::table('stock_voucher_edit_logs')->whereNotNull('edited_by')->distinct()->pluck('edited_by')
            );
        }

        $ids = $ids->filter()->unique()->values();
        if ($ids->isEmpty()) {
            return collect();
        }

        return User::query()
            ->whereIn('id', $ids)
            ->orderBy('name')
            ->get(['id', 'name']);
    }

    public function editLogSourceLabel(?string $source): string
    {
        return match ($source) {
            'booking' => 'Booking',
            'purchase' => 'Purchase',
            'voucher' => 'Stock Voucher',
            default => 'All',
        };
    }

    public function editLogReportListApi($data = null)
    {
        try {
            $pageLength = (int) ($data->page_length ?? 100);
            if ($pageLength <= 0) {
                $pageLength = 100;
            }

            $query = $this->editLogUnionQuery($data);
            if (! $query) {
                $result = new LengthAwarePaginator([], 0, $pageLength, 1);
            } else {
                $result = $query->paginate($pageLength);
                $rows = $result->getCollection()->map(fn($row) => $this->mapEditLogRow($row))->values();
                $result->setCollection($rows);
            }

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'pagination' => [
                    'total' => $result->total(),
                    'per_page' => $result->perPage(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ],
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function editLogRows($data = null)
    {
        $query = $this->editLogUnionQuery($data);
        if (! $query) {
            return collect();
        }

        return $query->get()->map(fn($row) => $this->mapEditLogRow($row))->values();
    }

    private function editLogUnionQuery($data)
    {
        $queries = $this->editLogSelectQueries($data);
        if ($queries === []) {
            return null;
        }

        $union = array_shift($queries);
        foreach ($queries as $query) {
            $union->unionAll($query);
        }

        return DB::query()
            ->fromSub($union, 'edit_logs')
            ->orderByDesc('created_at')
            ->orderByDesc('id');
    }

    private function editLogSelectQueries($data): array
    {
        $source = strtolower(trim((string) ($data->source_type ?? 'all')));
        if (! in_array($source, ['all', 'booking', 'purchase', 'voucher'], true)) {
            $source = 'all';
        }

        $queries = [];
        if (($source === 'all' || $source === 'booking') && Schema::hasTable('booking_edit_logs')) {
            $queries[] = $this->bookingEditLogSelect($data);
        }
        if (($source === 'all' || $source === 'purchase') && Schema::hasTable('stock_purchase_edit_logs')) {
            $queries[] = $this->purchaseEditLogSelect($data);
        }
        if (($source === 'all' || $source === 'voucher') && Schema::hasTable('stock_voucher_edit_logs')) {
            $queries[] = $this->voucherEditLogSelect($data);
        }

        return $queries;
    }

    private function bookingEditLogSelect($data)
    {
        $query = DB::table('booking_edit_logs')
            ->leftJoin('bookings', 'bookings.booking_id', '=', 'booking_edit_logs.booking_id')
            ->leftJoin('users', 'users.id', '=', 'booking_edit_logs.edited_by')
            ->selectRaw("
                booking_edit_logs.id as id,
                'booking' as source_type,
                booking_edit_logs.booking_id as record_id,
                COALESCE(bookings.booking_code, '') as reference_no,
                booking_edit_logs.edit_reason as edit_reason,
                booking_edit_logs.edited_by as edited_by,
                COALESCE(users.name, '') as edited_by_name,
                booking_edit_logs.created_at as created_at
            ");

        $this->applyEditLogFilters(
            $query,
            $data,
            'booking_edit_logs.created_at',
            'booking_edit_logs.edited_by',
            'booking_edit_logs.edit_reason',
            'bookings.booking_code'
        );

        return $query;
    }

    private function purchaseEditLogSelect($data)
    {
        $query = DB::table('stock_purchase_edit_logs')
            ->leftJoin('stock_purchases', 'stock_purchases.purchase_id', '=', 'stock_purchase_edit_logs.purchase_id')
            ->leftJoin('users', 'users.id', '=', 'stock_purchase_edit_logs.edited_by')
            ->selectRaw("
                stock_purchase_edit_logs.id as id,
                'purchase' as source_type,
                stock_purchase_edit_logs.purchase_id as record_id,
                COALESCE(stock_purchases.purchase_ref_no, '') as reference_no,
                stock_purchase_edit_logs.edit_reason as edit_reason,
                stock_purchase_edit_logs.edited_by as edited_by,
                COALESCE(users.name, '') as edited_by_name,
                stock_purchase_edit_logs.created_at as created_at
            ");

        $this->applyEditLogFilters(
            $query,
            $data,
            'stock_purchase_edit_logs.created_at',
            'stock_purchase_edit_logs.edited_by',
            'stock_purchase_edit_logs.edit_reason',
            'stock_purchases.purchase_ref_no'
        );

        return $query;
    }

    private function voucherEditLogSelect($data)
    {
        $query = DB::table('stock_voucher_edit_logs')
            ->leftJoin('transaction_groups', 'transaction_groups.id', '=', 'stock_voucher_edit_logs.transaction_group_id')
            ->leftJoin('users', 'users.id', '=', 'stock_voucher_edit_logs.edited_by')
            ->selectRaw("
                stock_voucher_edit_logs.id as id,
                'voucher' as source_type,
                stock_voucher_edit_logs.transaction_group_id as record_id,
                COALESCE(NULLIF(stock_voucher_edit_logs.voucher_no, ''), transaction_groups.voucher_no, '') as reference_no,
                stock_voucher_edit_logs.edit_reason as edit_reason,
                stock_voucher_edit_logs.edited_by as edited_by,
                COALESCE(users.name, '') as edited_by_name,
                stock_voucher_edit_logs.created_at as created_at
            ");

        $this->applyEditLogFilters(
            $query,
            $data,
            'stock_voucher_edit_logs.created_at',
            'stock_voucher_edit_logs.edited_by',
            'stock_voucher_edit_logs.edit_reason',
            'stock_voucher_edit_logs.voucher_no',
            'transaction_groups.voucher_no'
        );

        return $query;
    }

    private function applyEditLogFilters($query, $data, string $createdAtColumn, string $editedByColumn, string $reasonColumn, string ...$referenceColumns): void
    {
        $startDate = trim((string) ($data->start_date ?? ''));
        $endDate = trim((string) ($data->end_date ?? ''));
        $editedBy = $data->edited_by ?? '';
        $search = trim((string) ($data->search ?? ''));

        if ($startDate !== '') {
            $query->where($createdAtColumn, '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate !== '') {
            $query->where($createdAtColumn, '<=', Carbon::parse($endDate)->endOfDay());
        }
        if ($editedBy !== '' && $editedBy !== null) {
            $query->where($editedByColumn, $editedBy);
        }
        if ($search !== '') {
            $like = '%' . addcslashes($search, '%_\\') . '%';
            $query->where(function ($inner) use ($like, $reasonColumn, $referenceColumns) {
                $inner->where($reasonColumn, 'like', $like);
                foreach ($referenceColumns as $column) {
                    $inner->orWhere($column, 'like', $like);
                }
            });
        }
    }

    private function mapEditLogRow($row): array
    {
        $createdAt = ! empty($row->created_at) ? Carbon::parse($row->created_at) : null;
        $source = (string) ($row->source_type ?? '');
        $recordId = ! empty($row->record_id) ? (int) $row->record_id : null;
        $viewUrl = null;

        if ($recordId) {
            $viewUrl = match ($source) {
                'booking' => '/salon/booking/tab/' . $recordId . '/info',
                'purchase' => '/stock/stock-purchase/tab/' . $recordId . '/info',
                'voucher' => '/stock/stock-voucher/tab/' . $recordId . '/info',
                default => null,
            };
        }

        return [
            'id' => (int) $row->id,
            'source_type' => $source,
            'source_label' => $this->editLogSourceLabel($source),
            'record_id' => $recordId,
            'reference_no' => (string) ($row->reference_no ?? ''),
            'edit_reason' => (string) ($row->edit_reason ?? ''),
            'edited_by' => ! empty($row->edited_by) ? (int) $row->edited_by : null,
            'edited_by_name' => (string) ($row->edited_by_name ?? ''),
            'created_at' => $createdAt ? $createdAt->toDateTimeString() : null,
            'edited_at' => $createdAt ? $createdAt->format('d M Y, h:i A') : '',
            'view_url' => $viewUrl,
        ];
    }

    public function usedProductReportListApi($data = null)
    {
        try {
            $pageLength = (int) ($data->page_length ?? 100);
            if ($pageLength < 1) {
                $pageLength = 100;
            }
            if ($pageLength > 200) {
                $pageLength = 200;
            }

            $base = $this->usedProductReportQuery($data);
            $totalCost = (float) ((clone $base)
                ->select(DB::raw('COALESCE(SUM(CASE WHEN p.pack_size > 0 THEN p.quantity * p.cost_price / p.pack_size ELSE 0 END), 0) as total_cost'))
                ->value('total_cost'));

            $result = (clone $base)
                ->orderByDesc('s.appointment_date')
                ->orderByDesc('p.booking_service_product_id')
                ->paginate($pageLength);

            $rows = $result->getCollection()->map(fn($row) => $this->mapUsedProductRow($row))->values();
            $result->setCollection($rows);

            return response()->json([
                'errorcode' => 0,
                'errormessage' => 'Success',
                'pagination' => [
                    'total' => $result->total(),
                    'per_page' => $result->perPage(),
                    'current_page' => $result->currentPage(),
                    'last_page' => $result->lastPage(),
                    'from' => $result->firstItem(),
                    'to' => $result->lastItem(),
                ],
                'total_cost' => round($totalCost, 2),
                'data' => $result,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function usedProductReportRows($data = null)
    {
        return $this->usedProductReportQuery($data)
            ->orderByDesc('s.appointment_date')
            ->orderByDesc('p.booking_service_product_id')
            ->get()
            ->map(fn($row) => $this->mapUsedProductRow($row))
            ->values();
    }

    private function usedProductReportQuery($data)
    {
        $status = strtolower(trim((string) ($data->status ?? 'used')));
        if (! in_array($status, ['used', 'pending', 'all'], true)) {
            $status = 'used';
        }

        $query = DB::table('booking_service_products as p')
            ->join('booking_services as s', 's.booking_service_id', '=', 'p.booking_service_id')
            ->join('bookings as b', 'b.booking_id', '=', 'p.booking_id')
            ->leftJoin('users as u', 'u.id', '=', 's.beautician_id')
            ->whereNull('b.deleted_at')
            ->where('p.quantity', '>', 0)
            ->whereNotIn('b.booking_type', [Booking::BOOKING_TYPE_REFUND, 'Refunded', 'Cancel'])
            ->select([
                'p.booking_service_product_id',
                'p.booking_id',
                'p.item_name',
                'p.uom_name',
                'p.quantity',
                'p.posted_at',
                'p.pack_size',
                'p.cost_price',
                's.service_name',
                's.appointment_date',
                'b.booking_code',
                'b.client_name',
                'u.name as beautician_name',
                DB::raw('CASE WHEN p.pack_size > 0 THEN ROUND(p.quantity * p.cost_price / p.pack_size, 2) ELSE 0 END as estimated_cost'),
            ]);

        if ($status === 'used') {
            $query->whereNotNull('p.posted_at');
        } elseif ($status === 'pending') {
            $query->whereNull('p.posted_at');
        }

        $startDate = ! empty($data->start_date) ? $data->start_date : null;
        $endDate = ! empty($data->end_date) ? $data->end_date : null;
        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }
        if ($startDate && $endDate) {
            $query->whereBetween('s.appointment_date', [$startDate, $endDate]);
        } elseif ($startDate) {
            $query->where('s.appointment_date', '>=', $startDate);
        } elseif ($endDate) {
            $query->where('s.appointment_date', '<=', $endDate);
        }

        if (! empty($data->beautician_id)) {
            $query->where('s.beautician_id', $data->beautician_id);
        }

        $search = trim((string) ($data->search ?? ''));
        if ($search !== '') {
            $like = '%' . $search . '%';
            $query->where(function ($inner) use ($like) {
                $inner->where('b.booking_code', 'like', $like)
                    ->orWhere('b.client_name', 'like', $like)
                    ->orWhere('s.service_name', 'like', $like)
                    ->orWhere('p.item_name', 'like', $like);
            });
        }

        return $query;
    }

    private function mapUsedProductRow($row): array
    {
        $date = ! empty($row->appointment_date)
            ? Carbon::parse($row->appointment_date)->format('d M Y')
            : '';
        $quantity = round((float) $row->quantity, 3);
        $quantityText = rtrim(rtrim(number_format($quantity, 3, '.', ''), '0'), '.');

        return [
            'id' => (int) $row->booking_service_product_id,
            'date' => $date,
            'booking_code' => (string) ($row->booking_code ?? ''),
            'client_name' => (string) ($row->client_name ?? ''),
            'service_name' => (string) ($row->service_name ?? ''),
            'beautician_name' => (string) ($row->beautician_name ?? ''),
            'item_name' => (string) ($row->item_name ?? ''),
            'quantity' => $quantityText !== '' ? $quantityText : '0',
            'uom_name' => (string) ($row->uom_name ?? ''),
            'estimated_cost' => number_format((float) ($row->estimated_cost ?? 0), 2, '.', ''),
            'status' => ! empty($row->posted_at) ? 'Used' : 'Pending',
            'view_url' => ! empty($row->booking_id)
                ? route('tab.salon.booking.details', ['booking_id' => $row->booking_id, 'tab_name' => 'edit'])
                : '',
        ];
    }
}
