<?php

// -----------------------------------------------------------------------------
// THIS FILE IS AUTO GENERATED!
//
// DO NOT ADD ANY OTHER ROUTES HERE. CREATE ANOTHER ROUTE FILE YOURSELF.
// -----------------------------------------------------------------------------

Route::prefix('expenses/expense')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Expenses\ExpenseList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.expenses.expense.list');


    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{expense_id}/{tab_name?}', [App\Http\Controllers\Expenses\ExpenseDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.expenses.expense.details');


    Route::post('/tab/{expense_id}/{tab_name}/save', function ($expense_id, $tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Expenses\ExpenseDetails\AjaxTabs\Tab'
                . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'expense_id' => $expense_id,
            'tab_name' => $tab_name,
        ]);
    })
    ->name('tab.expenses.expense.save');
});
