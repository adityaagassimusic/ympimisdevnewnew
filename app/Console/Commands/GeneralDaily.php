<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GeneralDaily extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'general:daily';

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
// CEK UNION
        $unions = db::connection('ympimis_2')->table('labor_unions')->get();

        foreach ($unions as $union) {
            $employee = db::connection('sunfish')->table('VIEW_YMPI_Emp_OrgUnit')->where('Emp_no', '=', $union->created_by)->first();

            if ($employee) {
                if ($union->remark == 'leave') {
                    if ($employee->Labour_Union == 'NONE' || $employee->Labour_Union == '' || $employee->Labour_Union == null) {
                        db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', $union->created_by)->delete();
                    }
                }
                if ($union->remark == 'join') {
                    if ($employee->Labour_Union == $union->union_name) {
                        db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', $union->created_by)->delete();
                    }
                }
            }
        }

        $mail_to = ['mahendra.putra@music.yamaha.com', 'adhi.satya.indradhi@music.yamaha.com', 'ummi.ernawati@music.yamaha.com'];
        $mail_cc = ['khoirul.umam@music.yamaha.com'];
        $mail_bcc = ['ympi-mis-ML@music.yamaha.com'];

        $labor_unions = db::connection('ympimis_2')->table('labor_unions')->orderBy('created_at', 'ASC')->get();

        $data = [
            'labor_unions' => $labor_unions,
        ];

        if (count($labor_unions) > 0) {
            Mail::to($mail_to)->cc($mail_cc)->bcc($mail_bcc)->send(new SendEmail($data, 'union_mail'));
        }

        // RECORD REPAIR ROOM
        $date = date('Y-m-d', strtotime('-1 Day'));

        $repair_inventories = db::connection('ympimis_2')->select("SELECT
            *
            FROM
            repair_room_inventories");

        foreach ($repair_inventories as $row) {
            $records = db::connection('ympimis_2')->table('repair_room_records')
                ->insert([
                    'stock_date' => $date,
                    'material_number' => $row->material_number,
                    'material_description' => $row->material_description,
                    'quantity' => $row->quantity,
                    'location' => $row->location,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }

        $repair_logs = db::connection('ympimis_2')->select("SELECT
            	rrl.material_number,
            	rrl.material_description,
            	rrl.location,
            	sum( rrl.quantity ) AS quantity
            FROM
            	repair_room_logs AS rrl
            WHERE
            	rrl.quantity > 0
            GROUP BY
            	rrl.material_number,
            	rrl.material_description,
            	rrl.location
            ORDER BY
            	rrl.material_number ASC");

        $material_numbers = array();

        foreach ($repair_logs as $row) {
            array_push($material_numbers, $row->material_number);
        }

        $mpdls = db::table('material_plant_data_lists')->whereIn('material_number', $material_numbers)->get();

        if (count($material_numbers) > 0) {
            db::connection('ympimis_2')->table('repair_room_log_records')->truncate();
        }

        foreach ($repair_logs as $row) {
            $material_number = $row->material_number;
            $material_description = $row->material_description;
            $standard_price = 0;
            $quantity = $row->quantity;
            $location = $row->location;
            $amount = 0;

            foreach ($mpdls as $row2) {
                if ($row->material_number == $row2->material_number) {
                    $standard_price = round($row2->standard_price / 1000, 4);
                    $amount = round($quantity * $standard_price, 1);

                    break;
                }
            }

            db::connection('ympimis_2')->table('repair_room_log_records')->insert([
                'material_number' => $material_number,
                'material_description' => $material_description,
                'standard_price' => $standard_price,
                'quantity' => $quantity,
                'amount' => $amount,
                'location' => $location,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // CEK FLOS VS INVENTORIES
        $checks = db::select("SELECT
            material_number,
            location,
            sum( inventory ) AS inventory,
            sum( quantity ) AS actual
            FROM
            (
                SELECT
                f.material_number,
                IF
                ( STATUS = '2', 'FSTK', m.issue_storage_location ) AS location,
                0 AS inventory,
                sum( f.actual ) AS quantity
                FROM
                flos AS f
                LEFT JOIN materials AS m ON m.material_number = f.material_number
                WHERE
                STATUS IN ( 'M', '0', '1', '2' )
                GROUP BY
                f.material_number,
                IF
                ( STATUS = '2', 'FSTK', m.issue_storage_location ) UNION ALL
                SELECT
                i.material_number,
                i.storage_location AS location,
                sum( i.quantity ) AS inventory,
                0 AS quantity
                FROM
                inventories AS i
                LEFT JOIN materials AS m ON m.material_number = i.material_number
                WHERE
                m.category = 'FG'
                GROUP BY
                material_number,
                storage_location
                ) AS A
            GROUP BY
            material_number,
            location
            HAVING
            inventory <> actual");

        foreach ($checks as $row) {
            $update_inventory = db::table('inventories')->where('material_number', '=', $row->material_number)
                ->where('storage_location', '=', $row->location)
                ->update([
                    'quantity' => $row->actual,
                ]);
        }

        //CEK BENTO QUOTA
        $bento_quotas = db::select("SELECT
            cek.due_date, cek.serving_ordered, cek.cnt
            FROM
            bento_quotas
            INNER JOIN (
                SELECT
                b.due_date,
                bq.serving_ordered,
                count( b.id ) AS cnt
                FROM
                bentos AS b
                LEFT JOIN bento_quotas AS bq ON bq.due_date = b.due_date
                WHERE
                b.grade_code <> 'J0-'
                AND b.STATUS NOT IN ( 'Cancelled', 'Rejected' )
                GROUP BY
                b.due_date,
                bq.serving_ordered
                HAVING
                cnt <> bq.serving_ordered
            ) AS cek ON cek.due_date = bento_quotas.due_date");

        foreach ($bento_quotas as $bento_quota) {
            db::table('bento_quotas')->where('due_date', '=', $bento_quota->due_date)
                ->update([
                    'serving_ordered' => $bento_quota->cnt,
                ]);
        }

        //CEK BENTO GENERAL ATTENDANCE
        $bentos = db::select("SELECT
            b.employee_id,
            b.due_date
            FROM
            bentos AS b
            LEFT JOIN general_attendances AS ga ON ga.due_date = b.due_date
            AND ga.employee_id = b.employee_id
            WHERE
            `status` = 'Approved'
            AND ga.employee_id IS NULL
            AND b.deleted_at IS NULL
            AND b.grade_code <> 'J0-'");

        foreach ($bentos as $bento) {
            db::table('general_attendances')->insert([
                'purpose_code' => 'Bento',
                'due_date' => $bento->due_date,
                'employee_id' => $bento->employee_id,
                'created_by' => '1',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }

        // RECORD AKUMULASI EFFISIENSI
        // $date = date('Y-m-d', strtotime('-1 Day'));
        // $first = date('Y-m-01', strtotime($date));
        // $last = date('Y-m-t', strtotime($date));

        // $efficiency_outputs = db::select("SELECT
        //     est.location,
        //     est.category,
        //     est.remark,
        //     SUM(
        //         IFNULL( est.standard_time * k.output, 0 )) AS output
        //     FROM
        //     efficiency_standard_times AS est
        //     LEFT JOIN (
        //         SELECT
        //         date( h.created_at ) AS due_date,
        //         m.material_number,
        //         sum( h.lot ) AS output
        //         FROM
        //         kitto.histories AS h
        //         LEFT JOIN kitto.materials AS m ON m.id = h.completion_material_id
        //         WHERE
        //         h.category IN ( 'completion', 'completion_adjustment', 'completion_adjustment_excel', 'completion_adjustment_manual', 'completion_cancel', 'completion_error', 'completion_return', 'completion_scrap', 'completion_repair', 'completion_after_repair', 'completion_temporary_delete' )
        //         AND h.created_at >= '".$first."'
        //         AND h.created_at <= '".$last."'
        //         GROUP BY
        //         date( h.created_at ),
        //         m.material_number
        //         ) AS k ON k.material_number = est.material_number
        //         WHERE
        //         k.due_date IS NOT NULL
        //         GROUP BY
        //         est.location,
        //         est.category,
        //         est.remark
        //         ORDER BY
        //         k.due_date ASC");

        //     $manpowers = db::select("SELECT
        //         valid_date,
        //         employee_id,
        //         employee_name,
        //         location,
        //         category,
        //         remark
        //         FROM
        //         efficiency_manpowers AS ee
        //         WHERE
        //         ee.valid_date >= '".$first."'
        //         AND ee.valid_date <= '".$last."'");

        //     $where_idx = "";
        //     $idx = "";
        //     for($x = 0; $x < count($manpowers); $x++) {
        //         $idx = $idx."'".$manpowers[$x]->employee_id."'";
        //         if($x != count($manpowers)-1){
        //             $idx = $idx.",";
        //         }
        //     }
        //     $where_idx = " AND emp_no in (".$idx.") ";

        //     $efficiency_input = db::connection('sunfish')
        //     ->select("SELECT
        //         due_date,
        //         shift,
        //         emp_no,
        //         SUM ( IIF ( shift = 'Shift_1', 480, IIF ( shift = 'Shift_2', 450, 420 ) ) ) AS work_hour,
        //         SUM ( total_ot ) AS total_ot
        //         FROM
        //         (
        //             SELECT
        //             format ( shiftstarttime, 'yyyy-MM-dd' ) AS due_date,
        //             emp_no,
        //             IIF ( shiftdaily_code LIKE '%Shift_1%', 'Shift_1', IIF ( shiftdaily_code LIKE '%Shift_2%', 'Shift_2', 'Other' ) ) AS shift,
        //             total_ot
        //             FROM
        //             VIEW_YMPI_ATTENDANCE
        //             WHERE
        //             Attend_Code LIKE '%PRS%'
        //             ".$where_idx."
        //             AND format ( shiftstarttime, 'yyyy-MM-dd' ) >= '".$first."'
        //             AND format ( shiftstarttime, 'yyyy-MM-dd' ) <= '".$last."'
        //             ) AS attends
        //             GROUP BY
        //             due_date,
        //             shift,
        //             emp_no");

        //         $input = array();

        //         foreach($manpowers as $manpower) {
        //             $valid_date = $manpower->valid_date;
        //             $employee_id = $manpower->employee_id;
        //             $location = $manpower->location;
        //             $category = $manpower->category;
        //             $remark = $manpower->remark;
        //             $work_hour = 0;

        //             foreach($efficiency_input as $eff){
        //                 if($eff->emp_no == $employee_id && $eff->due_date == $valid_date){
        //                     $work_hour = $eff->work_hour+$eff->total_ot;
        //                 }
        //             }

        //             array_push($input,
        //                 [
        //                     'valid_date' => $valid_date,
        //                     'employee_id' => $employee_id,
        //                     'location' => $location,
        //                     'category' => $category,
        //                     'remark' => $remark,
        //                     'work_hour' => $work_hour
        //                 ]);
        //         }

        //         $groups = array();

        //         foreach($input as $row){
        //             $key = $row['location'].'!'.$row['category'].'!'.$row['remark'];
        //             if (!array_key_exists($key, $groups)) {
        //                 $groups[$key] = array(
        //                     'location' => $row['location'],
        //                     'category' => $row['category'],
        //                     'remark' => $row['remark'],
        //                     'input' => $row['work_hour'],
        //                 );
        //             }
        //             else{
        //                 $groups[$key]['input'] = $groups[$key]['input'] + $row['work_hour'];
        //             }
        //         }

        //         $efficiency_monthly = "";

        //         foreach($efficiency_outputs as $efficiency_output){
        //             $efficiency_monthly = db::select("SELECT *
        //                 FROM
        //                 efficiency_monthlies
        //                 WHERE
        //                 valid_date = '".$first."'
        //                 AND location = '".$efficiency_output->location."'
        //                 AND category = '".$efficiency_output->category."'
        //                 AND remark = '".$efficiency_output->remark."' ");

        //             if(count($efficiency_monthly) > 0){
        //                 db::table('efficiency_monthlies')
        //                 ->where('location', '=', $efficiency_output->location)
        //                 ->where('category', '=', $efficiency_output->category)
        //                 ->where('remark', '=', $efficiency_output->remark)
        //                 ->update([
        //                     'output' => $efficiency_output->output,
        //                     'updated_at' => date('Y-m-d H:i:s')
        //                 ]);
        //             }
        //             else{
        //                 db::table('efficiency_monthlies')
        //                 ->where('location', '=', $efficiency_output->location)
        //                 ->where('category', '=', $efficiency_output->category)
        //                 ->where('remark', '=', $efficiency_output->remark)
        //                 ->insert([
        //                     'valid_date' => $first,
        //                     'input' => 0,
        //                     'output' => $efficiency_output->output,
        //                     'location' => $efficiency_output->location,
        //                     'category' => $efficiency_output->category,
        //                     'remark' => $efficiency_output->remark,
        //                     'created_by' => 1,
        //                     'created_at' => date('Y-m-d H:i:s'),
        //                     'updated_at' => date('Y-m-d H:i:s')
        //                 ]);
        //             }
        //         }

        //         $efficiency_monthly = "";

        //         foreach($groups as $group){
        //             $efficiency_monthly = db::select("SELECT *
        //                 FROM
        //                 efficiency_monthlies
        //                 WHERE
        //                 valid_date = '".$first."'
        //                 AND location = '".$group['location']."'
        //                 AND category = '".$group['category']."'
        //                 AND remark = '".$group['remark']."' ");

        //             if(count($efficiency_monthly) > 0){
        //                 db::table('efficiency_monthlies')
        //                 ->where('location', '=', $group['location'])
        //                 ->where('category', '=', $group['category'])
        //                 ->where('remark', '=', $group['remark'])
        //                 ->update([
        //                     'input' => $group['input'],
        //                     'updated_at' => date('Y-m-d H:i:s')
        //                 ]);
        //             }
        //             else{
        //                 db::table('efficiency_monthlies')
        //                 ->where('location', '=', $group['location'])
        //                 ->where('category', '=', $group['category'])
        //                 ->where('remark', '=', $group['remark'])
        //                 ->insert([
        //                     'valid_date' => $first,
        //                     'input' => $group['input'],
        //                     'output' => 0,
        //                     'location' => $group['location'],
        //                     'category' => $group['category'],
        //                     'remark' => $group['remark'],
        //                     'created_by' => 1,
        //                     'created_at' => date('Y-m-d H:i:s'),
        //                     'updated_at' => date('Y-m-d H:i:s')
        //                 ]);
        //             }
        //         }

    }

}
