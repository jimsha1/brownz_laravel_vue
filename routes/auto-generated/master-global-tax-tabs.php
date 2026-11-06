<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

use App\Http\Controllers\Master\GlobalTaxDetails\AjaxTabs\Base\TabBaseController;
use App\Http\Controllers\Master\GlobalTaxList\AjaxTabs\Base\TabBaseController as BaseTabBaseController;

Route::prefix('master/global-tax')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    // Route::get('/{tab_name?}', 'Master\GlobalTaxList\AjaxTabs\Base\TabBaseController@index')
    //     ->name('tab.master.global.tax.list');

    Route::get('/{tab_name?}', [BaseTabBaseController::class, 'index'])
    ->name('tab.master.global.tax.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    // Route::get('/tab/{tax_id}/{tab_name?}', ['Master\GlobalTaxDetails\AjaxTabs\Base\TabBaseController@index'])
    //     ->name('tab.master.global.tax.details');

    Route::get('/tab/{tax_id}/{tab_name?}', [TabBaseController::class, 'index'])
    ->name('tab.master.global.tax.details');

    Route::post('/tab/{tax_id}/{tab_name}/save', function ($tax_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Master\GlobalTaxDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'tax_id' => $tax_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.master.global.tax.save');
});
