<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

use App\Http\Controllers\Master\ServiceDetails\AjaxTabs\TabEditController;

Route::prefix('master/service')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Master\ServiceList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.service.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{service_id}/{tab_name?}', [App\Http\Controllers\Master\ServiceDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.service.details');

    Route::post('/tab/{service_id}/{tab_name}/save', function ($service_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Master\ServiceDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'service_id' => $service_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.master.service.save');


    Route::post('/get-subcategories', [TabEditController::class, 'getSubcategories'])->name('get.subcategories');



});
