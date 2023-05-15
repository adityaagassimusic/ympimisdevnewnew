<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\PnPainica;
use App\PnCodePainica;
use App\PnOperator;
use App\HeaderBensuki;
use App\DetailBensuki;
use App\NgLogBensuki;
use App\Incoming;
use App\PnCodeOperator;
use Response;
use App\PnInventorie;
use App\PnLogProces;
use App\Material;
use App\PnLogNg;
use App\PnTag;
use App\PnOperatorLog;
use DataTables;
use Carbon\Carbon;
use App\SkillEmployee;
use App\SkillMap;
use App\EmployeeSync;
use App\Employee;
use App\NgList;
use App\WeeklyCalendar;
use App\TrainingReport;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use App\CodeGenerator;

use App\PnCaseLogNg;
use App\PnCaseLogProccess;

use App\LabelInformation;
use App\LabelEvidence;

class Pianica extends Controller{

    private $mesin;
    private $shift;
    public function __construct(){
      $this->mesin = [
          'H1',
          'H2',
          'H3',
          'M1',
          'M2',
          'M3', ];
          $this->shift = [
              'M',
              'B',
              'H',
          ];

          $this->model = [
              'P-25',
              'PS-25F',
              'P-32',
              'P-37',
          ];
          $this->line = [
              '1',
              '2',
              '3',
              '4',
              '5',
          ];
          $this->bagian = [
              'Bensuki',
              'Benage',
              'Pureto',
              'Tuning',
              'Kensa Awal',
              'Kensa Akhir',
              'Kakuning Visual',
              'Assembly',
              'Kakuning Case',
          ];

          $this->location = ['pn-assy-initial',
          'pn-assy-final'];
      }

      public function indexDailyNg(){
        $title = 'Daily NG Pianica';
        $title_jp = '??';

        return view('pianica.display.pn_daily_ng', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    public function indexNgRate(){
        $title = 'NG Rate by Operator';
        $title_jp = '??';

        return view('pianica.display.pn_ng_rate', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ));
    }

    // public function indexTrendsNgRate(){
    //     $title = 'Daily NG Rate by Operator';
    //     $title_jp = '??';

    //     return view('pianica.display.pn_ng_trends', array(
    //         'title' => $title,
    //         'title_jp' => $title_jp,
    //     ));
    // }

    public function assembly()
    {
        $models = $this->model;

        $query ="select ng_name, SUBSTRING_INDEX(location, 'Assembly_', -1) as location, id from ng_lists where location like 'PN_Assembly%' ";

        $ng_list =DB::select($query);

        return view('pianica.assembly',array(        
            'ng_list' => $ng_list,
        ))->with('page', 'Assembly');
    }

    public function index()
    {
        return view('pianica.index')->with('page', 'Bensuki')->with('location',$this->location);
    }


    public function bensuki()
    {
        $mesins = $this->mesin;
        $shifts = $this->shift;
        $models = $this->model;

        $low ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%LOW' and code.bagian='bensuki' ORDER BY code.kode asc";
        $lows = DB::select($low);

        $high ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%HIGH' and code.bagian='bensuki' ORDER BY code.kode asc";
        $highs = DB::select($high);

        $middle ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%MIDDLE' and code.bagian='bensuki' ORDER BY code.kode asc";
        $middles = DB::select($middle);

        $bennuki = "select op.nik, op.nama, code.kode from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.bagian='bennuki'";
        $bennukis = DB::select($bennuki);

        return view('pianica.bensuki3',array(
            'shifts' => $shifts,
            'mesins' => $mesins,
            'models' => $models,
            'lows' => $lows,
            'low' => $lows,
            'highs' => $highs,
            'high' => $highs,
            'middles' => $middles,
            'middle' => $middles,
            'bennukis' => $bennukis,        
        ))->with('page', 'Bentsuki');
    }


    public function pureto()
    {

        $models = $this->model;

        $low ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%LOW' and code.bagian='bensuki' ORDER BY code.kode asc";
        $lows = DB::select($low);

        $high ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%HIGH' and code.bagian='bensuki' ORDER BY code.kode asc";
        $highs = DB::select($high);

        $middle ="select DISTINCT (op.nik) as nik, op.nama, code.kode, SUBSTRING(code.kode,1,1) as warna from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.kode like '%MIDDLE' and code.bagian='bensuki' ORDER BY code.kode asc";
        $middles = DB::select($middle);

        $bennuki = "select op.nik, op.nama, code.kode from pn_operators as op
        LEFT JOIN pn_code_operators as code
        on op.nik = code.nik
        where code.bagian='bennuki'";
        $bennukis = DB::select($bennuki);

        return view('pianica.pureto2',array(        
            'models' => $models,
            'lows' => $lows,
            'title' => 'Detail Pureto',
            'title_jp' => 'プレート詳細',
            'low' => $lows,
            'highs' => $highs,
            'high' => $highs,
            'middles' => $middles,
            'middle' => $middles,
            'bennukis' => $bennukis,        
        ))->with('page', 'Pureto');
    }

    public function kensaawal()
    {

        $models = $this->model;

        $query ="SELECT ng_name, id from ng_lists where location ='PN_Kensa_Awal' ORDER BY ng_name asc";

        $ng_list =DB::select($query);

        return view('pianica.kensaawal',array(        
            'ng_list' => $ng_list,
        ))->with('page', 'Kensa Awal');
    }

    public function kensaakhir()
    {

        $models = $this->model;

        $query ="SELECT ng_name, id from ng_lists where location ='PN_Kensa_Akhir' ORDER BY ng_name asc";

        $ng_list =DB::select($query);

        return view('pianica.kensaakhir',array(        
            'ng_list' => $ng_list,
        ))->with('page', 'Kensa Akhir');
    }


    public function kakuningvisual()
    {

        $models = $this->model;

        $query ="select ng_name, SUBSTRING_INDEX(location, 'Visual_', -1) as location, id from ng_lists where location like 'PN_Kakuning_Visual%' ";

        $ng_list =DB::select($query);

        return view('pianica.kakuningvisual',array(        
            'ng_list' => $ng_list,
        ))->with('page', 'Kakuning Visual');
    }

    public function indexKakuningCase()
    {
        $models = $this->model;

        $ng_list = DB::select("SELECT ng_name, location, id, remark from ng_lists where location in ('Pianica-HardCase', 'Pianica-SoftCase') ");

        $date_code = db::select("SELECT date_code from weekly_calendars where week_date = DATE_FORMAT(now(),'%Y-%m-%d')");

        return view('pianica.case.kakuningvisual',array(        
            'ng_lists' => $ng_list,
            'date_code' => $date_code
        ))->with('page', 'Kakuning Visual');
    }

///-------------- operator 
    public function op()
    {
        $employees = EmployeeSync::get();
        $lines = $this->line;
        $bagians = $this->bagian;
        return view('pianica.op',array(        
            'lines' => $lines,
            'bagians' => $bagians,
            'employees' => $employees
        ))->with('page', 'Operator');
    }

    public function fillop($value='')
    {
        $op = "select * from pn_operators order by nama asc";
        $ops = DB::select($op);
        return DataTables::of($ops)

        ->addColumn('action', function($ops){
            return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-warning" onClick="editop(id)" id="' . $ops->id . '">Edit</a>&nbsp;<a href="javascript:void(0)" class="btn btn-danger" onClick="deleteop(id)" id="' . $ops->id . '" data-target="#modaldeleteOP" data-toggle="modal" title="Delete Operator">Delete</a>';
        })
        ->rawColumns(['action' => 'action'])
        ->make(true);
    }


    public function editop(Request $request)
    {
        $id_op = PnOperator::where('id', '=', $request->get('id'))->get();

        $response = array(
            'status' => true,
            'id_op' => $id_op,          
        );
        return Response::json($response);
    }

    public function updateop(Request $request){
        $id_user = Auth::id();

        try {  
            $op = PnOperator::where('id','=', $request->get('id'))       
            ->first(); 

            // $op->nama = $request->get('nama');
            // $op->nik = $request->get('nik');
            $op->tag = $request->get('tag');
            $op->line = $request->get('line');
            $op->bagian = $request->get('bagian');
            $op->created_by = $id_user;

            $op->save();

            $response = array(
              'status' => true,
              'message' => 'Update Success',
          );
            return redirect('/index/Op')->with('status', 'Update operator success')->with('page', 'Master Operator');
        }catch (QueryException $e){
            return redirect('/index/Op')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function addop(Request $request){
        $id_user = Auth::id();

        $dataemp = EmployeeSync::select('name')->where('employee_id','=',$request->get('nik'))->first();
        $datatag = Employee::select('tag')->where('employee_id','=',$request->get('nik'))->first();
        try { 
            $head = new PnOperator([
                'nik' => $request->get('nik'),
                'tag' => $datatag->tag,
                'nama' => $dataemp->name,
                'line' => $request->get('line'),
                'bagian' => $request->get('bagian'),
                'created_by' => $id_user
            ]);
            $head->save();



            $response = array(
              'status' => true,
              'message' => 'Add Operator Success',
          );
            return redirect('/index/Op')->with('status', 'Update operator success')->with('page', 'Master Operator');
        }catch (QueryException $e){
            return redirect('/index/Op')->with('error', $e->getMessage())->with('page', 'Master Operator');
        }

    }

    public function deleteop(Request $request)
    {
        try
        {
            $deletePN = PnOperator::where('id', '=', $request->get('id'))->delete();

            $response = array(
                'status' => true,
            );

            return Response::json($response);

        }
        catch(QueryException $e)
        {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );

            return Response::json($response);
        }

    }



    public function otokensa()
    {
     return view('pianica.otokensa')->with('page', 'Otokensa');
 }


//---------------------bensuki--------------

 public function input(Request $request)
 {
    $id_user = Auth::id();
    $id_head = HeaderBensuki::select('id')
    ->orderBy('id','desc')
    ->first();
    $id = $id_head->id+1;
    try {
        $ng = $request->get('ng');
        
        
        $head = new HeaderBensuki([
            'model' => $request->get('model'),
            'kode_op_bensuki' => $request->get('kodebensuki'),
            'nik_op_bensuki' => $request->get('nikbensuki'),
            'kode_op_plate' => $request->get('kodeplate'),
            'nik_op_plate' => $request->get('nikplate'),
            'shift' => $request->get('shift'),
            'mesin' =>  $request->get('mesin'),
            'line' =>  $request->get('line'),
            'created_by' => $id_user
        ]);
        $head->save();

        if ($ng) {
            foreach ($ng as $row) 
            {
                $detail = new DetailBensuki([
                    'kode_reed' => $row['reed'],
                    'posisi' => $request->get('posisi'),
                    'ng' =>  $row['ng'],
                    'qty' =>  $row['qty'],
                    'created_by' => $id_user,
                    'id_bensuki' => $head->id
                ]);
                $detail->save();            
            }
        }
        
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}

// public function input2(Request $request)
// {   
//     $id_user = Auth::id();
//     try {
//         $incoming = new Incoming([
//             'model' => $request->get('model'),
//             'qty' => $request->get('qty'),
//             'entry_date' => $request->get('entrydate'),
//             'created_by' => $id_user

//         ]);
//         $incoming->save();

//         $response = array(
//             'status' => true,
//             'invoice_number' => 'tes'
//         );
//         return Response::json($response);
//     }
//     catch(\Exception $e){
//         $response = array(
//             'status' => false,
//             'message' => $e->getMessage()
//         );
//         return Response::json($response);
//     }
// }

//--------------pureto -----------------------------

public function op_pureto(Request $request)
{  
    try {
        if (!str_contains(Auth::user()->username,'Line')) {
            if (str_contains($request->get('pureto'),'PI')) {
                $op_pureto = EmployeeSync::where('employee_id',$request->get('pureto'))->first();
                $response = array(
                    'status' => true,
                    'message' => 'Tag Ditemukan',
                    'nama' => $op_pureto->name,
                    'nik' => $op_pureto->employee_id,
                    'pesan_skill' => ''
                );
                return Response::json($response);
            }else{
                $op_pureto = PnOperator::where('tag', '=', $request->get('pureto'))
                // ->where('bagian','=' ,$request->get('op'))
                ->select('nik', 'nama', 'tag')
                ->first();
                $response = array(
                    'status' => true,
                    'message' => 'Tag Ditemukan',
                    'nama' => $op_pureto->nama,
                    'nik' => $op_pureto->nik,
                    'pesan_skill' => ''
                );
                return Response::json($response);
            }
        }


        if (str_contains($request->get('pureto'),'PI')) {
            $op_pureto = PnOperator::where('nik', '=', strtoupper($request->get('pureto')))
            // ->where('bagian','=' ,$request->get('op'))
            ->select('nik', 'nama', 'tag')
            ->first();
        }else{
            $op_pureto = PnOperator::where('tag', '=', $request->get('pureto'))
            // ->where('bagian','=' ,$request->get('op'))
            ->select('nik', 'nama', 'tag')
            ->first();
        }

        $employee_id = $op_pureto->nik;

        // $skillemp = SkillEmployee::where('employee_id',$employee_id)->first();
        $skillemp = DB::SELECT("SELECT
            GROUP_CONCAT( DISTINCT ( skills.process ) ) AS skill 
        FROM
            skill_maps
            JOIN skills ON skills.skill_code = skill_maps.skill_code 
        WHERE
            employee_id = '".$employee_id."'");

        if($op_pureto == null){
            $response = array(
                'status' => false,
                'message' => 'Tag Invalid',
            );
            return Response::json($response);
        }
        else{
            if ($request->get('op') == 'Kakuning Case') {
                if (str_contains($skillemp[0]->skill,'Prepare Case')) {
                    $response = array(
                        'status' => true,
                        'message' => 'Tag Ditemukan',
                        'nama' => $op_pureto->nama,
                        'nik' => $op_pureto->nik,
                        'pesan_skill' => ''
                    );
                    return Response::json($response);
                }else{
                    $response = array(
                        'status' => false,
                        'message' => 'Maaf, Anda tidak terdaftar di proses ini atau Skill anda belum terpenuhi untuk proses ini. Silahkan hubungi Leader untuk Upgrade Skill / Memindah Proses',
                        'pesan_skill' => 'Maaf, Anda tidak terdaftar di proses ini atau Skill anda belum terpenuhi untuk proses ini. Silahkan hubungi Leader untuk Upgrade Skill / Memindah Proses'
                    );
                    return Response::json($response);
                }
            }else{
                if (str_contains($skillemp[0]->skill,$request->get('op'))) {
                    $response = array(
                        'status' => true,
                        'message' => 'Tag Ditemukan',
                        'nama' => $op_pureto->nama,
                        'nik' => $op_pureto->nik,
                        'pesan_skill' => ''
                    );
                    return Response::json($response);
                }else{
                    $response = array(
                        'status' => false,
                        'message' => 'Maaf, Anda tidak terdaftar di proses ini atau Skill anda belum terpenuhi untuk proses ini. Silahkan hubungi Leader untuk Upgrade Skill / Memindah Proses',
                        'pesan_skill' => 'Maaf, Anda tidak terdaftar di proses ini atau Skill anda belum terpenuhi untuk proses ini. Silahkan hubungi Leader untuk Upgrade Skill / Memindah Proses'
                    );
                    return Response::json($response);
                }
            }
        }
    } catch (\Exception $e) {
       $response = array(
        'status' => false,
        'message' => $e->getMessage(),
        'pesan_skill' => ''
    );
       return Response::json($response);
   } 
}

public function savepureto(Request $request){
    $id_user = Auth::id();
    try {

        $date = date('Y-m-d');
        $prefix_now = 'PN'.date("y").date("m");
        $code_generator = CodeGenerator::where('note','=','pianica')->first();

        if ($prefix_now != $code_generator->prefix){
            $code_generator->prefix = $prefix_now;
            $code_generator->index = '0';
            $code_generator->save();
        }

        $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index+1);
        $order_no = $code_generator->prefix . $number;
        $code_generator->index = $code_generator->index+1;
        $code_generator->save();

        $inventori =  PnInventorie::updateOrCreate(
            [
                'tag' => $request->get('tag'),

            ],
            [
                'line' => $request->get('line'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'status' => '1',
                'created_by' => $request->get('pureto'),
            ]
        );

        $tag =  PnTag::updateOrCreate(['tag' => $request->get('tag')],
            [
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'position' => $request->get('location'),
                'form_id' => $order_no,
                'status' => 'Used',
                'created_by' => $request->get('pureto'),
            ]
        );

        if ($request->get('started_at') == '') {
            $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
        }else{
            $started_at = $request->get('started_at');
        }

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'operator' => $request->get('bensuki'),
            'tag' => $request->get('tag'),
            'model' => $request->get('model'),
            'started_at' => $started_at,
            'finished_at' => date('Y-m-d H:i:s'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'form_id' => $order_no,
            'created_by' => $request->get('pureto'),
        ]);

        $stock = DB::connection('ympimis_2')->table('pn_stocks')->where('model',$request->get('model'))->first();
        if ($stock) {
            $update_stock = DB::connection('ympimis_2')->table('pn_stocks')->where('model',$request->get('model'))->update([
                'quantity' => $stock->quantity-1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $log->save();
        $inventori->save();

        $response = array(
            'status' => true,
            'message' => 'Input Success'
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }

}

//------------ kensa awal ----------

public function savekensaawal(Request $request){

    $id_user = Auth::id();
    try {
        $inventori =  PnInventorie::updateOrCreate(
            [           
                'tag' => $request->get('tag'),            
            ],
            [
             'line' => $request->get('line'),
             'tag' => $request->get('tag'),
             'model' => $request->get('model'),
             'location' => $request->get('location'),
             'qty' => $request->get('qty'),
             'status' => '1',
             'created_by' => $request->get('op'),
         ]);

        $form_num = '';

        $form = PnTag::where('tag', $request->get('tag'))->select('form_id','position')->first();

        if($form) {
            $form_num = $form->form_id;
        }else{
            $log_process = PnLogProces::where('tag',$request->get('tag'))->where('form_id','!=',null)->orderby('id','desc')->first();
            if ($log_process) {
                $form_num = $log_process->form_id;
            }
        }

        $update_form = PnTag::where('tag', $request->get('tag'))->first();
        if ($update_form) {
            $update_form->position = 'PN_Kensa_Awal';
            $update_form->save();
        }else{
            $tag =  PnTag::insert(
                [
                    'tag' => $request->get('tag'),
                    'model' => $request->get('model'),
                    'position' => $request->get('location'),
                    'form_id' => $form_num,
                    'status' => 'Used',
                    'created_by' => $request->get('op'),
                ]
            );
        }

        if ($request->get('started_at') == '') {
            $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
        }else{
            $started_at = $request->get('started_at');
        }

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'operator' => $request->get('optuning')."#".$request->get('opfixing'),
            'form_id' => $form_num,
            'tag' => $request->get('tag'),
            'model' => $request->get('model'),
            'location' => $request->get('location'),
            'started_at' => $started_at,
            'finished_at' => date('Y-m-d H:i:s'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);

        $biri = $request->get('ngbiri');
        if($biri !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 101,                
                'line' => $request->get('line'),
                'operator' => $request->get('optuning')."#".$request->get('opfixing'),
                'form_id' => $form_num,
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngbiri'),
            ]);
            $detail->save();      
        }

        $oktaf = $request->get('ngoktaf');
        if($oktaf !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 102,                
                'line' => $request->get('line'),
                'operator' => $request->get('optuning')."#".$request->get('opfixing'),
                'form_id' => $form_num,
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngoktaf'),
            ]);
            $detail->save();      
        }

        $tinggi = $request->get('ngtinggi');
        if($tinggi !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 103,                
                'line' => $request->get('line'),
                'operator' => $request->get('optuning')."#".$request->get('opfixing'),
                'form_id' => $form_num,
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngtinggi'),
            ]);
            $detail->save();      
        }

        $rendah = $request->get('ngrendah');
        if($rendah !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 104,                
                'line' => $request->get('line'),
                'operator' => $request->get('optuning')."#".$request->get('opfixing'),
                'form_id' => $form_num,
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngrendah'),
            ]);
            $detail->save();      
        }

        $log->save();
        $inventori->save();

        $response = array(
            'status' => true,
            'message' => 'Input Success'
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }

}

// delete kakuning 

public function deleteInv(Request $request)
{
    $op = PnInventorie::where('tag','=', $request->get('tag')); 
    $op->forceDelete();
    $response = array(
        'status' => true,
        'message' => 'Delete Success'
    );
}

public function tag_model(Request $request)
{  
    $model = PnInventorie::where('tag', '=', $request->get('tag'))    
    ->select('model')
    ->first();

    $get_op_fixing_pl = db::select('SELECT
        operator,`name` as nama
        FROM
        pn_log_proces 
        join employee_syncs on employee_syncs.employee_id = operator
        WHERE
        tag = "'.$request->get('tag').'" 
        AND location = "PN_Pureto" 
        ORDER BY
        id DESC 
        LIMIT 1 ');
    // (SELECT operator from pn_tags left join (select form_id, operator from pn_log_proces where location = "PN_Pureto") pn_log_proces on pn_tags.form_id = pn_log_proces.form_id where pn_tags.tag = "0004220500") as data_op
    //     --         left join pn_operators on data_op.operator = pn_operators.nik

    if($model == null){
        $response = array(
            'status' => false,
            'message' => 'Tag not registered',
        );
        return Response::json($response);
    }
    else{
        $response = array(
            'status' => true,
            'message' => 'RFID found',
            'model' => $model->model,
            'op_fixing' => $get_op_fixing_pl,
            'started_at'=> date('Y-m-d H:i:s')

        );
        return Response::json($response);
    }
}


public function total_ng(Request $request)
{  
    $date = date('Y-m-d');
    $query ="
    SELECT ngs.ng_name, qty_ng as total from
    (select ng, sum(qty_ng) as qty_ng from
    (select form_id, ng, 1 as qty_ng from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' and line = '".$request->get('line')."'
    group by form_id, ng) ngs
    group by ng) as tot_ng
    left join
    (select id, ng_name from ng_lists where location ='".$request->get('location')."') ngs ON tot_ng.ng = ngs.id
    ";

    $query2 ="SELECT
    sum( total ) AS total,
    sum( total_ng ) AS ng,
    sum( total_pcs ) AS ng_pcs 
    FROM
    (
    SELECT
    sum( qty ) AS total,
    0 AS total_pcs,
    0 AS total_ng 
    FROM
    pn_log_proces 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m-%d' )= '".$date."' 
    AND location = '".$request->get('location')."' 
    AND line = '".$request->get('line')."' UNION ALL
    SELECT
    0 AS total,
    0 AS total_pcs,
    sum( qty_ng ) AS total_ng 
    FROM
    (
    SELECT
    form_id,
    ng,
    1 AS qty_ng 
    FROM
    pn_log_ngs 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m-%d' )= '".$date."' 
    AND location = '".$request->get('location')."' 
    AND line = '".$request->get('line')."' 
    GROUP BY
    form_id,
    ng 
    ) ngs UNION ALL
    SELECT
    0 AS total,
    sum( qty_ng ) AS total_pcs,
    0 AS total_ng 
    FROM
    (
    SELECT
    form_id,
    1 AS qty_ng 
    FROM
    pn_log_ngs 
    WHERE
    DATE_FORMAT( created_at, '%Y-%m-%d' )= '".$date."' 
    AND location = '".$request->get('location')."' 
    AND line = '".$request->get('line')."' 
    GROUP BY
    form_id 
    ) ngs 
) a";


$total_ng =DB::select($query);
$total =DB::select($query2);

$perolehan = null;

if ($request->get('type') == 'perolehan') {
    if ($request->get('employee_id') != '') {
        $perolehan = DB::SELECT("SELECT
            created_by,
            model,
            count(
            DISTINCT ( form_id )) AS qty,
            IF
            (
            model = 'P-25',
            count(
            DISTINCT ( form_id ))* 18,
            IF
            (
            model = 'P-32',
            count(
            DISTINCT ( form_id ))* 22,
            IF
            ( model = 'PS-25F', count( DISTINCT ( form_id ))* 20, count( DISTINCT ( form_id ))* 28 ))) AS screw 
            FROM
            pn_log_proces 
            WHERE
            location = 'PN_Pureto' 
            AND `line` = '".$request->get('line')."' 
            AND DATE( created_at ) = DATE(
            NOW()) 
            GROUP BY
            created_by,
            model");
    }
}

$response = array(
    'status' => true,
    'message' => 'NG Record found',
    'model' => $total_ng,
    'perolehan' => $perolehan,
    'total' => $total,

);
return Response::json($response);

}

public function total_ng_all(Request $request)
{  
    $date = date('Y-m-d');

    $query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' ) a";


    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);

}
public function GetNgBensuki(Request $request)
{
    $date = date('Y-m-d');

    $query2 ="select process_code as line, COALESCE(total,0) as total from ( 
    SELECT COUNT(model) as total, line from header_bensukis
    WHERE DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') ='".$date."' GROUP BY line
    )
    a
    RIGHT JOIN  
    (SELECT process_code from processes where remark ='pn') b
    on a.line=b.process_code";


    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);
}

