<?php

namespace App\Http\Controllers;

use App\Destination;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class SalesOrderController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexSalesOrder()
    {
        $title = 'Sales Order';
        $title_jp = '';

        $destination = Destination::orderBy('destination_shortname', 'ASC')->get();

        return view('sales_order.index', array(
            'destination' => $destination,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('Head', $title);
    }

    public function fetchSalesOrder(Request $request)
    {

        if (strlen($request->get('month')) > 0) {
            $month = $request->get('month') . '-01';
            $month_txt = date('M-Y', strtotime($month));
        } else {
            $month = date('Y-m') . '-01';
            $month_txt = date('M-Y');
        }

        $sales_order = db::connection('ympimis_2')
            ->table('sales_orders')
            ->where('sales_month', '=', $month)
            ->orderBy('sales_order', 'ASC')
            ->orderBy('material_number', 'ASC')
            ->get();

        $destination = db::table('destinations')->get();

        $mpdl = db::table('materials')
            ->where('valcl', '9010')
            ->get();

        $response = array(
            'status' => true,
            'month_txt' => $month_txt,
            'sales_order' => $sales_order,
            'destination' => $destination,
            'mpdl' => $mpdl,
        );
        return Response::json($response);

    }

    public function uploadSalesOrder(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $month = $request->get('month') . '-01';
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        DB::beginTransaction();
        $delete = db::connection('ympimis_2')
            ->table('sales_orders')
            ->where('sales_month', '=', $month)
            ->delete();

        $row = 1;
        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);

            $sales_order = $uploadColumn[0];
            $material_number = $uploadColumn[1];
            $destination_code = $uploadColumn[2];
            $quantity = $uploadColumn[3];
            $price = $uploadColumn[4];

            if (strlen($material_number) != 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Row ' . $row . ' : GMC unmatch',
                );
                return Response::json($response);
            }

            if (strlen($destination_code) < 6 || strlen($destination_code) > 7) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Row ' . $row . ' : Destination code unmatch',
                );
                return Response::json($response);
            } else {
                $is_destination = db::table('destinations')
                    ->where('destination_code', $destination_code)
                    ->first();

                if (!$is_destination) {
                    DB::rollback();
                    $response = array(
                        'status' => false,
                        'message' => 'Row ' . $row . ' : Destination code unregistered in system',
                    );
                    return Response::json($response);
                }
            }

            if (preg_match("/[a-z]/i", $quantity)) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Row ' . $row . ' : Qty data is not number',
                );
                return Response::json($response);
            }

            if (preg_match("/[a-z]/i", $price)) {
                DB::rollback();
                $response = array(
                    'status' => false,
                    'message' => 'Row ' . $row . ' : Price data is not number',
                );
                return Response::json($response);
            }

            try {
                $insert = db::connection('ympimis_2')
                    ->table('sales_orders')
                    ->insert([
                        'sales_month' => $month,
                        'sales_order' => $sales_order,
                        'material_number' => $material_number,
                        'destination_code' => $destination_code,
                        'quantity' => $quantity,
                        'price' => $price,
                        'created_by' => Auth::id(),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                $row++;

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
