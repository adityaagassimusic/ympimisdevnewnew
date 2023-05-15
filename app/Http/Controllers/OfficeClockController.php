<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\PushBlockMaster;
use App\PushBlockRecorder;
use App\PushBlockRecorderTemp;
use App\PushBlockRecorderResume;
use App\CodeGenerator;
use App\User;
use App\PlcCounter;
use App\Libraries\ActMLEasyIf;
use Response;
use DataTables;
use Excel;
use File;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\Visitor;

class OfficeClockController extends Controller
{
    public function __construct()
    {
      // $this->middleware('auth');
    }

    public function kosongan()
    {
    	$datetime = date('Y-m-d H:i:s');
    	$date = date('H:i');
    	$date2 = date('H:i:s');
    	$hour = date('H');
    	$minute = date('i');
    	$second = date('s');
    	$response = array(
			'status' => true,
			'date' => $date,
			'date2' => $date2,
			'hour' => $hour,
			'minute' => $minute,
			'second' => $second,
			'datetime' => $datetime,
		);
		return Response::json($response);
    }

    public function index()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s');
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	$dayTitle = date("l", strtotime(date('Y-m-d')));
    	return view('displays.office_clock')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date)->with('dayTitle',$dayTitle);
    }

    public function fetchVisitor()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->where('location','Security')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->where('destination','Office')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Security' 
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Lobby' 
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'dateTitle' => $dateTitle,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'dateTitle' => $dateTitle,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false,
				'dateTitle' => $dateTitle,
			);
			return Response::json($response);
    	}
    }

    public function index2()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	$dayTitle = date("l", strtotime(date('Y-m-d')));
    	return view('displays.office_clock2')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date)->with('dayTitle',$dayTitle);
    }

    public function fetchVisitor2()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor2')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby2')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->where('location','Security')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->where('destination','Office')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Security' 
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Lobby' 
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'dateTitle' => $dateTitle,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'dateTitle' => $dateTitle,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false,
				'dateTitle' => $dateTitle,
			);
			return Response::json($response);
    	}
    }

    public function index3()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	$dayTitle = date("l", strtotime(date('Y-m-d')));
    	return view('displays.office_clock3')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date)->with('dayTitle',$dayTitle);
    }

    public function fetchVisitor3()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor3')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby3')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->where('location','Security')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->where('destination','Office')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Security' 
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Lobby' 
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'dateTitle' => $dateTitle,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'dateTitle' => $dateTitle,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false,
				'dateTitle' => $dateTitle,
			);
			return Response::json($response);
    	}
    }

    public function index4()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	$dayTitle = date("l", strtotime(date('Y-m-d')));
    	return view('displays.office_clock4')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date)->with('dayTitle',$dayTitle);
    }

    public function fetchVisitor4()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor4')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby4')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->where('location','Security')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->where('destination','Office')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Security' 
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Lobby' 
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'dateTitle' => $dateTitle,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'dateTitle' => $dateTitle,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false,
				'dateTitle' => $dateTitle,
			);
			return Response::json($response);
    	}
    }

    public function index5()
    {
    	date_default_timezone_set('Asia/Jakarta');
		$date = date('m/d/Y H:i:s a', time());
    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));
    	$dayTitle = date("l", strtotime(date('Y-m-d')));
    	return view('displays.office_clock5')->with('page', 'Clock')->with('head', 'Clock')->with('dateTitle',$dateTitle)->with('date',$date)->with('dayTitle',$dayTitle);
    }

    public function fetchVisitor5()
    {
    	$plc = PlcCounter::where('origin_group_code','visitor5')->first();
    	$plc_lobby = PlcCounter::where('origin_group_code','visitor_lobby5')->first();

    	$counter = $plc->plc_counter;
    	$id_plc = $plc->id;

    	$counter_lobby = $plc_lobby->plc_counter;
    	$id_plc_lobby = $plc_lobby->id;

    	$visitor = Visitor::where('destination','Office')->where('location','Security')->get();
    	$visitor_lobby = Visitor::where('location','Lobby')->where('destination','Office')->get();

    	$jumlahvisitor = count($visitor);
    	$jumlahvisitor_lobby = count($visitor_lobby);

    	if ($jumlahvisitor != $counter) {
    		$visitors = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Security' 
			ORDER BY
				visitors.id DESC 
				LIMIT 1
			");

			$plccounter = PlcCounter::find($id_plc);
			$plccounter->plc_counter = $jumlahvisitor;
			$plccounter->save();
    	}

    	if ($jumlahvisitor_lobby != $counter_lobby) {
    		$visitors_lobby = DB::SELECT("
			    		SELECT
				company,
				`name`,
				COALESCE ( department_shortname, employee_syncs.position ) AS department,
				location 
			FROM
				`visitors`
				JOIN employee_syncs ON employee_syncs.employee_id = employee
				LEFT JOIN departments ON employee_syncs.department = department_name 
			WHERE
				destination = 'Office' 
				AND location = 'Lobby' 
			ORDER BY
				visitors.updated_at DESC 
				LIMIT 1
			");

			$plccounter_lobby = PlcCounter::find($id_plc_lobby);
			$plccounter_lobby->plc_counter = $jumlahvisitor_lobby;
			$plccounter_lobby->save();
    	}

    	$dateTitle = date("l, d F Y", strtotime(date('Y-m-d')));

    	if (isset($visitors)) {
    		$response = array(
				'status' => true,
				'visitors' => $visitors,
				'dateTitle' => $dateTitle,
				'visitors_lobby' => '',
			);
			return Response::json($response);
    	}else if(isset($visitors_lobby)){
    		$response = array(
				'status' => true,
				'visitors_lobby' => $visitors_lobby,
				'dateTitle' => $dateTitle,
				'visitors' => '',
			);
			return Response::json($response);
    	}else{
    		$response = array(
				'status' => false,
				'dateTitle' => $dateTitle,
			);
			return Response::json($response);
    	}
    }

    public function guest_room()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/".$cityId."?apikey=".$apiKey."&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = json_decode($response);
		$currentTime = time();

    	$dateTitle = date("d F Y", strtotime(date('Y-m-d')));
    	return view('displays.guest_room')
    	->with('page', 'Clock')
    	->with('head', 'Clock')
    	->with('dateTitle',$dateTitle)
    	->with('weather',$weather)
    	->with('currentTime',$currentTime);
    }

    public function fetchWeather()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/".$cityId."?apikey=".$apiKey."&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = json_decode($response);
		$currentTime = time();

		$response = array(
			'status' => true,
			'weather' => $weather,
			'currentTime' => $currentTime,
		);
		return Response::json($response);
    }

    public function guest_room2()
    {
    	$apiKey = "GYhzWhGEkprqemup8Ps7VVts2jrt9kU8";
		$cityId = "208971";
		$googleApiUrl = "http://dataservice.accuweather.com/forecasts/v1/daily/1day/208971?apikey=GYhzWhGEkprqemup8Ps7VVts2jrt9kU8&language=en-us&details=true&metric=true";

		$ch = curl_init();

		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $googleApiUrl);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$response = curl_exec($ch);

		curl_close($ch);
		$weather = $response;
		$currentTime = time();

    	$dateTitle = date("d F Y", strtotime(date('Y-m-d')));
    	return view('displays.guest_room2')
    	->with('page', 'Clock')
    	->with('head', 'Clock')
    	->with('dateTitle',$dateTitle)
    	->with('weather',$weather)
    	->with('currentTime',$currentTime);
    }
}
