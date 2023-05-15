<?php

namespace App\Console\Commands;

use App\Libraries\ActMLEasyIf;
use App\Plc;
use App\ErrorLog;
use App\Ticket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\QueryException;


class PowerMonitor extends Command
{

    protected $signature = 'log:power_monitor';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        echo "start ". date('H:i:s');
        echo PHP_EOL;

        $tickets = db::select("SELECT
           tickets.ticket_id,
           tickets.status,
           tickets.department,
           tickets.category,
           tickets.priority,
           tickets.priority_reason,
           tickets.case_title,
           tickets.case_description,
           tickets.document,
           tickets.due_date_from,
           tickets.due_date_to,
           tickets.estimated_due_date_from,
           tickets.estimated_due_date_to,
           tickets.pic_id,
           tickets.pic_name,
           tickets.difficulty,
           tickets.progress,
           tickets.project_name,
           tickets.remark,
           tickets.created_by,
           tickets.created_at,
           tickets.updated_at,
           ticket_pics.pic_shortname,
           d.department_shortname
           FROM
           tickets
           LEFT JOIN departments AS d ON d.department_name = tickets.department
           LEFT JOIN ticket_pics ON tickets.pic_id = ticket_pics.pic_id
           WHERE
           tickets.deleted_at is null
           ORDER BY FIELD(priority,'Very High','High','Normal') ASC,
           tickets.ticket_id DESC");

            var_dump(count($tickets));

        echo "end ". date('H:i:s');
        echo PHP_EOL;

        die();


        // START GET DATA FROM PLC
        $plcs = Plc::where('station', 4)
        ->where('remark', 'LIKE', '%Electric Energy Import%')
        ->select(
            'station',
            'location',
            db::raw('MAX(address) AS max'),
            db::raw('MIN(address) AS min')
        )
        ->groupBy('station', 'location')
        ->orderBy('location', 'ASC')
        ->get();

        // dd($plcs);

        $date = date('Y-m-d H:i:s');

        foreach ($plcs as $plc) {

            try {
                $cpu = new ActMLEasyIf($plc->station);

                // echo "Stat " . $plc->station . ', ';
                // echo "Loc " . $plc->location . ', ';
                // echo "Min " . $plc->min . '=' . $cpu->read_data($plc->min, 1)[0] . ', ';
                // echo "Max " . $plc->max . '=' . $cpu->read_data($plc->max, 1)[0] . ' ';
                // echo '__';

                $address_1 = $cpu->read_data($plc->min, 1);
                $address_2 = $cpu->read_data($plc->max, 1);

                $bin_data_1 = decbin($address_1[0]);
                $bin_data_2 = decbin($address_2[0]);

                $kwh = bindec($bin_data_2.$bin_data_1);

                $before = db::table('electricity_usages')
                ->where('location', $plc->location)
                ->where('datetime', '<', $date)
                ->orderBy('datetime', 'DESC')
                ->first();

                $usage = $kwh - $before->kwh_point;

                $log = db::table('electricity_usages')
                ->insert([
                    'location' => $plc->location,
                    'datetime' => $date,
                    'first' => $address_1[0],
                    'second' => $address_2[0],
                    'kwh_point' => $kwh,
                    'usage' => $usage,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            } catch (Exception $e) {

                $error_log = new ErrorLog([
                    'error_message' => '[Power Monitor] : '.$plc->station.' '.$datetime.' = '.$e->getMessage(),
                    'created_by' => $id,
                ]);
                $error_log->save();

            }            

        }
        // END GET DATA FROM PLC


        // START ERROR NOTIFIcATION
        if(intval(date('H')) == 23){

            $error = ErrorLog::where('created_at', 'LIKE', '%'.$date('Y-m-d').'%')
            ->where('error_message', 'LIKE', '%Power Monitor%')
            ->get();

        }
        // END ERROR NOTIFIcATION

    }
}
