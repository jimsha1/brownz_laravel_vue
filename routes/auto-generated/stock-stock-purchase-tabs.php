<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/stock-purchase')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockPurchaseList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.purchase.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{purchase_id}/{tab_name?}', [App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.purchase.details');

    Route::post('/tab/{purchase_id}/{tab_name}/save', function ($purchase_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs\Tab'
            . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'purchase_id' => $purchase_id,
            'tab_name' => $tab_name,
        ]);
    })->name('tab.stock.stock.purchase.save');

    Route::post('purchase-auth-check', [App\Http\Controllers\Stock\StockPurchaseDetails\AjaxTabs\TabEditController::class, 'checkPassword'])
        ->name('purchase.auth-check');

});
