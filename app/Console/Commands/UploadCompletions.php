<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Status;
use App\FloDetail;
use App\LogTransaction;
use Illuminate\Support\Facades\DB;
use File;
use Illuminate\Support\Facades\Auth;
use Response;
use FTP;
use App\ErrorLog;
use Illuminate\Support\Facades\Mail;

class UploadCompletions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'upload:completions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upload completion to SAP';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        $date = date('Y-m-d H:i:s');
        // $date = date('Y-m-d H:i:s', strtotime('2022-02-28 23:59:59'));

        $flofilename = 'ympi_upload_flo_' . date('ymdhis') . '.txt';
        $flofilepath = public_path() . "/uploads/sap/completions/" . $flofilename;
        $flofiledestination = "ma/ympi/prodordconf/" . $flofilename;

        $flo_details = FloDetail::whereNull('flo_details.completion')
        ->where('flo_details.created_at', '<=', $date);

        $flo_details->update(['completion' => $flofilename]);

        $flo_completions = DB::table('flo_details')
        ->leftJoin('materials', 'materials.material_number', '=', 'flo_details.material_number')
        // ->whereNull('flo_details.completion')
        ->where('flo_details.completion', '=', $flofilename)
        ->select(
            DB::raw('if(flo_details.flo_number like "Maedaoshi%", "M", "F") as stats'),
            'materials.issue_storage_location', 
            'flo_details.material_number',
            DB::raw('date(flo_details.created_at) as date'),
            DB::raw('sum(flo_details.quantity) as qty')
        )
        ->groupBy(
            DB::raw('if(flo_details.flo_number like "Maedaoshi%", "M", "F")'),
            'materials.issue_storage_location', 
            DB::raw('date(flo_details.created_at)'),
            'flo_details.material_number'
        )
        ->having(DB::raw('sum(flo_details.quantity)'), '>', 0)
        ->orderByRaw('if(flo_details.flo_number like "Maedaoshi%", "M", "F"), flo_details.material_number')
        ->get();

        $flo_text = "";
        if(count($flo_completions) > 0){
            foreach ($flo_completions as $flo_completion) {
                $flo_text .= self::writeString('8190', 4, " "); //plant ympi
                $flo_text .= self::writeString($flo_completion->issue_storage_location, 4, " "); //sloc
                $flo_text .= self::writeString($flo_completion->material_number, 18, " "); //gmc
                $flo_text .= self::writeDecimal($flo_completion->qty, 14, "0"); //qty
                // $flo_text .= self::writeDate($flo_completion->date, "completion"); //date
                $flo_text .= self::writeDate(date('Y-m-d'), "completion"); //date
                $flo_text .= self::writeString('', 16, " "); //reference number
                $flo_text .= "\r\n";
            }
            File::put($flofilepath, $flo_text);

            try{
                $success = self::uploadFTP($flofilepath, $flofiledestination);

                foreach ($flo_completions as $flo_completion) {
                    $log_transaction = new LogTransaction([
                        'material_number' => $flo_completion->material_number,
                        'issue_plant' => '8190',
                        'issue_storage_location' => $flo_completion->issue_storage_location,
                        'transaction_code' => 'MB1B',
                        'mvt' => '101',
                        'transaction_date' => $date,
                        'qty' => $flo_completion->qty,
                        'reference_file' => $flofilename,
                        'created_by' => 1
                    ]);
                    $log_transaction->save();
                }
            }
            catch(\Exception $e){
                $flo_error = FloDetail::where('completion', '=', $flofilename);
                $flo_error->update(['completion' => null]);

                $error_log = new ErrorLog([
                    'error_message' => $e->getMessage(),
                    'created_by' => '1'
                ]);
                $error_log->save();

                Mail::raw('Error Message: '.$e->getMessage().'; FLO will be uploaded in the next batch job.', function ($message) {
                    $message->to(['mei.rahayu@music.yamaha.com', 'istiqomah@music.yamaha.com', 'silvy.firliany@music.yamaha.com', 'aditya.agassi@music.yamaha.com', 'budhi.apriyanto@music.yamaha.com'])
                    ->subject('Error FLO Completion Upload '.$flofilename);
                });
            }
        }
        else{
            $flo_error = FloDetail::where('completion', '=', $flofilename);
            $flo_error->update(['completion' => null]);
            echo 'false';
        }
    }

    function uploadFTP($from, $to) {
        $upload = FTP::connection()->uploadFile($from, $to, FTP_BINARY);
        return $upload;
    }

    function writeString($text, $maxLength, $char) {
        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                $textLength = strlen($text);
            }
            else {
                $text = "";
            }
            for ($i = 0; $i < ($maxLength - $textLength); $i++) {
                $text .= $char;
            }
        }
        return strtoupper($text);
    }

    function writeDecimal($text, $maxLength, $char) {
        if ($maxLength > 0) {
            $textLength = 0;
            if ($text != null) {
                if(fmod($text,1) > 0){
                    $decimal = self::decimal(fmod($text,1));
                    $decimalLength = strlen($decimal);

                    for ($j = 0; $j < (3- $decimalLength); $j++) {
                        $decimal = $decimal . $char;
                    }
                }
                else{
                    $decimal = $char . $char . $char;
                }
                $textLength = strlen(floor($text));
                $text = floor($text);
            }
            else {
                $text = "";
            }
            for ($i = 0; $i < (($maxLength - 4) - $textLength); $i++) {
                $text = $char . $text;
            }
        }
        $text .= "." . $decimal;
        return $text;
    }

    function writeDate($created_at, $type) {
        $datetime = strtotime($created_at);
        if ($type == "completion") {
            $text = date("dmY", $datetime);
            return $text;
        }
        else {
            $text = date("Ymd", $datetime);
            return $text;
        }
    }

    function decimal($number){
        $num = explode('.', $number);
        return $num[1];
    }
}