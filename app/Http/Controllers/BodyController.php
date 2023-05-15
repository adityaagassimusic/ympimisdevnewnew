<?php

namespace App\Http\Controllers;

use App\BodyDetail;
use App\BodyInventory;
use App\BodyNgLog;
use App\BodyNgTemp;
use App\BodyTag;
use App\Employee;
use App\Http\Controllers\Controller;
use App\Material;
use App\NgList;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class BodyController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }

        $this->after_completions = [
            'lcq-incoming-body-sx',
            'plt-incoming-body-sx',
            'plt-incoming-body-fl',
            'plt-body-sx',
            'lcq-body-sx',
            'stockroom-body-sx',
        ];

        $this->completions = [
            'lcq-incoming-body-sx',
            'plt-incoming-body-sx',
            'plt-incoming-body-fl',
        ];

        $this->location = [
            'lcq-incoming-body-sx',
            'lcq-kensa-body-sx',
            'plt-incoming-body-fl',
            'plt-incoming-body-sx',
            'plt-kensa-body-fl',
        ];
    }
    public function indexBoard($location)
    {
        if ($location == 'stamp') {
            $title = 'HTS Stamp Board';
            $title_jp = '';
            $loc = 'hts-stamp';

            $view = 'processes.welding.body.display.board';
        } else if ($location == 'bff-sax-1') {
            $title = 'Buffing Sax Body Board';
            $title_jp = '';
            $loc = 'bff-sax-1';

            $view = 'processes.middle.body.display.board';
        } else if ($location == 'bff-sax-2') {
            $title = 'Buffing Sax Body Board';
            $title_jp = '';
            $loc = 'bff-sax-2';

            $view = 'processes.middle.body.display.board';
        } else if ($location == 'hts-sax-1') {
            $title = 'Handatsuke Sax Body Board 1';
            $title_jp = '';
            $loc = 'hts-sax-1';

            $view = 'processes.middle.body.display.board';
        } else if ($location == 'hts-sax-2') {
            $title = 'Handatsuke Sax Body Board 2';
            $title_jp = '';
            $loc = 'hts-sax-2';

            $view = 'processes.middle.body.display.board';
        }
        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'loc' => $loc,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function fetchBoard(Request $request)
    {
        try {
            $boards = [];
            if ($request->get('loc') == 'hts-stamp') {
                $model = DB::SELECT("SELECT DISTINCT
                    ( model )
                FROM
                    `materials`
                WHERE
                    `issue_storage_location` LIKE '%SX51%'
                    AND `material_description` LIKE '%Body%'
                    AND model IS NOT NULL
                ORDER BY
                    hpl,
                    model");
                $list_antrian = [];
                // for($i = 0; $i < count($model);$i++){
                $queue = DB::SELECT("SELECT
                            welding_body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.key,
                            materials.model,
                            materials.surface,
                            welding_body_queues.created_at
                        FROM
                            welding_body_queues
                            LEFT JOIN materials ON materials.material_number = welding_body_queues.material_number
                        WHERE
                            ( welding_body_queues.location = 'hts-stamp' AND materials.KEY LIKE '%BELL%' )
                            OR ( welding_body_queues.location = 'hts-stamp' AND materials.KEY LIKE '%BODY%' )
                            ORDER BY
                            welding_body_queues.created_at");

                // if (ISSET($queue[$i])) {
                //     array_push($list_antrian,$queue[$i]->surface.' - '.$queue[$i]->model.'<br>'.$queue[$i]->key);
                // }else{
                //     array_push($list_antrian, '<br>');
                // }
                // }

                for ($j = 0; $j < 11; $j++) {
                    array_push($boards, [
                        'index' => $j,
                        'model' => $queue[$j]->model,
                        'queue' => $queue[$j]->surface . ' - ' . $queue[$j]->model . '<br>' . $queue[$j]->key,
                    ]);
                }

                // $list_antrian = [];
                // $queue = DB::SELECT("SELECT
                //         welding_body_queues.material_number,
                //         materials.material_description,
                //         materials.hpl,
                //         materials.surface,
                //         materials.model,
                //         welding_body_queues.created_at
                //     FROM
                //         welding_body_queues
                //         LEFT JOIN materials ON materials.material_number = welding_body_queues.material_number
                //     WHERE
                //         welding_body_queues.location = 'hts-stamp'
                //         AND materials.key like '%BELL%'
                //         AND materials.model like '%A%'
                //         ORDER BY
                //         welding_body_queues.created_at");

                // for($j = 0; $j < 11; $j++){
                //     if (ISSET($queue[$j])) {
                //         array_push($list_antrian, $queue[$j]->surface.' - '.$queue[$j]->model.'<br>AS BELLBOW');
                //     }else{
                //         array_push($list_antrian, '<br>');
                //     }
                // }
                // array_push($boards, [
                //     'model' => 'AS BELL BOW',
                //     'queue_1' => $list_antrian[0],
                //     'queue_2' => $list_antrian[1],
                //     'queue_3' => $list_antrian[2],
                //     'queue_4' => $list_antrian[3],
                //     'queue_5' => $list_antrian[4],
                //     'queue_6' => $list_antrian[5],
                //     'queue_7' => $list_antrian[6],
                //     'queue_8' => $list_antrian[7],
                //     'queue_9' => $list_antrian[8],
                //     'queue_10' => $list_antrian[9],
                //     'queue_11' => $list_antrian[10],
                //     'jumlah_urutan' => count($queue)
                // ]);

                // $list_antrian = [];
                // $queue = DB::SELECT("SELECT
                //         welding_body_queues.material_number,
                //         materials.material_description,
                //         materials.hpl,
                //         materials.surface,
                //         materials.model,
                //         welding_body_queues.created_at
                //     FROM
                //         welding_body_queues
                //         LEFT JOIN materials ON materials.material_number = welding_body_queues.material_number
                //     WHERE
                //         welding_body_queues.location = 'hts-stamp'
                //         AND materials.key like '%BELL%'
                //         AND materials.model like '%T%'
                //         ORDER BY
                //         welding_body_queues.created_at");

                // for($j = 0; $j < 11; $j++){
                //     if (ISSET($queue[$j])) {
                //         array_push($list_antrian, $queue[$j]->surface.' - '.$queue[$j]->surface.' - '.$queue[$j]->model.'<br>TS BELLBOW');
                //     }else{
                //         array_push($list_antrian, '<br>');
                //     }
                // }
                // array_push($boards, [
                //     'model' => 'TS BELL BOW',
                //     'queue_1' => $list_antrian[0],
                //     'queue_2' => $list_antrian[1],
                //     'queue_3' => $list_antrian[2],
                //     'queue_4' => $list_antrian[3],
                //     'queue_5' => $list_antrian[4],
                //     'queue_6' => $list_antrian[5],
                //     'queue_7' => $list_antrian[6],
                //     'queue_8' => $list_antrian[7],
                //     'queue_9' => $list_antrian[8],
                //     'queue_10' => $list_antrian[9],
                //     'queue_11' => $list_antrian[10],
                //     'jumlah_urutan' => count($queue)
                // ]);
            } else if ($request->get('loc') == 'bff-sax-1') {
                $work_stations = DB::SELECT("SELECT
                        *
                    FROM
                        `body_dev_lists`
                        LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                    WHERE
                        location LIKE '%bff%'
                    LIMIT 14");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            middle_body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model,
                            materials.surface
                        FROM
                            middle_body_queues
                            LEFT JOIN materials ON materials.material_number = middle_body_queues.material_number
                        WHERE
                            middle_body_queues.location = 'bff' and
                            middle_body_queues.work_station = '" . $ws->location . "'
                            ORDER BY
                            middle_body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->surface . ' - ' . $queue[$j]->model . '<br>' . $queue[$j]->hpl);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
            } else if ($request->get('loc') == 'bff-sax-2') {
                $work_stations = DB::SELECT("SELECT
                        *
                    FROM
                        `body_dev_lists`
                        LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                    WHERE
                        location LIKE '%bff%'
                    ORDER BY
                        body_dev_lists.id DESC
                        LIMIT 14");

                krsort($work_stations);

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            middle_body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.surface,
                            materials.model
                        FROM
                            middle_body_queues
                            LEFT JOIN materials ON materials.material_number = middle_body_queues.material_number
                        WHERE
                            middle_body_queues.location = 'bff' and
                            middle_body_queues.work_station = '" . $ws->location . "'
                            ORDER BY
                            middle_body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->surface . ' - ' . $queue[$j]->model . '<br>' . $queue[$j]->hpl);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
            } else if ($request->get('loc') == 'hts-sax-1') {
                $work_stations = DB::SELECT("SELECT
                    *
                FROM
                    `body_dev_lists`
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                WHERE
                    location LIKE '%hts%'
                    AND location != 'hts-cuci'
                    AND location != 'hts-cutting-1'
                    AND location != 'hts-cutting-2'
                    AND location != 'hts-brass'
                    LIMIT 12");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model
                        FROM
                            body_queues
                            LEFT JOIN materials ON materials.material_number = body_queues.material_number
                        WHERE
                            body_queues.location = 'hts'
                            ORDER BY
                            body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->hpl . '<br>' . $queue[$j]->model);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
            } else if ($request->get('loc') == 'hts-sax-2') {
                $work_stations = DB::SELECT("SELECT
                    a.*,
                    employee_syncs.*
                FROM
                    (
                        (
                        SELECT
                            *
                        FROM
                            `body_dev_lists`
                        WHERE
                            location LIKE '%hts%'
                            AND location != 'hts-cuci'
                            AND location != 'hts-cutting-1'
                            AND location != 'hts-cutting-2'
                            AND location != 'hts-brass'
                        ORDER BY
                            id DESC
                            LIMIT 8
                        )) a
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = a.operator_id
                ORDER BY
                    a.id");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model
                        FROM
                            body_queues
                            LEFT JOIN materials ON materials.material_number = body_queues.material_number
                        WHERE
                            body_queues.location = 'hts'
                            ORDER BY
                            body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->hpl . '<br>' . $queue[$j]->model);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }

                $work_stations = DB::SELECT("SELECT
                    *
                FROM
                    `body_dev_lists`
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                WHERE
                    location = 'hts-cuci'");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model
                        FROM
                            body_queues
                            LEFT JOIN materials ON materials.material_number = body_queues.material_number
                        WHERE
                            body_queues.location like '%hts-cuci%'
                            ORDER BY
                            body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->hpl . '<br>' . $queue[$j]->model);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
                $work_stations = DB::SELECT("SELECT
                    *
                FROM
                    `body_dev_lists`
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                WHERE
                    location = 'hts-cutting-1'
                    OR location = 'hts-cutting-2'");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model
                        FROM
                            body_queues
                            LEFT JOIN materials ON materials.material_number = body_queues.material_number
                        WHERE
                            body_queues.location like '%hts-cutting%'
                            ORDER BY
                            body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->hpl . '<br>' . $queue[$j]->model);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
                $work_stations = DB::SELECT("SELECT
                    *
                FROM
                    `body_dev_lists`
                    LEFT JOIN employee_syncs ON employee_syncs.employee_id = body_dev_lists.operator_id
                WHERE
                    location = 'hts-brass'");

                foreach ($work_stations as $ws) {
                    $list_antrian = [];
                    $queue = DB::SELECT("SELECT
                            body_queues.material_number,
                            materials.material_description,
                            materials.hpl,
                            materials.model
                        FROM
                            body_queues
                            LEFT JOIN materials ON materials.material_number = body_queues.material_number
                        WHERE
                            body_queues.location like '%hts-brass%'
                            ORDER BY
                            body_queues.created_at");

                    for ($j = 0; $j < 11; $j++) {
                        if (isset($queue[$j])) {
                            array_push($list_antrian, $queue[$j]->hpl . '<br>' . $queue[$j]->model);
                        } else {
                            array_push($list_antrian, '<br>');
                        }
                    }

                    $board_sedang = '';
                    if ($ws->sedang_model != null) {
                        $board_sedang = $ws->sedang_model . '<br>' . $ws->sedang_serial_number;
                    } else {
                        $board_sedang = '<br>';
                    }

                    $dt_now = new DateTime();

                    $dt_sedang = new DateTime($ws->sedang_time);
                    $sedang_time = $dt_sedang->diff($dt_now);

                    array_push($boards, [
                        'ws_name' => strtoupper($ws->location),
                        'employee_id' => $ws->operator_id,
                        'employee_name' => $ws->name,
                        'sedang' => $board_sedang,
                        'sedang_time' => $sedang_time->format('%H:%i:%s'),
                        'queue_1' => $list_antrian[0],
                        'queue_2' => $list_antrian[1],
                        'queue_3' => $list_antrian[2],
                        'queue_4' => $list_antrian[3],
                        'queue_5' => $list_antrian[4],
                        'queue_6' => $list_antrian[5],
                        'queue_7' => $list_antrian[6],
                        'queue_8' => $list_antrian[7],
                        'queue_9' => $list_antrian[8],
                        'queue_10' => $list_antrian[9],
                        'queue_11' => $list_antrian[10],
                        'jumlah_urutan' => count($queue),
                    ]);
                }
            }

            $response = array(
                'status' => true,
                'boards' => $boards,
                'model' => $model,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexKensa($location)
    {
        if ($location == 'hts-kensa-body-sx') {
            $title = 'HTS Kensa Body';
            $title_jp = '';
            $loc = 'hts-kensa-body-sx';
            $ng_list = NgList::where('remark', 'welding')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'hts-kensa-neck') {
            $title = 'HTS Kensa Neck';
            $title_jp = '';
            $loc = 'hts-kensa-neck';
            $ng_list = NgList::where('remark', 'welding')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'hts-kensa-bellbow') {
            $title = 'HTS Kensa Bell & Bow';
            $title_jp = '';
            $loc = 'hts-kensa-neck';
            $ng_list = NgList::where('remark', 'welding')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'bff-kensa-body-sx') {
            $title = 'Buffing Kensa Body';
            $title_jp = '';
            $loc = 'bff-kensa-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'barrel-kensa-body-sx') {
            $title = 'Barrel Kensa Body';
            $title_jp = '';
            $loc = 'barrel-kensa-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'lcq-kensa-body-sx') {
            $title = 'Lacquering Kensa Body';
            $title_jp = '';
            $loc = 'lcq-kensa-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'lcq-incoming-body-sx') {
            $title = 'Lacquering Incoming Check Body';
            $title_jp = '';
            $loc = 'lcq-incoming-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'plt-kensa-body-sx') {
            $title = 'Plating Kensa Body';
            $title_jp = '';
            $loc = 'plt-kensa-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'plt-incoming-body-sx') {
            $title = 'Plating Incoming Check Body';
            $title_jp = '';
            $loc = 'plt-incoming-body-sx';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'plt-kensa-body-fl') {
            $title = 'Plating Kensa Body';
            $title_jp = '';
            $loc = 'plt-kensa-body-fl';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        } else if ($location == 'plt-incoming-body-fl') {
            $title = 'Plating Incoming Check Body';
            $title_jp = '';
            $loc = 'plt-incoming-body-fl';
            $ng_list = NgList::where('remark', 'middle')->where('location', $loc)->get();
            $view = 'processes.welding.body.kensa';
        }
        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'loc' => $loc,
            'ng_lists' => $ng_list,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function scanOperator(Request $request)
    {
        try {
            if (strpos($request->get('employee_id'), 'PI') !== false) {
                $nik = $request->get('employee_id');
                if (strlen($nik) > 9) {
                    $nik = substr($nik, 0, 9);
                }
                $emp = Employee::where('employee_id', $nik)->first();
            } else {
                $emp = Employee::where('tag', $request->get('employee_id'))->first();
            }

            if (count($emp) > 0) {
                $response = array(
                    'status' => true,
                    'message' => 'Login Success',
                    'employee' => $emp,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Invalid',
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function scanKanban(Request $request)
    {

        $started_at = date('Y-m-d H:i:s');

        try {
            if (is_numeric($request->get('tag'))) {
                $tags = BodyTag::where('tag', $request->get('tag'))->leftjoin('materials', 'materials.material_number', 'body_tags.material_number')->first();
            } else {
                $tags = DB::connection('mysql2')
                    ->table('completions')
                    ->select('materials.material_number',
                        'materials.description as material_description',
                        // 'ympimis.materials.model as model',
                        // 'ympimis.materials.hpl as hpl',
                        'completions.lot_completion as quantity')
                    ->where('barcode_number', $request->get('tag'))
                    ->join('materials', 'materials.id', 'material_id')
                // ->join('ympimis.materials','ympimis.materials.material_number','kitto.materials.material_number')
                    ->first();
            }

            $enthole_inventory = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $request->get('tag'))->where('location', 'enthole')->first();
            if (count($enthole_inventory) > 0) {
                if ($enthole_inventory->quantity > 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Material Belum Selesai Cuci Enthole',
                    );
                    return Response::json($response);
                }
            }

            if (count($tags) > 0) {

                $invent = BodyInventory::where('tag', $request->get('tag'))->first();
                if (count($invent) > 0) {
                    if (in_array($invent->location, $this->after_completions)) {
                        $d1 = strtotime($started_at);
                        $d2 = strtotime($invent->updated_at);
                        $totalMinuteDiff = abs($d1 - $d2) / 60;

                        if ($totalMinuteDiff < 30) {
                            $response = array(
                                'status' => false,
                                'message' => 'Kanban tidak dapat di CS, tunggu 30 menit lagi',
                            );
                            return Response::json($response);
                        }
                    }

                    $temp_ng = BodyNgTemp::select(DB::RAW('sum(quantity) as qty_ng'))->where('tag', $request->get('tag'))->first();
                    $response = array(
                        'status' => true,
                        'message' => 'Scan Kanban Success',
                        'tags' => $tags,
                        'temp_ng' => $temp_ng,
                    );
                    return Response::json($response);

                } else {
                    $completion = db::connection('mysql2')
                        ->table('completions')
                        ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
                        ->where('completions.barcode_number', $request->get('tag'))
                        ->first();

                    if ($completion) {

                        try {
                            if (str_contains($completion->location, 'FL')) {
                                $origin_group_code = '041';
                            } else if (str_contains($completion->location, 'SX')) {
                                $origin_group_code = '043';
                            }
                            $insert = new BodyInventory([
                                'tag' => $completion->barcode_number,
                                'material_number' => $completion->material_number,
                                'location' => 'new',
                                'storage_location' => $completion->location,
                                'quantity' => $completion->lot_completion,
                                'remark' => $completion->remark,
                                'origin_group_code' => $origin_group_code,

                            ]);
                            $insert->save();

                        } catch (Exception $e) {
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }

                        if (is_numeric($request->get('tag'))) {
                            $tags = BodyTag::where('tag', $request->get('tag'))->leftjoin('materials', 'materials.material_number', 'body_tags.material_number')->first();
                        } else {
                            $tags = DB::connection('mysql2')
                                ->table('completions')
                                ->select('materials.material_number',
                                    'materials.description as material_description',
                                    // 'ympimis.materials.model as model',
                                    // 'ympimis.materials.hpl as hpl',
                                    'completions.lot_completion as quantity')
                                ->where('barcode_number', $request->get('tag'))
                                ->join('materials', 'materials.id', 'material_id')
                            // ->join('ympimis.materials','ympimis.materials.material_number','kitto.materials.material_number')
                                ->first();
                        }

                        $invent = BodyInventory::where('tag', $request->get('tag'))->first();
                        // if (in_array($invent->location, $this->after_completions)) {
                        //     $response = array(
                        //         'status' => false,
                        //         'message' => 'Kanban Sudah Pernah di Scan',
                        //         'tags' => $tags,
                        //     );
                        //     return Response::json($response);
                        // }else{
                        $temp_ng = BodyNgTemp::select(DB::RAW('sum(quantity) as qty_ng'))->where('tag', $request->get('tag'))->first();
                        $response = array(
                            'status' => true,
                            'message' => 'Scan Kanban Success',
                            'tags' => $tags,
                            'temp_ng' => $temp_ng,
                        );
                        return Response::json($response);
                        // }
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'ID slip not found.',
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Tag Invalid',
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchBodyKensa(Request $request)
    {
        try {
            // $results = DB::SELECT("SELECT
            //     a.model,
            //     a.`key`,
            //     SUM( a.qty ) AS `check`,
            // IF
            //     (
            //         SUM( a.qty )- SUM( a.ng ) < 0,
            //         0,
            //     SUM( a.qty )- SUM( a.ng )) AS ok,
            //     SUM( a.ng ) AS ng
            // FROM
            //     (
            //     SELECT
            //         1 AS qty,
            //         0 AS ng,
            //         materials.model,
            //         materials.`key`,
            //         body_details.tag
            //     FROM
            //         body_details
            //         JOIN materials ON materials.material_number = body_details.material_number
            //     WHERE
            //         DATE( body_details.created_at ) = '".date('Y-m-d')."'
            //         AND operator_id = '".$request->get('employee_id')."'
            //         AND body_details.location = '".$request->get('location')."'
            //         AND remark IS NULL
            //     GROUP BY
            //         materials.model,
            //         materials.`key`,
            //         body_details.tag UNION ALL
            //     SELECT
            //         0 AS qty,
            //         1 AS ng,
            //         materials.model,
            //         materials.`key`,
            //         body_details.tag
            //     FROM
            //         body_details
            //         JOIN materials ON materials.material_number = body_details.material_number
            //     WHERE
            //         DATE( body_details.created_at ) = '".date('Y-m-d')."'
            //         AND operator_id = '".$request->get('employee_id')."'
            //         AND body_details.location = '".$request->get('location')."'
            //         AND remark = 'repair'
            //     GROUP BY
            //         materials.model,
            //         materials.`key`,
            //         body_details.tag
            //     ) a
            // GROUP BY
            //     a.model,
            //     a.`key`");

            $results = DB::select("SELECT
                a.model,
                a.`key`,
                SUM( a.qty ) AS `check`,
            IF
                (
                    SUM( a.qty )- SUM( a.ng ) < 0,
                    0,
                SUM( a.qty )- SUM( a.ng )) AS ok,
                SUM( a.ng ) AS ng
            FROM
                (
                SELECT
                    quantity AS qty,
                    0 AS ng,
                    materials.model,
                    materials.`key`,
                    body_details.tag
                FROM
                    body_details
                    JOIN materials ON materials.material_number = body_details.material_number
                WHERE
                    DATE( body_details.created_at ) = '" . date('Y-m-d') . "'
                    AND operator_id = '" . $request->get('employee_id') . "'
                    AND body_details.location = '" . $request->get('location') . "'
                    AND remark IS NULL
                GROUP BY
                    materials.model,
                    materials.`key`,
                    body_details.tag,
                    body_details.quantity UNION ALL
                SELECT
                    0 AS qty,
                    SUM( quantity ) AS ng,
                    materials.model,
                    materials.`key`,
                    body_ng_logs.tag
                FROM
                    body_ng_logs
                    JOIN materials ON materials.material_number = body_ng_logs.material_number
                WHERE
                    DATE( body_ng_logs.created_at ) = '" . date('Y-m-d') . "'
                    AND employee_id = '" . $request->get('employee_id') . "'
                    AND body_ng_logs.location = '" . $request->get('location') . "'
                GROUP BY
                    materials.model,
                    materials.`key`,
                    body_ng_logs.tag,
                    body_ng_logs.quantity
                ) a
            GROUP BY
                a.model,
                a.`key`");

            $response = array(
                'status' => true,
                'results' => $results,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputKensaBodySax(Request $request)
    {
        try {
            if (is_numeric($request->get('tag'))) {
                $tags = BodyTag::where('tag', $request->get('tag'))->first();
            } else {
                $tags = DB::connection('mysql2')
                    ->table('completions')
                    ->select('materials.material_number',
                        'materials.description as material_description',
                        // 'ympimis.materials.model as model',
                        // 'ympimis.materials.hpl as hpl',
                        'completions.lot_completion as quantity')
                    ->where('barcode_number', $request->get('tag'))
                    ->join('materials', 'materials.id', 'material_id')
                // ->join('ympimis.materials','ympimis.materials.material_number','kitto.materials.material_number')
                    ->first();
            }
            if (str_contains($request->get('loc'), 'hts')) {
                $storage_location = 'SX21';
            } else {
                $storage_location = 'SX51';
            }
            if ($request->get('ng')) {
                $ng = $request->get('ng');
                for ($i = 0; $i < count($ng); $i++) {
                    $temp = new BodyNgTemp([
                        'employee_id' => $request->get('employee_id'),
                        'tag' => $request->get('tag'),
                        // 'serial_number' => $tags->serial_number,
                        'material_number' => $tags->material_number,
                        'ng_name' => $ng[$i][0],
                        'quantity' => $ng[$i][1],
                        'location' => $request->get('loc'),
                        'started_at' => date('Y-m-d H:i:s'),
                    ]);
                    try {
                        $temp->save();
                    } catch (\Exception$e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }

                $detail = new BodyDetail([
                    'tag' => $request->get('tag'),
                    // 'serial_number' => $tags->serial_number,
                    // 'model' => $tags->model,
                    'material_number' => $tags->material_number,
                    'quantity' => $tags->quantity,
                    'location' => $request->get('loc'),
                    'storage_location' => $storage_location,
                    'origin_group_code' => '043',
                    'is_send_log' => 0,
                    'remark' => 'repair',
                    'sedang_start_time' => date('Y-m-d H:i:s'),
                    'sedang_finish_time' => date('Y-m-d H:i:s'),
                    'operator_id' => $request->get('employee_id'),
                ]);

                try {
                    DB::transaction(function () use ($detail) {
                        $detail->save();
                    });

                    $response = array(
                        'status' => true,
                        'message' => 'NG has been recorded.',
                    );
                    return Response::json($response);
                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            if (!$request->get('ng')) {
                $bodyInventory = BodyInventory::where('tag', $request->get('tag'))->first();
                $bodyInventory->location = $request->get('loc');
                $bodyInventory->storage_location = $storage_location;
                $bodyInventory->operator_id = $request->get('employee_id');
                $bodyInventory->updated_at = date('Y-m-d H:i:s');

                $ng_temp = BodyNgTemp::where('tag', $request->get('tag'))->get();
                if (count($ng_temp) > 0) {
                    for ($i = 0; $i < count($ng_temp); $i++) {
                        $ng_log = new BodyNgLog([
                            'employee_id' => $request->get('employee_id'),
                            'tag' => $request->get('tag'),
                            // 'serial_number' => $tags->serial_number,
                            'material_number' => $tags->material_number,
                            'ng_name' => $ng_temp[$i]->ng_name,
                            'quantity' => $ng_temp[$i]->quantity,
                            'location' => $request->get('loc'),
                            'started_at' => date('Y-m-d H:i:s'),
                        ]);

                        try {
                            $ng_log->save();
                        } catch (\Exception$e) {
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                        // $ng_temps = BodyNgTemp::where('id',$ng_temp[$i]->id)->first();
                        // $ng_temps->forceDelete();
                    }

                    $ng_temp = BodyNgTemp::where('tag', $request->get('tag'))->forceDelete();

                }

                $rework = null;
                $rework_detail = '';
                if (intval($request->get('count_rework')) > 0) {
                    $rework = 'rework';

                    $rework_detail = new BodyDetail([
                        'tag' => $request->get('tag'),
                        // 'serial_number' => $tags->serial_number,
                        // 'model' => $tags->model,
                        'material_number' => $tags->material_number,
                        'quantity' => intval($request->get('count_rework')),
                        'location' => $request->get('loc'),
                        'storage_location' => $storage_location,
                        'is_send_log' => 0,
                        'note' => $rework,
                        'origin_group_code' => '043',
                        'sedang_start_time' => date('Y-m-d H:i:s'),
                        'sedang_finish_time' => date('Y-m-d H:i:s'),
                        'operator_id' => $request->get('employee_id'),
                    ]);

                    $detail = new BodyDetail([
                        'tag' => $request->get('tag'),
                        // 'serial_number' => $tags->serial_number,
                        // 'model' => $tags->model,
                        'material_number' => $tags->material_number,
                        'quantity' => ($tags->quantity - intval($request->get('count_rework'))),
                        'location' => $request->get('loc'),
                        'storage_location' => $storage_location,
                        'is_send_log' => 0,
                        'origin_group_code' => '043',
                        'sedang_start_time' => date('Y-m-d H:i:s'),
                        'sedang_finish_time' => date('Y-m-d H:i:s'),
                        'operator_id' => $request->get('employee_id'),
                    ]);

                } else {
                    $detail = new BodyDetail([
                        'tag' => $request->get('tag'),
                        // 'serial_number' => $tags->serial_number,
                        // 'model' => $tags->model,
                        'material_number' => $tags->material_number,
                        'quantity' => $tags->quantity,
                        'location' => $request->get('loc'),
                        'storage_location' => $storage_location,
                        'is_send_log' => 0,
                        'note' => $rework,
                        'origin_group_code' => '043',
                        'sedang_start_time' => date('Y-m-d H:i:s'),
                        'sedang_finish_time' => date('Y-m-d H:i:s'),
                        'operator_id' => $request->get('employee_id'),
                    ]);

                }

                // if (in_array($request->get('loc'), $this->completions)) {
                //     $bom = BomTransaction::where('material_parent', $tags->material_number)->first();

                //     if($bom){
                //         $material = db::connection('mysql2')
                //         ->table('materials')
                //         ->where('material_number', '=', $bom->material_child)
                //         ->first();

                //         if(!$material){
                //             $mpdl = MaterialPlantDataList::where('material_number', $bom->material_child)->first();

                //             if($mpdl){
                //                 $material = db::connection('mysql2')
                //                 ->table('materials')
                //                 ->insert([
                //                     "material_number" => $mpdl->material_number,
                //                     "description" => $mpdl->material_description,
                //                     "location" => $mpdl->storage_location,
                //                     "lead_time" => 90,
                //                     "user_id" => Auth::id(),
                //                     'created_at' => date("Y-m-d H:i:s"),
                //                     'updated_at' => date("Y-m-d H:i:s")
                //                 ]);

                //                 $material = db::connection('mysql2')
                //                 ->table('materials')
                //                 ->where('material_number', '=', $bom->material_child)
                //                 ->first();

                //             }else{
                //                 $response = array(
                //                     'status' => false,
                //                     'message' => 'Material child not found',
                //                 );
                //                 return Response::json($response);
                //             }

                //         }

                //         $completion = db::connection('mysql2')
                //         ->table('histories')
                //         ->insert([
                //             "category" => "completion",
                //             "completion_barcode_number" => $request->get('tag'),
                //             "completion_description" => $material->description,
                //             "completion_location" => $material->location,
                //             "completion_issue_plant" => "8190",
                //             "completion_material_id" => $material->id,
                //             "completion_reference_number" => "",
                //             "lot" => $tags->quantity,
                //             "synced" => 0,
                //             'user_id' => "1",
                //             'created_at' => date("Y-m-d H:i:s"),
                //             'updated_at' => date("Y-m-d H:i:s")
                //         ]);

                //     }else{
                //         try{
                //             $error_log = new ErrorLog([
                //                 'error_message' => 'ERRORBOM_'. $tags->material_number .'_'. $tags->quantity,
                //                 'created_by' => Auth::id()
                //             ]);
                //             $error_log->save();

                //         }catch(\Exception $e){
                //             $response = array(
                //                 'status' => false,
                //                 'message' => $e->getMessage(),
                //             );
                //             return Response::json($response);
                //         }
                //     }
                // }

                try {
                    DB::transaction(function () use ($detail, $bodyInventory, $rework, $rework_detail) {
                        $detail->save();
                        $bodyInventory->save();

                        if ($rework != null) {
                            $rework_detail->save();
                        }
                    });
                    $response = array(
                        'status' => true,
                        'message' => 'Input material successfull.',
                    );
                    return Response::json($response);
                } catch (\Exception$e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexReportNG()
    {
        $title = 'Not Good Record';
        $title_jp = '不良内容';

        return view('processes.middle.body.report.not_good', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $this->location,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function fetchReportNG(Request $request)
    {
        try {
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            $location = $request->get('location');

            $not_good = DB::SELECT("SELECT
                body_ng_logs.employee_id,
                employee_syncs.`name`,
                body_ng_logs.tag,
                body_ng_logs.material_number,
                materials.material_description,
                materials.model,
                materials.surface,
                ng_name,
                quantity,
                body_ng_logs.location,
                body_ng_logs.created_at
            FROM
                body_ng_logs
                JOIN employee_syncs ON employee_syncs.employee_id = body_ng_logs.employee_id
                LEFT JOIN materials ON materials.material_number = body_ng_logs.material_number
            WHERE
                DATE( body_ng_logs.created_at ) >= '" . $date_from . "'
                AND DATE( body_ng_logs.created_at ) <= '" . $date_to . "'
                AND location = '" . $location . "'");

            $response = array(
                'status' => true,
                'not_good' => $not_good,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexProdResult()
    {
        $title = 'Middle Production Result';
        $title_jp = '中間工程生産実績';

        return view('processes.middle.body.report.report_prod_result', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $this->location,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function indexReworkResult()
    {
        $title = 'Middle Rework Report';
        $title_jp = '';

        return view('processes.middle.body.report.rework', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'locations' => $this->location,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function fetchProdResult(Request $request)
    {
        try {
            $date_from = $request->get('tanggal_from');
            $date_to = $request->get('tanggal_to');
            $location = $request->get('location');

            $prod_result = DB::SELECT("SELECT
                body_details.operator_id,
                employee_syncs.`name`,
                body_details.tag,
                body_details.material_number,
                materials.material_description,
                materials.model,
                materials.surface,
                quantity,
                body_details.location,
                body_details.note,
                body_details.created_at
            FROM
                body_details
                JOIN employee_syncs ON employee_syncs.employee_id = body_details.operator_id
                LEFT JOIN materials ON materials.material_number = body_details.material_number
            WHERE
                DATE( body_details.created_at ) >= '" . $date_from . "'
                AND DATE( body_details.created_at ) <= '" . $date_to . "'
                AND location = '" . $location . "'
                AND remark IS NULL");

            $response = array(
                'status' => true,
                'prod_result' => $prod_result,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchReworkResult(Request $request)
    {

        $month = $request->get('month');
        if (strlen($month) <= 0) {
            $month = date('Y-m');
        }

        $body_details = db::table('body_details')
            ->where('created_at', 'LIKE', '%' . $month . '%')
            ->where('note', 'rework')
            ->get();

        $materials = db::table('materials')
            ->leftJoin('bom_silvers', 'bom_silvers.material_parent', '=', 'materials.material_number')
            ->where('materials.hpl', 'FLBODY')
            ->select('materials.*', 'bom_silvers.usage', 'bom_silvers.divider')
            ->get();

        $calendars = db::table('weekly_calendars')
            ->where('week_date', 'LIKE', '%' . $month . '%')
            ->select('weekly_calendars.*', db::raw('DATE_FORMAT(week_date, "%d-%b") AS text_date'))
            ->get();

        $response = array(
            'status' => true,
            'body_details' => $body_details,
            'materials' => $materials,
            'calendars' => $calendars,
        );
        return Response::json($response);

    }

    public function fetchEntolLogs(Request $request)
    {
        try {
            $enthol = DB::connection('ympimis_2')->table('middle_enthol_logs')->where(DB::RAW('DATE(created_at)'), date('Y-m-d'))->where('location', $request->get('location'))->get();

            $descs = [];
            for ($i = 0; $i < count($enthol); $i++) {
                $desc = Material::where('material_number', $enthol[$i]->material_number)->first();
                array_push($descs, $desc->material_description);
            }
            $response = array(
                'status' => true,
                'enthol' => $enthol,
                'descs' => $descs,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function scanEntholKanban(Request $request)
    {
        try {

            $location = $request->get('loc');
            $tag = $request->get('tag');
            $employee_id = $request->get('employee_id');
            $type = $request->get('type');

            $completion = db::connection('mysql2')
                ->table('completions')
                ->leftJoin('materials', 'materials.id', '=', 'completions.material_id')
                ->where('completions.barcode_number', $request->get('tag'))
                ->first();

            $materials = DB::connection('ympimis_2')->table('middle_materials')->where('material_number', $completion->material_number)->where('remark', 'enthole')->first();

            if (!$materials) {
                $response = array(
                    'status' => false,
                    'message' => 'Material Tidak Termasuk Cuci Enthole',
                );
                return Response::json($response);
            }
            if ($type == 'selesai') {
                $inventory = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $tag)->where('location', 'enthole')->first();
                if (count($inventory) > 0) {
                    if ($inventory->quantity > 0) {
                        $update = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $tag)->where('location', 'enthole')->update([
                            'quantity' => $inventory->quantity - $completion->lot_completion,
                            'last_check' => $employee_id,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);

                        $log = db::connection('ympimis_2')->table('middle_enthol_logs')->insert([
                            'tag' => $tag,
                            'location' => $location,
                            'material_number' => $completion->material_number,
                            'quantity' => $completion->lot_completion,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    } else {
                        $response = array(
                            'status' => false,
                            'message' => 'Material Belum Cuci Enthole',
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Material Belum Cuci Enthole',
                    );
                    return Response::json($response);
                }
            } elseif ($type == 'enthole') {
                // $inventory = db::connection('ympimis_2')->table('middle_enthols')->where('tag',$tag)->where('location','start_end')->first();
                // if (count($inventory) > 0 ) {
                // if ($inventory->quantity > 0) {
                $inventory_enthol = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $tag)->where('location', 'enthole')->first();
                if (count($inventory_enthol) > 0) {
                    if ($inventory_enthol->quantity > 0) {
                        $response = array(
                            'status' => false,
                            'message' => 'Material Belum Finish Cuci Enthole & Kensa',
                        );
                        return Response::json($response);
                    } else {
                        $update = db::connection('ympimis_2')->table('middle_enthols')->where('tag', $tag)->where('location', 'enthole')->update([
                            'quantity' => $inventory_enthol->quantity + $completion->lot_completion,
                            'last_check' => $employee_id,
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }
                } else {
                    $insert = db::connection('ympimis_2')->table('middle_enthols')->insert([
                        'tag' => $tag,
                        'location' => 'enthole',
                        'material_number' => $completion->material_number,
                        'quantity' => $completion->lot_completion,
                        'last_check' => $employee_id,
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
                }

                // $update_start = db::connection('ympimis_2')->table('middle_enthols')->where('tag',$tag)->where('location','start_end')->update([
                //     'quantity' => $inventory->quantity - $completion->lot_completion,
                //     'last_check' => $employee_id,
                //     'updated_at' => date('Y-m-d H:i:s')
                // ]);

                $log = db::connection('ympimis_2')->table('middle_enthol_logs')->insert([
                    'tag' => $tag,
                    'location' => $location,
                    'material_number' => $completion->material_number,
                    'quantity' => $completion->lot_completion,
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
                // }else{
                //     $response = array(
                //         'status' => false,
                //         'message' => 'Material Belum Finish Cuci Enthole & Kensa'
                //     );
                //     return Response::json($response);
                // }
                // }else{
                //     $response = array(
                //         'status' => false,
                //         'message' => 'Material Belum Finish Cuci Enthole & Kensa'
                //     );
                //     return Response::json($response);
                // }
            }
            $response = array(
                'status' => true,
                'message' => 'Scan Success',
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexEnthol($location)
    {
        $title = 'Cuci Enthol Body';
        $title_jp = '';
        $loc = 'plt-enthol-body';

        $view = 'processes.middle.enthole';
        return view($view, array(
            'title' => $title,
            'title_jp' => $title_jp,
            'loc' => $loc,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function indexBodyResume($location)
    {
        $fys = db::select("select DISTINCT fiscal_year from weekly_calendars");

        return view('processes.middle.body.report.resume', array(
            'title' => 'NG Middle Body Report',
            'title_jp' => '',
            'fys' => $fys,
            'location' => $location,
        ))->with('page', 'NG Middle Body');
    }

    public function fetchBodyResume(Request $request)
    {
        try {

            if ($request->get('fy') != '') {
                $fy = $request->get('fy');
            } else {
                $fiscal_year = DB::SELECT("SELECT DISTINCT
                    ( fiscal_year )
                FROM
                    weekly_calendars
                WHERE
                    week_date = DATE(
                    NOW())");
                $fy = $fiscal_year[0]->fiscal_year;
            }

            $resume_monthly = DB::SELECT("SELECT DISTINCT
                (
                DATE_FORMAT( week_date, '%Y-%m' )) AS `months`,
                COALESCE ( resumes.ng, 0 ) AS ng,
                COALESCE ( resumes.`check`, 0 ) AS `check`,
                COALESCE ( resumes.ng / resumes.`check`, 0 ) AS ng_rate
            FROM
                weekly_calendars
                LEFT JOIN (
                SELECT
                    a.`month`,
                    sum( a.ng ) AS ng,
                    sum( `check` ) AS `check`
                FROM
                    (
                    SELECT
                        b.`month`,
                        count( b.`month` ) AS ng,
                        0 AS `check`
                    FROM
                        (
                        SELECT
                            DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) AS `month`,
                            count( quantity ) AS ng,
                            0 AS `check`
                        FROM
                            body_ng_logs
                            JOIN materials ON materials.material_number = body_ng_logs.material_number
                        WHERE
                            location = '" . explode('-', $request->get('location'))[0] . "-incoming-body-" . explode('-', $request->get('location'))[1] . "'
                            AND issue_storage_location LIKE '%" . strtoupper(explode('-', $request->get('location'))[1]) . "%'
                        GROUP BY
                            DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ),
                            body_ng_logs.tag,
                            body_ng_logs.quantity
                        ) b
                    GROUP BY
                        b.`month` UNION ALL
                    SELECT
                        DATE_FORMAT( body_details.created_at, '%Y-%m' ) AS `month`,
                        0 AS ng,
                        sum( quantity ) AS `check`
                    FROM
                        body_details
                        JOIN materials ON materials.material_number = body_details.material_number
                    WHERE
                        location = '" . explode('-', $request->get('location'))[0] . "-incoming-body-" . explode('-', $request->get('location'))[1] . "'
                        AND issue_storage_location LIKE '%" . strtoupper(explode('-', $request->get('location'))[1]) . "%'
                        AND remark IS NULL
                    GROUP BY
                    DATE_FORMAT( body_details.created_at, '%Y-%m' )) a
                GROUP BY
                    a.`month`
                ) AS resumes ON resumes.`month` = DATE_FORMAT( week_date, '%Y-%m' )
            WHERE
                fiscal_year = 'FY198'
                AND DATE_FORMAT( week_date, '%Y-%m' ) != '" . $fy . "'
            ORDER BY
                week_date");

            $response = array(
                'status' => true,
                'resume_monthly' => $resume_monthly,
                'fy' => $fy,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexResumeNG($product)
    {
        $title = 'Resume NG Body '.strtoupper($product);
        $title_jp = '';
        return view('processes.middle.body.display.resume_ng', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'product' => $product,
            'location' => $this->location,
            'location2' => $this->location,
            'location3' => $this->location,
            'location4' => $this->location,
            'location5' => $this->location,
        ))->with('page', $title)->with('jpn', $title_jp);
    }

    public function fetchResumeNG(Request $request)
    {
        try {
            $month_from = $request->get('month_from');
            $month_to = $request->get('month_to');
            if ($month_from == "") {
                  if ($month_to == "") {
                    $first = date('Y-m',strtotime('- 12 month'));
                    $last = date('Y-m',strtotime('-1 month'));
                    $firstTitle = date('M Y',strtotime($first));
                    $lastTitle = date("M Y", strtotime($last));
                  }else{
                    $first = date('Y-m',strtotime('- 12 month'));
                    $last = $month_to;
                    $firstTitle = date('M Y',strtotime($first));
                    $lastTitle = date("M Y", strtotime($last));
                  }
                }else{
                 if ($month_to == "") {
                  $first = $month_from;
                  $last = date('Y-m',strtotime('-1 month'));
                  $firstTitle = date('M Y',strtotime($first));
                  $lastTitle = date("M Y", strtotime($last));
                }else{
                  $first = $month_from;
                  $last = $month_to;
                  $firstTitle = date('M Y',strtotime($first));
                  $lastTitle = date("M Y", strtotime($last));
                }
              }
              $location = '';
              if($request->get('location') != null){
                $locations =  explode(",", $request->get('location'));
                for ($i=0; $i < count($locations); $i++) {
                  $location = $location."'".$locations[$i]."'";
                  if($i != (count($locations)-1)){
                    $location = $location.',';
                  }
                }
                $locationin = "AND location IN ( ".$location." ) ";
              }
              else{
                $locations =  explode(",", $request->get('location_all'));
                for ($i=0; $i < count($locations); $i++) {
                  $location = $location."'".$locations[$i]."'";
                  if($i != (count($locations)-1)){
                    $location = $location.',';
                  }
                }
                $locationin = "AND location IN ( ".$location." ) ";
              }

              $ng_rate_body = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS `months`,
                    (
                    DATE_FORMAT( week_date, '%b-%Y' )) AS `month_name`,
                    COALESCE ( resumes.ng, 0 ) AS ng,
                    COALESCE ( resumes.`check`, 0 ) AS `check`,
                    COALESCE ( resumes.ng / resumes.`check`, 0 ) AS ng_rate 
                FROM
                    weekly_calendars
                    LEFT JOIN (
                    SELECT
                        a.`month`,
                        sum( a.ng ) AS ng,
                        sum( `check` ) AS `check` 
                    FROM
                        (
                        SELECT
                            b.`month`,
                            sum( b.`ng` ) AS ng,
                            0 AS `check` 
                        FROM
                            (
                            SELECT
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) AS `month`,
                                sum( quantity ) AS ng,
                                0 AS `check` 
                            FROM
                                body_ng_logs
                                LEFT JOIN materials ON materials.material_number = body_ng_logs.material_number 
                            WHERE
                                hpl LIKE '%BODY%' 
                                ".$locationin."
                                AND `key` LIKE '%BODY%' 
                                AND remark IS NULL 
                            GROUP BY
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ),
                                body_ng_logs.tag 
                            ) b 
                        GROUP BY
                            b.`month` UNION ALL
                        SELECT
                            DATE_FORMAT( body_details.created_at, '%Y-%m' ) AS `month`,
                            0 AS ng,
                            sum( quantity ) AS `check` 
                        FROM
                            body_details
                            LEFT JOIN materials ON materials.material_number = body_details.material_number 
                        WHERE
                            hpl LIKE '%BODY%' 
                            ".$locationin."
                            AND `key` LIKE '%BODY%' 
                            AND remark IS NULL 
                        GROUP BY
                        DATE_FORMAT( body_details.created_at, '%Y-%m' )) a 
                    GROUP BY
                        a.`month` 
                    ) AS resumes ON resumes.`month` = DATE_FORMAT( week_date, '%Y-%m' ) 
                WHERE
                    DATE_FORMAT( week_date, '%Y-%m' ) >= '".$first."' 
                    AND DATE_FORMAT( week_date, '%Y-%m' ) <= '".$last."' 
                ORDER BY
                    week_date");
              $ng_rate_head = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS `months`,
                    (
                    DATE_FORMAT( week_date, '%b-%Y' )) AS `month_name`,
                    COALESCE ( resumes.ng, 0 ) AS ng,
                    COALESCE ( resumes.`check`, 0 ) AS `check`,
                    COALESCE ( resumes.ng / resumes.`check`, 0 ) AS ng_rate 
                FROM
                    weekly_calendars
                    LEFT JOIN (
                    SELECT
                        a.`month`,
                        sum( a.ng ) AS ng,
                        sum( `check` ) AS `check` 
                    FROM
                        (
                        SELECT
                            b.`month`,
                            sum( b.`ng` ) AS ng,
                            0 AS `check` 
                        FROM
                            (
                            SELECT
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) AS `month`,
                                sum( quantity ) AS ng,
                                0 AS `check` 
                            FROM
                                body_ng_logs
                                LEFT JOIN materials ON materials.material_number = body_ng_logs.material_number 
                            WHERE
                                hpl LIKE '%BODY%' 
                                ".$locationin."
                                AND `key` LIKE '%HEAD%' 
                                AND remark IS NULL 
                            GROUP BY
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ),
                                body_ng_logs.tag 
                            ) b 
                        GROUP BY
                            b.`month` UNION ALL
                        SELECT
                            DATE_FORMAT( body_details.created_at, '%Y-%m' ) AS `month`,
                            0 AS ng,
                            sum( quantity ) AS `check` 
                        FROM
                            body_details
                            LEFT JOIN materials ON materials.material_number = body_details.material_number 
                        WHERE
                            hpl LIKE '%BODY%' 
                            ".$locationin."
                            AND `key` LIKE '%HEAD%' 
                            AND remark IS NULL 
                        GROUP BY
                        DATE_FORMAT( body_details.created_at, '%Y-%m' )) a 
                    GROUP BY
                        a.`month` 
                    ) AS resumes ON resumes.`month` = DATE_FORMAT( week_date, '%Y-%m' ) 
                WHERE
                    DATE_FORMAT( week_date, '%Y-%m' ) >= '".$first."' 
                    AND DATE_FORMAT( week_date, '%Y-%m' ) <= '".$last."' 
                ORDER BY
                    week_date");

              $ng_rate_foot = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS `months`,
                    (
                    DATE_FORMAT( week_date, '%b-%Y' )) AS `month_name`,
                    COALESCE ( resumes.ng, 0 ) AS ng,
                    COALESCE ( resumes.`check`, 0 ) AS `check`,
                    COALESCE ( resumes.ng / resumes.`check`, 0 ) AS ng_rate 
                FROM
                    weekly_calendars
                    LEFT JOIN (
                    SELECT
                        a.`month`,
                        sum( a.ng ) AS ng,
                        sum( `check` ) AS `check` 
                    FROM
                        (
                        SELECT
                            b.`month`,
                            sum( b.`ng` ) AS ng,
                            0 AS `check` 
                        FROM
                            (
                            SELECT
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) AS `month`,
                                sum( quantity ) AS ng,
                                0 AS `check` 
                            FROM
                                body_ng_logs
                                LEFT JOIN materials ON materials.material_number = body_ng_logs.material_number 
                            WHERE
                                hpl LIKE '%BODY%' 
                                ".$locationin."
                                AND `key` LIKE '%FOOT%' 
                                AND remark IS NULL 
                            GROUP BY
                                DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ),
                                body_ng_logs.tag 
                            ) b 
                        GROUP BY
                            b.`month` UNION ALL
                        SELECT
                            DATE_FORMAT( body_details.created_at, '%Y-%m' ) AS `month`,
                            0 AS ng,
                            sum( quantity ) AS `check` 
                        FROM
                            body_details
                            LEFT JOIN materials ON materials.material_number = body_details.material_number 
                        WHERE
                            hpl LIKE '%BODY%' 
                            ".$locationin."
                            AND `key` LIKE '%FOOT%' 
                            AND remark IS NULL 
                        GROUP BY
                        DATE_FORMAT( body_details.created_at, '%Y-%m' )) a 
                    GROUP BY
                        a.`month` 
                    ) AS resumes ON resumes.`month` = DATE_FORMAT( week_date, '%Y-%m' ) 
                WHERE
                    DATE_FORMAT( week_date, '%Y-%m' ) >= '".$first."' 
                    AND DATE_FORMAT( week_date, '%Y-%m' ) <= '".$last."' 
                ORDER BY
                    week_date");
            $response = array(
                'status' => true,
                'ng_rate_head' => $ng_rate_head,
                'ng_rate_body' => $ng_rate_body,
                'ng_rate_foot' => $ng_rate_foot,
                'monthTitle' => $firstTitle.' - '.$lastTitle
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchPareto(Request $request)
    {
        try {
            $month_from = $request->get('month_from');
            $month_to = $request->get('month_to');
            if ($month_from == "") {
                  if ($month_to == "") {
                    $first = date('Y-m',strtotime('- 12 month'));
                    $last = date('Y-m',strtotime('-1 month'));
                    $firstTitle = date('M Y',strtotime($first));
                    $lastTitle = date("M Y", strtotime($last));
                  }else{
                    $first = date('Y-m',strtotime('- 12 month'));
                    $last = $month_to;
                    $firstTitle = date('M Y',strtotime($first));
                    $lastTitle = date("M Y", strtotime($last));
                  }
                }else{
                 if ($month_to == "") {
                  $first = $month_from;
                  $last = date('Y-m',strtotime('-1 month'));
                  $firstTitle = date('M Y',strtotime($first));
                  $lastTitle = date("M Y", strtotime($last));
                }else{
                  $first = $month_from;
                  $last = $month_to;
                  $firstTitle = date('M Y',strtotime($first));
                  $lastTitle = date("M Y", strtotime($last));
                }
              }
              $location = '';
              if($request->get('location') != null){
                $locations =  explode(",", $request->get('location'));
                for ($i=0; $i < count($locations); $i++) {
                  $location = $location."'".$locations[$i]."'";
                  if($i != (count($locations)-1)){
                    $location = $location.',';
                  }
                }
                $locationin = "AND location IN ( ".$location." ) ";
              }
              else{
                $locations =  explode(",", $request->get('location_all'));
                for ($i=0; $i < count($locations); $i++) {
                  $location = $location."'".$locations[$i]."'";
                  if($i != (count($locations)-1)){
                    $location = $location.',';
                  }
                }
                $locationin = "AND location IN ( ".$location." ) ";
              }

            $pareto = DB::SELECT("SELECT
                ng_name,
                sum( quantity ) AS qty 
            FROM
                body_ng_logs
                LEFT JOIN materials ON materials.material_number = body_ng_logs.material_number 
            WHERE
                hpl LIKE '%BODY%' 
                AND `key` LIKE '%".$request->get('cat')."%' 
                AND DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) >= '".$first."' 
                AND DATE_FORMAT( body_ng_logs.created_at, '%Y-%m' ) <= '".$last."' 
                ".$locationin."
            GROUP BY
                ng_name 
            ORDER BY
                sum( quantity ) DESC");
            $response = array(
                'status' => true,
                'pareto' => $pareto,
                'monthTitle' => $firstTitle.' - '.$lastTitle
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }
}
