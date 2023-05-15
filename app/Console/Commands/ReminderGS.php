<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class ReminderGS extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:gs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        $tgl = date('Y-m-d');
        $insert_data = db::select("
            INSERT INTO ympimis_2.gs_daily_job_logs (category, area, list_job, operator_gs, names, dates, status, created_at, updated_at)
            SELECT category, area, list_job, operator_gs, names, dates, status, created_at, updated_at FROM ympimis_2.gs_daily_jobs where dates = '".$tgl."'
        ");
        
    }
}
