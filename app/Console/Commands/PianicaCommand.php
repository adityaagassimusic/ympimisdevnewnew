<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class PianicaCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:pianica';

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
        // $jam_audit_screw = [
        //     '07:00:00 - 09:00:00',
        //     '09:00:00 - 11:00:00',
        //     '11:00:00 - 14:00:00',
        //     '14:00:00 - 16:00:00',
        // ];

        // $qty_audit = 0;
        // $hour = 0;

        // for ($i=0; $i < count($jam_audit_screw); $i++) { 
        //     if (explode(' - ', $jam_audit_screw[$i])[1] < date('H:i:s')) {
        //         $audit = DB::connection('ympimis_2')->table('pn_screw_audits')->where('created_at','>',date('Y-m-d').' '.explode(' - ', $jam_audit_screw[$i])[0])->where('created_at','<',date('Y-m-d').' '.explode(' - ', $jam_audit_screw[$i])[1])->get();
        //         $qty_audit = count($audit);
        //         $hour = $jam_audit_screw[$i];
        //         break;
        //     }
        // }

        // if ($qty_audit == 0) {
        //     $datas = array(
        //         'audit' => null,
        //         'statuses' => 'Not Implemented',
        //         'hour' => $hour,
        //         'diff' => 0,
        //     );
        //     $mail_to = [];
        //     array_push($mail_to, 'eko.prasetyo.wicaksono@music.yamaha.com');
        //     array_push($mail_to, 'khoirul.umam@music.yamaha.com');

        //     Mail::to($mail_to)
        //     ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com','nasiqul.ibat@music.yamaha.com'])
        //     ->send(new SendEmail($datas, 'audit_screw'));
        // }

        // $stocks = 0;

        // $ymes_first = DB::connection('ymes')->select("SELECT
        //     * 
        // FROM
        //     telas.vd_mes0020
        // WHERE
        //     move_type LIKE'%SD01%' 
        //     AND in_loc_code LIKE'%PN91%' 
        //     AND item_code IN ( 'VFV1940', 'ZK19410', 'VFV1950', 'VFV2000', 'VFV2020', 'VFV1990', 'ZM55220' ) 
        //     AND instdt BETWEEN '".date('Y-m-d')." 07:00:00' :: TIMESTAMP 
        //     AND '".date('Y-m-d')." 10:30:00' :: TIMESTAMP;");

        // if (count($ymes_first) > 0) {
        //     for ($i=0; $i < count($ymes_first); $i++) { 
        //         $materials = DB::table('material_plant_data_lists')->where('material_number',$ymes_first[$i]->item_code)->first();
        //         $check = DB::connection('ympimis_2')->table('pn_stocks')->where('receive_datetime',date('Y-m-d H:i:s',strtotime($ymes_first[$i]->instdt)))->first();
        //         if (!$check) {
        //             DB::connection('ympimis_2')->table('pn_stocks')->insert([
        //                 'material_number' => $ymes_first[$i]->item_code,
        //                 'material_description' => $materials->material_description,
        //                 'date' => $ymes_first[$i]->inout_date,
        //                 'receive_datetime' => date('Y-m-d H:i:s',strtotime($ymes_first[$i]->instdt)),
        //                 'issue_loc' => $ymes_first[$i]->issue_loc_code,
        //                 'receive_loc' => $ymes_first[$i]->in_loc_code,
        //                 'quantity' => $ymes_first[$i]->inout_qty,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //             $stocks = $stocks + $ymes_first[$i]->inout_qty;
        //         }
        //     }
        // }

        // $ymes_second = DB::connection('ymes')->select("SELECT
        //     * 
        // FROM
        //     telas.vd_mes0020
        // WHERE
        //     move_type LIKE'%SD01%' 
        //     AND in_loc_code LIKE'%PN91%' 
        //     AND item_code IN ( 'VFV1940', 'ZK19410', 'VFV1950', 'VFV2000', 'VFV2020', 'VFV1990', 'ZM55220' ) 
        //     AND instdt BETWEEN '".date('Y-m-d')." 10:30:01' :: TIMESTAMP 
        //     AND '".date('Y-m-d')." 14:00:00' :: TIMESTAMP;");

        // if (count($ymes_second) > 0) {
        //     for ($i=0; $i < count($ymes_second); $i++) { 
        //         $materials = DB::table('material_plant_data_lists')->where('material_number',$ymes_second[$i]->item_code)->first();
        //         $check = DB::connection('ympimis_2')->table('pn_stocks')->where('receive_datetime',date('Y-m-d H:i:s',strtotime($ymes_second[$i]->instdt)))->first();
        //         if (!$check) {
        //             DB::connection('ympimis_2')->table('pn_stocks')->insert([
        //                 'material_number' => $ymes_second[$i]->item_code,
        //                 'material_description' => $materials->material_description,
        //                 'date' => $ymes_second[$i]->inout_date,
        //                 'receive_datetime' => date('Y-m-d H:i:s',strtotime($ymes_second[$i]->instdt)),
        //                 'issue_loc' => $ymes_second[$i]->issue_loc_code,
        //                 'receive_loc' => $ymes_second[$i]->in_loc_code,
        //                 'quantity' => $ymes_second[$i]->inout_qty,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //             $stocks = $stocks + $ymes_first[$i]->inout_qty;
        //         }
        //     }
        // }

        // $ymes_third = DB::connection('ymes')->select("SELECT
        //     * 
        // FROM
        //     telas.vd_mes0020
        // WHERE
        //     move_type LIKE'%SD01%' 
        //     AND in_loc_code LIKE'%PN91%' 
        //     AND item_code IN ( 'VFV1940', 'ZK19410', 'VFV1950', 'VFV2000', 'VFV2020', 'VFV1990', 'ZM55220' ) 
        //     AND instdt BETWEEN '".date('Y-m-d')." 14:00:01' :: TIMESTAMP 
        //     AND '".date('Y-m-d')." 19:00:00' :: TIMESTAMP;");

        // if (count($ymes_third) > 0) {
        //     for ($i=0; $i < count($ymes_third); $i++) { 
        //         $materials = DB::table('material_plant_data_lists')->where('material_number',$ymes_third[$i]->item_code)->first();
        //         $check = DB::connection('ympimis_2')->table('pn_stocks')->where('receive_datetime',date('Y-m-d H:i:s',strtotime($ymes_third[$i]->instdt)))->first();
        //         if (!$check) {
        //             DB::connection('ympimis_2')->table('pn_stocks')->insert([
        //                 'material_number' => $ymes_third[$i]->item_code,
        //                 'material_description' => $materials->material_description,
        //                 'date' => $ymes_third[$i]->inout_date,
        //                 'receive_datetime' => date('Y-m-d H:i:s',strtotime($ymes_third[$i]->instdt)),
        //                 'issue_loc' => $ymes_third[$i]->issue_loc_code,
        //                 'receive_loc' => $ymes_third[$i]->in_loc_code,
        //                 'quantity' => $ymes_third[$i]->inout_qty,
        //                 'created_at' => date('Y-m-d H:i:s'),
        //                 'updated_at' => date('Y-m-d H:i:s'),
        //             ]);
        //             $stocks = $stocks + $ymes_first[$i]->inout_qty;
        //         }
        //     }
        // }

        // $message = urlencode("PIANICA STOCK Synced at " . date("Y-m-d H:i:s"));

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message=' . $message . '&type=chat',
        //     CURLOPT_HTTPHEADER => array(
        //         'Accept: application/json',
        //         'Content-Type: application/x-www-form-urlencoded',
        //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
        //     ),
        // ));
        // curl_exec($curl);

        // $curl = curl_init();
        // curl_setopt_array($curl, array(
        //     CURLOPT_URL => 'https://app.whatspie.com/api/messages',
        //     CURLOPT_RETURNTRANSFER => true,
        //     CURLOPT_ENCODING => '',
        //     CURLOPT_MAXREDIRS => 10,
        //     CURLOPT_TIMEOUT => 0,
        //     CURLOPT_FOLLOWLOCATION => true,
        //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        //     CURLOPT_CUSTOMREQUEST => 'POST',
        //     CURLOPT_POSTFIELDS => 'receiver=628980198771&device=6281130561777&message=' . $message . '&type=chat',
        //     CURLOPT_HTTPHEADER => array(
        //         'Accept: application/json',
        //         'Content-Type: application/x-www-form-urlencoded',
        //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
        //     ),
        // ));
        // curl_exec($curl);

        // $bodyHtml2 = "<html><h2>PIANICA STOCK FROM YMES</h2><p>Sycned At : <b>".date('Y-m-d H:i:s')."</b><br>Stock : <b>".$stocks." Pc(s)</b></p></html>";

        // $mail_to = [];

        // array_push($mail_to, 'nasiqul.ibat@music.yamaha.com');
        // array_push($mail_to, 'mokhamad.khamdan.khabibi@music.yamaha.com');

        //   Mail::raw([], function($message) use($bodyHtml2,$mail_to) {
        //       $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
        //       $message->to($mail_to);
        //       $message->subject('PIANICA STOCK');
        //       $message->setBody($bodyHtml2, 'text/html' );
        //   });
    }
}
