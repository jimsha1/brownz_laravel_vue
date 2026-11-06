<?php

use App\Http\Controllers\Accounts\AccountList\AjaxTabs\Base\TabBaseController;
use App\Http\Controllers\Accounts\AccountDetails\AjaxTabs\TabTransactionsController;
use Illuminate\Support\Str;

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('accounts/account')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [TabBaseController::class, 'index'])
        ->name('tab.accounts.account.list');

    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{account_id}/{tab_name?}', [App\Http\Controllers\Accounts\AccountDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.accounts.account.details');

    Route::get('/accounts/account-details/{account_id}/transactions',[TabTransactionsController::class, 'index'])
    ->name('tab.accounts.account.details.transactions');

    Route::post('/account-details/{account_id}/transactions/filter',[TabTransactionsController::class, 'filter']
    )->name('tab.accounts.account.details.transactions.filter');

    Route::post('/tab/{account_id}/{tab_name}/save', function ($account_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return App::call('App\Http\Controllers\Accounts\AccountDetails\AjaxTabs\Tab'
                .Str::studly($tab_name).'Controller@save', [
                    'account_id' => $account_id,
                    'tab_name' => $tab_name,
                ]);
    })
        ->name('tab.accounts.account.save');
});
