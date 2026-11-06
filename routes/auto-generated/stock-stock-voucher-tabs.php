<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/stock-voucher')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockVoucherList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.voucher.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{id}/{tab_name?}', [App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.voucher.details');

    Route::post('/tab/{id}/{tab_name}/save', function ($id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'id' => $id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.stock.stock.voucher.save');

    Route::post('voucher-auth-check', [App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs\TabEditController::class, 'checkPassword'])->name('voucher.auth-check');

    Route::get('account-balance/{account_id}', [App\Http\Controllers\Stock\StockVoucherDetails\AjaxTabs\TabEditController::class, 'accountBalance'])
        ->name('tab.stock.stock.voucher.account-balance');

});
