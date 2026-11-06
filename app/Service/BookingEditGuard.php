<?php

namespace App\Service;

use App\Models\AclRole;
use App\Models\Booking;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingEditGuard
{
    /**
     * Require password (and permission) before mutating a finalized booking.
     * Returns a JSON error response, or null when edit is allowed.
     */
    public function authorize(Request $request, ?Booking $booking): ?JsonResponse
    {
        if (! $booking || empty($booking->booking_code)) {
            return null;
        }

        $user = Auth::user();
        if (! $user) {
            return response()->json([
                'js_notifications' => ['error' => 'Unauthorized.'],
            ], 401);
        }

        if (! $user->hasRole(AclRole::ROLE_SUPER_ADMIN)
            && ! $user->can('void_completed_payment')
        ) {
            return response()->json([
                'js_notifications' => ['error' => 'You do not have permission to edit completed bookings.'],
            ], 403);
        }

        $passwordError = app(EmployeeService::class)
            ->validateCurrentUserPassword($request->input('auth_password'));

        if ($passwordError !== null) {
            return response()->json([
                'js_notifications' => ['error' => $passwordError],
            ], 403);
        }

        return null;
    }
}
