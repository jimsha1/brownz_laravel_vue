<?php

use App\Http\Controllers\Reports\SalesReportController;
use Illuminate\Support\Facades\Route;
Route::prefix('sales')->group(function(){
    Route::get('/report', [SalesReportController::class, 'index'])->name('sales.report.index');
    Route::post('/filter-report', [SalesReportController::class, 'filterReport'])->name('sales.report.filter');
    Route::any('/pdf-report', [SalesReportController::class, 'genaratePdfReport'])->name('sales.pdf.genarate');
    Route::any('/excel-report', [SalesReportController::class, 'genarateExcelReport'])->name('sales.excel.genarate');
});