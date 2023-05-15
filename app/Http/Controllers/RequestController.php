<?php

namespace App\Http\Controllers;

use App\Destination;
use App\Http\Controllers\Controller;
use App\ProductionRequest;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class RequestController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexRequest()
    {
        $title = 'Production Request';
        $title_jp = '';

        $destination = Destination::orderBy('destination_shortname', 'ASC')->get();

        return view(
            'production_request.index',
            array(
                'destination' => $destination,
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', $title)->with('Head', $title);
    }

    public function fetchRequest(Request $request)
    {
        $category = $request->get('category');
        $start = $request->get('start');
        $end = $request->get('end');

        if (strlen($start) == 0) {
            $start = date('Y-m') . '-01';
        } else {
            $start = $start . '-01';
        }

        if (strlen($end) == 0) {
            $end = date('Y-m', strtotime(date('Y-m') . '-01 +1 year')) . '-01';
        } else {
            $end = $end . '-01';
        }

        if (strlen($category) > 0) {
            $category = "AND pr.category = '" . $category . "' ";
        } else {
            $category = '';
        }

        $start = new DateTime($start);
        $end = new DateTime($end);
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $interval_month = [];
        foreach ($period as $dt) {
            $row = array();
            $row['month'] = $dt->format("Y-m");
            $row['text_month'] = $dt->format("F");
            $row['text_year'] = $dt->format("Y");
            $row['remark'] = "-";

            array_push($interval_month, $row);
        }

        $material = db::select("SELECT material.category, material.material_number, mpdl.material_description, destinations.destination_shortname FROM
			(SELECT DISTINCT pr.category, pr.material_number, pr.destination_code FROM production_requests pr
			WHERE pr.request_month BETWEEN '" . $start->format("Y-m-d") . "' AND '" . $end->format("Y-m-d") . "'
			" . $category . ") AS material
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = material.material_number
			LEFT JOIN destinations ON destinations.destination_code = material.destination_code
			ORDER BY material.category DESC, material.material_number, destinations.destination_shortname ASC");

        $request = db::select("SELECT pr.category, DATE_FORMAT(pr.request_month,'%Y-%m') as `month`, pr.material_number, d.destination_shortname, pr.quantity, pr.remark, pr.created_at FROM production_requests pr
			LEFT JOIN destinations d ON d.destination_code = pr.destination_code
			WHERE pr.request_month BETWEEN '" . $start->format("Y-m-d") . "' AND '" . $end->format("Y-m-d") . "'"
            . $category .
            'ORDER BY pr.request_month');

        for ($i = 0; $i < count($interval_month); $i++) {
            for ($j = 0; $j < count($request); $j++) {
                if ($interval_month[$i]['month'] == $request[$j]->month) {
                    $interval_month[$i]['remark'] = $request[$j]->remark;
                    break;
                }
            }
        }

        $response = array(
            'status' => true,
            'interval' => $interval_month,
            'material' => $material,
            'request' => $request,
        );
        return Response::json($response);
    }

    public function uploadRequest(Request $request)
    {

        $start = $request->get('start');
        $end = $request->get('end');
        $period = $request->get('period');
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();

        $delete = ProductionRequest::whereBetween('request_month', [$start . '-01', $end . '-01'])->forceDelete();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $month = date('Y-m-d', strtotime(str_replace('/', '-', $uploadColumn[0])));
            $category = $uploadColumn[1];
            $material_number = $uploadColumn[2];
            $destination_shortname = $uploadColumn[3];
            $quantity = $uploadColumn[4];

            $destination_code = '';
            $priority = null;
            $destination = Destination::where('destination_shortname', $destination_shortname)->first();

            if ($destination) {
                $destination_code = $destination->destination_code;

                if ($destination->destination_shortname == 'YMMJ') {
                    $priority = 1;
                } elseif ($destination->destination_shortname == 'XY') {
                    $priority = 2;
                }
            }

            try {
                $insert = new ProductionRequest([
                    'request_month' => $month,
                    'category' => strtoupper($category),
                    'material_number' => $material_number,
                    'destination_code' => $destination_code,
                    'priority' => $priority,
                    'quantity' => $quantity,
                    'remark' => $period,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();

            } catch (Exception $e) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

}