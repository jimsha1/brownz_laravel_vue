<?php

namespace App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Traits;

use Illuminate\Http\Request;

trait FiltersAppointmentDateRange
{
    /**
     * Default: first day of current month → today.
     *
     * @return array{0: string, 1: string}
     */
    protected function resolveAppointmentDateRange(Request $request): array
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        // Full current month so Upcoming still includes remaining days after today
        if (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        return [$startDate, $endDate];
    }

    protected function applyAppointmentDateRange($query, ?string $startDate, ?string $endDate)
    {
        return $query
            ->when($startDate, function ($q) use ($startDate) {
                $q->whereDate('booking_services.appointment_date', '>=', $startDate);
            })
            ->when($endDate, function ($q) use ($endDate) {
                $q->whereDate('booking_services.appointment_date', '<=', $endDate);
            });
    }

    protected function appointmentFilterAppends(Request $request, ?string $startDate, ?string $endDate): array
    {
        return array_filter([
            'pgajax' => 'y',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'search' => $request->search,
        ], function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
