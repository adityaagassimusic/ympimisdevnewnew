<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\CanteenLiveCooking;
use App\GeneralAttendance;
use App\Employee;
use App\AssemblyTarget;
use App\Bento;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\DB;

class LiveCookingCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:live_cooking';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
      parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

      $delete_sessions = db::select("DELETE 
        FROM
        sessions 
        WHERE
        TIMESTAMPDIFF(
          DAY,
          from_unixtime( last_activity, '%Y-%m-%d' ),
          now()) > 30");

      $now = date('Y-m-d');

      if ($now == date('Y-m-t')) {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatspie.com/api/messages',
          CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=REMINDER!!!%0A%0AMembuat%20Schedule%20Chorei%20MIS%20Bulanan.&type=chat',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
          ),
        ));
        curl_exec($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatspie.com/api/messages',
          CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'receiver=6281554119011&device=6281130561777&message=REMINDER!!!%0A%0AMembuat%20Schedule%20Chorei%20MIS%20Bulanan.&type=chat',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
          ),
        ));
        curl_exec($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatspie.com/api/messages',
          CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'receiver=6282244167224&device=6281130561777&message=REMINDER!!!%0A%0AMembuat%20Schedule%20Chorei%20MIS%20Bulanan.&type=chat',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
          ),
        ));
        curl_exec($curl);

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatspie.com/api/messages',
          CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=REMINDER!!!%0A%0AMembuat%20Schedule%20Chorei%20MIS%20Bulanan.&type=chat',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
          ),
        ));
        curl_exec($curl);


      }

      $live_cooking_attendance = DB::SELECT("SELECT
            *,
        canteen_live_cookings.id AS id_live 
        FROM
        `canteen_live_cookings`
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = canteen_live_cookings.order_for
        LEFT JOIN canteen_live_cooking_menus ON canteen_live_cooking_menus.due_date = canteen_live_cookings.due_date 
        WHERE
        canteen_live_cookings.due_date = DATE(NOW())
        AND attendance_generate_status = 0");

      for ($i=0; $i < count($live_cooking_attendance); $i++) { 
        $general_attendance = GeneralAttendance::create(
          [
            'purpose_code' => 'Live Cooking',
            'due_date' => $live_cooking_attendance[$i]->due_date,
            'employee_id' => $live_cooking_attendance[$i]->order_for,
            'created_by' => '1930'
          ]
        );
        $general_attendance->save();

        $liveupdate = CanteenLiveCooking::where('id',$live_cooking_attendance[$i]->id_live)->first();
        $liveupdate->attendance_generate_status = 1;
        $liveupdate->save();
      }

      $now = date('Y-m-d');

      if ($now == date('Y-m-01')) {
        $emp = Employee::where('end_date',null)->where('live_cooking',1)->get();
        if (count($emp) > 0) {
          foreach ($emp as $key) {
            $emp_edit = Employee::where('id',$key->id)->first();
            $emp_edit->live_cooking = 0;
            $emp_edit->save();
          }
        }
      }

      $live_cooking = DB::SELECT("SELECT
          *,
        canteen_live_cookings.id AS id_live 
        FROM
        `canteen_live_cookings`
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = canteen_live_cookings.order_for
        LEFT JOIN canteen_live_cooking_menus ON canteen_live_cooking_menus.due_date = canteen_live_cookings.due_date 
        WHERE
        whatsapp_status = 0 
        AND canteen_live_cookings.due_date = DATE(
          NOW())");

      if (count($live_cooking) > 0) {

        foreach ($live_cooking as $key) {

              // var_dump($key->menu_name);
              // die();
          $due_date = date('d F Y',strtotime($key->due_date));

          $due_date_replace = str_replace(" ","%20",$due_date);
          $menu_name = str_replace(" ","%20",ucwords($key->menu_name));

          if(substr($key->phone, 0, 1) == '+' ){
           $phone = substr($key->phone, 1, 15);
         }
         else if(substr($key->phone, 0, 1) == '0'){
           $phone = "62".substr($key->phone, 1, 15);
         }
         else{
           $phone = $key->phone;
         }

                // $phone = '6285645896741';

         $curl = curl_init();

         curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://app.whatspie.com/api/messages',
      CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=Anda%20telah%20terjadwal%20Live%20Cooking%20pada%20tanggal%20'.$due_date_replace.'%20dengan%20menu%20'.$menu_name.'.%0ASilahkan%20cek%20di%20MIRAI.%0A%0A-YMPI%20MIS%20Dept.-&type=chat',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
          ),
        ));
         curl_exec($curl);

         $liveupdate = CanteenLiveCooking::where('id',$key->id_live)->first();
         $liveupdate->whatsapp_status = 1;
         $liveupdate->save();
       }
     }

     $dayName = date('D',strtotime(date('Y-m-d')));

     if ($dayName == 'Fri') {
      $admin = DB::SELECT("SELECT DISTINCT
        ( canteen_live_cooking_admins.employee_id ),
        employee_syncs.`name`,
        employee_syncs.phone 
        FROM
        canteen_live_cooking_admins
        JOIN employee_syncs ON employee_syncs.employee_id = canteen_live_cooking_admins.employee_id 
        ORDER BY
        employee_syncs.`name`");

      for ($i=0; $i < count($admin); $i++) { 
        if(substr($admin[$i]->phone, 0, 1) == '+' ){
         $phone = substr($admin[$i]->phone, 1, 15);
       }
       else if(substr($admin[$i]->phone, 0, 1) == '0'){
         $phone = "62".substr($admin[$i]->phone, 1, 15);
       }
       else{
         $phone = $admin[$i]->phone;
       }

       $curl = curl_init();

       curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.whatspie.com/api/messages',
      CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=REMINDER%0A%0AKami%20mengingatkan%20Anda%20untuk%20input%20Live%20Cooking%20di%20MIRAI.%0AJika%20sudah%20melakukan%20input,%20abaikan%20pesan%20berikut.%0A%0A-YMPI%20MIS%20Dept.-&type=chat',
        CURLOPT_HTTPHEADER => array(
          'Accept: application/json',
          'Content-Type: application/x-www-form-urlencoded',
          'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
        ),
      ));
       curl_exec($curl);
     }
   }

   $bentos = DB::SELECT("SELECT
      bentos.id,
      bentos.employee_id,
      bentos.employee_name,
      bentos.whatsapp_status,
      employee_syncs.phone,
      bentos.due_date,
      bento_quotas.menu 
    FROM
      bentos
      JOIN employee_syncs ON employee_syncs.employee_id = bentos.employee_id
      JOIN bento_quotas ON bento_quotas.due_date = bentos.due_date 
    WHERE
      bentos.due_date = DATE(
      NOW()) 
      AND `status` = 'Approved' 
      AND location = 'YMPI' 
      AND whatsapp_status IS NULL 
      AND bentos.employee_id != 'PI0109004' 
      AND bentos.employee_id != 'PI9709001' 
      AND bentos.grade_code != 'J0-'");

   if (count($bentos) > 0) {
    foreach ($bentos as $key) {
      $due_date = date('d F Y',strtotime($key->due_date));

      $due_date_replace = str_replace(" ","%20",$due_date);
      $menu = str_replace(" ","%20",$key->menu);

      $employee_name = str_replace(" ","%20",$key->employee_name);

      if(substr($key->phone, 0, 1) == '+' ){
       $phone = substr($key->phone, 1, 15);
     }
     else if(substr($key->phone, 0, 1) == '0'){
       $phone = "62".substr($key->phone, 1, 15);
     }
     else{
       $phone = $key->phone;
     }


     $curl = curl_init();

     curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://app.whatspie.com/api/messages',
      CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => 'receiver='.$phone.'&device=6281130561777&message=Dear%20'.$employee_name.',%0A%0AAnda%20telah%20terjadwal%20Bento%20pada%20tanggal%20'.$due_date_replace.'%20dengan%20menu%20'.$menu.'.%0ASilahkan%20cek%20di%20MIRAI.%0A%0A-YMPI%20MIS%20Dept.-&type=chat',
      CURLOPT_HTTPHEADER => array(
        'Accept: application/json',
        'Content-Type: application/x-www-form-urlencoded',
        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
      ),
    ));
     curl_exec($curl);

     $bentoupdate = Bento::where('id',$key->id)->first();
     $bentoupdate->whatsapp_status = 1;
     $bentoupdate->save();
   }
 }

  $j = 2;
  $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays($j)));
  $weekly_calendars = DB::SELECT("SELECT * FROM `weekly_calendars`");
  foreach ($weekly_calendars as $key) {
      if ($key->week_date == $nextdayplus1) {
          if ($key->remark == 'H') {
              $nextdayplus1 = date('Y-m-d', strtotime(carbon::now()->addDays(++$j)));
          }
      }
  }


 $flute = DB::SELECT("SELECT
      a.material_number,
      a.material_description,
      sum( a.plan ) AS plan,
      sum( a.actual ) AS actual,
      sum( a.packing ) AS packing,
      sum( a.plan ) - (
      sum( a.actual )+ sum( a.packing )) AS debt 
    FROM
      (
      SELECT
        production_schedules.material_number,
        materials.material_description,
        SUM( quantity ) AS plan,
        0 AS actual,
        0 AS packing 
      FROM
        `production_schedules`
        JOIN materials ON materials.material_number = production_schedules.material_number 
      WHERE
        production_schedules.due_date >= '".date('Y-m-01')."' 
        AND production_schedules.due_date <= '".$nextdayplus1."' 
        AND materials.origin_group_code = 041 
        AND category = 'FG' 
      GROUP BY
        production_schedules.material_number,
        materials.material_description UNION ALL
      SELECT
        flo_details.material_number,
        materials.material_description,
        0 AS plan,
        count( flo_details.serial_number ) AS actual,
        0 AS packing 
      FROM
        flo_details
        JOIN materials ON materials.material_number = flo_details.material_number 
      WHERE
        DATE( flo_details.created_at ) >= '".date('Y-m-01')."' 
        AND DATE( flo_details.created_at ) <= '".$nextdayplus1."' 
        AND flo_details.origin_group_code = 041 
        AND materials.category = 'FG' 
      GROUP BY
        flo_details.material_number,
        materials.material_description UNION ALL
      SELECT
        materials.material_number,
        assembly_logs.model AS material_description,
        0 AS plan,
        0 AS actual,
        count(
        DISTINCT ( assembly_logs.serial_number )) AS packing 
      FROM
        assembly_logs
        JOIN materials ON materials.material_description = assembly_logs.model 
      WHERE
        location = 'packing' 
        AND DATE( assembly_logs.created_at ) >= '".date('Y-m-01')."' 
        AND DATE( assembly_logs.created_at ) <= '".$nextdayplus1."' 
        AND assembly_logs.serial_number NOT IN ( SELECT serial_number FROM flo_details WHERE origin_group_code = 041 ) 
      GROUP BY
        assembly_logs.model,
        materials.material_number 
      ) a 
    GROUP BY
      a.material_number,
      a.material_description");

 if (count($flute) > 0) {
   for ($i=0; $i < count($flute); $i++) { 
     if ($flute[$i]->debt > 0) {
       $target = DB::connection('ympimis_2')->table('assembly_targets')->updateOrInsert([
        'material_number' => $flute[$i]->material_number,
        'material_description' => $flute[$i]->material_description,
        'due_date' => date('Y-m-d'),
       ],[
          'material_number' => $flute[$i]->material_number,
          'material_description' => $flute[$i]->material_description,
          'due_date' => date('Y-m-d'),
          'quantity' => ($flute[$i]->debt),
          'created_by' => 1,
          'created_at' => date('Y-m-d H:i:s'),
          'updated_at' => date('Y-m-d H:i:s'),
         ]);
     }
   }
 }

 $stocks = 0;

  $ymes_first = DB::connection('ymes')->select("SELECT
    * 
  FROM
    telas.vd_mes0020 
  WHERE
    move_type LIKE'%SD01%' 
    AND in_loc_code LIKE'%PN91%' 
    AND item_code IN ( 'VFV1940', 'ZK19410', 'VFV1950', 'VFV2000', 'VFV2020', 'VFV1990', 'ZM55220' ) 
    AND inout_date = '".date('Y-m-d')."' :: TIMESTAMP;");

  if (count($ymes_first) > 0) {
      for ($i=0; $i < count($ymes_first); $i++) { 
          $materials = DB::table('material_plant_data_lists')->where('material_number',$ymes_first[$i]->item_code)->first();
          $check = DB::connection('ympimis_2')->table('pn_stock_histories')->where('receive_datetime',date('Y-m-d H:i:s',strtotime($ymes_first[$i]->instdt)))->first();
          if (!$check) {
              DB::connection('ympimis_2')->table('pn_stock_histories')->insert([
                  'material_number' => $ymes_first[$i]->item_code,
                  'material_description' => $materials->material_description,
                  'date' => $ymes_first[$i]->inout_date,
                  'receive_datetime' => date('Y-m-d H:i:s',strtotime($ymes_first[$i]->instdt)),
                  'issue_loc' => $ymes_first[$i]->issue_loc_code,
                  'receive_loc' => $ymes_first[$i]->in_loc_code,
                  'quantity' => $ymes_first[$i]->inout_qty,
                  'created_at' => date('Y-m-d H:i:s'),
                  'updated_at' => date('Y-m-d H:i:s'),
              ]);
              $stocks = $stocks + $ymes_first[$i]->inout_qty;
              $material = DB::table('materials')->where('material_number',$ymes_first[$i]->item_code)->first();
              $check_stock = DB::connection('ympimis_2')->table('pn_stocks')->where('model',$material->model)->first();
              if ($check_stock) {
                $update_stock = DB::connection('ympimis_2')->table('pn_stocks')->where('model',$material->model)->update([
                  'quantity' => $check_stock->quantity+$ymes_first[$i]->inout_qty,
                  'updated_at' => date('Y-m-d H:i:s'),
                ]);
              }else{
                $input_stock = DB::connection('ympimis_2')->table('pn_stocks')->insert([
                  'model' => $material->model,
                  'quantity' => $ymes_first[$i]->inout_qty,
                  'created_by' => '1930',
                  'created_at' => date('Y-m-d H:i:s'),
                  'updated_at' => date('Y-m-d H:i:s'),
                ]);
              }
          }
      }
  }

  if ($stocks > 0) {
    $message = urlencode("PIANICA STOCK Synced at " . date("Y-m-d H:i:s").'. Stock : '.$stocks.' Pc(s).');

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
        ),
    ));
    curl_exec($curl);

    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message=' . $message . '&type=chat',
        CURLOPT_HTTPHEADER => array(
            'Accept: application/json',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
        ),
    ));
    curl_exec($curl);

    $bodyHtml2 = "<html><h2>PIANICA STOCK FROM YMES</h2><p>Sycned At : <b>".date('Y-m-d H:i:s')."</b><br>Stock : <b>".$stocks." Pc(s)</b></p></html>";

    $mail_to = [];

    array_push($mail_to, 'nasiqul.ibat@music.yamaha.com');
    array_push($mail_to, 'mokhamad.khamdan.khabibi@music.yamaha.com');

      Mail::raw([], function($message) use($bodyHtml2,$mail_to) {
          $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
          $message->to($mail_to);
          $message->subject('PIANICA STOCK');
          $message->setBody($bodyHtml2, 'text/html' );
      });
  }
}
}
