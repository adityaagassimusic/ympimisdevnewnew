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
use App\PushBlockParameter;
use App\CodeGenerator;
use App\User;
use App\MaterialPlantDataList;
use App\RcPushPullLog;
use App\RcBoxResult;
use App\RcNgTrend;
use App\RcCameraKangoLog;
use App\PlcCounter;
use App\PushBlockTorqueTemp;
use App\PushBlockTorque;
use App\InjectionTag;
use App\RcAssyInitial;
use App\RcKensaInitial;
use App\RcKensa;
use App\RcReturnLog;
use App\RcQaAudit;
use App\RcNgBox;
use App\InjectionHistoryMoldingTemp;
use App\InjectionMoldingMaster;
use App\Libraries\ActMLEasyIf;
use App\EmployeeSync;
use App\Employee;
use Response;
use DataTables;
use Excel;
use File;
use DateTime;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\InjectionInventory;
use App\Inventory;
use App\PushBlockNotProcess;
use App\InjectionCdmCheck;
use App\WeeklyCalendar;
use App\TrainingReport;
use App\NgList;

class RecorderProcessController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      $this->product_type = ['YRS-27III //J',
                            'YRS-28BIII //J',
                            'YRF-21//ID',
                            'YRS-20BB //ID',
                            'YRS-20BG //ID',
                            'YRS-20BP //ID',
                            'YRS-23 //ID',
                            'YRS-23CA //ID',
                            'YRS-24B //ID',
                            'YRS-23K//ID',
                            'YRS-24BK//ID',
                            'YRS-20GP //ID',
                            'YRS-20GG //ID',
                            'YRS-20GB //ID',
                            'YRS-20BR //ID',
                            'YRS-23  //WOFB   ID',
                            'YRS-24BUKII //ID',
                            'YRS-24BCA //ID',
                            'YRS-23BR//ID',
                            'YRS-24BBR//ID',
                            'YRS-20BB // WOFB ID',
                            'YRS-20BG// WOFB ID',
                            'YRS-20BP// WOFB ID',
                            'YRS-20GBK//ID',
                            'YRS-20GGK//ID',
                            'YRS-20GPK//ID',
                            'YRS-24B // WOFB ID',
                            'YRF-21K//ID',
                            'YRS-24B//MX ID',
                            'YRS TRANSLUCENT (FSA)',
                            'YRS BROWN (FSA)',
                            'YRS IVORY (FSA)',
                            'YRF-21 (FSA)'];

      $this->mesin = ['#1',
                      '#2',
                      '#3',
                      '#4',
                      '#5',
                      '#6',
                      '#7',
                      '#8',
                      '#9',
                      '#10',
                      '#11',];

      $this->cavity = [
            '1-4',
            '5-8',
            '9-12',
            '13-16',
            '17-20',
            '01-04',
            '1-6',
            '11-16',
            '21-22',
            '9-16',
            '1-8',
            '17-24'
            ];

      $this->mesins = ['Mesin 1',
                      'Mesin 2',
                      'Mesin 3',
                      'Mesin 4',
                      'Mesin 5',
                      'Mesin 6',
                      'Mesin 7',
                      'Mesin 8',
                      'Mesin 9',
                      'Mesin 10',
                      'Mesin 11',];                      

      $this->molding = ['HJ 01',
                      'HJ 02',
                      'HJ 03',
                      'HJ 04',
                      'HJ 05',
                      'BL 01',
                      'BL 02',
                      'BL 03',
                      'BL 04'];

      $this->mail = ['budhi.apriyanto@music.yamaha.com',
                    'takashi.ohkubo@music.yamaha.com',
                    'imbang.prasetyo@music.yamaha.com',
                    'eko.prasetyo.wicaksono@music.yamaha.com',
                    'andik.yayan@music.yamaha.com',
                    'aditya.agassi@music.yamaha.com',
                    'susilo.basri@music.yamaha.com',
                    'darma.bagus@music.yamaha.com',];
      // $this->array_push_pull = [];
      $this->checked_at_time = date('Y-m-d H:i:s');
    }

  public function index(){
		return view('recorder.process.index')
    ->with('title', 'Recorder - Assembly Process')
    ->with('title_jp', 'リコーダー組立工程')
    ->with('page', 'Recorder - Assembly Process')
    ->with('head', '');
	}

	public function index_push_block($remark){
		$name = Auth::user()->name;
    // if ($remark == 'After Injection') {
    //   $view = 'recorder.process.index_push_block_assy'; //upload excel + tag
    // }
    if ($remark == 'After Injection') {
      $view = 'recorder.process.index_push_block'; //upload excel
    }
    // if ($remark == 'After Injection') {
    //   $view = 'recorder.process.index_push_block2'; //existing
    // }
    // else if ($remark == 'First Shot Approval') {
    //   $view = 'recorder.process.index_push_block2'; //existing
    // }
    if ($remark == 'First Shot Approval') {
      $view = 'recorder.process.index_push_block'; //upload excel
    }
		return view($view)
    ->with('page', 'Process Assy Recorder')
    ->with('head', 'Recorder Push Block Check')
    ->with('title', 'Recorder Push Block Check')
    ->with('title_jp', 'リコーダープッシュブロック検査')
    ->with('name', $name)
    ->with('product_type', $this->product_type)
    ->with('mesin', $this->mesin)
    ->with('mesin2', $this->mesin)
    ->with('mesin3', $this->mesin)
    ->with('batas_bawah', '3')
    ->with('batas_atas', '17')
    ->with('batas_tinggi', '0.2')
    ->with('batas_bawah2', '5.6')
    ->with('batas_atas2', '15.8')
    ->with('remark', $remark);
	}

  public function scanPushPullOperator(Request $request){

    $tag = $request->get('employee_id');

    if(strlen($tag) > 9){
      $tag = substr($tag,0,10);
    }

    $employee = db::table('employees')->where('tag', 'like', '%'.$tag.'%')->first();

    if($employee != null ){
      $response = array(
        'status' => true,
        'message' => 'Logged In',
        'employee' => $employee
      );
      return Response::json($response);
    }
    else{
      $response = array(
        'status' => false,
        'message' => 'Tag Invalid'
      );
      return Response::json($response);
    }
  }

	function fetch_push_block(Request $request)
    {
          try{
          	$no_cavity = $request->get("no_cavity");

            $detail = PushBlockMaster::find($no_cavity);
        	$data = array('type' => $detail->type,
        			        'no_cavity' => $detail->no_cavity,
                      'cavity_1' => $detail->cavity_1,
                      'cavity_2' => $detail->cavity_2,
                      'cavity_3' => $detail->cavity_3,
                      'cavity_4' => $detail->cavity_4);

            $response = array(
              'status' => true,
              'datas' => $data,
              'id' => $detail->id,
              'cavity_1' => $detail->cavity_1,
              'cavity_2' => $detail->cavity_2,
              'cavity_3' => $detail->cavity_3,
              'cavity_4' => $detail->cavity_4,
              'cavity_5' => $detail->cavity_5,
              'cavity_6' => $detail->cavity_6,
              'cavity_7' => $detail->cavity_7,
              'cavity_8' => $detail->cavity_8,
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function fetch_cavity_detail(Request $request)
    {
      try {
        $type = $request->get("type");
        $no_cavity = $request->get("no_cavity");

        $detail = PushBlockMaster::where('type',$type)->where('no_cavity',$no_cavity)->first();
        $data = array('type' => $detail->type,
                    'no_cavity' => $detail->no_cavity,
                    'cavity_1' => $detail->cavity_1,
                    'cavity_2' => $detail->cavity_2,
                    'cavity_3' => $detail->cavity_3,
                    'cavity_4' => $detail->cavity_4);

        $response = array(
          'status' => true,
          'datas' => $data,
          'id' => $detail->id,
          'cavity_1' => $detail->cavity_1,
          'cavity_2' => $detail->cavity_2,
          'cavity_3' => $detail->cavity_3,
          'cavity_4' => $detail->cavity_4,
          'cavity_5' => $detail->cavity_5,
          'cavity_6' => $detail->cavity_6,
          'cavity_7' => $detail->cavity_7,
          'cavity_8' => $detail->cavity_8,
        );
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
          'status' => false,
          'datas' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    function fetch_mesin_parameter(Request $request)
    {
          try{
            $mesin = $request->get("mesin");
            $remark = $request->get("remark");
            $part = $request->get("part_name").' '.$request->get("part_type");

            $first = "'".date('Y-m-01')."'";
            $last = "'".date('Y-m-d')."'";

            $best = DB::SELECT("SELECT
              * 
            FROM
              `rc_ng_trends` 
            WHERE
              rc_ng_trends.date BETWEEN ".date('Y-m-d',strtotime(str_replace("'", "", $first).' - 6 MONTHS'))."
              AND ".$last."
              AND part = '".$part."'
              AND mesin = '".str_replace('#', 'Mesin ', $mesin)."'
            ORDER BY
              qty_ng ASC
              LIMIT 5");

            // $parameter_best = DB::SELECT("SELECT
            //   * 
            // FROM
            //   push_block_parameters 
            // WHERE
            //   check_date <= '".explode('_', $best[0]->person_injeksi)[1]."'
            //   AND mesin = '#".$mesin."' 
            // ORDER BY
            //   id DESC 
            //   LIMIT 1");

            // var_dump(explode('_', $best[0]->person_injeksi)[1]);

            if (count($best) > 0) {
              $detail = PushBlockParameter::where('mesin',$mesin)->where('product_type',$part)->where('check_date','<=',explode('_', $best[0]->person_injeksi)[1])->orderBy('id','desc')->first();
              $detail_last = PushBlockParameter::where('mesin',$mesin)->where('product_type',$part)->orderBy('id','desc')->first();
            }else{
              $detail = null;
              
            }

            $response = array(
              'status' => true,
              // 'id' => $detail->id,
              'best' => $best,
              'detail' => $detail,
              'detail_last' => $detail_last
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

    function fetch_mesin_parameter_new(Request $request)
    {
          try{
            $mesin = $request->get("mesin");
            $remark = $request->get("remark");
            $part = $request->get("part_name").' '.$request->get("part_type");

            $first = "'".date('Y-m-01')."'";
            $last = "'".date('Y-m-d')."'";


            $best = DB::SELECT("SELECT
              * 
            FROM
              `rc_ng_trends` 
            WHERE
              rc_ng_trends.date BETWEEN ".date('Y-m-d',strtotime(str_replace("'", "", $first).' - 6 MONTHS'))."
              AND ".$last."
              AND part = '".$part."'
              AND mesin = '".str_replace('#', 'Mesin ', $mesin)."'
            ORDER BY
              qty_ng ASC
              LIMIT 5");

            // $parameter_best = DB::SELECT("SELECT
            //   * 
            // FROM
            //   push_block_parameters 
            // WHERE
            //   check_date <= '".explode('_', $best[0]->person_injeksi)[1]."'
            //   AND mesin = '#".$mesin."' 
            // ORDER BY
            //   id DESC 
            //   LIMIT 1");

            // var_dump(explode('_', $best[0]->person_injeksi)[1]);

            $details = [];

            if (count($best) > 0) {
              for ($i=0; $i < count($best); $i++) { 
                $detail = PushBlockParameter::where('mesin',$mesin)->where('product_type',$part)->where('check_date','<=',explode('_', $best[$i]->person_injeksi)[1])->orderBy('id','desc')->first();
                if ($detail != null) {
                  $date_param = date('Y-m-d',strtotime($detail->check_date));
                  $detailsss = array('detail' => $detail,
                  'date' => $best[$i]->date,
                  'date_param' => $date_param,
                  'person' => $best[$i]->person,
                  'product' => $best[$i]->product,
                  'part' => $best[$i]->part,
                  'molding' => $best[$i]->molding,
                  'qty_ng' => $best[$i]->qty_ng, );

                  array_push($details, $detailsss);
                }
              }
              $detail_last = PushBlockParameter::where('mesin',$mesin)->where('product_type',$part)->orderBy('id','desc')->first();
            }else{
              $detail = null;
            }

            $response = array(
              'status' => true,
              // 'id' => $detail->id,
              'best' => $best,
              'detail' => $details,
              'detail_last' => $detail_last
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



    function create(Request $request)
    {
        	try{    
              $id_user = Auth::id();
              $push_pull = $request->get('push_pull');
              $judgement = $request->get('judgement');
              $ketinggian = $request->get('ketinggian');
              $judgementketinggian = $request->get('judgementketinggian');
              $head = $request->get('head');
              $block = $request->get('block');
              $push_block_code = $request->get('push_block_code');
              $status_input = 1;
              if ($request->get('product_type') == "YRF-21K//ID" || $request->get('product_type') == "YRF-21//ID" || $request->get('product_type') == "YRF-21 (FSA)" || $request->get('product_type') == "YRF21") {
                for($i = 0; $i<8;$i++){
                  $check_date = $request->get('check_date');
                  $product_type = $request->get('product_type');
                  PushBlockRecorder::create([
                    'push_block_code' => $request->get('push_block_code'),
                    'push_block_id_gen' => $request->get('push_block_id_gen'),
                      'check_date' => $request->get('check_date'),
                      'injection_date_head' => $request->get('injection_date_head'),
                      'mesin_head' => $request->get('mesin_head'),
                      'injection_date_block' => $request->get('injection_date_block'),
                      'mesin_block' => $request->get('mesin_block'),
                      'product_type' => $request->get('product_type'),
                      'head' => $head[$i],
                      'block' => $block[$i],
                      'push_pull' => $push_pull[$i],
                      'judgement' => $judgement[$i],
                      'ketinggian' => $ketinggian[$i],
                      'judgement2' => $judgementketinggian[$i],
                      'pic_check' => $request->get('pic_check'),
                      'created_by' => $id_user
                  ]);

                  $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->delete();
                }
              }else{
                for($i = 0; $i<16;$i++){
                  $check_date = $request->get('check_date');
                  $product_type = $request->get('product_type');
                  PushBlockRecorder::create([
                    'push_block_code' => $request->get('push_block_code'),
                    'push_block_id_gen' => $request->get('push_block_id_gen'),
                      'check_date' => $request->get('check_date'),
                      'injection_date_head' => $request->get('injection_date_head'),
                      'mesin_head' => $request->get('mesin_head'),
                      'injection_date_block' => $request->get('injection_date_block'),
                      'mesin_block' => $request->get('mesin_block'),
                      'product_type' => $request->get('product_type'),
                      'head' => $head[$i],
                      'block' => $block[$i],
                      'push_pull' => $push_pull[$i],
                      'judgement' => $judgement[$i],
                      'ketinggian' => $ketinggian[$i],
                      'judgement2' => $judgementketinggian[$i],
                      'pic_check' => $request->get('pic_check'),
                      'created_by' => $id_user
                  ]);

                  $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->delete();
                }
              }

              $response = array(
                'status' => true,
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

    function create_temp(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $head = $request->get('head');
              $block = $request->get('block');
              $push_block_code = $request->get('push_block_code');
              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');
              if ($request->get('product_type') == "YRF-21K//ID" || $request->get('product_type') == "YRF-21//ID" || $request->get('product_type') == "YRF-21 (FSA)" || $request->get('product_type') == "YRF21") {
                for($i = 0; $i<8;$i++){
                  $check_date = $request->get('check_date');
                  $product_type = $request->get('product_type');
                  PushBlockRecorderTemp::create([
                    'push_block_code' => $request->get('push_block_code'),
                    'push_block_id_gen' => $push_block_id_gen,
                      'check_date' => $request->get('check_date'),
                      'injection_date_head' => $request->get('injection_date_head'),
                      'mesin_head' => $request->get('mesin_head'),
                      'injection_date_block' => $request->get('injection_date_block'),
                      'mesin_block' => $request->get('mesin_block'),
                      'product_type' => $request->get('product_type'),
                      'head' => $head[$i],
                      'block' => $block[$i],
                      'pic_check' => $request->get('pic_check'),
                      'created_by' => $id_user
                  ]);
                }
              }else{
                for($i = 0; $i<16;$i++){
                  $check_date = $request->get('check_date');
                  $product_type = $request->get('product_type');
                  PushBlockRecorderTemp::create([
                    'push_block_code' => $request->get('push_block_code'),
                    'push_block_id_gen' => $push_block_id_gen,
                      'check_date' => $request->get('check_date'),
                      'injection_date_head' => $request->get('injection_date_head'),
                      'mesin_head' => $request->get('mesin_head'),
                      'injection_date_block' => $request->get('injection_date_block'),
                      'mesin_block' => $request->get('mesin_block'),
                      'product_type' => $request->get('product_type'),
                      'head' => $head[$i],
                      'block' => $block[$i],
                      'pic_check' => $request->get('pic_check'),
                      'created_by' => $id_user
                  ]);
                }
              }

              $response = array(
                'status' => true,
                'message' => 'Success Create Temp',
                'push_block_id_gen' => $push_block_id_gen
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

    public function get_temp(Request $request){
        $array_head = $request->get('array_head');
        $array_block = $request->get('array_block');
        $remark = $request->get('remark');
        $product_type = $request->get('product_type');

        $temp = [];

        // $ng_temp = PushBlockRecorderTemp::where('mesin',$mesin)->get();
        if ($request->get('product_type') == "YRF-21K//ID" || $request->get('product_type') == "YRF-21//ID" || $request->get('product_type') == "YRF-21 (FSA)" || $request->get('product_type') == "YRF21") {
          for($i = 0; $i < 8; $i++){
            for($j = 0; $j < 2; $j++){
              $temptemp = PushBlockRecorderTemp::where('head',$array_head[$j])->where('block',$array_block[$i])->where('push_block_code',$remark)->where('product_type',$product_type)->get();
              if (count($temptemp) > 0) {
                $temp[] = $temptemp;
              }
            }
          }
        }else{
          for($i = 0; $i < 8; $i++){
            for($j = 0; $j < 4; $j++){
              $temptemp = PushBlockRecorderTemp::where('head',$array_head[$j])->where('block',$array_block[$i])->where('push_block_code',$remark)->where('product_type',$product_type)->get();
              if (count($temptemp) > 0) {
                $temp[] = $temptemp;
              }
            }
          }
        }

        $response = array(
            'status' => true,            
            'datas' => $temp,
            'message' => 'Success get Temp'
        );
        return Response::json($response);
    }

    function update_temp(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $push_pull = $request->get('push_pull');
              $judgement = $request->get('judgement');
              $ketinggian = $request->get('ketinggian');
              $judgementketinggian = $request->get('judgementketinggian');
              $head = $request->get('head');
              $block = $request->get('block');
              $push_block_code = $request->get('push_block_code');
              $notes = $request->get('notes');
              if ($request->get('product_type') == "YRF-21K//ID" || $request->get('product_type') == "YRF-21//ID" || $request->get('product_type') == "YRF-21 (FSA)" || $request->get('product_type') == "YRF21") {
                for($i = 0; $i<8;$i++){
                  $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->get();
                  foreach ($temptemp as $key) {
                    $update = PushBlockRecorderTemp::find($key->id);
                    $update->push_pull = $push_pull[$i];
                    $update->judgement = $judgement[$i];
                    $update->ketinggian = $ketinggian[$i];
                    $update->judgement2 = $judgementketinggian[$i];
                    $update->notes = $notes;
                    $update->save();
                  }
                }
              }else{
                for($i = 0; $i<16;$i++){
                  $temptemp = PushBlockRecorderTemp::where('head',$head[$i])->where('block',$block[$i])->where('push_block_code',$push_block_code)->get();
                  foreach ($temptemp as $key) {
                    $update = PushBlockRecorderTemp::find($key->id);
                    $update->push_pull = $push_pull[$i];
                    $update->judgement = $judgement[$i];
                    $update->ketinggian = $ketinggian[$i];
                    $update->judgement2 = $judgementketinggian[$i];
                    $update->notes = $notes;
                    $update->save();
                  }
                }
              }

              $response = array(
                'status' => true,
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

    function create_resume(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $push_pull_ng_name = $request->get('push_pull_ng_name');
              $push_pull_ng_value = $request->get('push_pull_ng_value');
              $height_ng_name = $request->get('height_ng_name');
              $height_ng_value = $request->get('height_ng_value');
              $head = $request->get('head');
              $block = $request->get('block');
              $remark = $request->get('remark');
              $notes = $request->get('notes');

              PushBlockRecorderResume::create([
                'remark' => $remark,
                'push_block_id_gen' => $request->get('push_block_id_gen'),
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $push_pull_ng_name,
                  'push_pull_ng_value' => $push_pull_ng_value,
                  'height_ng_name' => $height_ng_name,
                  'height_ng_value' => $height_ng_value,
                  'jumlah_cek' => '32',
                  'pic_check' => $request->get('pic_check'),
                  'notes' => $notes,
                  'created_by' => $id_user
              ]);

              // if ($remark == 'After Injection') {
              //   $tag_head = InjectionTag::where('tag',$request->get('tag_head'))->first();
              //   $tag_block = InjectionTag::where('tag',$request->get('tag_block'))->first();
              // }

              $contactList = [];
              $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';

              if($push_pull_ng_name != 'OK'){
                $data_push_pull = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'push_pull_ng_name' => $request->get('push_pull_ng_name2'),
                  'push_pull_ng_value' => $request->get('push_pull_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                    Mail::to($this->mail)->bcc($contactList,'Contact List')->send(new SendEmail($data_push_pull, 'push_pull_check'));
                // if ($remark == 'After Injection') {
                //   $tag_head->push_pull_check = $push_pull_ng_name.'_'.$push_pull_ng_value;
                //   $tag_block->push_pull_check = $push_pull_ng_name.'_'.$push_pull_ng_value;
                // }
              }else{
                // if ($remark == 'After Injection') {
                //   $tag_head->push_pull_check = 'OK';
                //   $tag_block->push_pull_check = 'OK';
                // }
              }

              if($height_ng_name != 'OK'){
                $data_height = array(
                  'push_block_code' => $remark,
                  'check_date' => $request->get('check_date'),
                  'injection_date_head' => $request->get('injection_date_head'),
                  'mesin_head' => $request->get('mesin_head'),
                  'injection_date_block' => $request->get('injection_date_block'),
                  'mesin_block' => $request->get('mesin_block'),
                  'product_type' => $request->get('product_type'),
                  'head' => $head,
                  'block' => $block,
                  'height_ng_name' => $request->get('height_ng_name2'),
                  'height_ng_value' => $request->get('height_ng_value2'),
                  'pic_check' => $request->get('pic_check'),
                );
                    Mail::to($this->mail)->bcc($contactList,'Contact List')->send(new SendEmail($data_height, 'height_check'));
                // if ($remark == 'After Injection') {
                //   $tag_block->height_check = $height_ng_name.'_'.$height_ng_value;
                //   $tag_head->height_check = $height_ng_name.'_'.$height_ng_value;
                // }
              }else{
                // if ($remark == 'After Injection') {
                //   $tag_block->height_check = 'OK';
                //   $tag_head->height_check = 'OK';
                // }
              }

              // if ($remark == 'After Injection') {
              //   $tag_head->save();
              //   $tag_block->save();
              // }

              $response = array(
                'status' => true,
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

    public function return_completion(Request $request)
    {
      try {        
        $tag = InjectionTag::where('tag',$request->get('tag'))->first();
        $tag->shot = $tag->shot-$request->get('quantity');
        $tag->save();

        $inventory = Inventory::firstOrNew(['plant' => '8190', 'material_number' => $request->get('material'), 'storage_location' => 'RC91']);
            $inventory->quantity = ($inventory->quantity-$request->get('quantity'));
            $inventory->save();

        $injectionInventory = InjectionInventory::firstOrNew(['material_number' => $request->get('material'), 'location' => 'RC91']);
            $injectionInventory->quantity = ($injectionInventory->quantity-$request->get('quantity'));
            $injectionInventory->save();

        $response = array(
          'status' => true,
          'message' => 'Return Success',
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

    function create_parameter(Request $request)
    {
          try{    
              $id_user = Auth::id();

              $name = Auth::user()->name;

              $front = 'FSA';

              if ($request->get('pic_check') == null) {
                $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$name;
              }else{
                $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');
              }

              PushBlockParameter::create([
                'molding_code' => $request->get('molding_code'),
                'push_block_code' => $request->get('push_block_code'),
                'push_block_id_gen' => $push_block_id_gen,
                  'check_date' => $request->get('check_date'),
                  'reason' => $request->get('reason'),
                  'product_type' => $request->get('product_type'),
                  'mesin' => $request->get('mesin'),
                  'molding' => $request->get('molding'),
                  'nh' => $request->get('nh'),
                  'h1' => $request->get('h1'),
                  'h2' => $request->get('h2'),
                  'h3' => $request->get('h3'),
                  'dryer' => $request->get('dryer'),
                  'mtc_temp' => $request->get('mtc_temp'),
                  'mtc_press' => $request->get('mtc_press'),
                  'chiller_temp' => $request->get('chiller_temp'),
                  'chiller_press' => $request->get('chiller_press'),
                  'clamp' => $request->get('clamp'),
                  'ph4' => $request->get('ph4'),
                  'ph3' => $request->get('ph3'),
                  'ph2' => $request->get('ph2'),
                  'ph1' => $request->get('ph1'),
                  'trh3' => $request->get('trh3'),
                  'trh2' => $request->get('trh2'),
                  'trh1' => $request->get('trh1'),
                  'vh' => $request->get('vh'),
                  'pi' => $request->get('pi'),
                  'ls10' => $request->get('ls10'),
                  'vi5' => $request->get('vi5'),
                  'vi4' => $request->get('vi4'),
                  'vi3' => $request->get('vi3'),
                  'vi2' => $request->get('vi2'),
                  'vi1' => $request->get('vi1'),
                  'ls4' => $request->get('ls4'),
                  'ls4d' => $request->get('ls4d'),
                  'ls4c' => $request->get('ls4c'),
                  'ls4b' => $request->get('ls4b'),
                  'ls4a' => $request->get('ls4a'),
                  'ls5' => $request->get('ls5'),
                  've1' => $request->get('ve1'),
                  've2' => $request->get('ve2'),
                  'vr' => $request->get('vr'),
                  'ls31a' => $request->get('ls31a'),
                  'ls31' => $request->get('ls31'),
                  'srn' => $request->get('srn'),
                  'rpm' => $request->get('rpm'),
                  'bp' => $request->get('bp'),
                  'tr1inj' => $request->get('tr1inj'),
                  'tr3cool' => $request->get('tr3cool'),
                  'tr4int' => $request->get('tr4int'),
                  'mincush' => $request->get('mincush'),
                  'fill' => $request->get('fill'),
                  'circletime' => $request->get('circletime'),
                  'created_by' => $id_user
              ]);

              if ($request->get('molding_code') != '') {
                $molding = InjectionHistoryMoldingTemp::where('molding_code',$request->get('molding_code'))->first();
                $molding->status_parameter = date('Y-m-d H:i:s');
                $molding->save();
              }

              $response = array(
                'status' => true,
                'message' => 'Success Create Parameter',
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

    function fetchResume(Request $request)
    {
          try{
          	$head_id = $request->get("head_id");

            $detail = PushBlockMaster::find($head_id);

            $response = array(
              'status' => true,
              'datas' => $detail,
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function report_push_block($remark)
    {

        $data = array(
          'mesin' => $this->mesin,
          'cavity' => $this->cavity,
          'cavity2' => $this->cavity,
          'mesin2' => $this->mesin,
          'remark' => $remark,);
      return view('recorder.report.report_push_block', $data
        )->with('page', 'Report Push Block Check')->with('remark', $remark);
    }

    public function filter_report_push_block(Request $request,$remark)
    {
      $judgement = $request->get('judgement');
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(push_block_recorders.check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(push_block_recorders.check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(push_block_recorders.check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $judgement = '';
      if($request->get('judgement') != null){
        $judgements =  explode(",", $request->get('judgement'));
        for ($i=0; $i < count($judgements); $i++) {
          $judgement = $judgement."'".$judgements[$i]."'";
          if($i != (count($judgements)-1)){
            $judgement = $judgement.',';
          }
        }
        $judgementin = " and `push_block_recorders.judgement` in (".$judgement.") ";
        $judgementin2 = " or `push_block_recorders.judgement2` in (".$judgement.") ";
      }
      else{
        $judgementin = "";
        $judgementin2 = "";
      }

      if($request->get('mesin_head') != null){
        $mesin_head = " and `push_block_recorders.mesin_head` =  '".$request->get('mesin_head')."'";
      }
      else{
        $mesin_head = "";
      }

      if($request->get('mesin_block') != null){
        $mesin_block = " and `push_block_recorders.mesin_block` =  '".$request->get('mesin_block')."'";
      }
      else{
        $mesin_block = "";
      }

      if($request->get('cavity_head') != null){
        $cavity_head = " and push_block_recorder_resumes.`head` =  '".$request->get('cavity_head')."'";
      }
      else{
        $cavity_head = "";
      }

      if($request->get('cavity_block') != null){
        $cavity_block = " and push_block_recorder_resumes.`block` =  '".$request->get('cavity_block')."'";
      }
      else{
        $cavity_block = "";
      }

      $push_block_check = DB::SELECT("SELECT
        push_block_recorders.*,
        push_block_recorder_resumes.head AS cavity_head,
        push_block_recorder_resumes.block AS cavity_block 
      FROM
        `push_block_recorders`
        JOIN push_block_recorder_resumes ON push_block_recorder_resumes.push_block_id_gen = push_block_recorders.push_block_id_gen 
      WHERE
        push_block_code = '".$remark."' ".$date." ".$judgementin." ".$judgementin2." ".$mesin_head." ".$mesin_block." ".$cavity_head." ".$cavity_block."
      ORDER BY
        push_block_recorders.id DESC");

      $data = array('push_block_check' => $push_block_check,
        'mesin' => $this->mesin,
        'cavity' => $this->cavity,
        'cavity2' => $this->cavity,
        'mesin2' => $this->mesin,
        'remark' => $remark,);
      return view('recorder.report.report_push_block', $data
        )->with('page', 'Report Push Block Check')->with('remark', $remark);
    }

    public function resume_push_block($remark)
    {
        $push_block_check = PushBlockRecorderResume::where('remark',$remark)->orderBy('push_block_recorder_resumes.id','desc')->get();

        $auth = Auth::user()->role_code;

        $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,
                      'auth' => $auth,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'product_type' => $this->product_type);
      return view('recorder.report.resume_push_block', $data
        )->with('page', 'Resume Push Block Check')->with('remark', $remark);
    }

    public function filter_resume_push_block(Request $request,$remark)
    {
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      $auth = Auth::user()->role_code;

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      if($request->get('mesin_head') != null){
        $mesin_head = " and `mesin_head` =  '".$request->get('mesin_head')."'";
      }
      else{
        $mesin_head = "";
      }

      if($request->get('mesin_block') != null){
        $mesin_block = " and `mesin_block` =  '".$request->get('mesin_block')."'";
      }
      else{
        $mesin_block = "";
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorder_resumes` where remark = '".$remark."' ".$date." ".$mesin_head." ".$mesin_block." ORDER BY push_block_recorder_resumes.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,
                      'auth' => $auth,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'product_type' => $this->product_type);
      return view('recorder.report.resume_push_block', $data
        )->with('page', 'Resume Push Block Check')->with('remark', $remark);
    }

    public function get_resume(Request $request)
    {
      $id = $request->get('id');

      $data = DB::SELECT("SELECT * FROM `push_block_recorder_resumes` where id = '".$id."'");

      $response = array(
        'status' => true,
        'data' => $data,
      );
      return Response::json($response);
    }

    public function update_resume(Request $request,$id)
    {

      $resume_push_block = PushBlockRecorderResume::where('remark',$request->get('remark'))->where('id',$id)->first();
      $resume_push_block->injection_date_head = $request->get('injection_date_head');
      $resume_push_block->injection_date_block = $request->get('injection_date_block');
      $resume_push_block->mesin_head = $request->get('mesin_head');
      $resume_push_block->mesin_block = $request->get('mesin_block');
      $resume_push_block->product_type = $request->get('product_type');
      $pic_check = $resume_push_block->pic_check;

      if ($request->get('remark') == 'After Injection') {
        $front = 'AI';
      }else{
        $front = 'FSA';
      }

      $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$pic_check;

      $resume_push_block->push_block_id_gen = $push_block_id_gen;

      $push_block_check = PushBlockRecorder::where('push_block_code',$request->get('remark'))->get();
      foreach ($push_block_check as $key) {
        $push_block = PushBlockRecorder::find($key->id);
        $push_block->injection_date_head = $request->get('injection_date_head');
        $push_block->injection_date_block = $request->get('injection_date_block');
        $push_block->mesin_head = $request->get('mesin_head');
        $push_block->mesin_block = $request->get('mesin_block');
        $push_block->product_type = $request->get('product_type');
        $push_block->push_block_id_gen = $push_block_id_gen;
        try {
          $push_block->save();
        } catch (\Exception $e) {
          $response = array(
            'status' => false
          );
          return Response::json($response);
        }
      }

      try {
        $resume_push_block->save();
      } catch (\Exception $e) {
        $response = array(
          'status' => false
        );
        return Response::json($response);
      }

      $response = array(
        'status' => true,
      );
      return Response::json($response);
    }

    public function push_block_check_monitoring($remark){
      $name = Auth::user()->name;
      $date7days = DB::SELECT('select week_date from weekly_calendars where remark != "H" and week_date BETWEEN DATE(NOW()) - INTERVAL 7 DAY and DATE(NOW())');

      return view('recorder.display.push_block_check_monitoring')->with('page', 'Recorder Push Block Check Monitoring')->with('head', 'Recorder Process Monitoring')->with('title', 'Recorder Process Monitoring')->with('title_jp', 'リコーダー製造工程管理')->with('name', $name)->with('product_type', $this->product_type)->with('remark', $remark)->with('date', $date7days);
    }

    public function fetch_push_block_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m-d', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m-d');
    }

    // $monthTitle = date("F Y", strtotime($date));

    $date7days = DB::SELECT("select week_date from weekly_calendars where remark != 'H' and week_date BETWEEN DATE('".$date."') - INTERVAL 7 DAY and DATE('".$date."')");

    // $datenew[] = '';

    // for($i = 0;$i<count($date7days);$i++) {
    //   $datenew[] = date('d F Y',strtotime($date7days[$i]));
    // }

    $data = db::select("select week_date,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date) 
    as countfsa,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date and judgement = 'NG') 
    as countfsappng,
    (select count(*) from push_block_recorders where push_block_code = 'First Shot Approval' and DATE(check_date) = week_date and judgement2 = 'NG') 
    as countfsahng,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date) 
    as countai,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date and judgement = 'NG') 
    as countaippng,
    (select count(*) from push_block_recorders where push_block_code = 'After Injection' and DATE(check_date) = week_date and judgement2 = 'NG') 
    as countaihng,
    (select count(*) from rc_push_pull_logs where DATE(check_date) = week_date) 
    as countppassy,
    (select count(*) from rc_push_pull_logs where DATE(check_date) = week_date and judgement = 'NG') 
    as countppassyng,
    (select count(*) from rc_camera_kango_logs where DATE(check_date) = week_date) 
    as countck,
    (select count(*) from rc_camera_kango_logs where DATE(check_date) = week_date and judgement = 'NG') 
    as countckng
    from weekly_calendars 
    where remark != 'H'
    and week_date BETWEEN DATE('".$date."') - INTERVAL 7 DAY and DATE('".$date."')");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date7days' => $date7days,
      // 'datenew' => $datenew,
      'date' => $date,
      // 'remark' => $remark,
      // 'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function fetch_height_check_monitoring(Request $request,$remark){
    $date = '';
    if(strlen($request->get("bulan")) > 0){
      $date = date('Y-m-d', strtotime($request->get("bulan")));
    }else{
      $date = date('Y-m-d');
    }

    // $monthTitle = date("F Y", strtotime($date));

    $data = db::select("select DISTINCT(pic_check),
        sum(jumlah_cek) as jumlah_cek,
        COALESCE((select SUM((CHAR_LENGTH(push_pull_ng_value) - CHAR_LENGTH(REPLACE(push_pull_ng_value, ',', '')) + 1)) from push_block_recorder_resumes where push_pull_ng_value != 'OK' and remark = '".$remark."' and pic_check  = pushresume1.pic_check and check_date = '".$date."'),0)
        as jumlah_ng_push_pull,
        COALESCE((select SUM((CHAR_LENGTH(height_ng_value) - CHAR_LENGTH(REPLACE(height_ng_value, ',', '')) + 1)) from push_block_recorder_resumes where height_ng_value != 'OK' and remark = '".$remark."' and pic_check  = pushresume1.pic_check and check_date = '".$date."'),0)
        as jumlah_ng_height 
        from push_block_recorder_resumes as pushresume1
        where check_date = '".$date."' 
        and remark = '".$remark."' 
        GROUP BY pic_check");

    $response = array(
      'status' => true,
      'datas' => $data,
      'date' => $date,
      'remark' => $remark,
      // 'monthTitle' => $monthTitle,
    );
    return Response::json($response);
  }

  public function detail_monitoring(Request $request)
  {
    if ($request->get("tanggal") == null) {
      $tanggal = date('Y-m-d');
    }
    else{
      $tanggal = $request->get("tanggal");
    }
    $pic = $request->get("pic");
    $remark = $request->get("remark");

    $query = "select * from push_block_recorder_resumes where check_date = '".$tanggal."' and pic_check = '".$pic."' and remark = '".$remark."'";

    $detail = db::select($query);

    $response = array(
      'status' => true,
      'tanggal' => $tanggal,
      'pic' => $pic,
      'remark' => $remark,
      'lists' => $detail,
    );
    return Response::json($response);
  }

  public function detail_monitoring2(Request $request)
  {
    $tanggal = $request->get("tanggal");
    $judgement = $request->get("judgement");
    if($judgement == 'Jumlah OK'){
      $jdgm = 'OK';
    }
    else{
      $jdgm = 'NG';
    }
    $remark = $request->get("remark");

    $query = "SELECT * FROM `push_block_recorders` where date(check_date) = '".$tanggal."' and judgement2 = '".$jdgm."' and push_block_code = '".$remark."'";

    $detail = db::select($query);

    $response = array(
      'status' => true,
      'jdgm' => $jdgm,
      'lists' => $detail,
    );
    return Response::json($response);
  }

  public function print_report_push_block(Request $request,$remark)
    {
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $push_block_check = DB::SELECT("SELECT * FROM `push_block_recorders` where push_block_code = '".$remark."' ".$date." ORDER BY push_block_recorders.id desc");

      $data = array('push_block_check' => $push_block_check,
                      'remark' => $remark,);
      return view('recorder.report.print_push_block', $data
        )->with('page', 'Print Push Block Check')->with('remark', $remark);
    }

    function get_push_pull(Request $request)
    {
          try{
            $detail = PushBlockRecorder::find($request->get("id"));
            $data = array('push_block_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'injection_date_head' => $detail->injection_date_head,
                          'mesin_head' => $detail->mesin_head,
                          'injection_date_block' => $detail->injection_date_block,
                          'mesin_block' => $detail->mesin_block,
                          'product_type' => $detail->product_type,
                          'head' => $detail->head,
                          'block' => $detail->block,
                          'push_pull' => $detail->push_pull,
                          'judgement' => $detail->judgement,
                          'ketinggian' => $detail->ketinggian,
                          'judgement2' => $detail->judgement2,
                          'pic_check' => $detail->pic_check);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    function update(Request $request,$id)
    {
        try{
                $push_pull = PushBlockRecorder::find($id);
                $push_pull->push_pull = $request->get('push_pull');
                $push_pull->judgement = $request->get('judgement');
                $push_pull->ketinggian = $request->get('ketinggian');
                $push_pull->judgement2 = $request->get('judgement2');
                $push_pull->save();

               $response = array(
                'status' => true,
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

    public function index_push_pull(){
      $name = Auth::user()->name;
      return view('recorder.process.index_push_pull')->with('page', 'Process Assy Recorder')->with('head', 'Recorder Camera Kango Check')->with('title', 'Recorder Camera Kango Check')->with('title_jp', 'リコーダープッシュプールチェック')->with('name', $name)->with('product_type', $this->product_type)->with('batas_bawah', '3')->with('batas_atas', '17');
    }

    public function fetchResultPushPull()
    {
      try{
            $detail = RcPushPullLog::orderBy('id','DESC')->get();
            $detail2 = RcPushPullLog::get();

            $response = array(
              'status' => true,
              'data' => $detail,
              'data2' => $detail2
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function fetchResultCamera()
    {
      try{
            $detail = RcCameraKangoLog::orderBy('id','DESC')->get();
            $detail_middle = RcCameraKangoLog::where('remark','Middle')->get();
            $detail_stamp = RcCameraKangoLog::where('remark','Stamp')->get();

            $response = array(
              'status' => true,
              'data' => $detail,
              'data_middle' => $detail_middle,
              'data_stamp' => $detail_stamp
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Name already exist",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Update  Error.",
            );
             return Response::json($response);
            }
        }
    }

    public function email($value,$judgement,$model,$checked_at,$pic_check,$remark)
    {
      // $bodyHtml2 = "<html><h2>NG Report of Push Pull Check Recorder リコーダープッシュプールチェック</h2><p>Model : ".$model."</p><p>Check Date : ".$checked_at."</p><p>Value : ".$value."</p><p>Judgement : ".$judgement."</p></html>";

      // $mail_to = 'budhi.apriyanto@music.yamaha.com';

      // Mail::raw([], function($message) use($bodyHtml2,$mail_to) {
      //     $message->from('ympimis@gmail.com', 'PT. Yamaha Musical Products Indonesia');
      //     $message->to($mail_to);
      //     $message->subject('NG Report of Recorder Push Pull Check');
      //     $message->setBody($bodyHtml2, 'text/html' );
      //     // $message->addPart("5% off its awesome\n\nGo get it now!", 'text/plain');
      // });

      $data_push_pull = array('value' => $value,
      'judgement' => $judgement,
      'checked_at' => $checked_at,
      'model' => $model,
      'remark' => $remark,
      'pic_check' => $pic_check, );
      // var_dump($data_push_pull);
      // foreach ($data_push_pull as $key) {
        // var_dump($data_push_pull['judgement']);
      // }

      foreach($this->mail as $mail_to){
          Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
      }

      //http://172.17.128.87/miraidev/public/post/display/email/2.3/NG/YRS%2023%20IVORY/2020-01-21%2015:30:18
    }

    public function store_push_pull(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
          $plc = new ActMLEasyIf(2);
          $counter_push_pull = $plc->read_data('D275', 1);
          $value_push_pull = $plc->read_data('D250', 1);

          $data = $counter_push_pull[0];
          $datavalue = $value_push_pull[0] / 120;

          $valuebefore = 0;

          $plc_counter = PlcCounter::where('origin_group_code', '=', '072_1')->first();
          $pushpull = RcPushPullLog::orderBy('rc_push_pull_logs.id','DESC')->first();
          // var_dump($pushpull);
          // foreach ($pushpull as $pushpull) {
            if ($pushpull == null) {
              $valuebefore = 0;
            }else{
              $valuebefore = $pushpull->value_check;
            }
          // }
        // }
        
        // $datavalue = '2.9';
        // $data = 11;
        // var_dump($counter_push_pull);
        // var_dump($value_push_pull);


        if($plc_counter->plc_counter != $data){

          if(Auth::user()->role_code == "OP-Assy-RC"){

          if ($datavalue > 0) {
            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            if ($datavalue < 3 || $datavalue > 17) {
              $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $datavalue,
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Push Pull Check RC Assy',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            }else{
              $judgement = 'OK';
            }

            // if ($request->get('check_date') == $this->checked_at_time) {
            //   $array_push_pull[] = $datavalue;
            //   $this->checked_at_time = $request->get('check_date');
            // }
            // else{
            //   $datavalue = max($array_push_pull);
              if ($valuebefore != $datavalue) {
                $push_pull = RcPushPullLog::create(
                  [
                    'model' => $request->get('model'),
                    'check_date' => $request->get('check_date'),
                    'value_check' => $datavalue,
                    // 'value_check' => '2.9',
                    'judgement' => $judgement,
                    'pic_check' => $request->get('pic_check'),
                    'created_by' => $id,
                  ]
                );
              }
            // }

            

            try{
                $plc_counter->save();
                $push_pull->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'push_pull',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter,
              'counter' => $data,
              'value' => $datavalue,
              'judgement' => $judgement,
              'valuebefore' => $valuebefore
              // 'counter_all' => $counter_push_pull,
              // 'value_all' => $value_push_pull

            );
            return Response::json($response);
          }
            
          }
        }
        else{
          $response = array(
            'status' => false,
            'statusCode' => 'noData',
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function store_camera(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
        //   $plc = new ActMLEasyIf(2);
        //   $datas = $plc->read_data('D0', 1);
        //   $plc2 = new ActMLEasyIf(2);
        //   $datas2 = $plc2->read_data('D0', 1);

        $jumlah_perolehan = 0;

        $date = date('Y-m-d');
        $plc_counter = PlcCounter::where('origin_group_code', '=', '072_2')->first();

        $perolehan = DB::SELECT("select count(*) as jumlah from rc_camera_kango_logs where DATE(check_date) = '".$date."'");

        if (count($perolehan) > 0) {
          foreach ($perolehan as $key) {
            $jumlah_perolehan = $key->jumlah;
            // $jumlah_perolehan = convertToK($key->jumlah);
          }
        }
        else{
          $jumlah_perolehan = 0;
        }
        // $plc_counter2 = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // }
        // $data = $datas[0];
        // $data2 = $datas2[0];
        $filenamefix = '';
        foreach (glob(public_path('RCImages/Cam1/*.txt')) as $filename) {
            // echo "$filename size " . filesize($filename) . "\n";
          // var_dump();
          $data = substr($filename,-9,5);
          $filenamefix = $filename;
          // File::delete($filename);
        }
        // $data2 = 1;

        //MIDDLE
        if($plc_counter->plc_counter != $data){

          $file = File::get($filenamefix);
          $filepecah = explode(' ', $file);
          // var_dump($filepecah[3]);
          $judgement = substr($filepecah[3], 7,2);
          if(Auth::user()->role_code == "OP-Assy-RC"){

            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              // $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $request->get('value_check'),
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Middle Camera Check RC Assy',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            // }else{
              // $judgement = 'OK';
            // }

            $camera = RcCameraKangoLog::create(
              [
                'model' => $request->get('model'),
                'check_date' => $request->get('check_date'),
                // 'value_check' => $request->get('value_check'),
                'value_check' => $request->get('value_check'),
                'judgement' => $judgement,
                'remark' => 'Middle',
                'file' => 'RC_'.$data.'.bmp',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                File::delete(glob(public_path('RCImages/Cam1/*.txt')));
                // File::delete(glob(public_path('RCImages/Cam1/*.bmp')));
                $plc_counter->save();
                $camera->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
                'jumlah_perolehan' => $jumlah_perolehan
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'camera',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter,
              'judgement' => $judgement,
              'jumlah_perolehan' => $jumlah_perolehan
            );
            return Response::json($response);
          }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
            'data' => $data,
            'plc' => $plc_counter->plc_counter,
            'filename' => $filenamefix,
            'jumlah_perolehan' => $jumlah_perolehan
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'jumlah_perolehan' => $jumlah_perolehan,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function store_camera2(Request $request)
    {
      try{
        // if ($request->get('originGroupCode') =='072') {
        //   $plc = new ActMLEasyIf(2);
        //   $datas = $plc->read_data('D0', 1);
        //   $plc2 = new ActMLEasyIf(2);
        //   $datas2 = $plc2->read_data('D0', 1);
        $plc_counter = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // $plc_counter2 = PlcCounter::where('origin_group_code', '=', '072_3')->first();
        // }
        // $data = $datas[0];
        // $data2 = $datas2[0];
        $filenamefix = '';
        foreach (glob(public_path('RCImages/Cam2/*.txt')) as $filename) {
            // echo "$filename size " . filesize($filename) . "\n";
          // var_dump();
          $data = substr($filename,-9,5);
          $filenamefix = $filename;
          // File::delete($filename);
        }

        // $filenamefix2 = '';
        // foreach (glob(public_path('RCImages/Cam2/*.txt')) as $filename2) {
        //     // echo "$filename size " . filesize($filename) . "\n";
        //   // var_dump();
        //   $data2 = substr($filename2,-9,5);
        //   $filenamefix2 = $filename2;
        //   // File::delete($filename);
        // }
        // $data2 = 1;

        //STAMP
        if($plc_counter->plc_counter != $data){

          $file = File::get($filenamefix);
          $filepecah = explode(' ', $file);
          // var_dump($filepecah[3]);
          $judgement = substr($filepecah[3], 7,2);
          if(Auth::user()->role_code == "OP-Assy-RC"){

            $id = Auth::id();

            $plc_counter->plc_counter = $data;

            // if ($request->get('value_check') < 3 || $request->get('value_check') > 17) {
              // $judgement = 'NG';
              $data_push_pull = array(
                  'value' => $request->get('value_check'),
                  'judgement' => $judgement,
                  'checked_at' => $request->get('check_date'),
                  'model' => $request->get('model'),
                  'remark' => 'Stamp Camera Check RC Assy',
                  'pic_check' => $request->get('pic_check'), );
              // var_dump($data_push_pull);
              // foreach ($data_push_pull as $key) {
                // var_dump($data_push_pull['judgement']);
              // }

              // foreach($this->mail as $mail_to){
              //     Mail::to($mail_to)->send(new SendEmail($data_push_pull, 'push_pull'));
              // }
            // }else{
              // $judgement = 'OK';
            // }

            $camera = RcCameraKangoLog::create(
              [
                'model' => $request->get('model'),
                'check_date' => $request->get('check_date'),
                // 'value_check' => $request->get('value_check'),
                'value_check' => $request->get('value_check'),
                'judgement' => $judgement,
                'remark' => 'Stamp',
                'file' => 'RC_'.$data.'.bmp',
                'pic_check' => $request->get('pic_check'),
                'created_by' => $id,
              ]
            );

            try{
                File::delete(glob(public_path('RCImages/Cam2/*.txt')));
                // File::delete(glob(public_path('RCImages/Cam2/*.bmp')));
                $plc_counter->save();
                $camera->save();
            }
            catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }

            $response = array(
              'status' => true,
              'statusCode' => 'camera',
              'message' => 'Push Pull success',
              'data' => $plc_counter->plc_counter,
              'judgement' => $judgement
            );
            return Response::json($response);
          }
        }
        else{
          $response = array(
            'status' => true,
            'statusCode' => 'noData',
            'data' => $data,
            'plc' => $plc_counter->plc_counter,
            'filename' => $filenamefix
          );
          return Response::json($response);
        }
      }
      catch (\Exception $e){
        $response = array(
          'status' => false,
          'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function index_resume_assy_rc()
    {
      $code = RcPushPullLog::orderBy('rc_push_pull_logs.id', 'asc')
        ->get();


        return view('recorder.report.resume_assy_rc',array(
          'code' => $code,
        ))->with('page', 'Process Assy RC')->with('head', 'Assembly Process');
    }

    public function filter_assy_rc(Request $request){
      if($request->get('process') == 'Middle Check'){
        $rc_assy_detail = DB::table('rc_camera_kango_logs')
      
        ->select('rc_camera_kango_logs.id','rc_camera_kango_logs.model', 'rc_camera_kango_logs.value_check', 'rc_camera_kango_logs.judgement','rc_camera_kango_logs.pic_check', db::raw('date_format(rc_camera_kango_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_camera_kango_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_camera_kango_logs.created_at', 'desc')->where('rc_camera_kango_logs.remark', 'Middle')->get();
      }else if($request->get('process') == 'Stamp Check'){
        $rc_assy_detail = DB::table('rc_camera_kango_logs')
      
        ->select('rc_camera_kango_logs.id','rc_camera_kango_logs.model', 'rc_camera_kango_logs.value_check', 'rc_camera_kango_logs.judgement','rc_camera_kango_logs.pic_check', db::raw('date_format(rc_camera_kango_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_camera_kango_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_camera_kango_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_camera_kango_logs.created_at', 'desc')->where('rc_camera_kango_logs.remark', 'Stamp')->get();
      }else{
        $rc_assy_detail = DB::table('rc_push_pull_logs')
      
        ->select('rc_push_pull_logs.id','rc_push_pull_logs.model', 'rc_push_pull_logs.value_check', 'rc_push_pull_logs.judgement','rc_push_pull_logs.pic_check', db::raw('date_format(rc_push_pull_logs.created_at, "%d-%b-%Y") as st_date') );

        if(strlen($request->get('datefrom')) > 0){
          $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_push_pull_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        // if(strlen($request->get('code')) > 0){
        //   $code = $request->get('code');
        //   $rc_assy_detail = $rc_assy_detail->where('rc_push_pull_logs.process_code','=', $code );
        // }

        if(strlen($request->get('dateto')) > 0){
          $date_to = date('Y-m-d', strtotime($request->get('dateto')));
          $rc_assy_detail = $rc_assy_detail->where(DB::raw('DATE_FORMAT(rc_push_pull_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        $rc_detail = $rc_assy_detail->orderBy('rc_push_pull_logs.created_at', 'desc')->get();
      }
      

      return DataTables::of($rc_detail)
      ->addColumn('action', function($rc_detail){
        return '<a href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deleteConfirmation(id)" id="' . $rc_detail->id . '"><i class="glyphicon glyphicon-trash"></i></a>';
      })
      ->make(true);
    }

    public function index_rc_picking_result()
    {
      return view('recorder.display.rc_picking_result',array(
        // 'code' => $code,
      ))->with('page', 'Recorder Assembly Picking Result')->with('jp', '???')->with('head', 'Assembly Process');
    }

    public function fetch_rc_picking_result(Request $request)
    {
      $date = '';
      if(strlen($request->get("date")) > 0){
        $date = date('Y-m-d', strtotime($request->get("date")));
      }else{
        $date = date('Y-m-d');
      }

      // var_dump($date);

      $monthTitle = date("F Y", strtotime($date));

      // $datenew[] = '';

      // for($i = 0;$i<count($date7days);$i++) {
      //   $datenew[] = date('d F Y',strtotime($date7days[$i]));
      // }

      $data = db::select("select SUM(quantity) as plan,materials.surface,materials.key,CONCAT(materials.key,' - ',materials.surface) as colorkey,(select count(id) as actual from rc_camera_kango_logs where remark = 'Middle' and DATE(created_at) = '".$date."' and model = materials.surface and value_check = materials.key) as actual from production_schedules join materials on production_schedules.material_number = materials.material_number where due_date = '".$date."' and materials.origin_group_code = '072' and materials.category = 'FG' GROUP BY colorkey,surface,materials.key");

      $response = array(
        'status' => true,
        'datas' => $data,
        // 'date7days' => $date7days,
        // 'datenew' => $datenew,
        'date' => $date,
        // 'remark' => $remark,
        'monthTitle' => $monthTitle,
      );
      return Response::json($response);


    }

    public function indexMachineParameter($remark)
    {
      $parameters = PushBlockParameter::where('push_block_code',$remark)->WHERE(DB::RAW('DATE_FORMAT(check_date,"%Y-%m")'),date('Y-m'))->orderBy('push_block_parameters.id', 'desc')
        ->get();

        $molding = InjectionMoldingMaster::select('part')->get();

      return view('recorder.process.index_machine_parameter')
      ->with('page', 'Machine Parameter')
      ->with('head', 'Machine Parameter')
      ->with('title', 'Machine Parameter')
      ->with('title_jp', '機械条件')
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('molding', $molding)
      ->with('molding2', $molding)
      ->with('molding3', $molding)
      ->with('parameter', $parameters)
      ->with('remark', $remark)
      ->with('product_type', $this->product_type)
      ->with('product_type2', $this->product_type);
    }

    function get_parameter(Request $request)
    {
          try{
            $detail = PushBlockParameter::find($request->get("id"));
            $data = array('push_block_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'reason' => $detail->reason,
                          'product_type' => $detail->product_type,
                          'mesin' => $detail->mesin,
                          'molding' => $detail->molding,
                          'nh' => $detail->nh,
                          'h1' => $detail->h1,
                          'h2' => $detail->h2,
                          'h3' => $detail->h3,
                          'dryer' => $detail->dryer,
                          'mtc_temp' => $detail->mtc_temp,
                          'mtc_press' => $detail->mtc_press,
                          'chiller_temp' => $detail->chiller_temp,
                          'chiller_press' => $detail->chiller_press,
                          'clamp' => $detail->clamp,
                          'ph4' => $detail->ph4,
                          'ph3' => $detail->ph3,
                          'ph2' => $detail->ph2,
                          'ph1' => $detail->ph1,
                          'trh3' => $detail->trh3,
                          'trh2' => $detail->trh2,
                          'trh1' => $detail->trh1,
                          'vh' => $detail->vh,
                          'pi' => $detail->pi,
                          'ls10' => $detail->ls10,
                          'vi5' => $detail->vi5,
                          'vi4' => $detail->vi4,
                          'vi3' => $detail->vi3,
                          'vi2' => $detail->vi2,
                          'vi1' => $detail->vi1,
                          'ls4' => $detail->ls4,
                          'ls4d' => $detail->ls4d,
                          'ls4c' => $detail->ls4c,
                          'ls4b' => $detail->ls4b,
                          'ls4a' => $detail->ls4a,
                          'ls5' => $detail->ls5,
                          've1' => $detail->ve1,
                          've2' => $detail->ve2,
                          'vr' => $detail->vr,
                          'ls31a' => $detail->ls31a,
                          'ls31' => $detail->ls31,
                          'srn' => $detail->srn,
                          'rpm' => $detail->rpm,
                          'bp' => $detail->bp,
                          'tr1inj' => $detail->tr1inj,
                          'tr3cool' => $detail->tr3cool,
                          'tr4int' => $detail->tr4int,
                          'mincush' => $detail->mincush,
                          'fill' => $detail->fill,
                          'circletime' => $detail->circletime,
                        );

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (QueryException $beacon){
            $error_code = $beacon->errorInfo[1];
            if($error_code == 1062){
             $response = array(
              'status' => false,
              'datas' => "Gagal Ambil Data",
            );
             return Response::json($response);
           }
           else{
             $response = array(
              'status' => false,
              'datas' => "Gagal Ambil Data.",
            );
             return Response::json($response);
            }
        }
    }

    function update_parameter(Request $request,$id)
    {
          try{    
              $id_user = Auth::id();

              $parameter = PushBlockParameter::find($id);
              $parameter->reason = $request->get('reason');
              $parameter->product_type = $request->get('product_type');
              $parameter->mesin = $request->get('mesin');
              $parameter->molding = $request->get('molding');
              $parameter->nh = $request->get('nh');
              $parameter->h1 = $request->get('h1');
              $parameter->h2 = $request->get('h2');
              $parameter->h3 = $request->get('h3');
              $parameter->dryer = $request->get('dryer');
              $parameter->mtc_temp = $request->get('mtc_temp');
              $parameter->mtc_press = $request->get('mtc_press');
              $parameter->chiller_temp = $request->get('chiller_temp');
              $parameter->chiller_press = $request->get('chiller_press');
              $parameter->clamp = $request->get('clamp');
              $parameter->ph4 = $request->get('ph4');
              $parameter->ph3 = $request->get('ph3');
              $parameter->ph2 = $request->get('ph2');
              $parameter->ph1 = $request->get('ph1');
              $parameter->trh3 = $request->get('trh3');
              $parameter->trh2 = $request->get('trh2');
              $parameter->trh1 = $request->get('trh1');
              $parameter->vh = $request->get('vh');
              $parameter->pi = $request->get('pi');
              $parameter->ls10 = $request->get('ls10');
              $parameter->vi5 = $request->get('vi5');
              $parameter->vi4 = $request->get('vi4');
              $parameter->vi3 = $request->get('vi3');
              $parameter->vi2 = $request->get('vi2');
              $parameter->vi1 = $request->get('vi1');
              $parameter->ls4 = $request->get('ls4');
              $parameter->ls4d = $request->get('ls4d');
              $parameter->ls4c = $request->get('ls4c');
              $parameter->ls4b = $request->get('ls4b');
              $parameter->ls4a = $request->get('ls4a');
              $parameter->ls5 = $request->get('ls5');
              $parameter->ve1 = $request->get('ve1');
              $parameter->ve2 = $request->get('ve2');
              $parameter->vr = $request->get('vr');
              $parameter->ls31a = $request->get('ls31a');
              $parameter->ls31 = $request->get('ls31');
              $parameter->srn = $request->get('srn');
              $parameter->rpm = $request->get('rpm');
              $parameter->bp = $request->get('bp');
              $parameter->tr1inj = $request->get('tr1inj');
              $parameter->tr3cool = $request->get('tr3cool');
              $parameter->tr4int = $request->get('tr4int');
              $parameter->mincush = $request->get('mincush');
              $parameter->fill = $request->get('fill');
              $parameter->circletime = $request->get('circletime');
              $parameter->save();

              $response = array(
                'status' => true,
                'message' => 'Success Create Parameter',
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

    function delete_parameter(Request $request,$id)
    {
          try{    
              $id_user = Auth::id();

              $parameter = PushBlockParameter::find($id);
              $parameter->delete();

              return redirect('/index/machine_parameter')
              ->with('status', 'Parameter has been deleted.')
              ->with('page', 'Machine Parameter');
            }catch(\Exception $e){
              $response = array(
                'status' => false,
                'message' => $e->getMessage(),
              );
              return Response::json($response);
            }
    }

    public function filterMachineParameter(Request $request,$remark)
    {
      $mesin = $request->get('mesin_filter');
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $mesin = '';
      if($request->get('mesin_filter') != null){
        $mesins =  explode(",", $request->get('mesin_filter'));
        for ($i=0; $i < count($mesins); $i++) {
          $mesin = $mesin."'".$mesins[$i]."'";
          if($i != (count($mesins)-1)){
            $mesin = $mesin.',';
          }
        }
        $mesinin = " and `mesin` in (".$mesin.") ";
      }
      else{
        $mesinin = "";
      }

      $parameters = DB::SELECT("SELECT
        * 
      FROM
        push_block_parameters 
        where push_block_code = 'First Shot Approval'
        ".$date." ".$mesinin." ORDER BY
          push_block_parameters.id DESC");

      $molding = InjectionMoldingMaster::select('part')->get();

      return view('recorder.process.index_machine_parameter')
      ->with('page', 'Machine Parameter')
      ->with('head', 'Machine Parameter')
      ->with('title', 'Machine Parameter')
      ->with('title_jp', '機械条件')
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('molding', $molding)
      ->with('molding2', $molding)
      ->with('molding3', $molding)
      ->with('parameter', $parameters)
      ->with('remark', $remark)
      ->with('product_type', $this->product_type)
      ->with('product_type2', $this->product_type);
    }

    public function index_torque($remark){
      $name = Auth::user()->name;
      return view('recorder.process.index_torque')
      ->with('page', 'Process Assy Recorder')
      ->with('head', 'Recorder Torque Check')
      ->with('title', 'Recorder Torque Check')
      ->with('title_jp', 'リコーダーのトルク確認')
      ->with('name', $name)
      ->with('product_type', $this->product_type)
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('batas_bawah_hm', '15')
      ->with('batas_atas_hm', '73')
      ->with('batas_bawah_mf', '15')
      ->with('batas_atas_mf', '78')
      ->with('remark', $remark);
    }

    function fetchResumeTorque(Request $request)
    {
          try{
            $middle_id = $request->get("middle_id");

            $detail_middle = PushBlockMaster::find($middle_id);

            // var_dump($detail_middle->cavity_1);
            $cav_middle = array(
                      '1' => $detail_middle->cavity_1,
                      '2' => $detail_middle->cavity_2,
                      '3' => $detail_middle->cavity_3,
                      '4' => $detail_middle->cavity_4 );

            $head_foot_id = $request->get("head_foot_id");

            $detail_head_foot = PushBlockMaster::find($head_foot_id);

            if ($detail_head_foot->cavity_5 == null) {
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4 );
            }else{
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4,
                      '5' => $detail_head_foot->cavity_5,
                      '6' => $detail_head_foot->cavity_6, );
            }

            $response = array(
              'status' => true,
              'detail_middle' => $detail_middle,
              'detail_head_foot' => $detail_head_foot,
              'cav_middle' => $cav_middle,
              'cav_head_foot' => $cav_head_foot,
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    function create_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $torque_1 = $request->get('torque_1');
              $torque_2 = $request->get('torque_2');
              $torque_3 = $request->get('torque_3');
              $average = $request->get('average');
              $middle = $request->get('middle');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');
              $judgement = $request->get('judgement');
              $check_type = $request->get('check_type');

              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');

              $ng_head = [];
              $ng_foot = [];

              $avg_head = [];
              $avg_foot = [];

              $status_input = 0;

              for($i = 0; $i<count($middle);$i++){
                if ($judgement[$i] == 'NG') {
                  if ($check_type == 'HJ-MJ') {
                    $ng_head[] = $middle[$i].'-'.$head_foot[$i];
                    $avg_head[] = $average[$i];
                  }else{
                    $ng_foot[] = $middle[$i].'-'.$head_foot[$i];
                    $avg_foot[] = $average[$i];
                  }
                }
                $create_log_torque = PushBlockTorque::create([
                  'push_block_code' => $request->get('push_block_code'),
                  'push_block_id_gen' => $push_block_id_gen,
                    'check_date' => $request->get('check_date'),
                    'check_type' => $request->get('check_type'),
                    'injection_date_middle' => $request->get('injection_date_middle'),
                    'mesin_middle' => $request->get('mesin_middle'),
                    'injection_date_head_foot' => $request->get('injection_date_head_foot'),
                    'mesin_head_foot' => $request->get('mesin_head_foot'),
                    'product_type' => $request->get('product_type'),
                    'middle' => $middle[$i],
                    'head_foot' => $head_foot[$i],
                    'torque1' => $torque_1[$i],
                    'torque2' => $torque_2[$i],
                    'torque3' => $torque_3[$i],
                    'torqueavg' => $average[$i],
                    'judgement' => $judgement[$i],
                    'pic_check' => $request->get('pic_check'),
                    'notes' => $request->get('notes'),
                    'created_by' => $id_user
                ]);

                if ($create_log_torque) {
                  $status_input++;
                }
              }

              if ($status_input > 0) {
                for($j = 0; $j<count($middle);$j++){
                  $temptemp = PushBlockTorqueTemp::where('middle',$middle[$j])->where('head_foot',$head_foot[$j])->where('push_block_code',$push_block_code)->where('check_type',$request->get('check_type'))->delete();
                }
              }

              $resume_head = 'HJ-MJ_'.join(',',$ng_head).'_'.join(',',$avg_head);
              $resume_foot = 'MJ-FJ_'.join(',',$ng_foot).'_'.join(',',$avg_foot);

              // if ($push_block_code == 'After Injection') {
              //   if ($request->get('product_type') == "YRF-21K//ID" || $request->get('product_type') == "YRF-21//ID" || $request->get('product_type') == "YRF-21 (FSA)" || $request->get('product_type') == "YRF21") {
              //     $tag_head = InjectionTag::where('tag',$request->get('tag_head'))->first();
              //     $tag_middle = InjectionTag::where('tag',$request->get('tag_middle'))->first();

              //     if ($check_type == 'HJ-MJ') {
              //       if ($resume_head != 'HJ-MJ__') {
              //         $tag_head->torque_check = $resume_head;
              //         $tag_middle->torque_check = $resume_head;
              //       }else{
              //         $tag_head->torque_check = 'OK';
              //         $tag_middle->torque_check = 'HJ-MJ_OK';
              //       }
              //     }

              //     $tag_head->save();
              //     $tag_middle->save();
              //   }else{
              //     $tag_head = InjectionTag::where('tag',$request->get('tag_head'))->first();
              //     $tag_middle = InjectionTag::where('tag',$request->get('tag_middle'))->first();
              //     $tag_foot = InjectionTag::where('tag',$request->get('tag_foot'))->first();

              //     if ($check_type == 'HJ-MJ') {
              //       if ($resume_head != 'HJ-MJ__') {
              //         $tag_head->torque_check = $resume_head;
              //         $tag_middle->torque_check = $resume_head;
              //       }else{
              //         $tag_head->torque_check = 'OK';
              //         $tag_middle->torque_check = 'HJ-MJ_OK';
              //       }
              //     }else{
              //       if ($resume_foot != 'MJ-FJ__') {
              //         $tag_foot->torque_check = $resume_foot;
              //         $tag_middle->torque_check = $tag_middle->torque_check.'&'.$resume_foot;
              //       }else{
              //         $tag_foot->torque_check = 'OK';
              //         $tag_middle->torque_check = $tag_middle->torque_check.'&MJ-FJ_OK';
              //       }
              //     }

              //     $tag_head->save();
              //     $tag_middle->save();
              //     $tag_foot->save();
              //   }
              // }

              $response = array(
                'status' => true,
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

    public function get_temp_torque(Request $request){
        $array_middle = $request->get('array_middle');
        $array_head_foot = $request->get('array_head_foot');
        $remark = $request->get('remark');
        $product_type = $request->get('product_type');
        $check_type = $request->get('check_type');

        $temp = [];

        $indexHeadFoot = (int)$request->get('indexHeadFoot');

        if ($product_type == 'YRF-21K//ID' || $product_type == 'YRF-21//ID' || $product_type == "YRF-21 (FSA)" || $product_type == "YRF21") {
          $index = 4;
        }else{
          $index = $indexHeadFoot * 4;
        }

        for($i = 0; $i < $index; $i++){
            $temptemp = PushBlockTorqueTemp::where('middle',$array_middle[$i])->where('head_foot',$array_head_foot[$i])->where('push_block_code',$remark)->where('product_type',$product_type)->where('check_type',$check_type)->first();
            
            if ($temptemp != null) {
              $temp[] = array('check_date' => $temptemp->check_date,
              'check_type' => $temptemp->check_type,
              'injection_date_middle' => $temptemp->injection_date_middle,
              'injection_date_head_foot' => $temptemp->injection_date_head_foot,
              'mesin_middle' => $temptemp->mesin_middle,
              'mesin_head_foot' => $temptemp->mesin_head_foot,
              'product_type' => $temptemp->product_type,
              'middle' => $temptemp->middle,
              'head_foot' => $temptemp->head_foot,
              'torque1' => $temptemp->torque1,
              'torque2' => $temptemp->torque2,
              'torque3' => $temptemp->torque3,
              'torqueavg' => $temptemp->torqueavg,
              'judgement' => $temptemp->judgement,
              'pic_check' => $temptemp->pic_check,
              'notes' => $temptemp->notes, );
            }
        }

        $response = array(
            'status' => true,            
            'datas' => $temp,
            'message' => 'Success get Temp'
        );
        return Response::json($response);
    }

    function create_temp_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $middle = $request->get('middle');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');

              if ($push_block_code == 'After Injection') {
                $front = 'AI';
              }else{
                $front = 'FSA';
              }

              $push_block_id_gen = $front."_".$request->get('check_date')."_".$request->get('product_type')."_".$request->get('pic_check');

              for($i = 0; $i<count($middle);$i++){
                PushBlockTorqueTemp::create([
                  'push_block_code' => $request->get('push_block_code'),
                  'push_block_id_gen' => $push_block_id_gen,
                    'check_date' => $request->get('check_date'),
                    'check_type' => $request->get('check_type'),
                    'injection_date_middle' => $request->get('injection_date_middle'),
                    'mesin_middle' => $request->get('mesin_middle'),
                    'injection_date_head_foot' => $request->get('injection_date_head_foot'),
                    'mesin_head_foot' => $request->get('mesin_head_foot'),
                    'product_type' => $request->get('product_type'),
                    'middle' => $middle[$i],
                    'head_foot' => $head_foot[$i],
                    'pic_check' => $request->get('pic_check'),
                    'created_by' => $id_user
                ]);
              }

              $response = array(
                'status' => true,
                'message' => 'Success Create Temp',
                'push_block_id_gen' => $push_block_id_gen
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

    function update_temp_torque(Request $request)
    {
          try{    
              $id_user = Auth::id();
              $torque_1 = $request->get('torque_1');
              $torque_2 = $request->get('torque_2');
              $torque_3 = $request->get('torque_3');
              $average = $request->get('average');
              $middle = $request->get('middle');
              $check_type = $request->get('check_type');
              $head_foot = $request->get('head_foot');
              $push_block_code = $request->get('push_block_code');
              $judgement = $request->get('judgement');
              $notes = $request->get('notes');
              for($i = 0; $i<count($middle);$i++){
                $temptemp = PushBlockTorqueTemp::where('middle',$middle[$i])->where('head_foot',$head_foot[$i])->where('push_block_code',$push_block_code)->where('check_type',$check_type)->get();
                foreach ($temptemp as $key) {
                  $update = PushBlockTorqueTemp::find($key->id);
                  $update->torque1 = $torque_1[$i];
                  $update->torque2 = $torque_2[$i];
                  $update->torque3 = $torque_3[$i];
                  $update->torqueavg = $average[$i];
                  $update->judgement = $judgement[$i];
                  $update->notes = $notes;
                  $update->save();
                }
              }

              $response = array(
                'status' => true,
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

    public function report_torque_check($remark)
    {

        $role = Auth::user()->role_code;
        $id_gen = DB::SELECT("SELECT DISTINCT
            ( push_block_id_gen ) 
          FROM
            `push_block_torques` 
          WHERE
            push_block_code = '".$remark."' 
          ORDER BY
            check_date DESC");
        $data = array(
                      // 'push_block_check' => $push_block_check,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'remark' => $remark,
                      'id_gen' => $id_gen,
                      'role' => $role,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'title_jp' => 'トルク測定報告',
                      'product_type' => $this->product_type);
      return view('recorder.report.report_torque_check', $data
        )->with('page', 'Report Torque Check')->with('remark', $remark);
    }

    public function filter_report_torque_check(Request $request,$remark)
    {
      $judgement = $request->get('judgement');
      $date_from = $request->get('date_from');
      $date_to = $request->get('date_to');
      $datenow = date('Y-m-d');

      $role = Auth::user()->role_code;

      $id_gen = DB::SELECT("SELECT DISTINCT
            ( push_block_id_gen ) 
          FROM
            `push_block_torques` 
          WHERE
            push_block_code = '".$remark."' 
          ORDER BY
            check_date DESC");

      if($request->get('date_to') == null){
        if($request->get('date_from') == null){
          $date = "";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$datenow."'";
        }
      }
      elseif($request->get('date_to') != null){
        if($request->get('date_from') == null){
          $date = "and date(check_date) <= '".$date_to."'";
        }
        elseif($request->get('date_from') != null){
          $date = "and date(check_date) BETWEEN '".$date_from."' and '".$date_to."'";
        }
      }

      $judgement = '';
      if($request->get('judgement') != null){
        $judgements =  explode(",", $request->get('judgement'));
        for ($i=0; $i < count($judgements); $i++) {
          $judgement = $judgement."'".$judgements[$i]."'";
          if($i != (count($judgements)-1)){
            $judgement = $judgement.',';
          }
        }
        $judgementin = " and `judgement` in (".$judgement.") ";
      }
      else{
        $judgementin = "";
      }

      if($request->get('check_type') != null){
        $check_type = " and `check_type` =  '".$request->get('check_type')."'";
      }
      else{
        $check_type = "";
      }

      if($request->get('mesin_middle') != null){
        $mesin_middle = " and `mesin_middle` =  '".$request->get('mesin_middle')."'";
      }
      else{
        $mesin_middle = "";
      }

      if($request->get('mesin_head_foot') != null){
        $mesin_head_foot = " and `mesin_head_foot` =  '".$request->get('mesin_head_foot')."'";
      }
      else{
        $mesin_head_foot = "";
      }

      $torque_check = DB::SELECT("SELECT * FROM `push_block_torques` where push_block_code = '".$remark."' ".$date." ".$judgementin." ".$check_type." ".$mesin_middle." ".$mesin_head_foot." ORDER BY push_block_torques.id desc");

      $data = array('torque_check' => $torque_check,
                      'mesin' => $this->mesin,
                      'mesin2' => $this->mesin,
                      'role' => $role,
                      'id_gen' => $id_gen,
                      'remark' => $remark,
                      'mesin3' => $this->mesin,
                      'mesin4' => $this->mesin,
                      'title_jp' => 'トルク測定報告',
                      'product_type' => $this->product_type);
      return view('recorder.report.report_torque_check', $data
        )->with('page', 'Report Torque Check')->with('remark', $remark);
    }

    public function index_torque_ai($remark){
      $name = Auth::user()->name;
      $view = 'recorder.process.index_torque'; //existing
      // $view = 'recorder.process.index_torque_ai2'; //tag rfid
      return view($view)
      ->with('page', 'Process Assy Recorder')
      ->with('head', 'Recorder Torque Check')
      ->with('title', 'Recorder Torque Check')
      ->with('title_jp', 'リコーダーのトルク確認')
      ->with('name', $name)
      ->with('product_type', $this->product_type)
      ->with('mesin', $this->mesin)
      ->with('mesin2', $this->mesin)
      ->with('mesin3', $this->mesin)
      ->with('batas_bawah_hm', '15')
      ->with('batas_atas_hm', '73')
      ->with('batas_bawah_mf', '15')
      ->with('batas_atas_mf', '78')
      ->with('remark', $remark);
    }

    function fetchResumeTorqueAi(Request $request)
    {
          try{
            $middle_id = $request->get("middle_id");

            $detail_middle = PushBlockMaster::find($middle_id);

            // var_dump($detail_middle->cavity_1);
            $cav_middle = array(
                      '1' => $detail_middle->cavity_1,
                      '2' => $detail_middle->cavity_2,
                      '3' => $detail_middle->cavity_3,
                      '4' => $detail_middle->cavity_4 );

            $head_foot_id = $request->get("head_foot_id");

            $detail_head_foot = PushBlockMaster::find($head_foot_id);

            if ($detail_head_foot->cavity_5 == null) {
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4 );
            }else{
              $cav_head_foot = array(
                      '1' => $detail_head_foot->cavity_1,
                      '2' => $detail_head_foot->cavity_2,
                      '3' => $detail_head_foot->cavity_3,
                      '4' => $detail_head_foot->cavity_4,
                      '5' => $detail_head_foot->cavity_5,
                      '6' => $detail_head_foot->cavity_6, );
            }

            $response = array(
              'status' => true,
              'detail_middle' => $detail_middle,
              'detail_head_foot' => $detail_head_foot,
              'cav_middle' => $cav_middle,
              'cav_head_foot' => $cav_head_foot,
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    function get_torque(Request $request)
    {
          try{
            $detail = PushBlockTorque::find($request->get("id"));
            $data = array('torque_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'check_type' => $detail->check_type,
                          'injection_date_middle' => $detail->injection_date_middle,
                          'mesin_middle' => $detail->mesin_middle,
                          'injection_date_head_foot' => $detail->injection_date_head_foot,
                          'mesin_head_foot' => $detail->mesin_head_foot,
                          'product_type' => $detail->product_type,
                          'middle' => $detail->middle,
                          'head_foot' => $detail->head_foot,
                          'torque1' => $detail->torque1,
                          'torque2' => $detail->torque2,
                          'torque3' => $detail->torque3,
                          'torqueavg' => $detail->torqueavg,
                          'judgement' => $detail->judgement,
                          'pic_check' => $detail->pic_check);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
          }
    }

    function update_torque(Request $request,$id)
    {
        try{
                $torque = PushBlockTorque::find($id);
                $torque->torque1 = $request->get('torque1');
                $torque->torque2 = $request->get('torque2');
                $torque->torque3 = $request->get('torque3');
                $torque->torqueavg = $request->get('average');
                $torque->judgement = $request->get('judgement');
                $torque->save();

               $response = array(
                'status' => true,
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

    function get_torque_all(Request $request)
    {
          try{
            $detail = PushBlockTorque::where('push_block_id_gen',$request->get("push_block_id_gen"))->first();
            $data = array('torque_id' => $detail->id,
                          'check_date' => $detail->check_date,
                          'check_type' => $detail->check_type,
                          'push_block_id_gen' => $detail->push_block_id_gen,
                          'injection_date_middle' => $detail->injection_date_middle,
                          'mesin_middle' => $detail->mesin_middle,
                          'injection_date_head_foot' => $detail->injection_date_head_foot,
                          'mesin_head_foot' => $detail->mesin_head_foot,
                          'product_type' => $detail->product_type,
                          'middle' => $detail->middle,
                          'head_foot' => $detail->head_foot,
                          'torque1' => $detail->torque1,
                          'torque2' => $detail->torque2,
                          'torque3' => $detail->torque3,
                          'torqueavg' => $detail->torqueavg,
                          'judgement' => $detail->judgement,
                          'pic_check' => $detail->pic_check);

            $response = array(
              'status' => true,
              'data' => $data
            );
            return Response::json($response);

          }
          catch (Exception $e){
             $response = array(
              'status' => false,
              'datas' => $e->getMessage(),
            );
            return Response::json($response);
          }
    }

    function update_torque_all(Request $request)
    {
        try{
                $torque = PushBlockTorque::where('push_block_id_gen',$request->get("push_block_id_gen"))->get();
                foreach($torque as $torque){
                  $torques = PushBlockTorque::find($torque->id);
                  if ($request->get('remark') == 'After Injection') {
                    $front = 'AI';
                  }else{
                    $front = 'FSA';
                  }
                  $push_block_id_gen = $front."_".$torques->check_date."_".$request->get('product_type')."_".$torques->pic_check;
                  $torques->injection_date_middle = $request->get('injection_date_middle');
                  $torques->injection_date_head_foot = $request->get('injection_date_head_foot');
                  $torques->mesin_middle = $request->get('mesin_middle');
                  $torques->mesin_head_foot = $request->get('mesin_head_foot');
                  $torques->product_type = $request->get('product_type');
                  $torques->push_block_id_gen = $push_block_id_gen;
                  $torques->save();
                }

               $response = array(
                'status' => true,
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

    public function import_push_block(Request $request)
    {
        // if($request->hasFile('upload_file')) {
          try{
            $push_block_id_gen = $request->get('push_block_id_gen2');
            $file = $request->file('file');
            $file_name = md5(date("dmYhisA")).'.'.$file->getClientOriginalExtension();
            $file->move('data_file/recorder/push_block_recorder', $file_name);

            $excel = 'data_file/recorder/push_block_recorder/' . $file_name;
            $rows = Excel::load($excel, function($reader) {
              $reader->noHeading();
              //Skip Header
              $reader->skipRows(6);
            })->get();
            $rows = $rows->toArray();

            $temp = PushBlockRecorderTemp::where('push_block_id_gen',$push_block_id_gen)->get();

            $batas_atas = 17;
            $batas_bawah = 3;

            for ($i=0; $i < count($rows); $i++) {
              $temptemp = PushBlockRecorderTemp::find($temp[$i]->id);
              $push =  $rows[$i][1]/10;
              $temptemp->push_pull =$push;
              if ($push < $batas_bawah || $push > $batas_atas) {
                $temptemp->judgement = 'NG';
              }else{
                $temptemp->judgement = 'OK';
              }
              $temptemp->save();
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

    public function scan_tag(Request $request)
    {
      try {
        $tag = $request->get('tag');
        $type = $request->get('type');
        $check = $request->get('check');

        if ($check == 'push_pull') {
          $data = DB::SELECT("SELECT
            tag,
            injection_tags.material_number,
            injection_tags.operator_id,
            injection_tags.part_name,
            injection_tags.color,
            injection_tags.cavity,
            injection_process_logs.mesin,
            DATE( injection_tags.created_at ) AS injection_date 
          FROM
            `injection_tags`
            left join injection_process_logs on injection_tags.tag = injection_process_logs.tag_product
          WHERE
            tag = '".$tag."' 
            AND push_pull_check = 'Uncheck' 
            AND height_check = 'Uncheck' 
            ORDER BY injection_process_logs.id desc
            LIMIT 1");
        }else{
          $data = DB::SELECT("SELECT
            tag,
            injection_tags.material_number,
            injection_tags.operator_id,
            injection_tags.part_name,
            injection_tags.color,
            injection_tags.cavity,
            injection_process_logs.mesin,
            DATE( injection_tags.created_at ) AS injection_date 
          FROM
            `injection_tags`
            left join injection_process_logs on injection_tags.tag = injection_process_logs.tag_product
          WHERE
            tag = '".$tag."' 
            AND torque_check = 'Uncheck' 
            ORDER BY injection_process_logs.id desc
            LIMIT 1");
        }

        if (count($data) > 0) {
          $response = array(
            'status' => true,
            'data' => $data,
            'message' => 'Scan Tag Success',
          );
          return Response::json($response);
        }else{
          $response = array(
            'status' => false,
            'message' => 'Data Not Found',
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

    public function fetch_cavity(Request $request)
    {
      try{
          $cavity = $request->get("cavity");
          $type = $request->get("type");

          $detail = PushBlockMaster::where('type',$type)->where('no_cavity',$cavity)->first();
          $data = array('type' => $detail->type,
                      'no_cavity' => $detail->no_cavity,
                      'cavity_1' => $detail->cavity_1,
                      'cavity_2' => $detail->cavity_2,
                      'cavity_3' => $detail->cavity_3,
                      'cavity_4' => $detail->cavity_4);

            if (count($data) > 0) {
              $response = array(
                'status' => true,
                'datas' => $data,
                'id' => $detail->id,
                'cavity_1' => $detail->cavity_1,
                'cavity_2' => $detail->cavity_2,
                'cavity_3' => $detail->cavity_3,
                'cavity_4' => $detail->cavity_4,
                'cavity_5' => $detail->cavity_5,
                'cavity_6' => $detail->cavity_6,
                'cavity_7' => $detail->cavity_7,
                'cavity_8' => $detail->cavity_8,
              );
              return Response::json($response);
            }else{
             $response = array(
                'status' => false,
                'datas' => "Data Not Found",
              );
               return Response::json($response); 
            }

          }
          catch (\Exception $e){
             $response = array(
              'status' => false,
              'datas' => "Get Data Error.",
            );
             return Response::json($response);
        }
    }

    public function indexReturn()
    {
      return view('recorder.process.return', array(
        'title' => 'Return Material Recorder',
        'title_jp' => 'リコーダーワークの返品'
      ))->with('page', 'Return Material Recorder');
    }


    public function fetchProductReturn(Request $request)
    {
      try {
          $product = DB::SELECT("SELECT
            * 
          FROM
            injection_parts 
          WHERE
            remark = 'injection' 
            AND deleted_at IS NULL ");

          if (count($product) > 0) {
              $response = array(
                  'status' => true,
                  'datas' => $product
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
              'message' => $e->getMessage(),
          );
          return Response::json($response);
      }
    }

    public function inputReturn(Request $request)
    {
      try {
        $cdm = RcReturnLog::create([
          'material_number' => $request->get('material_number'),
          'material_description' => $request->get('material_description'),
          'part_code' => $request->get('part_code'),
          'part_type' => $request->get('part_type'),
          'color' => $request->get('color'),
          'quantity' => $request->get('quantity'),
          'created_by' => Auth::id(),
        ]);

        $inventory = InjectionInventory::where('material_number',$request->get('material_number'))->where('location','RC91')->first();

        if ($inventory != null) {
          $inventory->quantity = $inventory->quantity - $request->get('quantity');
          $inventory->save(); 
        }

        // $mpdl = MaterialPlantDataList::where('material_number', '=', $request->get('material_number'))->first();

        // //YMES CANCEL CS
        // $plant_code = '8190';
        // $send_data_no = NULL;
        // $send_type_id = 'MIRAI';
        // $send_mgt_no = NULL;
        // $ext_result_type = '21';
        // $prod_individual_id = NULL;
        // $work_order_no = NULL;
        // $serial_no = NULL;
        // $item_code = $request->get('material_number');
        // $input_type = '2';
        // $qty = $request->get('quantity');
        // $defect_qty = NULL;
        // $reason_code = NULL;
        // $start_work_datetime = NULL;
        // $end_work_datetime = date('Y-m-d H:i:s');
        // $man_stat_cd = 'W'.$mpdl->mrpc.'S10';
        // $dest_location_code = $mpdl->storage_location;
        // $prod_stop_type = NULL;
        // $staff_id = NULL;
        // $machine_id = NULL;
        // $ot_resource_id = NULL;
        // $instid = '';
        // $instdt = date('Y-m-d H:i:s');
        // $instterm = '';
        // $instprgnm = '';
        // $updtid = '';
        // $updtdt = date('Y-m-d H:i:s');
        // $updtterm = '';
        // $updtprgnm = '';

        // $category = 'injection_cancel';
        // $action = 'production_result';
        // $function = 'inputReturn';
        // $remark = 'MIRAI';

        // $result_date = date('Y-m-d H:i:s');
        // $slip_number = '';
        // $created_by = Auth::user()->username;
        // $created_by_name = Auth::user()->name;

        // app('App\Http\Controllers\YMESController')->production_result(
        //   $plant_code,
        //   $send_data_no,
        //   $send_type_id,
        //   $send_mgt_no,
        //   $ext_result_type,
        //   $prod_individual_id,
        //   $work_order_no,
        //   $serial_no,
        //   $item_code,
        //   $input_type,
        //   $qty,
        //   $defect_qty,
        //   $reason_code,
        //   $start_work_datetime,
        //   $end_work_datetime,
        //   $man_stat_cd,
        //   $dest_location_code,
        //   $prod_stop_type,
        //   $staff_id,
        //   $machine_id,
        //   $ot_resource_id,
        //   $instid,
        //   $instdt,
        //   $instterm,
        //   $instprgnm,
        //   $updtid,
        //   $updtdt,
        //   $updtterm,
        //   $updtprgnm,
        //   $category,
        //   $action,
        //   $function,
        //   $remark,
        //   $result_date,
        //   $slip_number,
        //   $created_by,
        //   $created_by_name
        // );
        // //YMES END CANCEL CS

        // //YMES CANCEL TRANSFER
        // $plant_code = '8190';
        // $send_data_no = NULL;
        // $send_type_id = 'MIRAI';
        // $send_mgt_no = NULL;
        // $ext_move_type = '11';
        // $result_date = date('Y-m-d H:i:s');
        // $issue_loc_code = 'RC91';
        // $in_loc_code = 'RC11';
        // $issue_strg_area_id = NULL;
        // $in_strg_area_id = NULL;
        // $qty = $request->get('quantity');
        // $item_code = $request->get('material_number');
        // $serial_no = NULL;
        // $idtag_label_no = NULL;
        // $trace_label_no = NULL;
        // $prod_individual_id = NULL;
        // $wrapping_no = NULL;
        // $picking_no = NULL;
        // $staff_id = NULL;
        // $machine_id = NULL;
        // $ot_resource_id = NULL;
        // $instid = '';
        // $instdt = date('Y-m-d H:i:s');
        // $instterm = '';
        // $instprgnm = '';
        // $updtid = '';
        // $updtdt = date('Y-m-d H:i:s');
        // $updtterm = '';
        // $updtprgnm = '';

        // $category = 'injection_cancel';
        // $action = 'goods_movement';
        // $function = 'inputReturn';
        // $remark = 'MIRAI';

        // $slip_number = '';
        // $created_by = Auth::user()->username;
        // $created_by_name = Auth::user()->name;


        // app('App\Http\Controllers\YMESController')->goods_movement(
        //   $plant_code,
        //   $send_data_no,
        //   $send_type_id,
        //   $send_mgt_no,
        //   $ext_move_type,
        //   $result_date,
        //   $issue_loc_code,
        //   $in_loc_code,
        //   $issue_strg_area_id,
        //   $in_strg_area_id,
        //   $qty,
        //   $item_code,
        //   $serial_no,
        //   $idtag_label_no,
        //   $trace_label_no,
        //   $prod_individual_id,
        //   $wrapping_no,
        //   $picking_no,
        //   $staff_id,
        //   $machine_id,
        //   $ot_resource_id,
        //   $instid,
        //   $instdt,
        //   $instterm,
        //   $instprgnm,
        //   $updtid,
        //   $updtdt,
        //   $updtterm,
        //   $updtprgnm,
        //   $category,
        //   $action,
        //   $function,
        //   $remark,
        //   $slip_number,
        //   $created_by,
        //   $created_by_name
        // );
        $response = array(
            'status' => true,
            'message' => 'Return Success'
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

    public function fetchProductResume(Request $request)
    {
      try {
        $datefrom = $request->get('datefrom');
        $dateto = $request->get('dateto');

        if ($datefrom == '') {
          $datefrom = date('Y-m-01');
        }else{
          $datefrom = $request->get('datefrom');
        }

        if ($dateto == '') {
          $dateto = date('Y-m-d');
        }else{
          $dateto = $request->get('dateto');
        }

        $return = RcReturnLog::select('rc_return_logs.*','users.*','rc_return_logs.created_at as created','rc_return_logs.id as id_log')
          ->join('users','users.id','rc_return_logs.created_by')
          ->whereDate('rc_return_logs.created_at','>=',$datefrom)
          ->whereDate('rc_return_logs.created_at','<=',$dateto)
          ->orderBy('rc_return_logs.created_at','desc')->get();

        $datefromtitle = date('d M Y',strtotime($datefrom));
        $datetotitle = date('d M Y',strtotime($dateto));

        $response = array(
            'status' => true,
            'return' => $return,
            'datefromtitle' => $datefromtitle,
            'datetotitle' => $datetotitle,
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

    public function deleteProductResume(Request $request)
    {
      try {
        $rc_return_logs = RcReturnLog::where('id',$request->get('id'))->first();

        $inventory = InjectionInventory::where('material_number',$rc_return_logs->material_number)->where('location','RC91')->first();

        if ($inventory != null) {
          $inventory->quantity = $inventory->quantity + $rc_return_logs->quantity;
          $inventory->save(); 
        }

        $rc_return_logs->forceDelete();

        $response = array(
            'status' => true,
            'message' => 'Delete Return Success'
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

    public function indexCdm()
    {
      $head_a_bawah = 124;
      $head_a_atas = 124.7;
      $head_b_bawah = 22.5;
      $head_b_atas = 22.8;

      $middle_a_bawah = 173.1;
      $middle_a_atas = 174.1;
      $middle_b_bawah = 11.8;
      $middle_b_atas = 11.9;

      $foot_a_bawah = 13.3;
      $foot_a_atas = 14.7;
      $foot_b_bawah = 62.8;
      $foot_b_atas = 63.1;

      $head_yrf_a_bawah = 139.8;
      $head_yrf_a_atas = 140.2;
      $head_yrf_b_bawah = 16.5;
      $head_yrf_b_atas = 17.5;

      $body_yrf_a_bawah = 216.3;
      $body_yrf_a_atas = 216.7;
      $body_yrf_b_bawah = 10.5;
      $body_yrf_b_atas = 11.5;

      return view('recorder.process.index_cdm')
      ->with('machine', $this->mesin)
      ->with('head_a_bawah', $head_a_bawah)
      ->with('head_a_atas', $head_a_atas)
      ->with('head_b_bawah', $head_b_bawah)
      ->with('head_b_atas', $head_b_atas)
      ->with('middle_a_bawah', $middle_a_bawah)
      ->with('middle_a_atas', $middle_a_atas)
      ->with('middle_b_bawah', $middle_b_bawah)
      ->with('middle_b_atas', $middle_b_atas)
      ->with('foot_a_bawah', $foot_a_bawah)
      ->with('foot_a_atas', $foot_a_atas)
      ->with('foot_b_bawah', $foot_b_bawah)
      ->with('foot_b_atas', $foot_b_atas)
      ->with('head_yrf_a_bawah', $head_yrf_a_bawah)
      ->with('head_yrf_a_atas', $head_yrf_a_atas)
      ->with('head_yrf_b_bawah', $head_yrf_b_bawah)
      ->with('head_yrf_b_atas', $head_yrf_b_atas)
      ->with('body_yrf_a_bawah', $body_yrf_a_bawah)
      ->with('body_yrf_a_atas', $body_yrf_a_atas)
      ->with('body_yrf_b_bawah', $body_yrf_b_bawah)
      ->with('body_yrf_b_atas', $body_yrf_b_atas)
      ->with('title', 'CDM (Check Dimension Material) Recorder')
      ->with('title_jp', 'リコーダーのCDM');
    }

    public function fetchProduct(Request $request)
    {
      try {
          $product = DB::SELECT("SELECT
            * 
          FROM
            injection_parts 
          WHERE
            remark = 'injection' 
            AND part_code != 'BJ' 
            AND part_code != 'A YRF S' 
            AND deleted_at IS NULL 
          ORDER BY
            index_cdm ASC");

          if (count($product) > 0) {
              $response = array(
                  'status' => true,
                  'datas' => $product
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
              'message' => $e->getMessage(),
          );
          return Response::json($response);
      }
    }

    public function fetchCavity(Request $request)
    {
      try {
          $cavity = DB::SELECT("select * from push_block_masters where type = '".$request->get('type')."'");

          if (count($cavity) > 0) {
              $response = array(
                  'status' => true,
                  'datas' => $cavity
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
              'message' => $e->getMessage(),
          );
          return Response::json($response);
      }
    }

    public function inputCdm(Request $request)
    {
      try {
          $id_user = Auth::id();
          $datas = $request->get('data');

          $str = $datas['product'];
          $yrs = "/YRS/i";

          $cdm_code = $datas['product'].'_'.$datas['type'].'_'.$datas['color'].'_'.$datas['injection_date'].'_'.date('Y-m-d H:i:s');

          if ($datas['save_type'] == 'INPUT') {
            if (preg_match($yrs, $str) == 1) {
              if ($datas['type'] == 'HEAD') {
                  for ($i=0; $i < 4; $i++) { 
                    $awal_a = $datas['head'][$i]['awal_a'];
                    $awal_b = $datas['head'][$i]['awal_b'];
                    $awal_c = $datas['head'][$i]['awal_c'];
                    $awal_status = $datas['head'][$i]['awal_status'];

                    $ist1_a = $datas['head'][$i]['ist1_a'];
                    $ist1_b = $datas['head'][$i]['ist1_b'];
                    $ist1_c = $datas['head'][$i]['ist1_c'];
                    $ist1_status = $datas['head'][$i]['ist1_status'];

                    $ist2_a = $datas['head'][$i]['ist2_a'];
                    $ist2_b = $datas['head'][$i]['ist2_b'];
                    $ist2_c = $datas['head'][$i]['ist2_c'];
                    $ist2_status = $datas['head'][$i]['ist2_status'];

                    $ist3_a = $datas['head'][$i]['ist3_a'];
                    $ist3_b = $datas['head'][$i]['ist3_b'];
                    $ist3_c = $datas['head'][$i]['ist3_c'];
                    $ist3_status = $datas['head'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::create([
                      'cdm_code' => $cdm_code,
                      'product' => $datas['product'],
                      'type' => $datas['type'],
                      'part' => $datas['part'],
                      'color' => $datas['color'],
                      'injection_date' => $datas['injection_date'],
                      'machine' => $datas['machine'],
                      'machine_injection' => $datas['machine_injection'],
                      'cavity' => $datas['cavity'],
                      'cav' => $datas['head'][$i]['cav'],
                      'awal_a' => $awal_a,
                      'awal_b' => $awal_b,
                      'awal_c' => $awal_c,
                      'awal_status' => $awal_status,
                      'awal_employee_id' => $datas['employee_id'],
                      'awal_created_at' => date('Y-m-d H:i:s'),
                      'ist_1_a' => $ist1_a,
                      'ist_1_b' => $ist1_b,
                      'ist_1_c' => $ist1_c,
                      'ist_1_status' => $ist1_status,
                      'ist_1_employee_id' => $datas['employee_id'],
                      'ist_1_created_at' => date('Y-m-d H:i:s'),
                      'ist_2_a' => $ist2_a,
                      'ist_2_b' => $ist2_b,
                      'ist_2_c' => $ist2_c,
                      'ist_2_status' => $ist2_status,
                      'ist_2_employee_id' => $datas['employee_id'],
                      'ist_2_created_at' => date('Y-m-d H:i:s'),
                      'ist_3_a' => $ist3_a,
                      'ist_3_b' => $ist3_b,
                      'ist_3_c' => $ist3_c,
                      'ist_3_status' => $ist3_status,
                      'ist_3_employee_id' => $datas['employee_id'],
                      'ist_3_created_at' => date('Y-m-d H:i:s'),
                      'created_by' => $id_user,
                    ]);
                  }
              }

              if ($datas['type'] == 'MIDDLE') {
                  for ($i=0; $i < 4; $i++) { 
                    $awal_a = $datas['middle'][$i]['awal_a'];
                    $awal_b = $datas['middle'][$i]['awal_b'];
                    $awal_c = $datas['middle'][$i]['awal_c'];
                    $awal_status = $datas['middle'][$i]['awal_status'];

                    $ist1_a = $datas['middle'][$i]['ist1_a'];
                    $ist1_b = $datas['middle'][$i]['ist1_b'];
                    $ist1_c = $datas['middle'][$i]['ist1_c'];
                    $ist1_status = $datas['middle'][$i]['ist1_status'];

                    $ist2_a = $datas['middle'][$i]['ist2_a'];
                    $ist2_b = $datas['middle'][$i]['ist2_b'];
                    $ist2_c = $datas['middle'][$i]['ist2_c'];
                    $ist2_status = $datas['middle'][$i]['ist2_status'];

                    $ist3_a = $datas['middle'][$i]['ist3_a'];
                    $ist3_b = $datas['middle'][$i]['ist3_b'];
                    $ist3_c = $datas['middle'][$i]['ist3_c'];
                    $ist3_status = $datas['middle'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::create([
                      'cdm_code' => $cdm_code,
                      'product' => $datas['product'],
                      'type' => $datas['type'],
                      'part' => $datas['part'],
                      'color' => $datas['color'],
                      'injection_date' => $datas['injection_date'],
                      'machine' => $datas['machine'],
                      'machine_injection' => $datas['machine_injection'],
                      'cavity' => $datas['cavity'],
                      'cav' => $datas['middle'][$i]['cav'],
                      'awal_a' => $awal_a,
                      'awal_b' => $awal_b,
                      'awal_c' => $awal_c,
                      'awal_status' => $awal_status,
                      'awal_employee_id' => $datas['employee_id'],
                      'awal_created_at' => date('Y-m-d H:i:s'),
                      'ist_1_a' => $ist1_a,
                      'ist_1_b' => $ist1_b,
                      'ist_1_c' => $ist1_c,
                      'ist_1_status' => $ist1_status,
                      'ist_1_employee_id' => $datas['employee_id'],
                      'ist_1_created_at' => date('Y-m-d H:i:s'),
                      'ist_2_a' => $ist2_a,
                      'ist_2_b' => $ist2_b,
                      'ist_2_c' => $ist2_c,
                      'ist_2_status' => $ist2_status,
                      'ist_2_employee_id' => $datas['employee_id'],
                      'ist_2_created_at' => date('Y-m-d H:i:s'),
                      'ist_3_a' => $ist3_a,
                      'ist_3_b' => $ist3_b,
                      'ist_3_c' => $ist3_c,
                      'ist_3_status' => $ist3_status,
                      'ist_3_employee_id' => $datas['employee_id'],
                      'ist_3_created_at' => date('Y-m-d H:i:s'),
                      'created_by' => $id_user,
                    ]);
                  }
              }

              if ($datas['type'] == 'FOOT') {
                  if (count($datas['foot']) == 6) {
                    for ($i=0; $i < 6; $i++) { 
                      $awal_a = $datas['foot'][$i]['awal_a'];
                      $awal_b = $datas['foot'][$i]['awal_b'];
                      $awal_c = $datas['foot'][$i]['awal_c'];
                      $awal_status = $datas['foot'][$i]['awal_status'];

                      $ist1_a = $datas['foot'][$i]['ist1_a'];
                      $ist1_b = $datas['foot'][$i]['ist1_b'];
                      $ist1_c = $datas['foot'][$i]['ist1_c'];
                      $ist1_status = $datas['foot'][$i]['ist1_status'];

                      $ist2_a = $datas['foot'][$i]['ist2_a'];
                      $ist2_b = $datas['foot'][$i]['ist2_b'];
                      $ist2_c = $datas['foot'][$i]['ist2_c'];
                      $ist2_status = $datas['foot'][$i]['ist2_status'];

                      $ist3_a = $datas['foot'][$i]['ist3_a'];
                      $ist3_b = $datas['foot'][$i]['ist3_b'];
                      $ist3_c = $datas['foot'][$i]['ist3_c'];
                      $ist3_status = $datas['foot'][$i]['ist3_status'];
                      
                      $cdm = InjectionCdmCheck::create([
                        'cdm_code' => $cdm_code,
                        'product' => $datas['product'],
                        'type' => $datas['type'],
                        'part' => $datas['part'],
                        'color' => $datas['color'],
                        'injection_date' => $datas['injection_date'],
                        'machine' => $datas['machine'],
                        'machine_injection' => $datas['machine_injection'],
                        'cavity' => $datas['cavity'],
                        'cav' => $datas['foot'][$i]['cav'],
                        'awal_a' => $awal_a,
                        'awal_b' => $awal_b,
                        'awal_c' => $awal_c,
                        'awal_status' => $awal_status,
                        'awal_employee_id' => $datas['employee_id'],
                        'awal_created_at' => date('Y-m-d H:i:s'),
                        'ist_1_a' => $ist1_a,
                        'ist_1_b' => $ist1_b,
                        'ist_1_c' => $ist1_c,
                        'ist_1_status' => $ist1_status,
                        'ist_1_employee_id' => $datas['employee_id'],
                        'ist_1_created_at' => date('Y-m-d H:i:s'),
                        'ist_2_a' => $ist2_a,
                        'ist_2_b' => $ist2_b,
                        'ist_2_c' => $ist2_c,
                        'ist_2_status' => $ist2_status,
                        'ist_2_employee_id' => $datas['employee_id'],
                        'ist_2_created_at' => date('Y-m-d H:i:s'),
                        'ist_3_a' => $ist3_a,
                        'ist_3_b' => $ist3_b,
                        'ist_3_c' => $ist3_c,
                        'ist_3_status' => $ist3_status,
                        'ist_3_employee_id' => $datas['employee_id'],
                        'ist_3_created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $id_user,
                      ]);
                    }
                  }else{
                    for ($i=0; $i < 4; $i++) { 
                      $awal_a = $datas['foot'][$i]['awal_a'];
                      $awal_b = $datas['foot'][$i]['awal_b'];
                      $awal_c = $datas['foot'][$i]['awal_c'];
                      $awal_status = $datas['foot'][$i]['awal_status'];

                      $ist1_a = $datas['foot'][$i]['ist1_a'];
                      $ist1_b = $datas['foot'][$i]['ist1_b'];
                      $ist1_c = $datas['foot'][$i]['ist1_c'];
                      $ist1_status = $datas['foot'][$i]['ist1_status'];

                      $ist2_a = $datas['foot'][$i]['ist2_a'];
                      $ist2_b = $datas['foot'][$i]['ist2_b'];
                      $ist2_c = $datas['foot'][$i]['ist2_c'];
                      $ist2_status = $datas['foot'][$i]['ist2_status'];

                      $ist3_a = $datas['foot'][$i]['ist3_a'];
                      $ist3_b = $datas['foot'][$i]['ist3_b'];
                      $ist3_c = $datas['foot'][$i]['ist3_c'];
                      $ist3_status = $datas['foot'][$i]['ist3_status'];
                      
                      $cdm = InjectionCdmCheck::create([
                        'cdm_code' => $cdm_code,
                        'product' => $datas['product'],
                        'type' => $datas['type'],
                        'part' => $datas['part'],
                        'color' => $datas['color'],
                        'injection_date' => $datas['injection_date'],
                        'machine' => $datas['machine'],
                        'machine_injection' => $datas['machine_injection'],
                        'cavity' => $datas['cavity'],
                        'cav' => $datas['foot'][$i]['cav'],
                        'awal_a' => $awal_a,
                        'awal_b' => $awal_b,
                        'awal_c' => $awal_c,
                        'awal_status' => $awal_status,
                        'awal_employee_id' => $datas['employee_id'],
                        'awal_created_at' => date('Y-m-d H:i:s'),
                        'ist_1_a' => $ist1_a,
                        'ist_1_b' => $ist1_b,
                        'ist_1_c' => $ist1_c,
                        'ist_1_status' => $ist1_status,
                        'ist_1_employee_id' => $datas['employee_id'],
                        'ist_1_created_at' => date('Y-m-d H:i:s'),
                        'ist_2_a' => $ist2_a,
                        'ist_2_b' => $ist2_b,
                        'ist_2_c' => $ist2_c,
                        'ist_2_status' => $ist2_status,
                        'ist_2_employee_id' => $datas['employee_id'],
                        'ist_2_created_at' => date('Y-m-d H:i:s'),
                        'ist_3_a' => $ist3_a,
                        'ist_3_b' => $ist3_b,
                        'ist_3_c' => $ist3_c,
                        'ist_3_status' => $ist3_status,
                        'ist_3_employee_id' => $datas['employee_id'],
                        'ist_3_created_at' => date('Y-m-d H:i:s'),
                        'created_by' => $id_user,
                      ]);
                    }
                  }
              }
            }else{
              if ($datas['type'] == 'HEAD') {
                  for ($i=0; $i < 2; $i++) { 
                    $awal_a = $datas['head_yrf'][$i]['awal_a'];
                    $awal_b = $datas['head_yrf'][$i]['awal_b'];
                    $awal_c = $datas['head_yrf'][$i]['awal_c'];
                    $awal_status = $datas['head_yrf'][$i]['awal_status'];

                    $ist1_a = $datas['head_yrf'][$i]['ist1_a'];
                    $ist1_b = $datas['head_yrf'][$i]['ist1_b'];
                    $ist1_c = $datas['head_yrf'][$i]['ist1_c'];
                    $ist1_status = $datas['head_yrf'][$i]['ist1_status'];

                    $ist2_a = $datas['head_yrf'][$i]['ist2_a'];
                    $ist2_b = $datas['head_yrf'][$i]['ist2_b'];
                    $ist2_c = $datas['head_yrf'][$i]['ist2_c'];
                    $ist2_status = $datas['head_yrf'][$i]['ist2_status'];

                    $ist3_a = $datas['head_yrf'][$i]['ist3_a'];
                    $ist3_b = $datas['head_yrf'][$i]['ist3_b'];
                    $ist3_c = $datas['head_yrf'][$i]['ist3_c'];
                    $ist3_status = $datas['head_yrf'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::create([
                      'cdm_code' => $cdm_code,
                      'product' => $datas['product'],
                      'type' => $datas['type'],
                      'part' => $datas['part'],
                      'color' => $datas['color'],
                      'injection_date' => $datas['injection_date'],
                      'machine' => $datas['machine'],
                      'machine_injection' => $datas['machine_injection'],
                      'cavity' => $datas['cavity'],
                      'cav' => $datas['head_yrf'][$i]['cav'],
                      'awal_a' => $awal_a,
                      'awal_b' => $awal_b,
                      'awal_c' => $awal_c,
                      'awal_status' => $awal_status,
                      'awal_employee_id' => $datas['employee_id'],
                      'awal_created_at' => date('Y-m-d H:i:s'),
                      'ist_1_a' => $ist1_a,
                      'ist_1_b' => $ist1_b,
                      'ist_1_c' => $ist1_c,
                      'ist_1_status' => $ist1_status,
                      'ist_1_employee_id' => $datas['employee_id'],
                      'ist_1_created_at' => date('Y-m-d H:i:s'),
                      'ist_2_a' => $ist2_a,
                      'ist_2_b' => $ist2_b,
                      'ist_2_c' => $ist2_c,
                      'ist_2_status' => $ist2_status,
                      'ist_2_employee_id' => $datas['employee_id'],
                      'ist_2_created_at' => date('Y-m-d H:i:s'),
                      'ist_3_a' => $ist3_a,
                      'ist_3_b' => $ist3_b,
                      'ist_3_c' => $ist3_c,
                      'ist_3_status' => $ist3_status,
                      'ist_3_employee_id' => $datas['employee_id'],
                      'ist_3_created_at' => date('Y-m-d H:i:s'),
                      'created_by' => $id_user,
                    ]);
                  }
              }

              if ($datas['type'] == 'BODY') {
                  for ($i=0; $i < 2; $i++) { 
                    $awal_a = $datas['body_yrf'][$i]['awal_a'];
                    $awal_b = $datas['body_yrf'][$i]['awal_b'];
                    $awal_c = $datas['body_yrf'][$i]['awal_c'];
                    $awal_status = $datas['body_yrf'][$i]['awal_status'];

                    $ist1_a = $datas['body_yrf'][$i]['ist1_a'];
                    $ist1_b = $datas['body_yrf'][$i]['ist1_b'];
                    $ist1_c = $datas['body_yrf'][$i]['ist1_c'];
                    $ist1_status = $datas['body_yrf'][$i]['ist1_status'];

                    $ist2_a = $datas['body_yrf'][$i]['ist2_a'];
                    $ist2_b = $datas['body_yrf'][$i]['ist2_b'];
                    $ist2_c = $datas['body_yrf'][$i]['ist2_c'];
                    $ist2_status = $datas['body_yrf'][$i]['ist2_status'];

                    $ist3_a = $datas['body_yrf'][$i]['ist3_a'];
                    $ist3_b = $datas['body_yrf'][$i]['ist3_b'];
                    $ist3_c = $datas['body_yrf'][$i]['ist3_c'];
                    $ist3_status = $datas['body_yrf'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::create([
                      'cdm_code' => $cdm_code,
                      'product' => $datas['product'],
                      'type' => $datas['type'],
                      'part' => $datas['part'],
                      'color' => $datas['color'],
                      'injection_date' => $datas['injection_date'],
                      'machine' => $datas['machine'],
                      'machine_injection' => $datas['machine_injection'],
                      'cavity' => $datas['cavity'],
                      'cav' => $datas['body_yrf'][$i]['cav'],
                      'awal_a' => $awal_a,
                      'awal_b' => $awal_b,
                      'awal_c' => $awal_c,
                      'awal_status' => $awal_status,
                      'awal_employee_id' => $datas['employee_id'],
                      'awal_created_at' => date('Y-m-d H:i:s'),
                      'ist_1_a' => $ist1_a,
                      'ist_1_b' => $ist1_b,
                      'ist_1_c' => $ist1_c,
                      'ist_1_status' => $ist1_status,
                      'ist_1_employee_id' => $datas['employee_id'],
                      'ist_1_created_at' => date('Y-m-d H:i:s'),
                      'ist_2_a' => $ist2_a,
                      'ist_2_b' => $ist2_b,
                      'ist_2_c' => $ist2_c,
                      'ist_2_status' => $ist2_status,
                      'ist_2_employee_id' => $datas['employee_id'],
                      'ist_2_created_at' => date('Y-m-d H:i:s'),
                      'ist_3_a' => $ist3_a,
                      'ist_3_b' => $ist3_b,
                      'ist_3_c' => $ist3_c,
                      'ist_3_status' => $ist3_status,
                      'ist_3_employee_id' => $datas['employee_id'],
                      'ist_3_created_at' => date('Y-m-d H:i:s'),
                      'created_by' => $id_user,
                    ]);
                  }
              }
            }

            $message = 'Input Data Success';
          }


          if ($datas['save_type'] == "UPDATE") {
            if (preg_match($yrs, $str) == 1) {
              if ($datas['type'] == 'HEAD') {
                for ($i=0; $i < 4; $i++) { 
                  $awal_a = $datas['head'][$i]['awal_a'];
                  $awal_b = $datas['head'][$i]['awal_b'];
                  $awal_c = $datas['head'][$i]['awal_c'];
                  $awal_status = $datas['head'][$i]['awal_status'];

                  $ist1_a = $datas['head'][$i]['ist1_a'];
                  $ist1_b = $datas['head'][$i]['ist1_b'];
                  $ist1_c = $datas['head'][$i]['ist1_c'];
                  $ist1_status = $datas['head'][$i]['ist1_status'];

                  $ist2_a = $datas['head'][$i]['ist2_a'];
                  $ist2_b = $datas['head'][$i]['ist2_b'];
                  $ist2_c = $datas['head'][$i]['ist2_c'];
                  $ist2_status = $datas['head'][$i]['ist2_status'];

                  $ist3_a = $datas['head'][$i]['ist3_a'];
                  $ist3_b = $datas['head'][$i]['ist3_b'];
                  $ist3_c = $datas['head'][$i]['ist3_c'];
                  $ist3_status = $datas['head'][$i]['ist3_status'];
                  
                  $cdm = InjectionCdmCheck::find($datas['head'][$i]['id_cdm']);
                  $cdm->product = $datas['product'];
                  $cdm->type = $datas['type'];
                  $cdm->part = $datas['part'];
                  $cdm->color = $datas['color'];
                  $cdm->injection_date = $datas['injection_date'];
                  $cdm->machine = $datas['machine'];
                  $cdm->machine_injection = $datas['machine_injection'];
                  $cdm->cavity = $datas['cavity'];
                  $cdm->cav = $datas['head'][$i]['cav'];
                  if ($cdm->awal_a == null) {
                    $cdm->awal_employee_id = $datas['employee_id'];
                    $cdm->awal_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->awal_a = $awal_a;
                  $cdm->awal_b = $awal_b;
                  $cdm->awal_c = $awal_c;
                  $cdm->awal_status = $awal_status;
                  if ($cdm->ist_1_a == null) {
                    $cdm->ist_1_employee_id = $datas['employee_id'];
                    $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_1_a = $ist1_a;
                  $cdm->ist_1_b = $ist1_b;
                  $cdm->ist_1_c = $ist1_c;
                  $cdm->ist_1_status = $ist1_status;

                  if ($cdm->ist_2_a == null) {
                    $cdm->ist_2_employee_id = $datas['employee_id'];
                    $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_2_a = $ist2_a;
                  $cdm->ist_2_b = $ist2_b;
                  $cdm->ist_2_c = $ist2_c;
                  $cdm->ist_2_status = $ist2_status;
                  if ($cdm->ist_3_a == null) {
                    $cdm->ist_3_employee_id = $datas['employee_id'];
                    $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_3_a = $ist3_a;
                  $cdm->ist_3_b = $ist3_b;
                  $cdm->ist_3_c = $ist3_c;
                  $cdm->ist_3_status = $ist3_status;
                  $cdm->save();
                }
              }

              if ($datas['type'] == 'MIDDLE') {
                for ($i=0; $i < 4; $i++) { 
                  $awal_a = $datas['middle'][$i]['awal_a'];
                  $awal_b = $datas['middle'][$i]['awal_b'];
                  $awal_c = $datas['middle'][$i]['awal_c'];
                  $awal_status = $datas['middle'][$i]['awal_status'];

                  $ist1_a = $datas['middle'][$i]['ist1_a'];
                  $ist1_b = $datas['middle'][$i]['ist1_b'];
                  $ist1_c = $datas['middle'][$i]['ist1_c'];
                  $ist1_status = $datas['middle'][$i]['ist1_status'];

                  $ist2_a = $datas['middle'][$i]['ist2_a'];
                  $ist2_b = $datas['middle'][$i]['ist2_b'];
                  $ist2_c = $datas['middle'][$i]['ist2_c'];
                  $ist2_status = $datas['middle'][$i]['ist2_status'];

                  $ist3_a = $datas['middle'][$i]['ist3_a'];
                  $ist3_b = $datas['middle'][$i]['ist3_b'];
                  $ist3_c = $datas['middle'][$i]['ist3_c'];
                  $ist3_status = $datas['middle'][$i]['ist3_status'];
                  
                  $cdm = InjectionCdmCheck::find($datas['middle'][$i]['id_cdm']);
                  $cdm->product = $datas['product'];
                  $cdm->type = $datas['type'];
                  $cdm->part = $datas['part'];
                  $cdm->color = $datas['color'];
                  $cdm->injection_date = $datas['injection_date'];
                  $cdm->machine = $datas['machine'];
                  $cdm->machine_injection = $datas['machine_injection'];
                  $cdm->cavity = $datas['cavity'];
                  $cdm->cav = $datas['middle'][$i]['cav'];
                  if ($cdm->awal_a == null) {                    
                    $cdm->awal_employee_id = $datas['employee_id'];
                    $cdm->awal_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->awal_a = $awal_a;
                  $cdm->awal_b = $awal_b;
                  $cdm->awal_c = $awal_c;
                  $cdm->awal_status = $awal_status;
                  if ($cdm->ist_1_a == null) {
                    $cdm->ist_1_employee_id = $datas['employee_id'];
                    $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_1_a = $ist1_a;
                  $cdm->ist_1_b = $ist1_b;
                  $cdm->ist_1_c = $ist1_c;
                  $cdm->ist_1_status = $ist1_status;
                  if ($cdm->ist_2_a == null) {
                    $cdm->ist_2_employee_id = $datas['employee_id'];
                    $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_2_a = $ist2_a;
                  $cdm->ist_2_b = $ist2_b;
                  $cdm->ist_2_c = $ist2_c;
                  $cdm->ist_2_status = $ist2_status;
                  if ($cdm->ist_3_a == null) {
                    $cdm->ist_3_employee_id = $datas['employee_id'];
                    $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_3_a = $ist3_a;
                  $cdm->ist_3_b = $ist3_b;
                  $cdm->ist_3_c = $ist3_c;
                  $cdm->ist_3_status = $ist3_status;
                  $cdm->save();
                }
              }

              if ($datas['type'] == 'FOOT') {
                if (count($datas['foot']) == 6) {
                  for ($i=0; $i < 6; $i++) { 
                    $awal_a = $datas['foot'][$i]['awal_a'];
                    $awal_b = $datas['foot'][$i]['awal_b'];
                    $awal_c = $datas['foot'][$i]['awal_c'];
                    $awal_status = $datas['foot'][$i]['awal_status'];

                    $ist1_a = $datas['foot'][$i]['ist1_a'];
                    $ist1_b = $datas['foot'][$i]['ist1_b'];
                    $ist1_c = $datas['foot'][$i]['ist1_c'];
                    $ist1_status = $datas['foot'][$i]['ist1_status'];

                    $ist2_a = $datas['foot'][$i]['ist2_a'];
                    $ist2_b = $datas['foot'][$i]['ist2_b'];
                    $ist2_c = $datas['foot'][$i]['ist2_c'];
                    $ist2_status = $datas['foot'][$i]['ist2_status'];

                    $ist3_a = $datas['foot'][$i]['ist3_a'];
                    $ist3_b = $datas['foot'][$i]['ist3_b'];
                    $ist3_c = $datas['foot'][$i]['ist3_c'];
                    $ist3_status = $datas['foot'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::find($datas['foot'][$i]['id_cdm']);
                    $cdm->product = $datas['product'];
                    $cdm->type = $datas['type'];
                    $cdm->part = $datas['part'];
                    $cdm->color = $datas['color'];
                    $cdm->injection_date = $datas['injection_date'];
                    $cdm->machine = $datas['machine'];
                    $cdm->machine_injection = $datas['machine_injection'];
                    $cdm->cavity = $datas['cavity'];
                    $cdm->cav = $datas['foot'][$i]['cav'];
                    if ($cdm->awal_a == null) {                    
                      $cdm->awal_employee_id = $datas['employee_id'];
                      $cdm->awal_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->awal_a = $awal_a;
                    $cdm->awal_b = $awal_b;
                    $cdm->awal_c = $awal_c;
                    $cdm->awal_status = $awal_status;
                    if ($cdm->ist_1_a == null) {
                      $cdm->ist_1_employee_id = $datas['employee_id'];
                      $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_1_a = $ist1_a;
                    $cdm->ist_1_b = $ist1_b;
                    $cdm->ist_1_c = $ist1_c;
                    $cdm->ist_1_status = $ist1_status;
                    if ($cdm->ist_2_a == null) {
                      $cdm->ist_2_employee_id = $datas['employee_id'];
                      $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_2_a = $ist2_a;
                    $cdm->ist_2_b = $ist2_b;
                    $cdm->ist_2_c = $ist2_c;
                    $cdm->ist_2_status = $ist2_status;
                    if ($cdm->ist_3_a == null) {
                      $cdm->ist_3_employee_id = $datas['employee_id'];
                      $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_3_a = $ist3_a;
                    $cdm->ist_3_b = $ist3_b;
                    $cdm->ist_3_c = $ist3_c;
                    $cdm->ist_3_status = $ist3_status;
                    $cdm->save();
                  }
                }else{
                  for ($i=0; $i < 4; $i++) { 
                    $awal_a = $datas['foot'][$i]['awal_a'];
                    $awal_b = $datas['foot'][$i]['awal_b'];
                    $awal_c = $datas['foot'][$i]['awal_c'];
                    $awal_status = $datas['foot'][$i]['awal_status'];

                    $ist1_a = $datas['foot'][$i]['ist1_a'];
                    $ist1_b = $datas['foot'][$i]['ist1_b'];
                    $ist1_c = $datas['foot'][$i]['ist1_c'];
                    $ist1_status = $datas['foot'][$i]['ist1_status'];

                    $ist2_a = $datas['foot'][$i]['ist2_a'];
                    $ist2_b = $datas['foot'][$i]['ist2_b'];
                    $ist2_c = $datas['foot'][$i]['ist2_c'];
                    $ist2_status = $datas['foot'][$i]['ist2_status'];

                    $ist3_a = $datas['foot'][$i]['ist3_a'];
                    $ist3_b = $datas['foot'][$i]['ist3_b'];
                    $ist3_c = $datas['foot'][$i]['ist3_c'];
                    $ist3_status = $datas['foot'][$i]['ist3_status'];
                    
                    $cdm = InjectionCdmCheck::find($datas['foot'][$i]['id_cdm']);
                    $cdm->product = $datas['product'];
                    $cdm->type = $datas['type'];
                    $cdm->part = $datas['part'];
                    $cdm->color = $datas['color'];
                    $cdm->injection_date = $datas['injection_date'];
                    $cdm->machine = $datas['machine'];
                    $cdm->machine_injection = $datas['machine_injection'];
                    $cdm->cavity = $datas['cavity'];
                    $cdm->cav = $datas['foot'][$i]['cav'];
                    if ($cdm->awal_a == null) {                    
                      $cdm->awal_employee_id = $datas['employee_id'];
                      $cdm->awal_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->awal_a = $awal_a;
                    $cdm->awal_b = $awal_b;
                    $cdm->awal_c = $awal_c;
                    $cdm->awal_status = $awal_status;
                    if ($cdm->ist_1_a == null) {
                      $cdm->ist_1_employee_id = $datas['employee_id'];
                      $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_1_a = $ist1_a;
                    $cdm->ist_1_b = $ist1_b;
                    $cdm->ist_1_c = $ist1_c;
                    $cdm->ist_1_status = $ist1_status;
                    if ($cdm->ist_2_a == null) {
                      $cdm->ist_2_employee_id = $datas['employee_id'];
                      $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_2_a = $ist2_a;
                    $cdm->ist_2_b = $ist2_b;
                    $cdm->ist_2_c = $ist2_c;
                    $cdm->ist_2_status = $ist2_status;
                    if ($cdm->ist_3_a == null) {
                      $cdm->ist_3_employee_id = $datas['employee_id'];
                      $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                    }
                    $cdm->ist_3_a = $ist3_a;
                    $cdm->ist_3_b = $ist3_b;
                    $cdm->ist_3_c = $ist3_c;
                    $cdm->ist_3_status = $ist3_status;
                    $cdm->save();
                  }
                }
              }
            }else{
              if ($datas['type'] == 'HEAD') {
                for ($i=0; $i < 2; $i++) { 
                  $awal_a = $datas['head_yrf'][$i]['awal_a'];
                  $awal_b = $datas['head_yrf'][$i]['awal_b'];
                  $awal_c = $datas['head_yrf'][$i]['awal_c'];
                  $awal_status = $datas['head_yrf'][$i]['awal_status'];

                  $ist1_a = $datas['head_yrf'][$i]['ist1_a'];
                  $ist1_b = $datas['head_yrf'][$i]['ist1_b'];
                  $ist1_c = $datas['head_yrf'][$i]['ist1_c'];
                  $ist1_status = $datas['head_yrf'][$i]['ist1_status'];

                  $ist2_a = $datas['head_yrf'][$i]['ist2_a'];
                  $ist2_b = $datas['head_yrf'][$i]['ist2_b'];
                  $ist2_c = $datas['head_yrf'][$i]['ist2_c'];
                  $ist2_status = $datas['head_yrf'][$i]['ist2_status'];

                  $ist3_a = $datas['head_yrf'][$i]['ist3_a'];
                  $ist3_b = $datas['head_yrf'][$i]['ist3_b'];
                  $ist3_c = $datas['head_yrf'][$i]['ist3_c'];
                  $ist3_status = $datas['head_yrf'][$i]['ist3_status'];
                  
                  $cdm = InjectionCdmCheck::find($datas['head_yrf'][$i]['id_cdm']);
                  $cdm->product = $datas['product'];
                  $cdm->type = $datas['type'];
                  $cdm->part = $datas['part'];
                  $cdm->color = $datas['color'];
                  $cdm->injection_date = $datas['injection_date'];
                  $cdm->machine = $datas['machine'];
                  $cdm->machine_injection = $datas['machine_injection'];
                  $cdm->cavity = $datas['cavity'];
                  $cdm->cav = $datas['head_yrf'][$i]['cav'];
                  if ($cdm->awal_a == null) {
                    $cdm->awal_employee_id = $datas['employee_id'];
                    $cdm->awal_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->awal_a = $awal_a;
                  $cdm->awal_b = $awal_b;
                  $cdm->awal_c = $awal_c;
                  $cdm->awal_status = $awal_status;
                  if ($cdm->ist_1_a == null) {
                    $cdm->ist_1_employee_id = $datas['employee_id'];
                    $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_1_a = $ist1_a;
                  $cdm->ist_1_b = $ist1_b;
                  $cdm->ist_1_c = $ist1_c;
                  $cdm->ist_1_status = $ist1_status;
                  if ($cdm->ist_2_a == null) {
                    $cdm->ist_2_employee_id = $datas['employee_id'];
                    $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_2_a = $ist2_a;
                  $cdm->ist_2_b = $ist2_b;
                  $cdm->ist_2_c = $ist2_c;
                  $cdm->ist_2_status = $ist2_status;
                  if ($cdm->ist_3_a == null) {
                    $cdm->ist_3_employee_id = $datas['employee_id'];
                    $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_3_a = $ist3_a;
                  $cdm->ist_3_b = $ist3_b;
                  $cdm->ist_3_c = $ist3_c;
                  $cdm->ist_3_status = $ist3_status;
                  $cdm->save();
                }
              }

              if ($datas['type'] == 'BODY') {
                for ($i=0; $i < 2; $i++) { 
                  $awal_a = $datas['body_yrf'][$i]['awal_a'];
                  $awal_b = $datas['body_yrf'][$i]['awal_b'];
                  $awal_c = $datas['body_yrf'][$i]['awal_c'];
                  $awal_status = $datas['body_yrf'][$i]['awal_status'];

                  $ist1_a = $datas['body_yrf'][$i]['ist1_a'];
                  $ist1_b = $datas['body_yrf'][$i]['ist1_b'];
                  $ist1_c = $datas['body_yrf'][$i]['ist1_c'];
                  $ist1_status = $datas['body_yrf'][$i]['ist1_status'];

                  $ist2_a = $datas['body_yrf'][$i]['ist2_a'];
                  $ist2_b = $datas['body_yrf'][$i]['ist2_b'];
                  $ist2_c = $datas['body_yrf'][$i]['ist2_c'];
                  $ist2_status = $datas['body_yrf'][$i]['ist2_status'];

                  $ist3_a = $datas['body_yrf'][$i]['ist3_a'];
                  $ist3_b = $datas['body_yrf'][$i]['ist3_b'];
                  $ist3_c = $datas['body_yrf'][$i]['ist3_c'];
                  $ist3_status = $datas['body_yrf'][$i]['ist3_status'];
                  
                  $cdm = InjectionCdmCheck::find($datas['body_yrf'][$i]['id_cdm']);
                  $cdm->product = $datas['product'];
                  $cdm->type = $datas['type'];
                  $cdm->part = $datas['part'];
                  $cdm->color = $datas['color'];
                  $cdm->injection_date = $datas['injection_date'];
                  $cdm->machine = $datas['machine'];
                  $cdm->machine_injection = $datas['machine_injection'];
                  $cdm->cavity = $datas['cavity'];
                  $cdm->cav = $datas['body_yrf'][$i]['cav'];
                  if ($cdm->awal_a == null) {
                    $cdm->awal_employee_id = $datas['employee_id'];
                    $cdm->awal_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->awal_a = $awal_a;
                  $cdm->awal_b = $awal_b;
                  $cdm->awal_c = $awal_c;
                  $cdm->awal_status = $awal_status;
                  if ($cdm->ist_1_a == null) {
                    $cdm->ist_1_employee_id = $datas['employee_id'];
                    $cdm->ist_1_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_1_a = $ist1_a;
                  $cdm->ist_1_b = $ist1_b;
                  $cdm->ist_1_c = $ist1_c;
                  $cdm->ist_1_status = $ist1_status;
                  if ($cdm->ist_2_a == null) {
                    $cdm->ist_2_employee_id = $datas['employee_id'];
                    $cdm->ist_2_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_2_a = $ist2_a;
                  $cdm->ist_2_b = $ist2_b;
                  $cdm->ist_2_c = $ist2_c;
                  $cdm->ist_2_status = $ist2_status;
                  if ($cdm->ist_3_a == null) {
                    $cdm->ist_3_employee_id = $datas['employee_id'];
                    $cdm->ist_3_created_at = date('Y-m-d H:i:s');
                  }
                  $cdm->ist_3_a = $ist3_a;
                  $cdm->ist_3_b = $ist3_b;
                  $cdm->ist_3_c = $ist3_c;
                  $cdm->ist_3_status = $ist3_status;
                  $cdm->save();
                }
              }
            }
            $message = 'Update Data Success';
          }

          $response = array(
              'status' => true,
              'message' => $message
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

    public function fetchResumeCdm(Request $request)
    {
      try {
          $id_user = Auth::id();

          $data = DB::SELECT('SELECT DISTINCT
            ( cdm_code ),
            product,
            type,
            part,
            color,
            injection_date,
            machine,
            machine_injection,
            cavity,
          IF
            (
              awal_a IS NULL,
              "",
              CONCAT(
              CONCAT( awal_employee_id, "<br>", awal.name, "<br>At ", awal_created_at ))) AS awal_name,
          IF
            (
              ist_1_a IS NULL,
              "",
              CONCAT(
              CONCAT( ist_1_employee_id, "<br>", ist_1.name, "<br>At ", ist_1_created_at ))) AS ist_1_name,
          IF
            (
              ist_2_a IS NULL,
              "",
              CONCAT(
              CONCAT( ist_2_employee_id, "<br>", ist_2.name, "<br>At ", ist_2_created_at ))) AS ist_2_name,
          IF
            (
              ist_3_a IS NULL,
              "",
              CONCAT(
              CONCAT( ist_3_employee_id, "<br>", ist_3.name, "<br>At ", ist_3_created_at ))) AS ist_3_name 
          FROM
            `injection_cdm_checks`
            LEFT JOIN employee_syncs awal ON awal.employee_id = injection_cdm_checks.awal_employee_id
            LEFT JOIN employee_syncs ist_1 ON ist_1.employee_id = injection_cdm_checks.ist_1_employee_id
            LEFT JOIN employee_syncs ist_2 ON ist_2.employee_id = injection_cdm_checks.ist_2_employee_id
            LEFT JOIN employee_syncs ist_3 ON ist_3.employee_id = injection_cdm_checks.ist_3_employee_id 
          WHERE
            DATE( injection_cdm_checks.created_at ) BETWEEN DATE(
            NOW()) - INTERVAL 7 DAY 
            AND DATE(
            NOW()) 
          ORDER BY
            injection_cdm_checks.created_at DESC');

          $response = array(
              'status' => true,
              'datas' => $data
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

    public function fetchCdm(Request $request)
    {
      try {
          // $datass = InjectionCdmCheck::select('injection_cdm_checks.cdm_code')->where('id',$request->get('id'))->whereDate('created_at',date('Y-m-d'))->first();

          $data = InjectionCdmCheck::select('*','injection_cdm_checks.id as id_cdm')->where('cdm_code',$request->get('cdm_code'))->whereDate('created_at',date('Y-m-d'))->get();

          if (count($data) > 0) {
            $response = array(
                'status' => true,
                'datas' => $data
            );
            return Response::json($response);
          }else{
            $response = array(
                'status' => false,
                'message' => 'Data tidak tersedia'
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

    public function indexCdmReport()
    {
      return view('recorder.report.report_cdm')
      ->with('machine', $this->mesin)
      ->with('title', 'CDM (Check Dimension Material) Recorder Report')
      ->with('title_jp', 'リコーダーのCDM報告');
    }

    public function fetchCdmReport(Request $request)
    {
      try {

          $machine = $request->get('machine');
          $date_from = $request->get('date_from');
          $date_to = $request->get('date_to');
          $datenow = date('Y-m-d');

          if($request->get('date_to') == null){
            if($request->get('date_from') == null){
              $date = "";
            }
            elseif($request->get('date_from') != null){
              $date = "and date(injection_cdm_checks.created_at) BETWEEN '".$date_from."' and '".$datenow."'";
            }
          }
          elseif($request->get('date_to') != null){
            if($request->get('date_from') == null){
              $date = "and date(injection_cdm_checks.created_at) <= '".$date_to."'";
            }
            elseif($request->get('date_from') != null){
              $date = "and date(injection_cdm_checks.created_at) BETWEEN '".$date_from."' and '".$date_to."'";
            }
          }

          $machine = '';
          if($request->get('machine') != null){
            $machines =  explode(",", $request->get('machine'));
            for ($i=0; $i < count($machines); $i++) {
              $machine = $machine."'".$machines[$i]."'";
              if($i != (count($machines)-1)){
                $machine = $machine.',';
              }
            }
            $machinein = " and injection_cdm_checks.machine_injection in (".$machine.") ";
          }
          else{
            $machinein = "";
          }

          if ($request->get('type') == null) {
            $type = "";
          }else{
            $type = "AND injection_cdm_checks.type = '".$request->get('type')."'";
          }

          $data = DB::SELECT("SELECT
            product,
              type,
              part,
              color,
              injection_date,
              machine,
              machine_injection,
              cavity,
              cav,
              COALESCE(awal_a,'') as awal_a,
              COALESCE(awal_b,'') as awal_b,
              COALESCE(awal_c,'') as awal_c,
              COALESCE(awal_status,'') as awal_status,
              COALESCE(ist_1_a,'') as ist_1_a,
              COALESCE(ist_1_b,'') as ist_1_b,
              COALESCE(ist_1_c,'') as ist_1_c,
              COALESCE(ist_1_status,'') as ist_1_status,
              COALESCE(ist_2_a,'') as ist_2_a,
              COALESCE(ist_2_b,'') as ist_2_b,
              COALESCE(ist_2_c,'') as ist_2_c,
              COALESCE(ist_2_status,'') as ist_2_status,
              COALESCE(ist_3_a,'') as ist_3_a,
              COALESCE(ist_3_b,'') as ist_3_b,
              COALESCE(ist_3_c,'') as ist_3_c,
              COALESCE(ist_3_status,'') as ist_3_status,
              injection_cdm_checks.created_at AS created,
              awalemp.name as awal_name,
              ist1emp.name as ist_1_name,
              ist2emp.name as ist_2_name,
              ist3emp.name as ist_3_name,
              awal_employee_id,
              ist_1_employee_id,
              ist_2_employee_id,
              ist_3_employee_id,
              awal_created_at,
              ist_1_created_at,
              ist_2_created_at,
              ist_3_created_at

          FROM
              `injection_cdm_checks`
              LEFT JOIN employee_syncs as awalemp ON awalemp.employee_id = injection_cdm_checks.awal_employee_id
              LEFT JOIN employee_syncs as ist1emp ON ist1emp.employee_id = injection_cdm_checks.ist_1_employee_id
              LEFT JOIN employee_syncs as ist2emp ON ist2emp.employee_id = injection_cdm_checks.ist_2_employee_id
              LEFT JOIN employee_syncs as ist3emp ON ist3emp.employee_id = injection_cdm_checks.ist_3_employee_id
          WHERE
            deleted_at is null
            ".$date."
            ".$machinein."
            ".$type."
          ORDER BY
            injection_cdm_checks.created_at DESC");

          if (count($data) > 0) {
            $response = array(
                'status' => true,
                'datas' => $data,
                'message' => 'Success Get CDM Report'
            );
            return Response::json($response);
          }else{
            $response = array(
                'status' => false,
                'message' => 'Data tidak tersedia'
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

    public function indexKensa()
    {

      $ng_lists = NgList::where('location','kakuning')->where('remark','recorder')->get();

      return view('recorder.process.kensa')
      ->with('title', 'Kensa Kakuning Recorder')
      ->with('product_type',$this->product_type)
      ->with('ng_lists1',$ng_lists)
      ->with('ng_lists2',$ng_lists)
      ->with('ng_lists3',$ng_lists)
      ->with('ng_lists4',$ng_lists)
      ->with('title_jp', 'リコーダー検査確認')
      ->with('page', 'Kensa Kakuning Recorder');
    }

    public function scanKensaRecorderOperator(Request $request){

        $nik = $request->get('employee_id');
        $tray = $request->get('tray');
        $line = $request->get('line');

        if(strlen($nik) > 9){
            $nik = substr($nik,0,9);
        }

        $employee = db::table('employees')->where('tag', 'like', '%'.$nik.'%')->first();

        if($employee != null ){
          $kensas = RcKensaInitial::where('operator_kensa',$employee->employee_id)->where('status','Open')->where('line',$request->get('line'))->get();
          $tray = RcNgBox::where('tray',$tray)->where('date',date('Y-m-d'))->first();
          if (count($kensas) > 0) {
            $checkbox = RcBoxResult::where('operator_kensa',$employee->employee_id)->where('check_date',date('Y-m-d'))->where('product',$kensas[0]->product)->first();
            if ($checkbox == null) {
              RcBoxResult::create([
                'operator_kensa' => $employee->employee_id,
                'product' => $kensas[0]->product,
                'check_date' => date('Y-m-d'),
                'qty_box' => 0,
                'created_by' => 1,
              ]);

              $checkbox = RcBoxResult::where('operator_kensa',$employee->employee_id)->where('product',$kensas[0]->product)->where('check_date',date('Y-m-d'))->first();
              $response = array(
                  'status' => true,
                  'message' => 'Logged In',
                  'employee' => $employee,
                  'kensas' => $kensas,
                  'tray' => $tray,
                  'checkbox' => $checkbox,
              );
              return Response::json($response);
            }else{
              $response = array(
                  'status' => true,
                  'message' => 'Logged In',
                  'employee' => $employee,
                  'kensas' => $kensas,
                  'tray' => $tray,
                  'checkbox' => $checkbox,
              );
              return Response::json($response);
            }
          }else{
            $response = array(
                'status' => true,
                'message' => 'Logged In',
                'employee' => $employee,
                'kensas' => $kensas,
                'tray' => $tray,
                'checkbox' => null,
            );
            return Response::json($response);
          }
        }
        else{
            $response = array(
                'status' => false,
                'message' => 'Employee ID Invalid'
            );
            return Response::json($response);
        }
    }

    public function scanKensa(Request $request)
    {
      try {
        $part_type = $request->get('type');
        if ($part_type == 'head') {
          $part = 'HJ';
        }else if($part_type == 'middle'){
          $part = 'MJ';
        }else if($part_type == 'foot'){
          $part = 'FJ';
        }else if($part_type == 'block'){
          $part = 'BJ';
        }else if($part_type == 'head_yrf'){
          $part = 'A YRF H';
        }else if($part_type == 'body_yrf'){
          $part = 'A YRF B';
        }else if($part_type == 'stopper_yrf'){
          $part = 'A YRF S';
        }

        $tag = InjectionTag::where('tag',$request->get('tag'))->where('part_type','like','%'.$part.'%')->first();

        if ($tag != null) {
          $response = array(
              'status' => true,
              'message' => 'Scan Tag Success',
              'tag' => $tag,
          );
        }else{
          $response = array(
              'status' => false,
              'message' => 'Tag Tidak Ditemukan',
          );
        }
        return Response::json($response);
      } catch (\Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function inputKensaProduct(Request $request)
    {
      try {
        $emp_code = DB::SELECT('SELECT * FROM employee_groups where employee_id = "'.$request->get('employee_id').'" and location = "rc-assy"');

        foreach ($emp_code as $key) {
          $group = $key->group;
        }

        $code_generator = CodeGenerator::where('note', '=', 'kakuning-rc')->first();
        $serial_number = $group.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $code_generator->index = $code_generator->index+1;
        $code_generator->save();

        $rckensa = RcKensaInitial::where('status','Open')->where('operator_kensa',$request->get('employee_id'))->get();

        if (count($rckensa) > 0) {
          foreach ($rckensa as $key) {
            $kensaold = RcKensaInitial::find($key->id);
            $kensaold->status = 'Close';
            $kensaold->save();
          }
        }

        $operator_kensa = $request->get('employee_id');

        if (str_contains($request->get('product'), 'YRS')) {
          $product = $request->get('product');
          $tag_head = $request->get('tag_head');
          $tag_middle = $request->get('tag_middle');
          $tag_foot = $request->get('tag_foot');
          $tag_block = $request->get('tag_block');

          $material_number_head = $request->get('material_number_head');
          $part_name_head = $request->get('part_name_head');
          $part_type_head = $request->get('part_type_head');
          $color_head = $request->get('color_head');
          $cavity_head = $request->get('cavity_head');
          $location_head = $request->get('location_head');

          $material_number_middle = $request->get('material_number_middle');
          $part_name_middle = $request->get('part_name_middle');
          $part_type_middle = $request->get('part_type_middle');
          $color_middle = $request->get('color_middle');
          $cavity_middle = $request->get('cavity_middle');
          $location_middle = $request->get('location_middle');

          $material_number_foot = $request->get('material_number_foot');
          $part_name_foot = $request->get('part_name_foot');
          $part_type_foot = $request->get('part_type_foot');
          $color_foot = $request->get('color_foot');
          $cavity_foot = $request->get('cavity_foot');
          $location_foot = $request->get('location_foot');

          $material_number_block = $request->get('material_number_block');
          $part_name_block = $request->get('part_name_block');
          $part_type_block = $request->get('part_type_block');
          $color_block = $request->get('color_block');
          $cavity_block = $request->get('cavity_block');
          $location_block = $request->get('location_block');

          //INJECTION

          $injection_process_head = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_head as $key_head) {
            $no_kanban_head = $key_head->no_kanban;
            $start_time_head = $key_head->start_time;
            $end_time_head = $key_head->end_time;
            $mesin_head = $key_head->mesin;
            $qty_head = $key_head->qty;
            $employee_id_head = $key_head->employee_id;
            $ng_name_head = $key_head->ng_name;
            $ng_count_head = $key_head->ng_count;
          }

          $injection_process_middle = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_middle')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_middle as $key_middle) {
            $no_kanban_middle = $key_middle->no_kanban;
            $start_time_middle = $key_middle->start_time;
            $end_time_middle = $key_middle->end_time;
            $mesin_middle = $key_middle->mesin;
            $qty_middle = $key_middle->qty;
            $employee_id_middle = $key_middle->employee_id;
            $ng_name_middle = $key_middle->ng_name;
            $ng_count_middle = $key_middle->ng_count;
          }

          $injection_process_foot = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_foot')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_foot as $key_foot) {
            $no_kanban_foot = $key_foot->no_kanban;
            $start_time_foot = $key_foot->start_time;
            $end_time_foot = $key_foot->end_time;
            $mesin_foot = $key_foot->mesin;
            $qty_foot = $key_foot->qty;
            $employee_id_foot = $key_foot->employee_id;
            $ng_name_foot = $key_foot->ng_name;
            $ng_count_foot = $key_foot->ng_count;
          }

          $injection_process_block = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_block')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_block as $key_block) {
            $no_kanban_block = $key_block->no_kanban;
            $start_time_block = $key_block->start_time;
            $end_time_block = $key_block->end_time;
            $mesin_block = $key_block->mesin;
            $qty_block = $key_block->qty;
            $employee_id_block = $key_block->employee_id;
            $ng_name_block = $key_block->ng_name;
            $ng_count_block = $key_block->ng_count;
          }

          //MOLDING

          $molding_head = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          foreach ($molding_head as $key_head) {
            $molding_head = $key_head->part;
            $last_shot_before_head = $key_head->last_shot_pasang;
            $last_shot_injection_head = $key_head->last_shot_running;
            $start_molding_head = $key_head->start_time;
            $finish_molding_head = $key_head->end_time;
            $note_molding_head = $key_head->note;
            $pic_molding_head = $key_head->pic;
          }

          $molding_middle = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_middle')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          foreach ($molding_middle as $key_middle) {
            $molding_middle = $key_middle->part;
            $last_shot_before_middle = $key_middle->last_shot_pasang;
            $last_shot_injection_middle = $key_middle->last_shot_running;
            $start_molding_middle = $key_middle->start_time;
            $finish_molding_middle = $key_middle->end_time;
            $note_molding_middle = $key_middle->note;
            $pic_molding_middle = $key_middle->pic;
          }

          $molding_foot = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_foot')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          foreach ($molding_foot as $key_foot) {
            $molding_foot = $key_foot->part;
            $last_shot_before_foot = $key_foot->last_shot_pasang;
            $last_shot_injection_foot = $key_foot->last_shot_running;
            $start_molding_foot = $key_foot->start_time;
            $finish_molding_foot = $key_foot->end_time;
            $note_molding_foot = $key_foot->note;
            $pic_molding_foot = $key_foot->pic;
          }

          $molding_block = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_block')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          foreach ($molding_block as $key_block) {
            $molding_block = $key_block->part;
            $last_shot_before_block = $key_block->last_shot_pasang;
            $last_shot_injection_block = $key_block->last_shot_running;
            $start_molding_block = $key_block->start_time;
            $finish_molding_block = $key_block->end_time;
            $note_molding_block = $key_block->note;
            $pic_molding_block = $key_block->pic;
          }

          //DRYER

          $dryer_head = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          foreach ($dryer_head as $key_head) {
            $material_resin_head = $key_head->material_number;
            $dryer_resin_head = $key_head->dryer;
            $lot_number_resin_head = $key_head->lot_number;
            $qty_resin_head = $key_head->qty;
            $create_resin_head = $key_head->created_at;
            $operator_resin_head = $key_head->employee_id;
          }

          $dryer_middle = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_middle')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          foreach ($dryer_middle as $key_middle) {
            $material_resin_middle = $key_middle->material_number;
            $dryer_resin_middle = $key_middle->dryer;
            $lot_number_resin_middle = $key_middle->lot_number;
            $qty_resin_middle = $key_middle->qty;
            $create_resin_middle = $key_middle->created_at;
            $operator_resin_middle = $key_middle->employee_id;
          }

          $dryer_foot = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_foot')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          foreach ($dryer_foot as $key_foot) {
            $material_resin_foot = $key_foot->material_number;
            $dryer_resin_foot = $key_foot->dryer;
            $lot_number_resin_foot = $key_foot->lot_number;
            $qty_resin_foot = $key_foot->qty;
            $create_resin_foot = $key_foot->created_at;
            $operator_resin_foot = $key_head->employee_id;
          }

          $dryer_block = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_block')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          foreach ($dryer_block as $key_block) {
            $material_resin_block = $key_block->material_number;
            $dryer_resin_block = $key_block->dryer;
            $lot_number_resin_block = $key_block->lot_number;
            $qty_resin_block = $key_block->qty;
            $create_resin_block = $key_block->created_at;
            $operator_resin_block = $key_block->employee_id;
          }

          $transaction_head = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_head')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_head = [];
          $location_transaction_head = [];
          $operator_transaction_head = [];
          $status_transaction_head = [];

          foreach ($transaction_head as $key_head) {
            $create_transaction_head[] = $key_head->created_at;
            $location_transaction_head[] = $key_head->location;
            $status_transaction_head[] = $key_head->status;
            $operator_transaction_head[] = $key_head->employee_id;
          }

          $create_transaction_heads = join('_',$create_transaction_head);
          $location_transaction_heads = join('_',$location_transaction_head);
          $operator_transaction_heads = join('_',$operator_transaction_head);
          $status_transaction_heads = join('_',$status_transaction_head);

          $transaction_middle = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_middle')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_middle = [];
          $location_transaction_middle = [];
          $operator_transaction_middle = [];
          $status_transaction_middle = [];

          foreach ($transaction_middle as $key_middle) {
            $create_transaction_middle[] = $key_middle->created_at;
            $location_transaction_middle[] = $key_middle->location;
            $status_transaction_middle[] = $key_middle->status;
            $operator_transaction_middle[] = $key_middle->employee_id;
          }

          $create_transaction_middles = join('_',$create_transaction_middle);
          $location_transaction_middles = join('_',$location_transaction_middle);
          $operator_transaction_middles = join('_',$operator_transaction_middle);
          $status_transaction_middles = join('_',$status_transaction_middle);

          $transaction_foot = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_foot')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_foot = [];
          $location_transaction_foot = [];
          $operator_transaction_foot = [];
          $status_transaction_foot = [];

          foreach ($transaction_foot as $key_foot) {
            $create_transaction_foot[] = $key_foot->created_at;
            $location_transaction_foot[] = $key_foot->location;
            $status_transaction_foot[] = $key_foot->status;
            $operator_transaction_foot[] = $key_foot->employee_id;
          }

          $create_transaction_foots = join('_',$create_transaction_foot);
          $location_transaction_foots = join('_',$location_transaction_foot);
          $operator_transaction_foots = join('_',$operator_transaction_foot);
          $status_transaction_foots = join('_',$status_transaction_foot);

          $transaction_block = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_block')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_block = [];
          $location_transaction_block = [];
          $operator_transaction_block = [];
          $status_transaction_block = [];

          foreach ($transaction_block as $key_block) {
            $create_transaction_block[] = $key_block->created_at;
            $location_transaction_block[] = $key_block->location;
            $status_transaction_block[] = $key_block->status;
            $operator_transaction_block[] = $key_block->employee_id;
          }

          $create_transaction_blocks = join('_',$create_transaction_block);
          $location_transaction_blocks = join('_',$location_transaction_block);
          $operator_transaction_blocks = join('_',$operator_transaction_block);
          $status_transaction_blocks = join('_',$status_transaction_block);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_head,
            'part_name' => $part_name_head,
            'part_type' => $part_type_head,
            'color' => $color_head,
            'cavity' => $cavity_head,
            'location' => $location_head,
            'tag' => $tag_head,
            'no_kanban_injection' => $no_kanban_head,
            'start_injection' => $start_time_head,
            'finish_injection' => $end_time_head,
            'mesin_injection' => $mesin_head,
            'qty_injection' => $qty_head,
            'operator_injection' => $employee_id_head,
            'molding' => $molding_head,
            'last_shot_before' => $last_shot_before_head,
            'last_shot_injection' => $last_shot_injection_head,
            'start_molding' => $start_molding_head,
            'finish_molding' => $finish_molding_head,
            'note_molding' => $note_molding_head,
            'operator_molding' => $pic_molding_head,
            'material_resin' => $material_resin_head,
            'dryer_resin' => $dryer_resin_head,
            'lot_number_resin' => $lot_number_resin_head,
            'qty_resin' => $qty_resin_head,
            'create_resin' => $create_resin_head,
            'operator_resin' => $operator_resin_head,
            'ng_name' => $ng_name_head,
            'ng_count' => $ng_count_head,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_heads,
            'location_transaction' => $location_transaction_heads,
            'operator_transaction' => $operator_transaction_heads,
            'status_transaction' => $status_transaction_heads,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
             'product' => $product,
             'material_number' => $material_number_middle,
             'part_name' => $part_name_middle,
             'part_type' => $part_type_middle,
             'color' => $color_middle,
             'cavity' => $cavity_middle,
             'location' => $location_middle,
             'tag' => $tag_middle,
             'no_kanban_injection' => $no_kanban_middle,
              'start_injection' => $start_time_middle,
              'finish_injection' => $end_time_middle,
              'mesin_injection' => $mesin_middle,
              'qty_injection' => $qty_middle,
              'operator_injection' => $employee_id_middle,
              'molding' => $molding_middle,
              'last_shot_before' => $last_shot_before_middle,
              'last_shot_injection' => $last_shot_injection_middle,
              'start_molding' => $start_molding_middle,
              'finish_molding' => $finish_molding_middle,
              'note_molding' => $note_molding_middle,
              'operator_molding' => $pic_molding_middle,
              'material_resin' => $material_resin_middle,
            'dryer_resin' => $dryer_resin_middle,
            'lot_number_resin' => $lot_number_resin_middle,
            'qty_resin' => $qty_resin_middle,
            'create_resin' => $create_resin_middle,
            'operator_resin' => $operator_resin_middle,
            'ng_name' => $ng_name_middle,
            'ng_count' => $ng_count_middle,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_middles,
            'location_transaction' => $location_transaction_middles,
            'operator_transaction' => $operator_transaction_middles,
            'status_transaction' => $status_transaction_middles,
             'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_foot,
            'part_name' => $part_name_foot,
            'part_type' => $part_type_foot,
            'color' => $color_foot,
            'cavity' => $cavity_foot,
            'location' => $location_foot,
            'tag' => $tag_foot,
            'no_kanban_injection' => $no_kanban_foot,
            'start_injection' => $start_time_foot,
            'finish_injection' => $end_time_foot,
            'mesin_injection' => $mesin_foot,
            'qty_injection' => $qty_foot,
            'operator_injection' => $employee_id_foot,
            'molding' => $molding_foot,
            'last_shot_before' => $last_shot_before_foot,
            'last_shot_injection' => $last_shot_injection_foot,
            'start_molding' => $start_molding_foot,
            'finish_molding' => $finish_molding_foot,
            'note_molding' => $note_molding_foot,
            'operator_molding' => $pic_molding_foot,
            'material_resin' => $material_resin_foot,
            'dryer_resin' => $dryer_resin_foot,
            'lot_number_resin' => $lot_number_resin_foot,
            'qty_resin' => $qty_resin_foot,
            'create_resin' => $create_resin_foot,
            'operator_resin' => $operator_resin_foot,
            'ng_name' => $ng_name_foot,
            'ng_count' => $ng_count_foot,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_foots,
            'location_transaction' => $location_transaction_foots,
            'operator_transaction' => $operator_transaction_foots,
            'status_transaction' => $status_transaction_foots,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_block,
            'part_name' => $part_name_block,
            'part_type' => $part_type_block,
            'color' => $color_block,
            'cavity' => $cavity_block,
            'location' => $location_block,
            'tag' => $tag_block,
            'no_kanban_injection' => $no_kanban_block,
            'start_injection' => $start_time_block,
            'finish_injection' => $end_time_block,
            'mesin_injection' => $mesin_block,
            'qty_injection' => $qty_block,
            'operator_injection' => $employee_id_block,
            'molding' => $molding_block,
            'last_shot_before' => $last_shot_before_block,
            'last_shot_injection' => $last_shot_injection_block,
            'start_molding' => $start_molding_block,
            'finish_molding' => $finish_molding_block,
            'note_molding' => $note_molding_block,
            'operator_molding' => $pic_molding_block,
            'material_resin' => $material_resin_block,
            'dryer_resin' => $dryer_resin_block,
            'lot_number_resin' => $lot_number_resin_block,
            'qty_resin' => $qty_resin_block,
            'create_resin' => $create_resin_block,
            'operator_resin' => $operator_resin_block,
            'ng_name' => $ng_name_block,
            'ng_count' => $ng_count_block,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_blocks,
            'location_transaction' => $location_transaction_blocks,
            'operator_transaction' => $operator_transaction_blocks,
            'status_transaction' => $status_transaction_blocks,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);
        }else{
          $product = $request->get('product');
          $tag_head_yrf = $request->get('tag_head_yrf');
          $tag_body_yrf = $request->get('tag_body_yrf');
          $tag_stopper_yrf = $request->get('tag_stopper_yrf');

          $material_number_head_yrf = $request->get('material_number_head_yrf');
          $part_name_head_yrf = $request->get('part_name_head_yrf');
          $part_type_head_yrf = $request->get('part_type_head_yrf');
          $color_head_yrf = $request->get('color_head_yrf');
          $cavity_head_yrf = $request->get('cavity_head_yrf');
          $location_head_yrf = $request->get('location_head_yrf');

          $material_number_body_yrf = $request->get('material_number_body_yrf');
          $part_name_body_yrf = $request->get('part_name_body_yrf');
          $part_type_body_yrf = $request->get('part_type_body_yrf');
          $color_body_yrf = $request->get('color_body_yrf');
          $cavity_body_yrf = $request->get('cavity_body_yrf');
          $location_body_yrf = $request->get('location_body_yrf');

          $material_number_stopper_yrf = $request->get('material_number_stopper_yrf');
          $part_name_stopper_yrf = $request->get('part_name_stopper_yrf');
          $part_type_stopper_yrf = $request->get('part_type_stopper_yrf');
          $color_stopper_yrf = $request->get('color_stopper_yrf');
          $cavity_stopper_yrf = $request->get('cavity_stopper_yrf');
          $location_stopper_yrf = $request->get('location_stopper_yrf');

          $injection_process_head_yrf = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_head_yrf as $key_head_yrf) {
            $no_kanban_head_yrf = $key_head_yrf->no_kanban;
            $start_time_head_yrf = $key_head_yrf->start_time;
            $end_time_head_yrf = $key_head_yrf->end_time;
            $mesin_head_yrf = $key_head_yrf->mesin;
            $qty_head_yrf = $key_head_yrf->qty;
            $employee_id_head_yrf = $key_head_yrf->employee_id;
            $ng_name_head_yrf = $key_head_yrf->ng_name;
            $ng_count_head_yrf = $key_head_yrf->ng_count;
          }

          $injection_process_body_yrf = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_body_yrf as $key_body_yrf) {
            $no_kanban_body_yrf = $key_body_yrf->no_kanban;
            $start_time_body_yrf = $key_body_yrf->start_time;
            $end_time_body_yrf = $key_body_yrf->end_time;
            $mesin_body_yrf = $key_body_yrf->mesin;
            $qty_body_yrf = $key_body_yrf->qty;
            $employee_id_body_yrf = $key_body_yrf->employee_id;
            $ng_name_body_yrf = $key_body_yrf->ng_name;
            $ng_count_body_yrf = $key_body_yrf->ng_count;
          }

          $injection_process_stopper_yrf = DB::SELECT("SELECT
                injection_tags.no_kanban,
                injection_process_logs.start_time,
                injection_process_logs.end_time,
                injection_process_logs.mesin,
                injection_process_logs.shot as qty,
                opmesin.employee_id,
                injection_process_logs.ng_name,
                injection_process_logs.ng_count
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."'  
            ORDER BY
                injection_process_logs.created_at DESC
                LIMIT 1
                ");

          foreach ($injection_process_stopper_yrf as $key_stopper_yrf) {
            $no_kanban_stopper_yrf = $key_stopper_yrf->no_kanban;
            $start_time_stopper_yrf = $key_stopper_yrf->start_time;
            $end_time_stopper_yrf = $key_stopper_yrf->end_time;
            $mesin_stopper_yrf = $key_stopper_yrf->mesin;
            $qty_stopper_yrf = $key_stopper_yrf->qty;
            $employee_id_stopper_yrf = $key_stopper_yrf->employee_id;
            $ng_name_stopper_yrf = $key_stopper_yrf->ng_name;
            $ng_count_stopper_yrf = $key_stopper_yrf->ng_count;
          }

          $molding_head_yrf = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          if (count($molding_head_yrf) > 0) {
            foreach ($molding_head_yrf as $key_head_yrf) {
              $molding_head_yrf = $key_head_yrf->part;
              $last_shot_before_head_yrf = $key_head_yrf->last_shot_pasang;
              $last_shot_injection_head_yrf = $key_head_yrf->last_shot_running;
              $start_molding_head_yrf = $key_head_yrf->start_time;
              $finish_molding_head_yrf = $key_head_yrf->end_time;
              $note_molding_head_yrf = $key_head_yrf->note;
              $pic_molding_head_yrf = $key_head_yrf->pic;
            }
          }else{
            $molding_head_yrf = null;
            $last_shot_before_head_yrf = null;
            $last_shot_injection_head_yrf = null;
            $start_molding_head_yrf = null;
            $finish_molding_head_yrf = null;
            $note_molding_head_yrf = null;
            $pic_molding_head_yrf = null;
          }

          $molding_body_yrf = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          if (count($molding_body_yrf) > 0) {
            foreach ($molding_body_yrf as $key_body_yrf) {
              $molding_body_yrf = $key_body_yrf->part;
              $last_shot_before_body_yrf = $key_body_yrf->last_shot_pasang;
              $last_shot_injection_body_yrf = $key_body_yrf->last_shot_running;
              $start_molding_body_yrf = $key_body_yrf->start_time;
              $finish_molding_body_yrf = $key_body_yrf->end_time;
              $note_molding_body_yrf = $key_body_yrf->note;
              $pic_molding_body_yrf = $key_body_yrf->pic;
            }
          }else{
            $molding_body_yrf = null;
            $last_shot_before_body_yrf = null;
            $last_shot_injection_body_yrf = null;
            $start_molding_body_yrf = null;
            $finish_molding_body_yrf = null;
            $note_molding_body_yrf = null;
            $pic_molding_body_yrf = null;
          }

          $molding_stopper_yrf = DB::SELECT("SELECT
                injection_history_molding_logs.pic,
                injection_history_molding_logs.part,
                injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                injection_history_molding_logs.start_time,
                injection_history_molding_logs.end_time,
                injection_history_molding_logs.note
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."'  
                AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                AND injection_history_molding_logs.type = 'PASANG' 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_history_molding_logs.created_at DESC,
                injection_molding_logs.created_at DESC 
                LIMIT 1");

          if (count($molding_stopper_yrf) > 0) {
            foreach ($molding_stopper_yrf as $key_stopper_yrf) {
              $molding_stopper_yrf = $key_stopper_yrf->part;
              $last_shot_before_stopper_yrf = $key_stopper_yrf->last_shot_pasang;
              $last_shot_injection_stopper_yrf = $key_stopper_yrf->last_shot_running;
              $start_molding_stopper_yrf = $key_stopper_yrf->start_time;
              $finish_molding_stopper_yrf = $key_stopper_yrf->end_time;
              $note_molding_stopper_yrf = $key_stopper_yrf->note;
              $pic_molding_stopper_yrf = $key_stopper_yrf->pic;
            }
          }else{
            $molding_stopper_yrf = null;
            $last_shot_before_stopper_yrf = null;
            $last_shot_injection_stopper_yrf = null;
            $start_molding_stopper_yrf = null;
            $finish_molding_stopper_yrf = null;
            $note_molding_stopper_yrf = null;
            $pic_molding_stopper_yrf = null;
          }

          $dryer_head_yrf = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          if (count($dryer_head_yrf) > 0) {
            foreach ($dryer_head_yrf as $key_head_yrf) {
              $material_resin_head_yrf = $key_head_yrf->material_number;
              $dryer_resin_head_yrf = $key_head_yrf->dryer;
              $lot_number_resin_head_yrf = $key_head_yrf->lot_number;
              $qty_resin_head_yrf = $key_head_yrf->qty;
              $create_resin_head_yrf = $key_head_yrf->created_at;
              $operator_resin_head_yrf = $key_head_yrf->employee_id;
            }
          }else{
            $material_resin_head_yrf = null;
            $dryer_resin_head_yrf = null;
            $lot_number_resin_head_yrf = null;
            $qty_resin_head_yrf = null;
            $create_resin_head_yrf = null;
            $operator_resin_head_yrf = null;
          }

          $dryer_body_yrf = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          if (count($dryer_body_yrf) > 0) {
            foreach ($dryer_body_yrf as $key_body_yrf) {
              $material_resin_body_yrf = $key_body_yrf->material_number;
              $dryer_resin_body_yrf = $key_body_yrf->dryer;
              $lot_number_resin_body_yrf = $key_body_yrf->lot_number;
              $qty_resin_body_yrf = $key_body_yrf->qty;
              $create_resin_body_yrf = $key_body_yrf->created_at;
              $operator_resin_body_yrf = $key_body_yrf->employee_id;
            }
          }else{
            $material_resin_body_yrf = null;
            $dryer_resin_body_yrf = null;
            $lot_number_resin_body_yrf = null;
            $qty_resin_body_yrf = null;
            $create_resin_body_yrf = null;
            $operator_resin_body_yrf = null;
          }

          $dryer_stopper_yrf = DB::SELECT("SELECT
                injection_dryer_logs.material_number,
                injection_dryer_logs.dryer,
                injection_dryer_logs.qty,
                injection_dryer_logs.lot_number,
                injection_dryer_logs.created_at,
                opresin.employee_id
            FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                AND injection_dryer_logs.dryer = injection_process_logs.dryer
                LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
            WHERE
                injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."' 
                AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
            ORDER BY
                injection_process_logs.created_at DESC,
                injection_dryer_logs.created_at DESC
                LIMIT 1");

          if (count($dryer_stopper_yrf) > 0) {
            foreach ($dryer_stopper_yrf as $key_stopper_yrf) {
              $material_resin_stopper_yrf = $key_stopper_yrf->material_number;
              $dryer_resin_stopper_yrf = $key_stopper_yrf->dryer;
              $lot_number_resin_stopper_yrf = $key_stopper_yrf->lot_number;
              $qty_resin_stopper_yrf = $key_stopper_yrf->qty;
              $create_resin_stopper_yrf = $key_stopper_yrf->created_at;
              $operator_resin_stopper_yrf = $key_stopper_yrf->employee_id;
            }
          }else{
            $material_resin_stopper_yrf = null;
            $dryer_resin_stopper_yrf = null;
            $lot_number_resin_stopper_yrf = null;
            $qty_resin_stopper_yrf = null;
            $create_resin_stopper_yrf = null;
            $operator_resin_stopper_yrf = null;
          }

          $transaction_head_yrf = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_head_yrf = [];
          $location_transaction_head_yrf = [];
          $operator_transaction_head_yrf = [];
          $status_transaction_head_yrf = [];

          foreach ($transaction_head_yrf as $key_head_yrf) {
            $create_transaction_head_yrf[] = $key_head_yrf->created_at;
            $location_transaction_head_yrf[] = $key_head_yrf->location;
            $status_transaction_head_yrf[] = $key_head_yrf->status;
            $operator_transaction_head_yrf[] = $key_head_yrf->employee_id;
          }

          $create_transaction_head_yrfs = join('_',$create_transaction_head_yrf);
          $location_transaction_head_yrfs = join('_',$location_transaction_head_yrf);
          $operator_transaction_head_yrfs = join('_',$operator_transaction_head_yrf);
          $status_transaction_head_yrfs = join('_',$status_transaction_head_yrf);

          $transaction_body_yrf = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_body_yrf = [];
          $location_transaction_body_yrf = [];
          $operator_transaction_body_yrf = [];
          $status_transaction_body_yrf = [];

          foreach ($transaction_body_yrf as $key_body_yrf) {
            $create_transaction_body_yrf[] = $key_body_yrf->created_at;
            $location_transaction_body_yrf[] = $key_body_yrf->location;
            $status_transaction_body_yrf[] = $key_body_yrf->status;
            $operator_transaction_body_yrf[] = $key_body_yrf->employee_id;
          }

          $create_transaction_body_yrfs = join('_',$create_transaction_body_yrf);
          $location_transaction_body_yrfs = join('_',$location_transaction_body_yrf);
          $operator_transaction_body_yrfs = join('_',$operator_transaction_body_yrf);
          $status_transaction_body_yrfs = join('_',$status_transaction_body_yrf);

          $transaction_stopper_yrf = DB::SELECT("
            SELECT
              injection_transactions.location,
              injection_transactions.status,
              opinjeksi.employee_id,
              injection_transactions.created_at 
            FROM
              injection_tags
              LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
              LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
              LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
            WHERE
              injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."' 
              AND injection_transactions.created_at >= injection_process_logs.end_time 
            ORDER BY
              injection_process_logs.created_at DESC,
              injection_transactions.created_at ASC 
              LIMIT 3");

          $create_transaction_stopper_yrf = [];
          $location_transaction_stopper_yrf = [];
          $operator_transaction_stopper_yrf = [];
          $status_transaction_stopper_yrf = [];

          foreach ($transaction_stopper_yrf as $key_stopper_yrf) {
            $create_transaction_stopper_yrf[] = $key_stopper_yrf->created_at;
            $location_transaction_stopper_yrf[] = $key_stopper_yrf->location;
            $status_transaction_stopper_yrf[] = $key_stopper_yrf->status;
            $operator_transaction_stopper_yrf[] = $key_stopper_yrf->employee_id;
          }

          $create_transaction_stopper_yrfs = join('_',$create_transaction_stopper_yrf);
          $location_transaction_stopper_yrfs = join('_',$location_transaction_stopper_yrf);
          $operator_transaction_stopper_yrfs = join('_',$operator_transaction_stopper_yrf);
          $status_transaction_stopper_yrfs = join('_',$status_transaction_stopper_yrf);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_head_yrf,
            'part_name' => $part_name_head_yrf,
            'part_type' => $part_type_head_yrf,
            'color' => $color_head_yrf,
            'cavity' => $cavity_head_yrf,
            'location' => $location_head_yrf,
            'tag' => $tag_head_yrf,
            'no_kanban_injection' => $no_kanban_head_yrf,
            'start_injection' => $start_time_head_yrf,
            'finish_injection' => $end_time_head_yrf,
            'mesin_injection' => $mesin_head_yrf,
            'qty_injection' => $qty_head_yrf,
            'operator_injection' => $employee_id_head_yrf,
            'molding' => $molding_head_yrf,
            'last_shot_before' => $last_shot_before_head_yrf,
            'last_shot_injection' => $last_shot_injection_head_yrf,
            'start_molding' => $start_molding_head_yrf,
            'finish_molding' => $finish_molding_head_yrf,
            'note_molding' => $note_molding_head_yrf,
            'operator_molding' => $pic_molding_head_yrf,
            'material_resin' => $material_resin_head_yrf,
            'dryer_resin' => $dryer_resin_head_yrf,
            'lot_number_resin' => $lot_number_resin_head_yrf,
            'qty_resin' => $qty_resin_head_yrf,
            'create_resin' => $create_resin_head_yrf,
            'operator_resin' => $operator_resin_head_yrf,
            'ng_name' => $ng_name_head_yrf,
            'ng_count' => $ng_count_head_yrf,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_head_yrfs,
            'location_transaction' => $location_transaction_head_yrfs,
            'operator_transaction' => $operator_transaction_head_yrfs,
            'status_transaction' => $status_transaction_head_yrfs,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_body_yrf,
            'part_name' => $part_name_body_yrf,
            'part_type' => $part_type_body_yrf,
            'color' => $color_body_yrf,
            'cavity' => $cavity_body_yrf,
            'location' => $location_body_yrf,
            'tag' => $tag_body_yrf,
            'no_kanban_injection' => $no_kanban_body_yrf,
            'start_injection' => $start_time_body_yrf,
            'finish_injection' => $end_time_body_yrf,
            'mesin_injection' => $mesin_body_yrf,
            'qty_injection' => $qty_body_yrf,
            'operator_injection' => $employee_id_body_yrf,
            'molding' => $molding_body_yrf,
            'last_shot_before' => $last_shot_before_body_yrf,
            'last_shot_injection' => $last_shot_injection_body_yrf,
            'start_molding' => $start_molding_body_yrf,
            'finish_molding' => $finish_molding_body_yrf,
            'note_molding' => $note_molding_body_yrf,
            'operator_molding' => $pic_molding_body_yrf,
            'material_resin' => $material_resin_body_yrf,
            'dryer_resin' => $dryer_resin_body_yrf,
            'lot_number_resin' => $lot_number_resin_body_yrf,
            'qty_resin' => $qty_resin_body_yrf,
            'create_resin' => $create_resin_body_yrf,
            'operator_resin' => $operator_resin_body_yrf,
            'ng_name' => $ng_name_body_yrf,
            'ng_count' => $ng_count_body_yrf,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_body_yrfs,
            'location_transaction' => $location_transaction_body_yrfs,
            'operator_transaction' => $operator_transaction_body_yrfs,
            'status_transaction' => $status_transaction_body_yrfs,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          RcKensaInitial::create([
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'material_number' => $material_number_stopper_yrf,
            'part_name' => $part_name_stopper_yrf,
            'part_type' => $part_type_stopper_yrf,
            'color' => $color_stopper_yrf,
            'cavity' => $cavity_stopper_yrf,
            'location' => $location_stopper_yrf,
            'tag' => $tag_stopper_yrf,
            'no_kanban_injection' => $no_kanban_stopper_yrf,
            'start_injection' => $start_time_stopper_yrf,
            'finish_injection' => $end_time_stopper_yrf,
            'mesin_injection' => $mesin_stopper_yrf,
            'qty_injection' => $qty_stopper_yrf,
            'operator_injection' => $employee_id_stopper_yrf,
            'molding' => $molding_stopper_yrf,
            'last_shot_before' => $last_shot_before_stopper_yrf,
            'last_shot_injection' => $last_shot_injection_stopper_yrf,
            'start_molding' => $start_molding_stopper_yrf,
            'finish_molding' => $finish_molding_stopper_yrf,
            'note_molding' => $note_molding_stopper_yrf,
            'operator_molding' => $pic_molding_stopper_yrf,
            'material_resin' => $material_resin_stopper_yrf,
            'dryer_resin' => $dryer_resin_stopper_yrf,
            'lot_number_resin' => $lot_number_resin_stopper_yrf,
            'qty_resin' => $qty_resin_stopper_yrf,
            'create_resin' => $create_resin_stopper_yrf,
            'operator_resin' => $operator_resin_stopper_yrf,
            'ng_name' => $ng_name_stopper_yrf,
            'ng_count' => $ng_count_stopper_yrf,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $create_transaction_stopper_yrfs,
            'location_transaction' => $location_transaction_stopper_yrfs,
            'operator_transaction' => $operator_transaction_stopper_yrfs,
            'status_transaction' => $status_transaction_stopper_yrfs,
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);
        }
        $response = array(
              'status' => true,
              'kensa_code' => $serial_number,
              'product' => $request->get('product')
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

    public function inputKensa(Request $request)
    {
      try {
        if (str_contains($request->get('product'), 'YRS')) {
          $serial_number = $request->get('serial_number');
          $operator_kensa = $request->get('employee_id');
          $product = $request->get('product');
          $start_time = $request->get('start_time');
          $qty_check = $request->get('qty_check');

          $ng_name_head = $request->get('ng_name_head');
          $ng_name_middle = $request->get('ng_name_middle');
          $ng_name_foot = $request->get('ng_name_foot');
          $ng_name_block = $request->get('ng_name_block');

          $ng_count_head = $request->get('ng_count_head');
          $ng_count_middle = $request->get('ng_count_middle');
          $ng_count_foot = $request->get('ng_count_foot');
          $ng_count_block = $request->get('ng_count_block');

          $qty_ng_head = $request->get('qty_ng_head');
          $qty_ng_middle = $request->get('qty_ng_middle');
          $qty_ng_foot = $request->get('qty_ng_foot');
          $qty_ng_block = $request->get('qty_ng_block');

          $tray = RcNgBox::where('tray',$request->get('tray'))->where('date',date('Y-m-d'))->first();
          $tray->ng_head = $qty_ng_head;
          $tray->ng_middle = $qty_ng_middle;
          $tray->ng_foot = $qty_ng_foot;
          $tray->ng_block = $qty_ng_block;
          $tray->save();

          $initial_head = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','HJ')->where('line',$request->get('line'))->first();
          $tag_head = $initial_head->tag;
          $kensa_initial_code = $initial_head->kensa_initial_code;
          $material_number_head = $initial_head->material_number;
          $cavity_head = $initial_head->cavity;

          $box = RcBoxResult::where('operator_kensa',$initial_head->operator_kensa)->where('product',$request->get('product'))->where('check_date',date('Y-m-d'))->first();
          $box->qty_box = $qty_check;
          $box->save();

          $initial_head->ng_name_kensa = null;
          $initial_head->ng_count_kensa = null;
          $initial_head->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_head,
            'material_number' => $material_number_head,
            'cavity' => $cavity_head,
            'ng_name' => $ng_name_head,
            'ng_count' => $ng_count_head,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_head,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

          $initial_middle = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%MJ%')->where('line',$request->get('line'))->first();
          $tag_middle = $initial_middle->tag;
          $material_number_middle = $initial_middle->material_number;
          $cavity_middle = $initial_middle->cavity;
          $kensa_initial_code = $initial_middle->kensa_initial_code;

          $initial_middle->ng_name_kensa = null;
          $initial_middle->ng_count_kensa = null;
          $initial_middle->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_middle,
            'material_number' => $material_number_middle,
            'cavity' => $cavity_middle,
            'ng_name' => $ng_name_middle,
            'ng_count' => $ng_count_middle,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_middle,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

          $initial_foot = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%FJ%')->where('line',$request->get('line'))->first();
          $tag_foot = $initial_foot->tag;
          $kensa_initial_code = $initial_foot->kensa_initial_code;
          $material_number_foot = $initial_foot->material_number;
          $cavity_foot = $initial_foot->cavity;

          $initial_foot->ng_name_kensa = null;
          $initial_foot->ng_count_kensa = null;
          $initial_foot->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_foot,
            'material_number' => $material_number_foot,
            'cavity' => $cavity_foot,
            'ng_name' => $ng_name_foot,
            'ng_count' => $ng_count_foot,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_foot,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

          $initial_block = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%BJ%')->where('line',$request->get('line'))->first();
          $tag_block = $initial_block->tag;
          $kensa_initial_code = $initial_block->kensa_initial_code;
          $material_number_block = $initial_block->material_number;
          $cavity_block = $initial_block->cavity;

          $initial_block->ng_name_kensa = null;
          $initial_block->ng_count_kensa = null;
          $initial_block->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_block,
            'material_number' => $material_number_block,
            'cavity' => $cavity_block,
            'ng_name' => $ng_name_block,
            'ng_count' => $ng_count_block,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_block,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

        }else{
          $serial_number = $request->get('serial_number');
          $operator_kensa = $request->get('employee_id');
          $product = $request->get('product');
          $start_time = $request->get('start_time');

          $ng_name_head_yrf = $request->get('ng_name_head_yrf');
          $ng_name_body_yrf = $request->get('ng_name_body_yrf');
          $ng_name_stopper_yrf = $request->get('ng_name_stopper_yrf');

          $ng_count_head_yrf = $request->get('ng_count_head_yrf');
          $ng_count_body_yrf = $request->get('ng_count_body_yrf');
          $ng_count_stopper_yrf = $request->get('ng_count_stopper_yrf');

          $qty_ng_head_yrf = $request->get('qty_ng_head_yrf');
          $qty_ng_body_yrf = $request->get('qty_ng_body_yrf');
          $qty_ng_stopper_yrf = $request->get('qty_ng_stopper_yrf');

          $tray = RcNgBox::where('tray',$request->get('tray'))->where('date',date('Y-m-d'))->first();
          $tray->ng_head = $qty_ng_head_yrf;
          $tray->ng_middle = $qty_ng_body_yrf;
          $tray->ng_foot = $qty_ng_stopper_yrf;
          $tray->save();

          $initial_head_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','A YRF H')->where('line',$request->get('line'))->first();
          $tag_head_yrf = $initial_head_yrf->tag;
          $kensa_initial_code = $initial_head_yrf->kensa_initial_code;
          $material_number_head_yrf = $initial_head_yrf->material_number;
          $cavity_head_yrf = $initial_head_yrf->cavity;

          $box = RcBoxResult::where('operator_kensa',$initial_head_yrf->operator_kensa)->where('product',$request->get('product'))->where('check_date',date('Y-m-d'))->first();
          $box->qty_box = $qty_check;
          $box->save();

          $initial_head_yrf->ng_name_kensa = null;
          $initial_head_yrf->ng_count_kensa = null;
          $initial_head_yrf->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_head_yrf,
            'material_number' => $material_number_head_yrf,
            'cavity' => $cavity_head_yrf,
            'ng_name' => $ng_name_head_yrf,
            'ng_count' => $ng_count_head_yrf,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_head_yrf,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

          $initial_body_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%A YRF B%')->where('line',$request->get('line'))->first();
          $tag_body_yrf = $initial_body_yrf->tag;
          $kensa_initial_code = $initial_body_yrf->kensa_initial_code;
          $material_number_body_yrf = $initial_body_yrf->material_number;
          $cavity_body_yrf = $initial_body_yrf->cavity;

          $initial_body_yrf->ng_name_kensa = null;
          $initial_body_yrf->ng_count_kensa = null;
          $initial_body_yrf->save();

          RcKensa::create([
            'kensa_initial_code' => $kensa_initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_body_yrf,
            'material_number' => $material_number_body_yrf,
            'cavity' => $cavity_body_yrf,
            'ng_name' => $ng_name_body_yrf,
            'ng_count' => $ng_count_body_yrf,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_body_yrf,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);

          $initial_stopper_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%A YRF S%')->where('line',$request->get('line'))->first();
          $tag_stopper_yrf = $initial_stopper_yrf->tag;
          $kensa_initial_code = $initial_stopper_yrf->kensa_initial_code;
          $material_number_stopper_yrf = $initial_stopper_yrf->material_number;
          $cavity_stopper_yrf = $initial_stopper_yrf->cavity;

          $initial_stopper_yrf->ng_name_kensa = null;
          $initial_stopper_yrf->ng_count_kensa = null;
          $initial_stopper_yrf->save();

          RcKensa::create([
            'serial_number' => $serial_number,
            'kensa_initial_code' => $kensa_initial_code,
            'operator_kensa' => $operator_kensa,
            'product' => $product,
            'start_time' => $start_time,
            'end_time' => date('Y-m-d H:i:s'),
            'tag' => $tag_stopper_yrf,
            'material_number' => $material_number_stopper_yrf,
            'cavity' => $cavity_stopper_yrf,
            'ng_name' => $ng_name_stopper_yrf,
            'ng_count' => $ng_count_stopper_yrf,
            'qty_check' => $qty_check,
            'qty_ng' => $qty_ng_stopper_yrf,
            'tray' => $request->get('tray'),
            'line' => $request->get('line'),
          ]);
        }
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

    public function updateKensa(Request $request)
    {
      try {
        if (str_contains($request->get('product'), 'YRS')) {
          $serial_number = $request->get('serial_number');
          $operator_kensa = $request->get('employee_id');
          $product = $request->get('product');
          $start_time = $request->get('start_time');
          $qty_check = $request->get('qty_check');

          $ng_name_head = $request->get('ng_name_head');
          $ng_name_middle = $request->get('ng_name_middle');
          $ng_name_foot = $request->get('ng_name_foot');
          $ng_name_block = $request->get('ng_name_block');

          $ng_count_head = $request->get('ng_count_head');
          $ng_count_middle = $request->get('ng_count_middle');
          $ng_count_foot = $request->get('ng_count_foot');
          $ng_count_block = $request->get('ng_count_block');

          $qty_ng_head = $request->get('qty_ng_head');
          $qty_ng_middle = $request->get('qty_ng_middle');
          $qty_ng_foot = $request->get('qty_ng_foot');
          $qty_ng_block = $request->get('qty_ng_block');

          $tray = RcNgBox::where('tray',$request->get('tray'))->where('date',date('Y-m-d'))->first();
          if ($tray->ng_head < $qty_ng_head) {
            $tray->ng_head = $qty_ng_head;
          }
          if ($tray->ng_middle < $qty_ng_middle) {
            $tray->ng_middle = $qty_ng_middle;
          }
          if ($tray->ng_foot < $qty_ng_foot) {
            $tray->ng_foot = $qty_ng_foot;
          }
          if ($tray->ng_block < $qty_ng_block) {
            $tray->ng_block = $qty_ng_block;
          }
          $tray->save();

          $initial_head = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','HJ')->where('line',$request->get('line'))->first();
          $initial_head->ng_name_kensa = $ng_name_head;
          $initial_head->ng_count_kensa = $ng_count_head;
          $initial_head->qty_check = $qty_check;
          $initial_head->qty_ng = $qty_ng_head;

          $box = RcBoxResult::where('operator_kensa',$initial_head->operator_kensa)->where('product',$request->get('product'))->where('check_date',date('Y-m-d'))->first();
          $box->qty_box = $qty_check;
          $box->save();

          $initial_middle = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%MJ%')->where('line',$request->get('line'))->first();
          $initial_middle->ng_name_kensa = $ng_name_middle;
          $initial_middle->ng_count_kensa = $ng_count_middle;
          $initial_middle->qty_check = $qty_check;
          $initial_middle->qty_ng = $qty_ng_middle;

          $initial_foot = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%FJ%')->where('line',$request->get('line'))->first();
          $initial_foot->ng_name_kensa = $ng_name_foot;
          $initial_foot->ng_count_kensa = $ng_count_foot;
          $initial_foot->qty_check = $qty_check;
          $initial_foot->qty_ng = $qty_ng_foot;

          $initial_block = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%BJ%')->where('line',$request->get('line'))->first();
          $initial_block->ng_name_kensa = $ng_name_block;
          $initial_block->ng_count_kensa = $ng_count_block;
          $initial_block->qty_check = $qty_check;
          $initial_block->qty_ng = $qty_ng_block;

          $initial_head->save();
          $initial_middle->save();
          $initial_foot->save();
          $initial_block->save();

        }else{
          $serial_number = $request->get('serial_number');
          $operator_kensa = $request->get('employee_id');
          $product = $request->get('product');
          $start_time = $request->get('start_time');
          $qty_check = $request->get('qty_check');

          $ng_name_head_yrf = $request->get('ng_name_head_yrf');
          $ng_name_body_yrf = $request->get('ng_name_body_yrf');
          $ng_name_stopper_yrf = $request->get('ng_name_stopper_yrf');

          $ng_count_head_yrf = $request->get('ng_count_head_yrf');
          $ng_count_body_yrf = $request->get('ng_count_body_yrf');
          $ng_count_stopper_yrf = $request->get('ng_count_stopper_yrf');

          $qty_ng_head_yrf = $request->get('qty_ng_head_yrf');
          $qty_ng_body_yrf = $request->get('qty_ng_body_yrf');
          $qty_ng_stopper_yrf = $request->get('qty_ng_stopper_yrf');

          $tray = RcNgBox::where('tray',$request->get('tray'))->where('date',date('Y-m-d'))->first();
          if ($tray->ng_head < $qty_ng_head_yrf) {
            $tray->ng_head = $qty_ng_head_yrf;
          }
          if ($tray->ng_middle < $qty_ng_body_yrf) {
            $tray->ng_middle = $qty_ng_body_yrf;
          }
          if ($tray->ng_foot < $qty_ng_stopper_yrf) {
            $tray->ng_foot = $qty_ng_stopper_yrf;
          }
          $tray->save();

          $initial_head_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','A YRF H')->where('line',$request->get('line'))->first();
          $initial_head_yrf->ng_name_kensa = $ng_name_head_yrf;
          $initial_head_yrf->ng_count_kensa = $ng_count_head_yrf;
          $initial_head_yrf->qty_check = $qty_check;
          $initial_head_yrf->qty_ng = $qty_ng_head_yrf;

          $box = RcBoxResult::where('operator_kensa',$initial_head_yrf->operator_kensa)->where('product',$request->get('product'))->where('check_date',date('Y-m-d'))->first();
          $box->qty_box = $qty_check;
          $box->save();

          $initial_body_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%A YRF B%')->where('line',$request->get('line'))->first();
          $initial_body_yrf->ng_name_kensa = $ng_name_body_yrf;
          $initial_body_yrf->ng_count_kensa = $ng_count_body_yrf;
          $initial_body_yrf->qty_check = $qty_check;
          $initial_body_yrf->qty_ng = $qty_ng_body_yrf;

          $initial_stopper_yrf = RcKensaInitial::where('serial_number',$serial_number)->where('part_type','like','%A YRF S%')->where('line',$request->get('line'))->first();
          $initial_stopper_yrf->ng_name_kensa = $ng_name_stopper_yrf;
          $initial_stopper_yrf->ng_count_kensa = $ng_count_stopper_yrf;
          $initial_stopper_yrf->qty_check = $qty_check;
          $initial_stopper_yrf->qty_ng = $qty_ng_stopper_yrf;

          $initial_head_yrf->save();
          $initial_body_yrf->save();
          $initial_stopper_yrf->save();
        }
        $tray = RcNgBox::where('tray',$request->get('tray'))->where('date',date('Y-m-d'))->first();
        $response = array(
              'status' => true,
              'tray' => $tray
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

    public function indexKensaReport()
    {
      return view('recorder.report.report_kensa')
      ->with('title', 'Report Kensa Kakuning Recorder')
      ->with('title_jp', 'リコーダー検査報告')
      ->with('page', 'Report Kensa Kakuning Recorder');
    }

    public function fetchKensaReport(Request $request)
    {
      try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $datenow = date('Y-m-d');

        if($request->get('date_to') == null){
          if($request->get('date_from') == null){
            $date_from = date('Y-m-01');
            $date_to = date('Y-m-d');
          }
          elseif($request->get('date_from') != null){
            $date_from = $request->get('date_from');
            $date_to = date('Y-m-d');
          }
        }
        elseif($request->get('date_to') != null){
          if($request->get('date_from') == null){
            $date_from = date('Y-m-01');
            $date_to = $request->get('date_to');
          }
          elseif($request->get('date_from') != null){
            $date_from = $request->get('date_from');
            $date_to = $request->get('date_to');
          }
        }
        
        // $data = RcKensa::
        // select('*','rc_kensas.created_at as created')
        // ->join('employee_syncs','employee_syncs.employee_id','rc_kensas.operator_kensa')
        // ->join('injection_parts','injection_parts.gmc','rc_kensas.material_number')
        // ->where('injection_parts.deleted_at',null)
        // ->whereDate('rc_kensas.created_at','>=',$date_from)
        // ->whereDate('rc_kensas.created_at','<=',$date_to)
        // ->orderBy('rc_kensas.created_at','desc')->get();

        $data = DB::SELECT("SELECT DISTINCT
          ( rc_kensas.serial_number ),
          rc_kensas.operator_kensa,
          rc_kensas.material_number,
          rc_kensas.product,
          injection_parts.part_name,
          injection_parts.part_code,
          rc_kensas.start_time,
          rc_kensas.end_time,
          rc_kensas.cavity,
          rc_kensas.ng_name,
          rc_kensas.ng_count,
          rc_kensas.created_at,
          COALESCE ((
            SELECT
            IF
              ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', rc_box_results.qty_box * 20, rc_box_results.qty_box * 50 ) 
            FROM
              rc_box_results 
            WHERE
              rc_box_results.operator_kensa = rc_kensas.operator_kensa 
              AND rc_box_results.product = rc_kensas.product 
              AND rc_box_results.check_date = DATE( rc_kensas.created_at )),
            0 
          ) AS qty_check,
          rc_kensas.qty_ng,
          rc_kensa_initials.ng_name AS ng_name_injection,
          rc_kensa_initials.ng_count AS ng_count_injection,
          rc_kensa_initials.mesin_injection,
          rc_kensa_initials.molding,
          rc_kensa_initials.operator_molding,
          rc_kensa_initials.dryer_resin,
          rc_kensa_initials.lot_number_resin,
          rc_kensa_initials.qty_resin,
          rc_kensa_initials.operator_injection,
          empkensa.`name` AS operator_kensa_name,
          empinj.`name` AS operator_injection_name 
        FROM
          rc_kensas
          LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
          LEFT JOIN rc_kensa_initials ON rc_kensa_initials.kensa_initial_code = rc_kensas.kensa_initial_code
          LEFT JOIN employee_syncs empinj ON empinj.employee_id = rc_kensa_initials.operator_injection
          LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = rc_kensas.operator_kensa 
        WHERE
          rc_kensas.ng_name IS NOT NULL 
          AND injection_parts.deleted_at IS NULL 
          AND injection_parts.remark = 'injection' 
          AND DATE( rc_kensas.created_at ) >= '".$date_from."' 
          AND DATE( rc_kensas.created_at ) <= '".$date_to."' 
        ORDER BY
          rc_kensas.created_at DESC,
          rc_kensas.serial_number");

        $response = array(
              'status' => true,
              'datas' => $data
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

    public function indexKensaInitial()
    {
      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
      if ($emp != null) {
        if ($emp->position != 'Operator' || $emp->position != 'Senior Operator') {
          return view('recorder.process.kensa_initial')
          ->with('title', 'Inisialisasi Kensa Kakuning Recorder')
          ->with('title_jp', 'リコーダー検査初期化')
          ->with('product_type',$this->product_type)
          ->with('page', 'Inisialisasi Kensa Kakuning Recorder');
        }else{
          return view('404');
        }
      }else{
        return view('404');
      }
    }

    public function fetchKensaInitial()
    {
      try {
        $datas = DB::SELECT("SELECT
          *,
          injection_parts.part_name AS mat_desc,
          rc_assy_initials.created_at AS kensa_created,
          rc_assy_initials.part_type AS typepart,
          rc_assy_initials.part_name AS part_kensa 
        FROM
          rc_assy_initials
          JOIN injection_parts ON injection_parts.gmc = rc_assy_initials.material_number 
        WHERE
          injection_parts.deleted_at IS NULL 
          AND DATE( rc_assy_initials.created_at ) BETWEEN DATE(
          NOW()) - INTERVAL 3 DAY 
          AND DATE(
          NOW())
        ORDER BY
          rc_assy_initials.created_at DESC");

        $response = array(
              'status' => true,
              'datas' => $datas
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

    public function inputKensaInitial(Request $request)
    {
      try {
        // $emp_code = DB::SELECT('SELECT * FROM employee_groups where employee_id = "'.$request->get('employee_id').'" and location = "rc-assy"');

        // foreach ($emp_code as $key) {
        //   $group = $key->group;
        // }

        // $code_generator = CodeGenerator::where('note', '=', 'kakuning-rc')->first();
        // $serial_number = $group.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        // $code_generator->index = $code_generator->index+1;
        // $code_generator->save();

        $now = date('Y-m-d H:i:s');

        $rckensa = RcAssyInitial::where('status','Open')->where('line',$request->get('line'))->get();

        $kensaoldhead = [];
        $kensaoldmiddle = [];
        $kensaoldfoot = [];
        $kensaoldblock = [];

        $kensaoldheadyrf = [];
        $kensaoldbodyrf = [];
        $kensaoldstopperyrf = [];

        if (count($rckensa) > 0) {
          foreach ($rckensa as $key) {
            $kensaold = RcAssyInitial::where('id',$key->id)->where('line',$request->get('line'))->first();

            if (str_contains($kensaold->product, 'YRS')) {
              if ($kensaold->part_type == 'HJ') {
                array_push($kensaoldhead, $kensaold);
              }
              if ($kensaold->part_type == 'FJ') {
                array_push($kensaoldfoot, $kensaold);
              }
              if ($kensaold->part_type == 'BJ') {
                array_push($kensaoldblock, $kensaold);
              }
              if (str_contains($kensaold->part_type, 'MJ')) {
               array_push($kensaoldmiddle, $kensaold); 
              }
            }

            if (str_contains($kensaold->product, 'YRF')) {
              if ($kensaold->part_type == 'A YRF H') {
                array_push($kensaoldheadyrf, $kensaold);
              }
              if ($kensaold->part_type == 'A YRF B') {
                array_push($kensaoldbodyrf, $kensaold);
              }
              if ($kensaold->part_type == 'A YRF S') {
                array_push($kensaoldstopperyrf, $kensaold);
              }
            }

            // $kensaold->status = 'Close';
            // $kensaold->save();
          }
        }

        if (str_contains($request->get('product'), 'YRS')) {
          $product = $request->get('product');
          $tag_head = $request->get('tag_head');
          $tag_middle = $request->get('tag_middle');
          $tag_foot = $request->get('tag_foot');
          $tag_block = $request->get('tag_block');

          $material_number_head = $request->get('material_number_head');
          $part_name_head = $request->get('part_name_head');
          $part_type_head = $request->get('part_type_head');
          $color_head = $request->get('color_head');
          $cavity_head = $request->get('cavity_head');
          $location_head = $request->get('location_head');

          $material_number_middle = $request->get('material_number_middle');
          $part_name_middle = $request->get('part_name_middle');
          $part_type_middle = $request->get('part_type_middle');
          $color_middle = $request->get('color_middle');
          $cavity_middle = $request->get('cavity_middle');
          $location_middle = $request->get('location_middle');

          $material_number_foot = $request->get('material_number_foot');
          $part_name_foot = $request->get('part_name_foot');
          $part_type_foot = $request->get('part_type_foot');
          $color_foot = $request->get('color_foot');
          $cavity_foot = $request->get('cavity_foot');
          $location_foot = $request->get('location_foot');

          $material_number_block = $request->get('material_number_block');
          $part_name_block = $request->get('part_name_block');
          $part_type_block = $request->get('part_type_block');
          $color_block = $request->get('color_block');
          $cavity_block = $request->get('cavity_block');
          $location_block = $request->get('location_block');

          //INJECTION

          if ($tag_head != "") {
            $injection_process_head = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_head as $key_head) {
              $no_kanban_head = $key_head->no_kanban;
              $start_time_head = $key_head->start_time;
              $end_time_head = $key_head->end_time;
              $mesin_head = $key_head->mesin;
              $qty_head = $key_head->qty;
              $employee_id_head = $key_head->employee_id;
              $ng_name_head = $key_head->ng_name;
              $ng_count_head = $key_head->ng_count;
            }
          }

          if ($tag_middle != "") {
            $injection_process_middle = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_middle')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_middle as $key_middle) {
              $no_kanban_middle = $key_middle->no_kanban;
              $start_time_middle = $key_middle->start_time;
              $end_time_middle = $key_middle->end_time;
              $mesin_middle = $key_middle->mesin;
              $qty_middle = $key_middle->qty;
              $employee_id_middle = $key_middle->employee_id;
              $ng_name_middle = $key_middle->ng_name;
              $ng_count_middle = $key_middle->ng_count;
            }
          }

          if ($tag_foot != "") {
            $injection_process_foot = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_foot')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_foot as $key_foot) {
              $no_kanban_foot = $key_foot->no_kanban;
              $start_time_foot = $key_foot->start_time;
              $end_time_foot = $key_foot->end_time;
              $mesin_foot = $key_foot->mesin;
              $qty_foot = $key_foot->qty;
              $employee_id_foot = $key_foot->employee_id;
              $ng_name_foot = $key_foot->ng_name;
              $ng_count_foot = $key_foot->ng_count;
            }
          }

          if ($tag_block != "") {
            $injection_process_block = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count,
                  injection_process_logs.material_number,
                  injection_process_logs.part_name,
                  injection_process_logs.part_type,
                  injection_process_logs.color,
                  injection_process_logs.cavity
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_block')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_block as $key_block) {
              $no_kanban_block = $key_block->no_kanban;
              $start_time_block = $key_block->start_time;
              $end_time_block = $key_block->end_time;
              $mesin_block = $key_block->mesin;
              $qty_block = $key_block->qty;
              $employee_id_block = $key_block->employee_id;
              $ng_name_block = $key_block->ng_name;
              $ng_count_block = $key_block->ng_count;

              $material_number_block = $key_block->material_number;
              $part_name_block = $key_block->part_name;
              $part_type_block = $key_block->part_type;
              $color_block = $key_block->color;
              $cavity_block = $key_block->cavity;
              $location_block = 'RC91';
            }
          }

          //MOLDING

          if ($tag_head != "") {
            $molding_head = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            foreach ($molding_head as $key_head) {
              $molding_head = $key_head->part;
              $last_shot_before_head = $key_head->last_shot_pasang;
              $last_shot_injection_head = $key_head->last_shot_running;
              $start_molding_head = $key_head->start_time;
              $finish_molding_head = $key_head->end_time;
              $note_molding_head = $key_head->note;
              $pic_molding_head = $key_head->pic;
            }
          }

          if ($tag_middle != "") {
            $molding_middle = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_middle')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            foreach ($molding_middle as $key_middle) {
              $molding_middle = $key_middle->part;
              $last_shot_before_middle = $key_middle->last_shot_pasang;
              $last_shot_injection_middle = $key_middle->last_shot_running;
              $start_molding_middle = $key_middle->start_time;
              $finish_molding_middle = $key_middle->end_time;
              $note_molding_middle = $key_middle->note;
              $pic_molding_middle = $key_middle->pic;
            }
          }

          if ($tag_foot != "") {
            $molding_foot = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_foot')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            foreach ($molding_foot as $key_foot) {
              $molding_foot = $key_foot->part;
              $last_shot_before_foot = $key_foot->last_shot_pasang;
              $last_shot_injection_foot = $key_foot->last_shot_running;
              $start_molding_foot = $key_foot->start_time;
              $finish_molding_foot = $key_foot->end_time;
              $note_molding_foot = $key_foot->note;
              $pic_molding_foot = $key_foot->pic;
            }
          }

          if ($tag_block != "") {
            $molding_block = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_block')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            foreach ($molding_block as $key_block) {
              $molding_block = $key_block->part;
              $last_shot_before_block = $key_block->last_shot_pasang;
              $last_shot_injection_block = $key_block->last_shot_running;
              $start_molding_block = $key_block->start_time;
              $finish_molding_block = $key_block->end_time;
              $note_molding_block = $key_block->note;
              $pic_molding_block = $key_block->pic;
            }
          }

          //DRYER

          if ($tag_head != "") {
            $dryer_head = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            foreach ($dryer_head as $key_head) {
              $material_resin_head = $key_head->material_number;
              $dryer_resin_head = $key_head->dryer;
              $lot_number_resin_head = $key_head->lot_number;
              $qty_resin_head = $key_head->qty;
              $create_resin_head = $key_head->created_at;
              $operator_resin_head = $key_head->employee_id;
            }
          }

          if ($tag_middle != "") {
            $dryer_middle = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_middle')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            foreach ($dryer_middle as $key_middle) {
              $material_resin_middle = $key_middle->material_number;
              $dryer_resin_middle = $key_middle->dryer;
              $lot_number_resin_middle = $key_middle->lot_number;
              $qty_resin_middle = $key_middle->qty;
              $create_resin_middle = $key_middle->created_at;
              $operator_resin_middle = $key_middle->employee_id;
            }
          }

          if ($tag_foot != "") {
            $dryer_foot = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_foot')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            foreach ($dryer_foot as $key_foot) {
              $material_resin_foot = $key_foot->material_number;
              $dryer_resin_foot = $key_foot->dryer;
              $lot_number_resin_foot = $key_foot->lot_number;
              $qty_resin_foot = $key_foot->qty;
              $create_resin_foot = $key_foot->created_at;
              $operator_resin_foot = $key_foot->employee_id;
            }
          }

          if ($tag_block != "") {
            $dryer_block = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_block')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            foreach ($dryer_block as $key_block) {
              $material_resin_block = $key_block->material_number;
              $dryer_resin_block = $key_block->dryer;
              $lot_number_resin_block = $key_block->lot_number;
              $qty_resin_block = $key_block->qty;
              $create_resin_block = $key_block->created_at;
              $operator_resin_block = $key_block->employee_id;
            }
          }

          if ($tag_head != "") {
            $transaction_head = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_head = [];
            $location_transaction_head = [];
            $operator_transaction_head = [];
            $status_transaction_head = [];

            foreach ($transaction_head as $key_head) {
              $create_transaction_head[] = $key_head->created_at;
              $location_transaction_head[] = $key_head->location;
              $status_transaction_head[] = $key_head->status;
              $operator_transaction_head[] = $key_head->employee_id;
            }

            $create_transaction_heads = join('_',$create_transaction_head);
            $location_transaction_heads = join('_',$location_transaction_head);
            $operator_transaction_heads = join('_',$operator_transaction_head);
            $status_transaction_heads = join('_',$status_transaction_head);
          }

          if ($tag_middle != "") {
            $transaction_middle = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_middle')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_middle = [];
            $location_transaction_middle = [];
            $operator_transaction_middle = [];
            $status_transaction_middle = [];

            foreach ($transaction_middle as $key_middle) {
              $create_transaction_middle[] = $key_middle->created_at;
              $location_transaction_middle[] = $key_middle->location;
              $status_transaction_middle[] = $key_middle->status;
              $operator_transaction_middle[] = $key_middle->employee_id;
            }

            $create_transaction_middles = join('_',$create_transaction_middle);
            $location_transaction_middles = join('_',$location_transaction_middle);
            $operator_transaction_middles = join('_',$operator_transaction_middle);
            $status_transaction_middles = join('_',$status_transaction_middle);
          }

          if ($tag_foot != "") {
            $transaction_foot = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_foot')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_foot = [];
            $location_transaction_foot = [];
            $operator_transaction_foot = [];
            $status_transaction_foot = [];

            foreach ($transaction_foot as $key_foot) {
              $create_transaction_foot[] = $key_foot->created_at;
              $location_transaction_foot[] = $key_foot->location;
              $status_transaction_foot[] = $key_foot->status;
              $operator_transaction_foot[] = $key_foot->employee_id;
            }

            $create_transaction_foots = join('_',$create_transaction_foot);
            $location_transaction_foots = join('_',$location_transaction_foot);
            $operator_transaction_foots = join('_',$operator_transaction_foot);
            $status_transaction_foots = join('_',$status_transaction_foot);
          }

          if ($tag_block != "") {
            $transaction_block = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_block')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_block = [];
            $location_transaction_block = [];
            $operator_transaction_block = [];
            $status_transaction_block = [];

            foreach ($transaction_block as $key_block) {
              $create_transaction_block[] = $key_block->created_at;
              $location_transaction_block[] = $key_block->location;
              $status_transaction_block[] = $key_block->status;
              $operator_transaction_block[] = $key_block->employee_id;
            }

            $create_transaction_blocks = join('_',$create_transaction_block);
            $location_transaction_blocks = join('_',$location_transaction_block);
            $operator_transaction_blocks = join('_',$operator_transaction_block);
            $status_transaction_blocks = join('_',$status_transaction_block);
          }

          $now = date('Y-m-d H:i:s');

          if ($tag_head != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_head.'_'.$material_number_head.'_'.$product.'_'.$no_kanban_head.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_head,
              'part_name' => $part_name_head,
              'part_type' => $part_type_head,
              'color' => $color_head,
              'cavity' => $cavity_head,
              'location' => $location_head,
              'tag' => $tag_head,
              'no_kanban_injection' => $no_kanban_head,
              'start_injection' => $start_time_head,
              'finish_injection' => $end_time_head,
              'mesin_injection' => $mesin_head,
              'qty_injection' => $qty_head,
              'operator_injection' => $employee_id_head,
              'molding' => $molding_head,
              'last_shot_before' => $last_shot_before_head,
              'last_shot_injection' => $last_shot_injection_head,
              'start_molding' => $start_molding_head,
              'finish_molding' => $finish_molding_head,
              'note_molding' => $note_molding_head,
              'operator_molding' => $pic_molding_head,
              'material_resin' => $material_resin_head,
              'dryer_resin' => $dryer_resin_head,
              'lot_number_resin' => $lot_number_resin_head,
              'qty_resin' => $qty_resin_head,
              'create_resin' => $create_resin_head,
              'operator_resin' => $operator_resin_head,
              'ng_name' => $ng_name_head,
              'ng_count' => $ng_count_head,
              'create_transaction' => $create_transaction_heads,
              'location_transaction' => $location_transaction_heads,
              'operator_transaction' => $operator_transaction_heads,
              'status_transaction' => $status_transaction_heads,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldhead[0]->tag.'_'.$kensaoldhead[0]->material_number.'_'.$product.'_'.$kensaoldhead[0]->no_kanban_injection.'_'.$now,
              'product' => $product,
              'material_number' => $kensaoldhead[0]->material_number,
              'part_name' => $kensaoldhead[0]->part_name,
              'part_type' => $kensaoldhead[0]->part_type,
              'color' => $kensaoldhead[0]->color,
              'cavity' => $kensaoldhead[0]->cavity,
              'location' => $kensaoldhead[0]->location,
              'tag' => $kensaoldhead[0]->tag,
              'no_kanban_injection' => $kensaoldhead[0]->no_kanban_injection,
              'start_injection' => $kensaoldhead[0]->start_injection,
              'finish_injection' => $kensaoldhead[0]->finish_injection,
              'mesin_injection' => $kensaoldhead[0]->mesin_injection,
              'qty_injection' => $kensaoldhead[0]->qty_injection,
              'operator_injection' => $kensaoldhead[0]->operator_injection,
              'molding' => $kensaoldhead[0]->molding,
              'last_shot_before' => $kensaoldhead[0]->last_shot_before,
              'last_shot_injection' => $kensaoldhead[0]->last_shot_injection,
              'start_molding' => $kensaoldhead[0]->start_molding,
              'finish_molding' => $kensaoldhead[0]->finish_molding,
              'note_molding' => $kensaoldhead[0]->note_molding,
              'operator_molding' => $kensaoldhead[0]->operator_molding,
              'material_resin' => $kensaoldhead[0]->material_resin,
              'dryer_resin' => $kensaoldhead[0]->dryer_resin,
              'lot_number_resin' => $kensaoldhead[0]->lot_number_resin,
              'qty_resin' => $kensaoldhead[0]->qty_resin,
              'create_resin' => $kensaoldhead[0]->create_resin,
              'operator_resin' => $kensaoldhead[0]->operator_resin,
              'ng_name' => $kensaoldhead[0]->ng_name,
              'ng_count' => $kensaoldhead[0]->ng_count,
              'create_transaction' => $kensaoldhead[0]->create_transaction,
              'location_transaction' => $kensaoldhead[0]->location_transaction,
              'operator_transaction' => $kensaoldhead[0]->operator_transaction,
              'status_transaction' => $kensaoldhead[0]->status_transaction,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }

          if ($tag_middle != "") {
            RcAssyInitial::create([
               'initial_code' => $tag_middle.'_'.$material_number_middle.'_'.$product.'_'.$no_kanban_middle.'_'.$now,
               'product' => $product,
               'material_number' => $material_number_middle,
               'part_name' => $part_name_middle,
               'part_type' => $part_type_middle,
               'color' => $color_middle,
               'cavity' => $cavity_middle,
               'location' => $location_middle,
               'tag' => $tag_middle,
               'no_kanban_injection' => $no_kanban_middle,
                'start_injection' => $start_time_middle,
                'finish_injection' => $end_time_middle,
                'mesin_injection' => $mesin_middle,
                'qty_injection' => $qty_middle,
                'operator_injection' => $employee_id_middle,
                'molding' => $molding_middle,
                'last_shot_before' => $last_shot_before_middle,
                'last_shot_injection' => $last_shot_injection_middle,
                'start_molding' => $start_molding_middle,
                'finish_molding' => $finish_molding_middle,
                'note_molding' => $note_molding_middle,
                'operator_molding' => $pic_molding_middle,
                'material_resin' => $material_resin_middle,
              'dryer_resin' => $dryer_resin_middle,
              'lot_number_resin' => $lot_number_resin_middle,
              'qty_resin' => $qty_resin_middle,
              'create_resin' => $create_resin_middle,
              'operator_resin' => $operator_resin_middle,
              'ng_name' => $ng_name_middle,
              'ng_count' => $ng_count_middle,
              'create_transaction' => $create_transaction_middles,
              'location_transaction' => $location_transaction_middles,
              'operator_transaction' => $operator_transaction_middles,
              'status_transaction' => $status_transaction_middles,
              'line' => $request->get('line'),
               'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
               'initial_code' => $kensaoldmiddle[0]->tag.'_'.$kensaoldmiddle[0]->material_number.'_'.$product.'_'.$kensaoldmiddle[0]->no_kanban_injection.'_'.$now,
               'product' => $product,
               'material_number' => $kensaoldmiddle[0]->material_number,
               'part_name' => $kensaoldmiddle[0]->part_name,
               'part_type' => $kensaoldmiddle[0]->part_type,
               'color' => $kensaoldmiddle[0]->color,
               'cavity' => $kensaoldmiddle[0]->cavity,
               'location' => $kensaoldmiddle[0]->location,
               'tag' => $kensaoldmiddle[0]->tag,
               'no_kanban_injection' => $kensaoldmiddle[0]->no_kanban_injection,
                'start_injection' => $kensaoldmiddle[0]->start_injection,
                'finish_injection' => $kensaoldmiddle[0]->finish_injection,
                'mesin_injection' => $kensaoldmiddle[0]->mesin_injection,
                'qty_injection' => $kensaoldmiddle[0]->qty_injection,
                'operator_injection' => $kensaoldmiddle[0]->operator_injection,
                'molding' => $kensaoldmiddle[0]->molding,
                'last_shot_before' => $kensaoldmiddle[0]->last_shot_before,
                'last_shot_injection' => $kensaoldmiddle[0]->last_shot_injection,
                'start_molding' => $kensaoldmiddle[0]->start_molding,
                'finish_molding' => $kensaoldmiddle[0]->finish_molding,
                'note_molding' => $kensaoldmiddle[0]->note_molding,
                'operator_molding' => $kensaoldmiddle[0]->operator_molding,
                'material_resin' => $kensaoldmiddle[0]->material_resin,
                'dryer_resin' => $kensaoldmiddle[0]->dryer_resin,
                'lot_number_resin' => $kensaoldmiddle[0]->lot_number_resin,
                'qty_resin' => $kensaoldmiddle[0]->qty_resin,
                'create_resin' => $kensaoldmiddle[0]->create_resin,
                'operator_resin' => $kensaoldmiddle[0]->operator_resin,
                'ng_name' => $kensaoldmiddle[0]->ng_name,
                'ng_count' => $kensaoldmiddle[0]->ng_count,
                'create_transaction' => $kensaoldmiddle[0]->create_transaction,
                'location_transaction' => $kensaoldmiddle[0]->location_transaction,
                'operator_transaction' => $kensaoldmiddle[0]->operator_transaction,
                'status_transaction' => $kensaoldmiddle[0]->status_transaction,
                'line' => $request->get('line'),
                'status' => 'Open',
                'created_by' => Auth::id()
            ]);
          }

          if ($tag_foot != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_foot.'_'.$material_number_foot.'_'.$product.'_'.$no_kanban_foot.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_foot,
              'part_name' => $part_name_foot,
              'part_type' => $part_type_foot,
              'color' => $color_foot,
              'cavity' => $cavity_foot,
              'location' => $location_foot,
              'tag' => $tag_foot,
              'no_kanban_injection' => $no_kanban_foot,
              'start_injection' => $start_time_foot,
              'finish_injection' => $end_time_foot,
              'mesin_injection' => $mesin_foot,
              'qty_injection' => $qty_foot,
              'operator_injection' => $employee_id_foot,
              'molding' => $molding_foot,
              'last_shot_before' => $last_shot_before_foot,
              'last_shot_injection' => $last_shot_injection_foot,
              'start_molding' => $start_molding_foot,
              'finish_molding' => $finish_molding_foot,
              'note_molding' => $note_molding_foot,
              'operator_molding' => $pic_molding_foot,
              'material_resin' => $material_resin_foot,
              'dryer_resin' => $dryer_resin_foot,
              'lot_number_resin' => $lot_number_resin_foot,
              'qty_resin' => $qty_resin_foot,
              'create_resin' => $create_resin_foot,
              'operator_resin' => $operator_resin_foot,
              'ng_name' => $ng_name_foot,
              'ng_count' => $ng_count_foot,
              'create_transaction' => $create_transaction_foots,
              'location_transaction' => $location_transaction_foots,
              'operator_transaction' => $operator_transaction_foots,
              'status_transaction' => $status_transaction_foots,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldfoot[0]->tag.'_'.$kensaoldfoot[0]->material_number.'_'.$product.'_'.$kensaoldfoot[0]->no_kanban_injection.'_'.$now,
               'product' => $product,
               'material_number' => $kensaoldfoot[0]->material_number,
               'part_name' => $kensaoldfoot[0]->part_name,
               'part_type' => $kensaoldfoot[0]->part_type,
               'color' => $kensaoldfoot[0]->color,
               'cavity' => $kensaoldfoot[0]->cavity,
               'location' => $kensaoldfoot[0]->location,
               'tag' => $kensaoldfoot[0]->tag,
               'no_kanban_injection' => $kensaoldfoot[0]->no_kanban_injection,
                'start_injection' => $kensaoldfoot[0]->start_injection,
                'finish_injection' => $kensaoldfoot[0]->finish_injection,
                'mesin_injection' => $kensaoldfoot[0]->mesin_injection,
                'qty_injection' => $kensaoldfoot[0]->qty_injection,
                'operator_injection' => $kensaoldfoot[0]->operator_injection,
                'molding' => $kensaoldfoot[0]->molding,
                'last_shot_before' => $kensaoldfoot[0]->last_shot_before,
                'last_shot_injection' => $kensaoldfoot[0]->last_shot_injection,
                'start_molding' => $kensaoldfoot[0]->start_molding,
                'finish_molding' => $kensaoldfoot[0]->finish_molding,
                'note_molding' => $kensaoldfoot[0]->note_molding,
                'operator_molding' => $kensaoldfoot[0]->operator_molding,
                'material_resin' => $kensaoldfoot[0]->material_resin,
                'dryer_resin' => $kensaoldfoot[0]->dryer_resin,
                'lot_number_resin' => $kensaoldfoot[0]->lot_number_resin,
                'qty_resin' => $kensaoldfoot[0]->qty_resin,
                'create_resin' => $kensaoldfoot[0]->create_resin,
                'operator_resin' => $kensaoldfoot[0]->operator_resin,
                'ng_name' => $kensaoldfoot[0]->ng_name,
                'ng_count' => $kensaoldfoot[0]->ng_count,
                'create_transaction' => $kensaoldfoot[0]->create_transaction,
                'location_transaction' => $kensaoldfoot[0]->location_transaction,
                'operator_transaction' => $kensaoldfoot[0]->operator_transaction,
                'status_transaction' => $kensaoldfoot[0]->status_transaction,
                'line' => $request->get('line'),
                'status' => 'Open',
                'created_by' => Auth::id()
            ]);
          }

          if ($tag_block != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_block.'_'.$material_number_block.'_'.$product.'_'.$no_kanban_block.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_block,
              'part_name' => $part_name_block,
              'part_type' => $part_type_block,
              'color' => $color_block,
              'cavity' => $cavity_block,
              'location' => $location_block,
              'tag' => $tag_block,
              'no_kanban_injection' => $no_kanban_block,
              'start_injection' => $start_time_block,
              'finish_injection' => $end_time_block,
              'mesin_injection' => $mesin_block,
              'qty_injection' => $qty_block,
              'operator_injection' => $employee_id_block,
              'molding' => $molding_block,
              'last_shot_before' => $last_shot_before_block,
              'last_shot_injection' => $last_shot_injection_block,
              'start_molding' => $start_molding_block,
              'finish_molding' => $finish_molding_block,
              'note_molding' => $note_molding_block,
              'operator_molding' => $pic_molding_block,
              'material_resin' => $material_resin_block,
              'dryer_resin' => $dryer_resin_block,
              'lot_number_resin' => $lot_number_resin_block,
              'qty_resin' => $qty_resin_block,
              'create_resin' => $create_resin_block,
              'operator_resin' => $operator_resin_block,
              'ng_name' => $ng_name_block,
              'ng_count' => $ng_count_block,
              'create_transaction' => $create_transaction_blocks,
              'location_transaction' => $location_transaction_blocks,
              'operator_transaction' => $operator_transaction_blocks,
              'status_transaction' => $status_transaction_blocks,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldblock[0]->tag.'_'.$kensaoldblock[0]->material_number.'_'.$product.'_'.$kensaoldblock[0]->no_kanban_injection.'_'.$now,
               'product' => $product,
               'material_number' => $kensaoldblock[0]->material_number,
               'part_name' => $kensaoldblock[0]->part_name,
               'part_type' => $kensaoldblock[0]->part_type,
               'color' => $kensaoldblock[0]->color,
               'cavity' => $kensaoldblock[0]->cavity,
               'location' => $kensaoldblock[0]->location,
               'tag' => $kensaoldblock[0]->tag,
               'no_kanban_injection' => $kensaoldblock[0]->no_kanban_injection,
                'start_injection' => $kensaoldblock[0]->start_injection,
                'finish_injection' => $kensaoldblock[0]->finish_injection,
                'mesin_injection' => $kensaoldblock[0]->mesin_injection,
                'qty_injection' => $kensaoldblock[0]->qty_injection,
                'operator_injection' => $kensaoldblock[0]->operator_injection,
                'molding' => $kensaoldblock[0]->molding,
                'last_shot_before' => $kensaoldblock[0]->last_shot_before,
                'last_shot_injection' => $kensaoldblock[0]->last_shot_injection,
                'start_molding' => $kensaoldblock[0]->start_molding,
                'finish_molding' => $kensaoldblock[0]->finish_molding,
                'note_molding' => $kensaoldblock[0]->note_molding,
                'operator_molding' => $kensaoldblock[0]->operator_molding,
                'material_resin' => $kensaoldblock[0]->material_resin,
                'dryer_resin' => $kensaoldblock[0]->dryer_resin,
                'lot_number_resin' => $kensaoldblock[0]->lot_number_resin,
                'qty_resin' => $kensaoldblock[0]->qty_resin,
                'create_resin' => $kensaoldblock[0]->create_resin,
                'operator_resin' => $kensaoldblock[0]->operator_resin,
                'ng_name' => $kensaoldblock[0]->ng_name,
                'ng_count' => $kensaoldblock[0]->ng_count,
                'create_transaction' => $kensaoldblock[0]->create_transaction,
                'location_transaction' => $kensaoldblock[0]->location_transaction,
                'operator_transaction' => $kensaoldblock[0]->operator_transaction,
                'status_transaction' => $kensaoldblock[0]->status_transaction,
                'line' => $request->get('line'),
                'status' => 'Open',
                'created_by' => Auth::id()
            ]);
          }
        }else{
          $product = $request->get('product');
          $tag_head_yrf = $request->get('tag_head_yrf');
          $tag_body_yrf = $request->get('tag_body_yrf');
          $tag_stopper_yrf = $request->get('tag_stopper_yrf');

          $material_number_head_yrf = $request->get('material_number_head_yrf');
          $part_name_head_yrf = $request->get('part_name_head_yrf');
          $part_type_head_yrf = $request->get('part_type_head_yrf');
          $color_head_yrf = $request->get('color_head_yrf');
          $cavity_head_yrf = $request->get('cavity_head_yrf');
          $location_head_yrf = $request->get('location_head_yrf');

          $material_number_body_yrf = $request->get('material_number_body_yrf');
          $part_name_body_yrf = $request->get('part_name_body_yrf');
          $part_type_body_yrf = $request->get('part_type_body_yrf');
          $color_body_yrf = $request->get('color_body_yrf');
          $cavity_body_yrf = $request->get('cavity_body_yrf');
          $location_body_yrf = $request->get('location_body_yrf');

          $material_number_stopper_yrf = $request->get('material_number_stopper_yrf');
          $part_name_stopper_yrf = $request->get('part_name_stopper_yrf');
          $part_type_stopper_yrf = $request->get('part_type_stopper_yrf');
          $color_stopper_yrf = $request->get('color_stopper_yrf');
          $cavity_stopper_yrf = $request->get('cavity_stopper_yrf');
          $location_stopper_yrf = $request->get('location_stopper_yrf');

          if ($tag_head_yrf != "") {
            $injection_process_head_yrf = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_head_yrf as $key_head_yrf) {
              $no_kanban_head_yrf = $key_head_yrf->no_kanban;
              $start_time_head_yrf = $key_head_yrf->start_time;
              $end_time_head_yrf = $key_head_yrf->end_time;
              $mesin_head_yrf = $key_head_yrf->mesin;
              $qty_head_yrf = $key_head_yrf->qty;
              $employee_id_head_yrf = $key_head_yrf->employee_id;
              $ng_name_head_yrf = $key_head_yrf->ng_name;
              $ng_count_head_yrf = $key_head_yrf->ng_count;
            }
          }

          if ($tag_body_yrf != "") {
            $injection_process_body_yrf = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_body_yrf as $key_body_yrf) {
              $no_kanban_body_yrf = $key_body_yrf->no_kanban;
              $start_time_body_yrf = $key_body_yrf->start_time;
              $end_time_body_yrf = $key_body_yrf->end_time;
              $mesin_body_yrf = $key_body_yrf->mesin;
              $qty_body_yrf = $key_body_yrf->qty;
              $employee_id_body_yrf = $key_body_yrf->employee_id;
              $ng_name_body_yrf = $key_body_yrf->ng_name;
              $ng_count_body_yrf = $key_body_yrf->ng_count;
            }
          }

          if ($tag_stopper_yrf != "") {
            $injection_process_stopper_yrf = DB::SELECT("SELECT
                  injection_tags.no_kanban,
                  injection_process_logs.start_time,
                  injection_process_logs.end_time,
                  injection_process_logs.mesin,
                  injection_process_logs.shot as qty,
                  opmesin.employee_id,
                  injection_process_logs.ng_name,
                  injection_process_logs.ng_count
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."'  
              ORDER BY
                  injection_process_logs.created_at DESC
                  LIMIT 1
                  ");

            foreach ($injection_process_stopper_yrf as $key_stopper_yrf) {
              $no_kanban_stopper_yrf = $key_stopper_yrf->no_kanban;
              $start_time_stopper_yrf = $key_stopper_yrf->start_time;
              $end_time_stopper_yrf = $key_stopper_yrf->end_time;
              $mesin_stopper_yrf = $key_stopper_yrf->mesin;
              $qty_stopper_yrf = $key_stopper_yrf->qty;
              $employee_id_stopper_yrf = $key_stopper_yrf->employee_id;
              $ng_name_stopper_yrf = $key_stopper_yrf->ng_name;
              $ng_count_stopper_yrf = $key_stopper_yrf->ng_count;
            }
          }

          if ($tag_head_yrf != "") {
            $molding_head_yrf = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            if (count($molding_head_yrf) > 0) {
              foreach ($molding_head_yrf as $key_head_yrf) {
                $molding_head_yrf = $key_head_yrf->part;
                $last_shot_before_head_yrf = $key_head_yrf->last_shot_pasang;
                $last_shot_injection_head_yrf = $key_head_yrf->last_shot_running;
                $start_molding_head_yrf = $key_head_yrf->start_time;
                $finish_molding_head_yrf = $key_head_yrf->end_time;
                $note_molding_head_yrf = $key_head_yrf->note;
                $pic_molding_head_yrf = $key_head_yrf->pic;
              }
            }else{
              $molding_head_yrf = null;
              $last_shot_before_head_yrf = null;
              $last_shot_injection_head_yrf = null;
              $start_molding_head_yrf = null;
              $finish_molding_head_yrf = null;
              $note_molding_head_yrf = null;
              $pic_molding_head_yrf = null;
            }
          }

          if ($tag_body_yrf != "") {
            $molding_body_yrf = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            if (count($molding_body_yrf) > 0) {
              foreach ($molding_body_yrf as $key_body_yrf) {
                $molding_body_yrf = $key_body_yrf->part;
                $last_shot_before_body_yrf = $key_body_yrf->last_shot_pasang;
                $last_shot_injection_body_yrf = $key_body_yrf->last_shot_running;
                $start_molding_body_yrf = $key_body_yrf->start_time;
                $finish_molding_body_yrf = $key_body_yrf->end_time;
                $note_molding_body_yrf = $key_body_yrf->note;
                $pic_molding_body_yrf = $key_body_yrf->pic;
              }
            }else{
              $molding_body_yrf = null;
              $last_shot_before_body_yrf = null;
              $last_shot_injection_body_yrf = null;
              $start_molding_body_yrf = null;
              $finish_molding_body_yrf = null;
              $note_molding_body_yrf = null;
              $pic_molding_body_yrf = null;
            }
          }

          if ($tag_stopper_yrf != "") {
            $molding_stopper_yrf = DB::SELECT("SELECT
                  injection_history_molding_logs.pic,
                  injection_history_molding_logs.part,
                  injection_history_molding_logs.total_shot/injection_molding_masters.qty_shot AS last_shot_pasang,
                  injection_molding_logs.total_running_shot/injection_molding_masters.qty_shot AS last_shot_running,
                  injection_history_molding_logs.start_time,
                  injection_history_molding_logs.end_time,
                  injection_history_molding_logs.note
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_history_molding_logs ON injection_process_logs.molding = injection_history_molding_logs.part
                  LEFT JOIN employee_syncs opmesin ON opmesin.employee_id = injection_process_logs.operator_id 
                  LEFT JOIN injection_molding_masters ON injection_molding_masters.part = injection_history_molding_logs.part 
                  left join injection_molding_logs on injection_molding_logs.part = injection_process_logs.molding and injection_process_logs.created_at = injection_molding_logs.created_at
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."'  
                  AND injection_history_molding_logs.created_at <= injection_process_logs.start_time 
                  AND injection_history_molding_logs.type = 'PASANG' 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_history_molding_logs.created_at DESC,
                  injection_molding_logs.created_at DESC 
                  LIMIT 1");

            if (count($molding_stopper_yrf) > 0) {
              foreach ($molding_stopper_yrf as $key_stopper_yrf) {
                $molding_stopper_yrf = $key_stopper_yrf->part;
                $last_shot_before_stopper_yrf = $key_stopper_yrf->last_shot_pasang;
                $last_shot_injection_stopper_yrf = $key_stopper_yrf->last_shot_running;
                $start_molding_stopper_yrf = $key_stopper_yrf->start_time;
                $finish_molding_stopper_yrf = $key_stopper_yrf->end_time;
                $note_molding_stopper_yrf = $key_stopper_yrf->note;
                $pic_molding_stopper_yrf = $key_stopper_yrf->pic;
              }
            }else{
              $molding_stopper_yrf = null;
              $last_shot_before_stopper_yrf = null;
              $last_shot_injection_stopper_yrf = null;
              $start_molding_stopper_yrf = null;
              $finish_molding_stopper_yrf = null;
              $note_molding_stopper_yrf = null;
              $pic_molding_stopper_yrf = null;
            }
          }

          if ($tag_head_yrf != "") {
            $dryer_head_yrf = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            if (count($dryer_head_yrf) > 0) {
              foreach ($dryer_head_yrf as $key_head_yrf) {
                $material_resin_head_yrf = $key_head_yrf->material_number;
                $dryer_resin_head_yrf = $key_head_yrf->dryer;
                $lot_number_resin_head_yrf = $key_head_yrf->lot_number;
                $qty_resin_head_yrf = $key_head_yrf->qty;
                $create_resin_head_yrf = $key_head_yrf->created_at;
                $operator_resin_head_yrf = $key_head_yrf->employee_id;
              }
            }else{
              $material_resin_head_yrf = null;
              $dryer_resin_head_yrf = null;
              $lot_number_resin_head_yrf = null;
              $qty_resin_head_yrf = null;
              $create_resin_head_yrf = null;
              $operator_resin_head_yrf = null;
            }
          }

          if ($tag_body_yrf != "") {
            $dryer_body_yrf = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            if (count($dryer_body_yrf) > 0) {
              foreach ($dryer_body_yrf as $key_body_yrf) {
                $material_resin_body_yrf = $key_body_yrf->material_number;
                $dryer_resin_body_yrf = $key_body_yrf->dryer;
                $lot_number_resin_body_yrf = $key_body_yrf->lot_number;
                $qty_resin_body_yrf = $key_body_yrf->qty;
                $create_resin_body_yrf = $key_body_yrf->created_at;
                $operator_resin_body_yrf = $key_body_yrf->employee_id;
              }
            }else{
              $material_resin_body_yrf = null;
              $dryer_resin_body_yrf = null;
              $lot_number_resin_body_yrf = null;
              $qty_resin_body_yrf = null;
              $create_resin_body_yrf = null;
              $operator_resin_body_yrf = null;
            }
          }

          if ($tag_stopper_yrf != "") {
            $dryer_stopper_yrf = DB::SELECT("SELECT
                  injection_dryer_logs.material_number,
                  injection_dryer_logs.dryer,
                  injection_dryer_logs.qty,
                  injection_dryer_logs.lot_number,
                  injection_dryer_logs.created_at,
                  opresin.employee_id
              FROM
                  injection_tags
                  LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                  LEFT JOIN injection_dryer_logs ON injection_dryer_logs.lot_number = injection_process_logs.dryer_lot_number 
                  AND injection_dryer_logs.dryer = injection_process_logs.dryer
                  LEFT JOIN employee_syncs opresin ON opresin.employee_id = injection_dryer_logs.employee_id 
              WHERE
                  injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."' 
                  AND injection_dryer_logs.created_at <= injection_process_logs.start_time 
              ORDER BY
                  injection_process_logs.created_at DESC,
                  injection_dryer_logs.created_at DESC
                  LIMIT 1");

            if (count($dryer_stopper_yrf) > 0) {
              foreach ($dryer_stopper_yrf as $key_stopper_yrf) {
                $material_resin_stopper_yrf = $key_stopper_yrf->material_number;
                $dryer_resin_stopper_yrf = $key_stopper_yrf->dryer;
                $lot_number_resin_stopper_yrf = $key_stopper_yrf->lot_number;
                $qty_resin_stopper_yrf = $key_stopper_yrf->qty;
                $create_resin_stopper_yrf = $key_stopper_yrf->created_at;
                $operator_resin_stopper_yrf = $key_stopper_yrf->employee_id;
              }
            }else{
              $material_resin_stopper_yrf = null;
              $dryer_resin_stopper_yrf = null;
              $lot_number_resin_stopper_yrf = null;
              $qty_resin_stopper_yrf = null;
              $create_resin_stopper_yrf = null;
              $operator_resin_stopper_yrf = null;
            }
          }

          if ($tag_head_yrf != "") {
            $transaction_head_yrf = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_head_yrf')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_head_yrf = [];
            $location_transaction_head_yrf = [];
            $operator_transaction_head_yrf = [];
            $status_transaction_head_yrf = [];

            foreach ($transaction_head_yrf as $key_head_yrf) {
              $create_transaction_head_yrf[] = $key_head_yrf->created_at;
              $location_transaction_head_yrf[] = $key_head_yrf->location;
              $status_transaction_head_yrf[] = $key_head_yrf->status;
              $operator_transaction_head_yrf[] = $key_head_yrf->employee_id;
            }

            $create_transaction_head_yrfs = join('_',$create_transaction_head_yrf);
            $location_transaction_head_yrfs = join('_',$location_transaction_head_yrf);
            $operator_transaction_head_yrfs = join('_',$operator_transaction_head_yrf);
            $status_transaction_head_yrfs = join('_',$status_transaction_head_yrf);
          }

          if ($tag_body_yrf != "") {
            $transaction_body_yrf = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_body_yrf')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_body_yrf = [];
            $location_transaction_body_yrf = [];
            $operator_transaction_body_yrf = [];
            $status_transaction_body_yrf = [];

            foreach ($transaction_body_yrf as $key_body_yrf) {
              $create_transaction_body_yrf[] = $key_body_yrf->created_at;
              $location_transaction_body_yrf[] = $key_body_yrf->location;
              $status_transaction_body_yrf[] = $key_body_yrf->status;
              $operator_transaction_body_yrf[] = $key_body_yrf->employee_id;
            }

            $create_transaction_body_yrfs = join('_',$create_transaction_body_yrf);
            $location_transaction_body_yrfs = join('_',$location_transaction_body_yrf);
            $operator_transaction_body_yrfs = join('_',$operator_transaction_body_yrf);
            $status_transaction_body_yrfs = join('_',$status_transaction_body_yrf);
          }

          if ($tag_stopper_yrf != "") {
            $transaction_stopper_yrf = DB::SELECT("
              SELECT
                injection_transactions.location,
                injection_transactions.status,
                opinjeksi.employee_id,
                injection_transactions.created_at 
              FROM
                injection_tags
                LEFT JOIN injection_process_logs ON injection_process_logs.tag_product = injection_tags.tag
                LEFT JOIN injection_transactions ON injection_transactions.tag = injection_process_logs.tag_product
                LEFT JOIN employee_syncs opinjeksi ON opinjeksi.employee_id = injection_transactions.operator_id 
              WHERE
                injection_process_logs.tag_product = '".$request->get('tag_stopper_yrf')."' 
                AND injection_transactions.created_at >= injection_process_logs.end_time 
              ORDER BY
                injection_process_logs.created_at DESC,
                injection_transactions.created_at ASC 
                LIMIT 3");

            $create_transaction_stopper_yrf = [];
            $location_transaction_stopper_yrf = [];
            $operator_transaction_stopper_yrf = [];
            $status_transaction_stopper_yrf = [];

            foreach ($transaction_stopper_yrf as $key_stopper_yrf) {
              $create_transaction_stopper_yrf[] = $key_stopper_yrf->created_at;
              $location_transaction_stopper_yrf[] = $key_stopper_yrf->location;
              $status_transaction_stopper_yrf[] = $key_stopper_yrf->status;
              $operator_transaction_stopper_yrf[] = $key_stopper_yrf->employee_id;
            }

            $create_transaction_stopper_yrfs = join('_',$create_transaction_stopper_yrf);
            $location_transaction_stopper_yrfs = join('_',$location_transaction_stopper_yrf);
            $operator_transaction_stopper_yrfs = join('_',$operator_transaction_stopper_yrf);
            $status_transaction_stopper_yrfs = join('_',$status_transaction_stopper_yrf);
          }

          if ($tag_head_yrf != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_head_yrf.'_'.$material_number_head_yrf.'_'.$product.'_'.$no_kanban_head_yrf.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_head_yrf,
              'part_name' => $part_name_head_yrf,
              'part_type' => $part_type_head_yrf,
              'color' => $color_head_yrf,
              'cavity' => $cavity_head_yrf,
              'location' => $location_head_yrf,
              'tag' => $tag_head_yrf,
              'no_kanban_injection' => $no_kanban_head_yrf,
              'start_injection' => $start_time_head_yrf,
              'finish_injection' => $end_time_head_yrf,
              'mesin_injection' => $mesin_head_yrf,
              'qty_injection' => $qty_head_yrf,
              'operator_injection' => $employee_id_head_yrf,
              'molding' => $molding_head_yrf,
              'last_shot_before' => $last_shot_before_head_yrf,
              'last_shot_injection' => $last_shot_injection_head_yrf,
              'start_molding' => $start_molding_head_yrf,
              'finish_molding' => $finish_molding_head_yrf,
              'note_molding' => $note_molding_head_yrf,
              'operator_molding' => $pic_molding_head_yrf,
              'material_resin' => $material_resin_head_yrf,
              'dryer_resin' => $dryer_resin_head_yrf,
              'lot_number_resin' => $lot_number_resin_head_yrf,
              'qty_resin' => $qty_resin_head_yrf,
              'create_resin' => $create_resin_head_yrf,
              'operator_resin' => $operator_resin_head_yrf,
              'ng_name' => $ng_name_head_yrf,
              'ng_count' => $ng_count_head_yrf,
              'create_transaction' => $create_transaction_head_yrfs,
              'location_transaction' => $location_transaction_head_yrfs,
              'operator_transaction' => $operator_transaction_head_yrfs,
              'status_transaction' => $status_transaction_head_yrfs,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldheadyrf[0]->tag.'_'.$kensaoldheadyrf[0]->material_number.'_'.$product.'_'.$kensaoldheadyrf[0]->no_kanban_injection.'_'.$now,
              'product' => $product,
              'material_number' => $kensaoldheadyrf[0]->material_number,
              'part_name' => $kensaoldheadyrf[0]->part_name,
              'part_type' => $kensaoldheadyrf[0]->part_type,
              'color' => $kensaoldheadyrf[0]->color,
              'cavity' => $kensaoldheadyrf[0]->cavity,
              'location' => $kensaoldheadyrf[0]->location,
              'tag' => $kensaoldheadyrf[0]->tag,
              'no_kanban_injection' => $kensaoldheadyrf[0]->no_kanban_injection,
              'start_injection' => $kensaoldheadyrf[0]->start_injection,
              'finish_injection' => $kensaoldheadyrf[0]->finish_injection,
              'mesin_injection' => $kensaoldheadyrf[0]->mesin_injection,
              'qty_injection' => $kensaoldheadyrf[0]->qty_injection,
              'operator_injection' => $kensaoldheadyrf[0]->operator_injection,
              'molding' => $kensaoldheadyrf[0]->molding,
              'last_shot_before' => $kensaoldheadyrf[0]->last_shot_before,
              'last_shot_injection' => $kensaoldheadyrf[0]->last_shot_injection,
              'start_molding' => $kensaoldheadyrf[0]->start_molding,
              'finish_molding' => $kensaoldheadyrf[0]->finish_molding,
              'note_molding' => $kensaoldheadyrf[0]->note_molding,
              'operator_molding' => $kensaoldheadyrf[0]->operator_molding,
              'material_resin' => $kensaoldheadyrf[0]->material_resin,
              'dryer_resin' => $kensaoldheadyrf[0]->dryer_resin,
              'lot_number_resin' => $kensaoldheadyrf[0]->lot_number_resin,
              'qty_resin' => $kensaoldheadyrf[0]->qty_resin,
              'create_resin' => $kensaoldheadyrf[0]->create_resin,
              'operator_resin' => $kensaoldheadyrf[0]->operator_resin,
              'ng_name' => $kensaoldheadyrf[0]->ng_name,
              'ng_count' => $kensaoldheadyrf[0]->ng_count,
              'create_transaction' => $kensaoldheadyrf[0]->create_transaction,
              'location_transaction' => $kensaoldheadyrf[0]->location_transaction,
              'operator_transaction' => $kensaoldheadyrf[0]->operator_transaction,
              'status_transaction' => $kensaoldheadyrf[0]->status_transaction,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }

          if ($tag_body_yrf != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_body_yrf.'_'.$material_number_body_yrf.'_'.$product.'_'.$no_kanban_body_yrf.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_body_yrf,
              'part_name' => $part_name_body_yrf,
              'part_type' => $part_type_body_yrf,
              'color' => $color_body_yrf,
              'cavity' => $cavity_body_yrf,
              'location' => $location_body_yrf,
              'tag' => $tag_body_yrf,
              'no_kanban_injection' => $no_kanban_body_yrf,
              'start_injection' => $start_time_body_yrf,
              'finish_injection' => $end_time_body_yrf,
              'mesin_injection' => $mesin_body_yrf,
              'qty_injection' => $qty_body_yrf,
              'operator_injection' => $employee_id_body_yrf,
              'molding' => $molding_body_yrf,
              'last_shot_before' => $last_shot_before_body_yrf,
              'last_shot_injection' => $last_shot_injection_body_yrf,
              'start_molding' => $start_molding_body_yrf,
              'finish_molding' => $finish_molding_body_yrf,
              'note_molding' => $note_molding_body_yrf,
              'operator_molding' => $pic_molding_body_yrf,
              'material_resin' => $material_resin_body_yrf,
              'dryer_resin' => $dryer_resin_body_yrf,
              'lot_number_resin' => $lot_number_resin_body_yrf,
              'qty_resin' => $qty_resin_body_yrf,
              'create_resin' => $create_resin_body_yrf,
              'operator_resin' => $operator_resin_body_yrf,
              'ng_name' => $ng_name_body_yrf,
              'ng_count' => $ng_count_body_yrf,
              'create_transaction' => $create_transaction_body_yrfs,
              'location_transaction' => $location_transaction_body_yrfs,
              'operator_transaction' => $operator_transaction_body_yrfs,
              'status_transaction' => $status_transaction_body_yrfs,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldbodyyrf[0]->tag.'_'.$kensaoldbodyyrf[0]->material_number.'_'.$product.'_'.$kensaoldbodyyrf[0]->no_kanban_injection.'_'.$now,
              'product' => $product,
              'material_number' => $kensaoldbodyyrf[0]->material_number,
              'part_name' => $kensaoldbodyyrf[0]->part_name,
              'part_type' => $kensaoldbodyyrf[0]->part_type,
              'color' => $kensaoldbodyyrf[0]->color,
              'cavity' => $kensaoldbodyyrf[0]->cavity,
              'location' => $kensaoldbodyyrf[0]->location,
              'tag' => $kensaoldbodyyrf[0]->tag,
              'no_kanban_injection' => $kensaoldbodyyrf[0]->no_kanban_injection,
              'start_injection' => $kensaoldbodyyrf[0]->start_injection,
              'finish_injection' => $kensaoldbodyyrf[0]->finish_injection,
              'mesin_injection' => $kensaoldbodyyrf[0]->mesin_injection,
              'qty_injection' => $kensaoldbodyyrf[0]->qty_injection,
              'operator_injection' => $kensaoldbodyyrf[0]->operator_injection,
              'molding' => $kensaoldbodyyrf[0]->molding,
              'last_shot_before' => $kensaoldbodyyrf[0]->last_shot_before,
              'last_shot_injection' => $kensaoldbodyyrf[0]->last_shot_injection,
              'start_molding' => $kensaoldbodyyrf[0]->start_molding,
              'finish_molding' => $kensaoldbodyyrf[0]->finish_molding,
              'note_molding' => $kensaoldbodyyrf[0]->note_molding,
              'operator_molding' => $kensaoldbodyyrf[0]->operator_molding,
              'material_resin' => $kensaoldbodyyrf[0]->material_resin,
              'dryer_resin' => $kensaoldbodyyrf[0]->dryer_resin,
              'lot_number_resin' => $kensaoldbodyyrf[0]->lot_number_resin,
              'qty_resin' => $kensaoldbodyyrf[0]->qty_resin,
              'create_resin' => $kensaoldbodyyrf[0]->create_resin,
              'operator_resin' => $kensaoldbodyyrf[0]->operator_resin,
              'ng_name' => $kensaoldbodyyrf[0]->ng_name,
              'ng_count' => $kensaoldbodyyrf[0]->ng_count,
              'create_transaction' => $kensaoldbodyyrf[0]->create_transaction,
              'location_transaction' => $kensaoldbodyyrf[0]->location_transaction,
              'operator_transaction' => $kensaoldbodyyrf[0]->operator_transaction,
              'status_transaction' => $kensaoldbodyyrf[0]->status_transaction,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }

          if ($tag_stopper_yrf != "") {
            RcAssyInitial::create([
              'initial_code' => $tag_stopper_yrf.'_'.$material_number_stopper_yrf.'_'.$product.'_'.$no_kanban_stopper_yrf.'_'.$now,
              'product' => $product,
              'material_number' => $material_number_stopper_yrf,
              'part_name' => $part_name_stopper_yrf,
              'part_type' => $part_type_stopper_yrf,
              'color' => $color_stopper_yrf,
              'cavity' => $cavity_stopper_yrf,
              'location' => $location_stopper_yrf,
              'tag' => $tag_stopper_yrf,
              'no_kanban_injection' => $no_kanban_stopper_yrf,
              'start_injection' => $start_time_stopper_yrf,
              'finish_injection' => $end_time_stopper_yrf,
              'mesin_injection' => $mesin_stopper_yrf,
              'qty_injection' => $qty_stopper_yrf,
              'operator_injection' => $employee_id_stopper_yrf,
              'molding' => $molding_stopper_yrf,
              'last_shot_before' => $last_shot_before_stopper_yrf,
              'last_shot_injection' => $last_shot_injection_stopper_yrf,
              'start_molding' => $start_molding_stopper_yrf,
              'finish_molding' => $finish_molding_stopper_yrf,
              'note_molding' => $note_molding_stopper_yrf,
              'operator_molding' => $pic_molding_stopper_yrf,
              'material_resin' => $material_resin_stopper_yrf,
              'dryer_resin' => $dryer_resin_stopper_yrf,
              'lot_number_resin' => $lot_number_resin_stopper_yrf,
              'qty_resin' => $qty_resin_stopper_yrf,
              'create_resin' => $create_resin_stopper_yrf,
              'operator_resin' => $operator_resin_stopper_yrf,
              'ng_name' => $ng_name_stopper_yrf,
              'ng_count' => $ng_count_stopper_yrf,
              'create_transaction' => $create_transaction_stopper_yrfs,
              'location_transaction' => $location_transaction_stopper_yrfs,
              'operator_transaction' => $operator_transaction_stopper_yrfs,
              'status_transaction' => $status_transaction_stopper_yrfs,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }else{
            RcAssyInitial::create([
              'initial_code' => $kensaoldstopperyrf[0]->tag.'_'.$kensaoldstopperyrf[0]->material_number.'_'.$product.'_'.$kensaoldstopperyrf[0]->no_kanban_injection.'_'.$now,
              'product' => $product,
              'material_number' => $kensaoldstopperyrf[0]->material_number,
              'part_name' => $kensaoldstopperyrf[0]->part_name,
              'part_type' => $kensaoldstopperyrf[0]->part_type,
              'color' => $kensaoldstopperyrf[0]->color,
              'cavity' => $kensaoldstopperyrf[0]->cavity,
              'location' => $kensaoldstopperyrf[0]->location,
              'tag' => $kensaoldstopperyrf[0]->tag,
              'no_kanban_injection' => $kensaoldstopperyrf[0]->no_kanban_injection,
              'start_injection' => $kensaoldstopperyrf[0]->start_injection,
              'finish_injection' => $kensaoldstopperyrf[0]->finish_injection,
              'mesin_injection' => $kensaoldstopperyrf[0]->mesin_injection,
              'qty_injection' => $kensaoldstopperyrf[0]->qty_injection,
              'operator_injection' => $kensaoldstopperyrf[0]->operator_injection,
              'molding' => $kensaoldstopperyrf[0]->molding,
              'last_shot_before' => $kensaoldstopperyrf[0]->last_shot_before,
              'last_shot_injection' => $kensaoldstopperyrf[0]->last_shot_injection,
              'start_molding' => $kensaoldstopperyrf[0]->start_molding,
              'finish_molding' => $kensaoldstopperyrf[0]->finish_molding,
              'note_molding' => $kensaoldstopperyrf[0]->note_molding,
              'operator_molding' => $kensaoldstopperyrf[0]->operator_molding,
              'material_resin' => $kensaoldstopperyrf[0]->material_resin,
              'dryer_resin' => $kensaoldstopperyrf[0]->dryer_resin,
              'lot_number_resin' => $kensaoldstopperyrf[0]->lot_number_resin,
              'qty_resin' => $kensaoldstopperyrf[0]->qty_resin,
              'create_resin' => $kensaoldstopperyrf[0]->create_resin,
              'operator_resin' => $kensaoldstopperyrf[0]->operator_resin,
              'ng_name' => $kensaoldstopperyrf[0]->ng_name,
              'ng_count' => $kensaoldstopperyrf[0]->ng_count,
              'create_transaction' => $kensaoldstopperyrf[0]->create_transaction,
              'location_transaction' => $kensaoldstopperyrf[0]->location_transaction,
              'operator_transaction' => $kensaoldstopperyrf[0]->operator_transaction,
              'status_transaction' => $kensaoldstopperyrf[0]->status_transaction,
              'line' => $request->get('line'),
              'status' => 'Open',
              'created_by' => Auth::id()
            ]);
          }
        }

        if (count($rckensa) > 0) {
          foreach ($rckensa as $key) {
            $kensaold = RcAssyInitial::where('id',$key->id)->where('line',$request->get('line'))->first();
            $kensaold->status = 'Close';
            $kensaold->save();
          }
        }
        $response = array(
              'status' => true,
              'product' => $request->get('product')
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

    public function inputKensaInitialProduct(Request $request)
    {
      try {
        $emp_code = DB::SELECT('SELECT * FROM employee_groups where employee_id = "'.$request->get('employee_id').'" and location = "rc-assy"');

        foreach ($emp_code as $key) {
          $group = $key->group;
        }

        $code_generator = CodeGenerator::where('note', '=', 'kakuning-rc')->first();
        $serial_number = $group.sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $code_generator->index = $code_generator->index+1;
        $code_generator->save();

        $rckensa = RcKensaInitial::where('status','Open')->where('operator_kensa',$request->get('employee_id'))->where('line',$request->get('line'))->get();

        if (count($rckensa) > 0) {
          foreach ($rckensa as $key) {
            $kensaold = RcKensaInitial::where('id',$key->id)->where('line',$request->get('line'))->first();
            $kensaold->status = 'Close';
            $kensaold->save();
          }
        }

        $operator_kensa = $request->get('employee_id');

        $kensainitial = RcAssyInitial::where('status','Open')->where('line',$request->get('line'))->get();
        foreach ($kensainitial as $key) {
          RcKensaInitial::create([
            'initial_code' => $key->initial_code,
            'kensa_initial_code' => $serial_number.'_'.date('Y-m-d H:i:s').'_'.$key->initial_code,
            'serial_number' => $serial_number,
            'operator_kensa' => $operator_kensa,
            'product' => $key->product,
            'material_number' => $key->material_number,
            'part_name' => $key->part_name,
            'part_type' => $key->part_type,
            'color' => $key->color,
            'cavity' => $key->cavity,
            'location' => $key->location,
            'tag' => $key->tag,
            'no_kanban_injection' => $key->no_kanban_injection,
            'start_injection' => $key->start_injection,
            'finish_injection' => $key->finish_injection,
            'mesin_injection' => $key->mesin_injection,
            'qty_injection' => $key->qty_injection,
            'operator_injection' => $key->operator_injection,
            'molding' => $key->molding,
            'last_shot_before' => $key->last_shot_before,
            'last_shot_injection' => $key->last_shot_injection,
            'start_molding' => $key->start_molding,
            'finish_molding' => $key->finish_molding,
            'note_molding' => $key->note_molding,
            'operator_molding' => $key->operator_molding,
            'material_resin' => $key->material_resin,
            'dryer_resin' => $key->dryer_resin,
            'lot_number_resin' => $key->lot_number_resin,
            'qty_resin' => $key->qty_resin,
            'create_resin' => $key->create_resin,
            'operator_resin' => $key->operator_resin,
            'ng_name' => $key->ng_name,
            'ng_count' => $key->ng_count,
            'qty_check' => '0',
            'qty_ng' => '0',
            'create_transaction' => $key->create_transaction,
            'location_transaction' => $key->location_transaction,
            'operator_transaction' => $key->operator_transaction,
            'status_transaction' => $key->status_transaction,
            'line' => $key->line,
            'tray' => $request->get('tray'),
            'status' => 'Open',
            'created_by' => Auth::id()
          ]);

          $product = $key->product;
        }
        $response = array(
          'status' => true,
          'kensa_code' => $serial_number,
          'kensainitial' => $kensainitial,
          'product' => $product
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

    public function indexCleanKanban()
    {
      return view('recorder.process.clean_kanban', array(
        'title' => 'Clean Kanban Recorder',
        'title_jp' => 'リコーダーかんばんのフォーマット'
      ))->with('page', 'Clean Kanban Recorder');
    }

    public function indexDisplayKensa($line)
    {
      return view('recorder.display.kensa')
      ->with('title', 'Display Kensa Kakuning Recorder Line '.$line)
      ->with('title_jp', 'リコーダー検査の画面')
      ->with('page', 'Display Kensa Kakuning Recorder Line '.$line)
      ->with('line', $line);
    }

    public function fetchDisplayKensa(Request $request)
    {
      try {
        $initial = DB::SELECT("SELECT
          rc_assy_initials.product,
          rc_assy_initials.part_type,
          rc_assy_initials.material_number,
          rc_assy_initials.tag,
          rc_assy_initials.color,
          rc_assy_initials.no_kanban_injection,
          rc_assy_initials.cavity,
          injection_tags.mat_desc 
        FROM
          rc_assy_initials
          LEFT JOIN injection_tags ON injection_tags.tag = rc_assy_initials.tag 
        WHERE
          `status` = 'Open'
          and `line` = '".$request->get('line')."'
        ORDER BY
          rc_assy_initials.id ASC");

        $kensa = DB::SELECT("SELECT DISTINCT
          ( rc_kensa_initials.serial_number ),
          employee_id,
          rc_kensa_initials.product,
          COALESCE((
          SELECT
            SUM(qty_box) 
          FROM
            rc_box_results 
          WHERE
            operator_kensa = rc_kensa_initials.operator_kensa 
            AND check_date = DATE(
            NOW())),0) AS qty_box,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.part_type, '' ) ORDER BY rc_kensa_initials.id ASC ) AS part_type,
          `name`,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.material_number, '' ) ORDER BY rc_kensa_initials.id ASC ) AS material_number,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.tag, '' ) ORDER BY rc_kensa_initials.id ASC ) AS tag,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.color, '' ) ORDER BY rc_kensa_initials.id ASC ) AS color,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.no_kanban_injection, '' ) ORDER BY rc_kensa_initials.id ASC ) AS no_kanban,
          GROUP_CONCAT( COALESCE ( rc_kensa_initials.cavity, '' ) ORDER BY rc_kensa_initials.id ASC ) AS cavity,
          GROUP_CONCAT( COALESCE ( injection_tags.mat_desc, '' ) ORDER BY rc_kensa_initials.id ASC ) AS mat_desc 
        FROM
          rc_kensa_initials
          LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensa_initials.operator_kensa
          LEFT JOIN injection_tags ON injection_tags.tag = rc_kensa_initials.tag 
        WHERE
          `status` = 'Open' 
          AND DATE( rc_kensa_initials.created_at ) = DATE(
          NOW()) 
          and `line` = '".$request->get('line')."'
        GROUP BY
          serial_number,
          employee_id,
          `name`,
          rc_kensa_initials.product,
          rc_kensa_initials.operator_kensa
        ORDER BY
          rc_kensa_initials.id DESC ");

        $response = array(
          'status' => true,
          'initial' => $initial,
          'kensa' => $kensa,
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

    public function indexNgRateKensa()
    {
      return view('recorder.display.ng_kensa')
      ->with('title', 'Display NG Recorder')
      ->with('title_jp', 'リコーダー不良率の画面')
      ->with('page', 'Display NG Recorder');
    }

    public function fetchNgKensa(Request $request)
    {
      try {
        if ($request->get('tanggal') == "") {
          $now = date('Y-m-d');
        }else{
          $now = $request->get('tanggal');
        }
        $resumes = DB::SELECT("SELECT
          rc_kensas.serial_number,
          rc_kensas.operator_kensa,
          employee_syncs.`name`,
          ng_name,
          ng_count,
          part_code,
          rc_kensas.created_at,
          rc_kensas.product,
          rc_kensas.material_number,
          injection_parts.part_name,
          rc_kensas.cavity 
        FROM
          rc_kensas
          LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number
          LEFT JOIN employee_syncs ON employee_syncs.employee_id = rc_kensas.operator_kensa 
        WHERE
          rc_kensas.serial_number NOT LIKE '%Z%' 
          AND DATE( rc_kensas.created_at ) = '".$now."' 
          AND ng_name IS NOT NULL 
          AND injection_parts.deleted_at IS NULL 
          AND injection_parts.remark = 'injection'");

        $dateTitle = date('d M Y',strtotime($now));

        $response = array(
          'status' => true,
          'resumes' => $resumes,
          'dateTitle' => $dateTitle
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

    public function indexNgTrend()
    {
      $ng_lists = NgList::where('location','kakuning')->where('remark','recorder')->get();
      return view('recorder.display.ng_trend')
      ->with('title', 'Display NG Trend Recorder')
      ->with('title_jp', 'リコーダー不良傾向の画面')
      ->with('page', 'Display NG Trend Recorder')
      ->with('now', date('Y-m-d'))
      ->with('ng_lists', $ng_lists);
    }

    public function fetchNgTrend(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-t')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-t')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }

        $week_date = DB::SELECT("SELECT
          week_date,
          DATE_FORMAT(week_date,'%e-%b') as date_title
        FROM
          weekly_calendars 
        WHERE
          week_date >= ".$first."
          AND week_date <= ".$last."
          AND remark != 'H'");

        $resumes = [];
        $resume_trend = [];

        if ($request->get('part') == '') {
          $resumepart = '';
          $resumepartdetail = '';
        }else{
          $resumepart = "AND injection_parts.part_code like '%".$request->get('part')."%'";
          $resumepartdetail = "AND part_type like '%".$request->get('part')."%'";
        }

        if ($request->get('ng_name') == '') {
          $resumengname = '';
          $resumengnamedetail = '';
        }else{
          $resumengname = "AND rc_kensas.ng_name LIKE '%".$request->get('ng_name')."%'";
          $resumengnamedetail = "AND rc_kensas.ng_name LIKE '%".$request->get('ng_name')."%'";
        }

        for ($i=0; $i < count($week_date); $i++) { 
          $resume = DB::SELECT("SELECT DISTINCT
            ( part_code ),
            '".$week_date[$i]->week_date."' AS week_date,
            (
            SELECT
              GROUP_CONCAT( ng_name ) 
            FROM
              rc_kensas
              LEFT JOIN injection_parts a ON a.gmc = rc_kensas.material_number 
            WHERE
              DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' 
              AND rc_kensas.ng_name IS NOT NULL 
              AND a.deleted_at IS NULL 
              AND a.remark = 'injection' 
              AND a.part_code = injection_parts.part_code 
              ".$resumengname."
            ) AS ng_name,
            (
            SELECT
              GROUP_CONCAT( ng_count ) 
            FROM
              rc_kensas
              LEFT JOIN injection_parts a ON a.gmc = rc_kensas.material_number 
            WHERE
              DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' 
              AND rc_kensas.ng_name IS NOT NULL 
              AND a.deleted_at IS NULL 
              AND a.remark = 'injection' 
              AND a.part_code = injection_parts.part_code 
              ".$resumengname."
            ) AS ng_count 
          FROM
            injection_parts 
          WHERE
            injection_parts.deleted_at IS NULL 
            AND injection_parts.remark = 'injection'
            ".$resumepart);

          array_push($resumes, $resume);

          $trend_detail = DB::SELECT("SELECT DISTINCT
            ( rc_kensa_initials.mesin_injection ) AS mesin,
            part_type,
            '".$week_date[$i]->week_date."' AS week_date,
            GROUP_CONCAT( CONCAT( rc_kensas.ng_name, '=', rc_kensas.ng_count ) SEPARATOR ';' ) ng_name,
            SUM_OF_LIST ( GROUP_CONCAT(rc_kensas.ng_count) ) AS ng_count,
            operator_molding,
            molding,
            rc_kensa_initials.product,
            rc_kensa_initials.part_name,
            rc_kensa_initials.lot_number_resin,
            empinjeksi.employee_id,
            empinjeksi.`name`,
            rc_kensa_initials.start_injection,
            rc_kensa_initials.dryer_resin
          FROM
            rc_kensas
            LEFT JOIN rc_kensa_initials ON rc_kensa_initials.kensa_initial_code = rc_kensas.kensa_initial_code
            LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = rc_kensas.operator_kensa
            LEFT JOIN employee_syncs empinjeksi ON empinjeksi.employee_id = rc_kensa_initials.operator_injection
            LEFT JOIN employee_syncs empresin ON empresin.employee_id = rc_kensa_initials.operator_resin 
          WHERE
            rc_kensas.ng_name IS NOT NULL 
            ".$resumengnamedetail."
            AND DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' 
            ".$resumepartdetail."
          GROUP BY
            mesin_injection,
            operator_molding,
            molding,
            part_type,
            rc_kensa_initials.product,
            rc_kensa_initials.part_name,
            rc_kensa_initials.lot_number_resin,
            empinjeksi.employee_id,
            empinjeksi.`name`,
            rc_kensa_initials.start_injection,
            rc_kensa_initials.dryer_resin
          ORDER BY
            ng_count,
            part_type");

          if (count($trend_detail) > 0) {
            for ($j=0; $j < count($trend_detail); $j++) { 
                  $menu = RcNgTrend::updateOrCreate(
                      [
                          'date' => $trend_detail[$j]->week_date,
                          'mesin' => $trend_detail[$j]->mesin,
                          'part' => $trend_detail[$j]->part_name.' '.$trend_detail[$j]->part_type,
                      ],
                      [
                          'date' => $trend_detail[$j]->week_date,
                          'mesin' => $trend_detail[$j]->mesin,
                          'dryer' => $trend_detail[$j]->dryer_resin,
                          'resin' => $trend_detail[$j]->lot_number_resin,
                          'molding' => $trend_detail[$j]->molding,
                          'person' => $trend_detail[$j]->operator_molding,
                          'person_injeksi' => $trend_detail[$j]->name.'_'.$trend_detail[$j]->start_injection,
                          'product' => $trend_detail[$j]->product,
                          'part' => $trend_detail[$j]->part_name.' '.$trend_detail[$j]->part_type,
                          'ng_name' => $trend_detail[$j]->ng_name,
                          'qty_ng' => $trend_detail[$j]->ng_count,
                          'created_by' => 1,
                      ]
                  );
                  $menu->save();
            }
          }

          array_push($resume_trend, $trend_detail);
        }

        $response = array(
          'status' => true,
          'resumes' => $resumes,
          'resume_trend' => $resume_trend,
          'week_date' => $week_date,
          'now' => date('Y-m-d'),
          'dateTitleFirst' => $dateTitleFirst,
          'dateTitleLast' => $dateTitleLast
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

    public function inputNgTrend(Request $request)
    {
      try {
        $mesin_arr_save = $request->get('mesin_arr_save');
        $resin_arr_save = $request->get('resin_arr_save');
        $dryer_arr_save = $request->get('dryer_arr_save');
        $molding_arr_save = $request->get('molding_arr_save');
        $person_arr_save = $request->get('person_arr_save');
        $person_injeksi_arr_save = $request->get('person_injeksi_arr_save');
        $product_arr_save = $request->get('product_arr_save');
        $part_arr_save = $request->get('part_arr_save');
        $ng_name_arr_save = $request->get('ng_name_arr_save');
        $date_arr_save = $request->get('date_arr_save');
        $qty_ng_arr_save = $request->get('qty_ng_arr_save');


        for ($i=0; $i < count($date_arr_save); $i++) { 
          $date = $date_arr_save[$i];

          $mesin = '';
          $dryer = '';
          $molding = '';
          $person = '';
          $product = '';
          $part = '';
          $ng_name = '';
          $resin = '';
          $person_injeksi = '';
          $qty_ng = 0;

          for ($j=0; $j < count($mesin_arr_save); $j++) { 
            if ($mesin_arr_save[$j]['date'] == $date_arr_save[$i]) {
              $mesin = $mesin_arr_save[$j]['item'];
            }
          }

          for ($k=0; $k < count($molding_arr_save); $k++) { 
            if ($molding_arr_save[$k]['date'] == $date_arr_save[$i]) {
              $molding = $molding_arr_save[$k]['item'];
            }
          }

          for ($l=0; $l < count($person_arr_save); $l++) { 
            if ($person_arr_save[$l]['date'] == $date_arr_save[$i]) {
              $person = $person_arr_save[$l]['item'];
            }
          }

          for ($m=0; $m < count($product_arr_save); $m++) { 
            if ($product_arr_save[$m]['date'] == $date_arr_save[$i]) {
              $product = $product_arr_save[$m]['item'];
            }
          }

          for ($o=0; $o < count($part_arr_save); $o++) { 
            if ($part_arr_save[$o]['date'] == $date_arr_save[$i]) {
              $part = $part_arr_save[$o]['item'];
            }
          }

          for ($q=0; $q < count($ng_name_arr_save); $q++) { 
            if ($ng_name_arr_save[$q]['date'] == $date_arr_save[$i]) {
              $ng_name = $ng_name_arr_save[$q]['item'];
            }
          }

          for ($r=0; $r < count($qty_ng_arr_save); $r++) { 
            if ($qty_ng_arr_save[$r]['date'] == $date_arr_save[$i]) {
              $qty_ng = $qty_ng_arr_save[$r]['item'];
            }
          }

          for ($s=0; $s < count($resin_arr_save); $s++) { 
            if ($resin_arr_save[$s]['date'] == $date_arr_save[$i]) {
              $resin = $resin_arr_save[$s]['item'];
            }
          }

          for ($u=0; $u < count($dryer_arr_save); $u++) { 
            if ($dryer_arr_save[$u]['date'] == $date_arr_save[$i]) {
              $dryer = $dryer_arr_save[$u]['item'];
            }
          }

          for ($t=0; $t < count($person_injeksi_arr_save); $t++) { 
            if ($person_injeksi_arr_save[$t]['date'] == $date_arr_save[$i]) {
              $person_injeksi = $person_injeksi_arr_save[$t]['item'];
            }
          }

          $menu = RcNgTrend::updateOrCreate(
              [
                  'date' => $date,
              ],
              [
                  'mesin' => $mesin,
                  'dryer' => $dryer,
                  'resin' => $resin,
                  'molding' => $molding,
                  'person' => $person,
                  'person_injeksi' => $person_injeksi,
                  'product' => $product,
                  'part' => $part,
                  'ng_name' => $ng_name,
                  'qty_ng' => $qty_ng,
                  'created_by' => 1,
              ]
          );
          $menu->save();

        }

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

    public function fetchDetailNgTrend(Request $request)
    {
      try {
        $name = $request->get('name');
        $type = explode(' ', $name);

        if (count($type) == 2) {
          if ($type[1] == 'Head') {
            $types = 'HJ';
          }else if ($type[1] == 'Middle') {
            $types = 'MJ';
          }if ($type[1] == 'Foot') {
            $types = 'FJ';
          }if ($type[1] == 'Block') {
            $types = 'BJ';
          }
        }else{
          if ($type[1] == 'Head') {
            $types = 'A YRF H';
          }else if ($type[1] == 'Body') {
            $types = 'A YRF B';
          }if ($type[1] == 'Stopper') {
            $types = 'A YRF S';
          }
        }

        if ($request->get('ng_name') != '') {
          $ng_name = "AND rc_kensas.ng_name LIKE '%".$request->get('ng_name')."%'";
        }else{
          $ng_name = "";
        }

        $details = DB::SELECT("SELECT
          rc_kensa_initials.mesin_injection AS mesin,
          '".$request->get('date')."' AS week_date,
          rc_kensas.ng_name,
          rc_kensas.ng_count,
          operator_molding,
          rc_kensas.product,
          rc_kensa_initials.color,
          rc_kensa_initials.cavity,
          molding,
          rc_kensas.operator_kensa,
          empkensa.`name` AS kensa_name,
          operator_injection,
          empinjeksi.`name` AS injeksi_name,
          rc_kensas.created_at,
          rc_kensa_initials.material_resin,
          rc_kensa_initials.lot_number_resin,
          rc_kensa_initials.dryer_resin,
          rc_kensa_initials.part_type,
          rc_kensa_initials.material_number,
          injection_parts.part_name 
        FROM
          rc_kensas
          LEFT JOIN rc_kensa_initials ON rc_kensa_initials.kensa_initial_code = rc_kensas.kensa_initial_code
          LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensa_initials.material_number
          LEFT JOIN employee_syncs empkensa ON empkensa.employee_id = rc_kensas.operator_kensa
          LEFT JOIN employee_syncs empinjeksi ON empinjeksi.employee_id = rc_kensa_initials.operator_injection
          LEFT JOIN employee_syncs empresin ON empresin.employee_id = rc_kensa_initials.operator_resin 
        WHERE
          rc_kensas.ng_name IS NOT NULL 
          ".$ng_name."
          AND DATE( rc_kensas.created_at ) = '".$request->get('date')."' 
          AND rc_kensa_initials.part_type like '%".$types."%' 
          AND injection_parts.deleted_at IS NULL 
          AND injection_parts.remark = 'injection'
        ORDER BY
          rc_kensas.material_number");

        $dateTitle = date('d M Y',strtotime($request->get('date')));

        $response = array(
          'status' => true,
          'details' => $details,
          'dateTitle' => $dateTitle,
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


    public function indexNgRate()
    {
      return view('recorder.display.ng_rate')
      ->with('title', 'Display NG Rate Recorder')
      ->with('title_jp', 'リコーダー不良傾向の画面')
      ->with('page', 'Display NG Rate Recorder')
      ->with('now', date('Y-m-d'));
    }

    public function fetchNgRate(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }

        $week_date = DB::SELECT("SELECT
            week_date,
            fiscal_year,
            remark
          FROM
            weekly_calendars 
          WHERE
            week_date <= ".$last." AND week_date >= ".$first."
            and remark != 'H'
            and week_date >= '2021-09-16'");

        $first_fy = DB::SELECT("SELECT
          IF
            (( SELECT fiscal_year FROM weekly_calendars WHERE week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR )) = 'FY198', '2022-03-01', week_date ) AS week_date 
          FROM
            weekly_calendars 
          WHERE
            fiscal_year = (
            SELECT
              fiscal_year 
            FROM
              weekly_calendars 
            WHERE
            week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR )) 
          ORDER BY
            week_date 
            LIMIT 1");

        $last_fy = DB::SELECT("SELECT
          week_date 
        FROM
          weekly_calendars 
        WHERE
          fiscal_year = (
          SELECT
            fiscal_year 
          FROM
            weekly_calendars 
          WHERE
          week_date = DATE_ADD( DATE( NOW()), INTERVAL - 1 YEAR )) 
        ORDER BY
          week_date DESC
          LIMIT 1");

        $ng_target = DB::SELECT("SELECT COALESCE
          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
        FROM
          (
          SELECT
            date AS week_date,
            sum( ng_head ) + sum( ng_foot )+ sum( ng_middle )+ sum( ng_block ) AS qty_ng,
            0 AS qty_box 
          FROM
            rc_ng_boxes 
          WHERE
            date >= '".$first_fy[0]->week_date."' 
            AND date <= '".$last_fy[0]->week_date."' 
          GROUP BY
            date UNION ALL
          SELECT
            DATE( created_at ) AS week_date,
            0 AS qty_ng,
          IF
            ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
          FROM
            rc_box_results
            JOIN (
            SELECT
              operator_kensa AS employee_id,
              tray 
            FROM
              rc_kensas 
            WHERE
              DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' 
          GROUP BY
            DATE( created_at ),
          rc_box_results.product 
          ) a");

        $ng_target_head = DB::SELECT("SELECT COALESCE
          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
        FROM
          (
          SELECT
            date AS week_date,
            sum( ng_head ) AS qty_ng,
            0 AS qty_box 
          FROM
            rc_ng_boxes 
          WHERE
            date >= '".$first_fy[0]->week_date."' 
            AND date <= '".$last_fy[0]->week_date."' 
          GROUP BY
            date UNION ALL
          SELECT
            DATE( created_at ) AS week_date,
            0 AS qty_ng,
          IF
            ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
          FROM
            rc_box_results
            JOIN (
            SELECT
              operator_kensa AS employee_id,
              tray 
            FROM
              rc_kensas 
            WHERE
              DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' 
          GROUP BY
            DATE( created_at ),
          rc_box_results.product 
          ) a");

        $ng_target_middle = DB::SELECT("SELECT COALESCE
          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
        FROM
          (
          SELECT
            date AS week_date,
            sum( ng_middle ) AS qty_ng,
            0 AS qty_box 
          FROM
            rc_ng_boxes 
          WHERE
            date >= '".$first_fy[0]->week_date."' 
            AND date <= '".$last_fy[0]->week_date."' 
          GROUP BY
            date UNION ALL
          SELECT
            DATE( created_at ) AS week_date,
            0 AS qty_ng,
          IF
            ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
          FROM
            rc_box_results
            JOIN (
            SELECT
              operator_kensa AS employee_id,
              tray 
            FROM
              rc_kensas 
            WHERE
              DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' 
          GROUP BY
            DATE( created_at ),
          rc_box_results.product 
          ) a");

        $ng_target_foot = DB::SELECT("SELECT COALESCE
          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
        FROM
          (
          SELECT
            date AS week_date,
            sum( ng_foot ) AS qty_ng,
            0 AS qty_box 
          FROM
            rc_ng_boxes 
          WHERE
            date >= '".$first_fy[0]->week_date."' 
            AND date <= '".$last_fy[0]->week_date."' 
          GROUP BY
            date UNION ALL
          SELECT
            DATE( created_at ) AS week_date,
            0 AS qty_ng,
          IF
            ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
          FROM
            rc_box_results
            JOIN (
            SELECT
              operator_kensa AS employee_id,
              tray 
            FROM
              rc_kensas 
            WHERE
              DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' 
          GROUP BY
            DATE( created_at ),
          rc_box_results.product 
          ) a");

        $ng_target_block = DB::SELECT("SELECT COALESCE
          ( SUM( a.qty_ng ), 0 ) AS qty_ng,
          COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
        FROM
          (
          SELECT
            date AS week_date,
            sum( ng_block ) AS qty_ng,
            0 AS qty_box 
          FROM
            rc_ng_boxes 
          WHERE
            date >= '".$first_fy[0]->week_date."' 
            AND date <= '".$last_fy[0]->week_date."' 
          GROUP BY
            date UNION ALL
          SELECT
            DATE( created_at ) AS week_date,
            0 AS qty_ng,
          IF
            ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
          FROM
            rc_box_results
            JOIN (
            SELECT
              operator_kensa AS employee_id,
              tray 
            FROM
              rc_kensas 
            WHERE
              DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa, tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa WHERE DATE( created_at ) >= '".$first_fy[0]->week_date."' 
            AND DATE( created_at ) <= '".$last_fy[0]->week_date."' 
          GROUP BY
            DATE( created_at ),
          rc_box_results.product 
          ) a");

        $ng_rates = [];
        $ng_rates_head = [];
        $ng_rates_middle = [];
        $ng_rates_foot = [];
        $ng_rates_block = [];
        for($i = 0; $i < count($week_date);$i++){
          $interval = 1;
          if ($week_date[$i]->week_date <= '2021-11-19') {
            $ng_rate = DB::SELECT("
            SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box
            FROM
              (
              SELECT
                DATE( created_at ) AS week_date,
                SUM_OF_LIST ( GROUP_CONCAT(rc_kensas.ng_count) ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_kensas
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."'
              GROUP BY
                DATE( created_at ) UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
                IF
              ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM(qty_box) * 20, SUM(qty_box) * 50 ) AS qty_box 
              FROM
                rc_box_results 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
            GROUP BY
              DATE( created_at ),rc_box_results.product) a");
            $ng_rate_head = DB::SELECT("
              SELECT
                '".$week_date[$i]->week_date."' AS week_date,
                COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
              FROM
                (
                SELECT
                  DATE( rc_kensas.created_at ) AS week_date,
                  SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                  0 AS qty_box 
                FROM
                  rc_kensas
                  LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number 
                WHERE
                  ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'HJ' ) 
                    OR ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF H' ) 
                GROUP BY
                  DATE( rc_kensas.created_at ) UNION ALL
                SELECT
                  DATE( created_at ) AS week_date,
                  0 AS qty_ng,
                IF
                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
                FROM
                  rc_box_results 
                WHERE
                  DATE( created_at ) = '".$week_date[$i]->week_date."' 
                GROUP BY
                  DATE( created_at ),
                rc_box_results.product 
                ) a");

            $ng_rate_middle = DB::SELECT("
              SELECT
                '".$week_date[$i]->week_date."' AS week_date,
                COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
              FROM
                (
                SELECT
                  DATE( rc_kensas.created_at ) AS week_date,
                  SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                  0 AS qty_box 
                FROM
                  rc_kensas
                  LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number 
                WHERE
                  ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%MJ%' ) 
                    OR ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF B' ) 
                GROUP BY
                  DATE( rc_kensas.created_at ) UNION ALL
                SELECT
                  DATE( created_at ) AS week_date,
                  0 AS qty_ng,
                IF
                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
                FROM
                  rc_box_results 
                WHERE
                  DATE( created_at ) = '".$week_date[$i]->week_date."' 
                GROUP BY
                  DATE( created_at ),
                rc_box_results.product 
                ) a");

             $ng_rate_foot = DB::SELECT("
              SELECT
                '".$week_date[$i]->week_date."' AS week_date,
                COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
              FROM
                (
                SELECT
                  DATE( rc_kensas.created_at ) AS week_date,
                  SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                  0 AS qty_box 
                FROM
                  rc_kensas
                  LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number 
                WHERE
                  ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%FJ%' ) 
                GROUP BY
                  DATE( rc_kensas.created_at ) UNION ALL
                SELECT
                  DATE( created_at ) AS week_date,
                  0 AS qty_ng,
                IF
                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
                FROM
                  rc_box_results 
                WHERE
                  DATE( created_at ) = '".$week_date[$i]->week_date."' 
                GROUP BY
                  DATE( created_at ),
                rc_box_results.product 
                ) a");

             $ng_rate_block = DB::SELECT("
              SELECT
                '".$week_date[$i]->week_date."' AS week_date,
                COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
                COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
              FROM
                (
                SELECT
                  DATE( rc_kensas.created_at ) AS week_date,
                  SUM_OF_LIST ( GROUP_CONCAT( rc_kensas.ng_count ) ) AS qty_ng,
                  0 AS qty_box 
                FROM
                  rc_kensas
                  LEFT JOIN injection_parts ON injection_parts.gmc = rc_kensas.material_number 
                WHERE
                  ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code like '%BJ%' ) 
                  OR ( DATE( rc_kensas.created_at ) = '".$week_date[$i]->week_date."' AND injection_parts.remark = 'injection' AND injection_parts.deleted_at IS NULL AND injection_parts.part_code = 'A YRF S' )
                GROUP BY
                  DATE( rc_kensas.created_at ) UNION ALL
                SELECT
                  DATE( created_at ) AS week_date,
                  0 AS qty_ng,
                IF
                  ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
                FROM
                  rc_box_results 
                WHERE
                  DATE( created_at ) = '".$week_date[$i]->week_date."' 
                GROUP BY
                  DATE( created_at ),
                rc_box_results.product 
                ) a");
          }else if ($week_date[$i]->week_date >= '2021-11-20') {
            $ng_rate = DB::SELECT("SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
            FROM
              (
              SELECT
                date AS week_date,
                sum( ng_head ) + sum( ng_foot )+ sum( ng_middle )+ sum( ng_block ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_ng_boxes 
              WHERE
                date = '".$week_date[$i]->week_date."'
                GROUP BY date UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
              IF
                ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
              FROM
                rc_box_results
                JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '".$week_date[$i]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
              GROUP BY
                DATE( created_at ),
              rc_box_results.product 
              ) a");

            $ng_rate_head = DB::SELECT("SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
            FROM
              (
              SELECT
                date AS week_date,
                sum( ng_head ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_ng_boxes 
              WHERE
                date = '".$week_date[$i]->week_date."'
                GROUP BY date UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
              IF
                ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
              FROM
                rc_box_results
                JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '".$week_date[$i]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
              GROUP BY
                DATE( created_at ),
              rc_box_results.product 
              ) a");

            $ng_rate_middle = DB::SELECT("SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
            FROM
              (
              SELECT
                date AS week_date,
                sum( ng_middle ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_ng_boxes 
              WHERE
                date = '".$week_date[$i]->week_date."'
                GROUP BY date UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
              IF
                ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
              FROM
                rc_box_results
                JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '".$week_date[$i]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
              GROUP BY
                DATE( created_at ),
              rc_box_results.product 
              ) a");

            $ng_rate_foot = DB::SELECT("SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
            FROM
              (
              SELECT
                date AS week_date,
                sum( ng_foot ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_ng_boxes 
              WHERE
                date = '".$week_date[$i]->week_date."'
                GROUP BY date UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
              IF
                ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
              FROM
                rc_box_results
                JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '".$week_date[$i]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
              GROUP BY
                DATE( created_at ),
              rc_box_results.product 
              ) a");

            $ng_rate_block = DB::SELECT("SELECT
              '".$week_date[$i]->week_date."' AS week_date,
              COALESCE ( SUM( a.qty_ng ), 0 ) AS qty_ng,
              COALESCE ( sum( a.qty_box ), 0 ) AS qty_box 
            FROM
              (
              SELECT
                date AS week_date,
                sum( ng_block ) AS qty_ng,
                0 AS qty_box 
              FROM
                rc_ng_boxes 
              WHERE
                date = '".$week_date[$i]->week_date."'
                GROUP BY date UNION ALL
              SELECT
                DATE( created_at ) AS week_date,
                0 AS qty_ng,
              IF
                ( rc_box_results.product = 'YRS-27III //J' OR rc_box_results.product = 'YRS-28BIII //J', SUM( qty_box ) * 20, SUM( qty_box ) * 50 ) AS qty_box 
              FROM
                rc_box_results
                JOIN ( SELECT operator_kensa AS employee_id, tray FROM rc_kensas WHERE date( created_at ) = '".$week_date[$i]->week_date."' AND qty_ng != 0 GROUP BY operator_kensa,tray ) AS kensas ON kensas.employee_id = rc_box_results.operator_kensa 
              WHERE
                DATE( created_at ) = '".$week_date[$i]->week_date."' 
              GROUP BY
                DATE( created_at ),
              rc_box_results.product 
              ) a");
          }
          array_push( $ng_rates, $ng_rate);
          array_push( $ng_rates_head, $ng_rate_head);
          array_push( $ng_rates_middle, $ng_rate_middle);
          array_push( $ng_rates_foot, $ng_rate_foot);
          array_push( $ng_rates_block, $ng_rate_block);
        }


        $now = date('Y-m-d');

        $response = array(
            'status' => true,
            'ng_rate' => $ng_rates,
            'ng_rate_head' => $ng_rates_head,
            'ng_rate_foot' => $ng_rates_foot,
            'ng_rate_middle' => $ng_rates_middle,
            'ng_rate_block' => $ng_rates_block,
            'ng_target' => $ng_target,
            'ng_target_head' => $ng_target_head,
            'ng_target_middle' => $ng_target_middle,
            'ng_target_foot' => $ng_target_foot,
            'ng_target_block' => $ng_target_block,
            'dateTitleFirst' => $dateTitleFirst,
            'dateTitleLast' => $dateTitleLast,
            'now' => $now,
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

    public function indexQaAudit()
    {
      $product = DB::SELECT("SELECT
        * 
      FROM
        injection_parts 
      WHERE
        injection_parts.deleted_at IS NULL 
        AND remark = 'injection'");
      $ng_lists = NgList::where('location','kakuning')->where('remark','recorder')->get();
      return view('recorder.process.index_qa_audit')
      ->with('title', 'Audit QA')
      ->with('title_jp', '')
      ->with('page', 'Audit QA')
      ->with('ng_list', $ng_lists)
      ->with('product', $product)
      ->with('now', date('Y-m-d'));
    }

    public function fetchQaAudit(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }
        $audit = DB::SELECT("SELECT
          *,
          auditors.`name` AS auditor_name,
          auditees.`name` AS auditee_name,
          rc_qa_audits.created_at as created,
          DATE_FORMAT( date, '%d %M %Y' ) AS check_date
        FROM
          rc_qa_audits
          LEFT JOIN employee_syncs auditors ON auditors.employee_id = rc_qa_audits.auditor
          LEFT JOIN employee_syncs auditees ON auditees.employee_id = rc_qa_audits.auditor 
        WHERE
          date( rc_qa_audits.created_at ) >= ".$first." 
          AND date( rc_qa_audits.created_at ) <= ".$last."");
        $response = array(
            'status' => true,
            'audit' => $audit,
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

    public function scanQaAudit(Request $request)
    {
      try {
        $emp = Employee::where('tag',$request->get('employee_id'))->first();
        if ($emp != null) {
          $response = array(
            'status' => true,
            'message' => 'Scan Success',
            'emp' => $emp,
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

    public function inputQaAudit(Request $request)
    {
      try {
        $tujuan_upload = 'data_file/recorder/qa_audit';
        $file = $request->file('fileData');
        $filename = md5($request->input('auditor').date('YmdHisa')).'.'.$request->input('extension');
        $file->move($tujuan_upload,$filename);

        $date = $request->get('check_date');
        $product = $request->get('product');
        $auditor = $request->get('auditor');
        // $auditee = $request->get('auditee');
        $defect = $request->get('defect');
        $area = $request->get('area');
        $category = $request->get('category');
        $created_by = Auth::user()->id;

        $kensa_code = $request->get('kensa_code');

        $kensa = RcKensaInitial::where('serial_number',$kensa_code)->where('material_number',$product)->leftjoin('employee_syncs','employee_syncs.employee_id','rc_kensa_initials.operator_injection')->first();

        $kensa_auditee = RcKensaInitial::where('serial_number',$kensa_code)->where('material_number',$product)->leftjoin('employee_syncs','employee_syncs.employee_id','rc_kensa_initials.operator_kensa')->first();


        if ($kensa != null) {
          $audit = RcQaAudit::create([
            'date' => $date,
            'product' => $product,
            'auditor' => $auditor,
            'auditee' => $kensa_auditee->employee_id.' - '.$kensa_auditee->name,
            'kensa_code' => $kensa_code,
            'pic_injection' => $kensa->employee_id.' - '.$kensa->name,
            'defect' => $defect,
            'area' => $area,
            'category' => $category,
            'image' => $filename,
            'created_by' => $created_by,
          ]);

          $response = array(
            'status' => true,
            'message' => 'Input Success',
          );
          return Response::json($response);
        }else{
          $response = array(
            'status' => false,
            'message' => 'Kode Kensa Tidak Sesuai dengan Produk',
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

    public function indexDisplayQaAudit()
    {
      return view('recorder.display.qa_audit')
      ->with('title', 'RESUME AUDIT QA FINISH GOOD RECORDER')
      ->with('title_jp', '')
      ->with('page', 'RESUME AUDIT QA FINISH GOOD RECORDER')
      ->with('now', date('Y-m-d'));
    }

    public function fetchDisplayQaAudit(Request $request)
    {
      try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }
        $audit = DB::SELECT("SELECT
          *,
          auditors.`name` AS auditor_name,
          auditees.`name` AS auditee_name,
          rc_qa_audits.created_at as created,
          rc_qa_audits.id as id_audit,
          DATE_FORMAT( date, '%d %M %Y' ) AS check_date
        FROM
          rc_qa_audits
          LEFT JOIN employee_syncs auditors ON auditors.employee_id = rc_qa_audits.auditor
          LEFT JOIN employee_syncs auditees ON auditees.employee_id = rc_qa_audits.auditor 
          LEFT JOIN injection_parts ON injection_parts.gmc = rc_qa_audits.product 
        WHERE
          date( rc_qa_audits.created_at ) >= ".$first."
          AND date( rc_qa_audits.created_at ) <= ".$last."
          AND injection_parts.deleted_at IS NULL 
          AND injection_parts.remark = 'injection'
          ORDER BY
            date DESC");

        $response = array(
          'status' => true,
          'message' => 'Scan Success',
          'audit' => $audit,
          'dateTitleFirst' => $dateTitleFirst,
          'dateTitleLast' => $dateTitleLast,
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

    public function inputRecorderDocument(Request $request)
    {
      $id_user = Auth::id();
      $fy = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
      $training = TrainingReport::create([
          'activity_list_id' => 623,
          'department' => 'Educational Instrument (EI) Department',
          'section' => 'Recorder Proces',
          'product' => 'Recorder',
          'periode' => $fy->fiscal_year,
          'date' => date('Y-m-d'),
          'time' => '00:30:00',
          'trainer' => 'Rozaki',
          'training_title' => 'Training Audit QA Recorder',
          'theme' => 'Training Audit QA Recorder',
          'isi_training' => 'Training Audit QA Recorder',
          'tujuan' => 'Evaluasi Hasil Audit QA Recorder',
          'standard' => '-',
          'leader' => 'Rozaki',
          'foreman' => 'Eko Prasetyo Wicaksono',
          'notes' => '-',
          'remark' => date('Y-m-d H:i:s'),
          'created_by' => $id_user
      ]);
      $id = $training->id;

      return redirect('index/training_report/details/'.$id.'/recorder')
          ->with('page', 'Training Report')->with('status', 'Training Berhasil Dibuat.')->with('session_training','recorder');
    }

    public function inputNgBox(Request $request)
    {
      try {
        $tray = RcNgBox::where('date',date('Y-m-d'))->where('tray',$request->get('tray'))->first();
        if ($tray != null) {
          if ($request->get('ng_head') != 0) {
            $tray->ng_head = $tray->ng_head+1;
          }

          if ($request->get('ng_middle') != 0) {
            $tray->ng_middle = $tray->ng_middle+1;
          }

          if ($request->get('ng_foot') != 0) {
            $tray->ng_foot = $tray->ng_foot+1;
          }

          if ($request->get('ng_block') != 0) {
            $tray->ng_block = $tray->ng_block+1;
          }

          $tray->save();
        }else{
          $tray = RcNgBox::create([
            'date' => date('Y-m-d'),
            'tray' => $request->get('tray'),
            'ng_head' => $request->get('ng_head'),
            'ng_middle' => $request->get('ng_middle'),
            'ng_foot' => $request->get('ng_foot'),
            'ng_block' => $request->get('ng_block'),
            'created_by' => 1,
          ]);
        }

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

    public function fetchNGBox(Request $request)
    {
      try {
        $box = RcNgBox::where('date',date('Y-m-d'))->get();
        $response = array(
            'status' => true,
            'box' => $box,
        );
        return Response::json($response);
      } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function resetNGBox(Request $request)
    {
      try {
        $box = RcNgBox::where('date',date('Y-m-d'))->first();
        $box->ng_head = 0;
        $box->ng_middle = 0;
        $box->ng_foot = 0;
        $box->ng_block = 0;
        $box->save();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
      } catch (Exception $e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage(),
        );
        return Response::json($response);
      }
    }

    public function indexRcNgBox()
    {
      return view('recorder.report.report_ng_box')
      ->with('title', 'Recorder NG Box')
      ->with('title_jp', '')
      ->with('page', 'Recorder NG Box');
    }

    public function fetchRcNgBox(Request $request)
    {
      try {
        $ng_box = RcNgBox::orderby('date','desc')->get();
        $response = array(
            'status' => true,
            'ng_box' => $ng_box
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

    public function inputRecorderCounceling(Request $request)
    {
      try {
          $id_user = Auth::id();
          $audit =RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name')->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')->where('rc_qa_audits.id',$request->input('id_audit'))->first();
          $param = $request->get('param');
          if ($param == 'injeksi') {
            $audit->counceled_employee = $request->input('counceled_employee');
            $audit->counceled_at = date('Y-m-d H:i:s');
            $audit->car_description_inj = $request->get('description_inj');
            $audit->car_action_now_inj = $request->get('action_now_inj');
            $audit->car_cause_inj = $request->get('cause_inj');
            $audit->car_action_inj = $request->get('action_inj');
          }else{
            $audit->counceled_by = $request->input('counceled_by');
            $audit->counceled_at = date('Y-m-d H:i:s');
            $audit->car_description_rc = $request->get('description_rc');
            $audit->car_action_now_rc = $request->get('action_now_rc');
            $audit->car_cause_rc = $request->get('cause_rc');
            $audit->car_action_rc = $request->get('action_rc');
          }

          $audit->save();

          $audit =RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name')->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')->where('rc_qa_audits.id',$request->input('id_audit'))->first();

          $mail_to = [];
          $cc = [];

          $datas = array('data_email_rc' =>$audit,
          'remark' =>'chief', );

          array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');


          if ($param == 'assy') {
            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_assy'));
          }

          $datas = array('data_email_inj' =>$audit,
          'remark' =>'chief', );

          $mail_to = [];
          $cc = [];

          array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');


          if ($param == 'injeksi') {
            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_inj'));
          }

          $response = array(
              'status' => true,
              'message' => 'Konseling Berhasil'
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

    public function printQaAudit($type,$id)
    {
      $audit = RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name','users.name','users.username')
      ->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')
      ->join('users','rc_qa_audits.created_by','users.id')
      ->where('rc_qa_audits.id',$id)->first();

      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

      return view('recorder.display.print_qa_audit')
      ->with('type',$type)
      ->with('id',$id)
      ->with('audit',$audit)
      ->with('emp',$emp);
    }

    public function approveQaAudit($type,$remark,$id)
    {
      $audit =RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name')->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')->where('rc_qa_audits.id',$id)->first();

      if ($remark == 'chief') {
        if ($type == 'assy') {
          $mail_to = [];

          $datas = array('data_email_rc' =>$audit,
          'remark' =>'manager', );

          if ($audit->car_approver_id_rc == null) {
            $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
            $audit->car_approver_id_rc = Auth::user()->username;
            $audit->car_approver_name_rc = Auth::user()->name;
            $audit->car_approved_at_rc = date('Y-m-d H:i:s');

            Mail::to('imbang.prasetyo@music.yamaha.com')
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_assy'));
          }else{
            $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
          }
        }else{
          $mail_to = [];

          $datas = array('data_email_inj' =>$audit,
          'remark' =>'manager', );

          if ($audit->car_approver_id_inj == null) {
            $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
            $audit->car_approver_id_inj = Auth::user()->username;
            $audit->car_approver_name_inj = Auth::user()->name;
            $audit->car_approved_at_inj = date('Y-m-d H:i:s');

            Mail::to('imbang.prasetyo@music.yamaha.com')
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_inj'));
          }else{
            $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
          }
        }
      }

      if ($remark == 'manager') {
        if ($type == 'assy') {
          if ($audit->car_manager_id_rc == null) {
            $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
            $audit->car_manager_id_rc = Auth::user()->username;
            $audit->car_manager_name_rc = Auth::user()->name;
            $audit->car_approved_at_manager_rc = date('Y-m-d H:i:s');
          }else{
            $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
          }
        }else{
         if ($audit->car_manager_id_inj == null) {
            $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
            $audit->car_manager_id_inj = Auth::user()->username;
            $audit->car_manager_name_inj = Auth::user()->name;
            $audit->car_approved_at_manager_inj = date('Y-m-d H:i:s');
         }else{
          $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
         }
        }
      }

      $audit->save();

      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

      return redirect('/print/recorder/qa_audit/'.$type.'/'.$id)
      ->with('type',$type)
      ->with('id',$id)
      ->with('audit',$audit)
      ->with('status','Approve Berhasil')
      ->with('emp',$emp);
    }

    public function rejectQaAudit(Request $request,$id)
    {
      $type = $request->input('type');
      $reason = $request->input('reason');

      $audit = RcQaAudit::where('id',$id)->first();
      if ($type == 'assy') {
        $audit->reject_reason_rc = $reason;
        $audit->car_approver_id_rc = null;
        $audit->car_approver_name_rc = null;
        $audit->car_approved_at_rc = null;
      }else{
        $audit->reject_reason_inj = $reason;
        $audit->car_approver_id_inj = null;
        $audit->car_approver_name_inj = null;
        $audit->car_approved_at_inj = null;
      }
      $audit->save();

      $datas = array('data_email_rc' =>$audit,
      'remark' =>'chief',
      'reason' =>$reason, );

      if ($type == 'assy') {
        Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_rc_assy'));
      }

      $datas = array('data_email_inj' =>$audit,
      'remark' =>'chief',
      'reason' =>$reason, );

      if ($type == 'injeksi') {
        Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_rc_inj'));
      }

      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

      return redirect('/print/recorder/qa_audit/'.$type.'/'.$id)
      ->with('type',$type)
      ->with('id',$id)
      ->with('audit',$audit)
      ->with('status','Reject Berhasil')
      ->with('emp',$emp);
    }

    public function rejectQaAuditChief(Request $request,$id)
    {
      $type = $request->input('type_chief');
      $reason = $request->input('reason_chief');

      $audit = RcQaAudit::where('id',$id)->first();
      if ($type == 'assy') {
        $audit->reject_reason_rc = $reason;
        $audit->car_approver_id_rc = null;
        $audit->car_approver_name_rc = null;
        $audit->car_approved_at_rc = null;
        $audit->counceled_by = null;
        $audit->car_description_rc = null;
        $audit->car_action_now_rc = null;
        $audit->car_cause_rc = null;
        $audit->car_action_rc = null;
      }else{
        $audit->reject_reason_inj = $reason;
        $audit->car_approver_id_inj = null;
        $audit->car_approver_name_inj = null;
        $audit->car_approved_at_inj = null;
        $audit->counceled_employee = null;
        $audit->counceled_at = null;
        $audit->car_description_inj = null;
        $audit->car_action_now_inj = null;
        $audit->car_cause_inj = null;
        $audit->car_action_inj = null;
      }
      $audit->save();

      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

      return redirect('/print/recorder/qa_audit/'.$type.'/'.$id)
      ->with('type',$type)
      ->with('id',$id)
      ->with('audit',$audit)
      ->with('status','Reject Berhasil')
      ->with('emp',$emp);
    }

    public function updateRecorderCounceling(Request $request)
    {
      try {
          $id_user = Auth::id();
          $audit =RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name')->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')->where('rc_qa_audits.id',$request->input('id_audit'))->first();
          $param = $request->get('param');
          if ($param == 'injeksi') {
            $audit->car_approver_id_inj = Auth::user()->username;
            $audit->car_approver_name_inj = Auth::user()->name;
            $audit->car_approved_at_inj = date('Y-m-d H:i:s');
            $audit->car_description_inj = $request->get('description_inj');
            $audit->car_action_now_inj = $request->get('action_now_inj');
            $audit->car_cause_inj = $request->get('cause_inj');
            $audit->car_action_inj = $request->get('action_inj');
          }else{
            $audit->car_approver_id_rc = Auth::user()->username;
            $audit->car_approver_name_rc = Auth::user()->name;
            $audit->car_approved_at_rc = date('Y-m-d H:i:s');
            $audit->car_description_rc = $request->get('description_rc');
            $audit->car_action_now_rc = $request->get('action_now_rc');
            $audit->car_cause_rc = $request->get('cause_rc');
            $audit->car_action_rc = $request->get('action_rc');
          }

          $audit->save();

          $audit =RcQaAudit::select('rc_qa_audits.*','injection_parts.part_name')->join('injection_parts','injection_parts.gmc','rc_qa_audits.product')->where('rc_qa_audits.id',$request->input('id_audit'))->first();

          $mail_to = [];
          $cc = [];

          $datas = array('data_email_rc' =>$audit,
          'remark' =>'manager',
          'edit' => 'Edited' );

          array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');


          if ($param == 'assy') {
            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_assy'));
          }

          $datas = array('data_email_inj' =>$audit,
          'remark' =>'manager',
          'edit' => 'Edited' );

          $mail_to = [];
          $cc = [];

          array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');


          if ($param == 'injeksi') {
            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
            ->send(new SendEmail($datas, 'car_rc_inj'));
          }

          $response = array(
              'status' => true,
              'message' => 'Update Konseling Berhasil'
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

    public function indexDisplayTraceability()
    {
      return view('recorder.display.traceability')
      ->with('title', 'Recorder - Traceability')
      ->with('title_jp', 'リコーダー トレーサビリティ')
      ->with('page', 'Recorder - Assembly Process')
      ->with('head', 'リコーダー トレーサビリティ');
    }

    public function fetchDisplayTraceability(Request $request)
    {
      try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".date('Y-m-t')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
             }else{
                  $first = "'".date('Y-m-01')."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-01')));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-t')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-t')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }

        $existing = DB::SELECT("SELECT
          * 
        FROM
          `rc_ng_trends` 
        WHERE
          rc_ng_trends.date BETWEEN ".$first."
          AND ".$last."
        ORDER BY
          qty_ng DESC
          LIMIT 1");

        $parameter_existing = DB::SELECT("SELECT
          * 
        FROM
          push_block_parameters 
        WHERE
          check_date <= '".explode('_', $existing[0]->person_injeksi)[1]."'
          AND mesin = '#".explode(' ', $existing[0]->mesin)[1]."' 
        ORDER BY
          id DESC 
          LIMIT 1");

        $best = DB::SELECT("SELECT
          * 
        FROM
          `rc_ng_trends` 
        WHERE
          rc_ng_trends.date BETWEEN ".date('Y-m-d',strtotime(str_replace("'", "", $first).' - 1 MONTHS'))."
          AND ".$last."
          AND part = '".$existing[0]->part."'
          AND mesin = '".$existing[0]->mesin."'
        ORDER BY
          qty_ng ASC
          LIMIT 1");

        $parameter_best = DB::SELECT("SELECT
          * 
        FROM
          push_block_parameters 
        WHERE
          check_date <= '".explode('_', $best[0]->person_injeksi)[1]."'
          AND mesin = '#".explode(' ', $best[0]->mesin)[1]."' 
        ORDER BY
          id DESC 
          LIMIT 1");
        $response = array(
            'status' => true,
            'parameter_existing' => $parameter_existing,
            'parameter_best' => $parameter_best,
            'existing' => $existing,
            'best' => $best,
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

    public function indexDisplayParameter()
    {
      $parts = DB::select("SELECT DISTINCT
          (
          CONCAT( model, ' ', part_code )) AS part 
        FROM
          injection_parts 
        WHERE
          remark = 'injection' 
          AND deleted_at IS NULL 
        ORDER BY
          part");
      return view('recorder.display.big_data_parameter')
      ->with('title', 'Big Data Injection Parameter')
      ->with('title_jp', '成形条件のビッグデータ')
      ->with('page', 'Big Data Injection Parameter')
      ->with('head', 'Big Data Injection Parameter')
      ->with('mesin',$this->mesins)
      ->with('parts',$parts);
    }

    public function fetchDisplayParameter(Request $request)
    {
      try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        $machine = $request->get('machine');
        $part = $request->get('part');
        if ($date_from == "") {
             if ($date_to == "") {
                  $first = "'".date('Y-m-d',strtotime(date('Y-m-d').' - 30 days'))."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-d').' - 30 days'));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".date('Y-m-d',strtotime(date('Y-m-d').' - 30 days'))."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime(date('Y-m-d').' - 30 days'));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }else{
             if ($date_to == "") {
                  $first = "'".$date_from."'";
                  $last = "'".date('Y-m-d')."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime(date('Y-m-d')));
             }else{
                  $first = "'".$date_from."'";
                  $last = "'".$date_to."'";
                  $dateTitleFirst = date('d M Y',strtotime($date_from));
                  $dateTitleLast = date('d M Y',strtotime($date_to));
             }
        }

        $machine_filter = "";
        if ($machine != '') {
          $machine_filter = "AND mesin = '".$machine."'";
        }

        $part_filter = "";
        if ($part != '') {
          $part_filter = "AND part = '".$part."'";
        }

        $data_ng = DB::select("SELECT
          part,
          mesin,
          ".$first." AS `from`,
          ".$last." AS `to`,
          (
          SELECT
            CONCAT(
              a.qty_ng,
              '_',
              a.date,
              '_',
            SPLIT_STRING ( a.person_injeksi, '_', 2 )) 
          FROM
            rc_ng_trends a 
          WHERE
            a.part = rc_ng_trends.part 
            AND a.mesin = rc_ng_trends.mesin 
            AND a.date >= ".$first."
            AND a.date <= ".$last."
          ORDER BY
            a.qty_ng ASC 
            LIMIT 1 
          ) AS data_ng,
          (
          SELECT
            a.date 
          FROM
            rc_ng_trends a 
          WHERE
            a.part = rc_ng_trends.part 
            AND a.mesin = rc_ng_trends.mesin 
            AND a.date >= ".$first."
          AND a.date <= ".$last." ORDER BY a.qty_ng ASC LIMIT 1 ) AS check_date FROM rc_ng_trends WHERE date >= ".$first."
          AND date <= ".$last."
          ".$machine_filter."
          ".$part_filter."
        GROUP BY
          part,
          mesin 
        ORDER BY
          mesin");

        $parameters = [];
        $prod_result = [];
        for ($i=0; $i < count($data_ng); $i++) { 
          $param = DB::select("SELECT
            * 
          FROM
            `push_block_parameters` 
          WHERE
            check_date < '".explode('_', $data_ng[$i]->data_ng)[2]."' 
            AND mesin = '#".explode(' ', $data_ng[$i]->mesin)[1]."' 
          ORDER BY
            id DESC 
            LIMIT 1");
          array_push($parameters, $param);

          $prod = DB::select("SELECT
              '".$data_ng[$i]->check_date."' as check_date,
              '".$data_ng[$i]->part."' as part,
              SUM( b.qty ) AS qty 
            FROM
              (
              SELECT
              IF
                ( product = 'YRS-27III //J' OR product = 'YRS-28BIII //J', qty_check * 20, qty_check * 50 ) AS qty 
              FROM
                `ympimis`.`rc_kensas` 
              WHERE
              `created_at` LIKE '%".$data_ng[$i]->check_date."%' 
              AND `material_number` = ( SELECT gmc FROM injection_part_details WHERE injection_part_details.model = '".explode(' ', $data_ng[$i]->part)[0]."' AND injection_part_details.part_code = '".explode(' ', $data_ng[$i]->part)[1]."' ) GROUP BY serial_number,
            product,qty_check ) b");


          array_push($prod_result, $prod);
        }

        
        $response = array(
            'status' => true,
            'data_ng' => $data_ng,
            'parameters' => $parameters,
            'prod_result' => $prod_result,
            'dateTitleFirst' => $dateTitleFirst,
            'dateTitleLast' => $dateTitleLast,
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

    public function indexRcNgData(Request $request)
    {
      return view('recorder.report.ng_data')
      ->with('title', 'NG Rate Data')
      ->with('title_jp', '')
      ->with('page', 'NG Rate Data')
      ->with('head', 'NG Rate Data');
    }

    public function fetchRcNgData(Request $request)
    {
      try {
        $person_molding = DB::SELECT("SELECT
          a.date_injeksi,
          a.mesin,
          a.molding,
          SUM(a.ng_rate) as ng_rate,
          a.person
        FROM
          (
          SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ', ', 1 ) AS person 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person UNION ALL
          SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ', ', 2 ) AS person 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person UNION ALL
          SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ', ', 3 ) AS person 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person 
          ) a 
        WHERE
          a.person != '' 
        GROUP BY
          mesin,
          molding,
          person,
          date_injeksi");

        $resin = DB::SELECT("select a.date_injeksi,
          a.mesin,
          a.molding,
          SUM(a.ng_rate) as ng_rate,
          a.person,
          a.resin
          from
          (SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ',', 1 ) AS person,
            resin AS resin 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person,
            resin
            
            UNION ALL
            SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ', ', 2 ) AS person,
            resin AS resin 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person,
            resin
            
            UNION ALL
            SELECT
            GROUP_CONCAT(
    DISTINCT(DATE(
    SPLIT_STRING ( person_injeksi, '_', 2 )))) AS date_injeksi,
            mesin,
            molding,
            sum( qty_ng ) AS qty_ng_total,
            sum( a.quantity ) AS production,
            ROUND(( sum( qty_ng ) / sum( a.quantity )* 100 ), 2 ) AS ng_rate,
            SPLIT_STRING ( person, ', ', 3 ) AS person,
            resin AS resin 
          FROM
            rc_ng_trends
            JOIN (
            SELECT
              sum( quantity ) AS quantity,
              due_date 
            FROM
              production_schedules
              JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
              origin_group_code = 072 
            GROUP BY
              due_date 
            ) a ON a.due_date = date 
          GROUP BY
            mesin,
            molding,
            person,
            resin)
            a 
          WHERE
            a.person != '' 
          GROUP BY
            mesin,
            molding,
            resin,
            date_injeksi,
            person");

        $response = array(
            'status' => true,
            'resin' => $resin,
            'molding' => $person_molding,
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

    public function indexDisplayParameterNg()
    {
      return view('recorder.display.trend_parameter')
        ->with('title', 'Trend NG')
        ->with('title_jp', '')
        ->with('page', 'Trend NG')
        ->with('head', 'Trend NG');
    }

    public function fetchDisplayParameterNg(Request $request)
    {
      try {
        if ($request->get('date_from') != '' && $request->get('date_to') != '') {
          $where = "where injection_molding_logs.start_time >= '".$request->get('date_from')."' 
                    AND injection_molding_logs.start_time <= '".$request->get('date_to')."'";
          $where2 = "AND injection_molding_logs.start_time >= '".$request->get('date_from')."' 
                    AND injection_molding_logs.start_time <= '".$request->get('date_to')."'";
        }else{
          $where = '';
          $where2 = '';
        }
        $mesin = DB::SELECT("SELECT DISTINCT
        ( mesin ),
        part 
      FROM
        injection_molding_logs 
        ".$where."
      ORDER BY
        CAST( SPLIT_STRING ( mesin, ' ', 2 ) AS INT ),
        part");

        $ngs = [];

        for ($i=0; $i < count($mesin); $i++) { 
          // $ng = DB::SELECT("SELECT
          //   mesin,
          //   part,
          //   SUM( running_shot )/ 4 as shot,
          //   SUM(
          //   COALESCE ( SUM_OF_LIST ( ng_count ), 0 )) AS ng,
          //   COALESCE ((
          //   SELECT
          //     CONCAT( last_counter, '_', date( end_time ) ) 
          //   FROM
          //     injection_maintenance_molding_logs 
          //   WHERE
          //     injection_maintenance_molding_logs.part = injection_molding_logs.part 
          //     AND injection_maintenance_molding_logs.end_time < injection_molding_logs.start_time 
          //   ORDER BY
          //     id DESC 
          //     LIMIT 1 
          //     ),
          //   CONCAT(
          //     0,
          //     '_',
          //   date( start_time ))) AS maintenance 
          // FROM
          //   `injection_molding_logs` 
          // WHERE
          //   part = '".$mesin[$i]->part."'
          //   AND mesin = '".$mesin[$i]->mesin."'  
          //   AND running_shot != 0 
          // GROUP BY
          //   mesin,
          //   part,
          //   maintenance 
          // ORDER BY
          //   start_time");

          $ng = DB::SELECT("SELECT
            mesin,
            part,
            ROUND(running_shot / 4) as run_shot,
            ROUND(total_running_shot / (select qty_shot from injection_molding_masters where injection_molding_masters.part = injection_molding_logs.part)) as total_shot,
            start_time,
            DATE(start_time) as start,
            end_time,
            COALESCE ( SUM_OF_LIST ( ng_count ), 0 ) AS ng,
            (
            SELECT
              CONCAT( last_counter, '_', end_time ) 
            FROM
              injection_maintenance_molding_logs 
            WHERE
              injection_maintenance_molding_logs.part = injection_molding_logs.part 
              AND injection_maintenance_molding_logs.end_time < injection_molding_logs.end_time 
            ORDER BY
              id DESC 
              LIMIT 1 
            ) AS maintenance 
          FROM
            `injection_molding_logs` 
          WHERE
            part = '".$mesin[$i]->part."' 
            AND mesin = '".$mesin[$i]->mesin."' 
            ".$where2."
            AND running_shot != 0");

          array_push($ngs, $ng);
        }
        $response = array(
            'status' => true,
            'mesin' => $mesin,
            'ngs' => $ngs,
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

    public function indexDisplayNg()
    {
      $molding = DB::SELECT("SELECT DISTINCT
          ( molding ) 
        FROM
          rc_ng_trends 
        ORDER BY
          molding");

        $mesin = DB::SELECT("SELECT DISTINCT
          ( mesin ) 
        FROM
          rc_ng_trends 
        ORDER BY
          mesin");
      return view('recorder.display.trend_ng')
        ->with('title', 'Trend NG')
        ->with('title_jp', '')
        ->with('mesin', $mesin)
        ->with('molding', $molding)
        ->with('page', 'Trend NG')
        ->with('head', 'Trend NG');
    }

    public function fetchDisplayNg(Request $request)
    {
      try {
        $mesin = '';
        if ($request->get('mesin') != '') {
          $mesin = "WHERE mesin = '".$request->get('mesin')."'";
        }
        $part = DB::SELECT("SELECT DISTINCT
          (
          CONCAT( mesin, '_', part )) AS part_comb,
          GROUP_CONCAT(
          DISTINCT ( molding )) AS molding 
        FROM
          rc_ng_trends 
          ".$mesin."
        GROUP BY
          mesin,
          part");

        $date = DB::SELECT("SELECT DISTINCT
          ( week_date ) AS date 
        FROM
          weekly_calendars 
        WHERE
          week_date <= DATE( NOW()) AND week_date >= DATE( NOW() - INTERVAL 6 MONTH ) 
        ORDER BY
          week_date");

        $molding = DB::SELECT("SELECT DISTINCT
          ( molding ) 
        FROM
          rc_ng_trends 
        ORDER BY
          molding");

        $mesin = DB::SELECT("SELECT DISTINCT
          ( mesin ) 
        FROM
          rc_ng_trends 
          ".$mesin."
        ORDER BY
          mesin");

        $part_all = [];
        for ($i=0; $i < count($part); $i++) { 
          $parts = [];
          for ($j=0; $j < count($date); $j++) { 
            $molds = explode(',', $part[$i]->molding);
            for ($k=0; $k < count($molds); $k++) { 
              $gets = DB::SELECT("SELECT
                  COALESCE
                  ( SUM( qty_ng ), 0 ) AS ng
                FROM
                  rc_ng_trends 
                WHERE
                  mesin = '".explode('_', $part[$i]->part_comb)[0]."' 
                  AND part = '".explode('_', $part[$i]->part_comb)[1]."' 
                  AND date = '".$date[$j]->date."'
                  AND molding = '".$molds[$k]."'
                  ");
              array_push($parts, [
                'part' => $part[$i]->part_comb,
                'date' => $date[$j]->date,
                'molding' => $molds[$k],
                'ng' => $gets[0]->ng,
              ]);
            }
          }
          array_push($part_all, $parts);
        }

        
        $response = array(
            'status' =>true,
            'part_all' => $part_all,
            'part' => $part,
            'mesin' => $mesin,
            'date' => $date,
            'molding' => $molding,
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

    public function indexDisplayNgMesin()
    {
      return view('recorder.display.trend_ng_mesin')
        ->with('title', 'Trend NG')
        ->with('title_jp', '')
        ->with('page', 'Trend NG')
        ->with('head', 'Trend NG');
    }

    public function fetchDisplayNgMesin(Request $request)
    {
      try {

        $mesin = DB::SELECT("SELECT DISTINCT
          (mesin)
        FROM
          rc_ng_trends");

        $date = DB::SELECT("SELECT DISTINCT
          ( date ) 
        FROM
          rc_ng_trends 
        WHERE
          date <= DATE( NOW()) AND date >= DATE( NOW() - INTERVAL 6 MONTH ) 
        ORDER BY
          date");

        $molding = DB::SELECT("SELECT DISTINCT
        ( molding ),
        GROUP_CONCAT( DISTINCT ( mesin ) ) AS mesin 
      FROM
        rc_ng_trends 
        WHERE molding = '".$request->get('molding')."'
      GROUP BY
        molding 
      ORDER BY
        molding");

        $part_all = [];
        for ($i=0; $i < count($molding); $i++) { 
          $parts = [];
          for ($j=0; $j < count($date); $j++) { 
            $mesins = explode(',', $molding[$i]->mesin);
            for ($k=0; $k < count($mesins); $k++) { 
              $gets = DB::SELECT("SELECT
                  COALESCE
                  ( SUM( qty_ng ), 0 ) AS ng
                FROM
                  rc_ng_trends 
                WHERE
                  mesin = '".$mesins[$k]."' 
                  AND date = '".$date[$j]->date."'
                  AND molding = '".$molding[$i]->molding."'");
              array_push($parts, [
                'date' => $date[$j]->date,
                'mesin' => $mesins[$k],
                'molding' => $molding[$i]->molding,
                'ng' => $gets[0]->ng,
              ]);
            }
          }
          array_push($part_all, $parts);
        }

        
        $response = array(
            'status' =>true,
            'part_all' => $part_all,
            'mesin' => $mesin,
            'date' => $date,
            'molding' => $molding,
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
}
  