public function GetNgBensukiAll(Request $request)
{
    $date = date('Y-m-d');

    $query2 ="SELECT COUNT(model) as total from header_bensukis WHERE DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') ='".$date."' ";

    $query3 ="SELECT sum(total) as total, sum(total_ng) as ng from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Pureto'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Pureto' ) a";

    $total =DB::select($query2);
    $totalAll =DB::select($query3);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,
        'totalAll' => $totalAll,

    );
    return Response::json($response);
}

public function total_ng_all_line(Request $request)
{  
    $date = date('Y-m-d');

    $query2 =" SELECT COALESCE(total,0) as total, COALESCE(ng,0) as ng , process_code from ( SELECT sum(total) as total, sum(total_ng) as ng , line from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng , line from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' GROUP BY tag,line
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng , line from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='".$request->get('location')."' GROUP BY tag,line ) a GROUP BY line ORDER BY line asc ) a
    RIGHT JOIN 
    (SELECT process_code from processes where remark ='pn') b

    on a.line=b.process_code  ORDER BY process_code asc
    ";


    $total =DB::select($query2);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'total' => $total,

    );
    return Response::json($response);

}


//--------------kensa akhir

public function savekensaakhir(Request $request){
    $id_user = Auth::id();
    try {        

        // $ng = $request->get('ng');
        // if($ng !=""){
        //     $rows = explode(",", $ng);
        //     foreach ($rows as $row) 
        //     {                          
        //         $detail = new PnLogNg([                
        //             'ng' => $row,                
        //             'line' => $request->get('line'),
        //             'operator' => $request->get('op'),
        //             'tag' => $request->get('tag'),
        //             'model' => $request->get('model'),
        //             'location' => $request->get('location'),
        //             'qty' => $request->get('qty'),
        //             'created_by' => $request->get('op'),
        //         ]);
        //         $detail->save(); 


        //     }

            // $inventori =  PnInventorie::updateOrCreate(
            //     [           
            //         'tag' => $request->get('tag'),            
            //     ],
            //     [
            //      'line' => $request->get('line'),
            //      'tag' => $request->get('tag'),
            //      'model' => $request->get('model'),
            //      'location' => $request->get('location'),
            //      'qty' => $request->get('qty'),
            //      'status' => '0',
            //      'created_by' => $request->get('op'),
            //  ]);

            // $log = new PnLogProces([
            //     'line' => $request->get('line'),
            //     'operator' => $request->get('op'),
            //     'tag' => $request->get('tag'),
            //     'model' => $request->get('model'),
            //     'location' => $request->get('location'),
            //     'qty' => $request->get('qty'),
            //     'created_by' => $request->get('op'),
            // ]);
            // $log->save();
            // $inventori->save(); 

        // }else{

        $form_num = '';

        $form = PnTag::where('tag', $request->get('tag'))->select('form_id','position')->first();

        if($form) {
            $form_num = $form->form_id;
        }else{
            $log_process = PnLogProces::where('tag',$request->get('tag'))->where('form_id','!=',null)->orderby('id','desc')->first();
            if ($log_process) {
                $form_num = $log_process->form_id;
            }
        }

        $update_form = PnTag::where('tag', $request->get('tag'))->first();
        if ($update_form) {
            $update_form->position = 'PN_Kensa_Akhir';
            $update_form->save();
        }else{
            $tag =  PnTag::insert(
                [
                    'tag' => $request->get('tag'),
                    'model' => $request->get('model'),
                    'position' => $request->get('location'),
                    'form_id' => $form_num,
                    'status' => 'Used',
                    'created_by' => $request->get('op'),
                ]
            );
        }

        $inventori =  PnInventorie::updateOrCreate(
            [           
                'tag' => $request->get('tag'),            
            ],
            [
             'line' => $request->get('line'),
             'tag' => $request->get('tag'),
             'model' => $request->get('model'),
             'location' => $request->get('location'),
             'qty' => $request->get('qty'),
             'status' => '1',
             'created_by' => $request->get('op'),
         ]);

        if ($request->get('started_at') == '') {
            $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
        }else{
            $started_at = $request->get('started_at');
        }

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'operator' => $request->get('op'),
            'form_id' => $form_num,
            'tag' => $request->get('tag'),
            'model' => $request->get('model'),
            'started_at' => $started_at,
            'finished_at' => date('Y-m-d H:i:s'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);

        $biri = $request->get('ngbiri');
        if($biri !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 105,                
                'line' => $request->get('line'),
                'form_id' => $form_num,
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngbiri'),
            ]);
            $detail->save();      
        }

        $oktaf = $request->get('ngoktaf');
        if($oktaf !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 106,                
                'line' => $request->get('line'),
                'form_id' => $form_num,
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngoktaf'),
            ]);
            $detail->save();      
        }

        $tinggi = $request->get('ngtinggi');
        if($tinggi !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 107,                
                'line' => $request->get('line'),
                'form_id' => $form_num,
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngtinggi'),
            ]);
            $detail->save();      
        }

        $rendah = $request->get('ngrendah');
        if($rendah !="A" ){                                      
            $detail = new PnLogNg([                
                'ng' => 108,                
                'line' => $request->get('line'),
                'form_id' => $form_num,
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
                'reed' => $request->get('ngrendah'),
            ]);
            $detail->save();      
        }

        
        $log->save();
        $inventori->save(); 
        // }



        $response = array(
            'status' => true,
            'message' => 'Input Success'
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }

}

//--------------kensa akhir

public function saveKakuningVisual(Request $request){
    $id_user = Auth::id();
    try {        

        $form_num = '';

        $form = PnTag::where('tag', $request->get('tag'))->select('form_id','position')->first();

        if($form) {
            $form_num = $form->form_id;
        }else{
            $log_process = PnLogProces::where('tag',$request->get('tag'))->where('form_id','!=',null)->orderby('id','desc')->first();
            if ($log_process) {
                $form_num = $log_process->form_id;
            }
        }

        // $update_form = PnTag::where('tag', $request->get('tag'))->first();
        // if ($update_form) {
        //     $update_form->position = 'PN_Kensa_Awal';
        //     $update_form->save();
        // }

        $ng = $request->get('ng');
        if($ng !=""){
            $rows = explode(",", $ng);
            foreach ($rows as $row) 
            {                          
                $detail = new PnLogNg([                
                    'ng' => $row,                
                    'line' => $request->get('line'),
                    'operator' => $request->get('op'),
                    'tag' => $request->get('tag'),
                    'form_id' => $form_num,
                    'model' => $request->get('model'),
                    'location' => $request->get('location'),
                    'qty' => $request->get('qty'),
                    'created_by' => $request->get('op'),
                ]);
                $detail->save(); 
                

            }

            $inventori =  PnInventorie::updateOrCreate(
                [           
                    'tag' => $request->get('tag'),            
                ],
                [
                   'line' => $request->get('line'),
                   'tag' => $request->get('tag'),
                   'model' => $request->get('model'),
                   'location' => $request->get('location'),
                   'qty' => $request->get('qty'),
                   'status' => '0',
                   'created_by' => $request->get('op'),
               ]);

            if ($request->get('started_at') == '') {
                $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
            }else{
                $started_at = $request->get('started_at');
            }

            $log = new PnLogProces([
                'line' => $request->get('line'),
                'operator' => $request->get('op'),
                'form_id' => $form_num,
                'tag' => $request->get('tag'),
                'started_at' => $started_at,
                'finished_at' => date('Y-m-d H:i:s'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
            ]);
            $log->save();
            $inventori->save(); 

        }else{
            $inventori =  PnInventorie::updateOrCreate(
                [           
                    'tag' => $request->get('tag'),            
                ],
                [
                   'line' => $request->get('line'),
                   'tag' => $request->get('tag'),
                   'model' => $request->get('model'),
                   'location' => $request->get('location'),
                   'qty' => $request->get('qty'),
                   'status' => '1',
                   'created_by' => $request->get('op'),
               ]);

            if ($request->get('started_at') == '') {
                $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
            }else{
                $started_at = $request->get('started_at');
            }

            $log = new PnLogProces([
                'line' => $request->get('line'),
                'form_id' => $form_num,
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'started_at' => $started_at,
                'finished_at' => date('Y-m-d H:i:s'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
            ]);
            $log->save();
            $inventori->save(); 
        }

        $tags = PnTag::where('tag', '=', $request->get("tag"))->first();
        if ($tags) {
            $delete = PnTag::where('tag', '=', $request->get("tag"))->forceDelete();
        }

        $three_man_label = LabelInformation::get();

        $response = array(
            'status' => true,
            'message' => 'Input Success',
            'three_man_label' => $three_man_label
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }

}


/// Laporan bensuki

public function reportBensuki()
{

    return view('pianica.reportBensuki')->with('page', 'Report Bentsuki');
}

public function getTotalNG(Request $request)
{



    $datep = $request->get('datep');
    $dateF = "";
    $dateL = "";

    if ($datep != "") {
        $date = $datep;
        $dateF = date('Y-m-01', strtotime("-1 months",strtotime($datep)));
        $dateL = date('Y-m-t', strtotime("-1 months",strtotime($datep)));
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
        $date = date('Y-m-d');
        $dateF = date('Y-m-01', strtotime("-1 months"));
        $dateL = date('Y-m-t', strtotime("-1 months"));
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query = "SELECT ngH,sum(totalH+totalL) as total from (
    SELECT ngH,count(b.ng) as totalL from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' ) b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc
    ) a
    LEFT JOIN (

    SELECT ngL,count(b.ng) as totalH from (
    select ng_name as ngL from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' ) b
    on a.ngL = b.ng 
    GROUP BY a.ngL ORDER BY ngL asc) b
    on a.ngH = b.ngL
    GROUP BY ngH";



    $query2="SELECT ngH,count(b.ng) as totalL from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."') b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc";



    $query3="SELECT ngH,count(b.ng) as totalH from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."') b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc";

    $queryTot = "SELECT ngH,sum(totalH+totalL) as total from (
    SELECT ngH,count(b.ng) as totalL from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$dateL."' ) b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc
    ) a
    LEFT JOIN (

    SELECT ngL,count(b.ng) as totalH from (
    select ng_name as ngL from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$dateL."' ) b
    on a.ngL = b.ng 
    GROUP BY a.ngL ORDER BY ngL asc) b
    on a.ngH = b.ngL
    GROUP BY ngH";

    $queryTotL="SELECT ngH,count(b.ng) as totalL from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='LOW' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$dateL."') b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc";

    $queryTotH="SELECT ngH,count(b.ng) as totalH from (
    select ng_name as ngH from ng_lists WHERE location='PN_Bensuki' 
    )a
    left JOIN  (
    SELECT ng from detail_bensukis WHERE posisi='HIGH' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$dateL."') b
    on a.ngH = b.ng 
    GROUP BY a.ngH ORDER BY ngH asc";

    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

    $querytgl="
    SELECT DISTINCT(DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d')) as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') <= '".$dateL."'
    ";

    $tgl2 =DB::select($tgl);

    $total =DB::select($query);
    $totalL =DB::select($query2);
    $totalH =DB::select($query3);

    $total2 =DB::select($queryTot);
    $totalL2 =DB::select($queryTotL);
    $totalH2 =DB::select($queryTotH);

    $tlgtot =DB::select($querytgl);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'ngL' => $totalL,
        'ngH' => $totalH,
        'tgl' => $tgl2,
        'ng2' => $total2,
        'ngL2' => $totalL2,
        'ngH2' => $totalH2,
        'tlgtot' => $tlgtot,

    );
    return Response::json($response);
}


public function getMesinNg(Request $request)
{

    $datep = $request->get('datep');
    $dateF = "";
    $dateL = "";

    if ($datep != "") {
        $date = $datep;
        $dateF = date('Y-m-01', strtotime("-1 months",strtotime($datep)));
        $dateL = date('Y-m-t', strtotime("-1 months",strtotime($datep)));
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
        $date = date('Y-m-d');
        $dateF = date('Y-m-01', strtotime("-1 months"));
        $dateL = date('Y-m-t', strtotime("-1 months"));
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }
    $query="SELECT ng_name as mesin, COALESCE(ng,0) as ng from (
    SELECT mesin, COUNT(ng) as ng  FROM header_bensukis
    LEFT JOIN detail_bensukis ON  header_bensukis.id = detail_bensukis.id_bensuki where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY mesin ORDER BY mesin asc )
    a RIGHT JOIN (
    SELECT ng_name from ng_lists WHERE location='PN_Bensuki_Mesin'
)b on a.mesin = b.ng_name ORDER BY mesin asc";
$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

$querym="        
SELECT ng_name as mesin, COALESCE(ng,0) as ng from (
SELECT mesin, COUNT(ng) as ng  FROM header_bensukis
LEFT JOIN detail_bensukis ON  header_bensukis.id = detail_bensukis.id_bensuki where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') <= '".$dateL."'
GROUP BY mesin ORDER BY mesin asc )
a RIGHT JOIN (
SELECT ng_name from ng_lists WHERE location='PN_Bensuki_Mesin'
)b on a.mesin = b.ng_name ORDER BY mesin asc
";

$querytgl="
SELECT DISTINCT(DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d')) as tgl from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') >= '".$dateF."' and DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') <= '".$dateL."'
";

$tgl2 =DB::select($tgl);
$total =DB::select($query);
$totalm =DB::select($querym);
$totaltgl =DB::select($querytgl);
$response = array(
    'status' => true,
    'message' => 'NG Record found',        
    'ng' => $total,
    'tgl' => $tgl2,
    'totalm' => $totalm,
    'totaltgl' => $totaltgl,
    'dateF' => $dateF,



);
return Response::json($response);
}

// ----------- display

public function display()
{

    return view('pianica.display')->with('page', 'Production Result');
}

public function getTarget(Request $request){

    $hpl = "where materials.category = 'FG' and materials.origin_group_code = '073'";


    $first = date('Y-m-01');
    if(date('Y-m-d') != date('Y-m-01')){
        $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }
    else{
        $last = date('Y-m-d');
    }
    $now = date('Y-m-d');

    if($first != $now){
        $debt = "union all

        select material_number, sum(debt) as debt, 0 as plan, 0 as actual from
        (
        select material_number, -(sum(quantity)) as debt from production_schedules where due_date >= '". $first ."' and due_date <= '". $last ."' group by material_number

        union all

        select material_number, sum(quantity) as debt from flo_details where date(created_at) >= '". $first ."' and date(created_at) <= '". $last ."' group by material_number
        ) as debt
        group by material_number";
    }
    else{
        $debt= "";
    }


    $query = "select result.material_number, materials.material_description as model, sum(result.debt) as debt, sum(result.plan) as plan, sum(result.actual) as actual from
    (
    select material_number, 0 as debt, sum(quantity) as plan, 0 as actual 
    from production_schedules 
    where due_date = '". $now ."' 
    group by material_number

    union all

    select material_number, 0 as debt, 0 as plan, sum(quantity) as actual 
    from flo_details 
    where date(created_at) = '". $now ."'  
    group by material_number

    ".$debt."

    ) as result
    left join materials on materials.material_number = result.material_number
    ". $hpl ."
    group by result.material_number, materials.material_description
    having sum(result.debt) <> 0 or sum(result.plan) <> 0 or sum(result.actual) <> 0";

    $tableData = DB::select($query);


    $response = array(
        'status' => true,
        'target' => $tableData,

    );
    return Response::json($response);
}


    /// Laporan Kensa Awal

public function reportAwal()
{

    return view('pianica.reportAwal')->with('page', 'Report Kensa Awal');
}

public function getKensaAwalALL(Request $request)
{
    $datep = $request->get('datep');
    $now = date('Y-m-d');

    if ($datep != "") {
        $date = $datep;
        $date2 = date('Y-m-d',strtotime($date));
        $last = date('Y-m-d', strtotime("-3 months",strtotime($datep)));
    }else{
        $date = date('Y-m-d');
        $last = date('Y-m-01', strtotime("-3 months",strtotime($now)));
    }

    $query="SELECT b.ng_name, COALESCE(a.total,0) total from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc";

$querylas="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc";

$query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' 
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' )  a";

$querylas2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d') >='".$last."' and location='PN_Kensa_Awal' 
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') >='".$last."' and location='PN_Kensa_Awal' )  a";


$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Awal' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'  ORDER BY created_at desc limit 1";

$tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Awal' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$last."'  ORDER BY created_at asc limit 1";

$tgl2 =DB::select($tgl);
$tgl2y =DB::select($tgly);

$total_ng =DB::select($query2);
$total_nglas =DB::select($querylas2);

$total =DB::select($query);
$totallas =DB::select($querylas);
$response = array(
    'status' => true,
    'message' => 'NG Record found',        
    'ng' => $total,
    'total' => $total_ng,

    'nglas' => $totallas,
    'totallas' => $total_nglas,
    'tgl' => $tgl2,
    'tgly' => $tgl2y,



);
return Response::json($response);
}

 /// Laporan Kensa Awal Line

public function reportAwalLine()
{

    return view('pianica.reportAwalLine')->with('page', 'Report Kensa Awal All Line');
}

public function getKensaAwalALLLine(Request $request)
{
 $datep = $request->get('datep');

 if ($datep != "") {
    $date = $datep;
    // $date2 = date('Y-m-d',strtotime($date));
    $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
}else{
    $date = date('Y-m-d');
    $last = date('Y-m-d', strtotime(Carbon::yesterday()));
}

$query="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
) a
left join (select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)c)b on a.ng_name = b.ng_name
) 1_3
left join (select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_3.ng_name = b.ng_name
) 1_4 
LEFT JOIN
(
select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_4.ng_name = b.ng_name
) 1_5 
LEFT JOIN
( 
select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_5.ng_name = b.ng_name
";

$querylas="SELECT 1_5.*, b.total_5 from (
select 1_4.*, b.total_4 from(
SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
SELECT a.ng_name, a.total_1, b.total_2 from (
SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='1' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
) a
left join (select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)c)b on a.ng_name = b.ng_name
) 1_3
left join (select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='3' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_3.ng_name = b.ng_name
) 1_4 
LEFT JOIN
(
select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='4' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_4.ng_name = b.ng_name
) 1_5 
LEFT JOIN
( 
select * from (
SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Awal'  and line ='5' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Awal'
) b
on a.ng = b.id ORDER BY ng_name asc
)a)b on 1_5.ng_name = b.ng_name
";

$query2="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='1'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='1' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='2'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='2' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='3'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='3' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='4'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='4' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='5'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' and line ='5' )  a";


$query2las="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='1'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='1' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='2'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='2' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='3'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='3' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='4'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='4' )  a

union all

SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='5'
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Awal' and line ='5' )  a";

$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Awal' ORDER BY created_at desc limit 1";
$tgl2 =DB::select($tgl);

$total_ng =DB::select($query2);
$total_nglas =DB::select($query2las);

$total =DB::select($query);
$totallas =DB::select($querylas);
$response = array(
    'status' => true,
    'message' => 'NG Record found',        
    'ng' => $total,
    'total' => $total_ng,
    'nglas' => $totallas,
    'totallas' => $total_nglas,
    'tgl' => $tgl2,


);
return Response::json($response);
}


    /// Laporan Kensa Akhir

public function reportAkhir()
{

    return view('pianica.reportAkhir')->with('page', 'Report Kensa Akhir');
}

public function getKensaAkhirALL(Request $request)
{
    $datep = $request->get('datep');

    // if ($datep != "") {
    // $date = $datep;
    // $date2 = date('Y-m-d',strtotime($date));
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    // }else{
    // $date = date('Y-m-d');
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    // }

    $now = date('Y-m-d');

    if ($datep != "") {
        $date = $datep;
        $date2 = date('Y-m-d',strtotime($date));
        $last = date('Y-m-d', strtotime("-3 months",strtotime($datep)));
    }else{
        $date = date('Y-m-d');
        $last = date('Y-m-01', strtotime("-3 months",strtotime($now)));
    }

    $query="SELECT b.ng_name, COALESCE(a.total,0) total from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
on a.ng = b.id ORDER BY ng_name asc";

$querylas="SELECT b.ng_name, COALESCE(a.total,0) total from (
SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir' 
-- and line ='2' 
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$last."'
GROUP BY ng) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
) b
on a.ng = b.id ORDER BY ng_name asc";

$query2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' 
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' )  a";

$querylas2 ="SELECT sum(total) as total, sum(total_ng) as ng from (
select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d') >='".$last."' and location='PN_Kensa_Akhir' 
union all
select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')>='".$last."' and location='PN_Kensa_Akhir' )  a";


$tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces  where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'   ORDER BY created_at desc limit 1";

$tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$last."'  ORDER BY created_at desc limit 1";


$tgl2 =DB::select($tgl);

$tgl2 =DB::select($tgl);
$tgl2y =DB::select($tgly);

$total_ng =DB::select($query2);
$total_nglas =DB::select($querylas2);

