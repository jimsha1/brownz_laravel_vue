<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('acl/role')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Acl\AclRoleList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.acl.role.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{id}/{tab_name?}', [App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.acl.role.details');

    Route::post('/tab/{id}/{tab_name}/save', function ($id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return app()->call('App\Http\Controllers\Acl\AclRoleDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'id' => $id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.acl.role.save');
});
