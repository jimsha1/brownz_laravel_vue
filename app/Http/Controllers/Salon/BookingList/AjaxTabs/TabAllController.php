<?php

namespace App\Http\Controllers\Salon\BookingList\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BookingRepository;
use Illuminate\Support\Facades\Auth;

class TabAllController extends Controller
{
    public function index(Request $request, BookingRepository $booking_repo, $tab_name)
    {
        // dd($request);
        // Reset pagination page number, when search form submitted
        if ($request->tab_search) {
            $request->merge(['page' => 1]);
        }

        // A search term looks across every date. Staff often know the client
        // name but not the booking month, so the date range must not hide matches.
        $search = trim((string) $request->input('search', ''));
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        if ($search !== '') {
            $startDate = null;
            $endDate = null;
        } elseif (empty($startDate) && empty($endDate)) {
            $startDate = now()->startOfMonth()->toDateString();
            $endDate = now()->endOfMonth()->toDateString();
        }

        $db_records = $booking_repo->hunter()
            ->setSearchTerm($search !== '' ? $search : null)
            ->setStartDate($startDate)
            ->setEndDate($endDate)
            ->query(function ($query) use ($request) {
                if (!Auth::user()->hasRole(\App\Models\AclRole::ROLE_SUPER_ADMIN)) {
                    $query->where(function ($where) {
                        $where->orWhereNotNull('booking_code')
                            ->orWhere('created_by', Auth::id());
                    });
                }

                // Apply check-in filter

                if ($request->filled('check_availability')) {
                    $query->where('check_availability', $request->check_availability);
                }
                // Apply slot_start time filters on booking_services table
                if ($request->filled('slot_start') || $request->filled('slot_end')) {
                    $query->whereHas('bookingServices', function ($q) use ($request) {
                        if ($request->filled('slot_start')) {
                            $q->where('slot_start', '=', $request->slot_start);
                        }

                        // if ($request->filled('slot_start')) {
                        //     $q->where('slot_start', '>=', $request->slot_start);
                        // }

                        // if ($request->filled('slot_end')) {
                        //     $q->where('slot_end', '<=', $request->slot_end);
                        // }
                    });
                }
            })
            ->search();

        if (method_exists($db_records, 'appends')) {
            $db_records->appends(array_filter([
                'pgajax' => 'y',
                'start_date' => $startDate,
                'end_date' => $endDate,
                'search' => $search,
                'slot_start' => $request->slot_start,
                'check_availability' => $request->check_availability,
            ], function ($value) {
                return $value !== null && $value !== '';
            }));
        }

        // Add additional properties for the records
        foreach ($db_records as $row) {
            $row->edit_url = route('tab.salon.booking.details', ['booking_id' => $row->booking_id, 'tab_name' => 'edit']);
        }

        $db_results = [
            // Send some data for VueJs
            'vue_data' => [
                'vue_list_tab_all' => [
                    'paginator' => paginator_vue_data($db_records),
                    'format_date_id' => "format_date_",
                ],
            ],
            'response_values' => [
                '#tab_search' => '0',
            ]
        ];


        if ($request->tab_search || $request->pgajax === 'y') {
            return response()->json($db_results);
        }

        return view('salon.booking-list.ajax-tabs.tab-all', compact('db_results', 'tab_name'));
    }
}
