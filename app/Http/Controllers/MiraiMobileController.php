<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Response;
use DataTables;
use Excel;
use App\EmployeeSync;
use App\WeeklyCalendar;
use App\Employee;

class MiraiMobileController extends Controller
{

  public function index()
  {
    $title = 'YMPCOID Report';
    $title_jp = 'モバイルMIRAIの記録';

    return view('mirai_mobile.index', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPCOID');
  }

  public function indexCoronaMap(){
    $title = 'YMPI Corona Mapping';
    $title_jp = '??';

    return view('mirai_mobile.corona_map', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPCOID');
  }

  public function indexCoronaInformation(){
    $title = 'Daily Corona Data';
    $title_jp = 'インドネシア国内の新型コロナウイルス感染症の感染拡大データ';

    return view('mirai_mobile.corona_information', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPCOID');
  }

  public function indexVaksinReport(){
    $title = 'Employee Vaccination Report';
    $title_jp = '従業員ワクチン接種報告';

    return view('mirai_mobile.vaksin_report', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPCOID');
  }

  public function indexVaksinRegistrationReport(){
    $title = 'Employee Vaccination Registration Report';
    $title_jp = '';

    return view('mirai_mobile.vaksin_registration_report', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'YMPCOID');
  }

  public function fetchCoronaInformation(Request $request){
    $corona_informations = db::table('corona_informations')->orderBy('date', 'ASC')->get();

    $last_update = db::table('corona_informations')->orderBy('date', 'desc')->first();

    $now = date('Y-m-d', strtotime($last_update->date));
    $yesterday = date('Y-m-d',strtotime("-1 days"));
    if(strlen($request->get('date_now')) > 0 ){
      $now = date('Y-m-d', strtotime($request->get('date_now')));
      $yesterday = date('Y-m-d', strtotime('-1 days', strtotime($request->get('date_now'))));
    }

    $detail_now = db::table('corona_informations')->orderBy('date', 'ASC')->where('date', '=', $now)->get();
    $detail_yesterday = db::table('corona_informations')->orderBy('date', 'ASC')->where('date', '=', $yesterday)->get();

    $response = array(
      'status' => true,
      'corona_informations' => $corona_informations,
      'detail_now' => $detail_now,
      'detail_yesterday' => $detail_yesterday,
      'now' => $now
    );
    return Response::json($response);

  }

  public function health(){
    $title = 'Employee Attendance';
    $title_jp = '従業員の健康報告';


    if(Auth::user()->role_code == 'C-HR' || Auth::user()->role_code == 'M-HR' || Auth::user()->role_code == 'S-HR' || Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S-GA' || Auth::user()->role_code == 'C-GA' || Auth::user()->role_code == 'S'){
      return view('mirai_mobile.report_health', array(
        'title' => $title,
        'title_jp' => $title_jp
      ))->with('page', 'Employee Attendance');
    }

    return view('404',  
      array('message' => 'Silahkan menghubungi bagian HR untuk data absensi YMPICOID.'
    )
    );
  }

  public function shift(){
    $title = 'Employee Work Grup';
    $title_jp = '';

    return view('mirai_mobile.report_shift', array(
      'title' => $title,
      'title_jp' => $title_jp
    ))->with('page', 'Employee Work Grup');
  }

  public function fetch_detail(Request $request){

    $tgl = $request->get("tgl");
    // $remark = $request->get("remark");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }
    
    $data = DB::connection('mobile')->select("SELECT
      employee_id,
      name,
      kode,
      department,
      section,
      groupes,
      COALESCE(created_at,'Tidak Tersedia') as created_at,
      COALESCE(time(created_at),'Tidak Tersedia') as jam,
      remark
      FROM
      (
      SELECT
      groups.employee_id,
      groups.name,
      groups.kode,
      employees.department,
      employees.section,
      employees.group as groupes,
    --    log.department,
    log.created_at,
    IF
    (
    time( log.created_at ) > '07:00:00' 
    AND time( log.created_at ) <= '08:00:00', 'LTI', IF ( time( log.created_at ) > '08:00:00' 
    OR log.created_at IS NULL,
    'ABS',
    IF
    ( time( log.created_at ) <= '07:00:00', 'PRS', 'Unidentified' ))) AS remark 
    FROM
    groups
    LEFT JOIN (
    SELECT
    employee_id,
    name,
    department,
    min( created_at ) AS created_at 
    FROM
    quiz_logs 
    WHERE
    date( created_at ) = '".$tgl."' 
    GROUP BY
    employee_id,
    name,
    department 
    ) AS log ON log.employee_id = groups.employee_id 
    JOIN employees on groups.employee_id = employees.employee_id
    WHERE
    groups.tanggal = '".$tgl."' 
    AND groups.remark = 'OFF' 
    AND groups.employee_id NOT IN ( SELECT employee_id FROM LEAVES ) 
    ORDER BY
    remark,
    created_at 
    ) AS LOG ORDER BY remark
    ");

    $response = array(
      'status' => true,
      'lists' => $data,
    );
    return Response::json($response);
  }

  public function fetch_detail_sakit(Request $request){

    $tgl = $request->get("tgl");
    $penyakit = $request->get("penyakit");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }

    $data = DB::connection('mobile')->select("SELECT quiz_logs.employee_id,
      employees.name,
      employees.department,
      employees.section,
      employees.group
      FROM
      quiz_logs
      LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
      WHERE
      answer_date = '".$tgl."' 
      AND employees.end_date IS NULL 
      AND employees.keterangan IS NULL 
      AND question = '".$penyakit."'
      AND answer = 'Iya'");

    $response = array(
      'status' => true,
      'lists' => $data,
    );
    return Response::json($response);
  }

  public function fetchHealthData(Request $request)
  {
    $tgl = date('Y-m-d', strtotime($request->get('tanggal')));
    // $q =  'select att.*, groups.remark from
    // (select employee_id, `name`, answer_date, SUM(masuk) lat_in, SUM(masuk1) lng_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar),null) lat_out, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SUM(keluar2),null) lng_out, SEC_TO_TIME(SUM(time_in)) time_in, IF(SUM(id_out) - SUM(id_in) <> 7 AND SUM(jam_out) - SUM(jam_in) > 1, SEC_TO_TIME(SUM(time_out)),null) time_out, TRIM("," FROM GROUP_CONCAT(village)) as village, TRIM("," FROM GROUP_CONCAT(city)) as city from
    // (
    // SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2, id as id_in, 0 as id_out, DATE_FORMAT(created_at, "%H") as jam_in, 0 as jam_out, TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_in, 0 as time_out, village, city FROM quiz_logs
    // WHERE id IN (
    // SELECT MIN(id)
    // FROM quiz_logs
    // GROUP BY employee_id, `name`, answer_date
    // )
    // union all
    // SELECT employee_id, `name`, answer_date, 0 as masuk, 0 as masuk1, latitude as keluar, longitude as keluar2, 0 as id_in, id as id_out, 0 as jam_in,  DATE_FORMAT(created_at, "%H") as jam_out, 0 as time_in,  TIME_TO_SEC(DATE_FORMAT(created_at, "%H:%i")) as time_out, "" as village, "" as city FROM quiz_logs
    // WHERE id IN (
    // SELECT MAX(id)
    // FROM quiz_logs
    // GROUP BY employee_id, `name`, answer_date
    // )
    // ) as semua
    // group by employee_id, `name`, answer_date) as att
    // left join groups on att.employee_id = groups.employee_id AND att.answer_date = groups.tanggal where att.answer_date = "'.$tgl.'"';

    $list = DB::CONNECTION('sunfish')->select("SELECT
      * 
      FROM
      [dbo].[VIEW_AR_YMPI] AS A 
      WHERE
      format ( a.dateTime, 'yyyy-MM-dd' ) = '".$tgl."'");

    $lists = [];

    for ($i=0; $i < count($list); $i++) { 
      $listss = EmployeeSync::where('employee_id',$list[$i]->emp_no)->first();

      $listes = array(
       'employee_id' => $listss->employee_id,
       'name' => $listss->name,
       'date' => date('Y-m-d',strtotime($list[$i]->dateTime)),
       'date_in' => $list[$i]->dateTime,
       'task' => $list[$i]->taskDesc,
       'department' => $listss->department,
       'section' => $listss->section,
       'group' => $listss->group,
       'location' => json_decode($list[$i]->location),
     );
      array_push($lists,$listes);
    }


    $response = array(
      'status' => true,
      'lists' => $lists,
    );
    return Response::json($response);
  }

  public function fetchHealthDataLoc(Request $request)
  {
    $tgl = date('Y-m-d', strtotime($request->get('tanggal')));

    $lists = db::select("SELECT
        greatday_attendances.employee_id,
        `name`,
        date_in,
        task,
        department,
        section,
        `group`,
        startss.time_in AS time_in_start,
        detail_start.latitude AS latitude_start,
        detail_start.longitude AS longitude_start,
        detail_start.village AS village_start,
        detail_start.state AS state_start,
        detail_start.state_district AS state_district_start,
        detail_start.images AS images_start,
        endss.time_in AS time_in_end,
        detail_end.latitude AS latitude_end,
        detail_end.longitude AS longitude_end,
        detail_end.village AS village_end,
        detail_end.state AS state_end,
        detail_end.state_district AS state_district_end,
        detail_end.images AS images_end
      FROM
        greatday_attendances
        LEFT JOIN ( SELECT employee_id, min( time_in ) AS time_in FROM greatday_attendances WHERE DATE( time_in ) = '".$tgl."' GROUP BY employee_id ) AS startss ON startss.employee_id = greatday_attendances.employee_id
        LEFT JOIN ( SELECT employee_id, time_in, latitude, longitude, village, state, state_district,images FROM greatday_attendances WHERE DATE( time_in ) = '".$tgl."' ) AS detail_start ON detail_start.employee_id = greatday_attendances.employee_id 
        AND detail_start.time_in = startss.time_in
        LEFT JOIN ( SELECT employee_id, max( time_in ) AS time_in FROM greatday_attendances WHERE DATE( time_in ) = '".$tgl."' GROUP BY employee_id ) AS endss ON endss.employee_id = greatday_attendances.employee_id
        LEFT JOIN ( SELECT employee_id, time_in, latitude, longitude, village, state, state_district,images FROM greatday_attendances WHERE DATE( time_in ) = '".$tgl."' ) AS detail_end ON detail_end.employee_id = greatday_attendances.employee_id 
        AND detail_end.time_in = endss.time_in 
      WHERE
        DATE( greatday_attendances.time_in ) = '".$tgl."' 
      GROUP BY
        greatday_attendances.employee_id,
        `name`,
        date_in,
        task,
        department,
        section,
        `group`,
        startss.time_in,
        detail_start.latitude,
        detail_start.longitude,
        detail_start.village,
        detail_start.state,
        detail_start.state_district,
        endss.time_in,
        detail_end.latitude,
        detail_end.longitude,
        detail_end.village,
        detail_end.state,
        detail_end.state_district ");

    $emp = EmployeeSync::select('employee_syncs.*','departments.department_shortname')->where('end_date',null)->join('departments','department_name','employee_syncs.department')->get();

    $response = array(
      'status' => true,
      'lists' => $lists,
      'emp' => $emp,
    );
    return Response::json($response);
    
  }

  public function fetchShiftData(Request $request)
  {
    $q =  'select * from groups';

    $response = array(
      'status' => true,
      'lists' => DB::connection('mobile')->select($q),
    );
    return Response::json($response);
  }

  public function display_health(){
    $title = 'Employee Health Report';
    $title_jp = '作業者不良率';

    return view('mirai_mobile.health_report', array(
     'title' => $title,
     'title_jp' => $title_jp
   ))->with('page', 'Employee Health Report');
  }

  public function fetch_health(Request $request){

    $datefrom = date("Y-m-d", strtotime('-30 days'));
    $dateto = date("Y-m-d");

    if(strlen($request->get('datefrom')) > 0){
      $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
    }

    if(strlen($request->get('dateto')) > 0){
      $dateto = date('Y-m-d', strtotime($request->get('dateto')));
    }

    $data = DB::connection('mobile')->select("  
      SELECT
      groups.tanggal,
      count( groups.employee_id ) AS total,
      SUM(
      IF
      ( time( log.created_at ) > '07:00:00' AND time( log.created_at ) <= '08:00:00', 1, 0 )) AS lti,
      SUM(
      IF
      ( time( log.created_at ) > '08:00:00' OR log.created_at IS NULL, 1, 0 )) AS abs,
      SUM(
      IF
      ( time( log.created_at ) <= '07:00:00', 1, 0 )) AS prs
      FROM
      groups
      LEFT JOIN (
      SELECT
      employee_id,
      NAME,
      department,
      answer_date,
      min( created_at ) AS created_at 
      FROM
      quiz_logs 
      GROUP BY
      employee_id,
      NAME,
      department,
      answer_date
      ) AS log ON log.employee_id = groups.employee_id and log.answer_date = groups.tanggal
      WHERE
      groups.remark = 'OFF' 
      AND groups.employee_id NOT IN ( SELECT employee_id FROM `leaves` )
      group by groups.tanggal");
      //per tgl
    // $data = DB::connection('mobile')->select("
    //  select distinct answer_date, 
    //  (select count(employee_id) as emp from employees where end_date is null and keterangan is null) as karyawan,
    //  (select count(employee_id) as emp from employees where end_date is null and keterangan is null) - emplo.mengisi as belum,
    //  emplo.mengisi
    //  from
    //  (select answer_date, count(employee_id) as mengisi from
    //  (select answer_date, quiz_logs.employee_id from quiz_logs left join employees on quiz_logs.employee_id = employees.employee_id where keterangan is null
    //  group by employee_id, answer_date) dd
    //  group by answer_date) emplo");

    $data_sakit = DB::connection('mobile')->select("
      SELECT
      cat.answer_date,
      cat.question,
      IFNULL( ans, 0 ) AS count 
      FROM
      ( SELECT DISTINCT answer_date, question FROM quiz_logs WHERE question <> 'Suhu Tubuh' ) cat
      LEFT JOIN (
      SELECT
      answer_date,
      question,
      count( answer ) ans 
      FROM
      quiz_logs
      LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
      WHERE
      answer = 'Iya' 
      AND keterangan IS NULL 
      AND end_date IS NULL 
      GROUP BY
      question,
      answer_date 
      ) AS tidak ON cat.question = tidak.question 
      AND cat.answer_date = tidak.answer_date
      WHERE cat.answer_date >= '2020-04-15'");

    $cat_sakit = DB::connection('mobile')->select("
      select distinct answer_date, question from quiz_logs where question <> 'Suhu Tubuh' and answer_date >= '2020-04-15'");

    // $q2 = DB::connection('mobile')->select("SELECT employee_id, `name`, answer_date, latitude as masuk, longitude as masuk1, 0 as keluar, 0 as keluar2 FROM `quiz_logs` group by employee_id, `name`, answer_date");

    $year = date('Y');

    $response = array(
      'status' => true,
      'datas' => $data,
      'sakit' => $data_sakit,
      'cat_sakit' => $cat_sakit
    );

    return Response::json($response);
  }

  public function fetchLocationEmployee(Request $request){

    $loc = $this->getLocation($request->get('lat'), $request->get('lng'));

    $loc1 = json_encode($loc);

    $loc2 = explode('\"',$loc1);

    $keyStateDistrict = array_search('state_district', $loc2);
    $keyVillage = array_search('village', $loc2);
    $keyState = array_search('state', $loc2);
    $keyPostcode = array_search('postcode', $loc2);
    $keyCountry = array_search('country', $loc2);

    $data = array(
      'city' => $loc2[$keyStateDistrict + 2],
      'village' => $loc2[$keyVillage + 2],
      'province' => $loc2[$keyState + 2],
      'postcode' => $loc2[$keyPostcode + 2],
      'country' => $loc2[$keyCountry + 2]
    );

    $response = array(
      'status' => true,
      'data' => $data,
    );
    return Response::json($response);

  }

  public function getLocation($lat, $long){

    // $url = "https://locationiq.org/v1/reverse.php?key=pk.456ed0d079b6f646ad4db592aa541ba0&lat=".$lat."&lon=".$long."&format=json";
    // // $url = "https://www.google.com/maps/@".$lat.",".$long."";
    // $curlHandle = curl_init();
    // curl_setopt($curlHandle, CURLOPT_URL, $url);
    // curl_setopt($curlHandle, CURLOPT_HEADER, 0);
    // curl_setopt($curlHandle, CURLOPT_RETURNTRANSFER, 1);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYHOST, 2);
    // curl_setopt($curlHandle, CURLOPT_SSL_VERIFYPEER, 0);
    // curl_setopt($curlHandle, CURLOPT_TIMEOUT,30);
    // curl_setopt($curlHandle, CURLOPT_POST, 1);
    // $results = curl_exec($curlHandle);
    // curl_close($curlHandle);

    // $response = array(
    //  'status' => true,
    //  'data' => $results,
    // );
    // return Response::json($response);

    return "<img src='https://maps.locationiq.com/v3/staticmap?key=pk.456ed0d079b6f646ad4db592aa541ba0&center=".$lat.",".$long."&zoom=14&size=1800x1800&format=png&markers=icon:large-green-cutout|".$lat.",".$long."'>";
  }

  public function location(){
    $tglnow = date('Y-m-d');

    if(Auth::user()->role_code == 'C-HR' || Auth::user()->role_code == 'M-HR' || Auth::user()->role_code == 'S-HR' || Auth::user()->role_code == 'S-MIS' || Auth::user()->role_code == 'S'){

      return view('mirai_mobile.emp_location',  
        array('title' => 'Resume Employee Location', 
          'title_jp' => ''
        )
      )->with('page', 'Resume Employee Location');
    }

    return view('404',  
      array('message' => 'Silahkan menghubungi bagian HR untuk data absensi YMPCOID.'
    )
    );
  }

  public function fetchLocation()
  {

    $employee_location = db::connection('mobile')->select("SELECT act.answer_date, employees.department, count(act.employee_id) as jumlah from
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as act
      left join employees on employees.employee_id = act.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = act.employee_id AND all_groups.tanggal = act.answer_date
      where act.city <> employees.kota and answer_date >= '2020-04-13' and employees.department <> 'Management Information System'
      group by employees.department, answer_date
      ");

    $period = db::table('weekly_calendars')->where('week_date', '>=', '2020-04-13')->where('week_date', '<=', date('y-m-d'))->select('week_date')->orderBy('week_date', 'desc')->get();

    $response = array(
      'status' => true,
      'period' => $period,
      'emp_location' => $employee_location
    );
    return Response::json($response);
  }

  public function fetchLocationDetail(Request $request)
  {
    if ($request->get('department') != "") {
      $dept = "AND employees.department = '".$request->get('department')."'";
    }else{
      $dept = "";
    }

    if ($request->get('date') != "") {
      $date = "AND answer_date = '".$request->get('date')."'";
    }else{
      $date = "";
    }

    $location_detail = db::connection('mobile')->select("SELECT quiz.employee_id, quiz.`name`, quiz.city, employees.kota, employees.department FROM 
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as quiz
      left join employees on employees.employee_id = quiz.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = quiz.employee_id AND all_groups.tanggal = quiz.answer_date
      where quiz.city <> employees.kota ".$date." ".$dept." AND employees.department <> 'Management Information System'
      ");

    $response = array(
      'status' => true,
      'location_detail' => $location_detail
    );
    return Response::json($response);
  }

  public function fetchLocationDetailAll(Request $request)
  {

    if ($request->get('date') != "") {
      $date = "WHERE answer_date = '".$request->get('date')."'";
    }else{
      $date = "";
    }

    $location_detail = db::connection('mobile')->select("SELECT quiz.answer_date,quiz.employee_id, quiz.`name`, quiz.city, employees.kota, employees.department FROM 
      (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
      WHERE id IN (
      SELECT MIN(id)
      FROM quiz_logs
      GROUP BY employee_id, `name`, answer_date
      )) as quiz
      left join employees on employees.employee_id = quiz.employee_id
      join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = quiz.employee_id AND all_groups.tanggal = quiz.answer_date
      ".$date."
      ");

    $response = array(
      'status' => true,
      'location_detail' => $location_detail
    );
    return Response::json($response);
  }

  public function exportList(Request $request){

   $location_detail = db::connection('mobile')->select("SELECT act.answer_date, employees.department, act.employee_id, act.`name`, city, kota from
    (SELECT employee_id, `name`, answer_date, village, city, province FROM quiz_logs
    WHERE id IN (
    SELECT MIN(id)
    FROM quiz_logs
    GROUP BY employee_id, `name`, answer_date
    )) as act
    left join employees on employees.employee_id = act.employee_id
    join (select employee_id, tanggal from groups where remark = 'OFF') all_groups on all_groups.employee_id = act.employee_id AND all_groups.tanggal = act.answer_date
    where act.city <> employees.kota and answer_date >= '2020-04-13'
    and employees.department is not null and employees.department <> 'Management Information System'
    ");

   $data = array(
    'location' => $location_detail
  );

   ob_clean();

   Excel::create('List Lokasi Yang Tidak Sesuai', function($excel) use ($data){
    $excel->sheet('Location', function($sheet) use ($data) {
      return $sheet->loadView('mirai_mobile.location_excel', $data);
    });
  })->export('xlsx');

 }

 public function indication(){
  $tglnow = date('Y-m-d');

  $token = app(MiraiMobileController::class)->ympicoid_api_login();

  $param = '';
  $link = 'fetch/quiz_log';
  $method = 'GET';

  $q = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

  // $q = "Select distinct question from quiz_logs";

  return view('mirai_mobile.report_indication',  
    array(
      'title' => 'Resume Gejala Penyakit Karyawan', 
      'title_jp' => '',
      'question' => $q
    )
  )->with('page', 'Resume Gejala Penyakit Karyawan');
}

public function fetchIndicationData(Request $request)
{
  $tgl = date('Y-m-d', strtotime($request->get('tanggal')));

  $token = app(MiraiMobileController::class)->ympicoid_api_login();

  // $param = '';
  $param = 'tanggal='.$tgl;
  $link = 'fetch/quiz_log_data';
  $method = 'POST';

  $q = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

  // $q = "select
  // quiz_logs.employee_id,
  // quiz_logs.`name`,
  // quiz_logs.`department`,
  // date( quiz_logs.created_at ) AS date,
  // sum(
  // IF
  // ( question = 'Demam' AND answer = 'iya', 1, 0 )) AS Demam,
  // sum(
  // IF
  // ( question = 'Batuk' AND answer = 'iya', 1, 0 )) AS Batuk,
  // sum(
  // IF
  // ( question = 'Pusing' AND answer = 'iya', 1, 0 )) AS Pusing,
  // sum(
  // IF
  // ( question = 'Tenggorokan Sakit' AND answer = 'iya', 1, 0 )) AS Tenggorokan,
  // sum(
  // IF
  // ( question = 'Sesak Nafas' AND answer = 'iya', 1, 0 )) AS Sesak,
  // sum(
  // IF
  // ( question = 'Indera Perasa & Penciuman Terganggu' AND answer = 'iya', 1, 0 )) AS Indera,
  // sum(
  // IF
  // ( question = 'Pernah Berinteraksi dengan Suspect / Positif COVID-19' AND answer = 'iya', 1, 0 )) AS Kontak,
  // ROUND(REPLACE(REPLACE(GROUP_CONCAT(IF(question = 'Suhu Tubuh' AND answer IS NOT NULL, answer, 0 )), 0,''), ',', ''),1) AS Suhu 
  // FROM
  // quiz_logs
  // LEFT JOIN employees ON quiz_logs.employee_id = employees.employee_id 
  // WHERE
  // keterangan IS NULL 
  // AND end_date IS NULL 
  // AND answer_date = '".$tgl."'
  // GROUP BY
  // employee_id,
  // `name`,
  // date( created_at ),
  // department 
  // ORDER BY date";

   // having Demam = 1 or Batuk = 1 or Pusing = 1 or Tenggorokan = 1 or Sesak = 1 or Indera = 1 or Kontak = 1 

  $response = array(
    'status' => true,
    'lists' => $q,
  );
  return Response::json($response);
}

public function indexGuestAssessmentReport()
{
  $title = 'Report Guest Assessment Covid-19';
  $title_jp = '';

  return view('mirai_mobile.guest_assessment', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Guest Assessment')->with('head','Report Guest Assessment');
}

public function fetchGuestAssessmentReport()
{
  try {
    $guest = DB::SELECT("SELECT * from miraimobile.guest_logs order by created_at desc");

    $response = array(
      'status' => true,
      'guest' => $guest
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchGuestAssessmentReportDetail(Request $request)
{
  try {
    $guest = DB::SELECT("
      SELECT * FROM miraimobile.guest_logs where miraimobile.guest_logs.id = '".$request->get('id')."'
      ");

    $response = array(
      'status' => true,
      'guest' => $guest
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexVendorAssessmentReport()
{
  $title = 'Report Vendor Assessment Covid-19';
  $title_jp = '';

  return view('mirai_mobile.vendor_assessment', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Vendor Assessment')->with('head','Report Vendor Assessment');
}

public function fetchVendorAssessmentReport()
{
  try {
    $vendor = DB::SELECT("SELECT * from miraimobile.vendor_logs order by created_at desc");

    $response = array(
      'status' => true,
      'vendor' => $vendor
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexWposReport()
{
  $title = 'Report Work Permit With Enviromental & Safety Analysis';
  $title_jp = '';

  return view('mirai_mobile.wpos_report', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report WPOS')->with('head','Report WPOS');
}

public function fetchWposReport()
{
  try {
    $wpos = DB::SELECT("SELECT * from miraimobile.wpos_logs order by created_at desc");

    $response = array(
      'status' => true,
      'wpos' => $wpos
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchWposReportDetail(Request $request)
{
  try {
    $wpos = DB::SELECT("
      SELECT * FROM miraimobile.wpos_logs where miraimobile.wpos_logs.id = '".$request->get('id')."'
    ");

    $response = array(
      'status' => true,
      'wpos' => $wpos
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchVaksinReport(Request $request)
{
  try {
   $survey =  DB::SELECT("SELECT
    miraimobile.vaksin_surveys.id AS id_survey,
    miraimobile.vaksin_surveys.employee_id,
    employee_syncs.name,
    employee_syncs.department,
    employee_syncs.section,
    employee_syncs.`group`,
    employee_syncs.sub_group,
    miraimobile.vaksin_surveys.tanggal,
    miraimobile.vaksin_surveys.vaksin_1,
    miraimobile.vaksin_surveys.vaksin_2, 
    miraimobile.vaksin_surveys.jenis_vaksin,
    miraimobile.vaksin_surveys.vaksin_3,
    miraimobile.vaksin_surveys.jenis_vaksin_3
    FROM
    miraimobile.vaksin_surveys
    JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id ");

   $response = array(
    'status' => true,
    'survey' => $survey,
  );
   return Response::json($response);
 } catch (\Exception $e) {
  $response = array(
    'status' => false,
    'message' => $e->getMessage()
  );
  return Response::json($response);
}
}

public function fetchVaksinRegistrationReport(Request $request)
{
  try {
   $survey =  DB::SELECT("
    SELECT
    miraimobile.vaksin_register_news.employee_id,
    tanggal,
    miraimobile.vaksin_register_news.`name`,
    miraimobile.vaksin_register_news.department,
    miraimobile.vaksin_register_news.birth_place,
    miraimobile.vaksin_register_news.birth_date,
    miraimobile.vaksin_register_news.card_id,
    miraimobile.vaksin_register_news.address,
    no_hp,
    jumlah_keluarga,
    keluarga_hubungan,
    keluarga_name,
    keluarga_ktp,
    keluarga_birth_place,
    keluarga_birth_date,
    keluarga_address,
    keluarga_no_hp,
    call_vaksin_3
    FROM
    miraimobile.vaksin_register_news
    WHERE remark = 'vaksin_3'
    ");

   $response = array(
    'status' => true,
    'survey' => $survey,
  );
   return Response::json($response);
 } catch (\Exception $e) {
  $response = array(
    'status' => false,
    'message' => $e->getMessage()
  );
  return Response::json($response);
}
}

public function indexVaksinMonitoring()
{
  $title = 'Vaccine Monitoring';
  $title_jp = '';

  return view('mirai_mobile.vaksin_monitoring', array(
    'title' => $title,
    'title_jp' => $title_jp,
  ))->with('page', 'Vaksin Monitoring')->with('head','Vaksin Monitoring');
}

public function fetchVaksinMonitoring(Request $request)
{
  try {

        // $vaksin = DB::SELECT("
        //   SELECT
        //   SUM( a.count_sudah_vaksin ) AS sudah_vaksin,
        //   SUM( a.count_sudah_daftar ) AS sudah_daftar,
        //   (SUM( a.count_all ) - SUM( a.count_sudah_vaksin ) - SUM( a.count_sudah_daftar )) AS belum,
        //   SUM( a.count_all ) as total_karyawan,
        //   a.department,
        //   department_shortname 
        // FROM
        //   (
        // SELECT
        //   count( miraimobile.vaksin_surveys.employee_id ) AS count_sudah_vaksin,
        //   0 AS count_sudah_daftar,
        //   0 AS count_all,
        //   COALESCE ( employee_syncs.department, '' ) AS department 
        // FROM
        //   miraimobile.vaksin_surveys
        //   JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id 
        // WHERE
        //   employee_syncs.employee_id != 'PI1612005' 
        // GROUP BY
        //   employee_syncs.department 

        //   UNION ALL

        //   SELECT
        //   0 AS count_sudah_vaksin,
        //   count( miraimobile.vaksin_register_news.employee_id ) AS count_sudah_daftar,
        //   0 AS count_all,
        //   COALESCE ( employee_syncs.department, '' ) AS department 
        // FROM
        //   miraimobile.vaksin_register_news
        //   JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_register_news.employee_id 
        // WHERE
        //   employee_syncs.employee_id != 'PI1612005' 
        // GROUP BY
        //   employee_syncs.department 

        //   UNION ALL

        // SELECT
        //   0 AS count_sudah_vaksin,
        //   0 AS count_sudah_daftar,
        //   count( employee_syncs.employee_id ) AS count_all,
        //   COALESCE ( employee_syncs.department, '' ) AS department 
        // FROM
        //   employee_syncs
        // WHERE
        //   employee_syncs.end_date IS NULL 
        //   AND employee_syncs.employee_id != 'PI1612005' 
        // GROUP BY
        //   employee_syncs.department
        //   ) a
        //   LEFT JOIN departments ON a.department = departments.department_name 
        // WHERE
        //   a.department != '' 
        // GROUP BY
        //   a.department,
        //   departments.department_shortname
        // ");

    // $vaksin = DB::select("
    //   SELECT
    //   sum( A.sudah_vaksin_pertama ) AS sudah_vaksin_pertama,
    //   sum( A.sudah_vaksin_kedua ) AS sudah_vaksin_kedua,
    //   sum( A.sudah_vaksin_ketiga ) AS sudah_vaksin_ketiga,
    //   sum( A.sudah_daftar ) AS sudah_daftar,
    //   sum( A.belum ) AS belum,
    //   sum( A.sudah_vaksin_pertama ) + sum( A.sudah_vaksin_kedua ) + sum( A.sudah_vaksin_ketiga ) +sum( A.sudah_daftar )+sum( A.belum ) as total_karyawan,
    //   -- D.department_shortname
    //   IF(D.department_shortname is not null, department_shortname, 'MNGT') as department_shortname
    //   FROM
    //   (
    //   SELECT DISTINCT
    //   employee_id,
    //   1 AS sudah_vaksin_pertama,
    //   0 AS sudah_vaksin_kedua,
    //   0 AS sudah_vaksin_ketiga,
    //   0 AS sudah_daftar,
    //   0 AS belum 
    //   FROM
    //   miraimobile.vaksin_surveys
    //   where miraimobile.vaksin_surveys.vaksin_2 is null
    //   and miraimobile.vaksin_surveys.vaksin_3 is null
      
    //   UNION ALL
      
    //   SELECT DISTINCT
    //   employee_id,
    //   0 AS sudah_vaksin_pertama,
    //   1  AS sudah_vaksin_kedua,
    //   0 AS sudah_vaksin_ketiga,
    //   0 AS sudah_daftar,
    //   0 AS belum 
    //   FROM
    //   miraimobile.vaksin_surveys
    //   where miraimobile.vaksin_surveys.vaksin_2 is not null
    //   and miraimobile.vaksin_surveys.vaksin_1 is not null
      
    //   UNION ALL
      
    //   SELECT DISTINCT
    //   employee_id,
    //   0 AS sudah_vaksin_pertama,
    //   0 AS sudah_vaksin_kedua,
    //   1 AS sudah_vaksin_ketiga,
    //   0 AS sudah_daftar,
    //   0 AS belum 
    //   FROM
    //   miraimobile.vaksin_surveys
    //   where miraimobile.vaksin_surveys.vaksin_3 is not null
      
    //   UNION ALL

    //   SELECT DISTINCT
    //   vaksin_register_news.employee_id,
    //   0 AS sudah_vaksin_pertama,
    //   0 AS sudah_vaksin_kedua,
    //   0 AS sudah_vaksin_ketiga,
    //   1 AS sudah_daftar,
    //   0 AS belum 
    //   FROM
    //   miraimobile.vaksin_register_news 
    //   LEFT JOIN miraimobile.vaksin_surveys on miraimobile.vaksin_surveys.employee_id = miraimobile.vaksin_register_news.employee_id
    // WHERE
    //   remark = 'vaksin_3' 
    //   and miraimobile.vaksin_register_news.employee_id NOT LIKE '%OMI%'
    //   and miraimobile.vaksin_surveys.vaksin_3 is null
    //   -- employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys ) 
      
    //   UNION ALL
      
    //   SELECT
    //   employee_id,
    //   0 AS sudah_vaksin_pertama,
    //   0 AS sudah_vaksin_kedua,
    //   0 AS sudah_vaksin_ketiga,
    //   0 AS sudah_daftar,
    //   1 AS belum 
    //   FROM
    //   ympimis.employee_syncs 
    //   WHERE
    //   ympimis.employee_syncs.employee_id NOT IN(SELECT employee_id FROM miraimobile.vaksin_surveys ) 
    //   AND ympimis.employee_syncs.employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_register_news ) 
    //   ) AS A

    //   LEFT JOIN ympimis.employee_syncs AS ES ON A.employee_id = ES.employee_id
    //   LEFT JOIN ympimis.departments AS D ON D.department_name = ES.department 
    //   WHERE
    //   ES.end_date IS NULL 
    //   AND `grade_code` NOT LIKE '%J%'
    //   GROUP BY
    //   D.department_shortname");


    $token = app(MiraiMobileController::class)->ympicoid_api_login();

    $param = '';
    $link = 'fetch/vaksin_monitoring';
    $method = 'GET';
    //Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

    $vaksin = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

    $response = array(
      'status' => true,
      'vaksin' => $vaksin
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}


public function fetchVaksinMonitoringNew(Request $request)
{
  try {
    $vaksin = DB::select("
      SELECT
    sum( A.sudah_vaksin_pertama ) AS sudah_vaksin_pertama,
    sum( A.sudah_vaksin_kedua ) AS sudah_vaksin_kedua,
    sum( A.sudah_vaksin_ketiga ) AS sudah_vaksin_ketiga,
    sum( A.sudah_daftar ) AS sudah_daftar,
    sum( A.belum ) AS belum,
    sum( A.sudah_vaksin_pertama ) + sum( A.sudah_vaksin_kedua ) + sum( A.sudah_vaksin_ketiga ) + sum( A.sudah_daftar )+ sum( A.belum ) AS total_karyawan,-- D.department_shortname
IF
    ( D.department_shortname IS NOT NULL, department_shortname, 'MNGT' ) AS department_shortname
FROM
    (
    SELECT
        card_id,
        ympimis.employee_syncs.`name`,
        1 AS sudah_vaksin_pertama,
        0 AS sudah_vaksin_kedua,
        0 AS sudah_vaksin_ketiga,
        0 AS sudah_daftar,
        0 AS belum
    FROM
        miraimobile.vaksin_surveys
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    WHERE
        ympimis.employee_syncs.card_id != ''
        AND miraimobile.vaksin_surveys.vaksin_2 IS NULL
        AND miraimobile.vaksin_surveys.vaksin_3 IS NULL
    GROUP BY
        card_id,
        ympimis.employee_syncs.`name` UNION ALL
    SELECT
        card_id,
        ympimis.employee_syncs.`name`,
        0 AS sudah_vaksin_pertama,
        1 AS sudah_vaksin_kedua,
        0 AS sudah_vaksin_ketiga,
        0 AS sudah_daftar,
        0 AS belum
    FROM
        miraimobile.vaksin_surveys
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    WHERE
        ympimis.employee_syncs.card_id != ''
        AND miraimobile.vaksin_surveys.vaksin_2 IS NOT NULL
        AND miraimobile.vaksin_surveys.vaksin_1 IS NOT NULL
    GROUP BY
        card_id,
        ympimis.employee_syncs.`name` UNION ALL
    SELECT
        card_id,
        ympimis.employee_syncs.`name`,
        0 AS sudah_vaksin_pertama,
        0 AS sudah_vaksin_kedua,
        1 AS sudah_vaksin_ketiga,
        0 AS sudah_daftar,
        0 AS belum
    FROM
        miraimobile.vaksin_surveys
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    WHERE
        ympimis.employee_syncs.card_id != ''
        AND miraimobile.vaksin_surveys.vaksin_3 IS NOT NULL
    GROUP BY
        card_id,
        ympimis.employee_syncs.`name` UNION ALL
    SELECT DISTINCT
        vaksin_register_news.card_id,
        vaksin_register_news.`name`,
        0 AS sudah_vaksin_pertama,
        0 AS sudah_vaksin_kedua,
        0 AS sudah_vaksin_ketiga,
        1 AS sudah_daftar,
        0 AS belum
    FROM
        miraimobile.vaksin_register_news
        LEFT JOIN miraimobile.vaksin_surveys ON miraimobile.vaksin_surveys.employee_id = miraimobile.vaksin_register_news.employee_id
    WHERE
        remark = 'vaksin_3'
        AND miraimobile.vaksin_register_news.employee_id NOT LIKE '%OMI%'
        AND miraimobile.vaksin_surveys.vaksin_3 IS NULL UNION ALL
    SELECT
        card_id,
        ympimis.employee_syncs.`name`,
        0 AS sudah_vaksin_pertama,
        0 AS sudah_vaksin_kedua,
        0 AS sudah_vaksin_ketiga,
        0 AS sudah_daftar,
        1 AS belum
    FROM
        miraimobile.vaksin_surveys
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    WHERE
        ympimis.employee_syncs.card_id != ''
        AND ympimis.employee_syncs.card_id NOT IN ( SELECT ympimis.employee_syncs.card_id FROM miraimobile.vaksin_surveys JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id )
        AND ympimis.employee_syncs.card_id NOT IN ( SELECT ympimis.employee_syncs.card_id FROM miraimobile.vaksin_register_news JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = miraimobile.vaksin_register_news.employee_id )
    GROUP BY
        card_id,
        ympimis.employee_syncs.`name`
    ) AS A
    LEFT JOIN ympimis.employee_syncs AS ES ON A.card_id = ES.card_id
    LEFT JOIN ympimis.departments AS D ON D.department_name = ES.department
WHERE
    ES.end_date IS NULL
    AND `grade_code` NOT LIKE '%J%'
GROUP BY
    D.department_shortname");

    $response = array(
      'status' => true,
      'vaksin' => $vaksin
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchVaksinMonitoringDetail(Request $request)
{
  try {


    $answer = $request->get('answer');
    $dept = $request->get('dept');

    $department = "";

    if ($dept == "MNGT") {
      $department = "AND employee_syncs.department is null";
    }
    else{
      $department = "AND department_shortname = '".$dept."' ";
    }


    if ($answer == "Belum") {
     
      $vaksin = DB::SELECT("
        SELECT
        employee_syncs.employee_id,
        employee_syncs.name,
        COALESCE(department_shortname,'') as department
        FROM
        employee_syncs
        left join departments on department_name = employee_syncs.department
        WHERE
        end_date IS NULL
        AND employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys )
        AND employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_register_news)
        ".$department."
        and employee_syncs.end_date is null
        AND `grade_code` NOT LIKE '%J%'
        ");

    } else if ($answer == "Sudah Vaksin"){
      $vaksin = DB::SELECT("
        SELECT
        employee_syncs.employee_id,
        employee_syncs.name,
        COALESCE ( department_shortname, '' ) AS department 
        FROM
        miraimobile.vaksin_surveys
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
        left join departments ON department_name = employee_syncs.department 
        WHERE
        employee_syncs.end_date IS NULL
        AND `grade_code` NOT LIKE '%J%'
        ".$department."

        ");
    } else if ($answer == "Sudah Daftar"){
      $vaksin = DB::SELECT("
        SELECT
        employee_syncs.employee_id,
        employee_syncs.name,
        COALESCE ( department_shortname, '' ) AS department 
        FROM
        miraimobile.vaksin_register_news
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_register_news.employee_id
        left join departments ON department_name = employee_syncs.department 
        WHERE
        employee_syncs.employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys )
        ".$department."
        AND employee_syncs.end_date IS NULL
        AND `grade_code` NOT LIKE '%J%'

        ");
    } 
    $response = array(
      'status' => true,
      'vaksin' => $vaksin
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchVaksinMonitoringDetailAll(Request $request)
{
  try {


    $answer = $request->get('answer');

    $token = app(MiraiMobileController::class)->ympicoid_api_login();

    // $param = '';
    $param = 'answer='.$answer;
    $link = 'fetch/vaksin_monitoring/detail/all';
    $method = 'POST';

    $vaksin = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);


    // if ($answer == "Belum") {
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE(department_shortname,'') as department
    //     FROM
    //     employee_syncs
    //     LEFT JOIN departments on department_name = employee_syncs.department
    //     WHERE
    //     end_date IS NULL
    //     AND employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys )
    //     AND employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_register_news)
    //     and employee_syncs.end_date is null
    //     AND `grade_code` NOT LIKE '%J%'
    //     ");
    // } 
    // else if ($answer == "Belum Vaksin All"){
    //   $vaksin = DB::SELECT("
    //      SELECT
    //       employee_syncs.employee_id,
    //       employee_syncs.name,
    //       COALESCE(department_shortname,'') as department
    //       FROM
    //       employee_syncs
    //       LEFT JOIN departments on department_name = employee_syncs.department
    //       WHERE
    //       end_date IS NULL
    //       AND employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys )
    //       and employee_syncs.end_date is null
    //       AND `grade_code` NOT LIKE '%J%'

    //     ");
    // }
    // else if ($answer == "Sudah Vaksin"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // }
    // else if ($answer == "Sudah Vaksin Pertama"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_2 IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_3 IS NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // }

    // else if ($answer == "Sudah Vaksin Kedua"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_1 IS NOT NULL
    //     AND miraimobile.vaksin_surveys.vaksin_2 IS NOT NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // } 

    // else if ($answer == "Belum Vaksin Kedua"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_1 IS NOT NULL
    //     AND miraimobile.vaksin_surveys.vaksin_2 IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_3 IS NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // }

    // else if ($answer == "Sudah Vaksin Ketiga"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_3 IS NOT NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // } 

    // else if ($answer == "Belum Vaksin Ketiga"){
    //   $vaksin = DB::SELECT("
    //     SELECT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_surveys
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_surveys.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE employee_syncs.end_date IS NULL
    //     AND miraimobile.vaksin_surveys.vaksin_3 IS NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // } 

    // else if ($answer == "Sudah Daftar"){
    //   $vaksin = DB::SELECT("
    //      SELECT DISTINCT
    //     employee_syncs.employee_id,
    //     employee_syncs.name,
    //     COALESCE ( department_shortname, '' ) AS department 
    //     FROM
    //     miraimobile.vaksin_register_news
    //   LEFT JOIN miraimobile.vaksin_surveys on miraimobile.vaksin_surveys.employee_id = miraimobile.vaksin_register_news.employee_id
    //     LEFT JOIN employee_syncs ON employee_syncs.employee_id = miraimobile.vaksin_register_news.employee_id
    //     LEFT JOIN departments ON department_name = employee_syncs.department 
    //     WHERE
    //     miraimobile.vaksin_register_news.remark = 'vaksin_3'
    //   and miraimobile.vaksin_surveys.vaksin_3 is null
    //     -- employee_syncs.employee_id NOT IN ( SELECT employee_id FROM miraimobile.vaksin_surveys )
    //     AND employee_syncs.end_date IS NULL
    //     AND `grade_code` NOT LIKE '%J%'

    //     ");
    // }

    $response = array(
      'status' => true,
      'vaksin' => $vaksin
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexVaksinAttendance()
{
  $attend = DB::SELECT("SELECT * FROM miraimobile.general_attendances where type = 'vaksin3' and status is null");

  $title = 'Vaksin Attendance Dosis 3';
  $title_jp = '';

  return view('mirai_mobile.vaksin_attendance', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'attend' => $attend
  ))->with('page', 'Vaksin Attendance')->with('head','Vaksin Attendance');
}

public function fetchVaksinAttendance(Request $request)
{
  try {
    $emp = Employee::select('employees.employee_id','employees.name','miraimobile.general_attendances.no_ktp','miraimobile.general_attendances.keterangan','miraimobile.general_attendances.phone','miraimobile.general_attendances.hubungan_keluarga')->where('tag',$request->get('tag'))->join('miraimobile.general_attendances','miraimobile.general_attendances.employee_id','ympimis.employees.employee_id')->where('miraimobile.general_attendances.status',null)->first();

    if (count($emp) > 0 ) {


      $empsync = EmployeeSync::where('employee_id',$emp->employee_id)->first();

      $cek_vaksin = DB::SELECT("SELECT * from miraimobile.vaksin_surveys where employee_id = '".$emp->employee_id."'");

      if (count($cek_vaksin) == 0) {
          // $vaksin_survey = DB::SELECT("INSERT INTO miraimobile.vaksin_surveys (tanggal,employee_id,name,department,vaksin_1,jenis_vaksin,created_by,created_at,updated_at) VALUES ('".date('Y-m-d')."','".$emp->employee_id."','".$empsync->name."','".$empsync->department."','".date('Y-m-d')."','Sinovac','1','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."')");

          $response = array(
            'status' => false,
            'message' => 'Data Anda Terdeteksi Belum Vaksin Pertama'
          );
          return Response::json($response);
      }else{
        $vaksin_survey = DB::SELECT("UPDATE miraimobile.vaksin_surveys SET vaksin_3 = '".date('Y-m-d')."',updated_at = '".date('Y-m-d H:i:s')."',jenis_vaksin_3 = 'Pfizer' where employee_id = '".$emp->employee_id."'");
      }


      $stat = DB::SELECT("UPDATE miraimobile.general_attendances SET status = 'Hadir_".date('Y-m-d H:i:s')."' where type = 'vaksin3' and employee_id = '".$emp->employee_id."' and hubungan_keluarga = 'karyawan' and status is null");

      $response = array(
        'status' => true,
        'emp' => $emp
      );
      return Response::json($response);
    }else{
      $response = array(
        'status' => false,
        'message' => 'Anda Sudah Pernah Scan / Tidak Terdaftar.'
      );
      return Response::json($response);
    }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchVaksinAttendanceSelect(Request $request)
{
  try {

    $data = explode("_", $request->get('tag')); 

    $emp = DB::SELECT("SELECT name,no_ktp,hubungan_keluarga,phone from miraimobile.general_attendances where type = 'vaksin3' and employee_id = '".$data[0]."' and hubungan_keluarga = '".$data[1]."'");

    $empsync = EmployeeSync::where('employee_id',$data[0])->first();

    if ($data[1] == 'Karyawan') {
      $cek_vaksin = DB::SELECT("SELECT * from miraimobile.vaksin_surveys where employee_id = '".$data[0]."'");
      if (count($cek_vaksin) == 0) {
            $response = array(
            'status' => false,
            'message' => 'Data Anda Terdeteksi Belum Vaksin Pertama'
          );
          return Response::json($response); 
      }else{
        $vaksin_survey = DB::SELECT("UPDATE miraimobile.vaksin_surveys SET vaksin_3 = '".date('Y-m-d')."',updated_at = '".date('Y-m-d H:i:s')."',jenis_vaksin_3 = 'Pfizer' where employee_id = '".$data[0]."'");
      }
    }


    $stat = DB::SELECT("UPDATE miraimobile.general_attendances SET status = 'Hadir_".date('Y-m-d H:i:s')."' where type = 'vaksin3' and employee_id = '".$data[0]."' and hubungan_keluarga = '".$data[1]."' and status is null");

    $response = array(
      'status' => true,
      'emp' => $emp
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchVaksinAttendanceQueue()
{
  try {
    $emp = DB::SELECT("SELECT
        * 
      FROM
        miraimobile.general_attendances 
      WHERE
        type = 'vaksin3' 
        -- and DATE(created_at) = '2022-03-22'
      ORDER BY
        `status` DESC");

    $response = array(
      'status' => true,
      'emp' => $emp
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}


// Peduli Lindungi

public function indexPeduliLindungiReport()
{
  $title = 'Report Peduli Lindungi';
  $title_jp = '';

  return view('mirai_mobile.peduli_lindungi', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Pedulit Lindungi')->with('head','Report Pedulit Lindungi');
}

public function fetchPeduliLindungiReport()
{
  try {
    $peduli_lindungi = DB::SELECT("SELECT * from miraimobile.pedulilindungi_surveys order by created_at desc");

    $response = array(
      'status' => true,
      'peduli_lindungi' => $peduli_lindungi
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}


public function indexFamilyDayReport()
{
    $title = 'Report Family Day';
    $title_jp = '';

    $token = app(MiraiMobileController::class)->ympicoid_api_login();

    $param = '';
    $link = 'fetch/total_family';
    $method = 'GET';
    //Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

    $emp_total = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

  // $emp_total = DB::SELECT("
  //       SELECT
  //         count(id) as jumlah,
  //         sum( CASE WHEN destinasi = 'Opsi 1' THEN 1 ELSE 0 END ) AS destinasi_1,
  //         sum( CASE WHEN destinasi = 'Opsi 2' THEN 1 ELSE 0 END ) AS destinasi_2,
  //         sum( CASE WHEN destinasi = 'Opsi 3' THEN 1 ELSE 0 END ) AS destinasi_3,
  //         sum( CASE WHEN destinasi = 'Opsi 4' THEN 1 ELSE 0 END ) AS destinasi_4,
  //         sum(jumlah_tiket) as jumlah_all,
  //         sum( CASE WHEN destinasi = 'Opsi 1' THEN jumlah_tiket ELSE 0 END ) AS destinasi_all1,
  //         sum( CASE WHEN destinasi = 'Opsi 2' THEN jumlah_tiket ELSE 0 END ) AS destinasi_all2,
  //         sum( CASE WHEN destinasi = 'Opsi 3' THEN jumlah_tiket ELSE 0 END ) AS destinasi_all3,
  //         sum( CASE WHEN destinasi = 'Opsi 4' THEN jumlah_tiket ELSE 0 END ) AS destinasi_all4 
  //       FROM
  //         miraimobile.employee_vacations 
  //       WHERE
  //         destinasi IS NOT NULL 
  // ");

  return view('mirai_mobile.family_day', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'emp_total' => $emp_total
  ))->with('page', 'Report Family Day')->with('head','Report Family Day');
}

public function fetchFamilyDayReport()
{
  try {

    $token = app(MiraiMobileController::class)->ympicoid_api_login();

    $param = '';
    $link = 'fetch/family_day';
    $method = 'GET';

    $family = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

    // $family = DB::SELECT("SELECT * from miraimobile.employee_vacations order by destinasi desc");

    $response = array(
      'status' => true,
      'family' => $family
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}



public function indexVehicleReport()
{
  $title = 'Report Vehicle';
  $title_jp = '';

  return view('mirai_mobile.vehicle', array(
    'title' => $title,
    'title_jp' => $title_jp
  ))->with('page', 'Report Vehicle')->with('head','Report Vehicle');
}

public function fetchVehicleReport()
{
  try {

    $token = app(MiraiMobileController::class)->ympicoid_api_login();

    $param = '';
    $link = 'fetch/vehicle';
    $method = 'GET';

    $vehicle = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

    $response = array(
      'status' => true,
      'vehicle' => $vehicle
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexSloganMutu()
{
  $title = 'Report Slogan Mutu';
  $title_jp = '品質スローガンの報告';

  $fy_all = WeeklyCalendar::select('fiscal_year')->distinct()->orderBy('week_date','desc')->get();
  $emp = EmployeeSync::where('end_date',null)->get();

  return view('mirai_mobile.slogan_mutu', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'fy_all' => $fy_all,
    'fy_all2' => $fy_all,
    'fy_all3' => $fy_all,
    'emp' => $emp,
    'emp2' => $emp,
    'emp3' => $emp,
  ))->with('page', 'Report Slogan Mutu')->with('head','Report Slogan Mutu');
}

public function fetchSloganMutu(Request $request)
{
  try {
    $periode = $request->get('periode');
    if ($periode != '') {
      $fy = $periode;
    }else{
      $fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $fy = $fys->fiscal_year;
    }
    $slogan = DB::SELECT("SELECT
        *,
        SUM_OF_LIST (
        REPLACE ( selection_result, '_', ',' )) AS nilai,
        ROUND(COALESCE (
          COALESCE ( SUM_OF_LIST ( REPLACE ( final_result, '_', ',' )), 0 )/
        IF
          ( LENGTH( final_assessor_id ) > 0, LENGTH( final_assessor_id ) - LENGTH( REPLACE ( final_assessor_id, '_', '' )) + 1, 0 ),
          0 
        ),2) AS nilai_final 
      FROM
        miraimobile.std_slogans
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = std_slogans.employee_id 
      WHERE
        miraimobile.std_slogans.periode = '".$fy."' 
      ORDER BY
        std_slogans.selection_checks DESC,
        nilai_final DESC,
        nilai DESC");

    $assessor = DB::select('select * from miraimobile.std_slogan_assessors where miraimobile.std_slogan_assessors.periode = "'.$fy.'"');

    $response = array(
      'status' => true,
      'slogan' => $slogan,
      'assessor' => $assessor,
      'periode' => $fy,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function inputSloganFinal(Request $request)
{
  try {
    $periode = $request->get('periode');
    if ($periode != '') {
      $fy = $periode;
    }else{
      $fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $fy = $fys->fiscal_year;
    }

    
    if ($request->get('status') == 'Start') {
      $updateslogan = DB::table('miraimobile.std_slogans')->where('periode',$fy)->update([
        'selection_status' => 'Closed',
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }else{
      $updateslogan = DB::table('miraimobile.std_slogans')->where('periode',$fy)->update([
        'final_status' => 'Closed',
        'updated_at' => date('Y-m-d H:i:s')
      ]);
    }

    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function inputSloganSelection(Request $request)
{
  try {
    $periode = $request->get('periode');
    if ($periode != '') {
      $fy = $periode;
    }else{
      $fys = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $fy = $fys->fiscal_year;
    }

    $arr1 = [];
    $arr2 = [];

    $assessor = DB::table('miraimobile.std_slogan_assessors')->where('periode',$fy)->where('category','Selection')->get();

    if (count($assessor) == 0) {
      $response = array(
        'status' => false,
        'message' => 'Asesor belum tersedia.'
      );
      return Response::json($response);
    }

    for ($i=0; $i < count($assessor); $i++) { 
      array_push($arr1, $i);
    }

    $slogan = DB::table('miraimobile.std_slogans')->where('periode',$fy)->get();

    for ($i=0; $i < count($slogan); $i++) { 
      array_push($arr2, $i);
    }

    $arrRes = [];
    foreach($arr1 as $key){
        $arrRes[$key] = [];
    }
    $maxLength = intval(count($arr2) / count($arr1)) + 1;
    $pos = 0;
    for($i = 0; $i < count($arr1); ++$i) {
        $arraysLeftAfter = count($arr1) - $i - 1;
        for($j = 0; $j < $maxLength && $pos < count($arr2); ++$j) {
            if($arraysLeftAfter > 0) {
                $elCountAfter = count($arr2) - $pos - 1;
                $myLengthAfter = ($j + 1);
                $maxLengthAfter = floor(($elCountAfter / $arraysLeftAfter) + 1);
                if($myLengthAfter > $maxLengthAfter) {
                    break;
                }
            }
            $arrRes[$arr1[$i]][] = $arr2[$pos++];
        }
    }

    
    for ($i=0; $i < count($arrRes); $i++) {      
      for ($j=0; $j < count($arrRes[$i]); $j++) { 
        $updateslogan = DB::table('miraimobile.std_slogans')->where('id',$slogan[$arrRes[$i][$j]]->id)->update([
          'selection_assessor_id' => $assessor[$i]->assessor_id,
          'selection_assessor_name' => $assessor[$i]->assessor_name,
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }
    }

    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function indexPkbReport()
{
  $title = 'Report Surat Pernyataan PKB';
  $title_jp = '労働契約の宣言書のリポート';

  // $periode = DB::SELECT("SELECT DISTINCT
  //     ( periode ) 
  //   FROM
  //     miraimobile.pkb_periodes");

  $token = app(MiraiMobileController::class)->ympicoid_api_login();

  $param = '';
  $link = 'fetch/pkb_periode';
  $method = 'GET';

  $datas = app(MiraiMobileController::class)->ympicoid_api_json($token,$link,$method,$param);

  return view('mirai_mobile.report_pkb', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'periode' => $datas,
  ))->with('page', 'Report Surat Pernyataan PKB')->with('head','Report Surat Pernyataan PKB');
}

public function fetchPkbReport(Request $request)
{
  try {
    $token = app(MiraiMobileController::class)->ympicoid_api_login();
    if ($request->get('periode') == '') {

      $param = '';
      $link = 'fetch/pkb_periode';
      $method = 'GET';

      $datas = app(MiraiMobileController::class)->ympicoid_api_json($token,$link,$method,$param);
      $periodes = '';
      for ($i=0; $i < count($datas); $i++) { 
        if ($datas[$i]->status == 'Active') {
          $periodes = $datas[$i]->periode;
        }
      }
      // $periode = DB::SELECT("SELECT DISTINCT
      //   ( periode ) 
      // FROM
      //   miraimobile.pkb_periodes 
      //   where status = 'Active'
      // ORDER BY
      //   periode DESC 
      //   LIMIT 1");
      
    }else{
      $periodes = $request->get('periode');
    }

    $param = 'periode='.$periodes;
    $link = 'fetch/pkb_report';
    $method = 'POST';
    //Contoh $param = 'date_from='.$date_from.'&date_to='.$date_to;

    $datas = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

    // $pkb = DB::SELECT("SELECT
    //     miraimobile.pkbs.id as id_pkb,
    //     miraimobile.pkbs.periode,
    //     miraimobile.pkbs.employee_id,
    //     miraimobile.pkbs.agreement,
    //     miraimobile.pkbs.created_at AS created,
    //     departments.department_shortname,
    //     miraimobile.employee_syncs.* 
    //   FROM
    //     miraimobile.pkbs
    //     JOIN miraimobile.employee_syncs ON miraimobile.employee_syncs.employee_id = miraimobile.pkbs.employee_id
    //     LEFT JOIN departments ON departments.department_name = miraimobile.employee_syncs.department 
    //     where miraimobile.pkbs.periode = '".$periodes."'
    //     and miraimobile.employee_syncs.end_date is null
    //     and miraimobile.employee_syncs.employee_id not like '%OS%' 
    //   ORDER BY
    //     miraimobile.pkbs.created_at DESC");

    $response = array(
      'status' => true,
      'pkb' => $datas
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function printPkbReport($id)
{

  $pkbs = [];
  $pkbs2 = [];
  $index = 1;
  $ids = explode(',',$id);
  for ($i=0; $i < count($ids); $i++) { 
    $pkb = DB::select('select * from miraimobile.pkbs join employee_syncs on employee_syncs.employee_id = miraimobile.pkbs.employee_id where miraimobile.pkbs.id = '.$ids[$i]);
    array_push($pkbs, $pkb);
    // var_dump($index %2);
    if ($index %2 == 0 || $i == (count($ids)-1)) {
      array_push($pkbs2, $pkbs);
      $pkbs = [];
    }
    $index++;
  }

  // return view('mirai_mobile.print_pkb')->with('pkb',$pkbs2);

  $pdf = \App::make('dompdf.wrapper');
  $pdf->getDomPDF()->set_option("enable_php", true);
  $pdf->setPaper('A4', 'potrait');

  $pdf->loadView('mirai_mobile.print_pkb', array(
      'pkb' => $pkbs2,
  ));

  return $pdf->stream("Print PKB.pdf");
}

public function indexMasterPkb()
{
  $title = 'Master Surat Pernyataan PKB';
  $title_jp = '労働契約の宣言書のマスター';

  $token = app(MiraiMobileController::class)->ympicoid_api_login_trial();

  $param = '';
  $link = 'fetch/pkb_periode';
  $method = 'GET';

  $datas = app(MiraiMobileController::class)->ympicoid_api_trial_json($token,$link,$method,$param);

  $param = '';
  $link = 'fetch/pkb_question';
  $method = 'GET';

  $question = app(MiraiMobileController::class)->ympicoid_api_trial_json($token,$link,$method,$param);

  // $periode = DB::SELECT("SELECT *
  //   FROM
  //     miraimobile.pkb_periodes");

  // var_dump($periode);

  // $question = DB::SELECT("select * from miraimobile.pkb_questions");

  return view('mirai_mobile.master_pkb', array(
    'title' => $title,
    'title_jp' => $title_jp,
    'periode' => $datas,
    'periode2' => $datas,
    'periode3' => $datas,
    'question' => $question,
  ))->with('page', 'Master Surat Pernyataan PKB')->with('head','Master Surat Pernyataan PKB');
}

public function updateQuestionPkb(Request $request)
{
  try {
    
    DB::connection('mobile')
    ->table('pkb_questions')
    ->where('id',$request->get('id'))
    ->update([
      'periode' => $request->get('periode'),
      'question' => $request->get('question'),
      'answer' => $request->get('answer'),
      'right_answer' => $request->get('right_answer'),
      'discussion' => $request->get('discussion'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function updatePeriodePkb(Request $request)
{
  try {   
      DB::connection('mobile')
      ->table('pkb_periodes')
      ->where('id',$request->get('id'))
      ->update([
        'periode' => $request->get('periode'),
        'status' => $request->get('status'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);
    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function deletePeriodePkb(Request $request)
{
  try {   
      DB::connection('mobile')
      ->table('pkb_periodes')
      ->where('id',$request->get('id'))
      ->delete();
    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function deleteQuestionPkb(Request $request)
{
  try {   
      DB::connection('mobile')
      ->table('pkb_questions')
      ->where('id',$request->get('id'))
      ->delete();
    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function addPeriodePkb(Request $request)
{
  try {
      
      $insert = DB::
      table('miraimobile.pkb_periodes')
      ->insert([
        'periode' => $request->get('periode'),
        'status' => $request->get('status'),
        'created_by' => 1,
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s'),
      ]);

      if ($insert) {
       $response = array(
          'status' => true,
        );
        return Response::json($response); 
      }else{
        $response = array(
          'status' => false,
        );
        return Response::json($response);
      }
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function fetchQuestionPkb(Request $request)
{
  try {
    $question = DB::connection('mobile')
    ->table('pkb_questions')
    ->where('id',$request->get('id'))
    ->first();

    $response = array(
      'status' => true,
      'question'=> $question
    );
    return Response::json($response); 
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}

public function addQuestionPkb(Request $request)
{
  try {
    DB::connection('mobile')
    ->table('pkb_questions')
    ->insert([
      'periode' => $request->get('periode'),
      'question' => $request->get('question'),
      'answer' => $request->get('answer'),
      'right_answer' => $request->get('right_answer'),
      'discussion' => $request->get('discussion'),
      'created_by' => 1,
      'created_at' => date('Y-m-d H:i:s'),
      'updated_at' => date('Y-m-d H:i:s'),
    ]);
    $response = array(
      'status' => true,
    );
    return Response::json($response);
  } catch (\Exception $e) {
    $response = array(
      'status' => false,
      'message' => $e->getMessage()
    );
    return Response::json($response);
  }
}


    public function indexFamilyDayAttendance()
    {
        $title = 'Family Day Attendance';
        $title_jp = '';

        return view('mirai_mobile.family_day_attendance', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Family Day')->with('head', 'Family Day');
    }

    public function fetchFamilyDayAttendance(Request $request)
    {

        try {
            // $emp = Employee::select('miraimobile.employee_vacations.*')
            // ->join('miraimobile.employee_vacations', 'miraimobile.employee_vacations.employee_id', 'employees.employee_id')
            // ->where('tag', $request->get('tag'))
            // ->Orwhere('miraimobile.employee_vacations.employee_id', $request->get('tag'))
            // ->first();

            $token = app(MiraiMobileController::class)->ympicoid_api_login();

            $emp = Employee::select('employee_id')
              ->where('tag', $request->get('tag'))
              ->Orwhere('employee_id', $request->get('tag'))
              ->first();

            if (count($emp) > 0) {
                // $param = '';
                $link = 'fetch/family_attendance';
                $method = 'POST';
                $param = 'employee_id='.$emp->employee_id;

                $emp_total = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

                if ($emp_total->data) {

                  if ($emp_total->data->attend_date != null) {
                      $response = array(
                          'status' => false,
                          'message' => 'Anda Sudah Mengambil Tiket',
                      );
                      return Response::json($response);
                  } else {

                    // $param = '';
                    $link = 'attendance/family_day';
                    $method = 'POST';
                    $param = 'employee_id='.$emp->employee_id;

                    $update_stat = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

                    // $stat = DB::SELECT("UPDATE miraimobile.employee_vacations SET attend_date = '" . date('Y-m-d H:i:s') . "' where employee_id = '" . $emp->employee_id . "'");

                    $response = array(
                      'status' => true,
                      'emp' => $emp_total,
                    );
                    return Response::json($response);
                  }
                }
                else{
                  $response = array(
                      'status' => false,
                      'message' => 'Anda Tidak Terdaftar',
                  );
                  return Response::json($response);
                }
            } else {

                $response = array(
                    'status' => false,
                    'message' => 'Anda Sudah Mengambil Tiket',
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }


    public function fetchFamilyDayAttendanceQueue()
    {
        try {

            $token = app(MiraiMobileController::class)->ympicoid_api_login();

            $param = '';
            $link = 'fetch/family_day/queue';
            $method = 'GET';

            $emp = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

            // $emp = DB::SELECT("
            // SELECT *
            //    FROM
            //    miraimobile.employee_vacations
            //    WHERE
            //    destinasi is not null
            //    ORDER BY
            //    `attend_date`
            //    DESC
            // ");

          
            $response = array(
                'status' => true,
                'emp' => $emp
            );
            return Response::json($response);

        } catch (\Exception$e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function indexVehicleAttendance($id)
    {
        $title = 'Vehicle Attendance';
        $title_jp = '';
        $fix = '';

        if ($id == "motor") {
          $fix = "Motor";
        } else if ($id == "mobil"){
          $fix = "Mobil";
        }

        return view('mirai_mobile.vehicle_attendance', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'id' => $fix
        ))->with('page', 'Vehicle Attendance')->with('head', 'Vehicle Attendance');
    }

    public function fetchVehicleAttendance(Request $request)
    {

        try {
            // $emp = Employee::select('miraimobile.employee_vacations.*')
            // ->join('miraimobile.employee_vacations', 'miraimobile.employee_vacations.employee_id', 'employees.employee_id')
            // ->where('tag', $request->get('tag'))
            // ->Orwhere('miraimobile.employee_vacations.employee_id', $request->get('tag'))
            // ->first();

            $token = app(MiraiMobileController::class)->ympicoid_api_login();

            $emp = Employee::select('employee_id')
              ->where('tag', $request->get('tag'))
              ->Orwhere('employee_id', $request->get('tag'))
              ->first();

            if (count($emp) > 0) {
                // $param = '';
                $link = 'fetch/vehicle_attendance';
                $method = 'POST';
                $param = 'employee_id='.$emp->employee_id.'&id='.$request->get('id');

                $emp_total = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

                $errors = [];
                if (count($emp_total->data) > 0) {
                  for ($i=0; $i < count($emp_total->data); $i++) { 
                    if ($emp_total->data[$i]->attend_date != null) {
                      array_push($errors, "Sudah Mengambil tiket ke ".$i);
                    } else {

                      $link = 'attendance/vehicle';
                      $method = 'POST';
                      $param = 'employee_id='.$emp->employee_id.'&id='.$request->get('id');

                      $update_stat = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

                      array_push($errors, "Berhasil Mengambil tiket ke ".$i);
                    }
                  }

                  $response = array(
                      'status' => true,
                      'message' => $errors,
                      'emp' => $emp_total->data
                  );
                  return Response::json($response);
                }
                else{
                  $response = array(
                      'status' => false,
                      'message' => 'Anda Tidak Terdaftar / Sudah Dibagi',
                  );
                  return Response::json($response);
                }
            } else {

                $response = array(
                    'status' => false,
                    'message' => 'Anda Sudah Mengambil Sticker',
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }


    public function fetchVehicleAttendanceQueue(Request $request)
    {
        try {
            $token = app(MiraiMobileController::class)->ympicoid_api_login();
            
            $param = 'id='.$request->get('id');
            $link = 'fetch/vehicle/queue';
            $method = 'POST';

            $emp = app(MiraiMobileController::class)->ympicoid_api($token,$link,$method,$param);

          
            $response = array(
                'status' => true,
                'emp' => $emp
            );
            return Response::json($response);

        } catch (\Exception$e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);

        }
    }

    public function indexMonitoringAll(){
      return view('mirai_mobile.cool_finding',  
       array(
         'title' => 'Cool Finding Monitoring', 
         'title_jp' => ''
       )
     )->with('page', 'Audit Patrol Monitoring');
    }


    public function fetchMonitoringAll(Request $request){

      $datefrom = date("Y-m-d",  strtotime('-30 days'));
      $dateto = date("Y-m-d");

      $first = date("Y-m-d", strtotime('-30 days'));
      $location = "";

      if(strlen($request->get('date_from')) > 0){
        $datefrom = date('Y-m-d', strtotime($request->get('date_from')));
      }

      if(strlen($request->get('date_to')) > 0){
        $dateto = date('Y-m-d', strtotime($request->get('date_to')));
      }


      $data_bulan = db::select("
        SELECT
        MONTHNAME(tanggal) as bulan,
        year(tanggal) as tahun,
        sum( CASE WHEN (status_ditangani IS NULL) THEN 1 ELSE 0 END ) AS jumlah_belum,
        sum( CASE WHEN status_ditangani = 'progress' THEN 1 ELSE 0 END ) AS jumlah_progress,
        sum( CASE WHEN (status_ditangani = 'close') THEN 1 ELSE 0 END ) AS jumlah_sudah
        FROM
        miraimobile.employee_findings 
        WHERE
        tanggal >= '".$datefrom."' and tanggal <= '".$dateto."'
        GROUP BY
        tahun,monthname(tanggal)
        order by tahun, month(tanggal) ASC"
      );

      $response = array(
        'status' => true,
        'data_bulan' => $data_bulan
      );

      return Response::json($response);
    }


public function fetchTableAuditAll(Request $request)
{

  $tanggal = "";
  if (strlen($request->get('date_from')) > 0)
  {

    $date_from = date('Y-m-d', strtotime($request->get('date_from')));
    $tanggal = "and tanggal = '".$date_from."'";

    if (strlen($request->get('date_to')) > 0) {

      $date_from = date('Y-m-d', strtotime($request->get('date_from')));
      $date_to = date('Y-m-d', strtotime($request->get('date_to')));

      $tanggal = "and tanggal >= '".$date_from."'";
      $tanggal = $tanggal . "and tanggal  <= '" .$date_to."'";
    }
  }

  $data = db::select("select * from miraimobile.employee_findings where miraimobile.employee_findings.deleted_at is null ".$tanggal."  ");

  $response = array(
    'status' => true,
    'datas' => $data
  );

  return Response::json($response); 
}


    public function ympicoid_api($token,$link,$method,$param)
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://36.94.7.203:8000/api/'.$link);
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer '.$token
      ));
      if ($param != '') {
        curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
      }

      $data = curl_exec($curl);
      $datas = json_decode($data);

      curl_close($curl);

      return $datas;
    }

    public function ympicoid_api_json($token,$link,$method,$param)
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://36.94.7.203:8000/api/'.$link);
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
      ));
      if ($param != '') {
        curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
      }

      $data = curl_exec($curl);
      $datas = json_decode($data);

      curl_close($curl);

      return $datas;
    }

    public function ympicoid_api_login()
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://36.94.7.203:8000/api/login');
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  'POST');
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
      ));
      curl_setopt($curl,CURLOPT_POSTFIELDS,'username=PI1910002&password=Synyster123');

      $login = curl_exec($curl);

      curl_close($curl);
      
      $login_decode = json_decode($login);

      return $login_decode->access_token;
    }

    public function ympicoid_api_trial($token,$link,$method,$param)
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://10.109.33.10:8000/api/'.$link);
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer '.$token
      ));
      if ($param != '') {
        curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
      }

      $data = curl_exec($curl);
      $datas = json_decode($data);

      curl_close($curl);

      return $datas;
    }

    public function ympicoid_api_trial_json($token,$link,$method,$param)
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://10.109.33.10:8000/api/'.$link);
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  $method);
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/json',
        'Authorization: Bearer '.$token
      ));
      if ($param != '') {
        curl_setopt($curl,CURLOPT_POSTFIELDS,$param);
      }

      $data = curl_exec($curl);
      $datas = json_decode($data);

      curl_close($curl);

      return $datas;
    }

    public function ympicoid_api_login_trial()
    {
      $curl = curl_init();

      curl_setopt($curl,CURLOPT_URL,  'http://10.109.33.10:8000/api/login');
      curl_setopt($curl,CURLOPT_RETURNTRANSFER,  true);
      curl_setopt($curl,CURLOPT_ENCODING,  '');
      curl_setopt($curl,CURLOPT_MAXREDIRS,  10);
      curl_setopt($curl,CURLOPT_TIMEOUT,  0);
      curl_setopt($curl,CURLOPT_FOLLOWLOCATION,  true);
      curl_setopt($curl,CURLOPT_HTTP_VERSION,  CURL_HTTP_VERSION_1_1);
      curl_setopt($curl,CURLOPT_CUSTOMREQUEST,  'POST');
      curl_setopt($curl,CURLOPT_HTTPHEADER, array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
      ));
      curl_setopt($curl,CURLOPT_POSTFIELDS,'username=PI1910002&password=Synyster123');

      $login = curl_exec($curl);

      curl_close($curl);
      
      $login_decode = json_decode($login);

      return $login_decode->access_token;
    }

}