<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Role;
use App\Navigation;
use App\Permission;
use App\EmployeeSync;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;

class RoleController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::orderBy('id', 'ASC')
        // ->where('role_code', '<>', 'S')
        ->get();

        $position = DB::SELECT("SELECT DISTINCT
            (
            COALESCE ( position, role_name )) AS position 
        FROM
            roles 
        WHERE
            created_by = 42 
            AND created_at > '2022-07-25'");

        $navigation = DB::SELECT("SELECT DISTINCT
            ( navigation_name ),
            navigation_code
        FROM
            navigations 
        WHERE
            created_by = 42 
            AND created_at > '2022-07-29'");

        $permission = DB::SELECT("SELECT
            navigation_code,
            GROUP_CONCAT(
            DISTINCT ( position )) AS position,
            GROUP_CONCAT(
            DISTINCT ( permissions.role_code )) AS role_code 
        FROM
            permissions
            JOIN roles ON roles.role_code = permissions.role_code 
        WHERE
            permissions.created_by = 42 
            AND permissions.created_at > '2022-07-25' 
        GROUP BY
            navigation_code");

        return view('roles.index', array(
            'roles' => $roles,
            'position' => $position,
            'position2' => $position,
            'navigation' => $navigation,
            'permission' => $permission,
        ))->with('page', 'Role');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $nav_admins = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'A%')
        ->get();

        $nav_masters = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'M%')
        ->get();

        $nav_services = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'S%')
        ->get();

        $nav_reports = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'R%')
        ->get();

        $nav_transactions = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'T%')
        ->get();

        return view('roles.create', array(
            'nav_admins' => $nav_admins,
            'nav_masters' => $nav_masters,
            'nav_services' => $nav_services,
            'nav_reports' => $nav_reports,
            'nav_transactions' => $nav_transactions,
        ))->with('page', 'Role');
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            $id = Auth::id();
            $role = new Role([
                'role_code' => $request->get('role_code'),
                'role_name' => $request->get('role_name'),
                'created_by' => $id
            ]);
            $role->save();

            $permission = new Permission([
                'role_code' => $request->get('role_code'),
                'navigation_code' => 'Dashboard',
                'created_by' => $id
            ]);
            $permission->save();

            $navigations = $request->get('navigation_code');

            $permission = '';
            if($navigations != ''){
                foreach ($navigations as $navigation) {
                    $permission = new Permission([
                        'role_code' => $request->get('role_code'),
                        'navigation_code' => $navigation,
                        'created_by' => $id
                    ]);
                    $permission->save();
                }
            }

            return redirect('/index/role')->with('status', 'New role has been created.')->with('page', 'Role');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Role code already exist.')->with('page', 'Destination');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Destination');
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
        $role = Role::find($id);
        $permissions = Permission::where('role_code', '=', $role->role_code)->orderBy('id', 'asc')->get();

        return view('roles.show', array(
            'role' => $role,
            'permissions' => $permissions,
        ))->with('page', 'Role');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = Role::find($id);
        
        $nav_admins = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'A%')
        ->get();

        $nav_masters = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'M%')
        ->get();

        $nav_services = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'S%')
        ->get();

        $nav_reports = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'R%')
        ->get();

        $nav_transactions = Navigation::orderBy('ID', 'asc')
        ->where('navigation_code', 'like', 'T%')
        ->get();

        $emp = EmployeeSync::get();

        $nav_log = DB::connection("ympimis_2")->select("SELECT
            a.created_by,
            a.created_at,
            SUM( a.afters ) AS afters,
            suM( a.befores ) AS befores 
        FROM
            (
            SELECT
                created_by,
                count( navigation_code ) AS afters,
                0 AS befores,
                created_at 
            FROM
                `permission_logs` 
            WHERE
                role_code = '".$role->role_code."' 
                AND `condition` = 'after' 
            GROUP BY
                created_at,
                created_by UNION ALL
            SELECT
                created_by,
                0 AS afters,
                count( navigation_code ) AS befores,
                created_at 
            FROM
                `permission_logs` 
            WHERE
                role_code = '".$role->role_code."' 
                AND `condition` = 'before' 
            GROUP BY
                created_at,
                created_by 
            ) a 
        GROUP BY
            a.created_at,
            a.created_by");

        $nav_log_all = DB::connection('ympimis_2')->select("SELECT
            * 
        FROM
            permission_logs 
        WHERE
            role_code = '".$role->role_code."'");

        $permissions = Permission::where('role_code', '=', $role->role_code)->orderBy('id', 'asc')->get();

        $perm[] = '';
        foreach ($permissions as $permission) {
            $perm[] = $permission->navigation_code;
        }

        return view('roles.edit', array(
            'role' => $role,
            'nav_admins' => $nav_admins,
            'nav_masters' => $nav_masters,
            'nav_services' => $nav_services,
            'nav_reports' => $nav_reports,
            'emp' => $emp,
            'nav_log' => $nav_log,
            'nav_log_all' => $nav_log_all,
            'nav_transactions' => $nav_transactions,
            'permissions' => $perm,
        ))->with('page', 'Role');
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
        try{

            $role = Role::find($id);
            $role->role_name = $request->get('role_name');
            $role->save();

            $created = date('Y-m-d H:i:s');

            $permission_before = Permission::where('role_code', '=', $role->role_code)->get();
            if (count($permission_before) > 0) {
                for ($i=0; $i < count($permission_before); $i++) { 
                    $input_before = DB::connection('ympimis_2')->table('permission_logs')->insert([
                        'role_code' => $role->role_code,
                        'navigation_code' => $permission_before[$i]->navigation_code,
                        'condition' => 'before',
                        'created_by' => Auth::user()->username,
                        'created_at' => $created,
                        'updated_at' => $created,
                    ]);
                }
            }

            $permissions = Permission::where('role_code', '=', $role->role_code)
            ->where('navigation_code', '<>', 'Dashboard');
            $permissions->forceDelete();

            $navigations = $request->get('navigation_code');

            $permission = '';

            $input_after = DB::connection('ympimis_2')->table('permission_logs')->insert([
                'role_code' => $role->role_code,
                'navigation_code' => 'Dashboard',
                'condition' => 'after',
                'created_by' => Auth::user()->username,
                'created_at' => $created,
                'updated_at' => $created,
            ]);

            if($navigations != ''){
                foreach ($navigations as $navigation) {
                    $permission = new Permission([
                        'role_code' => $role->role_code,
                        'navigation_code' => $navigation,
                        'created_by' => $id
                    ]);
                    $permission->save();

                    $input_after = DB::connection('ympimis_2')->table('permission_logs')->insert([
                        'role_code' => $role->role_code,
                        'navigation_code' => $navigation,
                        'condition' => 'after',
                        'created_by' => Auth::user()->username,
                        'created_at' => $created,
                        'updated_at' => $created,
                    ]);
                }
            }

            return redirect('/index/role')->with('status', 'Role data has been edited.')->with('page', 'Role');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Role name already exist.')->with('page', 'Role');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Role');
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
        $role = Role::find($id);

        $permissions = Permission::where('role_code', '=', $role->role_code);

        $permissions->forceDelete();
        $role->forceDelete();

        return redirect('/index/role')->with('status', 'Role has been deleted.')->with('page', 'Role');
        //
    }
}