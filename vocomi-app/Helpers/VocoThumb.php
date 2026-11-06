<?php

namespace VocomiApp\Helpers;

use Illuminate\Http\Response;
use Intervention\Image\Laravel\Facades\Image;
use Intervention\Image\Laravel\ImageResponseFactory;

class VocoThumb
{
    private $zoom_crop = false;
    private $width = null;
    private $height = null;
    // cache lifetime in minutes
    private $lifetime = 60;
    private $default_image = null;

    /* intervention properties */
    private $greyscale = false;

    public function setLifetime($minute)
    {
        $this->lifetime = $minute;

        return $this;
    }

    public function setDefaultImage($image)
    {
        $this->default_image = $image;

        return $this;
    }

    public function greyscale()
    {
        $this->greyscale = true;

        return $this;
    }

    public function thumb($path, $filename, $width = null, $height = null, $zc = null)
    {
        $this->zoom_crop = ($zc === true || $zc == 1);

        if (empty($width) && empty($height)) {
            $this->width = 50;
            $this->height = 50;
        } else {
            $this->width = $width;
            $this->height = $height;
        }

        return $this->getImage($path, $filename);
    }

    // -------------------------------------------------------------------------
    // Private functions
    // -------------------------------------------------------------------------

    private function getImage($path, $filename): Response
    {
        $fullPath = $this->resolveFullPath($path, $filename);

        if ($fullPath !== null && file_exists($fullPath)) {
            return $this->resizeImage($fullPath);
        }

        if ($this->default_image !== null) {
            $defaultPath = public_path(ltrim($this->default_image, '/'));

            if (file_exists($defaultPath)) {
                return $this->resizeImage($defaultPath);
            }
        }

        return $this->onePixelImage();
    }

    private function resolveFullPath($path, $filename): ?string
    {
        $path = trim((string) $path, '/');
        $filename = trim((string) $filename, '/');

        if ($path === '' || $filename === '') {
            return null;
        }

        $combined = $path . DIRECTORY_SEPARATOR . $filename;

        if (file_exists($combined)) {
            return $combined;
        }

        if (str_starts_with($path, 'storage/app/public/')) {
            $relative = ltrim(substr($path, strlen('storage/app/public/')) . '/' . $filename, '/');
            $absolute = storage_path('app/public/' . $relative);

            if (file_exists($absolute)) {
                return $absolute;
            }
        }

        $relative = ltrim($path . '/' . $filename, '/');
        $absolute = storage_path('app/public/' . $relative);

        if (file_exists($absolute)) {
            return $absolute;
        }

        $publicStorage = public_path('storage/' . $relative);

        if (file_exists($publicStorage)) {
            return $publicStorage;
        }

        return null;
    }

    private function resizeImage(string $full_path): Response
    {
        $image = Image::read($full_path);
        $this->applyResize($image);

        return ImageResponseFactory::make($image);
    }

    private function applyResize($image): void
    {
        $resize_width = $this->width ? $this->width : null;
        $resize_height = $this->height ? $this->height : null;

        if ($this->greyscale) {
            $image->greyscale();
        }

        if ($this->zoom_crop && $resize_width && $resize_height) {
            $image->cover($resize_width, $resize_height);

            return;
        }

        $image->scaleDown(width: $resize_width, height: $resize_height);
    }

    private function onePixelImage(): Response
    {
        $image = Image::create(1, 1);

        if ($this->greyscale) {
            $image->greyscale();
        }

        if ($this->zoom_crop && $this->width && $this->height) {
            $image->cover($this->width, $this->height);
        } elseif ($this->width || $this->height) {
            $image->scaleDown(width: $this->width, height: $this->height);
        }

        return ImageResponseFactory::make($image);
    }
}
