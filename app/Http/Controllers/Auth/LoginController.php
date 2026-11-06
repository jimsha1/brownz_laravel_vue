<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserLoginHistory;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Client\Response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers {
        authenticated as protected traitAuthenticated;
    }

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/dashboard';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }
        Auth::logoutOtherDevices($request->password);
        return $request->wantsJson()
            ? new Response('', 204)
            : redirect()->intended($this->redirectPath());
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(\Illuminate\Http\Request $request)
    {

        $field = filter_var($request->input($this->username()), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        return [
            $field => $request->{$this->username()},
            'password' => $request->password,
            'user_status' => User::USER_STATUS_ACTIVE,

        ];
    }

    public function username()
    {
        return 'username';
    }

    protected function authenticated(Request $request, $user)
    {
        /* Save login details to db */

        $datetime = new \DateTime();

        $login = new UserLoginHistory();
        $login->user_id = $user->id;
        $login->ip_address = $request->getClientIp();
        $login->user_agent = $request->userAgent();
        $login->datetime = $datetime;

        $login->save();

        // Update last login time. Use 'DB::table()'
        DB::table('users')
            ->where('id', $user->id)
            ->where('user_status', User::USER_STATUS_ACTIVE)
            ->update(['last_login' => $datetime]);

        $this->traitAuthenticated($request, $user);
    }

    public function logout(Request $request)
    {

        $this->guard()->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/login');
    }
}
