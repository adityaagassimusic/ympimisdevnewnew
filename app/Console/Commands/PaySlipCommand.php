<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PaySlipCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:payslip';

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

        $payslips =[
            'bonus',
            'pesangon_pkwt_ympi',
            'pesangon_ympi',
            'recal_salary',
            'recalc_tax',
            'recalculate_tax',
            'resign_ympi',
            'salary_yen_ympi',
            'salary_ympi',
            'thkdes_ympi',
            'thkjuni_ympi',
            'thr_ympi',

        ];

        var_dump(date('Y-m-d H:i:s').' ----- ');

        $emp = DB::table('employee_syncs')
        ->where('employee_id','not like','%OS%')
        ->where(
           function($query) {
             return $query
                    ->where('end_date','<',date('Y-m-d'))
                    ->orwhere('end_date',null);
            })->get();
        for ($i=0; $i < count($emp); $i++) { 
          // if ($emp[$i]->employee_id == 'PI1910003') {
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'http://10.109.52.8/sfapi/?endpoint=ympi_SFFULL_PY_ExportPayslipV2',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              // CURLOPT_POSTFIELDS => 'emp_no='.$emp[$i]->employee_id.'&pay_period='.$payslips[$j].'&pay_month='.date('n').'&pay_year='.date('Y'),
              CURLOPT_POSTFIELDS => 'emp_no='.$emp[$i]->employee_id.'&pay_period=salary_ympi&pay_month='.date('n').'&pay_year='.date('Y'),
              CURLOPT_HTTPHEADER => array(
                'X-SFAPI-Account: ympi',
                'X-SFAPI-UserName: '.$emp[$i]->employee_id,
                'X-SFAPI-AppName: SunFishIntegration-yamahahris',
                'X-SFAPI-RSAKey: MIIDQTCCAikCAQAwgfsxCzAJBgNVBAYTAklEMRMwEQYDVQQIDApKYXdhIFRpbXVyMTwwOgYDVQQHDDNLYXdhc2FuIEluZHVzdHJpIFBpZXIsIEpsLiBSZW1iYW5nIEluZHVzdHJpIEkgTm8uMzYxLTArBgNVBAoMJFBULiBZYW1haGEgTXVzaWNhbCBQcm9kdWN0IEluZG9uZXNpYTEmMCQGA1UECwwdU3VuRmlzaEludGVncmF0aW9uLXlhbWFoYWhyaXMxCzAJBgNVBAMMAjgyMTUwMwYJKoZIhvcNAQkBFiZhbmdlbGluYS5kZXdpQGRhdGFvbi5jb20yNTExMjAyMjAxMjgwNDCCASIwDQYJKoZIhvcNAQEBBQADggEPADCCAQoCggEBAKXK1m11VDgs7wfm7NJEYysOy6FrS0zetNSf8hos2DEi2IZvxTW1gc32g4XZaqaZWFpBlAd2UNO35hRiojiVIUl9zAdJWFikC/PIx4r51htJqayPv74TsWyY+W294tGghaM9ZCpvj+NnFepuVfocq1vKnLH/GAa2O+A4X4nt2bYUxICn1Ml2FemIlSVJi0HcIN5bRc8Cgy1m2vw0GtzLSn4t828xlze3HZgvWo7JLUzht0RDsufpZ9Fc6K6j2JwJVDCgm+JmY6yqpi2c5iICKTUJ+mG9xbA+Vjks4A0+xkJRcfultYKrLWarhOUUd6mHwDIcsENzrHYwRWe01deitu0CAwEAAaAAMA0GCSqGSIb3DQEBCwUAA4IBAQBr/izixwI/jOKnjmI2oZowgv5pTTc1buvKBGOyNCAZHmzPmddw2aufBg5NqgZ51T1DgyMMuNP/13nEnJkTkPBDKiy0PbPLxgjFUFoZuJx1rPWRVQpQ86dM1fvbX5yY1wbeFoPJfhYtsUzlBjlOs9ehOU3Gl2rVzdIjrTs4eVpCHujNuZiab2A45F1lAaRcKr9wsSbJwnlShPSOWH+xm4aWfnvcc2TxcOlFw5HIcvjxNWVqYaFrf2EfIQzTzdwiH0K/Zih73hp0MTYF8qWOD3sphBU/UOwziWEg4qYYZjwoVGpFGsOQwCcboz2Lr/OZhoiiiGl4/1+fG8fZM1YmxHeZ',
                'Content-Type: application/x-www-form-urlencoded',
                'Cookie: CFID=22629413; CFTOKEN=74dfd5a67a949aff-6F74853B-CCD1-3F54-D8AAC2593F2F9F8E'
              ),
            ));

            $response = curl_exec($curl);

            curl_close($curl);

            if (json_decode($response)->STATUS) {
                if (json_decode($response)->RESULT != 'Invalid Request!') {
                    $check = DB::table('pay_slips')->where('employee_id',$emp[$i]->employee_id)->where('period','salary_ympi')->where('month',date('n'))->where('year',date('Y'))->first();
                    $datas = [];
                    if (!$check) {
                        $insert_response = db::table('pay_slips')
                        ->insert([
                            'employee_id' => $emp[$i]->employee_id,
                            'period' => 'salary_ympi',
                            'month' => date('n'),
                            'year' => date('Y'),
                            'result' => json_decode($response)->RESULT,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                    // array_push($datas, [
                    //   'employee_id' => $emp[$i]->employee_id,
                    //   'period' => 'salary_ympi',
                    //   'month' => date('n'),
                    //   'year' => date('Y'),
                    //   'result' => json_decode($response)->RESULT,
                    // ]);

                    // $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                    // $token = app()->call([$controller, 'ympicoid_api_login'], []);

                    // $link = 'insert/pay_slips';
                    // $method = 'POST';

                    // $param = 'employee_id='.$emp[$i]->employee_id.'&period='.'salary_ympi'.'&month='.date('n').'&year='.date('Y').'&result='.json_decode($response)->RESULT;

                    // $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                    // $insert_payslip = app()->call([$controller, 'ympicoid_api'], [
                    //     'token' => $token,
                    //     'link' => $link,
                    //     'method' => $method,
                    //     'param' => $param,
                    // ]);

                    // $param = json_encode($datas);

                    // $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                    // $insert_payslip = app()->call([$controller, 'ympicoid_api_json'], [
                    //     'token' => $token,
                    //     'link' => $link,
                    //     'method' => $method,
                    //     'param' => $param,
                    // ]);
                }
            }
          // }
        }
        var_dump(date('Y-m-d H:i:s'));
    }
}
