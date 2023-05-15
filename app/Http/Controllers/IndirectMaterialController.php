<?php

namespace App\Http\Controllers;

use App\ChemicalControlLog;
use App\ChemicalConvertion;
use App\ChemicalSolution;
use App\ChemicalSolutionComposer;
use App\CodeGenerator;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\IndirectMaterial;
use App\IndirectMaterialLog;
use App\IndirectMaterialOut;
use App\IndirectMaterialPick;
use App\IndirectMaterialSchedule;
use App\IndirectMaterialStock;
use App\Inventory;
use App\MaterialPlantDataList;
use Carbon\Carbon;
use DataTables;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Response;
use Yajra\DataTables\Exception;

class IndirectMaterialController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        $this->subtitle = 'Chemical';
        $this->subtitle_jp = '';
    }

    public function indexIndirectMaterialMonitoring()
    {
        $title = 'Expired Monitoring';

        return view('indirect_material.monitoring', array(
            'title' => $title,
            'title_jp' => '',
        ))->with('head', 'Indirect Material')->with('page', 'Monitoring');
    }

    public function indexLarutan()
    {
        $title = 'Larutan';

        return view('indirect_material.chemical.larutan', array(
            'title' => $title,
        ))->with('head', 'Chemical')->with('page', 'Larutan');
    }

    public function indexSolutionControl()
    {
        $title = 'Chemical Solution Control';
        $title_jp = ' 薬品管理表示';

        $solutions = db::select("SELECT * FROM chemical_solutions ORDER BY location ASC");

        return view('indirect_material.chemical.solution_control', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'solutions' => $solutions,
        ))->with('head', 'Chemical')->with('page', 'Chemical Solution Control');
    }

    public function indexPickingSchedule()
    {
        $title = 'Chemical Picking Schedule';
        $title_jp = ' 薬品取出し日程';

        $larutans = ChemicalSolution::get();

        $materials = ChemicalSolutionComposer::distinct()
            ->select('material_number', 'material_description')
            ->orderBy('material_description', 'ASC')
            ->get();

        $addition_materials = db::select("SELECT DISTINCT c.solution_id, c.solution_name, l.location FROM chemical_solution_composers c
			LEFT JOIN chemical_solutions l ON l.id = c.solution_id
			WHERE c.addition = 1");

        return view('indirect_material.chemical.schedule', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'larutans' => $larutans,
            'materials' => $materials,
            'addition_materials' => $addition_materials,
            'new_materials' => $larutans,
        ))->with('head', 'Chemical')->with('page', 'Chemical Picking Schedule');
    }

    public function indexIndirectMaterialLog()
    {
        $title = 'Indirect Material Logs';
        $title_jp = '';

        $materials = IndirectMaterial::select('material_number', 'material_description')->get();

        return view('indirect_material.logs', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'subtitle' => $this->subtitle,
            'subtitle_jp' => $this->subtitle_jp,
            'materials' => $materials,
        ))->with('head', 'Indirect Material')->with('page', 'Request');
    }

    public function indexRequest()
    {
        $title = 'Indirect Material Request';
        $title_jp = ' 間接材料の依頼';

        $locations = db::select("SELECT DISTINCT location FROM chemical_solutions
			ORDER BY location ASC");

        return view('indirect_material.chemical.request', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'subtitle' => $this->subtitle,
            'subtitle_jp' => $this->subtitle_jp,
            'locations' => $locations,
        ))->with('head', 'Chemical')->with('page', 'Request');
    }

    public function indexStock()
    {
        $title = 'Indirect Material Stock';
        $title_jp = '';

        $materials = IndirectMaterial::get();

        return view('indirect_material.stock', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'subtitle' => $this->subtitle,
            'subtitle_jp' => $this->subtitle_jp,
            'materials' => $materials,
        ))->with('head', 'Indirect Material')->with('page', 'Stock');
    }

    public function importStock(Request $request)
    {
        if ($request->hasFile('upload_file')) {
            try {
                $file = $request->file('upload_file');
                $file_name = 'import_chemical_stock' . '(' . date("ymd_h.i") . ')' . '.' . $file->getClientOriginalExtension();
                $file->move(public_path('uploads/indirect_material_chm/'), $file_name);

                $excel = public_path('uploads/indirect_material_chm/') . $file_name;
                $rows = Excel::load($excel, function ($reader) {
                    $reader->noHeading();
                    //Skip Header
                    $reader->skipRows(1);
                })->get();
                $rows = $rows->toArray();

                $notChm = array();

                $in_date = $request->get('upload_date');

                for ($i = 0; $i < count($rows); $i++) {
                    $material_number = $rows[$i][0];
                    $quantity = $rows[$i][1];

                    $isChm = IndirectMaterial::where('material_number', '=', $material_number)->first();

                    if ($isChm) {
                        $inventory = Inventory::where('plant', '=', '8190')
                            ->where('material_number', '=', $material_number)
                            ->where('storage_location', '=', 'MSTK')
                            ->first();

                        if ($inventory) {
                            $inventory->quantity = $inventory->quantity + $quantity;
                            $inventory->updated_at = Carbon::now();
                        } else {
                            $inventory = new Inventory([
                                'plant' => '8190',
                                'material_number' => $material_number,
                                'storage_location' => 'MSTK',
                                'quantity' => $quantity,
                            ]);
                        }
                        $inventory->save();

                        for ($j = 0; $j < $quantity; $j++) {
                            $prefix_now = 'INDM' . date("ymd");
                            $code_generator = CodeGenerator::where('note', '=', 'indirect-material')->first();
                            if ($prefix_now != $code_generator->prefix) {
                                $code_generator->prefix = $prefix_now;
                                $code_generator->index = '0';
                                $code_generator->save();
                            }

                            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                            $qr_code = $code_generator->prefix . $number;
                            $code_generator->index = $code_generator->index + 1;
                            $code_generator->save();

                            $log = new IndirectMaterialLog([
                                'in_date' => $in_date,
                                'qr_code' => $qr_code,
                                'material_number' => $material_number,
                                'remark' => 'in',
                                'quantity' => 1,
                                'created_by' => Auth::id(),
                            ]);
                            $log->save();

                            $stock = new IndirectMaterialStock([
                                'qr_code' => $qr_code,
                                'material_number' => $material_number,
                                'print_status' => 0,
                                'created_by' => Auth::id(),
                            ]);
                            $stock->save();
                        }
                    } else {
                        array_push($notChm, $material_number);
                    }
                }

                $notInsert = MaterialPlantDataList::whereIn('material_number', $notChm)
                    ->select('material_number', 'material_description')
                    ->get();

                $response = array(
                    'status' => true,
                    'message' => 'Upload file success',
                    'notInsert' => $notInsert,
                );
                return Response::json($response);

            } catch (\Exception$e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );
                return Response::json($response);
            }
        } else {
            $response = array(
                'status' => false,
                'message' => 'Upload failed, File not found',
            );
            return Response::json($response);
        }
    }

    public function deleteChmOut(Request $request)
    {
        $qr = $request->get('qr');
        $location = $request->get('location');

        $out = IndirectMaterialOut::where('qr_code', $qr)
            ->where('location', $location)
            ->first();

        if (!$out) {
            $response = array(
                'status' => false,
                'message' => 'QR Code tidak ditemukan',
            );
            return Response::json($response);
        }

        try {
            $stock_balance = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
				(SELECT material_number, quantity FROM indirect_material_stocks
					WHERE material_number = '" . $out->material_number . "'
					UNION ALL
					SELECT material_number, quantity FROM indirect_material_outs
					WHERE material_number = '" . $out->material_number . "'
					UNION ALL
					SELECT material_number, quantity FROM indirect_material_picks
					WHERE material_number = '" . $out->material_number . "'
					) AS all_stock
					GROUP BY material_number");

            $balance = 0;
            if (count($stock_balance) > 0) {
                $balance = $stock_balance[0]->quantity;
            }

            $balance_license = null;
            if (strlen($out->license) > 0) {
                $balance_license = 0;
                $stock_license = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
						(SELECT material_number, quantity FROM indirect_material_stocks
							WHERE license = '" . $out->license . "'
							UNION ALL
							SELECT material_number, quantity FROM indirect_material_outs
							WHERE license = '" . $out->license . "'
							UNION ALL
							SELECT material_number, quantity FROM indirect_material_picks
							WHERE license = '" . $out->license . "'
							) AS all_stock
							GROUP BY material_number");

                if (count($stock_license) > 0) {
                    $balance_license = $stock_license[0]->quantity;
                }
            }

            $idm_log = new IndirectMaterialLog([
                'in_date' => $out->in_date,
                'mfg_date' => $out->mfg_date,
                'exp_date' => $out->exp_date,
                'qr_code' => $out->qr_code,
                'material_number' => $out->material_number,
                'material_description' => $out->material_description,
                'license' => $out->license,
                'storage_location' => $location,
                'remark' => 'EMPTY',
                'quantity' => $out->quantity * -1,
                'balance' => $balance,
                'balance_license' => $balance_license,
                'bun' => $out->bun,
                'created_by' => Auth::id(),
            ]);
            $idm_log->save();

            $out->delete();

            $response = array(
                'status' => true,
                'message' => 'Chemical yang habis berhasil dibuang dari data stock out',
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

    public function deleteChmPicked(Request $request)
    {
        $id = $request->get('id');

        try {
            $pick = IndirectMaterialPick::where('id', $id)->first();

            if ($pick->remark == 'new') {
                $new = new IndirectMaterialStock([
                    'in_date' => $pick->in_date,
                    'mfg_date' => $pick->mfg_date,
                    'exp_date' => $pick->exp_date,
                    'qr_code' => $pick->qr_code,
                    'material_number' => $pick->material_number,
                    'material_description' => $pick->material_description,
                    'license' => $pick->license,
                    'storage_location' => $pick->storage_location,
                    'quantity' => $pick->quantity,
                    'bun' => $pick->bun,
                    'created_by' => Auth::id(),
                ]);
                $new->save();

            } elseif ($pick->remark == 'out') {
                $out = new IndirectMaterialOut([
                    'in_date' => $pick->in_date,
                    'mfg_date' => $pick->mfg_date,
                    'exp_date' => $pick->exp_date,
                    'qr_code' => $pick->qr_code,
                    'material_number' => $pick->material_number,
                    'material_description' => $pick->material_description,
                    'license' => $pick->license,
                    'location' => $pick->location,
                    'quantity' => $pick->quantity,
                    'bun' => $pick->bun,
                    'created_by' => Auth::id(),
                ]);
                $out->save();
            }

            $pick->delete();

            $response = array(
                'status' => true,
                'message' => 'Item terpilih telah dihapus',
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

    public function updateLarutan(Request $request)
    {
        $id = $request->get('id');
        $category = $request->get('category');
        $target_warning = $request->get('target_warning');
        $target_max = $request->get('target_max');

        try {

            if ($category == 'CONTROLLING CHART') {
                $larutan = ChemicalSolution::where('id', $id)
                    ->update([
                        'category' => $category,
                        'target_warning' => $target_warning,
                        'target_max' => $target_max,
                        'created_by' => Auth::id(),
                    ]);
            } else {
                $larutan = ChemicalSolution::where('id', $id)
                    ->update([
                        'category' => $category,
                        'target_warning' => null,
                        'target_max' => null,
                        'created_by' => Auth::id(),
                    ]);
            }

            $response = array(
                'status' => true,
                'message' => 'Data sukses diperbarui',
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

    public function inputChmOut(Request $request)
    {
        $now = date('Y-m-d');
        $qr_code = $request->get('qr');
        $location = $request->get('location');

        $stock = IndirectMaterialStock::where('qr_code', $qr_code)->first();

        // CHEMICAL TIDAK KADALUARSA
        $first_chemical = IndirectMaterialStock::where('material_number', $stock->material_number)
            ->where('exp_date', '>=', $now)
            ->orderBy('in_date', 'ASC')
            ->first();

        // CEK EXPIRED
        if ($now >= $stock->exp_date) {
            $response = array(
                'status' => false,
                'message' => 'Chemical Expired',
            );
            return Response::json($response);
        }

        // CEK FIFO
        if ($stock->in_date > $first_chemical->in_date) {
            $response = array(
                'status' => false,
                'message' => 'Pengambilan harus FIFO, Ambil chemical ' . $stock->material_number . ' dengan tanggal masuk ' . date('d-m-Y', strtotime($first_chemical->in_date)) . ' terlebih dahulu',
            );
            return Response::json($response);
        }

        DB::beginTransaction();
        try {

            $stock_balance = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
						(SELECT material_number, quantity FROM indirect_material_stocks
							WHERE material_number = '" . $stock->material_number . "'
							UNION ALL
							SELECT material_number, quantity FROM indirect_material_outs
							WHERE material_number = '" . $stock->material_number . "'
							UNION ALL
							SELECT material_number, quantity FROM indirect_material_picks
							WHERE material_number = '" . $stock->material_number . "'
							) AS all_stock
							GROUP BY material_number");

            $balance = 0;
            if (count($stock_balance) > 0) {
                $balance = $stock_balance[0]->quantity;
            }

            $balance_license = null;
            if (strlen($stock->license) > 0) {
                $balance_license = 0;
                $stock_license = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
								(SELECT material_number, quantity FROM indirect_material_stocks
									WHERE license = '" . $stock->license . "'
									UNION ALL
									SELECT material_number, quantity FROM indirect_material_outs
									WHERE license = '" . $stock->license . "'
									UNION ALL
									SELECT material_number, quantity FROM indirect_material_picks
									WHERE license = '" . $stock->license . "'
									) AS all_stock
									GROUP BY material_number");

                if (count($stock_license) > 0) {
                    $balance_license = $stock_license[0]->quantity;
                }
            }

            $idm_log = new IndirectMaterialLog([
                'in_date' => $stock->in_date,
                'mfg_date' => $stock->mfg_date,
                'exp_date' => $stock->exp_date,
                'qr_code' => $stock->qr_code,
                'material_number' => $stock->material_number,
                'material_description' => $stock->material_description,
                'license' => $stock->license,
                'storage_location' => $location,
                'remark' => 'OUT',
                'quantity' => $stock->quantity,
                'bun' => $stock->bun,
                'balance' => $balance,
                'balance_license' => $balance_license,
                'created_by' => Auth::id(),
            ]);
            $idm_log->save();

            $inventory = Inventory::where('storage_location', 'MSTK')
                ->where('material_number', $stock->material_number)
                ->first();
            if ($inventory) {
                $inventory->quantity = $inventory->quantity - 1;
                $inventory->save();
            }

            $bun = $stock->bun;
            $master = ChemicalSolutionComposer::where('material_number', $stock->material_number)->first();
            if ($master) {
                $bun = $master->bun;
            }

            $new_out = new IndirectMaterialOut([
                'in_date' => $stock->in_date,
                'mfg_date' => $stock->mfg_date,
                'exp_date' => $stock->exp_date,
                'qr_code' => $stock->qr_code,
                'material_number' => $stock->material_number,
                'material_description' => $stock->material_description,
                'license' => $stock->license,
                'location' => $location,
                'quantity' => $stock->quantity,
                'bun' => $bun,
                'created_by' => Auth::id(),
            ]);
            $new_out->save();

            $stock->delete();

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Success, status chemical OUT',
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

    public function inputChmPicked(Request $request)
    {
        $location = $request->get('location');
        $schedule_id = $request->get('schedule_id');

        $pick = IndirectMaterialPick::where('location', $location)
            ->where('schedule_id', $schedule_id)
            ->get();

        if (count($pick) == 0) {
            $response = array(
                'status' => false,
                'message' => 'Belum ada material yang di-scan',
            );
            return Response::json($response);
        }

        $schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();

        DB::beginTransaction();
        try {
            for ($i = 0; $i < count($pick); $i++) {

                $stock_balance = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
									(SELECT material_number, quantity FROM indirect_material_stocks
										WHERE material_number = '" . $pick[$i]->material_number . "'
										UNION ALL
										SELECT material_number, quantity FROM indirect_material_outs
										WHERE material_number = '" . $pick[$i]->material_number . "'
										UNION ALL
										SELECT material_number, quantity FROM indirect_material_picks
										WHERE material_number = '" . $pick[$i]->material_number . "'
										) AS all_stock
										GROUP BY material_number");

                $balance = 0;
                if (count($stock_balance) > 0) {
                    $balance = $stock_balance[0]->quantity - $pick[$i]->picking_quantity;
                }

                $balance_license = null;
                if (strlen($pick[$i]->license) > 0) {
                    $balance_license = 0;
                    $stock_license = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
											(SELECT material_number, quantity FROM indirect_material_stocks
												WHERE license = '" . $pick[$i]->license . "'
												UNION ALL
												SELECT material_number, quantity FROM indirect_material_outs
												WHERE license = '" . $pick[$i]->license . "'
												UNION ALL
												SELECT material_number, quantity FROM indirect_material_picks
												WHERE license = '" . $pick[$i]->license . "'
												) AS all_stock
												GROUP BY material_number");

                    if (count($stock_license) > 0) {
                        $balance_license = $stock_license[0]->quantity - $pick[$i]->picking_quantity;
                    }
                }

                $new_out = new IndirectMaterialOut([
                    'in_date' => $pick[$i]->in_date,
                    'mfg_date' => $pick[$i]->mfg_date,
                    'exp_date' => $pick[$i]->exp_date,
                    'qr_code' => $pick[$i]->qr_code,
                    'material_number' => $pick[$i]->material_number,
                    'material_description' => $pick[$i]->material_description,
                    'license' => $pick[$i]->license,
                    'location' => $pick[$i]->location,
                    'quantity' => $pick[$i]->quantity - $pick[$i]->picking_quantity,
                    'bun' => $pick[$i]->picking_bun,
                    'created_by' => Auth::id(),
                ]);
                $new_out->save();

                $log = new IndirectMaterialLog([
                    'in_date' => $pick[$i]->in_date,
                    'mfg_date' => $pick[$i]->mfg_date,
                    'exp_date' => $pick[$i]->exp_date,
                    'qr_code' => $pick[$i]->qr_code,
                    'material_number' => $pick[$i]->material_number,
                    'material_description' => $pick[$i]->material_description,
                    'license' => $pick[$i]->license,
                    'storage_location' => $pick[$i]->location,
                    'remark' => 'CONSUME',
                    'quantity' => $pick[$i]->picking_quantity * -1,
                    'bun' => $pick[$i]->picking_bun,
                    'balance' => $balance,
                    'balance_license' => $balance_license,
                    'created_by' => Auth::id(),
                ]);
                $log->save();

                // $log = new IndirectMaterialPickingLog([
                //     'schedule_id' => $schedule_id,
                //     'qr_code' => $pick[$i]->qr_code,
                //     'material_number' => $pick[$i]->material_number,
                //     'cost_center_id' => $pick[$i]->cost_center_id,
                //     'remark' => $pick[$i]->remark,
                //     'quantity' => $schedule->quantity,
                //     'bun' => $schedule->bun,
                //     'in_date' => $pick[$i]->in_date,
                //     'exp_date' => $pick[$i]->exp_date,
                //     'created_by' => Auth::id()
                // ]);
                // $log->save();

            }

            $schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();
            $schedule->picked_by = Auth::id();
            $schedule->picked_time = date('Y-m-d H:i:s');
            $schedule->save();

            $pick = IndirectMaterialPick::where('location', $location)
                ->where('schedule_id', $schedule_id)
                ->delete();

            DB::commit();
            $response = array(
                'status' => true,
                'message' => 'Pengambilan chemical sukses',
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

    public function inputChmNew(Request $request)
    {
        $date = $request->get('date');
        $time = $request->get('time');
        $solution_id = $request->get('solution_id');
        $note = $request->get('note');
        $schedule_date = date('Y-m-d H:i:s', strtotime($date . ' ' . $time . ':00'));

        $chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
            ->where('solution_id', $solution_id)
            ->select(
                'chemical_solution_composers.solution_name',
                'chemical_solution_composers.solution_id',
                'chemical_solution_composers.material_number',
                'chemical_solution_composers.storage_location',
                'chemical_solution_composers.quantity',
                'chemical_solution_composers.bun'
            )
            ->get();

        try {
            for ($i = 0; $i < count($chm_composer); $i++) {

                $schedule = new IndirectMaterialSchedule([
                    'schedule_date' => $schedule_date,
                    'category' => 'Pembuatan Baru',
                    'solution_id' => $solution_id,
                    'material_number' => $chm_composer[$i]->material_number,
                    'storage_location' => $chm_composer[$i]->storage_location,
                    'quantity' => $chm_composer[$i]->quantity,
                    'bun' => $chm_composer[$i]->bun,
                    'note' => $note,
                    'created_by' => Auth::id(),
                ]);
                $schedule->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Input schedule success',
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

    public function inputChmAddition(Request $request)
    {
        $date = $request->get('date');
        $time = $request->get('time');
        $solution_id = $request->get('solution_id');
        $composer = $request->get('composer');
        $schedule_date = date('Y-m-d H:i:s', strtotime($date . ' ' . $time . ':00'));

        try {
            for ($i = 0; $i < count($composer); $i++) {
                $chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
                    ->where('solution_id', $solution_id)
                    ->where('material_number', $composer[$i]['material_number'])
                    ->select(
                        'chemical_solution_composers.solution_name',
                        'chemical_solution_composers.solution_id',
                        'chemical_solution_composers.material_number',
                        'chemical_solution_composers.storage_location',
                        'chemical_solution_composers.bun'
                    )
                    ->first();

                $schedule = new IndirectMaterialSchedule([
                    'schedule_date' => $schedule_date,
                    'category' => 'Penambahan Chemical',
                    'solution_id' => $solution_id,
                    'material_number' => $composer[$i]['material_number'],
                    'storage_location' => $chm_composer->storage_location,
                    'quantity' => $composer[$i]['quantity'],
                    'note' => $composer[$i]['note'],
                    'bun' => $chm_composer->bun,
                    'created_by' => Auth::id(),
                ]);
                $schedule->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Input schedule success',
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

    public function inputStock(Request $request)
    {
        $in_date = $request->get('in_date');
        $mfg_date = $request->get('mfg_date');
        $exp_date = $request->get('exp_date');
        $material_number = $request->get('material_number');
        $quantity = $request->get('quantity');
        $type = $request->get('type');
        $license = $request->get('license');
        $material = IndirectMaterial::where('material_number', $material_number)->first();

        if ($type != 4) {
            if ($in_date >= $exp_date) {
                $response = array(
                    'status' => false,
                    'message' => 'In Date lebih lama dari Exp Date',
                );
                return Response::json($response);
            }
        }

        if ($mfg_date == '-') {
            $mfg_date = null;
        }

        if ($exp_date == '-') {
            $exp_date = null;
        }

        if ($license == '-') {
            $license = null;
        }

        try {
            $inventory = Inventory::where('plant', '=', '8190')
                ->where('material_number', '=', $material_number)
                ->where('storage_location', '=', 'MSTK')
                ->first();

            if ($inventory) {
                $inventory->quantity = $inventory->quantity + $quantity;
                $inventory->updated_at = Carbon::now();
            } else {
                $inventory = new Inventory([
                    'plant' => '8190',
                    'material_number' => $material_number,
                    'storage_location' => 'MSTK',
                    'quantity' => $quantity,
                ]);
            }
            $inventory->save();

            for ($i = 0; $i < $quantity; $i++) {
                $prefix_now = 'INDM' . date("ymd");
                $code_generator = CodeGenerator::where('note', '=', 'indirect-material')->first();
                if ($prefix_now != $code_generator->prefix) {
                    $code_generator->prefix = $prefix_now;
                    $code_generator->index = '0';
                    $code_generator->save();
                }

                $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
                $qr_code = $code_generator->prefix . $number;
                $code_generator->index = $code_generator->index + 1;
                $code_generator->save();

                $stock = new IndirectMaterialStock([
                    'in_date' => $in_date,
                    'mfg_date' => $mfg_date,
                    'exp_date' => $exp_date,
                    'qr_code' => $qr_code,
                    'material_number' => $material_number,
                    'material_description' => $material->material_description,
                    'license' => $license,
                    'storage_location' => $material->storage_location,
                    'quantity' => $material->lot,
                    'bun' => $material->bun,
                    'created_by' => Auth::id(),
                ]);
                $stock->save();

                $stock_balance = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
											(SELECT material_number, quantity FROM indirect_material_stocks
												WHERE material_number = '" . $material_number . "'
												UNION ALL
												SELECT material_number, quantity FROM indirect_material_outs
												WHERE material_number = '" . $material_number . "'
												UNION ALL
												SELECT material_number, quantity FROM indirect_material_picks
												WHERE material_number = '" . $material_number . "'
												) AS all_stock
												GROUP BY material_number");

                $balance = 0;
                if (count($balance) > 0) {
                    $balance = $stock_balance[0]->quantity;
                }

                $balance_license = null;
                if (strlen($license) > 0) {
                    $balance_license = 0;
                    $stock_license = db::select("SELECT material_number, SUM(quantity) AS quantity FROM
													(SELECT material_number, quantity FROM indirect_material_stocks
														WHERE license = '" . $license . "'
														UNION ALL
														SELECT material_number, quantity FROM indirect_material_outs
														WHERE license = '" . $license . "'
														UNION ALL
														SELECT material_number, quantity FROM indirect_material_picks
														WHERE license = '" . $license . "'
														) AS all_stock
														GROUP BY material_number");

                    if (count($stock_license) > 0) {
                        $balance_license = $stock_license[0]->quantity;
                    }
                }

                $log = new IndirectMaterialLog([
                    'in_date' => $in_date,
                    'mfg_date' => $mfg_date,
                    'exp_date' => $exp_date,
                    'qr_code' => $qr_code,
                    'material_number' => $material_number,
                    'material_description' => $material->material_description,
                    'license' => $license,
                    'storage_location' => $material->storage_location,
                    'remark' => 'IN',
                    'quantity' => $material->lot,
                    'bun' => $material->bun,
                    'balance' => $balance,
                    'balance_license' => $balance_license,
                    'created_by' => Auth::id(),
                ]);
                $log->save();

            }

            $response = array(
                'status' => true,
                'message' => 'Input stock success',
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

    public function inputResult($id, $material, $quantity, $convertion, $date)
    {

        $solution = ChemicalSolution::where('id', $id)->first();

        try {

            if ($solution->category == 'CONTROLLING CHART') {
                if ($solution->target_uom == 'DM2') {
                    $solution->actual_quantity = $solution->actual_quantity + ($quantity * $convertion);
                    $solution->updated_at = Carbon::now();
                    $solution->save();

                    $log = new ChemicalControlLog([
                        'date' => $date,
                        'solution_name' => $solution->solution_name,
                        'cost_center_id' => $solution->cost_center_id,
                        'target_max' => $solution->target_max,
                        'target_warning' => $solution->target_warning,
                        'note' => $material,
                        'quantity' => $quantity * $convertion,
                        'accumulative' => $solution->actual_quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();
                } else {
                    $solution->actual_quantity = $solution->actual_quantity + $quantity;
                    $solution->updated_at = Carbon::now();
                    $solution->save();

                    $log = new ChemicalControlLog([
                        'date' => $date,
                        'solution_name' => $solution->solution_name,
                        'cost_center_id' => $solution->cost_center_id,
                        'target_max' => $solution->target_max,
                        'target_warning' => $solution->target_warning,
                        'note' => $material,
                        'quantity' => $quantity,
                        'accumulative' => $solution->actual_quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();
                }

                if ($solution->actual_quantity > $solution->target_warning) {
                    if ($solution->is_add_schedule == 1) {
                        $this->inputChmControl($solution->id);

                        $solution->is_add_schedule = 0;
                        $solution->save();
                    }
                }
            }

        } catch (Exception $e) {
            $error_log = new ErrorLog([
                'error_message' => $e->getMessage(),
                'created_by' => $id,
            ]);
            $error_log->save();
        }

    }

    public function inputStrikeNickel($material, $quantity, $convertion, $date)
    {

        //STRIKE NICKEL
        // $solution = ChemicalSolution::where('id', 36)->first();
        $this->inputResult(36, $material, $quantity, $convertion, $date);

        //ULTRASONIC CLEANER
        // $solution = ChemicalSolution::where('id', 30)->first();
        $this->inputResult(30, $material, $quantity, $convertion, $date);

        //ALKALI DEGREASING
        // $solution = ChemicalSolution::where('id', 31)->first();
        $this->inputResult(31, $material, $quantity, $convertion, $date);

        //ELECTRO ALKALI DEGREASING
        // $solution = ChemicalSolution::where('id', 32)->first();
        $this->inputResult(32, $material, $quantity, $convertion, $date);

        //ACID ACTIVATION
        // $solution = ChemicalSolution::where('id', 33)->first();
        $this->inputResult(33, $material, $quantity, $convertion, $date);
    }

    public function inputStrikeSilver($material, $quantity, $convertion, $date)
    {

        //STRIKE SILVER
        // $solution = ChemicalSolution::where('id', 39)->first();
        $this->inputResult(39, $material, $quantity, $convertion, $date);

        //ULTRASONIC CLEANER
        // $solution = ChemicalSolution::where('id', 30)->first();
        $this->inputResult(30, $material, $quantity, $convertion, $date);

        //ALKALI DEGREASING
        // $solution = ChemicalSolution::where('id', 31)->first();
        $this->inputResult(31, $material, $quantity, $convertion, $date);

        //ELECTRO ALKALI DEGREASING
        // $solution = ChemicalSolution::where('id', 32)->first();
        $this->inputResult(32, $material, $quantity, $convertion, $date);

        //ACID ACTIVATION
        // $solution = ChemicalSolution::where('id', 33)->first();
        $this->inputResult(33, $material, $quantity, $convertion, $date);

        //ALKALI DIPPING
        // $solution = ChemicalSolution::where('id', 34)->first();
        $this->inputResult(34, $material, $quantity, $convertion, $date);

        //NETRALISASI
        // $solution = ChemicalSolution::where('id', 35)->first();
        $this->inputResult(35, $material, $quantity, $convertion, $date);

    }

    public function inputProductionResult(Request $request)
    {
        $date = $request->get('date');
        $larutan = $request->get('larutan');
        $material = $request->get('materials');

        try {
            for ($i = 0; $i < count($material); $i++) {
                $convertion = ChemicalConvertion::where('id', $material[$i][0])->first();

                $solution = ChemicalSolution::where('id', $larutan)->first();

                if (strpos($solution->solution_name, 'GLOSSY NI') !== false) {
                    $this->inputStrikeNickel($material[$i][1], $material[$i][2], $convertion->dm2, $date);
                } else if (strpos($solution->solution_name, 'GLOSSY SILVER') !== false) {
                    $this->inputStrikeSilver($material[$i][1], $material[$i][2], $convertion->dm2, $date);
                }

                if ($solution->category == 'CONTROLLING CHART') {
                    if ($solution->target_uom == 'DM2') {
                        $solution->actual_quantity = $solution->actual_quantity + ($material[$i][2] * $convertion->dm2);
                        $solution->updated_at = Carbon::now();
                        $solution->save();

                        $log = new ChemicalControlLog([
                            'date' => $date,
                            'solution_name' => $solution->solution_name,
                            'location' => $solution->location,
                            'target_max' => $solution->target_max,
                            'target_warning' => $solution->target_warning,
                            'note' => $material[$i][1],
                            'quantity' => $material[$i][2] * $convertion->dm2,
                            'accumulative' => $solution->actual_quantity,
                            'created_by' => Auth::id(),
                        ]);
                        $log->save();
                    } else {
                        $solution->actual_quantity = $solution->actual_quantity + $material[$i][2];
                        $solution->updated_at = Carbon::now();
                        $solution->save();

                        $log = new ChemicalControlLog([
                            'date' => $date,
                            'solution_name' => $solution->solution_name,
                            'location' => $solution->location,
                            'target_max' => $solution->target_max,
                            'target_warning' => $solution->target_warning,
                            'note' => $material[$i][1],
                            'quantity' => $material[$i][2],
                            'accumulative' => $solution->actual_quantity,
                            'created_by' => Auth::id(),
                        ]);
                        $log->save();
                    }

                    if ($solution->actual_quantity > $solution->target_warning) {
                        if ($solution->is_add_schedule == 1) {
                            $this->inputChmControl($solution->id);

                            $solution->is_add_schedule = 0;
                            $solution->save();
                        }
                    }
                } else {
                    $log = new ChemicalControlLog([
                        'date' => $date,
                        'solution_name' => $solution->solution_name,
                        'location' => $solution->location,
                        'target_max' => $solution->target_max,
                        'target_warning' => $solution->target_warning,
                        'note' => $material[$i][1],
                        'quantity' => $material[$i][2],
                        'accumulative' => $solution->actual_quantity,
                        'created_by' => Auth::id(),
                    ]);
                    $log->save();
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Input Production Result Success',
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

    public function inputChmControl($solution_id)
    {
        $date = date('Y-m-d', strtotime("+1 day"));
        $schedule_date = date('Y-m-d H:i:s', strtotime($date . ' 07:00:01'));
        $shift = 1;

        $chm_composer = ChemicalSolutionComposer::leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'chemical_solution_composers.solution_id')
            ->where('solution_id', $solution_id)
            ->select(
                'chemical_solution_composers.solution_name',
                'chemical_solution_composers.solution_id',
                'chemical_solution_composers.material_number',
                'chemical_solution_composers.storage_location',
                'chemical_solution_composers.quantity',
                'chemical_solution_composers.bun'
            )
            ->get();

        try {
            for ($i = 0; $i < count($chm_composer); $i++) {

                $schedule = new IndirectMaterialSchedule([
                    'schedule_date' => $schedule_date,
                    'schedule_shift' => $shift,
                    'category' => 'Pembuatan Baru',
                    'solution_id' => $solution_id,
                    'material_number' => $chm_composer[$i]->material_number,
                    'storage_location' => $chm_composer[$i]->storage_location,
                    'quantity' => $chm_composer[$i]->quantity,
                    'bun' => $chm_composer[$i]->bun,
                    'created_by' => 1,
                ]);
                $schedule->save();
            }

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteSchedule(Request $request)
    {
        $id = $request->get('id');

        try {
            $schedule = IndirectMaterialSchedule::where('id', $id)->first();

            $larutan = ChemicalSolution::where('id', $schedule->solution_id)
                ->update([
                    'is_add_schedule' => 1,
                ]);

            $delete = IndirectMaterialSchedule::where('schedule_date', $schedule->schedule_date)
                ->where('solution_id', $schedule->solution_id)
                ->delete();

            $response = array(
                'status' => true,
                'message' => 'Delete schedule success',
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

    public function changeSchedule(Request $request)
    {
        $id = $request->get('id');

        try {
            $schedule = IndirectMaterialSchedule::where('id', $id)->first();

            $larutan = ChemicalSolution::where('id', $schedule->solution_id)->first();
            $larutan->is_add_schedule = 1;
            $larutan->save();

            if ($schedule->category == 'Pembuatan Baru') {
                $update_qty_larutan = ChemicalSolution::where('id', $schedule->solution_id)
                    ->update([
                        'actual_quantity' => 0,
                    ]);

                $log = new ChemicalControlLog([
                    'date' => date('Y-m-d'),
                    'solution_name' => $larutan->solution_name,
                    'location' => $larutan->location,
                    'target_max' => $larutan->target_max,
                    'target_warning' => $larutan->target_warning,
                    'note' => '-',
                    'quantity' => 0,
                    'accumulative' => 0,
                    'created_by' => Auth::id(),
                ]);
                $log->save();
            }

            $change = IndirectMaterialSchedule::where('schedule_date', $schedule->schedule_date)
                ->where('solution_id', $schedule->solution_id)
                ->update([
                    'changed_by' => Auth::id(),
                    'changed_time' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Penggantian larutan success',
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

    public function changeScheduleByChm(Request $request)
    {
        $id = $request->get('id');

        try {
            $larutan = ChemicalSolution::where('id', $id)->first();
            $larutan->is_add_schedule = 1;
            $larutan->actual_quantity = 0;
            $larutan->save();

            $log = new ChemicalControlLog([
                'date' => date('Y-m-d'),
                'solution_name' => $larutan->solution_name,
                'cost_center_id' => $larutan->cost_center_id,
                'target_max' => $larutan->target_max,
                'target_warning' => $larutan->target_warning,
                'note' => '-',
                'quantity' => 0,
                'accumulative' => 0,
                'created_by' => Auth::id(),
            ]);
            $log->save();

            $response = array(
                'status' => true,
                'message' => 'Penggantian larutan success',
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

    public function fetchIndirectMaterialMonitoring()
    {
        $data = db::select("SELECT sum,
											SUM(exp) AS exp,
											SUM(one_month) AS one_month,
											SUM(three_month) AS three_month,
											SUM(six_month) AS six_month,
											SUM(nine_month) AS nine_month,
											SUM(twelve_month) AS twelve_month,
											SUM(more_year) AS more_year
											FROM
											(SELECT 'SUM' AS sum,
												IF(DATEDIFF(exp_date, NOW()) < 0, 1, 0) AS exp,
												IF(DATEDIFF(exp_date, NOW()) >= 0 AND DATEDIFF(exp_date, NOW()) <= 30, 1, 0) AS one_month,
												IF(DATEDIFF(exp_date, NOW()) > 30 AND DATEDIFF(exp_date, NOW()) <= 90, 1, 0) AS three_month,
												IF(DATEDIFF(exp_date, NOW()) > 90 AND DATEDIFF(exp_date, NOW()) <= 180, 1, 0) AS six_month,
												IF(DATEDIFF(exp_date, NOW()) > 180 AND DATEDIFF(exp_date, NOW()) <= 270, 1, 0) AS nine_month,
												IF(DATEDIFF(exp_date, NOW()) > 270 AND DATEDIFF(exp_date, NOW()) <= 365, 1, 0) AS twelve_month,
												IF(DATEDIFF(exp_date, NOW()) > 365, 1, 0) AS more_year,
												DATEDIFF(exp_date, NOW()) AS diff FROM indirect_material_stocks) AS resume
											GROUP BY sum");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchIndirectMaterialMonitoringDetail(Request $request)
    {
        $category = $request->get('category');
        $condition = '';

        if ($category == 'Expired') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) < 0';
        } elseif ($category == '< 30 Days') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) >= 0 AND DATEDIFF(s.exp_date, NOW()) <= 30';
        } elseif ($category == '< 90 Days') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) > 30 AND DATEDIFF(s.exp_date, NOW()) <= 90';
        } elseif ($category == '< 180 Days') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) > 90 AND DATEDIFF(s.exp_date, NOW()) <= 180';
        } elseif ($category == '< 270 Days') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) > 180 AND DATEDIFF(s.exp_date, NOW()) <= 270';
        } elseif ($category == '< 1 Year') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) > 270 AND DATEDIFF(s.exp_date, NOW()) <= 356';
        } elseif ($category == '> 1 Year') {
            $condition = 'DATEDIFF(s.exp_date, NOW()) > 365';
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

        $data = db::select("SELECT resume.material_number, resume.material_description, resume.storage_location, GROUP_CONCAT(' ', resume.exp_date, ' (', resume.qty, ')') AS exp_date, SUM(resume.qty) AS qty FROM
											(SELECT s.material_number, mpdl.material_description, chm.storage_location, s.in_date, s.exp_date, COUNT(s.material_number) AS qty FROM indirect_material_stocks s
												LEFT JOIN (SELECT DISTINCT material_number, material_description, storage_location FROM chemical_solution_composers) AS chm
												ON chm.material_number = s.material_number
												LEFT JOIN material_plant_data_lists mpdl ON mpdl.material_number = s.material_number
												WHERE " . $condition . "
												GROUP BY s.material_number, mpdl.material_description, chm.storage_location, s.in_date, s.exp_date) AS resume
												GROUP BY resume.material_number, resume.material_description, resume.storage_location
												ORDER BY qty DESC");

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);

    }

    public function fetchcheckResult(Request $request)
    {
        $id = $request->get('id');
        $data = ChemicalSolution::where('chemical_solutions.id', $id)->first();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchGetMaterial(Request $request)
    {
        $id = $request->get('id');

        $materials = ChemicalConvertion::where('solution_id', $id)
            ->orderBy('id', 'ASC')
            ->get();

        echo '<option value=""></option>';
        for ($i = 0; $i < count($materials); $i++) {
            echo '<option value="' . $materials[$i]['material'] . '(ime)' . $materials[$i]['material'] . '">' . $materials[$i]['material'] . '</option>';
        }
    }

    public function fetchLarutanDetail(Request $request)
    {
        $id = $request->get('id');

        $data = ChemicalSolution::leftJoin('indirect_material_cost_centers', 'indirect_material_cost_centers.id', '=', 'chemical_solutions.cost_center_id')
            ->where('chemical_solutions.id', $id)
            ->select(
                'chemical_solutions.id',
                'chemical_solutions.solution_name',
                db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
                'chemical_solutions.category',
                'chemical_solutions.target_warning',
                'chemical_solutions.target_max',
                'chemical_solutions.actual_quantity'
            )
            ->first();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchLarutan()
    {
        $data = ChemicalSolution::leftJoin('indirect_material_cost_centers', 'indirect_material_cost_centers.id', '=', 'chemical_solutions.cost_center_id')
            ->select(
                'chemical_solutions.id',
                'chemical_solutions.solution_name',
                db::raw('CONCAT(indirect_material_cost_centers.section," - ",indirect_material_cost_centers.location) AS location'),
                'chemical_solutions.category',
                'chemical_solutions.target_warning',
                'chemical_solutions.target_max',
                'chemical_solutions.actual_quantity'
            )
            ->get();

        return DataTables::of($data)
            ->addColumn('edit', function ($data) {
                if (Auth::user()->role_code == 'CHM' || Auth::user()->role_code == 'MIS') {
                    return '<a href="javascript:void(0)" class="btn btn-sm btn-warning" onClick="editSolution(id)" id="' . $data->id . '"><i class="fa fa-pencil"></i></a>&nbsp;<a href="javascript:void(0)" class="btn btn-sm btn-primary" onClick="changeSolution(id)" id="' . $data->id . '"><i class="fa fa-refresh"></i></a>';
                } else {
                    return '-';
                }
            })
            ->rawColumns([
                'edit' => 'edit',
            ])
            ->make(true);
    }

    public function fetchCheckOut(Request $request)
    {
        $qr = $request->get('qr');
        $location = $request->get('location');

        $out = IndirectMaterialOut::where('qr_code', $qr)
            ->where('location', $location)
            ->first();

        if ($out) {
            $response = array(
                'status' => true,
                'data' => $out,
                'message' => 'QR code ditemukan',
            );
            return Response::json($response);

        } else {
            $response = array(
                'status' => false,
                'message' => 'QR code tidak ditemukan',
            );
            return Response::json($response);

        }

    }

    public function fetchCheckReqOut(Request $request)
    {
        $qr = $request->get('qr');
        $stock = IndirectMaterialStock::where('qr_code', $qr)->first();

        if ($stock) {
            $response = array(
                'status' => true,
                'data' => $stock,
                'message' => 'QR code ditemukan',
            );
            return Response::json($response);

        } else {
            $response = array(
                'status' => false,
                'message' => 'QR code tidak ditemukan',
            );
            return Response::json($response);

        }

    }

    public function fetchCheckQrBk(Request $request)
    {
        $now = date('Y-m-d');

        $qr = $request->get('qr');
        $material_number = $request->get('material_number');
        $location = $request->get('location');
        $schedule_id = $request->get('schedule_id');

        $out = IndirectMaterialOut::where('material_number', $material_number)
            ->where('location', $location)
            ->first();

        DB::beginTransaction();
        if ($out) {
            // CEK OUT
            if ($out->qr_code != $qr) {
                $response = array(
                    'status' => false,
                    'message' => 'Scan chemical status out dahulu',
                );
                return Response::json($response);
            }

            // CEK EXPIRED
            if ($now >= $out->exp_date) {
                $response = array(
                    'status' => false,
                    'message' => 'Chemical Expired',
                );
                return Response::json($response);
            }

            $pick_out = IndirectMaterialOut::where('qr_code', $qr)
                ->where('location', $location)
                ->first();

            // CEK QR CODE SAMA
            $picked = IndirectMaterialPick::where('qr_code', $qr)->first();
            if ($picked) {
                $response = array(
                    'status' => false,
                    'message' => 'Chemical telah di scan',
                );
                return Response::json($response);
            }

            // CEK KESESUAIN MATERIAL
            if ($pick_out->material_number != $material_number) {
                $response = array(
                    'status' => false,
                    'message' => 'Material chemical salah',
                );
                return Response::json($response);
            }

            $schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();

            $picking_quantity = $schedule->quantity;
            if ($pick_out->quantity <= $schedule->quantity) {
                $picking_quantity = $pick_out->quantity;
            }

            try {
                $pick = new IndirectMaterialPick([
                    'remark' => 'out',
                    'in_date' => $pick_out->in_date,
                    'mfg_date' => $pick_out->mfg_date,
                    'exp_date' => $pick_out->exp_date,
                    'qr_code' => $pick_out->qr_code,
                    'schedule_id' => $schedule_id,
                    'material_number' => $pick_out->material_number,
                    'material_description' => $pick_out->material_description,
                    'license' => $pick_out->license,
                    'location' => $location,
                    'quantity' => $pick_out->quantity,
                    'bun' => $pick_out->bun,
                    'picking_quantity' => $picking_quantity,
                    'picking_bun' => $schedule->bun,
                    'created_by' => Auth::id(),
                ]);
                $pick->save();

                $delete_out = IndirectMaterialOut::where('qr_code', $qr)->delete();

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Chemical telah ditambahkan',
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

        $stock = IndirectMaterialStock::where('qr_code', $qr)->first();

        // CHEMICAL TIDAK KADALUARSA
        $first_chemical = IndirectMaterialStock::where('material_number', $material_number)
            ->where('exp_date', '>=', $now)
            ->orderBy('in_date', 'ASC')
            ->first();

        if ($stock) {
            if ($stock->material_number != $material_number) {
                $response = array(
                    'status' => false,
                    'message' => 'Material chemical salah',
                );
                return Response::json($response);
            }

            // CEK EXPIRED
            if ($now >= $stock->exp_date) {
                $response = array(
                    'status' => false,
                    'message' => 'Chemical Expired',
                );
                return Response::json($response);
            }

            // CEK FIFO
            // if($stock->in_date > $first_chemical->in_date){
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Pengambilan harus FIFO, Ambil chemical '.$stock->material_number.' dengan tanggal masuk ' . date('d-m-Y', strtotime($first_chemical->in_date)) . ' terlebih dahulu'
            //     );
            //     return Response::json($response);
            // }

            $schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();

            $picking_quantity = $schedule->quantity;
            if ($stock->quantity <= $schedule->quantity) {
                $picking_quantity = $stock->quantity;
            }

            try {
                $pick = new IndirectMaterialPick([
                    'remark' => 'new',
                    'in_date' => $stock->in_date,
                    'mfg_date' => $stock->mfg_date,
                    'exp_date' => $stock->exp_date,
                    'qr_code' => $stock->qr_code,
                    'schedule_id' => $schedule_id,
                    'material_number' => $stock->material_number,
                    'material_description' => $stock->material_description,
                    'license' => $stock->license,
                    'location' => $location,
                    'quantity' => $stock->quantity,
                    'bun' => $stock->bun,
                    'picking_quantity' => $picking_quantity,
                    'picking_bun' => $schedule->bun,
                    'created_by' => Auth::id(),
                ]);
                $pick->save();

                $delete_stock = IndirectMaterialStock::where('qr_code', $qr)->delete();

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Chemical telah ditambahkan',
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

            // CEK KESESUAIN MATERIAL
            $response = array(
                'status' => false,
                'message' => 'Material chemical salah',
            );
            return Response::json($response);
        }
    }

    public function fetchCheckQr(Request $request)
    {
        $now = date('Y-m-d');

        $qr = $request->get('qr');
        $material_number = $request->get('material_number');
        $location = $request->get('location');
        $schedule_id = $request->get('schedule_id');

        // $out = IndirectMaterialOut::where('material_number', $material_number)
        //     ->where('location', $location)
        //     ->first();

        $pick_out = IndirectMaterialOut::where('qr_code', $qr)
            ->where('location', $location)
            ->first();

        DB::beginTransaction();
        if ($pick_out) {
            // CEK OUT
            // if ($out->qr_code != $qr) {
            //     $response = array(
            //         'status' => false,
            //         'message' => 'Scan chemical status out dahulu',
            //     );
            //     return Response::json($response);
            // }

            // CEK EXPIRED
            if ($now >= $pick_out->exp_date) {
                $response = array(
                    'status' => false,
                    'message' => 'Chemical Expired',
                );
                return Response::json($response);
            }

            // CEK QR CODE SAMA
            $picked = IndirectMaterialPick::where('qr_code', $qr)->first();
            if ($picked) {
                $response = array(
                    'status' => false,
                    'message' => 'Chemical telah di scan',
                );
                return Response::json($response);
            }

            // CEK KESESUAIN MATERIAL
            if ($pick_out->material_number != $material_number) {
                $response = array(
                    'status' => false,
                    'message' => 'Material chemical salah',
                );
                return Response::json($response);
            }

            $schedule = IndirectMaterialSchedule::where('id', $schedule_id)->first();

            $picking_quantity = $schedule->quantity;
            if ($pick_out->quantity <= $schedule->quantity) {
                $picking_quantity = $pick_out->quantity;
            }

            try {
                $pick = new IndirectMaterialPick([
                    'remark' => 'out',
                    'in_date' => $pick_out->in_date,
                    'mfg_date' => $pick_out->mfg_date,
                    'exp_date' => $pick_out->exp_date,
                    'qr_code' => $pick_out->qr_code,
                    'schedule_id' => $schedule_id,
                    'material_number' => $pick_out->material_number,
                    'material_description' => $pick_out->material_description,
                    'license' => $pick_out->license,
                    'location' => $location,
                    'quantity' => $pick_out->quantity,
                    'bun' => $pick_out->bun,
                    'picking_quantity' => $picking_quantity,
                    'picking_bun' => $schedule->bun,
                    'created_by' => Auth::id(),
                ]);
                $pick->save();

                $delete_out = IndirectMaterialOut::where('qr_code', $qr)->delete();

                DB::commit();
                $response = array(
                    'status' => true,
                    'message' => 'Chemical telah ditambahkan',
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
            // CEK KESESUAIN MATERIAL
            $response = array(
                'status' => false,
                'message' => 'Material chemical out tidak ditemukan',
            );
            return Response::json($response);
        }

    }

    public function fetchChmPicked(Request $request)
    {

        $location = $request->get('location');
        $schedule_id = $request->get('schedule_id');

        $data = IndirectMaterialPick::where('indirect_material_picks.location', $location)
            ->where('indirect_material_picks.schedule_id', $schedule_id)
            ->select(
                'indirect_material_picks.id',
                'indirect_material_picks.qr_code',
                'indirect_material_picks.material_number',
                'indirect_material_picks.material_description',
                'indirect_material_picks.picking_quantity',
                'indirect_material_picks.picking_bun',
                'indirect_material_picks.remark'
            )
            ->get();

        return DataTables::of($data)
            ->addColumn('delete', function ($data) {
                return '<a style="padding-top: 0px; padding-bottom: 0px;" href="javascript:void(0)" class="btn btn-sm btn-danger" onClick="deletePicked(id)" id="' . $data->id . '"><i class="fa fa-close"></i></a>';
            })
            ->addColumn('qty_bun', function ($data) {
                return $data->picking_quantity . ' ' . $data->picking_bun;
            })
            ->rawColumns([
                'delete' => 'delete',
                'qty_bun' => 'qty_bun',
            ])
            ->make(true);

    }

    public function fetchAdditionChm(Request $request)
    {
        $solution_id = $request->get('solution_id');

        $getAdditionChm = ChemicalSolutionComposer::where('solution_id', $solution_id)
            ->where('addition', 1)
            ->select('solution_name', 'material_number', 'material_description', 'storage_location', 'bun')
            ->get();

        echo '<option value=""></option>';
        for ($i = 0; $i < count($getAdditionChm); $i++) {
            echo '<option value="' . $getAdditionChm[$i]['material_number'] . '(ime)' . $getAdditionChm[$i]['material_description'] . '(ime)' . $getAdditionChm[$i]['storage_location'] . '(ime)' . $getAdditionChm[$i]['bun'] . '">' . $getAdditionChm[$i]['material_number'] . ' - ' . $getAdditionChm[$i]['material_description'] . '</option>';
        }
    }

    public function fetchPickingScheduleDetail(Request $request)
    {
        $data = IndirectMaterialSchedule::leftJoin('chemical_solution_composers', function ($join) {
            $join->on('indirect_material_schedules.solution_id', '=', 'chemical_solution_composers.solution_id');
            $join->on('indirect_material_schedules.material_number', '=', 'chemical_solution_composers.material_number');
        })
            ->leftJoin('indirect_materials', 'indirect_materials.material_number', '=', 'indirect_material_schedules.material_number')
            ->leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'indirect_material_schedules.solution_id')
            ->leftJoin('users', 'indirect_material_schedules.picked_by', '=', 'users.id')
            ->where('indirect_material_schedules.id', $request->get('id'))
            ->select(
                'indirect_material_schedules.id',
                'indirect_material_schedules.schedule_date',
                'indirect_material_schedules.category',
                'chemical_solution_composers.solution_name',
                'chemical_solutions.location',
                'chemical_solution_composers.material_number',
                'chemical_solution_composers.material_description',
                'chemical_solution_composers.material_bun',
                'chemical_solution_composers.storage_location',
                'indirect_material_schedules.quantity',
                'indirect_material_schedules.bun',
                'indirect_material_schedules.note',
                'indirect_materials.license',
                db::raw('IF(users.name is null, "-", users.name) AS name'),
                db::raw('IF(indirect_material_schedules.picked_time is null, "-", indirect_material_schedules.picked_time) AS picked_time')
            )
            ->first();

        if (date('Y-m-d H:i:s') < $data->schedule_date) {
            //Belum waktunya ambil
            $response = array(
                'status' => false,
                'logic' => date('Y-m-d H:i:s') . ' < ' . $data->schedule_date,
            );
            return Response::json($response);
        }

        $inventory = IndirectMaterialStock::where('material_number', $data->material_number)
            ->select('material_number', 'bun', db::raw('SUM(quantity) AS quantity'))
            ->groupBy('material_number', 'bun')
            ->get();

        $out = IndirectMaterialOut::where('material_number', $data->material_number)
            ->where('location', $data->location)
            ->select('material_number', 'bun', db::raw('SUM(quantity) AS quantity'))
            ->groupBy('material_number', 'bun')
            ->get();

        $response = array(
            'status' => true,
            'data' => $data,
            'inventory' => $inventory,
            'out' => $out,
        );
        return Response::json($response);

    }

    public function fetchPickingSchedule(Request $request)
    {

        $data = IndirectMaterialSchedule::leftJoin('chemical_solution_composers', function ($join) {
            $join->on('indirect_material_schedules.solution_id', '=', 'chemical_solution_composers.solution_id');
            $join->on('indirect_material_schedules.material_number', '=', 'chemical_solution_composers.material_number');
        })
            ->leftJoin('chemical_solutions', 'chemical_solutions.id', '=', 'indirect_material_schedules.solution_id')
            ->leftJoin('users AS pick', 'indirect_material_schedules.picked_by', '=', 'pick.id')
            ->leftJoin('users AS change', 'indirect_material_schedules.changed_by', '=', 'change.id');

        $username = Auth::user()->username;
        // if ((!str_contains(strtoupper($username), 'PI')) || (Auth::user()->role_code == 'MIS' || Auth::user()->role_code == 'CHM')) {

        // } else {
        //     $emp = EmployeeSync::where('employee_id', strtoupper($username))->first();
        //     $data = $data->where('indirect_material_cost_centers.department', $emp->department);

        // }

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '>=', $datefrom);
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '<=', $dateto);
        }
        if ($request->get('group') != null) {
            $data = $data->whereIn('chemical_solutions.location', $request->get('group'));
        }
        if ($request->get('status') != null) {
            if ($request->get('status') == 'Picked') {
                $data = $data->whereNotNull('indirect_material_schedules.picked_by');
            } elseif ($request->get('status') == 'Scheduled') {
                $data = $data->whereNull('indirect_material_schedules.picked_by');
            }
        }
        if ($request->get('larutan') != null) {
            $data = $data->whereIn('indirect_material_schedules.solution_id', $request->get('larutan'));
        }
        if ($request->get('material') != null) {
            $data = $data->whereIn('indirect_material_schedules.material_number', $request->get('material'));
        }

        if ($request->get('request') != null) {
            // $dateto = date('Y-m-d H:i:s');
            // $data = $data->where(db::raw('date(indirect_material_schedules.schedule_date)'), '<=', $dateto);
            $data = $data->whereNull('indirect_material_schedules.picked_time');
        }
        if ($request->get('location') != null) {
            $data = $data->where('chemical_solutions.location', $request->get('location'));
        }

        $data = $data->select(
            'indirect_material_schedules.id',
            'indirect_material_schedules.schedule_date',
            'indirect_material_schedules.category',
            'chemical_solution_composers.solution_name',
            'chemical_solutions.location',
            'chemical_solution_composers.material_number',
            'chemical_solution_composers.material_description',
            'chemical_solution_composers.storage_location',
            'indirect_material_schedules.quantity',
            'indirect_material_schedules.bun',
            db::raw('IF(pick.name is null, "-", pick.name) AS picked_name'),
            db::raw('IF(indirect_material_schedules.picked_time is null, "-", indirect_material_schedules.picked_time) AS picked_time'),
            db::raw('IF(change.name is null, "-", change.name) AS changed_name'),
            db::raw('IF(indirect_material_schedules.changed_time is null, "-", indirect_material_schedules.changed_time) AS changed_time')
        );

        if ($request->get('request') != null) {
            $data = $data->orderBy('indirect_material_schedules.schedule_date', 'asc')
                ->get();

            $response = array(
                'status' => true,
                'data' => $data,
            );
            return Response::json($response);
        }

        $data = $data->orderBy('indirect_material_schedules.schedule_date', 'desc')
            ->orderBy('indirect_material_schedules.solution_id', 'desc')
            ->limit(500)
            ->get();

        return DataTables::of($data)
            ->addColumn('delete', function ($data) {

                $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                if ($emp && $data->picked_time == '-') {
                    if (str_contains(Auth::user()->role_code, 'CHM') || str_contains(Auth::user()->role_code, 'MIS')) {
                        return '<button style="width: 50%; height: 100%;" onclick="deleteSchedule(\'' . $data->id . '\')" class="btn btn-xs btn-danger form-control"><span><i class="fa fa-trash"></i></span></button>';
                    } else {
                        return '-';
                    }
                } else {
                    return '-';

                }
            })
            ->addColumn('change', function ($data) {

                $emp = EmployeeSync::where('employee_id', Auth::user()->username)->first();

                if (($data->category == 'Pembuatan Baru') && ($data->picked_time != '-') && ($data->changed_time == '-')) {
                    return '<button style="width: 50%; height: 100%;" onclick="change(\'' . $data->id . '\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-refresh"></i></span></button>';
                } else {
                    return '-';

                }
            })
            ->rawColumns([
                'delete' => 'delete',
                'change' => 'change',
            ])
            ->make(true);

    }

    public function fetchStock(Request $request)
    {

        $data = db::select("SELECT material_number, material_description, IF(license IS NULL, '-', license) AS license, storage_location, bun, SUM(quantity) AS quantity, MAX(updated_at) AS updated_at FROM indirect_material_stocks
												GROUP BY material_number, material_description, license, storage_location, bun");

        return DataTables::of($data)->make(true);

    }

    public function fetchNew(Request $request)
    {
        $data = IndirectMaterialStock::select(
            'indirect_material_stocks.qr_code',
            'indirect_material_stocks.material_number',
            'indirect_material_stocks.material_description',
            db::raw("IF(indirect_material_stocks.license IS NULL, '-', indirect_material_stocks.license) AS license"),
            'indirect_material_stocks.quantity',
            'indirect_material_stocks.bun',
            'indirect_material_stocks.storage_location',
            'indirect_material_stocks.in_date',
            'indirect_material_stocks.mfg_date',
            'indirect_material_stocks.exp_date',
            'indirect_material_stocks.print_status',
            'indirect_material_stocks.created_at'
        )
            ->orderBy('indirect_material_stocks.qr_code', 'desc')
            ->orderBy('indirect_material_stocks.created_at', 'desc')
            ->get();

        return DataTables::of($data)
            ->addColumn('print', function ($data) {
                if ($data->print_status == 1) {
                    return '<button style="width: 100%; height: 100%;" onclick="print(\'' . $data->qr_code . '\')" class="btn btn-xs btn-info form-control"><span><i class="fa fa-print"></i></span> Reprint</button>';
                } else {
                    return '<button style="width: 100%; height: 100%;" onclick="print(\'' . $data->qr_code . '\')" class="btn btn-xs btn-primary form-control"><span><i class="fa fa-print"></i></span> Print</button>';
                }
            })
            ->addColumn('check', function ($data) {
                return '<input type="checkbox" id="' . $data->qr_code . '" onclick="showSelected(this)">';
            })
            ->rawColumns([
                'reprint' => 'print',
                'check' => 'check',
            ])
            ->make(true);
    }

    public function fetchOut(Request $request)
    {

        $data = IndirectMaterialOut::orderBy('indirect_material_outs.created_at', 'desc');

        if ($request->get('material_number') != null) {
            $data = $data->whereIn('indirect_material_outs.material_number', $request->get('material_number'));
        }

        $data = $data->select(
            'indirect_material_outs.qr_code',
            'indirect_material_outs.material_number',
            'indirect_material_outs.material_description',
            'indirect_material_outs.location',
            'indirect_material_outs.license',
            'indirect_material_outs.in_date',
            'indirect_material_outs.mfg_date',
            'indirect_material_outs.exp_date',
            'indirect_material_outs.quantity',
            'indirect_material_outs.bun',
            'indirect_material_outs.created_at'
        )
            ->get();

        return DataTables::of($data)
            ->addColumn('print', function ($data) {
                return '<button style="width: 100%; height: 100%;" onclick="print(\'' . $data->qr_code . '\')" class="btn btn-xs btn-info form-control"><span><i class="fa fa-print"></i></span> Reprint</button>';
            })
            ->rawColumns([
                'reprint' => 'print',
            ])
            ->make(true);
    }

    public function fetchIndirectMaterialLog(Request $request)
    {

        $data = IndirectMaterialLog::leftJoin('users', 'users.id', '=', 'indirect_material_logs.created_by');

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $data = $data->where(db::raw('date(indirect_material_logs.created_at)'), '>=', $datefrom);
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
            $data = $data->where(db::raw('date(indirect_material_logs.created_at)'), '<=', $dateto);
        }
        if ($request->get('material_number') != null) {
            $data = $data->whereIn('indirect_material_logs.material_number', $request->get('material_number'));
        }
        if ($request->get('license') != null) {
            $data = $data->where('indirect_material_logs.license', $request->get('license'));
        }

        $data = $data->select(
            'indirect_material_logs.qr_code',
            'indirect_material_logs.in_date',
            'indirect_material_logs.mfg_date',
            'indirect_material_logs.exp_date',
            'indirect_material_logs.material_number',
            'indirect_material_logs.material_description',
            db::raw("IF(indirect_material_logs.license IS NULL, '-', indirect_material_logs.license) AS license"),
            'indirect_material_logs.storage_location',
            'indirect_material_logs.remark',
            'indirect_material_logs.quantity',
            'indirect_material_logs.balance',
            'indirect_material_logs.balance_license',
            'indirect_material_logs.bun',
            'users.name',
            'indirect_material_logs.created_at'
        )
            ->orderBy('indirect_material_logs.created_at', 'asc')
            ->orderBy('indirect_material_logs.id', 'asc')
            ->limit(500)
            ->get();

        return DataTables::of($data)->make(true);
    }

    public function fetchSolutionControl(Request $request)
    {
        $larutan_id = $request->get('larutan');

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = $request->get('datefrom');
        } else {
            $datefrom = date('Y-m-01');
        }

        if (strlen($request->get('dateto')) > 0) {
            $dateto = $request->get('dateto');
        } else {
            $dateto = date('Y-m-d');
        }

        $larutan = ChemicalSolution::where('chemical_solutions.id', $larutan_id)
            ->select(
                'chemical_solutions.id',
                'chemical_solutions.solution_name',
                'chemical_solutions.category',
                'chemical_solutions.target_uom',
                'chemical_solutions.location'
            )
            ->first();

        if ($larutan->category == 'CONTROLLING CHART') {
            $data = db::select("SELECT date.week_date AS date, accumulative, target_max, target_warning FROM
									(SELECT week_date FROM weekly_calendars
									WHERE week_date >= '" . $datefrom . "'
									AND week_date <= '" . $dateto . "'
									) date
									LEFT JOIN
									(SELECT date, target_max, target_warning, MAX(accumulative) as accumulative from chemical_control_logs
									WHERE date >= '" . $datefrom . "'
									AND date <= '" . $dateto . "'
									AND solution_name = '" . $larutan->solution_name . "'
									AND location = '" . $larutan->location . "'
									GROUP BY date, target_max, target_warning
									) chm
									ON date.week_date = chm.date
									ORDER BY date.week_date ASC");

            $date = db::select("SELECT week_date, DAY(week_date) AS date, MONTHNAME(week_date) AS `month`, remark FROM weekly_calendars
								    WHERE week_date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
									ORDER BY week_date ASC");

            $material = ChemicalConvertion::where('solution_id', $larutan->id)->get();

            $detail = db::select("SELECT date, note, SUM(quantity) AS quantity, MAX(accumulative) AS accumulative from chemical_control_logs
                                    WHERE date >= '" . $datefrom . "'
                                    AND date <= '" . $dateto . "'
                                    AND solution_name = '" . $larutan->solution_name . "'
                                    GROUP BY date, note
                                    ORDER BY date ASC");

            $response = array(
                'status' => true,
                'data' => $data,
                'date' => $date,
                'material' => $material,
                'detail' => $detail,
                'location' => $larutan,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }

    }

    public function printLabel($param)
    {

        $qr_code = explode(",", $param);

        $update = IndirectMaterialStock::whereIn('qr_code', $qr_code)
            ->update([
                'print_status' => 1,
            ]);

        $data = IndirectMaterialStock::leftJoin('indirect_materials', 'indirect_material_stocks.material_number', '=', 'indirect_materials.material_number')
            ->whereIn('qr_code', $qr_code)
            ->select(
                'indirect_material_stocks.qr_code',
                'indirect_material_stocks.material_number',
                'indirect_material_stocks.material_description',
                db::raw("IF(indirect_material_stocks.license IS NULL, '-', indirect_material_stocks.license) AS license"),
                'indirect_materials.label',
                'indirect_materials.expired',
                db::raw('date_format(indirect_material_stocks.in_date, "%d-%m-%Y") AS masuk'),
                db::raw('date_format(indirect_material_stocks.mfg_date, "%d-%m-%Y") AS mfg'),
                db::raw('date_format(indirect_material_stocks.exp_date, "%d-%m-%Y") AS exp'),
                db::raw('date_format(indirect_material_stocks.in_date, "%M") AS month')
            )
            ->orderBy('indirect_material_stocks.qr_code', 'desc')
            ->get();

        if (count($data) == 0) {
            $data = IndirectMaterialOut::leftJoin('indirect_materials', 'indirect_material_outs.material_number', '=', 'indirect_materials.material_number')
                ->whereIn('qr_code', $qr_code)
                ->select(
                    'indirect_material_outs.qr_code',
                    'indirect_material_outs.material_number',
                    'indirect_materials.material_description',
                    'indirect_materials.label',
                    db::raw('date_format(indirect_material_outs.in_date, "%d-%m-%Y") AS masuk'),
                    db::raw('date_format(indirect_material_outs.exp_date, "%d-%m-%Y") AS exp'),
                    db::raw('date_format(indirect_material_outs.in_date, "%M") AS month')
                )
                ->orderBy('indirect_material_outs.qr_code', 'desc')
                ->get();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView('indirect_material.chemical.label_pdf', array(
            'data' => $data,
        ));
        return $pdf->stream("Print_label.pdf");

    }

}
