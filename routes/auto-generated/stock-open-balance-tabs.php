<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/open-balance')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockOpenBalanceList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.open.balance.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{open_balance_id}/{tab_name?}', [App\Http\Controllers\Stock\StockOpenBalanceDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.open.balance.details');

    Route::post('/tab/{open_balance_id}/{tab_name}/save', function ($open_balance_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Stock\StockOpenBalanceDetails\AjaxTabs\Tab'
            . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'open_balance_id' => $open_balance_id,
            'tab_name' => $tab_name,
        ]);
    })->name('tab.stock.open.balance.save');


});
