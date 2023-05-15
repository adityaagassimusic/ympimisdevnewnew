<?php

namespace App\Console\Commands;

use App\MaterialPlantDataList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GenerateInitialSafetyStock extends Command
{
    protected $signature = 'generate:initial_safety_stock';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->output_breakdown = array();
        $this->check = array();
        $this->temp = array();
        $this->initial_location = array("SXA0", "SXA2", "FLA0", "FLA2", "CLA0", "CLA2", "ZPA0", "VNA0");

    }

    public function handle()
    {

        DB::table('ikhlas')->truncate();

        $materials = db::select("
            SELECT forecast.material_number, mpdl.material_description, forecast.quantity FROM
            (SELECT material_number, SUM(quantity) AS quantity FROM production_forecasts
            WHERE forecast_month LIKE '%" . date('Y-m') . "%'
            GROUP BY material_number
            ) AS forecast
            LEFT JOIN
            (SELECT * FROM material_plant_data_lists
            WHERE valcl = '9010') AS mpdl
            ON forecast.material_number = mpdl.material_number
            WHERE mpdl.material_description IS NOT NULL");

        for ($i = 0; $i < count($materials); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $materials[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if (in_array($breakdown[$j]->storage_location, $this->initial_location) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                    $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();

                    $row = array();
                    $row['material_parent'] = $materials[$i]->material_number;
                    $row['material_parent_description'] = $materials[$i]->material_description;
                    $row['target'] = $materials[$i]->quantity;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['material_child_description'] = $mpdl->material_description;
                    $row['uom'] = $mpdl->bun;
                    $row['storage_location'] = $mpdl->storage_location;
                    $row['usage'] = $materials[$i]->quantity * $breakdown[$j]->usage / $breakdown[$j]->divider;
                    $row['created_by'] = 1;
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->output_breakdown[] = $row;

                } else {

                    $row = array();
                    $row['material_parent'] = $breakdown[$j]->material_parent;
                    $row['target'] = $materials[$i]->quantity;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = $materials[$i]->quantity * $breakdown[$j]->usage / $breakdown[$j]->divider;
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->check[] = $row;

                }
            }
        }

        while (count($this->check) > 0) {
            $this->breakdownLoop();
        }

        $insert_breakdown = [];
        for ($i = 0; $i < count($this->output_breakdown); $i++) {
            $key = '';
            $key .= ($this->output_breakdown[$i]['material_parent'] . '#');
            $key .= ($this->output_breakdown[$i]['material_parent_description'] . '#');
            $key .= ($this->output_breakdown[$i]['material_child'] . '#');
            $key .= ($this->output_breakdown[$i]['material_child_description'] . '#');
            $key .= ($this->output_breakdown[$i]['uom'] . '#');
            $key .= ($this->output_breakdown[$i]['storage_location'] . '#');

            if (!array_key_exists($key, $insert_breakdown)) {
                $row = array();
                $row['material_parent'] = $this->output_breakdown[$i]['material_parent'];
                $row['material_parent_description'] = $this->output_breakdown[$i]['material_parent_description'];
                $row['target'] = $this->output_breakdown[$i]['target'];
                $row['material_child'] = $this->output_breakdown[$i]['material_child'];
                $row['material_child_description'] = $this->output_breakdown[$i]['material_child_description'];
                $row['uom'] = $this->output_breakdown[$i]['uom'];
                $row['storage_location'] = $this->output_breakdown[$i]['storage_location'];
                $row['usage'] = $this->output_breakdown[$i]['usage'];

                $insert_breakdown[$key] = $row;
            } else {
                $insert_breakdown[$key]['usage'] = $insert_breakdown[$key]['usage'] + $this->output_breakdown[$i]['usage'];
            }
        }

        foreach (array_chunk($insert_breakdown, 1000) as $row) {
            $output = DB::table('ikhlas')->insert($row);
        }

    }

    public function breakdownLoop()
    {

        $this->temp = array();

        for ($i = 0; $i < count($this->check); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $this->check[$i]['material_child'] . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if (in_array($breakdown[$j]->storage_location, $this->initial_location) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                    $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();
                    $parent = MaterialPlantDataList::where('material_number', $this->check[$i]['material_parent'])->first();

                    $row = array();
                    $row['material_parent'] = $this->check[$i]['material_parent'];
                    $row['material_parent_description'] = $parent->material_description;
                    $row['target'] = $this->check[$i]['target'];
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['material_child_description'] = $mpdl->material_description;
                    $row['uom'] = $mpdl->bun;
                    $row['storage_location'] = $mpdl->storage_location;
                    $row['usage'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                    $row['created_by'] = 1;
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->output_breakdown[] = $row;

                } else {
                    $row = array();
                    $row['material_parent'] = $this->check[$i]['material_parent'];
                    $row['target'] = $this->check[$i]['target'];
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['quantity'] = round($this->check[$i]['quantity'] * ($breakdown[$j]->usage / $breakdown[$j]->divider), 6);
                    $row['created_at'] = date('Y-m-d H:i:s');
                    $row['updated_at'] = date('Y-m-d H:i:s');

                    $this->temp[] = $row;
                }
            }
        }

        $this->check = array();
        $this->check = $this->temp;

    }

}
