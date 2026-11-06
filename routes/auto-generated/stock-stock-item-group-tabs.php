<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('stock/stock-item-group')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Stock\StockItemGroupList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.item.group.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{item_group_id}/{tab_name?}', [App\Http\Controllers\Stock\StockItemGroupDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.stock.stock.item.group.details');

    Route::post('/tab/{item_group_id}/{tab_name}/save', function ($item_group_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Stock\StockItemGroupDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'item_group_id' => $item_group_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.stock.stock.item.group.save');
});