$total =DB::select($query);
$totallas =DB::select($querylas);
$response = array(
    'status' => true,
    'message' => 'NG Record found',        
    'ng' => $total,
    'total' => $total_ng,

    'nglas' => $totallas,
    'totallas' => $total_nglas,
    'tgl' => $tgl2,
    'tgly' => $tgl2y,



);
return Response::json($response);
}

 /// Laporan Kensa Akhir Line

public function reportAkhirLine()
{

    return view('pianica.reportAkhirLine')->with('page', 'Report Kensa Akhir All Line');
}

public function getKensaAkhirALLLine(Request $request)
{
    $datep = $request->get('datep');

    if ($datep != "") {
        $date = $datep;
        $date2 = date('Y-m-d',strtotime($date));
        $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    }else{
        $date = date('Y-m-d');
        $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    }

    $query="SELECT 1_5.*, b.total_5 from (
    select 1_4.*, b.total_4 from(
    SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
    SELECT a.ng_name, a.total_1, b.total_2 from (
    SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='1' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    ) a
    left join (select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='2' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )c)b on a.ng_name = b.ng_name
    ) 1_3
    left join (select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='3' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_3.ng_name = b.ng_name
    ) 1_4 
    LEFT JOIN
    (
    select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='4' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_4.ng_name = b.ng_name
    ) 1_5 
    LEFT JOIN
    ( 
    select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='5' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_5.ng_name = b.ng_name
    ";

    $querylas="SELECT 1_5.*, b.total_5 from (
    select 1_4.*, b.total_4 from(
    SELECT 1_3.ng_name, 1_3.total_1, 1_3.total_2, b.total_3 from (
    SELECT a.ng_name, a.total_1, b.total_2 from (
    SELECT b.ng_name, COALESCE(a.total,0) total_1 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='1' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    ) a
    left join (select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_2 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='2' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )c)b on a.ng_name = b.ng_name
    ) 1_3
    left join (select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_3 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='3' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_3.ng_name = b.ng_name
    ) 1_4 
    LEFT JOIN
    (
    select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_4 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='4' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_4.ng_name = b.ng_name
    ) 1_5 
    LEFT JOIN
    ( 
    select * from (
    SELECT b.ng_name, COALESCE(a.total,0) total_5 from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kensa_Akhir'  and line ='5' 
    and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$last."'
    GROUP BY ng) a
    RIGHT JOIN 
    (
    select id,ng_name from ng_lists WHERE location='PN_Kensa_Akhir'
    ) b
    on a.ng = b.id ORDER BY ng_name asc
    )a)b on 1_5.ng_name = b.ng_name
    ";

    $query2="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='1' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='2' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='3' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='4' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' and location='PN_Kensa_Akhir' and line ='5' )  a";


    $query2las="SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='1'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='1' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='2'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='2' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='3'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='3' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='4'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='4' )  a

    union all

    SELECT sum(total) as total_1, sum(total_ng) as ng_1 from (
    select COUNT(DISTINCT(tag)) as total, 0 total_ng  from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='5'
    union all
    select 0 total, COUNT(DISTINCT(tag)) as total_ng  from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d')='".$last."' and location='PN_Kensa_Akhir' and line ='5' )  a";

    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kensa_Akhir' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."'  ORDER BY created_at desc limit 1";
    $tgl2 =DB::select($tgl);

    $total_ng =DB::select($query2);
    $total_nglas =DB::select($query2las);

    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,
        'total' => $total_ng,
        'nglas' => $totallas,
        'totallas' => $total_nglas,
        'tgl' => $tgl2,
        

    );
    return Response::json($response);
}


 /// Laporan Kensa Awal

public function reportVisual()
{

    return view('pianica.reportVisual')->with('page', 'Report Kakunin Visual');
}

public function getKensaVisualALL(Request $request)
{
    $datep = $request->get('datep');

    // if ($datep != "") {
    // $date = $datep;
    // $date2 = date('Y-m-d',strtotime($date));
    // $last = date('Y-m-d', strtotime('-1 day', strtotime($date)));
    // }else{
    // $date = date('Y-m-d');
    // $last = date('Y-m-d', strtotime(Carbon::yesterday()));
    // }

    $now = date('Y-m-d');

    if ($datep != "") {
        $date = $datep;
        $date2 = date('Y-m-d',strtotime($date));
        $last = date('Y-m-d', strtotime("-3 months",strtotime($datep)));
    }else{
        $date = date('Y-m-d');
        $last = date('Y-m-01', strtotime("-3 months",strtotime($now)));
    }

    $query="SELECT SUM(total) as tot, location from (
    select b.id, b.location, COALESCE(a.total,0) as total from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d')='".$date."' GROUP BY ng
    ) a
    RIGHT JOIN  
    (
    SELECT id,location from ng_lists WHERE location LIKE 'PN_Kakuning_Visual%'
    )b
    on a.ng = b.id
    ) c GROUP BY location
    ";

    $querylas="SELECT SUM(total) as tot, location from (
    select b.id, b.location, COALESCE(a.total,0) as total from (
    SELECT ng, COUNT(qty) as total from pn_log_ngs WHERE location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d')>='".$last."' GROUP BY ng
    ) a
    RIGHT JOIN  
    (
    SELECT id,location from ng_lists WHERE location LIKE 'PN_Kakuning_Visual%'
    )b
    on a.ng = b.id
    ) c GROUP BY location
    ";



    $tgl = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces  where location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."'   ORDER BY created_at desc limit 1";

    $tgly = "SELECT DATE_FORMAT(created_at,'%W, %d %b %Y %H:%I:%S') as tgl from pn_log_proces where location='PN_Kakuning_Visual' and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$last."'  ORDER BY created_at desc limit 1";

    $tgl2 =DB::select($tgl);
    $tgl2y =DB::select($tgly);



    $total =DB::select($query);
    $totallas =DB::select($querylas);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,       
        'nglas' => $totallas,
        'tgl' => $tgl2,
        'tgly' => $tgl2y,

        

    );
    return Response::json($response);
}



/// Laporan Kensa Awal

public function recordPianica()
{

    return view('pianica.record')->with('page', 'Pianica Inventori');
}

public function recordPianica2(Request $request){
    $flo_detailsTable = DB::table('pn_log_proces')
    
    ->select('tag', 'model', 'qty', db::raw('date_format(updated_at, "%d-%b-%Y") as st_date'), DB::raw('(CASE WHEN location = "PN_Pureto" THEN "Pureto" WHEN location = "PN_Kensa_Awal" THEN "Kensa Awal" WHEN location = "PN_Kensa_Akhir" THEN "Kensa Akhir" ELSE "Kakunin Visual" END) AS location') );

    if(strlen($request->get('datefrom')) > 0){
        $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), '>=', $date_from);
    }

    if(strlen($request->get('code')) > 0){
        $code = $request->get('code');
        $flo_detailsTable = $flo_detailsTable->where('location','=', $code );
    }

    if(strlen($request->get('dateto')) > 0){
        $date_to = date('Y-m-d', strtotime($request->get('dateto')));
        $flo_detailsTable = $flo_detailsTable->where(DB::raw('DATE_FORMAT(updated_at, "%Y-%m-%d")'), '<=', $date_to);
    }

    $stamp_detail = $flo_detailsTable->orderBy('updated_at', 'desc')->get();

    return DataTables::of($stamp_detail)->make(true);
}

//report monthly
public function reportDayAwal()
{

    return view('pianica.reportAwalMonthly')->with('page', 'Monthly Report');
}


public function reportDayAwalData(Request $request){

    $from = $request->get('datefrom');
    $to = $request->get('dateto');

    $process = $request->get('code');   
    
    if ($from == "") {
        $from = date('Y-m-01');
    }
    if ($to == "") {      
       $to = date('Y-m-d');
   }

   $query = "SELECT biri.tgl, biri, oktaf, tinggi, rendah,c.total  from (
   SELECT week_date as tgl,COALESCE(a.total,0) as biri from (
   SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
   SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
   and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
   GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
   RIGHT JOIN 
   (
   select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Biri'
   ) b
   on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
   RIGHT JOIN 
   (
   SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
   )b on b.week_date = a.tgl)

   Biri

   LEFT JOIN
   (

   SELECT week_date as tgl,COALESCE(a.total,0) as oktaf from (
   SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
   SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
   and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
   GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
   RIGHT JOIN 
   (
   select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Oktaf'
   ) b
   on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
   RIGHT JOIN 
   (
   SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
   )b on b.week_date = a.tgl 
   ) oktaf 
   on biri.tgl = oktaf.tgl

   LEFT JOIN
   (

   SELECT week_date as tgl,COALESCE(a.total,0) as tinggi from (
   SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
   SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
   and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
   GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
   RIGHT JOIN 
   (
   select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Tinggi'
   ) b
   on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
   RIGHT JOIN 
   (
   SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
   )b on b.week_date = a.tgl 
   ) tinggi 
   on biri.tgl = tinggi.tgl

   LEFT JOIN
   (

   SELECT week_date as tgl,COALESCE(a.total,0) as rendah from (
   SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
   SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
   and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
   GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
   RIGHT JOIN 
   (
   select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Rendah'
   ) b
   on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
   RIGHT JOIN 
   (
   SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
   )b on b.week_date = a.tgl 
   ) rendah
   on biri.tgl = rendah.tgl 
   JOIN (
   SELECT
   DATE_FORMAT( created_at, '%Y-%m-%d' ) AS tgl,
   sum( qty ) AS total 
   FROM
   pn_log_proces 
   WHERE
   location = '".$process."' 
   AND DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$from."' 
   AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$to."' 
   group by tgl
) c ON c.tgl = biri.tgl";


$query2=" SELECT 'visual' as pro,  a.tgl, COALESCE(frame,0) as frame, COALESCE(rl,0) as rl, COALESCE(lower,0) as lower, COALESCE(handle,0) as handle, COALESCE(button,0) as button, COALESCE(pianica,0) as pianica,c.total from (

SELECT 'visual' as pro,frame.tgl, frame,rl,lower,handle,button,pianica from (
SELECT week_date as tgl,sum(a.total) as frame from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date) frame

LEFT JOIN 

(
SELECT week_date as tgl,sum(a.total) as rl from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date

) rl
on frame.tgl = rl.tgl

LEFT JOIN
(
SELECT week_date as tgl,sum(a.total) as lower from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date

) lower
on frame.tgl = lower.tgl


LEFT JOIN
(
SELECT week_date as tgl,sum(a.total) as handle from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Handle'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date

) handle
on frame.tgl = handle.tgl

LEFT JOIN
(
SELECT week_date as tgl,sum(a.total) as button from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Button'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date

) button
on frame.tgl = button.tgl

LEFT JOIN
(
SELECT week_date as tgl,sum(a.total) as pianica from (
SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
RIGHT JOIN 
(
select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Pianica'  
) b
on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
RIGHT JOIN 
(
SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
)b on b.week_date = a.tgl GROUP BY b.week_date

) pianica
on frame.tgl = pianica.tgl ) a 
JOIN (
SELECT
DATE_FORMAT( created_at, '%Y-%m-%d' ) AS tgl,
sum( qty ) AS total 
FROM
pn_log_proces 
WHERE
location = 'PN_Kakuning_Visual' 
AND DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$from."' 
AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$to."' 
group by tgl
) c ON c.tgl = a.tgl
";

$query3="
SELECT week_date, 
count(IF(ng ='Celah Lebar' and posisi ='low' ,1,null)) as CLebarL,
count(IF(ng ='Celah Lebar' and posisi ='high' ,1,null)) as CLebarH,
count(IF(ng ='Celah Sempit' and posisi ='low' ,1,null)) as CSempitL,
count(IF(ng ='Celah Sempit' and posisi ='high' ,1,null)) as CSempitH,
count(IF(ng ='Kepala Rusak' and posisi ='low' ,1,null)) as KRusakL,
count(IF(ng ='Kepala Rusak' and posisi ='high' ,1,null)) as KRusakH,
count(IF(ng ='Kotor' and posisi ='low' ,1,null)) as KotorL,
count(IF(ng ='Kotor' and posisi ='high' ,1,null)) as KotorH,
count(IF(ng ='Lekukan' and posisi ='low' ,1,null)) as LekukanL,
count(IF(ng ='Lekukan' and posisi ='high' ,1,null)) as LekukanH,
count(IF(ng ='Lengkung' and posisi ='low' ,1,null)) as LengkungL,
count(IF(ng ='Lengkung' and posisi ='high' ,1,null)) as LengkungH,
count(IF(ng ='Lepas' and posisi ='low' ,1,null)) as LepasL,
count(IF(ng ='Lepas' and posisi ='high' ,1,null)) as LepasH,
count(IF(ng ='Longgar' and posisi ='low' ,1,null)) as LonggarL,
count(IF(ng ='Longgar' and posisi ='high' ,1,null)) as LonggarH,
count(IF(ng ='Melekat' and posisi ='low' ,1,null)) as MelekatL,
count(IF(ng ='Melekat' and posisi ='high' ,1,null)) as MelekatH,
count(IF(ng ='Pangkal Menempel' and posisi ='low' ,1,null)) as PMenempelL,
count(IF(ng ='Pangkal Menempel' and posisi ='high' ,1,null)) as PMenempelH,
count(IF(ng ='Panjang' and posisi ='low' ,1,null)) as PanjangL,
count(IF(ng ='Panjang' and posisi ='high' ,1,null)) as PanjangH,
count(IF(ng ='Patah' and posisi ='low' ,1,null)) as PatahL,
count(IF(ng ='Patah' and posisi ='high' ,1,null)) as PatahH,
count(IF(ng ='Salah Posisi' and posisi ='low' ,1,null)) as SPosisiL,
count(IF(ng ='Salah Posisi' and posisi ='high' ,1,null)) as SPosisiH,
count(IF(ng ='Terbalik' and posisi ='low' ,1,null)) as TerbalikL,
count(IF(ng ='Terbalik' and posisi ='high' ,1,null)) as TerbalikH,
count(IF(ng ='Ujung Menempel' and posisi ='low' ,1,null)) as UMenempelL,
count(IF(ng ='Ujung Menempel' and posisi ='high' ,1,null)) as UMenempelH,
count(IF(ng ='Double' and posisi ='low' ,1,null)) as DoubleL,
count(IF(ng ='Double' and posisi ='high' ,1,null)) as DoubleH,
c.total
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
JOIN (
SELECT
DATE_FORMAT( created_at, '%Y-%m-%d' ) AS tgl,
count( DISTINCT(tag) ) AS total
FROM
pn_log_proces 
WHERE
DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$from."' 
AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$to."' 
group by tgl
) c ON c.tgl = week_date
group by week_date,
c.total

";

$query4="
SELECT week_date, 
count(IF(ng ='Celah Lebar' ,1,null)) as CLebar,
count(IF(ng ='Celah Sempit' ,1,null)) as CSempit,
count(IF(ng ='Kepala Rusak' ,1,null)) as KRusak,
count(IF(ng ='Kotor' ,1,null)) as Kotor,
count(IF(ng ='Lekukan' ,1,null)) as Lekukan,
count(IF(ng ='Lengkung' ,1,null)) as Lengkung,
count(IF(ng ='Lepas' ,1,null)) as Lepas,
count(IF(ng ='Longgar' ,1,null)) as Longgar,
count(IF(ng ='Melekat' ,1,null)) as Melekat,
count(IF(ng ='Pangkal Menempel' ,1,null)) as PMenempel,
count(IF(ng ='Panjang' ,1,null)) as Panjang,
count(IF(ng ='Patah' ,1,null)) as Patah,
count(IF(ng ='Salah Posisi' ,1,null)) as SPosisi,
count(IF(ng ='Terbalik' ,1,null)) as Terbalik,
count(IF(ng ='Ujung Menempel',1,null)) as UMenempel
from 
(SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
left join (
SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
group by week_date
";

if ( $process == "PN_Kensa_Akhir" || $process == "PN_Kensa_Awal") {
 $record =DB::select($query); 
}else if($process == "PN_Kakuning_Visual"){
    $record =DB::select($query2); 
}else{
    $record =DB::select($query3); 
} 



return DataTables::of($record)->make(true);

}


public function reportDayAwalDataGrafik(Request $request){

    $from = $request->get('datefrom');
    $to = $request->get('dateto');

    $process = $request->get('code');   
    
    if ($from == "") {
        $from = date('Y-m-01');
    }
    if ($to == "") {      
       $to = date('Y-m-d');
   }

   if ($process == "") {
     $process = 'PN_Kensa_Awal'; 
 }

 $query = "SELECT 'awal' as pro,biri.tgl, biri, oktaf, tinggi, rendah,COALESCE(target,0) target from (
 SELECT week_date as tgl,COALESCE(a.total,0) as biri from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Biri'
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl)

 Biri

 LEFT JOIN
 (

 SELECT week_date as tgl,COALESCE(a.total,0) as oktaf from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='Oktaf'
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) oktaf 
 on biri.tgl = oktaf.tgl

 LEFT JOIN
 (

 SELECT week_date as tgl,COALESCE(a.total,0) as tinggi from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Tinggi'
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) tinggi 
 on biri.tgl = tinggi.tgl

 LEFT JOIN
 (

 SELECT week_date as tgl,COALESCE(a.total,0) as rendah from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location='".$process."' 
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location='".$process."'  and ng_name='T. Rendah'
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl 
 ) rendah
 on biri.tgl = rendah.tgl 

 left join (
 SELECT SUM(total) as target, due_date from (
 SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
 SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
 GROUP BY material_number, due_date
 ) a GROUP BY due_date
 ) target on biri.tgl = target.due_date
 ";

 $query2=" SELECT 'visual' as pro, tgl, COALESCE(frame,0) as frame, COALESCE(rl,0) as rl, COALESCE(lower,0) as lower, COALESCE(handle,0) as handle, COALESCE(button,0) as button, COALESCE(pianica,0) as pianica, COALESCE(target,0) target from (

 SELECT 'visual' as pro,frame.tgl, frame,rl,lower,handle,button,pianica, target from (
 SELECT week_date as tgl,sum(a.total) as frame from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date) frame

 LEFT JOIN 

 (
 SELECT week_date as tgl,sum(a.total) as rl from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date

 ) rl
 on frame.tgl = rl.tgl

 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as lower from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date

 ) lower
 on frame.tgl = lower.tgl


 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as handle from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Handle'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date

 ) handle
 on frame.tgl = handle.tgl

 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as button from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Button'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date

 ) button
 on frame.tgl = button.tgl

 LEFT JOIN
 (
 SELECT week_date as tgl,sum(a.total) as pianica from (
 SELECT b.ng_name, COALESCE(a.total,0) total, a.tgl from (
 SELECT ng, COUNT(qty) as total, DATE_FORMAT(created_at,'%Y-%m-%d') as tgl from pn_log_ngs WHERE location ='PN_Kakuning_Visual'
 and DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$to."'
 GROUP BY ng, DATE_FORMAT(created_at,'%Y-%m-%d')) a
 RIGHT JOIN 
 (
 select id,ng_name from ng_lists WHERE location = 'PN_Kakuning_Visual_Pianica'  
 ) b
 on a.ng = b.id ORDER BY ng_name, tgl asc ) a 
 RIGHT JOIN 
 (
 SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."'
 )b on b.week_date = a.tgl GROUP BY b.week_date

 ) pianica
 on frame.tgl = pianica.tgl  


 left join (
 SELECT SUM(total) as target, due_date from (
 SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
 SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
 GROUP BY material_number, due_date
 ) a GROUP BY due_date
 ) target on frame.tgl = target.due_date
 ) a ORDER BY a.tgl asc

 ";

 $query3 ="

 SELECT week_date as tgl, 
 count(IF(ng ='Celah Lebar' and posisi ='low' ,1,null)) as CLebarL,
 count(IF(ng ='Celah Lebar' and posisi ='high' ,1,null)) as CLebarH,
 count(IF(ng ='Celah Sempit' and posisi ='low' ,1,null)) as CSempitL,
 count(IF(ng ='Celah Sempit' and posisi ='high' ,1,null)) as CSempitH,
 count(IF(ng ='Kepala Rusak' and posisi ='low' ,1,null)) as KRusakL,
 count(IF(ng ='Kepala Rusak' and posisi ='high' ,1,null)) as KRusakH,
 count(IF(ng ='Kotor' and posisi ='low' ,1,null)) as KotorL,
 count(IF(ng ='Kotor' and posisi ='high' ,1,null)) as KotorH,
 count(IF(ng ='Lekukan' and posisi ='low' ,1,null)) as LekukanL,
 count(IF(ng ='Lekukan' and posisi ='high' ,1,null)) as LekukanH,
 count(IF(ng ='Lengkung' and posisi ='low' ,1,null)) as LengkungL,
 count(IF(ng ='Lengkung' and posisi ='high' ,1,null)) as LengkungH,
 count(IF(ng ='Lepas' and posisi ='low' ,1,null)) as LepasL,
 count(IF(ng ='Lepas' and posisi ='high' ,1,null)) as LepasH,
 count(IF(ng ='Longgar' and posisi ='low' ,1,null)) as LonggarL,
 count(IF(ng ='Longgar' and posisi ='high' ,1,null)) as LonggarH,
 count(IF(ng ='Melekat' and posisi ='low' ,1,null)) as MelekatL,
 count(IF(ng ='Melekat' and posisi ='high' ,1,null)) as MelekatH,
 count(IF(ng ='Pangkal Menempel' and posisi ='low' ,1,null)) as PMenempelL,
 count(IF(ng ='Pangkal Menempel' and posisi ='high' ,1,null)) as PMenempelH,
 count(IF(ng ='Panjang' and posisi ='low' ,1,null)) as PanjangL,
 count(IF(ng ='Panjang' and posisi ='high' ,1,null)) as PanjangH,
 count(IF(ng ='Patah' and posisi ='low' ,1,null)) as PatahL,
 count(IF(ng ='Patah' and posisi ='high' ,1,null)) as PatahH,
 count(IF(ng ='Salah Posisi' and posisi ='low' ,1,null)) as SPosisiL,
 count(IF(ng ='Salah Posisi' and posisi ='high' ,1,null)) as SPosisiH,
 count(IF(ng ='Terbalik' and posisi ='low' ,1,null)) as TerbalikL,
 count(IF(ng ='Terbalik' and posisi ='high' ,1,null)) as TerbalikH,
 count(IF(ng ='Ujung Menempel' and posisi ='low' ,1,null)) as UMenempelL,
 count(IF(ng ='Ujung Menempel' and posisi ='high' ,1,null)) as UMenempelH
 from 
 (SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
 left join (
 SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
 group by week_date
 ";

 $query4="
 select a.*, COALESCE(target,0) target from (
 SELECT week_date as tgl, 
 count(IF(ng ='Celah Lebar' ,1,null)) as CLebar,
 count(IF(ng ='Celah Sempit' ,1,null)) as CSempit,
 count(IF(ng ='Kepala Rusak' ,1,null)) as KRusak,
 count(IF(ng ='Kotor' ,1,null)) as Kotor,
 count(IF(ng ='Lekukan' ,1,null)) as Lekukan,
 count(IF(ng ='Lengkung' ,1,null)) as Lengkung,
 count(IF(ng ='Lepas' ,1,null)) as Lepas,
 count(IF(ng ='Longgar' ,1,null)) as Longgar,
 count(IF(ng ='Melekat' ,1,null)) as Melekat,
 count(IF(ng ='Pangkal Menempel' ,1,null)) as PMenempel,
 count(IF(ng ='Panjang' ,1,null)) as Panjang,
 count(IF(ng ='Patah' ,1,null)) as Patah,
 count(IF(ng ='Salah Posisi' ,1,null)) as SPosisi,
 count(IF(ng ='Terbalik' ,1,null)) as Terbalik,
 count(IF(ng ='Ujung Menempel',1,null)) as UMenempel,
 count(IF(ng ='Double',1,null)) as Doble
 from 
 (SELECT week_date from weekly_calendars WHERE DATE_FORMAT(week_date,'%Y-%m-%d') >= '".$from."' and DATE_FORMAT(week_date,'%Y-%m-%d') <= '".$to."') s
 left join (
 SELECT ng, posisi, created_at FROM detail_bensukis) as d on DATE_FORMAT(d.created_at,'%Y-%m-%d') = s.week_date
 group by week_date ) a

 left join (
 SELECT SUM(total) as target, due_date from (
 SELECT material_number, due_date, sum(quantity) as total from production_schedules WHERE material_number in (
 SELECT material_number from materials where materials.category = 'FG' and materials.origin_group_code = '073')
 GROUP BY material_number, due_date
 ) a GROUP BY due_date
 ) target on a.tgl = target.due_date  ORDER BY tgl asc

 ";


 if ( $process == "PN_Kensa_Akhir" || $process == "PN_Kensa_Awal") {
     $record =DB::select($query); 
 }else if($process == "PN_Kakuning_Visual"){
    $record =DB::select($query2); 
}else{
    $record =DB::select($query4); 
} 

$response = array(
    'status' => true,
    'message' => 'NG Record found',  
    'record' => $record,       

);
return Response::json($response);
}

//detail chart


public function getKensaVisualALL2(Request $request)
{
    $date = date('Y-m-d', strtotime($request->get('tgl')));
    $ng = $request->get('ng');

    $query="
    SELECT ng_lists.ng_name as ng, COUNT(qty) as total from pn_log_ngs 
    LEFT JOIN ng_lists on pn_log_ngs.ng = ng_lists.id
    WHERE pn_log_ngs.location='PN_Kakuning_Visual' and ng_lists.location like '%_".$ng."' AND DATE_FORMAT(pn_log_ngs.created_at,'%Y-%m-%d')='".$date."'
    GROUP BY ng_lists.ng_name
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total, 
        'asd'=> $date,      


    );
    return Response::json($response);
}

public function getKensaBensuki2(Request $request)
{
    $date = date('Y-m-d', strtotime($request->get('tgl')));
    $ng = $request->get('ng');

    if ($ng =="Mesin 1") {
        $ng = "H1";
    }

    if ($ng =="Mesin 2") {
        $ng = "H2";
    }

    if ($ng =="Mesin 3") {
        $ng = "H3";
    }

    if ($ng =="Mesin 4") {
        $ng = "M1";
    }

    if ($ng =="Mesin 5") {
        $ng = "M2";
    }

    if ($ng =="Mesin 6") {
        $ng = "M3";
    }


    $query="    
    SELECT posisi,ng,COUNT(posisi) as total, GROUP_CONCAT(id_bensuki) as id from detail_bensukis where id_bensuki in (
    SELECT id from header_bensukis where DATE_FORMAT(header_bensukis.created_at,'%Y-%m-%d') = '".$date."' and mesin='".$ng."'
    ) GROUP BY ng,posisi ORDER BY posisi asc
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,     


    );
    return Response::json($response);
}

