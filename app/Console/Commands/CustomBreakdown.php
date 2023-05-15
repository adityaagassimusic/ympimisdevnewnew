<?php

namespace App\Console\Commands;

use App\MaterialPlantDataList;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CustomBreakdown extends Command
{
    protected $signature = 'custom:breakdown';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
        $this->output_breakdown = array();
        $this->check = array();
        $this->temp = array();
        $this->control = [
            'VAW0800',
            'VAW0810',
            'VAW0820',
            'VAW0830',
            'VAW0840',
            'VDZ0440',
            'VDZ0441',
            'VDZ0442',
            'VEA973B',
            'VEA973C',
            'VEK0353',
            'VEK0363',
            'VEK0373',
            'VEK0383',
            'VEK0393',
            'VEK7863',
            'VEK7873',
            'VEK7883',
            'VEM5311',
            'VEM6420',
            'VEM6421',
            'VEM6424',
            'VEM6440',
            'VEM644A',
            'VEM644C',
            'VEM6450',
            'VEM6460',
            'VEM646A',
            'VEM646C',
            'VEM6470',
            'VEM6480',
            'VEM648A',
            'VEM648C',
            'VEM6490',
            'VEM649A',
            'VEM649C',
            'VEM6500',
            'VEM6520',
            'VEM652A',
            'VEM652C',
            'VEM6530',
            'VEM6540',
            'VEM654A',
            'VEM654C',
            'VEM6550',
            'VEM655A',
            'VEM655C',
            'VEM6560',
            'VEM656A',
            'VEM656C',
            'VEM6580',
            'VEM658A',
            'VEM6590',
            'VEM6600',
            'VEM660A',
            'VEM6610',
            'VEM6620',
            'VEM662A',
            'VEM6630',
            'VEM6640',
            'VEM664A',
            'VEM6650',
            'VEM6660',
            'VEM666A',
            'VEM666C',
            'VEM6670',
            'VEM667A',
            'VEM667C',
            'VEM6680',
            'VEM668A',
            'VEM668C',
            'VFU0610',
            'VFU0620',
            'VFU0630',
            'VFU0640',
            'VFU0750',
            'VFU0830',
            'VFU0950',
            'VFU1030',
            'VFU2060',
            'VFU2070',
            'VFU2080',
            'VFU2100',
            'VFU2110',
            'VFU2120',
            'VFU2150',
            'VFU2160',
            'VFU4090',
            'VFU4130',
            'VFX7470',
            'VFX7471',
            'VFX7473',
            'VFX7480',
            'VFX7481',
            'VFX7483',
            'VFX7490',
            'VFX7491',
            'VFX7493',
            'W56190B',
            'W56190C',
            'W60653A',
            'W61016A',
            'W61197A',
            'W93543B',
            'W93546B',
            'W93546C',
            'W93627B',
            'WF9525C',
            'WN7902A',
            'WQ4642B',
            'WQ4642C',
            'WQ4642E',
            'WQ4674B',
            'WQ4674C',
            'WQ4674E',
            'WS1955A',
            'WS2253A',
            'WY75110',
            'WY75260',
            'WY84940',
            'WY84980',
            'WY85000',
            'WY85940',
            'WY91390',
            'WY9341A',
            'WY9341C',
            'WZ70060',
            'WZ70070',
            'WZ91100',
            'WZ9110A',
            'WZ9110C',
            'WZ9110E',
            'WZ91160',
            'WZ9116A',
            'WZ9116C',
            'WZ91340',
            'WZ9134A',
            'WZ9134C',
            'WZ91370',
            'WZ9137A',
            'WZ9137C',
            'WZ91390',
            'WZ91400',
            'WZ91830',
            'WZ9183C',
            'WZ91840',
            'WZ9184A',
            'WZ9184C',
            'ZA14780',
            'ZA1478C',
            'ZA14930',
            'ZA1493C',
            'ZA14990',
            'ZA1499A',
            'ZA1499C',
            'ZA15000',
            'ZA1500A',
            'ZA1500C',
            'ZA15040',
            'ZA52440',
            'ZA5244A',
            'ZA52450',
            'ZA5245A',
            'ZA5245C',
            'ZA52480',
            'ZA89860',
            'ZA8986A',
            'ZA8986C',
            'ZA89870',
            'ZA8987A',
            'ZA8987C',
            'ZC34590',
            'ZC58580',
            'ZC58750',
            'ZC7290A',
            'ZC7633A',
            'ZC7633B',
            'ZC7633D',
            'ZC7636E',
            'ZC7636F',
            'ZC7636H',
            'ZC7783B',
            'ZC7783E',
            'ZC7785A',
            'ZC7785B',
            'ZC7785D',
            'ZD10850',
            'ZD10860',
            'ZD11700',
            'ZD11720',
            'ZD1173B',
            'ZE08440',
            'ZE20540',
            'ZE20620',
            'ZE20660',
            'ZE20670',
            'ZE20750',
            'ZE20810',
            'ZE20820',
            'ZE20840',
            'ZF62650',
            'ZF62660',
            'ZF6670C',
            'ZF6671C',
            'ZH49530',
            'ZH4953A',
            'ZH4953C',
            'ZH50360',
            'ZJ19280',
            'ZJ32730',
            'ZJ32850',
            'ZJ3285A',
            'ZJ3285C',
            'ZJ32860',
            'ZJ3286A',
            'ZJ3286C',
            'ZJ32870',
            'ZK13370',
            'ZK38110',
            'ZK3811A',
            'ZK3811C',
            'ZK50340',
            'ZK50360',
            'ZP55240',
            'ZT43760',
            'ZT43770',
            'ZT43800',
            'ZT43820',
            'ZT43830',
            'ZT43980',
            'ZT44020',
            'ZU02230',
            'ZU02270',
            'ZU02340',
            'ZU02350',
            'ZU02370',
            'ZU02400',
            'ZU02410',
            'ZU02430',
            'ZU02940',
            'ZU02980',
            'ZU03290',
            'ZU05690',
            'ZU06190',
            'ZU06200',
            'ZU10790',
            'ZU10800',
            'ZU10820',
            'ZU11650',
            'ZU11660',
            'ZU11680',
            'ZU22730',
            'ZU22740',
            'ZU22760',
            'ZU24030',
            'ZU24050',
            'ZU24060',
            'ZU24080',
            'ZU24290',
            'ZU24300',
            'ZU24320',
            'ZU24340',
            'ZU24350',
            'ZU44090',
            'ZU44140',
            'ZU94760',
            'ZU94810',
        ];
    }

    public function handle()
    {

        DB::table('ikhlas')->truncate();

        $materials = db::select("SELECT * FROM ikhlas_breakdown");

        for ($i = 0; $i < count($materials); $i++) {
            $breakdown = db::select("
                SELECT b.material_parent, b.material_child, b.`usage`, b.divider, b.spt, b.storage_location, b.valcl FROM bom_outputs b
                WHERE b.material_parent = '" . $materials[$i]->material_number . "'");

            for ($j = 0; $j < count($breakdown); $j++) {
                if (in_array($breakdown[$j]->material_child, $this->control) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                    $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();

                    $row = array();
                    $row['material_parent'] = $materials[$i]->material_number;
                    $row['material_parent_description'] = $materials[$i]->material_description;
                    $row['material_parent_issue_location'] = $materials[$i]->issue_location;
                    $row['quantity'] = $materials[$i]->quantity;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['material_child_description'] = $mpdl->material_description;
                    $row['material_child_issue_location'] = $mpdl->storage_location;
                    $row['usage'] = $materials[$i]->quantity * $breakdown[$j]->usage / $breakdown[$j]->divider;

                    $this->output_breakdown[] = $row;

                } else {

                    $row = array();
                    $row['material_parent'] = $materials[$i]->material_number;
                    $row['material_parent_description'] = $materials[$i]->material_description;
                    $row['material_parent_issue_location'] = $materials[$i]->issue_location;
                    $row['quantity'] = $materials[$i]->quantity;
                    $row['material_child'] = $breakdown[$j]->material_child;
                    $row['usage'] = $materials[$i]->quantity * $breakdown[$j]->usage / $breakdown[$j]->divider;

                    $this->check[] = $row;

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
                    if (in_array($breakdown[$j]->material_child, $this->control) && $breakdown[$j]->spt == null && $breakdown[$j]->valcl == '9030') {

                        $mpdl = MaterialPlantDataList::where('material_number', $breakdown[$j]->material_child)->first();
                        $parent = MaterialPlantDataList::where('material_number', $this->check[$i]['material_parent'])->first();

                        $row = array();
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                        $row['material_parent_issue_location'] = $this->check[$i]['material_parent_issue_location'];
                        $row['quantity'] = $this->check[$i]['quantity'];
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['material_child_description'] = $mpdl->material_description;
                        $row['material_child_issue_location'] = $mpdl->storage_location;
                        $row['usage'] = $this->check[$i]['usage'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);

                        $this->output_breakdown[] = $row;

                    } else {

                        $row = array();
                        $row['material_parent'] = $this->check[$i]['material_parent'];
                        $row['material_parent_description'] = $this->check[$i]['material_parent_description'];
                        $row['material_parent_issue_location'] = $this->check[$i]['material_parent_issue_location'];
                        $row['quantity'] = $this->check[$i]['quantity'];
                        $row['material_child'] = $breakdown[$j]->material_child;
                        $row['usage'] = $this->check[$i]['usage'] * ($breakdown[$j]->usage / $breakdown[$j]->divider);
                        $this->temp[] = $row;

                    }
                }
            }

            $this->check = array();
            $this->check = $this->temp;

        }

        foreach (array_chunk($this->output_breakdown, 1000) as $row) {
            $output = DB::table('ikhlas')->insert($row);
        }

    }

}
