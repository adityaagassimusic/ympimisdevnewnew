<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdatePointingCall extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:pointing_calls';

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
        $calendar = db::table('weekly_calendars')
        ->where('week_date', '=', date("Y-m-d"))
        ->first();

        if($calendar->remark != 'H'){

            $locations = db::table('pointing_calls')
            ->select('location')
            ->whereNull('deleted_at')
            ->distinct()
            ->get();

            foreach($locations as $location){
                $point_titles = db::table('pointing_calls')
                ->select('point_title')
                ->where('location', '=', $location->location)
                ->where('point_no', '<>', 0)
                ->whereNull('deleted_at')
                ->distinct()
                ->get();

                foreach($point_titles as $point_title){
                    $max_point = db::table('pointing_calls')
                    ->where('location', '=', $location->location)
                    ->where('point_title', '=', $point_title->point_title)
                    ->whereNull('deleted_at')
                    ->select(db::raw('max(point_no) as point_no'))
                    ->first();

                    $current_point = db::table('pointing_calls')
                    ->where('location', '=', $location->location)
                    ->where('point_title', '=', $point_title->point_title)
                    ->whereNull('deleted_at')
                    ->where('remark', '=', '1')
                    ->select('point_no')
                    ->first();

                    if($max_point->point_no > $current_point->point_no){
                        $reset_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->update([
                            'remark' => '0',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        $update_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->where('point_no', '=', $current_point->point_no+1)
                        ->update([
                            'remark' => '1',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                    else{
                        $reset_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->update([
                            'remark' => '0',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);

                        $update_point = db::table('pointing_calls')
                        ->where('location', '=', $location->location)
                        ->where('point_title', '=', $point_title->point_title)
                        ->whereNull('deleted_at')
                        ->where('point_no', '=', 1)
                        ->update([
                            'remark' => '1',
                            'updated_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            $current_pic = db::table('pointing_call_pics')
            ->where('remark', '=', 1)
            ->first();

            $max_pic = db::table('pointing_call_pics')
            ->select(db::raw('max(`index`) as max_index'))
            ->first();

            $point_pic = $current_pic->index+1;

            if($current_pic->index >= $max_pic->max_index){
                $point_pic = 1;
            }

            $update_pics = db::table('pointing_call_pics')
            ->update([
                'remark' => 0,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $update_pic = db::table('pointing_call_pics')
            ->where('index', '=', $point_pic)
            ->update([
                'remark' => 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        }
    }
}
