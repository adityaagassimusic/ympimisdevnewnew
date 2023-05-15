<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Mail\SendEmail;

class ReminderLocker extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:locker';

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
        $lockers = db::connection('ympimis_2')->table('lockers')->whereNotNull('employee_id')->get();
        $employees = db::table('employee_syncs')->get();

        $reminders = array();

        foreach($lockers as $locker){
            foreach($employees as $employee){
                if($locker->employee_id == $employee->employee_id){
                    $department = "";
                    $section = "";
                    if($employee->department != null){
                        $department = $employee->department;
                    }
                    if($employee->section != null){
                        $section = $employee->section;
                    }
                    $update_locker = db::connection('ympimis_2')->table('lockers')
                    ->where('employee_id', '=', $locker->employee_id)
                    ->update([
                        'department_name' => $department,
                        'section_name' => $section
                    ]);

                    if($employee->end_date != null){
                        $diff = date_diff(date_create(date('Y-m-d')), date_create($employee->end_date));
                        array_push($reminders, [
                            'locker_id' => $locker->locker_id,
                            'employee_id' => $locker->employee_id,
                            'employee_name' => $locker->employee_name,
                            'department_name' => $locker->department_name,
                            'section_name' => $locker->section_name,
                            'since' => $diff->format("%R%a days")
                        ]);
                    }
                }
            }
        }

        if(count($reminders) > 0){
            $data = [
                'lockers' => $reminders,
                'title' => 'Locker Users No Longer Exist Reminder'
            ];

            Mail::to(['rianita.widiastuti@music.yamaha.com', 'heriyanto@music.yamaha.com'])
            ->cc(['putri.sukma.riyanti@music.yamaha.com'])
            ->bcc('ympi-mis-ML@music.yamaha.com')
            ->send(new SendEmail($data, 'locker_reminder'));
        }
    }
}
