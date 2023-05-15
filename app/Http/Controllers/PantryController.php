<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use DataTables;
use Response;
use App\PantryOrder;
use App\PantryMenu;
use App\PantryLog;
use App\User;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;

class PantryController extends Controller
{
    var $bot_token = '1605038829:AAHNqENTLWIESB_Q5tI_JFMoI4TPD_BaoEM';
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexDisplayPantryVisit(){
        $title = 'Pantry Visitor Monitoring';
        $title_jp = '給湯室の来室者監視';

        return view('pantry.visitor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Pantry')->with('head', 'Pantry');
    }

// public function gw_send_sms($user,$pass,$sms_from,$sms_to,$sms_msg)  
// {           
//     $query_string = "api.aspx?apiusername=".$user."&apipassword=".$pass;
//     $query_string .= "&senderid=".rawurlencode($sms_from)."&mobileno=".rawurlencode($sms_to);
//     $query_string .= "&message=".rawurlencode(stripslashes($sms_msg)) . "&languagetype=1";        
//     $url = "http://gateway.onewaysms.com.au:10001/".$query_string;       
//     $fd = @implode('', file($url));      
//     if ($fd)  
//     {                       
//         if ($fd > 0) {
//             Print("MT ID : " . $fd);
//             $ok = "success";
//         }        
//         else {
//             print("Please refer to API on Error : " . $fd);
//             $ok = "fail";
//         }
//     }           
//     else      
//     {                       
//         $ok = "fail";       
//     }           
//     return $ok;  
// } 

