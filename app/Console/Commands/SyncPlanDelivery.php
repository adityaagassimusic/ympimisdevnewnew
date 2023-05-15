<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncPlanDelivery extends Command
{
    protected $signature = 'sync:plan_delivery';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();

        // DEV
        // $this->token_type = 'ympicoid_api_login_trial';
        // $this->api_return_type = 'ympicoid_api_trial';
        // $this->api_json_type = 'ympicoid_api_trial_json';

        // LIVE
        $this->token_type = 'ympicoid_api_login';
        $this->api_return_type = 'ympicoid_api';
        $this->api_json_type = 'ympicoid_api_json';

    }

    public function handle()
    {

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $token = app()->call([$controller, $this->token_type], []);

        $param = '';
        $link = 'fetch/sync_plan_delivery';
        $method = 'GET';

        $controller = app()->make('App\Http\Controllers\MiraiMobileController');
        $sync = app()->call([$controller, $this->api_return_type], [
            'token' => $token,
            'link' => $link,
            'method' => $method,
            'param' => $param,
        ]);

        if (count($sync)) {
            DB::beginTransaction();

            for ($i = 0; $i < count($sync); $i++) {
                try {
                    $update = DB::table('material_plan_deliveries')
                        ->where('id', $sync[$i]->id)
                        ->update([
                            'po_confirm' => $sync[$i]->po_confirm,
                            'po_confirm_at' => $sync[$i]->po_confirm_at,
                            'reminder_confirm_at' => $sync[$i]->reminder_confirm_at,
                            'updated_at' => $sync[$i]->updated_at,
                        ]);

                } catch (Exception $e) {
                    DB::rollback();

                    $messages = "Delivery Plan Successfully Failed to Sync from YMPICOID to MIRAI. ID : " . $sync[$i]->id . " Row(s). Reason :" . $e->getMessage();
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
                        CURLOPT_POSTFIELDS => 'receiver=6282234955505&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                        CURLOPT_HTTPHEADER => array(
                            'Accept: application/json',
                            'Content-Type: application/x-www-form-urlencoded',
                            'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                        ),
                    ));
                    curl_exec($curl);
                }
            }

            DB::commit();
            $messages = "Delivery Plan Successfully Synced from YMPICOID to MIRAI. Count : " . count($sync) . " Row(s).";
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
                CURLOPT_POSTFIELDS => 'receiver=6282234955505&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);

        } else {
            $messages = "No Synced Delivery Plan Data from YMPICOID to MIRAI";
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
                CURLOPT_POSTFIELDS => 'receiver=6282234955505&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            ));
            curl_exec($curl);

        }

    }
}
