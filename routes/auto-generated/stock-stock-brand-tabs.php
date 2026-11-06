<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/stock-brand')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockBrandList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.brand.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{brand_id}/{tab_name?}', [App\Http\Controllers\Stock\StockBrandDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.brand.details');

    Route::post('/tab/{brand_id}/{tab_name}/save', function ($brand_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Stock\StockBrandDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'brand_id' => $brand_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.stock.stock.brand.save');
});
