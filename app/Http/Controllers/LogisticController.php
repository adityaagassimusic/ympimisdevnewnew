<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\User;
use Carbon\Carbon;
use Response;
use DataTables;
use Excel;
use DateTime;
use File;
use App\Employee;
use App\EmployeeSync;
use PDF;
use App\AccReceive;
use App\AccReceiveReport;

use App\FixedAssetInvoice;
use App\FixedAssetDisposal;
use App\FixedAssetDisposalScrap;
use App\FixedAssetLabel;

use App\VendorLocal;
use App\VendorOversea;


class LogisticController extends Controller
{

	public function indexShipping(){

		$title = "Shipping Instruction Monitoring";
		$title_jp ="";

		$to_destinations = db::select("SELECT
			vendor_name,
            address
			FROM
			vendor_overseas
			where deleted_at IS NULL
			");

		$service_vendors = db::select("SELECT
			vendor_service,address
			FROM
			vendor_locals
			where deleted_at IS NULL
			");

		return view('logistic.shipping_instruction.shipping_index', array(
			'title' => $title,
			'title_jp' => $title_jp,
			'to_destinations' => $to_destinations,
			'service_vendors' => $service_vendors
		))->with('page', 'MIS Form')->with('head', 'MIS Form');
	}



    public function reportShippingLog(Request $request){
        // $mutasi = MutasiAnt::find($id);

        // $resumes = MutasiAnt::select(
        //     'id','status', 'posisi', 'nik', 'nama', 'sub_group', 'group', 'seksi', 'departemen', 'jabatan', 'rekomendasi', 'ke_sub_group', 'ke_group', 'ke_seksi', 'ke_departemen', 'ke_jabatan', 'tanggal', 'alasan', 'created_by', 

        //     'chief_or_foreman_asal', 'nama_chief_asal', DB::RAW('DATE_FORMAT(date_atasan_asal, "%d-%m-%Y") as date_atasan_asal'),
        //     'manager_asal', 'nama_manager_asal', DB::RAW('DATE_FORMAT(date_manager_asal, "%d-%m-%Y") as date_manager_asal'),
        //     'dgm_asal', 'nama_dgm_asal', DB::RAW('DATE_FORMAT(date_dgm_asal, "%d-%m-%Y") as date_dgm_asal'),
        //     'gm_asal', 'nama_gm_asal', DB::RAW('DATE_FORMAT(date_gm_asal, "%d-%m-%Y") as date_gm_asal'), 
        //     'chief_or_foreman_tujuan', 'nama_chief_tujuan', DB::RAW('DATE_FORMAT(date_atasan_tujuan, "%d-%m-%Y") as date_atasan_tujuan'),
        //     'manager_tujuan', 'nama_manager_tujuan', DB::RAW('DATE_FORMAT(date_manager_tujuan, "%d-%m-%Y") as date_manager_tujuan'),
        //     'dgm_tujuan', 'nama_dgm_tujuan', DB::RAW('DATE_FORMAT(date_dgm_tujuan, "%d-%m-%Y") as date_dgm_tujuan'), 
        //     'gm_tujuan', 'nama_gm_tujuan', DB::RAW('DATE_FORMAT(date_gm_tujuan, "%d-%m-%Y") as date_gm_tujuan'), 
        //     'manager_hrga', 'nama_manager', DB::RAW('DATE_FORMAT(date_manager_hrga, "%d-%m-%Y") as date_manager_hrga'),
        //     'direktur_hr', 'nama_direktur_hr', DB::RAW('DATE_FORMAT(date_direktur_hr, "%d-%m-%Y") as date_direktur_hr'), 

        //     'app_ca', 'app_ma', 'app_da', 'app_ga', 'app_ct', 'app_mt', 'app_dt', 'app_gt', 'app_m', 'app_dir')
        // ->where('mutasi_ant_depts.id', '=', $id)
        // ->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        $pdf->loadView('logistic.shipping_instruction.report_shipping', array(
            
        ));

        return $pdf->stream("test.pdf");
    }


//Barang Modal
    public function barang_modal(){
      $title = 'Kontrol Barang Modal';
      $title_jp = '';

      return view('warehouse_new.barang_modal.index_kedatangan_barang_produksi', array(
          'title' => $title,
          'title_jp' => $title_jp
      ))->with('page', 'Kontrol Barang Modal')->with('head', 'Kontrol Barang Modal');
  }

