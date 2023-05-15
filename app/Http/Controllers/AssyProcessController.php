<?php

namespace App\Http\Controllers;

use App\AssyAccSchedule;
use App\AssyBodySchedule;
use App\AssyPickingSchedule;
use App\Http\Controllers\Controller;
use App\Material;
use App\OriginGroup;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class AssyProcessController extends Controller
{
    public function indexDisplayAssy($id)
    {
        if ($id == 'assy_sax' || $id == 'welding_sax') {
            $title = 'Saxophone Picking Monitor';
            $title_jp = 'サックスのピッキング監視';

            $keys = db::select("select DISTINCT `key` from materials where issue_storage_location = 'SX51' order by `key` ASC");
            $models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");
            $surfaces = array
                (
                array("", "All"),
                array("LCQ", "Lacquering"),
                array("PLT", "Plating"),
                array("W", "Washed"),
            );

            $hpls = array('All', 'ASKEY', 'TSKEY');

            return view('displays.assys.assy_picking_sax', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'keys' => $keys,
                'models' => $models,
                'surfaces' => $surfaces,
                'hpls' => $hpls,
                'option' => $id,
            ))->with('page', 'Assy Schedule')->with('head', '');

        } elseif ($id == 'assy_cl' || $id == 'welding_cl') {
            $title = 'Clarinet Picking Monitor';
            $title_jp = 'クラリネットピッキング監視';

            return view('displays.assys.assy_picking_cl', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'option' => $id,
            ))->with('page', 'Assy Schedule')->with('head', '');

        } elseif ($id == 'assy_fl' || $id == 'welding_fl') {
            $title = 'Flute Picking Monitor';
            $title_jp = 'フルートのピッキング監視';

            return view('displays.assys.assy_picking_fl', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'option' => $id,
            ))->with('page', 'Assy Schedule')->with('head', '');

        } elseif ($id == 'assy_acc') {
            $title = 'Accessories Picking Monitor';
            $title_jp = 'アクセサリピッキングモニター';

            return view('displays.assys.assy_picking_acc', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'option' => $id,
            ))->with('page', 'Assy Schedule')->with('head', '');

        } elseif ($id == 'sax_body') {
            $title = 'Saxophone Body';
            $title_jp = '??';

            $keys = db::select("select DISTINCT `key` from materials order by `key` ASC");
            $models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");
            $surfaces = array
                (
                array("", "All"),
                array("LCQ", "Lacquering"),
                array("PLT", "Plating"),
                array("W", "Washed"),
            );

            $hpls = array('All', 'ASKEY', 'TSKEY');

            return view('displays.assys.assy_picking_sax', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'keys' => $keys,
                'models' => $models,
                'surfaces' => $surfaces,
                'hpls' => $hpls,
                'option' => $id,
            ))->with('page', 'Assy Schedule')->with('head', '');
        }
    }

    public function indexDisplayBody($id)
    {
        $keys = db::select("select DISTINCT `key` from materials where issue_storage_location = 'SX51' order by `key` ASC");
        $models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");
        $surfaces = array
            (
            array("", "All"),
            array("LCQ", "Lacquering"),
            array("PLT", "Plating"),
            array("W", "Washed"),
        );

        $hpls = array('All', 'ASKEY', 'TSKEY');

        if ($id == 'sax_body') {
            $title = 'Saxophone Body Picking Monitor';
            $title_jp = 'サックスのピッキング監視';

            return view('displays.assys.assy_body_sax', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'keys' => $keys,
                'models' => $models,
                'surfaces' => $surfaces,
                'hpls' => $hpls,
                'option' => $id,
            ))->with('page', 'Assy Body Schedule')->with('head', '');
        }

        if ($id == 'fl_body') {
            $title = 'Flute Body Picking Monitor';
            $title_jp = 'フルートのピッキング監視';

            return view('displays.assys.assy_body_fl', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'option' => $id,
            ))->with('page', 'Assy Body Schedule')->with('head', '');
        }
    }

    public function indexSchedule()
    {
        $title = 'Saxophone Picking Monitor';
        $title_jp = 'サックスのピッキング監視';

        $materials = Material::orderBy('material_number', 'ASC')->get();

        $origin_groups = OriginGroup::orderBy('origin_group_code', 'ASC')->get();

        return view('assy_schedules.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'materials' => $materials,
            'origin_groups' => $origin_groups,
        ))->with('page', 'Assy Picking Schedule');
    }

    public function fetchPicking(Request $request, $id)
    {
        $location = '';
        $mrpc = '';
        $order = "diff desc";
        $putih = '1';

        if ($id == "assy_sax") {
            $location = 'SX51';
            $mrpc = 'S51';
            $putih = '1';
            if ($request->get('order') == "3") {
                $order = "FIELD(CONCAT(model,' ',`key`,' ', surface), 'A480 C-1 N.PLT', 'A480 C-2 N.PLT', 'A480 C-3 N.PLT', 'A480 C-4 N.PLT', 'A280 C-5 N.PLT', 'A480 D-1 N.PLT', 'A480 D-2 N.PLT', 'A280 D-3 N.PLT', 'A480 D-4 N.PLT', 'A480 D-5 N.PLT', 'A26 E-1 N.PLT', 'A280 E-2 N.PLT', 'A280 E-3 N.PLT', 'A280 E-4 N.PLT', 'A280 E-5 N.PLT', 'A280 E-6 N.PLT', 'A280 E-7 N.PLT', 'A280 E-8 N.PLT', 'A26 F-1 N.PLT', 'A26 F-2 N.PLT', 'A26 F-3 N.PLT', 'A26 F-4 N.PLT', 'A480 G-1 N.PLT', 'A480 G-2 N.PLT', 'A280 H-1 N.PLT', 'A280 H-2 N.PLT', 'A480 H-3 N.PLT', 'A280 H-4 N.PLT', 'A280 H-5 N.PLT', 'A280 J-1 N.PLT', 'A280 J-2 N.PLT', 'A280 J-3 N.PLT', 'A280 J-4 N.PLT', 'A26 J-6 N.PLT', 'A480 C-1 S.PLT', 'A480 C-2 S.PLT', 'A480 C-3 S.PLT', 'A480 C-4 S.PLT', 'A480 C-5 S.PLT', 'A480 D-1 S.PLT', 'A480 D-2 S.PLT', 'A480 D-3 S.PLT', 'A480 D-4 S.PLT', 'A480 D-5 S.PLT', 'A280 E-1 S.PLT', 'A480 E-2 S.PLT', 'A280 E-3 S.PLT', 'A480 E-4 S.PLT', 'A280 E-5 S.PLT', 'A480 E-6 S.PLT', 'A480 E-7 S.PLT', 'A480 E-8 S.PLT', 'A280 F-1 S.PLT', 'A280 F-2 S.PLT', 'A280 F-3 S.PLT', 'A280 F-4 S.PLT', 'A480 G-1 S.PLT', 'A480 G-2 S.PLT', 'A280 H-1 S.PLT', 'A280 H-2 S.PLT', 'A480 H-3 S.PLT', 'A280 H-4 S.PLT', 'A280 H-5 S.PLT', 'A480 H-6 S.PLT', 'A480 J-1 S.PLT', 'A480 J-2 S.PLT', 'A480 J-3 S.PLT', 'A480 J-4 S.PLT', 'A280 J-6 S.PLT', 'A480 F-4 S.PLT', 'A480 H-1 S.PLT', 'A480 H-2 S.PLT', 'A480 H-4 S.PLT', 'A480 H-5 S.PLT', 'A480 J-6 S.PLT', 'A62 C-5 S.PLT', 'A62 D-2 S.PLT', 'A62 D-4 S.PLT', 'A62 H-3 S.PLT', 'A62 J-3 S.PLT', 'A62 J-6 S.PLT', 'A480 C-1 G.LCQ', 'A480 C-2 G.LCQ', 'A480 C-3 G.LCQ', 'A480 C-4 G.LCQ', 'A280 C-5 G.LCQ', 'A480 D-1 G.LCQ', 'A480 D-2 G.LCQ', 'A280 D-3 G.LCQ', 'A480 D-4 G.LCQ', 'A480 D-5 G.LCQ', 'A280 E-1 G.LCQ', 'A280 E-2 G.LCQ', 'A280 E-3 G.LCQ', 'A280 E-4 G.LCQ', 'A280 E-5 G.LCQ', 'A280 E-6 G.LCQ', 'A280 E-7 G.LCQ', 'A280 E-8 G.LCQ', 'A280 F-1 G.LCQ', 'A280 F-2 G.LCQ', 'A280 F-3 G.LCQ', 'A26 F-4 G.LCQ', 'A480 G-1 G.LCQ', 'A480 G-2 G.LCQ', 'A280 H-1 G.LCQ', 'A280 H-2 G.LCQ', 'A480 H-3 G.LCQ', 'A280 H-4 G.LCQ', 'A280 H-5 G.LCQ', 'A480 H-6 G.LCQ', 'A280 J-1 G.LCQ', 'A280 J-2 G.LCQ', 'A280 J-3 G.LCQ', 'A280 J-4 G.LCQ', 'A280 J-6 G.LCQ', 'A480 F-4 G.LCQ', 'A480 H-1 G.LCQ', 'A480 H-2 G.LCQ', 'A480 H-4 G.LCQ', 'A480 H-5 G.LCQ', 'A480 J-6 G.LCQ', 'A62 C-5 G.LCQ', 'A62 D-2 G.LCQ', 'A62 D-4 G.LCQ', 'A62 H-3 G.LCQ', 'A62 J-3 G.LCQ', 'A62 J-6 G.LCQ', 'A82Z C-1 G.LCQ', 'A82Z C-2 G.LCQ', 'A82Z C-3 G.LCQ', 'A82Z C-4 G.LCQ', 'A82Z C-5 G.LCQ', 'A82Z D-1 G.LCQ', 'A82Z D-2 G.LCQ', 'A82Z D-3 G.LCQ', 'A82Z D-4 G.LCQ', 'A82Z D-5 G.LCQ', 'A82Z E-1 G.LCQ', 'A82Z E-2 G.LCQ', 'A82Z E-3 G.LCQ', 'A82Z E-4 G.LCQ', 'A82Z E-5 G.LCQ', 'A82Z E-6 G.LCQ', 'A82Z E-7 G.LCQ', 'A82Z E-8 G.LCQ', 'A82Z F-1 G.LCQ', 'A82Z F-2 G.LCQ', 'A82Z F-3 G.LCQ', 'A82Z G-1 G.LCQ', 'A82Z G-2 G.LCQ', 'A82Z H-1 G.LCQ', 'A82Z H-2 G.LCQ', 'A82Z H-3 G.LCQ', 'A82Z H-4 G.LCQ', 'A82Z H-5 G.LCQ', 'A82Z H-6 G.LCQ', 'A82Z J-1 G.LCQ', 'A82Z J-2 G.LCQ', 'A82Z J-3 G.LCQ', 'A82Z J-4 G.LCQ', 'A82Z C-1 S.PLT', 'A82Z C-2 S.PLT', 'A82Z C-3 S.PLT', 'A82Z C-4 S.PLT', 'A82Z C-5 S.PLT', 'A82Z D-1 S.PLT', 'A82Z D-2 S.PLT', 'A82Z D-3 S.PLT', 'A82Z D-4 S.PLT', 'A82Z D-5 S.PLT', 'A82Z E-1 S.PLT', 'A82Z E-2 S.PLT', 'A82Z E-3 S.PLT', 'A82Z E-4 S.PLT', 'A82Z E-5 S.PLT', 'A82Z E-6 S.PLT', 'A82Z E-7 S.PLT', 'A82Z E-8 S.PLT', 'A82Z F-1 S.PLT', 'A82Z F-2 S.PLT', 'A82Z F-3 S.PLT', 'A82Z G-1 S.PLT', 'A82Z G-2 S.PLT', 'A82Z H-1 S.PLT', 'A82Z H-2 S.PLT', 'A82Z H-3 S.PLT', 'A82Z H-4 S.PLT', 'A82Z H-5 S.PLT', 'A82Z H-6 S.PLT', 'A82Z J-1 S.PLT', 'A82Z J-2 S.PLT', 'A82Z J-3 S.PLT', 'A82Z J-4 S.PLT', 'A82Z C-1 W', 'A82Z C-2 W', 'A82Z C-3 W', 'A82Z C-4 W', 'A82Z C-5 W', 'A82Z D-1 W', 'A82Z D-2 W', 'A82Z D-3 W', 'A82Z D-4 W', 'A82Z D-5 W', 'A82Z E-1 W', 'A82Z E-2 W', 'A82Z E-3 W', 'A82Z E-4 W', 'A82Z E-5 W', 'A82Z E-6 W', 'A82Z E-7 W', 'A82Z E-8 W', 'A82Z F-1 W', 'A82Z F-2 W', 'A82Z F-3 W', 'A82Z G-1 W', 'A82Z G-2 W', 'A82Z H-1 W', 'A82Z H-2 W', 'A82Z H-3 W', 'A82Z H-4 W', 'A82Z H-5 W', 'A82Z H-6 W', 'A82Z J-1 W', 'A82Z J-2 W', 'A82Z J-3 W', 'A82Z J-4 W', 'T26 C-1 N.PLT', 'T480 C-2 N.PLT', 'T26 C-3 N.PLT', 'T26 C-4 N.PLT', 'T26 C-5 N.PLT', 'T26 D-1 N.PLT', 'T26 D-2 N.PLT', 'T26 D-3 N.PLT', 'T26 D-4 N.PLT', 'T26 D-5 N.PLT', 'T26 E-1 N.PLT', 'T26 E-2 N.PLT', 'T26 E-3 N.PLT', 'T26 E-4 N.PLT', 'T26 E-5 N.PLT', 'T26 E-6 N.PLT', 'T26 F-1 N.PLT', 'T26 F-2 N.PLT', 'T26 F-3 N.PLT', 'T26 F-4 N.PLT', 'T26 G-1 N.PLT', 'T26 G-2 N.PLT', 'T26 H-1 N.PLT', 'T26 H-2 N.PLT', 'T26 H-3 N.PLT', 'T26 H-4 N.PLT', 'T26 H-5 N.PLT', 'T26 J-1 N.PLT', 'T480 J-2 N.PLT', 'T26 J-3 N.PLT', 'T26 J-4 N.PLT', 'T26 J-6 N.PLT', 'T480 C-1 S.PLT', 'T480 C-2 S.PLT', 'T480 C-3 S.PLT', 'T480 C-4 S.PLT', 'T280 C-5 S.PLT', 'T480 D-1 S.PLT', 'T480 D-2 S.PLT', 'T480 D-3 S.PLT', 'T480 D-4 S.PLT', 'T480 D-5 S.PLT', 'T280 E-1 S.PLT', 'T280 E-2 S.PLT', 'T280 E-3 S.PLT', 'T280 E-4 S.PLT', 'T280 E-5 S.PLT', 'T280 E-6 S.PLT', 'T480 E-7 S.PLT', 'T480 E-8 S.PLT', 'T280 F-1 S.PLT', 'T280 F-2 S.PLT', 'T280 F-3 S.PLT', 'T280 F-4 S.PLT', 'T480 G-1 S.PLT', 'T480 G-2 S.PLT', 'T280 H-1 S.PLT', 'T280 H-2 S.PLT', 'T480 H-3 S.PLT', 'T280 H-4 S.PLT', 'T280 H-5 S.PLT', 'T480 J-1 S.PLT', 'T480 J-2 S.PLT', 'T480 J-3 S.PLT', 'T480 J-4 S.PLT', 'T280 J-6 S.PLT', 'T480 F-4 S.PLT', 'T480 H-1 S.PLT', 'T480 H-2 S.PLT', 'T480 H-4 S.PLT', 'T480 H-5 S.PLT', 'T480 J-6 S.PLT', 'T62 C-5 S.PLT', 'T62 D-2 S.PLT', 'T62 D-4 S.PLT', 'T62 H-3 S.PLT', 'T62 J-3 S.PLT', 'T62 J-6 S.PLT', 'T480 C-1 G.LCQ', 'T480 C-2 G.LCQ', 'T480 C-3 G.LCQ', 'T480 C-4 G.LCQ', 'T480 C-5 G.LCQ', 'T480 D-1 G.LCQ', 'T480 D-2 G.LCQ', 'T480 D-3 G.LCQ', 'T480 D-4 G.LCQ', 'T480 D-5 G.LCQ', 'T280 E-1 G.LCQ', 'T280 E-2 G.LCQ', 'T280 E-3 G.LCQ', 'T280 E-4 G.LCQ', 'T280 E-5 G.LCQ', 'T280 E-6 G.LCQ', 'T480 E-7 G.LCQ', 'T480 E-8 G.LCQ', 'T280 F-1 G.LCQ', 'T280 F-2 G.LCQ', 'T280 F-3 G.LCQ', 'T280 F-4 G.LCQ', 'T480 G-1 G.LCQ', 'T480 G-2 G.LCQ', 'T280 H-1 G.LCQ', 'T280 H-2 G.LCQ', 'T480 H-3 G.LCQ', 'T280 H-4 G.LCQ', 'T280 H-5 G.LCQ', 'T480 J-1 G.LCQ', 'T480 J-2 G.LCQ', 'T480 J-3 G.LCQ', 'T480 J-4 G.LCQ', 'T280 J-6 G.LCQ', 'T480 F-4 G.LCQ', 'T480 H-1 G.LCQ', 'T480 H-2 G.LCQ', 'T480 H-4 G.LCQ', 'T480 H-5 G.LCQ', 'T480 J-6 G.LCQ', 'T62 C-5 G.LCQ', 'T62 D-2 G.LCQ', 'T62 D-4 G.LCQ', 'T62 H-3 G.LCQ', 'T62 J-6 G.LCQ', 'T62 J-3 G.LCQ')";
            }
        } elseif ($id == "assy_cl") {
            $location = 'CL51';
            $mrpc = 'L51';
            $putih = '1';
        } elseif ($id == "assy_fl") {
            $location = 'FL51';
            $mrpc = 'F51';
            $putih = '1';
            $order = "FIELD(CONCAT(`key`,' ', model), 'C-1 FL212 MIGITE', 'C-2 FL212 MIGITE','C-4 FL212 MIGITE','C-3 FL212 MIGITE','C-5 FL212 MIGITE','C-3 FL222 MIGITE','C-5 FL222 MIGITE','C-1 FL282 MIGITE','C-3 FL282 MIGITE','C-4 FL282 MIGITE','C-3 FL272 MIGITE','C-6 FL212 HIDARITE','C-7 FL212 HIDARITE','C-8 FL212 HIDARITE','C-6A FL282 HIDARITE','C-6B FL282 HIDARITE','C-7 FL282 HIDARITE','C-10 FL282 HIDARITE','G-1 FL212 THRILL','G-2 FL212 THRILL','G-3 FL212 THRILL','B-1 FL212 FOOT','B-2 FL212 FOOT','B-3 FL212 FOOT','B-0 FLXXXH FOOT','B-1 FLXXXH FOOT','B-2 FLXXXH FOOT','B-3 FLXXXH FOOT','C-9 FL212 OTHER','F-1 FL212 OTHER','F-2 FL212 OTHER','D-1 FL212 OTHER','D-2 FL212 OTHER','D-3 FL222 OTHER','D-4 FL262S OTHER','D-5 FL272 OTHER','ROLLER FL212 OTHER','ROLLER FL***H OTHER','E-1 FL212 OTHER','E-1 FL282 OTHER')";
        }

        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $where = "";
        $where2 = "";
        $where3 = "";
        $where4 = "";
        $minus = "0";

        if (date('d', strtotime($tanggal)) != "01") {
            $minus = " COALESCE(minus,0) ";
        }

        if ($request->get('key') != "") {
            $keys = explode(",", $request->get('key'));
            $keylength = count($keys);
            $key = "";

            for ($x = 0; $x < $keylength; $x++) {
                $key = $key . "'" . $keys[$x] . "'";
                if ($x != $keylength - 1) {
                    $key = $key . ",";
                }
            }

            $where = " WHERE materials.`key` IN (" . $key . ")";
        }

        if ($request->get('model') != "") {
            if ($where != "") {
                $where2 = "AND ";
            } else {
                $where2 = "WHERE ";
            }

            $models = explode(",", $request->get('model'));
            $modellength = count($models);
            $model = "";

            for ($x = 0; $x < $modellength; $x++) {
                $model = $model . "'" . $models[$x] . "'";
                if ($x != $modellength - 1) {
                    $model = $model . ",";
                }
            }

            $where2 .= " materials.model IN (" . $model . ")";
        }

        if ($request->get('surface') != "") {
            if ($where != "" or $where2 != "") {
                $where3 = "AND ";
            } else {
                $where3 = "WHERE ";
            }
            $surface = str_replace(",", "|", $request->get('surface'));

            $where3 .= " materials.surface REGEXP '" . $surface . "'";
        }

        if ($request->get('hpl') != "" or $request->get('hpl') != "All") {
            if ($where != "" or $where2 != "" or $where3 != "") {
                $where4 = "AND ";
            } else {
                $where4 = "WHERE ";
            }

            $hpl = str_replace(",", "|", $request->get('hpl'));

            $where4 .= " materials.hpl REGEXP '" . $hpl . "'";
        }

        if ($where == "" and $where2 == "" and $where3 == "" and $where4 == "") {
            $dd = "where";
        } else {
            $dd = "and";
        }

        $first = date('Y-m-01', strtotime($tanggal));

        if (substr($tanggal, -2) != "01") {
            $first2 = date('Y-m-01', strtotime($tanggal));
            $minsatu = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
            $minsatu2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        } else {
            $first2 = date('Y-m-02', strtotime($tanggal));
            $minsatu = date('Y-m-d');
            $minsatu2 = date('Y-m-02', strtotime($tanggal));
        }

        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0))) as picking
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0))) as picking
        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0)))) as plan
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0)))) as plan
        // (histories.lot),0)) as minus
        // IF(day(histories.created_at) < 3, 0, histories.lot),0)) as minus

        $table = "select materials.model, materials.`key`, materials.surface , sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
        (
        select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
        (
        select material_number, plan, picking, plus, minus, stock, plan_ori from
        (
        select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) = '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

        union all

        select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location = '" . $location . "' and materials.category = 'key' group by inventories.material_number

        union all

        select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
        (
        select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0)))) as plan, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first2 . "' and date(histories.created_at) <= '" . $minsatu2 . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

        union all

        select assy_picking_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_picking_schedules
        left join materials on materials.material_number = assy_picking_schedules.material_number
        where due_date >= '" . $first . "' and due_date <= '" . $tanggal . "'
        and assy_picking_schedules.remark = '" . $location . "'
        group by assy_picking_schedules.material_number
        ) as plan group by material_number

        union all

        select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, histories.lot),0)) as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first . "' and date(histories.created_at) <= '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
        ) as final group by material_number having plan_ori > 0
        ) as final2
        join materials on final2.material_number = materials.material_number
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . "
        group by materials.model, materials.`key`, materials.surface
        order by " . $order;

        $picking_assy = db::select($table);

        $tabellength = count($picking_assy);
        $gmc = "";

        for ($x = 0; $x < $tabellength; $x++) {
            $gmc = $gmc . "'" . $picking_assy[$x]->key . $picking_assy[$x]->model . $picking_assy[$x]->surface . "'";
            if ($x != $tabellength - 1) {
                $gmc = $gmc . ",";
            }
        }

        $picking2 = "select final2.`key`, final2.model, final2.surface, stockroom, barrel, lacquering, plating, welding from
        (select sum(stockroom) stockroom, sum(barrel) as barrel, sum(lacquering) as lacquering, sum(plating) as plating, sum(welding) welding, `key`, model, surface from
        (select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
        (
        select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location like '" . $location . "' group by kitto.inventories.material_number

        union all

        select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
        group by middle.material_number, materials.key, materials.model, materials.surface


        union all

        select kitto.inventories.material_number, 0 as stockroom,0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
        (
        select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
        (
        select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl LIKE '%KEY%' and materials.key is not null and mrpc in ('" . $mrpc . "')
        ) as parent
        left join bom_components on bom_components.material_parent = parent.material_child
        ) as welding
        left join kitto.inventories on kitto.inventories.material_number = welding.material_child
        group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
        group by `key`, model, surface) as final2
        join materials on materials.`key` = final2.`key` and materials.model = final2.model and materials.surface = final2.surface
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . " " . $dd . " concat(final2.`key`,final2.model,final2.surface) in (" . $gmc . ")
        order by field(concat(final2.`key`,final2.model,final2.surface), " . $gmc . ")";

        $stok = db::select($picking2);

        $bff_q = "SELECT m.model, m.`key`, MAX(s.buffing) AS buffing FROM
        (SELECT b.material_parent, SUM(i.material_qty) AS buffing FROM `buffing_inventories` i
        LEFT JOIN ympimis.bom_components b ON b.material_child = i.material_num
        WHERE i.lokasi = 'STORE'
        GROUP BY b.material_parent) AS s
        LEFT JOIN ympimis.materials m ON m.material_number = s.material_parent
        GROUP BY m.model, m.`key`";

        $buffing = db::connection('digital_kanban')->select($bff_q);

        $dd = [];
        $stat = 0;

        foreach ($stok as $stk) {
            $row = array();
            $row['key'] = $stk->key;
            $row['model'] = $stk->model;
            $row['stockroom'] = $stk->stockroom;
            $row['barrel'] = $stk->barrel;
            $row['lacquering'] = $stk->lacquering;
            $row['plating'] = $stk->plating;
            $row['welding'] = $stk->welding;

            foreach ($buffing as $bf) {
                if ($bf->model == $stk->model && $bf->key == $stk->key) {
                    $stat = 1;
                    $row['buffing'] = $bf->buffing;
                }
            }

            if ($stat == 0) {
                $row['buffing'] = 0;
            }

            $dd[] = $row;
            $stat = 0;
        }

        $response = array(
            'status' => true,
            'update_at' => date('Y-m-d H:i:s'),
            'plan' => $picking_assy,
            'stok' => $dd,
            'gmc' => $gmc,
        );
        return Response::json($response);
    }

    public function fetchPickingAcc(Request $request)
    {
        $location = "('CL51', 'FL51', 'SX51', 'VN51')";
        $mrpc = '';
        $order = "order by FIELD(SUBSTR(materials.model, 1, 3), 'SAX', 'LIG', 'FL_', 'CL_'), diff desc";
        $putih = '1';

        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $where = "";
        $where2 = "";
        $where3 = "";
        $where4 = "";
        $minus = "0";

        if (date('d', strtotime($tanggal)) != "01") {
            $minus = " COALESCE(minus,0) ";
        }

        if ($request->get('key') != "") {
            $keys = explode(",", $request->get('key'));
            $keylength = count($keys);
            $key = "";

            for ($x = 0; $x < $keylength; $x++) {
                $key = $key . "'" . $keys[$x] . "'";
                if ($x != $keylength - 1) {
                    $key = $key . ",";
                }
            }

            $where = " WHERE materials.`key` IN (" . $key . ")";
        }

        if ($request->get('model') != "") {
            if ($where != "") {
                $where2 = "AND ";
            } else {
                $where2 = "WHERE ";
            }

            $models = explode(",", $request->get('model'));
            $modellength = count($models);
            $model = "";

            for ($x = 0; $x < $modellength; $x++) {
                $model = $model . "'" . $models[$x] . "'";
                if ($x != $modellength - 1) {
                    $model = $model . ",";
                }
            }

            $where2 .= " materials.model IN (" . $model . ")";
        }

        if ($request->get('surface') != "") {
            if ($where != "" or $where2 != "") {
                $where3 = "AND ";
            } else {
                $where3 = "WHERE ";
            }
            $surface = str_replace(",", "|", $request->get('surface'));

            $where3 .= " materials.surface REGEXP '" . $surface . "'";
        }

        if ($request->get('hpl') != "" or $request->get('hpl') != "All") {
            if ($where != "" or $where2 != "" or $where3 != "") {
                $where4 = "AND ";
            } else {
                $where4 = "WHERE ";
            }

            $hpl = str_replace(",", "|", $request->get('hpl'));

            $where4 .= " materials.hpl REGEXP '" . $hpl . "'";
        }

        if ($where == "" and $where2 == "" and $where3 == "" and $where4 == "") {
            $dd = "where";
        } else {
            $dd = "and";
        }

        $first = date('Y-m-01', strtotime($tanggal));

        if (substr($tanggal, -2) != "01") {
            $first2 = date('Y-m-01', strtotime($tanggal));
            $minsatu = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
            $minsatu2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        } else {
            $first2 = date('Y-m-02', strtotime($tanggal));
            $minsatu = date('Y-m-d');
            $minsatu2 = date('Y-m-02', strtotime($tanggal));
        }

        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0))) as picking
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0))) as picking
        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0)))) as plan
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0)))) as plan
        // (histories.lot),0)) as minus
        // IF(day(histories.created_at) < 3, 0, histories.lot),0)) as minus

        $table = "select materials.model, materials.`key`, materials.surface , sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
        (
        select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
        (
        select material_number, plan, picking, plus, minus, stock, plan_ori from
        (
        select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location IN " . $location . " and category = 'ACC'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where histories.transfer_receive_location IN ('SX91','CL91','FL91','CLB9') and date(histories.created_at) = '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

        union all

        select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location IN " . $location . " and materials.category = 'ACC' group by inventories.material_number

        union all

        select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
        (
        select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0)))) as plan, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location IN " . $location . " and category = 'ACC'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where histories.transfer_receive_location IN ('SX91','CL91','FL91','CLB9') and date(histories.created_at) >= '" . $first2 . "' and date(histories.created_at) <= '" . $minsatu2 . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

        union all

        select assy_acc_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_acc_schedules
        left join materials on materials.material_number = assy_acc_schedules.material_number
        where due_date >= '" . $first . "' and due_date <= '" . $tanggal . "'
        and assy_acc_schedules.remark IN " . $location . "
        group by assy_acc_schedules.material_number
        ) as plan group by material_number

        union all

        select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, histories.lot),0)) as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location IN " . $location . " and category = 'ACC'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where histories.transfer_receive_location IN ('SX91','CL91','FL91','CLB9') and date(histories.created_at) >= '" . $first . "' and date(histories.created_at) <= '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
        ) as final group by material_number having plan_ori > 0
        ) as final2
        join materials on final2.material_number = materials.material_number
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . "
        group by materials.model, materials.`key`, materials.surface
        " . $order;

        $picking_assy = db::select($table);

        $tabellength = count($picking_assy);
        $gmc = "";

        for ($x = 0; $x < $tabellength; $x++) {
            $gmc = $gmc . "'" . $picking_assy[$x]->key . $picking_assy[$x]->model . $picking_assy[$x]->surface . "'";
            if ($x != $tabellength - 1) {
                $gmc = $gmc . ",";
            }
        }

        $picking2 = "select final2.`key`, final2.model, final2.surface, stockroom, barrel, lacquering, plating, welding from
        (select sum(stockroom) stockroom, sum(barrel) as barrel, sum(lacquering) as lacquering, sum(plating) as plating, sum(welding) welding, `key`, model, surface from
        (select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
        (
        select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location IN " . $location . " group by kitto.inventories.material_number

        union all

        select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
        group by middle.material_number, materials.key, materials.model, materials.surface


        union all

        select kitto.inventories.material_number, 0 as stockroom,0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
        (
        select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
        (
        select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl LIKE '%ACC%' and materials.key is not null
        ) as parent
        left join bom_components on bom_components.material_parent = parent.material_child
        ) as welding
        left join kitto.inventories on kitto.inventories.material_number = welding.material_child
        group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
        group by `key`, model, surface) as final2
        join materials on materials.`key` = final2.`key` and materials.model = final2.model and materials.surface = final2.surface
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . " " . $dd . " concat(final2.`key`,final2.model,final2.surface) in (" . $gmc . ")
        order by field(concat(final2.`key`,final2.model,final2.surface), " . $gmc . ")";

        $stok = db::select($picking2);

        $bff_q = "SELECT m.model, m.`key`, MAX(s.buffing) AS buffing FROM
        (SELECT b.material_parent, SUM(i.material_qty) AS buffing FROM `buffing_inventories` i
        LEFT JOIN ympimis.bom_components b ON b.material_child = i.material_num
        WHERE i.lokasi = 'STORE'
        GROUP BY b.material_parent) AS s
        LEFT JOIN ympimis.materials m ON m.material_number = s.material_parent
        GROUP BY m.model, m.`key`";

        $buffing = db::connection('digital_kanban')->select($bff_q);

        $dd = [];
        $stat = 0;

        foreach ($stok as $stk) {
            $stk_welding = $stk->welding;
            if ($stk->welding == null) {
                $stk_welding = 0;
            }

            $row = array();
            $row['key'] = $stk->key;
            $row['model'] = $stk->model;
            $row['stockroom'] = $stk->stockroom;
            $row['barrel'] = $stk->barrel;
            $row['lacquering'] = $stk->lacquering;
            $row['plating'] = $stk->plating;
            $row['welding'] = $stk_welding;

            foreach ($buffing as $bf) {
                if ($bf->model == $stk->model && $bf->key == $stk->key) {
                    $stat = 1;
                    $row['buffing'] = $bf->buffing;
                }
            }

            if ($stat == 0) {
                $row['buffing'] = 0;
            }

            $dd[] = $row;
            $stat = 0;
        }

        $response = array(
            'status' => true,
            'update_at' => date('Y-m-d H:i:s'),
            'plan' => $picking_assy,
            'stok' => $dd,
            'gmc' => $gmc,
        );
        return Response::json($response);
    }

    public function fetchPickingWelding(Request $request, $id)
    {
        $location = '';
        $mrpc = '';
        $order = 'diff desc';
        if ($id == "welding_sax") {
            $location = 'SX51';
            $mrpc = 'S51';
        } elseif ($id == "welding_cl") {
            $location = 'CL51';
            $mrpc = 'L51';
        } elseif ($id == "welding_fl") {
            $location = 'FL51';
            $mrpc = 'F51';

            $order = "FIELD(CONCAT(`key`,' ', model), 'C-1 FL212 MIGITE', 'C-2 FL212 MIGITE','C-4 FL212 MIGITE','C-3 FL212 MIGITE','C-5 FL212 MIGITE','C-3 FL222 MIGITE','C-5 FL222 MIGITE','C-1 FL282 MIGITE','C-3 FL282 MIGITE','C-4 FL282 MIGITE','C-3 FL272 MIGITE','C-6 FL212 HIDARITE','C-7 FL212 HIDARITE','C-8 FL212 HIDARITE','C-6A FL282 HIDARITE','C-6B FL282 HIDARITE','C-7 FL282 HIDARITE','C-10 FL282 HIDARITE','G-1 FL212 THRILL','G-2 FL212 THRILL','G-3 FL212 THRILL','B-1 FL212 FOOT','B-2 FL212 FOOT','B-3 FL212 FOOT','B-0 FLXXXH FOOT','B-1 FLXXXH FOOT','B-2 FLXXXH FOOT','B-3 FLXXXH FOOT','C-9 FL212 OTHER','F-1 FL212 OTHER','F-2 FL212 OTHER','D-1 FL212 OTHER','D-2 FL212 OTHER','D-3 FL222 OTHER','D-4 FL262S OTHER','D-5 FL272 OTHER','ROLLER FL212 OTHER','ROLLER FL***H OTHER','E-1 FL212 OTHER','E-1 FL282 OTHER')";
        }

        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $where = "";
        $where2 = "";
        $where3 = "";
        $where4 = "";
        $minus = "0";

        if (date('d', strtotime($tanggal)) != "01") {
            $minus = " COALESCE(minus,0) ";
        }

        if ($request->get('key') != "") {
            $keys = explode(",", $request->get('key'));
            $keylength = count($keys);
            $key = "";

            for ($x = 0; $x < $keylength; $x++) {
                $key = $key . "'" . $keys[$x] . "'";
                if ($x != $keylength - 1) {
                    $key = $key . ",";
                }
            }

            $where = " WHERE materials.`key` IN (" . $key . ")";
        }

        if ($request->get('model') != "") {
            if ($where != "") {
                $where2 = "AND ";
            } else {
                $where2 = "WHERE ";
            }

            $models = explode(",", $request->get('model'));
            $modellength = count($models);
            $model = "";

            for ($x = 0; $x < $modellength; $x++) {
                $model = $model . "'" . $models[$x] . "'";
                if ($x != $modellength - 1) {
                    $model = $model . ",";
                }
            }

            $where2 .= " materials.model IN (" . $model . ")";
        }

        if ($request->get('hpl') != "" or $request->get('hpl') != "All") {
            if ($where != "" or $where2 != "" or $where3 != "") {
                $where4 = "AND ";
            } else {
                $where4 = "WHERE ";
            }

            $hpl = str_replace(",", "|", $request->get('hpl'));

            $where4 .= " materials.hpl REGEXP '" . $hpl . "'";
        }

        if ($where == "" and $where2 == "" and $where4 == "") {
            $dd = "where";
        } else {
            $dd = "and";
        }

        $first = date('Y-m-01', strtotime($tanggal));

        $minsatu = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));

        $table = "select materials.model, materials.`key`, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
        (
        select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
        (
        select material_number, plan, picking, plus, minus, stock, plan_ori from
        (
        select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, -(histories.lot)),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) = '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

        union all

        select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location = '" . $location . "' and materials.category = 'key' group by inventories.material_number

        union all

        select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
        (
        select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, -(histories.lot)),0)))) as plan, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first . "' and date(histories.created_at) <= '" . $minsatu . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

        union all

        select assy_picking_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_picking_schedules
        left join materials on materials.material_number = assy_picking_schedules.material_number
        where due_date >= '" . $first . "' and due_date <= '" . $tanggal . "'
        and assy_picking_schedules.remark = '" . $location . "'
        group by assy_picking_schedules.material_number
        ) as plan group by material_number

        union all

        select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 5, 0, histories.lot),0)) as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'key'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first . "' and date(histories.created_at) <= '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
        ) as final group by material_number having plan > 0
        ) as final2
        join materials on final2.material_number = materials.material_number
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . "
        group by materials.model, materials.`key`
        order by " . $order;

        $picking_assy = db::select($table);

        $tabellength = count($picking_assy);
        $gmc = "";

        for ($x = 0; $x < $tabellength; $x++) {
            $gmc = $gmc . "'" . $picking_assy[$x]->key . $picking_assy[$x]->model . "'";
            if ($x != $tabellength - 1) {
                $gmc = $gmc . ",";
            }
        }

        $picking2 = "select final2.`key`, final2.model, max(stockroom) stockroom, max(barrel) barrel, max(lacquering) lacquering, max(plating) plating, max(welding) welding from
        (select sum(stockroom) stockroom, sum(barrel) barrel, sum(lacquering) lacquering, sum(plating) plating, sum(welding) welding, `key`, model from
        (select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model from
        (
        select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location like '" . $location . "' group by kitto.inventories.material_number

        union all

        select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number
        ) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
        group by middle.material_number, materials.key, materials.model


        union all

        select kitto.inventories.material_number, 0 as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model from
        (
        select distinct bom_components.material_child, parent.key, parent.model from
        (
        select bom_components.material_child, materials.key, materials.model from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl LIKE '%KEY%' and materials.key is not null and mrpc in ('" . $mrpc . "')
        ) as parent
        left join bom_components on bom_components.material_parent = parent.material_child
        ) as welding
        left join kitto.inventories on kitto.inventories.material_number = welding.material_child
        group by kitto.inventories.material_number, welding.key, welding.model) as semua
        group by `key`, model) as final2
        join materials on materials.`key` = final2.`key` and materials.model = final2.model
        " . $where . " " . $where2 . " " . $where4 . " " . $dd . " concat(final2.`key`,final2.model) in (" . $gmc . ")
        group by final2.`key`, final2.model
        order by field(concat(final2.`key`,final2.model), " . $gmc . ")";

        $stok = db::select($picking2);

        $bff_q = "SELECT m.model, m.`key`, MAX(s.buffing) AS buffing FROM
        (SELECT b.material_parent, SUM(i.material_qty) AS buffing FROM `buffing_inventories` i
        LEFT JOIN ympimis.bom_components b ON b.material_child = i.material_num
        WHERE i.lokasi = 'STORE'
        GROUP BY b.material_parent) AS s
        LEFT JOIN ympimis.materials m ON m.material_number = s.material_parent
        GROUP BY m.model, m.`key`";

        $buffing = db::connection('digital_kanban')->select($bff_q);

        $dd = [];
        $stat = 0;

        foreach ($stok as $stk) {
            $row = array();
            $row['key'] = $stk->key;
            $row['model'] = $stk->model;
            $row['stockroom'] = $stk->stockroom;
            $row['barrel'] = $stk->barrel;
            $row['lacquering'] = $stk->lacquering;
            $row['plating'] = $stk->plating;
            $row['welding'] = $stk->welding;

            foreach ($buffing as $bf) {
                if ($bf->model == $stk->model && $bf->key == $stk->key) {
                    $stat = 1;
                    $row['buffing'] = $bf->buffing;
                }
            }

            if ($stat == 0) {
                $row['buffing'] = 0;
            }

            $dd[] = $row;
            $stat = 0;
        }

        $response = array(
            'status' => true,
            'plan' => $picking_assy,
            'update_at' => date('Y-m-d H:i:s'),
            // 'stok' => $stok,
            // 'buffing' => $buffing,
            'stok' => $dd,
            'gmc' => $gmc,
        );
        return Response::json($response);

    }

    public function chartPicking(Request $request)
    {
        $where = "";

        if ($request->get('tanggal') == "") {
            $date = date('Y-m-d');
        } else {
            $date = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $first = date('Y-m-01', strtotime($date));

        if ($request->get('key') != "" or $request->get('surface') != "" or $request->get('model') or $request->get('hpl') != "") {
            $where = "WHERE ";
        }

        if ($request->get('key') != "") {
            $keys = explode(",", $request->get('key'));
            $keylength = count($keys);
            $key = "";

            for ($x = 0; $x < $keylength; $x++) {
                $key = $key . "'" . $keys[$x] . "'";
                if ($x != $keylength - 1) {
                    $key = $key . ",";
                }
            }

            $where .= " assy_schedules.`key` IN (" . $key . ")";
        }

        if ($request->get('model') != "") {
            if ($where != "WHERE ") {
                $where .= " AND ";
            }

            $models = explode(",", $request->get('model'));
            $modellength = count($models);
            $model = "";

            for ($x = 0; $x < $modellength; $x++) {
                $model = $model . "'" . $models[$x] . "'";
                if ($x != $modellength - 1) {
                    $model = $model . ",";
                }
            }

            $where .= " assy_schedules.model IN (" . $model . ")";
        }

        if ($request->get('surface') != "") {
            if ($where != "WHERE ") {
                $where .= " AND ";
            }

            if ($request->get('surface') == 'PLT') {
                $where .= " assy_schedules.surface LIKE '%PLT%'";
            } else if ($request->get('surface') == 'LCQ') {
                $where .= " assy_schedules.surface LIKE '%LCQ%'";
            } else {
                $where .= " assy_schedules.surface = 'W'";
            }
        }

        if ($request->get('hpl') != "") {
            if ($where != "WHERE ") {
                $where .= " AND ";
            }

            $where .= " assy_schedules.hpl = '" . $request->get('hpl') . "'";

        }

        $picking = "select assy_schedules.`key`, assy_schedules.model, assy_schedules.surface, stockroom, middle, welding from
        (select sum(stockroom) stockroom, sum(middle) middle, sum(welding) welding, `key`, model, surface from
        (select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.middle) as middle, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
        (
        select kitto.inventories.material_number, sum(lot) as stockroom, 0 as middle, 0 as welding from kitto.inventories where kitto.inventories.issue_location like 'SX51' group by kitto.inventories.material_number

        union all

        select ympimis.middle_inventories.material_number, 0 as stockroom, sum(ympimis.middle_inventories.quantity) as middle, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number
        ) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null group by middle.material_number, materials.key, materials.model, materials.surface

        union all

        select kitto.inventories.material_number, 0 as stockroom, 0 as middle, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
        (
        select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
        (
        select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl LIKE '%KEY%' and materials.key is not null and mrpc in ('S51')
        ) as parent
        left join bom_components on bom_components.material_parent = parent.material_child
        ) as welding
        left join kitto.inventories on kitto.inventories.material_number = welding.material_child
        group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
        group by `key`, model, surface) as final
        right join (select `key`, model, surface, hpl from (select distinct material_number from assy_picking_schedules where due_date BETWEEN '" . $first . "' AND '" . $date . "') asy left join materials on materials.material_number = asy.material_number) assy_schedules on assy_schedules.`key` = final.`key` and assy_schedules.model = final.model and assy_schedules.surface = final.surface
        " . $where . "
        ";

        $picking_assy = db::select($picking);

        $response = array(
            'status' => true,
            'picking' => $picking_assy,
            'order' => $order,
        );
        return Response::json($response);
    }

    public function fetchPickingDetail(Request $request)
    {
        $key = $request->get("key");
        $model = $request->get("model");
        $surface = $request->get("surface");

        $loc = $request->get("location");

        if ($loc == "Welding") {
            $query = "select inventories.barcode_number as tag,inventories.material_number, inventories.description as material_description , inventories.lot as quantity from
            (select distinct ympimis.bom_components.material_child, parent.key, parent.model, parent.surface from
            (select bom_components.material_child, materials.key, materials.model, materials.surface from ympimis.materials left join ympimis.bom_components on bom_components.material_parent = ympimis.materials.material_number where materials.hpl LIKE '%KEY%' and materials.key is not null and mrpc in ('S51')
            ) as parent
            left join ympimis.bom_components on ympimis.bom_components.material_parent = parent.material_child
            where parent.key = '" . $key . "' AND parent.model = '" . $model . "' AND parent.surface = '" . $surface . "'
            ) as welding
            left join inventories on inventories.material_number = welding.material_child ";

        } else if ($loc == "Middle") {
            $query = "select stok.tag ,stok.material_number, ympimis.materials.material_description, stok.quantity from
            (select tag, ympimis.middle_inventories.material_number, ympimis.middle_inventories.quantity from ympimis.middle_inventories) stok
            left join ympimis.materials on ympimis.materials.material_number = stok.material_number
            where ympimis.materials.key = '" . $key . "' AND ympimis.materials.model = '" . $model . "' AND ympimis.materials.surface = '" . $surface . "'";

        } else if ($loc == "Stockroom") {
            $query = "select tag, stok.material_number, ympimis.materials.material_description, stok.quantity from
            (select inventories.barcode_number as tag, inventories.material_number, lot as quantity from inventories where inventories.issue_location like 'SX51') stok
            left join ympimis.materials on ympimis.materials.material_number = stok.material_number
            where ympimis.materials.key = '" . $key . "' AND ympimis.materials.model = '" . $model . "' AND ympimis.materials.surface = '" . $surface . "'";
        }

        $detailData = db::connection('mysql2')->select($query);

        return DataTables::of($detailData)->make(true);
    }

    public function fetchSchedule(Request $request)
    {

        if (strlen($request->get('item_category')) <= 0 || strlen($request->get('mon')) <= 0) {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $item = $request->get('item_category');
        if (str_contains($item, 'Key')) {
            $assy_schedules = AssyPickingSchedule::where(db::raw('DATE_FORMAT(assy_picking_schedules.due_date,"%Y-%m")'), '=', $request->get('mon'))
                ->where('remark', '=', explode(' ', $item)[0])
                ->leftJoin("materials", "materials.material_number", "=", "assy_picking_schedules.material_number")
                ->select('assy_picking_schedules.id', 'assy_picking_schedules.material_number', 'assy_picking_schedules.due_date', 'assy_picking_schedules.quantity', 'materials.material_description', 'assy_picking_schedules.remark', 'assy_picking_schedules.created_at')
                ->orderByRaw('due_date DESC', 'assy_picking_schedules.material_number ASC')
                ->get();
        } elseif ($item == 'ACC') {
            $assy_schedules = AssyAccSchedule::where(db::raw('DATE_FORMAT(assy_acc_schedules.due_date,"%Y-%m")'), '=', $request->get('mon'))
                ->leftJoin("materials", "materials.material_number", "=", "assy_acc_schedules.material_number")
                ->select('assy_acc_schedules.id', 'assy_acc_schedules.material_number', 'assy_acc_schedules.due_date', 'assy_acc_schedules.quantity', 'materials.material_description', 'assy_acc_schedules.remark', 'assy_acc_schedules.created_at')
                ->orderByRaw('due_date DESC', 'assy_acc_schedules.material_number ASC')
                ->get();

        } else {
            $assy_schedules = AssyBodySchedule::where(db::raw('DATE_FORMAT(assy_body_schedules.due_date,"%Y-%m")'), '=', $request->get('mon'))
                ->where('remark', '=', explode(' ', $item)[0])
                ->leftJoin("materials", "materials.material_number", "=", "assy_body_schedules.material_number")
                ->select('assy_body_schedules.id', 'assy_body_schedules.material_number', 'assy_body_schedules.due_date', 'assy_body_schedules.quantity', 'materials.material_description', 'assy_body_schedules.remark', 'assy_body_schedules.created_at')
                ->orderByRaw('due_date DESC', 'assy_body_schedules.material_number ASC')
                ->get();
        }

        $response = array(
            'status' => true,
            'picking' => $assy_schedules,
        );
        return Response::json($response);
    }

    public function import(Request $request)
    {
        try {
            if ($request->hasFile('assy_schedule')) {

                $id = Auth::id();

                $file = $request->file('assy_schedule');
                $data = file_get_contents($file);

                $rows = explode("\r\n", $data);

                $date = date('Y-m', strtotime(str_replace('/', '-', explode("\t", $rows[0])[1])));

                $category = $request->get('item_imp');
                if ($category == 'ACC') {
                    // JIKA KUNCI
                    $delete_assy = AssyAccSchedule::where(db::raw('date_format(assy_acc_schedules.due_date,"%Y-%m")'), '=', $request->get('mon_imp'))
                        ->whereNull('assy_acc_schedules.note')
                        ->forceDelete();

                    foreach ($rows as $row) {
                        if (strlen($row) > 0) {
                            $row = explode("\t", $row);
                            $material = Material::where('material_number', $row[0])->first();

                            $assy_schedule = new AssyAccSchedule([
                                'remark' => $material->issue_storage_location,
                                'material_number' => $row[0],
                                'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                                'quantity' => $row[2],
                                'created_by' => $id,
                            ]);
                            $assy_schedule->save();
                        }
                    }

                } else {
                    // JIKA KUNCI
                    if (explode(' ', $category)[1] == 'Key') {
                        $delete_assy = AssyPickingSchedule::where(db::raw('date_format(assy_picking_schedules.due_date,"%Y-%m")'), '=', $request->get('mon_imp'))
                            ->whereNull('assy_picking_schedules.note')
                            ->where('remark', '=', explode(' ', $category)[0])
                            ->forceDelete();

                        foreach ($rows as $row) {
                            if (strlen($row) > 0) {
                                $row = explode("\t", $row);
                                $assy_schedule = new AssyPickingSchedule([
                                    'remark' => explode(' ', $category)[0],
                                    'material_number' => $row[0],
                                    'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                                    'quantity' => $row[2],
                                    'created_by' => $id,
                                ]);

                                $assy_schedule->save();
                            }
                        }
                    } else {
                        //JIKA BODY
                        $delete_assy = AssyBodySchedule::where(db::raw('date_format(assy_body_schedules.due_date,"%Y-%m")'), '=', $request->get('mon_imp'))
                            ->whereNull('assy_body_schedules.note')
                            ->where('remark', '=', explode(' ', $category)[0])
                            ->forceDelete();

                        foreach ($rows as $row) {
                            if (strlen($row) > 0) {
                                $row = explode("\t", $row);
                                $assy_schedule = new AssyBodySchedule([
                                    'remark' => explode(' ', $category)[0],
                                    'material_number' => $row[0],
                                    'due_date' => date('Y-m-d', strtotime(str_replace('/', '-', $row[1]))),
                                    'quantity' => $row[2],
                                    'created_by' => $id,
                                ]);

                                $assy_schedule->save();
                            }
                        }
                    }
                }

                return redirect('/index/assy_schedule')->with('status', 'New assy schedule has been imported.')->with('page', 'Assy Schedule');
            } else {
                return redirect('/index/assy_schedule')->with('error', 'Please select a file.')->with('page', 'Assy Schedule');
            }
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                return back()->with('error', 'Assy schedule with preferred due date already exist.')->with('page', 'Assy Schedule');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'Assy Schedule');
            }

        }
    }

    public function createSchedule(Request $request)
    {
        $due_date = date('Y-m-d', strtotime(str_replace('/', '-', $request->get('due_date'))));

        try
        {
            $id = Auth::id();
            $assy_schedule = new AssyPickingSchedule([
                'material_number' => $request->get('material_number'),
                'due_date' => $due_date,
                'quantity' => $request->get('quantity'),
                'created_by' => $id,
            ]);

            $assy_schedule->save();

            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $error_code = $e->errorInfo[1];
            if ($error_code == 1062) {
                $response = array(
                    'status' => false,
                    'Message' => 'already exist',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                );
                return Response::json($response);
            }
        }
    }

    public function delete(Request $request)
    {
        $assy_schedule = AssyPickingSchedule::where('id', '=', $request->get("id"))
            ->forceDelete();

        $response = array(
            'status' => true,
        );

        return Response::json($response);
    }

    public function edit(Request $request)
    {
        $head = AssyPickingSchedule::where('id', '=', $request->get('id'))
            ->first();

        $head->quantity = $request->get('quantity');
        $head->save();

        $response = array(
            'status' => true,
        );

        return Response::json($response);
    }

    public function fetchEdit(Request $request)
    {
        $assy_schedule = AssyPickingSchedule::where('id', '=', $request->get("id"))
            ->first();

        $response = array(
            'status' => true,
            'datas' => $assy_schedule,
        );

        return Response::json($response);
    }

    public function destroy(Request $request)
    {
        $date_from = date('Y-m-d', strtotime($request->get('datefrom')));
        $date_to = date('Y-m-d', strtotime($request->get('dateto')));

        $materials = Material::whereIn('origin_group_code', $request->get('origin_group'))->select('material_number')->get();

        $AssyPickingSchedule = AssyPickingSchedule::where('due_date', '>=', $date_from)
            ->where('due_date', '<=', $date_to)
            ->whereIn('material_number', $materials)
            ->forceDelete();

        return redirect('/index/assy_schedule')
            ->with('status', 'Assy schedules has been deleted.')
            ->with('page', 'Assy Picking Schedule');
    }

    public function view(Request $request)
    {
        $query = "select assy.material_number, assy.due_date, assy.quantity, users.`name`, material_description, origin_group_name, assy.created_at, assy.updated_at from
        (select material_number, due_date, quantity, created_by, created_at, updated_at from assy_picking_schedules where id = " . $request->get('id') . ") as assy
        left join materials on materials.material_number = assy.material_number
        left join origin_groups on origin_groups.origin_group_code = materials.origin_group_code
        left join users on assy.created_by = users.id";

        $assy_schedule = DB::select($query);

        $response = array(
            'status' => true,
            'datas' => $assy_schedule,
        );

        return Response::json($response);

    }

    public function fetchPickingBody(Request $request, $id)
    {
        $location = '';
        $order = 'diff desc';
        $putih = '1';
        if ($id == "sax_body") {
            $putih = '1';
            $location = 'SX51';
            $order = "FIELD(CONCAT(model,' ',`key`,' ', surface), 'YDS BELL G.LCQ', 'A280 BODY G.LCQ', 'A280 BELLBOW G.LCQ', 'A26 BODY G.LCQ', 'A275 NECK G.LCQ', 'A200AD BODY G.LCQ', 'A200AD BELLBOW G.LCQ', 'A480 BODY G.LCQ', 'A480 BELLBOW G.LCQ', 'A480 NECK G.LCQ', 'A580 BODY G.LCQ', 'A580 BELLBOW G.LCQ', 'A300AD BODY G.LCQ', 'A300AD BELLBOW G.LCQ', 'APLU1II BODY G.LCQ', 'APLU1II BELLBOW G.LCQ', 'A380 BODY G.LCQ', 'A380 BELLBOW G.LCQ', 'AVDHM BODY G.LCQ', 'AVDHM BELLBOW G.LCQ', 'A280 BODY S.PLT', 'A280 BELLBOW S.PLT', 'A26 BODY S.PLT', 'A275 NECK S.PLT', 'A200AD BODY S.PLT', 'A200AD BELLBOW S.PLT', 'A480 BODY S.PLT', 'A480 BELLBOW S.PLT', 'A480 NECK S.PLT', 'T280 BODY G.LCQ', 'T26 BODY C.LCQ', 'T275 NECK G.LCQ', 'T275 BELLBOW G.LCQ', 'T200ADII BODY G.LCQ', 'T200AD BELLBOW G.LCQ', 'TPLU1II BELLBOW G.LCQ', 'TPLU1II BODY G.LCQ', 'T480 BODY G.LCQ', 'T480 BELLBOW G.LCQ', 'T480 NECK G.LCQ', 'T580 BODY G.LCQ', 'T580 BELLBOW G.LCQ', 'T300AD BODY G.LCQ', 'T300AD BELLBOW G.LCQ', 'T380 BODY G.LCQ', 'T380 BELLBOW G.LCQ', 'T280 BODY S.PLT', 'T26 BODY S.PLT', 'T26 BELLBOW S.PLT', 'T275 NECK S.PLT', 'T480 BODY S.PLT', 'T480 BELLBOW S.PLT', 'T480 NECK S.PLT')";
        } elseif ($id == "cl_body") {
            $putih = '1';
            $location = 'CL51';
        } elseif ($id == "fl_body") {
            $putih = '1';
            $location = 'FL51';
        }

        if ($request->get('tanggal') == "") {
            $tanggal = date('Y-m-d');
        } else {
            $tanggal = date('Y-m-d', strtotime($request->get('tanggal')));
        }

        $where = "";
        $where2 = "";
        $where3 = "";
        $where4 = "";
        $minus = "0";

        if (date('d', strtotime($tanggal)) != "01") {
            $minus = " COALESCE(minus,0) ";
        }

        if ($request->get('key') != "") {
            $keys = explode(",", $request->get('key'));
            $keylength = count($keys);
            $key = "";

            for ($x = 0; $x < $keylength; $x++) {
                $key = $key . "'" . $keys[$x] . "'";
                if ($x != $keylength - 1) {
                    $key = $key . ",";
                }
            }

            $where = " WHERE materials.`key` IN (" . $key . ")";
        }

        if ($request->get('model') != "") {
            if ($where != "") {
                $where2 = "AND ";
            } else {
                $where2 = "WHERE ";
            }

            $models = explode(",", $request->get('model'));
            $modellength = count($models);
            $model = "";

            for ($x = 0; $x < $modellength; $x++) {
                $model = $model . "'" . $models[$x] . "'";
                if ($x != $modellength - 1) {
                    $model = $model . ",";
                }
            }

            $where2 .= " materials.model IN (" . $model . ")";
        }

        if ($request->get('surface') != "") {
            if ($where != "" or $where2 != "") {
                $where3 = "AND ";
            } else {
                $where3 = "WHERE ";
            }
            $surface = str_replace(",", "|", $request->get('surface'));

            $where3 .= " materials.surface REGEXP '" . $surface . "'";
        }

        if ($request->get('hpl') != "" or $request->get('hpl') != "All") {
            if ($where != "" or $where2 != "" or $where3 != "") {
                $where4 = "AND ";
            } else {
                $where4 = "WHERE ";
            }

            $hpl = str_replace(",", "|", $request->get('hpl'));

            $where4 .= " materials.hpl REGEXP '" . $hpl . "'";
        }

        if ($where == "" and $where2 == "" and $where3 == "" and $where4 == "") {
            $dd = "where";
        } else {
            $dd = "and";
        }

        $first = date('Y-m-01', strtotime($tanggal));

        if (substr($tanggal, -2) != "01") {
            $first2 = date('Y-m-01', strtotime($tanggal));
            $minsatu = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
            $minsatu2 = date('Y-m-d', strtotime('-1 day', strtotime($tanggal)));
        } else {
            $first2 = date('Y-m-02', strtotime($tanggal));
            $minsatu = date('Y-m-d');
            $minsatu2 = date('Y-m-02', strtotime($tanggal));
        }

        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0))) as picking
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0))) as picking
        // if(histories.transfer_movement_type = '9I4', -(histories.lot),0)))) as plan
        // if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < 3, 0, -(histories.lot)),0)))) as plan
        // (histories.lot),0)) as minus
        // IF(day(histories.created_at) < 3, 0, histories.lot),0)) as minus

        $table = "
        select materials.model, materials.`key`, materials.surface , sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori, (sum(plan)-sum(picking)) as diff, sum(stock) - (sum(plan)-sum(picking)) as diff2, round(sum(stock) / sum(plan), 1) as ava from
        (
        select material_number, sum(plan) as plan, sum(picking) as picking, sum(plus) as plus, sum(minus) as minus, sum(stock) as stock, sum(plan_ori) as plan_ori from
        (
        select material_number, plan, picking, plus, minus, stock, plan_ori from
        (
        select materials.material_number, 0 as plan, sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0))) as picking, 0 as plus, 0 as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'body'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) = '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number ) as pick

        union all

        select inventories.material_number, 0 as plan, 0 as picking, 0 as plus, 0 as minus, sum(inventories.lot) as stock, 0 as plan_ori from kitto.inventories left join kitto.materials on materials.material_number = inventories.material_number where materials.location = '" . $location . "' and materials.category = 'body' group by inventories.material_number

        union all

        select material_number, sum(plan) as plan, 0 as picking ,0 as plus, 0 as minus, 0 as stock, sum(plan_ori) as plan_ori from
        (
        select materials.material_number, -(sum(if(histories.transfer_movement_type = '9I3', histories.lot, if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, -(histories.lot)),0)))) as plan, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'body'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first2 . "' and date(histories.created_at) <= '" . $minsatu2 . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number

        union all

        select assy_body_schedules.material_number, sum(quantity) as plan, sum(quantity) as plan_ori from assy_body_schedules
        left join materials on materials.material_number = assy_body_schedules.material_number
        where due_date >= '" . $first . "' and due_date <= '" . $tanggal . "'
        and assy_body_schedules.remark = '" . $location . "'
        group by assy_body_schedules.material_number
        ) as plan group by material_number

        union all

        select materials.material_number, 0 as plan, 0 as picking, sum(if(histories.transfer_movement_type = '9I3', histories.lot,0)) as plus, sum( if(histories.transfer_movement_type = '9I4', IF(day(histories.created_at) < " . $putih . ", 0, histories.lot),0)) as minus, 0 as stock, 0 as plan_ori from
        (
        select materials.id, materials.material_number from kitto.materials where materials.location = '" . $location . "' and category = 'body'
        ) as materials left join kitto.histories on materials.id = histories.transfer_material_id where date(histories.created_at) >= '" . $first . "' and date(histories.created_at) <= '" . $tanggal . "' and histories.category in ('transfer', 'transfer_cancel', 'transfer_return', 'transfer_adjustment') group by materials.material_number
        ) as final group by material_number having plan_ori > 0
        ) as final2
        join materials on final2.material_number = materials.material_number
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . "
        group by materials.model, materials.`key`, materials.surface
        order by " . $order;

        try {
            $picking_assy = db::select($table);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $tabellength = count($picking_assy);
        $gmc = "";

        for ($x = 0; $x < $tabellength; $x++) {
            $gmc = $gmc . "'" . $picking_assy[$x]->key . $picking_assy[$x]->model . $picking_assy[$x]->surface . "'";
            if ($x != $tabellength - 1) {
                $gmc = $gmc . ",";
            }
        }

        $picking2 = "select final2.`key`, final2.model, final2.surface, stockroom, barrel, lacquering, plating, welding from
        (select sum(stockroom) stockroom, sum(barrel) as barrel, sum(lacquering) as lacquering, sum(plating) as plating, sum(welding) welding, `key`, model, surface from
        (select middle.material_number, sum(middle.stockroom) as stockroom, sum(middle.barrel) as barrel, sum(middle.lacquering) as lacquering, sum(middle.plating) as plating, sum(middle.welding) as welding, materials.key, materials.model, materials.surface from
        (
        select kitto.inventories.material_number, sum(lot) as stockroom, 0 as barrel, 0 as lacquering, 0 as plating, 0 as welding from kitto.inventories where kitto.inventories.issue_location like '" . $location . "' group by kitto.inventories.material_number

        union all

        select ympimis.middle_inventories.material_number, 0 as stockroom, sum(if(location = 'barrel',ympimis.middle_inventories.quantity, 0)) as barrel, sum(if(location LIKE 'lcq%',ympimis.middle_inventories.quantity, 0)) as lacquering, sum(if(location LIKE 'plt%',ympimis.middle_inventories.quantity, 0)) as plating, 0 as welding from ympimis.middle_inventories group by ympimis.middle_inventories.material_number) as middle left join materials on materials.material_number = middle.material_number where materials.key is not null
        group by middle.material_number, materials.key, materials.model, materials.surface


        union all

        select kitto.inventories.material_number, 0 as stockroom,0 as barrel, 0 as lacquering, 0 as plating, sum(kitto.inventories.lot) as welding, welding.key, welding.model, welding.surface from
        (
        select distinct bom_components.material_child, parent.key, parent.model, parent.surface from
        (
        select bom_components.material_child, materials.key, materials.model, materials.surface from materials left join bom_components on bom_components.material_parent = materials.material_number where materials.hpl in ('ASKEY', 'TSKEY') and materials.key is not null and mrpc in ('S51')
        ) as parent
        left join bom_components on bom_components.material_parent = parent.material_child
        ) as welding
        left join kitto.inventories on kitto.inventories.material_number = welding.material_child
        group by kitto.inventories.material_number, welding.key, welding.model, welding.surface) as semua
        group by `key`, model, surface) as final2
        join materials on materials.`key` = final2.`key` and materials.model = final2.model and materials.surface = final2.surface
        " . $where . " " . $where2 . " " . $where3 . " " . $where4 . " " . $dd . " concat(final2.`key`,final2.model,final2.surface) in (" . $gmc . ")
        group by `key`, model, surface, stockroom, barrel, lacquering, plating, welding
        order by field(concat(final2.`key`,final2.model,final2.surface), " . $gmc . ")";

        $stok = db::select($picking2);

        $bff_q = "SELECT m.model, m.`key`, MAX(s.buffing) AS buffing FROM
        (SELECT b.material_parent, SUM(i.material_qty) AS buffing FROM `buffing_inventories` i
        LEFT JOIN ympimis.bom_components b ON b.material_child = i.material_num
        WHERE i.lokasi = 'STORE'
        GROUP BY b.material_parent) AS s
        LEFT JOIN ympimis.materials m ON m.material_number = s.material_parent
        GROUP BY m.model, m.`key`";

        $buffing = db::connection('digital_kanban')->select($bff_q);

        $dd = [];
        $stat = 0;

        foreach ($stok as $stk) {
            $row = array();
            $row['key'] = $stk->key;
            $row['model'] = $stk->model;
            $row['stockroom'] = $stk->stockroom;
            $row['barrel'] = $stk->barrel;
            $row['lacquering'] = $stk->lacquering;
            $row['plating'] = $stk->plating;
            $row['welding'] = $stk->welding;

            foreach ($buffing as $bf) {
                if ($bf->model == $stk->model && $bf->key == $stk->key) {
                    $stat = 1;
                    $row['buffing'] = $bf->buffing;
                }
            }

            if ($stat == 0) {
                $row['buffing'] = 0;
            }

            $dd[] = $row;
            $stat = 0;
        }

        $response = array(
            'status' => true,
            'update_at' => date('Y-m-d H:i:s'),
            'plan' => $picking_assy,
            'stok' => $dd,
            'gmc' => $gmc,
        );
        return Response::json($response);
    }

    // -------------------------- GRAFIK -----------------------

    public function indexPickingBody($id)
    {
        $keys = db::select("select DISTINCT `key` from materials where issue_storage_location = 'SX51' order by `key` ASC");
        $models = db::select("select DISTINCT model from materials where mrpc='S51' order by model ASC");
        $surfaces = array
            (
            array("", "All"),
            array("LCQ", "Lacquering"),
            array("PLT", "Plating"),
            array("W", "Washed"),
        );

        $hpls = array('All', 'ASKEY', 'TSKEY');

        if ($id == 'sax_body') {
            $title = 'Saxophone Body Picking Monitor';
            $title_jp = 'サックスのピッキング監視';

            return view('displays.assys.body_sax', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'keys' => $keys,
                'models' => $models,
                'surfaces' => $surfaces,
                'hpls' => $hpls,
                'option' => $id,
            ))->with('page', 'Assy Body Schedule')->with('head', '');
        }

        if ($id == 'fl_body') {
            $title = 'Flute Body Picking Monitor';
            $title_jp = 'フルートのピッキング監視';

            return view('displays.assys.body_fl', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'option' => $id,
            ))->with('page', 'Assy Body Schedule')->with('head', '');
        }
    }
}
