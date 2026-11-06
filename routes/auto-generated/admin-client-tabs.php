<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('admin/client')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Admin\ClientList\AjaxTabs\Base\TabBaseController::class, 'index'])
            ->name('tab.admin.client.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{client_id}/{tab_name?}', [App\Http\Controllers\Admin\ClientDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
            ->name('tab.admin.client.details');

    Route::post('/tab/{client_id}/{tab_name}/save', function ($client_id, $tab_name) {

                // Be careful! dynamic controller used for direct invoking of controller
                return \App::call('App\Http\Controllers\Admin\ClientDetails\AjaxTabs\Tab'
                                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
                            'client_id' => $client_id,
                            'tab_name' => $tab_name,
                ]);
            })
            ->name('tab.admin.client.save');
});
