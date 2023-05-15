<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;
use App\ActivityList;
use App\ProductionAudit;
use Response;
use DataTables;
use Excel;
use App\User;
use App\PresenceLog;
use App\Division;
use App\Department;
use App\Section;
use App\SubSection;
use App\Group;
use App\Grade;
use App\Position;
use App\CostCenter;
use App\PromotionLog;
use App\WeeklyCalendar;
use App\Mutationlog;
use App\HrQuestionLog;
use App\HrQuestionDetail;
use App\Employee;
use App\AuditExternalClaim;
use App\EmployeeSync;
use App\EmploymentLog;
use App\Approver;
use App\OrganizationStructure;
use File;
use DateTime;
use Illuminate\Support\Arr;
use App\Mail\SendEmail;
use Illuminate\Support\Facades\Mail;
use App\UserActivityLog;

class ProductionReportController extends Controller
{
    public function __construct()
    {
      $this->middleware('auth');
      if (isset($_SERVER['HTTP_USER_AGENT']))
        {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT']; 
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) 
            {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
        $this->activity_type = ['Audit',
                        'Training',
                        'Laporan Aktivitas',
                        'Sampling Check',
                        'Pengecekan Foto',
                        'Interview',
                        'Pengecekan',
                        'Pemahaman Proses',
                        'Labelisasi',
                        'Cek Area',
                        'Jishu Hozen',
                        'Cek APD',
                        'Weekly Report',
                        'Temuan NG'];
    }

    function index($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);

        $queryDept = DB::SELECT("SELECT * FROM departments where id = '".$id."'");

        foreach ($queryDept as $key) {
            $department = strtoupper($key->department_name);
        }

        $role_code = Auth::user()->role_code;
        $name = Auth::user()->name;
        $emp = EmployeeSync::where('employee_id',$emp_id)->first();
        $queryActivity = "SELECT DISTINCT(activity_type),frequency,no FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null ORDER BY frequency";
    	$activityList = DB::select($queryActivity);
        $data = array('activity_list' => $activityList,
                      'role_code' => $role_code,
                      'name' => $name,
                      'id' => $id);
        return view('production_report.index', $data
          )->with('page', 'Leader Task Monitoring')->with('dept', $department);
    }

