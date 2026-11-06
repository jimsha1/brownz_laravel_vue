<?php

// ------------------------------------------------------------------------
// Beautician appointments
// ------------------------------------------------------------------------

// List appointments

use App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs\Base\TabBaseController;
use App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs\TabCompletedController;
use App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs\TabExpiredController;
use App\Http\Controllers\Appointment\BookingServiceList\AjaxTabs\TabUpcomingController;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\Base\TabBaseController as BaseTabBaseController;
use App\Http\Controllers\MyAccount\BookingServiceList\AjaxTabs\TabUpcomingController as AjaxTabsTabUpcomingController;
use Illuminate\Support\Facades\Route;

Route::get('my/appointments/{tab_name?}', [BaseTabBaseController::class, 'index'])
    ->name('tab.my.account.booking.service.list');

// Start Service
Route::post('my/appointments/startService',[AjaxTabsTabUpcomingController::class, 'startService'])
->name('my.account.booking.service.start');

// Stop Service
Route::post('my/appointments/stopService',[AjaxTabsTabUpcomingController::class, 'stopService'])
->name('my.account.booking.service.stop');

// ------------------------------------------------------------------------
// Admin appointments
// ------------------------------------------------------------------------

// List appointments
Route::get('report/appointments/{tab_name?}', [TabBaseController::class, 'index'])
->name('report.appointment.list');

// Reject Service
Route::post('report/appointments/rejectService',[TabCompletedController::class, 'rejectService'])
->name('report.appointment.reject');

// Approve Service
Route::post('report/appointments/approveService',[TabCompletedController::class, 'approveService'])
->name('report.appointment.approve');

// Split Service Amount
Route::post('report/appointments/splitServiceAmount', [TabCompletedController::class, 'splitServiceAmount'])
->name('report.appointment.splitServiceAmount');

// Cancel Service
Route::post('report/appointments/refund-service',[TabCompletedController::class, 'refundService'])
->name('report.appointment.refund');

// Mark client check-in
Route::post('admin/appointments/mark-client-check-in',[TabUpcomingController::class, 'clientCheckIn'])
->name('admin.appointment.client.check.in');

Route::post('admin/appointments/mark-client-undo-check-in',[TabUpcomingController::class, 'undoClientCheckIn'])
->name('admin.appointment.client.undo.check.in');


// Get Booking Details
Route::post('report/appointments/bookingDetails',[TabExpiredController::class, 'getBookingDetails'])
->name('report.appointment.booking.details');