    public function fetchPantryVisitorDetail(Request $request){
        $date = date('Y-m-d');
        $day = date('N');
        $query = "";

        if(strlen($request->get('tanggal')) > 0){
            $date = date('Y-m-d', strtotime($request->get('tanggal')));
            $day = date('N', strtotime($request->get('tanggal')));
        }

        $where = "";

        if($day == 5){
            $where = "AND (
            TIME( in_time ) BETWEEN '07:00:00' 
            AND '09:20:00' 
            OR time( in_time ) BETWEEN '09:30:00' 
            AND '12:00:00' 
            OR time( in_time ) BETWEEN '13:10:00' 
            AND '14:50:00' 
            OR time( in_time ) BETWEEN '15:00:00' 
            AND '16:00:00' ) ";
        }
        else{
            $where = "AND (
            TIME( in_time ) BETWEEN '07:00:00' 
            AND '09:20:00' 
            OR time( in_time ) BETWEEN '09:30:00' 
            AND '12:00:00' 
            OR time( in_time ) BETWEEN '12:40:00' 
            AND '14:20:00' 
            OR time( in_time ) BETWEEN '14:30:00' 
            AND '16:00:00' ) ";
        }

        if($request->get('type') == 'duration'){
            $query = "SELECT
            pantry_logs.employee_id,
            ympimis.employee_syncs.name,
            time( pantry_logs.in_time ) AS in_time,
            time( pantry_logs.out_time ) AS out_time,
            round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) AS duration 
            FROM
            pantry_logs
            LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = pantry_logs.employee_id 
            WHERE
            date( in_time ) = '".$date."' 
            ".$where."
            AND pantry_logs.employee_id in (
            SELECT
            employee_id 
            FROM
            (
            SELECT
            final.employee_id,
            IF
            (
            duration < 1, '<1 Min', IF ( duration >= 1 
            AND duration < 2, '<2 Min', IF ( duration >= 2 
            AND duration < 3, '<3 Min', IF ( duration >= 3 
            AND duration < 4, '<4 Min', IF ( duration >= 4 
            AND duration < 5, '<5 Min', IF ( duration >= 5 
            AND duration < 6, '<6 Min', IF ( duration >= 6 
            AND duration < 7, '<7 Min', IF ( duration >= 7 
            AND duration < 8,
            '<8 Min',
            '>8 Min' 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) 
            ) AS dur 
            FROM
            (
            SELECT
            employee_id,
            SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
            FROM
            pantry_logs 
            WHERE
            date( in_time ) = '".$date."'
            AND round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) > 0.1
            ".$where." 
            GROUP BY
            employee_id
            ) AS final 
            HAVING
            dur = '".$request->get('category')."' 
            ORDER BY
            duration DESC 
            ) AS final2
            )
            HAVING
            duration > 0.1
            ORDER BY
            pantry_logs.employee_id ASC,
            pantry_logs.in_time ASC";
        }
        else{
            $query = "SELECT
            pantry_logs.employee_id,
            ympimis.employee_syncs.name,
            time( pantry_logs.in_time ) AS in_time,
            time( pantry_logs.out_time ) AS out_time,
            concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) AS jam,
            round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) AS duration 
            FROM
            pantry_logs
            LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = pantry_logs.employee_id 
            WHERE
            date( in_time ) = '".$date."'
            AND round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) > 0.1
            ".$where."
            HAVING
            jam = '".$request->get('category')."' 
            ORDER BY
            duration DESC";
        }

        $details = db::connection('pantry')->select($query);

        $response = array(
            'status' => true,
            'details' => $details
        );
        return Response::json($response);
    }

    public function pesanmenu()
    {
        $title = 'Pantry Item Order';
        $title_jp = '給湯室注文品';
        $menus = PantryMenu::whereNull('deleted_at')->get();

        $menutop = PantryMenu::whereNull('deleted_at')->limit(3)->get();
        $menubot = PantryMenu::whereNull('deleted_at')->skip(3)->limit(3)->get();

        $username = Auth::user()->username;
        $user = "select name from users where username='$username'";
        $users = DB::select($user);

        return view('pantry.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'menus' => $menus,
            'menutop' => $menutop,
            'menubot' => $menubot,
            'users' => $users
        ))->with('page', 'Pantry')->with('head', 'Pantry');
    }

    public function daftarpesanan(){
        $title = 'Pantry Order List';
        $title_jp = '注文内容';
        $orders = PantryOrder::where('status', 'confirmed')
        ->get();

        return view('pantry.pesanan', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'orders' => $orders
        ))->with('page', 'Pantry Orders')->with('head', 'Pantry');
    }

    public function fetchPantryRealtimeVisitor() {
        $visitors = db::connection('pantry')->table('pantry_lists')
        ->leftJoin('ympimis.employee_syncs', 'ympimis.employee_syncs.employee_id', '=', 'pantry_lists.employee_id')
        ->select('pantry_lists.employee_id', 'ympimis.employee_syncs.name', 'pantry_lists.in_time')
        ->orderBy('pantry_lists.in_time', 'desc')
        ->get();

        $response = array(
            'status' => true,
            'visitors' => $visitors
        );
        return Response::json($response);
    }

    public function fetchPantryVisitor(Request $request){
        $date = date('Y-m-d');
        $day = date('N');
        $query = "";

        if(strlen($request->get('tanggal')) > 0){
            $date = date('Y-m-d', strtotime($request->get('tanggal')));
            $day = date('N', strtotime($request->get('tanggal')));
        }

        $where = "";

        if($day == 5){
            $where = "AND (
            TIME( in_time ) BETWEEN '07:00:00' 
            AND '09:20:00' 
            OR time( in_time ) BETWEEN '09:30:00' 
            AND '12:00:00' 
            OR time( in_time ) BETWEEN '13:10:00' 
            AND '14:50:00' 
            OR time( in_time ) BETWEEN '15:00:00' 
            AND '16:00:00' ) ";
        }
        else{
            $where = "AND (
            TIME( in_time ) BETWEEN '07:00:00' 
            AND '09:20:00' 
            OR time( in_time ) BETWEEN '09:30:00' 
            AND '12:00:00' 
            OR time( in_time ) BETWEEN '12:40:00' 
            AND '14:20:00' 
            OR time( in_time ) BETWEEN '14:30:00' 
            AND '16:00:00' ) ";
        }

        $query = "SELECT
        jam,
        count( employee_id ) AS qty_visit 
        FROM
        (
        SELECT DISTINCT
        pantry_logs.employee_id,
        concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) AS jam 
        FROM
        pantry_logs
        LEFT JOIN ympimis.employee_syncs ON ympimis.employee_syncs.employee_id = pantry_logs.employee_id 
        WHERE
        date( in_time ) = '".$date."'
        AND round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) > 0.1
        ".$where." 
        GROUP BY
        pantry_logs.employee_id,
        concat( DATE_FORMAT( in_time, '%H:00' ), ' - ', DATE_FORMAT( date_add( in_time, INTERVAL 1 HOUR ), '%H:00' ) ) 
        ) AS final 
        GROUP BY
        jam";

        $query2 = "SELECT
        count( employee_id ) AS qty_employee,
        sum( duration ) AS qty_duration 
        FROM
        (
        SELECT
        employee_id,
        SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
        FROM
        pantry_logs 
        WHERE
        date( in_time ) = '".$date."'
        AND round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) > 0.1
        ".$where."
        GROUP BY
        employee_id 
        HAVING
        duration > 0.1) AS final";

        $query3 = "SELECT
        count(employee_id) as qty_employee, duration, indek 
        FROM
        (
        SELECT
        employee_id,
        IF
        (
        duration < 1, '<1 Min', IF ( duration >= 1 
        AND duration < 2, '<2 Min', IF ( duration >= 2 
        AND duration < 3, '<3 Min', IF ( duration >= 3 
        AND duration < 4, '<4 Min', IF ( duration >= 4 
        AND duration < 5, '<5 Min', IF ( duration >= 5 
        AND duration < 6, '<6 Min', IF ( duration >= 6 
        AND duration < 7, '<7 Min', IF ( duration >= 7 
        AND duration < 8,
        '<8 Min',
        '>8 Min' 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) AS duration,
        IF
        (
        duration < 1, 1, IF ( duration >= 1 
        AND duration < 2, 2, IF ( duration >= 2 
        AND duration < 3, 3, IF ( duration >= 3 
        AND duration < 4, 4, IF ( duration >= 4 
        AND duration < 5, 5, IF ( duration >= 5 
        AND duration < 6, 6, IF ( duration >= 6 
        AND duration < 7, 7, IF ( duration >= 7 
        AND duration < 8,
        8,
        9 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) 
        ) AS indek 
        FROM
        (
        SELECT
        employee_id,
        SUM( round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) ) AS duration 
        FROM
        pantry_logs 
        WHERE
        date( in_time ) = '".$date."'
        AND round( TIMESTAMPDIFF( SECOND, pantry_logs.in_time, pantry_logs.out_time ) / 60, 2 ) > 0.1
        ".$where."
        GROUP BY
        employee_id 
        ) AS final 
        ) AS final2
        group by duration, indek order by indek asc";

        $query4 = "SELECT
        office_members.employee_id,
        employee_syncs.NAME 
        FROM
        office_members
        LEFT JOIN employee_syncs ON employee_syncs.employee_id = office_members.employee_id 
        WHERE
        office_members.employee_id NOT IN ( SELECT employee_id FROM ympipantry.pantry_logs WHERE date( in_time ) = '".$date."' GROUP BY employee_id ) 
        AND office_members.remark = 'office' 
        AND office_members.employee_id LIKE 'PI%' 
        AND employee_syncs.employee_id IS NOT NULL";

        $hourly = db::connection('pantry')->select($query);
        $total = db::connection('pantry')->select($query2);
        $duration = db::connection('pantry')->select($query3);
        $novisit = db::select($query4);

        $response = array(
            'status' => true,
            'hourly' => $hourly,
            'total' => $total,
            'duration' => $duration,
            'novisit' => $novisit

        );
        return Response::json($response);
    }

    public function fetchMenu(Request $request){

        $pemesan = Auth::user()->username;

        $pantry = PantryMenu::find($request->get("id"));
        $menu = $pantry->menu;
// if ($request->get('id') == 1) {
//     $menu = "Tea";
// } else if ($request->get('id') == 2) {
//     $menu = "Coffee";
// } else if ($request->get('id') == 3) {
//     $menu = "Oca";
// } else if ($request->get('id') == 4) {
//     $menu = "Water";
// }

        $response = array(
            'status' => true,
            'menu' => $menu,
            'pemesan' => $pemesan
        );
        return Response::json($response);
    }

    public function inputMenu(Request $request){
        try{
            $id_user = Auth::id();
            $menu = new PantryOrder([
                'pemesan' => $request->get('pemesan'),
                'minuman' => $request->get('menu'),
                'informasi' => $request->get('informasi'),
                'keterangan' => $request->get('keterangan'),
                'gula' => $request->get('gula'),
                'jumlah' => $request->get('jumlah'),
                'tempat' => $request->get('tempat'),
                'status' => 'unconfirmed',
                'tgl_pesan' => date('Y-m-d'),
                'created_by' => $id_user
            ]);

            $menu->save();

            $response = array(
                'status' => true,
                'message' => 'Minuman Berhasil Dikonfirmasi',
            );
            return Response::json($response);
        }
        catch(\Exception $e){
            $response = array(
                'status' => false,
                'message' => 'Pilih Item Terlebih Dahulu <br>注文品を予め選択してください',
            );
            return Response::json($response);
        }
    }

    public function deleteMenu(Request $request)
    {
        $pantry = PantryOrder::find($request->get("id"));
        $pantry->delete();

        $response = array(
            'status' => true
        );
        return Response::json($response);
    }

    public function fetchpesanan(Request $request){

        $datenow = date('Y-m-d');

        $lists = PantryOrder::where('pemesan', '=', $request->get('pemesan'))
        ->where('status', '=', 'unconfirmed')
        ->where('tgl_pesan', '=', $datenow)
        ->get();

        $response = array(
            'status' => true,
            'lists' => $lists,
        );
        return Response::json($response);
    }

    public function konfirmasipesanan(Request $request)
    {
        try{

            $emp = User::where('username','=',$request->get("pemesan"))->first();
            $name = $emp->name;

            $order = PantryOrder::where('pemesan', '=', $request->get("pemesan"))->where('status', 'unconfirmed')->get();

            if (count($order) == 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Pilih Item Terlebih Dahulu <br>注文品を予め選択してください',
                );
                return Response::json($response);
            }


            PantryOrder::where('pemesan', '=', $request->get("pemesan"))->where('status', 'unconfirmed')->update([
                'status' => 'confirmed'
            ]);

            $item = [];
            $item_email = [];
            $index = 1;
            $antar = "";
            foreach ($order as $key) {
                array_push($item, $index.'.%20'.$key->minuman.'%20-%20'.$key->informasi.'%20-%20'.$key->keterangan.'%20-%20'.$key->gula.'%20-%20Jumlah%20:%20'.$key->jumlah);
                array_push($item_email, ''.$index.'. '.$key->minuman.' - '.$key->informasi.' - '.$key->keterangan.' - '.$key->gula.' - Jumlah : '.$key->jumlah);
                $antar = $key->tempat;
                $index++;
            }

            // $query_string = "api.aspx?apiusername=API3Y9RTZ5R6Y&apipassword=API3Y9RTZ5R6Y3Y9RT";
            // $query_string .= "&senderid=".rawurlencode("PT YMPI")."&mobileno=".rawurlencode("62811372398");
            // $query_string .= "&message=".rawurlencode(stripslashes("Ada Pesanan Pantry Dari ".$name.", Mohon untuk segera dibuatkan. Terimakasih")) . "&languagetype=1";        
            // $url = "http://gateway.onewaysms.co.id:10002/".$query_string;       
            // $fd = @implode('', file($url));

            // $phone = [];
            // array_push($phone, '62811372398');
            // array_push($phone, '6281554119011');
            // array_push($phone, '6282336050550');

            $pesan = "Ada%20Pesanan%20Pantry%20:%0A".join('%0A',$item)."%0A%0ADipesan%20pada%20".date('d M Y H:i').".%0ADari%20".$name.".%0ADiantar%20ke%20".$antar.".%0A%0AMohon%20untuk%20segera%20dibuatkan.%0ATerimakasih.%0A%0AYMPI%20MIS%20Dept";

            $bodyHtml2 = "<html><h2>Pantry Order</h2><p>Ada Pesanan Pantry<br> ".join('<br>',$item_email)."</p><p>Dipesan pada : ".date('d M Y H:i')."</p><p>Dari : ".$name."</p><p>Diantar ke : ".$antar."</p><p>Mohon segera dibuatkan. Terimakasih.</p></html>";

            $bcc = [];
            $bcc[0] = "mokhamad.khamdan.khabibi@music.yamaha.com";
            $bcc[1] = "rio.irvansyah@music.yamaha.com";
            $bcc[2] = "anton.budi.santoso@music.yamaha.com";

            $mail_to = [];
            $mail_to[0] = "rianita.widiastuti@music.yamaha.com";
            $mail_to[1] = "putri.sukma.riyanti@music.yamaha.com";

            Mail::raw([], function($message) use($bodyHtml2,$mail_to,$bcc) {
                $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
                $message->to($mail_to);
                $message->bcc($bcc);
                $message->subject('Pantry Order');
                $message->setBody($bodyHtml2, 'text/html' );
            });

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
              CURLOPT_POSTFIELDS => 'receiver=62811372398&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

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
              CURLOPT_POSTFIELDS => 'receiver=6281554119011&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

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
              CURLOPT_POSTFIELDS => 'receiver=6282336050550&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

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
              CURLOPT_POSTFIELDS => 'receiver=6282334197238&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

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
              CURLOPT_POSTFIELDS => 'receiver=628113669871&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

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
              CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message='.$pesan.'&type=chat',
              CURLOPT_HTTPHEADER => array(
                'Accept: application/json',
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU'
              ),
            ));
            curl_exec($curl);

            curl_close($curl);

// $sms = gw_send_sms('API3Y9RTZ5R6Y','API3Y9RTZ5R6Y3Y9RT','YMPI','6285645896741','Terdapat Order Pantry');

            $response = array(
                'status' => true,
                'message' => 'Pesanan Berhasil Dikonfirmasi'
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





//CRUD Menu Gambar

    public function daftarmenu(){
        $title = 'Daftar Minuman Pantry';
        $title_jp = '???';

        $menus = PantryMenu::orderBy('id', 'ASC')
        ->get();

        return view('pantry.menu', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'menus' => $menus
        ))->with('page', 'Pantry Menu')->with('head', 'Pantry');
    }

    public function create_menu()
    {
        return view('pantry.create_menu')->with('page', 'Pantry');
    }

    public function create_menu_action(Request $request)
    {
        try{

            $file = $request->file('gambar');
            $tujuan_upload = 'images/minuman';
            $namafile = $file->getClientOriginalName();

            $file->move($tujuan_upload,$namafile);

            $id = Auth::id();
            $menu = new PantryMenu([
                'menu' => $request->get('menu'),
                'gambar' => $namafile,
                'created_by' => $id
            ]);

            $menu->save();
            return redirect('/index/pantry/menu')->with('status', 'Menu has been created.')->with('page', 'Pantry');
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Menu name already exist.')->with('page', 'Pantry');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Pantry');
            }
        }
    }

    public function edit_menu($id)
    {
        $menus = PantryMenu::find($id);

        return view('pantry.edit_menu', array(
            'menus' => $menus
        ))->with('page', 'Pantry');
    }

    public function edit_menu_action(Request $request, $id)
    {
        try{
            $file = $request->file('gambar');

            if ($file != NULL) {
                $tujuan_upload = 'images/minuman';
                $namafile = $file->getClientOriginalName();
                $file->move($tujuan_upload,$namafile);

                $menu = PantryMenu::find($id);
                $menu->menu = $request->get('menu');
                $menu->gambar = $namafile;
                $menu->save();
            }
            else{
                $menu = PantryMenu::find($id);
                $menu->menu = $request->get('menu');
                $menu->save();
            }

            return redirect('/index/pantry/menu')->with('status', 'Menu data has been edited.')->with('page', 'Pantry');

        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                return back()->with('error', 'Menu name already exist.')->with('page', 'Pantry');
            }
            else{
                return back()->with('error', $e->getMessage())->with('page', 'Pantry');
            }
        } 
    }

    public function delete_menu($id)
    {
        $menu = PantryMenu::find($id);
        $menu->delete();

        return redirect('/index/pantry/menu')->with('status', 'Menu has been deleted.')->with('page', 'Pantry');
    }

    function filter(Request $request)
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('name','minuman','informasi','keterangan','gula','jumlah','tempat','status')
        ->join('users','users.username','=','pantry_orders.pemesan')
        ->where('pemesan','=',Auth::user()->username)
        ->orWhere(function ($query) {
            $query->where('status', '=', 'proses')
            ->where('status', '=', 'confirmed');
        })
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

        return DataTables::of($pesanan)

        ->editColumn('status',function($pesanan){
            if($pesanan->status == "confirmed") {
                return '<label class="label label-danger">Waiting Confirmation</label>';
            }
            else if($pesanan->status == "proses") {
                return '<label class="label label-primary">Making Your Orders</label>';
            }
        })

        ->rawColumns(['status' => 'status'])
        ->make(true);
    }

    public function getPesanan()
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('name','minuman','informasi','keterangan','gula','jumlah','tempat','status')
        ->join('users','users.username','=','pantry_orders.pemesan')
        ->where('pemesan','=',Auth::user()->username)
        ->orWhere(function ($query) {
            $query->where('status', '=', 'proses')
            ->where('status', '=', 'confirmed');
        })
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

        $response = array(
            'status' => true,
            'pesanan' => $pesanan
        );
        return Response::json($response);
    }

    public function daftarkonfirmasi(){
        $title = 'Pantry Order Confirmation';
        $title_jp = '注文内容確認';

        return view('pantry.pesanan_konfirmasi', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('page', 'Pesanan Pantry');
    }

    function filterkonfirmasi(Request $request)
    {
        $detailpesanan = DB::table('pantry_orders')
        ->select('pantry_orders.id','name','minuman','informasi','keterangan','gula','jumlah','tempat','status',DB::raw('DATE_FORMAT(pantry_orders.created_at,"%H:%i") as tgl_pesan'))
        ->join('users','users.username','=','pantry_orders.pemesan')
        ->where('status', '=', 'confirmed')
        ->orwhere('status', '=', 'proses')
        ->whereNull('pantry_orders.deleted_at');

        $detailpesanan = $detailpesanan->orderBy('pantry_orders.id', 'DESC');
        $pesanan = $detailpesanan->get();

        $response = array(
            'status' => true,
            'pesanan' => $pesanan
        );
        return Response::json($response);
    }

    public function konfirmasi(Request $request){
        try{
            $pantry = PantryOrder::find($request->get("id"));
            $pantry->status = 'proses';
            $pantry->save();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                $response = array(
                    'status' => false,
                    'datas' => "Error",
                );
                return Response::json($response);
            }
            else{
                $response = array(
                    'status' => false,
                    'datas' => "Error",
                );
                return Response::json($response);
            }
        }
    }

    public function selesaikan(Request $request){
        try{
            $id_user = Auth::id();

            $pantry = PantryOrder::find($request->get("id"));

            $log = new PantryLog([
                'pemesan' => $pantry->pemesan,
                'minuman' => $pantry->minuman,
                'informasi' => $pantry->informasi,
                'keterangan' => $pantry->keterangan,
                'gula' => $pantry->gula,
                'jumlah' => $pantry->jumlah,
                'tempat' => $pantry->tempat,
                'tgl_pesan' => $pantry->created_at,
                'tgl_dibuat' => date('Y-m-d H:i:s'),
                'created_by' => $id_user
            ]);

            $log->save();

            $pantry->delete();

            $response = array(
                'status' => true,
                'datas' => "Berhasil",
            );
            return Response::json($response);
        }
        catch (QueryException $e){
            $error_code = $e->errorInfo[1];
            if($error_code == 1062){
                $response = array(
                    'status' => false,
                    'datas' => "Error",
                );
                return Response::json($response);
            }
            else{
                $response = array(
                    'status' => false,
                    'datas' => "Error",
                );
                return Response::json($response);
            }
        } 
    }
}