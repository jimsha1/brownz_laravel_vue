<?php

use App\Http\Controllers\CalendarController;
use App\Http\Controllers\MakeupController;
use Illuminate\Support\Facades\Route;

Route::post('/create_makeup_bridal_booking', [MakeupController::class, 'store_bridal'])->name('makeup.booking.bridal.store');
Route::post('/create_makeup_groom_booking', [MakeupController::class, 'store_groom'])->name('makeup.booking.groom.store');
Route::post('/create_makeup_party_booking', [MakeupController::class, 'store_party'])->name('makeup.booking.party.store');
Route::post('/calendar', [CalendarController::class, 'store'])->name('calendar.store');
Route::patch('/calendar/update/{id}', [CalendarController::class, 'update'])->name('calendar.update');
Route::delete('/calendar/destroy/{id}', [CalendarController::class, 'destroy'])->name('calendar.destroy');

Route::get('/makeup_booking', [MakeupController::class, 'index_bridal'])->name('makeup.booking.bridal');
Route::get('/makeup_groom_booking', [MakeupController::class, 'index_groom'])->name('makeup.booking.groom');
Route::get('/makeup_party_booking', [MakeupController::class, 'index_party'])->name('makeup.booking.party');

Route::post('/update_makeup_bridal_booking', [MakeupController::class, 'update_bridal'])->name('makeup.booking.bridal.update');
Route::post('/update_makeup_groom_booking', [MakeupController::class, 'update_groom'])->name('makeup.booking.groom.update');
Route::post('/update_makeup_party_booking', [MakeupController::class, 'update_party'])->name('makeup.booking.party.update');

Route::get('/makeup_booking_service', [MakeupController::class, 'makeup_booking_service'])->name('makeup.booking.service');
Route::post('/create_makeup_service', [MakeupController::class, 'store_service'])->name('makeup.booking.service.store');
Route::post('/update_makeup_service_booking', [MakeupController::class, 'update_service'])->name('makeup.booking.service.update');

Route::get('/makeup-services/search', [MakeupController::class, 'search'])->name('makeup_services.search');
