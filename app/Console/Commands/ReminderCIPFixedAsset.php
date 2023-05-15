<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\SendEmail;
use Mails;
use App\FixedAssetItem;

class ReminderCIPFixedAsset extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:fa_cip';

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
        $email = [];
        $date = date('Y-m-01');
        $datas = db::select("SELECT fa.*, applicant_id, applicant_name, users.email from
            (SELECT sap_number, fixed_asset_name, classification_category, amount_usd, section, pic, location, usage_estimation, request_date, investment FROM `fixed_asset_items` where usage_term = 'not use yet' and registration_status = 'Done' and retired_date is null and request_date < '".$date."') fa
            left join acc_investments on fa.investment = acc_investments.reff_number
            left join users on acc_investments.applicant_id = users.username");

        if (count($datas) > 0) {
            foreach ($datas as $ds) {
                if (!in_array($ds->email, $email) && $ds->email) {
                    array_push($email, $ds->email);
                }
            }

            if (count($email) > 0) {
                foreach ($email as $eml) {
                    $datas_ml = db::select("SELECT fa.*, applicant_id, applicant_name, users.email, employee_syncs.name from
                        (SELECT sap_number, fixed_asset_name, classification_category, amount_usd, section, pic, location, usage_term, usage_estimation, request_date, investment FROM `fixed_asset_items` where usage_term = 'not use yet' and registration_status = 'Done' and retired_date is null and request_date < '".$date."') fa
                        left join acc_investments on fa.investment = acc_investments.reff_number
                        left join users on acc_investments.applicant_id = users.username
                        left join employee_syncs on fa.pic = employee_syncs.employee_id
                        where users.email = '".$eml."'
                        order by request_date asc");


                    $tahun = date('y');
                    $bulan = date('m');

                    $query = "SELECT form_number FROM `fixed_asset_cip_logs` where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by id DESC LIMIT 1";
                    $nomorurut = DB::select($query);

                    if ($nomorurut != null) {
                        $nomor = substr($nomorurut[0]->form_number, -3);
                        $nomor = $nomor + 1;
                        $nomor = sprintf('%03d', $nomor);
                    } else {
                        $nomor = "001";
                    }

                    $result['tahun'] = $tahun;
                    $result['bulan'] = $bulan;
                    $result['no_urut'] = $nomor;

                    $form_number = 'FAC' . $result['tahun'] . $result['bulan'] . $result['no_urut'];

                    foreach ($datas_ml as $dts) {
                        DB::table('fixed_asset_cip_logs')->insert([
                            'period' => $date,
                            'form_number' => $form_number,
                            'sap_number' => $dts->sap_number,
                            'fixed_asset_name' => $dts->fixed_asset_name,
                            'acquisition_date' => $dts->request_date,
                            'amount_usd' => $dts->amount_usd,
                            'usage_term' => $dts->usage_term,
                            'usage_estimation' => $dts->usage_estimation,
                            'pic' => $dts->pic,
                            'section' => $dts->section,
                            'clasification_category' => $dts->classification_category,
                            'created_by' => $dts->pic,
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }

                    $data = [
                        'datas' => $datas_ml,
                        'form_number' => $form_number
                    ];

                    Mail::to($eml)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, 'fixed_asset_cip_reminder'));
                }
            }
        }
    }
}
