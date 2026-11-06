<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('master/shift')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Master\ShiftList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.shift.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{shift_id}/{tab_name?}', [App\Http\Controllers\Master\ShiftDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.shift.details');

    Route::post('/tab/{shift_id}/{tab_name}/save', function ($shift_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Master\ShiftDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'shift_id' => $shift_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.master.shift.save');
});
