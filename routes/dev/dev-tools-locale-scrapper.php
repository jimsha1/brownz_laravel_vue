<?php

Route::get('dev-tools/locale-scrapper', [App\Http\Controllers\DevTools\LocaleScrapperController::class, '__invoke']);
