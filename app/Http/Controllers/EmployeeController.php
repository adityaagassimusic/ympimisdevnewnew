<?php

namespace App\Http\Controllers;

use App\CostCenter;
use App\Department;
use App\Employee;
use App\EmployeeAttachment;
use App\EmployeeSync;
use App\EmployeeTax;
use App\EmployeeUpdate;
use App\EmploymentLog;
use App\Grade;
use App\Group;
use App\HrQuestionDetail;
use App\HrQuestionLog;
use App\Http\Controllers\Controller;
use App\KaizenCalculation;
use App\KaizenForm;
use App\KaizenNote;
use App\KaizenScore;
use App\Mail\SendEmail;
use App\Mutationlog;
use App\OrganizationStructure;
use App\Position;
use App\PresenceLog;
use App\PromotionLog;
use App\Section;
use App\StandartCost;
use App\User;
use Carbon\Carbon;
use DataTables;
use DateTime;
use Excel;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;
use Session;

class EmployeeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->keluarga = [
            'Tk',
            'K0',
            'K1',
            'K2',
            'K3',
            'Pk1',
            'Pk2',
            'Pk3',
            '0',
        ];

        $this->attend = [
            ['attend_code' => 'ABS', 'attend_name' => 'Absent', 'attend_type' => '-'],
            ['attend_code' => 'CK1', 'attend_name' => 'Keluarga Meninggal', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK10', 'attend_name' => 'Melahirkan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK11', 'attend_name' => 'Keguguran', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK12', 'attend_name' => 'Ibadah Haji / Ziarah Keagamaan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK13', 'attend_name' => 'Musibah', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK15', 'attend_name' => 'Saudara Kandung Menikah', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK2', 'attend_name' => 'Keluarga Serumah Meninggal', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK3', 'attend_name' => 'Menikah', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK4', 'attend_name' => 'Menikahkan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK5', 'attend_name' => 'Menghitankan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK6', 'attend_name' => 'Membaptiskan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK7', 'attend_name' => 'Istri Keguguran / Melahirkan', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK8', 'attend_name' => 'Tugas Negara', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CK9', 'attend_name' => 'Haid', 'attend_type' => 'Cuti'],
            ['attend_code' => 'CUTI', 'attend_name' => 'Cuti', 'attend_type' => 'Cuti'],
            ['attend_code' => 'IMP', 'attend_name' => 'Izin Meninggalkan Pekerjaan', 'attend_type' => 'Izin Meninggalkan Pekerjaan'],
            ['attend_code' => 'Izin', 'attend_name' => 'Izin', 'attend_type' => 'Izin'],
            ['attend_code' => 'Mangkir', 'attend_name' => 'Mangkir', 'attend_type' => 'Mangkir'],
            ['attend_code' => 'OFF', 'attend_name' => 'OFF', 'attend_type' => '-'],
            ['attend_code' => 'PC', 'attend_name' => 'Pulang Cepat', 'attend_type' => 'Pulang Cepat'],
            ['attend_code' => 'SAKIT', 'attend_name' => 'Sakit Surat Dokter', 'attend_type' => 'Sakit'],
            ['attend_code' => 'UPL', 'attend_name' => 'Cuti Tidak Di Bayar', 'attend_type' => 'Cuti'],
            ['attend_code' => 'EAI', 'attend_name' => 'Early In', 'attend_type' => '-'],
            ['attend_code' => 'EAO', 'attend_name' => 'Early Out', 'attend_type' => '-'],
            ['attend_code' => 'LTI', 'attend_name' => 'Late In', 'attend_type' => 'Terlambat'],
            ['attend_code' => 'NSI', 'attend_name' => 'No Swipe In', 'attend_type' => 'Tidak Ceklog Masuk'],
            ['attend_code' => 'NSO', 'attend_name' => 'No Swipe Out', 'attend_type' => 'Tidak Ceklog Pulang'],
            ['attend_code' => 'ODT', 'attend_name' => 'Dinas', 'attend_type' => 'Dinas Luar'],
            ['attend_code' => 'PRS', 'attend_name' => 'Present', 'attend_type' => 'Hadir'],
            ['attend_code' => 'PRSOFF', 'attend_name' => 'PRSOFF', 'attend_type' => '-'],
            ['attend_code' => 'STSHIFT2', 'attend_name' => 'Shift 2', 'attend_type' => '-'],
            ['attend_code' => 'STSHIFT3', 'attend_name' => 'Shift 3', 'attend_type' => '-'],
            ['attend_code' => 'STSHIFTG', 'attend_name' => 'Shift Group', 'attend_type' => '-'],
            ['attend_code' => 'TELAT', 'attend_name' => 'Izin Telat Masuk', 'attend_type' => 'Terlambat'],
            ['attend_code' => 'TRN', 'attend_name' => 'Training', 'attend_type' => '-'],
            ['attend_code' => 'UNPR', 'attend_name' => 'Unproductive', 'attend_type' => '-'],
        ];

        $this->status = [
            'Percobaan',
            'Kontrak 1',
            'Kontrak 2',
            'Tetap',
        ];

        $this->cat = [
            'Absensi',
            'Lembur',
            'BPJS Kes',
            'BPJS TK',
            'Cuti',
            "PKB",
            "Penggajian",
        ];

        $this->usr = "'PI1110001','PI0904007','PI9809011', 'PI1201001'";

        $this->wst = ['PI1808032', 'PI1809036', 'PI1505002'];

        $this->union_officers = [
            ['union_name' => 'SBM', 'officer_id' => 'PI1412008'],
            ['union_name' => 'SPMI', 'officer_id' => 'PI1412008'],
            ['union_name' => 'SPSI', 'officer_id' => 'PI1412008'],
            ['union_name' => 'SBM', 'officer_id' => 'PI9903003'],
            ['union_name' => 'SPMI', 'officer_id' => 'PI9806002'],
            ['union_name' => 'SPSI', 'officer_id' => 'PI9809012'],
        ];

    }

    public function indexManpowerInformationManagement()
    {
        $title = 'Employee Information';
        $title_jp = '従業員の情報';

        if (!in_array(Auth::user()->role_code, ['D', 'DGM', 'GM', 'S-HR', 'C-HR', 'M-HR', 'S-MIS', 'C-MIS', 'JPN'])) {
            return view('404');
        }

        return view('employees.manpower_information_management', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
        );
    }

    public function fetchManpowerInformationManagement(Request $request)
    {

        $employee_syncs = db::select("SELECT
        	es.employee_id,
        	es.card_id,
        	es.`name`,
	        es.birth_date,
	        TIMESTAMPDIFF(MONTH,es.birth_date,date(now())) as age_month,
        	es.gender,
        	es.hire_date,
        	es.position,
        	es.grade_code,
            IF
            	( es.department IS NULL, 'MGMT', d.department_shortname ) AS department_shortname,
            IF
            	( es.department IS NULL, 'Management', es.department ) AS department,
            IFNULL( es.section, '' ) AS section,
            IFNULL( es.`group`, '' ) AS `group`,
            IFNULL( es.sub_group, '' ) AS sub_group,
            IF
            	( es.employment_status LIKE '%CONTRACT%', 'CONTRACT', es.employment_status ) AS employment_status,
            IF
        	    ( es.`union` IS NULL OR es.`union` = '', 'NONE', es.`union` ) AS union_name,
            job_status,
        	p.`value` AS position_order,
        	g.`value` AS grade_code_order
        FROM
        	employee_syncs AS es
        	LEFT JOIN departments AS d ON d.department_name = es.department
        	LEFT JOIN positions AS p ON p.position = es.position
        	LEFT JOIN grades AS g ON g.grade_code = es.grade_code
        WHERE
        	( es.end_date IS NULL OR es.end_date >= date( now()) )
        ORDER BY
        	es.hire_date ASC");

        $work_lengths = db::select("SELECT
        	work_length.card_id,
        	sum( work_length.work_length_month ) AS work_length_month
        FROM
        	(
        	SELECT
        		card_id,
        		TIMESTAMPDIFF(
        			MONTH,
        			hire_date,
        		IF
        		( end_date IS NULL, date( now()), end_date )) AS work_length_month
        	FROM
        		employee_syncs UNION ALL
        	SELECT
        		card_id,
        		TIMESTAMPDIFF(
        			MONTH,
        			hire_date,
        		IF
        		( end_date IS NULL, date( now()), end_date )) AS work_length_month
        	FROM
        		employee_old_syncs
        	) AS work_length
        WHERE
        	card_id IS NOT NULL
        	AND card_id <> ''
        GROUP BY
        	work_length.card_id");

        $employees = array();

        foreach ($employee_syncs as $e) {
            $employee_id = $e->employee_id;
            $card_id = $e->card_id;
            $employee_name = $e->name;
            $birth_date = $e->birth_date;
            $age_month = $e->age_month;
            $gender = $e->gender;
            $hire_date = $e->hire_date;
            $position = $e->position;
            $position_order = $e->position_order;
            $grade_code = $e->grade_code;
            $grade_code_order = $e->grade_code_order;
            $department = $e->department;
            $department_shortname = $e->department_shortname;
            $section = $e->section;
            $group = $e->group;
            $sub_group = $e->sub_group;
            $employment_status = $e->employment_status;
            $job_status = $e->job_status;
            $union_name = $e->union_name;
            $work_length_month = 0;
            $position_category = "";
            $age_category = "";
            $work_length_category = "";

            if ($position == 'Chief' || $position == 'Foreman') {
                $position_category = "Chief/Foreman";
            } else if ($position == 'Deputy General Manager') {
                $position_category = "DGM";
            } else if ($position == 'General Manager') {
                $position_category = "GM";
            } else if ($position == 'President Director') {
                $position_category = "Presdir";
            } else if ($position == 'Operator Contract') {
                $position_category = "Contract";
            } else if ($position == 'Operator Outsource') {
                $position_category = "Outsource";
            } else {
                $position_category = $e->position;
            }

            if (($age_month / 12) >= 18 && ($age_month / 12) < 21) {
                $age_category = "18-20";
            } else if (($age_month / 12) >= 21 && ($age_month / 12) < 26) {
                $age_category = "21-25";
            } else if (($age_month / 12) >= 26 && ($age_month / 12) < 31) {
                $age_category = "26-30";
            } else if (($age_month / 12) >= 31 && ($age_month / 12) < 36) {
                $age_category = "31-35";
            } else if (($age_month / 12) >= 36 && ($age_month / 12) < 41) {
                $age_category = "36-40";
            } else if (($age_month / 12) >= 41 && ($age_month / 12) < 46) {
                $age_category = "41-45";
            } else if (($age_month / 12) >= 46 && ($age_month / 12) < 51) {
                $age_category = "46-50";
            } else if (($age_month / 12) >= 51 && ($age_month / 12) < 56) {
                $age_category = "51-55";
            } else {
                $age_category = "56+";
            }

            foreach ($work_lengths as $w) {
                if ($w->card_id == $e->card_id) {
                    $work_length_month = $w->work_length_month;
                    break;
                }
            }

            if (($work_length_month / 12) >= 0 && ($work_length_month / 12) < 1) {
                $work_length_category = "0 Year";
            } else if (($work_length_month / 12) >= 1 && ($work_length_month / 12) < 2) {
                $work_length_category = "1 Years";
            } else if (($work_length_month / 12) >= 2 && ($work_length_month / 12) < 3) {
                $work_length_category = "2 Years";
            } else if (($work_length_month / 12) >= 3 && ($work_length_month / 12) < 4) {
                $work_length_category = "3 Years";
            } else if (($work_length_month / 12) >= 4) {
                $work_length_category = "4+ Years";
            }

            array_push($employees, [
                'employee_id' => $employee_id,
                'card_id' => $card_id,
                'employee_name' => $employee_name,
                'birth_date' => $birth_date,
                'age_month' => $age_month,
                'gender' => $gender,
                'hire_date' => $hire_date,
                'position' => $position,
                'position_order' => $position_order,
                'grade_code' => $grade_code,
                'grade_code_order' => $grade_code_order,
                'department' => $department,
                'department_shortname' => $department_shortname,
                'section' => $section,
                'group' => $group,
                'sub_group' => $sub_group,
                'employment_status' => $employment_status,
                'job_status' => $job_status,
                'union_name' => $union_name,
                'work_length_month' => $work_length_month,
                'work_length_string' => floor($work_length_month / 12) . ' Th ' . $work_length_month % 12 . ' Bln',
                'position_category' => $position_category,
                'age_category' => $age_category,
                'work_length_category' => $work_length_category,
            ]);
        }

        $response = array(
            'status' => true,
            'employees' => $employees,
        );
        return Response::json($response);

    }

    public function indexManpowerInformation()
    {
        $title = 'Manpower Information';
        $title_jp = '人工の情報';

        return view('employees.manpower_information', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
        );
    }

    public function fetchManpowerInformation()
    {
        try {

            $employees = db::select("SELECT
            	es.employee_id,
            	es.`name`,
            	es.gender,
            	es.hire_date,
            	es.position,
            IF
            	( es.department IS NULL, 'MGMT', d.department_shortname ) AS department_shortname,
            IF
            	( es.department IS NULL, 'Management', es.department ) AS department,
	        IFNULL( es.section, '-' ) AS section,
	        IFNULL( es.`group`, '-' ) AS `group`,
	        IFNULL( es.sub_group, '-' ) AS sub_group,
            	es.employment_status,
            IF
            	( es.`union` IS NULL OR es.`union` = '', 'NONE', es.`union` ) AS union_name,
            IF
	            ( es.job_status = 'DIRECT', 'DIRECT', 'INDIRECT' ) AS job_status
            FROM
            	employee_syncs AS es
            	LEFT JOIN departments AS d ON d.department_name = es.department
            WHERE
            	( es.end_date IS NULL OR es.end_date >= '" . date('Y-m-d') . "' )
            ORDER BY
            	es.hire_date ASC");

            $response = array(
                'status' => true,
                'employees' => $employees,
            );
            return Response::json($response);

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function indexUnion()
    {
        $title = 'Registrasi Kepesertaan Serikat Pekerja';
        $title_jp = '労働組合会員登録';

        // $employee = db::table('employee_syncs')->where('employee_id', '=', Auth::user()->username)->first();
        $employee = db::connection('sunfish')->table('VIEW_YMPI_Emp_OrgUnit')->where('Emp_no', '=', Auth::user()->username)
            ->select(
                'Emp_no AS employee_id',
                'Full_name AS name',
                'gender AS gender',
                'birthplace AS birth_place',
                'birthdate AS birth_date',
                'Current_Address AS address',
                'phone AS phone',
                'identity_no AS card_id',
                'taxfilenumber AS npwp',
                'JP AS JP',
                'BPJS AS BPJS',
                'start_date AS hire_date',
                'end_date AS end_date',
                'pos_code AS position_code',
                'grade_code AS grade_code',
                'gradecategory_name AS grade_name',
                'Division AS division',
                'Department AS department',
                'section AS section',
                'Groups AS group',
                'Sub_Groups AS sub_group',
                'employ_code AS employment_status',
                'cost_center_code AS cost_center',
                'Penugasan AS assignment',
                'Labour_Union AS union',
                'NIK_Manager AS nik_manager',
                'Zona AS zona',
                'job_status_code AS job_status'
            )->first();
        $labor_union = db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', Auth::user()->username)->first();

        if ($labor_union) {
            if ($labor_union->remark == 'leave') {
                if ($employee->union == 'NONE' || $employee->union == '' || $employee->union == null) {
                    db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', Auth::user()->username)->delete();
                }
            }
            if ($labor_union->remark == 'join') {
                if ($employee->union == $labor_union->union_name) {
                    db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', Auth::user()->username)->delete();
                }
            }
        }

        return view('employees.union', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $employee,
        )
        );
    }

    public function inputUnion(Request $request)
    {
        try {
            $employee = $request->get('employee');
            $category = $request->get('category');
            $union_name = $request->get('union');

            $union = db::connection('ympimis_2')->table('labor_unions')->where('created_by', '=', $employee['employee_id'])->first();
            $text = "";
            if ($union) {
                $response = array(
                    'status' => false,
                    'message' => 'Data pengajuan anda masih diproses bagian HR.',
                );
                return Response::json($response);
            }

            if ($union_name == 'SBM') {
                $union_long_name = 'SARIKAT BURUH MUSLIMIN INDONESIA (SARBUMUSI)';
                $union_short_name = 'SARBUMUSI';

                $term = "Saya bersedia membayar Check Of System / COS setiap bulan.\n";
            }
            if ($union_name == 'SPMI') {
                $union_long_name = 'FEDERASI SERIKAT PEKERJA METAL INDONESIA (FSPMI)';
                $union_short_name = 'FSPMI';

                $term = "Saya bersedia membayar Check Of System / COS PUK SPEE FSPMI sebesar 1% dari gaji pokok per bulan (UMK Kab. Pasuruan).\n";
            }
            if ($union_name == 'SPSI') {
                $union_long_name = 'SERIKAT PEKERJA SELURUH INDONESIA (SPSI)';
                $union_short_name = 'SPSI';

                $term = "Saya bersedia membayar Check Of System / COS sebesar 1% dari gaji pokok per bulan (UMK Kab. Pasuruan), sesuai dengan keputusan AD / ART FSP LEM SPSI.\n";
            }

            if ($category == 'join') {
                $text .= "Menyatakan BERGABUNG dengan serikat pekerja\n\n";
                $text .= $union_long_name . "\n\n";
                $text .= $term;
                $text .= "Dan bersedia bersikap loyal serta mentaati dan mematuhi kebijakan yang di tetapkan oleh Pengurus " . $union_short_name . " PT. YAMAHA MUSICAL PRODUCTS INDONESIA.\n";
                $text .= "Demikian pernyataan ini dibuat dengan sebenar-benarnya dan tanpa ada paksaan dari pihak manapun.\n\n";
                $text .= "Pasuruan, " . date('d F Y');

                if (in_array($employee['union'], ['SBM', 'SPMI', 'SPSI'])) {
                    $response = array(
                        'status' => false,
                        'message' => 'Anda masih tergabung dalam serikat pekerja ' . $employee['union'],
                    );
                    return Response::json($response);
                }
            }
            if ($category == 'leave') {
                $text .= "Menyatakan MENGUNDURKAN DIRI dari serikat pekerja\n\n";
                $text .= $union_long_name . "\n\n";
                $text .= "Demikian pernyataan ini dibuat dengan sebenar-benarnya dan tanpa ada paksaan dari pihak manapun.\n\n";
                $text .= "Pasuruan, " . date('d F Y');

                if ($employee['union'] == 'NONE' || $employee['union'] == '' || $employee['union'] == null) {
                    $response = array(
                        'status' => false,
                        'message' => 'Anda tidak sedang tergabung dengan serikat pekerja manapun.',
                    );
                    return Response::json($response);
                }

                if ($employee['union'] != $union_name) {
                    $response = array(
                        'status' => false,
                        'message' => 'Serikat pekerja yang anda pilih tidak sama dengan serikat pekerja anda saat ini.',
                    );
                    return Response::json($response);
                }
            }

            $officer_ids = [];
            foreach ($this->union_officers as $union_officer) {
                if ($union_officer['union_name'] == $union_name) {
                    array_push($officer_ids, $union_officer['officer_id']);
                }
            }

            $officers = db::table('employee_syncs')->whereIn('employee_syncs.employee_id', $officer_ids)
                ->leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->select(
                    'users.email',
                    'employee_syncs.phone'
                )
                ->get();

            foreach ($officers as $officer) {

                if (substr($officer->phone, 0, 1) == '+') {
                    $phone = substr($officer->phone, 1, 15);
                } else if (substr($officer->phone, 0, 1) == '0') {
                    $phone = "62" . substr($officer->phone, 1, 15);
                } else {
                    $phone = $officer->phone;
                }
                $messages = "";
                $messages .= "Bahwa yang bertanda tangan dibawah ini:\n\n";
                $messages .= "NIK\t: " . $employee['employee_id'] . "\n";
                $messages .= "Nama\t: " . $employee['name'] . "\n";
                $messages .= "TTL\t: " . $employee['birth_place'] . ", " . date('d F Y', strtotime($employee['birth_date'])) . "\n";
                $messages .= "Alamat\t: " . $employee['address'] . "\n";
                $messages .= "Telp\t: " . $employee['phone'] . "\n";
                $messages .= "Bagian\t: " . $employee['department'] . " - " . $employee['section'] . "\n\n";
                $messages .= $text;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                    CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => 'receiver=' . $phone . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/x-www-form-urlencoded',
                        'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                    ),
                )
                );

                curl_exec($curl);
            }

            if (substr($employee['phone'], 0, 1) == '+') {
                $phone_applicant = substr($employee['phone'], 1, 15);
            } else if (substr($employee['phone'], 0, 1) == '0') {
                $phone_applicant = "62" . substr($employee['phone'], 1, 15);
            } else {
                $phone_applicant = $employee['phone'];
            }

            $curl = curl_init();
            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://app.whatspie.com/api/messages',
                CURLOPT_SSL_VERIFYHOST => FALSE,
                CURLOPT_SSL_VERIFYPEER => FALSE,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => 'receiver=' . $phone_applicant . '&device=6281130561777&message=' . urlencode($messages) . '&type=chat',
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/x-www-form-urlencoded',
                    'Authorization: Bearer UAqINT9e23uRiQmYttEUiFQ9qRMUXk8sADK2EiVSgLODdyOhgU',
                ),
            )
            );

            curl_exec($curl);

            db::connection('ympimis_2')->table('labor_unions')->insert([
                'union_name' => $union_name,
                'remark' => $category,
                'created_by' => $employee['employee_id'],
                'created_by_name' => $employee['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            db::connection('ympimis_2')->table('labor_union_logs')->insert([
                'union_name' => $union_name,
                'remark' => $category,
                'created_by' => $employee['employee_id'],
                'created_by_name' => $employee['name'],
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Pernyataan berhasil diajukan',
            );
            return Response::json($response);

        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function index()
    {
        return view('employees.master.index', array(
            'status' => $this->status,
        )
        )->with('page', 'Master Employee')->with('head', 'Employees Data');
    }

    public function indexEKaizen()
    {
        $title = 'E-kaizen Monitoring';
        $title_jp = 'E改善の監視';

        return view('employees.index_kaizen', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
        );
    }

    public function indexEmployeeResume()
    {
        $title = 'Employee Resume';
        $title_jp = '従業員のまとめ';

        // $datas = db::table('employee_syncs')->select("select * from employee_syncs");

        $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

        $datas = db::select($q);

        return view('employees.report.employee_resume', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'datas' => $datas,
        )
        );
    }

    public function indexUpdateEmpData($employee_id)
    {

        $title = 'Employee Self Services';
        $title_jp = '従業員の情報サービス';

        return view('employees.service.updateData', array(
            'employee_id' => $employee_id,
            'title' => $title,
            'title_jp' => $title_jp,
        )
        );
    }

    public function indexEmpData()
    {

        $title = 'Employee Data';
        $title_jp = '';

        $date_now = date('m-d');

        $emp_birth_date = db::select('SELECT
            employee_id,
            `name`,
            date_format(birth_date, "%d %M %Y") as birth_date
            FROM
            employee_syncs
            WHERE
            DATE_FORMAT( birth_date, "%m-%d" ) = "' . $date_now . '"
            AND end_date IS NULL
            ORDER BY
            employee_id');

        $emp_update = db::select('SELECT
            employee_id,
            `name`
            FROM
            employee_updates
            WHERE
            DATE_FORMAT( updated_at, "%Y-%m" ) = "2023-01"');

        $emp_sync = db::select('select employee_id, `name` from employee_syncs where end_date is null');

        return view('employees.report.employee_data', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'emp_birth_date' => $emp_birth_date,
            'emp_update' => $emp_update,
            'emp_sync' => $emp_sync,
        )
        );

    }

    public function fetchFillEmpData(Request $request)
    {
        $employee_id = $request->get('emp_id');

        $data = EmployeeUpdate::where('employee_id', $employee_id)
            ->select('employee_updates.*', db::raw('DATE_FORMAT(birth_date,"%d-%m-%Y") AS tgl_lahir'))
            ->first();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchUpdateEmpData(Request $request)
    {

        $nama_lengkap = $request->get('nama_lengkap');
        $employee_id = $request->get('employee_id');
        $nik = $request->get('nik');
        $npwp = $request->get('npwp');
        $tempat_lahir = $request->get('tempat_lahir');
        $tanggal_lahir = date('Y-m-d', strtotime($request->get('tanggal_lahir')));
        $jenis_kelamin = $request->get('jenis_kelamin');
        $agama = $request->get('agama');
        $status_perkawinan = $request->get('status_perkawinan');
        $alamat_asal = $request->get('alamat_asal');
        $alamat_domisili = $request->get('alamat_domisili');
        $telepon_rumah = $request->get('telepon_rumah');
        $hp = $request->get('hp');
        $email = $request->get('email');
        $bpjskes = $request->get('bpjskes');
        $faskes = $request->get('faskes');
        $bpjstk = $request->get('bpjstk');

        $nama_ayah = $request->get("nama_ayah");
        $kelamin_ayah = $request->get("kelamin_ayah");
        $tempat_lahir_ayah = $request->get("tempat_lahir_ayah");
        $tanggal_lahir_ayah = $request->get("tanggal_lahir_ayah");
        $pekerjaan_ayah = $request->get("pekerjaan_ayah");
        $f_ayah = $nama_ayah . '_' . $kelamin_ayah . '_' . $tempat_lahir_ayah . '_' . $tanggal_lahir_ayah . '_' . $pekerjaan_ayah;

        $nama_ibu = $request->get("nama_ibu");
        $kelamin_ibu = $request->get("kelamin_ibu");
        $tempat_lahir_ibu = $request->get("tempat_lahir_ibu");
        $tanggal_lahir_ibu = $request->get("tanggal_lahir_ibu");
        $pekerjaan_ibu = $request->get("pekerjaan_ibu");
        $f_ibu = $nama_ibu . '_' . $kelamin_ibu . '_' . $tempat_lahir_ibu . '_' . $tanggal_lahir_ibu . '_' . $pekerjaan_ibu;

        $nama_saudara1 = $request->get("nama_saudara1");
        $kelamin_saudara1 = $request->get("kelamin_saudara1");
        $tempat_lahir_saudara1 = $request->get("tempat_lahir_saudara1");
        $tanggal_lahir_saudara1 = $request->get("tanggal_lahir_saudara1");
        $pekerjaan_saudara1 = $request->get("pekerjaan_saudara1");
        $f_saudara1 = $nama_saudara1 . '_' . $kelamin_saudara1 . '_' . $tempat_lahir_saudara1 . '_' . $tanggal_lahir_saudara1 . '_' . $pekerjaan_saudara1;

        $nama_saudara2 = $request->get("nama_saudara2");
        $kelamin_saudara2 = $request->get("kelamin_saudara2");
        $tempat_lahir_saudara2 = $request->get("tempat_lahir_saudara2");
        $tanggal_lahir_saudara2 = $request->get("tanggal_lahir_saudara2");
        $pekerjaan_saudara2 = $request->get("pekerjaan_saudara2");
        $f_saudara2 = $nama_saudara2 . '_' . $kelamin_saudara2 . '_' . $tempat_lahir_saudara2 . '_' . $tanggal_lahir_saudara2 . '_' . $pekerjaan_saudara2;

        $nama_saudara3 = $request->get("nama_saudara3");
        $kelamin_saudara3 = $request->get("kelamin_saudara3");
        $tempat_lahir_saudara3 = $request->get("tempat_lahir_saudara3");
        $tanggal_lahir_saudara3 = $request->get("tanggal_lahir_saudara3");
        $pekerjaan_saudara3 = $request->get("pekerjaan_saudara3");
        $f_saudara3 = $nama_saudara3 . '_' . $kelamin_saudara3 . '_' . $tempat_lahir_saudara3 . '_' . $tanggal_lahir_saudara3 . '_' . $pekerjaan_saudara3;

        $nama_saudara4 = $request->get("nama_saudara4");
        $kelamin_saudara4 = $request->get("kelamin_saudara4");
        $tempat_lahir_saudara4 = $request->get("tempat_lahir_saudara4");
        $tanggal_lahir_saudara4 = $request->get("tanggal_lahir_saudara4");
        $pekerjaan_saudara4 = $request->get("pekerjaan_saudara4");
        $f_saudara4 = $nama_saudara4 . '_' . $kelamin_saudara4 . '_' . $tempat_lahir_saudara4 . '_' . $tanggal_lahir_saudara4 . '_' . $pekerjaan_saudara4;

        $nama_saudara5 = $request->get("nama_saudara5");
        $kelamin_saudara5 = $request->get("kelamin_saudara5");
        $tempat_lahir_saudara5 = $request->get("tempat_lahir_saudara5");
        $tanggal_lahir_saudara5 = $request->get("tanggal_lahir_saudara5");
        $pekerjaan_saudara5 = $request->get("pekerjaan_saudara5");
        $f_saudara5 = $nama_saudara5 . '_' . $kelamin_saudara5 . '_' . $tempat_lahir_saudara5 . '_' . $tanggal_lahir_saudara5 . '_' . $pekerjaan_saudara5;

        $nama_saudara6 = $request->get("nama_saudara6");
        $kelamin_saudara6 = $request->get("kelamin_saudara6");
        $tempat_lahir_saudara6 = $request->get("tempat_lahir_saudara6");
        $tanggal_lahir_saudara6 = $request->get("tanggal_lahir_saudara6");
        $pekerjaan_saudara6 = $request->get("pekerjaan_saudara6");
        $f_saudara6 = $nama_saudara6 . '_' . $kelamin_saudara6 . '_' . $tempat_lahir_saudara6 . '_' . $tanggal_lahir_saudara6 . '_' . $pekerjaan_saudara6;

        $nama_saudara7 = $request->get("nama_saudara7");
        $kelamin_saudara7 = $request->get("kelamin_saudara7");
        $tempat_lahir_saudara7 = $request->get("tempat_lahir_saudara7");
        $tanggal_lahir_saudara7 = $request->get("tanggal_lahir_saudara7");
        $pekerjaan_saudara7 = $request->get("pekerjaan_saudara7");
        $f_saudara7 = $nama_saudara7 . '_' . $kelamin_saudara7 . '_' . $tempat_lahir_saudara7 . '_' . $tanggal_lahir_saudara7 . '_' . $pekerjaan_saudara7;

        $nama_saudara8 = $request->get("nama_saudara8");
        $kelamin_saudara8 = $request->get("kelamin_saudara8");
        $tempat_lahir_saudara8 = $request->get("tempat_lahir_saudara8");
        $tanggal_lahir_saudara8 = $request->get("tanggal_lahir_saudara8");
        $pekerjaan_saudara8 = $request->get("pekerjaan_saudara8");
        $f_saudara8 = $nama_saudara8 . '_' . $kelamin_saudara8 . '_' . $tempat_lahir_saudara8 . '_' . $tanggal_lahir_saudara8 . '_' . $pekerjaan_saudara8;

        $nama_saudara9 = $request->get("nama_saudara9");
        $kelamin_saudara9 = $request->get("kelamin_saudara9");
        $tempat_lahir_saudara9 = $request->get("tempat_lahir_saudara9");
        $tanggal_lahir_saudara9 = $request->get("tanggal_lahir_saudara9");
        $pekerjaan_saudara9 = $request->get("pekerjaan_saudara9");
        $f_saudara9 = $nama_saudara9 . '_' . $kelamin_saudara9 . '_' . $tempat_lahir_saudara9 . '_' . $tanggal_lahir_saudara9 . '_' . $pekerjaan_saudara9;

        $nama_saudara10 = $request->get("nama_saudara10");
        $kelamin_saudara10 = $request->get("kelamin_saudara10");
        $tempat_lahir_saudara10 = $request->get("tempat_lahir_saudara10");
        $tanggal_lahir_saudara10 = $request->get("tanggal_lahir_saudara10");
        $pekerjaan_saudara10 = $request->get("pekerjaan_saudara10");
        $f_saudara10 = $nama_saudara10 . '_' . $kelamin_saudara10 . '_' . $tempat_lahir_saudara10 . '_' . $tanggal_lahir_saudara10 . '_' . $pekerjaan_saudara10;

        $nama_saudara11 = $request->get("nama_saudara11");
        $kelamin_saudara11 = $request->get("kelamin_saudara11");
        $tempat_lahir_saudara11 = $request->get("tempat_lahir_saudara11");
        $tanggal_lahir_saudara11 = $request->get("tanggal_lahir_saudara11");
        $pekerjaan_saudara11 = $request->get("pekerjaan_saudara11");
        $f_saudara11 = $nama_saudara11 . '_' . $kelamin_saudara11 . '_' . $tempat_lahir_saudara11 . '_' . $tanggal_lahir_saudara11 . '_' . $pekerjaan_saudara11;

        $nama_saudara12 = $request->get("nama_saudara12");
        $kelamin_saudara12 = $request->get("kelamin_saudara12");
        $tempat_lahir_saudara12 = $request->get("tempat_lahir_saudara12");
        $tanggal_lahir_saudara12 = $request->get("tanggal_lahir_saudara12");
        $pekerjaan_saudara12 = $request->get("pekerjaan_saudara12");
        $f_saudara12 = $nama_saudara12 . '_' . $kelamin_saudara12 . '_' . $tempat_lahir_saudara12 . '_' . $tanggal_lahir_saudara12 . '_' . $pekerjaan_saudara12;

        $nama_pasangan = $request->get("nama_pasangan");
        $kelamin_pasangan = $request->get("kelamin_pasangan");
        $tempat_lahir_pasangan = $request->get("tempat_lahir_pasangan");
        $tanggal_lahir_pasangan = $request->get("tanggal_lahir_pasangan");
        $pekerjaan_pasangan = $request->get("pekerjaan_pasangan");
        $m_pasangan = $nama_pasangan . '_' . $kelamin_pasangan . '_' . $tempat_lahir_pasangan . '_' . $tanggal_lahir_pasangan . '_' . $pekerjaan_pasangan;

        $nama_anak1 = $request->get("nama_anak1");
        $kelamin_anak1 = $request->get("kelamin_anak1");
        $tempat_lahir_anak1 = $request->get("tempat_lahir_anak1");
        $tanggal_lahir_anak1 = $request->get("tanggal_lahir_anak1");
        $pekerjaan_anak1 = $request->get("pekerjaan_anak1");
        $m_anak1 = $nama_anak1 . '_' . $kelamin_anak1 . '_' . $tempat_lahir_anak1 . '_' . $tanggal_lahir_anak1 . '_' . $pekerjaan_anak1;

        $nama_anak2 = $request->get("nama_anak2");
        $kelamin_anak2 = $request->get("kelamin_anak2");
        $tempat_lahir_anak2 = $request->get("tempat_lahir_anak2");
        $tanggal_lahir_anak2 = $request->get("tanggal_lahir_anak2");
        $pekerjaan_anak2 = $request->get("pekerjaan_anak2");
        $m_anak2 = $nama_anak2 . '_' . $kelamin_anak2 . '_' . $tempat_lahir_anak2 . '_' . $tanggal_lahir_anak2 . '_' . $pekerjaan_anak2;

        $nama_anak3 = $request->get("nama_anak3");
        $kelamin_anak3 = $request->get("kelamin_anak3");
        $tempat_lahir_anak3 = $request->get("tempat_lahir_anak3");
        $tanggal_lahir_anak3 = $request->get("tanggal_lahir_anak3");
        $pekerjaan_anak3 = $request->get("pekerjaan_anak3");
        $m_anak3 = $nama_anak3 . '_' . $kelamin_anak3 . '_' . $tempat_lahir_anak3 . '_' . $tanggal_lahir_anak3 . '_' . $pekerjaan_anak3;

        $nama_anak4 = $request->get("nama_anak4");
        $kelamin_anak4 = $request->get("kelamin_anak4");
        $tempat_lahir_anak4 = $request->get("tempat_lahir_anak4");
        $tanggal_lahir_anak4 = $request->get("tanggal_lahir_anak4");
        $pekerjaan_anak4 = $request->get("pekerjaan_anak4");
        $m_anak4 = $nama_anak4 . '_' . $kelamin_anak4 . '_' . $tempat_lahir_anak4 . '_' . $tanggal_lahir_anak4 . '_' . $pekerjaan_anak4;

        $nama_anak5 = $request->get("nama_anak5");
        $kelamin_anak5 = $request->get("kelamin_anak5");
        $tempat_lahir_anak5 = $request->get("tempat_lahir_anak5");
        $tanggal_lahir_anak5 = $request->get("tanggal_lahir_anak5");
        $pekerjaan_anak5 = $request->get("pekerjaan_anak5");
        $m_anak5 = $nama_anak5 . '_' . $kelamin_anak5 . '_' . $tempat_lahir_anak5 . '_' . $tanggal_lahir_anak5 . '_' . $pekerjaan_anak5;

        $nama_anak6 = $request->get("nama_anak6");
        $kelamin_anak6 = $request->get("kelamin_anak6");
        $tempat_lahir_anak6 = $request->get("tempat_lahir_anak6");
        $tanggal_lahir_anak6 = $request->get("tanggal_lahir_anak6");
        $pekerjaan_anak6 = $request->get("pekerjaan_anak6");
        $m_anak6 = $nama_anak6 . '_' . $kelamin_anak6 . '_' . $tempat_lahir_anak6 . '_' . $tanggal_lahir_anak6 . '_' . $pekerjaan_anak6;

        $nama_anak7 = $request->get("nama_anak7");
        $kelamin_anak7 = $request->get("kelamin_anak7");
        $tempat_lahir_anak7 = $request->get("tempat_lahir_anak7");
        $tanggal_lahir_anak7 = $request->get("tanggal_lahir_anak7");
        $pekerjaan_anak7 = $request->get("pekerjaan_anak7");
        $m_anak7 = $nama_anak7 . '_' . $kelamin_anak7 . '_' . $tempat_lahir_anak7 . '_' . $tanggal_lahir_anak7 . '_' . $pekerjaan_anak7;

        $sd_nama = $request->get("sd");
        $sd_masuk = $request->get("sd_masuk");
        $sd_lulus = $request->get("sd_lulus");
        $sd = $sd_nama . '_-_' . $sd_masuk . '_' . $sd_lulus;

        $smp_nama = $request->get("smp");
        $smp_masuk = $request->get("smp_masuk");
        $smp_lulus = $request->get("smp_lulus");
        $smp = $smp_nama . '_-_' . $smp_masuk . '_' . $smp_lulus;

        $sma_nama = $request->get("sma");
        $sma_jurusan = $request->get("sma_jurusan");
        $sma_masuk = $request->get("sma_masuk");
        $sma_lulus = $request->get("sma_lulus");
        $sma = $sma_nama . '_' . $sma_jurusan . '_' . $sma_masuk . '_' . $sma_lulus;

        $s1_nama = $request->get("s1");
        $s1_jurusan = $request->get("s1_jurusan");
        $s1_masuk = $request->get("s1_masuk");
        $s1_lulus = $request->get("s1_lulus");
        $s1 = $s1_nama . '_' . $s1_jurusan . '_' . $s1_masuk . '_' . $s1_lulus;

        $s2_nama = $request->get("s2");
        $s2_jurusan = $request->get("s2_jurusan");
        $s2_masuk = $request->get("s2_masuk");
        $s2_lulus = $request->get("s2_lulus");
        $s2 = $s2_nama . '_' . $s2_jurusan . '_' . $s2_masuk . '_' . $s2_lulus;

        $s3_nama = $request->get("s3");
        $s3_jurusan = $request->get("s3_jurusan");
        $s3_masuk = $request->get("s3_masuk");
        $s3_lulus = $request->get("s3_lulus");
        $s3 = $s3_nama . '_' . $s3_jurusan . '_' . $s3_masuk . '_' . $s3_lulus;

        $nama_darurat1 = $request->get("nama_darurat1");
        $telp_darurat1 = $request->get("telp_darurat1");
        $pekerjaan_darurat1 = $request->get("pekerjaan_darurat1");
        $hubungan_darurat1 = $request->get("hubungan_darurat1");
        $emergency1 = $nama_darurat1 . '_' . $telp_darurat1 . '_' . $pekerjaan_darurat1 . '_' . $hubungan_darurat1;

        $nama_darurat2 = $request->get("nama_darurat2");
        $telp_darurat2 = $request->get("telp_darurat2");
        $pekerjaan_darurat2 = $request->get("pekerjaan_darurat2");
        $hubungan_darurat2 = $request->get("hubungan_darurat2");
        $emergency2 = $nama_darurat2 . '_' . $telp_darurat2 . '_' . $pekerjaan_darurat2 . '_' . $hubungan_darurat2;

        $nama_darurat3 = $request->get("nama_darurat3");
        $telp_darurat3 = $request->get("telp_darurat3");
        $pekerjaan_darurat3 = $request->get("pekerjaan_darurat3");
        $hubungan_darurat3 = $request->get("hubungan_darurat3");
        $emergency3 = $nama_darurat3 . '_' . $telp_darurat3 . '_' . $pekerjaan_darurat3 . '_' . $hubungan_darurat3;

        try {

            if ($request->hasFile('attach')) {
                $empAtt = EmployeeAttachment::where('employee_id', $employee_id)->get();
                $count = count($empAtt);

                $files = $request->file('attach');
                foreach ($files as $file) {

                    $file_name = $employee_id . '(' . ++$count . ').' . $file->getClientOriginalExtension();
                    $file->move(public_path('employee_files/'), $file_name);

                    $attachment = new EmployeeAttachment([
                        'employee_id' => $employee_id,
                        'file_path' => "/employee_files/" . $file_name,
                        'created_by' => strtoupper(Auth::user()->username),
                    ]);
                    $attachment->save();
                }
            }

            $emp_update = EmployeeUpdate::where('employee_id', '=', strtoupper($employee_id))->first();
            $status_update = 0;

            $update_message = [];

            if (count($emp_update) > 0) {
                if ($emp_update->name != strtoupper($nama_lengkap)) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Nama Lengkap', 'before' => $emp_update->name, 'after' => strtoupper($nama_lengkap)]);
                }

                if ($emp_update->nik != $nik) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'NIK KTP', 'before' => $emp_update->nik, 'after' => $nik]);
                }

                if ($emp_update->npwp != $npwp) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Nomor NPWP', 'before' => $emp_update->npwp, 'after' => $npwp]);
                }
                if ($emp_update->birth_place != strtoupper($tempat_lahir)) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Tempat Lahir', 'before' => $emp_update->birth_place, 'after' => strtoupper($tempat_lahir)]);
                }
                if ($emp_update->birth_date != $tanggal_lahir) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Tanggal Lahir', 'before' => $emp_update->birth_date, 'after' => $tanggal_lahir]);
                }
                if ($emp_update->religion != $agama) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Agama', 'before' => $emp_update->religion, 'after' => $agama]);
                }
                if ($emp_update->mariage_status != $status_perkawinan) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Status Perkawinan', 'before' => $emp_update->mariage_status, 'after' => $status_perkawinan]);
                }
                if ($emp_update->address != strtoupper($alamat_asal)) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Alamat Asal', 'before' => $emp_update->address, 'after' => strtoupper($alamat_asal)]);
                }
                if ($emp_update->current_address != strtoupper($alamat_domisili)) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Alamat Domisili', 'before' => $emp_update->current_address, 'after' => strtoupper($alamat_domisili)]);
                }
                if ($emp_update->telephone != $telepon_rumah) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Nomor Telepon', 'before' => $emp_update->telephone, 'after' => $telepon_rumah]);
                }
                if ($emp_update->handphone != $hp) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Nomor HP', 'before' => $emp_update->handphone, 'after' => $hp]);
                }
                if ($emp_update->email != $email) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Email', 'before' => $emp_update->email, 'after' => $email]);
                }
                if ($emp_update->bpjskes != $bpjskes) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'BPJSKES', 'before' => $emp_update->bpjskes, 'after' => $bpjskes]);
                }
                if ($emp_update->faskes != strtoupper($faskes)) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'Faskes', 'before' => $emp_update->faskes, 'after' => strtoupper($faskes)]);
                }
                if ($emp_update->bpjstk != $bpjstk) {
                    $status_update = 1;
                    array_push($update_message, ['name' => 'BPJSTK', 'before' => $emp_update->bpjstk, 'after' => $bpjstk]);
                }
                if ($emp_update->f_ayah != strtoupper($f_ayah)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_ayah);
                    $usr_after = explode('_', strtoupper($f_ayah));

                    array_push($update_message, [
                        'name' => 'Ayah',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_ibu != strtoupper($f_ibu)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_ibu);
                    $usr_after = explode('_', strtoupper($f_ibu));

                    array_push($update_message, [
                        'name' => 'Ibu',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara1 != strtoupper($f_saudara1)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara1);
                    $usr_after = explode('_', strtoupper($f_saudara1));

                    array_push($update_message, [
                        'name' => 'Saudara 1',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara2 != strtoupper($f_saudara2)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara2);
                    $usr_after = explode('_', strtoupper($f_saudara2));

                    array_push($update_message, [
                        'name' => 'Saudara 2',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara3 != strtoupper($f_saudara3)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara3);
                    $usr_after = explode('_', strtoupper($f_saudara3));

                    array_push($update_message, [
                        'name' => 'Saudara 3',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara4 != strtoupper($f_saudara4)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara4);
                    $usr_after = explode('_', strtoupper($f_saudara4));

                    array_push($update_message, [
                        'name' => 'Saudara 4',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara5 != strtoupper($f_saudara5)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara5);
                    $usr_after = explode('_', strtoupper($f_saudara5));

                    array_push($update_message, [
                        'name' => 'Saudara 5',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara6 != strtoupper($f_saudara6)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara6);
                    $usr_after = explode('_', strtoupper($f_saudara6));

                    array_push($update_message, [
                        'name' => 'Saudara 6',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara7 != strtoupper($f_saudara7)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara7);
                    $usr_after = explode('_', strtoupper($f_saudara7));

                    array_push($update_message, [
                        'name' => 'Saudara 7',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara8 != strtoupper($f_saudara8)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara8);
                    $usr_after = explode('_', strtoupper($f_saudara8));

                    array_push($update_message, [
                        'name' => 'Saudara 8',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara9 != strtoupper($f_saudara9)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara9);
                    $usr_after = explode('_', strtoupper($f_saudara9));

                    array_push($update_message, [
                        'name' => 'Saudara 9',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara10 != strtoupper($f_saudara10)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara10);
                    $usr_after = explode('_', strtoupper($f_saudara10));

                    array_push($update_message, [
                        'name' => 'Saudara 10',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara11 != strtoupper($f_saudara11)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara11);
                    $usr_after = explode('_', strtoupper($f_saudara11));

                    array_push($update_message, [
                        'name' => 'Saudara 11',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->f_saudara12 != strtoupper($f_saudara12)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->f_saudara12);
                    $usr_after = explode('_', strtoupper($f_saudara12));

                    array_push($update_message, [
                        'name' => 'Saudara 12',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_pasangan != strtoupper($m_pasangan)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_pasangan);
                    $usr_after = explode('_', strtoupper($m_pasangan));

                    array_push($update_message, [
                        'name' => 'Suami/Istri',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak1 != strtoupper($m_anak1)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak1);
                    $usr_after = explode('_', strtoupper($m_anak1));

                    array_push($update_message, [
                        'name' => 'Anak 1',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak2 != strtoupper($m_anak2)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak2);
                    $usr_after = explode('_', strtoupper($m_anak2));

                    array_push($update_message, [
                        'name' => 'Anak 2',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak3 != strtoupper($m_anak3)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak3);
                    $usr_after = explode('_', strtoupper($m_anak3));

                    array_push($update_message, [
                        'name' => 'Anak 3',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak4 != strtoupper($m_anak4)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak4);
                    $usr_after = explode('_', strtoupper($m_anak4));

                    array_push($update_message, [
                        'name' => 'Anak 4',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak5 != strtoupper($m_anak5)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak5);
                    $usr_after = explode('_', strtoupper($m_anak5));

                    array_push($update_message, [
                        'name' => 'Anak 5',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak6 != strtoupper($m_anak6)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak6);
                    $usr_after = explode('_', strtoupper($m_anak6));

                    array_push($update_message, [
                        'name' => 'Anak 6',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->m_anak7 != strtoupper($m_anak7)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->m_anak7);
                    $usr_after = explode('_', strtoupper($m_anak7));

                    array_push($update_message, [
                        'name' => 'Anak 7',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>JK : ' . $usr_before[1] . '<br>Tempat Lahir : ' . $usr_before[2] . '<br> Tanggal Lahir : ' . $usr_before[3] . '<br> Pekerjaan : ' . $usr_before[4],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                    ]);
                }
                if ($emp_update->sd != strtoupper($sd)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->sd);
                    $usr_after = explode('_', strtoupper($sd));

                    array_push($update_message, [
                        'name' => 'SD',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->smp != strtoupper($smp)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->smp);
                    $usr_after = explode('_', strtoupper($smp));

                    array_push($update_message, [
                        'name' => 'SLTP',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->sma != strtoupper($sma)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->sma);
                    $usr_after = explode('_', strtoupper($sma));

                    array_push($update_message, [
                        'name' => 'SLTA',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Jurusan : ' . $usr_before[1] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->s1 != strtoupper($s1)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->s1);
                    $usr_after = explode('_', strtoupper($s1));

                    array_push($update_message, [
                        'name' => 'S1',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Jurusan : ' . $usr_before[1] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->s2 != strtoupper($s2)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->s2);
                    $usr_after = explode('_', strtoupper($s2));

                    array_push($update_message, [
                        'name' => 'S2',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Jurusan : ' . $usr_before[1] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->s3 != strtoupper($s3)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->s3);
                    $usr_after = explode('_', strtoupper($s3));

                    array_push($update_message, [
                        'name' => 'S3',
                        'before' => 'Nama Lembaga : ' . $usr_before[0] . '<br>Jurusan : ' . $usr_before[1] . '<br>Tahun Masuk : ' . $usr_before[2] . '<br>Tahun Lulus : ' . $usr_before[3],
                        'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->emergency1 != strtoupper($emergency1)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->emergency1);
                    $usr_after = explode('_', strtoupper($emergency1));

                    array_push($update_message, [
                        'name' => 'Nomor Darurat 1',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>Nomor Telepon : ' . $usr_before[1] . '<br>Pekerjaan : ' . $usr_before[2] . '<br>Hubungan : ' . $usr_before[3],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->emergency2 != strtoupper($emergency2)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->emergency2);
                    $usr_after = explode('_', strtoupper($emergency2));

                    array_push($update_message, [
                        'name' => 'Nomor Darurat 2',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>Nomor Telepon : ' . $usr_before[1] . '<br>Pekerjaan : ' . $usr_before[2] . '<br>Hubungan : ' . $usr_before[3],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                    ]);
                }
                if ($emp_update->emergency3 != strtoupper($emergency3)) {
                    $status_update = 1;
                    $usr_before = explode('_', $emp_update->emergency3);
                    $usr_after = explode('_', strtoupper($emergency3));

                    array_push($update_message, [
                        'name' => 'Nomor Darurat 3',
                        'before' => 'Nama : ' . $usr_before[0] . '<br>Nomor Telepon : ' . $usr_before[1] . '<br>Pekerjaan : ' . $usr_before[2] . '<br>Hubungan : ' . $usr_before[3],
                        'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                    ]);
                }
            } else {
                $status_update = 1;
                array_push($update_message, ['name' => 'Nama Lengkap', 'before' => '-', 'after' => strtoupper($nama_lengkap)]);
                array_push($update_message, ['name' => 'NIK KTP', 'before' => '-', 'after' => $nik]);
                array_push($update_message, ['name' => 'Nomor NPWP', 'before' => '-', 'after' => $npwp]);
                array_push($update_message, ['name' => 'Tempat Lahir', 'before' => '-', 'after' => strtoupper($tempat_lahir)]);
                array_push($update_message, ['name' => 'Tanggal Lahir', 'before' => '-', 'after' => $tanggal_lahir]);
                array_push($update_message, ['name' => 'Agama', 'before' => '-', 'after' => $agama]);
                array_push($update_message, ['name' => 'Status Perkawinan', 'before' => '-', 'after' => $status_perkawinan]);
                array_push($update_message, ['name' => 'Alamat Asal', 'before' => '-', 'after' => strtoupper($alamat_asal)]);
                array_push($update_message, ['name' => 'Alamat Domisili', 'before' => '-', 'after' => strtoupper($alamat_domisili)]);
                array_push($update_message, ['name' => 'Nomor Telepon', 'before' => '-', 'after' => $telepon_rumah]);
                array_push($update_message, ['name' => 'Nomor HP', 'before' => '-', 'after' => $hp]);
                array_push($update_message, ['name' => 'Email', 'before' => '-', 'after' => $email]);
                array_push($update_message, ['name' => 'BPJSKES', 'before' => '-', 'after' => $bpjskes]);
                array_push($update_message, ['name' => 'Faskes', 'before' => '-', 'after' => strtoupper($faskes)]);
                array_push($update_message, ['name' => 'BPJSTK', 'before' => '-', 'after' => $bpjstk]);
                $usr_after = explode('_', strtoupper($f_ayah));

                array_push($update_message, [
                    'name' => 'Ayah',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_ibu));

                array_push($update_message, [
                    'name' => 'Ibu',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara1));

                array_push($update_message, [
                    'name' => 'Saudara 1',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara2));

                array_push($update_message, [
                    'name' => 'Saudara 2',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara3));

                array_push($update_message, [
                    'name' => 'Saudara 3',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara4));

                array_push($update_message, [
                    'name' => 'Saudara 4',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara5));

                array_push($update_message, [
                    'name' => 'Saudara 5',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara6));

                array_push($update_message, [
                    'name' => 'Saudara 6',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara7));

                array_push($update_message, [
                    'name' => 'Saudara 7',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara8));

                array_push($update_message, [
                    'name' => 'Saudara 8',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara9));

                array_push($update_message, [
                    'name' => 'Saudara 9',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara10));

                array_push($update_message, [
                    'name' => 'Saudara 10',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara11));

                array_push($update_message, [
                    'name' => 'Saudara 11',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($f_saudara12));

                array_push($update_message, [
                    'name' => 'Saudara 12',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_pasangan));

                array_push($update_message, [
                    'name' => 'Suami/Istri',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak1));

                array_push($update_message, [
                    'name' => 'Anak 1',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak2));

                array_push($update_message, [
                    'name' => 'Anak 2',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak3));

                array_push($update_message, [
                    'name' => 'Anak 3',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak4));

                array_push($update_message, [
                    'name' => 'Anak 4',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak5));

                array_push($update_message, [
                    'name' => 'Anak 5',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak6));

                array_push($update_message, [
                    'name' => 'Anak 6',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($m_anak7));

                array_push($update_message, [
                    'name' => 'Anak 7',
                    'before' => 'Nama : -<br>JK : -<br>Tempat Lahir : -<br> Tanggal Lahir : -<br> Pekerjaan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>JK : ' . $usr_after[1] . '<br>Tempat Lahir : ' . $usr_after[2] . '<br> Tanggal Lahir : ' . $usr_after[3] . '<br> Pekerjaan : ' . $usr_after[4],
                ]);
                $usr_after = explode('_', strtoupper($sd));

                array_push($update_message, [
                    'name' => 'SD',
                    'before' => 'Nama Lembaga : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);

                $usr_after = explode('_', strtoupper($smp));

                array_push($update_message, [
                    'name' => 'SLTP',
                    'before' => 'Nama Lembaga : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);

                $usr_after = explode('_', strtoupper($sma));

                array_push($update_message, [
                    'name' => 'SLTA',
                    'before' => 'Nama Lembaga : -<br>Jurusan : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);
                $usr_after = explode('_', strtoupper($s1));

                array_push($update_message, [
                    'name' => 'S1',
                    'before' => 'Nama Lembaga : -<br>Jurusan : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);

                $usr_after = explode('_', strtoupper($s2));
                array_push($update_message, [
                    'name' => 'S2',
                    'before' => 'Nama Lembaga : -<br>Jurusan : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);

                $usr_after = explode('_', strtoupper($s3));
                array_push($update_message, [
                    'name' => 'S3',
                    'before' => 'Nama Lembaga : -<br>Jurusan : -<br>Tahun Masuk : -<br>Tahun Lulus : -',
                    'after' => 'Nama Lembaga : ' . $usr_after[0] . '<br>Jurusan : ' . $usr_after[1] . '<br>Tahun Masuk : ' . $usr_after[2] . '<br>Tahun Lulus : ' . $usr_after[3],
                ]);

                $usr_after = explode('_', strtoupper($emergency1));
                array_push($update_message, [
                    'name' => 'Nomor Darurat 1',
                    'before' => 'Nama : -<br>Nomor Telepon : -<br>Pekerjaan : -<br>Hubungan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                ]);
                $usr_after = explode('_', strtoupper($emergency2));

                array_push($update_message, [
                    'name' => 'Nomor Darurat 2',
                    'before' => 'Nama : -<br>Nomor Telepon : -<br>Pekerjaan : -<br>Hubungan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                ]);
                $usr_after = explode('_', strtoupper($emergency3));
                array_push($update_message, [
                    'name' => 'Nomor Darurat 3',
                    'before' => 'Nama : -<br>Nomor Telepon : -<br>Pekerjaan : -<br>Hubungan : -',
                    'after' => 'Nama : ' . $usr_after[0] . '<br>Nomor Telepon : ' . $usr_after[1] . '<br>Pekerjaan : ' . $usr_after[2] . '<br>Hubungan : ' . $usr_after[3],
                ]);
            }

            $update = EmployeeUpdate::updateOrCreate(
                [
                    'employee_id' => strtoupper($employee_id),
                ],
                [
                    'name' => strtoupper($nama_lengkap),
                    'nik' => $nik,
                    'npwp' => $npwp,
                    'birth_place' => strtoupper($tempat_lahir),
                    'birth_date' => $tanggal_lahir,
                    'gender' => $jenis_kelamin,
                    'religion' => $agama,
                    'mariage_status' => $status_perkawinan,
                    'address' => strtoupper($alamat_asal),
                    'current_address' => strtoupper($alamat_domisili),
                    'telephone' => $telepon_rumah,
                    'handphone' => $hp,
                    'email' => $email,
                    'bpjskes' => $bpjskes,
                    'faskes' => strtoupper($faskes),
                    'bpjstk' => $bpjstk,
                    'f_ayah' => strtoupper($f_ayah),
                    'f_ibu' => strtoupper($f_ibu),
                    'f_saudara1' => strtoupper($f_saudara1),
                    'f_saudara2' => strtoupper($f_saudara2),
                    'f_saudara3' => strtoupper($f_saudara3),
                    'f_saudara4' => strtoupper($f_saudara4),
                    'f_saudara5' => strtoupper($f_saudara5),
                    'f_saudara6' => strtoupper($f_saudara6),
                    'f_saudara7' => strtoupper($f_saudara7),
                    'f_saudara8' => strtoupper($f_saudara8),
                    'f_saudara9' => strtoupper($f_saudara9),
                    'f_saudara10' => strtoupper($f_saudara10),
                    'f_saudara11' => strtoupper($f_saudara11),
                    'f_saudara12' => strtoupper($f_saudara12),
                    'm_pasangan' => strtoupper($m_pasangan),
                    'm_anak1' => strtoupper($m_anak1),
                    'm_anak2' => strtoupper($m_anak2),
                    'm_anak3' => strtoupper($m_anak3),
                    'm_anak4' => strtoupper($m_anak4),
                    'm_anak5' => strtoupper($m_anak5),
                    'm_anak6' => strtoupper($m_anak6),
                    'm_anak7' => strtoupper($m_anak7),
                    'sd' => strtoupper($sd),
                    'smp' => strtoupper($smp),
                    'sma' => strtoupper($sma),
                    's1' => strtoupper($s1),
                    's2' => strtoupper($s2),
                    's3' => strtoupper($s3),
                    'emergency1' => strtoupper($emergency1),
                    'emergency2' => strtoupper($emergency2),
                    'emergency3' => strtoupper($emergency3),
                    'created_by' => strtoupper(Auth::user()->username),
                    'updated_at' => Carbon::now(),
                ]
            );

            $update->save();

            $data_email = array(
                'employee' => $update_message,
                'employee_id' => strtoupper($employee_id),
                'name' => strtoupper($nama_lengkap),
            );

            if ($status_update == 1) {
                Mail::to(['ummi.ernawati@music.yamaha.com', 'achmad.riski.bayu@music.yamaha.com'])->bcc('ympi-mis-ML@music.yamaha.com')->send(new SendEmail($data_email, 'update_employee'));
            }

            $response = array(
                'status' => true,
                'message' => 'Update data karyawan berhasil',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchEmpData(Request $request)
    {

        $data = EmployeeUpdate::orderBy('name', 'ASC')->get();
        // $data = EmployeeSync::where('end_date', '=', null)->where('employee_id', 'like', '%PI%')->orderBy('employee_id', 'ASC')->get();
        $data = EmployeeSync::where('end_date', '=', null)->orderBy('employee_id', 'ASC')->get();

        return DataTables::of($data)->make(true);
        //      $resumes = db::select('select * from employee_syncs where end_date is null and employee_id like "%PI%" order by employee_id asc');

        //      $response = array(
        //      'status'        => true,
        //      'resumes'       =>$resumes
        //  );
        //    return Response::json($response);
    }

    public function fetchExcelEmpData(Request $request)
    {

        $emp = EmployeeUpdate::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'employee_updates.employee_id')
            ->whereNotNull('employee_updates.employee_id')
            ->whereNull('employee_syncs.end_date')
            ->orderBy('employee_updates.name', 'ASC')
            ->get();

        $data = array(
            'emp' => $emp,
        );

        // return view('employees.report.employee_data_excel', $data);

        ob_clean();
        Excel::create('Employee_Data_(' . date('ymd H-i') . ')', function ($excel) use ($data) {
            $excel->sheet('Employee Data', function ($sheet) use ($data) {
                return $sheet->loadView('employees.report.employee_data_excel', $data);
            });
        })->export('xlsx');

    }

    public function indexEmpDataPajak($employee_id)
    {

        $title = 'Employee Tax Services';
        $title_jp = '従業員税金サービス';

        return view('employees.service.perpajakanData', array(
            'employee_id' => $employee_id,
            'title' => $title,
            'title_jp' => $title_jp,
        )
        );
    }

    public function fetchFillPerpajakanData(Request $request)
    {
        $employee_id = $request->get('emp_id');

        $data = EmployeeTax::where('employee_id', $employee_id)
            ->select('employee_taxes.*', db::raw('DATE_FORMAT(tanggal_lahir,"%d-%m-%Y") AS tgl_lahir'))
            ->first();

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchUpdatePerpajakanData(Request $request)
    {

        $employee_id = $request->get('employee_id');
        $nama_lengkap = $request->get('nama_lengkap');
        $nik = $request->get('nik');
        $tempat_lahir = $request->get('tempat_lahir');
        $tanggal_lahir = date('Y-m-d', strtotime($request->get('tanggal_lahir')));
        $jenis_kelamin = $request->get('jenis_kelamin');

        $jalan = $request->get('jalan');
        $rtrw = $request->get('rtrw');
        $kelurahan = $request->get('kelurahan');
        $kecamatan = $request->get('kecamatan');
        $kota = $request->get('kota');

        $status_perkawinan = $request->get('status_perkawinan');

        $tanggal_nikah = $request->get("tanggal_nikah");
        $nama_istri = $request->get("nama_istri");
        $tanggal_lahir_istri = $request->get("tanggal_lahir_istri");
        $pekerjaan_istri = $request->get("pekerjaan_istri");
        $istri = $tanggal_nikah . '_' . $nama_istri . '_' . $tanggal_lahir_istri . '_' . $pekerjaan_istri;

        $nama_anak1 = $request->get("nama_anak1");
        $kelamin_anak1 = $request->get("kelamin_anak1");
        $tempat_lahir_anak1 = $request->get("tempat_lahir_anak1");
        $tanggal_lahir_anak1 = $request->get("tanggal_lahir_anak1");
        $status_anak1 = $request->get("status_anak1");
        $anak1 = $nama_anak1 . '_' . $kelamin_anak1 . '_' . $tempat_lahir_anak1 . '_' . $tanggal_lahir_anak1 . '_' . $status_anak1;

        $nama_anak2 = $request->get("nama_anak2");
        $kelamin_anak2 = $request->get("kelamin_anak2");
        $tempat_lahir_anak2 = $request->get("tempat_lahir_anak2");
        $tanggal_lahir_anak2 = $request->get("tanggal_lahir_anak2");
        $status_anak2 = $request->get("status_anak2");
        $anak2 = $nama_anak2 . '_' . $kelamin_anak2 . '_' . $tempat_lahir_anak2 . '_' . $tanggal_lahir_anak2 . '_' . $status_anak2;

        $nama_anak3 = $request->get("nama_anak3");
        $kelamin_anak3 = $request->get("kelamin_anak3");
        $tempat_lahir_anak3 = $request->get("tempat_lahir_anak3");
        $tanggal_lahir_anak3 = $request->get("tanggal_lahir_anak3");
        $status_anak3 = $request->get("status_anak3");
        $anak3 = $nama_anak3 . '_' . $kelamin_anak3 . '_' . $tempat_lahir_anak3 . '_' . $tanggal_lahir_anak3 . '_' . $status_anak3;

        $npwp_kepemilikan = $request->get('npwp_kepemilikan');
        $npwp_status = $request->get('npwp_status');
        $npwp_nama = $request->get('npwp_nama');
        $npwp_nomor = $request->get('npwp_nomor');
        $npwp_alamat = $request->get('npwp_alamat');
        $npwp_change_status = $request->get('npwp_change_status');

        if ($npwp_change_status == null || $npwp_change_status == '' || $npwp_change_status == 'Tidak Ada') {
            $npwp_nama_change = null;
            $npwp_nomor_change = null;
            $npwp_alamat_change = null;
        } else {
            $npwp_nama_change = $request->get('npwp_nama');
            $npwp_nomor_change = $request->get('npwp_nomor');
            $npwp_alamat_change = $request->get('npwp_alamat');
        }

        try {

            $files = array();
            $file = new EmployeeTax();

            if ($request->hasFile('attach')) {
                $files = $request->file('attach');
                $nomor = 1;
                foreach ($files as $file) {
                    $file_name = $employee_id . '(' . $nomor . ')_' . $nama_lengkap . '_NPWP.' . $file->getClientOriginalExtension();
                    $file->move(public_path('tax_files/'), $file_name);

                    $data[] = $file_name;
                    $nomor++;
                }

                $file->filename = json_encode($data);

                if ($npwp_change_status == null || $npwp_change_status == '' || $npwp_change_status == 'Tidak Ada') {
                    $update = EmployeeTax::updateOrCreate(
                        [
                            'employee_id' => strtoupper($employee_id),
                        ],
                        [
                            'nama' => strtoupper($nama_lengkap),
                            'nik' => strtoupper($nik),
                            'tempat_lahir' => strtoupper($tempat_lahir),
                            'tanggal_lahir' => strtoupper($tanggal_lahir),
                            'jenis_kelamin' => strtoupper($jenis_kelamin),
                            'jalan' => strtoupper($jalan),
                            'rtrw' => strtoupper($rtrw),
                            'kelurahan' => strtoupper($kelurahan),
                            'kecamatan' => strtoupper($kecamatan),
                            'kota' => strtoupper($kota),
                            'status_perkawinan' => strtoupper($status_perkawinan),
                            'istri' => strtoupper($istri),
                            'anak1' => strtoupper($anak1),
                            'anak2' => strtoupper($anak2),
                            'anak3' => strtoupper($anak3),
                            'npwp_kepemilikan' => strtoupper($npwp_kepemilikan),
                            'npwp_status' => strtoupper($npwp_status),
                            'npwp_nama' => strtoupper($npwp_nama),
                            'npwp_nomor' => strtoupper($npwp_nomor),
                            'npwp_alamat' => strtoupper($npwp_alamat),
                            'npwp_file' => $file->filename,
                            'created_by' => strtoupper(Auth::user()->username),
                            'npwp_change_status' => strtoupper($npwp_change_status),
                            'status' => 'CONFIRMED',
                        ]
                    );

                } else {
                    $update = EmployeeTax::updateOrCreate(
                        [
                            'employee_id' => strtoupper($employee_id),
                        ],
                        [
                            'nama' => strtoupper($nama_lengkap),
                            'nik' => strtoupper($nik),
                            'tempat_lahir' => strtoupper($tempat_lahir),
                            'tanggal_lahir' => strtoupper($tanggal_lahir),
                            'jenis_kelamin' => strtoupper($jenis_kelamin),
                            'jalan' => strtoupper($jalan),
                            'rtrw' => strtoupper($rtrw),
                            'kelurahan' => strtoupper($kelurahan),
                            'kecamatan' => strtoupper($kecamatan),
                            'kota' => strtoupper($kota),
                            'status_perkawinan' => strtoupper($status_perkawinan),
                            'istri' => strtoupper($istri),
                            'anak1' => strtoupper($anak1),
                            'anak2' => strtoupper($anak2),
                            'anak3' => strtoupper($anak3),
                            'npwp_kepemilikan' => strtoupper($npwp_kepemilikan),
                            'npwp_status' => strtoupper($npwp_status),
                            'created_by' => strtoupper(Auth::user()->username),
                            'status' => 'CONFIRMED',
                            'npwp_change_status' => strtoupper($npwp_change_status),
                            'npwp_nama_change' => strtoupper($npwp_nama_change),
                            'npwp_nomor_change' => strtoupper($npwp_nomor_change),
                            'npwp_alamat_change' => strtoupper($npwp_alamat_change),
                        ]
                    );
                }
                $update->save();
            } else {

                if ($npwp_change_status == null || $npwp_change_status == '' || $npwp_change_status == 'Tidak Ada') {

                    $update = EmployeeTax::updateOrCreate(
                        [
                            'employee_id' => strtoupper($employee_id),
                        ],
                        [
                            'nama' => strtoupper($nama_lengkap),
                            'nik' => strtoupper($nik),
                            'tempat_lahir' => strtoupper($tempat_lahir),
                            'tanggal_lahir' => strtoupper($tanggal_lahir),
                            'jenis_kelamin' => strtoupper($jenis_kelamin),
                            'jalan' => strtoupper($jalan),
                            'rtrw' => strtoupper($rtrw),
                            'kelurahan' => strtoupper($kelurahan),
                            'kecamatan' => strtoupper($kecamatan),
                            'kota' => strtoupper($kota),
                            'status_perkawinan' => strtoupper($status_perkawinan),
                            'istri' => strtoupper($istri),
                            'anak1' => strtoupper($anak1),
                            'anak2' => strtoupper($anak2),
                            'anak3' => strtoupper($anak3),
                            'npwp_kepemilikan' => strtoupper($npwp_kepemilikan),
                            'npwp_status' => strtoupper($npwp_status),
                            'npwp_nama' => strtoupper($npwp_nama),
                            'npwp_nomor' => strtoupper($npwp_nomor),
                            'npwp_alamat' => strtoupper($npwp_alamat),
                            'created_by' => strtoupper(Auth::user()->username),
                            'npwp_change_status' => strtoupper($npwp_change_status),
                            'status' => 'CONFIRMED',
                        ]
                    );
                } else {
                    $update = EmployeeTax::updateOrCreate(
                        [
                            'employee_id' => strtoupper($employee_id),
                        ],
                        [
                            'nama' => strtoupper($nama_lengkap),
                            'nik' => strtoupper($nik),
                            'tempat_lahir' => strtoupper($tempat_lahir),
                            'tanggal_lahir' => strtoupper($tanggal_lahir),
                            'jenis_kelamin' => strtoupper($jenis_kelamin),
                            'jalan' => strtoupper($jalan),
                            'rtrw' => strtoupper($rtrw),
                            'kelurahan' => strtoupper($kelurahan),
                            'kecamatan' => strtoupper($kecamatan),
                            'kota' => strtoupper($kota),
                            'status_perkawinan' => strtoupper($status_perkawinan),
                            'istri' => strtoupper($istri),
                            'anak1' => strtoupper($anak1),
                            'anak2' => strtoupper($anak2),
                            'anak3' => strtoupper($anak3),
                            'npwp_kepemilikan' => strtoupper($npwp_kepemilikan),
                            'npwp_status' => strtoupper($npwp_status),
                            'created_by' => strtoupper(Auth::user()->username),
                            'status' => 'CONFIRMED',
                            'npwp_change_status' => strtoupper($npwp_change_status),
                            'npwp_nama_change' => strtoupper($npwp_nama_change),
                            'npwp_nomor_change' => strtoupper($npwp_nomor_change),
                            'npwp_alamat_change' => strtoupper($npwp_alamat_change),
                        ]
                    );
                }
                $update->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Update data karyawan berhasil',
            );
            return Response::json($response);

        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexResumePajak()
    {
        $title = 'Resume Pengisian Data NPWP';
        $title_jp = '納税義務者番号データのまとめ';

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        return view('employees.report.resume_pajak', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employee' => $emp,
        )
        )->with('page', 'Resume Pengisian Data NPWP')->with('head', 'Resume Pengisian Data NPWP');
    }

    public function fetchResumePajak(Request $request)
    {
        try {
            $pajak = DB::SELECT("
                SELECT
                SUM( a.count_sudah ) AS sudah,
                SUM( a.count_belum ) AS belum,
                a.department,
                COALESCE ( departments.department_shortname, 'MNGT' ) AS department_shortname
                FROM
                (
                SELECT
                count( employee_taxes.employee_id ) AS count_sudah,
                0 AS count_belum,
                COALESCE ( department, '' ) AS department
                FROM
                employee_taxes
                JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                WHERE
                `status` = 'CONFIRMED'
                GROUP BY
                department

                UNION ALL

                SELECT
                0 AS count_sudah,
                count( employee_syncs.employee_id ) AS count_belum,
                COALESCE ( department, '' ) AS department
                FROM
                employee_taxes
                JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                WHERE
                `status` = 'UNCONFIRMED'
                AND employee_syncs.end_date IS NULL
                GROUP BY
                department

                UNION ALL

                SELECT
                0 AS count_sudah,
                count( employee_syncs.employee_id ) AS count_belum,
                COALESCE ( department, '' ) AS department
                FROM
                employee_taxes
                RIGHT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                WHERE
                employee_taxes.employee_id IS NULL
                AND employee_syncs.end_date IS NULL
                GROUP BY
                department
                ) a
                LEFT JOIN departments ON a.department = departments.department_name
                GROUP BY
                a.department,
                departments.department_shortname"
            );

            $response = array(
                'status' => true,
                'pajak' => $pajak,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchResumePajakDetail(Request $request)
    {
        try {
            $status = $request->get('status');
            $dept = $request->get('dept');

            if ($dept == "MNGT") {
                if ($status == "Belum") {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      'Management' AS department
                      FROM
                      employee_taxes
                      RIGHT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      WHERE
                      department IS NULL
                      AND employee_taxes.employee_id IS NULL
                      AND employee_syncs.end_date IS NULL
                      AND `status` IS NULL

                      UNION ALL

                      SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      'Management' AS department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      WHERE
                      department IS NULL
                      AND employee_syncs.end_date IS NULL
                      AND `status` = 'UNCONFIRMED'
                      ");
                } else {
                    $pajak = DB::SELECT("
                      SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      'Management' AS department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      WHERE
                      department IS NULL
                      AND employee_syncs.end_date IS NULL
                      AND `status` = 'CONFIRMED'");
                }
            } else {
                if ($status == "Belum") {
                    $pajak = DB::SELECT("
                      SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      COALESCE(department_shortname,'') as department
                      FROM
                      employee_taxes
                      RIGHT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      join departments on department_name = employee_syncs.department
                      WHERE
                      department_shortname = '" . $dept . "'
                      and employee_taxes.employee_id is null
                      and employee_syncs.end_date is null

                      UNION ALL

                      SELECT
                      employee_syncs.employee_id,
                      employee_syncs.NAME,
                      COALESCE ( department_shortname, '' ) AS department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      JOIN departments ON department_name = employee_syncs.department
                      WHERE
                      department_shortname = '" . $dept . "'
                      AND employee_syncs.end_date IS NULL
                      AND `status` = 'UNCONFIRMED'
                      ");
                } else {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      COALESCE(department_shortname,'') as department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      join departments on department_name = employee_syncs.department
                      WHERE
                      department_shortname = '" . $dept . "'
                      and employee_syncs.end_date is null
                      AND `status` = 'CONFIRMED'");
                }
            }

            $response = array(
                'status' => true,
                'pajak' => $pajak,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchResumePajakDetailOld(Request $request)
    {
        try {
            $status = $request->get('status');
            $dept = $request->get('dept');

            if ($dept == "") {
                if ($status == "Belum") {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      '' as department
                      FROM
                      employee_taxes
                      RIGHT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      WHERE
                      department IS NULL
                      and employee_taxes.employee_id is null
                      and employee_syncs.end_date is null");
                } else {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      '' as department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      WHERE
                      department IS NULL
                      and employee_syncs.end_date is null");
                }
            } else {
                if ($status == "Belum") {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      COALESCE(department_shortname,'') as department
                      FROM
                      employee_taxes
                      RIGHT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      join departments on department_name = employee_syncs.department
                      WHERE
                      department_shortname = '" . $dept . "'
                      and employee_taxes.employee_id is null
                      and employee_syncs.end_date is null");
                } else {
                    $pajak = DB::SELECT("SELECT
                      employee_syncs.employee_id,
                      employee_syncs.name,
                      COALESCE(department_shortname,'') as department
                      FROM
                      employee_taxes
                      LEFT JOIN employee_syncs ON employee_syncs.employee_id = employee_taxes.employee_id
                      join departments on department_name = employee_syncs.department
                      WHERE
                      department_shortname = '" . $dept . "'
                      and employee_syncs.end_date is null");
                }
            }

            $response = array(
                'status' => true,
                'pajak' => $pajak,
            );
            return Response::json($response);
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function exportDataPajak(Request $request)
    {

        $time = date('d-m-Y H;i;s');
        $npwp_detail = db::select("SELECT * from employee_taxes order by id asc");

        $data = array(
            'npwp_detail' => $npwp_detail,
        );

        ob_clean();

        Excel::create('Data NPWP ' . $time, function ($excel) use ($data) {
            $excel->sheet('Data', function ($sheet) use ($data) {
                return $sheet->loadView('employees.report.resume_pajak_excel', $data);
            });
        })->export('xlsx');
    }

    public function fetchEmployeeResume(Request $request)
    {

        $tanggal = "";
        $addcostcenter = "";
        $adddepartment = "";
        $addsection = "";
        $addgrup = "";
        $addnik = "";

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-01', strtotime($request->get('datefrom')));
            $tanggal = "and format(A.shiftstarttime, 'yyyy-MM-dd') >= '" . $datefrom . "' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-t', strtotime($request->get('dateto')));
                if ($dateto > date('Y-m-d')) {
                    $dateto = date('Y-m-d', strtotime('-1 day'));
                }
                $tanggal = $tanggal . "and format(A.shiftstarttime, 'yyyy-MM-dd') <= '" . $dateto . "' ";
            }
        }

        if ($request->get('cost_center_code') != null) {
            $costcenter = implode(",", $request->get('cost_center_code'));
            $addcostcenter = "and bagian.cost_center in (" . $costcenter . ") ";
        }

        if ($request->get('department') != null) {
            $departments = $request->get('department');
            $deptlength = count($departments);
            $department = "";

            for ($x = 0; $x < $deptlength; $x++) {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != $deptlength - 1) {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and B.Department in (" . $department . ") ";
        }

        if ($request->get('section') != null) {
            $sections = $request->get('section');
            $sectlength = count($sections);
            $section = "";

            for ($x = 0; $x < $sectlength; $x++) {
                $section = $section . "'" . $sections[$x] . "'";
                if ($x != $sectlength - 1) {
                    $section = $section . ",";
                }
            }
            $addsection = "and bagian.section in (" . $section . ") ";
        }

        if ($request->get('group') != null) {
            $groups = $request->get('group');
            $grplen = count($groups);
            $group = "";

            for ($x = 0; $x < $grplen; $x++) {
                $group = $group . "'" . $groups[$x] . "'";
                if ($x != $grplen - 1) {
                    $group = $group . ",";
                }
            }
            $addgrup = "and bagian.group in (" . $group . ") ";
        }

        if ($request->get('employee_id') != null) {
            $niks = $request->get('employee_id');
            $niklen = count($niks);
            $nik = "";

            for ($x = 0; $x < $niklen; $x++) {
                $nik = $nik . "'" . $niks[$x] . "'";
                if ($x != $niklen - 1) {
                    $nik = $nik . ",";
                }
            }
            $addnik = "and A.Emp_no in (" . $nik . ") ";
        }

        $presences = db::connection('sunfish')->select("SELECT
          A.Emp_no,
          B.Full_name,
          B.Department,
          format ( A.shiftstarttime, 'yyyy-MM' ) AS orderer,
          format ( A.shiftstarttime, 'MMMM yyyy' ) AS periode,
          COUNT (
          IIF ( A.Attend_Code LIKE '%Mangkir%', 1, NULL )) AS mangkir,
          COUNT (
          IIF ( A.Attend_Code LIKE '%CK%' OR A.Attend_Code LIKE '%CUTI%' OR A.Attend_Code LIKE '%UPL%', 1, NULL )) AS cuti,
          COUNT (
          IIF ( A.Attend_Code LIKE '%Izin%', 1, NULL )) AS izin,
          COUNT (
          IIF ( A.Attend_Code LIKE '%SAKIT%', 1, NULL )) AS sakit,
          COUNT (
          IIF ( A.Attend_Code LIKE '%LTI%' OR A.Attend_Code LIKE '%TELAT%', 1, NULL )) AS terlambat,
          COUNT (
          IIF ( A.Attend_Code LIKE '%PC%', 1, NULL )) AS pulang_cepat,
          COUNT (
          IIF (
          A.Attend_Code LIKE '%ABS%'
          OR A.Attend_Code LIKE '%CK10%'
          OR A.Attend_Code LIKE '%CK11%'
          OR A.Attend_Code LIKE '%CK12%'
          OR A.Attend_Code LIKE '%CK8%'
          OR A.Attend_Code LIKE '%Izin%'
          OR A.Attend_Code LIKE '%Mangkir%'
          OR A.Attend_Code LIKE '%PC%'
          OR A.Attend_Code LIKE '%SAKIT%'
          OR A.Attend_Code LIKE '%UPL%'
          OR A.Attend_Code LIKE '%LTI%'
          OR A.Attend_Code LIKE '%TELAT%',
          1,
          NULL
          )) AS tunjangan,
          ISNULL(SUM (  A.total_ot / 60.0 ),0) AS overtime
          FROM
          VIEW_YMPI_Emp_Attendance AS A
          left join VIEW_YMPI_Emp_OrgUnit as B on B.Emp_no = A.Emp_no
          WHERE
          A.Emp_no IS NOT NULL
          " . $tanggal . "
          " . $addnik . "
          " . $adddepartment . "
          GROUP BY
          format ( A.shiftstarttime, 'MMMM yyyy' ),
          format ( A.shiftstarttime, 'yyyy-MM' ),
          A.Emp_no,
          B.Full_name,
          B.Department
          ORDER BY
          A.Emp_no asc,
          orderer ASC");

        $response = array(
            'status' => true,
            'presences' => $presences,
        );
        return Response::json($response);
    }

    public function indexTotalMeeting()
    {
        $title_jp = "トータルミーティング";
        $title = "Total Meeting";
        return view('employees.report.total_meeting', array(
            'title' => $title,
            'title_jp' => $title_jp,
        )
        )->with('page', 'Total Meeting');
    }

    public function indexTermination()
    {
        return view('employees.master.termination', array(
            'status' => $this->status,
        )
        )->with('page', 'Termination')->with('head', 'Employees Data');
    }

    public function indexEmployeeInformation2()
    {
        return view('employees.index_employee_information');
    }

    public function indexKaizenAssessment($id)
    {
        return view('employees.service.kaizenDetail', array(
            'title' => 'e-Kaizen Verification',
            'title_jp' => '??',
        )
        )->with('page', 'Kaizen');
    }

    public function indexKaizenData()
    {
        $dept = EmployeeSync::whereNotNull('department')->select('department')->groupBy('department')->get();
        $sec = EmployeeSync::whereNotNull('section')->select('section')->groupBy('section')->get();

        return view('employees.service.kaizenData', array(
            'depts' => $dept,
            'secs' => $sec,
            'title' => 'e-Kaizen Datas',
            'title_jp' => '',
        )
        )->with('page', 'Kaizen');
    }

    public function indexKaizenDataResume()
    {
        $dept = EmployeeSync::whereNotNull('department')->select('department')->groupBy('department')->get();
        $sec = EmployeeSync::whereNotNull('section')->select('section')->groupBy('section')->get();

        return view('employees.service.kaizenResume', array(
            'depts' => $dept,
            'secs' => $sec,
            'title' => 'e-Kaizen Datas',
            'title_jp' => '',
        )
        )->with('page', 'Kaizen');
    }

    public function attendanceData()
    {
        $title = 'Attendance Data';
        $title_jp = '出席データ';
        $attend_codes = $this->attend;

        $shifts = DB::SELECT("SELECT DISTINCT
          ( shiftdaily_code )
          FROM
          sunfish_shift_syncs
          ORDER BY
          shiftdaily_code");

        $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

        $datas = db::select($q);

        return view('employees.report.attendance_data', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'datas' => $datas,
            'attend_codes' => $attend_codes,
            'shifts' => $shifts,
        )
        );
    }

    public function checklogData()
    {
        $title = 'Checklog Data';
        $title_jp = 'チェックログのデータ';

        $q = "select employee_syncs.employee_id, employee_syncs.name, employee_syncs.department, employee_syncs.`section`, employee_syncs.`group`, employee_syncs.cost_center, cost_centers2.cost_center_name from employee_syncs left join cost_centers2 on cost_centers2.cost_center = employee_syncs.cost_center";

        $datas = db::select($q);

        return view('employees.report.checklog_data', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'datas' => $datas,
        )
        );
    }

    public function getNotif()
    {
        $ntf = HrQuestionLog::select(db::raw("SUM(remark) as ntf"))->first();
        return $ntf->ntf;
    }

    public function indexHRQA()
    {
        $q_question = "SELECT
        category,
        SUM(
        IF
        ( remark = 1, 1, 0 )) AS unanswer
        FROM
        hr_question_logs
        GROUP BY
        category
        ORDER BY
        category ASC";

        $question = DB::select($q_question);

        return view('employees.master.hrquestion', array(
            'title' => 'HR Question & Answer',
            'title_jp' => '??',
            'all_question' => $question,
        )
        )->with('page', 'qna');
    }

    public function indexHRQAResume(Request $request)
    {
        return view('employees.master.index_hrqa_resume', array(
            'title' => 'HR Question Monitoring',
            'title_jp' => 'HR 質問監視',
        )
        )->with('page', 'qna');
    }

    public function fetchHRQAResume(Request $request)
    {
        try {
            $datas = db::select('SELECT masters.mon, masters.category, IFNULL(jml,0) jmls from
                (SELECT * from
                (SELECT DATE_FORMAT(week_date,"%M") as mon from weekly_calendars
                where DATE_FORMAT(week_date,"%Y") = "' . $request->get('year') . '"
                group by DATE_FORMAT(week_date,"%M")
                order by week_date) mstr
                cross join
                (SELECT DISTINCT category from hr_question_logs) as ctg) masters
                left join (
                SELECT category, mon, count(category) as jml from
                (select category, DATE_FORMAT(hr_question_details.created_at,"%M") as mon from hr_question_logs
                left join hr_question_details on hr_question_logs.id = hr_question_details.message_id
                where hr_question_details.created_by <> "HR" and hr_question_details.deleted_at is null
                and DATE_FORMAT(hr_question_details.created_at,"%Y") = "' . $request->get('year') . '"
                union all
                select category, DATE_FORMAT(created_at,"%M") as mon from hr_question_logs
                where DATE_FORMAT(created_at,"%Y") = "' . $request->get('year') . '") datas
                group by category, mon
            ) hrq on masters.mon = hrq.mon and masters.category = hrq.category');

            $response = array(
                'status' => true,
                'datas' => $datas,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexKaizen()
    {
        $username = Auth::user()->username;

        $emp = User::join('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
            ->where('employee_syncs.employee_id', '=', $username)
            ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief", "Deputy Foreman") or role_code LIKE "%MIS" or username in (' . $this->usr . '))')
            ->select('position')
            ->first();

        $dd = [];

        $emp_usr = User::where('role_code', 'LIKE', '%MIS')->select('username')->get();

        for ($x = 0; $x < count($emp_usr); $x++) {
            array_push($dd, $emp_usr[$x]->username);
        }

        array_push($dd, 'PI0904007');

        $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

        $sc = db::select($sections);

        if ($emp) {
            return view('employees.service.indexKaizen', array(
                'title' => 'e-Kaizen (Assessment List)',
                'position' => $emp,
                'section' => $sc,
                'user' => $dd,
                'title_jp' => 'e-改善（採点対象改善提案リスト）',
            )
            )->with('page', 'Assess')->with('head', 'Kaizen');
        } else {
            return redirect()->back();
        }
    }

    public function indexKaizen2($section)
    {
        $username = Auth::user()->username;

        $emp = User::join('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
            ->where('employee_syncs.employee_id', '=', $username)
            ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief","Deputy Foreman") or role_code LIKE "%MIS")')
            ->select('position')
            ->first();

        $dd = [];

        $emp_usr = User::where('role_code', 'LIKE', '%MIS')->select('username')->get();

        for ($x = 0; $x < count($emp_usr); $x++) {
            array_push($dd, $emp_usr[$x]->username);
        }

        array_push($dd, 'PI0904007');

        $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

        $sc = db::select($sections);

        if ($emp) {
            return view('employees.service.indexKaizen', array(
                'title' => 'e-Kaizen (Assessment List)',
                'position' => $emp,
                'section' => $sc,
                'filter' => $section,
                'user' => $dd,
                'title_jp' => 'e-改善（採点対象改善提案リスト）',
            )
            )->with('page', 'Assess')->with('head', 'Kaizen');
        } else {
            return redirect()->back();
        }
    }

    public function indexKaizenApplied()
    {
        $username = Auth::user()->username;

        $emp = User::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
            ->where('employee_syncs.employee_id', '=', $username)
            ->whereRaw('(employee_syncs.position in ("Foreman","Manager","Chief","Deputy Foreman") or username in ("' . $this->usr . '"))')
            ->select('position')
            ->first();

        $dd = str_replace("'", "", $this->usr);
        $dd = explode(',', $dd);

        $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

        $sc = db::select($sections);

        if ($emp) {
            return view('employees.service.indexKaizenApplied', array(
                'title' => 'e-Kaizen (Applied list)',
                'position' => $emp,
                'section' => $sc,
                'user' => $dd,
                'title_jp' => '??',
            )
            )->with('page', 'Applied')->with('head', 'Kaizen');
        } else {
            return redirect()->back();
        }

    }

    public function indexKaizenReport()
    {
        return view('employees.report.kaizen_rank', array(
            'title' => 'Kaizen Teian Rank',
            'title_jp' => '??',
        )
        )->with('page', 'Kaizen Report');
    }

    public function indexKaizenResume()
    {
        return view('employees.report.kaizen_resume', array(
            'title' => 'Report Kaizen Teian',
            'title_jp' => '改善提案の報告',
        )
        )->with('page', 'Kaizen Resume');
    }

    public function indexKaizenApprovalResume()
    {
        $username = Auth::user()->username;

        $dd = str_replace("'", "", $this->usr);
        $dd = explode(',', $dd);

        $get_department = EmployeeSync::select('department')->where("employee_id", "=", Auth::user()->username)->first();

        for ($i = 0; $i < count($dd); $i++) {
            if ($username == $dd[$i] || str_contains(Auth::user()->role_code, 'S') || str_contains(Auth::user()->role_code, 'MIS')) {
                $d = "";
                break;
            } else {
                $d = "where department = '" . $get_department->department . "'";

                if ($get_department->department == 'Maintenance') {
                    $d .= " or department = 'Production Engineering Department'";
                }
            }
        }

        $q_data = "SELECT bagian.*, IFNULL(kz.count,0) as count  from
        (select fr.employee_id, `name`, position, fr.department, struktur.section from
        (select employee_id, `name`, position, department, section from employee_syncs where end_date is null and position in ('foreman', 'chief', 'Deputy Foreman')) as fr
        left join
        (select department, section from employee_syncs where department is not null and section is not null group by department, section) as struktur on fr.department = struktur.department) as bagian
        left join
        (select count(kaizen_forms.id) as count, area from kaizen_forms
        left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
        where `status` = -1 and kaizen_forms.deleted_at is null and employee_syncs.end_date is null group by area) as kz
        on bagian.section = kz.area
        " . $d . "
        order by `name` desc";

        $datas = db::select($q_data);

        return view('employees.service.kaizenAprovalResume', array(
            'title' => 'e-Kaizen Unverified Resume',
            'title_jp' => '',
            'datas' => $datas,
        )
        )->with('page', 'Kaizen Aproval Resume');
    }

    public function indexKaizenApprovalResumeGraph()
    {
        return view('employees.service.kaizenAprovalResumeGraph', array(
            'title' => 'e-Kaizen Outstanding Approval',
            'title_jp' => '',
        )
        )->with('page', 'Kaizen Aproval Resume');
    }

    public function indexKaizenApprovalResumeGraph2()
    {
        return view('employees.service.kaizenAprovalResumeGraph2', array(
            'title' => 'e-Kaizen Outstanding Resume',
            'title_jp' => '',
        )
        )->with('page', 'Kaizen Aproval Resume');
    }

    public function fetchKaizenApprovalGraph()
    {
        $datas = db::select("SELECT all_data.*, users.name from
            (select area, sum(jml) as ct from
            (select employee_id, employee_name, area, count(employee_id) as jml from kaizen_forms where `status` = '-1' and deleted_at is null and propose_date >= '2019-12-01'
            group by employee_id, employee_name, area) as kz
            join
            (select employee_id from employee_syncs where end_date is null) as emp on kz.employee_id = emp.employee_id
            group by area) all_data
            left join send_emails on all_data.area = send_emails.remark
            left join users on send_emails.email = users.email");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function fetchKaizenResumeGraph()
    {
        $datas = db::select("SELECT dpts.department, SUM(IF(atasan = 'chief', chf,0)) as chief, SUM(IF(atasan = 'foreman', chf,0)) as foreman, SUM(mngr) as manager from
            (SELECT mstr.area, SUM(jml_chf) as chf, SUM(jml_mngr) as mngr from
                (select kz_data1.* from 
                    (select employee_id, employee_name, area, count(employee_id) as jml_chf, 0 as jml_mngr from kaizen_forms where `status` = '-1' and deleted_at is null and propose_date >= '2019-12-01'
                        group by employee_id, employee_name, area) kz_data1
                    left join employee_syncs on employee_syncs.employee_id = kz_data1.employee_id
                    where end_date is null
                    union all
                    select kaizen_forms.employee_id, employee_name, area, 0 as jml_chf,sum(1) as jml_mngr from kaizen_forms
                    left join employee_syncs on employee_syncs.employee_id = kaizen_forms.employee_id
                    where `status` = '1' and kaizen_forms.id in (SELECT id_kaizen from kaizen_scores where manager_point_1 = 0) and end_date is null and kaizen_forms.deleted_at is null and propose_date >= '2019-12-01'
                    group by employee_id, employee_name, area
                    ) as mstr
                group by mstr.area) as kzs
            left join (
             SELECT mstr2.*, IF(approvers.approver_name is not null, 'foreman', IF(apr.approver_name <> '', 'chief', 'foreman')) as atasan from
             (SELECT department, section from employee_syncs where end_date is null
                and department is not null and section is not null
                group by department, section) as mstr2
             left join approvers on mstr2.section = approvers.section and approvers.remark = 'Foreman'
             left join approvers as apr on mstr2.department = apr.department and apr.remark = 'Chief'
             group by mstr2.department, mstr2.section, atasan
             ) as dpts on kzs.area = dpts.section
            group by department
            order by department asc");

        $query_cf = db::select("SELECT department, area, chf, mngr, atasan from
        (SELECT mstr.area, SUM(jml_chf) as chf, SUM(jml_mngr) as mngr from
            (select kz_data1.* from 
                (select employee_id, employee_name, area, count(employee_id) as jml_chf, 0 as jml_mngr from kaizen_forms where `status` = '-1' and deleted_at is null and propose_date >= '2019-12-01'
                    group by employee_id, employee_name, area) kz_data1
                left join employee_syncs on employee_syncs.employee_id = kz_data1.employee_id
                where end_date is null
                union all
                select kaizen_forms.employee_id, employee_name, area, 0 as jml_chf,sum(1) as jml_mngr from kaizen_forms
                left join employee_syncs on employee_syncs.employee_id = kaizen_forms.employee_id
                where `status` = '1' and kaizen_forms.id in (SELECT id_kaizen from kaizen_scores where manager_point_1 = 0) and end_date is null and kaizen_forms.deleted_at is null and propose_date >= '2019-12-01'
                group by employee_id, employee_name, area
                ) as mstr
            group by mstr.area) as kzs
        left join (
         SELECT mstr2.*, IF(approvers.approver_name is not null, 'foreman', IF(apr.approver_name <> '', 'chief', 'foreman')) as atasan from
         (SELECT department, section from employee_syncs where end_date is null
            and department is not null and section is not null
            group by department, section) as mstr2
         left join approvers on mstr2.section = approvers.section and approvers.remark = 'Foreman'
         left join approvers as apr on mstr2.department = apr.department and apr.remark = 'Chief'
     ) as dpts on kzs.area = dpts.section
        group by department, area, chf, mngr, atasan
        order by department");

        $response = array(
            'status' => true,
            'datas' => $datas,
            'details' => $query_cf,
        );
        return Response::json($response);
    }

    public function fetchKaizenApprovalDetail(Request $request)
    {
        $datas = db::select("SELECT all_data.*, users.name from
            (select area, sum(jml) as ct from
            (select employee_id, employee_name, area, count(employee_id) as jml from kaizen_forms where `status` = '-1' and deleted_at is null and propose_date >= '2019-12-01'
            group by employee_id, employee_name, area) as kz
            join
            (select employee_id from employee_syncs where end_date is null) as emp on kz.employee_id = emp.employee_id
            group by area) all_data
            left join send_emails on all_data.area = send_emails.remark
            left join users on send_emails.email = users.email
            where `name` = '" . $request->get('foreman') . "'
            order by ct desc");

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function indexUpdateKaizenDetail($id)
    {
        $data = KaizenForm::where('kaizen_forms.id', '=', $id)
            ->leftJoin('kaizen_calculations', 'kaizen_forms.id', '=', 'kaizen_calculations.id_kaizen')
            ->leftJoin('kaizen_notes', 'kaizen_forms.id', '=', 'kaizen_notes.id_kaizen')
            ->select('kaizen_forms.id', 'kaizen_forms.employee_name', 'kaizen_forms.propose_date', 'kaizen_forms.section', 'kaizen_forms.leader', 'kaizen_forms.title', 'kaizen_forms.purpose', 'kaizen_forms.condition', 'kaizen_forms.improvement', 'kaizen_forms.area', 'kaizen_forms.employee_id', 'kaizen_calculations.id_cost', 'kaizen_calculations.cost', 'kaizen_notes.foreman_note', 'kaizen_notes.manager_note')
            ->get();

        $section = explode(" ~", $data[0]->section)[0];

        $ldr = "position = 'Leader'";
        if ($section == 'Assembly Process Control Section') {
            $ldr = "grade_name = 'Staff'";
        }

        $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and " . $ldr . " and section = '" . $section . "' order by name asc";

        $subleader = db::select($q_subleader);

        $sections = "select section from employee_syncs where section is not null and position in ('Leader', 'Chief') group by section";

        $sc = db::select($sections);

        return view('employees.service.ekaizenUpdate', array(
            'title' => 'e-Kaizen Update',
            'title_jp' => '',
            'subleaders' => $subleader,
            'sc' => $sc,
            'data' => $data,
        )
        )->with('page', 'Kaizen Update');
    }

    public function indexUploadKaizenImage()
    {
        $username = Auth::user()->username;

        $mstr = EmployeeSync::where('employee_id', '=', $username)->select('sub_section')->first();

        $datas = EmployeeSync::where('section', '=', $mstr->sub_section)->select('employee_id', 'name')->get();

        return view('employees.service.ekaizenUpload', array(
            'title' => 'e-Kaizen Upload Images',
            'title_jp' => '',
            'employees' => $datas,
        )
        )->with('page', 'Kaizen Upload Images');
    }

    public function indexKaizenReward()
    {
        $username = Auth::user()->username;

        $user = db::select("select username from users
          left join employee_syncs on employee_syncs.employee_id = users.username
          where username = '" . $username . "' AND (role_code LIKE '%MIS' OR username = 'PI0904007' OR position in ('Manager','foreman','Deputy Foreman'))");

        if ($user) {
            return view('employees.report.report_kaizen_reward', array(
                'title' => 'e-Kaizen Reward',
                'title_jp' => '',
            )
            )->with('page', 'Kaizen Reward');
        } else {
            return redirect()->back();
        }
    }

    public function makeKaizen($id, $name, $section, $group)
    {
        $ldr = "position = 'Leader'";
        if ($section == 'Assembly Process Control Section') {
            $ldr = "grade_name = 'Staff'";
        } else if ($section == 'Maintenance Field Section') {
            $ldr = "employee_id = 'PI9906003' OR employee_id = 'PI9707002'";
        }

        if ($section == 'Accounting Admin Section') {
            $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and position = 'chief' and section = 'Accounting Admin Section' order by name asc";
        } else {
            $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and " . $ldr . " and section = '" . $section . "' order by name asc";
        }

        $subleader = db::select($q_subleader);

        if (in_array($id, $this->wst)) {

        }

        $sections = "select section from employee_syncs where position in ('Leader', 'chief') group by section";

        $sc = db::select($sections);

        return view('employees.service.ekaizenForm', array(
            'title' => 'e-Kaizen',
            'emp_id' => $id,
            'name' => $name,
            'section' => $section,
            'group' => $group,
            'subleaders' => $subleader,
            'sc' => $sc,
            'title_jp' => '',
        )
        );
    }

    public function makeKaizen2($id, $name, $section)
    {
        $group = "";

        $ldr = "position = 'Leader'";
        if ($section == 'Assembly Process Control Section') {
            $ldr = "grade_name = 'Staff'";
        } else if ($section == 'Maintenance Field Section') {
            $ldr = "employee_id = 'PI9906003' OR employee_id = 'PI9707002'";
        }

        $q_subleader = "select name, position, employee_id from employee_syncs where end_date is null and " . $ldr . " and section = '" . $section . "' order by name asc";

        $subleader = db::select($q_subleader);

        if (in_array($id, $this->wst)) {

        }

        $sections = "select section from employee_syncs where position in ('Leader', 'chief') group by section";

        $sc = db::select($sections);

        return view('employees.service.ekaizenForm', array(
            'title' => 'e-Kaizen',
            'emp_id' => $id,
            'name' => $name,
            'section' => $section,
            'group' => $group,
            'subleaders' => $subleader,
            'sc' => $sc,
            'title_jp' => '',
        )
        );
    }

    public function updateEmp($id)
    {
        $keluarga = $this->keluarga;
        $emp = Employee::where('employee_id', '=', $id)
            ->get();
        return view('employees.master.updateEmp', array(
            'emp' => $emp,
            'keluarga' => $keluarga,
        )
        )->with('page', 'Update Employee');
    }

    public function fetchTotalMeeting(Request $request)
    {
        try {
            $now = date('Y-m-01', strtotime($request->get('period') . '-01'));
            $period = date('Y-m', strtotime($request->get('period')));

            $first = date('Y-m-d', strtotime('-3 months', strtotime($now)));
            $last = date('Y-m-t', strtotime($now));
            $first_sunfish = date('Y-m-d', strtotime('-3 months', strtotime($now)));
            $last_mirai = date('Y-m-t', strtotime($now));

            $employees = db::select("SELECT date_format(period, '%M %Y') as period, date_format(period, '%Y-%m') as period2, count(full_name) as total, sum(if(employ_code = 'OUTSOURCE', 1, 0)) as outsource, sum(if(employ_code LIKE 'CONTRACT%', 1, 0)) as contract, sum(if(employ_code = 'PERMANENT', 1, 0)) as permanent, sum(if(employ_code = 'PROBATION', 1, 0)) as probation, sum(if(gender = 'L', 1, 0)) as male, sum(if(gender = 'P', 1, 0)) as female, sum(if(`Labour_Union` = 'NONE' or `Labour_Union` is null AND `employ_code` <> 'OUTSOURCE', 1, 0)) as no_union, sum(if(`Labour_Union` = 'SPSI' AND `employ_code` <> 'OUTSOURCE', 1, 0)) as spsi, sum(if(`Labour_Union` = 'SBM' AND `employ_code` <> 'OUTSOURCE', 1, 0)) as sbm, sum(if(`Labour_Union` = 'SPMI' AND `employ_code` <> 'OUTSOURCE', 1, 0)) as spmi from employee_histories where end_date is null and date_format(period, '%Y-%m-%d') >= '" . $first . "' and date_format(period, '%Y-%m') <= '" . $last . "' group by date_format(period, '%Y-%m'), date_format(period, '%M %Y') order by period2 asc");

            $mirai_overtimes1 = array();
            $sunfish_overtimes1 = array();
            if ($last >= '2020-01-01') {
                if ($first <= '2020-01-01') {
                    $first_sunfish = '2020-01-01';
                }
                $sunfish_overtimes1 = db::connection('sunfish')->select("SELECT DISTINCT
                    X.orderer,
                    X.period,
                    VIEW_YMPI_Emp_OrgUnit.Department,
                    COALESCE(Q.ot, 0) as ot,
                    COALESCE(Q.person, 0) as person
                    FROM
                    VIEW_YMPI_Emp_OrgUnit
                    CROSS JOIN (
                    SELECT DISTINCT
                    format ( ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    format ( ovtplanfrom, 'MMMM yyyy' ) AS period
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan
                    WHERE
                    ovtplanfrom >= '" . $first_sunfish . "'
                    AND ovtplanfrom <= '" . $last . "'
                    ) X
                    LEFT JOIN (
                    SELECT
                    orderer,
                    period,
                    B.Department,
                    SUM ( ot ) as ot,
                    COUNT ( final.Emp_no ) AS person
                    FROM
                    (
                    SELECT
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                    SUM ( ROUND( A.total_ot / 60.0, 2 ) ) AS ot
                    FROM
                    VIEW_YMPI_Emp_OvertimePlan A
                    WHERE
                    A.ovtplanfrom >= '" . $first_sunfish . "'
                    AND A.ovtplanfrom <= '" . $last . "'
                    GROUP BY
                    A.Emp_no,
                    FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                    FORMAT ( A.ovtplanfrom, 'MMMM yyyy' )
                    ) AS final
                    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = final.Emp_no
                    GROUP BY
                    orderer,
                    period,
                    B.Department
                    ) AS Q ON Q.orderer = X.orderer
                    AND Q.Department = VIEW_YMPI_Emp_OrgUnit.Department
                    WHERE
                    VIEW_YMPI_Emp_OrgUnit.Department IS NOT NULL
                    and VIEW_YMPI_Emp_OrgUnit.Emp_no NOT LIKE 'OS%'");
            }
            if ($first <= '2020-01-01') {
                if ($last_mirai >= '2020-01-01') {
                    $last_mirai = '2019-12-31';
                }
                $mirai_overtimes1 = db::select("select mon as period, department as Department, round(ot_hour / kar,2) as ot_person from
                   (
                   select em.mon ,em.department, IFNULL(sum(ovr.final),0) ot_hour, sum(jml) as kar from
                   (
                   select emp.*, bagian.department, 1 as jml from
                   (select employee_id, mon from
                   (
                   select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
                   cross join (
                   select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where week_date BETWEEN  '" . $first . "' and  '" . $last_mirai . "' group by date_format(week_date, '%Y-%m')) s
                   ) m
                   where hire_month <= mon and (mon < end_month OR end_month is null)
                   ) emp
                   left join (
                   SELECT id, employee_id, department, date_format(valid_from, '%Y-%m') as mon_from, coalesce(date_format(valid_to, '%Y-%m'), date_format(DATE_ADD(now(), INTERVAL 1 MONTH),'%Y-%m')) as mon_to FROM mutation_logs
                   WHERE id IN (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id, DATE_FORMAT(valid_from,'%Y-%m'))
                   ) bagian on emp.employee_id = bagian.employee_id and emp.mon >= bagian.mon_from and emp.mon < mon_to
                   where department is not null
                   ) as em
                   left join (
                   select nik, date_format(tanggal,'%Y-%m') as mon, sum(if(status = 0,om.jam,om.final)) as final from ftm.over_time as o left join ftm.over_time_member as om on o.id = om.id_ot
                   where deleted_at is null and jam_aktual = 0 and DATE_FORMAT(tanggal,'%Y-%m') in (
                   select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where week_date BETWEEN  '" . $first . "' and '" . $last_mirai . "' group by date_format(week_date, '%Y-%m')
                   )
                   group by date_format(tanggal,'%Y-%m'), nik
                   ) ovr on em.employee_id = ovr.nik and em.mon = ovr.mon
                   group by department, em.mon
               ) as semua");
            }

            $overtimes1 = array();
            if ($mirai_overtimes1 != null) {
                foreach ($mirai_overtimes1 as $key) {
                    array_push(
                        $overtimes1,
                        [
                            "period" => $key->period,
                            "Department" => $key->Department,
                            "ot_person" => $key->ot_person,
                        ]
                    );
                }
            }
            if ($sunfish_overtimes1 != null) {
                foreach ($sunfish_overtimes1 as $key) {
                    array_push(
                        $overtimes1,
                        [
                            "period" => $key->orderer,
                            "Department" => $key->Department,
                            "ot" => (float) $key->ot,
                            "person" => (int) $key->person,
                        ]
                    );
                }
            }

            if ($now >= '2020-01-01') {
                $overtimes2 = db::connection('sunfish')->select(" SELECT
                   orderer,
                   period,
                   Department,
                   SUM ( ot_3 ) AS ot_3,
                   SUM ( ot_14 ) AS ot_14,
                   IIF ( SUM ( ot_14 ) > 0 AND SUM ( ot_3 ) > 0, IIF(SUM ( ot_3 )>SUM ( ot_14 ), SUM ( ot_14 ), SUM ( ot_3 )), 0 ) AS ot_3_14,
                   SUM ( ot_56 ) AS ot_56
                   FROM
                   (
                   SELECT
                   orderer,
                   period,
                   Department,
                   COUNT ( ot_3 ) AS ot_3,
                   0 AS ot_14,
                   0 AS ot_56
                   FROM
                   (
                   SELECT
                   A.Emp_no,
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                   B.Department,
                   SUM (
                   IIF(ROUND( A.total_ot / 60.0, 2 ) > 4, 1 , null)
                   ) AS ot_3
                   FROM
                   VIEW_YMPI_Emp_OvertimePlan A
                   LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
                   WHERE
                   A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
                   and B.emp_no NOT LIKE 'OS%'
                   GROUP BY
                   A.Emp_no,
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                   B.Department
                   ) AS final
                   GROUP BY
                   orderer,
                   period,
                   Department UNION ALL
                   SELECT
                   orderer,
                   period,
                   Department,
                   0 AS ot_3,
                   COUNT ( ot_14 ) AS ot_14,
                   0 AS ot_56
                   FROM
                   (
                   SELECT
                   orderer,
                   period,
                   Emp_no,
                   Department,
                   SUM ( ot_14 ) AS ot_14
                   FROM
                   (
                   SELECT
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                   A.Emp_no,
                   DATEPART( week, A.ovtplanfrom ) AS wk,
                   B.Department,
                   CASE

                   WHEN SUM (
                   ROUND( A.total_ot / 60.0, 2 )
                   ) > 18 THEN
                   1 ELSE NULL
                   END AS ot_14
                   FROM
                   VIEW_YMPI_Emp_OvertimePlan A
                   LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
                   WHERE
                   A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
                   and B.emp_no NOT LIKE 'OS%'
                   GROUP BY
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                   A.Emp_no,
                   DATEPART( week, A.ovtplanfrom ),
                   B.Department
                   ) AS final
                   GROUP BY
                   orderer,
                   period,
                   Emp_no,
                   Department
                   ) AS final2
                   GROUP BY
                   orderer,
                   period,
                   Department UNION ALL
                   SELECT
                   orderer,
                   period,
                   Department,
                   0 AS ot_3,
                   0 AS ot_14,
                   COUNT ( ot_56 ) AS ot_56
                   FROM
                   (
                   SELECT
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) AS orderer,
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ) AS period,
                   A.Emp_no,
                   B.Department,
                   CASE

                   WHEN SUM (
                   ROUND( A.total_ot / 60.0, 2 )
                   ) > 72 THEN
                   1 ELSE NULL
                   END AS ot_56
                   FROM
                   VIEW_YMPI_Emp_OvertimePlan A
                   LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON B.Emp_no = A.Emp_no
                   WHERE
                   A.daytype = 'WD' and FORMAT ( A.ovtplanfrom, 'yyyy-MM' ) = '" . $period . "'
                   and B.emp_no NOT LIKE 'OS%'
                   GROUP BY
                   FORMAT ( A.ovtplanfrom, 'yyyy-MM' ),
                   FORMAT ( A.ovtplanfrom, 'MMMM yyyy' ),
                   A.Emp_no,
                   B.Department
                   ) AS final
                   GROUP BY
                   orderer,
                   period,
                   Department
                   ) AS ot_violation
                   where Department is not null
                   GROUP BY
                   orderer,
                   period,
                   Department");
            } else {
                $overtimes2 = db::select("select kd.department as Department, '" . $period . "' as orderer, COALESCE(tiga.tiga_jam,0) as ot_3, COALESCE(patblas.emptblas_jam,0) as ot_14, COALESCE(tiga_patblas.tiga_patblas_jam,0) as ot_3_14, COALESCE(lima_nam.limanam_jam,0) as ot_56 from
      (select child_code as department from organization_structures where remark = 'department') kd
      left join
      ( select department, count(nik) tiga_jam from (
      select d.nik, karyawan.department from
      (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
      left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
      where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $period . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
      group by nik, tanggal) d
      left join
      (
      select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "' and id IN (
      SELECT MAX(id)
      FROM mutation_logs
      where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "'
      GROUP BY employee_id
      )
      ) karyawan on karyawan.employee_id  = d.nik
      where jam > 3
      group by d.nik
      ) tiga_jam
      group by department
      ) as tiga on kd.department = tiga.department
      left join
      (
      select department, count(nik) as emptblas_jam from
      (select s.nik, department from
      (select nik, sum(jam) jam, week_name from
      (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
      left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
      where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $period . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
      group by nik, tanggal) m
      group by nik, week_name) s
      left join
      (
      select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "' and id IN (
      SELECT MAX(id)
      FROM mutation_logs
      where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "'
      GROUP BY employee_id
      )
      ) employee on employee.employee_id = s.nik
      where jam > 14
      group by s.nik) l
      group by department
      ) as patblas on kd.department = patblas.department
      left join
      (
      select employee.department, count(c.nik) as tiga_patblas_jam from
      ( select z.nik from
      ( select d.nik from
      (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
      left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
      where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $period . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
      group by nik, tanggal) d
      where jam > 3
      group by d.nik ) z

      INNER JOIN

      (select s.nik from
      (select nik, sum(jam) jam, week_name from
      (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam, week(ftm.over_time.tanggal) as week_name from ftm.over_time
      left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
      where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $period . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
      group by nik, tanggal) m
      group by nik, week_name) s
      where jam > 14
      group by s.nik) x on z.nik = x.nik
      ) c
      left join
      (
      select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "' and id IN (
      SELECT MAX(id)
      FROM mutation_logs
      where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "'
      GROUP BY employee_id
      )
      ) employee on employee.employee_id = c.nik
      group by employee.department
      ) tiga_patblas on kd.department = tiga_patblas.department
      left join
      (
      select department, count(nik) as limanam_jam from
      ( select d.nik, sum(jam) as jam, employee.department from
      (select tanggal, nik, sum(IF(status = 1, final, jam)) as jam from ftm.over_time
      left join ftm.over_time_member on ftm.over_time_member.id_ot = ftm.over_time.id
      where deleted_at IS NULL and date_format(ftm.over_time.tanggal, '%Y-%m') = '" . $period . "' and nik IS NOT NULL and jam_aktual = 0 and hari = 'N'
      group by nik, tanggal) d
      left join
      (
      select employee_id, department from mutation_logs where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "' and id IN (
      SELECT MAX(id)
      FROM mutation_logs
      where DATE_FORMAT(valid_from,'%Y-%m') <= '" . $period . "'
      GROUP BY employee_id
      )
      ) employee on employee.employee_id = d.nik
      group by d.nik ) c
      where jam > 56
      group by department
  ) lima_nam on lima_nam.department = kd.department");
            }

            $first_rate = date('Y-m-d', strtotime('-4 months', strtotime($now)));

            $att_rate = db::select("SELECT *, 100 - ((tidak_hadir_permanen/hadir_permanen) * 100) as rate_permanen, 100 - ((tidak_hadir_kontrak/hadir_kontrak) * 100) as rate_kontrak, 100 - ((tidak_hadir_os/hadir_os) * 100) as rate_os FROM
   (SELECT DATE_FORMAT(sunfish_shift_syncs.shift_date, '%M %Y') as mon,
   SUM(IF(employment_status = 'PERMANENT', IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,1,0), 0)) as tidak_hadir_permanen,

   SUM(IF(employment_status LIKE 'CONTRACT%',IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,1,0),0)) as tidak_hadir_kontrak,

   SUM(IF(employment_status = 'OUTSOURCING',IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,1,0),0)) as tidak_hadir_os,

   SUM(IF(employment_status = 'PERMANENT', IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,0,1), 0)) as hadir_permanen,

   SUM(IF(employment_status LIKE 'CONTRACT%',IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,0,1),0)) as hadir_kontrak,

   SUM(IF(employment_status = 'OUTSOURCING',IF(attend_code LIKE '%SAKIT%' OR attend_code LIKE '%Izin%' OR attend_code LIKE '%ABS%' OR attend_code LIKE '%CUTI%' OR attend_code LIKE '%CK%' OR attend_code is null,0,1),0)) as hadir_os

   FROM
   sunfish_shift_syncs
   LEFT JOIN employee_syncs ON employee_syncs.employee_id = sunfish_shift_syncs.employee_id
   WHERE DATE_FORMAT(sunfish_shift_syncs.shift_date, '%Y-%m') >= '" . $first_rate . "'
   AND DATE_FORMAT(sunfish_shift_syncs.shift_date, '%Y-%m') <= '" . $period . "'
   AND sunfish_shift_syncs.shiftdaily_code not like '%OFF%'
   AND sunfish_shift_syncs.attend_code not like '%OFF%'
   AND (end_date is null OR end_date = sunfish_shift_syncs.shift_date)
   GROUP BY DATE_FORMAT(sunfish_shift_syncs.shift_date, '%M %Y')
   ORDER BY sunfish_shift_syncs.shift_date asc
) as mstr");

            $response = array(
                'status' => true,
                'employees' => $employees,
                'att_rate' => $att_rate,
                'overtimes1' => $overtimes1,
                'overtimes2' => $overtimes2,
                'period' => $period,
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
        }
    }

    public function insertEmp()
    {
        $dev = OrganizationStructure::where('status', 'LIKE', 'DIV%')->get();
        $dep = OrganizationStructure::where('status', 'LIKE', 'DEP%')->get();
        $sec = OrganizationStructure::where('status', 'LIKE', 'SEC%')->get();
        $sub = OrganizationStructure::where('status', 'LIKE', 'SSC%')->get();
        $grup = OrganizationStructure::where('status', 'LIKE', 'GRP%')->get();
        $kode = DB::table('total_meeting_codes')->select('code')->groupBy('code')->get();
        $grade = Grade::orderBy('id', 'asc')->get();
        $position = Position::orderBy('id', 'asc')->get();
        $cc = CostCenter::get();

        return view('employees.master.insertEmp', array(
            'dev' => $dev,
            'dep' => $dep,
            'sec' => $sec,
            'sub' => $sub,
            'grup' => $grup,
            'grade' => $grade,
            'cc' => $cc,
            'kode' => $kode,
            'position' => $position,
            'keluarga' => $this->keluarga,
        )
        )->with('page', 'Master Employee');
    }

    public function fetchMasterEmp(Request $request)
    {
        $where = "";

        if ($request->get("filter") != "") {
            if ($request->get("filter") == "ofc") {
                $where = "where `remark` in ('0fc','Jps')";
            } else if ($request->get("filter") == "prod") {
                $where = "where `remark` in ('WH', 'AP', 'EI', 'MTC', 'PP', 'PE', 'QA', 'WST')";
            }
        }

        $emp = "select employees.employee_id,name, department, section, DATE_FORMAT(hire_date,' %d %b %Y') hire_date, stat.status from employees
    LEFT JOIN (select employee_id, department, section, `group` from mutation_logs where valid_to is null group by employee_id, department, section, `group`) mutation_logs on employees.employee_id = mutation_logs.employee_id
    left join (
    select employee_id, status from employment_logs
    WHERE id IN (
    SELECT MAX(id)
    FROM employment_logs
    GROUP BY employment_logs.employee_id
    )
    ) stat on stat.employee_id = employees.employee_id
    " . $where . "
    ORDER BY employees.remark asc";
        $masteremp = DB::select($emp);

        return DataTables::of($masteremp)
            ->addColumn('action', function ($masteremp) {

                if ($masteremp->status != 'Tetap') {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="detail(this.id)" id="' . $masteremp->employee_id . '">Details</a>
            <a href="' . url("index/updateEmp") . "/" . $masteremp->employee_id . '" class="btn btn-xs btn-warning"  id="' . $masteremp->employee_id . '">Update</a>
            <button class="btn btn-xs btn-success" data-toggle="tooltip" title="Upgrade" onclick="modalUpgrade(\'' . $masteremp->employee_id . '\', \'' . $masteremp->name . '\',\'' . $masteremp->status . '\')"><i class="fa fa-arrow-up"></i></button>';
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="detail(this.id)" id="' . $masteremp->employee_id . '">Details</a>
            <a href="' . url("index/updateEmp") . "/" . $masteremp->employee_id . '" class="btn btn-xs btn-warning"  id="' . $masteremp->employee_id . '">Update</a>';
                }
            })

            ->rawColumns(['action' => 'action'])
            ->make(true);
    }

    public function fetchdetail(Request $request)
    {

        $detail = "select employees.employee_id,employees.name,employees.avatar,employees.direct_superior,employees.birth_place, DATE_FORMAT(employees.birth_date,' %d %b %Y') birth_date,employees.gender,employees.address,employees.family_id, DATE_FORMAT(employees.hire_date,' %d %b %Y') hire_date,employees.remark,employees.phone,employees.account,employees.card_id,employees.npwp,employees.bpjstk,employees.jp,employees.bpjskes,mutation_logs.division,mutation_logs.department,mutation_logs.section,mutation_logs.sub_section,mutation_logs.group,promotion_logs.grade_code,promotion_logs.position,promotion_logs.grade_name from employees
    LEFT JOIN (select employee_id,cost_center, division, department, section, sub_section, `group` from mutation_logs where employee_id = '" . $request->get('nik') . "' and valid_to is null) mutation_logs on employees.employee_id = mutation_logs.employee_id
    LEFT JOIN (select employee_id,grade_code, grade_name, position from promotion_logs where employee_id = '" . $request->get('nik') . "' and valid_to is null) promotion_logs on employees.employee_id = promotion_logs.employee_id
    where employees.employee_id ='" . $request->get('nik') . "'
    ORDER BY employees.remark asc";

        $detail2 = DB::select($detail);
        $response = array(
            'status' => true,
            'detail' => $detail2,
        );
        return Response::json($response);
    }

    public function empCreate(Request $request)
    {
        $id = Auth::id();

        try {

            $hire_date = $request->get('tglM');

            if ($request->hasFile('foto')) {
                $files = $request->file('foto');
                foreach ($files as $file) {
                    $number = $request->get('nik');
                    $data = file_get_contents($file);
                    $ext = $file->getClientOriginalExtension();
                    $photo_number = $number . "." . $ext;
                    $filepath = public_path() . "/uploads/employee_photos/" . $photo_number;

                    $emp = new Employee([
                        'employee_id' => $request->get('nik'),
                        'name' => $request->get('nama'),
                        'gender' => $request->get('jk'),
                        'family_id' => $request->get('statusK'),
                        'birth_place' => $request->get('tmptL'),
                        'birth_date' => $request->get('tglL'),
                        'address' => $request->get('alamat'),
                        'phone' => $request->get('hp'),
                        'card_id' => $request->get('ktp'),
                        'account' => $request->get('no_rek'),
                        'bpjstk' => $request->get('bpjstk'),
                        'jp' => $request->get('jp'),
                        'bpjskes' => $request->get('bpjskes'),
                        'npwp' => $request->get('npwp'),
                        'direct_superior' => $request->get('leader'),
                        'hire_date' => $hire_date,
                        'avatar' => $photo_number,
                        'remark' => $request->get('pin'),
                        'created_by' => $id,
                    ]);

                    $emp->save();
                    File::put($filepath, $data);
                }
            } else {
                $emp = new Employee([
                    'employee_id' => $request->get('nik'),
                    'name' => $request->get('nama'),
                    'gender' => $request->get('jk'),
                    'family_id' => $request->get('statusK'),
                    'birth_place' => $request->get('tmptL'),
                    'birth_date' => $request->get('tglL'),
                    'address' => $request->get('alamat'),
                    'phone' => $request->get('hp'),
                    'card_id' => $request->get('ktp'),
                    'account' => $request->get('no_rek'),
                    'bpjstk' => $request->get('bpjstk'),
                    'jp' => $request->get('jp'),
                    'bpjskes' => $request->get('bpjskes'),
                    'npwp' => $request->get('npwp'),
                    'direct_superior' => $request->get('leader'),
                    'hire_date' => $hire_date,
                    'remark' => $request->get('pin'),
                    'created_by' => $id,
                ]);

                $emp->save();
            }

            // --------------- Promotion Log insert

            $grade1 = $request->get('grade');
            $grade2 = explode("#", $grade1);
            $grade = new PromotionLog([
                'employee_id' => $request->get('nik'),
                'grade_code' => $grade2[0],
                'grade_name' => $grade2[1],
                'position' => $request->get('jabatan'),
                'valid_from' => $hire_date,
                'created_by' => $id,

            ]);

            $grade->save();

            // --------------- Mutation Log insert
            $jabatan = new Mutationlog([
                'employee_id' => $request->get('nik'),
                'cost_center' => $request->get('cs'),
                'division' => $request->get('devisi'),
                'department' => $request->get('departemen'),
                'section' => $request->get('section'),
                'sub_section' => $request->get('subsection'),
                'group' => $request->get('group'),
                'valid_from' => $hire_date,
                'created_by' => $id,
            ]);

            $jabatan->save();

            // --------------- Employment Log insert

            $emp = new EmploymentLog([
                'employee_id' => $request->get('nik'),
                'status' => $request->get('statusKar'),
                'valid_from' => $hire_date,
                'created_by' => $id,
            ]);

            $emp->save();

            return redirect('/index/insertEmp')->with('status', 'Input Employee success')->with('page', 'Master Employee');
        } catch (QueryException $e) {
            return redirect('/index/insertEmp')->with('error', "Employee already exists")->with('page', 'Master Employee');
        }
    }

    public function getCostCenter(Request $request)
    {
        $cc = CostCenter::select('cost_center')
            ->where('section', '=', $request->get('section'))
            ->where('sub_sec', '=', $request->get('subsection'))
            ->where('group', '=', $request->get('group'))
            ->get();

        $response = array(
            'status' => true,
            'cost_center' => $cc,
        );
        return Response::json($response);

        // select cost_center from cost_centers where section = 'Assembly Process' and sub_sec = 'CL BODY' and `group` = 'Leader'
    }

    public function updateEmpData(Request $request)
    {
        $id = Auth::id();
        try {

            $idemp = $request->get('nik2');
            $emp = Employee::where('employee_id', '=', $idemp)
                ->withTrashed()
                ->first();

            if ($request->hasFile('foto')) {
                $files = $request->file('foto');
                foreach ($files as $file) {
                    $number = $request->get('nik');
                    $data = file_get_contents($file);
                    $ext = $file->getClientOriginalExtension();
                    $photo_number = $number . "." . $ext;
                    $filepath = public_path() . "/uploads/employee_photos/" . $photo_number;

                    $files = glob(public_path() . "/uploads/employee_photos/" . $number . ".*");
                    foreach ($files as $file) {
                        unlink($file);
                    }

                    $emp->employee_id = $request->get('nik');
                    $emp->name = $request->get('nama');
                    $emp->gender = $request->get('jk');
                    $emp->family_id = $request->get('statusK');
                    $emp->birth_place = $request->get('tmptL');
                    $emp->birth_date = $request->get('tglL');
                    $emp->address = $request->get('alamat');
                    $emp->phone = $request->get('hp');
                    $emp->card_id = $request->get('ktp');
                    $emp->account = $request->get('no_rek');
                    $emp->bpjstk = $request->get('bpjstk');
                    $emp->jp = $request->get('jp');
                    $emp->bpjskes = $request->get('bpjskes');
                    $emp->npwp = $request->get('npwp');
                    $emp->direct_superior = $request->get('leader');
                    $emp->hire_date = $request->get('tglM');
                    $emp->avatar = $photo_number;
                    $emp->remark = $request->get('pin');
                    $emp->created_by = $id;

                    $emp->save();
                    File::put($filepath, $data);
                }
            } else {

                $emp->employee_id = $request->get('nik');
                $emp->name = $request->get('nama');
                $emp->gender = $request->get('jk');
                $emp->family_id = $request->get('statusK');
                $emp->birth_place = $request->get('tmptL');
                $emp->birth_date = $request->get('tglL');
                $emp->address = $request->get('alamat');
                $emp->phone = $request->get('hp');
                $emp->card_id = $request->get('ktp');
                $emp->account = $request->get('no_rek');
                $emp->bpjstk = $request->get('bpjstk');
                $emp->jp = $request->get('jp');
                $emp->bpjskes = $request->get('bpjskes');
                $emp->npwp = $request->get('npwp');
                $emp->direct_superior = $request->get('leader');
                $emp->hire_date = $request->get('tglM');
                $emp->remark = $request->get('pin');
                $emp->created_by = $id;
                $emp->save();
            }

            $emp->category = $request->get('category');

            return redirect('/index/MasterKaryawan')->with('status', 'Update Employee Success')->with('page', 'Master Employee');
        } catch (QueryException $e) {
            return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
        }

    }

    // end master emp

    // absensi import

    public function importEmp(Request $request)
    {
        $id = Auth::id();
        try {
            $tanggal = [];

            if ($request->hasFile('import')) {
                $file = $request->file('import');
                $data = file_get_contents($file);
                $rows = explode("\r\n", $data);

                foreach ($rows as $row) {
                    if (strlen($row) > 0) {

                        $row = explode("\t", $row);
                        $tgl = date('Y-m-d', strtotime($row[2]));
                        $array = Arr::prepend($tanggal, $tgl);
                        // $array1 = Arr::collapse($array);
                        if ($row[3] == '  ') {
                            $row[3] = '00:00';
                        }
                        if ($row[4] == '  ') {
                            $row[4] = '00:00';
                        }
                        if ($row[5] == '') {
                            $row[5] = '-';
                        }

                        $detail = PresenceLog::updateOrCreate(
                            [
                                'employee_id' => $row[1],
                                'presence_date' => date('Y-m-d', strtotime($row[2])),

                            ]
                            ,
                            [
                                'employee_id' => $row[1],
                                'presence_date' => date('Y-m-d', strtotime($row[2])),
                                'in_time' => $row[3],
                                'out_time' => $row[4],
                                'shift' => $row[5],
                                'remark' => $row[0],
                                'created_by' => $id,

                            ]
                        );
                        $detail->save();
                    }
                }
            }
            return redirect('/index/MasterKaryawan')->with('status', 'Update Presence Employee Success' . $array[1])->with('page', 'Master Employee');
        } catch (QueryException $e) {
            $emp = PresenceLog::where('presence_date', '=', $tgl)
                ->forceDelete();
            return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
        }

    }
    // end absensi import

    // master promotion_logs

    public function indexpromotion()
    {
        return view('employees.master.promotion')->with('page', 'Promotion')->with('head', 'Employees Data');
    }

    public function fetchpromotion(Request $request)
    {
        $emp_id = $request->get('emp_id');

        $promotion_logs = PromotionLog::leftJoin('employees', 'employees.employee_id', '=', 'promotion_logs.employee_id')
            ->select('promotion_logs.employee_id', 'employees.name', 'grade_code', 'grade_name', 'position', 'valid_from', 'valid_to')
            ->where('promotion_logs.employee_id', '=', $emp_id)
            ->orderByRaw('promotion_logs.created_at desc')
            ->take(1)
            ->get();

        $pos = Position::orderBy('id', 'asc')->get();
        $grd = Grade::get();

        $response = array(
            'status' => true,
            'promotion_logs' => $promotion_logs[0],
            'positions' => $pos,
            'grades' => $grd,
        );
        return Response::json($response);
    }

    public function changePromotion(Request $request)
    {
        $grade = explode("#", $request->get('grade'));
        $emp_id = $request->get('emp_id');

        $data = PromotionLog::where('employee_id', '=', $emp_id)
            ->latest()
            ->first();
        $data->valid_to = $request->get('valid_to');
        $data->save();

        $promotion = new PromotionLog([
            'employee_id' => $emp_id,
            'grade_code' => $grade[0],
            'grade_name' => $grade[1],
            'valid_from' => $request->get('valid_from'),
            'position' => $request->get('position'),
            'created_by' => 1,
        ]);

        $promotion->save();

        $response = array(
            'status' => true,
            'data' => $promotion,
        );
        return Response::json($response);
    }

    // end promotion_logs

    // mutation log

    public function indexMutation()
    {
        return view('employees.master.mutation')->with('page', 'Mutation')->with('head', 'Employees Data');
    }

    public function fetchMutation(Request $request)
    {
        $emp_id = $request->get('emp_id');

        $mutation_logs = MutationLog::leftJoin('employees', 'employees.employee_id', '=', 'mutation_logs.employee_id')
            ->select('mutation_logs.employee_id', 'name', 'cost_center', 'division', 'department', 'section', 'sub_section', 'group', 'valid_from', 'valid_to')
            ->where('mutation_logs.employee_id', '=', $emp_id)
            ->orderByRaw('mutation_logs.created_at desc')
            ->take(1)
            ->get();

        $devision = OrganizationStructure::where('status', 'LIKE', 'DIV%')->get();
        $department = OrganizationStructure::where('status', 'LIKE', 'DEP%')->get();
        $section = OrganizationStructure::where('status', 'LIKE', 'SEC%')->get();
        $sub_section = OrganizationStructure::where('status', 'LIKE', 'SSC%')->get();
        $group = OrganizationStructure::where('status', 'LIKE', 'GRP%')->get();
        $cc = CostCenter::select('cost_center')->groupBy('cost_center')->get();

        $response = array(
            'status' => true,
            'mutation_logs' => $mutation_logs[0],
            'devision' => $devision,
            'department' => $department,
            'section' => $section,
            'sub_section' => $sub_section,
            'group' => $group,
            'cost_center' => $cc,
        );
        return Response::json($response);
    }

    public function changeMutation(Request $request)
    {
        $emp_id = $request->get('emp_id');

        $data = MutationLog::where('employee_id', '=', $emp_id)
            ->latest()
            ->first();
        $data->valid_to = $request->get('valid_to');
        $data->save();

        $mutation = new MutationLog([
            'employee_id' => $emp_id,
            'cost_center' => $request->get('cc'),
            'division' => $request->get('division'),
            'department' => $request->get('department'),
            'section' => $request->get('section'),
            'sub_section' => $request->get('subsection'),
            'group' => $request->get('group'),
            'reason' => $request->get('reason'),
            'valid_from' => $request->get('valid_from'),
            'created_by' => 1,
        ]);

        $mutation->save();

        $response = array(
            'status' => true,
            'data' => $mutation,
        );
        return Response::json($response);
    }

    public function changeStatusEmployee(Request $request)
    {
        $emp_id = $request->get('emp_id');

        $data = MutationLog::where('employee_id', '=', $emp_id)
            ->latest()
            ->first();
        $data->valid_to = $request->get('valid_to');
        $data->save();

        $mutation = new MutationLog([
            'employee_id' => $emp_id,
            'cost_center' => $request->get('cc'),
            'division' => $request->get('division'),
            'department' => $request->get('department'),
            'section' => $request->get('section'),
            'sub_section' => $request->get('subsection'),
            'group' => $request->get('group'),
            'reason' => $request->get('reason'),
            'valid_from' => $request->get('valid_from'),
            'created_by' => 1,
        ]);

        $mutation->save();

        $response = array(
            'status' => true,
            'data' => $mutation,
        );
        return Response::json($response);
    }

    //end mutation_log

    // --------------------- Total Meeting Report -------------------------

    public function indexReportGender()
    {
        return view('employees.report.manpower_by_gender', array(
            'title' => 'Report Employee by Gender',
            'title_jp' => '従業員報告 男女別',
        )
        )->with('page', 'Manpower by Gender');
    }

    public function fetchReportGender()
    {
        $tgl = date('Y-m-d');
        $fiskal = "select fiscal_year from weekly_calendars WHERE week_date = '" . $tgl . "'";

        $get_fiskal = db::select($fiskal);

        $gender = "select mon, gender, sum(tot_karyawan) as tot_karyawan from
    (select mon, gender, count(if(if(date_format(a.hire_date, '%Y-%m') <= mon, 1, 0 ) - if(date_format(a.end_date, '%Y-%m') <= mon, 1, 0 ) = 0, null, 1)) as tot_karyawan from
    (
    select distinct fiscal_year, date_format(week_date, '%Y-%m') as mon
    from weekly_calendars
    ) as b
    join
    (
    select '" . $get_fiskal[0]->fiscal_year . "' as fy, end_date, hire_date, employee_id, gender
    from employees
    ) as a
    on a.fy = b.fiscal_year
    where mon <= date_format('" . $tgl . "','%Y-%m-%d')
    group by mon, gender
    union all
    select mon, gender, count(if(if(date_format(a.entry_date, '%Y-%m') <= mon, 1, 0 ) - if(date_format(a.end_date, '%Y-%m') <= mon, 1, 0 ) = 0, null, 1)) as tot_karyawan from
    (
    select distinct fiscal_year, date_format(week_date, '%Y-%m') as mon
    from weekly_calendars
    ) as b
    join
    (
    select '" . $get_fiskal[0]->fiscal_year . "' as fy, end_date, entry_date, nik, gender
    from outsources
    ) as a
    on a.fy = b.fiscal_year
    where mon <= date_format('" . $tgl . "','%Y-%m-%d')
    group by mon, gender) semua
    group by mon, gender";

        $get_manpower = db::select($gender);

        $response = array(
            'status' => true,
            'manpower_by_gender' => $get_manpower,
        );

        return Response::json($response);
    }

    public function fetchReportGender2(Request $request)
    {

        if (strlen($request->get('tgl')) > 0) {
            $tgl = $request->get("tgl");
        } else {
            $tgl = date("Y-m");
        }
        $gender = "select gender, count(employee_id) as jml from employees where DATE_FORMAT(end_date,'%Y-%m') >= '" . $tgl . "' or end_date is null group by gender";

        $get_manpower = db::select($gender);
        $monthTitle = date("F Y", strtotime($tgl));

        $response = array(
            'status' => true,
            'manpower_by_gender' => $get_manpower,
            'monthTitle' => $monthTitle,
        );

        return Response::json($response);
    }

    public function fetchReportStatus()
    {
        $tanggal = date('Y-m');

        $fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '" . $tanggal . "' group by fiscal_year";

        $fy = db::select($fiskal);

        $statusS = "select count(c.employee_id) as emp, status, mon from
    (select * from
    (
    select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
    cross join (
    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') <= '" . $tanggal . "' group by date_format(week_date, '%Y-%m')) s
    ) m
    where hire_month <= mon and (mon < end_month OR end_month is null)
    ) as b
    left join
    (
    select id, employment_logs.employee_id, employment_logs.status, date_format(employment_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(employment_logs.valid_to, '%Y-%m'), date_format(now(), '%Y-%m')) as mon_to from employment_logs
    WHERE id IN (
    SELECT MAX(id)
    FROM employment_logs
    GROUP BY employment_logs.employee_id, date_format(employment_logs.valid_from, '%Y-%m')
    )
    ) as c on b.employee_id = c.employee_id
    where mon_from <= mon and mon_to >= mon
    group by mon, status
    union all
    select count(name) as emp, 'OUTSOURCES' as status, mon from
    (
    select name, date_format(entry_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from outsources
    cross join (
    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') <= '" . $tanggal . "' group by date_format(week_date, '%Y-%m')) s
    ) m
    where hire_month <= mon and (mon < end_month OR end_month is null)
    group by mon";

        $get_manpower_status = db::select($statusS);

        $response = array(
            'status' => true,
            'manpower_by_status_stack' => $get_manpower_status,
        );

        return Response::json($response);
    }

    public function reportSerikat()
    {
        $tanggal = date('Y-m');

        $fiskal = "select fiscal_year from weekly_calendars WHERE date_format(week_date,'%Y-%m') = '" . $tanggal . "' group by fiscal_year";

        $fy = db::select($fiskal);

        $get_union = "select count(employee_id) as emp_tot, serikat, mon from
    ( select emp.employee_id, COALESCE(serikat,'NON UNION') serikat, mon, COALESCE(mon_from,mon) mon_from, COALESCE(mon_to,mon) mon_to from
    (select * from
    (
    select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
    cross join (
    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') <= '" . $tanggal . "' group by date_format(week_date, '%Y-%m')) s
    ) m
    where hire_month <= mon and (mon < end_month OR end_month is null)
    ) as emp
    join
    (
    select id, labor_union_logs.employee_id, labor_union_logs.`union` as serikat, date_format(labor_union_logs.valid_from, '%Y-%m') as mon_from, coalesce(date_format(labor_union_logs.valid_to, '%Y-%m'), date_format(now(), '%Y-%m')) as mon_to from labor_union_logs
    WHERE id IN (
    SELECT MAX(id)
    FROM labor_union_logs
    GROUP BY labor_union_logs.employee_id, date_format(labor_union_logs.valid_from, '%Y-%m')
    )
    ) uni on emp.employee_id = uni.employee_id
    ) semua
    where mon_from <= mon and mon_to >= mon
    group by mon, serikat
    union all
    select count(employee_id) as emp_tot, 'NON UNION' as serikat, mon from
    (
    select employee_id, date_format(hire_date, '%Y-%m') as hire_month, date_format(end_date, '%Y-%m') as end_month, mon from employees
    cross join (
    select date_format(weekly_calendars.week_date, '%Y-%m') as mon from weekly_calendars where fiscal_year = '" . $fy[0]->fiscal_year . "' and date_format(week_date, '%Y-%m') <= '" . $tanggal . "' group by date_format(week_date, '%Y-%m')) s
    ) m
    where hire_month <= mon and (mon < end_month OR end_month is null) and employee_id not in (select employee_id from labor_union_logs)
    group by mon
    order by mon asc, serikat desc";

        $union = db::select($get_union);

        $response = array(
            'status' => true,
            'manpower_by_serikat' => $union,
        );

        return Response::json($response);

    }

    // --------------------- End Total Meeting Report ---------------------

    // --------------------- Start Employement ---------------------
    public function indexEmployment()
    {
        return view('employees.master.indexEmployment')->with('page', 'Employement');
    }
    // --------------------- End Employement -----------------------

    // -------------------------  Start Employee Service ------------------
    public function indexEmployeeService(Request $request)
    {
        $title = 'Employee Self Services';
        $title_jp = '従業員の情報サービス';
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);
        $now = date('Y-m-d');
        $press = [];

        $profil = db::select("select * from employee_syncs where employee_id = '" . $emp_id . "'");

        if ($request->get('tahun')) {
            $tahun = $request->get('tahun');
        } else {
            $tahun = date('Y');
        }

        try {
            // $presences = db::connection('sunfish')->select("SELECT
            //  Emp_no,
            //  format ( shiftstarttime, 'yyyy-MM' ) AS orderer,
            //  format ( shiftstarttime, 'MMMM yyyy' ) AS periode,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%ABS%', 1, NULL )) AS mangkir,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%', 1, NULL )) AS cuti,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%', 1, NULL )) AS izin,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%SAKIT%' OR Attend_Code LIKE '%SD%', 1, NULL )) AS sakit,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 1, NULL )) AS terlambat,
            //  COUNT (
            //  IIF ( Attend_Code LIKE '%PC%', 1, NULL )) AS pulang_cepat,
            //  COUNT (
            //  IIF (
            //  Attend_Code LIKE '%ABS%'
            //  OR Attend_Code LIKE '%CK10%'
            //  OR Attend_Code LIKE '%CK11%'
            //  OR Attend_Code LIKE '%CK12%'
            //  OR Attend_Code LIKE '%Izin%'
            //  OR Attend_Code LIKE '%Mangkir%'
            //  OR Attend_Code LIKE '%PC%'
            //  OR Attend_Code LIKE '%SAKIT%'
            //  OR Attend_Code LIKE '%UPL%'
            //  OR Attend_Code LIKE '%LTI%'
            //  OR Attend_Code LIKE '%TELAT%',
            //  1,
            //  NULL
            //  )) AS tunjangan,
            //  SUM ( ROUND( total_ot / 60.0, 2 ) ) as overtime
            //  FROM
            //  VIEW_YMPI_Emp_Attendance
            //  WHERE
            //  Emp_no = '" . $emp_id . "'
            //  AND YEAR ( shiftstarttime ) = '" . $tahun . "'
            //  AND shiftstarttime <= '" . $now . "'
            //  GROUP BY
            //  format ( shiftstarttime, 'MMMM yyyy' ),
            //  format ( shiftstarttime, 'yyyy-MM' ),
            //  Emp_no
            //  ORDER BY
            //  orderer ASC");

            $prs = db::connection('sunfish')
                ->select("SELECT Emp_no, format ( shiftstarttime, 'yyyy-MM' ) AS dt, format ( shiftstarttime, 'MMMM yyyy' ) AS periode,
          COUNT (
          IIF ( Attend_Code LIKE '%ABS%', 1, NULL )) AS mangkir,
          COUNT (
          IIF ( Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%', 1, NULL )) AS cuti,
          COUNT (
          IIF ( Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%', 1, NULL )) AS izin,
          COUNT (
          IIF ( Attend_Code LIKE '%SAKIT%' OR Attend_Code LIKE '%SD%', 1, NULL )) AS sakit,
          COUNT (
          IIF ( Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%', 1, NULL )) AS terlambat,
          COUNT (
          IIF ( Attend_Code LIKE '%PC%', 1, NULL )) AS pulang_cepat,
          COUNT (
          IIF (
          Attend_Code LIKE '%ABS%'
          OR Attend_Code LIKE '%CK10%'
          OR Attend_Code LIKE '%CK11%'
          OR Attend_Code LIKE '%CK12%'
          OR Attend_Code LIKE '%Izin%'
          OR Attend_Code LIKE '%Mangkir%'
          OR Attend_Code LIKE '%PC%'
          OR Attend_Code LIKE '%SAKIT%'
          OR Attend_Code LIKE '%UPL%'
          OR Attend_Code LIKE '%LTI%'
          OR Attend_Code LIKE '%TELAT%',
          1,
          NULL
          )) AS tunjangan
          FROM VIEW_YMPI_Emp_Attendance
          WHERE Emp_no = '" . $emp_id . "' AND YEAR ( shiftstarttime ) = '" . $tahun . "' AND shiftstarttime <= '" . $now . "'
          GROUP BY
          format ( shiftstarttime, 'MMMM yyyy' ),
          format ( shiftstarttime, 'yyyy-MM' ),
          Emp_no
          ORDER BY format ( shiftstarttime, 'yyyy-MM' ) ASC");

            $ovt = db::connection('sunfish')->select("
            SELECT emp_no, format ( convert(date, dt), 'yyyy-MM') as dts, SUM(ot) as ots,
	        SUM ( ot_satuan ) AS satuan_ot
            from (
            SELECT emp_no, format ( VIEW_YMPI_Emp_OvertimePlan.ovtplanfrom, 'yyyy-MM-dd') as dt, IIF (
            total_ot IS NOT NULL,
            CAST( VIEW_YMPI_Emp_OvertimePlan.total_ot / 60.0 AS FLOAT), 0) AS ot,
		    IIF ( total_ot IS NOT NULL, CAST ( VIEW_YMPI_Emp_OvertimePlan.total_otindex AS FLOAT ), 0 ) AS ot_satuan
            from VIEW_YMPI_Emp_OvertimePlan where emp_no =  '" . $emp_id . "' AND YEAR (ovtplanfrom) = '" . $tahun . "'
            )   ovts
            GROUP BY emp_no, format (  convert(date, dt), 'yyyy-MM')");

            foreach ($prs as $prs2) {
                $stat_ot = 0;
                $satuan_ot = 0;
                foreach ($ovt as $ot) {
                    if ($prs2->dt == $ot->dts) {
                        $stat_ot = $ot->ots;
                        $satuan_ot = $ot->satuan_ot;
                    }
                }

                array_push($press, [
                    'periode' => $prs2->periode,
                    'mangkir' => $prs2->mangkir,
                    'izin' => $prs2->izin,
                    'sakit' => $prs2->sakit,
                    'terlambat' => $prs2->terlambat,
                    'cuti' => $prs2->cuti,
                    'pulang_cepat' => $prs2->pulang_cepat,
                    'tunjangan' => $prs2->tunjangan,
                    'overtime' => $stat_ot,
                    'satuan_ot' => $satuan_ot,
                ]);
            }

            $employee = db::connection('sunfish')->select("SELECT
         SUM(VIEW_YMPI_LEAVE_BALANCE.remaining) as remaining
         FROM
         VIEW_YMPI_LEAVE_BALANCE
         WHERE
         VIEW_YMPI_LEAVE_BALANCE.emp_no = '" . $emp_id . "'
         AND VIEW_YMPI_LEAVE_BALANCE.startvaliddate <= '" . $now . "'
         AND VIEW_YMPI_LEAVE_BALANCE.endvaliddate >= '" . $now . "'");

            $certificate = DB::connection('ympimis_2')->select("SELECT DISTINCT
         ( qa_certificates.certificate_id )
         FROM
         qa_certificates
         WHERE
         employee_id = '" . $emp_id . "'
         AND `status` = 1 UNION ALL
         SELECT DISTINCT
         ( qa_certificate_inprocesses.certificate_id )
         FROM
         qa_certificate_inprocesses
         WHERE
         employee_id = '" . $emp_id . "'
         AND `status` = 1");
        } catch (\Exception$e) {

        }

        if (isset($prs)) {
            return view('employees.service.indexEmploymentService', array(
                'status' => true,
                'title' => $title,
                'title_jp' => $title_jp,
                'emp_id' => $emp_id,
                'profil' => $profil,
                'presences' => $press,
                'employee' => $employee,
                'certificate' => $certificate,
            )
            )->with('page', 'Employment Services');
        } else {
            return view('employees.service.indexEmploymentService', array(
                'status' => true,
                'title' => $title,
                'title_jp' => $title_jp,
                'emp_id' => $emp_id,
                'profil' => $profil,
                'certificate' => $certificate,
                // 'presences' => $presences,
// 'employee' => $employee,
            )
            )->with('page', 'Employment Services');
        }
    }

    public function fetchChat(Request $request)
    {
        $data = HrQuestionLog::leftJoin('hr_question_details', 'hr_question_details.message_id', '=', 'hr_question_logs.id')
            ->where('hr_question_logs.created_by', '=', $request->get('employee_id'))
            ->select('hr_question_logs.id', 'hr_question_logs.message', 'hr_question_logs.category', 'hr_question_logs.created_at', db::raw('date_format(hr_question_logs.created_at, "%b %d, %H:%i") as created_at_new'), db::raw('hr_question_details.message as message_detail'), db::raw('hr_question_details.created_by as dari'), db::raw('hr_question_details.created_at as reply_date'), db::raw('SPLIT_STRING(IF(hr_question_details.created_by is null, hr_question_logs.created_by, hr_question_details.created_by) ,"_",1) as avatar'))
            ->orderBy('hr_question_logs.updated_at', 'desc')
            ->orderBy('hr_question_details.created_at', 'asc')
            ->get();

        $response = array(
            'status' => true,
            'chats' => $data,
            // 'tes' => $obj,
            'base_avatar' => url('images/avatar/'),
        );

        return Response::json($response);
    }

    public function postChat(Request $request)
    {
        $quest = new HrQuestionLog([
            'message' => $request->get('message'),
            'category' => $request->get('category'),
            'created_by' => $request->get('from'),
            'remark' => 1,
        ]);

        $quest->save();

        $response = array(
            'status' => true,
        );

        return Response::json($response);
    }

    public function postComment(Request $request)
    {

        $id = $request->get('id');

        if ($request->get("from") == "HR") {
            $remark = 0;
        } else {
            $remark = 1;
        }

        try {
            $questDetail = new HrQuestionDetail([
                'message' => $request->get('message'),
                'message_id' => $id,
                'created_by' => $request->get("from"),
            ]);

            $questDetail->save();

            HrQuestionLog::where('id', $id)
                ->update(['remark' => $remark]);

            $response = array(
                'status' => true,
                'remark' => $remark,
            );

            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => 'Error',
            );

            return Response::json($response);
        }

    }
    // -------------------------  End Employee Service --------------------

    public function indexReportStatus()
    {
        return view('employees.report.employee_status', array(
            'title' => 'Report Employee by Status Kerja',
            'title_jp' => '従業員報告 ステータス別',
        )
        )->with('page', 'Manpower by Status Kerja');
    }

    public function indexReportManpower()
    {
        return view('employees.report.manpower', array(
            'title' => 'Manpower Information',
            'title_jp' => '人工の情報',
        )
        )->with('page', 'Manpower Report');
    }

    public function indexReportGrade()
    {
        return view('employees.report.employee_status', array(
            'title' => 'Report Employee by Grade',
            'title_jp' => '従業員報告 グレード別',
        )
        )->with('page', 'Manpower by Grade');
    }

    public function indexReportDepartment()
    {
        return view('employees.report.employee_status', array(
            'title' => 'Report Employee by Department',
            'title_jp' => '従業員報告 部門別',
        )
        )->with('page', 'Manpower by Department');
    }

    public function indexReportJabatan()
    {
        return view('employees.report.employee_status', array(
            'title' => 'Report Employee by Jabatan',
            'title_jp' => '従業員報告 役職別',
        )
        )->with('page', 'Manpower by jabatan');
    }

    public function fetchReportManpower(Request $request)
    {
        $date = date('Y-m-d');
        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $employees = db::connection("sunfish")->select("SELECT
      Emp_no,
      Full_name,
      employ_code,
      Department,
      Section,
      Groups,
      Sub_Groups,
      start_date,
      grade_code,
      CASE
      WHEN
      pos_name_en LIKE 'Sub Leader%' THEN
      'Sub Leader'
      WHEN pos_name_en LIKE 'Leader%' THEN
      'Leader'
      WHEN pos_name_en LIKE 'Senior Staff%' THEN
      'Senior Staff'
      WHEN pos_name_en LIKE 'Staff%' THEN
      'Staff'
      WHEN pos_name_en LIKE 'Operator Outsource%' THEN
      'Operator Outsource'
      WHEN pos_name_en LIKE 'Operator Contract%' THEN
      'Operator Contract'
      WHEN pos_name_en LIKE 'Senior Operator%' THEN
      'Senior Operator'
      WHEN pos_name_en LIKE 'Operator%' THEN
      'Operator'
      WHEN pos_name_en LIKE 'Senior Coordinator%' THEN
      'Senior Coordinator'
      WHEN pos_name_en LIKE 'Coordinator%' THEN
      'Coordinator'
      WHEN pos_name_en LIKE 'Junior Specialist%' THEN
      'Junior Specialist'
      WHEN pos_name_en LIKE 'Senior Specialist%' THEN
      'Senior Specialist'
      WHEN pos_name_en LIKE 'Specialist%' THEN
      'Specialist'
      WHEN pos_name_en LIKE 'Deputy Foreman%' THEN
      'Deputy Foreman'
      WHEN pos_name_en LIKE 'Foreman%' THEN
      'Foreman'
      WHEN pos_name_en LIKE 'Senior Chief%' THEN
      'Senior Chief'
      WHEN pos_name_en LIKE 'Deputy Chief%' THEN
      'Deputy Chief'
      WHEN pos_name_en LIKE 'Chief%' THEN
      'Chief'
      WHEN pos_name_en LIKE 'Deputy General Manager%' THEN
      'Deputy General Manager'
      WHEN pos_name_en LIKE 'General Manager%' THEN
      'General Manager'
      WHEN pos_name_en LIKE 'Asst. Manager%' THEN
      'Asst. Manager'
      WHEN pos_name_en LIKE 'Assistant Manager%' THEN
      'Assistant Manager'
      WHEN pos_name_en LIKE 'Manager%' THEN
      'Manager'
      WHEN pos_name_en LIKE 'President Director%' THEN
      'President Director'
      WHEN pos_name_en LIKE 'Vice President%' THEN
      'Vice President'
      WHEN pos_name_en LIKE 'Director%' THEN
      'Director'
      ELSE 'Undefined'
      END AS pos_name_en,
      gender,
      cost_center_code,
      CASE
      WHEN [Labour_Union] IS NULL THEN
      'NONE'
      WHEN [Labour_Union] = '' THEN
      'NONE'
      ELSE [Labour_Union]
      END AS [union]
      FROM
      [dbo].[VIEW_YMPI_Emp_OrgUnit]
      WHERE
      start_date < '" . $date . " 00:00:00'
      AND (end_date IS NULL or end_date >= '" . $date . " 00:00:00')
      ORDER BY
      employ_code ASC
      ");

        $cost_centers = db::table('cost_centers2')->get();

        $manpowers = array();

        foreach ($employees as $employee) {
            $Emp_no = $employee->Emp_no;
            $Full_name = $employee->Full_name;
            $employ_code = $employee->employ_code;
            $Department = $employee->Department;
            $grade_code = $employee->grade_code;
            $pos_name_en = $employee->pos_name_en;
            $gender = $employee->gender;
            $cost_center_code = $employee->cost_center_code;
            $union = $employee->union;
            $category = '';
            $Section = $employee->Section;
            $Groups = $employee->Groups;
            $Sub_Groups = $employee->Sub_Groups;
            $start_date = $employee->start_date;

            foreach ($cost_centers as $cost_center) {
                if ($cost_center->cost_center == $employee->cost_center_code) {
                    $category = $cost_center->remark;
                }
            }

            array_push($manpowers, [
                'Emp_no' => $Emp_no,
                'Full_name' => $Full_name,
                'employ_code' => $employ_code,
                'Department' => $Department,
                'Section' => $Section,
                'Groups' => $Groups,
                'Sub_Groups' => $Sub_Groups,
                'start_date' => $start_date,
                'grade_code' => $grade_code,
                'pos_name_en' => $pos_name_en,
                'gender' => $gender,
                'cost_center_code' => $cost_center_code,
                'union' => $union,
                'category' => $category,
            ]);
        }

        $by_departments = db::connection('sunfish')->select("SELECT
     *
      FROM
      (
      SELECT
      IIF ( Department IS NULL, NULL, Division ) AS Division,
      IIF ( Department IS NULL, 'Management', Department ) AS Department,
      COUNT ( Emp_no ) AS total
      FROM
      VIEW_YMPI_Emp_OrgUnit
      WHERE
      start_date < '" . $date . " 00:00:00'
      AND (end_date IS NULL or end_date >= '" . $date . " 00:00:00')
      GROUP BY
      IIF ( Department IS NULL, NULL, Division ),
      IIF ( Department IS NULL, 'Management', Department )
      ) AS by_department
      ORDER BY
      Division ASC,
      total DESC,
      Department ASC");

        $by_positions = db::connection('sunfish')->select("SELECT
        CASE
        WHEN
        pos_name_en LIKE 'Sub Leader%' THEN
        'Sub Leader'
        WHEN pos_name_en LIKE 'Leader%' THEN
        'Leader'
        WHEN pos_name_en LIKE 'Senior Staff%' THEN
        'Senior Staff'
        WHEN pos_name_en LIKE 'Staff%' THEN
        'Staff'
        WHEN pos_name_en LIKE 'Operator Outsource%' THEN
        'Operator Outsource'
        WHEN pos_name_en LIKE 'Operator Contract%' THEN
        'Operator Contract'
        WHEN pos_name_en LIKE 'Senior Operator%' THEN
        'Senior Operator'
        WHEN pos_name_en LIKE 'Operator%' THEN
        'Operator'
        WHEN pos_name_en LIKE 'Senior Coordinator%' THEN
        'Senior Coordinator'
        WHEN pos_name_en LIKE 'Coordinator%' THEN
        'Coordinator'
        WHEN pos_name_en LIKE 'Junior Specialist%' THEN
        'Junior Specialist'
        WHEN pos_name_en LIKE 'Senior Specialist%' THEN
        'Senior Specialist'
        WHEN pos_name_en LIKE 'Specialist%' THEN
        'Specialist'
        WHEN pos_name_en LIKE 'Deputy Foreman%' THEN
        'Deputy Foreman'
        WHEN pos_name_en LIKE 'Foreman%' THEN
        'Foreman'
        WHEN pos_name_en LIKE 'Senior Chief%' THEN
        'Senior Chief'
        WHEN pos_name_en LIKE 'Deputy Chief%' THEN
        'Deputy Chief'
        WHEN pos_name_en LIKE 'Chief%' THEN
        'Chief'
        WHEN pos_name_en LIKE 'Deputy General Manager%' THEN
        'Deputy General Manager'
        WHEN pos_name_en LIKE 'General Manager%' THEN
        'General Manager'
        WHEN pos_name_en LIKE 'Asst. Manager%' THEN
        'Asst. Manager'
        WHEN pos_name_en LIKE 'Assistant Manager%' THEN
        'Assistant Manager'
        WHEN pos_name_en LIKE 'Manager%' THEN
        'Manager'
        WHEN pos_name_en LIKE 'President Director%' THEN
        'President Director'
        WHEN pos_name_en LIKE 'Vice President%' THEN
        'Vice President'
        WHEN pos_name_en LIKE 'Director%' THEN
        'Director'
        ELSE 'Undefined'
        END AS pos_name_en,
        COUNT ( Emp_no ) AS total
        FROM
        VIEW_YMPI_Emp_OrgUnit
        WHERE
        start_date < '" . $date . " 00:00:00'
        AND (end_date IS NULL or end_date >= '" . $date . " 00:00:00')
        GROUP BY
        pos_name_en
        ORDER BY
        CASE
        WHEN pos_name_en LIKE 'Sub Leader%' THEN
        5
        WHEN pos_name_en LIKE 'Leader%' THEN
        6
        WHEN pos_name_en LIKE 'Senior Staff%' THEN
        8
        WHEN pos_name_en LIKE 'Staff%' THEN
        7
        WHEN pos_name_en LIKE 'Operator Outsource%' THEN
        1
        WHEN pos_name_en LIKE 'Operator Contract%' THEN
        2
        WHEN pos_name_en LIKE 'Senior Operator%' THEN
        4
        WHEN pos_name_en LIKE 'Operator%' THEN
        3
        WHEN pos_name_en LIKE 'Senior Coordinator%' THEN
        10
        WHEN pos_name_en LIKE 'Coordinator%' THEN
        9
        WHEN pos_name_en LIKE 'Junior Specialist%' THEN
        16
        WHEN pos_name_en LIKE 'Senior Specialist%' THEN
        18
        WHEN pos_name_en LIKE 'Specialist%' THEN
        17
        WHEN pos_name_en LIKE 'Deputy Foreman%' THEN
        11
        WHEN pos_name_en LIKE 'Foreman%' THEN
        12
        WHEN pos_name_en LIKE 'Senior Chief%' THEN
        15
        WHEN pos_name_en LIKE 'Deputy Chief%' THEN
        13
        WHEN pos_name_en LIKE 'Chief%' THEN
        14
        WHEN pos_name_en LIKE 'Deputy General Manager%' THEN
        22
        WHEN pos_name_en LIKE 'General Manager%' THEN
        23
        WHEN pos_name_en LIKE 'Asst. Manager%' THEN
        19
        WHEN pos_name_en LIKE 'Assistant Manager%' THEN
        20
        WHEN pos_name_en LIKE 'Manager%' THEN
        21
        WHEN pos_name_en LIKE 'President Director%' THEN
        26
        WHEN pos_name_en LIKE 'Vice President%' THEN
        25
        WHEN pos_name_en LIKE 'Director%' THEN
        24
        ELSE 27
        END");

        $departments = Department::get();

        $response = array(
            'status' => true,
            'manpowers' => $manpowers,
            'by_departments' => $by_departments,
            'by_positions' => $by_positions,
            'departments' => $departments,
        );
        return Response::json($response);
    }

    public function fetchReportManpowerDetail(Request $request)
    {
        $date = date('Y-m-d');
        if (strlen($request->get('date')) > 0) {
            $date = date('Y-m-d', strtotime($request->get('date')));
        }

        $where = "";
        $where = "and " . $request->get('filter') . " = '" . $request->get('category') . "'";

        if ($request->get('filter') == 'Department' && $request->get('category') == 'Management') {
            $where = "and Department is null";
        }

        if ($request->get('filter') == 'Labour_Union' && $request->get('category') == 'NONE') {
            $where = "and ([Labour_Union] = 'NONE' or [Labour_Union] = '')";
        }

        $manpowers = db::connection("sunfish")->select("SELECT
        Emp_no,
        Full_name,
        Division,
        Department,
        cost_center_code,
        Section,
        Groups,
        Sub_Groups,
        CONVERT ( VARCHAR, start_date, 105 ) AS start_date,
        employ_code,
        grade_code,
        pos_name_en,
        gender,
        CASE
        WHEN [Labour_Union] IS NULL THEN
        'NONE' ELSE [Labour_Union]
        END AS [union]
        FROM
        [dbo].[VIEW_YMPI_Emp_OrgUnit]
        WHERE
        (end_date IS NULL or end_date >= '" . $date . " 00:00:00') " . $where . "
        ORDER BY
        Emp_no ASC");

        $response = array(
            'status' => true,
            'details' => $manpowers,
        );
        return Response::json($response);
    }

    public function fetchReport(Request $request)
    {

        if (strlen($request->get('tgl')) > 0) {
            $tgl = $request->get("tgl");
        } else {
            $tgl = date("Y-m");
        }

        if ($request->get("ctg") == 'Report Employee by Status Kerja') {

            $emp = db::select("SELECT count( emp.employee_id ) jml, emp.`employment_status` as `status`
         FROM ( SELECT employee_id,employment_status FROM employee_syncs WHERE DATE_FORMAT( end_date, '%Y-%m' ) >= '" . $tgl . "' OR end_date IS NULL ) emp
         GROUP BY
         emp.`employment_status` ");
        } else if ($request->get("ctg") == 'Report Employee by Grade') {
            $emp = db::select("SELECT
            count( emp.employee_id ) jml,
            emp.`grade_code` AS `status`
            FROM
            ( SELECT employee_id, grade_code FROM employee_syncs WHERE DATE_FORMAT( end_date, '%Y-%m' ) >= '" . $tgl . "' OR end_date IS NULL ) emp
            GROUP BY
            emp.`grade_code`
            ORDER BY
            grade_code ASC");
        } else if ($request->get("ctg") == 'Report Employee by Department') {
            $emp = db::select("SELECT
           count( emp.employee_id ) jml,
           COALESCE ( departments.`department_shortname`, 'MNGT' ) AS `status`
           FROM
           ( SELECT employee_id, department FROM employee_syncs WHERE DATE_FORMAT( end_date, '%Y-%m' ) >= '" . $tgl . "' OR end_date IS NULL ) emp
           LEFT JOIN departments
           on departments.department_name = emp.department
           GROUP BY
           departments.`department_shortname`
           ORDER BY
           jml ASC");
        } else if ($request->get("ctg") == 'Report Employee by Jabatan') {
            $emp = db::select("
          SELECT
          count( emp.employee_id ) jml,
          emp.`position` AS `status`
          FROM
          ( SELECT employee_id, position FROM employee_syncs WHERE DATE_FORMAT( end_date, '%Y-%m' ) >= '" . $tgl . "' OR end_date IS NULL ) emp
          GROUP BY
          emp.`position`
          ORDER BY
          jml DESC");
        }

        $monthTitle = date("F Y", strtotime($tgl));

        $response = array(
            'status' => true,
            'datas' => $emp,
            'ctg' => $request->get("ctg"),
            'monthTitle' => $monthTitle,

        );

        return Response::json($response);
    }

    public function detailReport(Request $request)
    {
        $kondisi = $request->get("kondisi");

        if ($request->get("by") == 'Report Employee by Status Kerja') {
            $query = "SELECT * FROM employee_syncs where employee_syncs.end_date is null and employee_syncs.`employment_status` = '" . $kondisi . "'";

        } elseif ($request->get("by") == 'Report Employee by Department') {
            if ($kondisi == "MNGT") {
                $query = "SELECT * FROM employee_syncs WHERE employee_syncs.end_date IS NULL AND employee_syncs.`department` is null ";
            } else {
                $query = "SELECT * FROM employee_syncs JOIN departments on departments.department_name = employee_syncs.department WHERE employee_syncs.end_date IS NULL AND departments.`department_shortname` = '" . $kondisi . "'";
            }

        } elseif ($request->get("by") == 'Report Employee by Grade') {
            $query = "SELECT * FROM employee_syncs where employee_syncs.end_date is null and employee_syncs.grade_code = '" . $kondisi . "'";
        } elseif ($request->get("by") == 'Report Employee by Jabatan') {
            $query = "SELECT * FROM employee_syncs where employee_syncs.end_date is null and employee_syncs.position = '" . $kondisi . "'";
        }

        $detail = db::select($query);

        return DataTables::of($detail)->make(true);

    }

    public function exportBagian()
    {
        $bagian = Mutationlog::select("employee_id", "cost_center", "division", "department", "section", "sub_section", "group")
        // ->whereIn('id', db::raw(""))
            ->whereRaw('id in (SELECT MAX(id) FROM mutation_logs GROUP BY employee_id)')
            ->get()
            ->toArray();

        $bagian_array[] = array('employee_id', 'cost_center', 'division', 'department', 'section', 'sub_section', 'group');

        foreach ($bagian as $key) {
            $bagian_array[] = array(
                'employee_id' => $key['employee_id'],
                'cost_center' => $key['cost_center'],
                'division' => $key['division'],
                'department' => $key['department'],
                'section' => $key['section'],
                'sub_section' => $key['sub_section'],
                'group' => $key['group'],
            );
        }

        Excel::create('Bagian', function ($excel) use ($bagian_array) {
            $excel->setTitle('Bagian List');
            $excel->sheet('Employee Bagian Data', function ($sheet) use ($bagian_array) {
                $sheet->fromArray($bagian_array, null, 'A1', false, false);
            });
        })->download('xlsx');
    }

    public function importBagian(Request $request)
    {
        $id = Auth::id();
        try {

            if ($request->hasFile('importBagian')) {
                $file = $request->file('importBagian');
                $data = file_get_contents($file);
                $rows = explode("\r\n", $data);

                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);

                        $date_from = date("Y-m-d", strtotime($row[7]));
                        $date = DateTime::createFromFormat('d/m/Y', $row[7]);

                        $date_from = $date->format('Y-m-d');

                        date_sub($date, date_interval_create_from_date_string('1 days'));

                        $date_to = $date->format('Y-m-d');
                        Mutationlog::where('employee_id', $row[0])
                            ->orderBy('id', 'desc')
                            ->take(1)
                            ->update(['valid_to' => $date_to]);

                        $bagian = new Mutationlog([
                            'employee_id' => $row[0],
                            'cost_center' => $row[1],
                            'division' => $row[2],
                            'department' => $row[3],
                            'section' => $row[4],
                            'sub_section' => $row[5],
                            'group' => $row[6],
                            'valid_from' => $date_from,
                            'created_by' => $id,
                        ]);

                        $bagian->save();
                    }
                }
            }
            return redirect('/index/MasterKaryawan')->with('status', 'Update Bagian Employee Success')->with('page', 'Master Employee');
        } catch (QueryException $e) {
            // $emp = PresenceLog::where('presence_date','=',$tgl)
// ->forceDelete();
            return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
        }

    }

    public function importKaryawan(Request $request)
    {
        $id = Auth::id();
        try {
            if ($request->hasFile('importEmployee')) {
                $file = $request->file('importEmployee');
                $data = file_get_contents($file);
                $rows = explode("\r\n", $data);

                foreach ($rows as $row) {
                    if (strlen($row) > 0) {
                        $row = explode("\t", $row);

                        $date_from = date("Y-m-d", strtotime($row[7]));
                        $date = DateTime::createFromFormat('d/m/Y', $row[7]);

                        $date_from = $date->format('Y-m-d');

                        date_sub($date, date_interval_create_from_date_string('1 days'));

                        $date_to = $date->format('Y-m-d');
                        Mutationlog::where('employee_id', $row[0])
                            ->orderBy('id', 'desc')
                            ->take(1)
                            ->update(['valid_to' => $date_to]);

                        $bagian = new Mutationlog([
                            'employee_id' => $row[0],
                            'cost_center' => $row[1],
                            'division' => $row[2],
                            'department' => $row[3],
                            'section' => $row[4],
                            'sub_section' => $row[5],
                            'group' => $row[6],
                            'valid_from' => $date_from,
                            'created_by' => $id,
                        ]);

                        $bagian->save();
                    }
                }
            }
            return redirect('/index/MasterKaryawan')->with('status', 'Update Bagian Employee Success')->with('page', 'Master Employee');
        } catch (QueryException $e) {
            // $emp = PresenceLog::where('presence_date','=',$tgl)
// ->forceDelete();
            return redirect('/index/MasterKaryawan')->with('error', $e->getMessage())->with('page', 'Master Employee');
        }

    }

    //------------- Start DailyAttendance
    public function indexDailyAttendance()
    {
        return view('employees.report.daily_attendance', array(
            'title' => 'Attendance Rate',
            'title_jp' => '出勤率',
        )
        )->with('page', 'Daily Attendance');
    }

    public function fetchDailyAttendance(Request $request)
    {

        if (strlen($request->get('tgl')) > 0) {
            $tgl = $request->get("tgl");
        } else {
            $tgl = date("m-Y");
        }

        $queryAttendance = "SELECT  DATE_FORMAT(hadir.tanggal,'%d %b %Y') as tanggal, hadir.jml as hadir, tdk.jml as tdk from (SELECT tanggal, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' GROUP BY tanggal ) as hadir LEFT JOIN (SELECT tanggal, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' GROUP BY tanggal) as tdk on hadir.tanggal = tdk.tanggal";

        $attendanceData = db::connection('mysql3')->select($queryAttendance);

        $tgl = '01-' . $tgl;
        $titleChart = date("F Y", strtotime($tgl));

        $response = array(
            'status' => true,
            'titleChart' => $titleChart,
            'attendanceData' => $attendanceData,

        );
        return Response::json($response);

    }

    public function detailDailyAttendance(Request $request)
    {
        $tgl = date('d-m-Y', strtotime($request->get('tgl')));
        $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.masuk, presensi.keluar, presensi.shift from presensi
    LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
    LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
    WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null ORDER BY presensi.nik";
        $detail = db::connection('mysql3')->select($query);

        return DataTables::of($detail)->make(true);
    }
    //------------- End DailyAttendance

    //------------- Start Presence
    public function indexPresence()
    {
        return view('employees.report.presence', array(
            'title' => 'Presence',
            'title_jp' => '出勤',
        )
        )->with('page', 'Presence Data');
    }

    public function fetchPresence(Request $request)
    {
        if (strlen($request->get('tgl')) > 0) {
            $tgl = date('d-m-Y', strtotime($request->get("tgl")));
        } else {
            $tgl = date("d-m-Y");
        }

        $query = "SELECT shift, COUNT(nik) as jml from presensi WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' GROUP BY shift";

        $presence = db::connection('mysql3')->select($query);
        $titleChart = date('j F Y', strtotime($tgl));

        $response = array(
            'status' => true,
            'presence' => $presence,
            'titleChart' => $titleChart,
            'tgl' => $tgl,
        );
        return Response::json($response);
    }

    public function detailPresence(Request $request)
    {
        $tgl = date('d-m-Y', strtotime($request->get('tgl')));
        $shift = $request->get('shift');

        $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.masuk, presensi.keluar, presensi.shift from presensi
    LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
    LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
    WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift  REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null and shift = '" . $shift . "' ORDER BY presensi.nik";
        $detail = db::connection('mysql3')->select($query);

        return DataTables::of($detail)->make(true);
    }
    //------------- End Presence

    //------------- Start Absence
    public function indexAbsence()
    {
        $dept = DB::SELECT("SELECT
     department_shortname
     FROM
     departments
     ORDER BY
     department_shortname");

        return view('employees.report.absence', array(
            'title' => 'Daily Attendance',
            'title_jp' => 'YMPI日常出勤まと',
            'absence_category' => $this->attend,
            'dept' => $dept,
        )
        )->with('page', 'Absence Data');
    }

    public function indexAbsenceMonitoring()
    {
        return view('employees.report.absence_monitoring', array(
            'title' => 'Daily Absence Monitoring',
            'title_jp' => '日次出席監視',
        )
        )->with('page', 'Absence Monitoring');
    }

    public function fetchAbsenceMonitoring(Request $request)
    {
        $datefrom;
        $dateto;
        $category = '';
        $category_covid = '';

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = $request->get('datefrom');
        } else {
            $datefrom = date('Y-m-d', strtotime('-30 day'));
        }

        if (strlen($request->get('dateto')) > 0) {
            $dateto = $request->get('dateto');
        } else {
            $dateto = date('Y-m-d');
        }

        if (strlen($request->get('category')) > 0) {
            $category = "WHERE cc.remark = '" . $request->get('category') . "'";
            $category_covid = "AND cc.remark = '" . $request->get('category') . "'";
        }

        $employee = db::select("SELECT emp.employee_id FROM employee_syncs emp
     LEFT JOIN cost_centers2 cc ON emp.cost_center = cc.cost_center " . $category);

        $arr_employee = [];
        for ($i = 0; $i < count($employee); $i++) {
            array_push($arr_employee, $employee[$i]->employee_id);
        }

        $absence = db::connection('sunfish')->select("SELECT format(shiftstarttime, 'yyyy-MM-dd') AS date, emp_no,
     IIF(Attend_Code LIKE '%Mangkir%', 1, 0) AS mangkir,
     IIF((Attend_Code NOT LIKE '%Izin%') AND (Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%'), 1, 0) AS cuti,
     IIF(Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%', 1, 0) AS izin,
     IIF(Attend_Code LIKE '%SAKIT%', 1, 0) AS sakit,
     IIF(remark LIKE '%ISOMAN%', 1, 0) AS isoman,
     IIF(Attend_Code LIKE '%COVID%', 1, 0) AS covid
     FROM VIEW_YMPI_ATTENDANCE
     WHERE format(shiftstarttime, 'yyyy-MM-dd') >= '" . $datefrom . "'
     AND format(shiftstarttime, 'yyyy-MM-dd') <= '" . $dateto . "'
     ORDER BY format(shiftstarttime, 'yyyy-MM-dd')");

        $covid = db::select("SELECT att.date, att.employee_id, att.attend_code, cc.remark FROM attendances att
     LEFT JOIN employee_syncs emp ON att.employee_id = emp.employee_id
     LEFT JOIN cost_centers2 cc ON cc.cost_center = emp.cost_center
     WHERE att.date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
     AND att.attend_code = 'COVID' " . $category_covid);

        $calendar = db::select("SELECT DAYNAME(week_date) AS day, week_date AS date FROM weekly_calendars
     WHERE week_date BETWEEN '" . $datefrom . "' AND '" . $dateto . "'
     AND remark <> 'H'
     ORDER BY week_date ASC");

        $data = [];

        for ($h = 0; $h < count($calendar); $h++) {
            $row = array();

            $mangkir = 0;
            $cuti = 0;
            $izin = 0;
            $sakit = 0;
            $isoman = 0;
            $positif = 0;

            for ($i = 0; $i < count($absence); $i++) {
                if ((in_array($absence[$i]->emp_no, $arr_employee)) && ($calendar[$h]->date == $absence[$i]->date)) {

                    $is_covid = false;
                    for ($j = 0; $j < count($covid); $j++) {
                        if (($covid[$j]->date == $absence[$i]->date) && ($covid[$j]->employee_id == $absence[$i]->emp_no)) {
                            $is_covid = true;
                            $positif++;
                            break;
                        }
                    }

                    if (!$is_covid) {
                        $mangkir += $absence[$i]->mangkir;
                        $cuti += $absence[$i]->cuti;
                        $izin += $absence[$i]->izin;
                        $sakit += $absence[$i]->sakit;
                        $isoman += $absence[$i]->isoman;
                        $positif += $absence[$i]->covid;
                    }
                }
            }

            $row['day'] = $calendar[$h]->day;
            $row['date'] = $calendar[$h]->date;
            $row['mangkir'] = $mangkir;
            $row['cuti'] = $cuti;
            $row['izin'] = $izin;
            $row['sakit'] = $sakit;
            $row['isoman'] = $isoman;
            $row['covid'] = $positif;
            array_push($data, $row);

        }

        $response = array(
            'status' => true,
            'data' => $data,
            'category' => $request->get('category'),
            'datefrom' => $datefrom,
            'dateto' => $dateto,
        );
        return Response::json($response);
    }

    public function fetchAbsenceMonitoringDetail(Request $request)
    {

        $attend_code = $request->get('attend_code');
        $full_date = explode(', ', $request->get('date'));
        $date = $full_date[1];

        $attend_code_query = '';
        if ($attend_code == 'Mangkir') {
            $attend_code_query = "A.Attend_Code LIKE '%Mangkir%'";
        } else if ($attend_code == 'Cuti') {
            $attend_code_query = "(Attend_Code NOT LIKE '%Izin%') AND (Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%')";
        } else if ($attend_code == 'Izin') {
            $attend_code_query = "Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%'";
        } else if ($attend_code == 'Sakit') {
            $attend_code_query = "Attend_Code LIKE '%SAKIT%'";
        } else if ($attend_code == 'Isoman') {
            $attend_code_query = "remark LIKE '%ISOMAN%'";
        } else if ($attend_code == 'Positif COVID-19') {
            if ($date < '2021-08-01') {
                $data = db::select("SELECT att.employee_id, emp.`name`, emp.department, emp.section, '-' AS shift_code, att.attend_code FROM attendances att
               LEFT JOIN employee_syncs emp  ON att.employee_id = emp.employee_id
               WHERE att.date = '" . $date . "'
               AND att.attend_code = 'COVID'");

                $response = array(
                    'status' => true,
                    'data' => $data,
                );
                return Response::json($response);
            } else {
                $attend_code_query = "Attend_Code LIKE '%COVID%'";
            }
        }

        $detail = db::connection('sunfish')->select("SELECT A.emp_no, A.full_name, A.shiftdaily_code, A.Attend_Code FROM VIEW_YMPI_ATTENDANCE A
     WHERE format(A.shiftstarttime, 'yyyy-MM-dd') = '" . $date . "'
     AND (" . $attend_code_query . ")
     ORDER BY A.full_name");

        $employee_id = [];
        for ($i = 0; $i < count($detail); $i++) {
            array_push($employee_id, $detail[$i]->emp_no);
        }

        $employees = EmployeeSync::whereIn('employee_id', $employee_id)->get();

        $data = [];
        for ($i = 0; $i < count($detail); $i++) {
            $row = array();

            for ($j = 0; $j < count($employees); $j++) {
                if ($detail[$i]->emp_no == $employees[$j]->employee_id) {
                    $row['employee_id'] = $detail[$i]->emp_no;
                    $row['name'] = $detail[$i]->full_name;
                    $row['department'] = $employees[$j]->department;
                    $row['section'] = $employees[$j]->section;
                    $row['shift_code'] = $detail[$i]->shiftdaily_code;
                    $row['attend_code'] = $detail[$i]->Attend_Code;
                }
            }

            array_push($data, $row);
        }

        $response = array(
            'status' => true,
            'data' => $data,
        );
        return Response::json($response);
    }

    public function fetchAbsence(Request $request)
    {
        $now = date("Y-m-d");
        if (strlen($request->get('date')) > 0) {
            $now = date('Y-m-d', strtotime($request->get("date")));
        }

        $total_shift_1 = 0;
        $total_shift_2 = 0;
        $total_shift_3 = 0;
        $total_off = 0;

        $attendances = db::connection('sunfish')->select("SELECT
       a.emp_no,
       e.Full_name as full_name,
       e.Department as department,
       e.Section AS sections,
       e.Groups AS groups,
       a.shiftdaily_code,
       e.employ_code,
       e.grade_code,
       IIF(a.shiftdaily_code like '%shift_1%', 'shift_1', IIF(a.shiftdaily_code like '%shift_2%', 'shift_2', IIF(a.shiftdaily_code like '%shift_3%', 'shift_3', IIF(a.shiftdaily_code like '%off%', 'off', 'shift_1')))) as shift,
       Attend_Code,
       SUM(IIF( a.remark LIKE '%isoman%', 1, 0 ) ) AS isoman,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 1, 0))) AS covid,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 1, 0)))) AS sakit,

       0 as izin,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 1, 0))))) +

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 1, 0)))))) AS cuti,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 1, 0))))))) AS wfh,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 1, 0)))))))) AS hadir,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 0, IIF( a.Attend_Code like '%ABS%', 1, 0))))))))) AS absen,

       SUM(IIF( a.remark LIKE '%isoman%', 0, IIF(a.Attend_Code LIKE '%covid%', 0, IIF( a.Attend_Code like '%sakit%', 0, IIF( (a.Attend_Code like '%IZIN%') or (a.Attend_Code like '%IPU%'), 0, IIF( (a.Attend_Code like '%CK%') or (a.Attend_Code like '%CUTI%') or (a.Attend_Code like '%UPL%'), 0, IIF( a.Attend_Code like '%WFH%', 0, IIF( a.Attend_Code like '%PRS%', 0, IIF( a.Attend_Code like '%ABS%', 0, 1))))))))) AS libur

       FROM
       VIEW_YMPI_ATTENDANCE AS a
       LEFT JOIN VIEW_YMPI_Emp_OrgUnit AS e ON a.emp_no = e.Emp_no
       WHERE
       format ( a.shiftstarttime, 'yyyy-MM-dd' ) = '" . $now . "'
       AND (e.end_date IS NULL or e.end_date >= '" . $now . "')
       GROUP BY
       a.emp_no,
       e.Full_name,
       e.Department,
       e.Section,
       e.Groups,
       a.shiftdaily_code,
       a.Attend_Code,
       e.employ_code,
       e.grade_code");

        $employees = db::select("SELECT * from employees");

        $result1 = array();

        foreach ($attendances as $attendance) {
            $location = "production";

            foreach ($employees as $employee) {
                if ($employee->employee_id == $attendance->emp_no) {
                    if (substr($employee->employee_id, 0, 2) == 'OS') {
                        $location = "outsource";
                    } else if ($employee->remark == 'OFC' || $employee->remark == 'Jps') {
                        $location = "office";
                    }
                }
            }

            array_push($result1, [
                'location' => $location,
                'emp_no' => $attendance->emp_no,
                'full_name' => $attendance->full_name,
                'department' => $attendance->department,
                'sections' => $attendance->sections,
                'groups' => $attendance->groups,
                'employ_code' => $attendance->employ_code,
                'grade_code' => $attendance->grade_code,
                'shiftdaily_code' => $attendance->shiftdaily_code,
                'shift' => $attendance->shift,
                'attend_code' => $attendance->Attend_Code,
                'isoman' => $attendance->isoman,
                'covid' => $attendance->covid,
                'sakit' => $attendance->sakit,
                'izin' => $attendance->izin,
                'cuti' => $attendance->cuti,
                'wfh' => $attendance->wfh,
                'hadir' => $attendance->hadir,
                'libur' => $attendance->libur,
                'absen' => $attendance->absen,
            ]);
        }

        $result2 = array();

        foreach ($result1 as $row) {
            $key = $row['location'] . $row['shift'];
            if (!array_key_exists($key, $result2)) {
                $result2[$key] = array(
                    'location' => $row['location'],
                    'shift' => $row['shift'],
                    'isoman' => $row['isoman'],
                    'covid' => $row['covid'],
                    'sakit' => $row['sakit'],
                    'izin' => $row['izin'],
                    'cuti' => $row['cuti'],
                    'wfh' => $row['wfh'],
                    'hadir' => $row['hadir'],
                    'libur' => $row['libur'],
                    'absen' => $row['absen'],
                    'total' => $row['isoman'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'],
                );
            } else {
                $result2[$key]['isoman'] = $result2[$key]['isoman'] + $row['isoman'];
                $result2[$key]['covid'] = $result2[$key]['covid'] + $row['covid'];
                $result2[$key]['sakit'] = $result2[$key]['sakit'] + $row['sakit'];
                $result2[$key]['izin'] = $result2[$key]['izin'] + $row['izin'];
                $result2[$key]['cuti'] = $result2[$key]['cuti'] + $row['cuti'];
                $result2[$key]['wfh'] = $result2[$key]['wfh'] + $row['wfh'];
                $result2[$key]['hadir'] = $result2[$key]['hadir'] + $row['hadir'];
                $result2[$key]['libur'] = $result2[$key]['libur'] + $row['libur'];
                $result2[$key]['absen'] = $result2[$key]['absen'] + $row['absen'];
                $result2[$key]['total'] = $result2[$key]['total'] + $row['covid'] + $row['sakit'] + $row['izin'] + $row['cuti'] + $row['wfh'] + $row['hadir'] + $row['libur'] + $row['absen'];
            }
        }

        $response = array(
            'status' => true,
            'resume' => $result2,
            'attendances' => $result1,
            'now' => date('l, d F Y', strtotime($now)),

        );
        return Response::json($response);
    }

    public function detailAbsence(Request $request)
    {
        $tgl = date('d-m-Y', strtotime($request->get('tgl')));
        $shift = $request->get('shift');

        $query = "SELECT presensi.tanggal, presensi.nik, ympimis.employees.`name` as nama, ympimis.mutation_logs.section as section, presensi.shift as absensi from presensi
    LEFT JOIN ympimis.employees ON presensi.nik = ympimis.employees.employee_id
    LEFT JOIN ympimis.mutation_logs ON presensi.nik = ympimis.mutation_logs.employee_id
    WHERE DATE_FORMAT(tanggal,'%d-%m-%Y')='" . $tgl . "' and tanggal not in (select tanggal from kalender) and shift NOT REGEXP '^[1-9]+$' and ympimis.mutation_logs.valid_to is null and shift = '" . $shift . "' ORDER BY presensi.nik";
        $detail = db::connection('mysql3')->select($query);

        return DataTables::of($detail)->make(true);
    }
    //------------- End Absence

    public function fetchMasterQuestion(Request $request)
    {
        $filter = $request->get("filter");
        $ctg = $request->get("ctg");
        $order = $request->get("order");

        $filter2 = "";
        $ctg2 = "";

        if ($filter != "") {
            $filter2 = ' and hr_question_logs.created_by like "%' . $filter . '%"';
        }

        if ($ctg != "") {
            $ctg2 = ' and hr.category = "' . $ctg . '"';
        }

        if ($order == "tanggal") {
            $order2 = " order by created_at desc";
        } else {
            $order2 = " order by notif desc";
        }

        $getQuestion = db::select('SELECT message, category, created_at, created_at_new, created_by, notif from
      (select `hr_question_logs`.`message`, GROUP_CONCAT(hr.category) as category, `hr_question_logs`.`created_at`, date_format(hr_question_logs.created_at, "%b %d, %H:%i") as created_at_new, `hr_question_logs`.`created_by`, SUM(hr.remark) as notif from `hr_question_logs` left join hr_question_logs as hr on `hr`.`created_by` = `hr_question_logs`.`created_by` where hr_question_logs.id IN ( SELECT MAX(id) FROM hr_question_logs GROUP BY created_by ) ' . $filter2 . ' ' . $ctg2 . ' and `hr_question_logs`.`deleted_at` is null group by `hr_question_logs`.`created_by`, `hr_question_logs`.`message`, `hr_question_logs`.`created_at`, `hr_question_logs`.`created_by`) as main
      ' . $order2 . ' ');

        // $getQuestion = HrQuestionLog::leftJoin(db::raw('hr_question_logs as hr'),'hr.created_by' ,'=','hr_question_logs.created_by')
        // ->select('hr_question_logs.message', db::raw('GROUP_CONCAT(hr.category) as category'), 'hr_question_logs.created_at', db::raw('date_format(hr_question_logs.created_at, "%b %d, %H:%i") as created_at_new'), 'hr_question_logs.created_by', db::raw('SUM(hr.remark) as notif'))
        // ->whereRaw('hr_question_logs.id IN ( SELECT MAX(id) FROM hr_question_logs GROUP BY created_by )');

        // $getQuestion = $getQuestion->groupBy('hr_question_logs.created_by','hr_question_logs.message', 'hr_question_logs.created_at', 'hr_question_logs.created_by')
        // ->orderBy('hr_question_logs.created_at', 'desc')
        // ->get();

        $response = array(
            'status' => true,
            'question' => $getQuestion,
        );
        return Response::json($response);
    }

    public function fetchDetailQuestion(Request $request)
    {
        $getQuestionDetail = HrQuestionLog::select('message', 'category', 'created_at', 'created_by')
            ->where('created_by', '=', $request->get('employee_id'))
            ->orderBy('created_at', 'desc')
            ->get();

        $response = array(
            'status' => true,
            'questionDetails' => $getQuestionDetail,
        );
        return Response::json($response);
    }

    public function fetchAttendanceData(Request $request)
    {

        $tanggal = "";
        $addcostcenter = "";
        $adddepartment = "";
        $addsection = "";
        $addgrup = "";
        $addnik = "";
        $addattend_code = "";
        $addshift = "";

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and A.shiftstarttime >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and A.shiftstarttime <= '" . $dateto . " 23:59:59' ";
            }
        }

        if ($request->get('cost_center_code') != null) {
            $costcenter = implode(",", $request->get('cost_center_code'));
            $addcostcenter = 'and B.cost_center_code in (\'' . $costcenter . '\') ';
        }

        if ($request->get('department') != null) {
            $departments = $request->get('department');
            $deptlength = count($departments);
            $department = "";

            for ($x = 0; $x < $deptlength; $x++) {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != $deptlength - 1) {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and B.Department in (" . $department . ") ";
        }

        if ($request->get('section') != null) {
            $sections = $request->get('section');
            $sectlength = count($sections);
            $section = "";

            for ($x = 0; $x < $sectlength; $x++) {
                $section = $section . "'" . $sections[$x] . "'";
                if ($x != $sectlength - 1) {
                    $section = $section . ",";
                }
            }
            $addsection = "and B.[Section] in (" . $section . ") ";
        }

        if ($request->get('group') != null) {
            $groups = $request->get('group');
            $grplen = count($groups);
            $group = "";

            for ($x = 0; $x < $grplen; $x++) {
                $group = $group . "'" . $groups[$x] . "'";
                if ($x != $grplen - 1) {
                    $group = $group . ",";
                }
            }
            $addgrup = "and B.Groups in (" . $group . ") ";
        }

        if ($request->get('employee_id') != null) {
            $niks = $request->get('employee_id');
            $niklen = count($niks);
            $nik = "";

            for ($x = 0; $x < $niklen; $x++) {
                $nik = $nik . "'" . $niks[$x] . "'";
                if ($x != $niklen - 1) {
                    $nik = $nik . ",";
                }
            }
            $addnik = "and A.Emp_no in (" . $nik . ") ";
        }

        if ($request->get('attend_code') != null) {
            $attend_codes = $request->get('attend_code');
            $attend_codelen = count($attend_codes);
            $attend_code = "";

            for ($x = 0; $x < $attend_codelen; $x++) {
                $attend_code = $attend_code . "A.attend_code like '%" . $attend_codes[$x] . "%'";
                if ($x != $attend_codelen - 1) {
                    $attend_code = $attend_code . " or ";
                }
            }
            $addattend_code = "and (" . $attend_code . ") ";
        }

        if ($request->get('shift') != null) {
            // $shifts = $request->get('shift');
            // $shiftlen = count($shifts);
            // $shift = "";

            // for($x = 0; $x < $shiftlen; $x++) {
            //      for($x = 0; $x < $shiftlen; $x++) {
            //           $shift = $shift."'".$shifts[$x]."'";
            //           if($x != $shiftlen-1){
            //                $shift = $shift.",";
            //           }
            //      }
            // }
            $addshift = "and A.shiftdaily_code like '%" . $request->get('shift') . "%'";
        }

        $qry = "SELECT
    format ( A.shiftstarttime, 'yyyy-MM-dd' ) AS tanggal,
    A.emp_no,
    B.Full_name,
    B.Department,
    B.section,
    B.groups,
    B.cost_center_code,
    A.shiftdaily_code,
    A.starttime,
    A.endtime,
    A.Attend_Code
    FROM
    VIEW_YMPI_Emp_Attendance A
    LEFT JOIN VIEW_YMPI_Emp_OrgUnit B ON A.emp_no = B.emp_no
    WHERE
    A.emp_no IS NOT NULL " . $tanggal . "" . $addcostcenter . "" . $adddepartment . "" . $addsection . "" . $addgrup . "" . $addnik . "" . $addattend_code . " " . $addshift . "
    ORDER BY
    A.emp_no ASC";

        $attendances = db::connection('sunfish')->select($qry);

        $attendanceall = [];

        if (str_contains(join(',',$request->get('employee_id')),'OSK') || str_contains(join(',',$request->get('employee_id')),'OSR')) {
            $emps = $request->get('employee_id');
            $date_from = $request->get('datefrom');
                $date_to = $request->get('dateto');
                if ($date_from == "") {
                 if ($date_to == "") {
                  $first = "'".date('Y-m-d')."'";
                  $last = "'".date('Y-m-d')."'";
                }else{
                  $first = "'".date('Y-m-d')."'";
                  $last = "'".date('Y-m-d',strtotime($date_to))."'";
                }
              }else{
               if ($date_to == "") {
                $first = "'".date('Y-m-d',strtotime($date_from))."'";
                $last = "'".date('Y-m-d')."'";
              }else{
                $first = "'".date('Y-m-d',strtotime($date_from))."'";
                $last = "'".date('Y-m-d',strtotime($date_to))."'";
              }
            }

            $week_date = DB::SELECT("SELECT
                week_date 
            FROM
                weekly_calendars 
            WHERE
                week_date >= ".$first."
                AND week_date <= ".$last."");
            for ($i=0; $i < count($emps); $i++) { 
                $empsync = EmployeeSync::where('employee_id',$emps[$i])->first();
                for ($j=0; $j < count($week_date); $j++) { 
                    $empatt = DB::SELECT("SELECT
                    GROUP_CONCAT( DISTINCT ( ivms.ivms_attendance_triggers.auth_datetime ) ORDER BY ivms.ivms_attendance_triggers.auth_datetime ASC) AS attend_time,
                    IF
                    (
                    MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $week_date[$j]->week_date . " 04:00:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $week_date[$j]->week_date . " 07:00:00', 'Shift_1', IF ( MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $week_date[$j]->week_date . " 00:30:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $week_date[$j]->week_date . " 03:00:00', 'Shift_2', IF ( MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $week_date[$j]->week_date . " 07:01:01' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $week_date[$j]->week_date . " 08:00:00',
                    'Shift_3',
                    IF(MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $week_date[$j]->week_date . " 22:00:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $week_date[$j]->week_date . " 23:59:59','Shift_3','No Data' )
                    ))) AS shift_suggest
                    FROM
                    ivms.ivms_attendance_triggers
                    WHERE
                    employee_id = '" . $emps[$i] . "'
                    AND DATE( ivms.ivms_attendance_triggers.auth_datetime ) = '" . $week_date[$j]->week_date . "'
                    LIMIT 1");

                    array_push($attendanceall, [
                        'tanggal' => $week_date[$j]->week_date,
                        'emp_no' => $emps[$i],
                        'Full_name' => $empsync->name,
                        'Department' => '',
                        'section' => '',
                        'groups' => '',
                        'cost_center_code' => '',
                        'shiftdaily_code' => '',
                        'starttime' => '',
                        'endtime' => '',
                        'Attend_Code' => '',
                        'act_in' => $empatt[0]->attend_time,
                        'shift_suggest' => $empatt[0]->shift_suggest,
                    ]);
                }
            }
        }else{
            for ($i = 0; $i < count($attendances); $i++) {
                $empatt = DB::SELECT("SELECT
                GROUP_CONCAT( DISTINCT ( ivms.ivms_attendance_triggers.auth_datetime ) ORDER BY ivms.ivms_attendance_triggers.auth_datetime ASC) AS attend_time,
                IF
                (
                MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $attendances[$i]->tanggal . " 04:00:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $attendances[$i]->tanggal . " 07:00:00', 'Shift_1', IF ( MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $attendances[$i]->tanggal . " 00:30:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $attendances[$i]->tanggal . " 03:00:00', 'Shift_2', IF ( MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $attendances[$i]->tanggal . " 07:01:01' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $attendances[$i]->tanggal . " 08:00:00',
                'Shift_3',
                IF(MIN( ivms.ivms_attendance_triggers.auth_datetime ) >= '" . $attendances[$i]->tanggal . " 22:00:00' && MIN( ivms.ivms_attendance_triggers.auth_datetime ) <= '" . $attendances[$i]->tanggal . " 23:59:59','Shift_3','No Data' )
                ))) AS shift_suggest
                FROM
                ivms.ivms_attendance_triggers
                WHERE
                employee_id = '" . $attendances[$i]->emp_no . "'
                AND DATE( ivms.ivms_attendance_triggers.auth_datetime ) = '" . $attendances[$i]->tanggal . "'
                LIMIT 1");

                array_push($attendanceall, [
                    'tanggal' => $attendances[$i]->tanggal,
                    'emp_no' => $attendances[$i]->emp_no,
                    'Full_name' => $attendances[$i]->Full_name,
                    'Department' => $attendances[$i]->Department,
                    'section' => $attendances[$i]->section,
                    'groups' => $attendances[$i]->groups,
                    'cost_center_code' => $attendances[$i]->cost_center_code,
                    'shiftdaily_code' => $attendances[$i]->shiftdaily_code,
                    'starttime' => $attendances[$i]->starttime,
                    'endtime' => $attendances[$i]->endtime,
                    'Attend_Code' => $attendances[$i]->Attend_Code,
                    'act_in' => $empatt[0]->attend_time,
                    'shift_suggest' => $empatt[0]->shift_suggest,
                ]);
            }
        }

        // return DataTables::of($attendances)->make(true);

        $response = array(
            'status' => true,
            'attendances' => $attendanceall,
            'attendancesss' => $attendances,
            // 'qry' => $qry
        );
        return Response::json($response);
    }

    public function fetchChecklogData(Request $request)
    {

        $tanggal = "";
        $adddepartment = "";
        $addsection = "";
        $addgrup = "";
        $addnik = "";

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
            $tanggal = "and auth_datetime >= '" . $datefrom . " 00:00:00' ";
            if (strlen($request->get('dateto')) > 0) {
                $dateto = date('Y-m-d', strtotime($request->get('dateto')));
                $tanggal = $tanggal . "and auth_datetime <= '" . $dateto . " 23:59:59' ";
            }
        }

        if ($request->get('department') != null) {
            $departments = $request->get('department');
            $deptlength = count($departments);
            $department = "";

            for ($x = 0; $x < $deptlength; $x++) {
                $department = $department . "'" . $departments[$x] . "'";
                if ($x != $deptlength - 1) {
                    $department = $department . ",";
                }
            }
            $adddepartment = "and employee_syncs.department in (" . $department . ") ";
        }

        if ($request->get('section') != null) {
            $sections = $request->get('section');
            $sectlength = count($sections);
            $section = "";

            for ($x = 0; $x < $sectlength; $x++) {
                $section = $section . "'" . $sections[$x] . "'";
                if ($x != $sectlength - 1) {
                    $section = $section . ",";
                }
            }
            $addsection = "and employee_syncs.section in (" . $section . ") ";
        }

        if ($request->get('group') != null) {
            $groups = $request->get('group');
            $grplen = count($groups);
            $group = "";

            for ($x = 0; $x < $grplen; $x++) {
                $group = $group . "'" . $groups[$x] . "'";
                if ($x != $grplen - 1) {
                    $group = $group . ",";
                }
            }
            $addgrup = "and employee_syncs.group in (" . $group . ") ";
        }

        if ($request->get('employee_id') != null) {
            $niks = $request->get('employee_id');
            $niklen = count($niks);
            $nik = "";

            for ($x = 0; $x < $niklen; $x++) {
                $nik = $nik . "'" . $niks[$x] . "'";
                if ($x != $niklen - 1) {
                    $nik = $nik . ",";
                }
            }
            $addnik = "and employee_syncs.employee_id in (" . $nik . ") ";
        }

        $qry = "SELECT
          *
    FROM
    employee_syncs
    WHERE
    end_date IS NULL
    " . $adddepartment . "" . $addsection . "" . $addgrup . "" . $addnik . "
    ORDER BY
    employee_syncs.employee_id ASC";

        $emp = db::select($qry);

        $datachecklog = [];

        foreach ($emp as $key) {
            $checklog = DB::SELECT("SELECT
           IF
           (
           ivms.ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
           SPLIT_STRING ( person_name, ' ' , 1 ),
           IF
           (
           LENGTH( attend_id ) = 6 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI0', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 5 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI00', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 4 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 3 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI0000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 2 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI00000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 1 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI000000', attend_id ),
           IF
           ( LENGTH( attend_id ) = 6 && attend_id LIKE '%OS%', attend_id, CONCAT( 'PI', attend_id ) )))))))) AS `nik`,
           ivms.`ivms_attendance`.`device` AS `device`,
           ivms.`ivms_attendance`.auth_datetime,
           ivms.`ivms_attendance`.`device_serial` AS `device_serial`,
           DATE_FORMAT( auth_datetime, '%H:%i' ) AS time_in,
           auth_date
           FROM
           ivms.`ivms_attendance`
           WHERE
           IF
           (
           ivms.ivms_attendance.auth_datetime < '2020-12-14 10:00:00',
           SPLIT_STRING ( person_name, ' ', 1 ),
           IF
           (
           LENGTH( attend_id ) = 6 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI0', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 5 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI00', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 4 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 3 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI0000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 2 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI00000', attend_id ),
           IF
           (
           LENGTH( attend_id ) = 1 && attend_id NOT LIKE '%OS%',
           CONCAT( 'PI000000', attend_id ),
           IF
           ( LENGTH( attend_id ) = 6 && attend_id LIKE '%OS%', attend_id, CONCAT( 'PI', attend_id ) )))))))) = '" . $key->employee_id . "' " . $tanggal . "");

            foreach ($checklog as $val) {
                $datachecklog[] = array(
                    'employee_id' => $key->employee_id,
                    'date' => $val->auth_date,
                    'time' => $val->time_in,
                    'name' => $key->name,
                    'department' => $key->department,
                    'section' => $key->section,
                    'group' => $key->group,
                    'checklog' => $val->auth_datetime,
                );
            }
        }

        return DataTables::of($datachecklog)->make(true);
    }

    public function editNumber(Request $request)
    {
        try {
            $datas = Employee::where('employee_id', $request->get('employee_id'))
                ->update(['phone' => $request->get('phone_number'), 'wa_number' => $request->get('wa_number')]);

            $response = array(
                'status' => true,
                'datas' => $datas,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function fetchKaizen(Request $request)
    {
        $start = $request->get('bulanAwal');
        $end = $request->get('bulanAkhir');

        $kz = KaizenForm::leftJoin('kaizen_scores', 'kaizen_forms.id', '=', 'kaizen_scores.id_kaizen')
            ->where('employee_id', $request->get('employee_id'))
            ->select('kaizen_forms.id', 'employee_id', 'propose_date', 'title', 'application', 'status', 'foreman_point_1', 'manager_point_1');
        if ($start != "" && $end != "") {
            $kz = $kz->where('propose_date', '>=', $start)->where('propose_date', '<=', $end)->get();
        }

        return DataTables::of($kz)
            ->addColumn('action', function ($kz) {
                if ($kz->status == '-1' || $kz->status == '3') {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="cekDetail(this.id)" id="' . $kz->id . '"><i class="fa fa-eye"></i> Details</a>
            <a href="' . url("index/updateKaizen") . "/" . $kz->id . '" class="btn btn-xs btn-warning"><i class="fa fa-pencil"></i> Ubah</a>
            <button onclick="openDeleteDialog(' . $kz->id . ',\'' . $kz->title . '\', \'' . $kz->propose_date . '\')" class="btn btn-xs btn-danger"><i class="fa fa-trash"></i> Delete</button>';
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-primary" onClick="cekDetail(this.id)" id="' . $kz->id . '"><i class="fa fa-eye"></i> Details</a>';
                }

            })->addColumn('posisi', function ($kz) {
            if ($kz->foreman_point_1 != null && $kz->manager_point_1 == null) {
                return 'Sudah diverifikasi <b>Foreman</b>';
            } else if ($kz->foreman_point_1 != null && $kz->manager_point_1 != null) {
                return 'Sudah diverifikasi <b>Manager</b>';
            } else if ($kz->foreman_point_1 == null) {
                return 'Belum Verifikasi';
            }
        })->addColumn('stat', function ($kz) {
            if ($kz->status == '1') {
                return 'Kaizen';
            } else if ($kz->status == '0') {
                return 'Bukan Kaizen';
            } else if ($kz->status == '-1') {
                return 'Belum Verifikasi';
            } else if ($kz->status == '3') {
                return '<font style="color:red">Terdapat Catatan</font>';
            }

        })->addColumn('aplikasi', function ($kz) {
            if ($kz->application == '1') {
                return 'Telah di Aplikasikan';
            } else if ($kz->application == '0') {
                return 'Tidak di Aplikasikan';
            } else if ($kz->application == '') {
                return '';
            }

        })

            ->rawColumns(['posisi', 'action', 'stat'])
            ->make(true);
    }

    public function postKaizen(Request $request)
    {
        try {
            $kz = new KaizenForm([
                'employee_id' => $request->get('employee_id'),
                'employee_name' => $request->get('employee_name'),
                'propose_date' => $request->get('propose_date'),
                'section' => $request->get('section'),
                'leader' => $request->get('leader'),
                'title' => $request->get('title'),
                'condition' => $request->get('condition'),
                'improvement' => $request->get('improvement'),
                'area' => $request->get('area_kz'),
                'purpose' => $request->get('purpose'),
                'status' => '-1',
            ]);

            $kz = KaizenForm::create([
                'employee_id' => $request->get('employee_id'),
                'employee_name' => $request->get('employee_name'),
                'propose_date' => $request->get('propose_date'),
                'section' => $request->get('section'),
                'leader' => $request->get('leader'),
                'title' => $request->get('title'),
                'condition' => $request->get('condition'),
                'improvement' => $request->get('improvement'),
                'area' => $request->get('area_kz'),
                'purpose' => $request->get('purpose'),
                'status' => '-1',
            ]);
            if (isset($kz->id)) {
                if ($request->get('estimasi')) {
                    foreach ($request->get('estimasi') as $est) {
                        $kc = new KaizenCalculation([
                            'id_kaizen' => $kz->id,
                            'id_cost' => $est[0],
                            'cost' => $est[1],
                            'created_by' => Auth::id(),
                            'created_at' => date('Y-m-d H:i:s'),
                        ]);

                        $kc->save();
                    }
                }

                $response = array(
                    'status' => true,
                    'datas' => 'Kaizen Berhasil ditambahkan',
                );
                return Response::json($response);
            } else {
                //not inserted
            }

            // $kz->save();

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchSubLeader()
    {
        $ldr = Employee::leftJoin('promotion_logs', 'promotion_logs.employee_id', '=', 'employees.employee_id')
            ->whereNull("end_date")
            ->whereNull("valid_to")
            ->get();

        return Response::json($ldr);
    }

    public function getKaizen(Request $request)
    {
        $kzn = KaizenForm::where('id', $request->get('id'))->first();

        return Response::json($kzn);
    }

    public function fetchDataKaizen()
    {
        $username = Auth::user()->username;
        for ($i = 0; $i < count($_GET['user']); $i++) {
            if ($username == $_GET['user'][$i]) {
                $d = 1;
                break;
            } else {
                $d = 0;
            }
        }

        if (Auth::user()->email == 'susilo.basri@music.yamaha.com') {
            $dprt = db::select("select distinct section from employee_syncs where (department = (select department from employee_syncs where employee_id = '" . $username . "') or department LIKE '%Production Engineering%') and section is not null");
        } else if (Auth::id() == 81 || Auth::id() == 26) {
            $dprt = db::select("select distinct section from employee_syncs where section is not null");
        } else {
            $dprt = db::select("select distinct section from employee_syncs where department = (select department from employee_syncs where employee_id = '" . $username . "') and section is not null");
        }

        $kzn = KaizenForm::leftJoin('kaizen_scores', 'kaizen_forms.id', '=', 'kaizen_scores.id_kaizen')
            ->select('kaizen_forms.id', 'kaizen_forms.employee_id', 'employee_name', 'title', 'area', 'kaizen_forms.section', 'propose_date', 'status', 'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3');

        if (count($_GET['area']) > 0) {
            if ($_GET['area'][0] != "") {
                $areas = implode("','", $_GET['area']);

                $kzn = $kzn->whereRaw('area in (\'' . $areas . '\')');
            }
        }

        if ($_GET['status'] != "") {
            if ($_GET['status'] == '1') {
                $kzn = $kzn->whereRaw('( status = -1 OR status = 3 )');
            } else if ($_GET['status'] == '2') {
                $kzn = $kzn->where('manager_point_1', '=', '0');
                $kzn = $kzn->where('status', '=', '1');
            } else if ($_GET['status'] == '3') {
                $kzn = $kzn->where('status', '=', '1');
            } else if ($_GET['status'] == '4') {
                $kzn = $kzn->where('manager_point_1', '<>', '0');
            } else if ($_GET['status'] == '5') {
                $kzn = $kzn->where('status', '=', '2');
            } else if ($_GET['status'] == '6') {
                $kzn = $kzn->where('status', '=', '0');
            }
        }

        $dprt2 = [];
        foreach ($dprt as $dpr) {
            array_push($dprt2, $dpr->section);
        }

        $dprt3 = implode("','", $dprt2);

        if ($_GET['filter'] != "") {
            $kzn = $kzn->where('area', '=', $_GET['filter']);
            $kzn = $kzn->where('status', '=', '-1');
        }

        if ($d == 0) {
            $kzn = $kzn->whereRaw('area in (\'' . $dprt3 . '\')');
        }

        $fy = db::table('weekly_calendars')
            ->whereRaw("fiscal_year = (select fiscal_year from weekly_calendars where week_date = '" . date('Y-m-d') . "')")
            ->select("week_date")
            ->orderBy('id', 'asc')
            ->get()
            ->toArray();

        $fys = [];

        foreach ($fy as $f) {
            array_push($fys, $f->week_date);
        }

        // $kzn = $kzn->whereIn('propose_date', $fys);
        $kzn = $kzn->get();

        $emp = EmployeeSync::whereNull('end_date')->select('employee_id')->get();
        $emp_arr = [];

        foreach ($emp as $ep) {
            array_push($emp_arr, $ep->employee_id);
        }

        $response = array(
            'status' => true,
            'kaizen' => $kzn,
            'employee' => $emp_arr,
        );
        return Response::json($response);
    }

    public function inputKaizenDetailNote(Request $request)
    {

        $kaizen_forms = KaizenForm::find($request->get('id'));
        $kaizen_forms->status = 3;

        $kaizen_notes = KaizenNote::firstOrNew(array('id_kaizen' => $request->get('id')));
        if ($request->get('from') == 'foreman') {
            $kaizen_notes->foreman_note = $request->get('catatan');
        } else if ($request->get('from') == 'manager') {
            $kaizen_notes->manager_note = $request->get('catatan');
        }
        $kaizen_notes->created_by = Auth::id();

        try {
            $kaizen_notes->save();
            $kaizen_forms->save();

            return response()->json([
                'status' => true,
                'message' => 'Note saved successfully',
            ]);

        } catch (\Exception$e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ]);
        }
    }

    public function fetchDetailKaizen(Request $request)
    {
        $data = KaizenForm::select("kaizen_forms.id", "kaizen_forms.employee_id", "employee_name", db::raw("date_format(propose_date,'%d-%b-%Y') as date"), "title", "condition", "improvement", "area", "leader", "purpose", "section", db::raw("name as leader_name"), 'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3', 'kaizen_calculations.cost', 'standart_costs.cost_name', db::raw('kaizen_calculations.cost * standart_costs.cost as sub_total_cost'), 'frequency', 'unit', db::raw('standart_costs.cost as std_cost'), 'kaizen_forms.remark', 'kaizen_notes.foreman_note', 'kaizen_notes.manager_note')
            ->leftJoin('employees', 'employees.employee_id', '=', 'kaizen_forms.leader')
            ->leftJoin('kaizen_calculations', 'kaizen_forms.id', '=', 'kaizen_calculations.id_kaizen')
            ->leftJoin('standart_costs', 'standart_costs.id', '=', 'kaizen_calculations.id_cost')
            ->leftJoin('kaizen_scores', 'kaizen_scores.id_kaizen', '=', 'kaizen_forms.id')
            ->leftJoin('kaizen_notes', 'kaizen_forms.id', '=', 'kaizen_notes.id_kaizen')
            ->where('kaizen_forms.id', '=', $request->get('id'))
            ->get();

        $login = db::select("select username from users where '" . Auth::user()->username . "' in (select username from users where role_code LIKE '%MIS' or username = 'PI0904007')");

        if (count($login) > 0) {
            $aksi = true;
        } else {
            $aksi = false;
        }

        // return Response::json($data);
        return response()->json([
            'status' => true,
            'datas' => $data,
            'aksi' => $aksi,
        ]);
    }

    public function assessKaizen(Request $request)
    {
        $id = Auth::id();

        if ($request->get('category') == 'manager') {
            // --------------- JIKA inputor Manager ----
            if ($request->get('nilai1')) {
                try {
                    $data = KaizenScore::where('id_kaizen', '=', $request->get('id'))
                        ->first();

                    $data->manager_point_1 = $request->get('nilai1');
                    $data->manager_point_2 = $request->get('nilai2');
                    $data->manager_point_3 = $request->get('nilai3');
                    $data->save();

                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head', 'Kaizen');
                    // return ['status' => 'success', 'message' => 'Kaizen successfully assessed'];

                } catch (QueryException $e) {
                    // return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head', 'Kaizen');
                }
            } else {
                // -------------- Jika Kaizen False -----------
                try {
                    $data = KaizenForm::where('id', '=', $request->get('id'))
                        ->first();

                    $data->status = 2;
                    $data->save();

                    // return ['status' => 'success', 'message' => 'Kaizen successfully assessed (NOT KAIZEN)'];
                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed (NOT KAIZEN)')->with('page', 'Assess')->with('head', 'Kaizen');
                } catch (QueryException $e) {
                    // return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head', 'Kaizen');
                }
            }
        } else if ($request->get('category') == 'foreman') { // --------------- JIKA inputor Foreman ----
            if ($request->get('nilai1')) {
                // ----------------  JIKA KAIZEN true ------------
                try {
                    $data = KaizenForm::where('id', '=', $request->get('id'))
                        ->first();

                    $data->status = 1;
                    $data->save();

                    $kz_nilai = new KaizenScore([
                        'id_kaizen' => $request->get('id'),
                        'foreman_point_1' => $request->get('nilai1'),
                        'foreman_point_2' => $request->get('nilai2'),
                        'foreman_point_3' => $request->get('nilai3'),
                        'created_by' => $id,
                    ]);

                    $kz_nilai->save();

                    $total_nilai = ($request->get('nilai1') * 40) + ($request->get('nilai2') * 30) + ($request->get('nilai1') * 30);

                    if ($total_nilai <= 350) {
                        $manager = KaizenScore::where('id_kaizen', '=', $request->get('id'))
                            ->update([
                                'manager_point_1' => $request->get('nilai1'),
                                'manager_point_2' => $request->get('nilai2'),
                                'manager_point_3' => $request->get('nilai3'),
                            ]);
                    }

                    // return ['status' => 'success', 'message' => 'Kaizen successfully assessed'];
                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed')->with('page', 'Assess')->with('head', 'Kaizen');

                } catch (QueryException $e) {
                    // return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head', 'Kaizen');
                }
            } else {
                // ----------------  JIKA KAIZEN false ------------
                try {
                    $data = KaizenForm::where('id', '=', $request->get('id'))
                        ->first();

                    $data->status = 0;
                    $data->save();

                    // return ['status' => 'success', 'message' => 'Kaizen successfully assessed (NOT KAIZEN)'];

                    return redirect('/index/kaizen')->with('status', 'Kaizen successfully assessed (NOT KAIZEN)')->with('page', 'Assess')->with('head', 'Kaizen');
                } catch (QueryException $e) {
                    // return ['status' => 'error', 'message' => $e->getMessage()];
                    return redirect('/index/kaizen')->with('error', $e->getMessage())->with('page', 'Assess')->with('head', 'Kaizen');
                }
            }
        }
    }

    public function fetchAppliedKaizen()
    {
        $username = Auth::user()->username;
        for ($i = 0; $i < count($_GET['user']); $i++) {
            if ($username == $_GET['user'][$i]) {
                $d = 1;
                break;
            } else {
                $d = 0;
            }
        }

        $dprt = db::select("select distinct section from mutation_logs where valid_to is null and department = (select department from mutation_logs where employee_id = '" . $username . "' and valid_to is null)");

        $kzn = KaizenForm::Join('kaizen_scores', 'kaizen_forms.id', '=', 'kaizen_scores.id_kaizen')
            ->select('kaizen_forms.id', 'employee_name', 'title', 'area', 'section', 'application', 'propose_date', 'status', 'foreman_point_1', 'foreman_point_2', 'foreman_point_3', 'manager_point_1', 'manager_point_2', 'manager_point_3')
            ->where('manager_point_1', '<>', '0');
        if ($_GET['area'][0] != "") {
            $areas = implode("','", $_GET['area']);

            $kzn = $kzn->whereRaw('area in (\'' . $areas . '\')');
        }

        if ($_GET['status'] != "") {
            if ($_GET['status'] == '1') {
                $kzn = $kzn->whereNull('application');
            } else if ($_GET['status'] == '2') {
                $kzn = $kzn->where('application', '=', '1');
            } else if ($_GET['status'] == '3') {
                $kzn = $kzn->where('application', '=', '0');
            }
        }

        $dprt2 = [];
        foreach ($dprt as $dpr) {
            array_push($dprt2, $dpr->section);
        }

        $dprt3 = implode("','", $dprt2);
        if ($d == 0) {
            $kzn = $kzn->whereRaw('area in (\'' . $dprt3 . '\')');
        }

        $kzn->get();

        return DataTables::of($kzn)
            ->addColumn('app_stat', function ($kzn) {
                if ($kzn->application == '') {
                    return '<button class="label bg-yellow btn" onclick="modal_apply(' . $kzn->id . ',\'' . $kzn->title . '\')">UnApplied</a>';
                } else if ($kzn->application == '1') {
                    return '<span class="label bg-green"><i class="fa fa-check"></i> Applied</span>';
                } else if ($kzn->application == '0') {
                    return '<span class="label bg-red"><i class="fa fa-close"></i> NOT Applied</span>';
                }
            })
            ->addColumn('action', function ($kzn) {
                return '<button onClick="cekDetail(\'' . $kzn->id . '\')" class="btn btn-primary btn-xs"><i class="fa fa-eye"></i> Details</button>';
            })
            ->addColumn('fr_point', function ($kzn) {
                return ($kzn->foreman_point_1 * 40) + ($kzn->foreman_point_2 * 30) + ($kzn->foreman_point_3 * 30);
            })
            ->addColumn('mg_point', function ($kzn) {
                return ($kzn->manager_point_1 * 40) + ($kzn->manager_point_2 * 30) + ($kzn->manager_point_3 * 30);
            })
            ->rawColumns(['app_stat', 'fr_point', 'mg_point', 'action'])
            ->make(true);
    }

    public function fetchCost()
    {
        $costL = StandartCost::get();

        return Response::json($costL);
    }

    public function fetchKaizenReport(Request $request)
    {
        $date = date('Y-m');
        $dt2 = date('F');

        if ($request->get('tanggal') != "") {
            $date = $request->get('tanggal');
            $dt2 = date('F', strtotime($request->get('tanggal')));
        }

        $chart1 = "select count(kaizen_forms.employee_id) as kaizen , employee_syncs.department, employee_syncs.section from kaizen_forms
        left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
        left join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
        where DATE_FORMAT(kaizen_scores.updated_at,'%Y-%m') = '" . $date . "' and kaizen_forms.`status` = 1
        group by employee_syncs.department, employee_syncs.section";

        $kz_total = db::select($chart1);

        $q_rank1 = "select kz.employee_id, employee_name, CONCAT(department,' - ', section,' - ', `group`) as bagian, mp1+mp2+mp3 as nilai from
        (select employee_id, employee_name, SUM(manager_point_1 * 40) mp1, SUM(manager_point_2 * 30) mp2, SUM(manager_point_3 * 30) mp3 from kaizen_forms LEFT JOIN kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
        where DATE_FORMAT(kaizen_scores.updated_at,'%Y-%m') = '" . $date . "' and status = 1
        group by employee_id, employee_name
        ) as kz
        left join employee_syncs on kz.employee_id = employee_syncs.employee_id
        where employee_syncs.end_date is null
        order by (mp1+mp2+mp3) desc
        limit 3";

        $kz_rank1 = db::select($q_rank1);

        $q_rank2 = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ', employee_syncs.section,' - ', IFNULL(`group`, ' ')) as bagian , COUNT(kaizen_forms.employee_id) as count from kaizen_forms
        left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
        left join kaizen_scores on kaizen_scores.id_kaizen = kaizen_forms.id
        where `status` = 1 and DATE_FORMAT(kaizen_scores.updated_at,'%Y-%m') = '" . $date . "'
        and employee_syncs.end_date is null
        group by kaizen_forms.employee_id, employee_name, department, employee_syncs.section, `group`
        order by count desc
        limit 10";

        $kz_rank2 = db::select($q_rank2);

        $q_excellent = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ',employee_syncs.section,' - ',`group`) as bagian, title, (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) as score, kaizen_forms.id from kaizen_forms
        join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
        left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
        where DATE_FORMAT(kaizen_scores.updated_at,'%Y-%m') = '" . $date . "' and (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) > 450
        order by (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) desc";

        $kz_excellent = db::select($q_excellent);

        $q_a_excellent = "select kaizen_forms.employee_id, employee_name, CONCAT(department,' - ',employee_syncs.section,' - ',`group`) as bagian, title, (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) as score, kaizen_forms.id from kaizen_forms
        join kaizen_scores on kaizen_forms.id = kaizen_scores.id_kaizen
        left join employee_syncs on kaizen_forms.employee_id = employee_syncs.employee_id
        where DATE_FORMAT(kaizen_scores.updated_at,'%Y-%m') = '" . $date . "' and remark = 'excellent'
        order by (manager_point_1 * 40 + manager_point_2 * 30 + manager_point_3 * 30) desc";

        $kz_after_excellent = db::select($q_a_excellent);

        $response = array(
            'status' => true,
            'charts' => $kz_total,
            'rank1' => $kz_rank1,
            'rank2' => $kz_rank2,
            'excellent' => $kz_excellent,
            'true_excellent' => $kz_after_excellent,
            'date' => $dt2,
        );
        return Response::json($response);
    }

    public function applyKaizen(Request $request)
    {
        try {
            KaizenForm::where('id', $request->get('id'))
                ->update(['application' => $request->get('status')]);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'message' => 'e-Kaizen Updated Successfully',
        );
        return Response::json($response);
    }

    public function fetchKaizenResumeDetail(Request $request)
    {
        $tanggal = date('Y-m-t');
        if (strlen($request->get('tanggal')) > 0) {
            $tanggal = date('Y-m-t', strtotime($request->get('tanggal') . '-01'));
        }

        $fiscal = db::table('weekly_calendars')->where('week_date', '=', $tanggal)->first();

        $leader_name = db::select("SELECT leader_id from kaizen_leaders
          LEFT JOIN employee_syncs on employee_syncs.employee_id = kaizen_leaders.leader_id
          where `name` = '" . $request->get('leader') . "'
          group by leader_id");

        $q = "SELECT kaizen_leaders.employee_id, employee_syncs.`name`, employee_syncs.position as grade, employee_syncs.`section`, employee_syncs.`group`, COALESCE(kz,0) as kz from kaizen_leaders
        left join (select employee_id, count(kaizen_forms.id) as kz from kaizen_forms
        left join kaizen_scores on kaizen_scores.id_kaizen = kaizen_forms.id
        where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m-%d') in (select week_date from weekly_calendars where fiscal_year = '" . $fiscal->fiscal_year . "') and `status` = 1 group by employee_id) as kaizens on kaizens.employee_id = kaizen_leaders.employee_id
        inner join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
        where kaizen_leaders.leader_id = '" . $leader_name[0]->leader_id . "' and employee_syncs.end_date is null
        order by kz asc";

        $details = db::select($q);

        $response = array(
            'status' => true,
            'details' => $details,
        );
        return Response::json($response);
    }

    public function fetchKaizenResume(Request $request)
    {
        $tanggal = date('Y-m-t');
        if (strlen($request->get('tanggal')) > 0) {
            $tanggal = date('Y-m-t', strtotime($request->get('tanggal') . '-01'));
        }

        $fiscal = db::table('weekly_calendars')->where('week_date', '=', $tanggal)->select('fiscal_year')->first();

        try {

            // $q = "select final.leader_id as leader, employee_syncs.`name`, count(final.employee_id) as total_operator, count(final.kaizen) as total_sudah, count(if(final.kaizen is null, 1, null)) as total_belum, 0 as total_kaizen from
// (
// select kaizen_leaders.leader_id, kaizen_leaders.employee_id as employee_id, kaizens.employee_id as kaizen from kaizen_leaders left join
// (
// select employee_id from kaizen_forms left join weekly_calendars on kaizen_forms.propose_date = weekly_calendars.week_date where weekly_calendars.fiscal_year = '".$fiscal->fiscal_year."') as kaizens on kaizens.employee_id = kaizen_leaders.employee_id
// inner join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
// where employee_syncs.end_date is null
// group by kaizen_leaders.leader_id, kaizens.employee_id, kaizen_leaders.employee_id) as final
// inner join employee_syncs on employee_syncs.employee_id = final.leader_id where employee_syncs.end_date is null
// group by final.leader_id, employee_syncs.`name` order by total_belum desc";

            $q = "select kaizen_leaders.leader_id as leader, A.`name`, count(kz) as total_sudah, count(coalesce(kz, 1)) as total_operator, count(coalesce(kz, 1))-count(kz) total_belum from kaizen_leaders
            left join (select employee_id, count(kaizen_forms.id) as kz from kaizen_forms
            left join kaizen_scores on kaizen_scores.id_kaizen = kaizen_forms.id
            where DATE_FORMAT(kaizen_scores.created_at,'%Y-%m-%d') in (select week_date from weekly_calendars where fiscal_year = '" . $fiscal->fiscal_year . "') and `status` = '1' group by employee_id) as kaizens on kaizens.employee_id = kaizen_leaders.employee_id
            left join employee_syncs on employee_syncs.employee_id = kaizen_leaders.employee_id
            left join employee_syncs A on A.employee_id = kaizen_leaders.leader_id
            where employee_syncs.end_date is null and A.end_date is null and A.employee_id is not null
            group by kaizen_leaders.leader_id, A.`name`
            order by total_belum desc";

            $datas = db::select($q);

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

        $response = array(
            'status' => true,
            'datas' => $datas,
            'fiscal' => $fiscal->fiscal_year,
            'message' => 'Success',
        );
        return Response::json($response);
    }

    public function updateKaizen(Request $request)
    {
        $stt_q = KaizenScore::where('id_kaizen', '=', $request->get('id'))->first();

        if ($stt_q) {
            $stt = 1;
        } else {
            $stt = -1;
        }

        try {
            $kz = KaizenForm::where('id', $request->get('id'))
                ->update([
                    'leader' => $request->get('leader'),
                    'title' => $request->get('title'),
                    'condition' => $request->get('condition'),
                    'improvement' => $request->get('improvement'),
                    'area' => $request->get('area_kz'),
                    'purpose' => $request->get('purpose'),
                    'status' => $stt,
                ]);
            if ($request->get('estimasi')) {

                KaizenCalculation::where('id_kaizen', $request->get('id'))->forceDelete();

                foreach ($request->get('estimasi') as $est) {
                    $kc = new KaizenCalculation([
                        'id_kaizen' => $request->get('id'),
                        'id_cost' => $est[0],
                        'cost' => $est[1],
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                    ]);

                    $kc->save();
                }
            }

            $response = array(
                'status' => true,
                'datas' => 'Kaizen Berhasil diubah',
            );
            return Response::json($response);

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'datas' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function deleteKaizen(Request $request)
    {
        KaizenForm::where('id', $request->get('id'))->delete();

        $response = array(
            'status' => true,
            'datas' => 'Data Berhasil dihapus',
        );
        return Response::json($response);
    }

    public function UploadKaizenImage(Request $request)
    {
        $files = $request->file('fileupload');
        foreach ($files as $file) {
            $filename = $file->getClientOriginalName();

            if (!file_exists(public_path() . '/kcfinderimages/' . $request->get('employee_id'))) {
                mkdir(public_path() . '/kcfinderimages/' . $request->get('employee_id'), 0777, true);
                mkdir(public_path() . '/kcfinderimages/' . $request->get('employee_id') . '/files', 0777, true);
            }

            $file->move(public_path() . '/kcfinderimages/' . $request->get('employee_id') . '/files', $filename);

            // if (!file_exists(public_path().'/kcfinderimages/'.$request->get('employee_id'))) {
//   mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id'), 0777, true);
//   mkdir(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', 0777, true);
// }

            // $file->move(public_path().'/kcfinderimages/'.$request->get('employee_id').'/files', $filename);
            return redirect('/index/upload_kaizen')->with('status', 'Upload Image Successfully');
        }
    }

    public function executeKaizenExcellent(Request $request)
    {
        if ($request->get('status') == true) {
            $stat = 'excellent';
        } else {
            $stat = 'not excellent';
        }

        $kz = KaizenForm::where('id', $request->get('id'))
            ->update([
                'remark' => $stat,
            ]);

        $response = array(
            'status' => true,
            'message' => 'Kaizen Successfully Executed',
        );
        return Response::json($response);
    }

    public function getKaizenReward(Request $request)
    {
        if ($request->get("tanggal") == "") {
            $dt = '2019-12-01';
        } else {
            $dt = $request->get("tanggal") . "-01";
        }

        $db = db::select("SELECT DATE_FORMAT(CONCAT(mon,'-01'),'%M %Y') as  mons, doit, count(doit) as tot from
            (select DATE_FORMAT(propose_date,'%Y-%m') as mon, IF(total < 300,2000,IF(total >= 300 AND total <= 350,5000,IF(total > 350 AND total <= 400,10000,IF(total > 400 AND total <= 450,25000,50000)))) as doit from
            (select propose_date, manager_point_1 * 40 m1, manager_point_2 * 30 m2, manager_point_3 * 30 m3, id_kaizen, (manager_point_1 * 40+ manager_point_2 * 30+ manager_point_3 * 30) as total from kaizen_scores
            join kaizen_forms on kaizen_scores.id_kaizen = kaizen_forms.id
            where propose_date >= '" . $dt . "'
            order by id_kaizen asc) as total
            ) as total2
            group by doit, mon
            order by mon asc, doit asc");

        $response = array(
            'status' => true,
            'datas' => $db,
        );
        return Response::json($response);
    }

    public function fetchAbsenceEmployee(Request $request)
    {
        $username = Auth::user()->username;

        $attend_code = "";

        if ($request->get('attend_code') == 'Mangkir') {
            $attend_code = "Attend_code LIKE '%ABS%'";
        }

        if ($request->get('attend_code') == 'Cuti') {
            $attend_code = "Attend_Code LIKE '%CK%' OR Attend_Code LIKE '%CUTI%' OR Attend_Code LIKE '%UPL%'";
        }

        if ($request->get('attend_code') == 'Izin') {
            $attend_code = "Attend_Code LIKE '%Izin%' OR Attend_Code LIKE '%IPU%'";
        }

        if ($request->get('attend_code') == 'Sakit') {
            $attend_code = "Attend_Code LIKE '%SAKIT%' OR Attend_Code LIKE '%SD%'";
        }

        if ($request->get('attend_code') == 'Terlambat') {
            $attend_code = "Attend_Code LIKE '%LTI%' OR Attend_Code LIKE '%TELAT%'";
        }

        if ($request->get('attend_code') == 'Pulang Cepat') {
            $attend_code = "Attend_Code LIKE '%PC%'";
        }

        if ($request->get('attend_code') == 'Overtime') {
            $absence = db::connection('sunfish')->select("SELECT
             format ( shiftstarttime, 'dd MMM yyyy' ) AS tanggal,
             ovtactfrom AS starttime,
             ovtactto AS endtime,
             ovtrequest_no AS Attend_Code
             FROM
             VIEW_YMPI_Emp_Attendance
             WHERE
             ovtrequest_no IS NOT NULL
             AND Emp_no = '" . $username . "'
             AND format ( shiftstarttime, 'MMMM yyyy' ) = '" . $request->get('period') . "'");
        } else {
            $absence = db::connection('sunfish')->select("SELECT
             format ( shiftstarttime, 'dd MMM yyyy' ) AS tanggal,
             starttime,
             endtime,
             Attend_Code
             FROM
             VIEW_YMPI_Emp_Attendance
             WHERE
             Emp_no = '" . $username . "'
             AND format ( shiftstarttime, 'MMMM yyyy' ) = '" . $request->get('period') . "'
             AND ( " . $attend_code . " )");
        }

        $response = array(
            'status' => true,
            'datas' => $absence,
        );
        return Response::json($response);
    }

    public function fetchDataKaizenAll(Request $request)
    {
        $kzn = KaizenForm::join('kaizen_scores', 'kaizen_forms.id', '=', 'kaizen_scores.id_kaizen');

        if ($request->get('dari') != "") {
            $kzn = $kzn->where("kaizen_forms.propose_date", '>=', $request->get('dari'));
        }

        if ($request->get('sampai') != "") {
            $kzn = $kzn->where("kaizen_forms.propose_date", '<=', $request->get('sampai'));
        }

        if ($request->get('nik') != "") {
            $kzn = $kzn->where("kaizen_forms.employee_id", '=', $request->get('nik'));
        }

        if ($request->get('dept') != "") {
            $sec = EmployeeSync::where('department', '=', $request->get('dept'))
                ->whereNotNull('section')
                ->select('section')
                ->groupBy('section')
                ->get()
                ->toArray();

            $all_sec = [];

            foreach ($sec as $key) {
                array_push($all_sec, $key['section']);
            }

            $text_sec = implode('|', $all_sec);

            $kzn = $kzn->whereRaw("kaizen_forms.section REGEXP '" . $text_sec . "'");
        }

        if ($request->get('sec') != "") {
            $kzn = $kzn->whereRaw("kaizen_forms.section REGEXP '" . $request->get('sec') . "'");
        }

        $kzn = $kzn->whereNotNull('kaizen_scores.manager_point_1')
            ->select('kaizen_forms.id', 'kaizen_forms.propose_date', 'kaizen_forms.employee_id', 'kaizen_forms.employee_name', 'kaizen_forms.section', 'title', 'area', 'status', db::raw('(foreman_point_1 * 40) as FP1'), db::raw('(foreman_point_2 * 30) as FP2'), db::raw('(foreman_point_3 * 30) as FP3'), db::raw('(manager_point_1 * 40) as MP1'), db::raw('(manager_point_2 * 30) as MP2'), db::raw('(manager_point_3 * 30) as MP3'))
            ->orderBy('kaizen_forms.id', 'desc')
            ->get();

        return DataTables::of($kzn)
            ->addColumn('action', function ($kzn) {
                return '<button class="btn btn-primary" id="' . $kzn->id . '">details</button>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function setSession(Request $request)
    {
        // Session::put('kz_filter', $request->input('filter'));

        // Session::set('kz_filter', $request->input('filter'));
        session(['kz_filter' => $request->input('filter'), 'kz_stat' => $request->input('filter2')]);
        // session('kz_stat', $request->input('stat'));
        $data = [];
        foreach (Session::get('kz_filter') as $key) {
            $data[] = $key;
        }
        return Session::all();
    }

    public function fetchEmployeeByTag(Request $request)
    {
        try {
            $emp = Employee::whereNull('end_date')
                ->where('tag', '=', $request->get('tag'))
                ->orWhere('employee_id', '=', $request->get('tag'))
                ->first();

            if (count($emp) > 0) {
                $response = array(
                    'status' => true,
                    'datas' => $emp,
                );
                return Response::json($response);
            } else {
                //      $emp = Employee::whereNull('end_date');
                // $emp = $emp->where('tag', '=', $request->get('tag'));
                // $emp = $emp->first();
                $response = array(
                    'status' => false,
                    'datas' => null,
                );
                return Response::json($response);
            }

        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function fetchAttendanceRate(Request $request)
    {
        $attd = db::select("SELECT sunfish_shift_syncs.employee_id, sunfish_shift_syncs.shift_date, sunfish_shift_syncs.shiftdaily_code FROM `sunfish_shift_syncs`
          where shift_date >= '2021-04-01'");

        $emp = db::select("SELECT Emp_no, Full_name, employ_code
          FROM employee_histories
          WHERE id IN (
          SELECT MAX(id)
          FROM employee_histories
          GROUP BY Emp_no)");

        $base = [];

        foreach ($attd as $att) {
            foreach ($emp as $e) {
                if ($att->employee_id == $e->Emp_no) {
                    array_push($base, ['employee_id' => $att->employee_id, 'name' => $e->Full_name, 'date' => $att->shift_date, 'shift' => $att->shiftdaily_code]);
                }
            }
        }

        $response = array(
            'status' => false,
            'datas' => $base,
        );
        return Response::json($response);
    }

    public function detailAttendanceRate(Request $request)
    {
        $att_detail = db::select("SELECT sunfish_shift_syncs.employee_id, name, section, employment_status, attend_code, absence_name, count(attend_code) as jml_hari from sunfish_shift_syncs
          left join employee_syncs on sunfish_shift_syncs.employee_id = employee_syncs.employee_id
          left join absence_categories on sunfish_shift_syncs.attend_code = absence_categories.absence_code
          where DATE_FORMAT(sunfish_shift_syncs.shift_date, '%M %Y') = '" . $request->get('period') . "'
          AND sunfish_shift_syncs.attend_code not like '%PRS%'
          AND sunfish_shift_syncs.attend_code <> ' OFF'
          AND (end_date is null OR end_date = sunfish_shift_syncs.shift_date)
          group by employee_id, name, section, attend_code, absence_name, employment_status
          HAVING jml_hari >= 5
          ORDER BY count(attend_code) desc");

        $response = array(
            'status' => true,
            'att_detail' => $att_detail,
        );
        return Response::json($response);
    }

}
