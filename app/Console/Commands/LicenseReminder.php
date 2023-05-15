<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class LicenseReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reminder:license';

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
        db::connection('ympimis_2')->select("UPDATE licenses 
            SET STATUS = 'Active' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) > reminder
            AND STATUS <> 'Discontinue'
            ");

        db::connection('ympimis_2')->select("UPDATE licenses 
            SET STATUS = 'AtRisk' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) <= reminder
            AND STATUS <> 'Discontinue'
            ");

        db::connection('ympimis_2')->select("  UPDATE licenses 
            SET STATUS = 'Expired' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) <= 0
            AND STATUS <> 'Discontinue'");

        db::connection('ympimis_2')->select("UPDATE calibrations 
            SET status = 'Aktif' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) > reminder
            AND status <> 'Tidak Aktif'
            ");

        db::connection('ympimis_2')->select("UPDATE calibrations 
            SET status = 'Akan Kalibrasi' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) <= reminder
            AND status <> 'Tidak Aktif'
            ");

        db::connection('ympimis_2')->select("UPDATE calibrations 
            SET status = 'Harus Kalibrasi' 
            WHERE
            TIMESTAMPDIFF( DAY, now(), valid_to ) <= 0
            AND status <> 'Tidak Aktif'");

        app('App\Http\Controllers\LicenseController')->mailLicense();
        app('App\Http\Controllers\StandardizationController')->mailCalibration();
    }
}