public function getKensaBensuki3(Request $request)
{

    $ng = $request->get('id');


    $query=" 
    SELECT model,ben.nik as opbennik,ben.nama as opbennama, line, plate.nik as opplatenik, plate.nama as opplatenama,ben.shift from (
    SELECT header_bensukis.id,pn_operators.nik, pn_operators.nama, header_bensukis.line, model,IF(header_bensukis.shift='M','Shift 1','Shift 3') as shift from header_bensukis 
    LEFT JOIN pn_operators on header_bensukis.nik_op_bensuki = pn_operators.nik
    WHERE header_bensukis.id in (".$ng.") 
    )ben
    LEFT JOIN
    (
    SELECT header_bensukis.id,pn_operators.nik, pn_operators.nama from header_bensukis 
    LEFT JOIN pn_operators on header_bensukis.nik_op_plate = pn_operators.nik
    WHERE header_bensukis.id in (".$ng.") 
    ) plate 
    on ben.id = plate.id
    ";

    $total =DB::select($query);
    $response = array(
        'status' => true,
        'message' => 'NG Record found',        
        'ng' => $total,     


    );
    return Response::json($response);
}
///---------------------- master code op

public function opcode()
{
    $dataop = "SELECT nik,nama from pn_operators";
    $op = DB::select($dataop);
    return view('pianica.opcode', array(
        'op' => $op)
)->with('page', 'Master Code Operator');
}

public function fillopcode($value='')
{
    $op = "SELECT pn_code_operators.id,pn_code_operators.bagian,pn_code_operators.kode,pn_code_operators.nik,pn_operators.nama from pn_code_operators
    LEFT JOIN pn_operators on pn_code_operators.nik = pn_operators.nik order By pn_code_operators.id asc";
    $ops = DB::select($op);
    return DataTables::of($ops)

    // <a href="javascript:void(0)" class="btn btn-xs btn-danger" onClick="detailReport(id)" id="' . $ops->id . '">Delete</a>

    ->addColumn('action', function($ops){
        return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i> Edit</a>';
    })
    ->rawColumns(['action' => 'action'])

    ->make(true);
}

public function editopcode(Request $request)
{
    $id = $request->get('id');
    $op = "SELECT pn_code_operators.id,pn_code_operators.bagian,pn_code_operators.kode,pn_code_operators.nik,pn_operators.nama from pn_code_operators
    LEFT JOIN pn_operators on pn_code_operators.nik = pn_operators.nik where pn_code_operators.id ='".$id."'";
    $id_op = DB::select($op);
    $response = array(
        'status' => true,
        'id_op' => $id_op, 
    );
    return Response::json($response);
}

public function updateopcode(Request $request){
    $id_user = Auth::id();
    
    try {  
        $op = PnCodeOperator::where('id','=', $request->get('id'))       
        ->first();         
        $op->nik = $request->get('op');        
        $op->created_by = $id_user;
        $op->save();
        $response = array(
          'status' => true,
          'message' => 'Update Success',
      );
        return redirect('/index/Op_Code')->with('status', 'Update operator success')->with('page', 'Master Operator');
    }catch (QueryException $e){
        return redirect('/index/Op_Code')->with('error', $e->getMessage())->with('page', 'Master Operator');
    }

}


//report monthly new display spotwelding
public function reportSpotWelding()
{

    return view('pianica.reportSpotWelding')->with('page', 'Report Spot Welding');
}

