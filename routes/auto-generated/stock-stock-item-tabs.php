<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/stock-item')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockItemList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.item.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{item_id}/{tab_name?}', [App\Http\Controllers\Stock\StockItemDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.item.details');

    Route::post('/tab/{item_id}/{tab_name}/save', function ($item_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Stock\StockItemDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'item_id' => $item_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.stock.stock.item.save');

    Route::post('/get-item-subcategories', [App\Http\Controllers\Stock\StockItemDetails\AjaxTabs\TabEditController::class, 'getItemSubcategories'])->name('get.itemSubcategories');

    Route::post('/opening-stock-auth-check', [App\Http\Controllers\Stock\StockItemDetails\AjaxTabs\TabOpeningStockController::class, 'checkPassword'])
        ->name('opening.stock.auth-check');

});