  public function cek_kedatangan_produksi_all($location)
  {
    $title = 'Report Kedatangan Produksi';
    $title_jp = '';

    return view('warehouse_new.barang_modal.report_kedatangan_barang_produksi', array(
        'title' => $title,
        'title_jp' => $title_jp,
        'location' => $location
    ))->with('page', 'Report Kedatangan Produksi')->with('head', 'Report Kedatangan Produksi');
}

public function fetch_kedatangan_produksi_all(Request $request)
{
    try {

            // var_dump($request->get('location'));die();
        if ($request->get('location') == 'EDIN') {
              // code...
        }

        $kedatangan_barang = db::select("
            SELECT DISTINCT
            acc_receives.*,
            acc_purchase_order_details.no_pr,
            IF
            ( goods_price != 0, goods_price, service_price ) AS price,
            acc_purchase_orders.no_po_sap,
            acc_purchase_orders.supplier_code,
            acc_purchase_orders.supplier_name,
            acc_receive_reports.pic_receive,
            acc_receive_reports.pic_date_receive,
            acc_purchase_orders.tgl_po,
            IFNULL(acc_purchase_requisitions.submission_date,acc_investments.submission_date) as submission_date 
            FROM
            `acc_receives`
            LEFT JOIN acc_receive_reports ON acc_receive_reports.no_po = acc_receives.no_po 
            AND acc_receives.no_item = acc_receive_reports.no_item 
            LEFT JOIN acc_purchase_order_details ON acc_receives.no_po = acc_purchase_order_details.no_po
            JOIN acc_purchase_orders ON acc_purchase_orders.no_po = acc_receives.no_po 
            AND acc_receives.no_item = acc_purchase_order_details.no_item 
            LEFT JOIN acc_purchase_requisitions on acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr
            LEFT JOIN acc_investments on acc_purchase_order_details.no_pr = acc_investments.reff_number
            WHERE
            acc_receives.deleted_at IS NULL 
            AND acc_receives.`status` = 'Barang Modal' 
            and acc_purchase_orders.deleted_at IS NULL 
            AND ( acc_purchase_requisitions.submission_date is not null or acc_investments.submission_date is not null)
            and RIGHT(acc_purchase_orders.no_po,2) IN ('".$request->get('location')."')
               ORDER BY
               acc_receives.id desc
               ");

            $kedatangan_barang_all = db::select("
              SELECT DISTINCT
              acc_receives.*,
              acc_purchase_order_details.no_pr,
              IF
              ( goods_price != 0, goods_price, service_price ) AS price,
              acc_purchase_orders.no_po_sap,
              acc_purchase_orders.supplier_code,
              acc_purchase_orders.supplier_name,
              acc_receive_reports.pic_receive,
              acc_receive_reports.pic_date_receive,
              acc_purchase_orders.tgl_po,
              IFNULL(acc_purchase_requisitions.submission_date,acc_investments.submission_date) as submission_date 
              FROM
              `acc_receives`
              LEFT JOIN acc_receive_reports ON acc_receive_reports.no_po = acc_receives.no_po 
              AND acc_receives.no_item = acc_receive_reports.no_item 
              LEFT JOIN acc_purchase_order_details ON acc_receives.no_po = acc_purchase_order_details.no_po
              JOIN acc_purchase_orders ON acc_purchase_orders.no_po = acc_receives.no_po 
              AND acc_receives.no_item = acc_purchase_order_details.no_item 
              LEFT JOIN acc_purchase_requisitions on acc_purchase_order_details.no_pr = acc_purchase_requisitions.no_pr
              LEFT JOIN acc_investments on acc_purchase_order_details.no_pr = acc_investments.reff_number
              WHERE
              acc_receives.deleted_at IS NULL 
              and acc_purchase_orders.deleted_at IS NULL 
              AND ( acc_purchase_requisitions.submission_date is not null or acc_investments.submission_date is not null)
              and RIGHT(acc_purchase_orders.no_po,2) IN ('".$request->get('location')."')
               ORDER BY
               acc_receives.id desc
               ");

              $datefrom = date("Y-m-d", strtotime('-30 days'));
              $dateto = date("Y-m-d");

              $last = AccReceive::whereNull('status')
              ->whereRaw('right(no_po,2) in ("'.$request->get("location").'")')
              ->orderBy('date_receive', 'asc')
              ->select(db::raw('date(date_receive) as date_receive'))
              ->first();

              if($last){
                $tanggal = date_create($last->date_receive);
                $now = date_create(date('Y-m-d'));
                $interval = $now->diff($tanggal);
                $diff = $interval->format('%a%');

                if($diff > 30){
                  $datefrom = date('Y-m-d', strtotime($last->date_receive));
              }
          }
          
          $calendars = db::select("SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') AS `month`,  DATE_FORMAT(week_date,'%b-%y') AS month_text FROM weekly_calendars
            WHERE week_date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
            ORDER BY `month`");

          $response = array(
            'status' => true,
            'kedatangan_barang' => $kedatangan_barang,
            'kedatangan_barang_all' => $kedatangan_barang_all,
            'calendars' => $calendars
        );
          return Response::json($response);
      } catch (\Exception$e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage().' '.$e->getLine(),
        );
        return Response::json($response);
    }
}


  public function barang_modal_stock()
  {
    $title = 'Stock Barang Modal';
    $title_jp = '';

    return view('warehouse_new.barang_modal.stock_barang_modal', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Stock Barang Modal')->with('head', 'Stock Barang Modal');
}


public function fetch_barang_modal_stock()
{
    try {
          $barang_modal = db::select("SELECT
                nama_item,
                SUM( qty ) AS jumlah,
                GROUP_CONCAT( DISTINCT RIGHT ( no_po, 2 ) ) AS location 
            FROM
                acc_modal_stocks 
            GROUP BY
                nama_item 
            ORDER BY
                nama_item ASC");

          $response = array(
            'status' => true,
            'barang_modal' => $barang_modal
        );
          return Response::json($response);
      } catch (\Exception$e) {
        $response = array(
            'status' => false,
            'message' => $e->getMessage().' '.$e->getLine(),
        );
        return Response::json($response);
    }
}


public function index_kedatangan_dokumen_bc()
{
    $title = 'Kontrol Dokumen Bea Cukai';
    $title_jp = '';

    return view('warehouse_new.barang_modal.kedatangan_dokumen_bea_cukai', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Kontrol Dokumen Bea Cukai')->with('head', 'Kontrol Dokumen Bea Cukai');
}

public function fetch_kedatangan_dokumen_bc(Request $request)
{
    try {
        $surat_jalan = db::select("
            SELECT DISTINCT
            surat_jalan,
            GROUP_CONCAT(DISTINCT acc_receives.no_po) as no_po,
            acc_purchase_orders.supplier_code,
            acc_purchase_orders.supplier_name,
            date_receive,
            acc_receives.dokumen
            FROM
            acc_receives
            JOIN acc_purchase_orders ON acc_purchase_orders.no_po = acc_receives.no_po 
            WHERE
            acc_receives.deleted_at IS NULL 
            and acc_receives.`status` = 'Barang Modal'
            and surat_jalan IS NOT NULL
            GROUP BY
            surat_jalan,
            acc_purchase_orders.supplier_code,
            acc_purchase_orders.supplier_name,
            date_receive,
            acc_receives.dokumen
            ORDER BY
            acc_receives.id DESC
        ");

        // $surat_jalan_all = db::select("
        //     SELECT DISTINCT
        //     acc_receives.*,
        //     acc_purchase_order_details.no_pr,
        //     acc_purchase_orders.no_po_sap,
        //     acc_purchase_orders.supplier_code,
        //     acc_purchase_orders.supplier_name,
        //     acc_receive_reports.pic_receive,
        //     acc_receive_reports.pic_date_receive,
        //     acc_purchase_orders.tgl_po
        //     FROM
        //     `acc_receives`
        //     LEFT JOIN acc_receive_reports ON acc_receive_reports.no_po = acc_receives.no_po 
        //     AND acc_receives.no_item = acc_receive_reports.no_item 
        //     LEFT JOIN acc_purchase_order_details ON acc_receives.no_po = acc_purchase_order_details.no_po
        //     AND acc_receives.no_item = acc_purchase_order_details.no_item
        //     JOIN acc_purchase_orders ON acc_purchase_orders.no_po = acc_receives.no_po 

        //     WHERE
        //     acc_receives.deleted_at IS NULL 
        //     and acc_purchase_orders.deleted_at IS NULL 
        //     and acc_receives.`status` = 'Barang Modal'
        //     ORDER BY
        //     acc_receives.id desc
        // ");


        $datefrom = date("Y-m-d", strtotime('-30 days'));
        $dateto = date("Y-m-d");

        $last = AccReceive::whereNull('dokumen')
        ->where('status','Barang Modal')
        ->orderBy('date_receive', 'asc')
        ->select(db::raw('date(date_receive) as date_receive'))
        ->first();

        if($last){
            $tanggal = date_create($last->date_receive);
            $now = date_create(date('Y-m-d'));
            $interval = $now->diff($tanggal);
            $diff = $interval->format('%a%');

            if($diff > 30){
              $datefrom = date('Y-m-d', strtotime($last->date_receive));
          }
      }

      $calendars = db::select("SELECT DISTINCT DATE_FORMAT(week_date,'%Y-%m') AS `month`,  DATE_FORMAT(week_date,'%b-%y') AS month_text FROM weekly_calendars
        WHERE week_date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
        ORDER BY `month`");


      $response = array(
        'status' => true,
        'surat_jalan' => $surat_jalan,
        // 'surat_jalan_all' => $surat_jalan_all,
        'calendars' => $calendars
    );
      return Response::json($response);
  } catch (\Exception$e) {
    $response = array(
        'status' => false,
        'message' => $e->getMessage().' '.$e->getLine(),
    );
    return Response::json($response);
}
}


public function post_dokumen_bc(Request $request)
{
 try{
   $id_user = Auth::id();

   $att = null;
   $file_destination = 'files/dokumen_bc';
   $filenames = array();

   if ($request->get('att_count') > 0) {
    for ($i=0; $i < $request->get('att_count'); $i++) { 
       $file = $request->file('dokumen_bc_'.$i);
       $nama = $file->getClientOriginalName();
       $filename = pathinfo($nama, PATHINFO_FILENAME);
       $extension = pathinfo($nama, PATHINFO_EXTENSION);
       $filename = md5($filename.date('YmdHisa')).'.'.$extension;
            	// $filename = $request->input("surat_jalan").'_'.$nama;
       $file->move($file_destination, $filename);
       array_push($filenames, $filename);
   }
}

if (count($filenames) > 0 ) {
    $att = implode(',', $filenames);
}

	    // $tujuan_upload = 'files/dokumen_bc';
	    // $file = $request->file('dokumen_bc');
	    // $nama = $file->getClientOriginalName();
	    // $filename = pathinfo($nama, PATHINFO_FILENAME);
	    // $extension = pathinfo($nama, PATHINFO_EXTENSION);
	    // $filename = md5($filename.date('YmdHisa')).'.'.$extension;
	    // $file->move($tujuan_upload,$filename);

$update = AccReceive::where('surat_jalan','=',$request->input("surat_jalan"))
->update([
   'dokumen' => $att,
   'tanggal_upload' => date('Y-m-d')
]);

$response = array(
 'status' => true,
);
return Response::json($response);
}
catch (QueryException $e){
   $error_code = $e->errorInfo[1];
   if($error_code == 1062){
      $response = array(
         'status' => false,
         'datas' => "Audit Already Exist",
     );
      return Response::json($response);
  }
  else{
      $response = array(
         'status' => false,
         'datas' => $e->getMessage(),
     );
      return Response::json($response);
  }
}
}


public function indexNonAssetTransfer()
{
    $title = 'Non Fixed Asset Transfer Location';
    $title_jp = '??';

    return view('warehouse_new.barang_modal.transfer_form', array(
        'title' => $title,
        'title_jp' => $title_jp
    ))->with('page', 'Non Fixed Asset Transfer Location');  
}

public function fetchNonAssetTransfer()
{
    $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

    $sec = EmployeeSync::where('department', '=', $dept->department)->select('section')->groupBy('section')->get()->toArray();

    $asset_trans = FixedAssetTransfer::whereIn('old_section', $sec)->select('id', 'form_number', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") create_at'), 'fixed_asset_name', 'fixed_asset_no', 'old_section', 'old_location', 'old_pic', 'new_section', 'new_pic', 'status', 'last_status', 'reject_status')->get();

    $asset_receive = FixedAssetTransfer::whereIn('new_section', $sec)->select('id', 'form_number', db::raw('DATE_FORMAT(created_at, "%Y-%m-%d") create_at'), 'fixed_asset_name', 'fixed_asset_no', 'old_section', 'old_location', 'old_pic', 'new_section', 'status')->get();

    $response = array(
        'status' => true,
        'datas' => $asset_trans,
        'data_receives' => $asset_receive
    );
    return Response::json($response);
}



public function indexBarangModalMonitoringApproval()
{
    $title = 'Monitoring Kontrol Barang Modal';
    $title_jp = '';

    return view('warehouse_new.barang_modal.kontrol_barang_modal', array(
        'title' => $title,
        'title_jp' => $title_jp,        
    ))->with('page', 'Monitoring Kontrol Barang Modal'); 
}

public function fetchBarangModalMonitoringApproval(Request $request)
{
    $reg_out = FixedAssetInvoice::leftJoin('fixed_asset_registrations', 'fixed_asset_registrations.form_number', '=', 'fixed_asset_invoices.form_id')
    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_registrations.created_by')
    ->leftJoin(db::raw('employee_syncs es'), 'es.employee_id', '=', 'fixed_asset_invoices.created_for')
    // ->where('fixed_asset_registrations.status', '<>', 'finished')
    ->select('form_id', 'asset_name', 'pic', 'employee_syncs.name', db::raw('es.name as nm'), 'sap_id', 'manager_app', 'manager_app_date', 'manager_acc', 'manager_acc_date', 'update_fa_at', 'fixed_asset_registrations.created_by', 'fixed_asset_invoices.department', 'fixed_asset_registrations.created_at')
    ->get();

    $dispo_out = FixedAssetDisposal::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_disposals.created_by')
    ->where('status', '<>', 'finished')
    ->get();

    $dispo_data = FixedAssetDisposal::Select('*')
    ->where('status', '=', 'new_pic')
    ->get();

    $dispo_scrap_out = FixedAssetDisposalScrap::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'fixed_asset_disposal_scraps.created_by')
    ->where('status', '<>', 'finished')
    ->get();

    $label_out = FixedAssetLabel::get();

    $grafik = db::select("SELECT DATE_FORMAT(fixed_asset_invoices.created_at,'%b %Y') as mon,
        fixed_asset_registrations.form_number,
        'registrasi' as form,
        IF(fixed_asset_registrations.status = 'fa_receive', 'close', 'open') as stat
        from fixed_asset_invoices
        left join fixed_asset_registrations on fixed_asset_invoices.form_id = fixed_asset_registrations.form_number
        where fixed_asset_registrations.deleted_at is null and fixed_asset_invoices.deleted_at is null");

    $dpt_manager = db::select("SELECT department, approver_id, approver_name from approvers where remark = 'Manager'");

    $response = array(
        'status' => true,
        'registrations' => $reg_out,
        'disposals' => $dispo_out,
        'disposal_data' => $dispo_data,
        'disposal_scraps' => $dispo_scrap_out,
        'labels' => $label_out,
        'grafik' => $grafik,
        'managers' => $dpt_manager,
    );
    return Response::json($response);
}


}
