<?php

namespace App\Http\Controllers;

use PDF;
use File;
use Excel;
use Response;
use DataTables;
use App\EmployeeSync;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;

class WindsController extends Controller
{
  public function __construct()
  {
    $this->middleware('auth');
  }

  public function index(){
    return view('winds.index',  
      array(
        'title' => 'WINDS Z-PRO Dashboard', 
        'title_jp' => 'WINDS Z-PRO ダッシュボード'
      )
    )->with('page', 'WINDS');
  }

  public function fetchProcess(Request $request)
  {
    $process = DB::connection('winds')
    ->SELECT('SELECT id, gmc, no_proses, proses, ik, cdm_status FROM `master_processes` WHERE gmc = "'.$request->get('gmc').'" ORDER BY no_proses ASC');

    $response = array(
      'status' => true,
      'process' => $process
    );
    return Response::json($response);
  }

  public function masterfetch(){
    try {
      $resumes = DB::connection('winds')
      ->SELECT('SELECT gmc_ympi, gmc, deskripsi, lokasi, order_number,qc_kouteihyo FROM `master_dashboards` WHERE lokasi in ("ZPRO","TPRO","CLCX") ORDER BY order_number ASC');

      $response = array(
        'status' => true,
        'resumes' => $resumes
      );
      return Response::json($response);
    } 
    catch (\Exception $e) {
      $response = array(
        'status' => false,
        'message' => $e->getMessage()
      );
      return Response::json($response);
    }
  }

