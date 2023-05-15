<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Yajra\DataTables\Exception;
use Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use DataTables;
use Carbon\Carbon;
use App\TagMaterial;
use App\MiddleInventory;
use Illuminate\Support\Facades\Storage;
use App\BarrelQueue;
use File;
use App\Barrel;
use App\BarrelQueueInactive;
use App\BarrelLog;
use App\BarrelMachine;
use App\BarrelMachineLog;
use App\CodeGenerator;
use App\MiddleNgLog;
use App\MiddleLog;
use App\ErrorLog;
use App\Material;
use App\Employee;
use App\Mail\SendEmail;
use App\RfidBuffingInventory;
use Illuminate\Support\Facades\Mail;
use App\KnockDown;
use App\KnockDownDetail;
use App\TransactionTransfer;
use App\EmployeeSync;
use App\AccPurchaseOrderDetail;
use App\Libraries\ActMLEasyIf;
use Excel;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Illuminate\Support\Facades\Cache;

class TrialController extends Controller{


	private $storage_location;
	public function __construct()
	{
		Cache::remember('tes', 10000, function() {
			return Employee::all();
		});
	}

	 public function print_qr()
    {
        $printer_name = 'Plating2';
        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        // $code = [
        //     'INDM220820063',
        //     'INDM221017143',
        //     'INDM221018213',
        //     'INDM221018214',
        //     'INDM221226001',
        //     'INDM221226002',
        //     'INDM221227001',
        //     'INDM221227002',
        //     'INDM221227003',
        //     'INDM221227004',
        //     'INDM221227005',
        //     'INDM230203086',
        //     'INDM230203087',
        //     'INDM230203088',
        //     'INDM230203093',
        // ];

        // $material_description = [
        //     'NaCN 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'NaCN 50KG/UN CAN',
        //     'NaCN 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'KCN(POTASSIUM CYANIDE) 50KG/UN CAN',
        //     'NaCN 50KG/UN CAN',
        // ];


        $code = [
            'INDM220820062',
        ];

        $material_description = [
            'NaCN 50KG/UN CAN',
        ];

        for ($i = 0; $i < count($code); $i++) {
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->feed(1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->qrCode($code[$i], Printer::QR_ECLEVEL_L, 10, Printer::QR_MODEL_2);
            $printer->text($code[$i] . "\n");
            $printer->text($material_description[$i] . "\n");
            $printer->feed(2);

            $printer->cut();
            $printer->close();

        }

    }

	public function trialView(){
		return view('test');
	}

	public function indexVideoPakUra(){
		$title = 'Video Pak Ura';
		$title_jp = '';

		return view('trials.video_pak_ura', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'title_jp' => $title_jp,
		))->with('page', 'Video Pak Ura')->with('head', 'Stock Taking');
	}

	public function adjustShipment(){

		$count = 0;

		$cek = db::select("SELECT value1 AS id, value2 AS `loop` FROM ikhlas");

		DB::beginTransaction();
		for ($i=0; $i < count($cek); $i++) {
			for ($j=0; $j < $cek[$i]->loop; $j++) {

				$kdd = KnockDownDetail::where('shipment_schedule_id', $cek[$i]->id)
				->orderBy('kd_number', 'DESC')
				->first();

				try {

					$kdd->shipment_schedule_id = null;
					$kdd->save();

					$count++;

				} catch (Exception $e) {
					DB::rollback();
					echo $e->getMessage();
					exit;					
				}

			}			
		}

		DB::commit();

		echo $count;

	}


	public function fetchVideoPakUra()
	{
		$time = date('H:i:s', strtotime('13:57:30'));

		$response = array(
			'status' => true,
			'time' => $time,
		);
		return Response::json($response);

		// if (date('Y-m-d H:i:s') == $time) {
		// 	return "url('vid/pak_ura.mp4')";
		// } else {
		// 	return '';
		// }
	}


	public function test_kanban(){

		$pdf = \App::make('dompdf.wrapper');
		$pdf->getDomPDF()->set_option("enable_php", true);
		$pdf->setPaper('CR-80', 'portrait');
		$pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

		$pdf->loadView('kd.label.buffing_sx');
		return $pdf->stream("kanban.pdf");

	}



	public function ymes_functions(){

		$materials = db::table('materials')
		->where('hpl', 'TANPO')
		->where('category', 'KD')
		->select('material_number')
		->get();

		$material_number = [];
		foreach ($materials as $key) {
			array_push($material_number, $key->material_number);
		}


		$cek = db::table('material_plant_data_lists')
		->whereIn('material_number', $material_number)
		->get();



		$ymes_pr = db::connection('ymes')
		->table('vd_mes0010')
		->leftJoin('vm_item0010', 'vm_item0010.item_code', '=', 'vd_mes0010.item_code')
		->where('vd_mes0010.stockqty', '<>', 0)
		->orWhere('vd_mes0010.inspect_qty', '<>', 0)
		->orWhere('vd_mes0010.keep_qty', '<>', 0)
		->select(
			'vd_mes0010.item_code',
			'vm_item0010.item_name',
			'vd_mes0010.location_code',
			'vd_mes0010.stockqty',
			'vd_mes0010.inspect_qty',
			'vd_mes0010.keep_qty'
		)
		->get();

		return response()->json($cek);


	}

	public function tos() {

		foreach(glob(public_path('images\material\*.*') ) as $filename){
		}

	}


	function containsObject($obj, $list) {
		for ($i = 0; $i < count($list); $i++) {
			if ($list[$i] === $obj) {
				return $i;
			}
		}
		return false;
	}

	public function home_new(){
		return view('home_new');
	}

	public function trialymes(){
		$tes = db::connection('ymes')->table('vd_mes0010')->get();

		dd($tes[0]);
	}

