<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SDSRimender extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:sds';

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

        $update_artisk = db::connection('ympimis_2')->select("UPDATE safety_data_sheets 
            SET STATUS = 'AtRisk' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), last_date ) >= 0 AND TIMESTAMPDIFF( DAY, now(), last_date ) < 60
            ");
     
        $update_expired = db::connection('ympimis_2')->select("  UPDATE safety_data_sheets 
            SET STATUS = 'Expired' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), last_date ) <= 0");

        app('App\Http\Controllers\QualityAssuranceController')->mailExpaired();
    }
}
