<?php

namespace App\Console\Commands;

use App\MaterialPlantDataList;
use App\Smbmr;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class GenerateSmbmr extends Command
{
    protected $signature = 'generate:smbmr';

    protected $description = 'Command description';
    protected $output_breakdown;
    protected $check;
    protected $temp;
    protected $mpdl;

    public function __construct()
    {
        parent::__construct();
        $this->output_breakdown = [];
        $this->check = [];
        $this->temp = [];
        $this->mpdl = [];
    }

    public function handle()
    {
        // Run ketika ada perubahan
        $lock = db::table('locks')
            ->where('remark', 'breakdown_smbmr')
            ->first();

        if ($lock->status == 1) {
            exit();
        }

        Smbmr::truncate();

        $this->mpdl = MaterialPlantDataList::get();
        $this->mpdl = $this->mpdl->keyBy('material_number');

        $materials = db::select("
            SELECT DISTINCT material_number, material_description FROM
                (SELECT forecast.material_number, mpdl.material_description FROM
                    (SELECT DISTINCT material_number FROM production_forecasts) AS forecast
                        LEFT JOIN
                    (SELECT * FROM material_plant_data_lists
                        WHERE valcl = '9010') AS mpdl
                        ON forecast.material_number = mpdl.material_number
                        WHERE mpdl.material_description IS NOT NULL
            UNION ALL
                SELECT material_number, description FROM extra_order_materials
                    WHERE material_number != 'NEW'
                    AND is_completion = 1) AS all_material");

        for ($i = 0; $i < count($materials); $i++) {
            $breakdown = db::select("
            SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $materials[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->valcl != '9040' && $breakdown[$j]->spt != '30') {
                    $row = [];
                    $row['material_parent'] = $breakdown[$j]->material_parent;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = $breakdown[$j]->usage / $breakdown[$j]->divider;
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->check[] = $row;
                } else {
                    $pgr = ['G01', 'G08', 'G15', '999'];

                    if (in_array($this->mpdl[$materials[$i]->material_number]->pgr, $pgr)) {
                        $row = [];
                        $row['material_parent'] = $materials[$i]->material_number;
                        $row['material_parent_description'] = $materials[$i]->material_description;
                        $row['raw_material'] = $breakdown[$j]->material_child;
                        $row['raw_material_description'] = $this->mpdl[$materials[$i]->material_number]->material_description;
                        $row['uom'] = $this->mpdl[$materials[$i]->material_number]->bun;
                        $row['pgr'] = $this->mpdl[$materials[$i]->material_number]->pgr;
                        $row['usage'] = $breakdown[$j]->usage / $breakdown[$j]->divider;
                        $row['created_by'] = 1;
                        $row['created_at'] = date('Y-m-d H:i:s');
                        $row['updated_at'] = date('Y-m-d H:i:s');

                        $this->output_breakdown[] = $row;
                    }
                }
            }
        }

        while (count($this->check) > 0) {
            $this->breakdownLoop();
        }

        $insert_breakdown = [];
        for ($i = 0; $i < count($this->output_breakdown); $i++) {
            $key = '';
            $key .= $this->output_breakdown[$i]['material_parent'] . '#';
            $key .= $this->output_breakdown[$i]['material_parent_description'] . '#';
            $key .= $this->output_breakdown[$i]['raw_material'] . '#';
            $key .= $this->output_breakdown[$i]['raw_material_description'] . '#';
            $key .= $this->output_breakdown[$i]['uom'] . '#';
            $key .= $this->output_breakdown[$i]['pgr'] . '#';

            if (!array_key_exists($key, $insert_breakdown)) {
                $row = [];
                $row['material_parent'] = $this->output_breakdown[$i]['material_parent'];
                $row['material_parent_description'] = $this->output_breakdown[$i]['material_parent_description'];
                $row['raw_material'] = $this->output_breakdown[$i]['raw_material'];
                $row['raw_material_description'] = $this->output_breakdown[$i]['raw_material_description'];
                $row['uom'] = $this->output_breakdown[$i]['uom'];
                $row['pgr'] = $this->output_breakdown[$i]['pgr'];
                $row['usage'] = $this->output_breakdown[$i]['usage'];
                $row['created_by'] = 1;
                $row['created_at'] = date('Y-m-d H:i:s');
                $row['updated_at'] = date('Y-m-d H:i:s');

                $insert_breakdown[$key] = $row;
            } else {
                $insert_breakdown[$key]['usage'] = $insert_breakdown[$key]['usage'] + $this->output_breakdown[$i]['usage'];
            }
        }

        foreach (array_chunk($insert_breakdown, 1000) as $t) {
            $output = Smbmr::insert($t);
        }

        $update_lock = db::table('locks')
            ->where('remark', 'breakdown_smbmr')
            ->update([
                'status' => 1,
            ]);

        $to = ['muhammad.ikhlas@music.yamaha.com'];

        $bcc = [];

        $title = 'Generate SMBMR';
        $body = 'Count Data : ' . count($insert_breakdown);
        self::mailReport($title, $body, $to, $bcc);
    }

    public function breakdownLoop()
    {
        $this->temp = [];

        for ($i = 0; $i < count($this->check); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $this->check[$i]['material_child'] . "'"
            );

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->valcl != '9040' && $breakdown[$j]->spt != '30') {
                    $row = [];
                    $row['material_parent'] = $this->check[$i]['material_parent'];
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->temp[] = $row;
                } else {
                    $pgr = ['G01', 'G08', 'G15', '999'];

                    if (in_array($this->mpdl[$breakdown[$j]->material_child]->pgr, $pgr)) {
                        $row = [];
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_parent_description'] = $this->mpdl[$this->check[$i]['material_parent']]->material_description;
                        $row['raw_material'] = $breakdown[$j]->material_child;
                        $row['raw_material_description'] = $this->mpdl[$breakdown[$j]->material_child]->material_description;
                        $row['uom'] = $this->mpdl[$breakdown[$j]->material_child]->bun;
                        $row['pgr'] = $this->mpdl[$breakdown[$j]->material_child]->pgr;
                        $row['usage'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                        $row['created_by'] = 1;
                        $row['created_at'] = date('Y-m-d H:i:s');
                        $row['updated_at'] = date('Y-m-d H:i:s');

                        $this->output_breakdown[] = $row;
                    }
                }
            }
        }

        $this->check = [];
        $this->check = $this->temp;
    }

    public function mailReport($title, $body, $mail_to, $bcc)
    {
        Mail::raw([], function ($message) use ($title, $body, $mail_to, $bcc) {
            $message->from('mis@ympi.co.id', 'PT. Yamaha Musical Products Indonesia');
            $message->to($mail_to);
            $message->bcc($bcc);
            $message->subject($title);
            $message->setBody($body, 'text/plain');
        });
    }
}