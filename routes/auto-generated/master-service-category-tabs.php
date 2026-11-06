<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('master/category')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Master\ServiceCategoryList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.service.category.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{category_id}/{tab_name?}', [App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.master.service.category.details');

    Route::post('/tab/{category_id}/{tab_name}/save', function ($category_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Master\ServiceCategoryDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'category_id' => $category_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.master.service.category.save');
});
