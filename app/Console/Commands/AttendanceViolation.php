<?php

namespace App\Console\Commands;

use App\Mail\SendEmail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AttendanceViolation extends Command
{
    protected $signature = 'attendance:violation';
    protected $description = 'Command description';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $first_30 = date('Y-m-d', strtotime('-35 Days'));
        $last_30 = date('Y-m-d', strtotime('-5 Days'));

        $attendances_1 = db::connection('sunfish')->select("SELECT
	result.emp_no,
	result.full_name,
	SUM ( result.SAKIT ) AS sakit,
	SUM ( result.MANGKIR ) AS mangkir,
	SUM ( result.TERLAMBAT ) AS terlambat
FROM
	(
	SELECT
		a.emp_no,
		a.full_name,
		SUM ( IIF ( a.attend_code = 'SAKIT', 1, 0 ) ) AS 'SAKIT',
		SUM ( IIF ( a.attend_code = 'MANGKIR', 1, 0 ) ) AS 'MANGKIR',
		SUM ( IIF ( a.attend_code = 'TERLAMBAT/PULANG CEPAT', 1, 0 ) ) AS 'TERLAMBAT'
	FROM
		(
		SELECT
			a.emp_no,
			a.full_name,
			IIF (
				a.Attend_Code LIKE '%SAKIT%',
				'SAKIT',
				IIF (
					a.Attend_Code LIKE '%MANGKIR%',
					'MANGKIR',
					IIF ( a.Attend_Code LIKE '%LTI%', 'TERLAMBAT/PULANG CEPAT', IIF ( a.Attend_Code LIKE '%EAO%', 'TERLAMBAT/PULANG CEPAT', 'UNDEFINED' ) )
				)
			) AS attend_code,
			a.shiftstarttime
		FROM
			VIEW_YMPI_ATTENDANCE AS a
			LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS e ON e.Emp_no = a.emp_no
		WHERE
			format ( a.shiftstarttime, 'yyyy-MM-dd' ) <= '" . $last_30 . "'
			AND format ( a.shiftstarttime, 'yyyy-MM-dd' ) >= '" . $first_30 . "'
			AND a.shiftdaily_code NOT LIKE '%OFF%'
			AND a.emp_no NOT LIKE 'OS%'
			AND ( a.Attend_Code LIKE '%SAKIT%' OR a.Attend_Code LIKE '%MANGKIR%' OR a.Attend_Code LIKE '%LTI%' OR a.Attend_Code LIKE '%EAO%' )
			AND e.end_date IS NULL
		) AS a
	GROUP BY
		a.emp_no,
		a.full_name
	) AS result
GROUP BY
	result.emp_no,
	result.full_name");

        $attendances_2 = db::select("SELECT
	hr_leave_request_details.employee_id,
	hr_leave_request_details.`name`,
	count( hr_leave_request_details.request_id ) AS izin
FROM
	hr_leave_request_details
	JOIN hr_leave_requests ON hr_leave_requests.request_id = hr_leave_request_details.request_id
	JOIN employee_syncs ON employee_syncs.employee_id = hr_leave_request_details.employee_id
WHERE
	hr_leave_requests.remark != 'Rejected'
	AND hr_leave_requests.purpose_category = 'PRIBADI'
	AND hr_leave_request_details.employee_id NOT LIKE 'OS%'
	AND hr_leave_requests.time_departure >= '" . $first_30 . "'
	AND hr_leave_requests.time_departure <= '" . $last_30 . "'
	AND employee_syncs.`group` NOT LIKE '%Driver%'
GROUP BY
	employee_id, `name`");

        $result_1 = array();

        for ($i = 0; $i < count($attendances_1); $i++) {
            if ($attendances_1[$i]->sakit > 5 || $attendances_1[$i]->mangkir > 0 || $attendances_1[$i]->terlambat >= 3) {
                array_push($result_1, [
                    'employee_id' => $attendances_1[$i]->emp_no,
                    'name' => $attendances_1[$i]->full_name,
                    'sakit' => $attendances_1[$i]->sakit,
                    'mangkir' => $attendances_1[$i]->mangkir,
                    'terlambat' => $attendances_1[$i]->terlambat,
                    'izin' => 0,
                ]);
            }
        }

        for ($i = 0; $i < count($attendances_2); $i++) {
            if ($attendances_2[$i]->izin >= 5) {
                array_push($result_1, [
                    'employee_id' => $attendances_2[$i]->employee_id,
                    'name' => $attendances_2[$i]->name,
                    'sakit' => 0,
                    'mangkir' => 0,
                    'terlambat' => 0,
                    'izin' => $attendances_2[$i]->izin,
                ]);
            }

        }

        $result_2 = array();

        for ($i = 0; $i < count($result_1); $i++) {
            $key = '';
            $key .= ($result_1[$i]['employee_id'] . '#');
            $key .= ($result_1[$i]['name'] . '#');

            if (!array_key_exists($key, $result_2)) {
                $row = array();
                $row['employee_id'] = $result_1[$i]['employee_id'];
                $row['name'] = $result_1[$i]['name'];
                $row['sakit'] = $result_1[$i]['sakit'];
                $row['mangkir'] = $result_1[$i]['mangkir'];
                $row['terlambat'] = $result_1[$i]['terlambat'];
                $row['izin'] = $result_1[$i]['izin'];

                $result_2[$key] = $row;
            } else {
                $result_2[$key]['sakit'] = $result_2[$key]['sakit'] + $result_1[$i]['sakit'];
                $result_2[$key]['mangkir'] = $result_2[$key]['mangkir'] + $result_1[$i]['mangkir'];
                $result_2[$key]['terlambat'] = $result_2[$key]['terlambat'] + $result_1[$i]['terlambat'];
                $result_2[$key]['izin'] = $result_2[$key]['izin'] + $result_1[$i]['izin'];
            }
        }

        $data = array(
            'violations' => $result_2,
            'first_30' => $first_30,
            'last_30' => $last_30,
        );

        $date_now = date('Y-m-d');
        $wc = db::select('select week_name from weekly_calendars where week_date = "' . $date_now . '"');

        foreach ($data['violations'] as $row) {
            db::connection('ympimis_2')->table('problem_employees')->insert([
                'employee_id' => $row['employee_id'],
                'name' => $row['name'],
                'sakit' => $row['sakit'],
                'mangkir' => $row['mangkir'],
                'terlambat' => $row['terlambat'],
                'keluar_perusahaan' => $row['izin'],
                'weekly_calendar' => $wc[0]->week_name,
                'created_at' => date("Y-m-d H:i:s"),
                'updated_at' => date("Y-m-d H:i:s"),
            ]);
        };

        $mail_to = [
            'achmad.riski.bayu@music.yamaha.com',
            'mahendra.putra@music.yamaha.com',
            'dicky.kurniawan@music.yamaha.com',
            'adhi.satya.indradhi@music.yamaha.com',
        ];

        Mail::to($mail_to)->cc(['khoirul.umam@music.yamaha.com'])->bcc(['ympi-mis-ML@music.yamaha.com'])->send(new SendEmail($data, 'attendance_violation'));
    }
}
