<?php

namespace App\Console\Commands;

use App\KnockDownDetail;
use App\ShipmentSchedule;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class KDShipment extends Command
{

    protected $signature = 'kd:shipment';

    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {

        $knock_down_details = DB::select("SELECT * FROM
                (SELECT kdd.id, kdd.kd_number, kdd.material_number, kdd.quantity, m.hpl, kd.`status` FROM knock_down_details AS kdd
                LEFT JOIN knock_downs AS kd ON kd.kd_number = kdd.kd_number
                LEFT JOIN materials AS m ON m.material_number = kdd.material_number
                WHERE kdd.shipment_schedule_id IS NULL
                AND kd.`status` >= 2
                UNION ALL
                SELECT kdd.id, kdd.kd_number, kdd.material_number, kdd.quantity, m.hpl, kd.`status` FROM knock_down_details AS kdd
                LEFT JOIN knock_downs AS kd ON kd.kd_number = kdd.kd_number
                LEFT JOIN materials AS m ON m.material_number = kdd.material_number
                WHERE kdd.shipment_schedule_id IS NULL
                AND m.hpl IN ('VN-ASSY', 'VN-INJECTION', 'PN-PART', 'MP', 'ZPRO', 'TANPO', 'WELDING', 'BPRO')
                AND kd.`status` = 1) AS maedaoshi
                ORDER BY kd_number, `status` ASC");

        $kon = 0;
        foreach ($knock_down_details as $knock_down_detail) {

            if ($knock_down_detail->hpl == 'MP') {

                $shipment_schedules = DB::select("SELECT ss.id, ss.quantity, ss.actual_quantity FROM shipment_schedules AS ss
                    WHERE ss.quantity > ss.actual_quantity
                    AND ss.material_number = '" . $knock_down_detail->material_number . "'
                    ORDER BY ss.st_date ASC");

                $found = 0;

                foreach ($shipment_schedules as $shipment_schedule) {
                    $diff = $shipment_schedule->quantity - $shipment_schedule->actual_quantity;

                    if ($diff == $knock_down_detail->quantity) {
                        try {
                            $update_shipment = ShipmentSchedule::where('id', '=', $shipment_schedule->id)
                                ->update([
                                    'actual_quantity' => $shipment_schedule->actual_quantity + $knock_down_detail->quantity,
                                ]);
                            $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                                ->update([
                                    'shipment_schedule_id' => $shipment_schedule->id,
                                ]);
                            $found = 1;
                            break;
                        } catch (\Exception$e) {
                            $error_log = new ErrorLog([
                                'error_message' => $e->getMessage(),
                                'created_by' => 1,
                            ]);
                            $error_log->save();
                        }

                    }
                }

                if ($found == 0) {
                    foreach ($shipment_schedules as $shipment_schedule) {
                        $diff = $shipment_schedule->quantity - $shipment_schedule->actual_quantity;
                        $mod = $knock_down_detail->quantity % $diff;

                        if ($diff >= $knock_down_detail->quantity) {
                            if (($mod == 0) || ($knock_down_detail->quantity == 10) || ($knock_down_detail->quantity == 81) || ($knock_down_detail->quantity == 100) || ($knock_down_detail->quantity == 121) || ($knock_down_detail->quantity == 110) || ($diff % 100 == $knock_down_detail->quantity)) {
                                try {
                                    $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                                        ->update([
                                            'shipment_schedule_id' => $shipment_schedule->id,
                                        ]);

                                    $update_shipment = ShipmentSchedule::where('id', $shipment_schedule->id)->first();
                                    $update_shipment->actual_quantity = $update_shipment->actual_quantity + $knock_down_detail->quantity;
                                    $update_shipment->save();

                                    break;
                                } catch (\Exception$e) {
                                    $error_log = new ErrorLog([
                                        'error_message' => $e->getMessage(),
                                        'created_by' => 1,
                                    ]);
                                    $error_log->save();
                                }
                            }
                        }
                    }
                }

            } else {
                $shipment_schedule = ShipmentSchedule::whereRaw('shipment_schedules.quantity > shipment_schedules.actual_quantity')
                    ->where('material_number', '=', $knock_down_detail->material_number)
                    ->orderBy('st_date', 'ASC')
                    ->first();

                if ($shipment_schedule) {
                    $diff = $shipment_schedule->quantity - $shipment_schedule->actual_quantity;

                    if ($diff >= $knock_down_detail->quantity) {
                        try {
                            $update_detail = KnockDownDetail::where('id', '=', $knock_down_detail->id)
                                ->update([
                                    'shipment_schedule_id' => $shipment_schedule->id,
                                ]);

                            $shipment_schedule->actual_quantity = $shipment_schedule->actual_quantity + $knock_down_detail->quantity;
                            $shipment_schedule->save();
                        } catch (\Exception$e) {
                            $error_log = new ErrorLog([
                                'error_message' => $e->getMessage(),
                                'created_by' => 1,
                            ]);
                            $error_log->save();
                        }
                    }
                }
            }
        }

    }
}
