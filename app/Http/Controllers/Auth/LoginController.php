<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Hash;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

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

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function authenticated($request, $user)
    {

        // EXP
        $this->exp = [
            'PI1206001',
            'PI1612005',
            'PI2111044',
            'PI2111045',
            'PI2302030',
        ];

        if ($user->role_code != 'emp-srv' && $user->role_code != 'WINDS' && $user->role_code != 'BUYER') {
            if (Hash::check('123456', $user->password) && strlen($user->username) == 9 && str_contains(strtoupper($user->username), 'PI') && !in_array(strtoupper($user->username), $this->exp)) {
                return redirect('setting/user')->with('attention', 'Password anda masih standard "123456" anda dihimbau untuk merubah password sesuai ketentuan demi keamanan akun dan menghindari hal yang tidak diinginkan.')->with('page', 'Setting');
            } else {
                return redirect()->intended();
            }
        } else if ($user->role_code == 'WINDS') {
            return redirect()->route('winds');
        } else if ($user->role_code == 'GS') {
            return redirect()->route('gscontrol');
        } else if ($user->role_code == 'BUYER') {
            return redirect()->action('ExtraOrderController@indexExtraOrder');
        } else {
            if (Hash::check('123456', $user->password) && strlen($user->username) == 9 && str_contains(strtoupper($user->username), 'PI') && !in_array(strtoupper($user->username), $this->exp)) {
                return redirect('setting/user')->with('attention', 'Password anda masih standard "123456" anda dihimbau untuk merubah password sesuai ketentuan demi keamanan akun dan menghindari hal yang tidak diinginkan.')->with('page', 'Setting');
            } else {
                return redirect()->route('emp_service');
            }

        }
    }
}
