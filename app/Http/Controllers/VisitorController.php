<?php

namespace App\Http\Controllers;

use App\BodyTemperature;
use App\Employee;
use App\Mail\SendEmail;
use App\PlcCounter;
use App\Visitor;
use App\VisitorDetail;
use App\VisitorId;
use App\EmployeeSync;
use DataTables;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class VisitorController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        if (isset($_SERVER['HTTP_USER_AGENT'])) {
            $http_user_agent = $_SERVER['HTTP_USER_AGENT'];
            if (preg_match('/Word|Excel|PowerPoint|ms-office/i', $http_user_agent)) {
                // Prevent MS office products detecting the upcoming re-direct .. forces them to launch the browser to this link
                die();
            }
        }
    }

    public function index()
    {

        $employee_syncs = db::select("SELECT
			es.employee_id,
			es.name,
			es.department,
			d.department_shortname,
			es.section,
			es.group,
			es.sub_group
			FROM
			employee_syncs AS es
			LEFT JOIN departments AS d ON d.department_name = es.department
			WHERE
			(
				end_date IS NULL
				OR end_date >= date(
					now()))
			AND es.department IS NOT NULL
			AND es.grade_code <> 'J0-'
			ORDER BY es.hire_date ASC");

        return view('visitors.index', array(
            'employee_syncs' => $employee_syncs,
        ))->with('page', 'Visitor Index');
    }

//-------------registration

    public function registration()
    {

        $employees = "SELECT
		*,employee_syncs.employee_id as empid
		FROM
		employee_syncs
		LEFT JOIN office_members ON office_members.employee_id = employee_syncs.employee_id
		WHERE
		position NOT LIKE '%Operator%'
		and employee_syncs.end_date is null";
        $employee = DB::select($employees);

        // $companies = "SELECT
        // DISTINCT(company)
        // FROM
        // visitors";
        // $company = DB::select($companies);
        $company = DB::table('visitor_masters')
            ->orderBy('company', 'ASC')
            ->get();

        return view('visitors.registration', array(
            'employee' => $employee,
            'company' => $company,
        ))->with('page', 'Visitor Registration');
    }

    public function simpanheader(Request $request)
    {
        $lop = $request->get('lop2');
        $lop_suhu = $request->get('lop_suhu2');
        // var_dump($id);
        try {
            //----insert visitor
            if ($request->get('company2') == 'LAIN-LAIN') {
                $company = $request->get('company');
                $insert_company = db::table('visitor_masters')
                    ->insert([
                        'company' => strtoupper($company),
                        'created_by' => Auth::id(),
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

                if ($request->get('pol') == null) {
                    $visitor = new visitor([
                        'company' => strtoupper($request->get('company')),
                        'purpose' => strtoupper($request->get('purpose2')),
                        'detail_purpose' => strtoupper($request->get('purpose')),
                        'status' => strtoupper($request->get('status')),
                        'jumlah' => strtoupper($request->get('jumlah')),
                        'location' => "SECURITY",
                        'destination' => strtoupper($request->get('destination')),
                        'employee' => strtoupper($request->get('employee')),
                        'transport' => strtoupper($request->get('kendaraan')),
                    ]);
                } else {
                    $visitor = new visitor([
                        'company' => strtoupper($request->get('company')),
                        'purpose' => strtoupper($request->get('purpose2')),
                        'detail_purpose' => strtoupper($request->get('purpose')),
                        'status' => strtoupper($request->get('status')),
                        'jumlah' => strtoupper($request->get('jumlah')),
                        'location' => "SECURITY",
                        'destination' => strtoupper($request->get('destination')),
                        'employee' => strtoupper($request->get('employee')),
                        'transport' => strtoupper($request->get('kendaraan')),
                        'pol' => str_replace(' ', '', strtoupper($request->get('pol'))),
                    ]);
                }
            } else {
                $company = strtoupper($request->get('company2'));
                if ($request->get('pol') == null) {
                    $visitor = new visitor([
                        'company' => strtoupper($request->get('company2')),
                        'purpose' => strtoupper($request->get('purpose2')),
                        'detail_purpose' => strtoupper($request->get('purpose')),
                        'status' => strtoupper($request->get('status')),
                        'jumlah' => strtoupper($request->get('jumlah')),
                        'location' => "SECURITY",
                        'destination' => strtoupper($request->get('destination')),
                        'employee' => strtoupper($request->get('employee')),
                        'transport' => strtoupper($request->get('kendaraan')),
                    ]);
                } else {
                    $visitor = new visitor([
                        'company' => strtoupper($request->get('company2')),
                        'purpose' => strtoupper($request->get('purpose2')),
                        'detail_purpose' => strtoupper($request->get('purpose')),
                        'status' => strtoupper($request->get('status')),
                        'jumlah' => strtoupper($request->get('jumlah')),
                        'location' => "SECURITY",
                        'destination' => strtoupper($request->get('destination')),
                        'employee' => strtoupper($request->get('employee')),
                        'transport' => strtoupper($request->get('kendaraan')),
                        'pol' => str_replace(' ', '', strtoupper($request->get('pol'))),
                    ]);
                }
            }

            $visitor->save();
            $id = Visitor::orderby('created_at', 'desc')->first();

            for ($i = 0; $i < $lop_suhu; $i++) {

                $nama = "nama" . $i;
                $suhu = "suhu" . $i;
                $kota = "kota" . $i;
                $namavis = $request->get($nama);
                $kotavis = $request->get($kota);
                $suhuvis = $request->get($suhu);
                if ($request->get('company') == null) {
                    $suhunew = new BodyTemperature([
                        'company' => $request->get('company2'),
                        'name' => $request->get($nama),
                        'kota' => $request->get($kota),
                        'suhu' => $request->get($suhu),
                        'created_by' => Auth::id(),
                    ]);
                } else {
                    $suhunew = new BodyTemperature([
                        'company' => $request->get('company'),
                        'name' => $request->get($nama),
                        'kota' => $request->get($kota),
                        'suhu' => $request->get($suhu),
                        'created_by' => Auth::id(),
                    ]);
                }
                $suhunew->save();
            }

            if ($request->get('destination') == 'Office') {

                $emp_sync = DB::SELECT("SELECT * FROM employee_syncs WHERE employee_id = '" . $request->get('employee') . "'");

                if (count($emp_sync) > 0) {
                    foreach ($emp_sync as $key) {
                        $department = $key->department;
                        $name = $key->name;
                    }

                    if ($department == null && $name == 'Budhi Apriyanto') {
                        $department = 'Management Information System Department';
                        $mail_to = DB::SELECT("SELECT
							email
							FROM
							visitor_confirmers
							WHERE
							department = '" . $department . "'
							AND remark IS NULL
							AND email IS NOT NULL");

                        $namamanager[] = ['employees' => $name,
                            'department' => $department,
                            'company' => $company,
                            'nama' => $namavis,
                            'kota' => $kotavis,
                            'suhu' => $suhuvis,
                            'id' => $id->id,
                        ];
                    } elseif ($department == null && $name == 'Arief Soekamto') {
                        $department = 'Human Resources Department';
                        $mail_to = DB::SELECT("SELECT
						email
						FROM
						visitor_confirmers
						WHERE
						department = '" . $department . "'
						AND remark IS NULL
						AND email IS NOT NULL");

                        $namamanager[] = ['employees' => $name,
                            'department' => $department,
                            'company' => $company,
                            'nama' => $namavis,
                            'kota' => $kotavis,
                            'suhu' => $suhuvis,
                            'id' => $id->id,
                        ];
                    } else {
                        $mail_to = DB::SELECT("SELECT
					email
					FROM
					visitor_confirmers
					WHERE
					department = '" . $department . "'
					AND remark IS NULL
					AND email IS NOT NULL");

                        $namamanager[] = ['employees' => $name,
                            'department' => $department,
                            'company' => $company,
                            'nama' => $namavis,
                            'kota' => $kotavis,
                            'suhu' => $suhuvis,
                            'id' => $id->id,
                        ];
                    }
                }

                $contactList = [];
                $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
                $contactList[1] = 'rio.irvansyah@music.yamaha.com';
                $contactList[2] = 'muhammad.ikhlas@music.yamaha.com';
                Mail::to($mail_to)->bcc($contactList, 'Contact List')->send(new SendEmail($namamanager, 'incoming_visitor'));
            }

            //----insert detail
            for ($i = 0; $i < $lop; $i++) {

                $visitor_id = "visitor_id" . $i;
                $visitor_name = "visitor_name" . $i;
                $telp = "telp" . $i;
                $VisitorDetail = new VisitorDetail([
                    'id_number' => $request->get($visitor_id),
                    'id_visitor' => $id->id,
                    'full_name' => $request->get($visitor_name),
                    'telp' => $request->get($telp),
                    'status' => $request->get('status'),
                ]);
                $VisitorDetail->save();

                $tabelvisitorid = VisitorId::updateOrCreate(
                    [

                        'ktp' => $request->get($visitor_id),

                    ],
                    [
                        'ktp' => $request->get($visitor_id),
                        'full_name' => $request->get($visitor_name),
                        'telp' => $request->get($telp),
                    ]
                );
            }

            return redirect('visitor_registration')->with('status', 'Input Visitor Registration success ')->with('page', 'Visitor Registration');
        } catch (QueryException $e) {
            return redirect('visitor_registration')->with('error', $e->getMessage())->with('page', 'Visitor Registration');
        }
    }

    public function getdata(Request $request)
    {

        $id_list = VisitorId::where('ktp', '=', $request->get('ktp'))->get();

        $response = array(
            'status' => true,
            'id_list' => $id_list,
        );
        return Response::json($response);
    }

    //-------------------- end registration

    //----------------- list

    public function receive()
    {
        return view('visitors.receive')->with('page', 'Visitor Index');
    }

    public function filllist($nik)
    {
        $id = Auth::id();
        $tgl = date('Y-m-d');
        $kurang = date('Y-m-d', strtotime('-14 days'));

        if ($nik != "") {
            // $where = "where employee = '".$nik."'";
            $where = " WHERE
		a.created_at2 >= '" . $kurang . "'
		AND a.created_at2 <= '" . $tgl . "'
		AND employee IN (
		SELECT
		employee_id
		FROM
		employee_syncs
		WHERE
		employee_id = '" . $nik . "')";
        }

        if ($nik == "asd") {
            $where = "WHERE a.created_at2 >='" . $kurang . "' and a.created_at2<='" . $tgl . "' ";
        }

        $op = "SELECT
	*,
	count(
		DISTINCT ( total1 )) AS total
	FROM
	(
		SELECT
		DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
		visitors.created_at,
		visitors.employee,
		visitors.id,
        visitors.jumlah,
		company,
		visitor_details.full_name,
		visitor_details.id_number AS total1,
		purpose,
		visitors.status,
		employee_syncs.name,
		employee_syncs.department,
		visitor_details.in_time,
		visitor_details.out_time,
		visitors.remark
		FROM
		visitors
		LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
		LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
		) a
	" . $where . "
	GROUP BY
	a.id
	ORDER BY
	created_at DESC";
        $ops = DB::select($op);
        return DataTables::of($ops)

        // confirm pertag
        // ->addColumn('edit', function($ops){
        //     return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
        // })

            ->addColumn('edit', function ($ops) {
                return '<a href="javascript:void(0)" data-toggle="modal" class="btn btn-xs btn-warning" onClick="editop(id)" id="' . $ops->id . '"><i class="fa fa-edit"></i></a>';
            })
            ->rawColumns(['edit' => 'edit'])

            ->make(true);
    }

    public function editlist(Request $request)
    {
        $id = $request->get('id');
        $id_list = VisitorDetail::where('id_visitor', '=', $request->get('id'))->get();
        $header_lists = "SELECT DISTINCT
	( visitors.id ),
	company,
	name,
	employee_syncs.department,
	employee_syncs.department AS shortname
	FROM
	visitors
	LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
	WHERE
	visitors.id='" . $id . "'";
        $header_list = DB::select($header_lists);
        $response = array(
            'status' => true,
            'id_list' => $id_list,
            'header_list' => $header_list,
        );
        return Response::json($response);
    }

    public function inputtag(Request $request)
    {

        try {

            $id = $request->get('id');
            $intime = date('Y-m-d H:i:s');
            $visitordetail = VisitorDetail::where('id', '=', str_replace("V", "", $id))
                ->withTrashed()
                ->first();

            $visitordetail->tag = $request->get('idtag');
            $visitordetail->in_time = $intime;

            $tags = DB::connection('ympimis_2')->table('visitor_tags')->where('tag',$request->get('idtag'))->first();
            if ($tags) {
                $visitordetail->tag_number = $tags->number;
            }
            $visitordetail->save();

            $response = array(
                'status' => true,
                'message' => 'Input Success',
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

//-------------------- end list

//-------------------- at lobby

    public function scanVisitorLobby(Request $request)
    {

        $tag_visitor = $request->get('tag_visitor');

        if (strlen($tag_visitor) > 9) {
            $tag_visitor = substr($tag_visitor, 0, 9);
        }

        $visitorDetail = VisitorDetail::where('tag', 'like', '%' . $tag_visitor . '%')->orderby('id', 'desc')->first();

        if (count($visitorDetail) > 0) {
            $id_visitor = $visitorDetail->id_visitor;

            $visitor = Visitor::find($id_visitor);
            if ($visitor->location == 'Lobby') {
                $visitor2 = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->where('visitors.id', $id_visitor)->first();
            } else if ($visitor->location == 'Security' && $visitor->destination != 'Office') {
                $visitor2 = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->where('visitors.id', $id_visitor)->first();
            } else if ($visitor->location == 'Security' && $visitor->destination == 'Office') {
                $visitor->location = 'Lobby';
                $visitor->save();

                $visitor2 = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->where('visitors.id', $id_visitor)->first();

                $plc = PlcCounter::where('origin_group_code', 'visitor_lobby')->first();
                $counter = $plc->plc_counter;
                $id_plc = $plc->id;

                $plccounter = PlcCounter::find($id_plc);
                $plccounter->plc_counter = 0;
                $plccounter->save();

                $plc2 = PlcCounter::where('origin_group_code', 'visitor_lobby2')->first();
                $counter2 = $plc2->plc_counter;
                $id_plc2 = $plc2->id;

                $plccounter2 = PlcCounter::find($id_plc2);
                $plccounter2->plc_counter = 0;
                $plccounter2->save();

                $plc_sec = PlcCounter::where('origin_group_code', 'visitor')->first();
                $counter_sec = $plc_sec->plc_counter;
                $id_plc_sec = $plc_sec->id;

                $plccounter_sec = PlcCounter::find($id_plc_sec);
                $plccounter_sec->plc_counter = $counter_sec - 1;
                $plccounter_sec->save();

                $plc_sec2 = PlcCounter::where('origin_group_code', 'visitor2')->first();
                $counter_sec2 = $plc_sec2->plc_counter;
                $id_plc_sec2 = $plc_sec2->id;

                $plccounter_sec2 = PlcCounter::find($id_plc_sec2);
                $plccounter_sec2->plc_counter = $counter_sec2 - 1;
                $plccounter_sec2->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Scan Success',
                'visitor' => $visitor2,
                'location' => $visitor->location,
                'destination' => $visitor->destination,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Tag Invalid',
            );
            return Response::json($response);
        }
    }

//-------------------- end at lobby

//--------------------- confirmation

    public function confirmation()
    {
        return view('visitors.confirmation')
            ->with('page', 'Visitor Confirmation')
            ->with('title', 'Telephone List')
            ->with('title_jp', '');
    }

    public function confirmationPhoneList(Request $request)
    {
        $phone_list = db::select("SELECT
		*
		FROM
		telephone_lists");

        $response = array(
            'status' => true,
            'phone_list' => $phone_list,
        );
        return Response::json($response);
    }

    public function confirmation2()
    {
        return view('visitors.confirmationSatpam')->with('page', 'Visitor Confirmation');
    }

    public function updateremark(Request $request)
    {

        try {
            $id = $request->get('id');
            $tag = $request->get('idtag');
            $intime = date('H:i:s');
            $visitordetail = VisitorDetail::where('id_visitor', '=', $id)
                ->where('tag', '=', $tag)
                ->withTrashed()
                ->first();

            $visitordetail->remark = 'Confirmed';
            $visitordetail->save();

            $visitor = Visitor::where('id', '=', $id)
                ->first();
            $visitor->remark = 'Confirmed';
            $visitor->save();

            $response = array(
                'status' => true,
                'message' => 'Confirm Visitors Success',
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

    public function updateremarkall(Request $request)
    {

        try {
            $id = $request->get('id');
            if ($request->get('remark') != null) {
                $remark = $request->get('remark');
                $intime = date('H:i:s');

                $namamanager = [];

                for ($i = 0; $i < count($id); $i++) {

                    $visitordetail = VisitorDetail::where('id_visitor', '=', $id[$i])
                        ->withTrashed()
                        ->update(['remark' => $remark]);

                    $visitor = Visitor::where('id', '=', $id[$i])
                        ->first();
                    $visitor->remark = $remark;
                    $visitor->save();
                    $datavisitor = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->join('visitor_details', 'visitor_details.id_visitor', '=', 'visitors.id')->where('visitors.id', $id[$i])->first();

                    $name = $datavisitor->name;
                    $department = $datavisitor->department;
                    $company = $datavisitor->company;
                    $namavis = $datavisitor->full_name;
                    $send_email_manager = $datavisitor->send_email_manager;

                    // if ($send_email_manager == null) {
                    $visitordetail = VisitorDetail::where('id_visitor', '=', $id[$i])
                        ->withTrashed()
                        ->update(['send_email_manager' => 'Sent']);

                    $visitor = Visitor::where('id', '=', $id[$i])
                        ->first();
                    $visitor->send_email_manager = 'Sent';
                    $visitor->save();

                    if ($department == null && $name == 'Budhi Apriyanto') {
                        $department = 'Management Information System Department';
                        $mail_to = DB::SELECT("SELECT
						email
						FROM
						visitor_confirmers
						WHERE
						department = '" . $department . "'
						AND remark = 'Manager'
						AND email IS NOT NULL
						LIMIT 1");
                    } elseif ($department == null && $name == 'Arief Soekamto') {
                        $department = 'Human Resources Department';
                        $mail_to = DB::SELECT("SELECT
						email
						FROM
						visitor_confirmers
						WHERE
						department = '" . $department . "'
						AND remark = 'Manager'
						AND email IS NOT NULL
						LIMIT 1");
                    } else {
                        $mail_to = DB::SELECT("SELECT
						email
						FROM
						visitor_confirmers
						WHERE
						department = '" . $department . "'
						AND remark = 'Manager'
						AND email IS NOT NULL
						LIMIT 1");
                    }

                    $namamanager[] = ['employees' => $name,
                        'department' => $department,
                        'company' => $company,
                        'nama' => $namavis,
                        'remark' => $remark,
                        'confirmed_at' => date('Y-m-d H:i:s'),
                        'id' => $id[$i],
                    ];
                }

                // ];

                $contactList = [];
                $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
                $contactList[1] = 'rio.irvansyah@music.yamaha.com';
                $contactList[2] = 'muhammad.ikhlas@music.yamaha.com';
                Mail::to($mail_to)->bcc($contactList, 'Contact List')->send(new SendEmail($namamanager, 'visitor_to_manager'));

            } else {
                $intime = date('H:i:s');
                $visitordetail = VisitorDetail::where('id_visitor', '=', $id)
                    ->withTrashed()
                    ->update(['remark' => 'Confirmed']);

                $visitor = Visitor::where('id', '=', $id)
                    ->first();
                $visitor->remark = 'Confirmed';
                $visitor->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Confirm Visitors Success',
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

    public function confirm_manager($id)
    {

        try {
            $intime = date('H:i:s');
            $visitordetail = VisitorDetail::where('id_visitor', '=', $id)
                ->withTrashed()
                ->update(['remark' => 'Sudah Ditemui']);

            $visitor = Visitor::where('id', '=', $id)
                ->first();
            $visitor->remark = 'Sudah Ditemui';
            $visitor->save();

            $datavisitor = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->join('visitor_details', 'visitor_details.id_visitor', '=', 'visitors.id')->where('visitors.id', $id)->first();

            $name = $datavisitor->name;
            $department = $datavisitor->department;
            $company = $datavisitor->company;
            $namavis = $datavisitor->full_name;
            $send_email_manager = $datavisitor->send_email_manager;

            $message = $name . ' (' . $department . ') telah terkonfirmasi menemui ' . $company;

            if ($send_email_manager == null) {
                $visitordetail = VisitorDetail::where('id_visitor', '=', $id)
                    ->withTrashed()
                    ->update(['send_email_manager' => 'Sent']);

                $visitor = Visitor::where('id', '=', $id)
                    ->first();
                $visitor->send_email_manager = 'Sent';
                $visitor->save();

                if ($department == null && $name == 'Budhi Apriyanto') {
                    $department = 'Management Information System Department';
                    $mail_to = DB::SELECT("SELECT
					email
					FROM
					visitor_confirmers
					WHERE
					department = '" . $department . "'
					AND remark = 'Manager'
					AND email IS NOT NULL
					LIMIT 1");
                } elseif ($department == null && $name == 'Arief Soekamto') {
                    $department = 'Human Resources Department';
                    $mail_to = DB::SELECT("SELECT
					email
					FROM
					visitor_confirmers
					WHERE
					department = '" . $department . "'
					AND remark = 'Manager'
					AND email IS NOT NULL
					LIMIT 1");
                } else {
                    $mail_to = DB::SELECT("SELECT
					email
					FROM
					visitor_confirmers
					WHERE
					department = '" . $department . "'
					AND remark = 'Manager'
					AND email IS NOT NULL
					LIMIT 1");
                }

                $namamanager[] = ['employees' => $name,
                    'department' => $department,
                    'company' => $company,
                    'nama' => $namavis,
                    'confirmed_at' => date('Y-m-d H:i:s'),
                    'id' => $id,
                ];

                $contactList = [];
                $contactList[0] = 'mokhamad.khamdan.khabibi@music.yamaha.com';
                $contactList[1] = 'rio.irvansyah@music.yamaha.com';
                $contactList[2] = 'muhammad.ikhlas@music.yamaha.com';
                Mail::to($mail_to)->bcc($contactList, 'Contact List')->send(new SendEmail($namamanager, 'visitor_to_manager'));
            }
            return view('visitors.visitor_confirm_manager', array(
                'head' => $id,
                'message' => $message,
            ))->with('page', 'Visitor Confirmation');
        } catch (\Exception$e) {
            $message = $name . ' (' . $department . ') telah terkonfirmasi menemui ' . $company;
            return view('visitors.visitor_confirm_manager', array(
                'head' => $id,
                'message' => $message,
            ))->with('page', 'Visitor Confirmation');
        }

    }

    public function confirm_to_manager($id)
    {

        try {
            $intime = date('H:i:s');
            $visitordetail = VisitorDetail::where('id_visitor', '=', $id)
                ->withTrashed()
                ->update(['remark_manager' => 'Sudah Dikonfirmasi']);

            $visitor = Visitor::where('id', '=', $id)
                ->first();
            $visitor->remark_manager = 'Sudah Dikonfirmasi';
            $visitor->save();

            $datavisitor = Visitor::join('employee_syncs', 'employee_syncs.employee_id', '=', 'visitors.employee')->join('visitor_details', 'visitor_details.id_visitor', '=', 'visitors.id')->where('visitors.id', $id)->first();

            $name = $datavisitor->name;
            $department = $datavisitor->department;
            $company = $datavisitor->company;
            $namavis = $datavisitor->full_name;
            $send_email_manager = $datavisitor->send_email_manager;

            $message = 'Anda telah mengkonfirmasi bahwa ' . $name . ' (' . $department . ') telah menemui ' . $company;

            return view('visitors.visitor_confirm_manager', array(
                'head' => $id,
                'message' => $message,
            ))->with('page', 'Visitor Confirmation');
        } catch (\Exception$e) {
            $message = 'Anda telah mengkonfirmasi bahwa ' . $name . ' (' . $department . ') telah menemui ' . $company;
            return view('visitors.visitor_confirm_manager', array(
                'head' => $id,
                'message' => $message,
            ))->with('page', 'Visitor Confirmation');
        }

    }

    public function telpon()
    {

        $telpon = "select person, dept, nomor from telephone_lists";
        $telpons = DB::select($telpon);
        return DataTables::of($telpons)
            ->make(true);
    }

    public function getNotifVisitor()
    {
        if (Auth::user() !== null) {
            $manager = Auth::user()->username;
            $manager_name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $confirmers = DB::SELECT("select * from visitor_confirmers where employee_id = '" . $manager . "'");
            // $emp_sync = DB::SELECT("SELECT * FROM `employee_syncs` where employee_id = '".$manager."'");

            if ($role != 'MIS') {
                if (count($confirmers) > 0) {
                    foreach ($confirmers as $key) {
                        $name = $key->name;
                    }

                    $confirmer = '';
                    for ($i = 0; $i < count($confirmers); $i++) {
                        $confirmer = $confirmer . "'" . $confirmers[$i]->department . "'";
                        if ($i != (count($confirmers) - 1)) {
                            $confirmer = $confirmer . ',';
                        }
                    }
                    $confirmerin = " AND employee_syncs.department in (" . $confirmer . ") ";
                    if (preg_match('/Management Information System Department/i', $confirmerin)) {
                        $lists = DB::SELECT("SELECT
						visitors.id,
						name,
						department,
						company,
						DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
						visitors.created_at,
						visitor_details.full_name,
						visitors.jumlah AS total1,
						purpose,
						visitors.status,
						visitor_details.in_time,
						visitor_details.out_time,
						visitors.remark
						FROM
						visitors
						LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
						LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
						WHERE
						(visitors.remark IS NULL
							" . $confirmerin . ")
							OR
							(visitors.remark IS NULL
							and employee_syncs.employee_id = '" . $manager . "')
							OR
							(visitors.remark IS NULL
							and employee_syncs.employee_id = 'PI0109004')
							ORDER BY
							id DESC");
                    } else if (preg_match('/Human Resources Department/i', $confirmerin)) {
                        $lists = DB::SELECT("SELECT
							visitors.id,
							name,
							department,
							company,
							DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
							visitors.created_at,
							visitor_details.full_name,
							visitors.jumlah AS total1,
							purpose,
							visitors.status,
							visitor_details.in_time,
							visitor_details.out_time,
							visitors.remark
							FROM
							visitors
							LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
							LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
							WHERE
							(visitors.remark IS NULL
								" . $confirmerin . ")
								OR
								(visitors.remark IS NULL
								and employee_syncs.employee_id = '" . $manager . "')
								OR
								(visitors.remark IS NULL
								and employee_syncs.employee_id = 'PI9709001')
								ORDER BY
								id DESC");
                    } else {
                        $lists = DB::SELECT("SELECT
								visitors.id,
								name,
								department,
								company,
								DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
								visitors.created_at,
								visitor_details.full_name,
								visitors.jumlah AS total1,
								purpose,
								visitors.status,
								visitor_details.in_time,
								visitor_details.out_time,
								visitors.remark
								FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
								WHERE
								(visitors.remark IS NULL
									" . $confirmerin . ")
									OR
									(visitors.remark IS NULL
									and employee_syncs.employee_id = '" . $manager . "')
									ORDER BY
									id DESC");
                    }
                } else {
                    $lists = DB::SELECT("SELECT
								visitors.id,
								name,
								department,
								company,
								DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
								visitors.created_at,
								visitor_details.full_name,
								visitors.jumlah AS total1,
								purpose,
								visitors.status,
								visitor_details.in_time,
								visitor_details.out_time,
								visitors.remark
								FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
								WHERE
								visitors.remark IS NULL
								ORDER BY
								id DESC");
                }
            } else {
                $lists = DB::SELECT("SELECT
							visitors.id,
							name,
							department,
							company,
							DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
							visitors.created_at,
							visitor_details.full_name,
							visitors.jumlah AS total1,
							purpose,
							visitors.status,
							visitor_details.in_time,
							visitor_details.out_time,
							visitors.remark
							FROM
							visitors
							LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
							LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
							WHERE
							visitors.remark IS NULL
							ORDER BY
							id DESC");
            }

            $notif = 0;

            if (count($lists) > 0) {
                $notif = count($lists);
            }
            return $notif;
        }
    }

    public function confirmation_manager()
    {
        $manager = Auth::user()->username;
        $role = Auth::user()->role_code;
        $confirmers = DB::SELECT("select * from visitor_confirmers where employee_id = '" . $manager . "'");

        if ($role == 'MIS') {
            return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');
        } else if (count($confirmers) > 0) {

            return view('visitors.confirmation_manager')->with('page', 'Visitor Confirmation By Manager');

        } else {
            return redirect('home');
        }
    }

    public function fetchVisitorByManager()
    {
        try {
            $manager = Auth::user()->username;
            $manager_name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $confirmers = DB::SELECT("select * from visitor_confirmers where employee_id = '" . $manager . "'");

            if ($role != 'MIS') {
                if (count($confirmers) > 0) {
                    foreach ($confirmers as $key) {
                        $name = $key->name;
                    }

                    $confirmer = '';
                    for ($i = 0; $i < count($confirmers); $i++) {
                        $confirmer = $confirmer . "'" . $confirmers[$i]->department . "'";
                        if ($i != (count($confirmers) - 1)) {
                            $confirmer = $confirmer . ',';
                        }
                    }
                    $confirmerin = " AND employee_syncs.department in (" . $confirmer . ") ";
                    if (preg_match('/Management Information System Department/i', $confirmerin)) {
                        $lists = DB::SELECT("SELECT
									visitors.id,
									name,
									department,
									company,
									DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
									visitors.created_at,
									visitor_details.full_name,
									visitors.jumlah AS total1,
									purpose,
									visitors.status,
									visitor_details.in_time,
									visitor_details.out_time,
									visitors.remark
									FROM
									visitors
									LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
									LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
									WHERE
									(visitors.remark IS NULL
										" . $confirmerin . ")
										OR
										(visitors.remark IS NULL
										and employee_syncs.employee_id = '" . $manager . "')
										OR
										(visitors.remark IS NULL
										and employee_syncs.employee_id = 'PI0109004')
										ORDER BY
										id DESC");
                    } else if (preg_match('/Human Resources Department/i', $confirmerin)) {
                        $lists = DB::SELECT("SELECT
										visitors.id,
										name,
										department,
										company,
										DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
										visitors.created_at,
										visitor_details.full_name,
										visitors.jumlah AS total1,
										purpose,
										visitors.status,
										visitor_details.in_time,
										visitor_details.out_time,
										visitors.remark
										FROM
										visitors
										LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
										LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
										WHERE
										(visitors.remark IS NULL
											" . $confirmerin . ")
											OR
											(visitors.remark IS NULL
											and employee_syncs.employee_id = '" . $manager . "')
											OR
											(visitors.remark IS NULL
											and employee_syncs.employee_id = 'PI9709001')
											ORDER BY
											id DESC");
                    } else {
                        $lists = DB::SELECT("SELECT
											visitors.id,
											name,
											department,
											company,
											DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
											visitors.created_at,
											visitor_details.full_name,
											visitors.jumlah AS total1,
											purpose,
											visitors.status,
											visitor_details.in_time,
											visitor_details.out_time,
											visitors.remark
											FROM
											visitors
											LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
											LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
											WHERE
											(visitors.remark IS NULL
												" . $confirmerin . ")
												OR
												(visitors.remark IS NULL
												and employee_syncs.employee_id = '" . $manager . "')
												ORDER BY
												id DESC");
                    }

                    $confirmers_all = DB::SELECT("select * from visitor_confirmers");

                    $response = array(
                        'status' => true,
                        'lists' => $lists,
                        'manager_name' => $manager_name,
                        'confirmers_all' => $confirmers_all,
                        'name' => $name,
                    );
                    return Response::json($response);
                } else {
                    $lists = DB::SELECT("SELECT
											visitors.id,
											name,
											department,
											company,
											DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
											visitors.created_at,
											visitor_details.full_name,
											visitors.jumlah AS total1,
											purpose,
											visitors.status,
											visitor_details.in_time,
											visitor_details.out_time,
											visitors.remark
											FROM
											visitors
											LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
											LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
											WHERE
											visitors.remark IS NULL
											ORDER BY
											id DESC");

                    $confirmers_all = DB::SELECT("select * from visitor_confirmers");

                    $response = array(
                        'status' => true,
                        'lists' => $lists,
                        'manager_name' => $manager_name,
                        'confirmers_all' => $confirmers_all,
                        'name' => $manager_name,
                    );
                    return Response::json($response);
                }
            } else {
                $lists = DB::SELECT("SELECT
										visitors.id,
										name,
										department,
										company,
										DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
										visitors.created_at,
										visitor_details.full_name,
										visitors.jumlah AS total1,
										purpose,
										visitors.status,
										visitor_details.in_time,
										visitor_details.out_time,
										visitors.remark
										FROM
										visitors
										LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
										LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
										WHERE
										visitors.remark IS NULL
										ORDER BY
										id DESC");

                $confirmers_all = DB::SELECT("select * from visitor_confirmers");

                $response = array(
                    'status' => true,
                    'lists' => $lists,
                    'manager_name' => $manager_name,
                    'confirmers_all' => $confirmers_all,
                    'name' => $manager_name,
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

///----------------------- KELUAR

    public function leave()
    {
        return view('visitors.leave')->with('page', 'Visitor Leave');
    }

    public function getvisit(Request $request)
    {
        $id = $request->get('id');
        $op = "SELECT DISTINCT( visitor_details.tag),visitors.company,visitors.remark, visitor_details.id_number,visitor_details.full_name, visitor_details.in_time, employees.name, mutation_logs.department from visitors
							left join visitor_details on visitors.id = visitor_details.id_visitor
							left join employees on visitors.employee = employees.employee_id
							LEFT JOIN mutation_logs on employees.employee_id = mutation_logs.employee_id

							where visitor_details.tag='" . $id . "' and visitor_details.out_time ='' limit 1";

        $ops = DB::select($op);

        $response = array(
            'status' => true,
            'ops' => $ops,
            'message' => 'Confirm Visitors Leave Success',
        );
        return Response::json($response);
    }

    public function out(Request $request)
    {

        try {
            // $id = $request->get('id');
            $tag = $request->get('idtag');
            $reason = $request->get('reason');
            $time = date('Y-m-d H:i:s');

            $visitordetail = VisitorDetail::where('tag', '=', $tag)
                ->where('out_time', '=', '')
                ->first();
            $visitorhead = Visitor::where('id', '=', $visitordetail->id_visitor)
                ->first();

            if ($reason == "") {
                $visitordetail->out_time = $time;
                $visitordetail->save();
            } else {
                $visitordetail->out_time = $time;
                $visitordetail->save();
                $visitorhead->reason = $reason;
                $visitorhead->save();
            }

            $response = array(
                'status' => true,
                'message' => 'Confirm Visitors Leave Success',
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

//-------------------display

    public function display()
    {
        return view('visitors.list')->with('page', 'Visitor display');
    }

    public function filldisplay($nik, Request $request)
    {

        $kurang = date('Y-m-d', strtotime('-14 days'));
        $tgl = date('m-Y');
        $tgl2 = date('Y-m-d');

        if ($request->get('date') > 0) {
            $date = $request->get('date');
        } else {
            $date = date('Y-m-d');

        }

        if ($nik != "") {
            $where = "where employee = '" . $nik . "'";
        }

        if ($nik == "asd") {
            $where = "WHERE DATE_FORMAT(created_at,'%m-%Y') = '" . $tgl . "' ";
        }

        if ($nik == "display") {

            $where = " WHERE DATE_FORMAT(created_at,'%Y-%m-%d') ='" . $date . "' ";
        }

        $op = "SELECT
							*,
							count(
								DISTINCT ( total1 )) AS total
							FROM
							(
								SELECT
								visitors.reason,
								visitors.employee,
								visitors.id,
								visitors.company,
								visitor_details.full_name,
								visitor_details.id_number AS total1,
								purpose,
								visitors.status,
								employee_syncs.name,
								employee_syncs.department,
								visitor_details.in_time,
								visitor_details.out_time,
								visitors.remark,
								visitors.created_at,
								DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) AS tgl,
								CONCAT(body_temperatures.suhu,' °C') as suhu
								FROM
								visitors
								LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
								LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
								LEFT JOIN body_temperatures ON body_temperatures.created_at = visitors.created_at
								) a
							" . $where . "
							GROUP BY
							a.id
							ORDER BY
							a.id DESC";

        $ops = DB::select($op);

        return DataTables::of($ops)->make(true);
    }

    public function getchart(Request $request)
    {
        $kurang = date('Y-m-d', strtotime('-30 days'));
        $bln = date('m-Y');
        $tgl = date('Y-m-d');
        $id = $request->get('id');
        $oplama = "select tglok, COALESCE(d.vendor,0) as vendor, COALESCE(d.visitor,0) as visitor  from (
							SELECT  b.tgl, sum(b.vendor) as vendor, sum(b.visitor) as visitor  from (
							select final.status, final.tgl, sum(final.total_vendor) as vendor, sum(final.total_visitor) as visitor from
							(
							SELECT Status, tgl ,count(total1) as total_vendor, 0 as total_visitor from (
							select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
							left join visitor_details on visitors.id = visitor_details.id_visitor
							) a WHERE DATE_FORMAT(a.tgl,'%m-%Y') = '04-2019' and a.status = 'Vendor' GROUP BY a.tgl,a.Status

							union

							SELECT Status, tgl, 0 as total_vendor, count(total1) as total_visitor from (
							select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
							left join visitor_details on visitors.id = visitor_details.id_visitor
							) a WHERE DATE_FORMAT(a.tgl,'%m-%Y') = '" . $bln . "' and a.status = 'visitor' GROUP BY a.tgl,a.Status
							) as final
							group by final.status, final.tgl
							) b GROUP BY b.tgl
							) d

							RIGHT JOIN (
							select week_date as tglok, 0 as vendor, 0 as visitor from weekly_calendars
							WHERE DATE_FORMAT(week_date,'%d-%m-%Y')<='" . $tgl . "' and DATE_FORMAT(week_date,'%m-%Y')='" . $bln . "'

							) c on d.tgl = c.tglok
							ORDER BY tglok asc

							";

        $op = "select DATE_FORMAT(tglok,'%d %b %y') as tglok, COALESCE(d.vendor,0) as vendor, COALESCE(d.visitor,0) as visitor  from (
							SELECT  b.tgl, sum(b.vendor) as vendor, sum(b.visitor) as visitor  from (
							select final.status, final.tgl, sum(final.total_vendor) as vendor, sum(final.total_visitor) as visitor from
							(
							SELECT Status, tgl ,count(total1) as total_vendor, 0 as total_visitor from (
							select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
							left join visitor_details on visitors.id = visitor_details.id_visitor
							) a WHERE a.status = 'Vendor' GROUP BY a.tgl,a.Status

							union

							SELECT Status, tgl, 0 as total_vendor, count(total1) as total_visitor from (
							select visitor_details.id_number as total1, visitors.status, DATE_FORMAT(visitors.created_at,'%Y-%m-%d')as tgl from visitors
							left join visitor_details on visitors.id = visitor_details.id_visitor
							) a WHERE a.status = 'visitor' GROUP BY a.tgl,a.Status
							) as final
							group by final.status, final.tgl
							) b GROUP BY b.tgl
							) d

							RIGHT JOIN (
							select week_date as tglok, 0 as vendor, 0 as visitor from weekly_calendars
							WHERE week_date >='" . $kurang . "' and week_date<='" . $tgl . "'

							) c on d.tgl = c.tglok
							ORDER BY   DATE_FORMAT(tglok,'%Y-%m-%d') asc";

        $ops = DB::select($op);

        $response = array(
            'status' => true,
            'ops' => $ops,
            'message' => $kurang,
        );
        return Response::json($response);
    }

    public function indexEmpConfirmation()
    {
        return view('visitors.emp_confirmation')
            ->with('title', 'Employee Visitor Confirmation')
            ->with('title_jp', '従業員ビジター確認')
            ->with('page', 'Employee Visitor Confirmation');
    }

    public function fetchEmpConfirmation(Request $request)
    {
        try {
            $emp = Employee::
                join('employee_syncs', 'employees.employee_id', 'employee_syncs.employee_id')
                ->join('visitor_confirmers', 'employee_syncs.employee_id', 'visitor_confirmers.employee_id')
                ->where('tag', $request->get('tag'))
                ->first();

            if (count($emp) > 0) {
                if ($emp->department == null) {
                    $visitor = DB::SELECT("SELECT
											visitors.id,
											name,
											department,
											company,
											DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
											visitors.created_at,
											visitor_details.full_name,
											visitors.jumlah AS total1,
											purpose,
											visitors.status,
											visitor_details.in_time,
											visitor_details.out_time,
											visitors.remark
											FROM
											visitors
											LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
											LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
											WHERE
											(visitors.remark IS NULL
												AND employee_syncs.department is null )
											OR
											(visitors.remark IS NULL
												AND employee_syncs.employee_id = '" . $emp->employee_id . "')
												ORDER BY
												id DESC");
                } else if ($emp->department == 'Management Information System Department') {
                    $visitor = DB::SELECT("SELECT
												visitors.id,
												name,
												department,
												company,
												DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
												visitors.created_at,
												visitor_details.full_name,
												visitors.jumlah AS total1,
												purpose,
												visitors.status,
												visitor_details.in_time,
												visitor_details.out_time,
												visitors.remark
												FROM
												visitors
												LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
												LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
												WHERE
												(visitors.remark IS NULL
													AND employee_syncs.department = '" . $emp->department . "' )
													OR
													(visitors.remark IS NULL
													AND employee_syncs.employee_id = 'PI0109004')
													ORDER BY
													id DESC");
                } else if ($emp->department == 'Human Resources Department') {
                    $visitor = DB::SELECT("SELECT
													visitors.id,
													name,
													department,
													company,
													DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
													visitors.created_at,
													visitor_details.full_name,
													visitors.jumlah AS total1,
													purpose,
													visitors.status,
													visitor_details.in_time,
													visitor_details.out_time,
													visitors.remark
													FROM
													visitors
													LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
													LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
													WHERE
													(visitors.remark IS NULL
														AND employee_syncs.department = '" . $emp->department . "' )
														OR
														(visitors.remark IS NULL
														AND employee_syncs.employee_id = 'PI9709001')
														ORDER BY
														id DESC");
                } else {
                    $visitor = DB::SELECT("SELECT
														visitors.id,
														name,
														department,
														company,
														DATE_FORMAT( visitors.created_at, '%Y-%m-%d' ) created_at2,
														visitors.created_at,
														visitor_details.full_name,
														visitors.jumlah AS total1,
														purpose,
														visitors.status,
														visitor_details.in_time,
														visitor_details.out_time,
														visitors.remark
														FROM
														visitors
														LEFT JOIN visitor_details ON visitors.id = visitor_details.id_visitor
														LEFT JOIN employee_syncs ON visitors.employee = employee_syncs.employee_id
														WHERE
														(visitors.remark IS NULL
															AND employee_syncs.department = '" . $emp->department . "' )
															OR
															(visitors.remark IS NULL
															AND employee_syncs.employee_id = '" . $emp->employee_id . "')
															ORDER BY
															id DESC");
                }

                $response = array(
                    'status' => true,
                    'visitor' => $visitor,
                    'emp' => $emp,
                    'message' => 'Employee Found',
                );
                return Response::json($response);
            } else {
                $response = array(
                    'status' => false,
                    'message' => 'Anda Tidak Memiliki Otoritas',
                );
                return Response::json($response);
            }
        } catch (\Exception$e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function indexVisitorReport()
    {
        return view('visitors.report')
        ->with('title', 'Visitor Report')
        ->with('title_jp', '')
        ->with('page', 'Visitor Report')
        ->with('jpn', '');
    }

    public function fetchVisitorReport(Request $request)
    {
        try {
            $date_from = $request->get('date_from');
              $date_to = $request->get('date_to');
              if ($date_from == "") {
                if ($date_to == "") {
                  $first = date('Y-m-01');
                  $last = date('Y-m-d');
                }else{
                  $first = date('Y-m-01');
                  $last = $date_to;
                }
              }else{
               if ($date_to == "") {
                $first = $date_from;
                $last = date('Y-m-d');
              }else{
                $first = $date_from;
                $last = $date_to;
              }
            }
            $report = DB::select("SELECT
                * 
            FROM
                visitors
                LEFT JOIN visitor_details ON visitor_details.id_visitor = visitors.id 
            WHERE
                date( visitors.created_at ) >= '".$first."' 
                AND date( visitors.created_at ) <= '".$last."' 
                AND visitor_details.id IS NOT NULL");

            $emp = EmployeeSync::get();
            $response = array(
                'status' => true,
                'report' => $report,
                'emp' => $emp,
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
}
