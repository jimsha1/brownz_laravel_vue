<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('salon/beautician')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Salon\BeauticianList\AjaxTabs\Base\TabBaseController::class,'index'])
        ->name('tab.salon.beautician.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{beautician_id}/{tab_name?}', [App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.salon.beautician.details');

    Route::post('/tab/{beautician_id}/{tab_name}/save', function ($beautician_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Salon\BeauticianDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'beautician_id' => $beautician_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.salon.beautician.save');
});

Route::post('admin/update-beautician-profile-photo/{beautician_id}', [App\Http\Controllers\Salon\BeauticianDetails\UpdateProfilePhotoController::class, '__invoke'])
    ->name('admin.beautician.photo.save');
