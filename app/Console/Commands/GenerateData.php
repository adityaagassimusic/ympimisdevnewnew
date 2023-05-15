<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;


class GenerateData extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:data';

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
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {

        //Survey Stocktaking
        $date = (int) date('j');
        if($date == 12){
            $surveys = db::connection('ympimis_2')
            ->table('stocktaking_surveys')
            ->get();

            for ($i=0; $i < count($surveys); $i++) { 
                $insert = db::connection('ympimis_2')
                ->table('stocktaking_survey_logs')
                ->insert([
                    'survey_code' => $surveys[$i]->survey_code,
                    'date' => $surveys[$i]->date,
                    'employee_id' => $surveys[$i]->employee_id,
                    'name' => $surveys[$i]->name,
                    'department' => $surveys[$i]->department,
                    'question' => $surveys[$i]->question,
                    'answer' => $surveys[$i]->answer,
                    'poin' => $surveys[$i]->poin,
                    'score' => $surveys[$i]->score,
                    'remark' => $surveys[$i]->remark,
                    'created_at' => $surveys[$i]->created_at,
                    'updated_at' => $surveys[$i]->updated_at
                ]);  
            }

            db::connection('ympimis_2')
            ->table('stocktaking_surveys')
            ->truncate();
        }
        //End Survey Stocktaking


        //Daily Usage Raw Material
        //End Daily Usage Raw Material


    }
}
