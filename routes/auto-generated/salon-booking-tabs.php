<?php

use App\Http\Controllers\Salon\BookingDetails\UpdateBookingController;

Route::prefix('salon/booking')->group(function () {

    // -------------------------------------------------------------------------
    // Tabs for listing
    // Leave "tab_name" parameter empty, for ROOT URL in left sidebar
    // -------------------------------------------------------------------------

    Route::get('/{tab_name?}', [App\Http\Controllers\Salon\BookingList\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.salon.booking.list');


    // -------------------------------------------------------------------------
    // Various details tab and its "save" variant
    // -------------------------------------------------------------------------

    Route::get('/tab/{booking_id}/{tab_name?}', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\Base\TabBaseController::class, 'index'])
        ->name('tab.salon.booking.details');

    Route::post('/tab-saver/{tab_name}/save', function ($tab_name) {

        // Be careful! dynamic controller used for direct invoking of controller
        return \App::call('App\Http\Controllers\Salon\BookingDetails\AjaxTabs\Tab'
            . \Illuminate\Support\Str::studly($tab_name) . 'Controller@save', [
            'tab_name' => $tab_name,
        ]);
    })
        ->name('tab.salon.booking.save');
});

Route::prefix('booking')->group(function () {
    Route::get('new-booking', [App\Http\Controllers\Salon\BookingDetails\NewBookingController::class])
        ->name('salon.booking.new');
    Route::get('ProductDelivery', [App\Http\Controllers\Salon\BookingList\AjaxTabs\Base\TabBaseController::class, 'productDelivery'])
        ->name('salon.booking.product.delivery');
    Route::post('ProductDelivery', [App\Http\Controllers\Salon\BookingList\AjaxTabs\Base\TabBaseController::class, 'productDelivery'])
        ->name('salon.booking.product.delivery');
    Route::get('ProductDeliveryDetails', [App\Http\Controllers\Salon\BookingList\AjaxTabs\Base\TabBaseController::class, 'productDeliveryDetails'])
        ->name('salon.booking.product.delivery.details');
    Route::post('ProductDeliveryStatus', [App\Http\Controllers\Salon\BookingDetails\UpdateBookingController::class, 'productDeliveryStatus'])
        ->name('salon.booking.product.delivery.status');

    Route::post('checkItemExistInBatchNo', [App\Http\Controllers\Salon\BookingDetails\UpdateBookingController::class, 'checkItemExistInBatchNo'])
        ->name('salon.booking.product.delivery.check');

    Route::post('validateBatchNo', [App\Http\Controllers\Salon\BookingDetails\UpdateBookingController::class, 'validateBatchNo'])
        ->name('salon.booking.product.delivery.validate');

    Route::post('save-item', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, '__invoke'])
        ->name('salon.booking.item.save');

    Route::post('save-quick-item', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'quickServiceItem'])
        ->name('salon.booking.quick.item.save');
    Route::post('save-quick-product', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'quickProductItem'])
        ->name('salon.booking.quick.product.save');



    Route::post('update-item', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'update'])
        ->name('salon.booking.item.update');

    Route::post('filter-prodcts', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'filterProdcts'])
        ->name('salon.booking.filter.prodcts');



    Route::post('update-booking-price', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateBookingPrice'])
        ->name('salon.booking.price.update');

    Route::post('update-service-price', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateServicePrice'])
        ->name('salon.service.price.update');

    Route::post('update-service-quantity', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateServiceQuantity'])
        ->name('salon.service.quantity.update');


    Route::post('save-qty', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'addQty'])
        ->name('salon.booking.qty.save');


    Route::post('save-refund-qty', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateRefundQty'])
        ->name('salon.booking.refund.qty.save');

    Route::post('save-refund-service-qty', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateRefundServiceQty'])
        ->name('salon.booking.refund.service.qty.save');


    Route::post('update-date', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'updateDate'])
        ->name('salon.booking.date.update');

    Route::post('delete-item', [App\Http\Controllers\Salon\BookingDetails\DeleteBookingServiceController::class, '__invoke'])
        ->name('salon.booking.item.delete');

    Route::post('save-payment', [App\Http\Controllers\Salon\BookingDetails\SavePaymentController::class, '__invoke'])
        ->name('salon.booking.payment.save');

    Route::post('save-split-payment', [App\Http\Controllers\Salon\BookingDetails\SavePaymentController::class, 'saveSplitPayments'])
        ->name('salon.booking.payment.split.save');

    Route::post('rebalance-payment', [App\Http\Controllers\Salon\BookingDetails\SavePaymentController::class, 'rebalancePayments'])
        ->name('salon.booking.payment.rebalance');

    Route::post('delete-payment', [App\Http\Controllers\Salon\BookingDetails\SavePaymentController::class, 'delete'])
        ->name('salon.booking.payment.delete');

    Route::post('update-payment-mode', [App\Http\Controllers\Salon\BookingDetails\SavePaymentController::class, 'updateMode'])
        ->name('salon.booking.payment.mode.update');

    Route::post('save-discount', [App\Http\Controllers\Salon\BookingDetails\SaveDiscountController::class, '__invoke'])
        ->name('salon.booking.discount.save');


    Route::post('final-update-booking', [App\Http\Controllers\Salon\BookingDetails\UpdateBookingController::class, '__invoke'])
        ->name('salon.booking.final.update');

    Route::post('multi-refund-booking', [App\Http\Controllers\Salon\BookingDetails\RefundBookingController::class, '__invoke'])
        ->name('salon.booking.refund.items');

    Route::post('cancel-order', [App\Http\Controllers\Salon\BookingDetails\CancelBookingController::class, '__invoke'])
        ->name('salon.booking.cancel.order');

    Route::post('save-client-basic-info', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\TabEditController::class, 'saveBookingClient'])
        ->name('salon.booking.client.save');
    Route::any('get-product-list', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\TabEditController::class, 'getAllitems'])
        ->name('salon.booking.get.product');
    Route::any('get-service-list', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\TabEditController::class, 'getAllServices'])
        ->name('salon.booking.get.service');

    Route::post('approve-item-store', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'approveItemStore'])
        ->name('salon.booking.approve.data.store');

    Route::any('get-quick-service-list', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\TabEditController::class, 'getAllQuickServices'])
        ->name('salon.quick.booking.get.service');

    Route::any('get-beauticians-for-quick-service', [App\Http\Controllers\Salon\BookingDetails\AjaxTabs\TabEditController::class, 'getBeauticiansForQuickService'])
        ->name('salon.quick.booking.get.beauticians');
});


Route::post('/change-price', [App\Http\Controllers\Salon\BookingDetails\SaveBookingServiceController::class, 'changeBookingPrice'])
    ->name('price_change');

Route::post('/total-discount-price', [App\Http\Controllers\Salon\BookingDetails\SaveDiscountController::class, 'totalDiscountPrice'])
    ->name('total.discount.price');

Route::post('/store-roundoff', [App\Http\Controllers\Salon\BookingDetails\SaveDiscountController::class, 'store_roundoff'])
    ->name('store-roundoff');

Route::post('check-availability', [App\Http\Controllers\Salon\BookingDetails\CancelBookingController::class, 'check_availabilty'])
    ->name('salon.booking.check.availability');

Route::post('undo-check-in', [App\Http\Controllers\Salon\BookingDetails\CancelBookingController::class, 'undoCheckIn'])
    ->name('salon.booking.undo.check.in');




