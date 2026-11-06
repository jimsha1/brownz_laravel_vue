<?php

Route::get('salon/review-form','Salon\Review\ReviewController@showForm')
    ->name('salon.review.form');
Route::post('salon/review-service/booking-search','Salon\Review\ReviewController@bookingSearch')
    ->name('salon.review-service.booking-search');
Route::post('salon/review-service/save','Salon\Review\ReviewController@save')
    ->name('salon.review-service.save');
