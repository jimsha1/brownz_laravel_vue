<?php

Route::get('/change-outlet/{outlet_id}', [App\Http\Controllers\Outlet\SetSessionOutletController::class])->name('outlet.change');

Route::post('/switch-locale', [App\Http\Controllers\Locale\SwitchLocaleController::class])->name('switch.locale');
