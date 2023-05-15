<?php

namespace App\Http\Controllers;

use App\Inventory;
use App\LogTransaction;
use App\Material;
use App\OriginGroup;
use DataTables;
use DateInterval;
use DatePeriod;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class InventoryController extends Controller
{

    private $plant;
    private $transaction_status;

    public function __construct()
    {
        $this->middleware('auth');
        $this->plant = [
            '8190',
            '8191',
        ];
        $this->transaction_status = [
            'Uploaded',
            'Not Uploaded',
        ];
        $this->mvt = [
            '101',
            '102',
            '9P1',
            '9P2',
            '9I3',
            '9I4',
        ];
    }

    public function index()
    {
        $plants = $this->plant;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexInventory', array(
            'plants' => $plants,
            'origin_groups' => $origin_groups,
        ))->with('page', 'Location Stock');
    }

    public function indexCompletion()
    {
        $transaction_statuses = $this->transaction_status;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexCompletion', array(
            'origin_groups' => $origin_groups,
            'transaction_statuses' => $transaction_statuses,
        ))->with('page', 'Completion Transaction')->with('head', 'Transaction');
    }

    public function indexTransfer()
    {
        $transaction_statuses = $this->transaction_status;
        $origin_groups = OriginGroup::orderBy('origin_group_code', 'asc')->get();
        return view('inventories.indexTransfer', array(
            'origin_groups' => $origin_groups,
            'transaction_statuses' => $transaction_statuses,
        ))->with('page', 'Transfer Transaction')->with('head', 'Transaction');
    }

    public function indexHistory()
    {
        $mvts = $this->mvt;
        $origin_groups = Material::whereIn('category', ['KD', 'FG'])
            ->select('category', 'hpl')
            ->distinct()
            ->orderBy('category', 'ASC')
            ->orderBy('hpl', 'ASC')
            ->get();

        return view('inventories.indexHistory', array(
            'mvts' => $mvts,
            'origin_groups' => $origin_groups,
        ))->with('page', 'History Transaction')->with('head', 'Transaction');
    }

    public function fetchHistory(Request $request)
    {
        $log_transactions = LogTransaction::leftJoin('materials', 'materials.material_number', '=', 'log_transactions.material_number');
        $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
        $date_to = date('Y-m-d', strtotime($request->get('dateTo')));

        if (strlen($request->get('dateFrom')) > 0 && strlen($request->get('dateTo')) == 0) {
            $where1 = ' and date(transaction_date) >= "' . $date_from . '"';
            $where1_2 = ' and date( transaction_completions.created_at) >= "' . $date_from . '"';
            $where1_3 = ' and date(transaction_transfers.created_at) >= "' . $date_from . '"';
        } else {
            $where1 = '';
            $where1_2 = '';
            $where1_3 = '';
        }

        if (strlen($request->get('dateTo')) > 0 && strlen($request->get('dateFrom')) > 0) {
            $where1 = ' and date(transaction_date) >= "' . $date_from . '" and date(transaction_date) <= "' . $date_to . '"';
            $where1_2 = ' and date(transaction_completions.created_at) >= "' . $date_from . '" and date(transaction_completions.created_at) <= "' . $date_to . '"';
            $where1_3 = ' and date(transaction_transfers.created_at) >= "' . $date_from . '" and date(transaction_transfers.created_at) <= "' . $date_to . '"';
        }

        if ($request->get('originGroup') != null) {
            $origin_group_code = implode("','", $request->get('originGroup'));
            $where2 = ' and materials.hpl in (\'' . $origin_group_code . '\')';
        } else {
            $where2 = '';
        }

        if ($request->get('mvt') != null) {
            $mvt = implode("','", $request->get('mvt'));
            $where3 = ' and log_transactions.mvt in (\'' . $mvt . '\')';
            $where3_2 = ' and transaction_completions.movement_type in (\'' . $mvt . '\')';
            $where3_3 = ' and transaction_transfers.movement_type in (\'' . $mvt . '\')';
        } else {
            $where3 = '';
            $where3_2 = '';
            $where3_3 = '';
        }

        if (strlen($request->get('materialNumber')) > 0) {
            $material_number_arr = explode(",", $request->get('materialNumber'));
            $material_number = implode("','", $material_number_arr);
            $where4 = ' and materials.material_number in (\'' . $material_number . '\')';
        } else {
            $where4 = '';
        }

        if (strlen($request->get('issueStorageLocation')) > 0) {
            $sloc_arr = explode(",", $request->get('issueStorageLocation'));
            $sloc = implode("','", $sloc_arr);
            $where5 = ' and log_transactions.issue_storage_location in (\'' . $sloc . '\')';
            $where5_2 = ' and transaction_completions.issue_location in (\'' . $sloc . '\')';
            $where5_3 = ' and transaction_transfers.issue_location in (\'' . $sloc . '\')';
        } else {
            $where5 = '';
            $where5_2 = '';
            $where5_3 = '';
        }

        if (strlen($request->get('receiveStorageLocation')) > 0) {
            $toloc_arr = explode(",", $request->get('receiveStorageLocation'));
            $toloc = implode("','", $toloc_arr);
            $where6 = ' and log_transactions.receive_storage_location in (\'' . $toloc . '\')';
            $where6_3 = ' and transaction_transfers.receive_location in (\'' . $toloc . '\')';
        } else {
            $where6 = '';
            $where6_3 = '';
        }

        if ($request->get('category') == 'SCRAP') {
            $where7 = ' and reference_number = "' . $request->get('category') . '"';

            $query = "select ref, material_number, material_description, issue_storage_location, receive_storage_location, mvt, qty, transaction_date, created_at, reference_file, reference_number from (
            select serial_number as ref, transaction_completions.material_number, materials.material_description, transaction_completions.issue_location as issue_storage_location, '-' as receive_storage_location, transaction_completions.movement_type as mvt, transaction_completions.quantity as qty, transaction_completions.updated_at as transaction_date, transaction_completions.created_at, transaction_completions.reference_file, reference_number
            from transaction_completions
            left join material_plant_data_lists materials on materials.material_number = transaction_completions.material_number
            where transaction_completions.deleted_at is null
            " . $where1_2 . "
            " . $where2 . "
            " . $where3_2 . "
            " . $where5_2 . "
            " . $where7 . ") as final order by final.created_at asc";
        } else {
            $query = "select ref, material_number, material_description, issue_storage_location, receive_storage_location, mvt, qty, transaction_date, created_at, reference_file, reference_number from (
           select '-' as ref, log_transactions.material_number, materials.material_description, log_transactions.issue_storage_location, coalesce(log_transactions.receive_storage_location, '-') as receive_storage_location, log_transactions.mvt, log_transactions.qty, log_transactions.transaction_date, log_transactions.created_at, log_transactions.reference_file, '' as reference_number
           from log_transactions
           left join material_plant_data_lists materials on materials.material_number = log_transactions.material_number
           where log_transactions.deleted_at is null
           and log_transactions.mvt <> '9P2'
           " . $where1 . "
           " . $where2 . "
           " . $where3 . "
           " . $where4 . "
           " . $where5 . "
           " . $where6 . "
           union all
           select ref, material_number, material_description, issue_storage_location, receive_storage_location, mvt, qty, transaction_date, created_at, reference_file, reference_number from (
           select serial_number as ref, transaction_completions.material_number, materials.material_description, transaction_completions.issue_location as issue_storage_location, '-' as receive_storage_location, transaction_completions.movement_type as mvt, transaction_completions.quantity as qty, transaction_completions.updated_at as transaction_date, transaction_completions.created_at, transaction_completions.reference_file, reference_number
           from transaction_completions
           left join material_plant_data_lists materials on materials.material_number = transaction_completions.material_number
           where transaction_completions.deleted_at is null
           and transaction_completions.reference_number is null
           " . $where1_2 . "
           " . $where2 . "
           " . $where3_2 . "
           " . $where4 . "
           " . $where5_2 . "
           union all
           select serial_number as ref, transaction_transfers.material_number, materials.material_description, transaction_transfers.issue_location as issue_storage_location, transaction_transfers.receive_location as receive_storage_location, transaction_transfers.movement_type as mvt, transaction_transfers.quantity as qty, transaction_transfers.updated_at as transaction_date, transaction_transfers.created_at, transaction_transfers.reference_file, '' as reference_number
           from transaction_transfers
           left join material_plant_data_lists materials on materials.material_number = transaction_transfers.material_number
           where transaction_transfers.deleted_at is null
           " . $where1_3 . "
           " . $where2 . "
           " . $where3_3 . "
           " . $where4 . "
           " . $where5_3 . "
           " . $where6_3 . "
           union all
           select serial_number as ref, transaction_completions.material_number, materials.material_description, transaction_completions.issue_location as issue_storage_location, '-' as receive_storage_location, transaction_completions.movement_type as mvt, transaction_completions.quantity as qty, transaction_completions.updated_at as transaction_date, transaction_completions.created_at, transaction_completions.reference_file, reference_number
           from transaction_completions
           left join material_plant_data_lists materials on materials.material_number = transaction_completions.material_number
           where transaction_completions.deleted_at is null
           and transaction_completions.reference_number = 'SCRAP'
           " . $where1_2 . "
           " . $where3_2 . "
           " . $where4 . "
           " . $where5_2 . ") as transactions ) as final order by final.created_at asc";
        }

        $log_transactions = db::select($query);

        return DataTables::of($log_transactions)->make(true);
    }

    public function fetchCompletion(Request $request)
    {
        $completions = db::table('flo_details')
            ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
            ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
            ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code');

        if (strlen($request->get('dateFrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $completions = $completions->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if (strlen($request->get('dateTo')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $completions = $completions->where(DB::raw('DATE_FORMAT(flo_details.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if (strlen($request->get('materialNumber')) > 0) {
            $material_number = explode(",", $request->get('materialNumber'));
            $completions = $completions->whereIn('flo_details.material_number', $material_number);
        }

        if (strlen($request->get('storageLocation')) > 0) {
            $storage_location = explode(",", $request->get('storageLocation'));
            $completions = $completions->whereIn('materials.issue_storage_location', $storage_location);
        }

        if ($request->get('originGroup') != null) {
            $completions = $completions->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        if ($request->get('transactionStatus') == 'Uploaded') {
            $completions = $completions->whereNotNull('flo_details.completion');
        } elseif ($request->get('transactionStatus') == 'Not Uploaded') {
            $completions = $completions->whereNull('flo_details.completion');

        }

        $completions = $completions->orderBy('flo_details.created_at', 'asc')
            ->select(
                'flo_details.material_number',
                'materials.material_description',
                'materials.issue_storage_location',
                'flo_details.quantity',
                db::raw('if(flos.shipment_schedule_id = 0, "Maedaoshi", destinations.destination_shortname) as destination'),
                db::raw('if(flo_details.completion is not null, flo_details.completion, "-") as completion'),
                'flo_details.created_at'
            )
            ->get();

        $response = array(
            'status' => true,
            'tableData' => $completions,
        );
        return Response::json($response);
    }

    public function fetchTransfer(Request $request)
    {
        $transfers = db::table('flo_details')
            ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
            ->leftJoin('flos', 'flos.flo_number', '=', 'flo_details.flo_number')
            ->leftJoin('shipment_schedules', 'shipment_schedules.id', '=', 'flos.shipment_schedule_id')
            ->leftJoin('destinations', 'destinations.destination_code', '=', 'shipment_schedules.destination_code')
            ->leftJoin('flo_logs', 'flo_logs.flo_number', '=', 'flo_details.flo_number')
            ->where('flo_logs.status_code', '=', '2')
            ->whereIn('flos.status', ['2', '3', '4']);

        if (strlen($request->get('dateFrom')) > 0) {
            $date_from = date('Y-m-d', strtotime($request->get('dateFrom')));
            $transfers = $transfers->where(DB::raw('DATE_FORMAT(flo_logs.created_at, "%Y-%m-%d")'), '>=', $date_from);
        }

        if (strlen($request->get('dateTo')) > 0) {
            $date_to = date('Y-m-d', strtotime($request->get('dateTo')));
            $transfers = $transfers->where(DB::raw('DATE_FORMAT(flo_logs.created_at, "%Y-%m-%d")'), '<=', $date_to);
        }

        if (strlen($request->get('materialNumber')) > 0) {
            $material_number = explode(",", $request->get('materialNumber'));
            $transfers = $transfers->whereIn('flo_details.material_number', $material_number);
        }

        if (strlen($request->get('storageLocation')) > 0) {
            $storage_location = explode(",", $request->get('storageLocation'));
            $transfers = $transfers->whereIn('materials.issue_storage_location', $storage_location);
        }

        if ($request->get('originGroup') != null) {
            $transfers = $transfers->whereIn('materials.origin_group_code', $request->get('originGroup'));
        }

        if ($request->get('transactionStatus') == 'Uploaded') {
            $transfers = $transfers->whereNotNull('flo_details.transfer');
        } elseif ($request->get('transactionStatus') == 'Not Uploaded') {
            $transfers = $transfers->whereNull('flo_details.transfer');

        }

        $transfers = $transfers->orderBy('flo_details.created_at', 'asc')
            ->select(
                'flo_details.material_number',
                'materials.material_description',
                'materials.issue_storage_location',
                'flo_details.quantity',
                db::raw('if(flos.shipment_schedule_id = 0, "Maedaoshi", destinations.destination_shortname) as destination'),
                db::raw('if(flo_details.transfer is not null, flo_details.transfer, "-") as transfer'),
                'flo_logs.created_at'
            )
            ->get();

        $response = array(
            'status' => true,
            'tableData' => $transfers,
        );
        return Response::json($response);
    }

    public function fetch(Request $request)
    {
        $inventory = Inventory::leftJoin('materials', 'materials.material_number', '=', 'inventories.material_number')
            ->leftJoin('origin_groups', 'origin_groups.origin_group_code', '=', 'materials.origin_group_code')
            ->select('inventories.plant', 'origin_groups.origin_group_name', 'inventories.material_number', 'materials.material_description', 'inventories.storage_location', 'inventories.quantity', 'inventories.updated_at');

        if ($request->get('plant') != null) {
            $inventory = $inventory->whereIn('plant', $request->get('plant'));
        }

        if ($request->get('origin_group') != null) {
            $inventory = $inventory->whereIn('materials.origin_group_code', $request->get('origin_group'));
        }

        if (strlen($request->get('material_number')) > 0) {
            $material_number = explode(",", $request->get('material_number'));
            $inventory = $inventory->whereIn('inventories.material_number', $material_number);
        }

        if (strlen($request->get('storage_location')) > 0) {
            $storage_location = explode(",", $request->get('storage_location'));
            $inventory = $inventory->whereIn('inventories.storage_location', $storage_location);
        }

        $inventoryTable = $inventory->orderBy('inventories.material_number', 'asc')->get();

        return DataTables::of($inventoryTable)->make(true);
    }

    public function downloadCompletion(Request $request)
    {
        $file_path = "/uploads/sap/completions/" . $request->get('referenceFile');
        $path = url($file_path);

        $response = array(
            'file_path' => $path,
        );
        return Response::json($response);
    }

    public function downloadTransfer(Request $request)
    {
        $file_path = "/uploads/sap/transfers/" . $request->get('referenceFile');
        $path = url($file_path);

        $response = array(
            'file_path' => $path,
        );
        return Response::json($response);
    }

    public function indexEndingStock()
    {
        return view('inventories.ending_stock');
    }

    public function fetchEndingStock(Request $request)
    {

        $prev_month = date('Y-m', strtotime('-4 months'));
        $month = date('Y-m', strtotime('+2 months'));

        if (strlen($request->get('month')) > 0) {
            $prev_month = date('Y-m', strtotime('-4 months', strtotime($request->get('month') . '-01')));
            $month = date('Y-m', strtotime('+2 months', strtotime($request->get('month') . '-01')));
        }

        $start = new DateTime($prev_month . '-01');
        $end = new DateTime($month . '-01');
        $interval = DateInterval::createFromDateString('1 month');
        $period = new DatePeriod($start, $interval, $end);

        $interval_month = [];
        foreach ($period as $dt) {
            $row = array();
            $row['month'] = $dt->format("Y-m");
            $row['end_month'] = date("M-y", strtotime('-1 months', strtotime($dt->format("Y-m") . '-01')));

            array_push($interval_month, $row);
        }

        $materials = Material::whereIn('category', ['FG', 'KD'])
            ->orderBy('category', 'ASC')
            ->orderBy('hpl', 'ASC')
            ->orderBy('material_description', 'ASC')
            ->get();

        $stock = db::table('first_inventories')
            ->where('stock_date', '>=', $prev_month . '-01')
            ->where('stock_date', '<=', $month . '-01')
            ->select(
                'stock_date',
                'material_number',
                db::raw('SUM(quantity) AS quantity')
            )
            ->groupBy(
                'stock_date',
                'material_number'
            )
            ->get();

        $response = array(
            'status' => true,
            'interval_month' => $interval_month,
            'materials' => $materials,
            'stock' => $stock,
        );
        return Response::json($response);

    }

    public function inputEndingStock(Request $request)
    {

        $month = date('Y-m', strtotime('+1 months', strtotime($request->get('month') . '-01')));
        $upload = $request->get('upload');
        $uploadRows = preg_split("/\r?\n/", $upload);

        $materials = db::table('materials')
            ->leftJoin('material_volumes', 'material_volumes.material_number', '=', 'materials.material_number')
            ->whereIn('materials.category', ['KD', 'FG'])
            ->select(
                'materials.material_number',
                'materials.material_description',
                'materials.category',
                'materials.hpl',
                'material_volumes.lot_completion'
            )
            ->get();

        $formatted_materials = [];
        for ($i = 0; $i < count($materials); $i++) {
            $formatted_materials[$materials[$i]->material_number] = $materials[$i];
        }

        DB::beginTransaction();
        $delete = db::table('first_inventories')
            ->where('stock_date', 'LIKE', '%' . $month . '%')
            ->delete();

        foreach ($uploadRows as $uploadRow) {
            $uploadColumn = preg_split("/\t/", $uploadRow);
            if (count($uploadColumn) == 3) {

                $material_number = $uploadColumn[0];
                $storage_location = $uploadColumn[1];
                $quantity = $uploadColumn[2];

                if (isset($formatted_materials[$material_number])) {
                    if ($formatted_materials[$material_number]->category == 'KD') {
                        $mod = $quantity % $formatted_materials[$material_number]->lot_completion;
                        $quantity = $quantity - $mod;
                    }
                }

                if ($quantity > 0) {
                    try {
                        $insert = db::table('first_inventories')
                            ->insert([
                                'stock_date' => $month . '-01',
                                'material_number' => $material_number,
                                'storage_location' => $storage_location,
                                'quantity' => $quantity,
                                'created_by' => Auth::id(),
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
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
            }

        }

        DB::commit();
        $response = array(
            'status' => true,
        );
        return Response::json($response);

    }

}
