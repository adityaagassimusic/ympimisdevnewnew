<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;
use Artisan;
use App\KaizenForm;

class SendEmailKaizen extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:kaizen';

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

        // -------------- UPDATE NIK ---------------

        $kz_nik = db::select("SELECT employee_id, employee_name FROM `kaizen_forms` where deleted_at is null group by employee_id, employee_name");

        $emp_act = db::select("SELECT employee_id, `name` from employee_syncs where end_date is null and employee_id LIKE 'PI%'");

        foreach ($kz_nik as $kz) {
            foreach ($emp_act as $act) {
                if ($kz->employee_name == $act->name && $kz->employee_id != $act->employee_id) {
                    KaizenForm::where('employee_name', '=', $kz->employee_name)
                    ->where('employee_id', '=', $kz->employee_id)
                    ->update([
                        'employee_id' => $act->employee_id
                    ]);
                }
            }
        }

        $mail_to = db::table('employee_syncs')
        ->leftJoin('users','users.username','=','employee_syncs.employee_id')
        ->whereRaw("position in ('foreman','chief','manager')")
        ->whereNotNull('email')
        ->where("id", "<>", "2254")
        ->whereNull("end_date")
        ->select('employee_syncs.employee_id','employee_syncs.name','position', 'department','email')
        ->get();

        $fy = db::table('weekly_calendars')
        ->whereRaw("fiscal_year = (select fiscal_year from weekly_calendars where week_date = '".date('Y-m-d')."')")
        ->select("week_date")
        ->orderBy('id', 'asc')
        ->get()
        ->toArray();

        $fys = [];

        foreach ($fy as $f) {
            array_push($fys, $f->week_date);
        }

        $fiscal = "'".implode("','", $fys)."'";

        $query_cf = "SELECT department, section, SUM(unv_frm) as frm, SUM(unv_mngr) AS mngr from
        (
        select department, section, IFNULL(count,0) AS  unv_frm, 0 AS unv_mngr from
        (select department, section from employee_syncs where department is not null and section is not null group by department, section) as bagian
        left join ( SELECT count( id ) AS count, area FROM kaizen_forms 
        left join employee_syncs on employee_syncs.employee_id = kaizen_forms.employee_id
        WHERE `status` = -1 AND propose_date >= '2019-12-01' 
        and employee_syncs.end_date is null
        and kaizen_forms.deleted_at is null GROUP BY area ) AS kz on bagian.section = kz.area

        UNION ALL

        select department, section, 0 AS unv_frm, IFNULL(count,0) AS unv_mngr from
        (select department, section from employee_syncs where department is not null and section is not null group by department, section) as bagian
        left join ( 

        select count(kaizen_forms.employee_id) as count, area from kaizen_forms join kaizen_scores ON kaizen_forms.id = kaizen_scores.id_kaizen
        left join employee_syncs on employee_syncs.employee_id = kaizen_forms.employee_id
        WHERE `status` = 1 and kaizen_forms.deleted_at is null AND ( manager_point_1 IS NULL OR manager_point_1 = 0 ) AND propose_date >= '2019-12-01'
        and employee_syncs.end_date is null
        group by area

        ) AS kz on bagian.section = kz.area) as alls
        group by department, section
        having frm <> 0 or mngr <> 0
        ";

        $kzn = db::select($query_cf);

        $kzs = array();
        $cf_fr = array();
        $mngr = array();
        $mail_tos = array();

        foreach ($mail_to as $data) {
            if ($data->position == 'Chief' || $data->position == 'Foreman') {
                array_push($cf_fr, [
                    'employee_id' => $data->employee_id,
                    'name' => $data->name,
                    'position' => $data->position,
                    'department' => $data->department,
                    'email' => $data->email,
                ]);
            } else {
                array_push($mngr, [
                    'employee_id' => $data->employee_id,
                    'name' => $data->name,
                    'position' => $data->position,
                    'department' => $data->department,
                    'email' => $data->email,
                ]);
            }            
        }

        foreach ($cf_fr as $cf) {
            foreach($kzn as $data_cf) {
                if (strcasecmp($cf['department'], $data_cf->department) == 0 ) {
                  if (!in_array($cf['email'], $mail_tos)) {
                    array_push($mail_tos,$cf['email']);
                }
            }
        }
    }

    foreach ($mngr as $mngr) {
        foreach($kzn as $data_cf) {
            if (strcasecmp($mngr['department'], $data_cf->department) == 0 ) {
              if (!in_array($mngr['email'], $mail_tos)) {
                array_push($mail_tos,$mngr['email']);
            }
        } else if($mngr['department'] == "Maintenance" && $data_cf->department == "Production Engineering") {
            if (!in_array($mngr['email'], $mail_tos)) {
                array_push($mail_tos,$mngr['email']);
            }
        }
    }
}

array_push($mail_tos,'eko.prasetyo.wicaksono@music.yamaha.com');

$bcc_to = ['ympi-mis-ML@music.yamaha.com'];

$kaizens = [
    'kaizens' => $kzn
];

Artisan::call('cache:clear');
Artisan::call('config:clear');
Artisan::call('config:cache');

if($kzn != null){
    Mail::to($mail_tos)->cc(['rani.nurdiyana.sari@music.yamaha.com'])->bcc($bcc_to)->send(new SendEmail($kaizens, 'kaizen'));
}
}
}
