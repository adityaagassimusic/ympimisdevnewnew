<?php

namespace App\Http\Controllers;

use App\Approver;
use App\ClinicMedicine;
use App\ClinicMedicineLog;
use App\ClinicPatientDetail;
use App\EmployeeSync;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\User;
use DataTables;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\Printer;
use Response;

class ClinicController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');
        $this->diagnose = [
            'Abces',
            'Alergi',
            'Anemia',
            'Artritis',
            'Astma Bronchial',
            'Atralgia',
            'Bronkhitis',
            'Caries Dentis',
            'Cephalgia',
            'Chest Pain',
            'Colic Abdomen',
            'Combustio',
            'Commond Cold',
            'Conjungtivitis',
            'Contusio Musc',
            'Corpus Alienum',
            'Dermatitis Alergi',
            'Dermatitis Infecti',
            'Dermatomikosis',
            'Disfagia',
            'DKA (Dermatitis Kontak Alergi)',
            'Dysentri',
            'Dysmenorhae',
            'Dyspepsia',
            'Dyspneu',
            'Epistaxis',
            'Faringitis',
            'Flu',
            'Fluor Albus',
            'Furunkel',
            'Gastritis',
            'Gea',
            'Ginggivitis',
            'Gout',
            'Gravida',
            'Haemoroid',
            'Hematochizia',
            'Herpez Zoster',
            'Hiperemesis Gravidarum',
            'Hipertensi',
            'Hipertermi',
            'Hipotensi',
            'Hordeolum',
            'Hypertiroidisme',
            'Influenza',
            'Insect Bite',
            'Iritasi',
            'Isk',
            'Ispa',
            'Konstipasi',
            'Lbp',
            'Lethargi',
            'Leukore',
            'Limfadenopaty Submandibula',
            'Limphadenitis',
            'Menometroragi',
            'Metrorargia',
            'Migraen',
            'Morbili',
            'Myalgia',
            'Neuritis',
            'Obs.Tyfoid',
            'Observasi Febris',
            'Observasi Vomiting',
            'Oma',
            'Paronikia',
            'Parotitis',
            'Piodermi',
            'Pruritus',
            'Psikosomatik',
            'Ptirigium',
            'Pulpitis',
            'Rinitis',
            'Scabies',
            'Spasme Muscolorum',
            'Stomatitis',
            'Susp. Fam',
            'Suspec Abortus Iminen',
            'Suspec Apendixitis',
            'Suspec Thypoid',
            'Tension Headache',
            'Tinea Cruris',
            'Tinea Versicolor',
            'Tonsilitis',
            'Tonsilo Faringitis',
            'Trauma Kll',
            'Uri',
            'Urticaria',
            'Varicella',
            'Vertigo',
            'Vulnus Abratio',
            'Vulnus Infection',
            'Vulnus Laceratum',
        ];
        $this->doctor = [
            'Taliffia Setya, dr',
        ];
        $this->paramedic = [
            'Elis Kurniawati',
            'Ahmad Fanani',
            'Nanang Sugianto',
        ];
        $this->purpose = [
            'Pemeriksaan Kesehatan',
            'Konsultasi Kesehatan',
            'Istirahat Sakit',
            'Pulang (Sakit)',
            'Laktasi',
            'Kecelakaan Kerja',
        ];

    }

    public function indexMaskLog()
    {
        $title = "Surgical Mask Log";
        $title_jp = '';

        return view('clinic.mask_log', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Clinic');
    }

    public function indexClinicDisease()
    {
        $title = "Clinic Diagnostic Data";
        $title_jp = 'クリニック見立てデータ';

        return view('clinic.display.clinic_disease', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Clinic');
    }

    public function indexClinicMonitoring()
    {
        $title = 'Clinic Monitoring';
        $title_jp = 'クリニック監視';

        return view('clinic.display.clinic_visit_monitoring', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Clinic');
    }

    public function indexClinicVisit()
    {
        $title = 'Clinic Visit';
        $title_jp = 'クリニック訪問';

        return view('clinic.display.clinic_visit', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Clinic');
    }

    public function indexMedicines()
    {
        $title = "Clinic Medicines Data";
        $title_jp = 'クリニック薬品データ';

        return view('clinic.medicines', array(
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', $title)->with('head', 'Clinic');
    }

    public function indexDiagnose()
    {
        $title = 'Patient Diagnosis';
        $title_jp = '患者見立て';
        $medicines = ClinicMedicine::select('medicine_name')->get();

        return view('clinic.diagnose', array(
            'diagnoses' => $this->diagnose,
            'doctors' => $this->doctor,
            'paramedics' => $this->paramedic,
            'purposes' => $this->purpose,
            'medicines' => $medicines,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Diagnose')->with('head', 'Clinic');
    }

    public function indexVisitLog()
    {
        $title = 'Clinic Visit Logs';
        $title_jp = 'クリニック訪問記録';
        $employees = EmployeeSync::select('employee_id', 'name')->get();
        $medicines = ClinicMedicine::select('medicine_name')->get();
        $departments = db::select("SELECT DISTINCT
         department
         FROM
         employee_syncs
         WHERE
         department IS NOT NULL
         ORDER BY
         department ASC");

        return view('clinic.visit_logs', array(
            'diagnoses' => $this->diagnose,
            'doctors' => $this->doctor,
            'paramedics' => $this->paramedic,
            'purposes' => $this->purpose,
            'employees' => $employees,
            'departments' => $departments,
            'medicines' => $medicines,
            'title' => $title,
            'title_jp' => $title_jp,
        ))->with('page', 'Visit Logs')->with('head', 'Clinic');
    }

    public function fetchMedicines()
    {
        $medicine = ClinicMedicine::get();

        return DataTables::of($medicine)
            ->addColumn('button', function ($medicine) {
                return '<button style="padding: 3%;" class="btn btn-md btn-success" id="' . $medicine->id . '#' . $medicine->medicine_name . '#' . $medicine->quantity . '" onclick="addStock(this)">Edit Stock</button>';
            })
            ->rawColumns(['button' => 'button'])
            ->make(true);

    }

    public function fetchVisitEdit(Request $request)
    {

        $patient = db::select("SELECT c.visited_at, c.employee_id, e.`name`, e.department, c.purpose, c.paramedic, c.doctor, GROUP_CONCAT(c.diagnose) as diagnose FROM clinic_patient_details c
         left join employee_syncs e on e.employee_id = c.employee_id
         where c.patient_list_id = '" . $request->get('id') . "'
         group by c.visited_at, c.employee_id, e.`name`, e.department, c.purpose, c.paramedic, c.doctor");

        $medicines = db::select("select * from clinic_medicine_logs
         where clinic_patient_detail = '" . $request->get('id') . "'");

        $response = array(
            'status' => true,
            'patient' => $patient,
            'medicines' => $medicines,
        );
        return Response::json($response);
    }

    public function fetchVisitLogExcel(Request $request)
    {
        $clinic_visit_logs = ClinicPatientDetail::leftJoin(db::raw('(select * from employee_syncs) as patient'), 'patient.employee_id', '=', 'clinic_patient_details.employee_id');
        if (strlen($request->get('visitFrom')) > 0) {
            $visitFrom = date('Y-m-d', strtotime($request->get('visitFrom')));
            $clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '>=', $visitFrom);
        }
        if (strlen($request->get('visitTo')) > 0) {
            $visitTo = date('Y-m-d', strtotime($request->get('visitTo')));
            $clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '<=', $visitTo);
        }
        if ($request->get('employee_id') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('patient.employee_id', $request->get('employee_id'));
        }
        if ($request->get('department') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('patient.department', $request->get('department'));
        }
        if ($request->get('purpose') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.purpose', $request->get('purpose'));
        }
        if ($request->get('paramedic') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.paramedic', $request->get('paramedic'));
        }
        if ($request->get('diagnose') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.diagnose', $request->get('diagnose'));
        }
        $clinic_visit_logs = $clinic_visit_logs->groupBy('clinic_patient_details.visited_at',
            'clinic_patient_details.patient_list_id',
            'clinic_patient_details.employee_id',
            'patient.name',
            'patient.department',
            'clinic_patient_details.paramedic',
            'clinic_patient_details.purpose');
        $clinic_visit_logs = $clinic_visit_logs->orderBy('clinic_patient_details.visited_at', 'asc')
            ->select(
                'clinic_patient_details.visited_at',
                'clinic_patient_details.patient_list_id',
                'clinic_patient_details.employee_id',
                db::raw('concat(SPLIT_STRING(patient.name, " ", 1)," ",SPLIT_STRING(patient.name, " ", 2)) as name'),
                'patient.department',
                'clinic_patient_details.paramedic',
                'clinic_patient_details.purpose',
                db::raw('group_concat(diagnose) as diagnose')
            )->get();

        return $request;

        $data = array(
            'clinic_visit_logs' => $clinic_visit_logs,
        );
        ob_clean();

        Excel::create('Clinic Visit Logs', function ($excel) use ($data) {
            $excel->sheet('Visit Logs', function ($sheet) use ($data) {
                return $sheet->loadView('clinic.visit_log_excel', $data);
            });
        })->export('xlsx');
    }

    public function fetchVisitLog(Request $request)
    {
        $clinic_visit_logs = ClinicPatientDetail::leftJoin(db::raw('(select * from employee_syncs) as patient'), 'patient.employee_id', '=', 'clinic_patient_details.employee_id');

        if (strlen($request->get('visitFrom')) > 0) {
            $visitFrom = date('Y-m-d', strtotime($request->get('visitFrom')));
            $clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '>=', $visitFrom);
        }
        if (strlen($request->get('visitTo')) > 0) {
            $visitTo = date('Y-m-d', strtotime($request->get('visitTo')));
            $clinic_visit_logs = $clinic_visit_logs->where(db::raw('date(clinic_patient_details.visited_at)'), '<=', $visitTo);
        }
        if ($request->get('employee_id') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('patient.employee_id', $request->get('employee_id'));
        }
        if ($request->get('department') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('patient.department', $request->get('department'));
        }
        if ($request->get('purpose') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.purpose', $request->get('purpose'));
        }
        if ($request->get('paramedic') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.paramedic', $request->get('paramedic'));
        }
        if ($request->get('diagnose') != 0) {
            $clinic_visit_logs = $clinic_visit_logs->whereIn('clinic_patient_details.diagnose', $request->get('diagnose'));
        }
        $clinic_visit_logs = $clinic_visit_logs->groupBy(
            'clinic_patient_details.visited_at',
            'clinic_patient_details.employee_id',
            'patient.name',
            'patient.department',
            'clinic_patient_details.paramedic',
            'clinic_patient_details.purpose')
            ->orderBy('clinic_patient_details.visited_at', 'desc')
            ->select(
                'clinic_patient_details.visited_at',
                'clinic_patient_details.employee_id',
                'patient.name',
                'patient.department',
                'clinic_patient_details.paramedic',
                'clinic_patient_details.purpose',
                db::raw('group_concat(diagnose) as diagnose')
            )->get();

        $response = array(
            'status' => true,
            'logs' => $clinic_visit_logs,
        );
        return Response::json($response);
    }

    public function fetchDiagnose(Request $request)
    {
        $id = '';
        if ($request->get('id') != null) {
            $id = 'and p.idx = ' . $request->get('id');
        }

        $visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.name, e.hire_date, e.section  from patient_list p
         left join ympimis.employee_syncs e on e.employee_id = p.employee_id
         where p.`status` is null
         and p.note is null " . $id . "
         order by p.in_time asc");

        $response = array(
            'status' => true,
            'visitor' => $visitor,
        );
        return Response::json($response);
    }

    public function fetchPatient()
    {
        $visitor = db::connection('clinic')->select("select p.idx, p.in_time, p.employee_id, e.name, e.hire_date, e.section, d.purpose, p.note as bed from patient_list p
         left join ympimis.employee_syncs e on e.employee_id = p.employee_id
         left join (SELECT DISTINCT patient_list_id, purpose FROM ympimis.clinic_patient_details) d on d.patient_list_id = p.idx
         order by p.in_time asc");

        $response = array(
            'status' => true,
            'visitor' => $visitor,
        );
        return Response::json($response);
    }

    public function fetchDailyClinicVisit(Request $request)
    {

        $datefrom = date('Y-m-01');
        $dateto = date('Y-m-d');

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        }

        $clinic_visit = db::select("SELECT DATE_FORMAT( date.week_date, '%d %b %Y' ) AS week_date, COALESCE ( log.sum, 0 ) AS visit FROM
         (SELECT week_date, DATE_FORMAT( week_date, '%a' ) AS `day` FROM weekly_calendars
            WHERE	DATE_FORMAT( week_date, '%Y-%m-%d' ) >= '" . $datefrom . "'
            AND DATE_FORMAT( week_date, '%Y-%m-%d' ) <= '" . $dateto . "'
            AND remark <> 'H'
            ORDER BY week_date ASC) AS date
            LEFT JOIN
            (SELECT log.tanggal AS tanggal, count( log.patient_list_id ) AS sum FROM
            (SELECT DISTINCT date( visited_at ) as tanggal, patient_list_id FROM clinic_patient_details
            WHERE DATE_FORMAT( visited_at, '%Y-%m-%d' ) >= '" . $datefrom . "'
            AND DATE_FORMAT( visited_at, '%Y-%m-%d' ) <= '" . $dateto . "'
            AND purpose IN ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Pulang (Sakit)', 'Kecelakaan Kerja')) AS log
            GROUP BY tanggal) AS log
            ON date.week_date = log.tanggal");

        $response = array(
            'status' => true,
            'clinic_visit' => $clinic_visit,
            'datefrom' => date_format(date_create($datefrom), "d M Y"),
            'dateto' => date_format(date_create($dateto), "d M Y"),
        );
        return Response::json($response);
    }

    public function fetchClinicVisit(Request $request)
    {
        $datefrom = date('Y-m-01');
        $dateto = date('Y-m-d');

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        }

        $clinic_visit = db::select("select e.department, count(visit.employee_id) as qty from
            (select distinct c.employee_id, c.patient_list_id from clinic_patient_details c
            where DATE_FORMAT(c.created_at,'%Y-%m-%d') >= '" . $datefrom . "'
            and DATE_FORMAT(c.created_at,'%Y-%m-%d') <= '" . $dateto . "'
            and c.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Pulang (Sakit)', 'Kecelakaan Kerja')) visit
            left join employee_syncs e on visit.employee_id = e.employee_id
            where e.department is not null
            group by e.department
            order by qty desc");

        $clinic_visit_detail = db::select("select dept.department, dept.purpose, COALESCE(qty.qty,0) as qty from
            (select dept.department, purpose.purpose from
            (select distinct department from employee_syncs
            where department is not null
            order by department asc) as dept
            cross join
            (SELECT 'Pemeriksaan Kesehatan' AS purpose
            UNION ALL
            SELECT 'Konsultasi Kesehatan' AS purpose
            UNION ALL
            SELECT 'Istirahat Sakit' AS purpose
            UNION ALL
            SELECT 'Pulang (Sakit)' AS purpose
            UNION ALL
            SELECT 'Kecelakaan Kerja' AS purpose) as purpose) as dept
            left join
            (SELECT e.department, visit.purpose, count( visit.employee_id ) AS qty FROM
            (SELECT DISTINCT c.employee_id, c.patient_list_id, c.purpose FROM clinic_patient_details c
            WHERE	DATE_FORMAT( c.created_at, '%Y-%m-%d' ) >= '" . $datefrom . "'
            AND	DATE_FORMAT( c.created_at, '%Y-%m-%d' ) <= '" . $dateto . "'
            AND c.purpose IN ( 'Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Pulang (Sakit)', 'Kecelakaan Kerja' )
            ) visit
            LEFT JOIN employee_syncs e ON visit.employee_id = e.employee_id
            WHERE e.department IS NOT NULL
            GROUP BY e.department, visit.purpose) as qty
            on dept.department = qty.department and dept.purpose = qty. purpose");

        $tot_emp = db::select("select department ,count(employee_id) as emp from employee_syncs where end_date is null and department is not null GROUP BY department");

        $response = array(
            'status' => true,
            'clinic_visit' => $clinic_visit,
            'clinic_visit_detail' => $clinic_visit_detail,
            'employees' => $tot_emp,
            'datefrom' => date_format(date_create($datefrom), "d M Y"),
            'dateto' => date_format(date_create($dateto), "d M Y"),
        );
        return Response::json($response);
    }

    public function fetchClinicVisitDetail(Request $request)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));

        $detail = db::select("select distinct d.patient_list_id, d.employee_id, e.`name`, d.paramedic, d.visited_at, d.purpose  from clinic_patient_details d
            left join ympimis.employee_syncs e on e.employee_id = d.employee_id
            where DATE_FORMAT(d.visited_at,'%Y-%m-%d') >= '" . $datefrom . "'
            and DATE_FORMAT(d.visited_at,'%Y-%m-%d') <= '" . $dateto . "'
            and d.purpose in ('Pemeriksaan Kesehatan', 'Konsultasi Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja')
            and e.department like '%" . $request->get('department') . "%'");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
    }

    public function fetchClinicMasker(Request $request)
    {
        $datefrom = date('Y-m-01');
        $dateto = date('Y-m-d');

        if (strlen($request->get('datefrom')) > 0) {
            $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        }
        if (strlen($request->get('dateto')) > 0) {
            $dateto = date('Y-m-d', strtotime($request->get('dateto')));
        }

        $masker = db::select("SELECT medicine.department, SUM(medicine.quantity) AS quantity FROM
            (SELECT DISTINCT m.id, e.department, m.quantity FROM clinic_medicine_logs m
               LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
               LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id
               WHERE m.medicine_name = 'Surgical Masker'
               AND m.`status` = 'out'
               AND DATE_FORMAT( m.created_at, '%Y-%m-%d' ) >= '" . $datefrom . "'
               AND DATE_FORMAT( m.created_at, '%Y-%m-%d' ) <= '" . $dateto . "') medicine
               GROUP BY medicine.department
               ORDER BY quantity DESC");

        $response = array(
            'status' => true,
            'masker' => $masker,
            'datefrom' => date_format(date_create($datefrom), "d M Y"),
            'dateto' => date_format(date_create($dateto), "d M Y"),
        );
        return Response::json($response);
    }

    public function fetchMaskLog(Request $request)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('visitFrom')));
        $dateto = date('Y-m-d', strtotime($request->get('visitTo')));

        $logs = db::select("SELECT DISTINCT m.id, p.visited_at, p.employee_id, e.`name`, p.paramedic, p.purpose, m.quantity FROM clinic_medicine_logs m
               LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
               LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id
               WHERE m.medicine_name = 'Surgical Masker'
               AND m.`status` = 'out'
               AND DATE_FORMAT(m.created_at, '%Y-%m-%d') >= '" . $datefrom . "'
               AND DATE_FORMAT(m.created_at, '%Y-%m-%d') <= '" . $dateto . "'
               ORDER BY p.visited_at ASC");

        $response = array(
            'status' => true,
            'logs' => $logs,
        );
        return Response::json($response);
    }

    public function fetchClinicMaskerDetail(Request $request)
    {
        $datefrom = date('Y-m-d', strtotime($request->get('datefrom')));
        $dateto = date('Y-m-d', strtotime($request->get('dateto')));

        $detail = db::select("SELECT DISTINCT m.id, p.visited_at, p.employee_id, e.`name`, p.paramedic, p.purpose, m.quantity FROM clinic_medicine_logs m
               LEFT JOIN clinic_patient_details p ON p.patient_list_id = m.clinic_patient_detail
               LEFT JOIN employee_syncs e ON e.employee_id = p.employee_id
               WHERE m.medicine_name = 'Surgical Masker'
               AND m.`status` = 'out'
               AND DATE_FORMAT(m.created_at, '%Y-%m-%d') >= '" . $datefrom . "'
               AND DATE_FORMAT(m.created_at, '%Y-%m-%d') <= '" . $dateto . "'
               AND e.department like '%" . $request->get('department') . "%'
               ORDER BY p.visited_at ASC");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
    }

    public function fetchDisease(Request $request)
    {
        $date_log = "";
        $month = "";

        if (strlen($request->get('month')) > 0) {
            $date_log = "where DATE_FORMAT(visited_at,'%Y-%m') = '" . $request->get('month') . "'";
            $month = $request->get('month');
        } else {
            $date_log = "WHERE DATE_FORMAT(visited_at,'%Y-%m') = '" . date('Y-m') . "'";
            $month = date('Y-m');
        }

        $disease = db::select("select diagnose, count(employee_id) qty from clinic_patient_details " . $date_log . "
               and diagnose is not null
               and diagnose <> ''
               group by diagnose
               order by qty desc");

        $response = array(
            'status' => true,
            'disease' => $disease,
            'month' => $month,
        );
        return Response::json($response);
    }

    public function fetchDiseaseDetail(Request $request)
    {

        $detail = db::select("select p.diagnose, p.employee_id, e.name, p.paramedic, p.visited_at from clinic_patient_details p
               left join employee_syncs e on e.employee_id = p.employee_id
               where DATE_FORMAT(p.visited_at,'%Y-%m') = '" . $request->get('month') . "'
               and p.diagnose like '%" . $request->get('disease') . "%'
               order by p.visited_at asc");

        $response = array(
            'status' => true,
            'detail' => $detail,
        );
        return Response::json($response);
    }

    public function deleteVisitor(Request $request)
    {
        $id = $request->get('id');

        try {
            $patient = db::connection('clinic')->table('patient_list')
                ->where('idx', '=', $id)
                ->update([
                    'note' => 'delete',
                ]);

            $response = array(
                'status' => true,
                'message' => 'patient was successfully deleted',
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

    public function editDiagnose(Request $request)
    {
        $employee_id = $request->get('employee_id');
        $purpose = $request->get('purpose');
        $paramedic = $request->get('paramedic');
        $doctor = $request->get('doctor');
        $visited_at = $request->get('visited_at');

        try {

            //Last History
            $last = ClinicPatientDetail::where('visited_at', $visited_at)
                ->where('employee_id', $employee_id)
                ->first();

            //Input Patient Diagnose
            $delete = ClinicPatientDetail::where('visited_at', $visited_at)
                ->where('employee_id', $employee_id)
                ->forceDelete();

            if ($request->get('diagnose') != null) {
                $diagnoses = $request->get('diagnose');
                for ($x = 0; $x < count($diagnoses); $x++) {
                    $diagnose =
                    $clinic_patient_detail = new ClinicPatientDetail([
                        'visited_at' => $visited_at,
                        'employee_id' => $employee_id,
                        'purpose' => $purpose,
                        'diagnose' => $diagnoses[$x],
                        'body_temperature' => $last->body_temperature,
                        'oxy' => $last->oxy,
                        'paramedic' => $paramedic,
                        'doctor' => $doctor,
                        'family' => $last->family,
                        'family_name' => $last->family_name,
                        'action' => $last->action,
                        'suggestion' => $last->suggestion,
                    ]);
                    $clinic_patient_detail->save();
                }
            } else {
                $clinic_patient_detail = new ClinicPatientDetail([
                    'visited_at' => $visited_at,
                    'employee_id' => $employee_id,
                    'purpose' => $purpose,
                    'diagnose' => $last->diagnose,
                    'body_temperature' => $last->body_temperature,
                    'oxy' => $last->oxy,
                    'paramedic' => $paramedic,
                    'doctor' => $doctor,
                    'family' => $last->family,
                    'family_name' => $last->family_name,
                    'action' => $last->action,
                    'suggestion' => $last->suggestion,
                ]);
                $clinic_patient_detail->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Patient Data`s successfully saved',
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

    public function scanRegister(Request $request)
    {

        $tag = $request->get('tag');

        if (str_contains($tag, 'PI') || str_contains($tag, 'OS')) {

            $employee = db::table('employee_syncs')
                ->where('employee_id', $tag)
                ->first();

            if (!$employee) {
                $response = array(
                    'status' => false,
                    'message' => 'ID not registered',
                );
                return Response::json($response);
            }

            $patient = db::connection('clinic')
                ->table('patient_list')
                ->where('employee_id', $employee->employee_id)
                ->first();

            if ($patient) {
                $response = array(
                    'status' => false,
                    'message' => 'Employee already registered in clinic',
                );
                return Response::json($response);
            }

        } else {
            $employee = db::table('employees')
                ->where('tag', $tag)
                ->first();

            if (!$employee) {
                $response = array(
                    'status' => false,
                    'message' => 'ID Card not registered',
                );
                return Response::json($response);
            }

            $patient = db::connection('clinic')
                ->table('patient_list')
                ->where('employee_id', $employee->employee_id)
                ->first();

            if ($patient) {
                $response = array(
                    'status' => false,
                    'message' => 'Employee already registered in clinic',
                );
                return Response::json($response);
            }
        }

        try {
            $clinic_patient = db::connection('clinic')
                ->table('patient_list')
                ->insert([
                    'employee_id' => $employee->employee_id,
                    'tanggal' => date('Y-m-d'),
                    'in_time' => date('Y-m-d H:i:s'),
                    'last_seen' => date('Y-m-d H:i:s'),
                    'uid' => $tag,
                    'create_ts' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Employee registered successfully in clinic',
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

    public function inputDiagnose(Request $request)
    {
        $idx = $request->get('id');
        $employee_id = $request->get('nik');
        $purpose = $request->get('purpose');
        $bed = $request->get('bed');

        $diagnose = null;

        $body_temperature = $request->get('temperature');
        $oxy = $request->get('oxy');
        $paramedic = $request->get('paramedic');
        $doctor = $request->get('doctor');
        $family = $request->get('family');
        $family_name = $request->get('family_name');
        $visited_at = $request->get('date');

        $masker = $request->get('masker');
        $glove = $request->get('glove');

        $action = $request->get('action');
        $suggestion = $request->get('suggestion');

        try {
            //Input Patient Diagnose

            if ($request->get('diagnose') != null) {
                $diagnoses = $request->get('diagnose');
                for ($x = 0; $x < count($diagnoses); $x++) {
                    $diagnose =
                    $clinic_patient_detail = new ClinicPatientDetail([
                        'employee_id' => $employee_id,
                        'patient_list_id' => $idx,
                        'purpose' => $purpose,
                        'diagnose' => $diagnoses[$x],
                        'body_temperature' => $body_temperature,
                        'oxy' => $oxy,
                        'paramedic' => Auth::user()->name,
                        'doctor' => $doctor,
                        'family' => $family,
                        'family_name' => $family_name,
                        'visited_at' => $visited_at,
                        'action' => $action,
                        'suggestion' => $suggestion,
                    ]);
                    $clinic_patient_detail->save();

                }
            } else {
                $clinic_patient_detail = new ClinicPatientDetail([
                    'employee_id' => $employee_id,
                    'patient_list_id' => $idx,
                    'purpose' => $purpose,
                    'diagnose' => $diagnose,
                    'paramedic' => Auth::user()->name,
                    'doctor' => $doctor,
                    'family' => $family,
                    'family_name' => $family_name,
                    'visited_at' => $visited_at,
                ]);
                $clinic_patient_detail->save();

                if ($purpose == 'Petugas Cek Suhu') {
                    if ($masker > 0) {
                        $clinic_medicine = ClinicMedicine::where('medicine_name', 'Surgical Masker')->first();
                        $clinic_medicine->quantity = $clinic_medicine->quantity - $masker;
                        $clinic_medicine->save();

                        $medicine_log = new ClinicMedicineLog([
                            'medicine_name' => 'Surgical Masker',
                            'status' => 'out',
                            'clinic_patient_detail' => $idx,
                            'quantity' => $masker,
                        ]);
                        $medicine_log->save();
                    }

                    if ($glove > 0) {
                        $clinic_medicine = ClinicMedicine::where('medicine_name', 'Latex Glove')->first();
                        $clinic_medicine->quantity = $clinic_medicine->quantity - $glove;
                        $clinic_medicine->save();

                        $medicine_log = new ClinicMedicineLog([
                            'medicine_name' => 'Latex Glove',
                            'status' => 'out',
                            'clinic_patient_detail' => $idx,
                            'quantity' => $glove,
                        ]);
                        $medicine_log->save();
                    }
                }
            }

            //Input Medicine
            if ($request->get('medicine') != null) {
                $medicines = $request->get('medicine');
                $clinic_medicine_log = [];
                for ($x = 0; $x < count($medicines); $x++) {
                    $clinic_medicine_log[$x] = new ClinicMedicineLog([
                        'medicine_name' => $medicines[$x]['medicine_name'],
                        'status' => 'out',
                        'clinic_patient_detail' => $idx,
                        'quantity' => $medicines[$x]['quantity'],
                    ]);

                    $clinic_medicine[$x] = ClinicMedicine::where('medicine_name', $medicines[$x]['medicine_name'])->first();
                    $clinic_medicine[$x]->quantity = $clinic_medicine[$x]->quantity - $medicines[$x]['quantity'];

                    DB::transaction(function () use ($idx, $clinic_medicine_log, $clinic_medicine, $x, $bed) {
                        $clinic_medicine_log[$x]->save();
                        $clinic_medicine[$x]->save();

                        $clinic_patient = db::connection('clinic')->table('patient_list')
                            ->where('idx', '=', $idx)
                            ->update([
                                'status' => 'Yes',
                                'note' => $bed,
                            ]);
                    });
                }
            } else {
                $clinic_patient = db::connection('clinic')->table('patient_list')
                    ->where('idx', '=', $idx)
                    ->update([
                        'status' => 'Yes',
                        'note' => $bed,
                    ]);
            }

            //Update IVMS temperatur
            $include_purpose = [
                'Pemeriksaan Kesehatan',
                'Istirahat Sakit',
                'Pulang (Sakit)',
            ];

            if (in_array($purpose, $include_purpose)) {
                $check_status;
                if ($purpose == 'Pemeriksaan Kesehatan') {
                    $check_status = 'Kembali Bekerja';
                } else if ($purpose == 'Istirahat Sakit') {
                    $check_status = 'Istirahat di Klinik';
                } else if ($purpose == 'Pulang (Sakit)') {
                    $check_status = 'Pulang';
                }

                $ivms = db::table('ivms_temperatures')
                    ->where('employee_id', $employee_id)
                    ->where('date', date('Y-m-d'))
                    ->update([
                        'check_status' => $check_status,
                        'clinic_temperature' => $body_temperature,
                    ]);

                $oxy = db::table('general_attendance_logs')
                    ->where('employee_id', $employee_id)
                    ->where('purpose_code', 'Oxymeter')
                    ->where('remark', '<', 95)
                    ->where('due_date', date('Y-m-d'))
                    ->update([
                        'status' => $check_status,
                        'remark' => $oxy,
                    ]);
            }

            $to = db::select("SELECT users.email FROM employee_syncs
                  LEFT JOIN users ON users.username = employee_syncs.employee_id
                  WHERE employee_syncs.department IN ('Human Resources Department', 'General Affairs Department')
                  AND employee_syncs.position IN ('Chief', 'Manager')
                  AND (section <> 'Secretary Admin Section' OR section IS NULL)");

            $emp = EmployeeSync::leftJoin('employees', 'employees.employee_id', '=', 'employee_syncs.employee_id')
                ->where('employee_syncs.employee_id', $employee_id)
                ->select('employee_syncs.*', 'employees.remark')
                ->first();

            $cc = [];
            if ((strlen($emp->department)) > 0 && ($emp->division != 'Human Resources & General Affairs Division')) {

                $cc_mails = [];
                if ($emp->remark == 'OFC') {
                    $cc_mails = Approver::where('department', $emp->department)
                        ->where('approver_email', '<>', '')
                        ->whereIn('position', ['Manager', 'Chief'])
                        ->select(db::raw('approver_email AS email'))
                        ->get();
                } else {
                    $cc_mails = db::select("SELECT email FROM send_emails
                            WHERE remark = '" . $emp->section . "';");
                }

                for ($i = 0; $i < count($cc_mails); $i++) {
                    $cc[] = $cc_mails[$i]->email;
                }
            }

            $bcc = array();
            array_push($bcc, 'anton.budi.santoso@music.yamaha.com', 'muhammad.ikhlas@music.yamaha.com');

            $resume = db::select("SELECT cl.employee_id, e.`name`, e.department, cl.purpose, cl.paramedic, GROUP_CONCAT(' ', cl.diagnose) AS diagnose, MAX(cl.visited_at) AS visited_at FROM clinic_patient_details cl
                  LEFT JOIN employee_syncs e ON e.employee_id = cl.employee_id
                  WHERE cl.purpose IN ('Pemeriksaan Kesehatan', 'Istirahat Sakit', 'Kecelakaan Kerja', 'Pulang (Sakit)')
                  AND cl.employee_id = '" . $employee_id . "'
                  AND cl.visited_at = '" . $visited_at . "'
                  GROUP BY cl.employee_id, e.`name`, e.department, cl.purpose, cl.paramedic");

            $data = [
                'resume' => $resume,
            ];

            if (count($resume) > 0) {
                if (count($cc) > 0) {
                    Mail::to($to)
                        ->cc($cc)
                        ->bcc($bcc)
                        ->send(new SendEmail($data, 'clinic_visit'));
                } else {
                    Mail::to($to)
                        ->bcc($bcc)
                        ->send(new SendEmail($data, 'clinic_visit'));
                }

            }

            $response = array(
                'status' => true,
                'message' => 'Patient Data`s successfully saved',
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

    public function editMedicineStock(Request $request)
    {
        $id = $request->get('id');
        $quantity = $request->get('quantity');

        try {
            $medicine = ClinicMedicine::where('id', $id)->first();
            $medicine->quantity = $medicine->quantity + $quantity;
            $medicine->save();

            $medicine_log = new ClinicMedicineLog([
                'medicine_name' => $medicine->medicine_name,
                'status' => 'in',
                'quantity' => $quantity,
            ]);
            $medicine_log->save();

            $response = array(
                'status' => true,
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

    public function indexKontrolObat()
    {
        $title = 'Control Medicine Monitoring';
        // $title_jp = 'IK・DM・DLの管理';
        $role_user = User::where('username', Auth::user()->username)->first();
        $clinic_medicine = ClinicMedicine::whereNull('deleted_at')->get();

        // $this->printLabel("ss", "d");

        return view('clinic.control_medicine_index', array(
            'title' => $title,
            'role_user' => $role_user,
            'clinic_medicine' => $clinic_medicine,
        ))->with('page', 'Control Medicine Monitoring');
    }

    public function printLabel($jig_id, $item_alias)
    {

        $obat_id = "Amoxixilin";
        $item_alias = "12-10-2022";
        $md = "12-10-2022";
        $ed = "12-10-2024";

        $connector = new WindowsPrintConnector('MIS');
        $printer = new Printer($connector);

        // $printer->setJustification(Printer::JUSTIFY_CENTER);
        // $printer->setTextSize(1, 1);
        // $printer->text($jig_id.' '.$item_alias."\n");
        // $printer->setTextSize(1, 1);
        // $printer->qrCode($jig_id, Printer::QR_ECLEVEL_L, 5, Printer::QR_MODEL_2);
        // // $printer->feed(2);
        // $printer->initialize();
        // $printer->setEmphasis(true);
        // $printer->setTextSize(1, 1);
        // $printer->setJustification(Printer::JUSTIFY_CENTER);

        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 1);
        $printer->text($obat_id . "\n");
        $printer->setTextSize(1, 1);
        $printer->qrCode($jig_id, Printer::QR_ECLEVEL_L, 5, Printer::QR_MODEL_2);
        // $printer->textRaw("(" . date("d-M-Y H:i:s") . ")\n");
        $printer->feed(1);
        $printer->text('MD: ' . $md . ' | ' . 'EX: ' . $ed . "\n");
        $printer->setTextSize(1, 1);
        $printer->text(date("d-M-Y H:i:s") . "\n");
        $printer->cut();
        $printer->close();
    }

    public function indexCountStocktaking()
    {
        $title = 'Stocktaking Medicine Clinic';
        $title_jp = '';

        $obat = db::table('clinic_medicines')
            ->get();

        $department = [
            'Logistic Department',
            'Management Information System Department',
            'Production Control Department',
        ];

        $employees = EmployeeSync::whereIn('department', $department)
            ->whereNull('end_date')
            ->get();

        return view('clinic.index_stoctaking_medicine', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'employees' => $employees,
            'mpdl' => $obat,
        ))->with('page', 'Stock Taking Count Medicine')->with('head', 'Stock Taking Count Medicine');
    }

    public function fetchCountStoctakingObat(Request $request)
    {

        $obat = db::table('clinic_medicines')
            ->get();

        $response = array(
            'status' => true,
            'obat' => $obat,
        );
        return Response::json($response);

    }

}