	public function testCs($barcode_number){

		$server = $_SERVER['SERVER_ADDR'];
		$url = '';
		if($server == '10.109.52.4' || $server == '10.109.52.3'){
			$url = 'http://10.109.52.4/kitto/public/';
		}else if($server == '10.109.52.1'){
			$url = 'http://10.109.52.1:887/kittodev/public/';
		}

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url . 'completions',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'barcode_number='.$barcode_number,
		));

		$response = json_decode(curl_exec($curl));

		return Response::json($response);
	}

	public function testGms($barcode_number){

		$server = $_SERVER['SERVER_ADDR'];
		$url = '';
		if($server == '10.109.52.4' || $server == '10.109.52.3'){
			$url = 'http://10.109.52.4/kitto/public/';
		}else if($server == '10.109.52.1'){
			$url = 'http://10.109.52.1:887/kittodev/public/';
		}

		$curl = curl_init();
		curl_setopt_array($curl, array(
			CURLOPT_URL => $url . 'transfers',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'barcode_number='.$barcode_number,
		));

		$response = json_decode(curl_exec($curl));

		return Response::json($response);
	}

	public function generateAtt(){

		$data = db::select("SELECT value1 AS employee_id, value2 AS start_date, value3 AS finish_date FROM aga");

		for ($i=0; $i < count($data); $i++) {
			$date = db::select("SELECT * FROM weekly_calendars WHERE week_date BETWEEN '".$data[$i]->start_date."' AND '".$data[$i]->finish_date."'");

			for ($j=0; $j < count($date); $j++) {
				$insert = db::table('attendances')
				->insert([
					'employee_id' => $data[$i]->employee_id,
					'date' => $date[$j]->week_date,
					'attend_code' => 'COVID' 
				]);
			}
		}
	}

	public function indexPhSensor()
	{
		return view('trials.trial_ph');
	}

	public function ocr(){
		// ini_set('display_errors', 1); 
		// ini_set('display_startup_errors', 1); 
		// error_reporting(E_ALL);

		// $image = url('text.png');

		// echo $image;

		echo (new TesseractOCR('text.png'))
		->run();
		// echo (new TesseractOCR('E:\xampp\htdocs\miraidev\public\text.png'))
		// ->run();
	}


	public function fetchPhSensor(Request $request)
	{
		DB::connection()->enableQueryLog();

		$data_sensor = db::table('sensor_datas')->where('category', '=', 'Ph')
		->where(db::raw('DATE_FORMAT(data_time, "%Y-%m-%d")'), '=', $request->get('date'))
		->select(db::raw('DATE_FORMAT(data_time, "%Y-%m-%d %H:%i") as data_time2'), db::raw('MAX(sensor_value) sensor_value'))
		->groupBy('data_time2')
		->orderBy('data_time2', 'asc')
		->get();

		$response = array(
			'status' => true,
			'data_sensor' => $data_sensor,
			'query' => DB::getQueryLog()
		);

		return Response::json($response);
	}

	public function forcePrint(){

		$location = 'CASE';
		$kd_number = 'KDADD001';
		$quantity = '10';
		$material_number = 'ZW39580';
		$material_description = 'CLC-550AL//G W/SLS-205(NEW)';

		$printer_name = 'MIS';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Storage Location:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(3, 3);
		$printer->text(strtoupper($location."\n"));
		$printer->initialize();

		$printer->setUnderline(true);
		$printer->text('KDO:');
		$printer->feed(1);
		$printer->setUnderline(false);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($kd_number."\n");

		$printer->feed(2);
		$printer->initialize();
		$printer->text("GMC     | Description                     | Qty ");
		$qty = $this->writeString($quantity, 4, ' ');
		$material_description = substr($material_description, 0,31);
		$material_description = $this->writeString($material_description, 31, ' ');
		$printer->text($material_number." | ".$material_description." | ".$qty);

		$printer->feed(3);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Qty:             |Qty:            |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|Production       |Logistic        |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->text("Total Qty: ". $qty ."\n");
		$printer->feed(3);
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function minkd(){

		$aga = db::select("SELECT value1, value2 FROM aga");

		for ($i=0; $i < count($aga); $i++) {

			$qty = $aga[$i]->value2;

			$ps = db::table('production_schedules')
			->where(db::raw("DATE_FORMAT(due_date,'%Y-%m')"), '2021-01')
			->where('material_number', $aga[$i]->value1)
			->orderBy('due_date', 'DESC')
			->get();

			for ($j=0; $j < count($ps); $j++) {

				$sch = db::table('production_schedules')
				->where('material_number', $ps[$j]->material_number)
				->where('due_date', $ps[$j]->due_date)
				->first();

				if($sch->quantity >= $qty){
					db::table('production_schedules')
					->where('material_number', $ps[$j]->material_number)
					->where('due_date', $ps[$j]->due_date)
					->update([
						'quantity' => $sch->quantity - $aga[$i]->value2,
						'created_by' => 4321
					]);

					$asd = db::table('aga')
					->where('value1', $ps[$j]->material_number)
					->first();

					$update_aga = db::table('aga')
					->where('value1', $ps[$j]->material_number)
					->update([
						'value3' => $asd->value3 + $aga[$i]->value2
					]);


					$qty -= $aga[$i]->value2;

				}else{

					db::table('production_schedules')
					->where('material_number', $ps[$j]->material_number)
					->where('due_date', $ps[$j]->due_date)
					->update([
						'quantity' => $sch->quantity - $sch->quantity,
						'created_by' => 4321
					]);

					$asd = db::table('aga')
					->where('value1', $ps[$j]->material_number)
					->first();

					$update_aga = db::table('aga')
					->where('value1', $ps[$j]->material_number)
					->update([
						'value3' => $asd->value3 + $sch->quantity
					]);

					$qty -= $sch->quantity;
				}

				if($qty == 0){
					break;
				}
			}

			if($qty == 0){
				continue;
			}
		}
	}




	public function trialload(){

		preg_match("#load average: ([0-9\.]+), ([0-9\.]+), ([0-9\.]+)#", `uptime`, $matches);
		print_r($matches);
	}

	public function label_kedatangan($id){

		//Get PO
		$po_detail = AccPurchaseOrderDetail::where('id', $id)
		->first();

		$kode_item = "0005297";
		$description = '0005297 Besi Pipa Galvanis dia.1/2"x6000mm';
		$po = "EQ2010001-IT";
		$date = '2020-11-15';
		$quantity = 10;

		return view('accounting_purchasing.report.label_kedatangan', array(
			'kode_item' => $po_detail->no_item,
			'description' => $po_detail->nama_item,
			'po' => $po_detail->no_po,
			'date' => $po_detail->date_receive,
			'quantity' => $po_detail->qty_receive
		));
	}

	public function test_print(){
		$text = '___';

		print_r (explode("_",$text));


	}


	public function test1(){
		for ($i=0; $i <= 5; $i++) { 
			echo $i++;
		}	
	}

	public function test2(){

		$string = "Hello world. It's a beautiful day.";
		$newString = explode(" ", $string);

		echo $newString[0].' '.$newString[4];

	}

	public function test3(){

		for ($i=0; $i < 10; $i++) { 
			for ($j=0; $j < 10; $j++) { 
				if($i <  $j){
					echo $j;
				}
			}
			echo "<br>";
		}	

	}

	public function test4(){

		$myArr = ['A','B','C','D','E','F','G','H','I','J'];

		for ($i=0; $i < 10; $i++) { 
			if(($i % 2) == 0){
				echo $myArr[$i];
			}
		}



	}

	public function trialLoc(){

		$log = db::connection('mirai_mobile')->select("select distinct latitude, longitude from quiz_logs
			where city = ''
			or province = '';");


		for($i=0; $i < count($log); $i++) {

			$loc = $this->getLocation($log[$i]->latitude, $log[$i]->longitude);
			$loc1 = json_encode($loc);
			$loc2 = explode('\"',$loc1);

			$keyVillage = array_search('village', $loc2);
			$keyResidential = array_search('residential', $loc2);
			$keyHamlet = array_search('hamlet', $loc2);
			$keyNeighbourhood = array_search('neighbourhood', $loc2);

			$keyStateDistrict = array_search('state_district', $loc2);
			$keyCity = array_search('city', $loc2);
			$keyCounty = array_search('county', $loc2);

			$keyState = array_search('state', $loc2);
			$keyPostcode = array_search('postcode', $loc2);
			$keyCountry = array_search('country', $loc2);


			if ($keyVillage && $loc2[$keyVillage+2] != ":") {
				$village = $loc2[$keyVillage+2];
			}else if($keyResidential && $loc2[$keyResidential+2] != ":") {
				$village = $loc2[$keyResidential+2];
			}else if($keyHamlet && $loc2[$keyHamlet+2] != ":") {
				$village = $loc2[$keyHamlet+2];
			}else if($keyNeighbourhood && $loc2[$keyNeighbourhood+2] != ":") {
				$village = $loc2[$keyNeighbourhood+2];
			}else{	
				$village = "";
			}

			if ($keyStateDistrict && $loc2[$keyStateDistrict + 2] != ":") {
				$city = $loc2[$keyStateDistrict + 2];
			}else if($keyCity && $loc2[$keyCity + 2] != ":") {
				$city = $loc2[$keyCity + 2];
			}else if($keyCounty && $loc2[$keyCounty+2] != ":") {
				$city = $loc2[$keyCounty+2];
			}else{	
				$city = "";
			}

			if($keyState){
				$province = $loc2[$keyState + 2];
			}else{
				$province = "";
			}

			// $data = array(
			// 	'village' => $village,
			// 	'city' => $city,
			// 	'province' => $loc2[$keyState + 2],
			// 	'postcode' => $loc2[$keyPostcode + 2],
			// 	'country' => $loc2[$keyCountry + 2]
			// );
			// dd($data);

			$lists = db::connection('mirai_mobile')
			->table('quiz_logs')
			->where('latitude', $log[$i]->latitude)
			->where('longitude', $log[$i]->longitude)
			->update([
				'village' => $village,
				'city' => $city,
				'province' => $province
			]);

		}

	}

	public function testRam(){

		 //    $free = shell_exec('free');
			// $free = (string)trim($free);
			// $free_arr = explode("\n", $free);
			// $mem = explode(" ", $free_arr[1]);
			// $mem = array_filter($mem);
			// $mem = array_merge($mem);
			// $memory_usage = $mem[2]/$mem[1]*100;
			// var_dump($memory_usage);

			// $load = sys_getloadavg();
			// var_dump($load[0]);

	        // GET MEMORY PHP

	        // $mem_usage = memory_get_usage(true);

	        // if ($mem_usage < 1024)
	        //     echo $mem_usage." bytes";
	        // elseif ($mem_usage < 1048576)
	        //     echo round($mem_usage/1024,2)." kilobytes";
	        // else
	        //     echo round($mem_usage/1048576,2)." megabytes";

	        // echo "<br/>";

			// GET MEMORY SERVER

			// Get memory size
			// $memory_size = memory_get_usage();
			// // Specify memory unit
			// $memory_unit = array('Bytes','KB','MB','GB','TB','PB');
			// // Display memory size into kb, mb etc.
			// echo 'Used Memory : '.round($memory_size/pow(1024,($x=floor(log($memory_size,1024)))),2).' '.$memory_unit[$x]."\n";

	}

	public function writeString($text, $maxLength, $char) {
		if ($maxLength > 0) {
			$textLength = 0;
			if ($text != null) {
				$textLength = strlen($text);
			}
			else {
				$text = "";
			}
			for ($i = 0; $i < ($maxLength - $textLength); $i++) {
				$text .= $char;
			}
		}
		return strtoupper($text);
	}


	public function testPrint(){
		$printer_name = 'MIS';


		$location = 'CASE';
		$kd_number = 'KDADD001';
		$quantity = '10';
		$material_number = 'ZW39580';
		$material_description = 'CLC-550AL//G W/SLS-205(NEW)';

		$printer_name = 'MIS';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_LEFT);
		$printer->setUnderline(true);
		$printer->text('Storage Location:');
		$printer->setUnderline(false);
		$printer->feed(1);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(3, 3);
		$printer->text(strtoupper($location."\n"));
		$printer->initialize();

		$printer->setUnderline(true);
		$printer->text('KDO:');
		$printer->feed(1);
		$printer->setUnderline(false);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->qrCode($kd_number, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->text($kd_number."\n");

		$printer->feed(2);
		$printer->initialize();
		$printer->text("GMC     | Description                     | Qty ");
		$qty = $this->writeString($quantity, 4, ' ');
		$material_description = substr($material_description, 0,31);
		$material_description = $this->writeString($material_description, 31, ' ');
		$printer->text($material_number." | ".$material_description." | ".$quantity);

		$printer->feed(3);
		$printer->initialize();
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text("------------------------------------");
		$printer->feed(1);
		$printer->text("|Qty:             |Qty:            |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|                 |                |");
		$printer->feed(1);
		$printer->text("|Production       |Logistic        |");
		$printer->feed(1);
		$printer->text("------------------------------------");
		$printer->feed(2);
		$printer->initialize();
		$printer->text("Total Qty: ". $quantity ."\n");
		$printer->feed(3);

		$printer->cut();
		$printer->close();


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
		// 	'status' => true,
		// 	'data' => $results,
		// );
		// return Response::json($response);

		return "<img src='https://maps.locationiq.com/v3/staticmap?key=pk.456ed0d079b6f646ad4db592aa541ba0&center=".$lat.",".$long."&zoom=14&size=1800x1800&format=png&markers=icon:large-green-cutout|".$lat.",".$long."'>";
	}

	public function trial2(){
		$title = 'Production Achievement';
		$title_jp = '';

		return view('trial2', array(
			'title' => $title,
			'title_jp' => $title_jp
		))->with('page', 'Production Achievement');
	}

	public function fetchProductionAchievment(Request $request){

		$date = db::select("select week_date, remark from weekly_calendars
			where week_date <= '2020-04-16'
			and remark <> 'H'
			order by week_date desc
			limit 5");

		$datefrom = $date[4]->week_date;
		$dateto = date('Y-m-d');
		$origin_group = '043';

		if(strlen($request->get('datefrom'))>0){
			$datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
		}
		if(strlen($request->get('dateto'))>0){
			$dateto = date('Y-m-d', strtotime($request->get('dateto')));
		}
		if(strlen($request->get('origin_group'))>0){
			$origin_group = $request->get('origin_group');
		}

		$data = db::select("SELECT
			due_date,
			origin_group_code,
			sum( target ) as target,
			sum( surface_treatment ) as surface_treatment,
			sum( welding ) as welding 
			FROM
			(
			SELECT
			assy_picking_schedules.due_date,
			materials.origin_group_code,
			CEIL(
			IF
			(
			materials.origin_group_code = '043',
			sum( assy_picking_schedules.quantity ) / 34,
			IF
			( materials.origin_group_code = '042', sum( assy_picking_schedules.quantity ) / 21, sum( assy_picking_schedules.quantity ) / 20 ) 
			) 
			) AS target,
			0 AS surface_treatment,
			0 AS welding 
			FROM
			assy_picking_schedules
			LEFT JOIN materials ON materials.material_number = assy_picking_schedules.material_number 
			WHERE
			assy_picking_schedules.due_date >= '".$datefrom."' 
			AND assy_picking_schedules.due_date <= '".$dateto."' 
			AND materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND materials.origin_group_code = '".$request->get('origin_group')."'
			GROUP BY
			assy_picking_schedules.due_date,
			materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS surface_treatment,
			0 AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$request->get('origin_group')."'
			AND kitto.histories.completion_location IN ( 'SX51', 'CL51', 'FL51' ) 
			GROUP BY
			date( kitto.histories.created_at ),
			ympimis.materials.origin_group_code UNION ALL
			SELECT
			date( kitto.histories.created_at ) AS due_date,
			ympimis.materials.origin_group_code,
			0 AS target,
			0 AS surface_treatment,
			CEIL(
			IF
			(
			ympimis.materials.origin_group_code = '043',
			sum( kitto.histories.lot ) / 34,
			IF
			(
			ympimis.materials.origin_group_code = '042',
			sum( kitto.histories.lot ) / 21,
			sum( kitto.histories.lot ) / 20 
			) 
			) 
			) AS welding 
			FROM
			kitto.histories
			LEFT JOIN kitto.materials ON kitto.materials.id = kitto.histories.completion_material_id
			LEFT JOIN ympimis.materials ON ympimis.materials.material_number = kitto.materials.material_number 
			WHERE
			date( kitto.histories.created_at ) >= '".$datefrom."' 
			AND date( kitto.histories.created_at ) <= '".$dateto."' 
			AND kitto.histories.category LIKE 'completion%' 
			AND ympimis.materials.hpl IN ( 'ASKEY', 'TSKEY', 'CLKEY', 'FLKEY' ) 
			AND ympimis.materials.origin_group_code = '".$request->get('origin_group')."' 
			AND kitto.histories.completion_location IN ( 'SX21', 'CL21', 'FL21' ) 
			GROUP BY
			date( kitto.histories.created_at ) ,
			ympimis.materials.origin_group_code
			) AS wst 
			GROUP BY
			due_date,
			origin_group_code");


		$data2 = db::select("SELECT
			target.due_date,
			target.origin_group_code,
			target.target,
			result.result 
			FROM
			(
			SELECT
			production_schedules.due_date,
			materials.origin_group_code,
			sum( production_schedules.quantity ) AS target 
			FROM
			production_schedules
			LEFT JOIN materials ON production_schedules.material_number = materials.material_number 
			WHERE
			production_schedules.due_date >= '".$datefrom."' 
			AND production_schedules.due_date <= '".$dateto."' 
			AND materials.origin_group_code = '".$request->get('origin_group')."' 
			GROUP BY
			production_schedules.due_date,
			materials.origin_group_code 
			) AS target
			LEFT JOIN (
			SELECT
			date( flo_details.created_at ) AS date,
			flo_details.origin_group_code,
			sum( quantity ) AS result 
			FROM
			flo_details 
			WHERE
			date( flo_details.created_at ) >= '".$datefrom."' 
			AND date( flo_details.created_at ) <= '".$dateto."' 
			AND flo_details.origin_group_code = '".$request->get('origin_group')."' 
			GROUP BY
			date,
			flo_details.origin_group_code 
		) AS result ON result.date = target.due_date");

		$response = array(
			'status' => true,
			'data' => $data,
			'data2' => $data2,
			'datefrom' => $datefrom,
			'dateto' => $dateto,
			'origin_group' => $origin_group,
		);
		return Response::json($response);		
	}

	public function stocktaking(){

		$lists = db::select("SELECT
			s.id,
			s.store,
			s.category,
			s.material_number,
			mpdl.material_description,
			m.`key`,
			m.model,
			m.surface,
			mpdl.bun,
			s.location,
			mpdl.storage_location,
			v.lot_completion,
			v.lot_transfer,
			IF
			( s.location = mpdl.storage_location, v.lot_completion, v.lot_transfer ) AS lot 
			FROM
			stocktaking_lists s
			LEFT JOIN materials m ON m.material_number = s.material_number
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
			LEFT JOIN material_volumes v ON v.material_number = s.material_number
			ORDER BY
			s.id ASC");

		foreach ($lists as $list) {
			$this->printod($list);
		}

	}

	public function temp(){
		$plc = new ActMLEasyIf(3);
		$datas = $plc->read_data('W12', 10);
		$datas = $plc->read_data('W22', 10);

		$response = array(
			'status' => true,
			'datas' => $datas
		);
		return Response::json($response);

	}

	public function printod($list){
		$printer_name = 'TESTPRINTER';
		$connector = new WindowsPrintConnector($printer_name);
		$printer = new Printer($connector);

		// $id = '136';
		// $store = 'SUBASSY-CL-2B';
		// $category = '(ASSY)';
		// $material_number = 'W528860';
		// $sloc = 'CL91';
		// $description = 'CL-250N 7 ASSY CORK&PAD PACKED(YMPI) J';
		// $key = '7';
		// $model = 'CL250';
		// $surface = 'NICKEL';
		// $uom = 'PC';
		// $lot = '';

		$id = $list->id;
		$store = $list->store;
		$category = '('.$list->category.')';
		$material_number = $list->material_number;
		$sloc = $list->location;
		$description = $list->material_description;
		$key = $list->key;
		$model = $list->model;
		$surface = $list->surface;
		$uom = $list->bun;
		$lot = $list->lot;

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setEmphasis(true);
		$printer->setReverseColors(true);
		$printer->setTextSize(2, 2);
		$printer->text("  Summary of Counting  "."\n");
		$printer->initialize();
		$printer->setTextSize(3, 3);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($store."\n");
		if($list->category == 'ASSY'){
			$printer->setReverseColors(true);			
		}
		$printer->text($category."\n");
		$printer->feed(1);
		$printer->qrCode($id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
		$printer->feed(1);
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(4, 2);
		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->text($material_number."\n");
		$printer->text($sloc."\n\n");
		$printer->initialize();
		$printer->setEmphasis(true);
		$printer->setTextSize(2, 1);
		$printer->text($description."\n");
		$printer->feed(1);
		$printer->text($model."-".$key."-".$surface."\n");
		if(strlen($lot) == 0){
			$printer->text("Lot: \xDB\xDB ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat("\xDB", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		else{
			$printer->text("Lot: ".$lot." ".$uom."\n");
			$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
			$printer->textRaw("\xb3Lot:".str_repeat(" ", 18)."\xb3\n");
			$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		}
		$printer->textRaw("\xda".str_repeat("\xc4", 22)."\xbf\n");
		$printer->textRaw("\xb3Z1 :".str_repeat(" ", 18)."\xb3\n");
		$printer->textRaw("\xc0".str_repeat("\xc4", 22)."\xd9\n");
		$printer->feed(2);
		$printer->cut();
		$printer->close();
	}

	public function indexWhatsappApi()
	{
		return view('trials.whatsapp');
	}

	public function whatsapp_api()
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL => 'https://app.whatspie.com/api/messages',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=Order%20bento%20anda%20telah%20dikofirmasi.%0ASilahkan%20cek%20kembali%20di%20MIRAI.%0A%0A-YMPI%20MIS%20Dept.-&type=chat',
			CURLOPT_HTTPHEADER => array(
				'Accept: application/json',
				'Content-Type: application/x-www-form-urlencoded',
				'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
			),
		));

		curl_exec($curl);
	}


	public function index_push_pull_trial()
	{
		return view('trials.push_pull_trial', array(
		))->with('page', 'Trial');
	}

	public function push_pull_trial(Request $request)
	{
		try {
			$id_user = Auth::id();

			$file = $request->file('file');
			$file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
			$file->move('data_file/push_pull/trial/', $file_name);

			$excel = 'data_file/push_pull/trial/' . $file_name;
			$rows = Excel::load($excel, function($reader) {
                // $reader->noHeading();
                // $reader->skipRows(1);

                // $reader->each(function($row) {
                // });
				$reader->noHeading();
              //Skip Header
				$reader->skipRows(6);
			})->toObject();

             // $index = 0;
             // $index2 = 0;
             // for ($i=0; $i < count($rows); $i++) { 
             // 	if ($rows[$i][0] != 0) {
             // 		$rowfix[$i] = $rows[$i][0];
             // 	}
             // 	if ($index2 != $rows[$i][0]) {
             // 		$indexfix[] = $index++;
             // 	}
             // }


			$arr = [];
			$temp = [];
			$insert = true;

			var_dump($rows[0][0]);

			for ($i=0; $i < count($rows); $i++) { 

				if($rows[$i][1] != 0){
					if ($rows[$i][1] < 60 || $rows[$i][1] > 50) {
						array_push($temp, $rows[$i][1]);
						$insert = true;
					}
				}else{
					if($insert){
						$insert = false;
						array_push($arr, $temp);
						$temp = [];
					}
				}

			}


			for ($i=0; $i < count($arr); $i++) { 
				for ($j=0; $j < count($arr[$i]); $j++) { 
					DB::table('push_pull_trial_temps')->insert([
						'check_index' => $i,
						'value' => $arr[$i][$j],
						'created_by' => $id_user,
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s'),
					]);
				}
			}

			$fix = DB::SELECT('SELECT DISTINCT ( a.check_index ),( SELECT max( VALUE ) FROM push_pull_trial_temps WHERE check_index = a.check_index ) as value
				FROM
				push_pull_trial_temps a');
			foreach ($fix as $key) {
				DB::table('push_pull_trials')->insert([
					'check_index' => $key->check_index+1,
					'value' => $key->value/10,
					'created_by' => $id_user,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			}

			DB::table('push_pull_trial_temps')->truncate();

			$response = array(
				'status' => true,
				'message' => 'Upload file success',
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function fetch_push_pull_trial()
	{
		try {
			$push_pull = DB::SELECT('select * from push_pull_trials');

			$response = array(
				'status' => true,
				'push_pull' => $push_pull,
			);
			return Response::json($response);
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function whatsapp_api2(Request $request)
	{
		// $time2 = $request->get('time2');
		// $time = $request->get('time');
		$json = file_get_contents('https://api.chat-api.com/instance150276/messages?token=owl5cvgsqlil60xf&last=1&limit=3');
		$decoded = json_decode($json,true);

		var_dump($decoded);

        //write parsed JSON-body to the file for debugging
        // ob_start();
        // // var_dump($decoded);
        // $input = ob_get_contents();
        // ob_end_clean();
        // file_put_contents('input_requests.log',$input.PHP_EOL,FILE_APPEND);

        // if(isset($decoded['messages'])){
        // //check every new message
        // foreach($decoded['messages'] as $message){
        // //delete excess spaces and split the message on spaces. The first word in the message is a command, other words are parameters
        // $text = explode(' ',trim($message['body']));
        // // echo $text;
        // // echo $message['chatId'];
        // //current message shouldn't be send from your bot, because it calls recursion
        // if(!$message['fromMe']){
        // //check what command contains the first word and call the function
        // switch(mb_strtolower($text[0],'UTF-8')){
        // case 'hi':  {$this->welcome($message['chatId'],false); break;}
        //     case 'chatId': {$this->showchatId($message['chatId']); break;}
        //     case 'time':   {$this->time($message['chatId']); break;}
        //     case 'me':     {$this->me($message['chatId'],$message['senderName']); break;}
        //     case 'file':   {$this->file($message['chatId'],$text[1]); break;}
        //     case 'ptt':     {$this->ptt($message['chatId']); break;}
        //     case 'geo':    {$this->geo($message['chatId']); break;}
        //     case 'group':  {$this->group($message['author']); break;}
        //     default:        {$this->welcome($message['chatId'],true); break;}
        //     }}}}
	}

        //this function calls function sendRequest to send a simple message
        //@param $chatId [string] [required] - the ID of chat where we send a message
        //@param $text [string] [required] - text of the message
	public function welcome($chatId, $noWelcome = false){
		$welcomeString = ($noWelcome) ? "Incorrect command\n" : "WhatsApp Demo Bot PHP\n";
		$this->sendMessage($chatId,
			$welcomeString.
			"Commands:\n".
			"1. chatId - show ID of the current chat\n".
			"2. time - show server time\n".
			"3. me - show your nickname\n".
			"4. file [format] - get a file. Available formats: doc/gif/jpg/png/pdf/mp3/mp4\n".
			"5. ptt - get a voice message\n".
			"6. geo - get a location\n".
			"7. group - create a group with the bot"
		);
	}

	public function showchatId($chatId){
		$this->sendMessage($chatId,'chatId: '.$chatId);
	}

	public function time($chatId){
		$this->sendMessage($chatId,date('d.m.Y H:i:s'));
	}
    //sends your nickname. it is called when the bot gets the command "me"
    //@param $chatId [string] [required] - the ID of chat where we send a message
    //@param $name [string] [required] - the "senderName" property of the message
	public function me($chatId,$name){
		$this->sendMessage($chatId,$name);
	}
    //sends a file. it is called when the bot gets the command "file"
    //@param $chatId [string] [required] - the ID of chat where we send a message
    //@param $format [string] [required] - file format, from the params in the message body (text[1], etc)
	public function file($chatId,$format){
		$availableFiles = array(
			'doc' => 'document.doc',
			'gif' => 'gifka.gif',
			'jpg' => 'jpgfile.jpg',
			'png' => 'pngfile.png',
			'pdf' => 'presentation.pdf',
			'mp4' => 'video.mp4',
			'mp3' => 'mp3file.mp3'
		);

		if(isset($availableFiles[$format])){
			$data = array(
				'chatId'=>$chatId,
				'body'=>'https://domain.com/PHP/'.$availableFiles[$format],
				'filename'=>$availableFiles[$format],
				'caption'=>'Get your file '.$availableFiles[$format]
			);
			$this->sendRequest('sendFile',$data);}}

	    //sends a voice message. it is called when the bot gets the command "ptt"
	    //@param $chatId [string] [required] - the ID of chat where we send a message
			public function ptt($chatId){
				$data = array(
					'audio'=>'https://domain.com/PHP/ptt.ogg',
					'chatId'=>$chatId
				);
				$this->sendRequest('sendAudio',$data);
			}

    //sends a location. it is called when the bot gets the command "geo"
    //@param $chatId [string] [required] - the ID of chat where we send a message
			public function geo($chatId){
				$data = array(
					'lat'=>51.51916,
					'lng'=>-0.139214,
					'address'=>'Ваш адрес',
					'chatId'=>$chatId
				);
				$this->sendRequest('sendLocation',$data);
			}

    //creates a group. it is called when the bot gets the command "group"
    //@param chatId [string] [required] - the ID of chat where we send a message
    //@param author [string] [required] - "author" property of the message
			public function group($author){
				$phone = str_replace('@c.us','',$author);
				$data = array(
					'groupName'=>'Group with the bot PHP',
					'phones'=>array($phone),
					'messageText'=>'It is your group. Enjoy'
				);
				$this->sendRequest('group',$data);
			}

			public function sendMessage($chatId, $text){
				$data = array('chatId'=>$chatId,'body'=>$text);
				$this->sendRequest('message',$data);}

				public function sendRequest($method,$data){
					$url = $this->APIurl.$method.'?token='.$this->token;
					if(is_array($data)){ $data = json_encode($data);}
					$options = stream_context_create(['http' => [
						'method'  => 'POST',
						'header'  => 'Content-type: application/json',
						'content' => $data]]);
					$response = file_get_contents($url,false,$options);
					file_put_contents('requests.log',$response.PHP_EOL,FILE_APPEND);
				}

				function kirimTelegram($pesan) {
					$chat_id = [];
					array_push($chat_id, '895527318');

					for ($i = 0; $i < count($chat_id);$i++) {
						$pesan = json_encode($pesan);
						$API = "https://api.telegram.org/bot".$this->bot_token."/sendmessage?chat_id=".$chat_id[$i]."&text=$pesan";
						$ch = curl_init();
						curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
						curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
						curl_setopt($ch, CURLOPT_URL, $API);
						$result = curl_exec($ch);
						curl_close($ch);
						return $result;
					}
				}

				public function testmailnew()
				{
					try {
						$to = [
							'ympi-mis-ML@music.yamaha.com',
							'fakhrizal.ihza.mahendra@music.yamaha.com'
						];

						// $bodyHtml = "MIS NEW Test Mail From Domainesia";

						// Mail::raw([], function($message) use($bodyHtml,$to) {
						// 	$message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
						// 	$message->to($to);
						// 	$message->subject('Trial Mail');
						// 	$message->setBody($bodyHtml, 'text/html' );
						// });

						$bodyHtml = "MIS Test Mail";
			            Mail::raw($bodyHtml, function ($message) use ($to) {
			                $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
			                $message->to($to);
			                $message->subject('MIS Test Mail');
			                $message->text('Plain Text');
			            });





					} catch (\Exception $e) {
						echo $e->getMessage();
					}
				}

				public function birthday()
				{
					return view('trials.birthday');
				}

				public function jepang()
				{
					return view('trials.jepang');
				}


				public function testTesseract()
				{
					return view('trials.tesseract');
				}

				public function xmlParser()
				{
					return view('trials.xml_parser');
				}

				public function xmlParserUpload(Request $request)
				{
					try{
						$id_user = Auth::id();

						$file = $request->file('file');
						$file_name = 'temp_'. MD5(date("YmdHisa")) .'.'.$file->getClientOriginalExtension();
						$file->move('data_file/xml/', $file_name);

						$xml = 'data_file/xml/' . $file_name;
						$xmlparse = simplexml_load_file($xml) or die("Error: Cannot create object");

			                  // foreach ($xmlparse as $key) {
			                  // 	var_dump($key->entry);
			                  // }
						for ($i=0; $i < count($xmlparse->entry); $i++) { 
			                  	// var_dump($xmlparse->entry[$i]->organizer->component->observation->text->type);
							for ($j=0; $j < count($xmlparse->entry[$i]->organizer->component); $j++) { 
								var_dump($xmlparse->entry[$i]->organizer->component[$j]->observation->text);
							}
						}

						$response = array(
							'status' => true,
							'message' => 'Upload file success',
						);
						return Response::json($response);
					}catch(\Exception $e){
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}

				public function fetchFIbrationSensorData()
				{
					$history = DB::SELECT("SELECT DATE_FORMAT(data_time,'%Y-%m-%d') as dt,
						ROUND(AVG(sensor_value), 2) as avg_sensor
						FROM
						sensor_datas 
						WHERE
						DATE( created_at ) >= DATE_SUB(NOW(), INTERVAL 1 MONTH)
						and sensor_value > 0
						and category = 'Vibration2'
						group by DATE_FORMAT(data_time,'%Y-%m-%d')");

					$response = array(
						'status' => true,
						'data' => $history,
					);
					return Response::json($response);
				}

				public function fetchFibrationSensor()
				{
					$ch = curl_init();
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					// curl_setopt($ch, CURLOPT_URL,'http://10.109.73.35');
					curl_setopt($ch, CURLOPT_URL,'http://10.109.79.102');
					// Execute
					$result=curl_exec($ch);
					// Closing
					curl_close($ch);

					$datas = json_decode($result, true);

					if ($datas['Vibration'] > 20000 ) {
						$f1 = 20000;
					} else if($datas['Vibration'] > 0){
						// if ($datas['Vibration'] == 330) {
							// $f1 = 1;
						// } else {

							// $f1 = (330 - (int) $datas['Vibration']) + 1;
						// }
					} else {
						$f1 = 0;
					}
					$f1 = $datas['Vibration'];

					// if ($datas['Vibration 2'] > 20000 ) {
					// 	$f2 = 20000;
					// } else {
					// 	$f2 = $datas['Vibration 2'];
					// }

					DB::table('sensor_datas')->insert([
						'category' => 'Vibration', 
						'data_time' => date('Y-m-d H:i:s'), 
						'sensor_value' => $f1, 
						'unit' => '', 
						'remark' => $datas['Vibration'], 
						// 'remark' => 'Vibration New', 
						'created_by' => '1', 
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]);

					$history = DB::SELECT("SELECT DATE_FORMAT(data_time,'%d %b %Y %H:%i') as time,
						ROUND(AVG(sensor_value), 2) as avgpm
						FROM
						sensor_datas 
						WHERE
						DATE( created_at ) >= DATE('2021-04-27')
						and sensor_value > 0
						and sensor_value is not null
						group by DATE_FORMAT(data_time,'%d %b %Y %H:%i')");

					$fq = DB::SELECT('SELECT  DATE_FORMAT(NOW() - INTERVAL 1 DAY, "%Y-%m-%d") as d, IFNULL(AVG(sensor_value),0) as avg_day FROM sensor_datas
						WHERE DATE_FORMAT(data_time,"%Y-%m-%d") = DATE_FORMAT(NOW() - INTERVAL 1 DAY ,"%Y-%m-%d") and sensor_value > 0');

					$response = array(
						'status' => true,
						// 'message' => $datas,
						'records' => $history,
						'forecast' => $fq
					);

					return Response::json($response);
				}

				public function fetchFibrationSensorOld()
				{
					$ch = curl_init();
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					curl_setopt($ch, CURLOPT_URL,'http://10.109.7.148');
					// curl_setopt($ch, CURLOPT_URL,'http://10.109.7.181');
					// Execute
					$result=curl_exec($ch);
					// Closing
					curl_close($ch);

					$datas = json_decode($result, true);

					if ($datas['Getaran 1'] > 20000 ) {
						$f1 = 20000;
					} else {
						$f1 = $datas['Getaran 1'];
					}

					if ($datas['Getaran 2'] > 20000 ) {
						$f2 = 20000;
					} else {
						$f2 = $datas['Getaran 2'];
					}

					DB::table('sensor_data_olds')->insert([
						'category' => 'Fibration', 
						'data_time' => date('Y-m-d H:i:s'), 
						'sensor_value' => $f1, 
						'unit' => '', 
						'remark' => $f2, 
						// 'remark' => 'Vibration New', 
						'created_by' => '1', 
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]);

					$history = DB::SELECT("SELECT
						* 
						FROM
						sensor_data_olds 
						WHERE
						DATE( created_at ) <= DATE( NOW()) AND DATE( created_at ) >= DATE(
						NOW() - INTERVAL 1 HOUR)
						ORDER BY sensor_data_olds.sensor_value");

					$response = array(
						'status' => true,
						'message' => $datas,
						'records' => $history
					);

					return Response::json($response);
				}

				public function fetchFibrationSensor2(Request $request)
				{
					$response = array(
						'status' => true,
						'message' => $request,
					);
					return Response::json($response);
				}

				public function indexFIbrationSensor()
				{
					$weekly_calendars = db::select("SELECT week_date, remark from weekly_calendars where week_date >= '2021-04-27'");
					return view('trials.trial_getar', array(
						'calendars' => $weekly_calendars,
					)
				);
				}

				public function indexFIbrationSensorOld()
				{
					return view('trials.trial_getar_old');
				}

				public function indexPhpInfo()
				{
					return view('trials.trial_phpinfo');
				}

				public function fetchPhpInfo()
				{
					$ch = curl_init();
					// Will return the response, if false it print the response
					curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
					// Set the url
					// curl_setopt($ch, CURLOPT_URL,'http://10.109.73.35');
					curl_setopt($ch, CURLOPT_URL,'http://10.109.52.4/phpsysinfo/xml.php?json');
					// Execute
					$result=curl_exec($ch);
					// Closing
					curl_close($ch);

					$datas = json_decode($result, true);

					$response = array(
						'status' => true,
						'message' => $datas
					);

					return Response::json($response);
				}

				public function inputPhSensor(Request $request)
				{
					try {
						DB::table('sensor_datas')->insert([
							'category' => 'Ph', 
							'data_time' => date('Y-m-d H:i:s'), 
							'sensor_value' => round((float) $request->get('ph'), 2), 
							'unit' => '', 
							'created_by' => '1', 
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);

						$response = array(
							'status' => true,
							'message' => 'Input Berhasil',
						);
						return Response::json($response);
					} catch (\Exception $e) {
						$response = array(
							'status' => false,
							'message' => $e->getMessage(),
						);
						return Response::json($response);
					}
				}

				public function inputVibrationSensor(Request $request)
				{
					$f1 = 0;
					if ($request->get('vibration') > 20000 ) {
						$f1 = 20000;
					} else if($request->get('vibration') > 0){
						$f1 = $request->get('vibration');
					} else {
						$f1 = 0;
					}


					DB::table('sensor_datas')->insert([
						'category' => $request->get('name'), 
						'data_time' => date('Y-m-d H:i:s'), 
						'sensor_value' => $f1, 
						'unit' => '', 
						'remark' => $request->get('vibration'), 
						// 'remark' => 'Vibration New', 
						'created_by' => '1', 
						'created_at' => date('Y-m-d H:i:s'),
						'updated_at' => date('Y-m-d H:i:s')
					]);
				}

				public function inputMachineSensor()
				{

				}

				public function renameFile()
				{
					$table = db::select("select * from aga");

					foreach ($table as $tb) {
						$filePath = 'fa_temp/Solder/'.$tb->value1.' (Compressed).jpg';

						$newFileName = 'fa_temp/Solder/'.$tb->value1.'.jpg';

						/* Rename File name */
						if (file_exists($filePath)) {
							if( !rename($filePath, $newFileName) ) {  
								echo "File can't be renamed!";  
							}  

							else {  
								echo "File has been renamed!";  
							} 

						}

					}

					// dd($table);
				}
			}


