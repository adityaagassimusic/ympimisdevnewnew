<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneralMinute extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'general:minute';

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
        $omi_lists = db::connection('rfid')->select("SELECT
            id,
            employee_id,
            created_at,
            TIMESTAMPDIFF(
                SECOND,
                created_at,
                now()) AS diff 
            FROM
            `omi_lists` 
            WHERE
            TIMESTAMPDIFF(
                SECOND,
                created_at,
                now()) >= 300");

        $omi_ids = array();

        foreach($omi_lists as $omi_list){
            $omi_logs = db::connection('rfid')->table('omi_logs')->insert([
                'employee_id' => $omi_list->employee_id,
                'last_seen_1' => $omi_list->created_at,
                'last_seen_2' => date('Y-m-d H:i:s'),
                'created_at' => $omi_list->created_at,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            array_push($omi_ids, $omi_list->id);
        }

        $delete_omi_lists = db::connection('rfid')->table('omi_lists')
        ->whereIn('id', $omi_ids)
        ->delete();
    }
}
