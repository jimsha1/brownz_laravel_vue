<?php

namespace App\Service;

use App\Models\Booking;
use App\Models\Client;
use App\Models\Employee;
use App\Models\EmployeeAttendence;
use Carbon\Carbon;
use Exception;

class AdminAppAndroidService
{
    public function salesReportListAdminApi()
    {
        try {
            $result = Booking::with(['beauticians.user']);
            $result = $result->orderBy('created_at', 'asc')->where('payment_status', 'completed')->get();

            return $result;
        } catch (Exception $e) {
            return apiResponse(100, $e->getMessage(), null);
        }
    }

    public function clientReportListApi()
    {
        try {
            $result = Client::with('user');
            $result = $result->get();

            return $result;
        } catch (Exception $e) {
            return apiResponse(100, $e->getMessage(), null);
        }
    }

    public function getBarGraphData111($type)
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

            $months = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'June', 'July', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            $salesByMonth = array_replace(array_fill_keys($months, 0), $monthlySales->toArray());
            if ($type == 'week') {
                return $salesByDay;
            } else {
                return $salesByMonth;
            }

        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function getBarGraphData($type)
    {
        try {
            $startOfWeek = Carbon::now()->startOfWeek();
            $today = Carbon::now();

            // Fetch Weekly Sales Data
            $salesData = Booking::where('payment_status', 'completed')
                ->whereBetween('created_at', [$startOfWeek, $today])
                ->selectRaw('DAYNAME(created_at) as day, SUM(payment_total) as total')
                ->groupBy('day')
                ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday')")
                ->pluck('total', 'day');

            // Mapping full day names to short forms
            $dayMap = [
                'Monday' => 'Mon',
                'Tuesday' => 'Tue',
                'Wednesday' => 'Wed',
                'Thursday' => 'Thu',
                'Friday' => 'Fri',
                'Saturday' => 'Sat',
                'Sunday' => 'Sun',
            ];

            // Shortened days for frontend
            $daysOfWeek = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

            // Replace full day names with short names
            $salesByDay = [];
            foreach ($daysOfWeek as $shortDay) {
                $fullDay = array_search($shortDay, $dayMap);
                $salesByDay[$shortDay] = $salesData[$fullDay] ?? 0;
            }

            // Fetch Monthly Sales Data
            $monthlySales = Booking::where('payment_status', 'completed')
                ->selectRaw('MONTH(created_at) as month, SUM(payment_total) as total')
                ->groupBy('month')
                ->orderBy('month')
                ->pluck('total', 'month');

            // Define months array with indexes (1-12)
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            // Format monthly data properly
            $salesByMonth = [];
            foreach ($months as $num => $month) {
                $salesByMonth[$month] = $monthlySales[$num] ?? 0;
            }

            // Return based on the type requested
            if ($type == 'week') {
                return $salesByDay;
            } else {
                return $salesByMonth;
            }

        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }

    public function getSalesAnalytics($platform = 'web',$year = null)
    {
        try {
            $main_customer_data = $this->clientReportListApi();
            $main_data = $this->salesReportListAdminApi();
            $yearComparisonGraph = $this->getYearComparisonGraph($year);
            $main_data_for_graph_weekly = $this->getBarGraphData('week');
            $main_data_for_graph_monthly = $this->getBarGraphData('month');
            $clientTotalCounts = count($main_customer_data);
            $currentMonthSales = Booking::where('payment_status', 'completed')
                ->whereMonth('created_at', Carbon::now()->month)
                ->whereYear('created_at', Carbon::now()->year)
                ->sum('payment_total');

            $analytics = [
                'totalRevenue' => $currentMonthSales,
                'todaySales' => 0,
                'bookingsByDate' => [],
                'paymentStatusCount' => [],
                'financialYear' => $this->getFinancialYear(),
                'totalUser' => $clientTotalCounts,
                'weeklyGraph' => $main_data_for_graph_weekly,
                'monthlyGraph' => $main_data_for_graph_monthly,
                'yearComparisonGraph' => $yearComparisonGraph,
            ];

            $today = date('Y-m-d');

            foreach ($main_data as $item) {
                $total = (float) ($item['booking_total'] ?? 0);
                // $analytics['totalRevenue'] += round($total);

                if (isset($item['created_at']) && date('Y-m-d', strtotime($item['created_at'])) === $today) {
                    $analytics['todaySales'] += $total;
                }

                $date = $item['beauticians']['appointment_date'] ?? 'unknown_date';
                $analytics['bookingsByDate'][$date] =
                    ($analytics['bookingsByDate'][$date] ?? 0) + $total;

                $status = $item['payment_status'] ?? 'unknown_status';
                $analytics['paymentStatusCount'][$status] =
                    ($analytics['paymentStatusCount'][$status] ?? 0) + 1;
            }

            if ($platform == 'web') {
                return $analytics;
            } else {
                return apiResponse(0, 'Sales report analytics fetched successfully', $analytics);
            }
        } catch (Exception $e) {
            return apiResponse(1, 'Internal Error: '.$e->getMessage(), null);
        }
    }

    private function getFinancialYear()
    {
        $currentMonth = date('m');
        $currentYear = date('Y');

        if ($currentMonth < 4) {
            return ($currentYear - 1).'-'.$currentYear;
        }

        return $currentYear.'-'.($currentYear + 1);
    }

    public function getEmployeeAttendanceTime($request)
    {
        try {
            $emp_id = $request['emp_id'];
            $currentDate = now()->format('Y-m-d');

            $attendance = EmployeeAttendence::where('employee_id', $emp_id)
                ->where('date', $currentDate)
                ->first();
            $res = [
                'check_in' => $attendance->check_in,
                'check_out' => $attendance->check_out,
            ];

            if ($attendance) {
                return successResponse($res, 'Attendance fetched successfully', 0);
            }

            return apiResponse(1, 'No attendance found for the given employee', null);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function employeeListAndroid($request)
    {
        try {
            $result = Employee::with(['user', 'currentAttendence'])->get();

            return apiResponse(0, 'Employee list fetched successfully', $result);
        } catch (Exception $e) {
            return errorResponse($e->getMessage(), 500);
        }
    }

    public function getYearComparisonGraph($year = null)
    {
        try {
            $year = $year ?? Carbon::now()->year;
            $previousYear = $year - 1;

            // Fetch current year data
            $currentYearSales = Booking::where('payment_status', 'completed')
                ->whereYear('created_at', $year)
                ->selectRaw('MONTH(created_at) as month, SUM(payment_total) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            // Fetch previous year data
            $previousYearSales = Booking::where('payment_status', 'completed')
                ->whereYear('created_at', $previousYear)
                ->selectRaw('MONTH(created_at) as month, SUM(payment_total) as total')
                ->groupBy('month')
                ->pluck('total', 'month');

            // Month labels
            $months = [
                1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr',
                5 => 'May', 6 => 'Jun', 7 => 'Jul', 8 => 'Aug',
                9 => 'Sep', 10 => 'Oct', 11 => 'Nov', 12 => 'Dec',
            ];

            $currentData = [];
            $previousData = [];

            foreach ($months as $num => $month) {
                $currentData[] = $currentYearSales[$num] ?? 0;
                $previousData[] = $previousYearSales[$num] ?? 0;
            }

            return [
                'labels' => array_values($months),
                'currentYear' => [
                    'year' => $year,
                    'data' => $currentData,
                ],
                'previousYear' => [
                    'year' => $previousYear,
                    'data' => $previousData,
                ],
            ];

        } catch (Exception $e) {
            return response()->json([
                'errorcode' => 1,
                'errormessage' => $e->getMessage(),
            ], 500);
        }
    }
}
