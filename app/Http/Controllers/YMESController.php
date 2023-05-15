<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use DataTables;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;

class YMESController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->categories = [
            'goods_movement',
            'goods_movement_cancel',
            'goods_movement_adjustment',
            'goods_movement_repair',
            'goods_movement_return',
            'goods_movement_scrap',
            'goods_movement_error',
            'production_result',
            'production_result_cancel',
            'production_result_adjustment',
            'production_result_repair',
            'production_result_return',
            'production_result_scrap',
            'production_result_error',
            'production_result_temporary',
        ];
        $this->interface_frequencies = [
            'Every Thirty Minutes',
            'Every Hour',
            '8AM 11AM 3PM 8PM',
            '7PM',
        ];
        $this->error_interface_frequencies = [
            '8AM 11AM 3PM 8PM',
            '5AM',
        ];
    }

    public function indexProductionResultTemporary()
    {
        $title = "Production Result Temporary";
        $title_jp = "";

        $materials = db::connection('ymes')->table('vm_item0010')
            ->whereNull('plant_spitem_status')
            ->whereNull('special_prc_type')
            ->select('item_code', 'item_name', 'unit_code', 'mrp_ctrl', 'issue_loc_code')
            ->get();

        $locations = db::table('storage_locations')
            ->whereNotNull('area')
            ->where('area', '<>', 'SUBCONT')
            ->orderBy('storage_location', 'ASC')
            ->get();

        return view(
            'transactions.temporary',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'materials' => $materials,
                'locations' => $locations,
            )
        )->with('page', 'Production Result Temporary')->with('head', 'Transaction');
    }

    public function indexProductionResult()
    {
        $title = "Input Production Result";
        $title_jp = "";

        $materials = db::connection('ymes')->table('vm_item0010')
            ->whereNull('plant_spitem_status')
            ->whereNull('special_prc_type')
            ->whereIn('eval_class_code', ['9030'])
            ->select('item_code', 'item_name', 'unit_code', 'mrp_ctrl', 'issue_loc_code')
            ->get();

        return view(
            'transactions.production_result',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'materials' => $materials,
            )
        )->with('page', 'Production Result')->with('head', 'Transaction');
    }

    public function indexGoodsMovement()
    {
        $title = "Input Goods Movement";
        $title_jp = "";

        $materials = db::connection('ymes')->table('vm_item0010')
            ->whereNull('plant_spitem_status')
            ->whereNull('special_prc_type')
            // ->whereIn('eval_class_code', ['9030'])
            ->select('item_code', 'item_name', 'unit_code', 'mrp_ctrl', 'issue_loc_code')
            ->get();

        $locations = db::table('storage_locations')
            ->whereNotNull('area')
            ->where('area', '<>', 'WAREHOUSE')
            ->where('area', '<>', 'SUBCONT')
            ->orderBy('storage_location', 'ASC')
            ->get();

        return view(
            'transactions.goods_movement',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'materials' => $materials,
                'locations' => $locations,
            )
        )->with('page', 'Goods Movement')->with('head', 'Transaction');
    }

    public function indexInventory()
    {
        $title = "Inventroy Information";
        $title_jp = "";

        return view(
            'transactions.inventory',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'materials' => $this->materials,
                'slocs' => $this->slocs,
            )
        )->with('page', 'Inventory')->with('head', 'Transaction');
    }

    public function indexHistory()
    {
        $title = "Transaction History";
        $title_jp = "";

        $materials = db::connection('ymes')->table('vm_item0010')
            ->whereNull('plant_spitem_status')
            ->whereNull('special_prc_type')
            ->select('item_code', 'item_name', 'unit_code', 'mrp_ctrl', 'issue_loc_code')
            ->get();

        $locations = db::table('storage_locations')
            ->whereNotNull('area')
            ->where('area', '<>', 'SUBCONT')
            ->orderBy('storage_location', 'ASC')
            ->get();

        return view(
            'transactions.history',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'transactions' => [],
                'materials' => $materials,
                'locations' => $locations,
                'categories' => $this->categories
            )
        )->with('page', 'Transaction History')->with('head', 'Transaction');
    }

    public function indexInterfaceSetting()
    {
        $title = "YMES Interface Setting";
        $title_jp = "";

        $transaction = db::connection('ympimis_2')->table('ymes_interface_settings')
            ->where('remark', '=', 'transaction')
            ->first();
        $error = db::connection('ympimis_2')->table('ymes_interface_settings')
            ->where('remark', '=', 'error')
            ->first();
        $setting_logs = db::connection('ympimis_2')->table('ymes_interface_setting_logs')
            ->orderBy('created_at', 'DESC')
            ->get();

        $employee_sync = db::table('employee_syncs')->get();
        $employees = [];
        for ($i = 0; $i < count($employee_sync); $i++) {
            $employees[$employee_sync[$i]->employee_id] = $employee_sync[$i];
        }

        return view(
            'transactions.interface_setting',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'transaction' => $transaction,
                'error' => $error,
                'setting_logs' => $setting_logs,
                'employees' => $employees,
                'interface_frequencies' => $this->interface_frequencies,
                'error_interface_frequencies' => $this->error_interface_frequencies,
            )
        )->with('page', 'YMES Interface Setting')->with('head', 'YMES Interface Setting');
    }

    public function updateInterfaceSetting(Request $request)
    {
        try {

            DB::beginTransaction();

            $exclude_setting = [];
            $material_numbers = [];
            $material_number = $request->get('materialNumberTags');
            if (strlen($material_number) > 0) {
                $material_numbers = explode(',', $material_number);
            }
            $row['material_number'] = implode(',', $material_numbers);

            $storage_locations = [];
            $storage_location = $request->get('storageLocationTags');
            if (strlen($storage_location) > 0) {
                $storage_locations = explode(',', $storage_location);
            }
            $row['storage_location'] = implode(',', $storage_locations);
            $exclude_setting = (object) $row;

            $setting = db::connection('ympimis_2')->table('ymes_interface_settings')
                ->where('remark', '=', $request->get('saveRemark'))
                ->first();

            $update_setting = db::connection('ympimis_2')->table('ymes_interface_settings')
                ->where('remark', '=', $request->get('saveRemark'))
                ->update([
                    'interface' => $request->get('saveInterface'),
                    'interface_frequency' => $request->get('saveInterfaceFrequency'),
                ]);

            $max_posting_date = null;
            if ($request->get('saveRemark') == 'transaction') {
                $delete_excludes = db::connection('ympimis_2')
                    ->table('ymes_interface_excludes')
                    ->whereNull('deleted_at')
                    ->delete();

                for ($i = 0; $i < count($material_numbers); $i++) {
                    $insert_excludes = db::connection('ympimis_2')
                        ->table('ymes_interface_excludes')
                        ->insert([
                            'type' => 'material_number',
                            'exculde_point' => $material_numbers[$i],
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                for ($i = 0; $i < count($storage_locations); $i++) {
                    $insert_excludes = db::connection('ympimis_2')
                        ->table('ymes_interface_excludes')
                        ->insert([
                            'type' => 'storage_location',
                            'exculde_point' => $storage_locations[$i],
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

                if (strlen($request->get('maxPostingDate')) > 0) {
                    $datetime = $request->get('maxPostingDate') . ' ' . $request->get('maxPostingTime') . ':59';
                    $max_posting_date = date('Y-m-d H:i:s', strtotime($datetime));

                    $insert_excludes = db::connection('ympimis_2')
                        ->table('ymes_interface_excludes')
                        ->insert([
                            'type' => 'result_date',
                            'exculde_point' => $max_posting_date,
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }

            }

            $log_setting = db::connection('ympimis_2')
                ->table('ymes_interface_setting_logs')
                ->insert([
                    'interface' => $request->get('saveInterface'),
                    'interface_frequency' => $request->get('saveInterfaceFrequency'),
                    'interface_start_time' => $setting->interface_start_time,
                    'remark' => $setting->remark,
                    'excludes' => json_encode($exclude_setting),
                    'max_result_date' => $max_posting_date,
                    'created_by' => Auth::user()->username,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => Auth::user()->username,
                ]);

            DB::commit();
            return back()->with('success', 'Interface setting successfully updated')->with('page', 'YMES Interface Setting');
        } catch (\Exception $e) {
            DB::rollback();
            $msg = strtolower($e->getMessage());
            if (str_contains($msg, 'duplicate')) {
                return back()->with('error', 'GMC atau Sloc double')->with('page', 'YMES Interface Setting');
            } else {
                return back()->with('error', $e->getMessage())->with('page', 'YMES Interface Setting');
            }
        }
    }

    public function fetchSettingExclude()
    {
        $exclude = db::connection('ympimis_2')
            ->table('ymes_interface_excludes')
            ->get();

        $response = array(
            'status' => true,
            'exclude' => $exclude,
        );
        return Response::json($response);

    }

    public function fetchProductionResultTemporary(Request $request)
    {

        $temp = db::connection('ympimis_2')
            ->table('production_result_temps');

        if ($request->get('material_number') != null) {
            $temp = $temp->whereIn('material_number', $request->get('material_number'));
        }

        if ($request->get('issue_location') != null) {
            $temp = $temp->whereIn('issue_location', $request->get('issue_location'));
        }

        $temp = $temp->get();

        return DataTables::of($temp)
            ->addColumn('sync', function ($temp) {
                return '<button style="width: 50%; height: 100%;" onclick="sync(\'' . $temp->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-remove"></i></span></button>';
            })
            ->rawColumns([
                'sync' => 'sync',
            ])
            ->make(true);

    }

    public function indexError()
    {

        $title = "Transaction Error";
        $title_jp = "";

        return view(
            'transactions.error',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Transaction Error')->with('head', 'Transaction');
    }

    public function fetchError(Request $request)
    {
        $transactions = db::connection('ympimis_2')->select("
            SELECT
            *
            FROM
            (
                SELECT
                pr.id,
                pr.category,
                pr.action,
                date_format( pr.result_date, '%Y-%m-%d' ) AS posting_date,
                pr.created_at AS entry_date,
                pr.slip_number,
                pr.serial_number,
                pr.material_number,
                pr.material_description,
                pr.issue_location,
                '' AS receive_location,
                pr.quantity,
                pr.created_by,
                pr.created_by_name,
                pr.created_at,
                pr.synced,
                pr.synced_by
                FROM
                production_results AS pr
                WHERE
                pr.deleted_at IS NULL
                AND pr.category LIKE '%error'
                AND pr.synced IS NULL
                UNION ALL
                SELECT
                gm.id,
                gm.category,
                gm.action,
                gm.result_date AS posting_date,
                gm.created_at AS entry_date,
                gm.slip_number,
                gm.serial_number,
                gm.material_number,
                gm.material_description,
                gm.issue_location,
                gm.receive_location,
                gm.quantity,
                gm.created_by,
                gm.created_by_name,
                gm.created_at,
                gm.synced,
                gm.synced_by
                FROM
                goods_movements AS gm
                WHERE
                gm.deleted_at IS NULL
                AND gm.category LIKE '%error'
                AND gm.synced IS NULL
                ) AS t
            LEFT JOIN ( SELECT err_code, error_message FROM ymes_error_codes ) AS e ON e.err_code = t.slip_number
            ORDER BY
            created_at DESC"
        );

        return DataTables::of($transactions)
            ->addColumn('sync', function ($data) {
                if ($data->synced == null || $data->synced == '') {
                    return '<button style="width: 50%; height: 100%;" onclick="sync(\'' . $data->id . '\', \'' . $data->action . '\')" class="btn btn-xs btn-warning form-control"><span><i class="fa fa-refresh"></i></span></button>';
                } else {
                    return $data->synced_by;
                }
            })
            ->addColumn('del', function ($data) {
                if ($data->synced == null || $data->synced == '') {
                    return '<button style="width: 50%; height: 100%;" onclick="del(\'' . $data->id . '\', \'' . $data->action . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
                } else {
                    return "-";
                }
            })
            ->addColumn('error', function ($data) {
                if ($data->error_message == null || $data->error_message == '') {
                    return $data->slip_number . ' (-)';
                } else {
                    return $data->slip_number . ' (' . $data->error_message . ')';
                }
            })
            ->rawColumns([
                'sync' => 'sync',
                'error' => 'error',
                'del' => 'del',
            ])
            ->make(true);
    }

    public function fetchHistory(Request $request)
    {

        $where_category = "";
        $where_posting_date = "";
        $where_entry_date = "";
        $where_material_number = "";
        $where_issue_location = "";
        $where_receive_location = "";
        $where_receive_location_pr = "";
        $where_sync = "";
        $where_product = "";

        if ($request->get('filterProduct') == 'FG') {
            $where_product = "AND serial_number IS NOT NULL";
        }
        if ($request->get('filterProduct') == 'WIP') {
            $where_product = "AND serial_number IS NULL";
        }

        if ($request->get('filterCategory') != null) {
            $categories = $request->get('filterCategory');
            for ($i = 0; $i < count($categories); $i++) {
                $where_category = $where_category . "'" . $categories[$i] . "'";
                if ($i != (count($categories) - 1)) {
                    $where_category = $where_category . ',';
                }
            }
            $where_category = " AND category IN (" . $where_category . ") ";
        }

        if (strlen($request->get('filterPostingDate')) > 0) {
            $posting_date = explode(' - ', $request->get('filterPostingDate'));
            $posting_date_from = date('Y-m-d', strtotime($posting_date[0]));
            $posting_date_to = date('Y-m-d', strtotime($posting_date[1]));
            $where_posting_date = "AND DATE_FORMAT(result_date, '%Y-%m-%d') >= '" . $posting_date_from . "' AND DATE_FORMAT(result_date, '%Y-%m-%d') <= '" . $posting_date_to . "'";
        }
        if (strlen($request->get('filterEntryDate')) > 0) {
            $entry_date = explode(' - ', $request->get('filterEntryDate'));
            $entry_date_from = date('Y-m-d H:i:s', strtotime($entry_date[0]));
            $entry_date_to = date('Y-m-d H:i:s', strtotime($entry_date[1]));
            $where_entry_date = "AND created_at >= '" . $entry_date_from . "' AND created_at <= '" . $entry_date_to . "'";
        }
        if ($request->get('filterMaterial') != null) {
            $material_numbers = $request->get('filterMaterial');
            for ($i = 0; $i < count($material_numbers); $i++) {
                $where_material_number = $where_material_number . "'" . $material_numbers[$i] . "'";
                if ($i != (count($material_numbers) - 1)) {
                    $where_material_number = $where_material_number . ',';
                }
            }
            $where_material_number = " AND material_number IN (" . $where_material_number . ") ";
        }
        if ($request->get('filterIssue') != null) {
            $issue_locations = $request->get('filterIssue');
            for ($i = 0; $i < count($issue_locations); $i++) {
                $where_issue_location = $where_issue_location . "'" . $issue_locations[$i] . "'";
                if ($i != (count($issue_locations) - 1)) {
                    $where_issue_location = $where_issue_location . ',';
                }
            }
            $where_issue_location = " AND issue_location IN (" . $where_issue_location . ") ";
        }
        if ($request->get('filterReceive') != null) {
            $receive_locations = $request->get('filterReceive');
            for ($i = 0; $i < count($receive_locations); $i++) {
                $where_receive_location = $where_receive_location . "'" . $receive_locations[$i] . "'";
                if ($i != (count($receive_locations) - 1)) {
                    $where_receive_location = $where_receive_location . ',';
                }
            }
            $where_receive_location = " AND receive_location IN (" . $where_receive_location . ") ";
            $where_receive_location_pr = "AND id IS NULL";
        }
        if (strlen($request->get('filterSync')) > 0) {
            if ($request->get('filterSync') == 0) {
                $where_sync = "AND synced IS NULL";
            } else {
                $where_sync = "AND synced IS NOT NULL";
            }
        }

        set_time_limit(0);
        ini_set('memory_limit', -1);
        ob_start();

        $transactions = db::connection('ympimis_2')->select("
            SELECT
            *
            FROM
            (
                SELECT
                pr.id,
                pr.reference_number,
                pr.category,
                pr.action,
                date_format( pr.result_date, '%Y-%m-%d' ) AS posting_date,
                pr.created_at AS entry_date,
                pr.slip_number,
                pr.serial_number,
                pr.material_number,
                pr.material_description,
                pr.issue_location,
                '' AS receive_location,
                pr.quantity,
                pr.created_by,
                pr.created_by_name,
                pr.created_at,
                pr.synced,
                pr.synced_by
                FROM
                production_results AS pr
                WHERE
                pr.deleted_at IS NULL
                " . $where_category . "
                " . $where_posting_date . "
                " . $where_entry_date . "
                " . $where_material_number . "
                " . $where_issue_location . "
                " . $where_receive_location_pr . "
                " . $where_sync . "
                " . $where_product . "
                UNION ALL
                SELECT
                gm.id,
                gm.reference_number,
                gm.category,
                gm.action,
                date_format( gm.result_date, '%Y-%m-%d' ) AS posting_date,
                gm.created_at AS entry_date,
                gm.slip_number,
                gm.serial_number,
                gm.material_number,
                gm.material_description,
                gm.issue_location,
                gm.receive_location,
                gm.quantity,
                gm.created_by,
                gm.created_by_name,
                gm.created_at,
                gm.synced,
                gm.synced_by
                FROM
                goods_movements AS gm
                WHERE
                gm.deleted_at IS NULL
                " . $where_category . "
                " . $where_posting_date . "
                " . $where_entry_date . "
                " . $where_material_number . "
                " . $where_issue_location . "
                " . $where_receive_location . "
                " . $where_sync . "
                " . $where_product . "
                ) AS t
                ORDER BY
                created_at DESC"
        );

        ob_end_flush();
        ob_flush();
        flush();

        return DataTables::of($transactions)
            ->addColumn('sync', function ($data) {
                if (($data->synced == null || $data->synced == '') && $data->quantity > 0) {
                    return '<button style="width: 50%; height: 100%;" onclick="sync(\'' . $data->id . '\', \'' . $data->action . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-refresh"></i></span></button>';
                } else {
                    return $data->synced_by;
                }
            })
            ->rawColumns(['sync' => 'sync'])
            ->make(true);

    }

    public function syncTemporary(Request $request)
    {

        $now = date('Y-m-d H:i:s');

        DB::beginTransaction();
        try {

            $temp = db::connection('ympimis_2')
                ->table('production_result_temps')
                ->where('id', $request->get('id'))
                ->first();

            $delete = db::connection('ympimis_2')
                ->table('production_result_temps')
                ->where('id', $request->get('id'))
                ->delete();

            $completion_log = db::connection('ympimis_2')
                ->table('production_result_interface_logs')
                ->insert([
                    'category' => 'production_temporary',
                    'function' => 'YMES_30-39',
                    'action' => 'production_result',
                    'result_date' => $temp->result_date,
                    'reference_number' => $temp->reference_number,
                    'slip_number' => $temp->slip_number,
                    'serial_number' => $temp->serial_number,
                    'material_number' => $temp->material_number,
                    'issue_location' => $temp->issue_location,
                    'mstation' => $temp->mstation,
                    'quantity' => $temp->quantity,
                    'synced' => $now,
                    'synced_by' => 'manual',
                    'sync_remark' => 'directly_executed_to_ymes',
                    'remark' => 'YMES',
                    'created_by' => strtoupper(Auth::user()->username),
                    'created_by_name' => ucwords(Auth::user()->name),
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Production result temporary synced successfullly',
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

    public function syncAllTemporary(Request $request)
    {

        $now = date('Y-m-d H:i:s');
        $where_issue_location = '';
        $where_material_number = '';

        if ($request->get('issue_location') != null) {
            $issue_locations = $request->get('issue_location');
            for ($i = 0; $i < count($issue_locations); $i++) {
                $where_issue_location = $where_issue_location . "'" . $issue_locations[$i] . "'";
                if ($i != (count($issue_locations) - 1)) {
                    $where_issue_location = $where_issue_location . ',';
                }
            }
            $where_issue_location = " AND issue_location IN (" . $where_issue_location . ") ";
        }

        if ($request->get('material_number') != null) {
            $material_numbers = $request->get('material_number');
            for ($i = 0; $i < count($material_numbers); $i++) {
                $where_material_number = $where_material_number . "'" . $material_numbers[$i] . "'";
                if ($i != (count($material_numbers) - 1)) {
                    $where_material_number = $where_material_number . ',';
                }
            }
            $where_material_number = " AND material_number IN (" . $where_material_number . ") ";
        }

        $temp = db::connection('ympimis_2')
            ->select("SELECT * FROM production_result_temps
                WHERE deleted_at IS NULL "
                . $where_issue_location . " "
                . $where_material_number);

        if (count($temp) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Data not found',
            );
            return Response::json($response);
        }

        DB::beginTransaction();
        try {

            for ($i = 0; $i < count($temp); $i++) {

                $completion_log = db::connection('ympimis_2')
                    ->table('production_result_interface_logs')
                    ->insert([
                        'category' => $temp[$i]->category,
                        'function' => $temp[$i]->function,
                        'action' => $temp[$i]->action,
                        'result_date' => $temp[$i]->result_date,
                        'reference_number' => $temp[$i]->reference_number,
                        'slip_number' => $temp[$i]->slip_number,
                        'serial_number' => $temp[$i]->serial_number,
                        'material_number' => $temp[$i]->material_number,
                        'issue_location' => $temp[$i]->issue_location,
                        'mstation' => $temp[$i]->mstation,
                        'quantity' => $temp[$i]->quantity,
                        'synced' => $now,
                        'synced_by' => strtoupper(Auth::user()->username),
                        'sync_remark' => 'directly_executed_on_ymes_or_sap',
                        'remark' => $temp[$i]->remark,
                        'created_by' => 'System',
                        'created_by_name' => 'System',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
            }

            $delete = db::connection('ympimis_2')
                ->select("DELETE FROM production_result_temps
                    WHERE deleted_at IS NULL "
                    . $where_issue_location . " "
                    . $where_material_number);

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Production result temporary synced successfullly',
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

    public function syncTransactionAll(Request $request)
    {
        try {

        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function syncTransaction(Request $request)
    {

        $batch_time = date('Y-m-d H:i:s');
        $send_data_no = md5(uniqid('', true));

        try {
            if ($request->get('action') == 'production_result') {
                $transaction = db::connection('ympimis_2')->table('production_results')->where('id', '=', $request->get('id'))->first();

                $input_type = 1;
                if ($transaction->quantity < 0) {
                    $input_type = 2;
                }

                $insert_pr = db::connection('ymes')
                    ->table('i_ext0010')
                    ->insert([
                        'plant_code' => '8190',
                        'send_data_no' => $send_data_no,
                        'send_type_id' => 'MIRAI',
                        'send_mgt_no' => null,
                        'ext_result_type' => '21',
                        'prod_individual_id' => null,
                        'work_order_no' => null,
                        'serial_no' => strtoupper($transaction->serial_number),
                        'item_code' => strtoupper($transaction->material_number),
                        'input_type' => $input_type,
                        'qty' => $transaction->quantity,
                        'defect_qty' => null,
                        'reason_code' => null,
                        'start_work_datetime' => null,
                        'end_work_datetime' => $transaction->result_date,
                        'man_stat_cd' => strtoupper($transaction->mstation),
                        'dest_location_code' => strtoupper($transaction->issue_location),
                        'prod_stop_type' => null,
                        'staff_id' => null,
                        'machine_id' => null,
                        'ot_resource_id' => null,
                        'instid' => '',
                        'instdt' => $batch_time,
                        'instterm' => '',
                        'instprgnm' => '',
                        'updtid' => '',
                        'updtdt' => $batch_time,
                        'updtterm' => '',
                        'updtprgnm' => '',
                    ]);

                $transaction_update = db::connection('ympimis_2')->table('production_results')->where('id', '=', $request->get('id'))
                    ->update([
                        'synced' => $batch_time,
                        'synced_by' => Auth::user()->username,
                    ]);
            }

            if ($request->get('action') == 'goods_movement') {
                $transaction = db::connection('ympimis_2')->table('goods_movements')->where('id', '=', $request->get('id'))->first();

                $ext_move_type = '11';
                if (strlen($transaction->serial_number) > 0) {
                    $ext_move_type = '12';
                }

                $insert_gm = db::connection('ymes')
                    ->table('i_ext0020')
                    ->insert([
                        'plant_code' => '8190',
                        'send_data_no' => $send_data_no,
                        'send_type_id' => $transaction->remark,
                        'send_mgt_no' => null,
                        'ext_move_type' => $ext_move_type,
                        'result_date' => $transaction->result_date,
                        'issue_loc_code' => strtoupper($transaction->issue_location),
                        'in_loc_code' => strtoupper($transaction->receive_location),
                        'issue_strg_area_id' => null,
                        'in_strg_area_id' => null,
                        'qty' => $transaction->quantity,
                        'item_code' => strtoupper($transaction->material_number),
                        'serial_no' => strtoupper($transaction->serial_number),
                        'idtag_label_no' => null,
                        'trace_label_no' => null,
                        'prod_individual_id' => null,
                        'wrapping_no' => null,
                        'picking_no' => null,
                        'staff_id' => null,
                        'machine_id' => null,
                        'ot_resource_id' => null,
                        'instid' => '',
                        'instdt' => $batch_time,
                        'instterm' => '',
                        'instprgnm' => '',
                        'updtid' => '',
                        'updtdt' => $batch_time,
                        'updtterm' => '',
                        'updtprgnm' => '',
                    ]);

                $transaction_update = db::connection('ympimis_2')->table('goods_movements')->where('id', '=', $request->get('id'))
                    ->update([
                        'synced' => $batch_time,
                        'synced_by' => Auth::user()->username,
                    ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Transaction has been synced to YMES',
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

    public function deleteTransaction(Request $request)
    {

        try {
            if ($request->get('action') == 'production_result') {
                $soft_delete = db::connection('ympimis_2')
                    ->table('production_results')
                    ->where('id', '=', $request->get('id'))
                    ->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            if ($request->get('action') == 'goods_movement') {
                $soft_delete = db::connection('ympimis_2')
                    ->table('goods_movements')
                    ->where('id', '=', $request->get('id'))
                    ->update([
                        'deleted_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Transaction has been deleted',
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

    public function fetchInventoryCheck(Request $request)
    {
        try {
            if ($request->get('category') == 'goods_movement') {

                $inventory = db::connection('ymes')->table('vd_mes0010')
                    ->where('item_code', '=', $request->get('material_number'))
                    ->where('location_code', '=', $request->get('issue_location'))
                    ->select('item_code', 'location_code', 'stockqty', 'inspect_qty', 'keep_qty')
                    ->first();

                if (!$inventory) {
                    $inventory = new \stdClass();
                    $inventory->item_code = $request->get('material_number');
                    $inventory->location_code = $request->get('issue_location');
                    $inventory->stockqty = 0;
                    $inventory->inspect_qty = 0;
                    $inventory->keep_qty = 0;
                }

                if ($request->get('quantity')) {
                    if ($inventory->stockqty - $request->get('quantity') < 0) {

                        $minus = $inventory->stockqty - $request->get('quantity');
                        $response = array(
                            'status' => false,
                            'message' => 'Stock tidak mencukupi cek YMES 00-45.' . $request->get('material_number') . ' ' . $minus,
                        );
                        return Response::json($response);
                    }
                }

                $response = array(
                    'status' => true,
                    'inventory' => $inventory,
                );
                return Response::json($response);

            }

            if ($request->get('category') == 'production_result') {
                $material = db::connection('ymes')->table('vm_item0010')
                    ->where('item_code', '=', $request->get('material_number'))
                    ->first();

                $inventory = db::connection('ymes')->select("SELECT
                        b.par_item_code,
                        b.cld_item_code,
                        '" . $material->issue_loc_code . "' AS location_code,
                        " . $request->get('quantity') . " * b.bom_qty AS backflush,
                        COALESCE ( s.stockqty, 0 ) AS stockqty,
                        COALESCE(s.stockqty, 0) - ( " . $request->get('quantity') . " * b.bom_qty ) AS diff
                        FROM
                        vm_item0070 AS b
                        LEFT JOIN ( SELECT item_code, stockqty FROM vd_mes0010 WHERE location_code = '" . $material->issue_loc_code . "' ) AS s ON b.cld_item_code = s.item_code
                        WHERE
                        b.par_item_code = '" . $request->get('material_number') . "'
                        AND b.valid_to_date >= '" . date('Y-m-d') . "'
                        AND b.valid_from_date < '" . $request->get('date') . "'");
                $status = true;
                foreach ($inventory as $row) {
                    if ($request->get('quantity') > 0) {
                        if ($row->diff < 0) {
                            $status = false;
                        }
                    }
                }

                if ($status == false) {
                    $response = array(
                        'status' => false,
                        'message' => 'Stock backflush tidak mencukupi cek YMES 00-45.',
                    );
                    return Response::json($response);
                }

                $response = array(
                    'status' => true,
                    'inventory' => $inventory,
                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function inputGoodsMovement(Request $request)
    {
        $goods_movements = $request->get('goods_movements');

        foreach ($goods_movements as $goods_movement) {

            $category = 'goods_movement';
            $function = 'inputGoodsMovement';
            $action = 'goods_movement';
            $result_date = $goods_movement['date'];
            $slip_number = null;
            $serial_number = null;
            $material_number = $goods_movement['material_number'];
            $material_description = $goods_movement['material_description'];
            $issue_location = $goods_movement['issue_location'];
            $receive_location = $goods_movement['receive_location'];
            $quantity = $goods_movement['quantity'];
            $remark = 'MIRAI';
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;
            $synced = null;
            $synced_by = null;

            // $transaction_transfer = db::table('transaction_transfers')
            //     ->insert([
            //         'plant' => '8190',
            //         'material_number' => $goods_movement['material_number'],
            //         'issue_plant' => '8190',
            //         'issue_location' => $goods_movement['issue_location'],
            //         'receive_plant' => '8190',
            //         'receive_location' => $goods_movement['receive_location'],
            //         'transaction_code' => 'MB1B',
            //         'movement_type' => '9I3',
            //         'quantity' => $goods_movement['quantity'],
            //         'created_by' => Auth::id(),
            //         'created_at' => date('Y-m-d H:i:s'),
            //         'updated_at' => date('Y-m-d H:i:s'),
            //     ]);

            self::goods_movement(
                $category,
                $function,
                $action,
                $result_date,
                $slip_number,
                $serial_number,
                $material_number,
                $issue_location,
                $receive_location,
                $quantity,
                $remark,
                $created_by,
                $created_by_name,
                $synced,
                $synced_by,
                $material_description
            );
        }

        $response = array(
            'status' => true,
            'message' => 'Goods Movement Has Been Inputted',
        );
        return Response::json($response);

    }

    public function inputProductionResult(Request $request)
    {
        $production_results = $request->get('production_results');

        foreach ($production_results as $production_result) {
            $category = 'production_result';
            if ($production_result['quantity'] < 0) {
                $category = 'production_result_cancel';
            }
            $function = 'inputProductionResult';
            $action = 'production_result';
            $result_date = $production_result['date'];
            $slip_number = null;
            $serial_number = null;
            $material_number = $production_result['material_number'];
            $material_description = $production_result['material_description'];
            $issue_location = $production_result['location'];
            $mstation = $production_result['mstation'];
            $quantity = $production_result['quantity'];
            $remark = 'MIRAI';
            $created_by = Auth::user()->username;
            $created_by_name = Auth::user()->name;
            $synced = null;
            $synced_by = null;

            // $transaction_completion = db::table('transaction_completions')
            //     ->insert([
            //         'material_number' => $production_result['material_number'],
            //         'issue_plant' => '8190',
            //         'issue_location' => $production_result['location'],
            //         'quantity' => $production_result['quantity'],
            //         'movement_type' => '101',
            //         'created_by' => Auth::id(),
            //         'created_at' => date('Y-m-d H:i:s'),
            //         'updated_at' => date('Y-m-d H:i:s'),
            //     ]);

            self::production_result(
                $category,
                $function,
                $action,
                $result_date,
                $slip_number,
                $serial_number,
                $material_number,
                $issue_location,
                $mstation,
                $quantity,
                $remark,
                $created_by,
                $created_by_name,
                $synced,
                $synced_by,
                $material_description
            );
        }

        $response = array(
            'status' => true,
            'message' => 'Production Result Has Been Inputted',
        );
        return Response::json($response);
    }

    public function indexBomMultilevel(Request $request)
    {
        $title = "Bom Multilevel";
        $title_jp = "";

        return view(
            'transactions.ymes.bom_multilevel',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'BOM Multilevel');

    }

    public function fetchBomMultilevel(Request $request)
    {
        $material_numbers = $request->get('material_number');
        $material_numbers = ['WZ00190'];
        if (count($material_numbers) <= 0) {
            $response = array(
                'status' => false,
                'message' => 'Material tidak valid',
            );
            return Response::json($response);
        }
        $multilevels = array();
        $text = "";

        for ($i = 0; $i < count($material_numbers); $i++) {
            $boms = db::table('bom_outputs')
                ->where('material_parent', '=', $material_numbers[$i])
                ->get();

            $level = 0;
            $parent = $material_numbers[$i];
            $usage = 1;

            $row = array();
            $row['level'] = $level;
            $row['parent'] = $parent;
            $row['child'] = $parent;
            $row['usage'] = $usage;
            $multilevels[] = (object) $row;
            $text .= $level;
            $text .= $parent;
            $text .= $parent;
            $text .= $usage;
            $text .= "\r\n";

            // START BREAKDOWN
            $checks = array();
            for ($j = 0; $j < count($boms); $j++) {
                $row['parent'] = $parent;
                $row['child'] = $boms[$j]->material_child;
                $row['usage'] = $boms[$i]->usage / $boms[$i]->divider;
                $checks[] = $row;

            }

            while (count($checks) > 0) {
                $level++;
                $temps = array();

                for ($x = 0; $x < count($checks); $x++) {
                    $bom = db::table('bom_outputs')
                        ->where('material_parent', '=', $checks[$x]['child'])
                        ->get();

                    if (count($bom) > 0) {
                        for ($y = 0; $y < count($bom); $y++) {
                            $row = array();
                            $row['parent'] = $parent;
                            $row['child'] = $bom[$y]->material_child;
                            $row['usage'] = $checks[$x]['usage'] * ($bom[$y]->usage / $bom[$y]->divider);
                            $temps[] = $row;
                        }

                    }

                    $row = array();
                    $row['level'] = $level;
                    $row['parent'] = $parent;
                    $row['child'] = $checks[$x]['child'];
                    $row['usage'] = $checks[$x]['usage'];
                    $multilevels[] = $row;
                    $text .= $level;
                    $text .= $parent;
                    $text .= $checks[$x]['child'];
                    $text .= $checks[$x]['usage'];
                    $text .= "\r\n";

                }

                $checks = [];
                $checks = $temps;
            }
        }

        dd($multilevels);

        $filename = date('YmdHis') . '.txt';
        $filepath = public_path() . "/multilevel/" . $filename;

        // if (File::exists($filename)) {
        //     File::delete($filepath);
        // }
        // File::put($filepath, $text);

        $response = array(
            'status' => true,
            'multilevels' => $multilevels,
        );
        return Response::json($response);

    }

    public function production_result($category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $mstation, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description = null)
    {

        $production_result = db::connection('ympimis_2')->table('production_results')->insert([
            'category' => $category,
            'function' => $function,
            'action' => $action,
            'result_date' => $result_date,
            'reference_number' => null,
            'slip_number' => $slip_number,
            'serial_number' => $serial_number,
            'material_number' => $material_number,
            'material_description' => $material_description,
            'issue_location' => $issue_location,
            'mstation' => $mstation,
            'quantity' => $quantity,
            'synced' => $synced,
            'synced_by' => $synced_by,
            'remark' => $remark,
            'created_by' => strtoupper($created_by),
            'created_by_name' => $created_by_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function goods_movement($category, $function, $action, $result_date, $slip_number, $serial_number, $material_number, $issue_location, $receive_location, $quantity, $remark, $created_by, $created_by_name, $synced, $synced_by, $material_description = null)
    {

        $goods_movement = db::connection('ympimis_2')->table('goods_movements')->insert([
            'category' => $category,
            'function' => $function,
            'action' => $action,
            'result_date' => $result_date,
            'reference_number' => null,
            'slip_number' => $slip_number,
            'serial_number' => $serial_number,
            'material_number' => $material_number,
            'material_description' => $material_description,
            'issue_location' => $issue_location,
            'receive_location' => $receive_location,
            'quantity' => $quantity,
            'synced' => $synced,
            'synced_by' => $synced_by,
            'remark' => $remark,
            'created_by' => strtoupper($created_by),
            'created_by_name' => $created_by_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function register_shipment($plant_code, $ship_list_no, $ship_to_code, $invoice_no, $container_no, $vanning_date, $etd_date, $memo, $ship_reg_type, $serial_no, $item_code, $instid, $instdt, $instterm, $instprgnm, $updtid, $updtdt, $updtterm, $updtprgnm, $category, $action, $function, $remark, $created_by, $created_by_name, $sync = false)
    {

        $synced = date('Y-m-d H:i:s');
        $synced_by = 'System';
        $batch = db::connection('ympimis_2')->table('ymes_interface_settings')->first();

        if ($batch->interface == 0) {
            $synced = null;
            $synced_by = null;
        }

        if ($sync == true) {
            $synced = date('Y-m-d H:i:s');
            $synced_by = Auth::user()->username;
        }

        // Force IF shipment
        $synced = date('Y-m-d H:i:s');

        if ($synced != null) {
            try {
                $ymes_shipment = db::connection('ymes')
                    ->table('i_ext1010')
                    ->insert([
                        'plant_code' => $plant_code,
                        'ship_list_no' => $ship_list_no,
                        'ship_to_code' => $ship_to_code,
                        'invoice_no' => $invoice_no,
                        'container_no' => $container_no,
                        'vanning_date' => $vanning_date,
                        'etd_date' => $etd_date,
                        'memo' => $memo,
                        'ship_reg_type' => $ship_reg_type,
                        'serial_no' => $serial_no,
                        'item_code' => $item_code,
                        'instid' => $instid,
                        'instdt' => $instdt,
                        'instterm' => $instterm,
                        'instprgnm' => $instprgnm,
                        'updtid' => $updtid,
                        'updtdt' => $updtdt,
                        'updtterm' => $updtterm,
                        'updtprgnm' => $updtprgnm,
                    ]);
            } catch (\Exception $e) {
                $ymes_error_log = DB::connection('ympimis_2')->table('ymes_error_gm_logs')
                    ->insert([
                        'category' => $category,
                        'action' => $action,
                        'error_message' => '[ ' . $function . '] ' . $e->getMessage(),
                        'remark' => $remark,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'created_at' => date('Y-m-d H:i:s'),
                        'plant_code' => $plant_code,
                        'ship_list_no' => $ship_list_no,
                        'ship_to_code' => $ship_to_code,
                        'invoice_no' => $invoice_no,
                        'container_no' => $container_no,
                        'vanning_date' => $vanning_date,
                        'etd_date' => $etd_date,
                        'memo' => $memo,
                        'ship_reg_type' => $ship_reg_type,
                        'serial_no' => $serial_no,
                        'item_code' => $item_code,
                        'instid' => $instid,
                        'instdt' => $instdt,
                        'instterm' => $instterm,
                        'instprgnm' => $instprgnm,
                        'updtid' => $updtid,
                        'updtdt' => $updtdt,
                        'updtterm' => $updtterm,
                        'updtprgnm' => $updtprgnm,
                    ]);

                $synced = null;
                $synced_by = null;
            }
        }

        $shipment = db::connection('ympimis_2')->table('register_shipments')->insert([
            'category' => $category,
            'function' => $function,
            'action' => $action,
            'ship_list_no' => $ship_list_no,
            'ship_to_code' => $ship_to_code,
            'invoice_no' => $invoice_no,
            'container_no' => $container_no,
            'vanning_date' => $vanning_date,
            'etd_date' => $etd_date,
            'memo' => $memo,
            'ship_reg_type' => $ship_reg_type,
            'serial_no' => $serial_no,
            'item_code' => $item_code,
            'synced' => $synced,
            'synced_by' => $synced_by,
            'remark' => $remark,
            'created_by' => $created_by,
            'created_by_name' => $created_by_name,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
    }

    public function run_shipment_trigger($category, $action, $function, $remark)
    {
        try {

            $batch = db::connection('ympimis_2')->table('ymes_interface_settings')->first();

            if ($batch->interface == 1) {
                $trigger = DB::connection('ymes')->select("select fn_i_ext1010_d_mes0200()");
                return $trigger;
            }
        } catch (\Exception $e) {
            $ymes_error_log = DB::connection('ympimis_2')->table('ymes_error_triggers')
                ->insert([
                    'category' => $category,
                    'action' => $action,
                    'error_message' => '[ ' . $function . '] ' . $e->getMessage(),
                    'remark' => $remark,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
        }
    }
}

// FLOController (FG) OK TESTED
// MaedaoshiController (FG) OK TESTED
// KnockDownController (KD) OK TESTED
// ScrapController (Scrap) OK TESTED
// TransactionController (Return/Repair/Middle) OK TESTED
// ExtraOrderController OK TESTED
// InjectionController OK TESTED
// MouthpieceController OK TESTED
// ReedSyntheticController OK TESTED

// CompletionController (KITTO) OK TESTED
// TransferController (KITTO) OK TESTED

// TABLE
// i_ext0010        I/F Production Result
// i_ext0020        I/F Goods Movement
// i_ext1010        I/F Shipment

// VIEW
// vd_mes0010       Inventory information
// vd_mes0020       Entry / exit information VIEW
// vd_mes0040       Warehouse label information VIEW
// vd_mes0050       Warehouse inspection label information
// vd_mes0080       Picking instruction information
// vd_mes0090       Picking spot label information
// vd_mes0110_010   Manufacturing instruction information
// vd_mes0120       Manufacturing record information VIEW
// vd_mes0120_020   Manufacturing record information 2VIEW
// vd_mes0130       Manufacturing work status information VIEW
// vd_mes0200       Product shipping information VIEW
// vd_mes0210       Product shipment serial number information VIEW
// vd_mes0290       Serial number information VIEW
// vd_mes1020       Warehouse inventory label information VIEW
// vd_mes1050       Inventory record detail information VS.SAP (Inventory difference information VS.SAP) VIEW
// vd_mes1090       Tanden list information VIEW
// vd_mes1100       Inventory list detail information VIEW
// vd_sap0010       Production order information
// vd_sap0050       Purchase order information VIEW
// vd_sap0070       SAP inventory information VIEW
// vd_sap0100       Purchase Order Scheduled Delivery Information VIEW
// vd_sap0120_010   Entry/exit slip information VIEW
// vm_item0010      Item Plant Master VIEW
// vm_item0070      Configuration master (BOM)
// vm_item0080      Shipment master
// vm_item0170      Item name Master VIEW
// vm_item0230      Work procedure master
// vm_item0260      Purchasing Information Master VIEW
// vm_item0270      Purchasing condition master VIEW
// vm_mes0010       SCM type master VIEW
// vm_proc0010      Work area master VIEW
// vm_proc0020      Storage location master VIEW
// vm_proc0050      Storage bin master VIEW
// vm_proc0070      M station master VIEW
// vm_proc0090      Production line master VIEW
// vm_supp0010      Supplier master VIEW
// vm_supp0020      Supplier company master VIEW
// vm_supp0030      Supplier Purchasing Organization Master VIEW