public function reportSpotWeldingData(Request $request){
    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $ng = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");

    $ng_line_1 = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND header_bensukis.line = '1' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND a.line = '1' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND pn_log_proces.line = '1' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");

    $ng_line_2 = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND header_bensukis.line = '2' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND a.line = '2' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND pn_log_proces.line = '2' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");

    $ng_line_3 = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND header_bensukis.line = '3' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND a.line = '3' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND pn_log_proces.line = '3' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");

    $ng_line_4 = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND header_bensukis.line = '4' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND a.line = '4' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND pn_log_proces.line = '4' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");

    $ng_line_5 = DB::SELECT("SELECT
        cc.dates,
        sum( CASE WHEN cc.ng = 'H1' THEN ng_all ELSE 0 END ) AS mesin_1,
        sum( CASE WHEN cc.ng = 'H2' THEN ng_all ELSE 0 END ) AS mesin_2,
        sum( CASE WHEN cc.ng = 'H3' THEN ng_all ELSE 0 END ) AS mesin_3,
        sum( CASE WHEN cc.ng = 'M1' THEN ng_all ELSE 0 END ) AS mesin_4,
        sum( CASE WHEN cc.ng = 'M2' THEN ng_all ELSE 0 END ) AS mesin_5,
        sum( CASE WHEN cc.ng = 'M3' THEN ng_all ELSE 0 END ) AS mesin_6,
        pureto.prod_result 
        FROM
        (
        SELECT
        ng_name AS ng,
        dates,
        COALESCE ( ng, 0 ) AS ng_all 
        FROM
        (
        SELECT
                    * 
        FROM
        ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) a
        CROSS JOIN (
        SELECT DISTINCT
        (
        DATE_FORMAT( created_at, '%Y-%m-%d' )) AS dates 
        FROM
        header_bensukis 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND header_bensukis.line = '5' 
        ) AS tgl_all 
        ) a
        LEFT JOIN (
        SELECT
        mesin,
        COUNT( ng ) AS ng,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) AS tgl 
        FROM
        ( SELECT mesin, ng, header_bensukis.created_at,line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
        RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND a.line = '5' 
        GROUP BY
        mesin,
        DATE_FORMAT( a.created_at, '%Y-%m-%d' ) 
        ORDER BY
        mesin ASC 
        ) AS aa ON a.ng_name = aa.mesin 
        AND a.dates = aa.tgl 
        ) cc
        LEFT JOIN (
        SELECT
        DATE( created_at ) AS dates,
        count(
        DISTINCT ( form_id )) AS prod_result 
        FROM
        pn_log_proces 
        WHERE
        DATE_FORMAT( created_at, '%Y-%m-%d' ) >= '".$first."' 
        AND DATE_FORMAT( created_at, '%Y-%m-%d' ) <= '".$last."' 
        AND pn_log_proces.line = '5' 
        AND location = 'PN_Pureto' 
        GROUP BY
        dates 
        ) AS pureto ON pureto.dates = cc.dates 
        GROUP BY
        cc.dates,
        pureto.prod_result");
    $response = array(
        'status' => true,            
        'ng' => $ng,
        'ng_line_1' => $ng_line_1,
        'ng_line_2' => $ng_line_2,
        'ng_line_3' => $ng_line_3,
        'ng_line_4' => $ng_line_4,
        'ng_line_5' => $ng_line_5,
        'monthTitle' => $monthTitle,
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


public function reportSpotWeldingDataDetail(Request $request){
    $tgl = $request->get('tgl');
    $mesin = $request->get('mesin');

    if ($mesin == "Mesin 1") {
        $mesin = "H1";
    }

    if ($mesin == "Mesin 2") {
        $mesin = "H2";
    }

    if ($mesin == "Mesin 3") {
        $mesin = "H3";
    }

    if ($mesin == "Mesin 4") {
        $mesin = "M1";
    }

    if ($mesin == "Mesin 5") {
        $mesin = "M2";
    }
    if ($mesin == "Mesin 6") {
        $mesin = "M3";
    }

    $line = '';
    if ($request->get('line') != 'All') {
        $line = "AND a.line = '".$request->get('line')."' ";
    }


    $query = "SELECT
    ng,
    COUNT( ng ) AS total 
    FROM
    ( SELECT mesin, ng, header_bensukis.created_at, line FROM header_bensukis LEFT JOIN detail_bensukis ON header_bensukis.ID = detail_bensukis.id_bensuki ) a
    RIGHT JOIN ( SELECT ng_name FROM ng_lists WHERE location = 'PN_Bensuki_Mesin' ) b ON a.mesin = b.ng_name 
    WHERE
    DATE_FORMAT( a.created_at, '%Y-%m-%d' ) = '".$tgl."' 
    AND mesin = '".$mesin."' 
    AND ng IS NOT NULL 
    ".$line."
    GROUP BY
    ng";



    $ng = DB::select($query);
    $response = array(
        'status' => true,            
        'ng' => $ng,    
        'message' => 'Success',

    );
    return Response::json($response);
}



//report monthly new display spotwelding
public function reportKensaAwalDaily()
{

    return view('pianica.reportKensaAwalDaily')->with('page', 'Report Kensa Awal');
}

public function getReportKensaAwalDaily(Request $request){

    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $ng = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_1 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_2 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_3 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_4 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_5 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Awal' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Awal' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");
    $response = array(
        'status' => true,            
        'ng' => $ng,
        'ng_line_1' => $ng_line_1,
        'ng_line_2' => $ng_line_2,
        'ng_line_3' => $ng_line_3,
        'ng_line_4' => $ng_line_4,
        'ng_line_5' => $ng_line_5,
        'monthTitle' => $monthTitle,
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

    //report monthly new display spotwelding
public function reportKensaAkhirDaily()
{

    return view('pianica.reportKensaAkhirDaily')->with('page', 'Report Kensa Awal');
}

public function getReportKensaAkhirDaily(Request $request){

    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $ng = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_1 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."'
        AND pn_log_ngs.line = '1' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_2 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."'
        AND pn_log_ngs.line = '2' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_3 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."'
        AND pn_log_ngs.line = '3' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_4 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."'
        AND pn_log_ngs.line = '4' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_5 = DB::SELECT("SELECT
        a.date,
        sum( a.t_tinggi ) AS t_tinggi,
        sum( a.t_rendah ) AS t_rendah,
        sum( a.biri ) AS biri,
        sum( a.oktaf ) AS oktaf 
        FROM
        (
        SELECT
        date( created_at ) AS date,
        count( form_id ) AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Tinggi' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 t_tinggi,
        count( form_id ) AS t_rendah,
        0 AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'T. Rendah' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."'
        AND pn_log_ngs.line = '5' 
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        count( form_id ) AS biri,
        0 AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Biri' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        date( created_at ) AS date,
        0 AS t_tinggi,
        0 AS t_rendah,
        0 AS biri,
        count( form_id ) AS oktaf 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kensa_Akhir' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kensa_Akhir' AND ng_name = 'Oktaf' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");
    $response = array(
        'status' => true,            
        'ng' => $ng,
        'ng_line_1' => $ng_line_1,
        'ng_line_2' => $ng_line_2,
        'ng_line_3' => $ng_line_3,
        'ng_line_4' => $ng_line_4,
        'ng_line_5' => $ng_line_5,
        'monthTitle' => $monthTitle,
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

public function fetchDetailKensaAwalDaily(Request $request)
{
    try {
        $location = $request->get('location');
        $date = $request->get('date');
        $ng_name = $request->get('ng_name');
        $line = '';
        if ($request->get('line') != 'All') {
            $line = "AND pn_log_ngs.line = '".$request->get('line')."'";
        }
        $kensa = DB::SELECT("SELECT
            pn_log_ngs.*,
            '".$ng_name."' AS ng_name,
            SPLIT_STRING ( operator, '#', 1 ) AS id_tuning,
            SPLIT_STRING ( operator, '#', 2 ) AS id_bensuki 
            FROM
            pn_log_ngs 
            WHERE
            DATE( pn_log_ngs.created_at ) = '".$date."' 
            AND location = '".$location."' 
            AND ng IN (
            SELECT
            id 
            FROM
            ng_lists 
            WHERE
            location = '".$location."' 
            AND ng_name = '".$ng_name."' 
            )
            ".$line."");

        $emp = EmployeeSync::get();
        $response = array(
            'status' => true,
            'kensa' => $kensa,
            'emp' => $emp
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

public function fetchDetailKensaAkhirDaily(Request $request)
{
    try {
        $location = $request->get('location');
        $date = $request->get('date');
        $ng_name = $request->get('ng_name');
        $line = '';
        if ($request->get('line') != 'All') {
            $line = "AND pn_log_ngs.line = '".$request->get('line')."'";
        }
        $kensa = DB::SELECT("SELECT
            pn_log_ngs.*,
            '".$ng_name."' AS ng_name
            FROM
            pn_log_ngs 
            WHERE
            DATE( pn_log_ngs.created_at ) = '".$date."' 
            AND location = '".$location."' 
            AND ng IN (
            SELECT
            id 
            FROM
            ng_lists 
            WHERE
            location = '".$location."' 
            AND ng_name = '".$ng_name."' 
            )
            ".$line."");

        $emp = EmployeeSync::get();
        $response = array(
            'status' => true,
            'kensa' => $kensa,
            'emp' => $emp
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

      //report monthly new display spotwelding
public function reportVisualDaily()
{

    return view('pianica.reportKensaVisualDaily')->with('page', 'Report Kensa Awal');
}

public function getReportVisualDaily(Request $request){
    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $ng = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_1 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_2 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_3 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_4 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_5 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Kakuning_Visual' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Kakuning_Visual_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");
    $response = array(
        'status' => true,            
        'ng' => $ng,
        'ng_line_1' => $ng_line_1,
        'ng_line_2' => $ng_line_2,
        'ng_line_3' => $ng_line_3,
        'ng_line_4' => $ng_line_4,
        'ng_line_5' => $ng_line_5,
        'monthTitle' => $monthTitle,
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

public function fetchDetailKensaVisualDaily(Request $request)
{
    try {
        $location = $request->get('location');
        $date = $request->get('date');
        $ng_loc = $request->get('ng_loc');
        $line = '';
        if ($request->get('line') != 'All') {
            $line = "AND pn_log_ngs.line = '".$request->get('line')."'";
        }
        $kensa = DB::SELECT("SELECT
            pn_log_ngs.*,
            '".$ng_loc."' AS ng_loc,
            ngs.ng_name 
            FROM
            pn_log_ngs
            LEFT JOIN ( SELECT id, ng_name FROM ng_lists WHERE location = '".$ng_loc."' ) AS ngs ON ngs.id = pn_log_ngs.ng 
            WHERE
            DATE( pn_log_ngs.created_at ) = '".$date."' 
            AND location = '".$location."' 
            AND ng IN (
            SELECT
            id 
            FROM
            ng_lists 
            WHERE
            location = '".$ng_loc."' 
            )
            ".$line."");

        $emp = EmployeeSync::get();
        $response = array(
            'status' => true,
            'kensa' => $kensa,
            'emp' => $emp
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

//NG Trend Assembly

public function reportAssemblyDaily()
{

    return view('pianica.reportKensaAssemblyDaily')->with('page', 'Report Assembly');
}

public function getReportAssemblyDaily(Request $request){
    try {
        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $ng = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_1 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '1'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_2 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '2'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_3 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '3'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_4 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '4'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");

    $ng_line_5 = DB::SELECT("SELECT
        a.date,
        sum( a.cover_r_l ) AS cover_r_l,
        sum( a.cover_lower ) AS cover_lower,
        sum( a.handle ) AS handle,
        sum( a.button ) AS button,
        sum( a.frame ) AS frame 
        FROM
        (
        SELECT
        DATE( created_at ) AS date,
        count( form_id ) AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover R/L' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        count( form_id ) AS cover_lower,
        0 AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Cover Lower' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        count( form_id ) AS handle,
        0 AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Handle' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        count( form_id ) AS button,
        0 AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Button' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date UNION ALL
        SELECT
        DATE( created_at ) AS date,
        0 AS cover_r_l,
        0 AS cover_lower,
        0 AS handle,
        0 AS button,
        count( form_id ) AS frame 
        FROM
        pn_log_ngs 
        WHERE
        location = 'PN_Assembly' 
        AND ng IN ( SELECT id FROM ng_lists WHERE location = 'PN_Assembly_Frame Assy' ) 
        AND DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND pn_log_ngs.line = '5'
        GROUP BY
        date 
        ) a 
        GROUP BY
        a.date");
    $response = array(
        'status' => true,            
        'ng' => $ng,
        'ng_line_1' => $ng_line_1,
        'ng_line_2' => $ng_line_2,
        'ng_line_3' => $ng_line_3,
        'ng_line_4' => $ng_line_4,
        'ng_line_5' => $ng_line_5,
        'monthTitle' => $monthTitle,
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

public function fetchDetailKensaAssemblyDaily(Request $request)
{
    try {
        $location = $request->get('location');
        $date = $request->get('date');
        $ng_loc = $request->get('ng_loc');
        $line = '';
        if ($request->get('line') != 'All') {
            $line = "AND pn_log_ngs.line = '".$request->get('line')."'";
        }
        $kensa = DB::SELECT("SELECT
            pn_log_ngs.*,
            '".$ng_loc."' AS ng_loc,
            ngs.ng_name 
            FROM
            pn_log_ngs
            LEFT JOIN ( SELECT id, ng_name FROM ng_lists WHERE location = '".$ng_loc."' ) AS ngs ON ngs.id = pn_log_ngs.ng 
            WHERE
            DATE( pn_log_ngs.created_at ) = '".$date."' 
            AND location = '".$location."' 
            AND ng IN (
            SELECT
            id 
            FROM
            ng_lists 
            WHERE
            location = '".$ng_loc."' 
            )
            ".$line."");

        $emp = EmployeeSync::get();
        $response = array(
            'status' => true,
            'kensa' => $kensa,
            'emp' => $emp
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


public function fetchNgWelding(Request $request){

    // $date = '';

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
        if ($date_to == "") {
          $first = date('Y-m-01');
          $last = date('Y-m-d');
      }else{
          $first = date('Y-m-01');
          $last = $date_to;
      }
  }else{
     if ($date_to == "") {
        $first = $date_from;
        $last = date('Y-m-d');
    }else{
        $first = $date_from;
        $last = $date_to;
    }
}

$monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

$ng = db::select("SELECT
    op.nama,
    count( ng ) AS qty 
    FROM
    detail_bensukis d
    LEFT JOIN header_bensukis h ON h.id = d.id_bensuki
    LEFT JOIN pn_operators op ON op.nik = h.nik_op_plate 
    WHERE
    date( d.created_at ) >= '".$first."' 
    AND date( d.created_at ) <= '".$last."' 
    GROUP BY
    op.nama 
    ORDER BY
    qty DESC");

$response = array(
    'status' => true,            
    'ng' => $ng,
    'monthTitle' => $monthTitle,
    'date_from' => $first,
    'date_to' => $last,
);
return Response::json($response);
}


public function fetchNgBentsukiBenage(Request $request){
    // $date = '';
    // $before = '';

    // if( $request->get('tanggal') != "") {
    //     $date = date('Y-m-d', strtotime($request->get('tanggal')));
    //     $before = date('Y-m-d', strtotime('-7 days', strtotime($request->get('tanggal'))));
    // }else{
    //     $date = date('Y-m-d');
    //     $before = date('Y-m-d', strtotime('-7 days'));
    // }

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
        if ($date_to == "") {
          $first = date('Y-m-01');
          $last = date('Y-m-d');
      }else{
          $first = date('Y-m-01');
          $last = $date_to;
      }
  }else{
     if ($date_to == "") {
        $first = $date_from;
        $last = date('Y-m-d');
    }else{
        $first = $date_from;
        $last = $date_to;
    }
}

$monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

$ng = db::select("SELECT
    a.operator,
    a.ng_name,
    sum( a.qty ) AS jml 
    FROM
    (
    SELECT
    ng,
    ng_lists.ng_name,
    SPLIT_STRING ( operator, '#', 2 ) AS operator,
    pn_log_ngs.qty 
    FROM
    pn_log_ngs
    LEFT JOIN ng_lists ON ng_lists.id = pn_log_ngs.ng 
    WHERE
    pn_log_ngs.location = 'PN_Kensa_Awal' 
    AND date( pn_log_ngs.created_at ) >= '".$first."' 
    AND date( pn_log_ngs.created_at ) <= '".$last."' 
    ) a 
    GROUP BY
    a.operator,
    a.ng_name");

$op = db::select("SELECT distinct  operator, op.name as nama from pn_log_proces log
    left join employees op on op.employee_id = operator
    WHERE location = 'PN_Pureto'
    and date(log.created_at) <= '".$last."' 
    and date(log.created_at) >= '".$first."'");

$response = array(
    'status' => true,            
    'ng' => $ng,
    'op' => $op,
    'monthTitle' => $monthTitle,
    'date_from' => $first,
    'date_to' => $last,
);
return Response::json($response);
}

public function fetchNgKensaAwal(Request $request){
    // $date = '';
    // $before = '';

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
        if ($date_to == "") {
          $first = date('Y-m-01');
          $last = date('Y-m-d');
      }else{
          $first = date('Y-m-01');
          $last = $date_to;
      }
  }else{
     if ($date_to == "") {
        $first = $date_from;
        $last = date('Y-m-d');
    }else{
        $first = $date_from;
        $last = $date_to;
    }
}

$monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

$before = date('Y-m-d', strtotime('-7 days', strtotime($first)));

$ng = db::select("SELECT
    a.operator,
    a.ng_name,
    sum( a.qty ) AS jml 
    FROM
    (
    SELECT
    ng_lists.ng_name,
    pn_log_ngs.qty,
    log.operator 
    FROM
    pn_log_ngs
    LEFT JOIN ng_lists ON ng_lists.id = pn_log_ngs.ng
    LEFT JOIN (
    SELECT DISTINCT
    ( form_id ),
    created_by AS operator 
    FROM
    pn_log_proces 
    WHERE
    form_id IS NOT NULL 
    AND date( pn_log_proces.created_at ) >= '".$before."' 
    AND date( pn_log_proces.created_at ) <= '".$last."' 
    AND location = 'PN_Kensa_Awal' 
    ) AS log ON log.form_id = pn_log_ngs.form_id 
    WHERE
    pn_log_ngs.location = 'PN_Kensa_Akhir' 
    AND date( pn_log_ngs.created_at ) >= '".$first."' 
    AND date( pn_log_ngs.created_at ) <= '".$last."' 
    ) a 
    GROUP BY
    a.operator,
    a.ng_name");

$op = db::select("SELECT distinct log.created_by as operator , op.name as nama from pn_log_proces log
    left join employees op on op.employee_id = log.created_by
    WHERE location = 'PN_Kensa_Awal'
    and date(log.created_at) <= '".$last."' 
    and date(log.created_at) >= '".$before."'");

$response = array(
    'status' => true,            
    'ng' => $ng,
    'op' => $op,
    'monthTitle' => $monthTitle,
    'date_from' => $first,
    'date_to' => $last,
);
return Response::json($response);
}

public function fetchNgTuning(Request $request){
    // $date = '';

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    if ($date_from == "") {
        if ($date_to == "") {
          $first = date('Y-m-01');
          $last = date('Y-m-d');
      }else{
          $first = date('Y-m-01');
          $last = $date_to;
      }
  }else{
     if ($date_to == "") {
        $first = $date_from;
        $last = date('Y-m-d');
    }else{
        $first = $date_from;
        $last = $date_to;
    }
}

$monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

$ng = db::select("SELECT
    ng.*,
    SUM( qty ) AS total 
    FROM
    (
    SELECT
    ng,
    ng_name,
    SPLIT_STRING ( operator, '#', 1 ) AS tuning,
    qty 
    FROM
    pn_log_ngs
    LEFT JOIN ng_lists ON pn_log_ngs.ng = ng_lists.id 
    WHERE
    ng_lists.location = 'PN_Kensa_Awal' 
    AND date( pn_log_ngs.created_at ) >= '".$first."' 
    AND date( pn_log_ngs.created_at ) <= '".$last."' 
    AND pn_log_ngs.location = 'PN_Kensa_Awal' 
    ) ng 
    GROUP BY
    tuning,
    ng");

$op = db::select("SELECT nik, nama from pn_operators WHERE bagian='tuning' GROUP BY nik,nama");


$response = array(
    'status' => true,            
    'ng' => $ng,
    'op' => $op,
    'monthTitle' => $monthTitle,
    'date_from' => $first,
    'date_to' => $last,
);
return Response::json($response);
}

public function fetchTrendNgWelding(Request $request){

    $datefrom = date("Y-m-d", strtotime("-3 Months"));
    $dateto = date("Y-m-d");


    $trend = db::select("select date.week_date as tgl, date.nama, ng.qty from
        (select cal.week_date, op.nama from
        (select week_date from weekly_calendars
        where week_date >= '".$datefrom."'
        and week_date <= '".$dateto."') cal
        cross join
        (select DISTINCT op.nama from detail_bensukis d
        left join header_bensukis h on h.id = d.id_bensuki
        left join pn_operators op on op.nik = h.nik_op_plate
        where date(d.created_at) >= '".$datefrom."'
        and date(d.created_at) <= '".$dateto."') op) date
        left join
        (select date(d.created_at) as tgl, op.nama, count(ng) as qty from detail_bensukis d
        left join header_bensukis h on h.id = d.id_bensuki
        left join pn_operators op on op.nik = h.nik_op_plate
        where date(d.created_at) >= '".$datefrom."'
        and date(d.created_at) <= '".$dateto."'
        group by tgl, op.nama) ng
        on date.week_date = ng.tgl and date.nama = ng.nama");

    $op = db::select("select DISTINCT op.nama from detail_bensukis d
        left join header_bensukis h on h.id = d.id_bensuki
        left join pn_operators op on op.nik = h.nik_op_plate
        where date(d.created_at) >= '".$datefrom."'
        and date(d.created_at) <= '".$dateto."'");

    $response = array(
        'status' => true,            
        'trend' => $trend,
        'op' => $op,
    );
    return Response::json($response);
    
}

public function opTunning(Request $request)
{
    $line = $request->get('line');
    // $op = db::select("SELECT pn_operators.nik,kode,id_number,nama from pn_operators 
    //     LEFT JOIN (
    //     SELECT * from pn_code_operators WHERE bagian='tuning'
    //     ) a on pn_operators.nik = a.nik
    //     WHERE pn_operators.bagian='tuning' and pn_operators.line ='".$line."' ");

    $op = db::select("SELECT pn_operators.nik,kode,id_number,nama from pn_operators 
        JOIN (
        SELECT * from pn_code_operators WHERE bagian='tuning'
        ) a on pn_operators.nik = a.nik
        WHERE pn_operators.bagian='tuning'");

    $op2 = db::select("SELECT kode,nik,employees.`name` from pn_code_operators 
        LEFT JOIN employees on pn_code_operators.nik = employees.employee_id
        WHERE pn_code_operators.remark='Fixing';
        ");

    $response = array(
        'status' => true, 
        'opTunning' => $op,
        'opFokies' => $op2,
    );
    return Response::json($response);
}

// ---------------- NG Per RATE


public function totalNgReed(Request $request)
{
    $ng ="";
    $text = "";
    for ($i=1; $i < 38 ; $i++) { 
     $text1 =" IF( FIND_IN_SET('".$i."', reed) != '0',1,0) AS s".$i.", ";
     $text = $text . $text1;

     $ng1 = "SUM(s".$i.")s".$i.", ";
     $ng = $ng . $ng1;
 }

 $op = db::select("SELECT ".$ng." ng from (
    SELECT  ".$text." ng  FROM pn_log_ngs WHERE location='PN_Kensa_awal' 
    and DATE(created_at) ='2019-12-17' GROUP BY tag,ng,reed ORDER BY created_at
    ) a GROUP BY ng
    ");



 $response = array(
    'status' => true, 
    'twxt' => $ng,
    'ngTotal' => $op,
);
 return Response::json($response);
}

public function detailReedTuning(Request $request)
{
    $nama = $request->get('nama');
    $ng = $request->get('ng');
    // $tgl = '';

    $procescode =  $request->get('procescode');

    
    // $before = '';

    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    $before = date('Y-m-d',strtotime('-7 days',strtotime($date_from)));


    if ($procescode =="Tuning") {
       $op = db::select("SELECT ng, SPLIT_STRING(operator,'#',1) as tuning,reed,qty from pn_log_ngs 
        WHERE date(pn_log_ngs.created_at) >= '".$date_from."' and date(pn_log_ngs.created_at) <= '".$date_to."' AND SPLIT_STRING(operator,'#',1) in  (
        SELECT DISTINCT(nik) from pn_operators WHERE bagian='Tuning' and nama ='".$nama."') and ng='".$ng."' and location='PN_Kensa_Awal'
        ");
   }

   else if ($procescode =="Awal") {
      $op = db::select("
          SELECT * from (
          select ng, log.created_by  as tuning, reed, qty from
          (select ng, form_id, qty, reed from pn_log_ngs
          where location = 'PN_Kensa_Akhir'
          and date(created_at) >= '".$date_from."'
          and date(created_at) <= '".$date_to."') ng
          left join
          (SELECT created_by, form_id, created_at from pn_log_proces
          where id in (select MAX(id) id from pn_log_proces WHERE location = 'PN_Kensa_Awal'
          and date(created_at) <= '".$date_to."' 
          and date(created_at) >= '".$before."'
          group by form_id)) log
          on ng.form_id = log.form_id
          ) a  WHERE tuning in (  SELECT DISTINCT(nik) from pn_operators WHERE nama ='".$nama."') and ng='".$ng."'
          ");
  }

  else if ($procescode =="Bentsuki") {
      $op = db::select("
          SELECT
          ng,
          SPLIT_STRING ( operator, '#', 2 ) AS tuning,
          reed,
          qty 
          FROM
          pn_log_ngs 
          WHERE
          pn_log_ngs.location = 'PN_Kensa_Awal' 
          AND date( pn_log_ngs.created_at ) >= '".$date_from."' 
          AND date( pn_log_ngs.created_at ) <= '".$date_to."' 
          AND SPLIT_STRING ( operator, '#', 2 ) = ( SELECT DISTINCT ( nik ) FROM pn_operators WHERE nama = '".$nama."' ) 
          AND ng = '".$ng."'
          ");
  }

  $response = array(
    'status' => true, 
    'twxt' => $ng,
    'ngTotal' => $op,
);
  return Response::json($response);
}

public function totalNgReedSpotWelding(Request $request)
{
    $nama = $request->get('nama');
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');

    $op = db::select("
        select op.nama,ng, h.mesin, h.model, d.posisi  from detail_bensukis d
        left join header_bensukis h on h.id = d.id_bensuki
        left join pn_operators op on op.nik = h.nik_op_plate
        where date(d.created_at) >= '".$date_from."' and date(d.created_at) <= '".$date_to."' and op.nama='".$nama."'
        ");
    $response = array(
        'status' => true, 
        'ngTotal' => $op,
    );
    return Response::json($response);
}

public function indexQaAudit()
{
    $product = DB::SELECT("SELECT materials.material_number,materials.material_description as material_description FROM `ympimis`.`materials` WHERE `origin_group_code` LIKE '%073%' AND `issue_storage_location` LIKE '%PN91%' LIMIT 0,1000");
    $ng_lists = NgList::where('location','kakuning')->where('remark','pianica')->get();
    return view('pianica.index_qa_audit')
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
  $audit = DB::connection('ympimis_2')->SELECT("
    SELECT
                *,
    pn_qa_audits.created_at AS created,
    DATE_FORMAT( date, '%d %M %Y' ) AS check_date 
    FROM
    pn_qa_audits
    WHERE
    date( pn_qa_audits.created_at ) >= ".$first." 
    AND date( pn_qa_audits.created_at ) <= ".$last."");
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
    if (str_contains($request->get('employee_id'),'PI')) {
        $emp = Employee::where('employee_id',$request->get('employee_id'))->first();
    }else{
        $emp = Employee::where('tag',$request->get('employee_id'))->first();
    }
    if (count($emp) > 0) {
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
        $tujuan_upload = 'data_file/pianica/qa_audit';
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

        $empsnc = EmployeeSync::where('employee_id',Auth::user()->username)->first();

        $defect_type = $request->get('defect_type');
        
        $inputreed = $request->get('inputreed');
        $inputtuning = $request->get('inputtuning');
        $inputkensaawal = $request->get('inputkensaawal');
        $inputkensaakhir = $request->get('inputkensaakhir');
        $inputfixing = $request->get('inputfixing');
        $inputassembly = $request->get('inputassembly');
        $inputkakunin = $request->get('inputkakunin');
        $inputpreparecase = $request->get('inputpreparecase');
        $inputassemblycover = $request->get('inputassemblycover');
        $inputkakunincover = $request->get('inputkakunincover');

        if ($defect_type == 'Suara') {
            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Reed Adjustment',
                'employee_id' => explode(' - ', $inputreed)[0],
                'employee_name' => explode(' - ', $inputreed)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Tuning',
                'employee_id' => explode(' - ', $inputtuning)[0],
                'employee_name' => explode(' - ', $inputtuning)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Kensa Awal',
                'employee_id' => explode(' - ', $inputkensaawal)[0],
                'employee_name' => explode(' - ', $inputkensaawal)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Kensa Akhir',
                'employee_id' => explode(' - ', $inputkensaakhir)[0],
                'employee_name' => explode(' - ', $inputkensaakhir)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($defect_type == 'Visual Frame') {
            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Fixing Plate',
                'employee_id' => explode(' - ', $inputfixing)[0],
                'employee_name' => explode(' - ', $inputfixing)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Assembly',
                'employee_id' => explode(' - ', $inputassembly)[0],
                'employee_name' => explode(' - ', $inputassembly)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Kakunin',
                'employee_id' => explode(' - ', $inputkakunin)[0],
                'employee_name' => explode(' - ', $inputkakunin)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($defect_type == 'Case') {
            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Case',
                'employee_id' => explode(' - ', $inputpreparecase)[0],
                'employee_name' => explode(' - ', $inputpreparecase)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        if ($defect_type == 'Cover Lower') {
            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Assembly',
                'employee_id' => explode(' - ', $inputassemblycover)[0],
                'employee_name' => explode(' - ', $inputassemblycover)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $audit = DB::connection('ympimis_2')->table('pn_qa_audits')->insert([
                'date' => $date,
                'product' => $product,
                'auditor_id' => explode(' - ', $auditor)[0],
                'auditor_name' => explode(' - ', $auditor)[1],
                'auditee_id' => $empsnc->employee_id,
                'auditee_name' => $empsnc->name,
                'defect_type' => $defect_type,
                'check_type' => 'Kakunin',
                'employee_id' => explode(' - ', $inputkakunincover)[0],
                'employee_name' => explode(' - ', $inputkakunincover)[1],
                'defect' => $defect,
                'area' => $area,
                'category' => $category,
                'image' => $filename,
                'created_by' => $created_by,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

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

public function indexDisplayQaAudit()
{
  $emp_id = strtoupper(Auth::user()->username);
  $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
  return view('pianica.display.pn_qa_audit')
  ->with('title', 'RESUME AUDIT QA FINISH GOOD PIANICA')
  ->with('title_jp', '')
  ->with('page', 'RESUME AUDIT QA FINISH GOOD PIANICA')
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
$audit =  DB::connection('ympimis_2')->SELECT("
    SELECT
    date,
    product,
    auditor_id,
    auditor_name,
    auditee_id,
    auditee_name,
    GROUP_CONCAT( id ) AS id,
    GROUP_CONCAT( check_type ) AS check_type,
    GROUP_CONCAT( employee_id ) AS employee_id,
    GROUP_CONCAT( employee_name ) AS employee_name,
    GROUP_CONCAT( DISTINCT(reject_reason) ) AS reject_reason,
    GROUP_CONCAT( COALESCE ( counceled_employee, 'Belum' ) ) AS counceled_employee,
    GROUP_CONCAT( COALESCE ( counceled_by, 'Belum' ) ) AS counceled_by,
    GROUP_CONCAT( COALESCE ( counceled_at, 'Belum' ) ) AS counceled_at,
    GROUP_CONCAT( COALESCE ( car_description, 'Belum' ) ) AS car_description,
    GROUP_CONCAT( COALESCE ( car_action_now, 'Belum' ) ) AS car_action_now,
    GROUP_CONCAT( COALESCE ( car_cause, 'Belum' ) ) AS car_cause,
    GROUP_CONCAT( COALESCE ( car_action, 'Belum' ) ) AS car_action,
    GROUP_CONCAT( COALESCE ( car_approver_id, 'Belum' ) ) AS car_approver_id,
    GROUP_CONCAT( COALESCE ( car_approver_name, 'Belum' ) ) AS car_approver_name,
    GROUP_CONCAT( COALESCE ( car_approved_at, 'Belum' ) ) AS car_approved_at,
    GROUP_CONCAT( COALESCE ( car_manager_id, 'Belum' ) ) AS car_manager_id,
    GROUP_CONCAT( COALESCE ( car_manager_name, 'Belum' ) ) AS car_manager_name,
    GROUP_CONCAT( COALESCE ( car_approved_at_manager, 'Belum' ) ) AS car_approved_at_manager,
    defect,
    area,
    category,
    image,
    created_at,
    created_by,
    pn_qa_audits.created_at AS created,
    DATE_FORMAT( date, '%d %b %Y' ) AS check_date 
    FROM
    pn_qa_audits
    WHERE
    date( pn_qa_audits.created_at ) >= ".$first." 
    AND date( pn_qa_audits.created_at ) <= ".$last."
    GROUP BY
    check_date,
    date,
    product,
    auditor_id,
    auditor_name,
    auditee_id,
    auditee_name,
    defect,
    area,
    category,
    image,
    created_at,
    created_by,
    created");

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

public function inputPianicaCounceling(Request $request)
{
    try {
      $id_user = Auth::id();
      $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();
      $param = $request->get('param');
      $id = explode(',',$request->get('id_audit'));
      $counceled_employee = explode(',',$request->get('counceled_employee'));
      $audit_all = [];
      if ($param == 'initial') {
        for ($i=0; $i < count($id); $i++) { 
            $audit = db::connection('ympimis_2')->table('pn_qa_audits')
            ->where('pn_qa_audits.id',$id[$i])
            ->update([
                'counceled_employee' => $counceled_employee[$i],
                'counceled_by' => $emp->employee_id.' - '.$emp->name,
                'counceled_at' => date('Y-m-d H:i:s'),
                'car_description' => $request->get('description_initial'),
                'car_action_now' => $request->get('action_now_initial'),
                'car_cause' => $request->get('cause_initial'),
                'car_action' => $request->get('action_initial'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $audit = db::connection('ympimis_2')->table('pn_qa_audits')->where('pn_qa_audits.id',$id[$i])->first();
            array_push($audit_all, $audit);
        }
        $datas = array('data_email' =>$audit_all,
            'remark' =>'chief', );

        Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_pn_initial'));
    }else{
        for ($i=0; $i < count($id); $i++) { 
            $audit = db::connection('ympimis_2')->table('pn_qa_audits')
            ->where('pn_qa_audits.id',$id[$i])
            ->update([
                'counceled_employee' => $counceled_employee[$i],
                'counceled_by' => $emp->employee_id.' - '.$emp->name,
                'counceled_at' => date('Y-m-d H:i:s'),
                'car_description' => $request->get('description_final'),
                'car_action_now' => $request->get('action_now_final'),
                'car_cause' => $request->get('cause_final'),
                'car_action' => $request->get('action_final'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $audit = db::connection('ympimis_2')->table('pn_qa_audits')->where('pn_qa_audits.id',$id[$i])->first();
            array_push($audit_all, $audit);
        }
        $datas = array('data_email' =>$audit_all,
            'remark' =>'chief', );

        Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_pn_final'));
    }

    $response = array(
      'status' => true,
      'message' => 'Konseling Berhasil'
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

public function printQaAudit($type,$id)
{
  $id = explode(',', $id);
  $audit_all = [];
  for ($i=0; $i < count($id); $i++) { 
      $audit = db::connection('ympimis_2')
      ->table('pn_qa_audits')
      ->where('pn_qa_audits.id',$id[$i])
      ->first();
      array_push($audit_all, $audit);
  }

  $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

  return view('pianica.print_qa_audit')
  ->with('type',$type)
  ->with('id',join(',',$id))
  ->with('audit_all',$audit_all)
  ->with('emp',$emp);
}

public function approveQaAudit($type,$remark,$id)
{
    $id = explode(',', $id);
    $audit_all = [];

    for ($i=0; $i < count($id); $i++) { 
        $audit = db::connection('ympimis_2')->table('pn_qa_audits')
        ->where('pn_qa_audits.id',$id[$i])->first();
        array_push($audit_all, $audit);
    }

    if ($remark == 'chief') {
        if ($type == 'final') {

          if ($audit_all[0]->car_approver_id == null) {
            $audit_all = [];
            for ($i=0; $i < count($id); $i++) { 
               $audit = db::connection('ympimis_2')->table('pn_qa_audits')
               ->where('pn_qa_audits.id',$id[$i])
               ->update([
                'car_approver_id' => strtoupper(Auth::user()->username),
                'car_approver_name' => Auth::user()->name,
                'car_approved_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);

               $audit = db::connection('ympimis_2')->table('pn_qa_audits')
               ->where('pn_qa_audits.id',$id[$i])->first();
               array_push($audit_all, $audit);
           }

           $message = 'CAR '.strtoupper($type).' Telah Disetujui.';

           $datas = array('data_email' =>$audit_all,
            'remark' =>'manager', );

           Mail::to('imbang.prasetyo@music.yamaha.com')
           ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
           ->send(new SendEmail($datas, 'car_pn_final'));
       }else{
        $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
    }
}else{
    if ($audit_all[0]->car_approver_id == null) {
        $audit_all = [];
        for ($i=0; $i < count($id); $i++) { 
           $audit = db::connection('ympimis_2')->table('pn_qa_audits')
           ->where('pn_qa_audits.id',$id[$i])
           ->update([
            'car_approver_id' => strtoupper(Auth::user()->username),
            'car_approver_name' => Auth::user()->name,
            'car_approved_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);

           $audit = db::connection('ympimis_2')->table('pn_qa_audits')
           ->where('pn_qa_audits.id',$id[$i])->first();
           array_push($audit_all, $audit);
       }
       $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
       $datas = array('data_email' =>$audit_all,
        'remark' =>'manager', );

       Mail::to('imbang.prasetyo@music.yamaha.com')
       ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
       ->send(new SendEmail($datas, 'car_pn_initial'));
   }else{
    $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
}
}
}

if ($remark == 'manager') {
    if ($type == 'final') {

      if ($audit_all[0]->car_manager_id == null) {
        $audit_all = [];
        for ($i=0; $i < count($id); $i++) { 
           $audit = db::connection('ympimis_2')->table('pn_qa_audits')
           ->where('pn_qa_audits.id',$id[$i])
           ->update([
            'car_manager_id' => strtoupper(Auth::user()->username),
            'car_manager_name' => Auth::user()->name,
            'car_approved_at_manager' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
       }

       $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
   }else{
    $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
}
}else{
    if ($audit_all[0]->car_manager_id == null) {
        $audit_all = [];
        for ($i=0; $i < count($id); $i++) { 
           $audit = db::connection('ympimis_2')->table('pn_qa_audits')
           ->where('pn_qa_audits.id',$id[$i])
           ->update([
            'car_manager_id' => strtoupper(Auth::user()->username),
            'car_manager_name' => Auth::user()->name,
            'car_approved_at_manager' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
       }
       $message = 'CAR '.strtoupper($type).' Telah Disetujui.';
   }else{
    $message = 'CAR '.strtoupper($type).' Pernah Disetujui.';
}
}
}

$audit_all = [];
for ($i=0; $i < count($id); $i++) { 
    $audit = db::connection('ympimis_2')->table('pn_qa_audits')
    ->where('pn_qa_audits.id',$id[$i])->first();
    array_push($audit_all, $audit);
}

$emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

return redirect('/print/pn/qa_audit/'.$type.'/'.join(',',$id))
->with('type',$type)
->with('id',$id)
->with('audit_all',$audit_all)
->with('status','Approve Berhasil')
->with('emp',$emp);
}

public function rejectQaAudit(Request $request,$id)
{
    $type = $request->input('type');
    $reason = $request->input('reason');

    $id = explode(',', $id);

    $audit_all = [];

    for ($i=0; $i < count($id); $i++) { 
       $audit = db::connection('ympimis_2')->table('pn_qa_audits')
       ->where('pn_qa_audits.id',$id[$i])
       ->update([
        'reject_reason' => $reason,
        'car_approver_id' => null,
        'car_approver_name' => null,
        'car_approved_at' => null,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

       $audit = db::connection('ympimis_2')->table('pn_qa_audits')
       ->where('pn_qa_audits.id',$id[$i])->first();

       array_push($audit_all, $audit);
   }

   $datas = array('data_email' =>$audit_all,
      'remark' =>'chief',
      'reason' =>$reason, );

   if ($type == 'initial') {
    Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
    ->send(new SendEmail($datas, 'car_pn_initial'));
}

if ($type == 'final') {
    Mail::to('eko.prasetyo.wicaksono@music.yamaha.com')
    ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
    ->send(new SendEmail($datas, 'car_pn_final'));
}

$emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

return redirect('/print/pn/qa_audit/'.$type.'/'.join(',',$id))
->with('type',$type)
->with('id',$id)
->with('audit_all',$audit_all)
->with('status','Reject Berhasil')
->with('emp',$emp);
}

public function rejectQaAuditChief(Request $request,$id)
{
    $type = $request->input('type_chief');
    $reason = $request->input('reason_chief');

    $id = explode(',', $id);

    $audit_all = [];

    for ($i=0; $i < count($id); $i++) { 
       $audit = db::connection('ympimis_2')->table('pn_qa_audits')
       ->where('pn_qa_audits.id',$id[$i])
       ->update([
        'reject_reason' => $reason,
        'counceled_employee' => null,
        'counceled_by' => null,
        'counceled_at' => null,
        'car_description' => null,
        'car_action_now' => null,
        'car_cause' => null,
        'car_action' => null,
        'car_approver_id' => null,
        'car_approver_name' => null,
        'car_approved_at' => null,
        'car_manager_id' => null,
        'car_manager_name' => null,
        'car_approved_at_manager' => null,
        'updated_at' => date('Y-m-d H:i:s')
    ]);

       $audit = db::connection('ympimis_2')->table('pn_qa_audits')
       ->where('pn_qa_audits.id',$id[$i])->first();

       array_push($audit_all, $audit);
   }

   $emp = EmployeeSync::where('employee_id',Auth::user()->username)->join('users','users.username','employee_syncs.employee_id')->first();

   return redirect('/print/pn/qa_audit/'.$type.'/'.join(',',$id))
   ->with('type',$type)
   ->with('id',$id)
   ->with('audit_all',$audit_all)
   ->with('status','Reject Berhasil')
   ->with('emp',$emp);
}

public function updatePianicaCounceling(Request $request)
{
  try {
      $id_user = Auth::id();
      $id = explode(',', $request->input('id_audit'));
      for ($i=0; $i < count($id); $i++) { 
          $audit = db::connection('ympimis_2')->table('pn_qa_audits')
          ->where('pn_qa_audits.id',$id[$i])
          ->update([
            'car_approver_id' => Auth::user()->username,
            'car_approver_name' => Auth::user()->name,
            'car_approved_at' => date('Y-m-d H:i:s'),
            'car_description' => $request->get('description'),
            'car_action_now' => $request->get('action_now'),
            'car_cause' => $request->get('cause'),
            'car_action' => $request->get('action'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
      }
      $audit_all = [];
      for ($i=0; $i < count($id); $i++) { 
          $audit = db::connection('ympimis_2')->table('pn_qa_audits')
          ->where('pn_qa_audits.id',$id[$i])->first();
          array_push($audit_all, $audit);
      }

      $datas = array('data_email' =>$audit_all,
          'remark' =>'manager',
          'edit' => 'Edited' );


      if ($request->get('param') == 'initial') {
        Mail::to('imbang.prasetyo@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_pn_initial'));
    }

    if ($request->get('param') == 'final') {
        Mail::to('imbang.prasetyo@music.yamaha.com')
        ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
        ->send(new SendEmail($datas, 'car_pn_final'));
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

public function inputDocumentTrainingQa($type)
{
    if ($type == 'initial') {
        $aclistid = 658;
        $trainer = 'Khoirun Nisak';
    }else{
        $aclistid = 659;
        $trainer = 'Iwan Sugara';
    }
    $id_user = Auth::id();
    $fy = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
    $training = TrainingReport::create([
      'activity_list_id' => $aclistid,
      'department' => 'Educational Instrument (EI) Department',
      'section' => 'Pianica Process Section',
      'product' => 'Pianica',
      'periode' => $fy->fiscal_year,
      'date' => date('Y-m-d'),
      'time' => '00:30:00',
      'trainer' => $trainer,
      'training_title' => 'Training Audit QA Pianica',
      'theme' => 'Training Audit QA Pianica',
      'isi_training' => 'Training Audit QA Pianica',
      'tujuan' => 'Evaluasi Hasil Audit QA Pianica',
      'standard' => '-',
      'leader' => $trainer,
      'foreman' => 'Eko Prasetyo Wicaksono',
      'notes' => '-',
      'remark' => date('Y-m-d H:i:s'),
      'created_by' => $id_user
  ]);
    $id = $training->id;

    return redirect('index/training_report/details/'.$id.'/'.$type)
    ->with('page', 'Training Report')->with('status', 'Training Berhasil Dibuat.')->with('session_training',$type);
}

public function indexDiplayPnPart()
{
    $title = 'Quality Monitoring PN Parts';
    $title_jp = '';
    return view('pianica.display.pn_incoming_check')->with('page', 'Monitoring PN Part')->with('title', $title)->with('title_jp', $title_jp);
}

public function fetchDiplayPnPart(Request $request)
{
    if ($request->get('tanggal') == '') {
        $tanggal = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime("-1 days"));
        if (date('w') == 1) {
            $yesterday = date('Y-m-d', strtotime('-3 days'));
        }
        $tgl = date('d M Y');
    } else {
        $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        $yesterday = date('Y-m-d', strtotime($tanggal. ' -1 days'));
        if (date('w', strtotime($tanggal)) == 1) {
            $yesterday = date('Y-m-d', strtotime($tanggal. ' -3 days'));
        }
        
        $tgl = date('d M Y', strtotime($request->get('tanggal')));
    }

    $datas = db::select('SELECT ng.nik_op_plate, employee_syncs.name, IFNULL(ng.jml_ng, 0) as jml_ng from
        (SELECT nik_op_plate, SUM(qty) as jml_ng from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") = "'.$tanggal.'"
        group by nik_op_plate) ng
        left join employee_syncs on ng.nik_op_plate = employee_syncs.employee_id
        order by jml_ng asc');

    $datas_detail =  db::select('SELECT nik_op_plate, employee_syncs.name, kode_reed, ng, qty from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        left join employee_syncs on header_bensukis.nik_op_plate = employee_syncs.employee_id
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") = "'.$tanggal.'" AND qty is not null');

    $datas_ng = db::select('SELECT ng, SUM(IFNULL(qty,0)) as jml_ng from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") = "'.$tanggal.'"
        group by ng');

    $harian = db::select('SELECT ng.nik_op_plate, employee_syncs.name, IFNULL(ng.jml_ng, 0) as jml_ng from
        (SELECT nik_op_plate, SUM(qty) as jml_ng from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") = "'.$yesterday.'"
        group by nik_op_plate) ng
        left join employee_syncs on ng.nik_op_plate = employee_syncs.employee_id
        order by jml_ng asc');

    $ng_harian = db::select('SELECT ng.nik_op_plate, kode_reed, ng, employee_syncs.name, IFNULL(ng.jml_ng, 0) as jml_ng from
        (SELECT nik_op_plate, kode_reed, ng, SUM(qty) as jml_ng from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") = "'.$yesterday.'"
        group by nik_op_plate, kode_reed, ng) ng
        left join employee_syncs on ng.nik_op_plate = employee_syncs.employee_id
        where nik_op_plate = "'.$harian[count($harian)-1]->nik_op_plate.'"
        order by jml_ng desc
        limit 1');

    $week = db::select('SELECT week_date from weekly_calendars where week_name = CONCAT("W",(SELECT REGEXP_SUBSTR(week_name,"[0-9]+") as wk FROM weekly_calendars where week_date = DATE_FORMAT("'.$tanggal.'","%Y-%m-%d")) -1)
        and fiscal_year = (SELECT fiscal_year from weekly_calendars where week_date = DATE_FORMAT(now(),"%Y-%m-%d"))
        order by week_date asc');

    $mingguan = db::select('SELECT ng.nik_op_plate, employee_syncs.name, IFNULL(ng.jml_ng,0) jml_ng, "'.$week[0]->week_date.'" as `from`, "'.$week[count($week)-1]->week_date.'" as `to` from
        (SELECT nik_op_plate, SUM(qty) as jml_ng from header_bensukis 
        LEFT JOIN detail_bensukis on header_bensukis.id = detail_bensukis.id_bensuki
        where DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") >= "'.$week[0]->week_date.'"
        AND DATE_FORMAT(header_bensukis.created_at,"%Y-%m-%d") <= "'.$week[count($week)-1]->week_date.'"
        group by nik_op_plate) ng
        left join employee_syncs on ng.nik_op_plate = employee_syncs.employee_id
        order by jml_ng asc');

    $op = db::select('SELECT nik, nama from pn_operators where bagian = "bennuki"');

    $training = NgLogBensuki::get();

    $response = array(
        'status' => true,
        'datas' => $datas,
        'datas_detail' => $datas_detail,
        'datas_ng' => $datas_ng,
        'harian' => $harian,
        'ng_harian' => $ng_harian,
        'mingguan' => $mingguan,
        'operator' => $op,
        'training' => $training,
        'tgl' => $tgl
    );

    return Response::json($response);
}

public function inputPnPartCounceling(Request $request)
{
    try {
        $emp = explode('-', $request->input('counceled_employee'));
        $leader = explode('-', $request->input('counceled_by'));

        $part2 = new NgLogBensuki([
            'employee_id' => $emp[0],
            'name' => $emp[1],
            'total_ng' => $request->get('total_ng'),
            'period_from' => $request->get('first_date'),
            'period_to' => $request->get('last_date'),
            'trainer_id' => $leader[0],
            'trainer_name' => $leader[1],
            'status' => 'TRAINED',
            'trained_at' => date('Y-m-d'),
            'created_by' => Auth::user()->username
        ]);
        $part2->save();

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

public function indexDisplayKensaAwal()
{
    $title = 'Quality Monitoring NG Bensuki & Tuning';
    $title_jp = '';
    return view('pianica.display.pn_ng_kensa_awal')->with('page', 'Monitoring Pianica')->with('title', $title)->with('title_jp', $title_jp);
}

public function fetchDisplayKensaAwal(Request $request)
{
    if ($request->get('tanggal') == '' || $request->get('tanggal') == null) {
        $now = date('Y-m-d');
    }else{
        $now = $request->get('tanggal');
    }
    $yesterday = date('Y-m-d',strtotime($now.' -1 days'));
    $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars` order by week_date desc");
    foreach ($weekly_calendars as $key) {
        if ($key->week_date == $yesterday) {
            if ($key->remark == 'H') {
                $yesterday = date('Y-m-d', strtotime($yesterday.'-1 days'));
            }
        }
    }
    $line = '';
    $line_pureto = '';
    $line_op = '';
    if ($request->get('line') != '' || $request->get('line') != null) {
        $line = "where ngs.line = '".str_replace('Line ', '', $request->get('line'))."'" ;
        $line_op = "where line = '".str_replace('Line ', '', $request->get('line'))."'" ;
        $line_pureto = "where pureto.line = '".str_replace('Line ', '', $request->get('line'))."'" ;
    }           


    $data_ng_bensuki = db::select("SELECT op_ben.operator as op_ben, pn_operators.nama as name_ben, ng, SUM(ngs.qty) as jml from 
        (SELECT form_id, ng, 1 as qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$now."' and location = 'PN_Kensa_Awal' and ng <> '102' group by form_id, ng) as ngs join
        (select form_id, operator, qty from pn_log_proces where location = 'PN_Pureto') as op_ben on op_ben.form_id = ngs.form_id
        left join pn_operators on op_ben.operator = pn_operators.nik
        where ngs.qty is not null
        and op_ben.operator in (select operator_id from pn_operator_logs where date = '".$now."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and (process_name = 'Bensuki' or process_name = 'Benage') and deleted_at is null)
        group by op_ben.operator, nama, ng");


    $data_ng_tuning = db::select("SELECT operator_id as op_tuning, operator_name as name_tuning, ng, sum(qty) as jml from
        (select operator_id, operator_name from pn_operator_logs where date = '".$now."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and process_name = 'Tuning' and deleted_at is null) as op
        LEFT JOIN 
        (SELECT form_id, SPLIT_STRING(operator, '#', 1) as op_tuning, ng, qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$now."' and location = 'PN_Kensa_Awal') as tuning
        on op.operator_id = tuning.op_tuning
        group by operator_id, operator_name, ng");


    // $data_ng_now = [];
    // foreach ($data_ng_tuning as $tuning) {
    //     $stat_bensuki = false;
    //     $ng = [];
    //     $ng['ng'] = $tuning->ng;
    //     $ng['op_tuning'] = $tuning->op_tuning;
    //     $ng['nama_tuning'] = $tuning->nama_tuning;
    //     $ng['qty'] = $tuning->qty;

    //     foreach ($data_ng_bensuki as $bensuki) {
    //         if ($tuning->form_id == $bensuki->form_id) {
    //             $stat_bensuki = true;
    //             $ng['op_bensuki'] = $bensuki->op_bensuki;
    //             $ng['nama_bensuki'] = $bensuki->nama_bensuki;
    //         }
    //     }

    //     if (!$stat_bensuki) {
    //         $ng['op_bensuki'] = '';
    //         $ng['nama_bensuki'] = '';
    //     }

    //     array_push($data_ng_now, $ng);
    // }

    

    // $ng_bensuki_yesterday = db::select("SELECT op_ben.operator as op_ben, pn_operators.nama as name_ben, ng, SUM(ngs.qty) as jml from (select form_id, operator, qty from pn_log_proces where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$now."' and location = 'PN_Pureto' and line = '".str_replace('Line ', '', $request->get('line'))."') as op_ben left join
    //     (SELECT form_id, ng, 1 as qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$now."' and location = 'PN_Kensa_Awal' group by form_id, ng) as ngs on op_ben.form_id = ngs.form_id
    //     left join pn_operators on op_ben.operator = pn_operators.nik
    //     where ngs.qty is not null
    //     group by op_ben.operator, nama, ng");

    $ng_bensuki_yesterday = db::select("SELECT op_ben.operator as op_ben, pn_operators.nama as name_ben, SUM(ngs.qty) as jml from 
        (SELECT form_id, ng, 1 as qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$yesterday."' and location = 'PN_Kensa_Awal' and ng <> '102' group by form_id, ng) as ngs join
        (select form_id, operator, qty from pn_log_proces where location = 'PN_Pureto') as op_ben on op_ben.form_id = ngs.form_id
        left join pn_operators on op_ben.operator = pn_operators.nik
        where ngs.qty is not null
        and op_ben.operator in (select operator_id from pn_operator_logs where date = '".$yesterday."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and (process_name = 'Bensuki' or process_name = 'Benage') and deleted_at is null)
        group by op_ben.operator, nama
        order by jml asc");


    // $ng_tuning_yesterday = db::select("SELECT operator_id as op_tuning, operator_name as name_tuning, ng, sum(qty) as jml from
    //     (select operator_id, operator_name from pn_operator_logs where date = '".$now."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."') as op
    //     LEFT JOIN 
    //     (SELECT form_id, SPLIT_STRING(operator, '#', 1) as op_tuning, ng, qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$now."' and location = 'PN_Kensa_Awal') as tuning
    //     on op.operator_id = tuning.op_tuning
    //     group by operator_id, operator_name, ng");

    $ng_tuning_yesterday = db::select("SELECT operator_id as op_tuning, operator_name as name_tuning, sum(qty) as jml from
        (select operator_id, operator_name from pn_operator_logs where date = '".$yesterday."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and process_name = 'Tuning' and deleted_at is null) as op
        LEFT JOIN 
        (SELECT form_id, SPLIT_STRING(operator, '#', 1) as op_tuning, ng, qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$yesterday."' and location = 'PN_Kensa_Awal') as tuning
        on op.operator_id = tuning.op_tuning
        group by operator_id, operator_name
        order by jml asc");

    // $data_ng_yesterday = [];
    // foreach ($ng_tuning_yesterday as $tuning2) {
    //     $stat_bensuki2 = false;
    //     $ng = [];
    //     $ng['ng'] = $tuning2->ng;
    //     $ng['op_tuning'] = $tuning2->op_tuning;
    //     $ng['nama_tuning'] = $tuning2->nama_tuning;
    //     $ng['qty'] = $tuning2->qty;

    //     foreach ($ng_bensuki_yesterday as $bensuki2) {
    //         if ($tuning2->form_id == $bensuki2->form_id) {
    //             $stat_bensuki2 = true;
    //             $ng['op_bensuki'] = $bensuki2->op_bensuki;
    //             $ng['nama_bensuki'] = $bensuki2->nama_bensuki;
    //         }
    //     }

    //     if (!$stat_bensuki2) {
    //         $ng['op_bensuki'] = '';
    //         $ng['nama_bensuki'] = '';
    //     }

    //     array_push($data_ng_yesterday, $ng);
    // }

    // $out_tuning = array();
    // foreach ($data_ng_yesterday as $row) {
    //     if (!isset($out_tuning[$row['op_tuning']])) {
    //         $out_tuning[$row['op_tuning']] = array(
    //             'op_tuning' => $row['op_tuning'],
    //             'qty' => 0,
    //         );
    //     }
    //     $out_tuning[$row['op_tuning']]['qty'] += $row['qty'];
    // }

    // $out_tuning = array_values($out_tuning);

    // usort($out_tuning, function($a, $b) {
    //     return $a['qty'] - $b['qty'];
    // });

    // $out_bensuki = array();
    // foreach ($data_ng_yesterday as $row) {
    //     if ($row['op_bensuki'] != '') {
    //         if (!isset($out_bensuki[$row['op_bensuki']])) {
    //             $out_bensuki[$row['op_bensuki']] = array(
    //                 'op_bensuki' => $row['op_bensuki'],
    //                 'qty' => 0,
    //             );
    //         }
    //         $out_bensuki[$row['op_bensuki']]['qty'] += $row['qty'];
    //     }
    // }

    // $out_bensuki = array_values($out_bensuki);

    // usort($out_bensuki, function($a, $b) {
    //     return $a['qty'] - $b['qty'];
    // });


    $week = db::select("SELECT
        week_date 
        FROM
        weekly_calendars 
        WHERE
        week_name = CONCAT( 'W',( SELECT REPLACE ( week_name, 'W', '' ) AS wk FROM weekly_calendars WHERE week_date = DATE_FORMAT( '".$now."', '%Y-%m-%d' )) ) 
        AND fiscal_year = (
        SELECT
        fiscal_year 
        FROM
        weekly_calendars 
        WHERE
        week_date = DATE_FORMAT( now(), '%Y-%m-%d' )) 
        ORDER BY
        week_date ASC");

    $week_tuning = db::select("SELECT operator_id as op_tuning, operator_name as name_tuning, sum(qty) as jml from
        (select operator_id, operator_name from pn_operator_logs where date >= '".$week[0]->week_date."' and date <= '".$week[count($week)-1]->week_date."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and process_name = 'Tuning' and deleted_at is null) as op
        LEFT JOIN 
        (SELECT form_id, SPLIT_STRING(operator, '#', 1) as op_tuning, ng, qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$week[0]->week_date."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$week[count($week)-1]->week_date."' and location = 'PN_Kensa_Awal') as tuning
        on op.operator_id = tuning.op_tuning
        group by operator_id, operator_name
        order by jml asc");

    $week_bensuki = db::select("SELECT op_ben.operator as op_ben, pn_operators.nama as name_ben, SUM(ngs.qty) as jml from 
        (SELECT form_id, ng, 1 as qty from pn_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') >= '".$week[0]->week_date."' and DATE_FORMAT(created_at,'%Y-%m-%d') <= '".$week[count($week)-1]->week_date."' and location = 'PN_Kensa_Awal' and ng <> '102' group by form_id, ng) as ngs join
        (select form_id, operator, qty from pn_log_proces where location = 'PN_Pureto') as op_ben on op_ben.form_id = ngs.form_id
        left join pn_operators on op_ben.operator = pn_operators.nik
        where ngs.qty is not null
        and op_ben.operator in (select operator_id from pn_operator_logs where date = '".$yesterday."' and SPLIT_STRING(line, ' ', 2) = '".str_replace('Line ', '', $request->get('line'))."' and (process_name = 'Bensuki' or process_name = 'Benage') and deleted_at is null)
        group by op_ben.operator, nama
        order by jml asc");

    // $data_ng_week = [];
    // foreach ($week_tuning as $week_tun) {
    //     if ($week_tun->op_tuning != '') {
    //         $stat_bensuki_week = false;
    //         $ng = [];
    //         $ng['ng'] = $week_tun->ng;
    //         $ng['op_tuning'] = $week_tun->op_tuning;
    //         $ng['nama_tuning'] = $week_tun->nama_tuning;
    //         $ng['qty'] = $week_tun->qty;

    //         foreach ($week_bensuki as $week_ben) {
    //             if ($week_tun->form_id == $week_ben->form_id) {
    //                 $stat_bensuki_week = true;
    //                 $ng['op_bensuki'] = $week_ben->op_bensuki;
    //                 $ng['nama_bensuki'] = $week_ben->nama_bensuki;
    //             }
    //         }

    //         if (!$stat_bensuki_week) {
    //             $ng['op_bensuki'] = '';
    //             $ng['nama_bensuki'] = '';
    //         }

    //         array_push($data_ng_week, $ng);
    //     }
    // }

    // --- WEEK TUNING --
    // $week_tuning = array();
    // foreach ($data_ng_week as $row) {
    //     if (!isset($week_tuning[$row['op_tuning']])) {
    //         $week_tuning[$row['op_tuning']] = array(
    //             'op_tuning' => $row['op_tuning'],
    //             'qty' => 0,
    //         );
    //     }
    //     $week_tuning[$row['op_tuning']]['qty'] += $row['qty'];
    // }

    // $week_tuning = array_values($week_tuning);

    // usort($week_tuning, function($a, $b) {
    //     return $a['qty'] - $b['qty'];
    // });

    // --- WEEK BENSUKI --
    // $week_bensuki = array();
    // foreach ($data_ng_week as $row) {
    //     if ($row['op_bensuki'] != '') {
    //         if (!isset($week_bensuki[$row['op_bensuki']])) {
    //             $week_bensuki[$row['op_bensuki']] = array(
    //                 'op_bensuki' => $row['op_bensuki'],
    //                 'qty' => 0,
    //             );
    //         }
    //         $week_bensuki[$row['op_bensuki']]['qty'] += $row['qty'];
    //     }
    // }

    // $week_bensuki = array_values($week_bensuki);

    // usort($week_bensuki, function($a, $b) {
    //     return $a['qty'] - $b['qty'];
    // });

    $data_op = db::select("SELECT nik, nama, bagian from pn_operators where bagian in ('Bensuki','Benage','Tuning')");
    $data_op_login = PnOperatorLog::where('line', '=',  $request->get('line'))
    ->where('date', '=', $now)
    ->get();

    $firstdayweek = '';
    $lastdayweek = '';

    $firstweek = DB::SELECT("SELECT
      week_date 
      FROM
      weekly_calendars 
      WHERE
      week_name = ( SELECT week_name FROM weekly_calendars WHERE week_date = '".$now."' - INTERVAL 7 DAY ) 
      AND fiscal_year = (
      SELECT
      fiscal_year 
      FROM
      weekly_calendars 
      WHERE
      week_date = '".$now."') 
      ORDER BY
      week_date ASC 
      LIMIT 1 ");

    foreach ($firstweek as $key) {
      $firstdayweek = $key->week_date;
  }

  $lastweek = DB::SELECT("SELECT
      week_date 
      FROM
      weekly_calendars 
      WHERE
      week_name = ( SELECT week_name FROM weekly_calendars WHERE week_date = '".$now."' - INTERVAL 7 DAY ) 
      AND fiscal_year = (
      SELECT
      fiscal_year 
      FROM
      weekly_calendars 
      WHERE
      week_date = DATE( NOW() )) 
      ORDER BY
      week_date DESC 
      LIMIT 1 ");

  foreach ($lastweek as $key) {
      $lastdayweek = $key->week_date;
  }

  // $op_training = [];

  // array_push($op_training, $week_tuning[count($week_tuning)-1]['op_tuning']);
  // array_push($op_training, $week_bensuki[count($week_bensuki)-1]['op_bensuki']);

    // $training_ops = TrainingReport::leftJoin('training_participants', 'training_reports.id', '=', 'training_participants.training_id')
    // ->where('date', '>=', $firstdayweek)
    // ->where('date', '<=', $lastdayweek)
    // ->where('theme', '=', 'Sosialisasi NG Tertinggi')
    // ->where('section', '=', 'Pianica Process Section')
    // ->whereIn('participant_id', $op_training)
    // ->select('participant_id', 'training_reports.id')
    // ->get();

  $training_ops = db::table('pn_training_logs')
  ->whereIn('category', ['NG Tinggi Tuning', 'NG Tinggi Bensuki'])
  // ->whereIn('employee_id', $op_training)
  ->where('period_from', '>=', $firstdayweek)
  ->where('period_to', '<=', $lastdayweek)
  ->select('employee_id', 'period_from', 'period_to')
  ->get();


  $response = array(
    'status' => true,
    'data_ng_bensuki' => $data_ng_bensuki,
    'data_ng_tuning' => $data_ng_tuning,
    'yesterday_tuning' => $ng_tuning_yesterday,
    'yesterday_bensuki' => $ng_bensuki_yesterday,
    'data_op' => $data_op,
    'week_tuning' => $week_tuning,
    'week_bensuki' => $week_bensuki,
    'line' => $request->get('line'),
    'training_week' => $training_ops,
    'firstdayweek' => $firstdayweek,
    'lastdayweek' => $lastdayweek,
    'op_login' => $data_op_login
);
  return Response::json($response);
}

public function inputKensaAwalCounceling(Request $request)
{
    try {
        $emp = explode('-', $request->input('counceled_employee'));
        $leader = explode('-', $request->input('counceled_by'));

        DB::table('pn_training_logs')->insert([
            'employee_id' => $emp[0],
            'name' => $emp[1],
            'total_ng' => $request->get('total_ng'),
            'category' => $request->get('category'),
            'period_from' => $request->get('first_date'),
            'period_to' => $request->get('last_date'),
            'trainer_id' => $leader[0],
            'trainer_name' => $leader[1],
            'remark' => 'TRAINED',
            'training_date' => date('Y-m-d'),
            'created_by' => Auth::user()->username,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);



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

public function inputKensaAwalDocument()
{
  $id_user = Auth::id();
  $fy = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
  $training = TrainingReport::create([
    'activity_list_id' => 651,
    'department' => 'Educational Instrument (EI) Department',
    'section' => 'Pianica Process Section',
    'product' => 'Pianica',
    'periode' => $fy->fiscal_year,
    'date' => date('Y-m-d'),
    'time' => '00:30:00',
    'trainer' => 'Iwan Sugara',
    'theme' => 'Training NG Rate Operator Pianica',
    'isi_training' => 'Training NG Rate Operator Pianica',
    'tujuan' => 'Evaluasi Hasil NG Per Operator Pianica',
    'standard' => '-',
    'leader' => 'Iwan Sugara',
    'foreman' => 'Eko Prasetyo Wicaksono',
    'notes' => '-',
    'remark' => date('Y-m-d H:i:s'),
    'created_by' => $id_user
]);
  $id = $training->id;

  return redirect('index/training_report/details/'.$id.'/pianica')
  ->with('page', 'Training Report')->with('status', 'Training Berhasil Dibuat.')->with('session_training','pianica');
}

public function checkTag(Request $request)
{
    $tags = PnTag::where('tag', '=', $request->get('tag'))->get();

    $response = array(
        'status' => true,
        'tag' => count($tags),
        'started_at' => date('Y-m-d H:i:s')
    );
    return Response::json($response);
}

public function fetchOPTuning(Request $request)
{
    $log = PnOperatorLog::where('line', '=', str_replace('+', ' ', $request->get('line')))
    ->where('date', '=', date('Y-m-d'))
    ->get();

    $response = array(
        'status' => true,
        'data_log' => $log,
    );
    return Response::json($response);
}

public function inputOPTuning(Request $request)
{
    $emp = Employee::where('employee_id', '=', $request->get('id'))
    ->orWhere('tag', '=', $request->get('id'))
    ->whereNull('end_date')
    ->first();

    if (count($emp) > 0) {
        $cek_op = PnOperatorLog::where('operator_id', '=', $emp->employee_id)
        ->where('date', '=', date('Y-m-d'))
        ->first();

        if (count($cek_op) > 0) {
            $response = array(
                'status' => false,
                'message' => 'Karyawan sudah Cek In pada '.$cek_op->line,
            );
            return Response::json($response);
        } else {
            $op = PnOperator::where('nik', '=', $emp->employee_id)->select('bagian')->first();

            if (count($op) > 0) {
                $bagian = $op->bagian;
            } else {
                $bagian = '';
            }

            $insert = new PnOperatorLog();

            $insert->operator_id = $emp->employee_id;
            $insert->operator_name = $emp->name;
            $insert->process_name = $bagian;
            $insert->date = date('Y-m-d');
            $insert->line = str_replace('+', ' ', $request->get('line'));
            $insert->created_by = Auth::user()->username;

            $insert->save();

            $response = array(
                'status' => true,
                'datas' => $insert
            );
            return Response::json($response);
        }

    } else {
        $response = array(
            'status' => false,
            'message' => 'Karyawan tidak terdaftar',
        );
        return Response::json($response);
    }
}

public function deleteOPTuning(Request $request)
{
    PnOperatorLog::where('operator_id', '=', $request->get('operator_id'))
    ->where('date', '=', date('Y-m-d'))
    ->delete();
}

public function inputAuditScrew(Request $request)
{
    try {
        $screw_counter = $request->get('screw_counter');
        $screw_system = $request->get('screw_system');
        $screw_ng = $request->get('screw_ng');
        $auditor_id = $request->get('auditor_id');
        $auditor_name = $request->get('auditor_name');
        $line = $request->get('line');

        $input = DB::connection('ympimis_2')->table('pn_screw_audits')->insert([
            'screw_counter' => $screw_counter,
            'screw_system' => $screw_system,
            'screw_ng' => $screw_ng,
            'auditor_id' => $auditor_id,
            'auditor_name' => $auditor_name,
            'line' => $line,
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $audit_detail = array(
            'screw_counter' => $screw_counter,
            'screw_system' => $screw_system,
            'screw_ng' => $screw_ng,
            'auditor_id' => $auditor_id,
            'auditor_name' => $auditor_name,
            'line' => $line,
            'created_at' => date('Y-m-d H:i:s'),
        );

        $diff = $screw_counter - $screw_ng;

        $datas = array(
            'audit' => $audit_detail,
            'statuses' => 'Unbalance',
            'hour' => null,
            'diff' => $screw_counter - $screw_ng - $screw_system
        );

        $mail_to = [];
        array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');
        array_push($mail_to, 'imbang.prasetyo@music.yamaha.com');

        if ($screw_system != $diff) {
            Mail::to($mail_to)
            ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','nasiqul.ibat@music.yamaha.com'])
            ->send(new SendEmail($datas, 'audit_screw'));
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

public function indexReportAuditScrew()
{
    return view('pianica.report_audit_screw')
    ->with('title', 'Report Audit Screw')
    ->with('title_jp', '')
    ->with('page', 'Report Audit Screw');
}

public function fetchReportAuditScrew()
{
    try {
        $audit = DB::connection('ympimis_2')->table('pn_screw_audits')->orderby('created_at','desc')->get();
        $response = array(
            'status' => true,
            'audit' => $audit
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

public function indexStockMonitoring()
{
    $target = DB::SELECT("SELECT
        sum( quantity ) AS qty 
        FROM
        production_schedules
        JOIN materials ON materials.material_number = production_schedules.material_number 
        WHERE
        due_date = DATE(
        NOW()) 
        AND origin_group_code = '073' 
        AND category = 'FG'");
    return view('pianica.display.pn_stock_monitoring')
    ->with('title', 'Pianica Stock Monitoring')
    ->with('title_jp', '')
    ->with('target',$target[0]->qty)
    ->with('emp',strtoupper(Auth::user()->username))
    ->with('page', 'Pianica Stock Monitoring');
}

public function inputTarget(Request $request)
{
    try {
        $line = $request->get('line');
        $target = $request->get('target');

        $updateline = DB::connection('ympimis_2')->table('pn_targets')->where('line',$line)->update([
            'status' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $updateline = DB::connection('ympimis_2')->table('pn_targets')->where('line',$line)->update([
            'status' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);

        $inputline = DB::connection('ympimis_2')->table('pn_targets')->insert([
            'target' => $target,
            'line' => $line,
            'status' => 'Active',
            'created_by' => Auth::user()->id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
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

public function fetchStockMonitoring(Request $request)
{
    try {
        $first = date('Y-m-d');
        $last = date('Y-m-d');

        $first_ideal = date('Y-m-d',strtotime(' - 3 MONTH'));
        $last_ideal = date('Y-m-d');

        $stock = DB::SELECT("SELECT
            a.model,
            sum( pureto ) AS pureto,
            sum( kensa_awal ) AS kensa_awal,
            sum( assembly ) AS assembly,
            sum( kensa_akhir ) AS kensa_akhir 
            FROM
            (
            SELECT
            model,
            count( tag ) AS pureto,
            0 AS kensa_awal,
            0 AS assembly,
            0 AS kensa_akhir 
            FROM
            pn_tags 
            WHERE
            position = 'PN_Pureto' 
            GROUP BY
            model UNION ALL
            SELECT
            model,
            0 AS pureto,
            count( tag ) AS kensa_awal,
            0 AS assembly,
            0 AS kensa_akhir 
            FROM
            pn_tags 
            WHERE
            position = 'PN_Kensa_Awal' 
            GROUP BY
            model UNION ALL
            SELECT
            model,
            0 AS pureto,
            0 AS kensa_awal,
            0 AS assembly,
            count( tag ) AS kensa_akhir 
            FROM
            pn_tags 
            WHERE
            position = 'PN_Kensa_Akhir' 
            GROUP BY
            model UNION ALL
            SELECT
            model,
            0 AS pureto,
            0 AS kensa_awal,
            count( tag ) AS assembly,
            0 AS kensa_akhir 
            FROM
            pn_tags 
            WHERE
            position = 'PN_Assembly' 
            GROUP BY
            model 
            ) a 
            GROUP BY
            a.model");

        $timing = DB::SELECT("SELECT
            c.model,
            ROUND(AVG( c.minutes ),1) AS avg 
            FROM
            (
            SELECT
            b.form_id,
            b.model,
            IF
            (
            IF
            (
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ) > 460,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )- 960,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )) > 460,
            460,
            IF
            (
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ) > 460,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )- 960,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ))) AS minutes 
            FROM
            (
            SELECT
            a.form_id,
            a.model,
            GROUP_CONCAT( a.awal ) AS awal,
            GROUP_CONCAT( a.akhir ) AS akhir 
            FROM
            (
            SELECT DISTINCT
            ( form_id ),
            model,
            created_at AS awal,
            NULL AS akhir 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) >= '".$first."' 
            AND date( created_at ) <= '".$last."' 
            AND form_id IS NOT NULL 
            AND form_id != '' 
            AND location = 'PN_Pureto' UNION ALL
            SELECT DISTINCT
            ( form_id ),
            model,
            NULL AS awal,
            created_at AS akhir 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) >= '".$first."' 
            AND date( created_at ) <= '".$last."' 
            AND form_id IS NOT NULL 
            AND form_id != '' 
            AND location = 'PN_Kakuning_Visual' 
            ) a 
            GROUP BY
            form_id,
            model 
            ) b 
            WHERE
            b.awal IS NOT NULL 
            AND b.akhir IS NOT NULL 
            ) c 
            GROUP BY
            c.model");

        $timing_ideal = DB::SELECT("SELECT
            c.model,
            ROUND(AVG( c.minutes ),1) AS avg 
            FROM
            (
            SELECT
            b.form_id,
            b.model,
            IF
            (
            IF
            (
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ) > 460,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )- 960,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )) > 460,
            460,
            IF
            (
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ) > 460,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir )- 960,
            TIMESTAMPDIFF( MINUTE, b.awal, b.akhir ))) AS minutes 
            FROM
            (
            SELECT
            a.form_id,
            a.model,
            GROUP_CONCAT( a.awal ) AS awal,
            GROUP_CONCAT( a.akhir ) AS akhir 
            FROM
            (
            SELECT DISTINCT
            ( form_id ),
            model,
            created_at AS awal,
            NULL AS akhir 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) >= '".$first_ideal."' 
            AND date( created_at ) <= '".$last_ideal."' 
            AND form_id IS NOT NULL 
            AND form_id != '' 
            AND location = 'PN_Pureto' UNION ALL
            SELECT DISTINCT
            ( form_id ),
            model,
            NULL AS awal,
            created_at AS akhir 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) >= '".$first_ideal."' 
            AND date( created_at ) <= '".$last_ideal."' 
            AND form_id IS NOT NULL 
            AND form_id != '' 
            AND location = 'PN_Kakuning_Visual' 
            ) a 
            GROUP BY
            form_id,
            model 
            ) b 
            WHERE
            b.awal IS NOT NULL 
            AND b.akhir IS NOT NULL 
            ) c 
            GROUP BY
            c.model");

        $tact = DB::select("SELECT
            line,
            ROUND( ( TIMESTAMPDIFF( SECOND, GROUP_CONCAT( started_at ), GROUP_CONCAT( finished_at )))/ 60, 2 ) AS diff 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) = DATE(
            NOW()) 
            AND location = 'PN_Kakuning_Visual' 
            GROUP BY
            line");

        $person_tuning = DB::SELECT("SELECT
            process_name,
            count( operator_id ) AS qty 
            FROM
            `pn_operator_logs` 
            WHERE
            date = DATE(
            NOW()) 
            AND process_name = 'Tuning' 
            GROUP BY
            process_name");

        $person_all = DB::SELECT("SELECT
            location,
            count(
            DISTINCT ( created_by )) AS qty 
            FROM
            pn_log_proces 
            WHERE
            DATE( created_at ) = DATE(
            NOW()) 
            GROUP BY
            location");

        $stock_frame = DB::connection('ympimis_2')->select("SELECT
            model,
            quantity 
            FROM
            `pn_stocks`");

        $tact_pureto = DB::SELECT("SELECT
            line,
            ROUND( ( TIMESTAMPDIFF( SECOND, GROUP_CONCAT( started_at ), GROUP_CONCAT( finished_at )))/ 60, 2 ) AS diff 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) = DATE(
            NOW()) 
            AND location = 'PN_Pureto' 
            GROUP BY
            line");

        $tact_kensa_awal = DB::SELECT("SELECT
            line,
            ROUND( ( TIMESTAMPDIFF( SECOND, GROUP_CONCAT( started_at ), GROUP_CONCAT( finished_at )))/ 60, 2 ) AS diff 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) = DATE(
            NOW()) 
            AND location = 'PN_Kensa_Awal' 
            GROUP BY
            line");

        $tact_assembly = DB::SELECT("SELECT
            line,
            ROUND( ( TIMESTAMPDIFF( SECOND, GROUP_CONCAT( started_at ), GROUP_CONCAT( finished_at )))/ 60, 2 ) AS diff 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) = DATE(
            NOW()) 
            AND location = 'PN_Assembly' 
            GROUP BY
            line");

        $tact_kensa_akhir = DB::SELECT("SELECT
            line,
            ROUND( ( TIMESTAMPDIFF( SECOND, GROUP_CONCAT( started_at ), GROUP_CONCAT( finished_at )))/ 60, 2 ) AS diff 
            FROM
            pn_log_proces 
            WHERE
            date( created_at ) = DATE(
            NOW()) 
            AND location = 'PN_Kensa_Akhir' 
            GROUP BY
            line");

        $target = DB::connection('ympimis_2')->select("SELECT
                * 
            FROM
            `pn_targets` 
            WHERE
            `status` = 'Active'");

        $schedule = DB::SELECT("SELECT
            model,
            sum( quantity ) AS quantity 
            FROM
            production_schedules
            JOIN materials ON materials.material_number = production_schedules.material_number 
            WHERE
            due_date = date(
            NOW()) 
            AND materials.category = 'FG' 
            AND materials.origin_group_code = '073' 
            GROUP BY
            model");
        $response = array(
            'status' => true,
            'stock' => $stock,
            'timing' => $timing,
            'timing_ideal' => $timing_ideal,
            'stock_awal' => $stock_frame,
            'person_tuning' => $person_tuning,
            'person_all' => $person_all,
            'tact' => $tact,
            'tact_pureto' => $tact_pureto,
            'target' => $target,
            'tact_kensa_awal' => $tact_kensa_awal,
            'schedule' => $schedule,
            'tact_assembly' => $tact_assembly,
            'tact_kensa_akhir' => $tact_kensa_akhir,
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

public function indexCardCleaning()
{
    return view('pianica.pn_card_cleaning')
    ->with('title', 'Pianica Card Cleaning')
    ->with('title_jp', '')
    ->with('page', 'Pianica Card Cleaning');
}

public function scanCardCleaning(Request $request)
{
    try {
        $card = PnTag::where('tag',$request->get('tag'))->first();
        if ($card) {
            $delete = PnTag::where('tag',$request->get('tag'))->forceDelete();
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

public function indexPassRatio()
{
    return view('pianica.display.pn_pass_ratio')
    ->with('title', 'Pianica Pass Ratio')
    ->with('title_jp', '')
    ->with('page', 'Pianica Pass Ratio')
    ->with('model',$this->model);
}

public function fetchPassRatio(Request $request)
{
    try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
           if ($date_to == "") {
              $first = date('Y-m-01');
              $last = date('Y-m-d');
          }else{
              $first = date('Y-m-01');
              $last = $date_to;
          }
      }else{
         if ($date_to == "") {
            $first = $date_from;
            $last = date('Y-m-d');
        }else{
            $first = $date_from;
            $last = $date_to;
        }
    }
    $models = "";
    $models2 = "";
    if ($request->get('model') != '') {
        $models = "WHERE model = '".$request->get('model')."'";
        $models2 = "AND pn_log_ngs.model = '".$request->get('model')."' ";
    }
    $prod_result = DB::SELECT("SELECT DATE( created_at ) AS date,
        DATE_FORMAT( created_at,'%d-%b-%Y' ) AS date_name,
        DATE_FORMAT( created_at,'%Y-%m' ) AS `month`,
        DATE_FORMAT( created_at,'%b-%Y' ) AS `month_name`,
        model,
        form_id from pn_log_proces JOIN (
        SELECT form_id as form_ids, MAX(id) as ids
        FROM pn_log_proces
        WHERE
        DATE( created_at ) >= '".$first."' 
        AND DATE( created_at ) <= '".$last."' 
        AND model IS NOT NULL 
        AND form_id != '' 
        GROUP BY form_id
        ) a
        ON a.ids = pn_log_proces.id
        ".$models."
        order by DATE( created_at ),form_id asc");

    // $month_first = DB::SELECT("SELECT
    //     DATE_FORMAT( week_date, '%Y-%m' ) AS `month` 
    //     FROM
    //     weekly_calendars 
    //     WHERE
    //     fiscal_year = (
    //     SELECT DISTINCT
    //     ( fiscal_year ) 
    //     FROM
    //     weekly_calendars 
    //     WHERE
    //     week_date = DATE(
    //     NOW())) 
    //     GROUP BY
    //     DATE_FORMAT( week_date, '%Y-%m' ) 
    //     ORDER BY
    //     `month` ASC
    //     LIMIT 1");

    // $month_last = DB::SELECT("SELECT
    //     DATE_FORMAT( week_date, '%Y-%m' ) AS `month` 
    //     FROM
    //     weekly_calendars 
    //     WHERE
    //     fiscal_year = (
    //     SELECT DISTINCT
    //     ( fiscal_year ) 
    //     FROM
    //     weekly_calendars 
    //     WHERE
    //     week_date = DATE(
    //     NOW())) 
    //     GROUP BY
    //     DATE_FORMAT( week_date, '%Y-%m' ) 
    //     ORDER BY
    //     `month` DESC
    //     LIMIT 1");

    // $query = "";
    // for ($i=0; $i < count($month_all); $i++) { 
    //     $union = "";
    //     if ((count($month_all)-1) != $i) {
    //         $union = " UNION ALL ";
    //     }

    //     $query .= "SELECT
    //         DATE_FORMAT( created_at, '%Y-%m' ) AS `month`,
    //         DATE_FORMAT( created_at, '%b-%Y' ) AS `month_name`,
    //         model,
    //         form_id 
    //         FROM
    //         pn_log_proces 
    //         WHERE
    //         DATE_FORMAT( created_at, '%Y-%m' ) = '".$month_all[$i]->month."' 
    //         AND model IS NOT NULL 
    //         AND form_id != '' 
    //         GROUP BY
    //         `month`,
    //         month_name,
    //         model,
    //         form_id ".$union;
    // }
    // var_dump($query);
    // die();

    // $prod_result_monthly = DB::SELECT("SELECT
    //     DATE_FORMAT( created_at, '%Y-%m' ) AS `month`,
    //     DATE_FORMAT( created_at, '%b-%Y' ) AS `month_name`,
    //     model,
    //     form_id 
    //     FROM
    //     pn_log_proces 
    //     WHERE
    //     DATE_FORMAT( created_at, '%Y-%m' ) >= '".$month_first[0]->month."' 
    //     AND DATE_FORMAT( created_at, '%Y-%m' ) <= '".$month_last[0]->month."' 
    //     AND model IS NOT NULL 
    //     AND form_id != '' 
    //     GROUP BY
    //     `month`,
    //     month_name,
    //     model,
    //     form_id");

    $ng = DB::SELECT("SELECT
        a.form_id,
        GROUP_CONCAT( a.ng_name ) AS ng_name 
        FROM
        (
        SELECT
        form_id,
        GROUP_CONCAT( ng ) AS ng_id,
        GROUP_CONCAT( ng_lists.ng_name,';',ng_lists.location,';',pn_log_ngs.location ) AS ng_name 
        FROM
        pn_log_ngs
        LEFT JOIN ng_lists ON ng_lists.id = ng 
        WHERE
        form_id IS NOT NULL 
        AND pn_log_ngs.location = 'PN_Kensa_Akhir' 
        AND pn_log_ngs.form_id != '' 
        ".$models2."
        GROUP BY
        form_id UNION ALL
        SELECT
        form_id,
        GROUP_CONCAT( ng ) AS ng_id,
        GROUP_CONCAT( ng_lists.ng_name,';',ng_lists.location,';',pn_log_ngs.location ) AS ng_name 
        FROM
        pn_log_ngs
        LEFT JOIN ng_lists ON ng_lists.id = ng 
        WHERE
        form_id IS NOT NULL 
        AND pn_log_ngs.location = 'PN_Kakuning_Visual' 
        AND pn_log_ngs.form_id != '' 
        ".$models2."
        GROUP BY
        form_id 
        ) a 
        GROUP BY
        a.form_id");

    $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));

    $response = array(
        'status' => true,
        'prod_result' => $prod_result,
        // 'prod_result_monthly' => $prod_result_monthly,
        'ng' => $ng,
        'monthTitle' => $monthTitle,
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

public function saveAssembly(Request $request)
{
 $id_user = Auth::id();
 try {        

    $form_num = '';

    $form = PnTag::where('tag', $request->get('tag'))->select('form_id','position')->first();

    if($form) {
        $form_num = $form->form_id;
    }else{
        $log_process = PnLogProces::where('tag',$request->get('tag'))->where('form_id','!=',null)->orderby('id','desc')->first();
        if ($log_process) {
            $form_num = $log_process->form_id;
        }
    }

    $update_form = PnTag::where('tag', $request->get('tag'))->first();
    if ($update_form) {
        $update_form->position = 'PN_Assembly';
        $update_form->save();
    }else{
        $tag =  PnTag::insert(
            [
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'position' => $request->get('location'),
                'form_id' => $form_num,
                'status' => 'Used',
                'created_by' => $request->get('op'),
            ]
        );
    }

    $ng = $request->get('ng');
    if($ng !=""){
        $rows = explode(",", $ng);
        foreach ($rows as $row) 
        {                          
            $detail = new PnLogNg([                
                'ng' => $row,                
                'line' => $request->get('line'),
                'operator' => $request->get('op'),
                'tag' => $request->get('tag'),
                'form_id' => $form_num,
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'created_by' => $request->get('op'),
            ]);
            $detail->save(); 


        }

        $inventori =  PnInventorie::updateOrCreate(
            [           
                'tag' => $request->get('tag'),            
            ],
            [
               'line' => $request->get('line'),
               'tag' => $request->get('tag'),
               'model' => $request->get('model'),
               'location' => $request->get('location'),
               'qty' => $request->get('qty'),
               'status' => '0',
               'created_by' => $request->get('op'),
           ]);

        if ($request->get('started_at') == '') {
            $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
        }else{
            $started_at = $request->get('started_at');
        }

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'operator' => $request->get('op'),
            'form_id' => $form_num,
            'tag' => $request->get('tag'),
            'started_at' => $started_at,
            'finished_at' => date('Y-m-d H:i:s'),
            'model' => $request->get('model'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);
        $log->save();
        $inventori->save(); 

    }else{
        $inventori =  PnInventorie::updateOrCreate(
            [           
                'tag' => $request->get('tag'),            
            ],
            [
                'line' => $request->get('line'),
                'tag' => $request->get('tag'),
                'model' => $request->get('model'),
                'location' => $request->get('location'),
                'qty' => $request->get('qty'),
                'status' => '1',
                'created_by' => $request->get('op'),
            ]);

        if ($request->get('started_at') == '') {
            $started_at = date('Y-m-d H:i:s',strtotime($request->get('started_at').'- 5 seconds'));
        }else{
            $started_at = $request->get('started_at');
        }

        $log = new PnLogProces([
            'line' => $request->get('line'),
            'form_id' => $form_num,
            'operator' => $request->get('op'),
            'tag' => $request->get('tag'),
            'started_at' => $started_at,
            'finished_at' => date('Y-m-d H:i:s'),
            'model' => $request->get('model'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);
        $log->save();
        $inventori->save(); 
    }

    $response = array(
        'status' => true,
        'message' => 'Input Success'
    );
    return Response::json($response);
}
catch(\Exception $e){
    $response = array(
        'status' => false,
        'message' => $e->getMessage()
    );
    return Response::json($response);
}
}

public function indexNGTrend()
{
    return view('pianica.display.pn_ng_trends')->with('page', 'Report Kensa Awal')->with('title','Trend NG Pianica')->with('title_jp','');
}

public function fetchNGTrend(Request $request)
{
    try {

        $date_from = $request->get('date_from');
        $date_to = $request->get('date_to');
        if ($date_from == "") {
            if ($date_to == "") {
                $first = date('Y-m-01');
                $last = date('Y-m-d');
            }else{
                $first = date('Y-m-01');
                $last = $date_to;
            }
        }else{
            if ($date_to == "") {
                $first = $date_from;
                $last = date('Y-m-d');
            }else{
                $first = $date_from;
                $last = $date_to;
            }
        }

        if ($request->get('location') == '') {
            $location = 'Bentsuki - Benage';
        }else{
            $location = $request->get('location');
        }

        if ($location == 'Bentsuki - Benage') {
            $reeds = "SPLIT_STRING ( operator, '#', 2 ) AS tuning,";
            $ngs = "SPLIT_STRING ( operator, '#', 2 ) AS operator,";
            $op = DB::SELECT("SELECT DISTINCT
                operator,
                op.NAME AS nama 
                FROM
                pn_log_proces log
                LEFT JOIN employees op ON op.employee_id = operator 
                WHERE
                location = 'PN_Pureto' 
                AND date( log.created_at ) <= '".$last."' AND date( log.created_at ) >= '".$first."'");
            $op_all = DB::SELECT("SELECT
                operator_id,
                process_name,
                line 
                FROM
                pn_operator_logs 
                WHERE
                process_name = 'Bensuki' 
                AND date = DATE(
                NOW())");
        }else if($location == 'Tuning'){
            $reeds = "SPLIT_STRING ( operator, '#', 1 ) AS tuning,";
            $ngs = "SPLIT_STRING ( operator, '#', 1 ) AS operator,";
            $op = db::select("SELECT nik as operator, nama from pn_operators WHERE bagian='tuning' GROUP BY nik,nama");
            $op_all = DB::SELECT("SELECT
                operator_id,
                process_name,
                line 
                FROM
                pn_operator_logs 
                WHERE
                process_name = 'Tuning' 
                AND date = DATE(
                NOW())");
        }

        $monthTitle = date("d M Y", strtotime($first)).' to '.date("d M Y", strtotime($last));
        $reed = DB::SELECT("SELECT
            ng,
            ".$reeds."
            reed,
            qty,
            IF(ng_lists.ng_name = 'T. Tinggi','Tinggi',IF(ng_lists.ng_name = 'T. Rendah','Rendah',ng_lists.ng_name)) as ng_name,
            line 
            FROM
            pn_log_ngs 
            LEFT JOIN ng_lists ON ng_lists.id = pn_log_ngs.ng 
            WHERE
            pn_log_ngs.location = 'PN_Kensa_Awal' 
            AND date( pn_log_ngs.created_at ) >= '".$first."' 
            AND date( pn_log_ngs.created_at ) <= '".$last."'");

        $ng = DB::SELECT("SELECT
            a.operator,
            IF(a.ng_name = 'T. Tinggi','Tinggi',IF(a.ng_name = 'T. Rendah','Rendah',a.ng_name)) as ng_name,
            sum( a.qty ) AS jml 
            FROM
            (
            SELECT
            ng,
            ng_lists.ng_name,
            ".$ngs."
            pn_log_ngs.qty 
            FROM
            pn_log_ngs
            LEFT JOIN ng_lists ON ng_lists.id = pn_log_ngs.ng 
            WHERE
            pn_log_ngs.location = 'PN_Kensa_Awal' 
            AND date( pn_log_ngs.created_at ) >= '".$first."' 
            AND date( pn_log_ngs.created_at ) <= '".$last."' 
            ) a 
            where a.operator != ''
            GROUP BY
            a.operator,
            a.ng_name 
            ORDER BY
            a.operator,
            a.ng_name,
            jml");



        $response = array(
            'status' => true,
            'ng' => $ng,
            'op' => $op,
            'reed' => $reed,
            'location' => $location,
            'op_all' => $op_all,
            'monthTitle' => $monthTitle
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

public function saveLabelKakuning(Request $request)
{
    try {
        if (count($request->file('file_evidence')) > 0) {
            $tujuan_upload2 = 'files/label/three_man_eff';

            $file2 = $request->file('file_evidence');
            $nama2 = $file2->getClientOriginalName();
            $extension2 = pathinfo($nama2, PATHINFO_EXTENSION);
            $filename2 = $request->get('material_number') . ' (' . date('d-M-y H-i-s') . ').' . $extension2;
            $file2->move($tujuan_upload2, $filename2);

            $labels = new LabelEvidence([
                'material_number' => $request->get('material_number'),
                'material_description' => $request->get('material_description'),
                'product' => 'Pianica',
                'remark' => 'Three Man',
                'evidence' => $filename2,
                'created_by' => Auth::user()->username,
            ]);
            $labels->save();
        }

        $response = array(
            'status' => true
        );
        return Response::json($response);
    } catch (Exception $e) {
       $response = array(
        'status' => false,
        'message' => $e->getMessage()
    );
       return Response::json($response);
   }

}

public function saveKakuningCase(Request $request){
    $id_user = Auth::id();
    try {
        $tahun = date('y');
        $bulan = date('m');

        $query = "SELECT form_number FROM pn_case_log_proccesses where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by form_number DESC LIMIT 1";
        $nomorurut = DB::select($query);

        if (count($nomorurut) > 0)
        {
            $nomor = substr($nomorurut[0]->form_number, -5);
            $nomor = $nomor + 1;
            $nomor = sprintf('%05d', $nomor);
        }
        else
        {
            $nomor = "00001";
        }

        $result['tahun'] = $tahun;
        $result['bulan'] = $bulan;
        $result['no_urut'] = $nomor;

        $form_number = 'CASE'.$result['tahun'].$result['bulan'].$result['no_urut'];
        $log = new PnCaseLogProccess([
            'line' => $request->get('line'),
            'form_number' => $form_number,
            'operator' => $request->get('op'),
            'type' => $request->get('type'),
            'location' => $request->get('location'),
            'qty' => $request->get('qty'),
            'created_by' => $request->get('op'),
        ]);
        $log->save();
        $ids = $log->id;

        $ng = $request->get('ng');
        if($ng != ""){
            $rows = explode(",", $ng);
            foreach ($rows as $row) 
            {
                $status_ng = 'scrap';
                $rmk = '';

                if (count($request->get('ng_status')) > 0) {
                    foreach ($request->get('ng_status') as $ng_stat) {
                        if ($ng_stat['ng_name'] == $row) {
                            $status_ng = $ng_stat['status'];
                            $rmk = $ng_stat['location'];
                        }
                    }
                }
                

                $detail = new PnCaseLogNg([                
                    'ng' => $row,
                    'form_number' => $form_number,
                    'ng_status' => $status_ng,
                    'line' => $request->get('line'),
                    'operator' => $request->get('op'),
                    'type' => $request->get('type'),
                    'log_id' => $ids,
                    'location' => $request->get('location'),
                    'qty' => $request->get('qty'),
                    'remark' => $rmk,
                    'created_by' => $request->get('op'),
                ]);
                $detail->save(); 
            }
        }

        $response = array(
            'status' => true,
            'message' => 'Input Success',
            'form' => $form_number
        );
        return Response::json($response);
    }
    catch(\Exception $e){
        $response = array(
            'status' => false,
            'message' => $e->getMessage()
        );
        return Response::json($response);
    }
}


public function fetchCaseNg(Request $request)
{  
    $date = date('Y-m-d');

    $query2 = "SELECT COUNT(id) as total, SUM(IF(log_id is null, 1,0)) as oke, SUM(IF(log_id is not null, 1,0)) as ng from pn_case_log_proccesses
    left join (select log_id, GROUP_CONCAT(ng) as ngs from pn_case_log_ngs group by log_id) nngs  on pn_case_log_proccesses.id = nngs.log_id
    where DATE_FORMAT(pn_case_log_proccesses.created_at,'%Y-%m-%d') = '".$date."' and pn_case_log_proccesses.line = '".$request->get('line')."'";

    $query = "SELECT SUM(qty) as ng from pn_case_log_ngs where DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' and line = '".$request->get('line')."'";

    $ng_logs = "SELECT operator, type,
    IFNULL(SUM(IF(ng_status = 'scrap', 1, 0)),0) as scrap,
    IFNULL(SUM(IF(ng_status = 'return', 1, 0)),0) as `return`,
    IFNULL(SUM(IF(ng_status = 'repair', 1, 0)),0) as `repair`
    FROM `pn_case_log_ngs` where line = '".$request->get('line')."' and DATE_FORMAT(created_at,'%Y-%m-%d') = '".$date."' 
    group by operator, type";

    $total_ng =DB::select($query);
    $total =DB::select($query2);
    $ng_log =DB::select($ng_logs);

    $response = array(
        'status' => true,
        'message' => 'NG Record found',
        'total_ng' => $total_ng,
        'total' => $total,
        'ng_status' => $ng_log
    );
    return Response::json($response);

}

public function indexDisplayNgCase(Request $request)
{
    return view('pianica.display.pn_case_ng_trend')->with('page', 'Display Trend NG')
    ->with('title', 'Trend NG Case Pianica')
    ->with('title_jp', 'ピアニカケースの不良推移');
}

public function fetchDisplayNgCase(Request $request)
{
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

    $data_trend = db::select("SELECT pn_case_log_proccesses.id, DATE_FORMAT(created_at,'%Y-%m-%d') as dt, log_id, ng_status, IF(log_id is not null, 'ng', 'oke') as stat from pn_case_log_proccesses
        LEFT JOIN (select log_id, ng_status from pn_case_log_ngs
        where DATE_FORMAT(created_at,'%Y-%m-%d') >= ".$first." and DATE_FORMAT(created_at,'%Y-%m-%d') <= ".$last."
        group by log_id, ng_status) ngs on pn_case_log_proccesses.id = ngs.log_id
        where DATE_FORMAT(created_at,'%Y-%m-%d') >= ".$first." and DATE_FORMAT(created_at,'%Y-%m-%d') <= ".$last);

    $response = array(
        'status' => true,
        'data_trend' => $data_trend,
        'monthTitle' => $dateTitleFirst.' - '.$dateTitleLast
    );
    return Response::json($response);
}

public function fetchDisplayNgCaseDetail(Request $request)
{
    $date_from = $request->get('date_from');
    $date_to = $request->get('date_to');
    $where = '';

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

    if ($request->get('category') == 'Grafik') {
        if ($request->get('remark') == 'Total NG') {
            $where = 'having stat is not null';
        }
    } else if ($request->get('category') == 'bar') {
        $where = 'having ng_statuses LIKE "%'.$request->get('remark').'%"';
    }

    $data_trend = db::select("SELECT pn_case_log_proccesses.id, DATE_FORMAT(pn_case_log_proccesses.created_at,'%Y-%m-%d') as dt, pn_case_log_proccesses.type, pn_case_log_proccesses.line, GROUP_CONCAT(ng) as ngs, GROUP_CONCAT(ng_status) as ng_statuses, GROUP_CONCAT(log_id) as stat from pn_case_log_proccesses
        LEFT JOIN pn_case_log_ngs on pn_case_log_proccesses.id = pn_case_log_ngs.log_id
        where DATE_FORMAT(pn_case_log_proccesses.created_at,'%Y-%m-%d') >= ".$first." and DATE_FORMAT(pn_case_log_proccesses.created_at,'%Y-%m-%d') <= ".$last."
        group by pn_case_log_proccesses.id, DATE_FORMAT(pn_case_log_proccesses.created_at,'%Y-%m-%d'), pn_case_log_proccesses.type, pn_case_log_proccesses.line ".$where);

    $response = array(
        'status' => true,
        'datas' => $data_trend,
        'monthTitle' => $dateTitleFirst.' - '.$dateTitleLast
    );
    return Response::json($response);
}

public function indexCardMigration()
{
    $title = 'Card Migration';
    $title_jp = '??';

    return view('pianica.card_migration', array(
        'title' => $title,
        'title_jp' => $title_jp,
    ));
}

public function fetchCardMigration()
{
    try {
        $datas = DB::connection('ympimis_2')->table('pn_card_migrations')->get();
        $response = array(
            'status' => true,
            'datas' => $datas
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

public function scanCardMigrationCheck(Request $request)
{
    try {
        $tag = $request->get('tag');
        $pn_tags = PnTag::where('tag',$tag)->first();
        $datas = null;
        if (!$pn_tags) {
            $log = PnLogProces::where('tag',$tag)->orderBy('created_at','desc')->first();
            if ($log) {
                $datas = $log;
            }
        }else{
            $datas = $pn_tags;
        }
        $response = array(
            'status' => true,
            'datas' => $datas
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

public function scanCardMigration(Request $request)
{
    try {
        $tag = $request->get('tag');
        $tag_new = $request->get('tag_new');
        $pn_tags = PnTag::where('tag',$tag)->first();
        $datas = null;
        if (!$pn_tags) {
            $log = PnLogProces::where('tag',$tag)->orderBy('created_at','desc')->first();
            if ($log) {
                $datas = $log;
            }
        }else{
            $datas = $pn_tags;
        }

        if ($datas != null) {
            $pn_tags = PnTag::where('tag',$tag)->where('form_id',$datas->form_id)->first();
            if ($pn_tags) {
                $pn_tags->tag = $tag_new;
                $pn_tags->save();
                $insert = DB::connection('ympimis_2')->table('pn_card_migrations')->insert([
                    'tag_old' => $tag,
                    'tag_new' => $tag_new,
                    'model' => $pn_tags->model,
                    'form_id' => $pn_tags->form_id,
                    'created_by' => Auth::user()->id,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            $pn_logs = PnLogProces::where('tag',$tag)->where('form_id',$datas->form_id)->first();
            if ($pn_logs) {
                $pn_logs->tag = $tag_new;
                $pn_logs->save();
            }
            $pn_log_ngs = PnLogNg::where('tag',$tag)->where('form_id',$datas->form_id)->first();
            if ($pn_log_ngs) {
                $pn_log_ngs->tag = $tag_new;
                $pn_log_ngs->save();
            }
            $pn_invent = PnInventorie::where('tag',$tag)->first();
            if ($pn_invent) {
                $pn_invent->tag = $tag_new;
                $pn_invent->save();
            }
        }
        $response = array(
            'status' => true,
            'datas' => $datas
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

public function indexBoard($line)
{
    return view('pianica.display.pn_board')
    ->with('title', 'Pianica Tuning Monitoring Line '.$line)
    ->with('title_jp', '')
    ->with('loc', $line)
    ->with('page', 'Pianica Tuning Monitoring');
}

public function fetchBoard(Request $request)
{
    try {
        $line = $request->get('loc');
        $tuning = DB::table('pn_devices')->where('location_number',$line)->get();
        $board = [];
        for ($i=0; $i < count($tuning); $i++) { 
            $name = '';
            if ($tuning[$i]->operator_id != null) {
                $emp = EmployeeSync::where('employee_id',$tuning[$i]->operator_id)->first();
                $name = $emp->name;
            }
            array_push($board, [
                'location' => $tuning[$i]->location,
                'employee_id' => $tuning[$i]->operator_id,
                'name' => $name,
                'sedang' => '<span style="font-size:50px;">'.$tuning[$i]->sedang_model."</span><br>".$tuning[$i]->sedang_serial_number,
                'sedang_time' => $tuning[$i]->sedang_time,
            ]);
        }
        $response = array(
            'status' => true,
            'board' => $board
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

public function fetchBoardResult(Request $request)
{
    try {
        $operators = $request->get('operators');
        $results = [];
        for ($i=0; $i < count($operators); $i++) { 
            if ($operators[$i] != '') {
                $datas = DB::SELECT("SELECT
                        count(DISTINCT ( form_id )) AS qty 
                    FROM
                        `pn_log_proces` 
                    WHERE
                        DATE( created_at ) = DATE(
                        NOW()) 
                        AND operator = '".$operators[$i]."'");
                array_push($results, $datas[0]->qty);
            }else{
                array_push($results, '');
            }
        }
        $response = array(
            'status' => true,
            'results' => $results
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
