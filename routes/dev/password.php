<?php

Route::get('/admin/change-password', [App\Http\Controllers\Master\AdminPasswordController::class, 'changePassword'])->name('admin.change-password');

Route::post('/admin/save-password', [App\Http\Controllers\Master\AdminPasswordController::class, 'savePassword'])->name('admin.save-password');
