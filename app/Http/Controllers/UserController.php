<?php

namespace App\Http\Controllers;

use App\Role;
use App\User;
use Hash;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $created_by = User::orderBy('name', 'ASC')
            ->get();

        $users = User::orderBy('name', 'ASC')
            ->where('role_code', '<>', 'S')
            ->get();

        return view('users.index', array(
            'users' => $users,
            'created_by' => $created_by,
        ))->with('page', 'User');
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $roles = Role::orderBy('role_name', 'ASC')
            ->where('role_code', '<>', 'S')
            ->get();
        return view('users.create', array(
            'roles' => $roles,
        ))->with('page', 'User');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $requesst
     * @return \Illuminate\Http\Response
     */
    public function store(request $request)
    {
        try {
            if ($request->get('password') == $request->get('password_confirmation')) {
                $id = Auth::id();
                $user = new User([
                    'name' => ucwords($request->get('name')),
                    'username' => strtoupper($request->get('username')),
                    'email' => $request->get('email'),
                    'password' => bcrypt($request->get('password')),
                    'role_code' => $request->get('role_code'),
                    'avatar' => 'image-user.png',
                    'created_by' => $id,
                ]);

                $user->save();
                return redirect('/index/user')->with('status', 'New user has been created.')->with('page', 'User');
            } else {
                return back()->withErrors(['password' => ['Password confirmation is invalid.']])->with('page', 'User');
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Username or e-mail already exist.')->with('page', 'User');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'User');
            }
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $created_bys = User::orderBy('name', 'ASC')->get();
        $user = User::find($id);

        return view('users.show', array(
            'user' => $user,
            'created_bys' => $created_bys,
        ))->with('page', 'User');
        //
    }

    public function index_setting()
    {
        $user = User::find(Auth::id());

        return view('auth.setting', array(
            'user' => $user,
        ))->with('page', 'Setting');
    }

    public function setting(Request $request)
    {
        try {
            $user = User::find(Auth::id());
            if (strlen($request->get('oldPassword')) > 0 && strlen($request->get('newPassword')) > 0 && strlen($request->get('confirmPassword')) > 0) {
                if (Hash::check($request->get('oldPassword'), Auth::user()->password)) {
                    if ($request->get('newPassword') == $request->get('confirmPassword')) {
                        if ($request->get('validation') > 0) {
                            return back()->with('error', "Harap ikuti aturan password");
                        } else {
                            $user->name = ucwords($request->get('name'));
                            $user->email = $request->get('email');
                            $user->password = bcrypt($request->get('newPassword'));
                            $user->save();

                            if (Auth::user()->role_code == 'emp-srv') {
                                return redirect()->route('emp_service');
                            } else {
                                return redirect('/setting/user')->with('status', 'Data pengguna telah diedit.')->with('page', 'User');
                            }

                        }

                    } else {
                        return redirect('/setting/user')->with('error', 'Konfirmasi password tidak cocok.')->with('page', 'Setting');
                    }
                } else {
                    return redirect('/setting/user')->with('error', 'Kata Sandi Lama tidak cocok.')->with('page', 'Setting');
                }
            } else {
                $user->name = ucwords($request->get('name'));
                $user->email = $request->get('email');
                $user->save();

                if (Auth::user()->role_code == 'emp-srv') {
                    return redirect()->route('emp_service');
                } else {
                    return redirect('/setting/user')->with('status', 'Data pengguna telah diedit.')->with('page', 'User');
                }

            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return redirect('/setting/user')->with('error', 'Nama pengguna atau email sudah ada.')->with('page', 'User');
            } else {
                return redirect('/setting/user')->with('error', $e->getMessage() . 'Hubungi MIS')->with('page', 'User');
            }

        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $roles = Role::orderBy('role_name', 'ASC')
            ->where('role_code', '<>', 'S')
            ->get();

        $user = User::find($id);
        return view('users.edit', array(
            'user' => $user,
            'roles' => $roles,
        ))->with('page', 'User');
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::find($id);
        if ($request->get('password') != "" || $request->get('password_confirmation') != "") {
            if ($request->get('password') == $request->get('password_confirmation')) {
                try {
                    $user->name = ucwords($request->get('name'));
                    $user->username = strtoupper($request->get('username'));
                    $user->email = $request->get('email');
                    $user->password = bcrypt($request->get('password'));
                    $user->role_code = $request->get('role_code');
                    $user->save();
                    return redirect('/index/user')->with('status', 'User data has been edited.')->with('page', 'User');
                } catch (QueryException $e) {
                    $error_code = $e->errorInfo[1];
                    if ($error_code == 1062) {
                        return back()->with('error', 'Username or e-mail already exist.')->with('page', 'User');
                    } else {
                        return back()->with('error', $e->getMessage())->with('page', 'User');
                    }
                }
            } else {
                return back()->withErrors(['password' => ['Password confirmation is invalid.']])->with('page', 'User');
            }
        } else {
            try {
                $user->name = ucwords($request->get('name'));
                $user->username = strtoupper($request->get('username'));
                $user->email = $request->get('email');
                $user->role_code = $request->get('role_code');
                $user->save();
                return redirect('/index/user')->with('status', 'User data has been edited.')->with('page', 'User');
            } catch (QueryException $e) {
                $error_code = $e->errorInfo[1];
                if ($error_code == 1062) {
                    return back()->with('error', 'Username or e-mail already exist.')->with('page', 'User');
                } else {
                    return back()->with('error', $e->getMessage())->with('page', 'User');
                }
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::find($id);
        $user->delete();

        return redirect('/index/user')->with('status', 'User has been deleted.')->with('page', 'User');
        //
    }
}
