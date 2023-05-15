<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\User;
use App\Employee;
use App\EmployeeSync;
use App\GreatdayAttendance;
use Carbon\Carbon;
use App\Mail\SendEmail;

class GreatdayAttendanceCommand extends Command
{
/**
* The name and signature of the console command.
*
* @var string
*/
protected $signature = 'sync:greatday_attendance';

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

    $now = date('Y-m-d');

    $attendances = db::connection('sunfish')->select("SELECT * 
        FROM
        VIEW_AR_YMPI
        WHERE format(dateTime, 'yyyy-MM-dd') = '".$now."'
        ");

    foreach ($attendances as $attendance) {
        $employee = EmployeeSync::where('employee_id', '=', $attendance->emp_no)->first();
        $latlong = json_decode($attendance->location);
        $mock = null;
        if (ISSET($latlong->mock)) {
            $mock = $latlong->mock;
        }
        // pk.2a8bbcfe2d56fd3f61c2aeb2dd556e69

        // pk.c876df7db9f4dc1f1ec939df75a98ae7

        // $insert = GreatdayAttendance::updateOrCreate(
        //     [
        //         'date_in' => date('Y-m-d', strtotime($attendance->dateTime)),
        //         'employee_id' => $attendance->emp_no
        //     ],
        $insert = GreatdayAttendance::create(
            [
                'employee_id' => $attendance->emp_no,
                'name' => $employee->name,
                'date_in' => date('Y-m-d', strtotime($attendance->dateTime)),
                'time_in' => $attendance->dateTime,
                'task' => $attendance->taskDesc,
                'department' => $employee->department,
                'section' => $employee->section,
                'group' => $employee->group,
                'latitude' => $latlong->latitude,
                'longitude' => $latlong->longitude,
                'mock' => $mock               
            ]
        );

        $insert->save();
    }

    $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);

        $param = '';
        $link = 'fetch/attendance';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $get_attendance = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        if (count($get_attendance) > 0) {
            for ($i=0; $i < count($get_attendance); $i++) { 
                $emp = EmployeeSync::where('employee_id',$get_attendance[$i]->employee_id)->first();
                $insert = DB::table('greatday_attendances')->insert([
                    'employee_id' => $get_attendance[$i]->employee_id,
                    'name' => $get_attendance[$i]->name,
                    'department' => $emp->department,
                    'section' => $emp->section,
                    'group' => $emp->group,
                    'time_in' => $get_attendance[$i]->datetime,
                    'date_in' => date('Y-m-d', strtotime($get_attendance[$i]->datetime)),
                    'images' => $get_attendance[$i]->images,
                    'task' => 'YMPICOID Attendance',
                    'latitude' => $get_attendance[$i]->latitude,
                    'longitude' => $get_attendance[$i]->longitude,
                    'state' => $get_attendance[$i]->state,
                    'state_district' => $get_attendance[$i]->state_district,
                    'village' => $get_attendance[$i]->village,
                    'mock' => null,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

                // 2023-02-27-11-00-00

                if(!str_contains($get_attendance[$i]->employee_id, 'OS')) {
                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                      CURLOPT_URL => 'http://10.109.52.8/sfapi/index.cfm?endpoint=ympi_FULL_insAttTemp',
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_POSTFIELDS => 'attendanceid='.$get_attendance[$i]->employee_id.'&datetime='.date('Y-m-d-H-i-s',strtotime($get_attendance[$i]->datetime)).'&latitude='.$get_attendance[$i]->latitude.'&longitude='.$get_attendance[$i]->longitude,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'X-SFAPI-Account: ympi',
                        'X-SFAPI-AppName: SunFishIntegration-yamahahris',
                        'X-SFAPI-RSAKey: MIIDQTCCAikCAQAwgfsxCzAJBgNVBAYTAklEMRMwEQYDVQQIDApKYXdhIFRpbXVyMTwwOgYDVQQHDDNLYXdhc2FuIEluZHVzdHJpIFBpZXIsIEpsLiBSZW1iYW5nIEluZHVzdHJpIEkgTm8uMzYxLTArBgNVBAoMJFBULiBZYW1haGEgTXVzaWNhbCBQcm9kdWN0IEluZG9uZXNpYTEmMCQGA1UECwwdU3VuRmlzaEludGVncmF0aW9uLXlhbWFoYWhyaXMxCzAJBgNVBAMMAjgyMTUwMwYJKoZIhvcNAQkBFiZhbmdlbGluYS5kZXdpQGRhdGFvbi5jb20yNTExMjAyMjAxMjgwNDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKXK1m11VDgs7wfm7NJEYysOy6FrS0zetNSf8hos2DEi2IZvxTW1gc32g4XZaqaZWFpBlAd2UNO35hRiojiVIUl9zAdJWFikC/PIx4r51htJqayPv74TsWyY+W294tGghaM9ZCpvj+NnFepuVfocq1vKnLH/GAa2O+A4X4nt2bYUxICn1Ml2FemIlSVJi0HcIN5bRc8Cgy1m2vw0GtzLSn4t828xlze3HZgvWo7JLUzht0RDsufpZ9Fc6K6j2JwJVDCgm+JmY6yqpi2c5iICKTUJ+mG9xbA+Vjks4A0+xkJRcfultYKrLWarhOUUd6mHwDIcsENzrHYwRWe01deitu0CAwEAAaAAMA0GCSqGSIb3DQEBCwUAA4IBAQBr/izixwI/jOKnjmI2oZowgv5pTTc1buvKBGOyNCAZHmzPmddw2aufBg5NqgZ51T1DgyMMuNP/13nEnJkTkPBDKiy0PbPLxgjFUFoZuJx1rPWRVQpQ86dM1fvbX5yY1wbeFoPJfhYtsUzlBjlOs9ehOU3Gl2rVzdIjrTs4eVpCHujNuZiab2A45F1lAaRcKr9wsSbJwnlShPSOWH+xm4aWfnvcc2TxcOlFw5HIcvjxNWVqYaFrf2EfIQzTzdwiH0K/Zih73hp0MTYF8qWOD3sphBU/UOwziWEg4qYYZjwoVGpFGsOQwCcboz2Lr/OZhoiiiGl4/1+fG8fZM1YmxHeZ',
                        'Content-Type: application/x-www-form-urlencoded'
                      ),
                    ));

                    $resp = curl_exec($curl);

                    curl_close($curl);
                }
            }
        }

        $message = urlencode("ATTENDANCE YMPICOID Synced at " . date("Y-m-d H:i:s"));

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
}
