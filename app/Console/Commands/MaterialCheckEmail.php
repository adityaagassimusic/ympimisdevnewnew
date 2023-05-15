<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class MaterialCheckEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'material:check_email';

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
        $material_checks = db::connection('ympimis_2')->table('material_checks')
            ->where('status', '=', 'Waiting')
            ->get();

        $data = array();
        $to = array();

        $approvers = db::select("SELECT
            a.approver_email, d.department_shortname_2
            FROM
            approvers AS a
            LEFT JOIN departments AS d ON d.department_name = a.department
            WHERE
            a.remark = 'Foreman'");

        foreach ($material_checks as $row) {
            $inout_no = $row->inout_no;
            $entry_date = $row->entry_date;
            $posting_date = $row->posting_date;
            $material_number = $row->material_number;
            $material_description = $row->material_description;
            $receive_qty = $row->receive_qty;
            $sample_qty = $row->sample_qty;
            $uom = $row->uom;
            $location = $row->location;
            $status = 'Warehouse';
            $created_at = $row->created_at;
            $updated_at = $row->updated_at;

            foreach ($approvers as $approver) {
                if ($approver->department_shortname_2 == $location) {
                    if (!in_array($approver->approver_email, $to)) {
                        array_push($to, $approver->approver_email);
                    }
                }
            }

            array_push($data, [
                'inout_no' => $inout_no,
                'entry_date' => $entry_date,
                'posting_date' => $posting_date,
                'material_number' => $material_number,
                'material_description' => $material_description,
                'receive_qty' => $receive_qty,
                'sample_qty' => $sample_qty,
                'uom' => $uom,
                'location' => $location,
                'status' => $status,
                'created_at' => $created_at,
                'updated_at' => $updated_at,
            ]);
        }

        if (count($data) > 0) {
            Mail::to($to)
                ->cc(['dwi.misnanto@music.yamaha.com', 'fatchur.rozi@music.yamaha.com'])
                ->bcc(['ympi-mis-ML@music.yamaha.com'])
                ->queue(new SendEmail($data, 'material_check_notification'));
        }

    }
}
