<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Employee;
use Carbon\Carbon;
use DataTables;
use Response;
use DateTime;
use Excel;

class BproController extends Controller{
    public function IndexMonitoringBpro(Request $request){
        $title = 'Body Parts Process Monitoring';
        $title_jp = '最初工程の在庫監視';

        return view('bpro.daily_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp
        ))->with('head', 'Body Parts Process');
    }

    public function FetchMonitoringBpro(Request $request){
        try {
            $data = db::connection('ympimis_2')->select('select * from production_results where issue_location like "%A1%"');

            $data_grafik = db::connection('ympimis_2')->select('select issue_location, sum(quantity) as quantity, DATE_FORMAT(result_date, "%Y-%m-%d") as date from production_results where issue_location like "%A1%" GROUP BY date, issue_location order by date desc');

            $data_target = db::connection('ympimis_2')->select('select date, sum(quantity) as quantity from bpro_targets group by date');

            $date_now = date('Y-m-d');
            $fy = db::select('SELECT fiscal_year FROM weekly_calendars where week_date = "'.$date_now.'"');
            $week_name = db::select('SELECT week_name FROM weekly_calendars where week_date = "'.$date_now.'"');
            $week = db::select('SELECT week_date FROM weekly_calendars where week_name = "'.$week_name[0]->week_name.'" and fiscal_year = "'.$fy[0]->fiscal_year.'" order by id asc');

            $response = array(
                'status' => true,
                'data' => $data,
                'data_grafik' => $data_grafik,
                'data_target' => $data_target,
                'week' => $week);
            return Response::json($response);
        } 
        catch (QueryException $e) {
           $response = array(
            'status' => false);
           return Response::json($response);
       }
   }

    public function UploadTargetBpro(Request $request){
        $filename = "";
        $file_destination = 'data_file/update_karyawan';

        if (count($request->file('file_excel')) > 0) {
                $file = $request->file('file_excel');
                $filename = 'Bpro'.date('YmdHisa').'.'.$file->getClientOriginalExtension();
                $file->move($file_destination, $filename);

                $excel = 'data_file/update_karyawan/' . $filename;
                $rows = Excel::load($excel, function($reader) {
                    $reader->noHeading();
                    $reader->skipRows(1);
                })->toObject();

                for ($i=0; $i < count($rows); $i++) {
                    db::connection('ympimis_2')->table('bpro_targets')->insert([
                        'series' => $rows[$i][0],
                        'date' => $rows[$i][1],
                        'quantity' => $rows[$i][2],
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ]);
                }

                return redirect('/index/monitoring/daily/bpro')->with('success','Target Bulanan Berhasil Di Tambahkan'); 
        }
        else{
            return redirect('/index/monitoring/daily/bpro')->with('error','Tambahkan File Dulu'); 
        }
    }

    public function FetchProductionResultBpro(Request $request){
        if(strlen($request->get('date')) > 0){
            $year = date('Y', strtotime($request->get('date')));
            $date = date('Y-m-d', strtotime($request->get('date')));
            $week_date = date('Y-m-d', strtotime($date. '+ 2 day'));
            $now = date('Y-m-d', strtotime($date));
            $first = date('Y-m-d', strtotime(Carbon::parse('first day of '. date('F Y', strtotime($date)))));
            $week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
            $week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
        }
        else{
            $year = date('Y');
            $date = date('Y-m-d');
            $now = date('Y-m-d');
            $week_date = date('Y-m-d', strtotime(carbon::now()->addDays(2)));
            $first = date('Y-m-01');
            $week = DB::table('weekly_calendars')->where('week_date', '=', $week_date)->first();
            $week2 = DB::table('weekly_calendars')->where('week_date', '=', $date)->first();
        }

        if($date == date('Y-m-01', strtotime($date))){
            $last = $date;
            $debt = "";
        }
        else{
            $last = date('Y-m-d', strtotime('yesterday', strtotime($date)));
        }

        $query = "SELECT
        final.gmc,
        final.series,
        sum( final.plan )- sum( final.actual ) AS plan,
        sum( final.actual ) AS actual
        FROM
        (
            SELECT
            result.gmc,
            result.series,
            IF
            (sum( result.plan )< 0, 0, sum( result.plan )) AS plan,
            IF
            (sum( result.actual )>IF(sum( result.plan )< 0, 0, sum( result.plan )),
                IF
                (sum( result.plan )< 0, 0, sum( result.plan )), sum( result.actual )) AS actual
            FROM
            (
                SELECT
                gmc,
                series,
                0 AS debt,
                sum( quantity ) AS plan,
                0 AS actual
                FROM
                bpro_targets
                GROUP BY
                gmc, series UNION ALL
                SELECT
                material_number AS gmc,
                material_description AS series,
                0 AS debt,
                0 AS plan,
                sum( quantity ) AS actual
                FROM
                production_results
                WHERE issue_location like '%A1%'
                GROUP BY
                gmc, series
            ) AS result
            GROUP BY
            result.series, result.gmc
        ) AS final
        WHERE
        series IS NOT NULL
        GROUP BY
        final.gmc, final.series, plan, actual ORDER BY series ASC";

        $chartResult1 = db::connection('ympimis_2')->select($query);

        $shift = $request->get('p');

        if ($shift == 'all') {
            $remark = " ";
        }else{
            $remark = "and remark = '".$shift."'";
        }

        $query2 = "SELECT
        final.gmc,
        final.series,
        sum( final.plan )- sum( final.actual ) AS plan,
        sum( final.actual ) AS actual
        FROM
        (
            SELECT
            result.gmc,
            result.series,
            IF
            (sum( result.plan )< 0, 0, sum( result.plan )) AS plan,
            IF
            (sum( result.actual )>IF(sum( result.plan )< 0, 0, sum( result.plan )),
                IF
                (sum( result.plan )< 0, 0, sum( result.plan )), sum( result.actual )) AS actual
            FROM
            (
                SELECT
                gmc,
                series,
                0 AS debt,
                sum( quantity ) AS plan,
                0 AS actual
                FROM
                bpro_targets
                WHERE `date` = '".$now."' ".$remark." 
                GROUP BY
                gmc, series UNION ALL
                SELECT
                material_number AS gmc,
                material_description AS series,
                0 AS debt,
                0 AS plan,
                sum( quantity ) AS actual
                FROM
                production_results
                WHERE issue_location like '%A1%' AND date (created_at) = '".$now."'
                GROUP BY
                gmc, series
            ) AS result
            GROUP BY
            result.series, result.gmc
        ) AS final
        WHERE
        series IS NOT NULL
        GROUP BY
        final.gmc, final.series, plan, actual ORDER BY series ASC";

        $chartResult2 = db::connection('ympimis_2')->select($query2);

        $week_min_max = DB::table('weekly_calendars')->where('week_name', '=', $week->week_name)
        ->where(db::raw('date_format(week_date, "%Y")'), $year)
        ->select('week_name', db::raw('date_format(min(week_date), "%d %b") as min_date'), db::raw('date_format(max(week_date), "%d %b %Y") as max_date'))
        ->groupBy('week_name')
        ->get();

        $reason = db::connection('ympimis_2')
        ->table('chorei_reasons')
        ->where('date', $date)
        ->first();

        $response = array(
            'status' => true,
            'chartResult1' => $chartResult1,
            'chartResult2' => $chartResult2,
            'week' => 'Week ' . substr($week->week_name, 1),
            'week2' => 'Week ' . substr($week2->week_name, 1),
            'weekTitle' => 'Week ' . substr($week->week_name, 1),
            'dateTitle' => date('d F Y', strtotime($date)),
            'reason' => $reason,
            'now' => $now,
            'first' => $first,
            'last' => $last,
            'week_min_max' => $week_min_max
        );
        return Response::json($response);
    }

}
