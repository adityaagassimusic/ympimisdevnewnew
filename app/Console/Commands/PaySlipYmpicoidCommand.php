<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class PaySlipYmpicoidCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:payslip_ympicoid';

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
        $datas = DB::connection('ympimis_2')->table('pay_slips')->where('synced',null)->limit(100)->get();
        if (count($datas) > 0) {
            for ($i=0; $i < count($datas); $i++) { 
                $datass = [];
                array_push($datass, [
                  'employee_id' => $datas[$i]->employee_id,
                  'period' => $datas[$i]->period,
                  'month' => $datas[$i]->month,
                  'year' => $datas[$i]->year,
                  'result' => $datas[$i]->result,
                ]);

                $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                $token = app()->call([$controller, 'ympicoid_api_login'], []);

                $link = 'insert/pay_slips';
                $method = 'POST';

                $param = json_encode($datass);

                $controller = app()->make('App\Http\Controllers\MiraiMobileController');
                $insert_payslip = app()->call([$controller, 'ympicoid_api_json'], [
                    'token' => $token,
                    'link' => $link,
                    'method' => $method,
                    'param' => $param,
                ]);
                $update = DB::connection('ympimis_2')->table('pay_slips')->where('id',$datas[$i]->id)->update([
                    'synced' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }
    }
}
