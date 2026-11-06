<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests, DispatchesJobs;

    final protected function safeRedirect($url)
    {
        // @TODO
    }

    public function __construct()
    {
        $this->middleware(function (Request $request, $next) {

            // Load dynamic configurations
            $this->loadDynamicConfig();

            $this->loadUserLocale();

            // Return request
            return $next($request);
        });
    }
    
    private function loadDynamicConfig()
    {
        /*
         * @TODO - load dynamic configs here...
         * like
         * config(['app.company' => '']);
         */

        $business_configs = \App\Models\BusinessConfig::pluck('config_value', 'config_key');
        foreach ($business_configs as $key => $value) {
            config(['business.' . str_replace('_', '.', $key) => $value]);
        }

         config(['vocomi.settings.outlet.enabled' => true]);
    }

    private function loadUserLocale()
    {
        // if (!auth()->check()) {
        //     return;
        // }

        // @TODO - get user locale from database

        $user_locale = session('user_session_locale');
        $app_locale = strtolower(app()->getLocale());

        if ($user_locale === null) {
            // Store default locale to session
            session(['user_session_locale' => $app_locale]);
        } elseif ($user_locale !== $app_locale) {
            // User has already set a locale in previous request, set back to app locale
            app()->setLocale($user_locale);
        }
    }
}
