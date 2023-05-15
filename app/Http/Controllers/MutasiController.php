<?php

namespace App\Http\Controllers;

use App\Approver;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Mutasi;
use App\MutasiAnt;
use App\User;
use DataTables;
use Excel;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;
use Yajra\DataTables\Exception;

class MutasiController extends Controller
{
   public function __construct()
   {
      $this->middleware('auth');
  }

  public function dashboard(Request $request)
  {

      $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
      ->select('employee_id', 'department')
      ->first();

      $departement2 = 'Woodwind Instrument - Key Parts Process (WI-KPP) Department';
      $departement3 = 'Woodwind Instrument - Welding Process (WI-WP) Department';
      $departement4 = 'Production Engineering Department';

      $department = db::select('SELECT DISTINCT department FROM employee_syncs WHERE department IS NOT NULL ORDER BY department ASC');

      if ($emp_dept->department == 'Management Information System Department' || $emp_dept->department == 'Human Resources Department') {
         $user      = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
         $dept      = db::select('SELECT DISTINCT department FROM employee_syncs');
         $post      = db::select('SELECT DISTINCT position FROM employee_syncs');
         $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs');
         $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs');
         $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs');
     } elseif ($emp_dept->employee_id == 'PI1710002') {
         $user      = db::select('SELECT employee_id,name FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '" and `end_date` is null');
         $dept      = db::select('SELECT DISTINCT department FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '"');
         $post      = db::select('SELECT DISTINCT position FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '"');
         $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '"');
         $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '"');
         $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement2 . '"');
     } elseif ($emp_dept->employee_id == 'PI0804012') {
         $user      = db::select('SELECT employee_id,name FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '" and `end_date` is null');
         $dept      = db::select('SELECT DISTINCT department FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '"');
         $post      = db::select('SELECT DISTINCT position FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '"');
         $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '"');
         $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '"');
         $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement3 . '"');
     } else {
         $user      = db::select('SELECT employee_id,name FROM employee_syncs where department = "' . $emp_dept->department . '" and `end_date` is null');
         $dept      = db::select('SELECT DISTINCT department FROM employee_syncs where department = "' . $emp_dept->department . '"');
         $post      = db::select('SELECT DISTINCT position FROM employee_syncs where department = "' . $emp_dept->department . '"');
         $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs where department = "' . $emp_dept->department . '"');
         $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs where department = "' . $emp_dept->department . '"');
         $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs where department = "' . $emp_dept->department . '"');
     }

  // var_dump($position);

  // $departement = db::select("select DISTINCT department from employee_syncs");

     return view('mutasi.dashboard',
         array(
            'title'      => 'Mutasi Satu Department Monitoring & Control',
            'title_jp'   => 'ミューテーション1部門の監視と制御',

            'emp_dept'   => $emp_dept,
            'dept'       => $dept,
            'department' => $department,
            'post'       => $post,
            'group'      => $group,
            'section'    => $section,
            'sub_group'  => $sub_group,
            'user'       => $user,
        )
     )->with('page', 'Mutasi Satu Departemen');
 }
 public function viewCekEmail()
 {
  $mutasi  = MutasiAnt::find($id);
  $isimail = "select id, departemen from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
  $mutasi  = db::select($isimail);
  return view('mails.mutasi_antar', array(
  ))->with('page', 'Mutasi');
}

public function KirimReport(Request $request)
{
  $id      = $request->get('id');
  $mutasi  = MutasiAnt::find($id);
  $mailtoo = 'yayuk.wahyuni@music.yamaha.com'; // diisi custom
  $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
  $mutasi  = db::select($isimail);

  Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));
  return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function KirimUlangApproval(Request $request)
{
  $id      = $request->get('id');
  $mutasi  = MutasiAnt::find($id);
  $mailtoo = 'arief.soekamto@music.yamaha.com'; // diisi custom
  $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
  $mutasi  = db::select($isimail);

  Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
  return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function dashboardAnt()
{
  // return view('mutasi.dashboard_ant', array(
  // ))->with('page', 'Mutasi');

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department')
  ->first();

  $departement1 = 'Production Engineering Department';
  $department   = db::select('SELECT DISTINCT department FROM employee_syncs WHERE department IS NOT NULL ORDER BY department ASC');

  if ($emp_dept->department == 'Management Information System Department' || $emp_dept->department == 'Human Resources Department' || $emp_dept->department == '' || $emp_dept->department == null) {
     $dept      = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
     $post      = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
     $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
     $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
     $user      = db::select('SELECT employee_id,name FROM employee_syncs where `end_date` is null');
     $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs ORDER BY sub_group ASC');
 } elseif ($emp_dept->employee_id == 'PI0703002') {
     $dept      = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
     $post      = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
     $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
     $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
     $user      = db::select('SELECT employee_id,name FROM employee_syncs where department = "' . $emp_dept->department . '" or department = "' . $departement1 . '" and `end_date` is null order by name ASC');
     $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs ORDER BY sub_group ASC');
   // $user    = db::select('SELECT employee_id,name FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'" and `end_date` is null order by name ASC');
   // $dept  = db::select('SELECT DISTINCT department FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'"');
   // $post    = db::select('SELECT DISTINCT position FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'"');
   // $section = db::select('SELECT DISTINCT department, section FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'"');
   // $group   = db::select('SELECT DISTINCT section, `group` FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'"');
   // $sub_group   = db::select('SELECT DISTINCT sub_group FROM employee_syncs where department = "'.$emp_dept->department.'" or department = "'.$departement1.'"');
 } else {
     $dept      = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
     $post      = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
     $section   = db::select('SELECT DISTINCT department, section FROM employee_syncs ORDER BY section ASC');
     $group     = db::select('SELECT DISTINCT section, `group` FROM employee_syncs ORDER BY `group` ASC');
     $user      = db::select('SELECT employee_id,name FROM employee_syncs where department = "' . $emp_dept->department . '" and `end_date` is null');
     $sub_group = db::select('SELECT DISTINCT sub_group FROM employee_syncs ORDER BY sub_group ASC');
 }

  // $departement = db::select("select DISTINCT department from employee_syncs");

 return view('mutasi.dashboard_ant',
     array(
        'title'      => 'Mutasi Antar Department Monitoring & Control',
        'title_jp'   => '部門の監視と管理の間の変化',
        'emp_dept'   => $emp_dept,
        'dept'       => $dept,
        'post'       => $post,
        'group'      => $group,
        'section'    => $section,
        'sub_group'  => $sub_group,
        'user'       => $user,
        'department' => $department,
    )
 )->with('page', 'Mutasi Antar Departemen');
}

public function get_employee(Request $request)
{
  try {
     $emp = DB::SELECT("select employee_id, `name`, sub_group, `group`, section, department, position, grade_code from employee_syncs where
        `employee_id` = '" . $request->get('employee_id') . "'
        AND `end_date` IS NULL");

   // $grade = $request->get('jabatan');
   // $jabatan = "where grade_code = '".$grade."' ";

   // $position = db::select("SELECT position FROM jabatan ".$jabatan." ");

     if (count($emp) > 0) {
        $response = array(
           'status'   => true,
           'message'  => 'Success',
           'employee' => $emp,
       );
        return Response::json($response);
    } else {
        $response = array(
           'status'   => false,
           'message'  => 'Failed',
           'employee' => '',
       );
        return Response::json($response);
    }
} catch (\Exception $e) {
 $response = array(
    'status'  => false,
    'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function get_grade(Request $request)
{
  try {
     $grade   = $request->get('jabatan');
     $jabatan = "where grade_code = '" . $grade . "' ";

     dd($grade, $jabatan);

     $position = db::select("SELECT position FROM jabatan " . $jabatan . "");

   // var_dump($positon);

     if (count($grade) > 0) {
        $response = array(
           'status'   => true,
           'message'  => 'Success',
           'position' => $position,
       );
        return Response::json($response);
    } else {
        $response = array(
           'status'  => false,
           'message' => 'failed',
       );
        return Response::json($response);
    }
} catch (\Exception $e) {
 $response = array(
    'status'  => false,
    'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function get_tujuan(Request $request)
{
  try {

     $emp = DB::SELECT("SELECT
        DISTINCT(sub_group),
        `group`,
        section,
        department
        FROM
        employee_syncs
        WHERE
        `sub_group` = '" . $request->get('ke_sub_group') . "' ");

     if (count($emp) > 0) {
        $response = array(
           'status'   => true,
           'message'  => 'Success',
           'employee' => $emp,
       );
        return Response::json($response);
    } else {
        $response = array(
           'status'   => false,
           'message'  => 'Failed',
           'employee' => '',
       );
        return Response::json($response);
    }
} catch (\Exception $e) {
 $response = array(
    'status'  => false,
    'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function get_section(Request $request)
{
  try {
     if ($request->get('ke__seksi') == 'Kosong') {
        $emp = '';
    } else {
        $emp = DB::SELECT("select section, department, position from employee_syncs where
            `section` = '" . $request->get('section') . "'
            AND `end_date` IS NULL");

        if (count($emp) > 0) {
           $response = array(
              'status'   => true,
              'message'  => 'Success',
              'employee' => $emp,
          );
           return Response::json($response);
       } else {
           $response = array(
              'status'   => false,
              'message'  => 'Failed',
              'employee' => '',
          );
           return Response::json($response);
       }
   }
} catch (\Exception $e) {
 $response = array(
    'status'  => false,
    'message' => $e->getMessage(),
);
 return Response::json($response);
}
}

public function get_group(Request $request)
{
  try {
     if ($request->get('ke__group') == 'Kosong') {
        $emp = '';
    } else {
        $emp = DB::SELECT("select section, department, position from employee_syncs where
            `group` = '" . $request->get('group') . "'
            AND `end_date` IS NULL");

        if (count($emp) > 0) {
           $response = array(
              'status'   => true,
              'message'  => 'Success',
              'employee' => $emp,
          );
           return Response::json($response);
       } else {
           $response = array(
              'status'   => false,
              'message'  => 'Failed',
              'employee' => '',
          );
           return Response::json($response);
       }
   }
} catch (\Exception $e) {
 $response = array(
    'status'  => false,
    'message' => $e->getMessage(),
);
 return Response::json($response);
}
}
 //reject
public function rejected(Request $request, $id)
{
  try {
     $mutasi = Mutasi::find($id);

     if ($mutasi->posisi == 'chf_asal') {
        $mutasi->status           = 'Rejected';
        $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
        $mutasi->app_ca           = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'chf_tujuan') {
        $mutasi->status             = 'Rejected';
        $mutasi->date_atasan_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_ct             = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'mgr') {
        $mutasi->status              = 'Rejected';
        $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_mt              = 'Rejected';
        $mutasi->save();
    }

    $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019'
    union all
    select email from mutasi_depts join users on mutasi_depts.chief_or_foreman_asal = users.username where mutasi_depts.id = '" . $mutasi->id . "'";
    $mailtoo = DB::select($mails);

    $isimail = "select id, status, nik, nama, sub_group, `group`, seksi, departemen, ke_seksi, ke_group, ke_sub_group, ke_jabatan, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;
    $mutasi  = db::select($isimail);
    Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'rejected_mutasi'));
    return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}
 //reject antar departemen
public function rejectedAnt(Request $request, $id)
{
  try {
     $mutasi = MutasiAnt::find($id);
     if ($mutasi->posisi == 'chf_asal') {
        $mutasi->status           = 'Rejected';
        $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
        $mutasi->app_ca           = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'mgr_asal') {
        $mutasi->status            = 'Rejected';
        $mutasi->date_manager_asal = date('Y-m-d H-y-s');
        $mutasi->app_ma            = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'dgm_asal') {
        $mutasi->status        = 'Rejected';
        $mutasi->date_dgm_asal = date('Y-m-d H-y-s');
        $mutasi->app_da        = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'gm_asal') {
        $mutasi->status       = 'Rejected';
        $mutasi->date_gm_asal = date('Y-m-d H-y-s');
        $mutasi->app_ga       = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'chf_tujuan') {
        $mutasi->status             = 'Rejected';
        $mutasi->date_atasan_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_ct             = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'mgr_tujuan') {
        $mutasi->status              = 'Rejected';
        $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_mt              = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'dgm_tujuan') {
        $mutasi->status          = 'Rejected';
        $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_dt          = 'Rejected';
        $mutasi->save();
    } elseif ($mutasi->posisi == 'gm_tujuan') {
        $mutasi->status         = 'Rejected';
        $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
        $mutasi->app_gt         = 'Rejected';
        $mutasi->save();
    }

    $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019'
    union all
    select email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_asal = users.username where mutasi_ant_depts.id = '" . $mutasi->id . "'";
    $mailtoo = DB::select($mails);

    $isimail = "select id, status, nik, nama, sub_group, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_departemen, ke_jabatan, alasan, tanggal, tanggal_maksimal, departemen,ke_departemen from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
    $mutasi  = db::select($isimail);
    Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'rejected_mutasi_ant'));
    return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}
 // ====================================================================================================================== SATU DEPARTEMEN
 //tampilan dashboard
public function fetchResumeMutasi(Request $request)
{
  $today  = date('Y-m-d');
  $dateto = $request->get('dateto');
  $tahun  = date('Y');

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();
  $dept = $emp_dept->department;

  $email = Auth::user()->email;
  $dpts  = db::select("SELECT remark FROM send_emails where email = '" . $email . "' and remark like '%Department%'");
  $dpts  = json_decode(json_encode($dpts), true);

  $dpts_st = db::select("SELECT remark FROM send_emails_staff where email = '" . $email . "' and remark like '%Department%'");
  $dpts_st = json_decode(json_encode($dpts_st), true);

  if (Auth::user()->role_code == "S-MIS" || $emp_dept->employee_id == "PI0603019" || $emp_dept->employee_id == "PI1110002" || $emp_dept->employee_id == "PI0811002") {
     if ($dateto == "") {
        $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
           'users.name', 'mutasi_depts.created_by', 'remark')
        ->where('mutasi_depts.deleted_at', null)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y')"), $tahun)
        ->where('mutasi_depts.status', null)
    // ->where('mutasi_depts.status', null)
        ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
        ->orderBy('mutasi_depts.tanggal', 'asc')
        ->get();

    // dd($resumes);
    } else {
        $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
           'users.name', 'mutasi_depts.created_by', 'remark')
        ->where('mutasi_depts.deleted_at', null)
        ->where('mutasi_depts.status', null)
        ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
        ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
        ->orderBy('mutasi_depts.tanggal', 'asc')
        ->get();
    }
} elseif ($emp_dept->position == "Manager") {
 if ($dateto == "") {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->whereIn('mutasi_depts.departemen', $dpts)
    ->where('mutasi_depts.deleted_at', null)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$today)
    ->where('mutasi_depts.status', null)
    // ->where('mutasi_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
} else {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->whereIn('mutasi_depts.departemen', $dpts)
    ->where('mutasi_depts.deleted_at', null)
    ->where('mutasi_depts.status', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
}
} elseif ($emp_dept->employee_id == "PI1710002" || $emp_dept->employee_id == "PI0804012") {
 if ($dateto == "") {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->whereIn('mutasi_depts.departemen', $dpts_st)
    ->where('mutasi_depts.deleted_at', null)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$today)
    ->where('mutasi_depts.status', null)
    // ->where('mutasi_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
} else {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->whereIn('mutasi_depts.departemen', $dpts_st)
    ->where('mutasi_depts.deleted_at', null)
    ->where('mutasi_depts.status', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
}
} else {
 if ($dateto == "") {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi', 'departemen',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->where('mutasi_depts.departemen', $dept)
    // ->whereIn('mutasi_depts.departemen', $dpts)
    ->where('mutasi_depts.deleted_at', null)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$today)
    ->where('mutasi_depts.status', null)
    // ->where('mutasi_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
} else {
    $resumes = Mutasi::select('mutasi_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'posisi', 'departemen',
       'users.name', 'mutasi_depts.created_by', 'remark')
    ->where('mutasi_depts.departemen', $dept)
    // ->where('mutasi_depts.departemen', $emp_dept->department)
    ->where('mutasi_depts.deleted_at', null)
    ->where('mutasi_depts.status', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->leftJoin('users', 'users.id', '=', 'mutasi_depts.created_by')
    ->orderBy('mutasi_depts.tanggal', 'asc')
    ->get();
}
}

$response = array(
 'status'  => true,
 'resumes' => $resumes,
 'dateto'  => $dateto,
);
return Response::json($response);
}
 //tampilan detail
public function showApproval($id)
{

  $mutasi = Mutasi::select('mutasi_tanggal', 'status', 'mutasi_nik', 'mutasi_nama', 'mutasi_bagian', 'mutasi_jabatan1', 'mutasi_rekomendasi', 'mutasi_ke_bagian', 'mutasi_jabatan', 'chief_or_foreman', 'manager', 'gm', 'director', db::raw('chief.name as nama_chief'), db::raw('manager.name as nama_manager'), 'id', db::raw('gm.name as nama_gm'))
  ->leftJoin(db::raw('employee_syncs as chief'), 'mutasi_depts.chief_or_foreman', '=', 'chief.employee_id')
  ->leftJoin(db::raw('employee_syncs as manager'), 'mutasi_depts.manager', '=', 'manager.employee_id')
  ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_depts.gm', '=', 'gm.employee_id')
  ->orderBy('mutasi_depts.created_at', 'desc')
  ->where('mutasi_depts.id', '=', $id)
  ->get();

  return view('mutasi.print', array(
     'mutasi' => $mutasi,
 ))->with('page', 'Mutasi');
}
 //new create mutasi
public function create()
{
  $dept = db::select('SELECT DISTINCT department FROM employee_syncs
    ORDER BY department ASC');

  $sect = db::select('SELECT DISTINCT section FROM employee_syncs
    ORDER BY section ASC');

  $post = db::select('SELECT DISTINCT position FROM employee_syncs
    ORDER BY position ASC');

  $user = db::select('SELECT employee_id,name FROM employee_syncs');

  return view('mutasi.create', array(
     'dept' => $dept,
     'sect' => $sect,
     'post' => $post,
     'user' => $user,

 ))->with('page', 'Mutasi');
}

public function getMutasi(Request $request)
{
  $resumes = Mutasi::select('nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'tanggal', 'alasan')
  ->where('mutasi_depts.id', '=', $request->get('id'))
  ->get();

  $response = array(
     'status'  => true,
     'resumes' => $resumes,
 );
  return Response::json($response);
}

public function store(Request $request)
{
    $chief        = null;
    $nama_chief   = null;
    $posit        = null;
    $manager      = null;
    $nama_manager = null;

    $submission_date = $request->get('submission_date');
    $mutasi_date     = date('Y-m-d', strtotime($submission_date . ' + 7 days'));

    $departemen   = $request->get('department');
    $seksi        = $request->get('section');
    $ke_sub_group = $request->get('ke_sub_group');
    $ke_group     = $request->get('ke_group');
    $ke_seksi     = $request->get('ke_section');
    $position     = $request->get('position1');

    if ($ke_sub_group == 'Kosong' || $ke_sub_group == '') {
        $sub       = ' sub_group is null';
        $sub_group = null;
    } else {
        $sub       = " sub_group = '" . $ke_sub_group . "'";
        $sub_group = $request->get('ke_sub_group');
    }

    if ($ke_group == 'Kosong' || $ke_group == '') {
        $group = ' and `group` is null';
        $grp   = null;
    } else {
        $group = " and `group` = '" . $ke_group . "'";
        $grp   = $request->get('ke_group');
    }

    if ($ke_seksi == 'Kosong' || $ke_seksi == '') {
        $section = ' and section is null';
        $sks     = null;
    } else {
        $section = " and section = '" . $ke_seksi . "'";
        $sks     = $request->get('ke_section');
    }

    if ($departemen == 'Kosong' || $departemen == '') {
        $dept = ' and department is null';
        $dpt  = null;
    } else {
        $dept = " and department = '" . $departemen . "'";
        $dpt  = $request->get('department');
    }

    $post = " and position = '" . $position . "'";

    $poss = "select position_code, division, department, section, `group`, sub_group, position from position_code where " . $sub . " " . $group . " " . $section . " " . $dept . " " . $post . " ";
    $pst  = db::select($poss);
    if (count($pst) > 0) {
        foreach ($pst as $pst) {
            $posit = $pst->position_code;
        }
    }

    $id = Auth::id();

    if ($position == 'Staff' || $position == 'Senior Staff') {
        $chf = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Chief' AND department = '".$departemen."'");
        foreach ($chf as $cf) {
            $chief      = $cf->approver_id;
            $nama_chief = $cf->approver_name;
        }
    } elseif ($position == 'Chief' || $position == 'Foreman' || $position == 'Coordinator') {
        $manager = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Manager' AND department = '".$departemen."'");
        foreach ($manager as $mgr) {
            $manager      = $mgr->approver_id;
            $nama_manager = $mgr->approver_name;
        }
    } else {
        $chf = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Foreman' AND department = '".$departemen."'");
        if ($chf != null) {
            if (count($chf) > 1) {
                $chff = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'foreman' AND section = '".$seksi."'");
                foreach ($chff as $cff) {
                    $chief      = $cff->approver_id;
                    $nama_chief = $cff->approver_name;
                }
            }else{
                foreach ($chf as $cf) {
                    $chief      = $cf->approver_id;
                    $nama_chief = $cf->approver_name;
                }
            }
        }
    }

    try {
        $mutasi = new Mutasi([
            'posisi'                => 'chf_asal',
            'nik'                   => $request->get('employee_id'),
            'nama'                  => $request->get('name'),
            'sub_group'             => $request->get('sub_group'),
            'group'                 => $request->get('group'),
            'seksi'                 => $request->get('section'),
            'departemen'            => $request->get('department'),
            'jabatan'               => $request->get('position'),
            'rekomendasi'           => $request->get('rekom'),
            'ke_sub_group'          => $sub_group,
            'ke_group'              => $grp,
            'ke_seksi'              => $sks,
            'ke_jabatan'            => $position,
            'tanggal'               => $request->get('tanggal'),
            'tanggal_maksimal'      => $mutasi_date,
            'alasan'                => $request->get('alasan'),
            'chief_or_foreman_asal' => $chief,
            'nama_chief_asal'       => $nama_chief,
            'manager_tujuan'        => $manager,
            'nama_manager_tujuan'   => $nama_manager,
            'position_code'         => $posit,
            'created_by'            => $id,
        ]);
        $mutasi->save();
        return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
    } catch (QueryException $e) {
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

 //approval chief asal
public function mutasi_approvalchief_or_foreman_asal(Request $request, $id)
{
    try {
        $mutasi       = Mutasi::find($id);
        $chief        = null;
        $nama_chief   = null;
        $manager      = null;
        $nama_manager = null;
        $dgm          = null;
        $nama_dgm     = null;

        $chf = db::select("SELECT approver_id, approver_name FROM approvers WHERE ( remark = 'chief' OR remark = 'foreman' ) AND department = '".$mutasi->departemen."'");


        if ($chf != null) {
            if (count($chf) > 1) {
                $chff = db::select("SELECT approver_id, approver_name FROM approvers WHERE ( remark = 'chief' OR remark = 'foreman' ) AND section LIKE '%".$mutasi->ke_seksi."%' ");
                foreach ($chff as $cff) {
                    $chief      = $cff->approver_id;
                    $nama_chief = $cff->approver_name;
                }
            }else{
                foreach ($chf as $cf) {
                    $chief      = $cf->approver_id;
                    $nama_chief = $cf->approver_name;
                }
            }
        } else {
            $manager = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Manager' AND department = '".$mutasi->departemen."'");
            foreach ($manager as $mgr) {
                $manager      = $mgr->approver_id;
                $nama_manager = $mgr->approver_name;
            }
        }

        $mutasi->app_ca                  = 'Approved';
        $mutasi->date_atasan_asal        = date('Y-m-d H-y-s');
        $mutasi->posisi                  = 'chf_tujuan';
        $mutasi->chief_or_foreman_tujuan = $chief;
        $mutasi->nama_chief_tujuan       = $nama_chief;
        $mutasi->manager_tujuan          = $manager;
        $mutasi->nama_manager_tujuan     = $nama_manager;
        $mutasi->save();


        if ($mutasi->chief_or_foreman_asal == $mutasi->chief_or_foreman_tujuan) {
            $manager = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Manager' AND department = '".$mutasi->departemen."'");
            foreach ($manager as $mgr) {
                $manager      = $mgr->approver_id;
                $nama_manager = $mgr->approver_name;
            }

            $mutasi->app_ct              = 'Approved';
            $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
            $mutasi->posisi              = 'mgr';
            $mutasi->manager_tujuan      = $manager;
            $mutasi->nama_manager_tujuan = $nama_manager;
            $mutasi->save();

            $mails   = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
            $mailtoo = DB::select($mails);

            $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;
            $mutasi  = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));

            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
        } else {
            if ($mutasi->manager_tujuan != null) {
                $mutasi->posisi = 'mgr';
                $mutasi->save();

                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
            } else {
                $mails = "select distinct email from mutasi_depts join users on mutasi_depts.chief_or_foreman_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
            }

            $mailtoo = DB::select($mails);
            $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;
            $mutasi  = db::select($isimail);
            Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
            return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
        }
    } catch (QueryException $e) {
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

 //approval chief tujuan
public function mutasi_approvalchief_or_foreman_tujuan(Request $request, $id)
{
    try {
        $manager      = null;
        $nama_manager = null;
        $dgm          = null;
        $nama_dgm     = null;

        $mutasi  = Mutasi::find($id);
        $manager = db::select("SELECT approver_id, approver_name FROM approvers WHERE remark = 'Manager' AND department = '".$mutasi->departemen."'");
        foreach ($manager as $mgr) {
            $manager      = $mgr->approver_id;
            $nama_manager = $mgr->approver_name;
        }

        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr';
        $mutasi->manager_tujuan      = $manager;
        $mutasi->nama_manager_tujuan = $nama_manager;
        $mutasi->save();

        $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;

        $mutasi = db::select($isimail);
        Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));

        return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
    } catch (QueryException $e) {
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

 //approval manager
public function mutasi_approvalmanager(Request $request, $id)
{
    try {
        $mutasi = Mutasi::find($id);

        $mutasi->status              = 'All Approved';
        $mutasi->app_mt              = 'Approved';
        $mutasi->posisi              = 'hr';
        $mutasi->manager_hrga        = 'PI0603019';
        $mutasi->nama_manager        = 'Ummi Ernawati';
        $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
        $mutasi->date_manager_hrga   = date('Y-m-d H-y-s');
        $mutasi->save();

        if ($mutasi->status == 'All Approved') {

            $resumes = Mutasi::select(
                'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark',
                'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
                'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
                'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
                'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
                'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
                'manager_hrga', 'nama_manager', 'date_manager_hrga',
                'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
                db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
            ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_depts.nik', '=', 'pegawai.employee_id')
            ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_depts.nik', '=', 'grade.employee_id')
            ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_depts.nik', '=', 'posisi.employee_id')
            ->where('mutasi_depts.id', '=', $id)
            ->get();

            $data = array(
                'resumes' => $resumes,
            );
        }

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;

        $mutasi = db::select($isimail);
        Mail::to('ummi.ernawati@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_satu'));

        return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
    } catch (QueryException $e) {
        return back()->with('error', 'Error')->with('page', 'Mutasi Error');
    }
}

 //approval dgm
public function mutasi_approval_dgm(Request $request, $id)
{
  try {
     $mutasi = Mutasi::find($id);
   // if ($mutasi->dgm_tujuan != null) {
   //     $gm = 'PI1206001';
   //     $nama_gm = 'Yukitaka Hayakawa';
   // }

     $mutasi->app_dt          = 'Approved';
     $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
     $mutasi->posisi          = 'gm';
   // $mutasi->gm_tujuan = $gm;
   // $mutasi->nama_gm_tujuan = $nama_gm;
     $mutasi->save();

     $mails   = "select distinct email from mutasi_depts join users on mutasi_depts.gm_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
     $mailtoo = DB::select($mails);
     $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;
     $mutasi  = db::select($isimail);
     Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
     return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
 } catch (QueryException $e) {
     return back()->with('error', 'Error')->with('page', 'Mutasi Error');
 }
}

 //approval gm
public function mutasi_approval_gm(Request $request, $id)
{
  try {
     $mutasi                 = Mutasi::find($id);
     $mutasi->app_gt         = 'Approved';
     $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');

     $mutasi->posisi            = 'mgr_hrga';
     $mutasi->manager_hrga      = 'PI9707011';
     $mutasi->nama_manager      = 'Prawoto';
     $mutasi->status            = 'All Approved';
     $mutasi->app_m             = 'Approved';
     $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
     $mutasi->save();

   // $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_hrga = users.username where mutasi_depts.id = ".$mutasi->id;
     $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0603019', 'PI0811002', 'PI9707011')";

     $mailtoo = DB::select($mails);
     $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;

   // $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = ".$mutasi->id;
     $mutasi = db::select($isimail);
     Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
     return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
 } catch (QueryException $e) {
     return back()->with('error', 'Error')->with('page', 'Mutasi Error');
 }
}

 //approval manager hrga
public function mutasi_manager_hrga(Request $request, $id)
{
  try {
     $mutasi = Mutasi::find($id);

     $mutasi->status            = 'All Approved';
     $mutasi->app_m             = 'Approved';
     $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
     $mutasi->save();

     if ($mutasi->status == 'All Approved') {

        $resumes = Mutasi::select(
           'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark',

           'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
           'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
           'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
           'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
           'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
           'manager_hrga', 'nama_manager', 'date_manager_hrga',

           'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
           db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
        ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_depts.nik', '=', 'pegawai.employee_id')
        ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_depts.nik', '=', 'grade.employee_id')
        ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_depts.nik', '=', 'posisi.employee_id')
        ->where('mutasi_depts.id', '=', $id)
        ->get();

        $data = array(
           'resumes' => $resumes,
       );

    // dd($data);

    // $nik = $mutasi->nik;
    // $position_code = $mutasi->position_code;
    // $create = $mutasi->created_at;

    //     $upload = create UploadMutasi([
    //     'nik' => $nik,
    //     'position_code' => $position_code,
    //     'created_at' => $create,
    // ]);

    // $upload = UploadMutasi::find($id);

    // $upload->nik = $nik;
    // $upload->position_code = $position_code;
    // $upload->created_at = $create;
    // $upload->save();
    // var_dump($nik);
    // var_dump($position_code);
    // var_dump($create);
    // die();
    }

    $mails   = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019' or employee_id = 'PI0811002'";
    $mailtoo = DB::select($mails);

    $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;

    $mutasi = db::select($isimail);
    Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_satu'));

    return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
   // var_dump($data);
   // die();
} catch (QueryException $e) {
   // dd($e);

 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 // ====================================================================================================================== ANTAR DEPARTEMEN
 //tampilan dashboard
public function fetchResumeMutasiAnt(Request $request)
{
  // $submission_date = $request->get('submission_date');
  $tahun   = date('Y');
  $tanggal = date('Y-m-d');
  $dateto  = $request->get('dateto');

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();

  if (($emp_dept->position == "Chief") || ($emp_dept->position == "Foreman")) {
     if ($emp_dept->employee_id == "PI1110002") {
        $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
           'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
        ->where('mutasi_ant_depts.deleted_at', null)
        ->where(DB::raw("DATE_FORMAT(tanggal, '%Y')"), $tahun)
        ->where('mutasi_ant_depts.status', null)
        ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
        ->orderBy('mutasi_ant_depts.tanggal', 'asc')
        ->get();

        $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
           'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
        ->where('mutasi_ant_depts.deleted_at', null)
        ->where(DB::raw("DATE_FORMAT(tanggal, '%Y')"), $tahun)
        ->where('mutasi_ant_depts.status', '=', 'All Approved')
        ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
        ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
        ->get();
    } else {
        $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
           'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
        ->where(function ($query) {
          $query->where('mutasi_ant_depts.posisi', '=', 'chf_asal')
          ->orWhere('mutasi_ant_depts.posisi', '=', 'chf_tujuan');
      })
        ->where('mutasi_ant_depts.deleted_at', null)
        ->where('mutasi_ant_depts.status', null)
        ->where(function ($query) use ($emp_dept) {
          $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
          ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
      })
        ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
        ->orderBy('mutasi_ant_depts.tanggal', 'asc')
        ->get();

        $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
           'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
        ->where('mutasi_ant_depts.deleted_at', null)
        ->where('mutasi_ant_depts.status', '=', 'All Approved')
        ->where(function ($query) use ($emp_dept) {
          $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
          ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
      })
        ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
        ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
        ->get();
    }
} elseif ($emp_dept->position == "Manager") {
 if ($emp_dept->employee_id == "PI0108010") {

    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    ->where(function ($query) {
      $query->where('mutasi_ant_depts.posisi', '=', 'mgr_asal')
      ->orWhere('mutasi_ant_depts.posisi', '=', 'mgr_tujuan');
  })
    // ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    // ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
    // ->where(function ($query) {
    //     $query->where('mutasi_ant_depts.departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department')
    //     ->orWhere('mutasi_ant_depts.ke_departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department');
    // })
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department')
      ->orWhere('mutasi_ant_depts.ke_departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department');
  })
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department')
    // ->orWhere('mutasi_ant_depts.ke_departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department')
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department')
      ->orWhere('mutasi_ant_depts.ke_departemen', '=', 'Woodwind Instrument - Welding Process (WI-WP) Department');
  })
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
} else {
    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    ->where(function ($query) {
      $query->where('mutasi_ant_depts.posisi', '=', 'mgr_asal')
      ->orWhere('mutasi_ant_depts.posisi', '=', 'mgr_tujuan')
      ->orWhere('mutasi_ant_depts.created_by', Auth::id());
  })
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
  })
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
  })
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
}
} elseif (Auth::user()->role_code == "S-MIS" || $emp_dept->employee_id == "PI0603019" || $emp_dept->employee_id == "PI1110002" || $emp_dept->employee_id == "PI0811002" || ($emp_dept->position == "Deputy General Manager") || ($emp_dept->position == "General Manager") || ($emp_dept->position == "Director")) {
 if ($dateto == "") {
    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y')"), $tahun)
    ->where('mutasi_ant_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y')"), $tahun)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$tanggal)
    // ->where('mutasi_ant_depts.status',null)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
}

   // var_dump($tanggal);
   // die();
else {
    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->where('mutasi_ant_depts.status', null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    // ->where('mutasi_ant_depts.status',null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
}
} else {
 if ($dateto == "") {
    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi', 'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$tanggal)
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
  })
    ->where('mutasi_ant_depts.status', null)
    ->where('mutasi_ant_depts.deleted_at', null)
    // ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    // ->orwhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    // ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->where('mutasi_ant_depts.deleted_at', null)
    // ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m-%d')"),$tanggal)
    // ->where('mutasi_ant_depts.status',null)
    ->where(function ($query) use ($emp_dept) {
      $query->where('mutasi_ant_depts.departemen', $emp_dept->department)
      ->orWhere('mutasi_ant_depts.ke_departemen', $emp_dept->department);
  })
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
}

   // var_dump($tanggal);
   // die();
else {
    $resumes = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    // ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->where('mutasi_ant_depts.status', null)
    ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->orwhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'asc')
    ->get();

    $resumesComplate = MutasiAnt::select('mutasi_ant_depts.id', 'status', 'nik', 'nama', 'nama_chief_asal', 'nama_manager_asal', 'nama_dgm_asal', 'nama_gm_asal', 'nama_chief_tujuan', 'nama_manager_tujuan', 'nama_dgm_tujuan', 'nama_gm_tujuan', 'nama_manager', 'nama_direktur_hr', 'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir', 'posisi',
       'users.name', 'mutasi_ant_depts.created_by', 'remark', 'jabatan')
    // ->where('mutasi_ant_depts.created_by', '=', Auth::user()->id)
    // ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->where('mutasi_ant_depts.deleted_at', null)
    ->where('mutasi_ant_depts.status', '=', 'All Approved')
    ->where(DB::raw("DATE_FORMAT(tanggal, '%Y-%m')"), $dateto)
    ->where('mutasi_ant_depts.departemen', $emp_dept->department)
    ->orwhere('mutasi_ant_depts.ke_departemen', $emp_dept->department)
    // ->where('mutasi_ant_depts.status',null)
    ->leftJoin('users', 'users.id', '=', 'mutasi_ant_depts.created_by')
    ->orderBy('mutasi_ant_depts.tanggal', 'DESC')
    ->get();
}
}

$response = array(
 'status'          => true,
 'resumes'         => $resumes,
 'resumesComplate' => $resumesComplate,
 'emp_dept'        => $emp_dept,
);
return Response::json($response);
}

 //tampilan detail
public function showAntApproval($id)
{
  $mutasi = MutasiAnt::select('status', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'chief_or_foreman_asal', 'date_atasan_asal', 'chief_or_foreman_tujuan', 'date_atasan_tujuan', 'gm_division', 'date_gm', 'manager_hrga', 'date_manager_hrga', 'pres_dir', 'date_pres_dir', 'direktur_hr', 'date_direktur_hr', db::raw('atasan_asal.name as nama_atasan_asal'), db::raw('atasan_tujuan.name as nama_atasan_tujuan'), db::raw('gm.name as nama_gm'), db::raw('manager_hrga.name as nama_manager'), db::raw('pres_dir.name as nama_pres_dir'), db::raw('direktur_hr.name as nama_direktur_hr'))
  ->leftJoin(db::raw('employee_syncs as atasan_asal'), 'mutasi_ant_depts.chief_or_foreman_asal', '=', 'atasan_asal.employee_id')
  ->leftJoin(db::raw('employee_syncs as atasan_tujuan'), 'mutasi_ant_depts.chief_or_foreman_tujuan', '=', 'atasan_tujuan.employee_id')
  ->leftJoin(db::raw('employee_syncs as gm'), 'mutasi_ant_depts.gm_division', '=', 'gm.employee_id')
  ->leftJoin(db::raw('employee_syncs as manager_hrga'), 'mutasi_ant_depts.manager_hrga', '=', 'manager_hrga.employee_id')
  ->leftJoin(db::raw('employee_syncs as pres_dir'), 'mutasi_ant_depts.pres_dir', '=', 'pres_dir.employee_id')
  ->leftJoin(db::raw('employee_syncs as direktur_hr'), 'mutasi_ant_depts.direktur_hr', '=', 'direktur_hr.employee_id')
  ->orderBy('mutasi_ant_depts.created_at', 'desc')
  ->where('mutasi_ant_depts.id', '=', $id)
  ->get();
  return view('mutasi.print_ant', array(
     'mutasi' => $mutasi,
 ))->with('page', 'Mutasi');
}

public function fetchMutasiDetail(Request $request)
{

  $resumes = MutasiAnt::select('id', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan')
  ->where('mutasi_ant_depts.id', '=', $request->get('id'))
  ->get();

  $response = array(
     'status'  => true,
     'resumes' => $resumes,
 );
  return Response::json($response);
}

public function editMutasi(Request $request)
{
  $id = $request->get('id');

  $mutasi               = Mutasi::where('id', $id)->first();
  $mutasi->tanggal      = $request->get('tanggal');
  $mutasi->ke_seksi     = $request->get('ke_seksi');
  $mutasi->ke_group     = $request->get('ke_group');
  $mutasi->ke_sub_group = $request->get('ke_sub_group');
  $mutasi->alasan       = $request->get('alasan');
  $mutasi->save();

  return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function editMutasiAnt(Request $request)
{
  $id = $request->get('id');

  $mutasi                = MutasiAnt::where('id', $id)->first();
  $mutasi->tanggal       = $request->get('tanggal');
  $mutasi->ke_seksi      = $request->get('ke_seksi');
  $mutasi->ke_group      = $request->get('ke_group');
  $mutasi->ke_sub_group  = $request->get('ke_sub_group');
  $mutasi->ke_departemen = $request->get('ke_department_edit');
  $mutasi->alasan        = $request->get('alasan');
  $mutasi->save();

  return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function fetchMutasiSatuDetail(Request $request)
{

  $resumes = Mutasi::select('id', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'tanggal', 'alasan')
  ->where('mutasi_depts.id', '=', $request->get('id'))
  ->get();

  $response = array(
     'status'  => true,
     'resumes' => $resumes,
 );
  return Response::json($response);
}

public function viewMutasiDetail(Request $request)
{
  $tahun    = date('Y');
  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department')
  ->first();

  $bulan  = $request->get('bulan');
  $status = $request->get('status');

  if ($status == "Tidak Disetujui") {
     $status = "status = 'Rejected'";
 } elseif ($status == "Disetujui") {
     $status = "status = 'All Approved'";
 } elseif ($status == "Proses") {
     $status = "status is null";
 }

 $dateto = $request->get('dateto');

 if ($dateto != "") {
     $resumes = db::select("
        SELECT
        id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan
        FROM
        mutasi_ant_depts
        WHERE
        DATE_FORMAT(tanggal, '%Y-%m') = '" . $dateto . "'
        and " . $status . "
        and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
        ORDER BY
        tanggal
        ");
 } else {
     $resumes = db::select("
        SELECT
        id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_departemen, ke_jabatan, tanggal, alasan
        FROM
        mutasi_ant_depts
        WHERE
        DATE_FORMAT(tanggal, '%M') = '" . $bulan . "'
        and " . $status . "
        and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
        ORDER BY
        tanggal
        ");
 }

 $response = array(
     'status'  => true,
     'resumes' => $resumes,
     'dateto'  => $dateto,
     'status'  => $status,
 );
 return Response::json($response);
}

public function viewMutasiSatuDetail(Request $request)
{
  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();
  $dept = $emp_dept->department;

  $email = Auth::user()->email;
  $dpts  = db::select("SELECT remark FROM send_emails where email = '" . $email . "' and remark like '%Department%'");
  $dpts  = json_decode(json_encode($dpts), true);
  $dpts2 = [];

  foreach ($dpts as $dpt) {
     array_push($dpts2, $dpt['remark']);
 }

 $dpts2 = "'" . implode("' , '", $dpts2) . "'";

 $dpts_st  = db::select("SELECT remark FROM send_emails_staff where email = '" . $email . "' and remark like '%Department%'");
 $dpts_st  = json_decode(json_encode($dpts_st), true);
 $dpts_st2 = [];

 foreach ($dpts_st as $dpt_st) {array_push($dpts_st2, $dpt_st['remark']);}
 $dpts_st2 = "'" . implode("' , '", $dpts_st2) . "'";

 $bulan  = $request->get('bulan');
 $status = $request->get('status');

 if ($status == "Tidak Disetujui") {
     $status = "status = 'Rejected'";
 } elseif ($status == "Disetujui") {
     $status = "status = 'All Approved'";
 } elseif ($status == "Proses") {
     $status = "status is null";
 }

 $dateto = $request->get('dateto');
 $tahun  = $request->get('tahun');

 if (Auth::user()->role_code == "S-MIS" || $emp_dept->employee_id == "PI0603019" || $emp_dept->employee_id == "PI1110002") {
     if ($dateto != "") {
        $resumes = db::select("
            SELECT id ,status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
            FROM
            mutasi_depts
            WHERE
            DATE_FORMAT(tanggal, '%Y-%m') = '" . $dateto . "'
            and " . $status . "
            ORDER BY
            tanggal
            ");
    } else {
        $resumes = db::select("
            SELECT
            id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
            FROM
            mutasi_depts
            WHERE
            DATE_FORMAT(tanggal, '%M') = '" . $bulan . "'
            and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
            and " . $status . "
            ORDER BY
            tanggal
            ");
    }
} elseif ($emp_dept->position == "Manager") {
 if ($dateto != "") {
    $resumes = db::select("
        SELECT id ,status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts2 . ")
        AND
        DATE_FORMAT(tanggal, '%Y-%m') = '" . $dateto . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
} else {
    $resumes = db::select("
        SELECT
        id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts2 . ")
        AND
        DATE_FORMAT(tanggal, '%M') = '" . $bulan . "'
        and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
}
} elseif ($emp_dept->employee_id == "PI1710002" || $emp_dept->employee_id == "PI0804012") {
 if ($dateto != "") {
    $resumes = db::select("
        SELECT id ,status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts_st2 . ")
        AND
        DATE_FORMAT(tanggal, '%Y-%m') = '" . $dateto . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
} else {
    $resumes = db::select("
        SELECT
        id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts_st2 . ")
        AND
        DATE_FORMAT(tanggal, '%M') = '" . $bulan . "'
        and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
}
} else {
 if ($dateto != "") {
    $resumes = db::select("
        SELECT id ,status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen = '" . $dept . "'
        AND
        DATE_FORMAT(tanggal, '%Y-%m') = '" . $dateto . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
} else {
    $resumes = db::select("
        SELECT
        id, status, nik, nama, `sub_group`, `group`, seksi, departemen, jabatan, rekomendasi, ke_sub_group, ke_group, ke_seksi, ke_jabatan, tanggal, alasan
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen = '" . $dept . "'
        AND
        DATE_FORMAT(tanggal, '%M') = '" . $bulan . "'
        and DATE_FORMAT(tanggal, '%Y') = '" . $tahun . "'
        and " . $status . "
        ORDER BY
        tanggal
        ");
}
}

$response = array(
 'status'  => true,
 'resumes' => $resumes,
 'status'  => $status,
);
return Response::json($response);
}

 //new create mutasi
public function createAnt()
{
  $dept    = db::select('SELECT DISTINCT department FROM employee_syncs ORDER BY department ASC');
  $post    = db::select('SELECT DISTINCT position FROM employee_syncs ORDER BY position ASC');
  $section = db::select('SELECT DISTINCT section FROM employee_syncs ORDER BY section ASC');
  $group   = db::select('SELECT DISTINCT `group` FROM employee_syncs ORDER BY `group` ASC');
  $user    = db::select('SELECT employee_id,name FROM employee_syncs');
  return view('mutasi.create_ant', array(
     'dept'    => $dept,
     'post'    => $post,
     'group'   => $group,
     'section' => $section,
     'user'    => $user,
 ))->with('page', 'Mutasi');
}

public function storeAnt(Request $request)
{
  $manager      = null;
  $nama_manager = null;
  $chief        = null;
  $nama_chief   = null;
  $posit        = null;
  $dgm          = null;
  $nama_dgm     = null;

  $submission_date = $request->get('submission_date');
  $mutasi_date     = date('Y-m-d', strtotime($submission_date . ' + 7 days'));

  $department_asal = $request->get('department');
  $seksi           = $request->get('section');

  $ke_sub_group = $request->get('ke_sub_group');
  $ke_group     = $request->get('ke_group');
  $ke_seksi     = $request->get('ke_section');
  $departemen   = $request->get('ke_department');
  $position     = $request->get('position1');

  if ($ke_sub_group == 'Kosong' || $ke_sub_group == '') {
     $sub       = ' sub_group is null';
     $sub_group = null;
 } else {
     $sub       = " sub_group = '" . $ke_sub_group . "'";
     $sub_group = $request->get('ke_sub_group');
 }

 if ($ke_group == 'Kosong' || $ke_group == '') {
     $group = ' and `group` is null';
     $grp   = null;
 } else {
     $group = " and `group` = '" . $ke_group . "'";
     $grp   = $request->get('ke_group');
 }

 if ($ke_seksi == 'Kosong' || $ke_seksi == '') {
     $section = ' and section is null';
     $sks     = null;
 } else {
     $section = " and section = '" . $ke_seksi . "'";
     $sks     = $request->get('ke_section');
 }

 if ($departemen == 'Kosong' || $departemen == '') {
     $dept = ' and department is null';
     $dpt  = null;
 } else {
     $dept = " and department = '" . $departemen . "'";
     $dpt  = $request->get('department');
 }

 $post = " and position = '" . $position . "'";

 $poss = "select position_code, division, department, section, `group`, sub_group, position from position_code where " . $sub . " " . $group . " " . $section . " " . $dept . " " . $post . " ";
 $pst  = db::select($poss);
 if (count($pst) > 0) {
     foreach ($pst as $pst) {
        $posit = $pst->position_code;
    }
}

try {
 $id = Auth::id();

 if ($request->get('position1') == 'Chief' || $request->get('position1') == 'Foreman') {
    $mgr = db::select("select employee_id, `name` from employee_syncs where position = 'manager' and department = '" . $department_asal . "'");

    if ($mgr != null) {
       foreach ($mgr as $mg) {
          $manager      = $mg->employee_id;
          $nama_manager = $mg->name;
      }
  } elseif ($request->get('department') == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
   $manager      = 'PI0108010';
   $nama_manager = 'Yudi Abtadipa';
} elseif
($request->get('department') == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
   $manager      = 'PI9906002';
   $nama_manager = 'Khoirul Umam';
} elseif
($request->get('department') == 'Purchasing Control Department') {
   $manager      = 'PI9807014';
   $nama_manager = 'Imron Faizal';
} else {
   $manager  = null;
   $dgm      = 'PI0109004';
   $nama_dgm = 'Budhi Apriyanto';
}
} elseif ($request->get('position1') == 'Manager') {
    if ($department_asal == 'Woodwind Instrument - Assembly (WI-A) Department' ||
       $department_asal == 'Maintenance Department' ||
       $department_asal == 'Production Engineering Department' ||
       $department_asal == 'Woodwind Instrument - Surface Treatment (WI-ST) Department' ||
       $department_asal == 'Quality Assurance Department' ||
       $department_asal == 'Woodwind Instrument - Welding Process (WI-WP) Department' ||
       $department_asal == 'Educational Instrument (EI) Department' ||
       $department_asal == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' ||
       $department_asal == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
       $dgm      = 'PI0109004';
   $nama_dgm = 'Budhi Apriyanto';
} elseif ($mutasi->departemen == 'Logistic Department' ||
   $mutasi->departemen == 'Procurement Department' ||
   $mutasi->departemen == 'Production Control Department' ||
   $mutasi->departemen == 'Purchasing Control Department') {
   $gm      = 'PI0109004';
   $nama_gm = 'Budhi Apriyanto';
}
} else {
    $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and section = '" . $seksi . "'");
    if ($chf != null) {
       foreach ($chf as $cf) {
          $chief      = $cf->employee_id;
          $nama_chief = $cf->name;
      }
  } elseif ($department_asal == 'Educational Instrument (EI) Department') {
   $chief      = 'PI1110001';
   $nama_chief = 'Eko Prasetyo Wicaksono';
} elseif ($department_asal == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
   $chief      = 'PI9903003';
   $nama_chief = 'Slamet Hariadi';
} else {
   if ($request->get('section') == 'Software Section') {
      $chief      = 'PI0103002';
      $nama_chief = 'Agus Yulianto';
  } elseif ($request->get('section') == 'Assembly CL . Tanpo . Case Process Section') {
      $chief      = 'PI9707008';
      $nama_chief = 'Imbang Prasetyo';
  } elseif ($request->get('section') == 'Body Buffing-Barrel Process Section') {
      $chief      = 'PI9707010';
      $nama_chief = 'Mawan Sujianto';
  } else {
      $mgr = db::select("select employee_id, `name` from employee_syncs where position = 'manager' and department = '" . $department_asal . "'");
      if ($mgr != null) {
         foreach ($mgr as $mg) {
            $manager      = $mg->employee_id;
            $nama_manager = $mg->name;
        }
    } elseif ($request->get('department') == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
     $manager      = 'PI0108010';
     $nama_manager = 'Yudi Abtadipa';
 } elseif
 ($request->get('department') == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
     $manager      = 'PI9906002';
     $nama_manager = 'Khoirul Umam';
 } elseif
 ($request->get('department') == 'Purchasing Control Department') {
     $manager      = 'PI9807014';
     $nama_manager = 'Imron Faizal';
 }
}
}
}

$mutasi = new MutasiAnt([
    'posisi'                => 'chf_asal',
    'nik'                   => $request->get('employee_id'),
    'nama'                  => $request->get('name'),
    'sub_group'             => $request->get('sub_group'),
    'group'                 => $request->get('group'),
    'seksi'                 => $request->get('section'),
    'departemen'            => $request->get('department'),
    'jabatan'               => $request->get('position'),
    'rekomendasi'           => $request->get('rekom'),
    'ke_sub_group'          => $request->get('ke_sub_group'),
    'ke_group'              => $request->get('ke_group'),
    'ke_seksi'              => $request->get('ke_section'),
    'ke_departemen'         => $request->get('ke_department'),
    'ke_jabatan'            => $request->get('position1'),
    'tanggal'               => $request->get('tanggal'),
    'tanggal_maksimal'      => $mutasi_date,
    'alasan'                => $request->get('alasan'),
    'chief_or_foreman_asal' => $chief,
    'nama_chief_asal'       => $nama_chief,
    'manager_asal'          => $manager,
    'nama_manager_asal'     => $nama_manager,
    'dgm_asal'              => $dgm,
    'nama_dgm_asal'         => $nama_dgm,
    'position_code'         => $posit,
    'created_by'            => Auth::id(),
]);
$mutasi->save();

   // if ($mutasi->chief_or_foreman_asal != null) {
   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }
   // else if($mutasi->manager_asal != null){
   //     $mutasi->posisi = 'mgr_asal';
   //     $mutasi->save();

   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }
   // else if($mutasi->dgm_asal != null){
   //     $mutasi->posisi = 'dgm_asal';
   //     $mutasi->save();

   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }
   // else{
   //     $mutasi->posisi = 'mgr_asal';
   //     $mutasi->save();

   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }

   // $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
   // $mutasi = db::select($isimail);
   // Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //test approve chief asal
public function cobaApproveChiefAsal(Request $request, $id)
{
  try {
     $mutasi                   = MutasiAnt::find($id);
     $mutasi->app_ca           = 'Approved';
     $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
     $mutasi->posisi           = 'mgr_asal';

     $manager = Approver::where('remark', '=', 'Manager')
     ->where('department', $mutasi->departemen)
     ->first();

     $mutasi->manager_asal      = $manager->approver_id;
     $mutasi->nama_manager_asal = $manager->approver_name;
     $mutasi->save();

     if ($mutasi->manager_asal != null) {
        $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);
    } elseif ($mutasi->posisi == 'chf_tujuan') {
        $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);
    } else {
        $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);
    }
    $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
    $mutasi  = db::select($isimail);
    Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));

    return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

public function ApprovalMessage()
{
  $title    = 'Mutasi Antar Departemen';
  $title_jp = '???';
  $h1       = 'Approval Mutasi Antar Departemen';
  $note     = 'Berhasil Disetujui';

  return view('mutasi/verifikasi/approval_message', array(
     'title'    => $title,
     'title_jp' => $title_jp,
     'h1'       => $h1,
     'note'     => $note,
 ));
}

 // public function CobaApproveManagerAsal(Request $request, $id){
 //     try{
 //         $mutasi = MutasiAnt::find($id);

 //         $a = Approver::where('department', $mutasi->ke_departemen)->where('remark', '=', 'Chief')->first();
 //         $dgm = Approver::where('department', $mutasi->departemen)->where('remark', '=', 'Deputy General Manager')->first();
 //         $gm = Approver::where('department', $mutasi->departemen)->where('remark', '=', 'General Manager')->first();

 //         if ($mutasi->departemen == 'Management Information System Department') {
 //             $mutasi->app_ma = 'Approved';
 //             $mutasi->app_da = 'Approved';
 //             $mutasi->app_ga = 'Approved';
 //             $mutasi->nama_dgm_asal = 'Budhi Apriyanto';
 //             $mutasi->nama_gm_asal = 'Yukitaka Hayakawa';
 //             $mutasi->date_manager_asal = date('Y-m-d H-y-s');
 //             $mutasi->posisi = 'chf_tujuan';
 //             $mutasi->chief_or_foreman_tujuan = $a->approver_id;
 //             $mutasi->nama_chief_tujuan = $a->approver_name;
 //             $mutasi->save();
 //         }
 //         else if ($mutasi->departemen == 'General Affairs Department' && $mutasi->ke_departemen == 'Human Resources Department') {
 //             var_dump('Pak Arief');
 //             die();
 //         }
 //         else{
 //             if ($dgm == null) {
 //                 $mutasi->app_ma = 'Approved';
 //                 $mutasi->date_manager_asal = date('Y-m-d H-y-s');
 //                 $mutasi->posisi = 'gm_asal';
 //                 $mutasi->gm_asal = $gm->approver_id;
 //                 $mutasi->nama_gm_asal = $gm->approver_name;
 //                 $mutasi->save();
 //             }else{
 //                 $mutasi->app_ma = 'Approved';
 //                 $mutasi->date_manager_asal = date('Y-m-d H-y-s');
 //                 $mutasi->posisi = 'dgm_asal';
 //                 $mutasi->dgm_asal = $dgm->approver_id;
 //                 $mutasi->nama_dgm_asal = $dgm->approver_name;
 //                 $mutasi->save();
 //             }
 //         }
 //         return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
 //     }catch (QueryException $e){
 //         return back()->with('error', 'Error')->with('page', 'Mutasi Error');
 //     }
 // }

 //approval chief or foreman asal
public function mutasi_approvalchief_or_foremanAsal(Request $request, $id)
{
  try {
     $chief        = null;
     $nama_chief   = null;
     $manager      = null;
     $nama_manager = null;
     $dgm          = null;
     $nama_dgm     = null;

     $mutasi = MutasiAnt::find($id);

     if ($mutasi->departemen == 'General Affairs Department' && $mutasi->ke_departemen == 'Human Resources Department') {
        $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '" . $mutasi->ke_departemen . "'");
        if ($chf != null) {
           foreach ($chf as $cf) {
              $chief      = $cf->employee_id;
              $nama_chief = $cf->name;
          }
      }
      $mutasi->app_ca                  = 'Approved';
      $mutasi->date_atasan_asal        = date('Y-m-d H-y-s');
      $mutasi->posisi                  = 'chf_tujuan';
      $mutasi->chief_or_foreman_tujuan = $chief;
      $mutasi->nama_chief_tujuan       = $nama_chief;
      $mutasi->save();
  } elseif ($mutasi->departemen == 'Human Resources Department' && $mutasi->ke_departemen == 'General Affairs Department') {
    $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '" . $mutasi->ke_departemen . "'");
    if ($chf != null) {
       foreach ($chf as $cf) {
          $chief      = $cf->employee_id;
          $nama_chief = $cf->name;
      }
  }
  $mutasi->app_ca                  = 'Approved';
  $mutasi->date_atasan_asal        = date('Y-m-d H-y-s');
  $mutasi->posisi                  = 'chf_tujuan';
  $mutasi->chief_or_foreman_tujuan = $chief;
  $mutasi->nama_chief_tujuan       = $nama_chief;
  $mutasi->save();
} else {
    // if ($mutasi->manager_asal == null)
    // {
    // $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->departemen."'");
    // $manager = db::select('select approver_id, approver_name, position, remark from approvers where remark = "Manager" and department = "'.$mutasi->departemen.'"');
    $manager = db::select('select approver_id, approver_name from approvers where department = "' . $mutasi->departemen . '" and remark = "Manager"');

    foreach ($manager as $mgr) {
       $manager      = $mgr->approver_id;
       $nama_manager = $mgr->approver_name;
   }

    // var_dump($manager);
    // var_dump($nama_manager);
    // die();

   $mutasi->app_ca            = 'Approved';
   $mutasi->date_atasan_asal  = date('Y-m-d H-y-s');
   $mutasi->posisi            = 'mgr_asal';
   $mutasi->manager_asal      = $manager;
   $mutasi->nama_manager_asal = $nama_manager;
   $mutasi->save();
}
   // else
   // {
   // if ($mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department') {
   //     $manager = 'PI0108010';
   //     $nama_manager = 'Yudi Abtadipa';
   // }
   // elseif
   //     ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
   //     $manager = 'PI9906002';
   //     $nama_manager = 'Khoirul Umam';
   // }
   // elseif
   //     ($mutasi->departemen == 'Purchasing Control Department') {
   //     $manager = 'PI9807014';
   //     $nama_manager = 'Imron Faizal';
   // }
   // elseif
   //     ($mutasi->departemen == 'General Affairs Department') {
   //     $manager = 'PI9707011';
   //     $nama_manager = 'Prawoto';
   // }
   // elseif
   //     ($mutasi->departemen == 'Production Engineering Department') {
   //     $manager = 'PI0703002';
   //     $nama_manager = 'Susilo Basri Prasetyo';
   // }
   // elseif
   //     ($mutasi->departemen == 'Woodwind Instrument - Assembly (WI-A) Department') {
   //     $manager = 'PI9707006';
   //     $nama_manager = 'Fattatul Mufidah';
   // }
   // $manager = null;
   // $dgm = 'PI0109004';
   // $nama_dgm = 'Budhi Apriyanto';
   // $mutasi->posisi = 'dgm_asal';
   // $mutasi->dgm_asal = $dgm;
   // $mutasi->nama_dgm_asal = $nama_dgm;
   // $mutasi->save();
   // }

   // if ($mutasi->manager_asal == null) {
   //     $manager = null;
   //     $dgm = 'PI0109004';
   //     $nama_dgm = 'Budhi Apriyanto';
   //     $mutasi->posisi = 'dgm_asal';
   //     $mutasi->dgm_asal = $dgm;
   //     $mutasi->nama_dgm_asal = $nama_dgm;
   // }else{
   //     $mutasi->posisi = 'mgr_asal';
   //     $mutasi->manager_asal = $manager;
   //     $mutasi->nama_manager_asal = $nama_manager;
   // }
   // $mutasi->app_ca = 'Approved';
   // $mutasi->date_atasan_asal = date('Y-m-d H-y-s');
   // $mutasi->posisi = 'mgr_asal';
   // $mutasi->manager_asal = $manager;
   // $mutasi->nama_manager_asal = $nama_manager;
   // $mutasi->save();
   // }

   // if ($mutasi->manager_asal == null) {
   //     $manager = null;
   //     $dgm = 'PI0109004';
   //     $nama_dgm = 'Budhi Apriyanto';
   //     $mutasi->posisi = 'dgm_asal';
   //     $mutasi->dgm_asal = $dgm;
   //     $mutasi->nama_dgm_asal = $nama_dgm;
   //     $mutasi->save();
   // }

if ($mutasi->manager_asal != null) {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} elseif ($mutasi->posisi == 'chf_tujuan') {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} else {
    $manager               = null;
    $dgm                   = 'PI0109004';
    $nama_dgm              = 'Budhi Apriyanto';
    $mutasi->posisi        = 'dgm_asal';
    $mutasi->dgm_asal      = $dgm;
    $mutasi->nama_dgm_asal = $nama_dgm;
    $mutasi->save();

    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
}
$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval manager asal
public function mutasi_approval_managerAsal(Request $request, $id)
{
  try {
     $dgm      = null;
     $nama_dgm = null;
     $gm       = null;
     $nama_gm  = null;

     $mutasi = MutasiAnt::find($id);
     if ($mutasi->dgm_asal == null) {
        if ($mutasi->departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
           $mutasi->departemen == 'Maintenance Department' ||
           $mutasi->departemen == 'Production Engineering Department' ||
           $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department' ||
           $mutasi->departemen == 'Quality Assurance Department' ||
           $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department' ||
           $mutasi->departemen == 'Educational Instrument (EI) Department' ||
           $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' ||
           $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department' ||
           $mutasi->departemen == 'General Process Control Department' ||
           $mutasi->departemen == 'Management Information System Department') {

           $dgm      = 'PI0109004';
       $nama_dgm = 'Budhi Apriyanto';
   } elseif ($mutasi->departemen == 'Logistic Department' ||
       $mutasi->departemen == 'Procurement Department' ||
       $mutasi->departemen == 'Production Control Department' ||
       $mutasi->departemen == 'Purchasing Control Department') {

       $dgm      = 'PI9905001';
       $nama_dgm = 'Mei Rahayu';
   } elseif ($mutasi->departemen == 'Human Resources Department' ||
       $mutasi->departemen == 'General Affairs Department') {

       $gm      = 'PI9709001';
       $nama_gm = 'Arief Soekamto';
   }

   $mutasi->app_ma            = 'Approved';
   $mutasi->date_manager_asal = date('Y-m-d H-y-s');
   $mutasi->posisi            = 'dgm_asal';
   $mutasi->dgm_asal          = $dgm;
   $mutasi->nama_dgm_asal     = $nama_dgm;
   $mutasi->gm_asal           = $gm;
   $mutasi->nama_gm_asal      = $nama_gm;
   $mutasi->save();

    // if ($mutasi->departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
    //     $mutasi->departemen == 'Maintenance Department'||
    //     $mutasi->departemen == 'Production Engineering Department'||
    //     $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
    //     $mutasi->departemen == 'Quality Assurance Department'||
    //     $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
    //     $mutasi->departemen == 'Educational Instrument (EI) Department'||
    //     $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
    //     $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department' ||
    //     $mutasi->departemen == 'General Process Control Department'||
    //     $mutasi->departemen == 'Management Information System Department') {

    //     $next = db::select('select approver_id, approver_name from approvers where remark in ("Chief", "Foreman") and section like "%'.$mutasi->ke_seksi.'%"');

    //     $mutasi->chief_or_foreman_tujuan = $next[0]->approver_id;
    //     $mutasi->nama_chief_tujuan = $next[0]->approver_name;
    //     $mutasi->app_da = 'Approved';
    //     $mutasi->date_dgm_asal = date('Y-m-d H-y-s');
    //     $mutasi->posisi = 'chf_tujuan';
    //     $mutasi->save();

    // }

}

if ($mutasi->dgm_asal != null) {
    if ($mutasi->posisi == 'chf_tujuan') {
       $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
       $mailtoo = DB::select($mails);
   } elseif ($mutasi->posisi == 'mgr_tujuan') {
       $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
       $mailtoo = DB::select($mails);
   } else {
       $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
       $mailtoo = DB::select($mails);
   }
} else {
    $mutasi->posisi = 'gm_asal';
    $mutasi->save();

    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
}

$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval dgm asal
public function mutasi_approval_dgmAsal(Request $request, $id)
{
  try {
     $chief           = null;
     $nama_chief      = null;
     $manager         = null;
     $nama_manager    = null;
     $dgm_tujuan      = null;
     $nama_dgm_tujuan = null;

     $mutasi = MutasiAnt::find($id);

   // $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and section = '".$mutasi->ke_seksi."'");
     $chf = db::select("select approver_id, approver_name from approvers where department = '" . $mutasi->ke_departemen . "' and position = 'Foreman'");

     if ($chf != null) {
        foreach ($chf as $cf) {
           $chief      = $cf->approver_id;
           $nama_chief = $cf->approver_name;
       }
   } elseif ($chf == null) {
    if ($mutasi->ke_seksi == 'Software Section') {
       $chief      = 'PI0103002';
       $nama_chief = 'Agus Yulianto';
   } elseif ($mutasi->ke_seksi == 'Press and Sanding Process Section') {
       $chief      = 'PI9903003';
       $nama_chief = 'Slamet Hariadi';
   } elseif ($mutasi->ke_seksi == 'Assembly CL . Tanpo . Case Process Section') {
       $chief      = 'PI9707008';
       $nama_chief = 'Imbang Prasetyo';
   } elseif ($mutasi->ke_seksi == 'Recorder Process Section' || $mutasi->ke_seksi == 'Pianica Process Section') {
       $chief      = 'PI1110001';
       $nama_chief = 'Eko Prasetyo Wicaksono';
   } elseif ($mutasi->ke_seksi == 'Koshuha Solder Process Section' || $mutasi->ke_seksi == 'Welding Process Control Section') {
       $chief      = 'PI9809008';
       $nama_chief = 'Mey Indah Astuti';
   } elseif ($mutasi->ke_seksi == 'General Process Control Section') {
       $chief      = 'PI0004003';
       $nama_chief = 'Hadi Firmansyah';
   } else {
       $chief = null;
   }
}

$manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='" . $mutasi->ke_departemen . "'");

if ($manager != null) {
    foreach ($manager as $mgr) {
       $manager      = $mgr->employee_id;
       $nama_manager = $mgr->name;
   }
} elseif ($manager == null) {
    if ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
       $manager      = 'PI0108010';
       $nama_manager = 'Yudi Abtadipa';
   } elseif ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
       $manager      = 'PI9906002';
       $nama_manager = 'Khoirul Umam';
   } elseif ($mutasi->departemen == 'Purchasing Control Department') {
       $manager      = 'PI9807014';
       $nama_manager = 'Imron Faizal';
   } elseif ($mutasi->departemen == 'General Process Control Department') {
       $manager      = 'PI0109004';
       $nama_manager = 'Budhi Apriyanto';
   }
}

if ($mutasi->created_by == '20' && $mutasi->jabatan == 'Manager') {
    $mutasi->posisi = 'dir_hr';

    $mutasi->app_da = 'Approved';
    // $mutasi->app_ga = 'Approved';
    $mutasi->app_dt = 'Approved';
    // $mutasi->app_gt = 'Approved';
    $mutasi->app_m = 'Approved';

    // $mutasi->nama_gm_asal = 'Yukitaka Hayakawa';
    $mutasi->nama_dgm_tujuan = 'Budhi Apriyanto';
    // $mutasi->nama_gm_tujuan = 'Yukitaka Hayakawa';
    // $mutasi->nama_gm_tujuan = 'Yukitaka Hayakawa';
    $mutasi->nama_manager = 'Prawoto';

    // $mutasi->date_gm_asal = date('Y-m-d H-y-s');
    $mutasi->date_dgm_asal   = date('Y-m-d H-y-s');
    $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
    // $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
    $mutasi->date_manager_hrga = date('Y-m-d H-y-s');

    // $mutasi->save();

    // $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;

    // $mutasi = db::select($isimail);
    // Mail::to(['arief.soekamto@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    // Mail::to(['prawoto@music.yamaha.com', 'hiromichi.ichimura@music.yamaha.com', 'lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com', 'yukitaka.hayakawa@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_mgr_hrga'));

} else {
    $mutasi->app_da        = 'Approved';
    $mutasi->date_dgm_asal = date('Y-m-d H-y-s');
    if ($mutasi->departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
       $mutasi->departemen == 'Maintenance Department' ||
       $mutasi->departemen == 'Production Engineering Department' ||
       $mutasi->departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department' ||
       $mutasi->departemen == 'Quality Assurance Department' ||
       $mutasi->departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department' ||
       $mutasi->departemen == 'Educational Instrument (EI) Department' ||
       $mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' ||
       $mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department' ||
       $mutasi->departemen == 'General Process Control Department') {
     // $mutasi->posisi = 'chf_tujuan';

       if ($chief != null) {
          $mutasi->posisi                  = 'chf_tujuan';
          $mutasi->chief_or_foreman_tujuan = $chief;
          $mutasi->nama_chief_tujuan       = $nama_chief;
      } else {
          $mutasi->posisi              = 'mgr_tujuan';
          $mutasi->manager_tujuan      = $manager;
          $mutasi->nama_manager_tujuan = $nama_manager;
      }
  } elseif ($mutasi->departemen == 'Logistic Department' ||
   $mutasi->departemen == 'Procurement Department' ||
   $mutasi->departemen == 'Production Control Department' ||
   $mutasi->departemen == 'Purchasing Control Department') {

   $mutasi->nama_gm_asal = 'Budhi Apriyanto';
   $mutasi->gm_asal      = 'PI0109004';
   $mutasi->posisi       = 'gm_asal';
}

$mutasi->save();

if ($mutasi->chief_or_foreman_tujuan != null) {
   $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
   $mailtoo = DB::select($mails);
} elseif ($mutasi->gm_asal != null) {
   $mailtoo = 'budhi.apriyanto@music.yamaha.com';
} else {
   $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
   $mailtoo = DB::select($mails);
}

$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
}
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval gm asal
public function mutasi_approval_gmAsal(Request $request, $id)
{
  try {
     $chief           = null;
     $nama_chief      = null;
     $manager         = null;
     $nama_manager    = null;
     $dgm_tujuan      = null;
     $nama_dgm_tujuan = null;

     $mutasi = MutasiAnt::find($id);

     $department_tujuan = $mutasi->ke_departemen;
     $jabatan           = $mutasi->ke_jabatan;

     if ($jabatan == 'Chief' || $jabatan == 'Foreman') {
        $mgr = db::select("select employee_id, `name` from employee_syncs where position = 'manager' and department = '" . $department_tujuan . "'");

        if ($mgr != null) {
           foreach ($mgr as $mg) {
              $manager      = $mg->employee_id;
              $nama_manager = $mg->name;
          }
      }
      if ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
       $manager      = 'PI0108010';
       $nama_manager = 'Yudi Abtadipa';
   } elseif
   ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
       $manager      = 'PI9906002';
       $nama_manager = 'Khoirul Umam';
   } elseif
   ($mutasi->departemen == 'Purchasing Control Department') {
       $manager      = 'PI9807014';
       $nama_manager = 'Imron Faizal';
   } else {
       $manager         = null;
       $dgm_tujuan      = 'PI0109004';
       $nama_dgm_tujuan = 'Budhi Apriyanto';
   }
} elseif ($mutasi->ke_jabatan == 'Manager') {
    if ($mutasi->ke_departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
       $mutasi->ke_departemen == 'Maintenance Department' ||
       $mutasi->ke_departemen == 'Production Engineering Department' ||
       $mutasi->ke_departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department' ||
       $mutasi->ke_departemen == 'Quality Assurance Department' ||
       $mutasi->ke_departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department' ||
       $mutasi->ke_departemen == 'Educational Instrument (EI) Department' ||
       $mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department' ||
       $mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
       $dgm_tujuan      = 'PI0109004';
   $nama_dgm_tujuan = 'Budhi Apriyanto';
} elseif ($mutasi->departemen == 'Logistic Department' ||
   $mutasi->departemen == 'Procurement Department' ||
   $mutasi->departemen == 'Production Control Department' ||
   $mutasi->departemen == 'Purchasing Control Department') {
   $gm_tujuan      = 'PI0109004';
   $nama_gm_tujuan = 'Budhi Apriyanto';
}
} else {
    if ($mutasi->ke_jabatan == 'Staff' || $mutasi->ke_jabatan == 'Senior Staff') {
       $chf = db::select("select employee_id, `name` from employee_syncs where position = 'chief' and department = '" . $mutasi->ke_departemen . "'");
       if ($chf != null) {
          foreach ($chf as $cf) {
             $chief      = $cf->employee_id;
             $nama_chief = $cf->name;
         }
     } elseif ($chf != null) {
      if ($request->get('section') == 'Software Section') {
         $chief      = 'PI0103002';
         $nama_chief = 'Agus Yulianto';
     } else {
         $chief = null;
     }
 } elseif ($mutasi->chief_or_foreman_tujuan == null) {
  $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='" . $mutasi->ke_departemen . "'");
  if ($manager != null) {
     foreach ($manager as $mgr) {
        $manager      = $mgr->employee_id;
        $nama_manager = $mgr->name;
    }
} elseif ($manager == null) {
 if ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
    $manager      = 'PI0108010';
    $nama_manager = 'Yudi Abtadipa';
} elseif
($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
    $manager      = 'PI9906002';
    $nama_manager = 'Khoirul Umam';
} elseif
($mutasi->departemen == 'Purchasing Control Department') {
    $manager      = 'PI9807014';
    $nama_manager = 'Imron Faizal';
}
}
}
} else {
   $chf = db::select("select employee_id, `name` from employee_syncs where position = 'foreman' and department = '" . $mutasi->ke_departemen . "'");
   if ($chf != null) {
      foreach ($chf as $cf) {
         $chief      = $cf->employee_id;
         $nama_chief = $cf->name;
     }
 } elseif ($chf != null) {
  if ($request->get('section') == 'Software Section') {
     $chief      = 'PI0103002';
     $nama_chief = 'Agus Yulianto';
 } else {
     $chief = null;
 }
} elseif ($mutasi->chief_or_foreman_tujuan == null) {
  $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='" . $mutasi->ke_departemen . "'");
  if ($manager != null) {
     foreach ($manager as $mgr) {
        $manager      = $mgr->employee_id;
        $nama_manager = $mgr->name;
    }
} elseif ($manager == null) {
 if ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
    $manager      = 'PI0108010';
    $nama_manager = 'Yudi Abtadipa';
} elseif
($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
    $manager      = 'PI9906002';
    $nama_manager = 'Khoirul Umam';
} elseif
($mutasi->departemen == 'Purchasing Control Department') {
    $manager      = 'PI9807014';
    $nama_manager = 'Imron Faizal';
}
}
}
}
}

$mutasi->app_ga                  = 'Approved';
$mutasi->date_gm_asal            = date('Y-m-d H-y-s');
$mutasi->posisi                  = 'chf_tujuan';
$mutasi->chief_or_foreman_tujuan = $chief;
$mutasi->nama_chief_tujuan       = $nama_chief;
$mutasi->manager_tujuan          = $manager;
$mutasi->nama_manager_tujuan     = $nama_manager;
$mutasi->dgm_tujuan              = $dgm_tujuan;
$mutasi->nama_dgm_tujuan         = $nama_dgm_tujuan;
$mutasi->save();

if ($mutasi->chief_or_foreman_tujuan != null) {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} else {
    $mutasi->posisi = 'mgr_tujuan';
    $mutasi->save();
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
}
$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval chief or foreman tujuan
public function mutasi_approvalchief_or_foremanTujuan(Request $request, $id)
{
  try {
     $manager      = null;
     $nama_manager = null;
     $dgm          = null;
     $nama_dgm     = null;

     $mutasi = MutasiAnt::find($id);

     if ($mutasi->departemen == 'General Affairs Department' && $mutasi->ke_departemen == 'Human Resources Department') {
    // $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '".$mutasi->ke_departemen."'");
    // if ($chf != null)
    // {
    //     foreach ($chf as $cf)
    //     {
    //         $chief = $cf->employee_id;
    //         $nama_chief = $cf->name;
    //     }
    // }
        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr_tujuan';
        $mutasi->manager_tujuan      = 'PI9707011';
        $mutasi->nama_manager_tujuan = 'Prawoto';
        $mutasi->save();
    } elseif ($mutasi->departemen == 'Human Resources Department' && $mutasi->ke_departemen == 'General Affairs Department') {
    // $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '".$mutasi->ke_departemen."'");
    // if ($chf != null)
    // {
    //     foreach ($chf as $cf)
    //     {
    //         $chief = $cf->employee_id;
    //         $nama_chief = $cf->name;
    //     }
    // }
        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr_tujuan';
        $mutasi->manager_tujuan      = 'PI9707011';
        $mutasi->nama_manager_tujuan = 'Prawoto';
        $mutasi->save();
    } elseif ($mutasi->ke_departemen == 'General Affairs Department') {
    // $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '".$mutasi->ke_departemen."'");
    // if ($chf != null)
    // {
    //     foreach ($chf as $cf)
    //     {
    //         $chief = $cf->employee_id;
    //         $nama_chief = $cf->name;
    //     }
    // }
        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr_tujuan';
        $mutasi->manager_tujuan      = 'PI9707011';
        $mutasi->nama_manager_tujuan = 'Prawoto';
        $mutasi->save();
    } elseif ($mutasi->ke_departemen == 'Human Resources Department') {
    // $chf = db::select("select employee_id, `name` from employee_syncs where position = 'Chief' and department = '".$mutasi->ke_departemen."'");
    // if ($chf != null)
    // {
    //     foreach ($chf as $cf)
    //     {
    //         $chief = $cf->employee_id;
    //         $nama_chief = $cf->name;
    //     }
    // }
        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr_tujuan';
        $mutasi->manager_tujuan      = 'PI9707011';
        $mutasi->nama_manager_tujuan = 'Prawoto';
        $mutasi->save();
    } else {
        $manager      = '';
        $nama_manager = '';

    // $mn = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->ke_departemen."'");
        $mn = db::select("select approver_id, approver_name from approvers where remark = 'Manager' and department = '" . $mutasi->ke_departemen . "'");

    // foreach ($mn as $mgr)
    // {
        $manager      = $mn[0]->approver_id;
        $nama_manager = $mn[0]->approver_name;
    // }

    // if ($manager != null) {
    //     $manager = $manager;
    //     $nama_manager = $nama_manager;
    // }else{
    //     if ($mutasi->ke_departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department') {
    //     $manager = 'PI0108010';
    //     $nama_manager = 'Yudi Abtadipa';
    //     }else if($mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
    //     $manager = 'PI9906002';
    //     $nama_manager = 'Khoirul Umam';
    //     }else if($mutasi->ke_departemen == 'Purchasing Control Department') {
    //     $manager = 'PI9807014';
    //     $nama_manager = 'Imron Faizal';
    //     }else if($mutasi->ke_departemen == 'General Affairs Department') {
    //     $manager = 'PI9707011';
    //     $nama_manager = 'Prawoto';
    //     }else if($mutasi->ke_departemen == 'Production Engineering Department') {
    //     $manager = 'PI0703002';
    //     $nama_manager = 'Susilo Basri Prasetyo';
    //     }else{
    //     $manager = null;
    //     $dgm = 'PI0109004';
    //     $nama_dgm = 'Budhi Apriyanto';
    //     }
    // }

        $mutasi->app_ct              = 'Approved';
        $mutasi->date_atasan_tujuan  = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'mgr_tujuan';
        $mutasi->manager_tujuan      = $manager;
        $mutasi->nama_manager_tujuan = $nama_manager;
        $mutasi->dgm_tujuan          = $dgm;
        $mutasi->nama_dgm_tujuan     = $nama_dgm;
        $mutasi->save();

    // if ($mutasi->manager_tujuan != null) {
        if ($mutasi->ke_departemen == 'General Process Control Department') {

           $manager      = 'PI0109004';
           $nama_manager = 'Budhi Apriyanto';

           $mutasi->manager_tujuan      = $manager;
           $mutasi->nama_manager_tujuan = $nama_manager;
           $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
           $mutasi->app_mt              = 'Approved';

           $mutasi->dgm_tujuan        = $mutasi->dgm_asal;
           $mutasi->nama_dgm_tujuan   = $mutasi->nama_dgm_asal;
           $mutasi->date_dgm_tujuan   = date('Y-m-d H-y-s');
           $mutasi->app_dt            = 'Approved';
           $mutasi->manager_hrga      = 'PI9707011';
           $mutasi->nama_manager      = 'Prawoto';
           $mutasi->app_m             = 'Mengetahui';
           $mutasi->manager_hrga      = 'PI9707011';
           $mutasi->nama_manager      = 'Prawoto';
           $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
           $mutasi->direktur_hr       = 'PI9709001';
           $mutasi->nama_direktur_hr  = 'Arief Soekamto';
           $mutasi->posisi            = 'dir_hr';
           $mutasi->save();
       }
    // else{
    //         $mutasi->posisi = 'dgm_tujuan';
    //         $mutasi->save();
    //     }
    // }

       if ($mutasi->manager_asal == $mutasi->manager_tujuan) {
           $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
           $mutasi->date_dgm_tujuan     = date('Y-m-d H-y-s');
           $mutasi->dgm_tujuan          = $mutasi->dgm_asal;
           $mutasi->nama_dgm_tujuan     = $mutasi->nama_dgm_asal;
           $mutasi->app_mt              = 'Approved';
           $mutasi->app_dt              = 'Approved';
           $mutasi->posisi              = 'mgr_hrga';
           $mutasi->manager_hrga        = 'PI9707011';
           $mutasi->nama_manager        = 'Prawoto';
           $mutasi->app_m               = 'Mengetahui';
           $mutasi->date_manager_hrga   = date('Y-m-d H-y-s');
           $mutasi->direktur_hr         = 'PI9709001';
           $mutasi->nama_direktur_hr    = 'Arief Soekamto';
           $mutasi->posisi              = 'dir_hr';
           $mutasi->save();
       }
   }

   if (($mutasi->manager_tujuan != null) && ($mutasi->manager_asal != $mutasi->manager_tujuan)) {
    if ($mutasi->ke_departemen == 'General Process Control Department') {
       $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
       $mutasi  = db::select($isimail);
       Mail::to('prawoto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
       Mail::to('arief.soekamto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
       return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
   } else {
       $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
       $mailtoo = DB::select($mails);
   }
} elseif ($mutasi->posisi == 'mgr_hrga') {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_hrga = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} elseif ($mutasi->posisi == 'dgm_tujuan') {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} elseif ($mutasi->posisi == 'dir_hr') {
    $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
    $mutasi  = db::select($isimail);
    Mail::to('prawoto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
    Mail::to('arief.soekamto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}
 //approval manager tujuan
public function mutasi_approval_managerTujuan(Request $request, $id)
{
  try {
     $dgm      = null;
     $nama_dgm = null;
     $gm       = null;
     $nama_gm  = null;

     $mutasi = MutasiAnt::find($id);

     if (($mutasi->ke_departemen == 'Human Resources Department') || ($mutasi->ke_departemen == 'General Affairs Department')) {
        $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
        $mutasi->nama_manager        = 'Prawoto';
        $mutasi->app_mt              = 'Approved';
        $mutasi->app_gt              = 'Approved';
// $mutasi->gm_tujuan = 'PI9709001';
    // $mutasi->nama_gm_tujuan = 'Arief Soekamto';
        $mutasi->direktur_hr      = 'PI9709001';
        $mutasi->nama_direktur_hr = 'Arief Soekamto';
        $mutasi->app_m            = 'Mengetahui';
        $mutasi->posisi           = 'dir_hr';
        $mutasi->save();
    } else {
    // if ($mutasi->dgm_tujuan == null) {
    //     if ($mutasi->ke_departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
    //         $mutasi->ke_departemen == 'Maintenance Department'||
    //         $mutasi->ke_departemen == 'Production Engineering Department'||
    //         $mutasi->ke_departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
    //         $mutasi->ke_departemen == 'Quality Assurance Department'||
    //         $mutasi->ke_departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
    //         $mutasi->ke_departemen == 'Educational Instrument (EI) Department'||
    //         $mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
    //         $mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
    //         $dgm = 'PI0109004';
    //         $nama_dgm = 'Budhi Apriyanto';
    //     }
    //     else if($mutasi->ke_departemen == 'Logistic Department'||
    //         $mutasi->ke_departemen == 'Procurement Department'||
    //         $mutasi->ke_departemen == 'Production Control Department'||
    //         $mutasi->ke_departemen == 'Purchasing Control Department'){
    //         $dgm = 'PI9905001';
    //     $nama_dgm = 'Mei Rahayu';
    // }
    // else if($mutasi->ke_departemen == 'Accounting Department'){
    //     $gm = 'PI2302030';
    //     $nama_gm = 'Mikinori Yano';
    // }
    // else if (($mutasi->ke_departemen == 'Human Resources Department') || ($mutasi->ke_departemen == 'General Affairs Department')){
    //     $mutasi->gm = 'PI9709001';
    //     $mutasi->nama_gm = 'Arief Soekamto';
    // }
    // }
        $approver_dgm = db::table('approvers')->where('department', $mutasi->ke_departemen)->where('remark', 'Deputy General Manager')->first();
        $dgm          = $approver_dgm->approver_id;
        $nama_dgm     = $approver_dgm->approver_name;

    // $approver_gm = db::table('approvers')->where('department', $mutasi->ke_departemen)->where('remark', 'General Manager')->first();
    // $gm = $approver_gm->approver_id;
    // $nama_dgm = $approver_gm->approver_name;

        $mutasi->app_mt              = 'Approved';
        $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
        $mutasi->posisi              = 'dgm_tujuan';
        $mutasi->dgm_tujuan          = $dgm;
        $mutasi->nama_dgm_tujuan     = $nama_dgm;
    // $mutasi->gm_tujuan = $gm;
    // $mutasi->nama_gm_tujuan = $nama_gm;
        $mutasi->save();
    }

// if ($mutasi->dgm_tujuan == null) {
   //     if ($mutasi->ke_departemen == 'Woodwind Instrument - Assembly (WI-A) Department' ||
   //         $mutasi->ke_departemen == 'Maintenance Department'||
   //         $mutasi->ke_departemen == 'Production Engineering Department'||
   //         $mutasi->ke_departemen == 'Woodwind Instrument - Surface Treatment (WI-ST) Department'||
   //         $mutasi->ke_departemen == 'Quality Assurance Department'||
   //         $mutasi->ke_departemen == 'Woodwind Instrument - Welding Process (WI-WP) Department'||
   //         $mutasi->ke_departemen == 'Educational Instrument (EI) Department'||
   //         $mutasi->ke_departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department'||
   //         $mutasi->ke_departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
   //         $dgm = 'PI0109004';
   //         $nama_dgm = 'Budhi Apriyanto';
   //     }
   //     else if($mutasi->ke_departemen == 'Logistic Department'||
   //         $mutasi->ke_departemen == 'Procurement Department'||
   //         $mutasi->ke_departemen == 'Production Control Department'||
   //         $mutasi->ke_departemen == 'Purchasing Control Department'){
   //         $gm = 'PI0109004';
   //         $nama_gm = 'Budhi Apriyanto';
   //     }
   //     else if($mutasi->ke_departemen == 'Accounting Department'){
   //         $gm = 'PI2302030';
   //         $nama_gm = 'Mikinori Yano';
   //     }
   // }
   // else{
   //     $gm = 'PI1206001';
   //     $nama_gm = 'Yukitaka Hayakawa';
   // }

// else{
   //     $mutasi->app_mt = 'Approved';
   //     $mutasi->date_manager_tujuan = date('Y-m-d H-y-s');
   //     $mutasi->posisi = 'dgm_tujuan';
   //     $mutasi->dgm_tujuan = $dgm;
   //     $mutasi->nama_dgm_tujuan = $nama_dgm;
   //     $mutasi->gm_tujuan = $gm;
   //     $mutasi->nama_gm_tujuan = $nama_gm;
   // }
    if ($mutasi->dgm_tujuan == $mutasi->dgm_asal || $mutasi->dgm_tujuan == $mutasi->gm_asal) {
        $mutasi->app_dt            = 'Approved';
        $mutasi->date_dgm_tujuan   = date('Y-m-d H-y-s');
        $mutasi->app_m             = 'Mengetahui';
        $mutasi->manager_hrga      = 'PI9707011';
        $mutasi->nama_manager      = 'Prawoto';
        $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
        $mutasi->direktur_hr       = 'PI9709001';
        $mutasi->nama_direktur_hr  = 'Arief Soekamto';
        $mutasi->posisi            = 'dir_hr';
        $mutasi->save();

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
        $mutasi  = db::select($isimail);
        Mail::to('prawoto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
        Mail::to('arief.soekamto@music.yamaha.com')->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    } else {

        $mutasi->posisi = 'dgm_tujuan';
        $mutasi->save();

        $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
        $mutasi  = db::select($isimail);
        Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    }

   // if ($mutasi->dgm_tujuan != null) {
   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }else{
   //     $mutasi->posisi = 'gm_tujuan';
   //     $mutasi->save();

   //     $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.gm_tujuan = users.username where mutasi_ant_depts.id = ".$mutasi->id;
   //     $mailtoo = DB::select($mails);
   // }
   // $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = ".$mutasi->id;
   // $mutasi = db::select($isimail);
   // Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval dgm tujuan
public function mutasi_approval_dgmTujuan(Request $request, $id)
{
  try {
     $chief           = null;
     $nama_chief      = null;
     $manager         = null;
     $nama_manager    = null;
     $dgm_tujuan      = null;
     $nama_dgm_tujuan = null;

     $mutasi = MutasiAnt::find($id);

   // dd('masuk sini');

     $manager = db::select("select approver_id, approver_name from approvers where department = '" . $mutasi->ke_departemen . "' and remark = 'General Manager'");
   // if ($mutasi->dgm_asal != null) {
   // $gm = 'PI1206001';
   // $nama_gm = 'Yukitaka Hayakawa';
   // $chf = db::select("select employee_id, `name` from employee_syncs where (position = 'chief' or position = 'foreman') and department = '".$mutasi->ke_departemen."' and section = '".$mutasi->ke_seksi."'");

   // if ($chf != null)
   // {
   //     foreach ($chf as $cf)
   //     {
   //         $chief = $cf->employee_id;
   //         $nama_chief = $cf->name;
   //     }
   // }
   // elseif($chf != null)
   // {
   //     if ($request->get('section') == 'Software Section') {
   //         $chief = 'PI0103002';
   //         $nama_chief = 'Agus Yulianto';
   //     }
   //     else{
   //         $chief = null;
   //     }
   // }

   // elseif($mutasi->chief_or_foreman_tujuan == null){
   // $manager = db::select("select employee_id, `name` from employee_syncs where position = 'Manager' and department ='".$mutasi->ke_departemen."'");
     if ($manager != null) {
        foreach ($manager as $mgr) {
           $manager      = $mgr->approver_id;
           $nama_manager = $mgr->approver_name;
       }
   } elseif ($manager == null) {
    if ($mutasi->departemen == 'Woodwind Instrument - Body Parts Process (WI-BPP) Department') {
       $manager      = 'PI0108010';
       $nama_manager = 'Yudi Abtadipa';
   } elseif
   ($mutasi->departemen == 'Woodwind Instrument - Key Parts Process (WI-KPP) Department') {
       $manager      = 'PI9906002';
       $nama_manager = 'Khoirul Umam';
   } elseif
   ($mutasi->departemen == 'Purchasing Control Department') {
       $manager      = 'PI9807014';
       $nama_manager = 'Imron Faizal';
   }
}

if ($mutasi->dgm_asal == 'PI9905001') {
    // dd('pak budhi');
    $mutasi->app_dt          = 'Approved';
    $mutasi->date_dgm_tujuan = date('Y-m-d H-y-s');
    $mutasi->gm_tujuan       = 'PI0109004';
    $mutasi->nama_gm_tujuan  = 'Budhi Apriyanto';
    $mutasi->posisi          = 'gm_tujuan';
} else {
    // dd('pak haya');
    $mutasi->app_dt = 'Approved';
    $mutasi->app_gt = 'Approved';
    // $mutasi->nama_gm_tujuan = 'Yukitaka Hayakawa';
    // $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
    $mutasi->date_dgm_tujuan   = date('Y-m-d H-y-s');
    $mutasi->gm_tujuan         = $manager;
    $mutasi->nama_gm_tujuan    = $nama_manager;
    $mutasi->date_gm_tujuan    = date('Y-m-d H-y-s');
    $mutasi->posisi            = 'dir_hr';
    $mutasi->manager_hrga      = 'PI9707011';
    $mutasi->nama_manager      = 'Prawoto';
    $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
    $mutasi->app_m             = 'Mengetahui';
    $mutasi->direktur_hr       = 'PI9709001';
    $mutasi->nama_direktur_hr  = 'Arief Soekamto';
}
$mutasi->save();
   // }

   // }

$mails1 = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI9707011'";
$mails2 = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI9709001'";

$mailtoo1 = DB::select($mails1);
$mailtoo2 = DB::select($mails2);
$isimail  = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi   = db::select($isimail);

Mail::to($mailtoo1)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));
Mail::to($mailtoo2)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval gm tujuan
public function mutasi_approval_gmTujuan(Request $request, $id)
{
  try {
     $mutasi                 = MutasiAnt::find($id);
     $mutasi->app_gt         = 'Approved';
     $mutasi->date_gm_tujuan = date('Y-m-d H-y-s');
     $mutasi->posisi         = 'dir_hr';

     $mutasi->manager_hrga = 'PI9707011';
     $mutasi->nama_manager = 'Prawoto';
// $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
     $mutasi->app_m = 'Mengetahui';

     $mutasi->direktur_hr      = 'PI9709001';
     $mutasi->nama_direktur_hr = 'Arief Soekamto';
     $mutasi->save();

     $mails_mgr = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_hrga = users.username where mutasi_ant_depts.id = " . $mutasi->id;
     $mails_dir = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.direktur_hr = users.username where mutasi_ant_depts.id = " . $mutasi->id;
// $mails = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_hrga = users.username where mutasi_ant_depts.id = ".$mutasi->id;
     $mailtoo_mgr = DB::select($mails_mgr);
     $mailtoo_dir = DB::select($mails_dir);
     $isimail     = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
     $mutasi      = db::select($isimail);

     Mail::to($mailtoo_mgr)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));

     $m = MutasiAnt::find($id);
   // if (($m->jabatan != 'Operator') && ($m->jabatan != 'Operator Contract')) {
   //     Mail::to(['hiromichi.ichimura@music.yamaha.com'])->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));
   // }

     Mail::to($mailtoo_dir)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));

     return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
 } catch (QueryException $e) {
     return back()->with('error', 'Error')->with('page', 'Mutasi Error');
 }
}
 //approval manager hrga
public function mutasi_approvalManager_Hrga(Request $request, $id)
{
  try {
     $mutasi = MutasiAnt::find($id);
     $like   = $mutasi->ke_jabatan;

     if (strpos($like, 'Operator') !== false) {
// mengandung kata operator
        $mutasi->status            = 'All Approved';
        $mutasi->app_m             = 'Approved';
        $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
        $mutasi->app_dir           = 'Approved';
        $mutasi->nama_direktur_hr  = 'Arief Soekamto';
        $mutasi->date_direktur_hr  = date('Y-m-d H-y-s');
        $mutasi->save();

        $resumes = MutasiAnt::select(
           'id', 'status', 'posisi', 'nik', 'nama', 'mutasi_ant_depts.sub_group', 'mutasi_ant_depts.group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'alasan', 'created_by',

           'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
           'manager_asal', 'nama_manager_asal', 'date_manager_asal',
           'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
           'gm_asal', 'nama_gm_asal', 'date_gm_asal',
           'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
           'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
           'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
           'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
           'manager_hrga', 'nama_manager', 'date_manager_hrga',
           'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr',

           'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir',
           db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'), db::raw('code.position_code as code'))
        ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
        ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
        ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
        ->leftJoin(db::raw('employee_syncs as code'), 'mutasi_ant_depts.nik', '=', 'code.employee_id')
        ->where('mutasi_ant_depts.id', '=', $id)
        ->get();
        $data = array(
           'resumes' => $resumes,
       );

        $mails   = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019";
        $mailtoo = DB::select($mails);

        $mailscc   = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI9709001'";
        $mailtoocc = DB::select($mailscc);

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
        $mutasi  = db::select($isimail);
        Mail::to($mailtoo)->cc($mailtoocc)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
    } else {
        $mutasi->posisi            = 'dir_hr';
        $mutasi->app_m             = 'Mengetahui';
        $mutasi->date_manager_hrga = date('Y-m-d H-y-s');
        $mutasi->direktur_hr       = 'PI9709001';
        $mutasi->nama_direktur_hr  = 'Arief Soekamto';
        $mutasi->save();

        $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.direktur_hr = users.username where mutasi_ant_depts.id = " . $mutasi->id;
        $mailtoo = DB::select($mails);
        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;

        $mutasi = db::select($isimail);
        Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    }

    return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
} catch (QueryException $e) {
 return back()->with('error', 'Error')->with('page', 'Mutasi Error');
}
}

 //approval direktur hr
public function mutasi_approvalDirektur_Hr(Request $request, $id)
{
  try {
     $mutasi                   = MutasiAnt::find($id);
     $mutasi->status           = 'All Approved';
     $mutasi->app_dir          = 'Approved';
     $mutasi->date_direktur_hr = date('Y-m-d H-y-s');
     $mutasi->save();

     $resumes = MutasiAnt::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'mutasi_ant_depts.sub_group', 'mutasi_ant_depts.group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'alasan', 'created_by',

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'manager_asal', 'nama_manager_asal', 'date_manager_asal',
        'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
        'gm_asal', 'nama_gm_asal', 'date_gm_asal',
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
        'manager_hrga', 'nama_manager', 'date_manager_hrga',
        'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr',

        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir',
        db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'), db::raw('code.position_code as code'))
     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
     ->leftJoin(db::raw('employee_syncs as code'), 'mutasi_ant_depts.nik', '=', 'code.employee_id')
     ->where('mutasi_ant_depts.id', '=', $id)
     ->get();
     $data = array(
        'resumes' => $resumes,
    );

     $mails   = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id = 'PI0603019'";
     $mailtoo = DB::select($mails);

     $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;

     $mutasi = db::select($isimail);
     Mail::to(['ummi.ernawati@music.yamaha.com'])->bcc(['lukmannul.arif@music.yamaha.com', 'mokhamad.khamdan.khabibi@music.yamaha.com'])->send(new SendEmail($mutasi, 'done_mutasi_ant'));
     return redirect('/index/message/approval')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
 } catch (QueryException $e) {
     return back()->with('error', 'Error')->with('page', 'Mutasi Error');
// dd($e);
 }
}

 // ===========================================================================================================
 //==================================//
 //          Verifikasi mutasi           //
 //==================================//
public function verifikasi_mutasi_ant(Request $request, $id)
{
  $mutasi = MutasiAnt::find($id);

  $resumes = MutasiAnt::select(
     'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by',

     'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
     'manager_asal', 'nama_manager_asal', DB::RAW('DATE_FORMAT(date_manager_asal, "%d-%m-%Y") as date_manager_asal'),
     'dgm_asal', 'nama_dgm_asal', DB::RAW('DATE_FORMAT(date_dgm_asal, "%d-%m-%Y") as date_dgm_asal'),
     'gm_asal', 'nama_gm_asal', DB::RAW('DATE_FORMAT(date_gm_asal, "%d-%m-%Y") as date_gm_asal'),
     'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
     'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
     'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'),
     'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'),
     'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
     'direktur_hr', 'nama_direktur_hr', DB::RAW('DATE_FORMAT(date_direktur_hr, "%d-%m-%Y") as date_direktur_hr'),

     'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
  ->where('mutasi_ant_depts.id', '=', $id)
  ->get();

  return view('mutasi.verifikasi.verifikasi_mutasi_ant', array(
// 'title' => 'Mutasi Antar Departemen Monitoring & Control',
    // 'title_jp' => '監視・管理',

     'mutasi'  => $mutasi,
     'resumes' => $resumes,
 ))->with('page', 'Mutasi');
}
public function verifikasi_mutasi(Request $request, $id)
{
  $mutasi = Mutasi::find($id);

  $resumes = Mutasi::select(
     'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_seksi', 'ke_sub_group', 'ke_group', 'ke_jabatan', 'tanggal', 'alasan', 'created_by',

     'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
     'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
     'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
     'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'),
     'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'),
     'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),

     'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m')
  ->where('mutasi_depts.id', '=', $id)
  ->get();

  return view('mutasi.verifikasi.verifikasi_mutasi', array(
     'title'    => 'Mutasi Satu Departemen Monitoring & Control',
     'title_jp' => '監視・管理',

     'mutasi'   => $mutasi,
     'resumes'  => $resumes,
 ))->with('page', 'Mutasi');
}

public function report_mutasi_ant(Request $request, $id)
{
  $mutasi = MutasiAnt::find($id);

  $resumes = MutasiAnt::select(
     'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by',

     'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
     'manager_asal', 'nama_manager_asal', DB::RAW('DATE_FORMAT(date_manager_asal, "%d-%m-%Y") as date_manager_asal'),
     'dgm_asal', 'nama_dgm_asal', DB::RAW('DATE_FORMAT(date_dgm_asal, "%d-%m-%Y") as date_dgm_asal'),
     'gm_asal', 'nama_gm_asal', DB::RAW('DATE_FORMAT(date_gm_asal, "%d-%m-%Y") as date_gm_asal'),
     'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
     'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
     'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'),
     'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'),
     'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
     'direktur_hr', 'nama_direktur_hr', DB::RAW('DATE_FORMAT(date_direktur_hr, "%d-%m-%Y") as date_direktur_hr'),

     'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
  ->where('mutasi_ant_depts.id', '=', $id)
  ->get();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A5', 'landscape');

  $pdf->loadView('mutasi.report.report_ant', array(
     'pr' => $resumes,
 ));

  $path = "mutasi/" . $resumes[0]->nik . ".pdf";
  return $pdf->stream("Mutasi " . $resumes[0]->nik . ".pdf");
}

public function report_mutasi(Request $request, $id)
{
  $mutasi = Mutasi::find($id);

  $resumes = Mutasi::select(
     'id', 'status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'tanggal', 'alasan', 'created_by',

     'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
     'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
     'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
     'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'),
     'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'),
     'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),

     'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m')
  ->where('mutasi_depts.id', '=', $id)
  ->get();

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A5', 'landscape');

  $pdf->loadView('mutasi.report.report', array(
     'title'    => 'Mutasi Satu Departemen Monitoring & Control',
     'title_jp' => '監視・管理',

     'pr'       => $resumes,
 ));

  $path = "mutasi/" . $resumes[0]->nik . ".pdf";
  return $pdf->stream("Mutasi " . $resumes[0]->nik . ".pdf");
}

public function finish_ant(Request $request, $id)
{
  $mutasi         = MutasiAnt::find($id);
  $mutasi->remark = '1';
  $mutasi->save();

  return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function finish(Request $request, $id)
{
  $mutasi         = Mutasi::find($id);
  $mutasi->remark = '1';
  $mutasi->save();

  return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function email(Request $request, $id)
{
  $chief      = null;
  $nama_chief = null;
  $posit      = null;

// $departemen = $mutasi->departemen;
  // $seksi = $mutasi->seksi;
  // $ke_sub_group = $mutasi->ke_sub_group;
  // $ke_group = $mutasi->ke_group;
  // $ke_seksi = $mutasi->ke_seksi;
  // $position = $mutasi->position;
  $mutasi         = Mutasi::find($id);
  $mutasi->remark = '2';
  $mutasi->save();

  if ($mutasi->chief_or_foreman_asal != null) {
     $mails = "select distinct email from mutasi_depts join users on mutasi_depts.chief_or_foreman_asal = users.username where mutasi_depts.id = " . $mutasi->id;
 } elseif ($mutasi->manager_tujuan != null) {
     $mutasi->posisi = 'mgr';
     $mutasi->save();
     $mails = "select distinct email from mutasi_depts join users on mutasi_depts.manager_tujuan = users.username where mutasi_depts.id = " . $mutasi->id;
 }

 $mailtoo = DB::select($mails);

 $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, jabatan, rekomendasi, tanggal, tanggal_maksimal, alasan from mutasi_depts where mutasi_depts.id = " . $mutasi->id;
 $mutasi  = db::select($isimail);
 Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_satu'));
 return redirect('/dashboard/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function emailAnt(Request $request, $id)
{
  $manager      = null;
  $nama_manager = null;
  $chief        = null;
  $nama_chief   = null;
  $posit        = null;
  $dgm          = null;
  $nama_dgm     = null;

  $mutasi         = MutasiAnt::find($id);
  $mutasi->remark = '2';
  $mutasi->save();

  if ($mutasi->created_by == '20' && $mutasi->jabatan == 'Manager') {
     if ($mutasi->gm_tujuan == 'PI0109004') {
        $mutasi->posisi = 'gm_tujuan';
        $mutasi->save();

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;

        $mutasi = db::select($isimail);
        Mail::to(['budhi.apriyanto@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    // Mail::to(['yukitaka.hayakawa@music.yamaha.com'])->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));
    } else {
        $mutasi->posisi           = 'dir_hr';
        $mutasi->direktur_hr      = 'PI9709001';
        $mutasi->nama_direktur_hr = 'Arief Soekamto';
        $mutasi->app_m            = 'Mengetahui';
        $mutasi->save();

        $isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;

        $mutasi = db::select($isimail);
        Mail::to(['arief.soekamto@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
    // Mail::to(['prawoto@music.yamaha.com', 'yukitaka.hayakawa@music.yamaha.com', 'hiromichi.ichimura@music.yamaha.com'])->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mgr_hrga'));
    }
} else {
 if ($mutasi->chief_or_foreman_asal != null) {
    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.chief_or_foreman_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} elseif ($mutasi->manager_asal != null) {
    $mutasi->posisi = 'mgr_asal';
    $mutasi->save();

    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} elseif ($mutasi->dgm_asal != null) {
    $mutasi->posisi = 'dgm_asal';
    $mutasi->save();

    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.dgm_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
} else {
    $mutasi->posisi = 'mgr_asal';
    $mutasi->save();

    $mails   = "select distinct email from mutasi_ant_depts join users on mutasi_ant_depts.manager_asal = users.username where mutasi_ant_depts.id = " . $mutasi->id;
    $mailtoo = DB::select($mails);
}
$isimail = "select id, nama, nik, sub_group, ke_sub_group, `group`, ke_group, seksi, ke_seksi, departemen, ke_departemen, jabatan, ke_jabatan, rekomendasi, tanggal, alasan from mutasi_ant_depts where mutasi_ant_depts.id = " . $mutasi->id;
$mutasi  = db::select($isimail);
Mail::to($mailtoo)->bcc(['lukmannul.arif@music.yamaha.com', 'rio.irvansyah@music.yamaha.com'])->send(new SendEmail($mutasi, 'mutasi_ant'));
}
return redirect('/dashboard_ant/mutasi')->with('status', 'New Karyawan Mutasi has been created.')->with('page', 'Mutasi');
}

public function fetchMonitoringMutasiAnt(Request $request)
{
  $tahun  = date('Y');
  $dateto = $request->get('dateto');
  $today  = date('Y-m');

// if ($dateto == "") {
  //     $dateto = date('Y-m', strtotime(carbon::now()));
  // } else {
  //     $dep = '';
  // }

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department')
  ->first();

  if (Auth::user()->role_code == "S-MIS" || $emp_dept->employee_id == "PI0603019" || $emp_dept->employee_id == "PI1110002") {
     if ($dateto != "") {
        $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
            FROM
            mutasi_ant_depts
            WHERE
            mutasi_ant_depts.deleted_at IS NULL
            AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
            GROUP BY
            bulan,
            tahun
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
    } else {
    // $data = db::select("
    //     SELECT
    //     count( nik ) AS jumlah,
    //     monthname( tanggal ) AS bulan,
    //     YEAR ( tanggal ) AS tahun,
    //     sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
    //     sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
    //     sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
    //     FROM
    //     mutasi_ant_depts
    //     WHERE
    //     mutasi_ant_depts.deleted_at IS NULL and YEAR ( tanggal ) = '".$tahun."'
    //     GROUP BY
    //     bulan,
    //     tahun
    //     ORDER BY
    //     tahun,
    //     MONTH ( tanggal ) ASC
    //     ");

        $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
            FROM
            mutasi_ant_depts as mt
            left join weekly_calendars as wc on mt.tanggal = wc.week_date
            WHERE
            mt.deleted_at IS NULL and fiscal_year = 'FY199'
            GROUP BY
            bulan,
            tahun
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
    }
} else {
 if ($dateto != "") {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_ant_depts
        WHERE
        mutasi_ant_depts.deleted_at IS NULL
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
} else {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_ant_depts as mt
        left join weekly_calendars as wc on mt.tanggal = wc.week_date
        WHERE
        mt.deleted_at IS NULL and fiscal_year = 'FY199'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
}
}

$response = array(
 'status' => true,
 'datas'  => $data,
 'tahun'  => $tahun,
 'dateto' => $dateto,
);
return Response::json($response);
}

public function fetchMonitoringMutasi(Request $request)
{
  $tahun  = date('Y');
  $dateto = $request->get('dateto');
  $today  = date('Y-m');

  $emp_dept = EmployeeSync::where('employee_id', Auth::user()->username)
  ->select('employee_id', 'department', 'position')
  ->first();
  $dept = $emp_dept->department;

  $email = Auth::user()->email;
  $dpts  = db::select("SELECT remark FROM send_emails where email = '" . $email . "' and remark like '%Department%'");
  $dpts  = json_decode(json_encode($dpts), true);
  $dpts2 = [];

  foreach ($dpts as $dpt) {
     array_push($dpts2, $dpt['remark']);
 }

 $dpts2 = "'" . implode("' , '", $dpts2) . "'";
  // dd($dpts2);

 $dpts_st  = db::select("SELECT remark FROM send_emails_staff where email = '" . $email . "' and remark like '%Department%'");
 $dpts_st  = json_decode(json_encode($dpts_st), true);
 $dpts_st2 = [];

 foreach ($dpts_st as $dpt_st) {
     array_push($dpts_st2, $dpt_st['remark']);
 }

 $dpts_st2 = "'" . implode("' , '", $dpts_st2) . "'";
  // dd($dpts_st2);

 if (Auth::user()->role_code == "S-MIS" || $emp_dept->employee_id == "PI0603019" || $emp_dept->employee_id == "PI1110002") {
     if ($dateto != "") {
        $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
            FROM
            mutasi_depts
            WHERE
            mutasi_depts.deleted_at IS NULL
            AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
            GROUP BY
            bulan,
            tahun
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
    } else {
        $data = db::select("
            SELECT
            count( nik ) AS jumlah,
            monthname( tanggal ) AS bulan,
            YEAR ( tanggal ) AS tahun,
            sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
            sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
            sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
            FROM
            mutasi_depts as mt
            LEFT JOIN weekly_calendars AS wc ON mt.tanggal = wc.week_date
            WHERE
            mt.deleted_at IS NULL
            AND fiscal_year = 'FY199'
            GROUP BY
            bulan,
            tahun
            ORDER BY
            tahun,
            MONTH ( tanggal ) ASC
            ");
    }
} elseif ($emp_dept->position == "Manager") {
 if ($dateto != "") {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts2 . ")
        AND mutasi_depts.deleted_at IS NULL
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
} else {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts AS mt
        LEFT JOIN weekly_calendars AS wc ON mt.tanggal = wc.week_date
        WHERE
        mt.deleted_at IS NULL
        AND fiscal_year = 'FY199'
        AND mt.departemen IN ( " . $dpts2 . " )
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
}
} elseif ($emp_dept->employee_id == "PI1710002" || $emp_dept->employee_id == "PI0804012") {
 if ($dateto != "") {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen IN (" . $dpts_st2 . ")
        AND mutasi_depts.deleted_at IS NULL
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
} else {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts AS mt
        LEFT JOIN weekly_calendars AS wc ON mt.tanggal = wc.week_date
        WHERE
        mt.deleted_at IS NULL
        AND fiscal_year = 'FY199'
        AND mt.departemen IN ( " . $dpts2 . " )
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
}
} else {
 if ($dateto != "") {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` is null THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts
        WHERE
        mutasi_depts.departemen = '" . $dept . "'
        AND mutasi_depts.deleted_at IS NULL
        AND DATE_FORMAT( tanggal, '%Y-%m' ) = '" . $dateto . "'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
} else {
    $data = db::select("
        SELECT
        count( nik ) AS jumlah,
        monthname( tanggal ) AS bulan,
        YEAR ( tanggal ) AS tahun,
        sum( CASE WHEN `status` IS NULL THEN 1 ELSE 0 END ) AS Proces,
        sum( CASE WHEN `status` = 'All Approved' THEN 1 ELSE 0 END ) AS Signed,
        sum( CASE WHEN `status` = 'Rejected' THEN 1 ELSE 0 END ) AS NotSigned
        FROM
        mutasi_depts AS mt
        LEFT JOIN weekly_calendars AS wc ON mt.tanggal = wc.week_date
        WHERE
        mt.deleted_at IS NULL
        AND fiscal_year = 'FY199'
        AND mt.departemen = '" . $dept . "'
        GROUP BY
        bulan,
        tahun
        ORDER BY
        tahun,
        MONTH ( tanggal ) ASC
        ");
}
}

$response = array(
 'status' => true,
 'datas'  => $data,
 'tahun'  => $tahun,
 'dateto' => $dateto,
);

return Response::json($response);
}
public function HrExport()
{
  return view('mutasi.hr_export', array(
     'title'    => 'HR',
     'title_jp' => 'スクラップ材料',

 ))->with('page', 'Scrap');
}

public function FetchHrExport(Request $request)
{
  $today   = date("Y-m-d");
  $tanggal = $request->get('dateto');

  if ($tanggal == null) {
     $resumes = Mutasi::select(
        'mutasi_depts.id',
        'mutasi_depts.nik',
        'mutasi_depts.nama',
        'mutasi_depts.tanggal',
        'mutasi_depts.position_code',
        'mutasi_depts.ke_jabatan')
   // ->where(db::raw('date(created_at)'),'=', $today)
     ->where('mutasi_depts.status', '=', 'All Approved')
     ->where('mutasi_depts.remark', '=', '2')
     ->orderBy('mutasi_depts.tanggal', 'asc')
     ->get();
 } else {
     $resumes = Mutasi::select(
        'mutasi_depts.id',
        'mutasi_depts.nik',
        'mutasi_depts.nama',
        'mutasi_depts.tanggal',
        'mutasi_depts.position_code',
        'mutasi_depts.ke_jabatan')
     ->where('mutasi_depts.status', '=', 'All Approved')
     ->where('mutasi_depts.remark', '=', '2')
     ->where(db::raw('date(tanggal)'), '=', $tanggal)
     ->orderBy('mutasi_depts.tanggal', 'asc')
     ->get();
 }

 return DataTables::of($resumes)

 ->addColumn('CareerTransition', function ($resumes) {
    return 'Movement';
})
 ->addColumn('CareerTransType', function ($resumes) {
    return 'Mutation';
})
  // ->addColumn('no',function($resumes){
  //   return 'Mutation';
  // })

  // ->editColumn('tgl_permintaan',function($resumes){
  //     return date('d-m-Y', strtotime($cpar_details->tgl_permintaan));
  //   })

  // ->rawColumns(['no' => 'no'])
 ->rawColumns(['CareerTransition' => 'CareerTransition'])
 ->rawColumns(['CareerTransType' => 'CareerTransType'])
 ->make(true);
}

public function HrExportExcel(Request $request)
{

  $tanggal = $request->get('dateto');
  $time    = date('d-m-Y H;i;s');

  if ($tanggal == null) {
     $resumes = Mutasi::select(
        'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark',

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
        'manager_hrga', 'nama_manager', 'date_manager_hrga',

        'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
        db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_depts.nik', '=', 'pegawai.employee_id')
     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_depts.nik', '=', 'grade.employee_id')
     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_depts.nik', '=', 'posisi.employee_id')
     ->where('mutasi_depts.status', '=', 'All Approved')
     ->where('mutasi_depts.remark', '=', '2')
     ->orderBy('mutasi_depts.tanggal', 'asc')
     ->get();
 } else {
     $resumes = Mutasi::select(
        'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark',

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
        'manager_hrga', 'nama_manager', 'date_manager_hrga',

        'app_ca', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
        db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_depts.nik', '=', 'pegawai.employee_id')
     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_depts.nik', '=', 'grade.employee_id')
     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_depts.nik', '=', 'posisi.employee_id')
     ->where('mutasi_depts.status', '=', 'All Approved')
     ->where('mutasi_depts.remark', '=', '2')
     ->where(db::raw('date(tanggal)'), '=', $tanggal)
     ->orderBy('mutasi_depts.tanggal', 'asc')
     ->get();
 }

 $data = array(
     'resumes' => $resumes,
 );

 $mutasi = Mutasi::where('status', '=', 'All Approved')->get();
 foreach ($mutasi as $update) {
     $update->remark = '1';
     $update->save();
 }

  // $mutasi = Mutasi::where('mutasi_depts.tanggal')->update([
  //           'remark' => '1'
  //       ]);

 ob_clean();

 Excel::create('Mutasi Satu Departemen ' . $time, function ($excel) use ($data) {
     $excel->sheet('HR', function ($sheet) use ($data) {
        return $sheet->loadView('mutasi.mutasi_excel', $data);
    });
 })->export('xls');
}

public function AntHrExport()
{
  return view('mutasi.hr_export_ant', array(
     'title'    => 'HR',
     'title_jp' => 'スクラップ材料',

 ))->with('page', 'Scrap');
}

public function AntFetchHrExport(Request $request)
{

  // $tanggal = "";
  // $adddepartment = "";

  // if (strlen($request->get('datefrom')) > 0)
  // {
  //     $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
  //     $tanggal = "and A.tgl_po >= '" . $datefrom . " 00:00:00' ";
  //     if (strlen($request->get('dateto')) > 0)
  //     {
  //         $dateto = date('Y-m-d', strtotime($request->get('dateto')));
  //         $tanggal = $tanggal . "and A.tgl_po  <= '" . $dateto . " 23:59:59' ";
  //     }
  // }

  // $qry = "SELECT * FROM acc_purchase_orders A WHERE A.deleted_at IS NULL " . $tanggal . " order by A.id DESC";
  // $po = DB::select($qry);

  $today   = date("Y-m-d");
  $tanggal = $request->get('dateto');

  if ($tanggal == null) {
     $resumes = MutasiAnt::select(
        'mutasi_ant_depts.id',
        'mutasi_ant_depts.nik',
        'mutasi_ant_depts.nama',
        'mutasi_ant_depts.tanggal',
        'mutasi_ant_depts.position_code',
        'mutasi_ant_depts.ke_jabatan')
   // ->where(db::raw('date(created_at)'),'=', $today)
     ->where('mutasi_ant_depts.status', '=', 'All Approved')
     ->where('mutasi_ant_depts.remark', '=', '2')
     ->orderBy('mutasi_ant_depts.tanggal', 'asc')
     ->get();
 } else {
     $resumes = MutasiAnt::select(
        'mutasi_ant_depts.id',
        'mutasi_ant_depts.nik',
        'mutasi_ant_depts.nama',
        'mutasi_ant_depts.tanggal',
        'mutasi_ant_depts.position_code',
        'mutasi_ant_depts.ke_jabatan')
     ->where('mutasi_ant_depts.status', '=', 'All Approved')
     ->where('mutasi_ant_depts.remark', '=', '2')
     ->where(db::raw('date(tanggal)'), '=', $tanggal)
     ->orderBy('mutasi_ant_depts.tanggal', 'asc')
     ->get();
 }

 return DataTables::of($resumes)

 ->addColumn('CareerTransition', function ($resumes) {
    return 'Movement';
})
 ->addColumn('CareerTransType', function ($resumes) {
    return 'Mutation';
})->addColumn('no', function ($resumes) {
 return 'Mutation';
})

  // ->editColumn('tgl_permintaan',function($resumes){
  //     return date('d-m-Y', strtotime($cpar_details->tgl_permintaan));
  //   })

->rawColumns(['no' => 'no'])
->rawColumns(['CareerTransition' => 'CareerTransition'])
->rawColumns(['CareerTransType' => 'CareerTransType'])
->make(true);
}

public function AntHrExportExcel(Request $request)
{

  $tanggal = $request->get('dateto');
  $time    = date('d-m-Y H;i;s');

  if ($tanggal == null) {
     $resumes = MutasiAnt::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'mutasi_ant_depts.sub_group', 'mutasi_ant_depts.group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'alasan', 'created_by',

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'manager_asal', 'nama_manager_asal', 'date_manager_asal',
        'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
        'gm_asal', 'nama_gm_asal', 'date_gm_asal',
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
        'manager_hrga', 'nama_manager', 'date_manager_hrga',
        'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr',

        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir',
        db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'), db::raw('code.position_code as code'))
     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
     ->leftJoin(db::raw('employee_syncs as code'), 'mutasi_ant_depts.nik', '=', 'code.employee_id')
     ->where('mutasi_ant_depts.status', '=', 'All Approved')
     ->where('mutasi_ant_depts.remark', '=', '2')
     ->orderBy('mutasi_ant_depts.tanggal', 'asc')
     ->get();
   // $resumes = MutasiAnt::select(
   //     'status', 'posisi', 'nik', 'nama', 'seksi', 'departemen', 'jabatan', 'rekomendasi','ke_sub_group', 'ke_group', 'ke_seksi', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'tanggal_maksimal', 'alasan', 'created_by', 'remark',

   //     'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
   //     'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
   //     'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
   //     'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
   //     'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
   //     'manager_hrga', 'nama_manager', 'date_manager_hrga',

   //     'app_ca','app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m',
   //     db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'))
   //     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
   //     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
   //     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
   //     ->where('mutasi_ant_depts.status', '=', 'All Approved')
   //     ->where('mutasi_ant_depts.remark', null)
   //     ->orderBy('mutasi_ant_depts.tanggal', 'asc')
   //     ->get();
 } else {
     $resumes = MutasiAnt::select(
        'id', 'status', 'posisi', 'nik', 'nama', 'mutasi_ant_depts.sub_group', 'mutasi_ant_depts.group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'mutasi_ant_depts.position_code', 'tanggal', 'alasan', 'created_by',

        'chief_or_foreman_asal', 'nama_chief_asal', 'date_atasan_asal',
        'manager_asal', 'nama_manager_asal', 'date_manager_asal',
        'dgm_asal', 'nama_dgm_asal', 'date_dgm_asal',
        'gm_asal', 'nama_gm_asal', 'date_gm_asal',
        'chief_or_foreman_tujuan', 'nama_chief_tujuan', 'date_atasan_tujuan',
        'manager_tujuan', 'nama_manager_tujuan', 'date_manager_tujuan',
        'dgm_tujuan', 'nama_dgm_tujuan', 'date_dgm_tujuan',
        'gm_tujuan', 'nama_gm_tujuan', 'date_gm_tujuan',
        'manager_hrga', 'nama_manager', 'date_manager_hrga',
        'direktur_hr', 'nama_direktur_hr', 'date_direktur_hr',

        'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir',
        db::raw('pegawai.employment_status as pegawai'), db::raw('grade.grade_code as grade'), db::raw('posisi.position as posisi'), db::raw('code.position_code as code'))
     ->leftJoin(db::raw('employee_syncs as pegawai'), 'mutasi_ant_depts.nik', '=', 'pegawai.employee_id')
     ->leftJoin(db::raw('employee_syncs as grade'), 'mutasi_ant_depts.nik', '=', 'grade.employee_id')
     ->leftJoin(db::raw('employee_syncs as posisi'), 'mutasi_ant_depts.nik', '=', 'posisi.employee_id')
     ->leftJoin(db::raw('employee_syncs as code'), 'mutasi_ant_depts.nik', '=', 'code.employee_id')
     ->where('mutasi_ant_depts.status', '=', 'All Approved')
     ->where('mutasi_ant_depts.remark', '=', '2')
     ->where(db::raw('date(tanggal)'), '=', $tanggal)
     ->orderBy('mutasi_ant_depts.tanggal', 'asc')
     ->get();
 }

 $data = array(
     'resumes' => $resumes,
 );

 if ($tanggal != null) {
     $mutasi = MutasiAnt::where('tanggal', '=', $tanggal)->get();
     foreach ($mutasi as $update) {
        $update->remark = '1';
        $update->save();
    }
} else {
 $mutasi = MutasiAnt::where('mutasi_ant_depts.status', '=', 'All Approved')->get();
 foreach ($mutasi as $update) {
    $update->remark = '1';
    $update->save();
}
}

  // $mutasi = Mutasi::where('mutasi_ant_depts.tanggal')->update([
  //           'remark' => '1'
  //       ]);

ob_clean();

Excel::create('Mutasi Antar Departemen ' . $time, function ($excel) use ($data) {
 $excel->sheet('HR', function ($sheet) use ($data) {
    return $sheet->loadView('mutasi.mutasi_ant_excel', $data);
});
})->export('xls');
}

public function getNotifMutasiSatu()
{
  if (Auth::user() !== null) {
     $user = strtoupper(Auth::user()->username);
     $name = Auth::user()->name;
     $role = Auth::user()->role_code;

     $chief_asal   = DB::SELECT('select nama_chief_asal from mutasi_depts where posisi = "chf_asal" and nama_chief_asal = "' . $name . '" and remark = "2" and status is null');
     $chief_tujuan = DB::SELECT('select nama_chief_tujuan from mutasi_depts where posisi = "chf_tujuan" and nama_chief_tujuan = "' . $name . '" and remark = "2" and status is null');
     $manager      = DB::SELECT('select nama_manager_tujuan from mutasi_depts where posisi = "mgr" and nama_manager_tujuan = "' . $name . '" and remark = "2" and status is null');
     $hr           = DB::SELECT('select nama_manager from mutasi_depts where posisi = "hr" and nama_manager = "' . $name . '" and remark = "2" and status is null');

     $notif = 0;

     if (count($chief_asal) > 0 || count($chief_tujuan) > 0 || count($manager) > 0 || count($hr) > 0) {

        $notif = count($chief_asal) + count($chief_tujuan) + count($manager) + count($hr);
    }

   // dd($notif);
    return $notif;
}
}

public function getNotifMutasiAntar()
{
  if (Auth::user() !== null) {
     $user = strtoupper(Auth::user()->username);
     $name = Auth::user()->name;
     $role = Auth::user()->role_code;

     $chief_asal   = DB::SELECT('select nama_chief_asal from mutasi_ant_depts where posisi = "chf_asal" and nama_chief_asal = "' . $name . '" and remark = "2" and status is null');
     $manager_asal = DB::SELECT('select nama_manager_asal from mutasi_ant_depts where posisi = "mgr_asal" and nama_manager_asal = "' . $name . '" and remark = "2" and status is null');
     $dgm_asal     = DB::SELECT('select nama_dgm_asal from mutasi_ant_depts where posisi = "dgm_asal" and nama_dgm_asal = "' . $name . '" and remark = "2" and status is null');
     $gm_asal      = DB::SELECT('select nama_gm_asal from mutasi_ant_depts where posisi = "gm_asal" and nama_gm_asal = "' . $name . '" and remark = "2" and status is null');

     $chief_tujuan   = DB::SELECT('select nama_chief_tujuan from mutasi_ant_depts where posisi = "chf_tujuan" and nama_chief_tujuan = "' . $name . '" and remark = "2" and status is null');
     $manager_tujuan = DB::SELECT('select nama_manager_tujuan from mutasi_ant_depts where posisi = "mgr_tujuan" and nama_manager_tujuan = "' . $name . '" and remark = "2" and status is null');
     $dgm_tujuan     = DB::SELECT('select nama_dgm_tujuan from mutasi_ant_depts where posisi = "dgm_tujuan" and nama_dgm_tujuan = "' . $name . '" and remark = "2" and status is null');
     $gm_tujuan      = DB::SELECT('select nama_gm_tujuan from mutasi_ant_depts where posisi = "gm_tujuan" and nama_gm_tujuan = "' . $name . '" and remark = "2" and status is null');

     $manager_hr  = DB::SELECT('select nama_manager from mutasi_ant_depts where posisi = "mgr_hrga" and nama_manager = "' . $name . '" and remark = "2" and status is null');
     $direktur_hr = DB::SELECT('select nama_direktur_hr from mutasi_ant_depts where posisi = "dir_hr" and nama_direktur_hr = "' . $name . '" and remark = "2" and status is null');

     $notif = 0;

     if (count($chief_asal) > 0 || count($manager_asal) > 0 || count($dgm_asal) > 0 || count($gm_asal) > 0 || count($chief_tujuan) > 0 || count($manager_tujuan) > 0 || count($dgm_tujuan) > 0 || count($gm_tujuan) > 0 || count($manager_hr) > 0 || count($direktur_hr) > 0) {

        $notif = count($chief_asal) + count($manager_asal) + count($dgm_asal) + count($gm_asal) + count($chief_tujuan) + count($manager_tujuan) + count($dgm_tujuan) + count($gm_tujuan) + count($manager_hr) + count($direktur_hr);
    }

    return $notif;
}
}

}
