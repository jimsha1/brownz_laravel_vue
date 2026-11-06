<?php

use App\Http\Controllers\Admin\LeaveManagement\LeaveManagementController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\Dashboard\AdminDashboardController;
use App\Http\Controllers\MakeupController;
use App\Http\Controllers\OpenCloseController;
use App\Http\Controllers\Payroll\PayrollReportController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\Stock\StockItemList\AjaxTabs\TabAllController;
use App\Http\Controllers\Reports\FinancialStatementController;
use App\Http\Controllers\Accounts\AccountCategoryController;
use App\Http\Controllers\Accounts\FinancialYearController;
use App\Http\Controllers\Master\CompanyProfileController;
use App\Http\Controllers\Salon\BookingDetails\InvoiceController;
use App\Http\Controllers\SocialContactUsController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Route;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/salon/invoice/{id}', [InvoiceController::class, 'viewInvoice'])->name('salon.invoice');
Route::post('/whatsapp-invoice/{id}/resend', [InvoiceController::class, 'resendWhatsAppInvoice'])->name('whatsapp-invoice.resend');

Route::get('/encrypt-booking/{id}', function ($id) {
    return Crypt::encryptString((string) $id);
});

Route::domain(config('vocomi.domain.admin'))->middleware(['auth'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });

    Route::get('/dashboard', [AdminDashboardController::class, '__invoke'])->name('dashboard');

    Route::get('/admin/company/profile', [CompanyProfileController::class, 'index'])->name('admin.business.profile');
    Route::post('/admin/company/profile/save', [CompanyProfileController::class, 'save'])->name('admin.business.profile.save');

    Route::any('/logout', [LoginController::class, 'logout'])->name('Logout-user');
    Route::any(\App\Providers\RouteServiceProvider::CALENDER, [CalendarController::class, 'beautician_calender_index'])->name('beatician.calender');
    Route::any(\App\Providers\RouteServiceProvider::AllCALENDER, [CalendarController::class, 'allBeaticianCalender'])->name('all.beatician.calender');
    Route::any(\App\Providers\RouteServiceProvider::BOOKINGDETAILS, [CalendarController::class, 'allBookingDetails'])->name('booking.details');
    Route::any(\App\Providers\RouteServiceProvider::BOOKINGCARD, [CalendarController::class, 'bookingCard'])->name('booking.card');

    Route::any(\App\Providers\RouteServiceProvider::OPENCLOSE, [OpenCloseController::class, 'index'])->name('denomination');
    Route::any(\App\Providers\RouteServiceProvider::DELETEDENOMINATION, [OpenCloseController::class, 'deleteDenomination'])->name('delete.denomination');
    Route::any(\App\Providers\RouteServiceProvider::FLOATAMT, [OpenCloseController::class, 'floatAmt'])->name('float_amt');
    Route::any(\App\Providers\RouteServiceProvider::FORCECLOSEFLOATAMT, [OpenCloseController::class, 'forcefloatAmt'])->name('force_close_float_amt');
    Route::any(\App\Providers\RouteServiceProvider::EDITCLOSEDFLOAT, [OpenCloseController::class, 'editClosedFloat'])->name('edit.closed.float');
    Route::any(\App\Providers\RouteServiceProvider::UPDATECLOSEDFLOAT, [OpenCloseController::class, 'updateClosedFloatCount'])->name('update.closed.float');
    Route::any(\App\Providers\RouteServiceProvider::CLOSEFLOATAMT, [OpenCloseController::class, 'closeFloatAmount'])->name('close.float_amt');
    Route::any(\App\Providers\RouteServiceProvider::CLOSEFLOATBUTTON, [OpenCloseController::class, 'closeFloatButton'])->name('close.float_button');

    Route::any(\App\Providers\RouteServiceProvider::FLOATCLOSEAMT, [OpenCloseController::class, 'floatCloseAmt'])->name('close.float.denomination');
    Route::any(\App\Providers\RouteServiceProvider::PRINTBILL, [OpenCloseController::class, 'printAmtCloseBill'])->name('close.float.print.bill');
    Route::any(\App\Providers\RouteServiceProvider::PRINTXOUTBILL, [OpenCloseController::class, 'printXoutBill'])->name('print.xout.bill');
    Route::any(\App\Providers\RouteServiceProvider::REPRINTBILL, [OpenCloseController::class, 'reprintCloseAmount'])->name('re.print.bill');
    Route::any(\App\Providers\RouteServiceProvider::XOUT, [OpenCloseController::class, 'Xout'])->name('x.out');
    Route::any(\App\Providers\RouteServiceProvider::DENOMINATIONPERSMISSION, [OpenCloseController::class, 'denominationPermission'])->name('denomination.permission');

    // Include all dev routes
    foreach (glob(__DIR__ . '/dev/*.php') as $file) {
        require_once $file;
    }

    // Include all auto generated routes
    foreach (glob(__DIR__ . '/auto-generated/*.php') as $file) {
        require_once $file;
    }

    // include all routes to website (public)
    foreach (glob(__DIR__ . '/web-public/*.php') as $file) {
        require_once $file;
    }

    Route::any('/channge_booking', [App\Http\Controllers\BookingTime\BookingChangeController::class, '__invoke'])->name('booking.change');
    Route::any('/all_booking', [App\Http\Controllers\BookingTime\BookingChangeController::class, 'allBooking'])->name('all.booking');
    Route::any('/all_booking_existing', [App\Http\Controllers\BookingTime\BookingChangeController::class, 'allBookingExisting'])->name('all.booking.existing');
    Route::get('/ajax-autocomplete-search', [App\Http\Controllers\BookingTime\BookingChangeController::class, 'selectSearch'])->name('ajax-autocomplete-search');
    Route::any('/channge_service_by_beautician', [App\Http\Controllers\BookingTime\BookingChangeController::class, 'changeServiceByBooking'])->name('services-by-beautician');

    Route::controller(ReportController::class)->group(function () {

        // sales Report
        Route::get('report/sales', 'sales_index')->name('sales.report');
        Route::get('report/daily-sales', 'daily_sales_index')->name('daily-sales.report');
        Route::get('report/client', 'client_index')->name('client.report');
        Route::post('report/client-update', 'client_update')->name('client-report.update');
        Route::get('report/vendor', 'vendor_index')->name('vendor.report');
        Route::get('report/edit-log', 'edit_log_index')->name('edit-log.report');
        Route::get('report/tax', 'tax_index')->name('tax.report');
        Route::get('report/service', 'service_index')->name('service.report');
        Route::get('report/service-used-products', 'used_product_index')->name('service-used-product.report');
        Route::get('report/beautician', 'beautician_index')->name('beautician.report');
        Route::get('report/whatsapp-invoice', 'whatsapp_invoice_index')->name('whatsapp-invoice.report');

        Route::get('sales-report-export-to-pdf', 'sales_report_exportToPDF')->name('sales-report.export-to-pdf');
        Route::get('client-report-export-to-pdf', 'client_report_exportToPDF')->name('client-report.export-to-pdf');
        Route::get('vendor-report-export-to-pdf', 'vendor_report_exportToPDF')->name('vendor-report.export-to-pdf');
        Route::get('edit-log-report-export-to-pdf', 'edit_log_report_exportToPDF')->name('edit-log-report.export-to-pdf');
        Route::get('tax-report-export-to-pdf', 'tax_report_exportToPDF')->name('tax-report.export-to-pdf');
        Route::get('service-report-export-to-pdf', 'service_report_exportToPDF')->name('service-report.export-to-pdf');
        Route::get('service-used-product-report-export-to-pdf', 'used_product_report_exportToPDF')->name('service-used-product-report.export-to-pdf');
        Route::get('beautician-report-export-to-pdf', 'beautician_report_exportToPDF')->name('beautician-report.export-to-pdf');

        Route::get('sales-report-export-to-excel', 'SalesReportExportToExcel')->name('sales-report.export-to-excel');
        Route::get('daily-sales-report-export-to-excel', 'dailySalesReportExportToExcel')->name('daily-sales-report.export-to-excel');
        Route::get('client-report-export-to-excel', 'ClientReportExportToExcel')->name('client-report.export-to-excel');
        Route::get('vendor-report-export-to-excel', 'VendorReportExportToExcel')->name('vendor-report.export-to-excel');
        Route::get('edit-log-report-export-to-excel', 'EditLogReportExportToExcel')->name('edit-log-report.export-to-excel');
        Route::get('tax-report-export-to-excel', 'TaxReportExportToExcel')->name('tax-report.export-to-excel');
        Route::get('service-report-export-to-excel', 'ServiceReportExportToExcel')->name('service-report.export-to-excel');
        Route::get('service-used-product-report-export-to-excel', 'UsedProductReportExportToExcel')->name('service-used-product-report.export-to-excel');
        Route::get('beautician-report-export-to-excel', 'BeauticianReportExportToExcel')->name('beautician-report.export-to-excel');

        Route::get('sales-report-summary-export-to-pdf', 'sales_report_summary_exportToPDF')->name('sales-report-summary.export-to-pdf');
        Route::get('service-report-summary-export-to-pdf', 'service_report_summary_exportToPDF')->name('service-report-summary.export-to-pdf');
        Route::get('beautician-report-summary-export-to-pdf', 'beautician_report_summary_exportToPDF')->name('beautician-report-summary.export-to-pdf');


        Route::get('report/consultation', 'consultation_index')->name('consultation.report');
        Route::post('report/consultation-list', 'consultation_vue_list_api')->name('consultation.vue-list');
        Route::get('skin_consultation_print', 'skin_consultation_print')->name('skin_consultation.print');
        Route::get('/consultation/print/skin/{id}', 'print1')->name('consultation.print.skin');
        Route::get('/consultation/print/hair/{id}', 'print2')->name('consultation.print.hair');
        Route::get('consultation-report-export-to-pdf', 'consultation_report_exportToPDF')->name('consultation-report.export-to-pdf');
    });

    Route::get('stock-item-report-export-to-pdf', [TabAllController::class, 'exportPdf'])->name('stock-item-report.export-to-pdf');
    Route::get('stock-item-report-export-to-excel', [TabAllController::class, 'exportExcel'])->name('stock-item-report.export-to-excel');

    Route::controller(PayrollReportController::class)->group(function () {

        Route::get('report/salary', 'salary_index')->name('salary.report');
        Route::get('report/commission', 'commission_index')->name('commission.report');
        Route::get('report/attendance', 'attendance_index')->name('attendance.report');
        Route::get('report/payroll', 'payroll_index')->name('payroll.report');
        Route::get('report/incentive', 'incentive_index')->name('incentive.report');

        Route::get('salary-report-export-to-pdf', 'salary_report_exportToPDF')->name('salary-report.export-to-pdf');
        Route::get('salary-report-print', 'salary_report_print')->name('salary-report.print');
        Route::get('commission-report-export-to-pdf', 'Commission_report_exportToPDF')->name('commission-report.export-to-pdf');
        Route::get('commission-report-print', 'commission_report_print')->name('commission-report.print');
        Route::get('attendance-report-export-to-pdf', 'Attendance_report_exportToPDF')->name('attendance-report.export-to-pdf');
        Route::get('attendance-report-print', 'attendance_report_print')->name('attendance-report.print');
        Route::get('payroll-report-export-to-pdf', 'Payroll_report_exportToPDF')->name('payroll-report.export-to-pdf');
        Route::get('payroll-report-print', 'payroll_report_print')->name('payroll-report.print');
        Route::get('incentive-report-export-to-pdf', 'Incentive_report_exportToPDF')->name('incentive-report.export-to-pdf');
        Route::get('incentive-report-print', 'incentive_report_print')->name('incentive-report.print');

        Route::get('salary-report-export-to-excel', 'SalaryReportExportToExcel')->name('salary-report.export-to-excel');
        Route::get('commission-report-export-to-excel', 'CommissionReportExportToExcel')->name('commission-report.export-to-excel');
        Route::get('attendance-report-export-to-excel', 'AttendanceReportExportToExcel')->name('attendance-report.export-to-excel');
        Route::get('payroll-report-export-to-excel', 'PayrollReportExportToExcel')->name('payroll-report.export-to-excel');
        Route::get('incentive-report-export-to-excel', 'IncentiveReportExportToExcel')->name('incentive-report.export-to-excel');

        Route::get('report/working_hours', 'working_hours_index')->name('working-hours.report');
        Route::get('working-hours-report-print', 'working_hours_report_print')->name('working-hours-report.print');
    });

    Route::post('/admin/leave-management/update-status/{id}', [LeaveManagementController::class, 'updateStatus'])
        ->name('admin.leave.management.updateStatus');

    // hair consultation
    Route::get('consultation-for-booking', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'findForBooking'])
        ->name('consultation.for.booking');
    Route::post('hair-partial-submit', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'hairPartialSubmit'])
        ->name('hair.partial.submit');
    Route::get('hair-form-complete/{id}', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'showCompleteForm'])
        ->name('hair.form.complete');
    Route::post('/hair-consultation/{id}', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'update'])->name('hair.update');
    Route::post('hair-consultation/approve/{id}', [ReportController::class, 'approveHairConsultation']);
    Route::post('skin-consultation/approve/{id}', [ReportController::class, 'approveSkinConsultation']);


    // skin consultation
    Route::post('skin-partial-submit', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'skinPartialSubmit'])
        ->name('skin.partial.submit');
    Route::get('skin-form-complete/{id}', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'showCompleteSkinForm'])
        ->name('skin.form.complete');
    Route::post('/skin-consultation/{id}', [App\Http\Controllers\Salon\BookingDetails\HairConsultationController::class, 'skinUpdate'])->name('skin.update');

    Route::post('accounts/account-categories', [AccountCategoryController::class, 'store'])
        ->name('accounts.account-categories.store');
    Route::delete('accounts/account-categories/{category}', [AccountCategoryController::class, 'destroy'])
        ->name('accounts.account-categories.destroy');

    Route::controller(FinancialYearController::class)->group(function () {
        Route::get('financial-year', 'index')->name('financial-year');
        Route::get('financial-year-list', 'list')->name('financial-year.list');
        Route::post('financial-year-initialize', 'initialize')->name('financial-year.initialize');
        Route::post('financial-year-preview-close', 'previewClose')->name('financial-year.preview-close');
        Route::post('financial-year-close', 'close')->name('financial-year.close');
        Route::post('financial-year-preview-open', 'previewOpen')->name('financial-year.preview-open');
        Route::post('financial-year-open', 'open')->name('financial-year.open');
        Route::get('financial-year-opening-balance', 'openingBalance')->name('financial-year.opening-balance');
        Route::get('financial-year-opening-balance-print', 'openingBalancePrint')->name('financial-year.opening-balance.print');
        Route::get('financial-year-opening-balance-export-to-pdf', 'openingBalanceExportToPDF')->name('financial-year.opening-balance.export-to-pdf');
        Route::get('financial-year-opening-balance-export-to-excel', 'openingBalanceExportToExcel')->name('financial-year.opening-balance.export-to-excel');
        Route::get('financial-year-details', 'details')->name('financial-year.details');
        Route::get('financial-year-details-print', 'detailsPrint')->name('financial-year.details.print');
        Route::get('financial-year-details-export-to-pdf', 'detailsExportToPDF')->name('financial-year.details.export-to-pdf');
        Route::get('financial-year-details-export-to-excel', 'detailsExportToExcel')->name('financial-year.details.export-to-excel');
    });

    Route::controller(FinancialStatementController::class)->group(function () {
        // Trial Balance
        Route::get('trial-balance', 'trialBalance')->name('trial-balance');
        Route::post('trialbalance-vue-list', 'trialbalance_vue_list')->name('trialbalance-vue-list');
        Route::get('trial-balance-export-to-pdf', 'trialBalanceExportToPDF')->name('trial-balance.export-to-pdf');
        Route::get('trial-balance-export-to-excel', 'trialBalanceExportToExcel')->name('trial-balance.export-to-excel');
        Route::get('trial-balance-print', 'trialBalancePrint')->name('trial-balance.print');
        // Profit And Loss
        Route::get('profit-lossprofit-loss', 'profitLoss')->name('profit-loss');
        Route::post('profit-and-loss-account-transactions', 'profitlossAccountTransactions')->name('profit-and-loss-account-transactions');
        Route::get('profit-loss-account-transactions-export-to-pdf', 'profitLossAccountTransactionsExportToPDF')->name('profit-loss-account-transactions.export-to-pdf');
        Route::get('profit-loss-account-transactions-export-to-excel', 'profitLossAccountTransactionsExportToExcel')->name('profit-loss-account-transactions.export-to-excel');
        Route::get('profit-loss-account-transactions-print', 'profitLossAccountTransactionsPrint')->name('profit-loss-account-transactions.print');
        Route::post('profit-and-loss-vue-list', 'profitloss_vue_list')->name('profit-and-loss-vue-list');
        Route::get('profit-loss-export-to-pdf', 'profitLossExportToPDF')->name('profit-loss.export-to-pdf');
        Route::get('profit-loss-export-to-excel', 'profitLossExportToExcel')->name('profit-loss.export-to-excel');
        Route::get('profit-loss-print', 'profitLossPrint')->name('profit-loss.print');
        // Balance Sheet
        Route::get('balance-sheet', 'balanceSheet')->name('balance-sheet');
        Route::post('balance-sheet-vue-list', 'balanceSheet_vue_list')->name('balance-sheet-vue-list');
        Route::get('balance-sheet-export-to-pdf', 'balanceSheetExportToPDF')->name('balance-sheet.export-to-pdf');
        Route::get('balance-sheet-export-to-excel', 'balanceSheetExportToExcel')->name('balance-sheet.export-to-excel');
        Route::get('balance-sheet-print', 'balanceSheetPrint')->name('balance-sheet.print');
        // Day Book
        Route::get('day-book', 'dayBook')->name('day-book');
        Route::post('day-book-vue-list', 'day_book_vue_list')->name('day-book-vue-list');
        Route::get('day-book-export-to-pdf', 'dayBookExportToPDF')->name('day-book.export-to-pdf');
        Route::get('day-book-export-to-excel', 'dayBookExportToExcel')->name('day-book.export-to-excel');
        Route::get('day-book-print', 'dayBookPrint')->name('day-book.print');
    });

    Route::post('cancel-order', [App\Http\Controllers\Salon\BookingDetails\CancelBookingController::class, '__invoke'])
        ->name('salon.booking.cancel.order');

    Route::get('used-products/search', [App\Http\Controllers\Salon\BookingDetails\ServiceUsedProductController::class, 'search'])
        ->name('salon.booking.used-product.search');
    Route::post('used-products/quantity', [App\Http\Controllers\Salon\BookingDetails\ServiceUsedProductController::class, 'updateQuantity'])
        ->name('salon.booking.used-product.quantity');
    Route::post('used-products/add', [App\Http\Controllers\Salon\BookingDetails\ServiceUsedProductController::class, 'add'])
        ->name('salon.booking.used-product.add');
    Route::post('used-products/remove', [App\Http\Controllers\Salon\BookingDetails\ServiceUsedProductController::class, 'remove'])
        ->name('salon.booking.used-product.remove');
});

// Auth::routes();

Route::get('/social-contact-us', [SocialContactUsController::class, 'index'])->name('contact.index');
Route::post('/store-contact-us', [SocialContactUsController::class, 'store'])->name('contact.store');

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::controller(AdminDashboardController::class)->group(function () {
    Route::get('remove-my-account', 'removedAccount');
    Route::get('privacy-policy', 'privacy');
    Route::post('remove-my-account-post', 'removedAccountPost')->name('delete-account-request-submit');
});
