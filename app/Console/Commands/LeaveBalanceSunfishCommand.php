<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class LeaveBalanceSunfishCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:leave_balance';

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
        //UPDATE LEAVE BALANCE
        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, 'ympicoid_api_login'], []);
        // $token = app()->call([$controller, 'ympicoid_api'], []);

        $param = '';
        $link = 'delete/leave_balance';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $delete_emp = app()->call([$controller, 'ympicoid_api_json'], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        $leave_balance = DB::connection('sunfish')->select("SELECT
                * 
            FROM
                [dbo].[view_empgetleaveympi] 
            WHERE
                [active_status] LIKE '%1%'");

        $leave_balance2 = json_decode(json_encode($leave_balance), true);

        $insert = array();
        $insert_all = [];

        for ($i = 0; $i < count($leave_balance2); $i++) {
            $row = array();

            if ($leave_balance2[$i]['emp_no'] == null) {
                $row['employee_id'] = '';
            } else {
                $row['employee_id'] = $leave_balance2[$i]['emp_no'];
            }

            if ($leave_balance2[$i]['leave_code'] == null) {
                $row['leave_code'] = '';
            } else {
                $row['leave_code'] = $leave_balance2[$i]['leave_code'];
            }

            if ($leave_balance2[$i]['leavename_en'] == null) {
                $row['leavename_en'] = '';
            } else {
                $row['leavename_en'] = $leave_balance2[$i]['leavename_en'];
            }

            if ($leave_balance2[$i]['startvaliddate'] == null) {
                $row['startvaliddate'] = '';
            } else {
                $row['startvaliddate'] = $leave_balance2[$i]['startvaliddate'];
            }

            if ($leave_balance2[$i]['endvaliddate'] == null) {
                $row['endvaliddate'] = '';
            } else {
                $row['endvaliddate'] = $leave_balance2[$i]['endvaliddate'];
            }

            if ($leave_balance2[$i]['nextvaliddate'] == null) {
                $row['nextvaliddate'] = '';
            } else {
                $row['nextvaliddate'] = $leave_balance2[$i]['nextvaliddate'];
            }

            if ($leave_balance2[$i]['entitlement'] == null) {
                $row['balance'] = '';
            } else {
                $row['balance'] = $leave_balance2[$i]['entitlement'];
            }

            if ($leave_balance2[$i]['adjustment'] == null) {
                $row['adjustment'] = '';
            } else {
                $row['adjustment'] = $leave_balance2[$i]['adjustment'];
            }

            if ($leave_balance2[$i]['used'] == null) {
                $row['used'] = '';
            } else {
                $row['used'] = $leave_balance2[$i]['used'];
            }

            if ($leave_balance2[$i]['remaining'] == null) {
                $row['remaining'] = '';
            } else {
                $row['remaining'] = $leave_balance2[$i]['remaining'];
            }

            $row['created_by'] = '1';
            $row['created_at'] = date('Y-m-d H:i:s');
            $row['updated_at'] = date('Y-m-d H:i:s');

            $insert[] = $row;

            if (($i % 100 == 0 || $i == (count($leave_balance2) - 1)) && $i != 0) {
                array_push($insert_all, $insert);
                $insert = [];
            }
        }

        if (count($insert_all) > 0) {
            for ($i = 0; $i < count($insert_all); $i++) {
                $link = 'insert/leave_balance';
                $method = 'POST';
                $param = json_encode($insert_all[$i]);

                $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                $insert_emp = app()->call([$controller, 'ympicoid_api_json'], [
                    'token' => $token,
                    'link' => $link,
                    'method' => $method,
                    'param' => $param,
                ]);
            }
        }
    }
}
