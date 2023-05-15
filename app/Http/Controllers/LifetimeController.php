<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Illuminate\Support\Facades\Mail;
use App\CodeGenerator;
use App\WorkshopJobOrderLog;
use App\WorkshopJobOrder;
use App\User;
use App\EmployeeSync;
use App\Employee;
use App\Material;
use Response;

class LifetimeController extends Controller
{
    public function __construct()
	{
		$this->middleware('auth');
		if (isset($_SERVER['HTTP_USER_AGENT']))
		{
			$http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
			if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
			{
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
				die();
			}
		}

		$this->product = [
			'FLUTE',
			'CLARINET',
			'SAXOPHONE',
			'KEY POST',
			'KEY POST',
			'Au ( Gold )',
		];
	}

	public function indexMonitoringLifetime($category,$location)
	{
		$products = DB::connection('ympimis_2')->select("SELECT DISTINCT
				( product ) 
			FROM
				lifetimes");

		$products = DB::connection('ympimis_2')->select("SELECT DISTINCT
				( product ) 
			FROM
				lifetimes");

		return view('lifetime.index', array(
			'title' => 'Monitoring Lifetime '.ucwords($category).' '.ucwords($location),
			'title_jp' => '寿命管理表示',
		))
		->with('page', 'Lifetime')
		->with('category', $category)
		->with('location', $location)
		->with('products', $products)
		->with('role', Auth::user()->role_code)
		->with('product', $this->product);
	}

	public function fetchMonitoringLifetime($category,$location,Request $request)
	{
		try {
			if ($request->get('product') == '' || $request->get('product') == 'All') {
				$products = "";
			}else{
				$products = "AND product = '".$request->get('product')."'";
			}

			if ($request->get('item_name') == '' || $request->get('item_name') == 'All') {
				$item_name = "";
			}else{
				$item_name = "AND item_name = '".$request->get('item_name')."'";
			}
			$lifetime = DB::connection('ympimis_2')->select("SELECT
					*,
					coalesce(TIMESTAMPDIFF(
						DAY,
						used_start,
					NOW()) ,0) AS days
				FROM
					`lifetimes` 
				WHERE
					category = '".$category."'
					and location = '".$location."'
					".$products."
					".$item_name."
				order by availability,lifetime desc");

			$item = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
				( item_name ),
				product 
			FROM
				lifetimes 
				where category = '".$category."'
				and location = '".$location."'
			ORDER BY
				item_name");

			if ($category == 'jig') {
				$pie_charts = DB::connection('ympimis_2')->select("SELECT
					sum( CASE WHEN availability = '1' THEN 1 ELSE 0 END ) AS used,
					sum( CASE WHEN availability = '2' THEN 1 ELSE 0 END ) AS `repair`,
					sum( CASE WHEN availability = '3' THEN 1 ELSE 0 END ) AS not_use 
				FROM
					lifetimes 
				WHERE
					category = '".$category."'
					".$item_name."
					and location = '".$location."'");
			}else{
				$pie_charts = DB::connection('ympimis_2')->select("SELECT
					sum( CASE WHEN availability = '1' THEN 1 ELSE 0 END ) AS used,
					sum( CASE WHEN availability = '2' THEN 1 ELSE 0 END ) AS `repair`,
					sum( CASE WHEN availability = '3' THEN 1 ELSE 0 END ) AS not_use 
				FROM
					lifetimes 
				WHERE
					category = '".$category."'
					".$item_name."
					and location = '".$location."'");
			}

			$response = array(
				'status' => true,
				'lifetime' => $lifetime,
				'pie_charts' => $pie_charts,
				'item' => $item,
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

	public function indexMasterLifetime($category,$location)
	{
		return view('lifetime.master', array(
			'title' => 'Master '.ucwords($category).' '.ucwords($location),
			'title_jp' => 'マスターデータ',
		))
		->with('page', 'Lifetime')
		->with('category', $category)
		->with('location', $location)
		->with('role', Auth::user()->role_code)
		->with('product', $this->product)
		->with('product2', $this->product);
	}

	public function fetchMasterLifetime($category,$location)
	{
		try {
			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->orderby('updated_at','desc')->get();
			$response = array(
				'status' => true,
				'lifetime' => $lifetime
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

	public function inputMasterLifetime(Request $request,$category,$location)
	{
		try {
			$product = $request->get('product');
			$item_name = $request->get('item_name');
			$item_type = $request->get('item_type');
			$lifetime_limit = $request->get('lifetime_limit');
			$limit_unit = $request->get('limit_unit');
			$item_made_in = $request->get('item_made_in');
			$item_alias = $request->get('item_alias');
			$tag = $request->get('tag');

			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('item_name',$item_name)->where('item_type',$item_type)->orderby('id','desc')->first();
			$code_generator = CodeGenerator::where('note', '=', 'Lifetime')->first();

			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
	        $item_code = strtoupper($category).$number;
	        $code_generator->index = $code_generator->index+1;

			if ($lifetime) {
				$item_index = $lifetime->item_index+1;
			}else{
				$item_index = 1;
			}

			$cek_tag = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->where('product',null)->first();

			if ($cek_tag) {
				$input = DB::connection('ympimis_2')->table('lifetimes')->where('tag',$tag)->update([
					'category' => $category,
					'product' => $product,
					'item_code' => $item_code,
					'item_name' => $item_name,
					'item_type' => $item_type,
					'item_index' => $item_index,
					'item_alias' => $item_alias,
					'item_made_in' => $item_made_in,
					'lifetime_limit' => $lifetime_limit,
					'limit_unit' => $limit_unit,
					'location' => $location,
					'availability' => 1,
					'lifetime' => 0,
					'repair' => 0,
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}else{
				$input = DB::connection('ympimis_2')->table('lifetimes')->insert([
					'tag' => $tag,
					'category' => $category,
					'product' => $product,
					'item_code' => $item_code,
					'item_name' => $item_name,
					'item_type' => $item_type,
					'item_index' => $item_index,
					'item_alias' => $item_alias,
					'item_made_in' => $item_made_in,
					'lifetime_limit' => $lifetime_limit,
					'limit_unit' => $limit_unit,
					'location' => $location,
					'availability' => 1,
					'lifetime' => 0,
					'repair' => 0,
					'created_by' => Auth::user()->username,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
			}

			$code_generator->save();
			$response = array(
				'status' => true,
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

	public function updateMasterLifetime(Request $request,$category,$location)
	{
		try {

			$id = $request->get('id');
			$product = $request->get('product');
			$tag = $request->get('tag');
			$item_name = $request->get('item_name');
			$item_type = $request->get('item_type');
			$item_alias = $request->get('item_alias');
			$lifetime_limit = $request->get('lifetime_limit');
			$limit_unit = $request->get('limit_unit');
			$availability = $request->get('availability');
			$item_made_in = $request->get('item_made_in');

			$cektag = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->first();
			$data = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('id',$id)->first();

			if ($cektag && $data->tag != $tag && $tag != '') {
				$response = array(
					'status' => false,
					'message' => 'Tag Used for Other '.$category,
				);
				return Response::json($response);
			}

			$update = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('id',$id)->update([
				'product' => $product,
				'tag' => $tag,
				'item_name' => $item_name,
				'item_type' => $item_type,
				'item_alias' => $item_alias,
				'availability' => $availability,
				'item_made_in' => $item_made_in,
				'lifetime_limit' => $lifetime_limit,
				'limit_unit' => $limit_unit,
				'updated_at' => date('Y-m-d H:i:s')
			]);

			$this->printLabel($data->item_code, $data->item_alias);

			$response = array(
				'status' => true,
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

	public function deleteMasterLifetime(Request $request,$category,$location)
	{
		try {
			$id = $request->get('id');

			$delete = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('id',$id)->delete();

			$response = array(
				'status' => true,
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

	public function indexRecordLifetime($category,$location)
	{
		return view('lifetime.record', array(
			'title' => 'Record Lifetime '.ucwords($category).' '.ucwords($location),
			'title_jp' => '寿命記録',
		))
		->with('page', 'Lifetime')
		->with('category', $category)
		->with('location', $location);
	}

	public function fetchRecordLifetime(Request $request,$category,$location)
	{
		try {
			$lifetime = DB::connection('ympimis_2')->table('lifetime_details')->select('lifetime_details.*','lifetimes.product')->join('lifetimes','lifetimes.tag','lifetime_details.tag')->where('lifetime_details.category',$category)->where('lifetime_details.location',$location)->where(DB::RAW("DATE(lifetime_details.updated_at)"),'>=',date('Y-m-d',strtotime('- 1 days')))->orderby('lifetime_details.updated_at','desc')->get();

			$employee = EmployeeSync::where('end_date',null)->get();

			$response = array(
				'status' => true,
				'lifetime' => $lifetime,
				'employee' => $employee,
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

	public function scanOperatorRecordLifetime(Request $request,$category,$location)
	{
		try {
			$employee_id = $request->get('employee_id');
			if (str_contains($employee_id,'PI')) {
				$employee = EmployeeSync::where('employee_id',$employee_id)->first();
			}else{
				$employee = Employee::where('tag',$employee_id)->first();
			}

			if ($employee) {
				$response = array(
					'status' => true,
					'employee' => $employee,
					'message' => 'Karyawan Ditemukan'
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Tag Invalid',
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function scanRecordLifetime(Request $request,$category,$location)
	{
		try {
			$tag = $request->get('tag');
			$kanban = $request->get('kanban');
			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->first();
			if ($lifetime) {
				if ($category == 'jig') {
					if ($lifetime->limit_unit == 'Pemakaian') {
						if ($lifetime->used_start != null && count($kanban) > 0) {
							$response = array(
								'status' => false,
								'message' => 'Jig Belum Discan Selesai Proses Lifetime',
							);
							return Response::json($response);
						}else if ($lifetime->used_start == null && count($kanban) > 0){
							$new_lifetime = $lifetime->lifetime+1;
							$lifetime_limit = $lifetime->lifetime_limit;
							
							$lifetimeupdate = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->update([
								'lifetime' => $new_lifetime,
								'created_by' => Auth::user()->username,
								'used_start' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s')
							]);

							$message = 'Record Lifetime Success';

							$lifetimedetail = DB::connection('ympimis_2')->table('lifetime_details')->insert([
								'category' => $lifetime->category,
								'tag' => $lifetime->tag,
								'item_code' => $lifetime->item_code,
								'item_name' => $lifetime->item_name,
								'item_index' => $lifetime->item_index,
								'item_type' => $lifetime->item_type,
								'item_made_in' => $lifetime->item_made_in,
								'location' => $lifetime->location,
								'lifetime' => $new_lifetime,
								'kanban' => join($kanban,','),
								'created_by' => Auth::user()->username,
								'created_at' => date('Y-m-d H:i:s'),
								'updated_at' => date('Y-m-d H:i:s'),
							]);
						}else if ($lifetime->used_start != null && count($kanban) == 0) {
							$lifetimedetailupdate = DB::connection('ympimis_2')->table('lifetime_details')->where('category',$category)->where('location',$location)->where('tag',$tag)->orderby('id','desc')->limit(1)->update([
								'updated_at' => date('Y-m-d H:i:s')
							]);
							$lifetimeupdate = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->update([
								'created_by' => Auth::user()->username,
								'used_start' => null,
								'updated_at' => date('Y-m-d H:i:s')
							]);
							$message = 'Record Finish Lifetime Success';
						}else{
							$response = array(
								'status' => false,
								'message' => 'Jig Belum Discan Mulai Proses Lifetime',
							);
							return Response::json($response);
						}

						

						// $lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('category',$category)->where('location',$location)->where('tag',$tag)->first();

						// $datas = array(
				  //           'lifetime' => $lifetime,
				  //           'category' => $category,
				  //           'location' => $location,
				  //         );

						// if ($new_lifetime == $lifetime_limit) {
						// 	Mail::to($mail_to)->bcc(['ympi-mis-ML@music.yamaha.com'],'BCC')->send(new SendEmail($datas, 'lifetime_limit'));
						// }

						// if ($new_lifetime > $lifetime_limit) {
						// 	$message = ucwords($category).' telah mencapai limit lifetime. Silahkan hubungi Leader / Staff terkait.';
						// }
					}
				}

				if ($category == 'screwdriver') {
					$message = 'Scan Success';
				}

				$response = array(
					'status' => true,
					'message' => $message,
					'lifetime' => $lifetime,
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Tag Invalid',
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function indexRepairLifetime(Request $request,$category,$location,$id)
	{
		$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('id',$id)->where('category',$category)->where('location',$location)->first();
		if ($lifetime) {
			if ($category == 'jig') {
				if ($lifetime->availability == 1) {
					$remark = 'Not Yet';
					$message = 'Masukkan Reason Repair '.ucwords($category).' '.ucwords($location).' "'.$lifetime->item_name.' '.$lifetime->item_type.' ('.$lifetime->item_index.')"';
				}else{
					$remark = 'Done';
					$message = 'Success Input Repair '.ucwords($category).' '.ucwords($location).' "'.$lifetime->item_name.' '.$lifetime->item_type.' ('.$lifetime->item_index.')"';
				}
			}else{
				if ($lifetime->availability == 3) {
					$remark = 'Not Yet';
					$message = 'Masukkan Reason Repair '.ucwords($category).' '.ucwords($location).' "'.$lifetime->item_name.' '.$lifetime->item_type.' ('.$lifetime->item_index.')"';
				}else if ($lifetime->availability == 2) {
					$remark = 'Done';
					$message = 'Success Input Repair '.ucwords($category).' '.ucwords($location).' "'.$lifetime->item_name.' '.$lifetime->item_type.' ('.$lifetime->item_index.')"';
				}else if ($lifetime->availability == 1) {
					$remark = 'Done';
					$message = ucwords($category).' '.ucwords($location).' "'.$lifetime->item_name.' '.$lifetime->item_type.' ('.$lifetime->item_index.') " Masih Dipakai. Silakan Input Selesai Pakai di Monitoring';
				}
			}
			return view('lifetime.repair')->with('page','Repair '.ucwords($category).' '.ucwords($location))->with('head','Repair '.ucwords($category).' '.ucwords($location))->with('message',$message)->with('id',$id)->with('lifetime',$lifetime)->with('remark',$remark)->with('category',$category)->with('location',$location);
		}else{
			return view('lifetime.repair')->with('head','Repair '.ucwords($category).' '.ucwords($location))->with('message',ucwords($category).' '.ucwords($location).' Tidak Ditemukan.')->with('page','Repair '.ucwords($category).' '.ucwords($location))->with('remark','Done')->with('category',$category)->with('location',$location);
		}
	}

	public function inputRepairLifetime(Request $request,$category,$location)
	{
		try {
			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('id',$request->get('id'))->where('category',$category)->where('location',$location)->first();
			$update_lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('id',$request->get('id'))->where('category',$category)->where('location',$location)->update([
				'availability' => '2',
				'reason' => $request->get('reason')
			]);

			$date = date('Y-m-d');
			$prefix_now = 'WJO'.date("y").date("m");
			$code_generator = CodeGenerator::where('note','=','wjo')->first();
			if ($prefix_now != $code_generator->prefix){
				$code_generator->prefix = $prefix_now;
				$code_generator->index = '0';
				$code_generator->save();
			}

			$number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
			$order_no = $code_generator->prefix . $number;
			$code_generator->index = $code_generator->index+1;
			$code_generator->save();

			$sub_section = 'Woodwind Instrument - Surface Treatment (WI-ST) Department_SurfaceTreatment Section';
			$item_name = ucwords($category).' '.ucwords($location).' '.ucwords(strtolower($lifetime->item_name)).' '.ucwords(strtolower($lifetime->item_type)).' ('.ucwords(strtolower($lifetime->item_index)).')';
			$categories = ucwords($category);
			$drawing_name = '-';
			$item_number = $lifetime->item_code;
			$part_number = '-';
			$quantity = '1';
			$priority = 'Normal';
			$type = 'Perbaikan Ketidaksesuaian';
			$material = 'SUS';
			$problem_desc = ucwords($category).' '.ucwords($location).' '.ucwords(strtolower($lifetime->item_name)).' '.ucwords(strtolower($lifetime->item_type)).' ('.ucwords(strtolower($lifetime->item_index)).'), '.$request->get('reason');
			$request_date = date('Y-m-d',strtotime('14 days'));

			$wjo = new WorkshopJobOrder([
				'order_no' => $order_no,
				'sub_section' => $sub_section,
				'item_name' => $item_name,
				'category' => $categories,
				'drawing_name' => $drawing_name,
				'item_number' => $item_number,
				'part_number' => $part_number,
				'quantity' => $quantity,
				'target_date' => $request_date,
				'priority' => $priority,
				'type' => $type,
				'material' => $material,
				'automation' => 'auto',
				'problem_description' => $problem_desc,
				'remark' => '1',
				'created_by' => Auth::user()->username,
			]);

			$wjo_log = new WorkshopJobOrderLog([
				'order_no' => $order_no,
				'remark' => '1',
				'created_by' => Auth::user()->username,
			]);

			$wjo->save();
			$wjo_log->save();

			$mail_to = [];
			$datas = [];

			if ($category == 'jig') {
				array_push($mail_to, 'danang.harianto@music.yamaha.com');
				array_push($mail_to, 'hartono@music.yamaha.com');
				array_push($mail_to, 'rano.anugrawan@music.yamaha.com');
			}else{
				array_push($mail_to, 'ardiyanto@music.yamaha.com');
			}

			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->select('lifetimes.*')->where('category',$category)->where('location',$location)->where('tag',$lifetime->tag)->first();
			$days = 0;
			if (strtolower($category) == 'screwdriver') {
				$details = DB::connection('ympimis_2')->select("SELECT COALESCE
					(
						SPLIT_STRING ( remark, '_', 1 ),
						DATE(
						NOW())) AS firsts,
					TIMESTAMPDIFF(
						DAY,
						COALESCE (
							SPLIT_STRING ( remark, '_', 1 ),
							DATE(
							NOW())),
						DATE(
						NOW())) AS diff 
				FROM
					lifetime_details 
				WHERE
					category = '".$category."' 
					AND location = '".$location."' 
					AND tag = '".$lifetime->tag."' 
					LIMIT 1");

				if (count($details) > 0) {
					$days = $details[0]->diff;
				}
			}

			$datas = array(
	            'lifetime' => $lifetime,
	            'category' => $category,
	            'location' => $location,
	            'days' => $days,
	            'reason' => $request->get('reason')
	          );

			// if ($new_lifetime == $lifetime_limit) {
				Mail::to($mail_to)->bcc(['ympi-mis-ML@music.yamaha.com'],'BCC')->send(new SendEmail($datas, 'lifetime_limit'));
			// }

			$response = array(
				'status' => true,
				'message' => 'Repair '.$category.' '.$location.' '.$lifetime->item_name.' Berhasil dibuat WJO',
				'remark' => 'Done'
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

	public function fetchReportLifetime($category,$location)
	{
		try {
			$lifetime = DB::connection('ympimis_2')->select("SELECT
				lifetime_details.category,
				lifetime_details.item_code,
				lifetime_details.item_name,
				lifetime_details.item_type,
				lifetime_details.item_index,
				lifetime_details.item_made_in,
				lifetime_details.location,
				lifetime_details.lifetime,
				0 AS `repair`,
				lifetime_details.created_at AS lifetime_at,
				lifetimes.product 
			FROM
				lifetime_details
				JOIN lifetimes ON lifetimes.item_code = lifetime_details.item_code 
			where lifetime_details.category = '".$category."'
				and lifetime_details.location = '".$location."'
				UNION ALL
			SELECT
				lifetime_logs.category,
				lifetime_logs.item_code,
				lifetime_logs.item_name,
				lifetime_logs.item_type,
				lifetime_logs.item_index,
				lifetime_logs.item_made_in,
				lifetime_logs.location,
				lifetime_logs.lifetime,
				lifetime_logs.`repair`,
				lifetime_logs.lifetime_at,
				lifetimes.product 
			FROM
				lifetime_logs
				JOIN lifetimes ON lifetimes.item_code = lifetime_logs.item_code
			where lifetime_logs.category = '".$category."'
				and lifetime_logs.location = '".$location."'");

			$repair = DB::connection('ympimis_2')->select("SELECT
				lifetime_repair_logs.*,
				lifetimes.product 
			FROM
				`lifetime_repair_logs`
				JOIN lifetimes ON lifetime_repair_logs.item_code = lifetimes.item_code
			where lifetime_repair_logs.category = '".$category."'
			and lifetime_repair_logs.location = '".$location."'");

			$response = array(
				'status' => true,
				'lifetime' => $lifetime,
				'repair' => $repair,
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

	public function printLabel($jig_id,$item_alias)
	{
		$connector = new WindowsPrintConnector('MIS');
		$printer = new Printer($connector);

		$printer->setJustification(Printer::JUSTIFY_CENTER);
		$printer->setTextSize(1, 1);
		$printer->text($jig_id.' '.$item_alias."\n");
		$printer->cut();
		$printer->close();
	}

	public function inputUseItem($category,$location,Request $request)
	{
		try {
			$id = $request->get('id');
			$employee_id = $request->get('employee_id');
			$employee_name = $request->get('employee_name');

			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('id',$id)->first();

			if ($lifetime) {
				$update = DB::connection('ympimis_2')->table('lifetimes')->where('id',$id)->update([
					'employee_id' => $employee_id,
					'employee_name' => $employee_name,
					'availability' => '1',
					'used_start' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$lifetimedetail = DB::connection('ympimis_2')->table('lifetime_details')->insert([
					'category' => $lifetime->category,
					'tag' => $lifetime->tag,
					'item_code' => $lifetime->item_code,
					'item_name' => $lifetime->item_name,
					'item_index' => $lifetime->item_index,
					'item_type' => $lifetime->item_type,
					'item_made_in' => $lifetime->item_made_in,
					'location' => $lifetime->location,
					'lifetime' => 1,
					'created_by' => $employee_id,
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$response = array(
					'status' => true,
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Not Found'
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function inputUnUseItem($category,$location,Request $request)
	{
		try {
			$id = $request->get('id');
			$employee_id = $request->get('employee_id');
			$employee_name = $request->get('employee_name');
			$subleader_id = $request->get('subleader_id');
			$subleader_name = $request->get('subleader_name');

			$lifetime = DB::connection('ympimis_2')->table('lifetimes')->where('id',$id)->first();

			if ($lifetime) {
				$lifetimedetail = DB::connection('ympimis_2')->table('lifetime_details')->where('tag',$lifetime->tag)->where('remark',null)->update([
					'employee_id' => $employee_id,
					'employee_name' => $employee_name,
					'subleader_id' => $subleader_id,
					'subleader_name' => $subleader_name,
					'remark' => $lifetime->used_start.'_'.date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$update = DB::connection('ympimis_2')->table('lifetimes')->where('id',$id)->update([
					'employee_id' => null,
					'employee_name' => null,
					'availability' => '3',
					'used_start' => null,
					'updated_at' => date('Y-m-d H:i:s'),
				]);

				$response = array(
					'status' => true,
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Not Found'
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}

	public function scanKanbanLifetime(Request $request)
	{
		try {
			$tags = DB::connection('mysql2')
                ->table('completions')
                ->select('materials.material_number',
                    'materials.description as material_description',
                    // 'ympimis.materials.model as model',
                    // 'ympimis.materials.hpl as hpl',
                    'completions.lot_completion as quantity')
                ->where('barcode_number', $request->get('tag'))
                ->join('materials', 'materials.id', 'material_id')
            // ->join('ympimis.materials','ympimis.materials.material_number','kitto.materials.material_number')
                ->first();

			if ($tags) {
				$materials = Material::where('material_number',$tags->material_number)->first();
				$response = array(
					'status' => true,
					'materials' => $materials,
					'tags' => $tags,
				);
				return Response::json($response);
			}else{
				$response = array(
					'status' => false,
					'message' => 'Barcode Not Found',
				);
				return Response::json($response);
			}
		} catch (\Exception $e) {
			$response = array(
				'status' => false,
				'message' => $e->getMessage(),
			);
			return Response::json($response);
		}
	}
}
