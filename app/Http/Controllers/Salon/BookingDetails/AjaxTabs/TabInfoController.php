<?php

namespace App\Http\Controllers\Salon\BookingDetails\AjaxTabs;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\BookingRepository;
use Auth;

class TabInfoController extends Controller
{
    public function index(Request $request, BookingRepository $booking_repo, $booking_id, $tab_name)
    {
    
        $booking_info = $booking_repo->model()
            ->with('createdUser')
            ->with('updatedUser')
            ->with(['editLogs.editedUser'])
            ->findOrFail($booking_id);

        $booking_info->loadEssentialsForVueJs();

        $details_info_vue = [
            'booking_id' => $booking_id,
            'tab_name' => $tab_name,
            'booking_info' => $booking_info,
        ];

        return view('salon.booking-details.ajax-tabs.tab-info', compact('details_info_vue'));
    }
}
