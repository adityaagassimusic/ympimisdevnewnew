<?php

namespace App\Console\Commands;

use App\MaterialPlantDataList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateBomScrap extends Command
{

    protected $signature = 'generate:bom_scrap';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->output_breakdown = array();
        $this->check = array();
        $this->temp = array();

    }

    public function handle()
    {

        // Run ketika ada perubahan
        // $lock = db::table('locks')
        //     ->where('remark', 'breakdown_scrap')
        //     ->first();

        // if ($lock->status == 1) {
        //     exit;
        // }

        $messages = "*Generate BOM Scrap :*%0A%0A";
        $messages .= date('Y-m-d H:i:s') . " - Start%0A";

        $mpdl = MaterialPlantDataList::get();
        $mpdl = $mpdl->keyBy('material_number');

        $phantom_material = db::table('scrap_materials')
            ->where('spt', 50)
            ->get();

        for ($i = 0; $i < count($phantom_material); $i++) {
            $breakdown = db::select("
                    SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                    WHERE b.material_parent = '" . $phantom_material[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if ($breakdown[$j]->spt != 50) {
                    if (isset($mpdl[$phantom_material[$i]->material_number])) {
                        $row = array();
                        $row['material_parent'] = $phantom_material[$i]->material_number;
                        $row['material_parent_description'] = $phantom_material[$i]->material_description;
                        $row['material_parent_uom'] = $phantom_material[$i]->uom;
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['material_child_description'] = $mpdl[$breakdown[$j]->material_child]->material_description;
                        $row['material_child_uom'] = $mpdl[$breakdown[$j]->material_child]->bun;
                        $row['material_child_valcl'] = $mpdl[$breakdown[$j]->material_child]->valcl;
                        $row['usage'] = $breakdown[$j]->usage / $breakdown[$j]->divider;

                        $this->output_breakdown[] = $row;

                    }

                } else {
                    if (isset($mpdl[$phantom_material[$i]->material_number])) {
                        $row = array();
                        $row['material_parent'] = $phantom_material[$i]->material_number;
                        $row['material_parent_description'] = $phantom_material[$i]->material_description;
                        $row['material_parent_uom'] = $phantom_material[$i]->uom;
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['usage'] = $breakdown[$j]->usage / $breakdown[$j]->divider;

                        $this->check[] = $row;

                    }
                }
            }
        }

        while (count($this->check) > 0) {
            $this->temp = array();

            for ($i = 0; $i < count($this->check); $i++) {
                $breakdown = db::select("
                        SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                        WHERE b.material_parent = '" . $this->check[$i]['material_child'] . "'");

                for ($j = 0; $j < count($breakdown); $j++) {
                    if ($breakdown[$j]->spt != 50) {
                        if (isset($mpdl[$breakdown[$j]->material_child])) {
                            $row = array();
                            $row['material_parent'] = $this->check[$i]['material_parent'];
                            $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                            $row['material_parent_uom'] = $this->check[$i]['material_parent_uom'];
                            $row['material_child'] = $breakdown[$j]->material_child;
                            $row['material_child_description'] = $mpdl[$breakdown[$j]->material_child]->material_description;
                            $row['material_child_uom'] = $mpdl[$breakdown[$j]->material_child]->bun;
                            $row['material_child_valcl'] = $mpdl[$breakdown[$j]->material_child]->valcl;
                            $row['usage'] = $this->check[$i]['usage'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);

                            $this->output_breakdown[] = $row;
                        }

                    } else {
                        if (isset($mpdl[$breakdown[$j]->material_child])) {
                            $row = array();
                            $row['material_parent'] = $this->check[$i]['material_parent'];
                            $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                            $row['material_parent_uom'] = $this->check[$i]['material_parent_uom'];
                            $row['material_child'] = $breakdown[$j]->material_child;
                            $row['usage'] = $this->check[$i]['usage'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);

                            $this->temp[] = $row;
                        }
                    }
                }
            }

            $this->check = array();
            $this->check = $this->temp;
        }

        if (count($this->output_breakdown) > 0) {
            db::table('bom_scraps')->truncate();

            foreach ($this->output_breakdown as $row) {
                $insert = db::table('bom_scraps')
                    ->insert([
                        'material_parent' => $row['material_parent'],
                        'parent_description' => $row['material_parent_description'],
                        'material_child' => $row['material_child'],
                        'child_description' => $row['material_child_description'],
                        'child_uom' => $row['material_child_uom'],
                        'child_valcl' => $row['material_child_valcl'],
                        'usage' => $row['usage'],
                        'created_by' => 1,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $update_lock = db::table('locks')
                ->where('remark', 'breakdown_scrap')
                ->update([
                    'status' => 1,
                ]);

            $messages .= date('Y-m-d H:i:s') . " - Scrap Bom Breakdown Success%0A";

            $phones = [
                '6282234955505',
                '6287865101302',
            ];

            for ($i = 0; $i < count($phones); $i++) {
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
                    CURLOPT_POSTFIELDS => "receiver=" . $phones[$i] . "&device=6281130561777&message=" . $messages . '&type=chat',
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
}
