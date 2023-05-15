<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;
use Carbon\Carbon;

class SkillMapCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'skill_map:reminder';

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
        $now = date('Y-m');

        $location = DB::SELECT("SELECT DISTINCT
            ( location ) 
        FROM
            skill_maps");

        for ($i=0; $i < count($location); $i++) { 
            $skill_schedule = DB::SELECT("SELECT
                skill_map_schedules.periode,
                skill_map_schedules.process,
                skill_map_schedules.skill_code,
                skills.skill,
                skill_maps.employee_id,
                employee_syncs.`name`,
                skill_maps.`value` 
            FROM
                `skill_map_schedules`
                JOIN skills ON skills.location = skill_map_schedules.location 
                AND skills.process = skill_map_schedules.process 
                AND skills.skill_code = skill_map_schedules.skill_code
                JOIN skill_maps ON skill_maps.location = skill_map_schedules.location 
                AND skill_maps.process = skill_map_schedules.process 
                AND skill_maps.skill_code = skill_map_schedules.skill_code
                JOIN employee_syncs ON employee_syncs.employee_id = skill_maps.employee_id 
            WHERE
                skill_maps.`value` = 1 
                AND skill_maps.location = '".$location[$i]->location."' 
                AND DATE_FORMAT( periode, '%Y-%m' ) = '".$now."'");

            if (count($skill_schedule)) {
                $mail_to = DB::SELECT("select email from send_emails where remark = '".$location[$i]->location."'");

                $data = [
                    'skill_schedule' => $skill_schedule,
                    'periode' => date("F Y", strtotime(date('Y-m-d'))),
                    'location' => $location[$i]->location,
                ];

                Mail::to($mail_to[0]->email)
                ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                ->send(new SendEmail($data, 'skill_map'));
            }
        }
    }
}