    function activity($id)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        
    	$activityList = ActivityList::find($id);
    	// foreach ($activityList as $activity) {
    		$activity_type = $activityList->activity_type;
    	// }
    	if ($activity_type == "Audit") {
    		return redirect('/index/production_audit/details/'.$id)->with('page', 'Production Audit')->with('no', $activityList->no);
    	}
    	elseif($activity_type == "Training"){
            return redirect('/index/training_report/index/'.$id)->with('page', 'Training')->with('no', $activityList->no);
    	}
    	elseif($activity_type == "Laporan Aktivitas"){
            return redirect('/index/audit_report_activity/index/'.$id)->with('page', 'Laporan Aktivitas')->with('no', $activityList->no);
    	}
    	elseif($activity_type == "Sampling Check"){
            return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', $activityList->no);
    	}
    	elseif($activity_type == "Pengecekan Foto"){
            return redirect('/index/daily_check_fg/product/'.$id)->with('page', 'Daily Check FG')->with('no', $activityList->no);
    	}
        elseif($activity_type == "Interview"){
            return redirect('/index/interview/index/'.$id)->with('page', 'Interview')->with('no', $activityList->no);
        }
        elseif($activity_type == "Labelisasi"){
            return redirect('/index/labeling/index/'.$id)->with('page', 'Labeling')->with('no', $activityList->no);
        }
        elseif($activity_type == "Pengecekan"){
            return redirect('/index/first_product_audit/list_proses/'.$id)->with('page', 'First Product Audit')->with('no', $activityList->no);
        }
        elseif($activity_type == "Pemahaman Proses"){
            return redirect('/index/audit_process/index/'.$id)->with('page', 'Audit Process')->with('no', $activityList->no);
        }
        elseif($activity_type == "Cek Area"){
            return redirect('/index/area_check/index/'.$id)->with('page', 'Area Check')->with('no', $activityList->no);
        }
        elseif($activity_type == "Jishu Hozen"){
            return redirect('/index/jishu_hozen/nama_pengecekan/'.$id)->with('page', 'Jishu Hozen')->with('no', $activityList->no);
        }
        elseif($activity_type == "Cek APD"){
            return redirect('/index/apd_check/index/'.$id)->with('page', 'APD Check')->with('no', $activityList->no);
        }
        elseif($activity_type == "Weekly Report"){
            return redirect('/index/weekly_report/index/'.$id)->with('page', 'Weekly Report')->with('no', $activityList->no);
        }
        elseif($activity_type == "Temuan NG"){
            return redirect('/index/ng_finding/index/'.$id)->with('page', 'Temuan NG')->with('no', $activityList->no);
        }
        elseif($activity_type == "Audit Kanban"){
            return redirect('/index/audit_kanban/index/'.$id)->with('page', 'Audit Kanban')->with('no', $activityList->no);
        }elseif($activity_type == "Daily Audit"){
            $url = secure_url('/index/daily/audit/'.$id.'/'.$activityList->remark);
            return redirect($url)->with('page', 'Daily Audit')->with('no', $activityList->no);
        }
    }

    function report_all($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }

        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();
        $queryLeader2 = "select DISTINCT(employees.name), employees.employee_id
            from employees
            join mutation_logs on employees.employee_id= mutation_logs.employee_id
            join promotion_logs on employees.employee_id= promotion_logs.employee_id
            where (mutation_logs.department = '".$departments."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='foreman')";
        $leader = DB::select($queryLeader2);
        $leader2 = DB::select($queryLeader2);
        $leader3 = DB::select($queryLeader2);

        $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        return view('production_report.report_all2',  array('title' => 'Leader Task Monitoring',
            'title_jp' => '職長業務管理',
            'id' => $id,
            'data' => $data,
            'activity_list' => $activityList,
            'leader2' => $leader2,
            'leader3' => $leader3,
            'leader' => $leader,
            'departments' => strtoupper($departments),
        ))->with('page', strtoupper('Leader Task Monitoring'));
    }

    public function fetchReportByLeader(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $date = db::select("select week_name,week_date from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."'");

      $queryPMonth = "select DATE_FORMAT(date_sub(concat('".$bulan."','-01'), INTERVAL 1 MONTH),'%Y-%m') as last_month";
      $pMonth = DB::select($queryPMonth);

      foreach($pMonth as $pMonth){
        $prevMonth = $pMonth->last_month;
      }

      $queryLeader = "select DISTINCT(leader_dept) from activity_lists where department_id = '".$id."'";
      $leaderrr = DB::select($queryLeader);

      $data[] = null;
      $dataleader[] = null;

      $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."'");

      $data_compressor = [];

      foreach($leaderrr as $leader){
        $dataleader = $leader->leader_dept; 
        $compressor = DB::connection('ympimis_2')->SELECT("SELECT
            count(
            DISTINCT ( date )) AS count 
        FROM
            daily_audits 
        WHERE
            auditor_name = '".$dataleader."' 
            AND category = 'compressor'
            AND DATE_FORMAT( date, '%Y-%m' ) = '".$bulan."'");

        $steam = DB::connection('ympimis_2')->SELECT("SELECT
            count(
            DISTINCT ( date )) AS count 
        FROM
            daily_audits 
        WHERE
            auditor_name = '".$dataleader."' 
            AND category = 'steam'
            AND DATE_FORMAT( date, '%Y-%m' ) = '".$bulan."'");
        $data[] = db::select("SELECT
            monthly.leader_name,
            monthly.jumlah_activity_monthly,
            monthly.jumlah_training + monthly.jumlah_laporan_aktivitas + monthly.jumlah_labeling + monthly.jumlah_interview + monthly.jumlah_first_product_audit + monthly.jumlah_jishu_hozen+ monthly.jumlah_cek_apd_bulanan AS jumlah_monthly,
            COALESCE (((
                        monthly.jumlah_training + monthly.jumlah_laporan_aktivitas + monthly.jumlah_labeling + monthly.jumlah_interview + monthly.jumlah_first_product_audit + monthly.jumlah_jishu_hozen + monthly.jumlah_cek_apd_bulanan
                        )/ monthly.jumlah_activity_monthly 
                    )* 100,
                0 
            ) AS persen_monthly,
            weekly.jumlah_activity_weekly * 4 AS jumlah_activity_weekly,
            ( weekly.jumlah_sampling_kd + weekly.jumlah_sampling_fg + weekly.jumlah_audit + weekly.jumlah_audit_process + weekly.jumlah_apd_check + weekly.jumlah_weekly_report + weekly.jumlah_training_report_weekly+weekly.jumlah_audit_kanban_foreman ) AS jumlah_all_weekly,
            ( weekly.jumlah_sampling_kd + weekly.jumlah_sampling_fg + weekly.jumlah_audit + weekly.jumlah_audit_process + weekly.jumlah_apd_check + weekly.jumlah_weekly_report + weekly.jumlah_training_report_weekly+weekly.jumlah_audit_kanban_foreman )/(
                weekly.jumlah_activity_weekly * 4 
            )* 100 AS persen_weekly,
            (
            SELECT
                count( week_date ) 
            FROM
                weekly_calendars 
            WHERE
                DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$bulan."' 
            AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' ))* daily.jumlah_activity_daily AS jumlah_activity_daily,
            daily.jumlah_daily_check + daily.jumlah_area_check + daily.jumlah_apd_check + daily.jumlah_apd_check_cuci_asam+daily.jumlah_audit_kanban+".$compressor[0]->count."+".$steam[0]->count." AS jumlah_daily,
            COALESCE (((
                        daily.jumlah_daily_check + daily.jumlah_area_check + daily.jumlah_apd_check + daily.jumlah_apd_check_cuci_asam+daily.jumlah_audit_kanban+".$compressor[0]->count."+".$steam[0]->count."
                        )/((
                        SELECT
                            count( week_date ) 
                        FROM
                            weekly_calendars 
                        WHERE
                            DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$bulan."' 
                        AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' ))* daily.jumlah_activity_daily 
                        ))* 100,
                0 
            ) AS persen_daily,
            daily.jumlah_day,
            daily.cur_day,
            ( daily.cur_day / daily.jumlah_day )* 100 AS persen_cur_day 
        FROM
            (
            SELECT
                count( activity_type ) AS jumlah_activity_monthly,
                leader_dept AS leader_name,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( actlist.id )) AS jumlah_training 
                    FROM
                        training_reports
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( training_reports.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND training_reports.leader = '".$dataleader."' 
                        AND training_reports.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        AND actlist.deleted_at is null
                    GROUP BY
                        training_reports.leader 
                        ),
                    0 
                ) AS jumlah_training,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jumlah_laporan 
                    FROM
                        audit_report_activities
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( audit_report_activities.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND audit_report_activities.leader = '".$dataleader."' 
                        AND audit_report_activities.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        audit_report_activities.leader 
                        ),
                    0 
                ) AS jumlah_laporan_aktivitas,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jumlah_labeling 
                    FROM
                        labelings
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( labelings.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND labelings.leader = '".$dataleader."' 
                        AND labelings.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        labelings.leader 
                        ),
                    0 
                ) AS jumlah_labeling,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jumlah_interview 
                    FROM
                        interviews
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( interviews.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND interviews.leader = '".$dataleader."' 
                        AND interviews.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        interviews.leader 
                        ),
                    0 
                ) AS jumlah_interview,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jumlah_first_product_audit 
                    FROM
                        first_product_audit_details
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        MONTH = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND first_product_audit_details.leader = '".$dataleader."' 
                        AND first_product_audit_details.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        first_product_audit_details.leader 
                        ),
                    0 
                ) AS jumlah_first_product_audit,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jishu_hozens 
                    FROM
                        jishu_hozens
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        jishu_hozens.month = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND jishu_hozens.leader = '".$dataleader."' 
                        AND jishu_hozens.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        jishu_hozens.leader 
                        ),
                    0 
                ) AS jumlah_jishu_hozen,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( leader )) AS jumlah_cek_apd_bulanan
                    FROM
                        apd_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT(apd_checks.created_at,'%Y-%m') = '".$bulan."' 
                        AND actlist.frequency = 'Monthly' 
                        AND apd_checks.leader = '".$dataleader."'
                        AND apd_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."'  
                    GROUP BY
                        apd_checks.leader 
                        ),
                    0 
                ) AS jumlah_cek_apd_bulanan
            FROM
                activity_lists 
            WHERE
                deleted_at IS NULL 
                AND department_id = '".$id."' 
                AND leader_dept = '".$dataleader."' 
                AND activity_lists.frequency = 'Monthly' 
            GROUP BY
                leader_dept 
            ) monthly,
            (
            SELECT
                count( activity_type ) AS jumlah_activity_weekly,
                leader_dept AS leader_name,
                COALESCE ((
                    SELECT COUNT(a.jumlah_sampling) FROM(
                        SELECT
                                sampling_checks.week_name AS jumlah_sampling 
                        FROM
                                sampling_checks
                                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                        WHERE
                                DATE_FORMAT( sampling_checks.date, '%Y-%m' ) = '".$bulan."'
                                AND actlist.frequency = 'Weekly' 
                                AND sampling_checks.leader = '".$dataleader."'
                                AND sampling_checks.deleted_at IS NULL 
                                AND actlist.department_id = '".$id."' 
                        GROUP BY
                                actlist.activity_name,week_name) a
                        ),
                    0 
                ) AS jumlah_sampling_fg,
                0 AS jumlah_sampling_kd,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( audit_processes.week_name )) AS jumlah_sampling 
                    FROM
                        audit_processes
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( audit_processes.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND audit_processes.leader = '".$dataleader."' 
                        AND audit_processes.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        audit_processes.leader 
                        ),
                    0 
                ) AS jumlah_audit_process,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( production_audits.week_name )) AS jumlah_audit 
                    FROM
                        production_audits
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id
                        JOIN point_check_audits AS point_check ON point_check.id = point_check_audit_id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND point_check.leader = '".$dataleader."' 
                        AND production_audits.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        point_check.leader 
                        ),
                    0 
                ) AS jumlah_audit,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( apd_checks.week_name )) AS jumlah_audit 
                    FROM
                        apd_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( apd_checks.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND apd_checks.leader = '".$dataleader."' 
                        AND apd_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        apd_checks.leader 
                        ),
                    0 
                ) AS jumlah_apd_check,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( weekly_activity_reports.week_name )) AS jumlah_weekly_report 
                    FROM
                        weekly_activity_reports
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( weekly_activity_reports.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND weekly_activity_reports.leader = '".$dataleader."' 
                        AND weekly_activity_reports.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        weekly_activity_reports.leader 
                        ),
                    0 
                ) AS jumlah_weekly_report,
                COALESCE ((
                SELECT SUM(a.jumlah_training_report_weekly) FROM (
                SELECT
                        count(
                        DISTINCT(weekly_calendars.week_name) ) AS jumlah_training_report_weekly 
                    FROM
                        training_reports
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id
                        LEFT JOIN weekly_calendars ON weekly_calendars.week_date = training_reports.date
                    WHERE
                        DATE_FORMAT( training_reports.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND training_reports.leader = '".$dataleader."' 
                        AND training_reports.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        training_reports.leader,activity_list_id) a
                        ),
                    0 
                ) AS jumlah_training_report_weekly,
                COALESCE ((
                SELECT SUM(a.jumlah_audit_kanban_foreman) FROM (
                SELECT
                        count(
                        DISTINCT(weekly_calendars.week_name) ) AS jumlah_audit_kanban_foreman 
                    FROM
                        audit_kanbans
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id
                        LEFT JOIN weekly_calendars ON weekly_calendars.week_date = audit_kanbans.check_date
                    WHERE
                        DATE_FORMAT( audit_kanbans.check_date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Weekly' 
                        AND audit_kanbans.leader = '".$dataleader."' 
                        AND audit_kanbans.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                    GROUP BY
                        audit_kanbans.leader,activity_list_id) a
                        ),
                    0 
                ) AS jumlah_audit_kanban_foreman
            FROM
                activity_lists 
            WHERE
                deleted_at IS NULL 
                AND department_id = '".$id."' 
                AND leader_dept = '".$dataleader."' 
                AND activity_lists.frequency = 'Weekly' 
            GROUP BY
                leader_dept 
            ) weekly,
            (
            SELECT COALESCE
                ( count( DISTINCT(activity_name) ), 0 ) AS jumlah_activity_daily,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( daily_checks.production_date )) AS jumlah_laporan 
                    FROM
                        daily_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( daily_checks.production_date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Daily' 
                        AND daily_checks.leader = '".$dataleader."' 
                        AND daily_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        ),
                    0 
                ) AS jumlah_daily_check,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( area_checks.date )) AS jumlah_area_check 
                    FROM
                        area_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( area_checks.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Daily' 
                        AND area_checks.leader = '".$dataleader."' 
                        AND area_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        ),
                    0 
                ) AS jumlah_area_check,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( apd_checks.date )) AS jumlah_apd_check 
                    FROM
                        apd_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( apd_checks.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Daily' 
                        AND apd_checks.leader = '".$dataleader."' 
                        AND apd_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        AND actlist.activity_name = 'Cek Alat Pelindung Diri (APD)' 
                        ),
                    0 
                ) AS jumlah_apd_check,
                COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( apd_checks.date )) AS jumlah_apd_check_cuci_asam 
                    FROM
                        apd_checks
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( apd_checks.date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Daily' 
                        AND apd_checks.leader = '".$dataleader."' 
                        AND apd_checks.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        AND actlist.activity_name = 'Cek Alat Pelindung Diri (APD) Cuci Asam' 
                        ),
                    0 
                ) AS jumlah_apd_check_cuci_asam,
                        COALESCE ((
                    SELECT
                        count(
                        DISTINCT ( check_date )) AS jumlah_audit_kanban
                    FROM
                        audit_kanbans
                        JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
                    WHERE
                        DATE_FORMAT( audit_kanbans.check_date, '%Y-%m' ) = '".$bulan."' 
                        AND actlist.frequency = 'Daily' 
                        AND audit_kanbans.leader = '".$dataleader."' 
                        AND audit_kanbans.deleted_at IS NULL 
                        AND actlist.department_id = '".$id."' 
                        ),
                    0 
                ) AS jumlah_audit_kanban,
                (
                SELECT
                    count( week_date ) AS jumlah_day 
                FROM
                    weekly_calendars 
                WHERE
                    DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$bulan."' 
                AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' )) AS jumlah_day,
                4 AS jumlah_week,
                (
                SELECT
                IF
                    (
                        DATE_FORMAT( CURDATE(), '%Y-%m' ) != '".$bulan."',
                        (
                        SELECT
                            count( week_date ) AS jumlah_day 
                        FROM
                            weekly_calendars 
                        WHERE
                            week_date BETWEEN concat( '".$bulan."', '-01' ) 
                            AND LAST_DAY(
                            concat( '".$bulan."', '-01' )) 
                        AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' )),
                        (
                        SELECT
                            count( week_date ) AS jumlah_day 
                        FROM
                            weekly_calendars 
                        WHERE
                            week_date BETWEEN concat( DATE_FORMAT( CURDATE(), '%Y-%m' ), '-01' ) 
                            AND CURDATE() 
                        AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' ))) AS jumlah_day 
                ) AS cur_day,
                (
                SELECT
                    count(
                    DISTINCT ( week_name )) AS jumlah_week 
                FROM
                    weekly_calendars 
                WHERE
                    week_date BETWEEN concat( LEFT ( curdate(), 7 ), '-01' ) 
                AND CURDATE()) AS cur_week 
            FROM
                activity_lists 
            WHERE
                deleted_at IS NULL 
                AND department_id = '".$id."' 
                AND leader_dept = '".$dataleader."' 
                AND activity_lists.frequency = 'Daily' 
            GROUP BY
            leader_dept 
            ) daily");
      }
      $monthTitle = date("F Y", strtotime($bulan));


      $response = array(
        'status' => true,
        'leaderrr' => $leaderrr,
        'datas' => $data,
        'date' => $date,
        'id' => $id,
        'dataleader' => $dataleader,
        'monthTitle' => $monthTitle
      );

      return Response::json($response);
    }

    public function fetchDetailReportWeekly(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = date('Y-m');
        }
        $detail[] = null;
        $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."' ORDER BY
    week_date");
        $date2 = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."' ORDER BY
    week_date");

        foreach($date2 as $date2){
            $detail[] = db::select("SELECT detail.id_activity,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.week_name,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            4 as plan,
            '".$date2->week_name."' as week_name,
            IF(activity_type = 'Audit',
            (select count(production_audits.week_name) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and week_name = '".$date2->week_name."'
                                and actlist.id = id_activity
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Sampling Check',
            (select count(sampling_checks.week_name) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pemahaman Proses',
            (select count(audit_processes.week_name) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$week_date."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Cek APD',
            (select count(apd_checks.week_name) as jumlah_apd_check
                from apd_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$week_date."'
                                and apd_checks.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Weekly Report',
            (select count(weekly_activity_reports.week_name) as jumlah_weekly_report
                from weekly_activity_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$week_date."'
                                and weekly_activity_reports.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Audit Kanban',
            (select count(DISTINCT(weekly_calendars.week_name)) as jumlah_audit_kanban
                from audit_kanbans
                    join activity_lists as actlist on actlist.id = activity_list_id
                                        left join weekly_calendars on weekly_calendars.week_date = audit_kanbans.check_date
                    where DATE_FORMAT(audit_kanbans.check_date,'%Y-%m') = '".$week_date."'
                                and audit_kanbans.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and weekly_calendars.week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Training',
            (select count(weekly_calendars.week_name) as jumlah_training_report
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                                        left join weekly_calendars on weekly_calendars.week_date = training_reports.date
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."'
                                and training_reports.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and weekly_calendars.week_name = '".$date2->week_name."'
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),0)))))))
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and frequency = '".$frequency."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        }
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'date' => $date,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailReportMonthly(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = date('Y-m');
        }

        $detail = db::select("SELECT detail.id_activity,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     IF(frequency = '".$frequency."', 1,4) as plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/IF(frequency = '".$frequency."', 1,4))*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,frequency,
            IF(activity_type = 'Audit',
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Training',
            (select count(DISTINCT(leader)) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(DISTINCT(leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Sampling Check',
            (select count(DISTINCT(sampling_checks.date)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and  actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$week_date."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Labelisasi',
            (select count(DISTINCT(leader)) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$week_date."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Pemahaman Proses',
            (select count(DISTINCT(audit_processes.date)) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$week_date."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Interview',
            (select count(DISTINCT(interviews.leader)) as jumlah_interview
                from interviews
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(interviews.date,'%Y-%m') = '".$week_date."'
                                and interviews.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
                        IF(activity_type = 'Pengecekan',
            (select count(DISTINCT(first_product_audit_details.leader)) as jumlah_first_product_audit
                from first_product_audit_details
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$week_date."'
                                and first_product_audit_details.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Jishu Hozen',
            (select count(DISTINCT(jishu_hozens.leader)) as jumlah_jishu_hozen
                from jishu_hozens
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where month = '".$week_date."'
                                and jishu_hozens.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),
            IF(activity_type = 'Cek APD',
            (select count(DISTINCT(apd_checks.leader)) as jumlah_apd_check
                from apd_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(apd_checks.created_at,'%Y-%m') = '".$week_date."'
                                and apd_checks.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    and actlist.frequency = '".$frequency."'),0)))))))))))
            jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and frequency = '".$frequency."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    and deleted_at is null
                    GROUP BY activity_type, frequency,id,activity_name,leader_dept) detail");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailReportDaily(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $frequency = $request->get('frequency');
            $week_date = date('Y-m');
        }

        $date = db::select("SELECT
                week_date 
            FROM
                weekly_calendars 
            WHERE
                DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$week_date."' 
                AND remark != 'H'");

        $act_name = DB::select("select DISTINCT(activity_name),frequency,activity_type from activity_lists where 
             leader_dept = '".$leader_name."'
            and activity_lists.department_id = '".$id."'
            and activity_lists.frequency = '".$frequency."'");

        $compressor = DB::CONNECTION('ympimis_2')->SELECT("SELECT DISTINCT
                ( date ) 
            FROM
                daily_audits 
            WHERE
                auditor_name = '".$leader_name."' 
                AND category = 'compressor' 
                AND DATE_FORMAT( date, '%Y-%m' ) = '".$week_date."'");

        $steam = DB::CONNECTION('ympimis_2')->SELECT("SELECT DISTINCT
                ( date ) 
            FROM
                daily_audits 
            WHERE
                auditor_name = '".$leader_name."' 
                AND category = 'steam' 
                AND DATE_FORMAT( date, '%Y-%m' ) = '".$week_date."'");

        $detail = db::select("SELECT
            weekly_calendars.week_date,
            (
            SELECT
                count( week_date ) 
            FROM
                weekly_calendars 
            WHERE
                DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$week_date."' 
            AND week_date NOT IN ( SELECT week_date FROM weekly_calendars where remark = 'H' )) AS plan,
            (
            SELECT
                count(
                DISTINCT ( production_date )) 
            FROM
                daily_checks
                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
            WHERE
                DATE_FORMAT( production_date, '%Y-%m' ) = '".$week_date."' 
                AND leader = '".$leader_name."' 
                AND production_date = weekly_calendars.week_date 
                AND actlist.department_id = '".$id."' 
                AND actlist.frequency = '".$frequency."' 
            ) AS jumlah_daily_check,
            (
            SELECT
                count(
                DISTINCT ( date )) 
            FROM
                area_checks
                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
            WHERE
                DATE_FORMAT( date, '%Y-%m' ) = '".$week_date."' 
                AND leader = '".$leader_name."' 
                AND date = weekly_calendars.week_date 
                AND actlist.department_id = '".$id."' 
                AND actlist.frequency = '".$frequency."' 
            ) AS jumlah_area_check,
            (
            SELECT
                count(
                DISTINCT ( date )) 
            FROM
                apd_checks
                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
            WHERE
                DATE_FORMAT( date, '%Y-%m' ) = '".$week_date."' 
                AND leader = '".$leader_name."' 
                AND date = weekly_calendars.week_date 
                AND actlist.department_id = '".$id."' 
                AND actlist.frequency = '".$frequency."' 
                AND actlist.activity_name = 'Cek Alat Pelindung Diri (APD) Cuci Asam'
            ) AS jumlah_apd_check_cuci_asam,
            (
            SELECT
                count(
                DISTINCT ( date )) 
            FROM
                apd_checks
                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
            WHERE
                DATE_FORMAT( date, '%Y-%m' ) = '".$week_date."' 
                AND leader = '".$leader_name."' 
                AND date = weekly_calendars.week_date 
                AND actlist.department_id = '".$id."' 
                AND actlist.frequency = '".$frequency."' 
                AND actlist.activity_name = 'Cek Alat Pelindung Diri (APD)'
            ) AS jumlah_apd_check,
            (
            SELECT
                count(
                DISTINCT ( check_date )) 
            FROM
                audit_kanbans
                JOIN activity_lists AS actlist ON actlist.id = activity_list_id 
            WHERE
                DATE_FORMAT( check_date, '%Y-%m' ) = '".$week_date."' 
                AND leader = '".$leader_name."' 
                AND check_date = weekly_calendars.week_date 
                AND actlist.department_id = '".$id."' 
                AND actlist.frequency = '".$frequency."' 
            ) AS jumlah_audit_kanban
        FROM
            weekly_calendars 
        WHERE
            DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$week_date."' 
            AND weekly_calendars.remark != 'H'");
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'date' => $date,
            'act_name' => $act_name,
            'leader_name' => $leader_name,
            'frequency' => $frequency,
            'compressor' => $compressor,
            'steam' => $steam,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);
    }

    public function fetchDetailReportPrev(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $week_date = date('Y-m');
        }

        $queryPMonth = "select DATE_FORMAT(date_sub(concat('".$week_date."','-01'), INTERVAL 1 MONTH),'%Y-%m') as last_month";
        $pMonth = DB::select($queryPMonth);

        foreach($pMonth as $pMonth){
            $prevMonth = $pMonth->last_month;
        }

        $detail = db::select("SELECT detail.id_activity,
                     detail.link,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            sum(plan_item) as plan,
            IF(activity_type = 'Audit',CONCAT('index/production_audit/details/',activity_lists.id),
            IF(activity_type = 'Training',CONCAT('index/training_report/index/',activity_lists.id),
            IF(activity_type = 'Laporan Aktivitas',CONCAT('index/audit_report_activity/index/',activity_lists.id),
            IF(activity_type = 'Sampling Check',CONCAT('index/sampling_check/index/',activity_lists.id),
            IF(activity_type = 'Pengecekan Foto',CONCAT('index/daily_check_fg/index/',activity_lists.id,'/',(select DISTINCT(product) from daily_checks where activity_list_id = activity_lists.id)),
            IF(activity_type = 'Labelisasi',CONCAT('index/labeling/index/',activity_lists.id),
            IF(activity_type = 'Pemahaman Proses',CONCAT('index/audit_process/index/',activity_lists.id),0)))))))
            as link,
            IF(activity_type = 'Audit',
            (select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    ),
            IF(activity_type = 'Training',
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Sampling Check',
            (select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Labelisasi',
            (select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$prevMonth."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id ='".$id."'
                    ),
            IF(activity_type = 'Pemahaman Proses',
            (select count(*) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') ='".$prevMonth."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),0))))))) 
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        $monthTitle = date("F Y", strtotime($prevMonth));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'leader_name' => $leader_name,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailReportMonthly2(Request $request,$id){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $week_date = date('Y-m');
        }

        $queryPMonth = "select DATE_FORMAT(date_sub(concat('".$week_date."','-01'), INTERVAL 1 MONTH),'%Y-%m') as last_month";
        $pMonth = DB::select($queryPMonth);

        foreach($pMonth as $pMonth){
            $prevMonth = $pMonth->last_month;
        }

        $detail = db::select("SELECT detail.id_activity,
                     detail.link,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
        from 
        (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
            sum(plan_item) as plan,
            IF(activity_type = 'Audit',CONCAT('index/production_audit/details/',activity_lists.id),
            IF(activity_type = 'Training',CONCAT('index/training_report/index/',activity_lists.id),
            IF(activity_type = 'Laporan Aktivitas',CONCAT('index/audit_report_activity/index/',activity_lists.id),
            IF(activity_type = 'Sampling Check',CONCAT('index/sampling_check/index/',activity_lists.id),
            IF(activity_type = 'Pengecekan Foto',CONCAT('index/daily_check_fg/index/',activity_lists.id,'/',(select DISTINCT(product) from daily_checks where activity_list_id = activity_lists.id)),
            IF(activity_type = 'Labelisasi',CONCAT('index/labeling/index/',activity_lists.id),
            IF(activity_type = 'Pemahaman Proses',CONCAT('index/audit_process/index/',activity_lists.id),0)))))))
            as link,
            IF(activity_type = 'Audit',
            (select count(*) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.department_id = '".$id."'
                                and actlist.id = id_activity
                    ),
            IF(activity_type = 'Training',
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Laporan Aktivitas',
            (select count(*) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Sampling Check',
            (select count(*) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Pengecekan Foto',
            (select count(*) as jumlah_daily_check
                from daily_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(daily_checks.check_date,'%Y-%m') = '".$prevMonth."'
                                and leader_dept = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),
            IF(activity_type = 'Labelisasi',
            (select count(*) as jumlah_labeling
                from labelings
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(labelings.date,'%Y-%m') = '".$prevMonth."'
                                and labelings.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id ='".$id."'
                    ),
            IF(activity_type = 'Pemahaman Proses',
            (select count(*) as jumlah_audit_process
                from audit_processes
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_processes.date,'%Y-%m') ='".$prevMonth."'
                                and audit_processes.leader = '".$leader_name."'
                                and actlist.id = id_activity
                                and actlist.department_id = '".$id."'
                    ),0))))))) 
            as jumlah_aktual
                from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                    and activity_name != 'Null'
                    GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        $monthTitle = date("F Y", strtotime($prevMonth));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'leader_name' => $leader_name,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchReportDaily(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week_date, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            week_date,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."'
                        and activity_lists.frequency = 'Daily')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."'
                              and production_audits.date = week_date)
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Daily'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and training_reports.date = week_date)
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.date = week_date)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Daily'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and audit_report_activities.date = week_date)
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Daily'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Daily'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportWeekly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            DISTINCT(week_name) as week,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."' 
                        and activity_lists.frequency = 'Weekly')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Weekly'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.week_name = week)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Weekly'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Weekly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Weekly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportMonthly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $tahun = $request->get('week_date');
      }
      else{
        $tahun = date('Y');
      }

      $data = db::select("select tot.month, 
        (tot.jumlah_audit-tot.jumlah_not_good)+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_plan,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            DISTINCT(DATE_FORMAT(week_date,'%Y-%m')) as month,
            (select
                        count(*) as jumlah_plan
                        from activity_lists 
                        where deleted_at is null 
                        and department_id = '".$id."' 
                        and activity_lists.frequency = 'Monthly')
            as jumlah_plan,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = month
                    and actlist.frequency = 'Monthly'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."')
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                        and  actlist.frequency = 'Monthly'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."')
            as jumlah_good,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = month
                    and  actlist.frequency = 'Monthly'
                                and production_audits.kondisi = 'Not Good'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."')
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y') = '".$tahun."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailMonthly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        count(activity_lists.activity_alias) as jumlah_plan,
        IF(activity_type = 'Audit',
        ((select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly') - (select count(DISTINCT(production_audits.date)) as jumlah_audit
                        from production_audits
                                join activity_lists as actlist on actlist.id = activity_list_id
                                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                                and actlist.department_id = '".$id."'
                                and  actlist.frequency = 'Monthly'
                                and production_audits.kondisi = 'Not Good'
                                and production_audits.deleted_at is null)),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Monthly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Monthly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and actlist.department_id = '".$id."'
                    and  actlist.frequency = 'Monthly'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Monthly'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportConditional(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
      }
      else{
        $bulan = date('Y-m');
      }

      $data = db::select("select tot.week_date, 
        tot.jumlah_audit+
        tot.jumlah_training+
        tot.jumlah_sampling+
        tot.jumlah_laporan_aktivitas as jumlah_all,
        tot.jumlah_audit,
        tot.jumlah_training,
        tot.jumlah_sampling,
        tot.jumlah_laporan_aktivitas,
        tot.jumlah_good,
        tot.jumlah_not_good
        from 
        (select
            week_date,
            (select count(DISTINCT(production_audits.date)) as jumlah_audit
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and production_audits.deleted_at is null 
                              and actlist.department_id = '".$id."'
                                and production_audits.date = week_date)
            as jumlah_audit,
            (select count(*) as jumlah_training
                from training_reports
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."'
                    and actlist.frequency = 'Conditional'
                                and training_reports.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and training_reports.date = week_date)
            as jumlah_training,
            (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
                from sampling_checks
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and sampling_checks.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and sampling_checks.date = week_date)
            as jumlah_sampling,
            (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
                from audit_report_activities
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."'
                    and  actlist.frequency = 'Conditional'
                                and audit_report_activities.deleted_at is null 
                                and actlist.department_id = '".$id."'
                                and audit_report_activities.date = week_date)
            as jumlah_laporan_aktivitas,
            (select
                sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
                as jumlah_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and  actlist.frequency = 'Conditional'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_good,
            (select
                sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
                as jumlah_not_good
                    from production_audits
                        join activity_lists as actlist on actlist.id = activity_list_id
                        where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."'
                        and actlist.frequency = 'Conditional'
                                        and production_audits.deleted_at is null 
                                        and actlist.department_id = '".$id."'
                                        and production_audits.date = week_date)
            as jumlah_not_good
            from weekly_calendars
            where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$bulan."') tot");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        'ctg' => $request->get("ctg"),
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailConditional(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $tanggal = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.date = '".$tanggal."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Conditional'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Conditional'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$tanggal."'
                    and actlist.department_id = '".$id."'
                    and  actlist.frequency = 'Conditional'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Conditional'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailWeekly(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $week_name = $request->get('week_date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        count(activity_lists.activity_alias) as jumlah_plan,
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.week_name = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Weekly'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Weekly'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and  actlist.frequency = 'Weekly'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Weekly'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportDetailDaily(Request $request,$id)
    {
      if($request->get('date') != null){
        $week_name = $request->get('date');
      }

      $data = db::select("select
        DISTINCT(activity_type),
        IF(activity_type = 'Audit',
        (select count(DISTINCT(production_audits.date)) as jumlah_audit
            from production_audits
                join activity_lists as actlist on actlist.id = activity_list_id
                where production_audits.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        (IF(activity_type = 'Training',
        (select count(*) as jumlah_training
            from training_reports
                join activity_lists as actlist on actlist.id = activity_list_id
                where training_reports.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        IF(activity_type = 'Laporan Aktivitas',
        (select count(DISTINCT(audit_report_activities.leader)) as jumlah_laporan
            from audit_report_activities
                join activity_lists as actlist on actlist.id = activity_list_id
                where audit_report_activities.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),
        IF(activity_type = 'Sampling Check',
        (select count(DISTINCT(sampling_checks.leader)) as jumlah_sampling
            from sampling_checks
                join activity_lists as actlist on actlist.id = activity_list_id
                where sampling_checks.date = '".$week_name."'
                and actlist.department_id = '".$id."'
                and  actlist.frequency = 'Daily'),0)))))
        as jumlah_aktual,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Good' then 1 else 0 end)
            as jumlah_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and actlist.frequency = 'Daily'),null)
        as jumlah_good,
        IF(activity_type = 'Audit',
          (select
            sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) 
            as jumlah_not_good
                from production_audits
                    join activity_lists as actlist on actlist.id = activity_list_id
                    where production_audits.date = '".$week_name."'
                    and actlist.department_id = '".$id."'
                    and  actlist.frequency = 'Daily'),null)
        as jumlah_not_good
        from activity_lists 
        where deleted_at is null 
        and department_id = '".$id."' 
        and  activity_lists.frequency = 'Daily'
        GROUP BY activity_type");
      // $monthTitle = date("F Y", strtotime($tgl));


      $response = array(
        'status' => true,
        'datas' => $data,
        // 'monthTitle' => $monthTitle

      );

      return Response::json($response); 
    }

    public function fetchReportAudit(Request $request,$id)
    {
      if($request->get('tgl') != null){
        $bulan = $request->get('tgl');
        $frequency = $request->get('frequency');
        $fynow = DB::select("select DISTINCT(fiscal_year) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."'");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
        $fynow = DB::select("select fiscal_year from weekly_calendars where CURDATE() = week_date");
        foreach($fynow as $fynow){
            $fy = $fynow->fiscal_year;
        }
      }

      $data = DB::select("select weekly_calendars.week_date,count(*) as jumlah_semua, sum(case when production_audits.kondisi = 'Good' then 1 else 0 end) as jumlah_good, sum(case when production_audits.kondisi = 'Not Good' then 1 else 0 end) as jumlah_not_good from (select week_date from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$bulan."' and fiscal_year='".$fy."') as weekly_calendars join production_audits on production_audits.date = weekly_calendars.week_date join activity_lists on activity_lists.id = production_audits.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and production_audits.deleted_at is null GROUP BY  weekly_calendars.week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function fetchReportTraining(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $frequency = $request->get('frequency');
        $bulan = $request->get('week_date');
      }
      else{
        $frequency = $request->get('frequency');
        $bulan = date('Y-m');
      }

      $data = DB::select("select CONCAT(year(date),'-',month(date)) as month, (select count(*) as jumlah_training from activity_lists where activity_type = 'Training' and frequency = '".$frequency."') as plan, count(*) as jumlah_training from weekly_calendars join training_reports on training_reports.date = weekly_calendars.week_date join activity_lists on activity_lists.id = training_reports.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null GROUP BY CONCAT(year(date),'-',month(date))");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")
      );

      return Response::json($response);
    }

    public function fetchReportSampling(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
        $frequency = $request->get('frequency');
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
      }

      $data = DB::select("select week_date, count(*) as jumlah_sampling_check from weekly_calendars join sampling_checks on sampling_checks.date = weekly_calendars.week_date join activity_lists on activity_lists.id = sampling_checks.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = '".$frequency."' and sampling_checks.deleted_at is null GROUP BY week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function fetchReportLaporanAktivitas(Request $request,$id)
    {
      if($request->get('week_date') != null){
        $bulan = $request->get('week_date');
        $frequency = $request->get('frequency');
      }
      else{
        $bulan = date('Y-m');
        $frequency = $request->get('frequency');
      }

      $data = DB::select("select week_date, count(*) as jumlah_laporan from weekly_calendars join audit_report_activities on audit_report_activities.date = weekly_calendars.week_date join activity_lists on activity_lists.id = audit_report_activities.activity_list_id where activity_lists.department_id = '".$id."' and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$bulan."' and activity_lists.frequency = 'Monthly' and audit_report_activities.deleted_at is null GROUP BY week_date");
      $monthTitle = date("F Y", strtotime($bulan));

      // $monthTitle = date("F Y", strtotime($tgl));

      $response = array(
        'status' => true,
        'datas' => $data,
        'monthTitle' => $monthTitle,
        // 'bulan' => $request->get("tgl")

      );

      return Response::json($response);
    }

    public function detailTraining(Request $request, $id){
      if($request->get('week_date') != null){
        $week_date = $request->get("week_date");
        $frequency = $request->get('frequency');
      }
      else{
        $week_date = date('Y-m');
        $frequency = $request->get('frequency');
      }
      if($frequency == 'Conditional' || $frequency == 'Daily'){
        $query = "select *, training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id where department_id = '".$id."' and activity_type = 'Training' and training_reports.date = '".$week_date."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null";
      }
      else{
        $query = "select *, training_reports.id as training_id from training_reports join activity_lists on activity_lists.id = training_reports.activity_list_id where department_id = '".$id."' and activity_type = 'Training' and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$week_date."' and activity_lists.frequency = '".$frequency."' and training_reports.deleted_at is null";
      }

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    public function detailProductionReport(Request $request, $id){
      $activity_type = $request->get("activity_type");
        $query = "SELECT *, activity_lists.id as id_activity FROM `activity_lists` join departments on departments.id = activity_lists.department_id where activity_lists.activity_type = '".$activity_type."' and activity_lists.deleted_at is null and activity_lists.department_id = '".$id."'";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);

    }

    public function detailSamplingCheck(Request $request, $id){
      $week_date = $request->get("week_date");
        $query = "select *,CONCAT(activity_lists.id, '/', sampling_checks.subsection, '/', DATE_FORMAT(sampling_checks.date,'%Y-%m')) as linkurl, sampling_checks.id as sampling_check_id from sampling_checks join activity_lists on activity_lists.id = sampling_checks.activity_list_id where department_id = '".$id."' and activity_type = 'Sampling Check' and week_name = '".$week_date."' and sampling_checks.deleted_at is null";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    public function fetchPlanReport(Request $request, $id){
      $frequency = $request->get("frequency");
        $query = "select * from activity_lists where frequency = '".$frequency."' and department_id = '".$id."'";

      $detail = db::select($query);

      return DataTables::of($detail)->make(true);
    }

    function report_by_act_type($id,$activity_type)
    {
        // $activityList = ActivityList::find($id);
        // // foreach ($activityList as $activity) {
        //     $activity_type = $activityList->activity_type;
        // }
        if ($activity_type == "Audit") {
            return redirect('/index/production_audit/report_audit/'.$id)->with('page', 'Production Audit');
        }
        elseif($activity_type == "Training"){
            return redirect('/index/training_report/report_training/'.$id)->with('page', 'Training');
        }
        elseif($activity_type == "Laporan Aktivitas"){
            var_dump("halooo");
        }
        elseif($activity_type == "Sampling Check"){
            return redirect('/index/sampling_check/index/'.$id)->with('page', 'Sampling Check')->with('no', '3');
        }
        elseif($activity_type == "Pengecekan Foto"){

        }
        elseif($activity_type == "Interview"){

        }
        elseif($activity_type == "Labelisasi"){

        }
        elseif($activity_type == "Pengecekan"){

        }
    }

    function approval($id)
    {
        $leader = DB::SELECT("SELECT DISTINCT(leader_dept) FROM activity_lists where department_id = '".$id."' and activity_lists.activity_name is not null and activity_lists.deleted_at is null");
        $data = array('leader' => $leader,
                      'id' => $id);
        return view('production_report.approval', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_list($id,$leader_name)
    {
        $month = date('Y-m');
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
             detail.activity_type,
             detail.activity_name,
             detail.jumlah_approval,
             detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                    IF(activity_type = 'Audit',
                        (SELECT count(*) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT count(*) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT count(*) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT count(*) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT count(*) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT count(*) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT count(*) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT count(*) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT count(*) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT count(*) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT count(*) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT count(*) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT count(*) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Temuan NG',
                        (SELECT count(*) FROM ng_findings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0))))))))))))))
                as jumlah_approval,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT(CONCAT('/index/audit_report_activity/print_audit_report_email/',id_activity_list,'/','".$month."')) FROM audit_report_activities
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_report_activities.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Temuan NG',
                        (SELECT DISTINCT(CONCAT('/index/ng_finding/print_ng_finding_email/',id_activity_list,'/','".$month."')) FROM ng_findings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(ng_findings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0))))))))))))))
                as link
                        from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                        and activity_name is not null
                        and deleted_at is null) detail");
        $monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'id' => $id);
        return view('production_report.approval_list', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_list_filter(Request $request,$id,$leader_name)
    {
        $month = $request->get('month');
        $activity_list = DB::SELECT("SELECT detail.id_activity_list,
             detail.activity_type,
             detail.activity_name,
             detail.jumlah_approval,
             detail.link
                from
                (select activity_type, activity_lists.id as id_activity_list, activity_name,
                    IF(activity_type = 'Audit',
                        (SELECT count(*) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT count(*) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT count(*) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT count(*) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        SELECT
                            count(*) 
                        FROM
                            audit_report_activities
                            JOIN audit_guidances ON audit_guidances.id = audit_guidance_id 
                        WHERE
                            audit_report_activities.leader = '".$leader_name."'
                            AND audit_guidances.`month` = '".$month."'
                            AND audit_report_activities.activity_list_id = id_activity_list 
                            AND approval IS NULL 
                            AND audit_report_activities.deleted_at IS NULL),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT count(*) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT count(*) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT count(*) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT count(*) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT count(*) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT count(*) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT count(*) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT count(*) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as jumlah_approval,
                IF(activity_type = 'Audit',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM production_audits
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Training',
                        (SELECT DISTINCT(CONCAT('/index/training_report/print_training_approval/',id_activity_list,'/','".$month."')) FROM training_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(training_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Sampling Check',
                        (SELECT DISTINCT(CONCAT('/index/sampling_check/print_sampling_email/',id_activity_list,'/','".$month."')) FROM sampling_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(sampling_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan Foto',
                        (SELECT DISTINCT(CONCAT('/index/daily_check_fg/print_daily_check_email/',id_activity_list,'/','".$month."')) FROM daily_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(daily_checks.production_date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Laporan Aktivitas',
                        (SELECT DISTINCT
                            (
                            CONCAT( '/index/audit_report_activity/print_audit_report_email/556/', '".$month."' )) 
                        FROM
                            audit_report_activities
                            JOIN audit_guidances ON audit_guidances.id = audit_guidance_id 
                        WHERE
                            audit_report_activities.leader = '".$leader_name."'
                            AND audit_guidances.`month` = '".$month."' 
                            AND audit_report_activities.activity_list_id = id_activity_list 
                            AND approval IS NULL 
                            AND audit_report_activities.deleted_at IS NULL),
                    IF(activity_type = 'Pemahaman Proses',
                        (SELECT DISTINCT(CONCAT('/index/audit_process/print_audit_process_email/',id_activity_list,'/','".$month."')) FROM audit_processes
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(audit_processes.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Pengecekan',
                        (SELECT DISTINCT(CONCAT('/index/production_report/approval_detail/',id_activity_list,'/','".$month."')) FROM first_product_audit_details
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Interview',
                        (SELECT DISTINCT(CONCAT('/index/interview/print_approval/',id_activity_list,'/','".$month."')) FROM interviews
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(interviews.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Labelisasi',
                        (SELECT DISTINCT(CONCAT('/index/labeling/print_labeling_email/',id_activity_list,'/','".$month."')) FROM labelings
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(labelings.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek Area',
                        (SELECT DISTINCT(CONCAT('/index/area_check/print_area_check_email/',id_activity_list,'/','".$month."')) FROM area_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(area_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Jishu Hozen',
                        (SELECT DISTINCT(CONCAT('/index/jishu_hozen/print_jishu_hozen_approval/',id_activity_list,'/','".$month."')) FROM jishu_hozens
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(jishu_hozens.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Cek APD',
                        (SELECT DISTINCT(CONCAT('/index/apd_check/print_apd_check_email/',id_activity_list,'/','".$month."')) FROM apd_checks
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(apd_checks.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),
                    IF(activity_type = 'Weekly Report',
                        (SELECT DISTINCT(CONCAT('/index/weekly_report/print_weekly_report_email/',id_activity_list,'/','".$month."')) FROM weekly_activity_reports
                        where send_status = 'Sent'
                        and leader = '".$leader_name."'
                        and DATE_FORMAT(weekly_activity_reports.date,'%Y-%m') = '".$month."'
                        and activity_list_id = id_activity_list
                        and approval is null
                        and deleted_at is null),0)))))))))))))
                as link
                        from activity_lists
                        where leader_dept = '".$leader_name."'
                        and department_id = '".$id."'
                        and activity_name is not null
                        and deleted_at is null) detail");
        $monthTitle = date("F Y", strtotime($month));
        $data = array('activity_list' => $activity_list,
                      'leader_name' => $leader_name,
                      'monthTitle' => $monthTitle,
                      'id' => $id);
        return view('production_report.approval_list', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function approval_detail($activity_list_id,$month)
    {
        $activityList = ActivityList::find($activity_list_id);
        $activity_name = $activityList->activity_name;
        $departments = $activityList->departments->department_name;
        $id_departments = $activityList->departments->id;
        $activity_alias = $activityList->activity_alias;
        $activity_type = $activityList->activity_type;
        $leader = $activityList->leader_dept;

        // $month = date('Y-m');

        if ($activity_type == 'Audit') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/production_audit/print_audit_email/',production_audits.activity_list_id,'/','".$month."','/',product,'/',proses)) as link,
                CONCAT(product,' - ',proses) as title
                FROM production_audits
                        join point_check_audits on production_audits.point_check_audit_id = point_check_audits.id
                        where send_status = 'Sent'
                        and DATE_FORMAT(production_audits.date,'%Y-%m') = '".$month."'
                        and production_audits.activity_list_id = '".$activity_list_id."'
                        and approval is null
                        and production_audits.deleted_at is null");
        }
        else if ($activity_type == 'Pengecekan') {
            $detail = DB::select("SELECT DISTINCT(CONCAT('/index/first_product_audit/print_first_product_audit_email/',first_product_audit_details.activity_list_id,'/',first_product_audit_details.first_product_audit_id,'/','".$month."')) as link, CONCAT(proses,' - ',jenis) as title FROM first_product_audit_details
                    join first_product_audits on first_product_audits.id = first_product_audit_details.first_product_audit_id
                    where send_status = 'Sent'
                    and DATE_FORMAT(first_product_audit_details.date,'%Y-%m') = '".$month."'
                    and first_product_audit_details.activity_list_id = '".$activity_list_id."'
                    and approval is null
                    and first_product_audit_details.deleted_at is null");
        }
        $data = array('detail' => $detail,
                      'leader' => $leader,
                      'activity_name' => $activity_name,
                      'id_departments' => $id_departments,
                      'activity_list_id' => $activity_list_id);
        return view('production_report.approval_detail', $data
          )->with('page', 'Approval Leader Task Monitoring');
    }

    function report_by_task($id)
    {
        $queryDepartments = "SELECT * FROM departments where id='".$id."'";
        $department = DB::select($queryDepartments);
        foreach($department as $department){
            $departments = $department->department_name;
        }

        $activityList = ActivityList::where('department_id',$id)->where('activity_name','!=','Null')->get();
        // $queryLeader2 = "select DISTINCT(employees.name), employees.employee_id
        //     from employees
        //     join mutation_logs on employees.employee_id= mutation_logs.employee_id
        //     join promotion_logs on employees.employee_id= promotion_logs.employee_id
        //     where (mutation_logs.department = '".$departments."' and promotion_logs.`position` = 'leader') or (mutation_logs.department = '".$departments."' and promotion_logs.`position`='foreman')";
        // $leader = DB::select($queryLeader2);
        // $leader2 = DB::select($queryLeader2);
        // $leader3 = DB::select($queryLeader2);

        // $data = db::select("select count(*) as jumlah_activity, activity_type from activity_lists where deleted_at is null and department_id = '".$id."' GROUP BY activity_type");
        return view('production_report.report_by_task',  array('title' => 'Leader Tasks',
            'title_jp' => '???',
            'id' => $id,
            // 'data' => $data,
            'activity_list' => $activityList,
            'activity_type' => $this->activity_type,
            // 'leader2' => $leader2,
            // 'leader3' => $leader3,
            // 'leader' => $leader,
            'departments' => $departments,
        ))->with('page', 'Leader Task Monitoring');
    }

    public function fetchReportByTask(Request $request)
    {
        if ($request->get('month') == null) {
            $month = date('Y-m');
        }
        else{
            $month = $request->get('month');
        }

        if ($request->get('activity_type') == null) {
            $activity_type = 'Audit';
        }
        else{
            $activity_type = $request->get('activity_type');
        }

        $monthTitle = date("F Y", strtotime($month));

        $week = DB::SELECT("SELECT DISTINCT(week_name) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$month."'");

        $day = DB::SELECT("select week_date from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$month."' and remark != 'H'");

        if ($activity_type == 'Audit') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from production_audits where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and production_audits.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Audit' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Training') {
            $data = DB::SELECT("select leader_dept,activity_name,(select count(*) from training_reports where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and training_reports.deleted_at is null) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Training' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Laporan Aktivitas') {
            $data = DB::SELECT("select activity_lists.id,leader_dept,activity_name,(select count(*) from audit_report_activities where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and audit_report_activities.deleted_at is null ) as hasil,(SELECT count(*) from audit_guidances where leader = leader_dept and month = '".$month."' and audit_guidances.deleted_at is null) as plan,frequency from activity_lists  where activity_lists.activity_type = 'Laporan Aktivitas' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Sampling Check') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from sampling_checks where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and sampling_checks.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Sampling Check' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Pengecekan Foto') {
            $data = DB::SELECT("select leader_dept,
            activity_name,
            (select COALESCE(GROUP_CONCAT(check_date ORDER BY check_date),0) from daily_checks where activity_lists.id = activity_list_id and DATE_FORMAT(check_date,'%Y-%m') = '".$month."' and daily_checks.deleted_at is null ) as hasil,
            (select count(*) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$month."' and remark != 'H') as plan,
            frequency 
            from activity_lists 
            where activity_lists.activity_type = 'Pengecekan Foto' 
            GROUP BY activity_lists.id,leader_dept,activity_name,frequency 
            ORDER BY activity_name");
        }

        if ($activity_type == 'Interview') {
            $data = DB::SELECT("select leader_dept,activity_name,(select count(*) from interviews where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and interviews.deleted_at is null) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Interview' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Pengecekan') {
            $data = DB::SELECT("select leader_dept,activity_name,(select count(DISTINCT(leader)) from first_product_audit_details where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and first_product_audit_details.deleted_at is null) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Pengecekan' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Pemahaman Proses') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from audit_processes where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and audit_processes.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Pemahaman Proses' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Labelisasi') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(COUNT(DISTINCT(leader)),0) from labelings where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and labelings.deleted_at is null ) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Labelisasi' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Cek Area') {
            $data = DB::SELECT("select leader_dept,
            activity_name,
            (select COALESCE(GROUP_CONCAT(date ORDER BY date),0) from area_checks where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and area_checks.deleted_at is null ) as hasil,
            (select count(*) from weekly_calendars where DATE_FORMAT(week_date,'%Y-%m') = '".$month."' and remark != 'H') as plan,
            frequency 
            from activity_lists 
            where activity_lists.activity_type = 'Cek Area' 
            GROUP BY activity_lists.id,leader_dept,activity_name,frequency 
            ORDER BY activity_name");
        }

        if ($activity_type == 'Jishu Hozen') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(COUNT(DISTINCT(leader)),0) from jishu_hozens where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and jishu_hozens.deleted_at is null ) as hasil,1 as plan,frequency from activity_lists where activity_lists.activity_type = 'Jishu Hozen' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Cek APD') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from apd_checks where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and apd_checks.deleted_at is null ) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Cek APD' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }

        if ($activity_type == 'Weekly Report') {
            $data = DB::SELECT("select leader_dept,activity_name,(select COALESCE(GROUP_CONCAT(DISTINCT(week_name) ORDER BY week_name),0) from weekly_activity_reports where activity_lists.id = activity_list_id and DATE_FORMAT(date,'%Y-%m') = '".$month."' and weekly_activity_reports.deleted_at is null) as hasil,4 as plan,frequency from activity_lists where activity_lists.activity_type = 'Weekly Report' GROUP BY activity_lists.id,leader_dept,activity_name,frequency ORDER BY activity_name");
        }


        // $response = array(
        //     'status' => true,
        //     'activity_type' => $activity_type,
        //     'datas' => $audit,
        //     'monthTitle' => $monthTitle,
        //     'month' => $month
        //   );

        //   return Response::json($response);

          $response = array(
            'status' => true,
            'activity_type' => $activity_type,
            'datas' => $data,
            'week' => $week,
            'day' => $day,
            'monthTitle' => $monthTitle,
            'month' => $month
          );

      return Response::json($response);
    }
    
    public function indexNgJelasMonitoring()
    {
        $title = 'Audit NG Jelas Monitoring';
        $title_jp = '明らか不良監査の監視';

        $loc = 'All';

        $fiscal = DB::SELECT("SELECT DISTINCT
                fiscal_year 
            FROM
                weekly_calendars
                ORDER BY week_date");

        // return view('production_report.audit_ng_jelas', array(
        return view('production_report.audit_ng_jelas2', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fiscal' => $fiscal,
            'loc' => $loc,
        ))->with('page', 'Audit NG Jelas Monitoring');
    }

    public function fetchNgJelasMonitoring(Request $request)
    {
        try {
            if ($request->get('fiscal_year') != "") {
                $month = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%M %Y' ) AS `month_name`,
                    fiscal_year 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = '".$request->get('fiscal_year')."'");
            }else{
                $month = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%M %Y' ) AS `month_name`,
                    fiscal_year 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = (
                    SELECT
                        fiscal_year 
                    FROM
                        weekly_calendars 
                WHERE
                    week_date = DATE(
                    NOW()))");
            }

            $audits = [];
            for ($i=0; $i < count($month); $i++) { 
                $audit = DB::SELECT("SELECT
                    count(
                    DISTINCT ( audit_title )) AS point_claim,
                    0 AS done_claim,
                    count(
                    DISTINCT ( point_check_audits.id )) point_audit,
                    0 as done_audit,
                    'production' AS type,
                    '".$month[$i]->months."' AS `month`,
                    DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                FROM
                    activity_lists
                    JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id 
                WHERE
                    activity_lists.activity_type = 'Audit' 
                    AND audit_title IS NOT NULL UNION ALL
                SELECT
                    count(
                    DISTINCT ( audit_title )) AS point_claim,
                    0 AS done_claim,
                    count(
                    DISTINCT ( audit_external_claim_points.id )) point_audit,
                    0 AS done_audit,
                    'qa' AS type,
                    '".$month[$i]->months."' AS `month`,
                    DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                FROM
                    audit_external_claim_schedules
                    JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$month[$i]->months."'
                where audit_external_claim_schedules.remark = 'ng_jelas'");

                array_push($audits, $audit);
            }

            $titles = DB::SELECT("SELECT DISTINCT
                ( audit_title ),
                area,
                periode,
                departments.department_shortname 
            FROM
                audit_external_claim_points
                JOIN departments ON departments.department_name = audit_external_claim_points.department 
            ORDER BY
                area");

            $schedules = DB::SELECT("SELECT DISTINCT
                ( audit_external_claim_schedules.audit_id ),
                audit_external_claim_points.audit_title,
                DATE_FORMAT( schedule_date, '%Y-%m' ) AS `month` 
            FROM
                audit_external_claim_schedules
                JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                where remark = 'ng_jelas'
            ORDER BY
                `month`");

            $response = array(
                'status' => true,
                'audits' => $audits,
                'titles' => $titles,
                'schedules' => $schedules,
                'fiscalTitle' => $month[0]->fiscal_year,
                'month' => $month,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            
        }
    }

    public function fetchNgJelasMonitoring2(Request $request)
    {
        try {
            if ($request->get('fiscal_year') != "") {
                $month = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`,
                    fiscal_year 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = '".$request->get('fiscal_year')."'
                ORDER BY week_date");
            }else{
                $month = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`,
                    fiscal_year 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = (
                    SELECT
                        fiscal_year 
                    FROM
                        weekly_calendars 
                WHERE
                    week_date = DATE(
                    NOW()))
                ORDER BY week_date");
            }

            $titles = DB::SELECT("SELECT DISTINCT
                ( audit_title ),
                area,
                periode,
                departments.department_shortname 
            FROM
                audit_external_claim_points
                JOIN departments ON departments.department_name = audit_external_claim_points.department 
            ORDER BY
                area,periode");

            $audits = [];
            $resumes = [];
            $resumes_claim = [];
            for ($i=0; $i < count($month); $i++) {
                $production = "SELECT
                    sum( a.point_claim ) AS point_claim,
    sum( a.done_claim_ok ) AS done_claim_ok,
    sum( a.done_claim_ns ) AS done_claim_ns,
    sum( a.done_claim_ng ) AS done_claim_ng,
                    sum( a.point_audit ) AS point_audit,
                    sum( a.done_audit ) AS done_audit,
                    a.`month`,
                    a.months 
                FROM
                    (
                    SELECT
                        count(
                        DISTINCT ( audit_title )) AS point_claim,
                        (
                        SELECT
            sum( CASE WHEN c.done_claim = 'OK' THEN 1 ELSE 0 END ) 
        FROM
            (
            SELECT
            IF
                ( GROUP_CONCAT( `kondisi` ) LIKE '%Not Good%', 'NG', 'OK' ) AS done_claim 
            FROM
                activity_lists
                JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id
                LEFT JOIN production_audits ON production_audits.point_check_audit_id = point_check_audits.id 
            WHERE
                activity_lists.activity_type = 'Audit' 
                AND audit_title IS NOT NULL 
                AND DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$month[$i]->months."' 
            GROUP BY
                audit_title 
            ) c 
        ) AS done_claim_ok,
        0 AS done_claim_ns,
        (
        SELECT
            sum( CASE WHEN c.done_claim = 'NG' THEN 1 ELSE 0 END ) 
        FROM
            (
            SELECT
            IF
                ( GROUP_CONCAT( `kondisi` ) LIKE '%Not Good%', 'NG', 'OK' ) AS done_claim 
                        FROM
                            activity_lists
                            JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id
                            LEFT JOIN production_audits ON production_audits.point_check_audit_id = point_check_audits.id 
                        WHERE
                            activity_lists.activity_type = 'Audit' 
                            AND audit_title IS NOT NULL 
                            AND DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$month[$i]->months."' 
            GROUP BY
                audit_title 
            ) c 
        ) AS done_claim_ng,
    IF
        (
            '".$month[$i]->months."' <= DATE_FORMAT( '2021-08-01', '%Y-%m' ),
            count(
            DISTINCT ( point_check_audits.id ))- 9,
            count(
                        DISTINCT ( point_check_audits.id ))) point_audit,
                        (
                        SELECT
                            count(
                            DISTINCT ( point_check_audit_id )) AS done_audit 
                        FROM
                            activity_lists
                            JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id
                            LEFT JOIN production_audits ON production_audits.point_check_audit_id = point_check_audits.id 
                        WHERE
                            activity_lists.activity_type = 'Audit' 
                            AND audit_title IS NOT NULL 
                            AND DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$month[$i]->months."' 
                        ) AS done_audit,
                        'production' AS type,
                        '".$month[$i]->months."' AS `month`,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                    FROM
                        activity_lists
                        JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id 
                    WHERE
                        activity_lists.activity_type = 'Audit' 
                    AND audit_title IS NOT NULL 
    ) a 
GROUP BY
    a.`month`,
    a.months";

                $qa = "SELECT
                        sum( a.point_claim ) AS point_claim,
                        sum( a.done_claim_ok ) AS done_claim_ok,
                        sum( a.done_claim_ns ) AS done_claim_ns,
                        sum( a.done_claim_ng ) AS done_claim_ng,
                        sum( a.point_audit ) AS point_audit,
                        sum( a.done_audit ) AS done_audit,
                        a.`month`,
                        a.months 
                    FROM
                        (
                        SELECT
                            count(
                            DISTINCT ( audit_title )) AS point_claim,
                            (
                            SELECT
                                sum( CASE WHEN c.done_claim = 'OK' THEN 1 ELSE 0 END ) 
                            FROM
                                (
                                SELECT
                                IF
                                    (
                                        GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%' 
                                        AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL,
                                        'NG',
                                    IF
                                    ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%' AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL, 'NS', 'OK' )) AS done_claim 
                                FROM
                                    audit_external_claims
                                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                                WHERE
                                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                                    and audit_external_claims.remark = 'ng_jelas'
                                GROUP BY
                                    schedule_id 
                                ) c 
                            ) AS done_claim_ok,
                            (
                            SELECT
                                sum( CASE WHEN c.done_claim = 'NG' THEN 1 ELSE 0 END ) 
                            FROM
                                (
                                SELECT
                                IF
                                    (
                                        GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%' 
                                        AND GROUP_CONCAT( audit_external_claims.handling ) IS NOT NULL,
                                        'OK',
                                    IF
                                    ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%' AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL, 'NG', 'OK' )) AS done_claim 
                                FROM
                                    audit_external_claims
                                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                                WHERE
                                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                                    and audit_external_claims.remark = 'ng_jelas'
                                GROUP BY
                                    schedule_id 
                                ) c 
                            ) AS done_claim_ng,
                            (
                            SELECT
                                sum( CASE WHEN c.done_claim = 'NS' THEN 1 ELSE 0 END ) 
                            FROM
                                (
                                SELECT
                                IF
                                    (
                                        GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%' 
                                        AND GROUP_CONCAT( audit_external_claims.handling ) IS NOT NULL,
                                        'OK',
                                    IF
                                    ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%' AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL, 'NS', 'OK' )) AS done_claim 
                                FROM
                                    audit_external_claims
                                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                                WHERE
                                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                                    and audit_external_claims.remark = 'ng_jelas'
                                GROUP BY
                                    schedule_id 
                                ) c 
                            ) AS done_claim_ns,
                            count(
                            DISTINCT ( audit_external_claim_points.id )) point_audit,
                            (
                            SELECT
                                COUNT(
                                DISTINCT ( audit_external_claims.id )) AS done_audit 
                            FROM
                                audit_external_claims
                                JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                            WHERE
                                DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                                and audit_external_claims.remark = 'ng_jelas'
                            ) AS done_audit,
                            'qa' AS type,
                            '".$month[$i]->months."' AS `month`,
                            DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                        FROM
                            audit_external_claim_schedules
                            JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                            AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                            where audit_external_claim_schedules.remark = 'ng_jelas'
                        ) a 
                    GROUP BY
                        a.`month`,
                        a.months";
                if ($request->get('type') == '') {
                    $audit = DB::SELECT("
                        SELECT
                            sum( b.point_claim ) AS point_claim,
                            COALESCE(sum( b.done_claim_ok ),0) AS done_claim_ok,
                            COALESCE(sum( b.done_claim_ns ),0) AS done_claim_ns,
                            COALESCE(sum( b.done_claim_ng ),0) AS done_claim_ng,
                            sum( b.point_audit ) AS point_audit,
                            sum( b.done_audit ) AS done_audit,
                            b.`month`,
                            b.months 
                        FROM
                        (
                        ".$production."
                        UNION ALL
                        ".$qa."
                            ) b GROUP BY b.`month`,b.months");
                }else if($request->get('type') == 'Production'){
                    $audit = DB::SELECT("
                        SELECT
                            sum( b.point_claim ) AS point_claim,
                            COALESCE(sum( b.done_claim_ok ),0) AS done_claim_ok,
                            COALESCE(sum( b.done_claim_ns ),0) AS done_claim_ns,
                            COALESCE(sum( b.done_claim_ng ),0) AS done_claim_ng,
                            sum( b.point_audit ) AS point_audit,
                            sum( b.done_audit ) AS done_audit,
                            b.`month`,
                            b.months 
                        FROM
                        (
                        ".$production."
                            ) b group by b.`month`,b.months");
                }else if($request->get('type') == 'QA'){
                    $audit = DB::SELECT("
                        SELECT
                            sum( b.point_claim ) AS point_claim,
                            COALESCE(sum( b.done_claim_ok ),0) AS done_claim_ok,
                            COALESCE(sum( b.done_claim_ns ),0) AS done_claim_ns,
                            COALESCE(sum( b.done_claim_ng ),0) AS done_claim_ng,
                            sum( b.point_audit ) AS point_audit,
                            sum( b.done_audit ) AS done_audit,
                            b.`month`,
                            b.months 
                        FROM
                        (
                        ".$qa."
                            ) b group by b.`month`,b.months");
                }
                array_push($audits, $audit);

                for ($k=0; $k < count($titles); $k++) { 
                    $qa_resume = "SELECT
                        '".$titles[$k]->audit_title."' AS audit_title,
                        count(
                        DISTINCT ( audit_external_claim_points.id )) point_audit,
                        (SELECT
                                    COUNT(
                                    DISTINCT ( audit_external_claims.id )) AS done_audit 
                                FROM
                                    audit_external_claims
                                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                                WHERE
                                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."'
                                and audit_external_claim_points.audit_title = '".$titles[$k]->audit_title."'  and audit_external_claims.remark = 'ng_jelas'  ) as done_audit,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%Y-%m' ) AS `month`,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                    FROM
                        audit_external_claim_schedules
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                    WHERE
                        audit_external_claim_points.audit_title = '".$titles[$k]->audit_title."'
                        and audit_external_claim_schedules.remark = 'ng_jelas'
                        group by `month`,months,audit_title";

                    $qa_resume_claim = "SELECT
                        '".$titles[$k]->audit_title."' AS audit_title,
                        count(
                        DISTINCT ( audit_external_claim_points.audit_id )) point_audit,
                        (SELECT
                                    COUNT(
                                    DISTINCT ( audit_external_claims.audit_id )) AS done_audit 
                                FROM
                                    audit_external_claims
                                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                                WHERE
                                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$month[$i]->months."'
                                and audit_external_claim_points.audit_title = '".$titles[$k]->audit_title."' and  audit_external_claims.remark = 'ng_jelas' ) as done_audit,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%Y-%m' ) AS `month`,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                    FROM
                        audit_external_claim_schedules
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$month[$i]->months."' 
                    WHERE
                        audit_external_claim_points.audit_title = '".$titles[$k]->audit_title."'
                        and audit_external_claim_schedules.remark = 'ng_jelas'
                        group by `month`,months,audit_title";

                    $production_resume = "SELECT
                        '".$titles[$k]->audit_title."' as audit_title,
                        IF
                        (
                            '".$month[$i]->months."' <= DATE_FORMAT( '2021-08-01', '%Y-%m' ) && audit_title = 'YFL-212 C-2 Key Tanpa Neji',
                            count(
                            DISTINCT ( point_check_audits.id ))- 3,
                        IF
                            (
                                '".$month[$i]->months."' <= DATE_FORMAT( '2021-08-01', '%Y-%m' ) && audit_title = 'YFL-472H Dalam 1 Outerbox terdapat 1 pc YFL-462H',
                                COUNT(
                                DISTINCT ( point_check_audits.id ))- 6,
                                COUNT(
                                DISTINCT ( point_check_audits.id )))) point_audit,
                        (
                        SELECT
                            count(
                            DISTINCT ( point_check_audit_id )) AS done_audit 
                        FROM
                            activity_lists
                            JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id
                            LEFT JOIN production_audits ON production_audits.point_check_audit_id = point_check_audits.id 
                        WHERE
                            activity_lists.activity_type = 'Audit' 
                            AND audit_title IS NOT NULL 
                            AND DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$month[$i]->months."' 
                            AND audit_title = '".$titles[$k]->audit_title."' 
                        ) AS done_audit,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%Y-%m' ) AS `month`,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                    FROM
                        activity_lists
                        JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id 
                    WHERE
                        activity_lists.activity_type = 'Audit' 
                        AND audit_title IS NOT NULL 
                        AND point_check_audits.audit_title = '".$titles[$k]->audit_title."'
                        group by `month`,months,audit_title";

                    $production_resume_claim = "SELECT
                        '".$titles[$k]->audit_title."' as audit_title,
                        IF
                        (
                            '".$month[$i]->months."' <= DATE_FORMAT( '2021-08-01', '%Y-%m' ) && audit_title = 'YFL-212 C-2 Key Tanpa Neji',
                            count(
                            DISTINCT ( leader ))- 1,
                        IF
                            (
                                '".$month[$i]->months."' <= DATE_FORMAT( '2021-08-01', '%Y-%m' ) && audit_title = 'YFL-472H Dalam 1 Outerbox terdapat 1 pc YFL-462H',
                                COUNT(
                                DISTINCT ( leader ))- 1,
                                COUNT(
                                DISTINCT ( leader )))) point_audit,
                        (
                        SELECT
                            count(
                            DISTINCT ( leader )) AS done_audit 
                        FROM
                            activity_lists
                            JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id
                            LEFT JOIN production_audits ON production_audits.point_check_audit_id = point_check_audits.id 
                        WHERE
                            activity_lists.activity_type = 'Audit' 
                            AND audit_title IS NOT NULL 
                            AND DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$month[$i]->months."' 
                            AND audit_title = '".$titles[$k]->audit_title."' 
                        ) AS done_audit,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%Y-%m' ) AS `month`,
                        DATE_FORMAT( '".$month[$i]->months."-01', '%M %Y' ) AS `months` 
                    FROM
                        activity_lists
                        JOIN point_check_audits ON point_check_audits.activity_list_id = activity_lists.id 
                    WHERE
                        activity_lists.activity_type = 'Audit' 
                        AND audit_title IS NOT NULL 
                        AND point_check_audits.audit_title = '".$titles[$k]->audit_title."'
                        group by `month`,months,audit_title";

                    if ($request->get('type') == '') {
                        $resume = DB::SELECT("
                                SELECT
                                    DISTINCT
                                    ( b.audit_title ),
                                    sum( b.point_audit ) AS point_audit,
                                    sum( b.done_audit ) AS done_audit,
                                    b.`month`,
                                    b.months 
                                FROM
                                (
                                ".$production_resume."
                                UNION ALL
                                ".$qa_resume."
                                    ) b group by b.`month`,b.months,b.audit_title");

                        $resume_claim = DB::SELECT("
                                SELECT
                                    DISTINCT
                                    ( b.audit_title ),
                                    sum( b.point_audit ) AS point_audit,
                                    sum( b.done_audit ) AS done_audit,
                                    b.`month`,
                                    b.months 
                                FROM
                                (
                                ".$production_resume_claim."
                                UNION ALL
                                ".$qa_resume_claim."
                                    ) b group by b.`month`,b.months,b.audit_title");
                    }else if($request->get('type') == 'Production'){
                        $resume = DB::SELECT("
                            SELECT
                                DISTINCT
                                ( b.audit_title ),
                                sum( b.point_audit ) AS point_audit,
                                sum( b.done_audit ) AS done_audit,
                                b.`month`,
                                b.months 
                            FROM
                            (
                            ".$production_resume."
                                ) b group by b.`month`,b.months,b.audit_title");

                        $resume_claim = DB::SELECT("
                            SELECT
                                DISTINCT
                                ( b.audit_title ),
                                sum( b.point_audit ) AS point_audit,
                                sum( b.done_audit ) AS done_audit,
                                b.`month`,
                                b.months 
                            FROM
                            (
                            ".$production_resume_claim."
                                ) b group by b.`month`,b.months,b.audit_title");
                    }else if($request->get('type') == 'QA'){
                        $resume = DB::SELECT("
                            SELECT
                                DISTINCT
                                ( b.audit_title ),
                                sum( b.point_audit ) AS point_audit,
                                sum( b.done_audit ) AS done_audit,
                                b.`month`,
                                b.months 
                            FROM
                            (
                            ".$qa_resume."
                                ) b group by b.`month`,b.months,b.audit_title");

                        $resume_claim = DB::SELECT("
                            SELECT
                                DISTINCT
                                ( b.audit_title ),
                                sum( b.point_audit ) AS point_audit,
                                sum( b.done_audit ) AS done_audit,
                                b.`month`,
                                b.months 
                            FROM
                            (
                            ".$qa_resume_claim."
                                ) b group by b.`month`,b.months,b.audit_title");
                    }

                    if (count($resume == 0)) {
                        array_push($resume, [
                            'audit_title' => $titles[$k]->audit_title,
                            'point_audit' => 0,
                            'done_audit' => 0,
                            'month' => $month[$i]->months,
                            'months' => $month[$i]->month_name,
                        ]);
                    }

                    if (count($resume_claim == 0)) {
                        array_push($resume_claim, [
                            'audit_title' => $titles[$k]->audit_title,
                            'point_audit' => 0,
                            'done_audit' => 0,
                            'month' => $month[$i]->months,
                            'months' => $month[$i]->month_name,
                        ]);
                    }

                    array_push($resumes, $resume);
                    array_push($resumes_claim, $resume_claim);
                }
            }

            $awal_fy = current($month)->months;
            $akhir_fy = end($month)->months;

            $schedules = DB::SELECT("SELECT DISTINCT
                ( audit_external_claim_schedules.audit_id ),
                audit_external_claim_points.audit_title,
                DATE_FORMAT( schedule_date, '%Y-%m' ) AS `month`,
                '1' AS plan,
                employee_syncs.employee_id,
                CONCAT(
                    SPLIT_STRING ( employee_syncs.`name`, ' ', 1 ),
                    ' ',
                SPLIT_STRING ( employee_syncs.`name`, ' ', 2 )) AS `name` 
            FROM
                audit_external_claim_schedules
                JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id
                JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
            WHERE
                DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$awal_fy."' 
                AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$akhir_fy."' 
                and audit_external_claim_schedules.remark = 'ng_jelas'
            ORDER BY
                `month`");

            // $on_schedule = DB::SELECT("SELECT DISTINCT
            //         (
            //         DATE_FORMAT( schedule_date, '%Y-%m' )) AS `month`,
            //         audit_title,
            //         audit_external_claims.audit_id,
            //         audit_external_claims.schedule_id,
            //     IF
            //         (
            //             GROUP_CONCAT( result_check ) LIKE '%NG%',
            //             'NG',
            //         IF
            //         ( GROUP_CONCAT( result_check ) LIKE '%NS%', 'NS', 'OK' )) AS `condition`,
            //         GROUP_CONCAT( audit_external_claims.handling ) AS handling 
            //     FROM
            //         audit_external_claims
            //         JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
            //     WHERE
            //         DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$awal_fy."' 
            //         AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$akhir_fy."' 
            //     GROUP BY
            //         DATE_FORMAT( schedule_date, '%Y-%m' ),
            //         audit_title,
            //         audit_external_claims.audit_id,
            //         audit_external_claims.schedule_id");

            $on_schedule = DB::SELECT("SELECT
                a.audit_code,
                a.audit_title,
                a.audit_id,
                a.schedule_id,
                a.`month`,
                GROUP_CONCAT(a.`condition`) AS `condition`,
                GROUP_CONCAT(a.handling) AS handling
            FROM
                (
                SELECT DISTINCT
                    (
                    CONCAT( DATE_FORMAT( schedule_date, '%Y-%m' ), audit_external_claims.audit_id )) AS audit_code,
                    DATE_FORMAT( schedule_date, '%Y-%m' ) AS `month`,
                    audit_title,
                    audit_external_claims.audit_id,
                    audit_external_claims.schedule_id,
                IF
                    (
                        GROUP_CONCAT( result_check ) LIKE '%NG%',
                        'NG',
                    IF
                    ( GROUP_CONCAT( result_check ) LIKE '%NS%', 'NS', 'OK' )) AS `condition`,
                    GROUP_CONCAT( audit_external_claims.handling ) AS handling 
                FROM
                    audit_external_claims
                    JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                WHERE
                    DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$awal_fy."' 
                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$akhir_fy."' 
                    and audit_external_claims.remark = 'ng_jelas'
                GROUP BY
                    DATE_FORMAT( schedule_date, '%Y-%m' ),
                    audit_title,
                    audit_external_claims.audit_id,
                    audit_external_claims.schedule_id,
                    audit_external_claim_schedules.schedule_date UNION ALL
                SELECT DISTINCT
                    (
                    CONCAT( DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ), audit_external_claim_schedules.audit_id )),
                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) AS `month`,
                    audit_external_claim_points.audit_title,
                    audit_external_claim_schedules.audit_id,
                    audit_external_claim_schedules.id AS schedule_id,
                    NULL AS `condition`,
                    NULL AS handling 
                FROM
                    audit_external_claim_schedules
                    JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                WHERE
                    DATE_FORMAT( schedule_date, '%Y-%m' ) >= '".$awal_fy."' 
                    AND DATE_FORMAT( schedule_date, '%Y-%m' ) <= '".$akhir_fy."' 
                    and audit_external_claim_schedules.remark = 'ng_jelas'
                ) a 
            GROUP BY
                a.audit_code,
                a.audit_title,
                a.audit_id,
                a.schedule_id,
                a.`month`
            ORDER BY a.`month`,a.audit_id");

            if ($request->get('fiscal_year') != "") {
                $temuan = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`,
                    fiscal_year,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NS' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS ns,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NG' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS ng,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NG' THEN 1 ELSE 0 END )+ sum( CASE WHEN result_check = 'NS' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            AND audit_external_claims.handling IS NOT NULL 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS handling 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = '".$request->get('fiscal_year')."' 
                ORDER BY
                    week_date");
            }else{
                $temuan = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS months,
                    DATE_FORMAT( week_date, '%b %Y' ) AS `month_name`,
                    fiscal_year,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NS' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS ns,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NG' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS ng,
                    COALESCE ((
                        SELECT
                            sum( CASE WHEN result_check = 'NG' THEN 1 ELSE 0 END )+ sum( CASE WHEN result_check = 'NS' THEN 1 ELSE 0 END ) AS ns 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = audit_external_claims.schedule_id 
                        WHERE
                            DATE_FORMAT( schedule_date, '%Y-%m' ) = DATE_FORMAT( week_date, '%Y-%m' ) 
                            AND audit_external_claims.handling IS NOT NULL 
                            and audit_external_claims.remark = 'ng_jelas'
                        GROUP BY
                            schedule_date 
                            ),
                        0 
                    ) AS handling 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = (
                    SELECT
                        fiscal_year 
                    FROM
                        weekly_calendars 
                    WHERE
                        week_date = DATE(
                        NOW())) 
                ORDER BY
                    week_date");
            }

            $response = array(
                'status' => true,
                'audits' => $audits,
                'resumes' => $resumes,
                'resumes_claim' => $resumes_claim,
                'schedules' => $schedules,
                'on_schedule' => $on_schedule,
                'titles' => $titles,
                'temuan' => $temuan,
                'fiscalTitle' => $month[0]->fiscal_year,
                'month' => $month,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' =>$e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchDetailNgJelasMonitoring(Request $request){
        if($request->get('week_date') != Null){
            $leader_name = $request->get('leader_name');
            $dept_id = $request->get('dept_id');
            $week_date = $request->get('week_date');
        }
        else{
            $leader_name = $request->get('leader_name');
            $dept_id = $request->get('dept_id');
            $week_date = date('Y-m');
        }
        $detail[] = null;
        $date = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");
        $date2 = db::select("select DISTINCT(week_name) as week_name from weekly_calendars where DATE_FORMAT(weekly_calendars.week_date,'%Y-%m') = '".$week_date."'");

        foreach($date2 as $date2){
            $detail[] = db::select("SELECT detail.id_activity,
                     detail.activity_name,
                     detail.activity_type,
                     detail.leader_dept,
                     detail.week_name,
                     detail.plan,
                     detail.jumlah_aktual,
                     (detail.jumlah_aktual/detail.plan)*100 as persen
                    from 
                    (select activity_lists.id as id_activity,activity_name, activity_type,leader_dept,
                        4 as plan,
                        '".$date2->week_name."' as week_name,
                        (select count(production_audits.week_name) as jumlah_audit
                            from production_audits
                                join activity_lists as actlist on actlist.id = activity_list_id
                                where DATE_FORMAT(production_audits.date,'%Y-%m') = '".$week_date."'
                                            and leader_dept = '".$leader_name."'
                                            and actlist.department_id = '".$dept_id."'
                                            and week_name = '".$date2->week_name."'
                                            and actlist.id = id_activity
                                and actlist.frequency = 'Weekly')
                        as jumlah_aktual
                            from activity_lists
                                where leader_dept = '".$leader_name."'
                                and frequency = 'Weekly'
                                and department_id = '".$dept_id."'
                                and activity_name != 'Null'
                                and activity_type = 'Audit'
                                GROUP BY activity_type, plan_item,id,activity_name,leader_dept) detail");
        }
        $monthTitle = date("F Y", strtotime($week_date));

        $response = array(
            'status' => true,
            'detail' => $detail,
            'date' => $date,
            'leader_name' => $leader_name,
            'week_date' => $week_date,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailNgJelasMonitoringClaim(Request $request){
        $kondisi = $request->get('kondisi');
        $date = $request->get('date');
        $audit_type = $request->get('audit_type');

        if ($kondisi == 'Belum Dilakukan Audit') {
            if ($audit_type == 'Production') {
                $title = DB::SELECT("SELECT DISTINCT
                    ( audit_title ),
                    'Production' as type,
                    GROUP_CONCAT(DISTINCT(leader)) AS auditor 
                FROM
                    point_check_audits 
                WHERE
                    audit_title IS NOT NULL 
                    AND audit_title NOT IN (
                    SELECT DISTINCT
                        ( audit_title ) 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    ORDER BY
                        audit_title 
                    ) 
                GROUP BY
                    audit_title
                ORDER BY
                    audit_title
                ");
            }else if($audit_type == 'QA'){
                $title = DB::SELECT("SELECT DISTINCT
                    ( audit_title ),
                    'QA' AS type,
                    employee_syncs.`name` AS auditor 
                FROM
                    audit_external_claim_schedules
                    JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                    JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
                WHERE
                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$date."' 
                    and audit_external_claim_schedules.remark = 'ng_jelas'
                    AND audit_title NOT IN (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ) 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claims.remark = 'ng_jelas'
                ORDER BY
                    audit_external_claims.audit_title)");
            }else if($audit_type == ''){
                $title = DB::SELECT("SELECT DISTINCT
                    ( audit_title ),
                    'Production' as type,
                    GROUP_CONCAT(DISTINCT(leader)) AS auditor 
                FROM
                    point_check_audits 
                WHERE
                    audit_title IS NOT NULL 
                    AND audit_title NOT IN (
                    SELECT DISTINCT
                        ( audit_title ) 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    ORDER BY
                        audit_title 
                    )
                    GROUP BY audit_title 
                UNION ALL
                SELECT DISTINCT
                    ( audit_title ),
                    'QA' AS type,
                    employee_syncs.`name` AS auditor 
                FROM
                    audit_external_claim_schedules
                    JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claim_schedules.audit_id 
                    JOIN employee_syncs ON employee_syncs.employee_id = audit_external_claim_schedules.employee_id 
                WHERE
                    DATE_FORMAT( audit_external_claim_schedules.schedule_date, '%Y-%m' ) = '".$date."' 
                    and audit_external_claim_schedules.remark = 'ng_jelas'
                    AND audit_title NOT IN (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ) 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claims.remark = 'ng_jelas'
                ORDER BY
                    audit_external_claims.audit_title)");
            }
        }else if($kondisi == 'Sudah Dilakukan Audit (Temuan Tidak Dilakukan)'){
            $details = [];
            if ($audit_type == 'Production') {
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_title ),
                        proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NG'");
            }else if($audit_type == 'QA'){
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%',
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%', 'NS', 'OK' )) AS audit_result 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                    where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NG'");
        }else{
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_title ),
                        proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NG'
                            UNION ALL
                SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%',
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%', 'NS', 'OK' )) AS audit_result 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NG'");
            }
        }else if($kondisi == 'Sudah Dilakukan Audit (Temuan Belum Sempurna)'){
            $details = [];
            if ($audit_type == 'Production') {
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_title ),
                        proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NS'");
            }else if($audit_type == 'QA'){
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%',
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%', 'NS', 'OK' )) AS audit_result 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NS'");
            }else{
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_title ),
                        proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                    FROM
                        production_audits
                        JOIN point_check_audits ON point_check_audit_id = point_check_audits.id 
                    WHERE
                        DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                        AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NS'
                            UNION ALL
                SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%',
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%', 'NS', 'OK' )) AS audit_result 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'NS'");
            }
        }else if($kondisi == 'Sudah Dilakukan Audit (OK)'){
            $details = [];
            if ($audit_type == 'Production') {
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                            ( audit_title ),
                            proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                        FROM
                            production_audits
                            JOIN point_check_audits ON point_check_audit_id = point_check_audits.id
                        WHERE
                            DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                            AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                        ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'OK'");
            }else if($audit_type == 'QA'){
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%' AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL,
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%' AND GROUP_CONCAT( audit_external_claims.handling ) IS NULL, 'NS', 'OK' )) AS audit_result 
                    FROM
                        audit_external_claims
                        JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                        JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                        AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'OK'");
            }else{
                $title = DB::SELECT("SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                    SELECT DISTINCT
                        ( audit_title ),
                        proses,
                        'Production' AS type,
                    IF
                        ( GROUP_CONCAT( production_audits.kondisi ) LIKE '%Not Good%', 'NG', 'OK' ) AS audit_result 
                        FROM
                            production_audits
                            JOIN point_check_audits ON point_check_audit_id = point_check_audits.id
                        WHERE
                            DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                            AND audit_title IS NOT NULL 
                    GROUP BY
                        audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'OK'
                            UNION ALL
                SELECT
                    c.audit_title,
                    c.proses,
                    c.type 
                FROM
                    (
                        SELECT DISTINCT
                            ( audit_external_claims.audit_title ),
                        proses,
                        'QA' AS type,
                    IF
                        (
                            GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NG%',
                            'NG',
                        IF
                        ( GROUP_CONCAT( audit_external_claims.result_check ) LIKE '%NS%', 'NS', 'OK' )) AS audit_result 
                        FROM
                            audit_external_claims
                            JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                            JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                            AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                        AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."' 
                        where audit_external_claim_schedules.remark = 'ng_jelas'
                    GROUP BY
                        audit_external_claims.audit_title,
                        proses,
                        type 
                    ORDER BY
                        audit_external_claims.audit_title 
                    ) c 
                WHERE
                    c.audit_result = 'OK'");
            }
        }

        $monthTitle = date("F Y", strtotime($date));

        $response = array(
            'status' => true,
            'title' => $title,
            'date' => $date,
            'kondisi' => $kondisi,
            'monthTitle' => $monthTitle
        );
        return Response::json($response);

    }

    public function fetchDetailNgJelasMonitoringClaimDetail(Request $request)
    {
        try {
            $audit_title = $request->get('audit_title');
            $date = $request->get('date');
            if ($request->get('audit_type') == 'Production') {
                $details = DB::SELECT("SELECT
                 audit_title,
                 title AS title,
                 'null' as audit_images,
                 proses AS proses,
                 point_check AS point_check,
                 cara_cek AS cara_cek,
                 kondisi AS kondision,
                 foto_kondisi_aktual AS images,
                 auditor AS auditor,
                 '' as note,
                 employee_syncs.`name` AS `name`,
                 DATE_FORMAT(production_audits.date,'%d %M %Y') AS created_at
             FROM
                 production_audits
                 JOIN point_check_audits ON point_check_audit_id = point_check_audits.id
                 JOIN employee_syncs ON employee_syncs.employee_id = auditor 
             WHERE
                 DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
                 AND point_check_audits.audit_title IS NOT NULL
                 AND point_check_audits.audit_title = '".$audit_title."'");
            }else{
                $details = DB::SELECT("SELECT DISTINCT
                     ( audit_external_claims.audit_title ),
                     audit_external_claims.audit_point AS title,
                     audit_external_claims.audit_images,
                     audit_external_claim_points.proses AS proses,
                     audit_external_claims.audit_point AS point_check,
                     audit_external_claims.audit_point AS cara_cek,
                     audit_external_claims.note AS note,
                     audit_external_claims.chief_foreman,
                     audit_external_claims.manager,
                     audit_external_claims.send_status,
                     audit_external_claims.id,
                     auditor AS auditor,
                     result_check AS kondision,
                     result_image AS images,
                     employee_syncs.`name` AS `name` ,
                     DATE_FORMAT(audit_external_claims.`created_at`,'%d %M %Y') AS `created_at`
                 FROM
                     audit_external_claims
                     JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                     JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                     AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                     AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."'
                     JOIN employee_syncs ON employee_syncs.employee_id = auditor 
                     AND audit_external_claim_points.audit_title = '".$audit_title."' 
                     where audit_external_claims.remark = 'ng_jelas'
                 ORDER BY
                     audit_external_claims.audit_index
                     ");
            }
            $response = array(
                'status' => true,
                'details' => $details,
                'audit_title' => $audit_title,
                'date' => $date,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function printPdfAuditNgJelas($audit_title,$date,$type)
    {
        if ($type == 'Production') {
            $details = DB::SELECT("SELECT
             audit_title,
             title AS title,
             'null' as audit_images,
             proses AS proses,
             point_check AS point_check,
             cara_cek AS cara_cek,
             kondisi AS kondision,
             '' as note,
             foto_kondisi_aktual AS images,
             auditor AS auditor,
             '' AS handling,
             employee_syncs.`name` AS `name`,
             DATE_FORMAT(production_audits.date,'%d %M %Y') AS created_at
         FROM
             production_audits
             JOIN point_check_audits ON point_check_audit_id = point_check_audits.id
             JOIN employee_syncs ON employee_syncs.employee_id = auditor 
         WHERE
             DATE_FORMAT( production_audits.date, '%Y-%m' ) = '".$date."' 
             AND point_check_audits.audit_title IS NOT NULL
             AND point_check_audits.audit_title = '".$audit_title."'");
        }else{
            $details = DB::SELECT("SELECT DISTINCT
                 ( audit_external_claims.audit_title ),
                 audit_external_claims.audit_point AS title,
                 audit_external_claims.audit_images,
                 audit_external_claim_points.proses AS proses,
                 audit_external_claims.audit_point AS point_check,
                 audit_external_claims.audit_point AS cara_cek,
                 audit_external_claims.note AS note,
                 auditor AS auditor,
                 result_check AS kondision,
                 result_image AS images,
                 handling,
                 employee_syncs.`name` AS `name` ,
                 DATE_FORMAT(audit_external_claims.`created_at`,'%d %M %Y') AS `created_at`
             FROM
                 audit_external_claims
                 JOIN audit_external_claim_schedules ON audit_external_claim_schedules.id = schedule_id
                 JOIN audit_external_claim_points ON audit_external_claim_points.audit_id = audit_external_claims.audit_id 
                 AND audit_external_claim_points.audit_index = audit_external_claims.audit_index 
                 AND DATE_FORMAT( schedule_date, '%Y-%m' ) = '".$date."'
                 JOIN employee_syncs ON employee_syncs.employee_id = auditor 
                 AND audit_external_claim_points.audit_title = '".$audit_title."' 
                 where audit_external_claims.remark = 'ng_jelas'
             ORDER BY
                 audit_external_claims.audit_index
                 ");
        }
        $monthTitle = date("F Y", strtotime($date));

        // return view('audit.print_pdf_ng_jelas')->with('details',$details)->with('monthTitle',$monthTitle)->with('type',$type);
        // die();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'landscape');

        $pdf->loadView('audit.print_pdf_ng_jelas', array(
            'details' => $details,
            'monthTitle' => $monthTitle,
            'type' => $type
        ));

        return $pdf->stream("Audit NG Jelas - ".$audit_title." (".$monthTitle.").pdf");
    }

    public function indexAuditIKMonitoring()
    {
        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
        $title = 'Audit IK Monitoring';
        $title_jp = '作業手順書監査表示';

        $department_all = DB::SELECT("SELECT DISTINCT
                ( department_id ) as id,
                department_shortname,
                department_name 
            FROM
                audit_guidances
                JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
                JOIN departments ON departments.id = activity_lists.department_id 
            ORDER BY
                department_id");

        $fiscal = DB::SELECT("SELECT DISTINCT
                fiscal_year 
            FROM
                weekly_calendars");

        return view('production_report.audit_ik_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fiscal' => $fiscal,
            'department_all' => $department_all,
            'role_code' => Auth::user()->role_code
        ))->with('page', 'Audit IK Monitoring');
    }

    public function fetchAuditIKMonitoring(Request $request)
    {
        try {
            // $month_from = $request->get('month_from');
            // $month_to = $request->get('month_to');
            if ($request->get('fiscal_year') != "") {
                $month = DB::SELECT('( SELECT DISTINCT
                (
                DATE_FORMAT( week_date, "%Y-%m" )) AS count_month,
                fiscal_year
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = "'.$request->get('fiscal_year').'" 
                    AND DATE_FORMAT( week_date, "%Y-%m" ) <= DATE_FORMAT( DATE( NOW()), "%Y-%m" ) 
                ORDER BY
                    DATE_FORMAT( week_date, "%Y-%m" ) ASC 
                    LIMIT 1 
                ) UNION ALL
                (
                SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, "%Y-%m" )) AS count_month,
                    fiscal_year
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = "'.$request->get('fiscal_year').'" 
                    AND DATE_FORMAT( week_date, "%Y-%m" ) <= DATE_FORMAT( DATE( NOW()), "%Y-%m" ) 
                ORDER BY
                DATE_FORMAT( week_date, "%Y-%m" ) DESC 
                LIMIT 1)');
            }else{
                $month = DB::SELECT('( SELECT DISTINCT
                (
                DATE_FORMAT( week_date, "%Y-%m" )) AS count_month,
                fiscal_year 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = (
                    SELECT
                        fiscal_year 
                    FROM
                        weekly_calendars 
                    WHERE
                        week_date = DATE(
                        NOW())) 
                    AND DATE_FORMAT( week_date, "%Y-%m" ) <= DATE_FORMAT( DATE( NOW()), "%Y-%m" ) 
                ORDER BY
                    DATE_FORMAT( week_date, "%Y-%m" ) ASC 
                    LIMIT 1 
                ) UNION ALL
                (
                SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, "%Y-%m" )) AS count_month,
                    fiscal_year
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = (
                    SELECT
                        fiscal_year 
                    FROM
                        weekly_calendars 
                    WHERE
                        week_date = DATE(
                        NOW())) 
                    
                ORDER BY
                DATE_FORMAT( week_date, "%Y-%m" ) DESC 
                LIMIT 1)');
            }

            $first = $month[0]->count_month; 
            $last = $month[1]->count_month;
            $fiscalTitle = $month[0]->fiscal_year;
            $month_name = DB::SELECT("SELECT DISTINCT
                (
                DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                DATE_FORMAT( week_date, '%b %Y' ) AS month_name 
            FROM
                weekly_calendars 
            WHERE
                DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) BETWEEN '".$first."' 
                AND '".$last."'
            ORDER BY
                `month`");

            $department_id = "";
            if ($request->get('department') != "") {
                $department_id = 'AND a.department_id = '.$request->get('department').'';
            }else {
                $department_id = "";
            }

            $audit_ik = DB::SELECT("
                SELECT
                    a.department_id,
                    a.department_shortname,
                    a.department_name,
                    a.`month`,
                    sum( a.sudah ) AS sudah,
                    sum( a.belum ) AS belum,
                    sum( a.training_ulang_ik ) AS training_ulang_ik,
                    sum( a.revisi_ik ) AS revisi_ik,
                    sum( a.revisi_qc ) AS revisi_qc,
                    sum( a.revisi_jig ) AS revisi_jig,
                    sum( a.ik_obsolete ) AS ik_obsolete,
                    sum( a.training_ulang_ik_sudah ) AS training_ulang_ik_sudah,
                    sum( a.revisi_ik_sudah ) AS revisi_ik_sudah,
                    sum( a.revisi_qc_sudah ) AS revisi_qc_sudah,
                    sum( a.revisi_jig_sudah ) AS revisi_jig_sudah,
                    sum( a.ik_obsolete_sudah ) AS ik_obsolete_sudah 
                FROM
                    (
                    SELECT DISTINCT
                        ( activity_lists.department_id ),
                        department_shortname,
                        department_name,
                        count( audit_guidances.id ) AS sudah,
                        0 AS belum,
                        sum( CASE WHEN audit.handling = 'Training Ulang IK' THEN 1 ELSE 0 END ) AS training_ulang_ik,
                        sum( CASE WHEN audit.handling = 'Revisi IK' THEN 1 ELSE 0 END ) AS revisi_ik,
                        IF(`month` > '2022-06',sum( CASE WHEN audit.handling = 'Revisi QC Kouteihyo' THEN 1 ELSE 0 END ),0) AS revisi_qc,
                        sum( CASE WHEN audit.handling = 'Pembuatan Jig / Repair Jig' THEN 1 ELSE 0 END ) AS revisi_jig,
                        sum( CASE WHEN audit.handling = 'IK Tidak Digunakan' THEN 1 ELSE 0 END ) AS ik_obsolete,
                        sum( CASE WHEN audit.handling = 'Training Ulang IK' AND audit.handling_status IS NOT NULL THEN 1 ELSE 0 END ) AS training_ulang_ik_sudah,
                        sum( CASE WHEN audit.handling = 'Revisi IK' AND audit.handling_status IS NOT NULL THEN 1 ELSE 0 END ) AS revisi_ik_sudah,
                        sum( CASE WHEN audit.handling = 'Revisi QC Kouteihyo' AND audit.handling_status IS NOT NULL THEN 1 ELSE 0 END ) AS revisi_qc_sudah,
                        sum( CASE WHEN audit.handling = 'Pembuatan Jig / Repair Jig' AND audit.handling_status IS NOT NULL THEN 1 ELSE 0 END ) AS revisi_jig_sudah,
                        sum( CASE WHEN audit.handling = 'IK Tidak Digunakan' AND audit.handling_status IS NOT NULL THEN 1 ELSE 0 END ) AS ik_obsolete_sudah,
                        `month` 
                    FROM
                        audit_guidances
                        JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
                        JOIN departments ON departments.id = activity_lists.department_id
                        LEFT JOIN (
                        SELECT DISTINCT
                            ( audit_guidance_id ),
                            handling,
                            handling_status 
                        FROM
                            audit_report_activities
                            JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id 
                        WHERE
                            audit_report_activities.deleted_at IS NULL 
                            AND audit_guidances.`month` >= '".$first."' 
                            AND audit_guidances.`month` <= '".$last."' 
                        ) AS audit ON audit.audit_guidance_id = audit_guidances.id 
                    WHERE
                        audit_guidances.`status` = 'Sudah Dikerjakan' 
                        AND audit_guidances.deleted_at IS NULL 
                    GROUP BY
                        activity_lists.department_id,
                        department_shortname,
                        department_name,
                        `month` UNION ALL
                    SELECT DISTINCT
                        ( activity_lists.department_id ),
                        department_shortname,
                        department_name,
                        0 AS sudah,
                        count( audit_guidances.id ) AS belum,
                        0 AS training_ulang_ik,
                        0 AS revisi_ik,
                        0 AS revisi_qc,
                        0 AS revisi_jig,
                        0 AS ik_obsolete,
                        0 AS training_ulang_ik_sudah,
                        0 AS revisi_ik_sudah,
                        0 AS revisi_qc_sudah,
                        0 AS revisi_jig_sudah,
                        0 AS ik_obsolete_sudah,
                        `month` 
                    FROM
                        audit_guidances
                        JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
                        JOIN departments ON departments.id = activity_lists.department_id 
                    WHERE
                        audit_guidances.`status` = 'Belum Dikerjakan' 
                        AND audit_guidances.deleted_at IS NULL 
                    GROUP BY
                        activity_lists.department_id,
                        department_shortname,
                        department_name,
                        `month` 
                    ) a 
                WHERE
                    a.`month` >= '".$first."' 
                    AND a.`month` <= '".$last."' 
                    ".$department_id."
                GROUP BY
                    a.department_id,
                    a.department_shortname,
                    a.department_name,
                    a.`month`
                ");

            $audit_ik_all = DB::SELECT("
                SELECT
                    a.department_id,
                    a.department_shortname,
                    a.department_name,
                    a.`month`,
                    sum( a.sudah ) AS sudah,
                    sum( a.belum ) AS belum,
                    sum( a.training_ulang_ik ) AS training_ulang_ik,
                    sum( a.revisi_ik ) AS revisi_ik,
                    sum( a.revisi_qc ) AS revisi_qc,
                    sum( a.revisi_jig ) AS revisi_jig,
                    sum( a.ik_obsolete ) AS ik_obsolete 
                FROM
                    (
                    SELECT DISTINCT
                        ( activity_lists.department_id ),
                        department_shortname,
                        department_name,
                        count( audit_guidances.id ) AS sudah,
                        0 AS belum,
                        sum( CASE WHEN audit.handling = 'Training Ulang IK' THEN 1 ELSE 0 END ) AS training_ulang_ik,
                        sum( CASE WHEN audit.handling = 'Revisi IK' THEN 1 ELSE 0 END ) AS revisi_ik,
                        if(`month` > '2022-06',sum( CASE WHEN audit.handling = 'Revisi QC Kouteihyo' THEN 1 ELSE 0 END ),0) AS revisi_qc,
                        sum( CASE WHEN audit.handling = 'Pembuatan Jig / Repair Jig' THEN 1 ELSE 0 END ) AS revisi_jig,
                        sum( CASE WHEN audit.handling = 'IK Tidak Digunakan' THEN 1 ELSE 0 END ) AS ik_obsolete,
                        `month` 
                    FROM
                        audit_guidances
                        JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
                        JOIN departments ON departments.id = activity_lists.department_id
                        LEFT JOIN (
                        SELECT DISTINCT
                            ( audit_guidance_id ),
                            handling 
                        FROM
                            audit_report_activities
                            JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id 
                        WHERE
                            audit_report_activities.deleted_at IS NULL 
                            AND audit_guidances.`month` >= '".$first."' 
                            AND audit_guidances.`month` <= '".$last."' 
                        ) AS audit ON audit.audit_guidance_id = audit_guidances.id 
                    WHERE
                        audit_guidances.`status` = 'Sudah Dikerjakan' 
                        AND audit_guidances.deleted_at IS NULL 
                    GROUP BY
                        activity_lists.department_id,
                        department_shortname,
                        department_name,
                        `month` UNION ALL
                    SELECT DISTINCT
                        ( activity_lists.department_id ),
                        department_shortname,
                        department_name,
                        0 AS sudah,
                        count( audit_guidances.id ) AS belum,
                        0 AS training_ulang_ik,
                        0 AS revisi_ik,
                        0 AS revisi_qc,
                        0 AS revisi_jig,
                        0 AS ik_obsolete,
                        `month` 
                    FROM
                        audit_guidances
                        JOIN activity_lists ON activity_lists.id = audit_guidances.activity_list_id
                        JOIN departments ON departments.id = activity_lists.department_id 
                    WHERE
                        audit_guidances.`status` = 'Belum Dikerjakan' 
                        AND audit_guidances.deleted_at IS NULL 
                    GROUP BY
                        activity_lists.department_id,
                        department_shortname,
                        department_name,
                        `month` 
                    ) a 
                WHERE
                    a.`month` >= '".$first."' 
                    AND a.`month` <= '".$last."' 
                GROUP BY
                    a.department_id,
                    a.department_shortname,
                    a.department_name,
                    a.`month`
                ");

            $response = array(
                'status' => true,
                'audit_ik' => $audit_ik,
                'audit_ik_all' => $audit_ik_all,
                'fiscalTitle' => $fiscalTitle,
                'month_name' => $month_name,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchDetailAuditIKMonitoring(Request $request)
    {
        try {
            if ($request->get('month') != "") {
                $month = $request->get('month');
            }else{
                $month = date('Y-m');
            }
            $kondisi = $request->get('kondisi');

            $department_id = "";
            if ($request->get('department') != "") {
                $department_id = 'AND department_id = "'.$request->get('department').'"';
            }else if($request->get('department') == ""){
                $department_id = "";
            }

            $datas = DB::SELECT("
                SELECT
                    audit_guidances.*,
                    audit.*,
                    activity_lists.department_id,
                    activity_lists.auditor_effectivity_id,
                    activity_lists.auditor_effectivity_name,
                    departments.department_name,
                    departments.department_shortname
                FROM
                    audit_guidances
                    LEFT JOIN (
                    SELECT DISTINCT
                        ( audit_guidance_id ),
                        department,
                        section,
                        subsection,
                        audit_report_activities.date AS date_audit,
                        audit_report_activities.kesesuaian_aktual_proses,
                        audit_report_activities.kesesuaian_qc_kouteihyo,
                        audit_report_activities.kelengkapan_point_safety,
                        audit_report_activities.tindakan_perbaikan,
                        audit_report_activities.target,
                        audit_report_activities.operator,
                        audit_report_activities.handling,
                        audit_report_activities.handling_status,
                        audit_report_activities.handled_id,
                        audit_report_activities.handled_name,
                        audit_report_activities.handled_at,
                        audit_report_activities.handling_result,
                        audit_report_activities.`condition`,
                        audit_report_activities.`handling_evidence`,
                        audit_report_activities.`result_qc_koteihyo`,
                        audit_report_activities.qa_auditor_id,
                        audit_report_activities.qa_auditor_name,
                        audit_report_activities.qa_audited_at,
                        audit_report_activities.qa_audit_result,
                        audit_report_activities.`qa_audit_evidence`,
                        audit_report_activities.`qa_verification`,
                        audit_report_activities.`qa_verification_reason`,
                        audit_report_activities.audit_effectivity,
                        audit_report_activities.audit_effectivity_note,
                        audit_report_activities.audit_effectivity_at,
                        audit_report_activities.id as id_audit
                    FROM
                        audit_report_activities
                        JOIN audit_guidances ON audit_guidances.id = audit_report_activities.audit_guidance_id 
                    WHERE
                        audit_report_activities.deleted_at IS NULL 
                        AND audit_guidances.`month` = '".$month."' 
                        AND audit_guidances.deleted_at IS NULL 
                    ) AS audit ON audit.audit_guidance_id = audit_guidances.id
                    LEFT JOIN activity_lists ON audit_guidances.activity_list_id = activity_lists.id
                    LEFT JOIN departments ON departments.id = activity_lists.department_id 
                WHERE
                    audit_guidances.`month` = '".$month."' 
                    ".$department_id."
                    AND audit_guidances.deleted_at IS NULL
            ");

            $monthTitle = date("F Y", strtotime($month));


            $response = array(
                'status' => true,
                'datas' => $datas,
                'month' => $month,
                'monthTitle' => $monthTitle,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexAuditIkHandling($id)
    {
      $emp_id = strtoupper(Auth::user()->username);
      $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/".$emp_id);
      $audit = DB::table('audit_report_activities')->select('audit_report_activities.*','department_shortname','activity_lists.auditor_effectivity_id','activity_lists.auditor_effectivity_name')->leftjoin('departments','departments.department_name','audit_report_activities.department')->join('activity_lists','activity_list_id','activity_lists.id')->where('audit_report_activities.id',$id)->first();
      $emp = EmployeeSync::where('employee_id',$emp_id)->first();
      return view('production_report.handling')
      ->with('title', 'Penanganan Audit IK')
      ->with('title_jp', '品証 工程監査')
      ->with('page', 'Quality Assurance')
      ->with('audit',$audit)
      ->with('emp',$emp)
      ->with('count_audit',count($audit))
      ->with('role',Auth::user()->role_code)
      ->with('employee_id',Auth::user()->username)
      ->with('jpn', '品保');
    }

    public function inputAuditIkHandling(Request $request)
    {
        try {
            $id = $request->get('id');
            $handling_result = $request->get('handling_result');

            $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

            $filename = "";
            $file_destination = 'data_file/qa/audit_ik/handling';

            if (count($request->file('file')) > 0) {
              $file = $request->file('file');
              $filename = 'handling_'.$id.'_'.date('YmdHisa').'.'.$request->input('extension');
              $file->move($file_destination, $filename);
            }

            $update = DB::table('audit_report_activities')->where('id',$id)->update([
                'handling_status' => 'Close',
                'handled_id' => $emp->employee_id,
                'handled_name' => $emp->name,
                'handled_at' => date('Y-m-d H:i:s'),
                'handling_result' => $handling_result,
                'handling_evidence' => $filename,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function inputAuditIkAuditQA(Request $request)
    {
        try {
            $id = $request->get('id');
            $qa_audit_result = $request->get('qa_audit_result');

            $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

            $filename = "";
            $file_destination = 'data_file/qa/audit_ik/audit_qa';

            if (count($request->file('file')) > 0) {
              $file = $request->file('file');
              $filename = 'audit_qa_'.$id.'_'.date('YmdHisa').'.'.$request->input('extension');
              $file->move($file_destination, $filename);
            }

            $update = DB::table('audit_report_activities')->where('id',$id)->update([
                'qa_auditor_id' => $emp->employee_id,
                'qa_auditor_name' => $emp->name,
                'qa_audited_at' => date('Y-m-d H:i:s'),
                'qa_audit_result' => $qa_audit_result,
                'qa_audit_evidence' => $filename,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchDetailNgJelasTemuan(Request $request)
    {
       try {
            $month = $request->get('month');
            $kondisi = $request->get('kondisi');
            $audit_type = $request->get('audit_type');

            if ($kondisi == 'Temuan Belum Sempurna') {
                $temuan = AuditExternalClaim::select('audit_external_claims.*','departments.department_shortname')->where(DB::RAW('DATE_FORMAT(schedule_date,"%Y-%m")'),$month)->where('result_check','=','NS')->join('departments','department','department_name')->join('audit_external_claim_schedules','audit_external_claim_schedules.id','audit_external_claims.schedule_id')->where('audit_external_claims.remark','ng_jelas')->get();
            }
            if ($kondisi == 'Temuan Tidak Dilakukan') {
                $temuan = AuditExternalClaim::select('audit_external_claims.*','departments.department_shortname')->where(DB::RAW('DATE_FORMAT(schedule_date,"%Y-%m")'),$month)->where('result_check','=','NG')->join('departments','department','department_name')->join('audit_external_claim_schedules','audit_external_claim_schedules.id','audit_external_claims.schedule_id')->where('audit_external_claims.remark','ng_jelas')->get();
            }
            if ($kondisi == 'Temuan Sudah Ditangani') {
                $temuan = AuditExternalClaim::select('audit_external_claims.*','departments.department_shortname')->where(DB::RAW('DATE_FORMAT(schedule_date,"%Y-%m")'),$month)->where('result_check','!=','OK')->where('handling','!=',null)->join('departments','department','department_name')->join('audit_external_claim_schedules','audit_external_claim_schedules.id','audit_external_claims.schedule_id')->where('audit_external_claims.remark','ng_jelas')->get();
            }

            $monthTitle = date("F Y", strtotime($month));

            $response = array(
                'status' => true,
                'temuan' => $temuan,
                'month' => $month,
                'monthTitle' => $monthTitle,
            );
            return Response::json($response);
       } catch (\Exception $e) {
           $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
       }
    }

    public function indexDailyCheckMesin()
    {
        $title = 'Daily Check Mesin Monitoring';
        $title_jp = '??';

        $department_all = DB::SELECT("SELECT DISTINCT
                ( department_id ) as id,
                department_name,
                department_shortname 
            FROM
                activity_lists
                JOIN departments ON departments.id = department_id 
            WHERE
                activity_type = 'Jishu Hozen' 
            ORDER BY
                department_id");

        $fiscal = DB::SELECT("SELECT DISTINCT
                fiscal_year 
            FROM
                weekly_calendars");

        return view('production_report.daily_check_mesin', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'fiscal' => $fiscal,
            'department_all' => $department_all,
        ))->with('page', 'Daily Check Mesin');
    }

    public function fetchDailyCheckMesin(Request $request)
    {
        try {
            
            if ($request->get('fiscal_year') != "") {
                $fiscal_year = $request->get('fiscal_year');
            }else{
                $fy = WeeklyCalendar::where('week_date',date('Y-m-d'))->first();
                $fiscal_year = $fy->fiscal_year;
            }

            $departmentwhere = '';
            if ($request->get('department') != null) {
                $departmentwhere = "AND department_id = '".$request->get('department')."'";
            }
            $dailycheck = DB::SELECT("SELECT DISTINCT
                (
                DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                DATE_FORMAT( week_date, '%b %Y' ) AS month_name,
                (
                SELECT
                    count(
                    DISTINCT ( jishu_hozen_points.id )) 
                FROM
                    activity_lists
                    LEFT JOIN jishu_hozen_points ON jishu_hozen_points.activity_list_id = activity_lists.id 
                WHERE
                    activity_type = 'Jishu Hozen' 
                    AND jishu_hozen_points.deleted_at IS NULL 
                    ".$departmentwhere."
                ) AS point,
                (
                SELECT
                    count(
                    DISTINCT ( jishu_hozens.jishu_hozen_point_id )) AS actual  
                FROM
                    jishu_hozens
                    JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id
                    JOIN activity_lists ON activity_lists.id = jishu_hozens.activity_list_id 
                WHERE
                    jishu_hozens.deleted_at IS NULL 
                    AND jishu_hozen_points.deleted_at IS NULL 
                    AND activity_lists.deleted_at IS NULL 
                AND `month` = DATE_FORMAT( week_date, '%Y-%m' )
                ".$departmentwhere.") AS actual 
            FROM
                weekly_calendars 
            WHERE
                fiscal_year = '".$fiscal_year."' 
            ORDER BY
                week_date");

            $department = DB::SELECT("SELECT DISTINCT
                ( department_id ),
                department_name,
                department_shortname 
            FROM
                activity_lists
                JOIN departments ON departments.id = department_id 
            WHERE
                activity_type = 'Jishu Hozen' 
                ".$departmentwhere."
            ORDER BY
                department_id");

            $resumes = [];

            for ($i=0; $i < count($department); $i++) { 
                $resume = DB::SELECT("SELECT DISTINCT
                    (
                    DATE_FORMAT( week_date, '%Y-%m' )) AS `month`,
                    DATE_FORMAT( week_date, '%b %Y' ) AS month_name,
                    (
                    SELECT
                        count(
                        DISTINCT ( jishu_hozen_points.id )) 
                    FROM
                        activity_lists
                        LEFT JOIN jishu_hozen_points ON jishu_hozen_points.activity_list_id = activity_lists.id 
                    WHERE
                        activity_type = 'Jishu Hozen' 
                        AND jishu_hozen_points.deleted_at IS NULL 
                        AND activity_lists.department_id = '".$department[$i]->department_id."' 
                    ) AS point,
                    (
                    SELECT
                        count(
                        DISTINCT ( jishu_hozens.jishu_hozen_point_id )) AS actual 
                    FROM
                        jishu_hozens
                        JOIN jishu_hozen_points ON jishu_hozen_points.id = jishu_hozens.jishu_hozen_point_id
                        JOIN activity_lists ON activity_lists.id = jishu_hozens.activity_list_id 
                    WHERE
                        jishu_hozens.deleted_at IS NULL 
                        AND jishu_hozen_points.deleted_at IS NULL 
                        AND activity_lists.deleted_at IS NULL 
                        AND `month` = DATE_FORMAT( week_date, '%Y-%m' ) 
                        AND department_id = '".$department[$i]->department_id."' 
                    ) AS actual 
                FROM
                    weekly_calendars 
                WHERE
                    fiscal_year = '".$fiscal_year."' 
                ORDER BY
                    week_date");

                array_push($resumes,[
                    'department_id' => $department[$i]->department_id,
                    'department_name' => $department[$i]->department_name,
                    'department_shortname' => $department[$i]->department_shortname,
                    'resume' =>  $resume
                ]);
            }
            $response = array(
                'status' => true,
                'dailycheck' => $dailycheck,
                'resumes' => $resumes,
                'fiscal_year' => $fiscal_year
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function fetchDailyCheckMesinDetail(Request $request)
    {
        try {
            $where = "";
            if ($request->get('department') != null) {
                $where = "AND activity_lists.department_id = ".$request->get('department')." ";
            }else{
                $where = "";
            }

            if ($request->get('statuses') == 'All') {
                $detail = DB::SELECT("SELECT
                    * 
                FROM
                    jishu_hozen_points
                    JOIN activity_lists ON activity_lists.id = jishu_hozen_points.activity_list_id
                    LEFT JOIN (
                    SELECT DISTINCT
                        ( jishu_hozen_point_id ),
                        id,
                        pic,
                        foto_aktual AS aktual,
                        `month`  
                    FROM
                        jishu_hozens 
                    WHERE
                        `month` = '".$request->get('month')."' 
                        AND deleted_at IS NULL 
                    ORDER BY
                        created_at DESC 
                    ) a ON a.jishu_hozen_point_id = jishu_hozen_points.id 
                WHERE
                    activity_lists.deleted_at IS NULL 
                    AND jishu_hozen_points.deleted_at IS NULL 
                    ".$where."
                ORDER BY
                    pic DESC");
            }else if($request->get('statuses') == 'Sudah Dikerjakan'){
                $detail = DB::SELECT("SELECT
                    * 
                FROM
                    jishu_hozen_points
                    JOIN activity_lists ON activity_lists.id = jishu_hozen_points.activity_list_id
                    LEFT JOIN (
                    SELECT DISTINCT
                        ( jishu_hozen_point_id ),
                        id,
                        pic,
                        foto_aktual AS aktual,
                        `month`  
                    FROM
                        jishu_hozens 
                    WHERE
                        `month` = '".$request->get('month')."' 
                        AND deleted_at IS NULL 
                    ORDER BY
                        created_at DESC 
                    ) a ON a.jishu_hozen_point_id = jishu_hozen_points.id 
                WHERE
                    activity_lists.deleted_at IS NULL 
                    AND jishu_hozen_points.deleted_at IS NULL 
                    ".$where."
                    AND a.jishu_hozen_point_id IS NOT NULL 
                ORDER BY
                    pic DESC");
            }else if($request->get('statuses') == 'Belum Dikerjakan'){
                $detail = DB::SELECT("SELECT
                    * 
                FROM
                    jishu_hozen_points
                    JOIN activity_lists ON activity_lists.id = jishu_hozen_points.activity_list_id
                    LEFT JOIN (
                    SELECT DISTINCT
                        ( jishu_hozen_point_id ),
                        id,
                        pic,
                        foto_aktual AS aktual,
                        `month`  
                    FROM
                        jishu_hozens 
                    WHERE
                        `month` = '".$request->get('month')."' 
                        AND deleted_at IS NULL 
                    ORDER BY
                        created_at DESC 
                    ) a ON a.jishu_hozen_point_id = jishu_hozen_points.id 
                WHERE
                    activity_lists.deleted_at IS NULL 
                    AND jishu_hozen_points.deleted_at IS NULL 
                    ".$where."
                    AND a.jishu_hozen_point_id IS NULL 
                ORDER BY
                    pic DESC");
            }
            $response = array(
                'status' => true,
                'detail' => $detail,
                'monthTitle' => date('F Y',strtotime($request->get('month').'-01'))
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexDailyAudit($id,$category){
        if ($category == 'clean-room-lcq') {
            $title = 'AUDIT KEBERSIHAN AREA CLEAN ROOM LACQUERING';
            $title_jp = '??';
            $activity = ActivityList::where('id',$id)->first();
        }
        if ($category == 'compressor') {
            $title = 'AUDIT KEBOCORAN PIPING LINE COMPRESSOR';
            $title_jp = '??';
            $activity = ActivityList::where('id',$id)->first();
        }
        if ($category == 'steam') {
            $title = 'AUDIT KEBOCORAN STEAM LINE';
            $title_jp = '??';
            $activity = ActivityList::where('id',$id)->first();
        }

        return view('processes.middle.audit.index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'category' => $category,
            'id' => $id,
            'activity' => $activity,
        ))->with('page', 'Audit')->with('head','Audit');
    }

    public function fetchDailyAudit(Request $request)
    {
        try {
            if ($request->get('month') != '') {
                $month = $request->get('month');
            }else{
                $month = date('Y-m');
            }
            $months = DB::SELECT("SELECT
                week_date AS date,
                DATE_FORMAT( week_date, '%d' ) AS date_name 
            FROM
                weekly_calendars 
            WHERE
                DATE_FORMAT( week_date, '%Y-%m' ) = '".$month."' 
                AND remark != 'H'");

            $point_check = DB::connection('ympimis_2')->table('daily_audit_points')->where('category',$request->get('category'))->where('activity_list_id',$request->get('id'))->get();

            $audit = DB::connection('ympimis_2')->table('daily_audits')->where('category',$request->get('category'))->where('activity_list_id',$request->get('id'))->where(DB::RAW("DATE_FORMAT( date, '%Y-%m' )"),$month)->get();

            $monthTitle = date("M-Y", strtotime($month));

            $response = array(
                'status' => true,
                'months' => $months,
                'monthTitle' => $monthTitle,
                'point_check' => $point_check,
                'audit' => $audit,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function inputDailyAudit(Request $request)
    {
        try {


            $point_check = $request->get('point_check');
            $category = $request->get('category');
            $location = $request->get('location');
            $image_reference = $request->get('image_reference');
            $condition = $request->get('condition');
            $activity_list_id = $request->get('activity_list_id');
            $note = $request->get('note');

            $check = DB::connection('ympimis_2')->table('daily_audits')->where('date',date('Y-m-d'))->where('activity_list_id',$activity_list_id[0])->get();
            if (count($check) > 0) {
                $response = array(
                    'status' => false,
                    'message' => 'Audit Sudah Diinput Hari Ini'
                );
                return Response::json($response);
            }
            $emp = EmployeeSync::where('employee_id',Auth::user()->username)->first();

            $filename = "";
            $file_destination = 'data_file/daily_audit/'.$category.'/';
            if (count($request->file('file')) > 0) {
              $file = $request->file('file');
              $filename = 'evidence_'.$category.'_'.Auth::id().'_'.date('YmdHisa').'.'.$request->input('extension');
              $file->move($file_destination, $filename);
            }

            // for ($i=0; $i < count($point_check); $i++) { 
            if ($emp->name == 'M. Subekhan') {
                $empname = 'Moh.Subekhan';
            }else{
                $empname = $emp->name;
            }
                $input = DB::connection('ympimis_2')->table('daily_audits')->insert([
                    'point_check' => $point_check,
                    'category' => $category,
                    'location' => $location,
                    'image_reference' => $image_reference,
                    'condition' => $condition,
                    'activity_list_id' => $activity_list_id,
                    'note' => $note,
                    'evidence' => $filename,
                    'auditor_id' => $emp->employee_id,
                    'auditor_name' => $empname,
                    'date' => date('Y-m-d'),
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            // }
            $datass = [];
            array_push($datass, ['point_check' => $point_check,
                    'category' => $category,
                    'location' => $location,
                    'image_reference' => $image_reference,
                    'condition' => $condition,
                    'activity_list_id' => $activity_list_id,
                    'note' => $note,
                    'evidence' => $filename,
                    'auditor_id' => $emp->employee_id,
                    'auditor_name' => $empname,
                    'date' => date('Y-m-d'),
                    'created_by' => Auth::id(),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')]);

            

            $mail_to = [];
            array_push($mail_to, 'bambang.supriyadi@music.yamaha.com');
            array_push($mail_to, 'nadif@music.yamaha.com');

            $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('position','Foreman')->first();
            if ($foreman) {
                $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('position','Foreman')->first();
            }else{
                $foreman = Approver::where('department',$emp->department)->where('section',$emp->section)->where('position','Chief')->first();
            }

            if ($foreman) {
                array_push($mail_to, $foreman->approver_email);
            }

            if ($category == 'compressor') {
                if ($condition == 'NG') {
                    $datas = [
                      'category' => 'COMPRESSOR',
                      'data' => $datass,
                    ];

                    // return view('mails.daily_audit_warning')->with('data',$datas);
                    // die();
                    Mail::to($mail_to)
                      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                      ->send(new SendEmail($datas, 'daily_audit_warning'));
                }
            }

            if ($category == 'steam') {
                if ($condition == 'NG') {
                    $datas = [
                      'category' => 'STEAM',
                      'data' => $datass,
                    ];

                    // return view('mails.daily_audit_warning')->with('data',$datas);
                    // die();
                    Mail::to($mail_to)
                      ->bcc(['mokhamad.khamdan.khabibi@music.yamaha.com'])
                      ->send(new SendEmail($datas, 'daily_audit_warning'));
                }
            }

            $response = array(
                'status' => true,
                'message' => 'Success Input Audit'
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexHandlingCompressor(){
        $title = 'Penanganan Audit Pipeline Compressor';
        $title_jp = '??';

        return view('processes.middle.audit.handling_compressor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Compressor')->with('head','Compressor');
    }

    public function fetchHandlingCompressor(Request $request)
    {
        try {
            $audit = db::connection('ympimis_2')->table('daily_audits')->where('handling_status',null)->where('category','compressor')->where('condition','NG')->get();
            $audit_kerjakan = db::connection('ympimis_2')->table('daily_audits')->where('handling_status','InProgress')->where('category','compressor')->where('condition','NG')->where('handled_id',strtoupper(Auth::user()->username))->get();

            $response = array(
                'status' => true,
                'audit' => $audit,
                'audit_kerjakan' => $audit_kerjakan
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function doingHandlingCompressor(Request $request)
    {
        try {
            $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->where('handling_status',null)->first();
            if ($audit != null) {
                $emp = EmployeeSync::where('employee_id',strtoupper(Auth::user()->username))->first();
                $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->update([
                    'handled_id' => strtoupper(Auth::user()->username),
                    'handled_name' => $emp->name,
                    'started_at' => date('Y-m-d H:i:s'),
                    'handling_status' => 'InProgress',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $response = array(
                    'status' => true,
                    'message' => 'Mulai Mengerjakan'
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                    'message' => 'Sudah Ditangani. Pilih yang lain.'
                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function inputHandlingCompressor(Request $request)
    {
        try {
            $filename = "";
            $file_destination = 'data_file/daily_audit/compressor/penanganan';
            if (count($request->file('file')) > 0) {
              $file = $request->file('file');
              $filename = 'handling_'.$request->get('id').'_'.Auth::id().'_'.date('YmdHisa').'.'.$request->input('extension');
              $file->move($file_destination, $filename);
            }

            $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->update([
                'handling' => $request->get('handling'),
                'handling_evidence' => $filename,
                'finished_at' => date('Y-m-d H:i:s'),
                'handling_status' => 'Finished',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $response = array(
                'status' => true,
                'message' => 'Sukses Input Penanganan'
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexCompressorMonitoring(){
        $title = 'Monitoring Audit Compressor';
        $title_jp = '??';

        return view('processes.middle.audit.monitoring_compressor', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Compressor')->with('head','Compressor');
    }

    public function fetchCompressorMonitoring(Request $request)
    {
        try {
            $month = $request->get('month');
            if ($month == '') {
                $date_all = DB::SELECT("SELECT
                    week_date,
                    DATE_FORMAT( week_date, '%d-%b-%Y' ) AS date_name 
                FROM
                    weekly_calendars 
                WHERE
                    DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".date('Y-m')."' 
                    AND remark != 'H' 
                ORDER BY
                    week_date");
                $month = date('Y-m');
                $monthTitle = date("F Y", strtotime($month));
            }else{
                $date_all = DB::SELECT("SELECT
                    week_date,
                    DATE_FORMAT( week_date, '%d-%b-%Y' ) AS date_name 
                FROM
                    weekly_calendars 
                WHERE
                    DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$month."' 
                    AND remark != 'H' 
                ORDER BY
                    week_date");

                $month = $request->get('month');
                $monthTitle = date("F Y", strtotime($month));
            }

            $activity = DB::SELECT("SELECT
                *
                FROM
                    activity_lists 
                WHERE
                    activity_type = 'Daily Audit' 
                    AND remark = 'compressor'
                    and deleted_at is null");

            $audit_all = [];
            $temuan_all = [];

            for ($i=0; $i < count($date_all); $i++) { 
                $audit = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                    ( activity_list_id ),
                    '".$date_all[$i]->week_date."' as date,
                    GROUP_CONCAT(
                    DISTINCT ( location )) AS location,
                    GROUP_CONCAT( point_check SEPARATOR '_' ) AS point_check,
                    GROUP_CONCAT( `condition` SEPARATOR '_' ) AS `condition`,
                    GROUP_CONCAT( COALESCE ( `note`, '' ) SEPARATOR '_' ) AS `note`,
                    GROUP_CONCAT(
                    DISTINCT ( auditor_id )) AS auditor_id,
                    GROUP_CONCAT(
                    DISTINCT ( auditor_name )) AS auditor_name 
                FROM
                    daily_audits 
                WHERE
                    date = '".$date_all[$i]->week_date."'
                    AND category = 'compressor' 
                GROUP BY
                    activity_list_id");

                $temuan = DB::connection('ympimis_2')->select("SELECT
                    activity_list_id,
                    '".$date_all[$i]->week_date."' AS date,
                    location,
                    point_check,
                    `condition`,
                    `note`,
                    auditor_id,
                    `evidence`,
                    auditor_name 
                FROM
                    daily_audits 
                WHERE
                    date = '".$date_all[$i]->week_date."' 
                    AND category = 'compressor' 
                    AND `condition` = 'NG'");

                array_push($audit_all, $audit);
                array_push($temuan_all, $temuan);
            }

            $response = array(
                'status' => true,
                'audit_all' => $audit_all,
                'temuan_all' => $temuan_all,
                'date_all' => $date_all,
                'activity' => $activity,
                'monthTitle' => $monthTitle,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function indexSteamMonitoring(){
        $title = 'Monitoring Audit Steam';
        $title_jp = '??';

        return view('processes.middle.audit.monitoring_steam', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Steam')->with('head','Steam');
    }

    public function fetchSteamMonitoring(Request $request)
    {
        try {
            $month = $request->get('month');
            if ($month == '') {
                $date_all = DB::SELECT("SELECT
                    week_date,
                    DATE_FORMAT( week_date, '%d-%b-%Y' ) AS date_name 
                FROM
                    weekly_calendars 
                WHERE
                    DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".date('Y-m')."' 
                    AND remark != 'H' 
                ORDER BY
                    week_date");
                $month = date('Y-m');
                $monthTitle = date("F Y", strtotime($month));
            }else{
                $date_all = DB::SELECT("SELECT
                    week_date,
                    DATE_FORMAT( week_date, '%d-%b-%Y' ) AS date_name 
                FROM
                    weekly_calendars 
                WHERE
                    DATE_FORMAT( weekly_calendars.week_date, '%Y-%m' ) = '".$month."' 
                    AND remark != 'H' 
                ORDER BY
                    week_date");

                $month = $request->get('month');
                $monthTitle = date("F Y", strtotime($month));
            }

            $activity = DB::SELECT("SELECT
                *
                FROM
                    activity_lists 
                WHERE
                    activity_type = 'Daily Audit' 
                    AND remark = 'steam'
                    and deleted_at is null");

            $audit_all = [];
            $temuan_all = [];

            for ($i=0; $i < count($date_all); $i++) { 
                $audit = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
                    ( activity_list_id ),
                    '".$date_all[$i]->week_date."' as date,
                    GROUP_CONCAT(
                    DISTINCT ( location )) AS location,
                    GROUP_CONCAT( point_check SEPARATOR '_' ) AS point_check,
                    GROUP_CONCAT( `condition` SEPARATOR '_' ) AS `condition`,
                    GROUP_CONCAT( COALESCE ( `note`, '' ) SEPARATOR '_' ) AS `note`,
                    GROUP_CONCAT(
                    DISTINCT ( auditor_id )) AS auditor_id,
                    GROUP_CONCAT(
                    DISTINCT ( auditor_name )) AS auditor_name 
                FROM
                    daily_audits 
                WHERE
                    date = '".$date_all[$i]->week_date."'
                    AND category = 'steam' 
                GROUP BY
                    activity_list_id");

                $temuan = DB::connection('ympimis_2')->select("SELECT
                    activity_list_id,
                    '".$date_all[$i]->week_date."' AS date,
                    location,
                    point_check,
                    `condition`,
                    `note`,
                    auditor_id,
                    `evidence`,
                    auditor_name 
                FROM
                    daily_audits 
                WHERE
                    date = '".$date_all[$i]->week_date."' 
                    AND category = 'steam' 
                    AND `condition` = 'NG'");

                array_push($audit_all, $audit);
                array_push($temuan_all, $temuan);
            }

            $response = array(
                'status' => true,
                'audit_all' => $audit_all,
                'temuan_all' => $temuan_all,
                'date_all' => $date_all,
                'activity' => $activity,
                'monthTitle' => $monthTitle,
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    //HANDLING STEAM
    public function indexHandlingSteam(){
        $title = 'Penanganan Audit Steam';
        $title_jp = '??';

        return view('processes.middle.audit.handling_steam', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Steam')->with('head','Steam');
    }

    public function fetchHandlingSteam(Request $request)
    {
        try {
            $audit = db::connection('ympimis_2')->table('daily_audits')->where('handling_status',null)->where('category','steam')->where('condition','NG')->get();
            $audit_kerjakan = db::connection('ympimis_2')->table('daily_audits')->where('handling_status','InProgress')->where('category','steam')->where('condition','NG')->where('handled_id',strtoupper(Auth::user()->username))->get();

            $response = array(
                'status' => true,
                'audit' => $audit,
                'audit_kerjakan' => $audit_kerjakan
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function doingHandlingSteam(Request $request)
    {
        try {
            $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->where('handling_status',null)->first();
            if ($audit != null) {
                $emp = EmployeeSync::where('employee_id',strtoupper(Auth::user()->username))->first();
                $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->update([
                    'handled_id' => strtoupper(Auth::user()->username),
                    'handled_name' => $emp->name,
                    'started_at' => date('Y-m-d H:i:s'),
                    'handling_status' => 'InProgress',
                    'updated_at' => date('Y-m-d H:i:s')
                ]);

                $response = array(
                    'status' => true,
                    'message' => 'Mulai Mengerjakan'
                );
                return Response::json($response);
            }else{
                $response = array(
                    'status' => false,
                    'message' => 'Sudah Ditangani. Pilih yang lain.'
                );
                return Response::json($response);
            }
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    public function inputHandlingSteam(Request $request)
    {
        try {
            $filename = "";
            $file_destination = 'data_file/daily_audit/steam/penanganan';
            if (count($request->file('file')) > 0) {
              $file = $request->file('file');
              $filename = 'handling_'.$request->get('id').'_'.Auth::id().'_'.date('YmdHisa').'.'.$request->input('extension');
              $file->move($file_destination, $filename);
            }

            $audit = DB::connection('ympimis_2')->table('daily_audits')->where('id',$request->get('id'))->update([
                'handling' => $request->get('handling'),
                'handling_evidence' => $filename,
                'finished_at' => date('Y-m-d H:i:s'),
                'handling_status' => 'Finished',
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $response = array(
                'status' => true,
                'message' => 'Sukses Input Penanganan'
            );
            return Response::json($response);
        } catch (\Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage()
            );
            return Response::json($response);
        }
    }

    

    
}

