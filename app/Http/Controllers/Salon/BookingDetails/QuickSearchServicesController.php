<?php

namespace App\Http\Controllers\Salon\BookingDetails;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\ServiceRepository;
use App\Repositories\BookingRepository;

class QuickSearchServicesController extends Controller
{
    // public function __invoke(Request $request, BookingRepository $booking_repo, ServiceRepository $service_repo)
    // {
    //     $booking = $booking_repo->hunter()->findBooking($request->booking_id);

    //     // Reset pagination page number, when search form submitted
    //     if ($request->tab_search) {
    //         $request->merge(['page' => 1]);
    //     }

    //     $db_records = $service_repo->hunter()
    //             ->applyPagination(false)
    //             ->setSearchTerm($request->search)
    //             ->search()

    //             // Exclude already added services
    //             ->whereNotIn('service_id', function ($query) use($request) {
    //                 $query->select('service_id')
    //                     ->from('booking_services')
    //                     ->where('booking_id', $request->booking_id);
    //             })

    //             // Show only services, to which beautician assigned
    //             ->whereIn('service_id', function ($query) use($request) {
    //                 // @TODO - optimize query (do not traverse entire table)
    //                 $query->select('service_id')
    //                     ->from('beautician_services');
    //             })
    //             ->where('outlet_id', session_outlet_id())
    //             ->where('is_active', 1)
    //             ->paginate(5)
    //             ->appends(['pgajax' => 'y']);

    //     $db_results = [
    //         // Send some data for VueJs
    //         'vue_data' => [
    //             'vue_details_tab_edit' => [
    //                 'service_quick_search_results' => [
    //                     'checked_services' => [],
    //                     'paginator' => paginator_vue_data($db_records),
    //                 ]
    //             ],
    //         ],
    //         'response_values' => [
    //             '#tab_search' => '0',
    //         ]
    //     ];

    //     return response()->json($db_results);
    // }
}