  public function IndexItemDetails($gmc, $id, $no_proc)
  {
    $data_detail = DB::connection('winds')
    ->select('SELECT  master_parameters.id, master_dashboards.gmc, master_dashboards.deskripsi, master_dashboards.gmc_raw, master_dashboards.deskripsi_raw, master_parameters.id_proses, master_parameters.parameter_eng, master_parameters.parameter_jpn, master_parameters.parameter_ind, master_parameters.value FROM `master_parameters`
      LEFT JOIN master_dashboards on master_parameters.gmc = master_dashboards.gmc
      WHERE master_parameters.id_proses = '.$id);      

    $process = DB::connection('winds')
    ->select('SELECT gmc, proses from master_processes where id = '.$id);            

    $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();

    return view('winds.detail',  
      array(
        'title' => 'WINDS - Item Detail', 
        'title_jp' => 'WINDSアイテム詳細',
        'data' => $data_detail,
        'proses' => $process,
        'gmc' => $gmc,
        'proses_status' => $id,
        'user' => $user,
      )
    )->with('page', 'WINDS');
  }

  public function UpdateItemDetails($table, Request $request){    

    $user = EmployeeSync::where('employee_id',Auth::user()->username)->first();    

    if($user->department !== 'Production Engineering Department' && $user->department !== 'Management Information System Department') {
      abort(403, 'Unauthorized action.');
    }

    switch ($table) {
      case 'material':
        try {
          DB::beginTransaction();
          $material = DB::connection('winds')->table('master_dashboards')
          ->where('gmc', '=', $request->get('gmc'))
          ->update([            
            'gmc_raw' => $request->get('gmc_raw'),            
            'deskripsi_raw' => $request->get('deskripsi_raw'),
            'updated_at' => date('Y-m-d H:i:s'),
          ]);
          DB::commit();

          $response = array(
            'status' => true,
            'message' => 'Update Success'
          );

          return Response::json($response);

        } catch (\Throwable $th) {
          DB::rollback();
          $response = array(
            'status' => false,
            'message' => $th->getMessage()
          );
          return Response::json($response);
        }
        break;
        
      case 'parameter':
        try{            
          DB::beginTransaction();      
          foreach ($request->get('parameter') as $key => $value) {        
    
            DB::connection('winds')->table('master_parameters')
                ->updateOrInsert(['id' => $value['id']], [
                    'gmc' => $value['gmc'],
                    'id_proses' => $value['id_proses'],
                    'parameter_ind' => $value['parameter_ind'],
                    'parameter_jpn' => $value['parameter_jpn'],
                    'value' => $value['value'],
                    'created_by' => $value['created_by'],
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
          }
    
          DB::commit();
          $response = array(
            'status' => true,
            'message' => 'Update Success'
          );
          return Response::json($response);
        }
        catch(\Exception $e){
          DB::rollback();
          $response = array(
            'status' => false,
            'message' => $e->getMessage()
          );
          return Response::json($response);
        }
        break;
      
      default:
          abort(403, 'Unauthorized action.');
        break;
    }
  }

  public function excelMasterChecklists(Request $request)
  {    
    
    $file = $request->file('file_');    
    $file_name = 'master_checklist_'. date("ymd_h.i") .'.'.$file->getClientOriginalExtension();
    
    DB::beginTransaction();    
    try {      
      $file->move(public_path('public/winds/master_checklists'), $file_name);
      $excel = public_path('public/winds/master_checklists/'.$file_name);    
      
      $rows = Excel::load($excel, function($reader) {
        $reader->noHeading();
        $reader->skipRows(1);
      })->get();
  
      $rows = $rows->toArray();
          
      $truncate_winds = DB::connection('winds')->table('master_checklists')->truncate();
      
      foreach ($rows as $key => $value) {
        $data = array(
          'id' => null,
          'gmc' => $value[1],
          'proses' => $value[2],
          'note' => $value[3],
          'poin_cek' => $value[4],
          'standar' => $value[5],
          'min' => $value[6],
          'max' => $value[7],
          'metode' => $value[8],
          'frekuensi' => $value[9],
          'remark' => $value[10],
          'created_by' => Auth::user()->username,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
          'deleted_at' => date('Y-m-d H:i:s'),
        );
        $insert_winds = DB::connection('winds')->table('master_checklists')->insert($data);
      }

      DB::commit();
      $response = array(
        'status' => true,
        'message' => 'Upload Success'
      );
      return Response::json($response);
    } catch (\Throwable $th) {
      DB::rollback();
      $response = array(
        'status' => false,
        'message' => $th->getMessage()
      );
      return Response::json($response);
    }        
  }

  public function deleteItemDetails(Request $request){
    // dd($request->all());

    try{
      DB::beginTransaction();

      DB::connection('winds')->table('master_parameters')
      ->where('id', '=', $request->get('id'))
      ->delete();

      DB::commit();
      $response = array(
        'status' => true,
        'message' => 'Delete Success'
      );
      return Response::json($response);
    }
    catch(\Exception $e){
      DB::rollback();
      $response = array(
        'status' => false,
        'message' => $e->getMessage()
      );
      return Response::json($response);
    }
  }

  public function indexCDM($gmc, $id_process, $process, $no_proc)
  {
    $note = null;
    $note2 = '';

    if ($no_proc > 1) {
      $note = $no_proc;
      $note2 = ' and cdms.proses_number = "'.$note.'"';
    }


    if ($note == null) {
      $where = 'master_checklists.note is null';
    } else {
      $where = 'master_checklists.note = "'.$note.'"';
    }

    $poin_cek = DB::connection('winds')
    ->SELECT('SELECT master_dashboards.gmc, master_dashboards.deskripsi, master_checklists.proses, master_checklists.poin_cek, master_checklists.standar, master_checklists.metode, master_checklists.min, master_checklists.max, master_checklists.frekuensi, remark from master_dashboards 
      left join master_checklists on master_dashboards.gmc = master_checklists.gmc and master_checklists.proses = "'.$process.'"
      WHERE master_dashboards.gmc = "'.$gmc.'" and '.$where);


    $jumlah_poin_cek = DB::connection('winds')
    ->SELECT('SELECT count(master_checklists.id) as jumlah from master_dashboards left join master_checklists on master_dashboards.gmc = master_checklists.gmc and master_checklists.proses = "'.$process.'" WHERE master_dashboards.gmc = "'.$gmc.'"');

    $operator = db::select("select DISTINCT employee_id, name, section, position from employee_syncs where end_date is null and (sub_group = 'Z Pro Key Initial Unit' or section = 'PE Control Section') order by position asc");

    $cdms_data = DB::connection('winds')
    ->SELECT('SELECT * from cdms WHERE cdms.gmc = "'.$gmc.'" and cdms.proses = "'.$process.'" '.$note2.' order by id limit 3');

    return view('winds.cdm_form',  
      array(
        'title' => 'WINDS - CDM Form',
        'title_jp' => '寸法検査表',
        'poin_cek' => $poin_cek,
        'jumlah_poin_cek' => $jumlah_poin_cek,
        'id_process' => $id_process,
        'process' => $process,
        'cdms_data' => $cdms_data,
        'operator' => $operator,
      )
    )->with('page', 'WINDS');
  }

  public function InsertCDM(Request $request){
    try{
      $inputor = $request->get('inputor_id');
      $employee = explode("_", $inputor);

      $id = DB::connection('winds')
      ->table('cdms')
      ->insertGetId([
        'tanggal' => $request->get('submission_date'),
        'inputor_id' => $employee[0],
        'inputor_name' => $employee[1],
        'note' => $request->get('note'),
        'gmc' => $request->get('gmc'),
        'proses' => $request->get('process'),
        'created_by' => $employee[0],
        'created_at' => date('Y-m-d H:i:s'),
        'updated_at' => date('Y-m-d H:i:s')
      ]);

      $jumlah = $request->get('jumlah');

      for ($i=1; $i <= $jumlah; $i++) { 
        $cdm_details = DB::connection('winds')
        ->table('cdm_details')
        ->insert([
          'id_cdm' => $id,
          'poin_cek' => $request->get('poin_cek_'.$i),
          'awal' => $request->get('cdm_awal_'.$i),
          'tengah' => $request->get('cdm_tengah_'.$i),
          'akhir' => $request->get('cdm_akhir_'.$i),
          'penilaian' => $request->get('penilaian_'.$i),
          'created_by' => $employee[0],
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s')
        ]);
      }

      return redirect('/winds/index/cdm/'.$request->get('gmc').'/'.$request->get('id_process').'/'.$request->get('process').'/'.$request->get('process_num'))->with('status', 'Data CDM Berhasil Dibuat')
      ->with('page', 'WINDS');

    }catch(Exception $e){

      return redirect('/winds/index/cdm/'.$request->get('gmc').'/'.$request->get('id_process').'/'.$request->get('process').'/'.$request->get('process_num'))->with('error', $e->getMessage())
      ->with('page', 'WINDS');

    }
  }

  public function DetailCDM(Request $request)
  {
    try {

      $cdms_data = DB::connection('winds')
      ->SELECT('SELECT cdm_details.*, cdms.gmc, cdms.proses from cdm_details join cdms on cdms.id = cdm_details.id_cdm where cdm_details.id_cdm = "'.$request->get('id').'"');

      $response = array(
        'status' => true,
        'cdms' => $cdms_data
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

  public function exportCDM($gmc, $proses)
  {

    $cdm_detail = DB::connection('winds')->select(
      'SELECT cdms.id, cdms.tanggal, cdms.inputor_id, cdms.inputor_name, cdms.note, cdms.gmc,cdms.proses , cdm_details.poin_cek, cdm_details.awal, cdm_details.tengah, cdm_details.akhir, cdm_details.penilaian from cdms join cdm_details on cdms.id = cdm_details.id_cdm where cdms.gmc = "'.$gmc.'" and proses = "'.$proses.'" ');

    $poin_cek = DB::connection('winds')->select(
      "SELECT poin_cek FROM `master_checklists` where gmc = '".$gmc."' and proses = '".$proses."'");

    $data = array(
      'cdm_detail' => $cdm_detail,
      'point_check' => $poin_cek
    );

    ob_clean();

    if (count($poin_cek) > 0 ) {
      Excel::create('Data CDM '.$gmc.' '.$proses, function($excel) use ($data){
        $excel->sheet('CDM', function($sheet) use ($data) {
          return $sheet->loadView('winds.export_cdm', $data);
        });
      })->export('xlsx');
    } else {
      return back();
    }

  }

  public function fetchProcessAntrian(Request $request)
  {

  }

  public function indexMainGrafikTrendline()
  {
    return view('winds.grafik_trendline',
      array(
        'title' => 'WINDS Dashboard', 
        'title_jp' => 'WINDS ダッシュボード',
      )
    )->with('page', 'WINDS');
  }

  public function indexGrafikTrendline($gmc, $proses)
  {
    $detail_item = db::connection('winds')
    ->select("SELECT master_checklists.gmc, master_checklists.proses, master_checklists.poin_cek, master_dashboards.deskripsi from master_checklists 
      left join master_dashboards on master_dashboards.gmc = master_checklists.gmc
      where master_checklists.gmc = '".$gmc."' and master_checklists.proses = '".$proses."'");

    return view('winds.grafik_trendline',
      array(
        'title' => 'WINDS Dashboard', 
        'title_jp' => 'WINDS ダッシュボード',
        'detail_item' => $detail_item
      )
    )->with('page', 'WINDS');
  }

  public function fetchGrafikTrendline($gmc, $proses)
  {
    $detail_item = db::connection('winds')
    ->select("SELECT cdms.tanggal, cdm_details.poin_cek, awal, tengah, akhir, min, max, master_checklists.remark FROM `cdms` 
      LEFT JOIN cdm_details on cdms.id = cdm_details.id_cdm
      LEFT JOIN master_checklists on master_checklists.gmc = cdms.gmc and master_checklists.proses = cdms.proses and master_checklists.poin_cek = cdm_details.poin_cek
      where master_checklists.gmc = '".$gmc."' and master_checklists.proses = '".$proses."' and remark = 'Input hasil pengukuran'
      order by cdms.tanggal asc");

    $item = db::connection('winds')
    ->select("SELECT master_checklists.gmc, master_checklists.proses, master_checklists.poin_cek, master_dashboards.deskripsi from master_checklists 
      left join master_dashboards on master_dashboards.gmc = master_checklists.gmc
      where master_checklists.gmc = '".$gmc."' and master_checklists.proses = '".$proses."'");

    $response = array(
      'status' => true,
      'datas' => $detail_item,
      'item_detail' => $item
    );
    return Response::json($response);
  }

  public function getIndexBySedang($gmc)
  {
    // SELECT order_id FROM t_antrian a WHERE flow_id=".$fl." ORDER BY antrian_no LIMIT 1
  }

  
  public function indexWindsMpro(){
    return view('winds.mpro.index',  
      array(
        'title' => 'WINDS MPRO Dashboard', 
        'title_jp' => 'WINDS MPRO ダッシュボード'
      )
    )->with('page', 'WINDS MPRO');
  }

  public function masterfetchMpro(){
    try {
      $resumes = DB::connection('winds')
      ->SELECT('SELECT gmc_ympi, gmc, deskripsi, lokasi, order_number FROM `master_dashboards` WHERE lokasi = "MPRO" ORDER BY order_number ASC');

      $response = array(
        'status' => true,
        'resumes' => $resumes
      );
      return Response::json($response);
    } 
    catch (\Exception $e) {
      $response = array(
        'status' => false,
        'message' => $e->getMessage()
      );
      return Response::json($response);
    }
  }
}
