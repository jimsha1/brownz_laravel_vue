<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('admin/vendor')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Admin\VendorList\AjaxTabs\Base\TabBaseController::class, 'index'])
            ->name('tab.admin.vendor.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{vendor_id}/{tab_name?}', [App\Http\Controllers\Admin\VendorDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
            ->name('tab.admin.vendor.details');

    Route::post('/tab/{vendor_id}/{tab_name}/save', function ($vendor_id, $tab_name) {

                // Be careful! dynamic controller used for direct invoking of controller
                return \App::call('App\Http\Controllers\Admin\VendorDetails\AjaxTabs\Tab'
                                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
                            'vendor_id' => $vendor_id,
                            'tab_name' => $tab_name,
                ]);
            })
            ->name('tab.admin.vendor.save');
});
