<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\ProductionForecast;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class ForecastController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexForecast()
    {
        $title = 'Production Forecast';
        $title_jp = '';

        return view('production_forecast.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('Head', $title);
    }

    public function fetchForecast(Request $request)
    {
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

            array_push($interval_month, $row);
        }

        $material = db::select("SELECT material.material_number, mpdl.material_description FROM
			(SELECT DISTINCT material_number FROM production_forecasts
			WHERE forecast_month BETWEEN '" . $start->format("Y-m-d") . "' AND '" . $end->format("Y-m-d") . "') AS material
			LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = material.material_number");

        $forecast = db::select("SELECT DATE_FORMAT(forecast_month,'%Y-%m') as `month`, material_number, quantity FROM production_forecasts
			WHERE forecast_month BETWEEN '" . $start->format("Y-m-d") . "' AND '" . $end->format("Y-m-d") . "'");

        $response = array(
            'status' => true,
            'interval' => $interval_month,
            'material' => $material,
            'forecast' => $forecast,
        );
        return Response::json($response);

    }

    public function uploadForecast(Request $request)
    {
        $start = $request->get('start');
        $end = $request->get('end');
        $period = $request->get('period');
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();

        $delete = ProductionForecast::whereBetween('forecast_month', [$start . '-01', $end . '-01'])->forceDelete();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $month = date('Y-m-d', strtotime(str_replace('/', '-', $uploadColumn[0])));
            $material_number = $uploadColumn[1];
            $quantity = $uploadColumn[2];

            try {
                $insert = new ProductionForecast([
                    'forecast_month' => $month,
                    'material_number' => $material_number,
                    'quantity' => $quantity,
                    'remark' => $period,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();

                $update_lock = db::table('locks')
                    ->where('remark', 'breakdown_smbmr')
                    ->update([
                        'status' => 0,
                    ]);

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
