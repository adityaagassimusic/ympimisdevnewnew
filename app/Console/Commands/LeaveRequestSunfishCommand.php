<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class LeaveRequestSunfishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:leave_request';

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
        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);

        $param = '';
        $link = 'fetch/leave';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $get_leave = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        $errors = [];
        $errors_detail = [];

        if (count($get_leave) > 0) {
            for ($i=0; $i < count($get_leave); $i++) {

                if ($get_leave[$i]->filename != null) {
                    // $file = $this->file_get_contents_ssl('https://10.109.33.10/ympicoid/public/files/leave_request/'.$get_leave[$i]->filename);
                    // $file = $this->file_get_contents_curl('https://10.109.33.10/ympicoid/public/files/leave_request/'.$get_leave[$i]->filename);
                    // $file = file_get_contents('https://10.109.33.10/ympicoid/public/files/leave_request/'.$get_leave[$i]->filename);
                    $arrContextOptions= [
                        'ssl' => [
                            'verify_peer'=> false,
                            'verify_peer_name'=> false,
                        ],
                    ];
                    $file = file_get_contents('https://ympi.co.id/ympicoid/public/files/leave_request/'.$get_leave[$i]->filename, false, stream_context_create($arrContextOptions) );
                    file_put_contents('public/sunfish/leave_request/'.$get_leave[$i]->filename, $file);
                    $filepath = "E:/xampp/htdocs/mirai/public/sunfish/leave_request/".$get_leave[$i]->filename;

                    $datalist = array(
                    'requestedby' => $get_leave[$i]->requested_by,
                    'requestfor' => $get_leave[$i]->requested_for,
                    'requestdate' => $get_leave[$i]->request_date,
                    'leave_startdate' => $get_leave[$i]->leave_startdate,
                    'leave_enddate' => $get_leave[$i]->leave_enddate,
                    'leave_code' => explode('_', $get_leave[$i]->leave_type)[0],
                    'remark' => $get_leave[$i]->remark,
                    'attachment' => new \CURLFILE($filepath));
                }else{
                    $datalist = array(
                    'requestedby' => $get_leave[$i]->requested_by,
                    'requestfor' => $get_leave[$i]->requested_for,
                    'requestdate' => $get_leave[$i]->request_date,
                    'leave_startdate' => $get_leave[$i]->leave_startdate,
                    'leave_enddate' => $get_leave[$i]->leave_enddate,
                    'leave_code' => explode('_', $get_leave[$i]->leave_type)[0],
                    'remark' => $get_leave[$i]->remark);
                }

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'http://10.109.52.8/sfapi/?endpoint=ympi_FULL_insLeaveReq',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                   CURLOPT_POSTFIELDS => $datalist,
                  CURLOPT_HTTPHEADER => array(
                    'X-SFAPI-Account: ympi',
                    'X-SFAPI-AppName: SunFishIntegration-yamahahris',
                    'X-SFAPI-RSAKey: MIIDQTCCAikCAQAwgfsxCzAJBgNVBAYTAklEMRMwEQYDVQQIDApKYXdhIFRpbXVyMTwwOgYDVQQHDDNLYXdhc2FuIEluZHVzdHJpIFBpZXIsIEpsLiBSZW1iYW5nIEluZHVzdHJpIEkgTm8uMzYxLTArBgNVBAoMJFBULiBZYW1haGEgTXVzaWNhbCBQcm9kdWN0IEluZG9uZXNpYTEmMCQGA1UECwwdU3VuRmlzaEludGVncmF0aW9uLXlhbWFoYWhyaXMxCzAJBgNVBAMMAjgyMTUwMwYJKoZIhvcNAQkBFiZhbmdlbGluYS5kZXdpQGRhdGFvbi5jb20yNTExMjAyMjAxMjgwNDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKXK1m11VDgs7wfm7NJEYysOy6FrS0zetNSf8hos2DEi2IZvxTW1gc32g4XZaqaZWFpBlAd2UNO35hRiojiVIUl9zAdJWFikC/PIx4r51htJqayPv74TsWyY+W294tGghaM9ZCpvj+NnFepuVfocq1vKnLH/GAa2O+A4X4nt2bYUxICn1Ml2FemIlSVJi0HcIN5bRc8Cgy1m2vw0GtzLSn4t828xlze3HZgvWo7JLUzht0RDsufpZ9Fc6K6j2JwJVDCgm+JmY6yqpi2c5iICKTUJ+mG9xbA+Vjks4A0+xkJRcfultYKrLWarhOUUd6mHwDIcsENzrHYwRWe01deitu0CAwEAAaAAMA0GCSqGSIb3DQEBCwUAA4IBAQBr/izixwI/jOKnjmI2oZowgv5pTTc1buvKBGOyNCAZHmzPmddw2aufBg5NqgZ51T1DgyMMuNP/13nEnJkTkPBDKiy0PbPLxgjFUFoZuJx1rPWRVQpQ86dM1fvbX5yY1wbeFoPJfhYtsUzlBjlOs9ehOU3Gl2rVzdIjrTs4eVpCHujNuZiab2A45F1lAaRcKr9wsSbJwnlShPSOWH+xm4aWfnvcc2TxcOlFw5HIcvjxNWVqYaFrf2EfIQzTzdwiH0K/Zih73hp0MTYF8qWOD3sphBU/UOwziWEg4qYYZjwoVGpFGsOQwCcboz2Lr/OZhoiiiGl4/1+fG8fZM1YmxHeZ',
                    'Content-Type: multipart/form-data',
                    'Cookie: CFID=22626845; CFTOKEN=5b2a84d90443e4d2-6DFD05D1-C1C5-3322-07A462C9156C8463; JSESSIONID=AC3148DAFD425EE9D81C1C873C89405C.cfusion; LASTSESSIONFROM=%7Bts%20%272023%2D03%2D03%2015%3A46%3A06%27%7D; LASTSESSIONTO=%7Bts%20%272023%2D03%2D03%2015%3A46%3A06%27%7D'
                  ),
                ));

                $response = curl_exec($curl);
                // var_dump($response);

                curl_close($curl);

                if ($response) {
                    if (str_contains(json_decode($response)->RESULT,'YMPI-LVR-')) {
                        $request_no = substr(json_decode($response)->RESULT,21,19);

                        $param = 'id='.$get_leave[$i]->id.'&request_no='.$request_no.'&reason=';
                        $link = 'update/leave';
                        $method = 'POST';

                        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                        $update_leave = app()->call([$controller, 'ympicoid_api'], [
                            'token' => $token,
                            'link' => $link,
                            'method' => $method,
                            'param' => $param,
                        ]);
                    }else{
                        array_push($errors, [
                            'requestedby' => $get_leave[$i]->requested_by,
                            'requestfor' => $get_leave[$i]->requested_for,
                            'requestdate' => $get_leave[$i]->request_date,
                            'leave_startdate' => $get_leave[$i]->leave_startdate,
                            'leave_enddate' => $get_leave[$i]->leave_enddate,
                            'leave_code' => explode('_', $get_leave[$i]->leave_type)[0],
                            'remark' => $get_leave[$i]->remark,
                            'reason' => json_decode($response)->RESULT
                        ]);

                        if (date('Y-m-d',strtotime($get_leave[$i]->leave_enddate)) == date('Y-m-d',strtotime($get_leave[$i]->leave_startdate))) {
                            array_push($errors_detail, $get_leave[$i]->requested_by.' - '.$get_leave[$i]->requested_for.' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_startdate)).' - '.explode('_', $get_leave[$i]->leave_type)[0].' - '.json_decode($response)->RESULT);
                        }else{
                            array_push($errors_detail, $get_leave[$i]->requested_by.' - '.$get_leave[$i]->requested_for.' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_startdate)).' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_enddate)).' - '.explode('_', $get_leave[$i]->leave_type)[0].' - '.json_decode($response)->RESULT);
                        }

                        $param = 'id='.$get_leave[$i]->id.'&request_no=&reason='.json_decode($response)->RESULT;
                        $link = 'update/leave';
                        $method = 'POST';

                        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                        $update_leave = app()->call([$controller, 'ympicoid_api'], [
                            'token' => $token,
                            'link' => $link,
                            'method' => $method,
                            'param' => $param,
                        ]);
                    }
                }else{
                    array_push($errors, [
                        'requestedby' => $get_leave[$i]->requested_by,
                        'requestfor' => $get_leave[$i]->requested_for,
                        'requestdate' => $get_leave[$i]->request_date,
                        'leave_startdate' => $get_leave[$i]->leave_startdate,
                        'leave_enddate' => $get_leave[$i]->leave_enddate,
                        'leave_code' => explode('_', $get_leave[$i]->leave_type)[0],
                        'remark' => $get_leave[$i]->remark,
                        'reason' => 'FALSE'
                    ]);

                    if (date('Y-m-d',strtotime($get_leave[$i]->leave_enddate)) == date('Y-m-d',strtotime($get_leave[$i]->leave_startdate))) {
                        array_push($errors_detail, $get_leave[$i]->requested_by.' - '.$get_leave[$i]->requested_for.' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_startdate)).' - '.explode('_', $get_leave[$i]->leave_type)[0].' - FALSE');
                    }else{
                        array_push($errors_detail, $get_leave[$i]->requested_by.' - '.$get_leave[$i]->requested_for.' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_startdate)).' - '.date('Y-m-d',strtotime($get_leave[$i]->leave_enddate)).' - '.explode('_', $get_leave[$i]->leave_type)[0].' - FALSE');
                    }

                    $param = 'id='.$get_leave[$i]->id.'&request_no=&reason=FALSE';
                    $link = 'update/leave';
                    $method = 'POST';

                    $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                    $update_leave = app()->call([$controller, 'ympicoid_api'], [
                        'token' => $token,
                        'link' => $link,
                        'method' => $method,
                        'param' => $param,
                    ]);
                }
            }

            if (count($errors_detail) > 0) {
                for ($i=0; $i < count($errors_detail); $i++) { 
                    // $message = urlencode($errors_detail[$i]);

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
                    //     CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . $message . '&type=chat',
                    //     CURLOPT_HTTPHEADER => array(
                    //         'Accept: application/json',
                    //         'Content-Type: application/x-www-form-urlencoded',
                    //         'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    //     ),
                    // ));
                    // curl_exec($curl);
                }
            }
            $message = urlencode(count($get_leave)." Leave Request Data Sycned With ".count($errors)." Error(s)");

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
                CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);
        }else{
            $message = urlencode("No Leave Request Data Sycned");

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
                CURLOPT_POSTFIELDS => 'receiver=6285645896741&device=6281130561777&message=' . $message . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);
        }

        //UPDATE STATUS LEAVE REQUEST
        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);

        $param = '';
        $link = 'fetch/leave_ongoing';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $get_ongoing = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        if (count($get_ongoing) > 0) {
            for ($i=0; $i < count($get_ongoing); $i++) { 
                $cek_approval = DB::connection('sunfish')->SELECT("SELECT
                    * 
                FROM
                    [dbo].[vw_approval_list] 
                WHERE
                    [req_no] LIKE '%".$get_ongoing[$i]->request_no."%'");

                $cek_approved = DB::connection('sunfish')->SELECT("SELECT
                    * 
                FROM
                    [dbo].[vw_approved_list] 
                WHERE
                    [req_no] LIKE '%".$get_ongoing[$i]->request_no."%'");

                $approved_list = null;
                $approval_list = null;
                $status_approval = 0;

                if ($cek_approved != null || count($cek_approved) > 0) {
                    $approved = json_decode(json_encode($cek_approved), true);
                    $approved_list = $approved[0]['approved_by'];
                }

                if ($cek_approval != null || count($cek_approval) > 0) {
                    $approval = json_decode(json_encode($cek_approval), true);
                    $approval_list = $approval[0]['approved_by'];
                    $status_approval = $approved[0]['status'];
                }

                $param = 'id='.$get_ongoing[$i]->id.'&status_approval='.$status_approval.'&approved_list='.$approved_list.'&approval_list='.$approval_list;
                $link = 'update/leave_ongoing';
                $method = 'POST';

                $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                $update_leave = app()->call([$controller, 'ympicoid_api'], [
                    'token' => $token,
                    'link' => $link,
                    'method' => $method,
                    'param' => $param,
                ]);
            }
        }
    }

    function file_get_contents_ssl($url) {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3000); // 3 sec.
        curl_setopt($ch, CURLOPT_TIMEOUT, 10000); // 10 sec.
        curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE );
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    function file_get_contents_curl( $url ) {
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_AUTOREFERER, TRUE );
        curl_setopt( $ch, CURLOPT_HEADER, 0 );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_URL, $url );
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, TRUE );
        $data = curl_exec( $ch );
        curl_close( $ch );
        return $data;
    }
}
