<?php

namespace App\Hunters;

use App\Hunters\Base\BaseHunter;

class BookingHunter extends BaseHunter
{
    public function __construct(\App\Models\Booking $model)
    {
        parent::__construct($model);
    }

    public function applyCriteria()
    {


        if (session_outlet_id() != 1) {
            return $this->model()
                ->when($this->search_term, function ($query) {
                    $query->where(function ($where) {
                        $where->where('booking_code', 'like', '%' . $this->search_term . '%');
                        if ($this->search_term == 'Draft' || $this->search_term == 'draft') {
                            $where->orWhereNull('booking_code');
                        }
                        $where->orwhere('booking_source', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('booking_type', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('refund_invoice_no', 'like', '%' . $this->search_term . '%');
                        $where->orWhere('booking_barcode', $this->search_term);
                        $where->orwhere('client_mobile', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('booking_code', 'like', '%' . $this->search_term . '%');
                    });
                })
                ->when($this->start_date && $this->end_date, function ($query) {
                    // $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
                    if (!empty($this->start_date) && !empty($this->end_date)) {
                        $query->whereBetween('created_at', ["$this->start_date 00:00:00", "$this->end_date 23:59:59"]);
                    } elseif (!empty($this->start_date)) {
                        $query->where('created_at', '>=', "$this->start_date 00:00:00");
                    } elseif (!empty($this->end_date)) {
                        $query->where('created_at', '<=', "$this->end_date 23:59:59");
                    }
                })

                ->orderByRaw('booking_code IS NULL DESC') // Add this to prioritize NULLs
                ->orderBy('created_at', 'desc');

        } else {
            return $this->model()->with('bookingServices')
                ->when($this->search_term, function ($query) {
                    $query->where(function ($where) {
                        $where->where('booking_code', 'like', '%' . $this->search_term . '%');
                        if ($this->search_term == 'Draft' || $this->search_term == 'draft') {
                            $where->orWhereNull('booking_code');
                        }
                        $where->orwhere('booking_source', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('booking_type', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('refund_invoice_no', 'like', '%' . $this->search_term . '%');
                        $where->orWhere('booking_barcode', $this->search_term);
                        $where->orwhere('client_mobile', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('client_name', 'like', '%' . $this->search_term . '%');
                        $where->orwhere('booking_code', 'like', '%' . $this->search_term . '%');
                    });
                })


                // Include: services by appointment_date OR bookings without services (empty / product-only) by created_at
                ->when(!empty($this->start_date) && !empty($this->end_date), function ($query) {
                    $start = "$this->start_date 00:00:00";
                    $end = "$this->end_date 23:59:59";
                    $query->where(function ($q) use ($start, $end) {
                        $q->whereHas('bookingOnlyServices', function ($sq) use ($start, $end) {
                            $sq->whereBetween('appointment_date', [$start, $end]);
                        })->orWhere(function ($oq) use ($start, $end) {
                            $oq->whereDoesntHave('bookingOnlyServices')
                                ->whereBetween('created_at', [$start, $end]);
                        });
                    });
                })
                // Filtering by start date only
                ->when(!empty($this->start_date) && empty($this->end_date), function ($query) {
                    $start = "$this->start_date 00:00:00";
                    $query->where(function ($q) use ($start) {
                        $q->whereHas('bookingOnlyServices', function ($sq) use ($start) {
                            $sq->where('appointment_date', '>=', $start);
                        })->orWhere(function ($oq) use ($start) {
                            $oq->whereDoesntHave('bookingOnlyServices')
                                ->where('created_at', '>=', $start);
                        });
                    });
                })
                // Filtering by end date only
                ->when(empty($this->start_date) && !empty($this->end_date), function ($query) {
                    $end = "$this->end_date 23:59:59";
                    $query->where(function ($q) use ($end) {
                        $q->whereHas('bookingOnlyServices', function ($sq) use ($end) {
                            $sq->where('appointment_date', '<=', $end);
                        })->orWhere(function ($oq) use ($end) {
                            $oq->whereDoesntHave('bookingOnlyServices')
                                ->where('created_at', '<=', $end);
                        });
                    });
                })

                ->orderByRaw('booking_code IS NULL DESC')
                ->whereNull('canceled_at')
                ->orderBy('created_at', 'desc');
        }
    }
    public function applyCriteria_1()
    {
        if (session_outlet_id() != 1) {
            return $this->model()
                ->when($this->search_term, function ($query) {
                    $query->where(function ($where) {
                        $where->where('booking_code', 'like', '%' . $this->search_term . '%');
                        $where->orWhere('booking_barcode', $this->search_term);
                    });
                })
                ->when($this->start_date && $this->end_date, function ($query) {
                    $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
                })

                //->where('outlet_id', session_outlet_id())
                ->orderBy('booking_id', 'desc');

            /* DO NOT USE get(), first() etc at end of this query builder */
        } else {
            return $this->model()
                ->when($this->search_term, function ($query) {
                    $query->where(function ($where) {
                        $where->where('booking_code', 'like', '%' . $this->search_term . '%');
                        $where->orWhere('booking_barcode', $this->search_term);
                    });
                })
                ->when($this->start_date && $this->end_date, function ($query) {
                    $query->whereBetween('created_at', [$this->start_date, $this->end_date]);
                })
                ->orderBy('booking_id', 'desc');
        }
    }


    // @TODO - don't allow user to access bookings of other outlets
    public function findBooking($booking_id)
    {
        $booking_info = $this->applyPagination(false)
            ->search_1()
            ->where('booking_id', $booking_id)
            ->with('bookingOnlyServices')
            ->with('bookingItems')
            ->with('payments')
            ->firstOrFail();

        return $booking_info;
    }
}
