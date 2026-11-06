<?php

namespace App\Http\Controllers\Locale;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\Session\Session;
use Illuminate\Http\Request;

class SwitchLocaleController extends Controller
{
    public function __invoke(Request $request)
    {
        // Store requested locale to session
        session(['user_session_locale' => $request->locale]);
        // Redirect back to previous page
        return back();
    }
}
