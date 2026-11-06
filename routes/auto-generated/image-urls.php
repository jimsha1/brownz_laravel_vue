<?php

use Illuminate\Support\Facades\Route;

Route::get('beautician-photo/{w}/{h}/{zc}/{image}', function ($w, $h, $zc, $image) {
    $image = urldecode($image);
    $thumb = voco_thumb()->setDefaultImage('images/default-beautician.png');

    if ($image !== 'default' && str_contains($image, '/')) {
        $fullPath = storage_path('app/public/' . ltrim($image, '/'));

        return $thumb->thumb(dirname($fullPath), basename($fullPath), min($w, 500), min($h, 500), $zc);
    }

    $legacyDir = storage_path('app/public/' . \App\Models\Beautician::BEAUTICIAN_UPLOAD_PATH);

    return $thumb->thumb($legacyDir, $image, min($w, 500), min($h, 500), $zc);
})->where('image', '.+')->name('beautician.photo.url');

// Route::get('testimonial-photo/{w}/{h}/{zc}/{image}', function ($w, $h, $zc, $image) {
//     return voco_thumb()
//         ->setDefaultImage('images/default-customer.png')
//         ->thumb('storage/' . \App\Models\Testimonial::CUSTOMER_PHOTO_UPLOAD_PATH, $image, min($w, 500), min($h, 500), $zc);
// })->name('testimonial.photo.url');

Route::get('service-banner-photo/{w}/{h}/{zc}/{image}', function ($w, $h, $zc, $image) {
    return voco_thumb()
        ->setDefaultImage('images/default-service-banner.jpg')
        ->thumb('storage/' . \App\Models\ServiceCategory::CATEGORY_IMAGE_UPLOAD_PATH, $image, min($w, 2000), min($h, 1000), $zc);
})->name('service.banner.image.url');

Route::get('service-square-photo/{w}/{h}/{zc}/{image}', function ($w, $h, $zc, $image) {
    return voco_thumb()
        ->setDefaultImage('images/default-service-square.jpg')
        ->thumb('storage/' . \App\Models\ServiceCategory::CATEGORY_IMAGE_UPLOAD_PATH, $image, min($w, 500), min($h, 500), $zc);
})->name('service.square.image.url');
