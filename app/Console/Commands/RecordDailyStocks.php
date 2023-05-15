<?php

namespace App\Console\Commands;

use App\DailyStock;
use App\ErrorLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class RecordDailyStocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'record:daily_stocks';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Record daily stock from KITTO';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $truncate_session = db::connection('mysql2')->table('sessions')->truncate();

        $query = "
        SELECT
        	m.remark,
        	i.material_number,
        	i.issue_location,
        	sum( i.lot ) AS quantity
        FROM
        	kitto.inventories AS i
        	LEFT JOIN kitto.materials AS m ON m.material_number = i.material_number
        GROUP BY
        	m.remark,
        	i.material_number,
        	i.issue_location";

        $inventories = db::select($query);

        foreach ($inventories as $inventory) {
            $data = [
                'remark' => $inventory->remark,
                'material_number' => $inventory->material_number,
                'location' => $inventory->issue_location,
                'quantity' => $inventory->quantity,
                'created_by' => 1,
            ];
            try {
                $daily_stock = new DailyStock($data);
                $daily_stock->save();
            } catch (\Exception$e) {
                echo $e->getMessage();
                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => '1',
                ]);
                $error_log->save();
            }
        }
    }
}
