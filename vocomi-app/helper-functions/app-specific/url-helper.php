<?php

function beautician_photo_url($image, $width = null, $height = null, $zc = null)
{
    $image = trim($image ?? '');

    // Uploaded photos are stored under storage/app/public/uploads/beauticians/
    // and served directly via the public/storage symlink.
    if ($image !== '' && str_contains($image, '/')) {
        return asset('storage/' . ltrim($image, '/'));
    }

    return route('beautician.photo.url', [
        'image' => $image ?: 'default',
        'w' => (int) $width,
        'h' => (int) $height,
        'zc' => (int) $zc,
    ]);
}

function testimonial_photo_url($image, $width = null, $height = null, $zc = null)
{
    return route('testimonial.photo.url', [
        'image' => trim($image) ?: 'default',
        'w' => (int) $width,
        'h' => (int) $height,
        'zc' => (int) $zc,
    ]);
}

function category_banner_image_url($image, $width = null, $height = null, $zc = null)
{
    return route('service.banner.image.url', [
        'image' => trim($image) ?: 'default',
        'w' => (int) $width,
        'h' => (int) $height,
        'zc' => (int) $zc,
    ]);
}

function category_square_image_url($image, $width = null, $height = null, $zc = null)
{
    return route('service.square.image.url', [
        'image' => trim($image) ?: 'default',
        'w' => (int) $width,
        'h' => (int) $height,
        'zc' => (int) $zc,
    ]);
}
