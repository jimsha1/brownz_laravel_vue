<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use PhpParser\Node\Stmt\Const_;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * This namespace is applied to your controller routes.
     *
     * In addition, it is set as the URL generator's root namespace.
     *
     * @var string
     */
    protected $namespace = 'App\Http\Controllers';

    /**
     * The path to the "home" route for your application.
     *
     * @var string
     */
    public const HOME = '/dashboard';
    public const PROFILE = '/profile';
    public const BOOKINGCHANGE = '/channge_booking';
    public const ALLBOOKING = '/all_booking';
    public const ALLBOOKINGEXISTING = '/all_booking_existing';
    public const OPENCLOSE = '/open_close';
    public const DELETEDENOMINATION = '/delete_denomination';
    public const FLOATAMT = '/float_amt';
    public const FORCECLOSEFLOATAMT = '/force_close_float_amt';
    public const EDITCLOSEDFLOAT = '/edit_closed_float';
    public const UPDATECLOSEDFLOAT = '/update_closed_float_count';
    public const CLOSEFLOATAMT = '/close_float_amt';
    public const CLOSEFLOATBUTTON  = '/close_float_button';
    public const LOGOUT  = '/logout';
    public Const FLOATCLOSEAMT = '/float_close_amt';
    public Const PRINTBILL = '/print_float_close_amt';
    public Const PRINTXOUTBILL = '/print_x_out_bill';
    
    public Const REPRINTBILL = '/re_print_float_close_amt';
    public Const XOUT = '/x_out';
    public Const DENOMINATIONPERSMISSION = '/denomoination_permission';
    public Const CALENDER = '/beatician_calender';
    public Const AllCALENDER = '/all_beatician_calender';
    public const BOOKINGCARD = '/booking_card';
    public const BOOKINGDETAILS = '/booking_details';
    public const HOMESLIDER = '/home_slider';
    public const DELETESLIDER = '/delete_slider';
    public const STATUSSLIDER = '/status_slider';
    public const SERVICETYPE = '/service_type';
    public const DELETESERVICETYPE = '/delete_service_type';
    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        //

        parent::boot();
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        $this->mapWebRoutes();

        //
    }

    /**
     * Define the "web" routes for the application.
     *
     * These routes all receive session state, CSRF protection, etc.
     *
     * @return void
     */
    protected function mapWebRoutes()
    {
        Route::middleware('web')
            ->namespace($this->namespace)
            ->group(base_path('routes/web.php'));
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix('api')
            ->middleware('api')
            ->namespace($this->namespace)
            ->group(base_path('routes/api.php'));
    }
}
