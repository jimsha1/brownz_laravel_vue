<?php

// Settings

use App\Http\Controllers\Admin\LeaveManagement\LeaveManagementController;
use App\Http\Controllers\Salon\BookingDetails\InvoiceController;
Route::get('/admin/holiday', 'Master\Holidays\HolidayController@index')->name('admin.holiday');
Route::get('/admin/holiday/create', 'Master\Holidays\HolidayController@create')->name('admin.holiday.create');

Route::get('/admin/holiday/create-sundays', 'Master\Holidays\CreateSundayController@index')->name('admin.holiday.create.sundays');

Route::post('/admin/holiday/save', 'Master\Holidays\HolidayController@save')->name('admin.holiday.save');

Route::match(['get', 'post'], '/admin/holiday/search', 'Master\Holidays\HolidayController@search')->name('holiday.search');

Route::get('/admin/holiday/delete/{date}', 'Master\Holidays\RemoveHolidayController@remove')->name('holiday.delete');
Route::post('/admin/holiday/store', 'Master\Holidays\HolidayController@store')->name('admin.holiday_store');




Route::get('/admin/leave-management/list', [LeaveManagementController::class, 'index'])->name('admin.leave.management.list');
Route::post('/admin/leave-management/save', [LeaveManagementController::class, 'save'])->name('admin.leave.management.save');
Route::post('/admin/leave-management/delete/{id}', [LeaveManagementController::class, 'delete'])->name('admin.leave.management.delete');
// Route::post('/admin/leave-management/update-status/{id}', [LeaveManagementController::class, 'updateStatus'])
//     ->name('admin.leave.management.updateStatus');



Route::get('/salon/booking-details/invoice/{id}/{gift?}', [InvoiceController::class, 'index'])->name('salon.booking.invoice');
Route::post('/salon/booking-details/notify/whatsapp', [InvoiceController::class, 'sendWhatsAppNotification'])->name('salon.booking.notify.whatsapp');


Route::get('/admin/test', function (){
    var_dump(\Illuminate\Support\Facades\Hash::make('admin'));
});
