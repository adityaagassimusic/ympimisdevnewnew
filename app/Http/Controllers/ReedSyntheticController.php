<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Inventory;
use App\KnockDown;
use App\KnockDownDetail;
use App\KnockDownLog;
use App\Material;
use App\MaterialPlantDataList;
use App\ReedApproval;
use App\ReedApprovalDetail;
use App\ReedInjectionOrder;
use App\ReedInjectionOrderDetail;
use App\ReedInjectionOrderList;
use App\ReedInjectionOrderLog;
use App\ReedLaserOrder;
use App\ReedLaserOrderList;
use App\ReedLaserOrderLog;
use App\ReedMasterChecksheet;
use App\ReedPackingOrder;
use App\ReedPackingOrderList;
use App\ReedPackingOrderLog;
use App\ReedStandardMeasurement;
use App\ReedTransactionLog;
use App\ReedWarehouseDelivery;
use App\ReedWarehouseReceive;
use App\StampHierarchy;
use App\TransactionCompletion;
use App\TransactionTransfer;
use Carbon\Carbon;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class ReedSyntheticController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexTransfer()
    {
        $title = "Transfer Reed Synthetic Material";
        $title_jp = " ";

        return view('reed_synthetic.transfer', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Reed Synthetic')->with('head', 'Reed Synthetic');
    }

    public function indexReedClosure()
    {
        $title = "Reed Synthetic Closure";
        $title_jp = " ";
        $location = "reed-synthetic";

        return view('reed_synthetic.final.closure', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'location' => $location,
        ))->with('page', 'Reed Synthetic')->with('head', 'Reed Synthetic');

    }

    public function indexPrintLabelIitem($material_number)
    {
        $material = MaterialPlantDataList::where('material_number', $material_number)->first();

        return view('reed_synthetic.final.label.label_desc', array(
            'material' => $material,
        ))->with('page', 'Reed Synthetic')->with('head', 'Reed Synthetic');

    }

    public function indexPrintLabelShipment($material_number)
    {
        $material = MaterialPlantDataList::where('material_number', $material_number)->first();
        $stamp = StampHierarchy::where('finished', $material_number)->first();

        return view('reed_synthetic.final.label.label_shipment', array(
            'material' => $material,
            'stamp' => $stamp,
        ))->with('page', 'Reed Synthetic')->with('head', 'Reed Synthetic');

    }

    public function indexReed()
    {
        $title = "Injection Reed Synthetic";
        $title_jp = " ";

        return view('reed_synthetic.injection.index_reed', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Reed')->with('head', 'Injection Reed');
    }

    public function indexFinalReed()
    {
        $title = "Final Process Reed Synthetic";
        $title_jp = " ";

        return view('reed_synthetic.final.index_packing', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Reed Synthetic')->with('head', 'Reed Synthetic');
    }

    public function indexInjectionOrder()
    {
        $title = "Create Injection Order";
        $title_jp = " ";

        return view('reed_synthetic.injection.create_injection_order', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Order')->with('head', 'Injection Order');
    }

    public function indexInjectionVerification()
    {
        $title = "Injection Verification";
        $title_jp = " ";

        return view('reed_synthetic.injection.injection_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Reed')->with('head', 'Injection Reed');
    }

    public function indexInjectionResinReceive()
    {
        $title = "Injection Resin Reception";
        $title_jp = " ";

        return view('reed_synthetic.injection.resin_reception', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Injection Reed')->with('head', 'Injection Reed');
    }

    public function indexInjectionReport($id)
    {

        $material = db::select("SELECT DISTINCT material_number, material_description FROM `reed_master_checksheets`
			WHERE process = 'INJECTION'
			AND location = 'INJECTION'
			ORDER BY material_description ASC");

        if ($id == 'approval') {
            $title = "Reed Synthetic Approval Report";
            $title_jp = " ";

            return view('reed_synthetic.injection.report.approval', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => $id,
                'material' => $material,
            ))->with('page', 'Reed Synthetic Report')->with('head', 'Reed Synthetic Report');
        } else if ($id == 'check-dimensi') {
            $title = "Check Dimensi Material Injection Reed Synthetic Report";
            $title_jp = " ";

            return view('reed_synthetic.injection.report.check_dimensi', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id' => $id,
                'material' => $material,
            ))->with('page', 'Reed Synthetic Report')->with('head', 'Reed Synthetic Report');
        }
    }

    public function indexMoldingVerification()
    {
        $title = "Setup Molding Verification";
        $title_jp = " ";

        return view('reed_synthetic.molding.molding_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Setup Molding Verification')->with('head', 'Setup Molding Verification');
    }

    public function indexApproval($location, $order_id, $employee_id)
    {

        $order = ReedInjectionOrder::where('order_id', $order_id)->first();
        $list = ReedInjectionOrderList::where('order_id', $order_id)->where('picking_list', 'MESIN INJEKSI')->first();
        $employee = EmployeeSync::where('employee_id', $employee_id)->first();
        $point_check = asset("/files/reed/point_check.png");
        $material_stardard = ReedStandardMeasurement::where('material_number', $order->material_number)->first();
        $stardard = ReedStandardMeasurement::get();

        if ($location == 'molding') {
            $title = "Pre Approval Reed Synthetic";
            $title_jp = " ";

            return view('reed_synthetic.molding.approval', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'order' => $order,
                'list' => $list,
                'employee' => $employee,
                'point_check' => $point_check,
                'material_stardard' => $material_stardard,
                'stardard' => $stardard,
            ))->with('page', 'Pre Approval Reed Synthetic')->with('head', 'Pre Approval Reed Synthetic');

        } else if ($location == 'injection') {
            $title = "Check Dimensi Reed Synthetic";
            $title_jp = " ";

            return view('reed_synthetic.injection.check_dimensi', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'order' => $order,
                'list' => $list,
                'employee' => $employee,
                'point_check' => $point_check,
                'material_stardard' => $material_stardard,
                'stardard' => $stardard,
            ))->with('page', 'Approval Reed Synthetic')->with('head', 'Approval Reed Synthetic');
        }

    }

    public function indexDelivery($loc)
    {

        if ($loc == 'injection') {
            $title = "After Injection Delivery";
            $title_jp = "";

            return view('reed_synthetic.injection.after_injection_delivery', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'process' => strtoupper($loc),
            ))->with('page', 'Injection')->with('head', 'Delivery');
        }

        if ($loc == 'laser_marking') {
            $title = "After Laser Marking Delivery";
            $title_jp = "";

            return view('reed_synthetic.laser.delivery', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'process' => 'LASER MARKING',
            ))->with('page', 'Laser Marking')->with('head', 'Delivery');
        }

        if ($loc == 'trimming') {
            $title = "After Trimming Delivery";
            $title_jp = "";

            return view('reed_synthetic.laser.delivery', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'process' => strtoupper($loc),
            ))->with('page', 'Trimming')->with('head', 'Delivery');
        }

        if ($loc == 'annealing') {
            $title = "After Annealing Delivery";
            $title_jp = "";

            return view('reed_synthetic.laser.delivery', array(
                'title' => $title,
                'title_jp' => $title_jp,
                'process' => strtoupper($loc),
            ))->with('page', 'Annealing')->with('head', 'Delivery');
        }

    }

    public function indexLaserVerification()
    {
        $title = "Laser Marking Verification";
        $title_jp = " ";

        return view('reed_synthetic.laser.laser_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Laser Marking Verification')->with('head', 'Laser Marking Verification');
    }

    public function indexTrimmingVerification()
    {
        $title = "Trimming Verification";
        $title_jp = " ";

        return view('reed_synthetic.laser.trimming_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Annealing Verification')->with('head', 'Annealing Verification');
    }

    public function indexAnnealingVerification()
    {
        $title = "Annealing Verification";
        $title_jp = " ";

        return view('reed_synthetic.laser.annealing_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Annealing Verification')->with('head', 'Annealing Verification');
    }

    public function indexCaseSuportPaper()
    {
        $title = "Case & Suport Paper Verification";
        $title_jp = " ";

        return view('reed_synthetic.final.case_support', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Case & Suport Paper Verification')->with('head', 'Case & Suport Paper Verification');
    }

    public function indexPickingVerification()
    {
        $title = "Picking Verification";
        $title_jp = " ";

        return view('reed_synthetic.final.picking_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Picking Verification')->with('head', 'Picking Verification');
    }

    public function indexPackingVerification()
    {
        $title = "Packing Verification";
        $title_jp = " ";

        return view('reed_synthetic.final.packing_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Packing Verification')->with('head', 'Packing Verification');
    }

    public function indexPackingOrder()
    {
        $title = "Create Packing Order";
        $title_jp = " ";

        return view('reed_synthetic.final.create_packing_order', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Create Packing Order')->with('head', 'Create Packing Order');
    }

    public function indexStoreVerification()
    {
        $title = "Store Verification";
        $title_jp = " ";

        return view('reed_synthetic.warehouse.store_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Store Verification')->with('head', 'Store Verification');
    }

    public function indexLabelVerification()
    {
        $title = "Label Verification";
        $title_jp = " ";

        return view('reed_synthetic.warehouse.label_verification', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Label Verification')->with('head', 'Label Verification');
    }

    public function indexResinReceive()
    {
        $title = "Warehouse Resin Reception";
        $title_jp = " ";

        $material = MaterialPlantDataList::where('material_number', 'VEW1570')->get();

        $quantity = 0;
        $inventory = Inventory::where('material_number', 'VEW1570')
            ->where('storage_location', 'MSTK')
            ->first();
        if ($inventory) {
            $quantity = $inventory->quantity;
        }

        $now = date('Y-m-d H:i:s');

        return view('reed_synthetic.warehouse.receive', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'now' => $now,
            'inventory' => $quantity,
            'materials' => $material,
        ))->with('page', 'Resin Receive')->with('head', 'Resin Receive');
    }

    public function indexResinDelivery()
    {
        $title = "Resin Delivery";
        $title_jp = " ";

        return view('reed_synthetic.warehouse.delivery', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Resin Delivery')->with('head', 'Resin Delivery');
    }

    public function indexPreApprovalPdf($id)
    {

        $approval = ReedApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'reed_approvals.operator_id')
            ->where('reed_approvals.id', $id)
            ->select('reed_approvals.*', db::raw('date(reed_approvals.created_at) AS date'), db::raw('concat(SPLIT_STRING(employee_syncs.`name`, " ", 1), " ", SPLIT_STRING(employee_syncs.`name`, " ", 2)) AS `name`'))
            ->first();

        $detail = db::select("SELECT approval_id, AVG(length) AS length, AVG(diameter) AS diameter, AVG(thickness) AS thickness, AVG(weight) AS weight FROM `reed_approval_details`
			WHERE approval_id = " . $id . "
			GROUP BY approval_id");

        $measurement = ReedStandardMeasurement::where('material_number', $approval->material_number)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('reed_synthetic.injection.report.first_approval_pdf', array(
            'approval' => $approval,
            'detail' => $detail,
            'measurement' => $measurement,
        ));
        return $pdf->stream("first_approval_pdf.pdf");
    }

    public function indexApprovalPdf($id)
    {
        $approval = ReedApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'reed_approvals.operator_id')
            ->where('reed_approvals.id', $id)
            ->select('reed_approvals.*', db::raw('date(reed_approvals.created_at) AS date'), db::raw('concat(SPLIT_STRING(employee_syncs.`name`, " ", 1), " ", SPLIT_STRING(employee_syncs.`name`, " ", 2)) AS `name`'))
            ->first();

        $detail = db::select("SELECT approval_id, AVG(length) AS length, AVG(diameter) AS diameter, AVG(thickness) AS thickness, AVG(weight) AS weight FROM `reed_approval_details`
			WHERE approval_id = " . $id . "
			GROUP BY approval_id");

        $measurement = ReedStandardMeasurement::where('material_number', $approval->material_number)->first();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('reed_synthetic.injection.report.approval_pdf', array(
            'approval' => $approval,
            'detail' => $detail,
            'measurement' => $measurement,
        ));
        return $pdf->stream("approval_pdf.pdf");
    }

    public function fetchTransfer(Request $request)
    {
        $employee_id = $request->get('employee_id');
        $kanban = $request->get('kanban');

        DB::beginTransaction();
        if (substr($kanban, 0, 2) == 'RS') {
            $order_detail = ReedInjectionOrderDetail::where('kanban', $kanban)->first();

            if ($order_detail) {
                if ($order_detail->remark == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kanban belum dicompletion',
                    );
                    return Response::json($response);
                }

                if ($order_detail->remark == 2) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kanban sudah ditransfer',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban tidak terdaftar',
                );
                return Response::json($response);
            }

            $employee = EmployeeSync::where('employee_id', $employee_id)->first();
            $mpdl = MaterialPlantDataList::where('material_number', $order_detail->material_number)->first();
            $inventory = Inventory::where('plant', '8190')
                ->where('material_number', $order_detail->material_number)
                ->where('storage_location', $mpdl->storage_location)
                ->first();

            try {

                $transfer = new TransactionTransfer([
                    'plant' => '8190',
                    'serial_number' => $kanban,
                    'material_number' => $order_detail->material_number,
                    'issue_plant' => '8190',
                    'issue_location' => $mpdl->storage_location,
                    'receive_plant' => '8190',
                    'receive_location' => 'VN91',
                    'transaction_code' => 'MB1B',
                    'movement_type' => '9I3',
                    'quantity' => $order_detail->quantity,
                    'created_by' => Auth::id(),
                ]);
                $transfer->save();

                // YMES TRANSFER NEW
                $category = 'goods_movement';
                $function = 'fetchTransfer';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $kanban;
                $serial_number = null;
                $material_number = $order_detail->material_number;
                $material_description = $mpdl->material_description;
                $issue_location = $mpdl->storage_location;
                $receive_location = 'VN91';
                $quantity = $order_detail->quantity;
                $remark = 'MIRAI';
                $created_by = strtoupper($employee_id);
                $created_by_name = $employee->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                $tr_log = new ReedTransactionLog([
                    'category' => 'completion',
                    'material_number' => $order_detail->material_number,
                    'issue_location' => $mpdl->storage_location,
                    'receive_location' => 'VN91',
                    'movement_type' => '9I3',
                    'transacted_by' => strtoupper($employee_id),
                    'quantity' => $order_detail->quantity,
                    'created_by' => Auth::id(),
                ]);
                $tr_log->save();

                if ($inventory) {
                    $inventory->quantity = $inventory->quantity - $order_detail->quantity;
                    $inventory->save();
                }

                $order_detail->remark = 2;
                $order_detail->picking_by = $employee_id;
                $order_detail->picking_at = date('Y-m-d H:i:s');
                $order_detail->save();

                DB::commit();

                $response = array(
                    'status' => true,
                    'message' => 'Transfer berhasil',
                );
                return Response::json($response);

            } catch (Exception $e) {
                DB::rollback();

                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

        } else {

            $inventory = db::table('reed_inventories')
                ->where('kanban', '=', $kanban)
                ->first();

            if ($inventory) {
                if ($inventory->lot == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kanban belum dicompletion',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban tidak terdaftar',
                );
                return Response::json($response);
            }

            $mpdl = MaterialPlantDataList::where('material_number', $inventory->material_number)->first();
            $employee = EmployeeSync::where('employee_id', $employee_id)->first();

            $issue_location = $mpdl->storage_location;
            $receive_location = 'VN91';
            if (str_contains($mpdl->material_description, 'LASER')) {
                $receive_location = 'VNA0';
            }

            $inventory_issue = Inventory::where('plant', '8190')
                ->where('material_number', $inventory->material_number)
                ->where('storage_location', $issue_location)
                ->first();

            $inventory_receive = Inventory::where('plant', '8190')
                ->where('material_number', $inventory->material_number)
                ->where('storage_location', $receive_location)
                ->first();

            $tr_log = new ReedTransactionLog([
                'category' => 'completion',
                'material_number' => $inventory->material_number,
                'issue_location' => $issue_location,
                'receive_location' => $receive_location,
                'movement_type' => '9I3',
                'transacted_by' => strtoupper($employee_id),
                'quantity' => $inventory->lot,
                'created_by' => Auth::id(),
            ]);

            $transfer = new TransactionTransfer([
                'plant' => '8190',
                'serial_number' => $kanban,
                'material_number' => $inventory->material_number,
                'issue_plant' => '8190',
                'issue_location' => $issue_location,
                'receive_plant' => '8190',
                'receive_location' => $receive_location,
                'transaction_code' => 'MB1B',
                'movement_type' => '9I3',
                'quantity' => $inventory->lot,
                'created_by' => Auth::id(),
            ]);

            try {

                if ($inventory_issue) {
                    $inventory_issue->quantity = $inventory_issue->quantity - $inventory->lot;
                    $inventory_issue->save();
                }

                if ($inventory_receive) {
                    $inventory_receive->quantity = $inventory_receive->quantity + $inventory->lot;
                    $inventory_receive->save();
                }

                $reed_inventory = db::table('reed_inventories')
                    ->where('kanban', '=', $kanban)
                    ->update([
                        'lot' => 0,
                        'created_by' => Auth::id(),
                        'remark' => 'transfer',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                $tr_log->save();
                $transfer->save();

                // YMES TRANSFER NEW
                $category = 'goods_movement';
                $function = 'fetchTransfer';
                $action = 'goods_movement';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $kanban;
                $serial_number = null;
                $material_number = $inventory->material_number;
                $material_description = $mpdl->material_description;
                $issue_location = $issue_location;
                $receive_location = $receive_location;
                $quantity = $inventory->lot;
                $remark = 'MIRAI';
                $created_by = strtoupper($employee_id);
                $created_by_name = $employee->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->goods_movement(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Transfer berhasil',
                );
                return Response::json($response);

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

    public function fetchInventory(Request $request)
    {
        $proc = $request->get('proc');
        $material_number = $request->get('material_number');
        $material = ReedMasterChecksheet::where('material_number', $material_number)->first();

        if (!$material) {
            $response = array(
                'status' => false,
                'message' => 'Data store tidak ditemukan',
            );
            return Response::json($response);
        }

        if ($proc == 'INJECTION') {
            $inventory = Inventory::where('material_number', $material_number)->get();

            $response = array(
                'status' => true,
                'material' => $material,
                'inventory' => $inventory,
            );
            return Response::json($response);

        } else {
            $inventory = db::connection('mysql2')
                ->table('inventories')
                ->where('material_number', '=', $material_number)
                ->select('material_number', db::raw('SUM(lot) AS quantity'))
                ->groupBy('material_number')
                ->get();

            $response = array(
                'status' => true,
                'material' => $material,
                'inventory' => $inventory,
            );
            return Response::json($response);
        }
    }

    public function fetchCheckKanban(Request $request)
    {

        $proc = $request->get('proc');
        $kanban = $request->get('kanban');

        if ($proc == 'INJECTION') {
            $detail = ReedInjectionOrderDetail::where('kanban', '=', $kanban)->first();

            if (!$detail) {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban tidak terdaftar',
                );
                return Response::json($response);
            }

            if ($detail->remark != 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban sudah pernah ditransaksikan',
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'material' => $detail,
            );
            return Response::json($response);
        } else {

            $inventory = db::table('reed_inventories')
                ->where('kanban', '=', $kanban)
                ->first();

            if ($inventory) {
                if ($inventory->lot > 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kanban ' . strtoupper($kanban) . ', Jumlah pada inventory masih tersedia ' . $inventory->lot . ' dikarenakan transfer belum dilakukan',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban tidak terdaftar',
                );
                return Response::json($response);
            }

            $response = array(
                'status' => true,
                'material' => $inventory,
            );
            return Response::json($response);
        }
    }

    public function scanDelivery(Request $request)
    {
        $employee_id = $request->get('employee_id');
        $proc = $request->get('proc');
        $kanban = $request->get('kanban');

        if ($proc == 'INJECTION') {
            $order_detail = ReedInjectionOrderDetail::where('kanban', $kanban)->first();

            if ($order_detail) {

                $order = ReedInjectionOrder::where('order_id', $order_detail->order_id)->first();
                if ($order->remark == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Proses injeksi belum selesai',
                    );
                    return Response::json($response);
                }

                $mpdl = MaterialPlantDataList::where('material_number', $order_detail->material_number)->first();
                $storage_location = $mpdl->storage_location;

                DB::beginTransaction();
                if ($order_detail->remark == 1) {
                    $response = array(
                        'status' => false,
                        'message' => 'Delivery sudah dilakukan',
                    );
                    return Response::json($response);
                }

                $order_detail->remark = 1;
                $order_detail->delivered_by = $employee_id;
                $order_detail->delivered_at = date('Y-m-d H:i:s');

                $log = new ReedInjectionOrderLog([
                    'order_id' => $order_detail->id,
                    'kanban' => $kanban,
                    'material_number' => $order_detail->material_number,
                    'material_description' => $order_detail->material_description,
                    'location' => 'AFTER INJECTION',
                    'quantity' => $order_detail->quantity,
                    'picked_by' => strtoupper($employee_id),
                    'picked_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::id(),
                ]);

                $tr_log = new ReedTransactionLog([
                    'category' => 'completion',
                    'material_number' => $order_detail->material_number,
                    'issue_location' => $storage_location,
                    'movement_type' => '101',
                    'transacted_by' => strtoupper($employee_id),
                    'quantity' => $order_detail->quantity,
                    'created_by' => Auth::id(),
                ]);

                $transaction_completion = new TransactionCompletion([
                    'serial_number' => $kanban,
                    'material_number' => $order_detail->material_number,
                    'issue_plant' => '8190',
                    'issue_location' => $storage_location,
                    'quantity' => $order_detail->quantity,
                    'movement_type' => '101',
                    'created_by' => Auth::id(),
                ]);

                $inventory = Inventory::where('plant', '8190')
                    ->where('material_number', $order_detail->material_number)
                    ->where('storage_location', $storage_location)
                    ->first();

                if ($inventory) {
                    $inventory->quantity = $inventory->quantity + $order_detail->quantity;
                } else {
                    $inventory = new Inventory([
                        'plant' => '8190',
                        'material_number' => $order_detail->material_number,
                        'storage_location' => $storage_location,
                        'quantity' => $order_detail->quantity,
                    ]);
                }

                $employee = EmployeeSync::where('employee_id', strtoupper($employee_id))->first();

                try {

                    $log->save();
                    $tr_log->save();
                    $inventory->save();
                    $order_detail->save();
                    $transaction_completion->save();

                    // YMES COMPLETION NEW
                    $category = 'production_result';
                    $function = 'scanDelivery';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $kanban;
                    $serial_number = null;
                    $material_number = $order_detail->material_number;
                    $material_description = $mpdl->material_description;
                    $issue_location = $mpdl->storage_location;
                    $mstation = 'W' . $mpdl->mrpc . 'S10';
                    $quantity = $order_detail->quantity;
                    $remark = 'MIRAI';
                    $created_by = strtoupper($employee_id);
                    $created_by_name = $employee->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END

                    DB::commit();

                    $response = array(
                        'status' => true,
                        'order_detail' => $order_detail,
                        'message' => 'Delivery berhasil',
                    );
                    return Response::json($response);

                } catch (Exception $e) {
                    DB::rollback();

                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Kanban finish injection tidak ditemukan',
                );
                return Response::json($response);
            }

        } else {

            $inventory = db::table('reed_inventories')
                ->where('kanban', '=', $kanban)
                ->first();

            if ($inventory) {
                if ($inventory->lot > 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'Kanban ' . strtoupper($kanban) . ', Jumlah pada inventory masih tersedia ' . $inventory->lot . ' dikarenakan transfer belum dilakukan',
                    );
                    return Response::json($response);
                }
            }

            $order = ReedLaserOrder::where('kanban', $kanban)->first();
            if ($order->remark == 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Proses ' . strtolower($proc) . ' belum selesai',
                );
                return Response::json($response);
            }

            $employee = EmployeeSync::where('employee_id', $employee_id)->first();
            $mpdl = MaterialPlantDataList::where('material_number', $order->material_number)->first();
            $storage_location = $mpdl->storage_location;

            DB::beginTransaction();

            $tr_log = new ReedTransactionLog([
                'category' => 'completion',
                'material_number' => $order->material_number,
                'issue_location' => $storage_location,
                'movement_type' => '101',
                'transacted_by' => strtoupper($employee_id),
                'quantity' => 100,
                'created_by' => Auth::id(),
            ]);

            $inventory = Inventory::where('plant', '8190')
                ->where('material_number', $order->material_number)
                ->where('storage_location', $storage_location)
                ->first();

            if ($inventory) {
                $inventory->quantity = $inventory->quantity + $order->quantity;
            } else {
                $inventory = new Inventory([
                    'plant' => '8190',
                    'material_number' => $order->material_number,
                    'storage_location' => $storage_location,
                    'quantity' => $order->quantity,
                ]);
            }

            $transaction_completion = new TransactionCompletion([
                'serial_number' => $kanban,
                'material_number' => $order->material_number,
                'issue_plant' => '8190',
                'issue_location' => $storage_location,
                'quantity' => $order->quantity,
                'movement_type' => '101',
                'created_by' => Auth::id(),
            ]);

            try {
                $tr_log->save();
                $inventory->save();
                $transaction_completion->save();
                $reed_inventory = db::table('reed_inventories')
                    ->where('kanban', '=', $kanban)
                    ->update([
                        'lot' => $order->quantity,
                        'created_by' => Auth::id(),
                        'remark' => 'completion',
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                // YMES COMPLETION NEW
                $category = 'production_result';
                $function = 'scanDelivery';
                $action = 'production_result';
                $result_date = date('Y-m-d H:i:s');
                $slip_number = $kanban;
                $serial_number = null;
                $material_number = $order->material_number;
                $material_description = $mpdl->material_description;
                $issue_location = $mpdl->storage_location;
                $mstation = 'W' . $mpdl->mrpc . 'S10';
                $quantity = $order->quantity;
                $remark = 'MIRAI';
                $created_by = strtoupper($employee_id);
                $created_by_name = $employee->name;
                $synced = null;
                $synced_by = null;

                app(YMESController::class)->production_result(
                    $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                // YMES END

                DB::commit();

                $response = array(
                    'status' => true,
                    'order_detail' => $order,
                    'message' => 'Delivery berhasil',
                );
                return Response::json($response);
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

    public function fetchInjectionReportDetail(Request $request)
    {
        $id = $request->get('id');

        $approval = ReedApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'reed_approvals.operator_id')
            ->where('reed_approvals.id', $id)
            ->select('reed_approvals.*', db::raw('date(reed_approvals.created_at) AS date'), db::raw('concat(SPLIT_STRING(employee_syncs.`name`, " ", 1), " ", SPLIT_STRING(employee_syncs.`name`, " ", 2)) AS `name`'))
            ->first();

        $detail = ReedApprovalDetail::where('approval_id', $id)->get();
        $measurement = ReedStandardMeasurement::where('material_number', $approval->material_number)->first();
        $photo = asset("/files/reed/parameter/" . $approval->parameter_photo);

        $response = array(
            'status' => true,
            'approval' => $approval,
            'detail' => $detail,
            'measurement' => $measurement,
            'photo' => $photo,
        );
        return Response::json($response);

    }

    public function fetchInjectionReport(Request $request)
    {

        $datefrom;
        $dateto;

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = $request->get('datefrom');
        } else {
            $datefrom = date('Y-m-d', strtotime('-30 day'));
        }

        if (strlen($request->get('dateto')) > 0) {
            $dateto = $request->get('dateto');
        } else {
            $dateto = date('Y-m-d');
        }

        $material = '';
        if ($request->get('material') != null) {
            $materials = $request->get('material');
            for ($i = 0; $i < count($materials); $i++) {
                $material = $material . "'" . $materials[$i] . "'";
                if ($i != (count($materials) - 1)) {
                    $material = $material . ',';
                }
            }
            $material = " AND app.material_number IN (" . $material . ") ";
        }

        $status = '';
        if (strlen($request->get('status')) > 0) {
            $status = $request->get('status');
            $status = " AND app.`status` = '" . $status . "' ";
        }

        $location = '';
        if ($request->get('id') == 'approval') {
            $location = $request->get('id');
            $location = " AND app.`location` = 'molding' ";
        } else if ($request->get('id') == 'check-dimensi') {
            $location = $request->get('id');
            $location = " AND app.`location` = 'injection' ";
        }

        $data = db::select("SELECT app.*, concat(SPLIT_STRING(emp.`name`, ' ', 1), ' ', SPLIT_STRING(emp.`name`, ' ', 2)) as `name` FROM reed_approvals app
			LEFT JOIN employee_syncs emp ON app.operator_id = emp.employee_id
			WHERE date(app.created_at) BETWEEN '" . $datefrom . "' AND '" . $dateto . "'"
            . $material
            . $status
            . $location
            . " ORDER BY app.created_at DESC");

        return DataTables::of($data)
            ->addColumn('operator', function ($data) {
                return '<span>' . $data->operator_id . '<br>' . $data->name . '</span>';
            })
            ->addColumn('status_name', function ($data) {
                if ($data->status == 1) {
                    return 'OK';
                } else {
                    return 'NG';
                }
            })
            ->addColumn('approval', function ($data) {
                if ($data->status == 1) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-danger" onClick="showApproval(id)" id="' . $data->id . '"><i class="fa fa-print"></i>&nbsp;&nbsp;Approval</a>';
                } else {
                    return '<span><i class="fa fa-minus"></i></span>';
                }
            })
            ->addColumn('pre_approval', function ($data) {
                if ($data->status == 1) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-default" onClick="showPreApproval(id)" id="' . $data->id . '"><i class="fa fa-print"></i>&nbsp;&nbsp;First Approval</a>';
                } else {
                    return '<span><i class="fa fa-minus"></i></span>';
                }
            })
            ->addColumn('cdm', function ($data) {
                if ($data->status == 1) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-default" onClick="showCdm(id)" id="' . $data->id . '"><i class="fa fa-print"></i>&nbsp;&nbsp;CDM</a>';
                } else {
                    return '<span><i class="fa fa-minus"></i></span>';
                }
            })
            ->addColumn('detail', function ($data) {
                return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-primary" onClick="showDetail(id)" id="' . $data->id . '"><i class="fa fa-eye"></i>&nbsp;&nbsp;Detail</a>';
            })
            ->rawColumns([
                'operator' => 'operator',
                'status_name' => 'status_name',
                'approval' => 'approval',
                'pre_approval' => 'pre_approval',
                'cdm' => 'cdm',
                'detail' => 'detail',
            ])
            ->make(true);
    }

    public function fetchSubmitApprovalNg(Request $request)
    {
        $date = $request->input('date');
        $sample = $request->input('sample');
        $location = $request->input('location');
        $process = $request->input('process');
        $order_id = $request->input('order_id');
        $employee_id = $request->input('employee_id');
        $machine = $request->input('machine');
        $resin = $request->input('resin');
        $lot = $request->input('lot');
        $parameter = $request->input('parameter');

        $order = ReedInjectionOrder::where('order_id', $order_id)->first();

        try {
            $directory = 'files\reed\parameter';
            $file = $request->file('file_datas');
            $original = $file->getClientOriginalName();
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename = md5($order_id . date('YmdHisa')) . '.' . $extension;
            $file->move($directory, $filename);

            $approval = new ReedApproval([
                'order_id' => $order_id,
                'material_number' => $order->material_number,
                'material_description' => $order->material_description,
                'status' => 0,
                'operator_id' => $employee_id,
                'location' => $location,
                'process' => $process,
                'mesin' => $machine,
                'resin' => $resin,
                'parameter' => $parameter,
                'parameter_photo' => $filename,
                'lot_resin' => $lot,
                'remark' => 'approval',
                'created_by' => Auth::id(),
            ]);
            $approval->save();

            $response = array(
                'status' => true,
                'message' => 'Pre Approval berhasil disimpan',
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

    public function fetchSubmitApproval(Request $request)
    {
        $date = $request->input('date');
        $sample = $request->input('sample');
        $location = $request->input('location');
        $process = $request->input('process');
        $order_id = $request->input('order_id');
        $employee_id = $request->input('employee_id');
        $machine = $request->input('machine');
        $resin = $request->input('resin');
        $lot = $request->input('lot');

        $parameter = $request->input('parameter');
        $status = explode(',', $request->input('status'));
        $status_approval = 1;
        for ($i = 0; $i < count($status); $i++) {
            if ($status[$i] == 'NG') {
                $status_approval = 0;
            }
        }

        $length = explode(',', $request->input('length'));
        $diameter = explode(',', $request->input('diameter'));
        $thickness = explode(',', $request->input('thickness'));
        $weight = explode(',', $request->input('weight'));

        $order = ReedInjectionOrder::where('order_id', $order_id)->first();

        try {
            $directory = 'files\reed\parameter';
            $file = $request->file('file_datas');
            $original = $file->getClientOriginalName();
            $extension = pathinfo($original, PATHINFO_EXTENSION);
            $filename = md5($order_id . date('YmdHisa')) . '.' . $extension;
            $file->move($directory, $filename);

            $approval = new ReedApproval([
                'order_id' => $order_id,
                'material_number' => $order->material_number,
                'material_description' => $order->material_description,
                'status' => $status_approval,
                'operator_id' => $employee_id,
                'location' => $location,
                'process' => $process,
                'mesin' => $machine,
                'resin' => $resin,
                'parameter' => $parameter,
                'parameter_photo' => $filename,
                'lot_resin' => $lot,
                'remark' => 'approval',
                'created_by' => Auth::id(),
            ]);
            $approval->save();

            $approval_id = $approval->id;
            $shot = 1;
            for ($i = 0; $i < $sample; $i++) {
                $detail = new ReedApprovalDetail([
                    'approval_id' => $approval_id,
                    'shot' => $shot++,
                    'length' => $length[$i],
                    'diameter' => $diameter[$i],
                    'thickness' => $thickness[$i],
                    'weight' => $weight[$i],
                    'created_by' => Auth::id(),
                ]);
                $detail->save();
            }

            $order->operator_molding_id = $employee_id;
            if ($status_approval == 1) {
                $order->setup_molding = 1;
            }
            $order->save();

            $response = array(
                'status' => true,
                'message' => 'Pre Approval berhasil disimpan',
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

    public function fetchSubmitCdm(Request $request)
    {
        $location = $request->get('location');
        $process = $request->get('process');
        $date = $request->get('date');
        $sample = $request->get('sample');
        $order_id = $request->get('order_id');
        $employee_id = $request->get('employee_id');
        $machine = $request->get('machine');
        $resin = $request->get('resin');
        $lot = $request->get('lot');
        $first = $request->get('awal');
        $mid = $request->get('tengah');
        $length = $request->get('length');
        $diameter = $request->get('diameter');
        $thickness = $request->get('thickness');
        $weight = $request->get('weight');

        $order = ReedInjectionOrder::where('order_id', $order_id)->first();

        try {
            $approval = new ReedApproval([
                'order_id' => $order_id,
                'material_number' => $order->material_number,
                'material_description' => $order->material_description,
                'status' => 1,
                'operator_id' => $employee_id,
                'location' => $location,
                'process' => $process,
                'mesin' => $machine,
                'resin' => $resin,
                'lot_resin' => $lot,
                'created_by' => Auth::id(),
            ]);
            $approval->save();

            $approval_id = $approval->id;
            for ($i = 0; $i < count($first); $i++) {
                $detail = new ReedApprovalDetail([
                    'approval_id' => $approval_id,
                    'shot' => 'Awal',
                    'length' => $first[$i]['length'],
                    'diameter' => $first[$i]['diameter'],
                    'thickness' => $first[$i]['thickness'],
                    'weight' => $first[$i]['weight'],
                    'created_by' => Auth::id(),
                ]);
                $detail->save();
            }

            for ($i = 0; $i < count($mid); $i++) {
                $detail = new ReedApprovalDetail([
                    'approval_id' => $approval_id,
                    'shot' => 'Tengah',
                    'length' => $mid[$i]['length'],
                    'diameter' => $mid[$i]['diameter'],
                    'thickness' => $mid[$i]['thickness'],
                    'weight' => $mid[$i]['weight'],
                    'created_by' => Auth::id(),
                ]);
                $detail->save();
            }

            $shot = 1;
            for ($i = 0; $i < $sample; $i++) {
                $detail = new ReedApprovalDetail([
                    'approval_id' => $approval_id,
                    'shot' => $shot++,
                    'length' => $length[$i],
                    'diameter' => $diameter[$i],
                    'thickness' => $thickness[$i],
                    'weight' => $weight[$i],
                    'created_by' => Auth::id(),
                ]);
                $detail->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Approval berhasil disimpan',
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

    public function fetchInjectionMaterial(Request $request)
    {
        $target = db::select("SELECT DISTINCT material_number, material_description FROM `reed_master_checksheets`
			WHERE process = 'INJECTION'
			AND location = 'INJECTION'
			ORDER BY material_description ASC");

        $response = array(
            'status' => true,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fetchPackingMaterial(Request $request)
    {
        $target = db::select("SELECT DISTINCT material_number, material_description FROM `reed_master_checksheets`
			WHERE process = 'PICKING'
			AND location = 'PACKING'
			ORDER BY material_number ASC");

        $response = array(
            'status' => true,
            'target' => $target,
        );
        return Response::json($response);
    }

    public function fetchInjectionResinReceive(Request $request)
    {

        $data = ReedWarehouseDelivery::orderBy('request_at', 'DESC')->limit(100)->get();

        return DataTables::of($data)
            ->addColumn('action', function ($data) {
                if ($data->remark == 1) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-primary" onClick="reception(id)" id="' . $data->id . '"><i class="fa fa-sign-in"></i>&nbsp;&nbsp;Receive</a>';
                } else {
                    return '<span><i class="fa fa-minus"></i></span>';
                }
            })
            ->addColumn('label', function ($data) {
                if ($data->remark == 0) {
                    return '<span class="label label-default">Requested</span>';
                } else if ($data->remark == 1) {
                    return '<span class="label label-info">Delivered</span>';
                } else if ($data->remark == 2) {
                    return '<span class="label label-success">Finished</span>';
                }
            })
            ->rawColumns([
                'action' => 'action',
                'label' => 'label',
            ])
            ->make(true);
    }

    public function fetchPrintOther($material_number)
    {

        $reed = ReedMasterChecksheet::where('material_number', $material_number)
            ->where('process', 'PICKING')
            ->first();

        $path;
        if ($reed->remark == 'SINGLE') {
            if (str_contains($reed->material_description, 'TSR')) {
                $path = '/files/reed/label/07kg.pdf';
            } else {
                $path = '/files/reed/label/05kg.pdf';
            }
        } else {
            $path = '/files/reed/label/12kg.pdf';
        }

        $file_path = asset($path);

        $response = array(
            'status' => true,
            'file_path' => $file_path,
        );
        return Response::json($response);
    }

    public function fetchResinReceive(Request $request)
    {

        $data = db::select("SELECT receive_date, material_number, material_description, SUM(quantity) AS quantity, SUM(bag_quantity) AS bag_quantity, MAX(print_status) As print_status FROM reed_warehouse_receives
			GROUP BY receive_date, material_number, material_description
			ORDER BY receive_date DESC
			LIMIT 100");

        return DataTables::of($data)
            ->addColumn('print', function ($data) {
                if ($data->print_status == 0) {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-primary" onClick="print(id)" id="' . $data->receive_date . '"><i class="fa fa-print"></i>&nbsp;&nbsp;PRINT</a>';
                } else {
                    return '<a href="javascript:void(0)" style="padding-top: 2%; padding-bottom: 2%; margin-top: 2%; margin-bottom: 2%;" class="btn btn-sm btn-default" onClick="print(id)" id="' . $data->receive_date . '"><i class="fa fa-print"></i>&nbsp;&nbsp;REPRINT</a>';
                }
            })
            ->rawColumns([
                'print' => 'print',
            ])
            ->make(true);
    }

    public function fetchInjectionPickingList(Request $request)
    {

        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $process = $request->get('proses');

        $order = ReedInjectionOrder::where('order_id', $order_id)
            ->where('remark', '<', 1)
            ->first();

        if ($order) {
            $data = ReedInjectionOrderList::where('order_id', $order_id)
                ->where('location', $location)
                ->get();

            $response = array(
                'status' => true,
                'order' => $order,
                'data' => $data,
            );
            return Response::json($response);

        } else {
            $response = array(
                'status' => false,
                'message' => 'Order ID tidak ditemukan',
            );
            return Response::json($response);
        }
    }

    public function fetchLaserPickingList(Request $request)
    {

        $kanban = $request->get('kanban');
        $location = $request->get('location');
        $process = $request->get('proses');

        $order = ReedLaserOrder::where('kanban', $kanban)
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($order) {

            if ($order->remark == '0') {
                $data = ReedLaserOrderList::where('order_id', $order->id)
                    ->where('location', strtoupper($location))
                    ->get();

                $response = array(
                    'status' => true,
                    'order' => $order,
                    'data' => $data,
                );
                return Response::json($response);
            } elseif ($order->remark == '1') {
                $material_number = substr($kanban, 4, 7);

                $checksheet = ReedMasterChecksheet::where('material_number', $material_number)
                    ->where('process', $process)
                    ->orderBy('picking_queue')
                    ->get();

                if (count($checksheet) > 0) {

                    $last_id;
                    try {
                        $new_order = new ReedLaserOrder([
                            'kanban' => $kanban,
                            'material_number' => $material_number,
                            'material_description' => $checksheet[0]->material_description,
                            'quantity' => $checksheet[0]->lot,
                            'hako' => 1,
                            'created_by' => Auth::id(),
                        ]);
                        $new_order->save();
                        $last_id = $new_order->id;

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }

                    for ($i = 0; $i < count($checksheet); $i++) {
                        try {
                            $order_list = new ReedLaserOrderList([
                                'order_id' => $last_id,
                                'kanban' => $kanban,
                                'material_number' => $checksheet[$i]->material_picking,
                                'material_description' => $checksheet[$i]->material_description,
                                'picking_list' => $checksheet[$i]->picking_list,
                                'picking_description' => $checksheet[$i]->picking_description,
                                'location' => $checksheet[$i]->location,
                                'quantity' => $checksheet[$i]->quantity,
                                'created_by' => Auth::id(),
                            ]);
                            $order_list->save();

                        } catch (Exception $e) {
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }

                    $data = ReedLaserOrderList::where('order_id', $last_id)
                        ->where('location', strtoupper($location))
                        ->get();

                    $response = array(
                        'status' => true,
                        'order' => $new_order,
                        'data' => $data,
                    );
                    return Response::json($response);
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Master kanban tidak ditemukan',
                    );
                    return Response::json($response);
                }
            }

        } else {
            $material_number = substr($kanban, 4, 7);
            $checksheet = ReedMasterChecksheet::where('material_number', $material_number)
                ->where('process', $process)
                ->orderBy('picking_queue')
                ->get();

            if (count($checksheet) > 0) {

                $last_id;
                try {
                    $new_order = new ReedLaserOrder([
                        'kanban' => $kanban,
                        'material_number' => $material_number,
                        'material_description' => $checksheet[0]->material_description,
                        'quantity' => $checksheet[0]->lot,
                        'hako' => 1,
                        'created_by' => Auth::id(),
                    ]);
                    $new_order->save();
                    $last_id = $new_order->id;

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }

                for ($i = 0; $i < count($checksheet); $i++) {
                    try {
                        $order_list = new ReedLaserOrderList([
                            'order_id' => $last_id,
                            'kanban' => $kanban,
                            'material_number' => $checksheet[$i]->material_picking,
                            'material_description' => $checksheet[$i]->material_description,
                            'picking_list' => $checksheet[$i]->picking_list,
                            'picking_description' => $checksheet[$i]->picking_description,
                            'location' => $checksheet[$i]->location,
                            'quantity' => $checksheet[$i]->quantity,
                            'created_by' => Auth::id(),
                        ]);
                        $order_list->save();

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }

                $data = ReedLaserOrderList::where('order_id', $last_id)
                    ->where('location', strtoupper($location))
                    ->get();

                $response = array(
                    'status' => true,
                    'order' => $new_order,
                    'data' => $data,
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Master kanban tidak ditemukan',
                );
                return Response::json($response);
            }
        }
    }

    public function createPackingOrder(Request $request)
    {
        $material_number = $request->get('material_number');
        $due_date = $request->get('packing_date');
        $proses = $request->get('proses');

        $checksheet = ReedMasterChecksheet::where('material_number', $material_number)
            ->where('location', strtoupper($proses))
            ->get();

        if (count($checksheet) > 0) {
            $prefix_now = 'KD' . date("y") . date("m");
            $code_generator = CodeGenerator::where('note', '=', 'kd')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $order_id = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            try {
                $new_order = new ReedPackingOrder([
                    'order_id' => $order_id,
                    'due_date' => $due_date,
                    'material_number' => $material_number,
                    'material_description' => $checksheet[0]->material_description,
                    'quantity' => $checksheet[0]->lot,
                    'hako' => $checksheet[0]->lot,
                    'created_by' => Auth::id(),
                ]);
                $new_order->save();

            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            for ($i = 0; $i < count($checksheet); $i++) {
                try {
                    $order_list = new ReedPackingOrderList([
                        'order_id' => $order_id,
                        'material_number' => $checksheet[$i]->material_picking,
                        'material_description' => $checksheet[$i]->material_description,
                        'picking_list' => $checksheet[$i]->picking_list,
                        'picking_description' => $checksheet[$i]->picking_description,
                        'material_check' => $checksheet[$i]->material_check,
                        'check_description' => $checksheet[$i]->check_description,
                        'process' => $checksheet[$i]->process,
                        'location' => $checksheet[$i]->location,
                        'remark' => $checksheet[$i]->remark,
                        'quantity' => $checksheet[$i]->quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $order_list->save();

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Master kanban ditemukan',

            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Master kanban tidak ditemukan',
            );
            return Response::json($response);
        }
    }

    public function createInjectionOrder(Request $request)
    {
        $material_number = $request->get('material_number');
        $due_date = $request->get('due_date');
        $quantity = $request->get('quantity');
        $location = $request->get('location');
        $loop = $quantity / 96;

        $checksheet = ReedMasterChecksheet::where('material_number', $material_number)
            ->where('process', strtoupper($location))
            ->get();

        if (count($checksheet) > 0) {
            $prefix_now = 'RS' . date("y") . date("m");
            $code_generator = CodeGenerator::where('note', '=', 'reed-synthetic')->first();
            if ($prefix_now != $code_generator->prefix) {
                $code_generator->prefix = $prefix_now;
                $code_generator->index = '0';
                $code_generator->save();
            }

            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $order = $code_generator->prefix . $number;
            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            try {
                $new_order = new ReedInjectionOrder([
                    'order_id' => $order,
                    'due_date' => $due_date,
                    'material_number' => $material_number,
                    'material_description' => $checksheet[0]->material_description,
                    'quantity' => $quantity,
                    'hako' => ($quantity / 96),
                    'created_by' => Auth::id(),
                ]);
                $new_order->save();

            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }

            for ($i = 1; $i <= $loop; $i++) {
                $order_list = new ReedInjectionOrderDetail([
                    'order_id' => $order,
                    'due_date' => $due_date,
                    'kanban' => $order . $i,
                    'material_number' => $material_number,
                    'material_description' => $checksheet[0]->material_description,
                    'quantity' => 96,
                    'created_by' => Auth::id(),
                ]);
                $order_list->save();
            }

            for ($i = 0; $i < count($checksheet); $i++) {
                try {
                    if ($checksheet[$i]->picking_list == 'HAKO') {
                        $picking_quantity = $quantity / 96;
                    } else {
                        $picking_quantity = $checksheet[$i]->quantity;
                    }

                    $order_list = new ReedInjectionOrderList([
                        'order_id' => $order,
                        'material_number' => $checksheet[$i]->material_picking,
                        'material_description' => $checksheet[$i]->material_description,
                        'picking_queue' => $checksheet[$i]->picking_queue,
                        'picking_list' => $checksheet[$i]->picking_list,
                        'picking_description' => $checksheet[$i]->picking_description,
                        'material_check' => $checksheet[$i]->material_check,
                        'check_description' => $checksheet[$i]->check_description,
                        'process' => $checksheet[$i]->process,
                        'location' => $checksheet[$i]->location,
                        'remark' => $checksheet[$i]->remark,
                        'quantity' => $picking_quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $order_list->save();

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Order berhasil dibuat',

            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Master kanban tidak ditemukan',
            );
            return Response::json($response);
        }
    }

    public function fetchInjectionOrder(Request $request)
    {

        $checksheets = ReedInjectionOrder::where('remark', '<', '1')->orderBy('order_id', 'DESC')->get();

        $response = array(
            'status' => true,
            'checksheets' => $checksheets,
        );
        return Response::json($response);
    }

    public function fetchPackingOrder(Request $request)
    {

        $checksheets = ReedPackingOrder::where('remark', '<=', '1')->orderBy('order_id', 'DESC')->get();

        $response = array(
            'status' => true,
            'checksheets' => $checksheets,
        );
        return Response::json($response);
    }

    public function fetchPackingVerification(Request $request)
    {
        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $process = $request->get('proses');

    }

    public function fetchPackingPickingList(Request $request)
    {

        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $process = $request->get('proses');

        $data = ReedPackingOrderList::where('order_id', $order_id)
            ->where('location', strtoupper($location))
            ->where('process', strtoupper($process))
            ->get();

        $order = ReedPackingOrder::where('order_id', $order_id)->first();

        if (count($data) > 0) {

            $response = array(
                'status' => true,
                'data' => $data,
                'order' => $order,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Packing order tidak ditemukan',
            );
            return Response::json($response);
        }
    }

    public function fetchInjectionDelivery(Request $request)
    {

        $kanban = $request->get('kanban');
        $location = $request->get('location');

        $storage_location = 'VN11';
        $material_number = substr($kanban, 4, 7);

        $order = ReedInjectionOrderDetail::where('kanban', $kanban)->first();

        if ($order) {
            $inventory = Inventory::where('plant', '8090')
                ->where('material_number', $order->material_number)
                ->where('storage_location', 'VN11')
                ->first();

            $response = array(
                'status' => true,
                'order' => $order,
                'inventory' => $inventory,
                'storage_location' => $storage_location,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Kanban finish injection tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function fetchUpdateInjectionDelivery(Request $request)
    {

        $kanban = $request->get('kanban');
        $location = $request->get('location');
        $order_id = $request->get('order_id');

        $storage_location = substr($kanban, 0, 4);
        $material_number = substr($kanban, 4, 7);

        $order = ReedInjectionOrder::where('id', $order_id)->first();

        if ($order) {
            $inventory = Inventory::where('plant', '8190')
                ->where('material_number', $material_number)
                ->where('storage_location', $storage_location)
                ->first();

            $response = array(
                'status' => true,
                'order' => $order,
                'inventory' => $inventory,
                'storage_location' => $storage_location,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Kanban finish injection tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function updateWarehouseDelivery(Request $request)
    {

        $label = $request->get('label');
        $kanban = $request->get('kanban');
        $employee_id = $request->get('employee_id');

        $data_label = explode('-', $label);
        $id = $data_label[1];

        $data = ReedWarehouseDelivery::where('kanban', $kanban)
            ->where('remark', '0')
            ->orderBy('created_at', 'DESC')
            ->first();

        //Cek label tersedia dan kesesuaian material
        if ($data) {
            $resin = ReedWarehouseReceive::where('id', $id)->first();
            if ($resin) {
                if ($resin->material_number != $data->material_number) {
                    $response = array(
                        'status' => false,
                        'message' => 'Material salah',
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Data tidak ditemukan',
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Qty sudah terpenuhi',
            );
            return Response::json($response);
        }

        //Kesesuaian
        $fifo = ReedWarehouseReceive::where('bag_delivered', 0)
            ->orderBy('receive_date', 'ASC')
            ->first();

        if ($resin->receive_date > $fifo->receive_date) {
            $response = array(
                'status' => false,
                'message' => 'Pengambilan harus FIFO, ambil Resin ' . $fifo->material_description . ' dengan tanggal Kedatangan ' . date('d F Y', strtotime($fifo->receive_date)),
            );
            return Response::json($response);
        }

        try {

            $data->operator_delivery = $employee_id;
            $data->delivery_at = date('Y-m-d H:i:s');
            $data->bag_delivered = $data->bag_delivered + 1;
            $data->remark = 1;
            $data->save();

            $resin->bag_delivered = $resin->bag_delivered + 1;
            $resin->save();

            $inventory = Inventory::firstOrNew([
                'plant' => '8190',
                'material_number' => $data->material_number,
                'storage_location' => 'MSTK',
            ]);
            $inventory->quantity = $inventory->quantity - $data->quantity;
            $inventory->save();

            $response = array(
                'status' => true,
                'message' => 'Pengambilan Berhasil',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function updateInjectionResinDelivery(Request $request)
    {

        $id = $request->get('id');
        $pic_injection = $request->get('pic_injection');
        $pic_warehouse = $request->get('pic_warehouse');
        $material = $request->get('material');
        $store = $request->get('store');

        $data = explode('-', $material);
        $id_resin = $data[1];

        $resin = ReedWarehouseReceive::where('id', $id_resin)
            ->where('bag_delivered', '>', 0)
            ->first();

        if ($resin) {
            if ($resin->material_number != $store) {
                $response = array(
                    'status' => false,
                    'message' => 'Material salah',
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Data resin tidak ditemukan',
            );
            return Response::json($response);
        }

        $data = ReedWarehouseDelivery::where('id', $id)->first();

        try {

            $data->operator_receive = $pic_injection;
            $data->receive_at = date('Y-m-d H:i:s');
            $data->remark = 2;
            $data->save();

            $response = array(
                'status' => true,
                'message' => 'Pengambilan Berhasil',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function scanReedClosure(Request $request)
    {

        $kd_number = $request->get('kd_number');
        $location = $request->get('location');

        $reed = ReedPackingOrder::where('order_id', $kd_number)->first();

        if ($reed) {
            if ($reed->remark == 1) {

                $material_number = $reed->material_number;
                $quantity = $reed->quantity;
                $mpdl = MaterialPlantDataList::where('material_number', $material_number)->first();
                $storage_location = $mpdl->storage_location;

                //Production Schedule
                $reed->remark = 2;

                //KnockDown
                $knock_down = new KnockDown([
                    'kd_number' => $kd_number,
                    'created_by' => Auth::id(),
                    'max_count' => 1,
                    'actual_count' => 1,
                    'remark' => $location,
                    'status' => 1,
                ]);

                //KnockDown Log
                $knock_down_log = KnockDownLog::updateOrCreate(
                    ['kd_number' => $kd_number, 'status' => 1],
                    ['created_by' => Auth::id(), 'status' => 1, 'updated_at' => Carbon::now()]
                );

                //Inisiasi Serial Number
                $serial_generator = CodeGenerator::where('note', '=', 'kd_serial_number')->first();
                $serial_number = sprintf("%'.0" . $serial_generator->length . "d", $serial_generator->index + 1);
                $serial_number = $serial_generator->prefix . $serial_number . $this->generateRandomString();
                $serial_generator->index = $serial_generator->index + 1;
                $serial_generator->save();

                //KnockDown Detail
                $knock_down_detail = new KnockDownDetail([
                    'kd_number' => $kd_number,
                    'material_number' => $material_number,
                    'quantity' => $quantity,
                    'storage_location' => $storage_location,
                    'serial_number' => $serial_number,
                    'created_by' => Auth::id(),
                ]);

                //Inventory
                $inventory = Inventory::where('plant', '=', '8190')
                    ->where('material_number', '=', $material_number)
                    ->where('storage_location', '=', $storage_location)
                    ->first();

                if ($inventory) {
                    $inventory->quantity = $inventory->quantity + $quantity;
                } else {
                    $inventory = new Inventory([
                        'plant' => '8190',
                        'material_number' => $material_number,
                        'storage_location' => $storage_location,
                        'quantity' => $quantity,
                    ]);
                }

                //Transaction Completion
                $transaction_completion = new TransactionCompletion([
                    'serial_number' => $kd_number,
                    'material_number' => $material_number,
                    'issue_plant' => '8190',
                    'issue_location' => $storage_location,
                    'quantity' => $quantity,
                    'movement_type' => '101',
                    'created_by' => Auth::id(),
                ]);

                try {
                    DB::transaction(function () use ($reed, $knock_down, $knock_down_detail, $knock_down_log, $inventory, $transaction_completion) {
                        $reed->save();
                        $knock_down->save();
                        $knock_down_detail->save();
                        $knock_down_log->save();
                        $inventory->save();
                        $transaction_completion->save();
                    });

                    $material = Material::where('material_number', '=', $material_number)->first();

                    // YMES COMPLETION NEW
                    $category = 'production_result';
                    $function = 'scanReedClosure';
                    $action = 'production_result';
                    $result_date = date('Y-m-d H:i:s');
                    $slip_number = $kd_number;
                    $serial_number = $serial_number;
                    $material_number = $material_number;
                    $material_description = $material->material_description;
                    $issue_location = $material->issue_storage_location;
                    $mstation = $material->mstation;
                    $quantity = $quantity;
                    $remark = 'MIRAI';
                    $created_by = Auth::user()->username;
                    $created_by_name = Auth::user()->name;
                    $synced = null;
                    $synced_by = null;

                    app(YMESController::class)->production_result(
                        $category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description);
                    // YMES END

                    $response = array(
                        'status' => true,
                        'message' => 'Closure Sukses',
                        'knock_down_detail' => $knock_down_detail,
                    );
                    return Response::json($response);

                } catch (Exception $e) {
                    $error_log = new ErrorLog([
                        'error_message' => $e->getMessage(),
                        'created_by' => $id,
                    ]);
                    $error_log->save();
                }

            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Alur proses salah',
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'KD Number tidak ditemukan',
            );
            return Response::json($response);
        }
    }

    public function scanWarehouseDelivery(Request $request)
    {
        $kanban = $request->get('kanban');

        $storage_location = substr($kanban, 0, 4);
        $material_number = substr($kanban, 4, 7);

        $data = ReedWarehouseDelivery::where('kanban', $kanban)
            ->where('remark', '<', '2')
            ->orderBy('created_at', 'DESC')
            ->first();

        if ($data) {
            $response = array(
                'status' => true,
                'data' => $data,
            );
            return Response::json($response);
        } else {

            $material = ReedMasterChecksheet::where('material_number', $material_number)->first();

            try {
                $insert = new ReedWarehouseDelivery([
                    'request_at' => date('Y-m-d H:i:s'),
                    'kanban' => $kanban,
                    'material_number' => $material->material_number,
                    'material_description' => $material->material_description,
                    'quantity' => $material->lot,
                    'bag_quantity' => 1,
                    'created_by' => Auth::id(),
                ]);
                $insert->save();

                $data = ReedWarehouseDelivery::where('kanban', $kanban)
                    ->where('remark', '0')
                    ->orderBy('created_at', 'DESC')
                    ->first();

                $response = array(
                    'status' => true,
                    'data' => $data,
                    'message' => 'Request Berhasil',
                );
                return Response::json($response);

            } catch (Exception $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        }
    }

    public function scanStoreVerification(Request $request)
    {
        $data_id = $request->get('id');
        $material_number = $request->get('material_number');
        $receive_date = $request->get('receive_date');
        $employee_id = $request->get('employee_id');

        $data = explode('-', $data_id);
        $id = $data[1];

        $data = ReedWarehouseReceive::where('id', $id)->first();

        if (!$data) {
            $response = array(
                'status' => false,
                'message' => 'QR Code label tidak ditemukan',
            );
            return Response::json($response);
        }

        if ($data->bag_arranged == 1) {
            $response = array(
                'status' => false,
                'message' => 'Scan Verifikasi store sudah pernah dilakukan',
            );
            return Response::json($response);
        }

        try {

            $data->bag_arranged = $data->bag_arranged + 1;
            $data->operator_storage = $employee_id;
            $data->save();

            $response = array(
                'status' => true,
                'message' => 'Print Success',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function scanReedOperator(Request $request)
    {

        $employee_id = $request->get('employee_id');
        $employee_sync = EmployeeSync::where('employee_id', '=', $employee_id)->first();

        if ($employee_sync == "") {
            $response = array(
                'status' => false,
                'message' => "ID karyawan tidak ditemukan",
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'employee' => $employee_sync,
        );
        return Response::json($response);
    }

    public function scanInjectionPicking(Request $request)
    {
        $qr_item = $request->get('qr_item');
        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $employee_id = $request->get('employee_id');

        if (str_contains(strtoupper($qr_item), 'HAKO') || str_contains(strtoupper($qr_item), 'MOLDING')) {
            $data = explode('-', $qr_item);

            $picking_list = $data[0];
            $material_number = $data[1];

            $order_list = ReedInjectionOrderList::where('order_id', $order_id)
                ->where('picking_list', strtoupper($picking_list))
                ->where('material_number', strtoupper($material_number))
                ->where('location', strtoupper($location))
                ->where('actual_quantity', '<', db::raw('quantity'))
                ->first();

            if ($order_list) {

                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');

                    $log = new ReedInjectionOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Pengambilan ' . $picking_list . ' Salah'),
                );
                return Response::json($response);
            }

        } else {
            $remark = null;
            $order_list = ReedInjectionOrderList::where('order_id', $order_id)
                ->where('material_number', strtoupper($qr_item))
                ->first();

            if ($order_list) {
                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');
                    $order_list->remark = $remark;

                    $log = new ReedInjectionOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'remark' => $remark,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }

            } else {
                if (str_contains(strtoupper($qr_item), 'INJEKSI')) {
                    $remark = $qr_item;
                    $qr_item = 'MESIN INJEKSI';
                }

                $order_list = ReedInjectionOrderList::where('order_id', $order_id)
                    ->where('picking_list', strtoupper($qr_item))
                    ->where('location', strtoupper($location))
                    ->first();

                if ($order_list) {
                    if ($order_list->actual_quantity == $order_list->quantity) {
                        $response = array(
                            'status' => false,
                            'message' => 'Quanity sudah terpenuhi',
                        );
                        return Response::json($response);
                    } else {
                        $order_list->actual_quantity = $order_list->actual_quantity + 1;
                        $order_list->picked_by = strtoupper($employee_id);
                        $order_list->picked_at = date('Y-m-d H:i:s');
                        $order_list->remark = $remark;

                        $log = new ReedInjectionOrderLog([
                            'order_id' => $order_list->order_id,
                            'kanban' => $order_list->kanban,
                            'material_number' => $order_list->material_number,
                            'material_description' => $order_list->material_description,
                            'picking_list' => $order_list->picking_list,
                            'picking_description' => $order_list->picking_description,
                            'location' => $order_list->location,
                            'quantity' => 1,
                            'remark' => $remark,
                            'picked_by' => strtoupper($employee_id),
                            'picked_at' => date('Y-m-d H:i:s'),
                            'created_by' => Auth::id(),
                        ]);

                        try {
                            DB::transaction(function () use ($order_list, $log) {
                                $order_list->save();
                                $log->save();
                            });

                            $response = array(
                                'status' => true,
                                'message' => 'Verifikasi Berhasil',
                            );
                            return Response::json($response);

                        } catch (Exception $e) {
                            $response = array(
                                'status' => false,
                                'message' => $e->getMessage(),
                            );
                            return Response::json($response);
                        }
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Pengambilan Salah',
                    );
                    return Response::json($response);
                }
            }
        }
    }

    public function scanLaserPicking(Request $request)
    {
        $qr_item = $request->get('qr_item');
        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $employee_id = $request->get('employee_id');

        if (str_contains(strtoupper($qr_item), 'HAKO') || str_contains(strtoupper($qr_item), 'LASER') || str_contains(strtoupper($qr_item), 'MOLDING')) {
            $data = explode('-', $qr_item);

            $picking_list = $data[0];
            $material_number = $data[1];

            if (strtoupper($picking_list) == 'LASER') {
                $picking_list = 'APLIKASI LASER';
            }

            $order_list = ReedLaserOrderList::where('order_id', $order_id)
                ->where('picking_list', strtoupper($picking_list))
                ->where('material_number', strtoupper($material_number))
                ->where('location', strtoupper($location))
                ->first();

            if ($order_list) {

                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');

                    $log = new ReedLaserOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Pengambilan ' . $picking_list . ' Salah'),
                );
                return Response::json($response);
            }

        } else if (str_contains(strtoupper($qr_item), 'RS')) {
            $order_list = ReedLaserOrderList::where('order_id', $order_id)
                ->where('picking_list', 'KANBAN')
                ->where('location', strtoupper($location))
                ->first();

            if ($order_list->actual_quantity == $order_list->quantity) {
                $response = array(
                    'status' => false,
                    'message' => 'Quanity sudah terpenuhi',
                );
                return Response::json($response);
            } else {

                $reed_inj = ReedInjectionOrderDetail::where('kanban', $qr_item)->first();
                if ($reed_inj) {
                    if ($order_list->material_number != $reed_inj->material_number) {
                        $response = array(
                            'status' => false,
                            'message' => ucwords('Material Salah'),
                        );
                        return Response::json($response);
                    }

                    if ($reed_inj->remark > 1) {
                        $response = array(
                            'status' => false,
                            'message' => ucwords('Material sudah pernah dipicking untuk laser'),
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => ucwords('Kanban injeksi tidak terdaftar'),
                    );
                    return Response::json($response);
                }

                $reed_inj->remark = 2;
                $reed_inj->picking_by = strtoupper($employee_id);
                $reed_inj->picking_at = date('Y-m-d H:i:s');

                $order_list->actual_quantity = $order_list->actual_quantity + 1;
                $order_list->picked_by = strtoupper($employee_id);
                $order_list->picked_at = date('Y-m-d H:i:s');
                $order_list->remark = $qr_item;

                $log = new ReedLaserOrderLog([
                    'order_id' => $order_list->order_id,
                    'kanban' => $order_list->kanban,
                    'material_number' => $order_list->material_number,
                    'material_description' => $order_list->material_description,
                    'picking_list' => $order_list->picking_list,
                    'picking_description' => $order_list->picking_description,
                    'location' => $order_list->location,
                    'quantity' => 1,
                    'remark' => $qr_item,
                    'picked_by' => strtoupper($employee_id),
                    'picked_at' => date('Y-m-d H:i:s'),
                    'created_by' => Auth::id(),
                ]);

                try {
                    DB::transaction(function () use ($order_list, $log, $reed_inj) {
                        $order_list->save();
                        $log->save();
                        $reed_inj->save();
                    });

                    $response = array(
                        'status' => true,
                        'message' => 'Verifikasi Berhasil',
                    );
                    return Response::json($response);

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            }

        } else {
            $remark = null;

            $order_list = ReedLaserOrderList::where('order_id', $order_id)
                ->where('picking_list', strtoupper($qr_item))
                ->where('location', strtoupper($location))
                ->first();

            if ($order_list) {

                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');
                    $order_list->remark = $remark;

                    $log = new ReedLaserOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'remark' => $remark,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Pengambilan Salah',
                );
                return Response::json($response);
            }

        }
    }

    public function scanPackingBox(Request $request)
    {
        $order_id = $request->get('order_id');
        $outer_box = $request->get('outer_box');
        $blister = $request->get('blister');
        $operator_id = $request->get('operator_id');
        $date = date('Y-m-d H:i:s');

        $cek = ReedPackingOrderList::where('order_id', $order_id)
            ->where('material_check', strtoupper($outer_box))
            ->where('material_number', strtoupper($blister))
            ->first();

        if ($cek) {
            if ($cek->actual_quantity < $cek->quantity) {
                try {
                    $cek->actual_quantity = $cek->actual_quantity + 1;

                    if ($cek->quantity == $cek->actual_quantity) {
                        $reed = ReedPackingOrder::where('order_id', $order_id)
                            ->update([
                                'remark' => 1,
                            ]);
                    }

                    $cek->picked_by = $operator_id;
                    $cek->picked_at = $date;
                    $cek->save();

                    $response = array(
                        'status' => true,
                        'quantity' => $cek->actual_quantity,
                        'message' => 'Verifikasi Berhasil',
                    );
                    return Response::json($response);

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Quanity sudah terpenuhi'),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => ucwords('Outer box & support paper tidak sesuai'),
            );
            return Response::json($response);
        }
    }

    public function scanPackingReedCase(Request $request)
    {
        $order_id = $request->get('order_id');
        $support_paper = $request->get('support_paper');
        $reed_case = $request->get('reed_case');
        $order_by = $request->get('order_by');
        $date = date('Y-m-d H:i:s');

        $scan = ReedPackingOrderList::where('order_id', $order_id)
            ->where('material_check', strtoupper($support_paper))
            ->where('material_number', strtoupper($reed_case))
            ->first();

        $cek = ReedPackingOrderList::where('order_id', $order_id)
            ->where('check_description', 'SUPPORT PAPER ID');

        if ($order_by == 'DESC') {
            $cek = $cek->orderBy('picking_description', 'DESC');
        } else {
            $cek = $cek->orderBy('picking_description', 'ASC');
        }
        $cek = $cek->first();

        if ($scan) {
            if ($scan->material_number == $cek->material_number) {
                if ($scan->actual_quantity < $scan->quantity) {
                    try {
                        $scan->actual_quantity = $scan->actual_quantity + 1;
                        $scan->picked_by = Auth::id();
                        $scan->picked_at = $date;
                        $scan->save();

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => ucwords('Quanity sudah terpenuhi'),
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Posisi reed case tidak sesuai'),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => ucwords('Support paper & Reed case tidak sesuai'),
            );
            return Response::json($response);
        }

        if ($cek1 && $cek2) {
            if (($cek1->actual_quantity < $cek1->quantity) && ($cek2->actual_quantity < $cek2->quantity)) {
                try {
                    $cek1->actual_quantity = $cek1->actual_quantity + 1;
                    $cek1->picked_by = Auth::id();
                    $cek1->picked_at = $date;
                    $cek1->save();

                    $cek2->actual_quantity = $cek2->actual_quantity + 1;
                    $cek2->picked_by = Auth::id();
                    $cek1->picked_at = $date;
                    $cek2->save();

                    $response = array(
                        'status' => true,
                        'message' => 'Verifikasi Berhasil',
                    );
                    return Response::json($response);

                } catch (Exception $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                    return Response::json($response);
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Quanity sudah terpenuhi'),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => ucwords('Support paper & Reed case tidak sesuai'),
            );
            return Response::json($response);
        }

    }

    public function scanPackingPicking(Request $request)
    {
        $qr_item = $request->get('qr_item');
        $order_id = $request->get('order_id');
        $location = $request->get('location');
        $employee_id = $request->get('employee_id');

        if (str_contains(strtoupper($qr_item), '-')) {
            $data = explode('-', $qr_item);

            $picking_list = $data[0];
            $material_number = $data[1];

            if (strtoupper($picking_list) == 'LASER') {
                $picking_list = 'APLIKASI LASER';
            }

            $order_list = ReedPackingOrderList::where('order_id', $order_id)
                ->where('picking_list', strtoupper($picking_list))
                ->where('material_number', strtoupper($material_number))
                ->where('location', strtoupper($location))
                ->first();

            if ($order_list) {

                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');

                    $log = new ReedPackingOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => ucwords('Pengambilan ' . $picking_list . ' Salah'),
                );
                return Response::json($response);
            }

        } else {

            $order_list = ReedPackingOrderList::where('order_id', $order_id)
                ->where('material_number', strtoupper($qr_item))
                ->where('location', strtoupper($location))
                ->first();

            if ($order_list) {

                if ($order_list->actual_quantity == $order_list->quantity) {
                    $response = array(
                        'status' => false,
                        'message' => 'Quanity sudah terpenuhi',
                    );
                    return Response::json($response);
                } else {
                    $order_list->actual_quantity = $order_list->actual_quantity + 1;
                    $order_list->picked_by = strtoupper($employee_id);
                    $order_list->picked_at = date('Y-m-d H:i:s');

                    $log = new ReedPackingOrderLog([
                        'order_id' => $order_list->order_id,
                        'kanban' => $order_list->kanban,
                        'material_number' => $order_list->material_number,
                        'material_description' => $order_list->material_description,
                        'picking_list' => $order_list->picking_list,
                        'picking_description' => $order_list->picking_description,
                        'location' => $order_list->location,
                        'quantity' => 1,
                        'picked_by' => strtoupper($employee_id),
                        'picked_at' => date('Y-m-d H:i:s'),
                        'created_by' => Auth::id(),
                    ]);

                    try {
                        DB::transaction(function () use ($order_list, $log) {
                            $order_list->save();
                            $log->save();
                        });

                        $response = array(
                            'status' => true,
                            'message' => 'Verifikasi Berhasil',
                        );
                        return Response::json($response);

                    } catch (Exception $e) {
                        $response = array(
                            'status' => false,
                            'message' => $e->getMessage(),
                        );
                        return Response::json($response);
                    }
                }
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Pengambilan Salah',
                );
                return Response::json($response);
            }

        }
    }

    public function fetchStartInjection(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedInjectionOrder::where('order_id', $id)
                ->update([
                    'operator_injection_id' => strtoupper($employee_id),
                    'start_injection' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Injeksi berhasil dimulai',
                'start' => date('Y-m-d H:i:s'),
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchFinishInjection(Request $request)
    {
        $order_id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedInjectionOrder::where('order_id', $order_id)
                ->update([
                    'operator_injection_id' => strtoupper($employee_id),
                    'finish_injection' => date('Y-m-d H:i:s'),
                    'remark' => 1,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Injeksi berhasil diakhiri',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchStartLaser(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedLaserOrder::where('id', $id)
                ->update([
                    'operator_laser_id' => strtoupper($employee_id),
                    'start_laser' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Laser berhasil dimulai',
                'start' => date('Y-m-d H:i:s'),
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchFinishLaser(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedLaserOrder::where('id', $id)
                ->update([
                    'operator_laser_id' => strtoupper($employee_id),
                    'finish_laser' => date('Y-m-d H:i:s'),
                    'remark' => 1,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Laser berhasil diakhiri',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchStartPacking(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedPackingOrder::where('id', $id)
                ->update([
                    'operator_packing_id' => strtoupper($employee_id),
                    'start_packing' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Packing berhasil dimulai',
                'start' => date('Y-m-d H:i:s'),
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchFinishPacking(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedPackingOrder::where('id', $id)
                ->update([
                    'operator_packing_id' => strtoupper($employee_id),
                    'finish_packing' => date('Y-m-d H:i:s'),
                    'remark' => 1,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Packing berhasil diakhiri',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchFinishMolding(Request $request)
    {
        $id = $request->get('order_id');
        $employee_id = $request->get('employee_id');

        try {
            $order = ReedInjectionOrder::where('id', $id)
                ->update([
                    'operator_molding_id' => strtoupper($employee_id),
                    'setup_molding' => 1,
                ]);

            $response = array(
                'status' => true,
                'message' => 'Proses Injeksi berhasil diakhiri',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchLabelVerification(Request $request)
    {

        $date_receive = $request->get('date_receive');

        $cek_kedatangan = ReedWarehouseReceive::where('receive_date', $date_receive)->first();

        $order = db::select("SELECT receive_date, material_number, material_description, SUM(quantity) AS quantity, SUM(bag_quantity) AS bag_quantity, SUM(bag_arranged) AS bag_arranged FROM reed_warehouse_receives
			WHERE receive_date = '" . $date_receive . "'
			GROUP BY receive_date, material_number, material_description");

        if ($cek_kedatangan) {
            $response = array(
                'status' => true,
                'order' => $order,
                'message' => 'Data Ditemukan',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Data tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function fetchPrintLabelInjection(Request $request)
    {
        $order_id = $request->get('order_id');

        $order = ReedInjectionOrder::where('order_id', '=', $order_id)
            ->whereNotNull('operator_injection_id')
            ->first();

        if ($order) {
            $this->printLabelInjection($order);

            $response = array(
                'status' => true,
                'message' => 'Print label sukses',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Data pre approval tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function fetchPrintWorkOrder(Request $request)
    {
        $order_id = $request->get('order_id');

        $order = ReedInjectionOrder::where('order_id', '=', $order_id)->first();

        if ($order) {
            $order->print = 1;
            $order->save();

            $this->printWorkOrder($order);

            $response = array(
                'status' => true,
                'message' => 'Print label sukses',
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Data tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function fetchPrintReceive(Request $request)
    {

        try {

            $data = ReedWarehouseReceive::where('receive_date', $request->get('receive_date'))
                ->update([
                    'print_status' => 1,
                ]);

            $this->printLabel($request->get('receive_date'));

            $response = array(
                'status' => true,
                'message' => 'Print Success',
            );
            return Response::json($response);
        } catch (Exception $e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function postLabelVerification(Request $request)
    {
        try {
            $id_user = Auth::id();
            $tujuan_upload = 'files/reed';

            for ($i = 0; $i < $request->input('jumlah'); $i++) {

                $file = $request->file('file_datas_' . $i);
                $nama = $file->getClientOriginalName();

                $filename = pathinfo($nama, PATHINFO_FILENAME);
                $extension = pathinfo($nama, PATHINFO_EXTENSION);

                $filename = md5($filename . date('YmdHisa')) . '.' . $extension;

                $file->move($tujuan_upload, $filename);

                $data[] = $filename;
            }

            $file_saved = json_encode($data);

            $audit_all = ReedWarehouseReceive::where('receive_date', '=', $request->input('date_receive'))
                ->where('material_number', '=', $request->input('material_number'))
                ->update([
                    'photo_date' => date('Y-m-d'),
                    'photo' => $file_saved,
                    'operator_label' => $request->input('employee_id'),
                ]);

            $response = array(
                'status' => true,
                'message' => "Data Berhasil Disimpan",
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

    public function inputResinReceive(Request $request)
    {

        $material = ReedMasterChecksheet::where('material_number', $request->get('material_number'))
            ->where('location', 'WAREHOUSE')
            ->where('process', 'RECEIVE')
            ->first();

        $quantity = $request->get('quantity');
        $bag_quantity = $quantity / $material->lot;

        try {
            for ($i = 0; $i < $bag_quantity; $i++) {
                $receive = new ReedWarehouseReceive([
                    'receive_date' => $request->get('date'),
                    'material_number' => $material->material_number,
                    'material_description' => $material->material_description,
                    'quantity' => $material->lot,
                    'bag_quantity' => 1,
                    'created_by' => Auth::id(),
                ]);
                $receive->save();
            }

            $inventory = Inventory::firstOrNew([
                'plant' => '8190',
                'material_number' => $material->material_number,
                'storage_location' => 'MSTK',
            ]);
            $inventory->quantity = ($inventory->quantity + $quantity);
            $inventory->save();

            $response = array(
                'status' => true,
                'message' => 'Process Receive Success',
            );
            return Response::json($response);
        } catch (Exception $e) {

            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function reprintPackingOrder(Request $request)
    {

        $data = ReedPackingOrder::where('order_id', $request->get('order_id'))->first();

        try {
            $data->print = 1;
            $data->save();

            $this->printOrder($data);

            $response = array(
                'status' => true,
                'message' => 'Print Packing Order Success',
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function printOrder($data)
    {
        if (Auth::user()->role_code == 'MIS') {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'KDO MP';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_LEFT);
        $printer->setUnderline(true);
        $printer->text('Storage Location:');
        $printer->setUnderline(false);
        $printer->feed(1);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 3);
        $printer->text(strtoupper("REED SYNTHETIC" . "\n"));
        $printer->initialize();

        $printer->setUnderline(true);
        $printer->text('KDO:');
        $printer->feed(1);
        $printer->setUnderline(false);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($data->order_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->text($data->order_id . "\n");

        $printer->initialize();
        $printer->setUnderline(true);
        $printer->text('Packing Date:');
        $printer->setUnderline(false);
        $printer->feed(1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(3, 2);
        $printer->text(date('d-M-Y', strtotime($data->due_date)) . "\n");
        $printer->feed(1);

        $printer->initialize();
        $printer->text("No |GMC     | Description                 | Qty ");
        $number = $this->writeString(1, 2, ' ');
        $qty = $this->writeString($data->quantity, 4, ' ');
        $material_description = substr($data->material_description, 0, 27);
        $material_description = $this->writeString($material_description, 27, ' ');
        $printer->text($number . " |" . $data->material_number . " | " . $material_description . " | " . $qty);

        $printer->feed(2);
        $printer->initialize();
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("------------------------------------");
        $printer->feed(1);
        $printer->text("|Qty:             |Qty:            |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|                 |                |");
        $printer->feed(1);
        $printer->text("|Production       |Logistic        |");
        $printer->feed(1);
        $printer->text("------------------------------------");
        $printer->feed(2);
        $printer->initialize();
        $printer->text("Total Qty: " . $data->quantity . "\n");
        $printer->feed(1);
        $printer->cut();
        $printer->close();

    }

    public function printLabel($receive_date)
    {
        if (Auth::user()->role_code == 'MIS') {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'FLO Printer LOG';
        }

        $data = ReedWarehouseReceive::where('receive_date', $receive_date)->get();

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        for ($i = 0; $i < count($data); $i++) {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setReverseColors(true);
            $printer->setTextSize(3, 3);
            $printer->text("  WAREHOUSE  " . "\n");
            $printer->feed(1);
            $printer->initialize();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->qrCode($data[$i]->material_number . '-' . $data[$i]->id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
            $printer->initialize();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setTextSize(2, 2);
            $printer->text($data[$i]->material_number . "\n");

            $printer->initialize();
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($data[$i]->material_description . "\n");

            $printer->initialize();
            $printer->setEmphasis(true);
            $printer->setTextSize(1, 1);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("Tanggal Kedatangan : " . date('j F Y', strtotime($data[$i]->receive_date)) . "\n");

            $printer->feed(1);
            $printer->initialize();
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text("------------------------------------");
            $printer->feed(1);
            $printer->text("| Ttd  :                           |");
            $printer->feed(1);
            $printer->text("|                                  |");
            $printer->feed(1);
            $printer->text("|                                  |");
            $printer->feed(1);
            $printer->text("|                                  |");
            $printer->feed(1);
            $printer->text("|                                  |");
            $printer->feed(1);
            $printer->text("------------------------------------");
            $printer->feed(1);
            $printer->text("| NIK  :                           |");
            $printer->feed(1);
            $printer->text("| Nama :                           |");
            $printer->feed(1);
            $printer->text("------------------------------------");
            $printer->feed(2);
            $printer->cut();
            $printer->close();
        }
    }

    public function printLabelInjection($order)
    {
        if (Auth::user()->role_code == 'MIS') {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'Injection';
        }

        $approval = ReedApproval::where('order_id', $order->order_id)
            ->where('status', 1)
            ->where('location', 'molding')
            ->first();

        $employee = EmployeeSync::where('employee_id', $order->operator_injection_id)->first();

        $lot = $order->quantity / $order->hako;

        $detail = ReedInjectionOrderDetail::where('order_id', $order->order_id)->get();

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        for ($i = 0; $i < count($detail); $i++) {
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->setEmphasis(true);
            $printer->setReverseColors(true);
            $printer->setTextSize(3, 3);
            $printer->text(" REED SYNTHETIC " . "\n");
            $printer->feed(1);
            $printer->initialize();

            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->qrCode($detail[$i]->kanban, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
            $printer->setTextSize(1, 1);
            $printer->text($detail[$i]->kanban . "\n");
            $printer->feed(1);
            $printer->initialize();

            $printer->initialize();
            $printer->setTextSize(3, 3);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($order->material_number . "\n");
            $printer->initialize();
            $printer->setTextSize(2, 2);
            $printer->setJustification(Printer::JUSTIFY_CENTER);
            $printer->text($order->material_description . "\n");

            $printer->setEmphasis(true);
            $printer->setTextSize(1, 1);
            $printer->setJustification(Printer::JUSTIFY_LEFT);
            $printer->feed(2);
            $printer->text("Prod. Date : " . date('j F Y', strtotime($order->due_date)) . "\n");
            $printer->text("Quanity    : " . $lot . " PC(s)\n");
            $printer->text("OP Injeksi : " . $employee->name . "\n");
            $printer->text("Param. LS4 : " . $approval->parameter . "\n");
            $printer->initialize();

            $printer->feed(2);
            $printer->cut();
            $printer->close();
        }
    }

    public function printWorkOrder($order)
    {
        if (Auth::user()->role_code == 'MIS') {
            $printer_name = 'MIS';
        } else {
            $printer_name = 'Injection';
        }

        $connector = new WindowsPrintConnector($printer_name);
        $printer = new Printer($connector);

        //Molding
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setUnderline(true);
        $printer->setTextSize(3, 3);
        $printer->text("  WORK ORDER  " . "\n");
        $printer->initialize();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("(Slip for MTC Molding)" . "\n");
        $printer->feed(1);
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($order->order_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setTextSize(1, 1);
        $printer->text($order->order_id . "\n");
        $printer->feed(1);
        $printer->initialize();

        $printer->initialize();
        $printer->setTextSize(3, 3);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($order->material_number . "\n");
        $printer->initialize();
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($order->material_description . "\n");

        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->feed(2);
        $printer->text("Due Date : " . date('j F Y', strtotime($order->due_date)) . "\n");
        $printer->text("Quanity : " . $order->quantity . " PC(s)\n");
        $printer->initialize();

        $printer->feed(2);
        $printer->cut();
        $printer->close();

        //Injeksi
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setEmphasis(true);
        $printer->setUnderline(true);
        $printer->setTextSize(3, 3);
        $printer->text("  WORK ORDER  " . "\n");
        $printer->initialize();

        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text("(Slip for Injection)" . "\n");
        $printer->feed(1);
        $printer->initialize();

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->qrCode($order->order_id, Printer::QR_ECLEVEL_L, 7, Printer::QR_MODEL_2);
        $printer->setTextSize(1, 1);
        $printer->text($order->order_id . "\n");
        $printer->feed(1);
        $printer->initialize();

        $printer->initialize();
        $printer->setTextSize(3, 3);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($order->material_number . "\n");
        $printer->initialize();
        $printer->setTextSize(2, 2);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->text($order->material_description . "\n");

        $printer->setEmphasis(true);
        $printer->setTextSize(1, 1);
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->feed(2);
        $printer->text("Due Date : " . date('j F Y', strtotime($order->due_date)) . "\n");
        $printer->text("Quanity : " . $order->quantity . " PC(s)\n");
        $printer->initialize();

        $printer->feed(2);
        $printer->cut();
        $printer->close();

    }

    public function writeString($text, $maxLength, $char)
    {
        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                $textLength = strlen($text);
            } else {
                $text = "";
            }
            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $text .= $char;
            }
        }
        return strtoupper($text);
    }

    public function kittoCompletion($barcode_number)
    {

        $server = $_SERVER['SERVER_ADDR'];
        $url = '';
        if ($server == '10.109.52.4' || $server == '10.109.52.3') {
            $url = 'http://10.109.52.4/kitto/public/';
        } else if ($server == '10.109.52.1') {
            $url = 'http://10.109.52.1:887/kittodev/public/';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . 'completions',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'barcode_number=' . $barcode_number,
        ));

        $response = json_decode(curl_exec($curl));

        return $response;
    }

    public function kittoTransfer($barcode_number)
    {

        $server = $_SERVER['SERVER_ADDR'];
        $url = '';
        if ($server == '10.109.52.4' || $server == '10.109.52.3') {
            $url = 'http://10.109.52.4/kitto/public/';
        } else if ($server == '10.109.52.1') {
            $url = 'http://10.109.52.1:887/kittodev/public/';
        }

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $url . 'transfers',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'barcode_number=' . $barcode_number,
        ));

        $response = json_decode(curl_exec($curl));

        return $response;
    }

    public function generateRandomString()
    {

        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return $characters[rand(0, strlen($characters) - 1)];

    }

}
