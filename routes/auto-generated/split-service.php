<?php


use App\Http\Controllers\Master\ServiceDetails\SplitServiceController;
use Illuminate\Support\Facades\Route;


Route::post('api/store-service-split',[SplitServiceController::class,'storeSplitPrice'])->name('api.service.split');
Route::post('/get-split-details', [SplitServiceController::class, 'getSplitDetails'])->name('get.split.details');

Route::post('api/store-split-draft',[SplitServiceController::class,'storeSplitDraft'])->name('api.split.draft');
Route::get('get.split.amount', [SplitServiceController::class, 'getSplitAmount'])->name('get.split.amount');
Route::post('delete-split-amount', [SplitServiceController::class, 'deleteSplitAmount'])->name('delete-split-amount');
