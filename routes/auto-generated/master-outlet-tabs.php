<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

use App\Http\Controllers\Master\OutletDetails\AjaxTabs\Base\TabBaseController;
use App\Http\Controllers\Master\OutletList\AjaxTabs\Base\TabBaseController as BaseTabBaseController;

Route::prefix('master/outlet')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

        Route::get('/{tab_name?}', [BaseTabBaseController::class, 'index'])
        ->name('tab.master.outlet.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{outlet_id}/{tab_name?}', [TabBaseController::class, 'index'])
            ->name('tab.master.outlet.details');

    Route::post('/tab/{outlet_id}/{tab_name}/save', function ($outlet_id, $tab_name) {

                // Be careful! dynamic controller used for direct invoking of controller
                return \App::call('App\Http\Controllers\Master\OutletDetails\AjaxTabs\Tab'
                                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
                            'outlet_id' => $outlet_id,
                            'tab_name' => $tab_name,
                ]);
            })
            ->name('tab.master.outlet.save');
});
