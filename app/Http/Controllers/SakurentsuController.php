<?php

namespace App\Http\Controllers;

use App\Approver;
use App\CodeGenerator;
use App\Department;
use App\EmployeeSync;
use App\ExtraOrderMaterial;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Sakurentsu;
use App\SakurentsuInformation;
use App\SakurentsuThreeM;
use App\sakurentsuThreeMApproval;
use App\SakurentsuThreeMDistribution;
use App\SakurentsuThreeMDocument;
use App\SakurentsuThreeMImpApproval;
use App\SakurentsuThreeMImplementation;
use App\SakurentsuThreeMSpecial;
use App\SakurentsuTrialRequest;
use App\SakurentsuTrialStatus;
use App\SakurentsuThreeMSpTemp;
use App\StorageLocation;
use App\User;
use File;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Mails;
use Response;

class SakurentsuController extends Controller
{
    public function __construct()
    {
        $this->dgm = ['PI0109004', 'budhi.apriyanto@music.yamaha.com', 'PI9905001', 'mei.rahayu@music.yamaha.com'];
        $this->dgm2 = ['PI9905001', 'mei.rahayu@music.yamaha.com'];
        $this->gm = ['PI1206001', 'yukitaka.hayakawa@music.yamaha.com'];
    }

    public function index_sakurentsu()
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        if (strtoupper(Auth::user()->username) == 'PI2111045' || strtoupper(Auth::user()->username) == 'PI2002021' || strtoupper(Auth::user()->username) == 'PI0812002' || strtoupper(Auth::user()->username) == 'PI1506003') {
            return view(
                'sakurentsu.master.index_sakurentsu',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                )
            )->with('page', 'Sakurentsu List')
                ->with('head', 'Sakurentsu');
        } else {
            return redirect('/home');
        }
    }

    public function index_tiga_em()
    {
        $title = '3M List';
        $title_jp = '3Mリスト';

        return view(
            'sakurentsu.master.index_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', '3M List')
            ->with('head', 'Sakurentsu');
    }

    public function index_form_tiga_em($sk_number)
    {
        $title = '3M Form';
        $title_jp = '3Mフォーム';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $judul = Sakurentsu::where('sakurentsu_number', '=', $sk_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'), 'remark')->first();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        $ava = SakurentsuThreeM::where('sakurentsu_number', '=', $sk_number)->whereNotNull('reason')->select('sakurentsu_number')->first();

        $pics = db::select("SELECT employee_id, users.`name` FROM
            `users`
            JOIN employee_syncs ON employee_syncs.employee_id = users.username
            WHERE
            email LIKE '%@music.yamaha%'
            AND (
                position LIKE '%staff%'
                OR position LIKE '%foreman%'
                OR position LIKE '%chief%'
                OR position LIKE '%coordinator%'
                OR position LIKE '%manager%')
            AND end_date is null
            order by hire_date asc");

        // if (count($ava) == 0) {
        return view(
            'sakurentsu.master.index_3m_form',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'judul' => $judul,
                'departemen' => $departments,
                'pics' => $pics,
            )
        )->with('page', '3M Form')
            ->with('head', 'Sakurentsu');
        // } else {
        //     return view('sakurentsu.master.index_3m', array(
        //         'title' => $title,
        //         'title_jp' => $title_jp,
        //     ))->with('page', '3M List')
        //     ->with('head', 'Sakurentsu');
        // }
    }

    public function index_form_tiga_em_trial($trial_id)
    {
        $title = '3M Form';
        $title_jp = '3Mフォーム';

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        return view(
            'sakurentsu.master.index_3m_form',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'id_trial' => $trial_id,
                'judul' => '',
                'departemen' => $departments,
            )
        )->with('page', '3M Form')
            ->with('head', 'Sakurentsu');
    }

    public function index_form_tiga_em_new()
    {
        $title = '3M Form';
        $title_jp = '3Mフォーム';

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $pics = db::select("SELECT employee_id, users.`name` FROM
            `users`
            JOIN employee_syncs ON employee_syncs.employee_id = users.username
            WHERE
            email LIKE '%@music.yamaha%'
            AND (
                position LIKE '%staff%'
                OR position LIKE '%foreman%'
                OR position LIKE '%chief%'
                OR position LIKE '%coordinator%'
                OR position LIKE '%manager%')
            AND end_date is null
            order by hire_date asc");

        return view(
            'sakurentsu.master.index_3m_form',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'judul' => '',
                'departemen' => $departments,
                'pics' => $pics,
            )
        )->with('page', '3M Form')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_premeeting($id_three_m)
    {
        $title = '3M Meeting';
        $title_jp = '3M ミーティング';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->select('id', 'form_number', 'form_identity_number', 'sakurentsu_number', 'title', 'title_jp', 'product_name', 'proccess_name', 'unit', 'category', 'reason', 'benefit', 'check_before', 'started_date', 'date_note', 'bom_change', 'special_items', 'related_department', 'remark', 'att')->first();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $dept = Department::select('department_name', 'department_shortname')->get();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        $pics = db::select("SELECT employee_id, users.`name` FROM
            `users`
            JOIN employee_syncs ON employee_syncs.employee_id = users.username
            WHERE
            email LIKE '%@music.yamaha%'
            AND (
                position LIKE '%staff%'
                OR position LIKE '%foreman%'
                OR position LIKE '%chief%'
                OR position LIKE '%coordinator%'
                OR position LIKE '%manager%')
            AND end_date is null
            order by hire_date asc");

        $item_khusus = SakurentsuThreeMSpecial::where('form_number', '=', $data->form_identity_number)->orderBy('id', 'asc')->get();

        return view(
            'sakurentsu.master.index_3m_premeeting',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'judul' => $judul,
                'data' => $data,
                'departemen' => $departments,
                'dept_name' => $dept,
                'pics' => $pics,
                'item_khusus' => $item_khusus
            )
        )->with('page', '3M List')
            ->with('head', 'Sakurentsu');
    }

    public function index_translate_sakurentsu()
    {
        $title = 'Translate List';
        $title_jp = '翻訳リスト';

        return view(
            'sakurentsu.master.index_translate_sakurentsu',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Sakurentsu Translate List')
            ->with('head', 'Sakurentsu');
    }

    public function index_translate_tiga_em($id)
    {
        $title = 'Translate 3M';
        $title_jp = '3M翻訳';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data_tiga_em->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();

        return view(
            'sakurentsu.master.index_3m_translate',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'judul' => $judul,
                'departemen' => $departments,
            )
        )->with('page', '3M Translate')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_upload($id)
    {
        $title = '3M Upload Document';
        $title_jp = '3M書類アップロード';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();
        $emp_dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();
        $doc_tiga_em = SakurentsuThreeMDocument::leftJoin('employee_syncs', 'pic', '=', 'employee_syncs.employee_id')
            ->where('form_id', '=', $id);

        if (!str_contains(Auth::user()->role_code, 'MIS')) {
            $doc_tiga_em = $doc_tiga_em->where('department', '=', $emp_dept->department);
        }

        $doc_tiga_em = $doc_tiga_em->distinct()
            ->get(['document_name', 'document_description', 'target_date', 'pic', 'name']);

        return view(
            'sakurentsu.report.upload_3m_document',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'doc_tiga_m' => $doc_tiga_em,
            )
        )->with('page', '3M Upload Document')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_finalmeeting($id)
    {
        $title = '3M Final Meeting';
        $title_jp = '3Mファイナルミーティング';

        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();
        $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->get();

        $judul = Sakurentsu::where('sakurentsu_number', '=', $data_tiga_em->sakurentsu_number)->select('sakurentsu_number', 'title', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'))->first();

        $departments = EmployeeSync::whereNull('end_date')->select('department')->groupBy('department')->get();
        $dept = Department::select('department_name', 'department_shortname')->get();

        return view(
            'sakurentsu.master.index_3m_finalmeeting',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'doc_tiga_m' => $doc_tiga_em,
                'judul' => $judul,
                'departemen' => $departments,
                'dept_name' => $dept,
            )
        )->with('page', '3M FinalMeeting')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_detail($id_three_m)
    {
        $title = '3M Detail';
        $title_jp = '3M詳細内容';

        // $sign = [];

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->first();
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $id_three_m)
            ->leftJoin('departments', 'departments.department_name', '=', 'sakurentsu_three_m_documents.pic')
            ->select('form_id', 'document_name', 'document_description', 'pic', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'), 'departments.department_shortname')->get();
        $relate_dept = SakurentsuThreeM::where('id', '=', $id_three_m)->select('related_department')->first();

        $dept = explode(',', $relate_dept->related_department);

        $sign_master = EmployeeSync::whereIn('position', ['Deputy General Manager', 'General Manager'])->whereNull('end_date')->select('department', 'employee_id', 'name', 'position', db::raw('0 as remark'))->orderBy('position', 'desc')->get();

        $sign_user = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)->where('status', '=', 'approve')->whereNotNull('approver_department')->select('approver_id', 'approver_name', 'approver_department', 'position', 'approve_at')->orderBy('position', 'desc')->get();

        // $sign_user = EmployeeSync::whereIn('department', $dept)->whereIn('position', ['Foreman','Manager','Chief'])->select('department', 'employee_id', 'name', 'position', db::raw('IF(position = "Manager", 1, IF(position = "Foreman", 2, 3)) as remark'))->orderBy('remark')->get();

        // $signed = sakurentsuThreeMApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_approvals.approver_id')->select('employee_syncs.division','employee_syncs.department', 'sakurentsu_three_m_approvals.approver_id', 'sakurentsu_three_m_approvals.approver_name', 'employee_syncs.position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_date'), 'status')->where('form_id', '=', $id_three_m)->get();

        $signed = SakurentsuThreeMApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_approvals.approver_id')
            ->where('form_id', '=', $id_three_m)
            ->select('approver_id', 'approver_name', 'approver_department', db::raw('approver_division as division'), 'sakurentsu_three_m_approvals.position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_date'), 'status')->get();

        $sign = array_merge($sign_master->toArray(), $sign_user->toArray());

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id_three_m)->first();

        $proposer = db::select('SELECT `name`, department from employee_syncs where position = "manager" AND department = (SELECT department from employee_syncs where employee_id = "' . $data_tiga_em->created_by . '")');

        $distribution = SakurentsuThreeMDistribution::where('form_id', '=', $id_three_m)->where('distribute_status', '=', 'Checked')->select('distribution_to', 'distribute_status')->get();

        $implement = SakurentsuThreeMImplementation::where('form_id', '=', $id_three_m)->first();

        $sign_std = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)
            ->where('position', '=', 'President Director')
            ->select(db::raw('DATE_FORMAT(approve_at, "%d %b %Y") as dt'))
            ->first();

        $file_sakurentsu = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsu_three_ms.sakurentsu_number', 'sakurentsus.sakurentsu_number')
            ->where('sakurentsu_three_ms.id', $id_three_m)
            ->select('file', 'file_translate')
            ->first();

        $item_khusus = SakurentsuThreeMSpecial::where('sakurentsu_three_m_specials.form_number', $data_tiga_em->form_identity_number)
            ->select('item_khusus', 'target_change', 'actual_change', 'pic', 'eviden_description', 'eviden_att')
            ->get();

        return view(
            'sakurentsu.report.detail_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
                'docs' => $docs,
                'sign_user' => $sign,
                'signed_user' => $signed,
                'sign_gm' => $sign_master,
                'implement' => $implement,
                'distribusi' => $distribution,
                'sign_std' => $sign_std,
                'sk_file' => $file_sakurentsu,
                'sp_items' => $item_khusus
            )
        )->with('page', 'Report Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_detail2($id_three_m, $position)
    {
        $title = '3M Detail';
        $title_jp = '3M詳細内容';

        // $sign = [];

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->first();
        $docs = SakurentsuThreeMDocument::leftJoin('employee_syncs', 'sakurentsu_three_m_documents.pic', '=', 'employee_syncs.employee_id')
            ->where('form_id', '=', $id_three_m)->select('form_id', 'document_name', 'document_description', 'pic', 'employee_syncs.name', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'))->get();
        $relate_dept = SakurentsuThreeM::where('id', '=', $id_three_m)->select('related_department')->first();

        $dept = explode(',', $relate_dept->related_department);

        $sign_master = EmployeeSync::whereIn('position', ['Deputy General Manager', 'General Manager'])->whereNull('end_date')->select(db::raw('employee_id as approver_id'), db::raw('name as approver_name'), db::raw('department as approver_department'), 'position', db::raw('0 as remark'))->orderBy('position', 'desc')->get();

        $sign_user = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)->where('status', '=', 'approve')->whereNotNull('approver_department')->select('approver_id', 'approver_name', 'approver_department', 'position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_at'))->orderBy('position', 'desc')->get();

        // $sign_user = EmployeeSync::whereIn('department', $dept)->whereIn('position', ['Foreman','Manager','Chief'])->select('department', 'employee_id', 'name', 'position', db::raw('IF(position = "Manager", 1, IF(position = "Foreman", 2, 3)) as remark'))->orderBy('remark')->get();

        // $signed = sakurentsuThreeMApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_approvals.approver_id')->select('employee_syncs.division','employee_syncs.department', 'sakurentsu_three_m_approvals.approver_id', 'sakurentsu_three_m_approvals.approver_name', 'employee_syncs.position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_date'), 'status')->where('form_id', '=', $id_three_m)->get();

        $distribution = SakurentsuThreeMDistribution::where('form_id', '=', $id_three_m)->where('distribute_status', '=', 'Checked')->select('distribution_to', 'distribute_status')->get();

        $signed = SakurentsuThreeMApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_approvals.approver_id')
            ->where('form_id', '=', $id_three_m)
            ->select('approver_id', 'approver_name', 'approver_department', db::raw('approver_division as division'), 'sakurentsu_three_m_approvals.position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_date'), 'status')->get();

        $sign = array_merge($sign_master->toArray(), $sign_user->toArray());

        $implement = SakurentsuThreeMImplementation::where('form_id', '=', $id_three_m)->first();
        $sign_imp = SakurentsuThreeMImpApproval::where('form_id', '=', $id_three_m)->get();

        $sign_std = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)
            ->where('position', '=', 'President Director')
            ->select(db::raw('DATE_FORMAT(approve_at, "%d %b %Y") as dt'))
            ->first();

        $file_sakurentsu = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsu_three_ms.sakurentsu_number', 'sakurentsus.sakurentsu_number')
            ->where('sakurentsu_three_ms.id', $id_three_m)
            ->select('file', 'file_translate')
            ->first();

        $item_khusus = SakurentsuThreeMSpecial::leftjoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_specials.pic')
            ->where('sakurentsu_three_m_specials.form_number', $data->form_identity_number)
            ->select('item_khusus', 'target_change', 'actual_change', 'pic', 'eviden_description', 'eviden_att', 'name')
            ->get();

        return view(
            'sakurentsu.report.detail_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
                'docs' => $docs,
                'sign_user' => $sign,
                'signed_user' => $signed,
                'sign_gm' => $sign_master,
                'implement' => $implement,
                'sign_imp' => $sign_imp,
                'distribusi' => $distribution,
                'sign_std' => $sign_std,
                'sk_file' => $file_sakurentsu,
                'sp_items' => $item_khusus
            )
        )->with('page', 'Report Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_pdf($id_three_m)
    {
        $title = '3M Detail';
        $title_jp = '3M詳細内容';

        $data = SakurentsuThreeM::where('id', '=', $id_three_m)->first();
        $docs = SakurentsuThreeMDocument::leftJoin('employee_syncs', 'sakurentsu_three_m_documents.pic', '=', 'employee_syncs.employee_id')
            ->where('form_id', '=', $id_three_m)->select('form_id', 'document_name', 'document_description', 'pic', 'employee_syncs.name', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'))->get();
        $relate_dept = SakurentsuThreeM::where('id', '=', $id_three_m)->select('related_department')->first();

        $dept = explode(',', $relate_dept->related_department);

        $sign_master = EmployeeSync::whereIn('position', ['Deputy General Manager', 'General Manager'])->whereNull('end_date')->select(db::raw('employee_id as approver_id'), db::raw('name as approver_name'), db::raw('department as approver_department'), 'position', db::raw('0 as remark'))->orderBy('position', 'desc')->get();

        $sign_user = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)->where('status', '=', 'approve')->whereNotNull('approver_department')->select('approver_id', 'approver_name', 'approver_department', 'position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_at'))->orderBy('position', 'desc')->get();

        $distribution = SakurentsuThreeMDistribution::where('form_id', '=', $id_three_m)->where('distribute_status', '=', 'Checked')->select('distribution_to', 'distribute_status')->get();

        $signed = SakurentsuThreeMApproval::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_approvals.approver_id')
            ->where('form_id', '=', $id_three_m)
            ->select('approver_id', 'approver_name', 'approver_department', db::raw('approver_division as division'), 'sakurentsu_three_m_approvals.position', db::raw('DATE_FORMAT(sakurentsu_three_m_approvals.approve_at, "%d-%m-%Y") as approve_date'), 'status')->get();

        $sign = array_merge($sign_master->toArray(), $sign_user->toArray());

        $implement = SakurentsuThreeMImplementation::where('form_id', '=', $id_three_m)->first();
        $sign_imp = SakurentsuThreeMImpApproval::where('form_id', '=', $id_three_m)->get();

        $sign_std = SakurentsuThreeMApproval::where('form_id', '=', $id_three_m)
            ->where('position', '=', 'President Director')
            ->select(db::raw('DATE_FORMAT(approve_at, "%d %b %Y") as dt'))
            ->first();

        $file_sakurentsu = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsu_three_ms.sakurentsu_number', 'sakurentsus.sakurentsu_number')
            ->where('sakurentsu_three_ms.id', $id_three_m)
            ->select('file', 'file_translate')
            ->first();

        return view(
            'sakurentsu.report.report_pdf_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
                'docs' => $docs,
                'sign_user' => $sign,
                'signed_user' => $signed,
                'sign_gm' => $sign_master,
                'implement' => $implement,
                'sign_imp' => $sign_imp,
                'distribusi' => $distribution,
                'sign_std' => $sign_std,
                'sk_file' => $file_sakurentsu,
            )
        )->with('page', 'Report Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_implement($id)
    {
        $title = '3M Implementation Form';
        $title_jp = '';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();

        $proposer = db::select('SELECT name, remark from send_emails left join users on send_emails.email = users.email where remark = (SELECT department from employee_syncs where employee_id = "' . $data_tiga_em->created_by . '")');

        // $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->get();
        $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->select('form_id', 'document_name', 'document_description', 'pic', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'))->get();

        $implement = SakurentsuThreeMImplementation::where('form_id', '=', $id)->first();

        $emp = EmployeeSync::whereNull('end_date')->where('position', 'NOT LIKE', '%Operator%')->where('position', '<>', 'President Director')->orderBy('name', 'ASC')->get();

        $imp_sign = SakurentsuThreeMImpApproval::where('form_id', $id)->whereIn('position', ['Deputy General Manager', 'General Manager'])->get();

        $sign_master = EmployeeSync::whereIn('position', ['Deputy General Manager', 'General Manager'])->whereNull('end_date')->select('department', 'employee_id', 'name', 'position', db::raw('0 as remark'))->orderBy('position', 'desc')->get();

        // $sign_user = EmployeeSync::whereIn('department', $dept)->whereIn('position', ['Foreman','Manager','Chief'])->select('department', 'employee_id', 'name', 'position', db::raw('IF(position = "Manager", 1, IF(position = "Foreman", 2, 3)) as remark'))->orderBy('remark')->get();

        // $sign = array_merge($sign_master->toArray(), $sign_user->toArray());
        $sign = $sign_master->toArray();

        return view(
            'sakurentsu.master.index_3m_implement_form',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'doc_tiga_m' => $doc_tiga_em,
                'implement' => $implement,
                'proposer' => $proposer,
                'employee' => $emp,
                'imp_sign' => $sign,
                'user_sign' => $imp_sign,
            )
        )->with('page', '3M Implementation')
            ->with('head', 'Sakurentsu');
    }

    public function index_tiga_em_implement2($id, $cat)
    {
        $title = '3M Implementation Form';
        $title_jp = '';

        $data_tiga_em = SakurentsuThreeM::where('id', '=', $id)->first();

        $proposer = db::select('SELECT name, remark from send_emails left join users on send_emails.email = users.email where remark = (SELECT department from employee_syncs where employee_id = "' . $data_tiga_em->created_by . '")');

        // $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->get();
        $doc_tiga_em = SakurentsuThreeMDocument::where('form_id', '=', $id)->select('form_id', 'document_name', 'document_description', 'pic', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'))->get();

        $implement = SakurentsuThreeMImplementation::where('form_id', '=', $id)->first();

        $emp = EmployeeSync::whereNull('end_date')->where('position', 'NOT LIKE', '%Operator%')->orderBy('name', 'ASC')->get();

        $imp_sign = SakurentsuThreeMImpApproval::where('form_id', $id)->orderBy('remark', 'asc')->get();

        return view(
            'sakurentsu.master.index_3m_implement_form',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'doc_tiga_m' => $doc_tiga_em,
                'implement' => $implement,
                'proposer' => $proposer,
                'employee' => $emp,
                'imp_sign' => $imp_sign,
            )
        )->with('page', '3M Implementation')
            ->with('head', 'Sakurentsu');
    }

    public function tiga_3m_monitoring()
    {
        $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)
            ->select('department')
            ->first();

        if ($dept != null) {
            return view(
                'sakurentsu.monitoring.sakurentsu_three_m_monitoring',
                array(
                    'title' => 'Sakurentsu Monitoring',
                    'title_jp' => '作連通監視',
                    'dept_user' => $dept,
                )
            )->with('page', 'Sakurentsu Monitoring');
        } else {
            return view('404');
        }

    }

    public function monitoring_notifikasi($category)
    {
        $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)
            ->select('department')
            ->first();

        return view(
            'sakurentsu.monitoring.sakurentsu_three_m_monitoring',
            array(
                'title' => 'Sakurentsu Monitoring',
                'title_jp' => '作連通監視',
                'dept_user' => $dept,
            )
        )->with('page', 'Sakurentsu Monitoring');
    }

    public function index_trial_request_temp()
    {
        $emp_id = strtoupper(Auth::user()->username);
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        return view(
            'sakurentsu.master.index_trial',
            array(
                'title' => 'Trial Request List',
                'title_jp' => '',
            )
        )->with('page', 'Trial Request');
    }

    public function index_assign_to_staff($sk_num, $category)
    {
        if ($category == "interpreter_tiga_em") {
            $sk = sakurentsuThreeM::where('id', $sk_num)->select(db::raw('id as sakurentsu_number'), 'category', 'title', 'name')
                ->leftJoin('employee_syncs', 'sakurentsu_three_ms.created_by', '=', 'employee_syncs.employee_id')->first();
        } else {
            $sk = Sakurentsu::where('sakurentsu_number', '=', $sk_num)->first();
        }

        if ($category == 'manager') {
            $staff = EmployeeSync::where('position', 'LIKE', '%staff%')->Orwhere('position', 'LIKE', '%Chief%')->Orwhere('position', 'LIKE', '%Foreman%')->Orwhere('position', 'LIKE', '%Coordinator%')->Orwhere('position', 'LIKE', '%Manager%')->whereNull('end_date')->select('employee_id', 'name', 'group')->orderBy('name', 'asc')->get();
        } else if ($category == 'interpreter') {
            $staff = EmployeeSync::where('section', '=', 'Secretary Admin Section')->where('employee_id', '!=', 'PI9704001')->whereNull('end_date')->select('employee_id', 'name', 'group')->orderBy('employee_id', 'asc')->get();
        } else if ($category == 'interpreter_tiga_em') {
            $staff = EmployeeSync::where('section', '=', 'Secretary Admin Section')->where('employee_id', '!=', 'PI9704001')->whereNull('end_date')->select('employee_id', 'name', 'group')->orderBy('employee_id', 'asc')->get();
        } else if ($category == 'Trial') {
            $dept = Sakurentsu::select('pic')->where('sakurentsu_number', '=', $sk_num)->first();

            $staff = EmployeeSync::where('department', '=', $dept->pic)->whereNull('end_date')->whereIn('grade_name', ['Staff', 'Coordinator', 'Specialist'])->where('position', 'not like', '%Operator')->where('position', 'not like', '%Leader')->select('employee_id', 'name')->orderBy('employee_id', 'asc')->get();
        }

        return view(
            'sakurentsu.master.index_assign',
            array(
                'title' => 'Sakurentsu Assign to Staff',
                'title_jp' => '',
                'sk' => $sk,
                'category' => $category,
                'staff_list' => $staff,
            )
        )->with('page', 'Assign');
    }

    public function index_trial_pss($sk_number)
    {
        $trial = Sakurentsu::where('sakurentsus.sakurentsu_number', '=', $sk_number)->leftJoin('sakurentsu_trial_statuses', 'sakurentsu_trial_statuses.sakurentsu_number', '=', 'sakurentsus.sakurentsu_number')->first();

        return view(
            'sakurentsu.master.index_trial_pss',
            array(
                'title' => 'Sakurentsu Trial Request - PSS',
                'title_jp' => '',
                'sk' => $trial,
            )
        )->with('page', 'Trial Request PSS');
    }

    public function index_upload_pss($sk_num)
    {
        $trial = Sakurentsu::where('sakurentsus.sakurentsu_number', '=', $sk_num)->leftJoin('sakurentsu_trial_statuses', 'sakurentsu_trial_statuses.sakurentsu_number', '=', 'sakurentsus.sakurentsu_number')->first();

        // $logged = EmployeeSync::whereIn('employee_id', ['PI9808012','PI9807014','PI1102002'])->orWhere('department', 'Management Information System Department')->first();
        $logged = db::select("SELECT * from employee_syncs where employee_id = '" . Auth::user()->username . "' and (employee_id in ('PI9808012','PI9807014','PI1102002') or department = 'Management Information System Department')");

        // dd($logged);
        if (count($logged) > 0) {
            return view(
                'sakurentsu.report.upload_pss',
                array(
                    'title' => 'Sakurentsu Trial Request - Upload PSS',
                    'title_jp' => '',
                    'sk' => $trial,
                )
            )->with('page', 'Trial Upload PSS');
        } else {
            return view(
                'sakurentsu.monitoring.sakurentsu_three_m_monitoring',
                array(
                    'title' => 'Sakurentsu Monitoring',
                    'title_jp' => '作連通監視',
                )
            )->with('page', 'Sakurentsu Monitoring');
        }
    }

    public function index_receive_information($sk_num)
    {
        // $trial = Sakurentsu::where('sakurentsus.sakurentsu_number', '=', $sk_num)->leftJoin('sakurentsu_trial_statuses', 'sakurentsu_trial_statuses.sakurentsu_number', '=', 'sakurentsus.sakurentsu_number')->first();

        return view(
            'sakurentsu.report.index_receive_info',
            array(
                'title' => '',
                'title_jp' => '',
                'sk_num' => $sk_num,
            )
        )->with('page', 'Sakurentsu Information');
    }

    public function index_material()
    {

        $emp = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position', 'department', 'section', 'group')
            ->first();

        $dept = EmployeeSync::whereNotNull('department')
            ->select('department')
            ->distinct()
            ->get();

        $dept_prod = EmployeeSync::whereNotNull('department')
            ->where('division', 'Production Division')
            ->select('department')
            ->distinct()
            ->orderBy('department', 'asc')
            ->get();

        $section = EmployeeSync::whereNotNull('section')
            ->select('department', 'section')
            ->distinct()
            ->get();

        $storage_location = StorageLocation::where('category', 'WIP')
            ->whereNotNull('plnt')
            ->get();

        $mpdl = db::connection('ymes')
            ->table('vm_item0010')
            ->whereIn('eval_class_code', ['9010', '9040'])
            ->whereNull('plant_spitem_status')
            ->whereNull('special_prc_type')
            ->select(
                db::raw('item_code AS material_number'),
                db::raw('item_name AS material_description'),
                db::raw('issue_loc_code AS sloc'),
                db::raw('eval_class_code AS valcl')
            )
            ->get();

        return view(
            'sakurentsu.trial_request.master.index_material',
            array(
                'title' => 'Material List',
                'title_jp' => '材料一覧',
                'mpdl' => $mpdl,
                'emp' => $emp,
                'dept' => $dept,
                'dept_prod' => $dept_prod,
                'section' => $section,
                'storage_location' => $storage_location,
            )
        )->with('page', 'Sakurentsu Information');
    }

    public function index_tiga_em_document()
    {
        return view(
            'sakurentsu.master.index_3m_document',
            array(
                'title' => '3M Document List',
                'title_jp' => '',
            )
        )->with('page', '3M Document List')
            ->with('head', 'Sakurentsu');
    }

    //==================================//
    //              END INDEX           //
    //==================================//

    //==================================//
    //         Upload Sakurentsu        //
    //==================================//
    public function upload_sakurentsu()
    {
        $title = 'Sakurentsu';
        $title_jp = '作連通';

        $employee = User::where('username', Auth::user()->username)
            ->select('name')->first();

        if (strtoupper(Auth::user()->username) == 'PI2111045' || strtoupper(Auth::user()->username) == 'PI2002021' || strtoupper(Auth::user()->username) == 'PI0812002' || strtoupper(Auth::user()->username) == 'PI1506003') {
            return view(
                'sakurentsu.report.upload_sakurentsu',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'employee' => $employee,
                )
            )->with('page', 'Upload Sakurentsu')
                ->with('head', 'Sakurentsu');
        } else {
            return redirect('/home');
        }

    }

    public function upload_file_sakurentsu(Request $request)
    {
        try {

            $id_user = Auth::id();

            $files = array();
            $file = new Sakurentsu();
            $arr_files = [];

            $number_sk = $request->get('sakurentsu_number');

            // -------------- INSERT TRANSLATION REQUEST ------------

            $filename = null;
            $code_generator = CodeGenerator::where('note', '=', 'translation')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $translation_id = $code_generator->prefix . $number;

            $file_destination = 'files/translation';
            $filenames = array();

            if ($request->file('file') != null) {
                if ($files = $request->file('file')) {

                    for ($i = 0; $i < count($files); $i++) {
                        $file = $files[$i];

                        $nama = $file->getClientOriginalName();

                        $filename = $number_sk;
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $filename = $translation_id . '_' . $filename . '_' . $i . '.' . $extension;

                        $file->move($file_destination, $filename);
                        array_push($filenames, $filename);

                        $translation_attachments = db::connection('ympimis_2')->table('translation_attachments')
                            ->insert([
                                'translation_id' => $translation_id,
                                'file_name' => $filename,
                                'file_name_result' => "",
                                'created_by' => Auth::user()->username,
                                'created_by_name' => Auth::user()->name,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s'),
                            ]);
                    }
                }

                $arr_files = json_encode($filenames);
            } else {
                $arr_files = null;
            }

            $data2 = Sakurentsu::firstOrNew([
                'sakurentsu_number' => $number_sk,
            ]);

            $data2->applicant = $request->get('applicant');
            $data2->title_jp = $request->get('title_jp');
            $data2->file = $arr_files;
            $data2->upload_date = date('Y-m-d');
            $data2->created_by = $id_user;
            $data2->target_date = $request->get('target_date');
            $data2->category = $request->get('sakurentsu_category');

            if ($request->get('sakurentsu_category') == 'Trial') {
                $data2->remark = $request->get('pss_req');
                $data2->send_status = $request->get('send_item');
            }

            $data2->status = 'translate';
            $data2->position = 'interpreter';
            $data2->save();

            //Kirim Ke Koordinator Interpreter
            // $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where section = 'Secretary Admin Section' and employee_id != 'PI9704001'";
            // $mailtoo = DB::select($mails);

            // ------------------ PSS --------------

            // if ($request->get('pss_req') == 'pss') {
            //     $pss_email = " or employee_id = 'PI'"
            // }

            // $mailcc = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where  employee_id='PI1206001' and 'PI0812002'";
            // $mailtoocc = DB::select($mailcc);

            // $isimail = "select * FROM sakurentsus where sakurentsus.id =".$data2->id;
            // $sakurentsuisi = db::select($isimail);

            // Mail::to($mailtoo)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

            if ($request->get('sakurentsu_category') == 'Trial') {
                if ($request->get('send_item') == 'YES_NEW' || $request->get('send_item') == 'YES_EXIST') {

                    $isimail = "select * FROM sakurentsus where sakurentsus.id =" . $data2->id;
                    $sakurentsuisi = db::select($isimail);

                    // $eo_request =

                    $mailto = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where 'PI0812002'";
                    $mailtoo2 = DB::select($mailto);

                    // Mail::to($mailtoo2)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($eo_request, 'eo_request'));
                }
            }

            $std_time = 0;
            $load_time = 0;
            $number_of_page = 4;

            $std_time = 50;

            $status = "Waiting";

            $load_time = $std_time * $number_of_page;

            $translation = db::connection('ympimis_2')->table('translations')->insert([
                'translation_id' => $translation_id,
                'category' => 'translation',
                'document_type' => 'Khusus',
                'title' => 'Sakurentsu ' . $number_sk,
                'number_page' => $number_of_page,
                'request_date' => date('Y-m-d'),
                'std_time' => $std_time,
                'load_time' => $load_time,
                'requester_id' => Auth::user()->username,
                'requester_name' => Auth::user()->name,
                'requester_email' => Auth::user()->email,
                'pic_id' => '',
                'pic_name' => '',
                'department_name' => 'Production Control Department',
                'department_shortname' => 'PC',
                'translation_request' => '<p>' . $request->get('title_jp') . '</p>',
                'remark' => $number_sk,
                'status' => $status,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $translation_id,
                'status' => $status,
                'remark' => 'Created',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $translation_id)
                ->first();

            $data = [
                'translation' => $translation,
                'filenames' => $filenames,
            ];

            if (!str_contains(Auth::user()->role_code, 'S-INT')) {

                $translation_pics = db::connection('ympimis_2')->table('translation_pics')
                    ->whereNull('deleted_at')
                    ->get();

                $translators = array();

                foreach ($translation_pics as $translation_pic) {
                    array_push($translators, $translation_pic->email);
                }

                Mail::to($translators)
                    ->bcc(['nasiqul.ibat@music.yamaha.com'])
                    ->send(new SendEmail($data, 'translation_request'));

            }

            $isimail = "select * FROM sakurentsus where sakurentsus.sakurentsu_number = '" . $number_sk . "'";
            $sakurentsuisi = db::select($isimail);

            Mail::to(['mamluatul.atiyah@music.yamaha.com', 'farizca.nurma@music.yamaha.com', 'takashi.ohkubo@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

            return redirect('/index/sakurentsu/upload_sakurentsu')->with('success', 'Sakurentsu has been uploaded & send to Interpreter');
        } catch (Exception $e) {

            return redirect('/index/sakurentsu/upload_sakurentsu')->with('error', 'Error upload Sakurentsu ' . $e->getMessage());

        }

    }

    public function update_file_sakurentsu(Request $request)
    {
        try {

            $id_user = Auth::id();

            $files = array();
            $file = new Sakurentsu();
            $arr_files = [];

            $number_sk = $request->get('sakurentsu_number_edit');

            // -------------- INSERT TRANSLATION REQUEST ------------

            // $filename = null;

            // $file_destination = 'files/translation';
            // $filenames = array();

            // if($request->file('file') != NULL)
            // {
            //     if ($files = $request->file('file'))
            //     {

            //         for ($i=0; $i < count($files); $i++) {
            //             $file = $files[$i];

            //             $nama = $file->getClientOriginalName();

            //             $filename = $number_sk;
            //             $extension = pathinfo($nama, PATHINFO_EXTENSION);

            //             $filename = $translation_id.'_'.$filename.'_'.$i.'.'.$extension;

            //             $file->move($file_destination, $filename);
            //             array_push($filenames, $filename);

            //             $translation_attachments = db::connection('ympimis_2')->table('translation_attachments')
            //             ->insert([
            //                 'translation_id' => $translation_id,
            //                 'file_name' => $filename,
            //                 'file_name_result' => "",
            //                 'created_by' => Auth::user()->username,
            //                 'created_by_name' => Auth::user()->name,
            //                 'created_at' => date('Y-m-d H:i:s'),
            //                 'updated_at' => date('Y-m-d H:i:s')
            //             ]);
            //         }
            //     }

            //     $arr_files = json_encode($filenames);
            // } else {
            //     $arr_files = null;
            // }

            sakurentsu::where('id', $request->get('id_edit'))
                ->update([
                    'sakurentsu_number' => $request->get('sakurentsu_number_edit'),
                    'title_jp' => $request->get('title_jp_edit'),
                    'category' => $request->get('sakurentsu_category_edit'),
                    'target_date' => $request->get('target_date_edit'),
                    'created_by' => Auth::id(),
                ]);

            if ($request->get('sakurentsu_category_edit') == 'Trial') {
                sakurentsu::where('id', $request->get('id_edit'))
                    ->update([
                        'remark' => $request->get('pss_req_edit'),
                        'send_status' => $request->get('send_item_edit'),
                    ]);
            }

            // if ($request->get('sakurentsu_category') == 'Trial') {
            //     if ($request->get('send_item') == 'YES_NEW' || $request->get('send_item') == 'YES_EXIST') {

            //         $isimail = "select * FROM sakurentsus where sakurentsus.id =".$data2->id;
            //         $sakurentsuisi = db::select($isimail);

            //             // $eo_request =

            //         $mailto = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where 'PI0812002'";
            //         $mailtoo2 = DB::select($mailto);

            //             // Mail::to($mailtoo2)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($eo_request, 'eo_request'));
            //     }
            // }

            return redirect('/index/sakurentsu/upload_sakurentsu')->with('success', 'Sakurentsu has been updated');
        } catch (Exception $e) {

            return redirect('/index/sakurentsu/upload_sakurentsu')->with('error', 'Error upload Sakurentsu ' . $e->getMessage());

        }

    }

    public function deleteSakurentsu(Request $request)
    {
        try {
            Sakurentsu::where('id', $request->get('id'))
                ->update([
                    'delete_note' => $request->get('notes'),
                    'created_by' => Auth::id(),
                ]);

            Sakurentsu::where('id', $request->get('id'))
                ->delete();

            $response = array(
                'status' => true,
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

    public function fetch_sakuretsu(Request $request)
    {
        $data = Sakurentsu::orderBy('status', 'asc')->get();
        // db::select('SELECT * from sakurentsus where deleted_at is null order by id desc');

        $response = array(
            'status' => true,
            'datas' => $data,
        );

        return Response::json($response);
    }

    public function fetch_translate_sakurentsu(Request $request)
    {
        $list = Sakurentsu::where('position', '=', 'interpreter2')->select('sakurentsu_number', 'title_jp', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as tgl_target'), 'applicant', db::raw('DATE_FORMAT(upload_date, "%d %M %Y") as tgl_upload'), 'file', 'id')->get();

        $tiga_em = SakurentsuThreeM::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_ms.created_by')
            ->where('remark', '=', '1')
            ->select('id', 'title', 'product_name', 'proccess_name', 'unit', 'category', 'sakurentsu_three_ms.created_at', db::raw('employee_syncs.name as applicant'))
            ->get();

        $response = array(
            'status' => true,
            'datas' => $list,
            'tiga_em' => $tiga_em,
        );

        return Response::json($response);
    }

    // Penerjemah

    public function upload_sakurentsu_translate($id)
    {
        $title = 'Upload Translated Sakurentsu';
        $title_jp = '翻訳済作連通のアップロード';

        $sakurentsu = Sakurentsu::find($id);

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position')->first();

        return view(
            'sakurentsu.report.upload_sakurentsu_translate',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'employee' => $employee,
                'sakurentsu' => $sakurentsu,
            )
        )->with('page', 'Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function upload_file_sakurentsu_translate(Request $request, $id)
    {

        try {

            $id_user = Auth::id();

            $sakurentsu = Sakurentsu::find($id);

            $files = array();
            $file = new Sakurentsu();

            if ($request->file('file') != null) {
                if ($files = $request->file('file')) {
                    $num = 1;
                    foreach ($files as $file) {
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $nama = $id . '_' . $num . '_' . date('YmdHi') . '.' . $extension;
                        $file->move('uploads/sakurentsu/translated', $nama);
                        $data[] = $nama;

                        $num++;
                    }
                }
                $file->filename = json_encode($data);
            } else {
                $file->filename = null;
            }

            $sakurentsu->title = $request->get('title');
            $sakurentsu->translator = $request->get('translator');
            $sakurentsu->file_translate = $file->filename;
            $sakurentsu->translate_date = date('Y-m-d');

            // if ($sakurentsu->category == 'Trial') {
            $sakurentsu->position = 'PC1';
            // } else {
            //     $sakurentsu->position = 'PC2';
            // }

            $sakurentsu->status = 'approval';
            $sakurentsu->save();

            //Kirim Ke All Mbak Lulu
            $mails = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");

            $mailtoo = [];

            foreach ($mails as $mls) {
                array_push($mailtoo, $mls->email);
            }

            $isimail = "select * FROM sakurentsus where sakurentsus.id =" . $id;
            $sakurentsuisi = db::select($isimail);

            Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

            $response = array(
                'status' => true,
                'message' => 'Sakurentsu has been Translated',
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

    //Detail
    public function detail($id)
    {
        $title = 'Detail Sakurentsu';
        $title_jp = '作連通';

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position')->first();

        if (strtoupper(Auth::user()->username) == 'PI2111045' || strtoupper(Auth::user()->username) == 'PI2002021' || strtoupper(Auth::user()->username) == 'PI0812002' || strtoupper(Auth::user()->username) == 'PI1506003') {
            return view(
                'sakurentsu.report.upload_sakurentsu',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'employee' => $employee,
                )
            )->with('page', 'Upload Sakurentsu')
                ->with('head', 'Sakurentsu');
        } else {
            return redirect('/home');
        }
    }

    public function detail_sakurentsu($id)
    {
        $title = 'Detail Sakurentsu';
        $title_jp = '作連通';

        $employee = EmployeeSync::where('employee_id', Auth::user()->username)
            ->select('employee_id', 'name', 'position')->first();

        $depts = db::select('select department_name, department_shortname from departments');

        if (strtoupper(Auth::user()->username) == 'PI2111045' || strtoupper(Auth::user()->username) == 'PI2002021' || strtoupper(Auth::user()->username) == 'PI0812002' || strtoupper(Auth::user()->username) == 'PI1506003') {
            return view(
                'sakurentsu.report.detail_sakurentsu',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'employee' => $employee,
                    'depts' => $depts,
                )
            )->with('page', 'Sakurentsu List')
                ->with('head', 'Sakurentsu');
        } else {
            return redirect('/home');
        }
    }

    public function fetch_sakurentsu(Request $request)
    {
        $emp_id = Auth::user()->username;
        $_SESSION['KCFINDER']['uploadURL'] = url("kcfinderimages/" . $emp_id);

        $sk = Sakurentsu::where('id', '=', $request->get('id'))->select('id', 'sakurentsu_number', 'title_jp', 'title', 'applicant', 'file_translate', 'file', 'upload_date', 'target_date', 'send_status', 'translate_date', 'translator', 'category', 'remark')->first();

        $response = array(
            'status' => true,
            'datas' => $sk,
        );
        return Response::json($response);
    }

    public function post_sakurentsu_type(Request $request)
    {
        try {
            $tujuan_upload = 'uploads/sakurentsu/add_file';
            $arr_file = [];

            if ($request->get('ctg') == '3M') {
                sakurentsu::where('sakurentsu_number', $request->get('sk_number'))
                    ->update([
                        'status' => 'determined',
                        'position' => 'PIC',
                        'pic' => $request->get('dept'),
                        'category' => $request->get('ctg'),
                        'remark' => $request->get('tiga_em_cat'),
                    ]);
            } else if ($request->get('ctg') == 'Trial') {
                sakurentsu::where('sakurentsu_number', $request->get('sk_number'))
                    ->update([
                        'status' => 'determined',
                        'position' => 'PIC2',
                        'pic' => 'Production Control Department',
                        'send_status' => $request->get('send_item_trial'),
                        'category' => $request->get('ctg'),
                    ]);
            } else if ($request->get('ctg') == 'Information') {
                if ($request->get('att_count') > 0) {
                    for ($i = 0; $i < $request->get('att_count'); $i++) {
                        $file = $request->file('lampiran_file_' . $i);
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $filename = $request->get('sk_number') . '_' . $filename . '.' . $extension;

                        $file->move($tujuan_upload, $filename);

                        array_push($arr_file, $filename);
                    }
                }

                sakurentsu::where('sakurentsu_number', $request->get('sk_number'))
                    ->update([
                        'status' => 'close',
                        'position' => 'PIC',
                        'additional_file' => implode(',', $arr_file),
                        'pic' => $request->get('dept'),
                        'category' => $request->get('ctg'),
                    ]);
            } else if ($request->get('ctg') == 'Not Related') {
                if ($request->get('att_count') > 0) {
                    for ($i = 0; $i < $request->get('att_count'); $i++) {
                        $file = $request->file('lampiran_file_' . $i);
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $filename = $request->get('sk_number') . '_' . $filename . '.' . $extension;

                        $file->move($tujuan_upload, $filename);

                        array_push($arr_file, $filename);
                    }
                }

                sakurentsu::where('sakurentsu_number', $request->get('sk_number'))
                    ->update([
                        'status' => 'close',
                        'position' => 'PIC',
                        'additional_file' => implode(',', $arr_file),
                        'category' => $request->get('ctg'),
                    ]);
            }

            if ($request->get('ctg') != 'Not Related') {
                $dpts = explode(',', $request->get('dept'));

                $section = EmployeeSync::where('department', '=', $dpts)->whereNotNull('section')->select('section')->groupBy('section')->get();

                $arr_section = [];

                foreach ($section as $sec) {
                    array_push($arr_section, $sec->section);
                }

                foreach ($dpts as $dept) {
                    array_push($arr_section, $dept);
                }

                $mng_mails = Mails::leftJoin('users', 'users.email', '=', 'send_emails.email')
                    ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
                    ->WhereIn('remark', $arr_section)
                    ->Where('position', '<>', 'Foreman')
                    ->select('send_emails.email')
                    ->get()
                    ->toArray();

                if ($dpts[0] == 'Purchasing Control Department') {
                    array_push($mng_mails, ['email' => 'nunik.erwantiningsih@music.yamaha.com']);
                    array_push($mng_mails, ['email' => 'jihan.rusdi@music.yamaha.com']);
                    array_push($mng_mails, ['email' => 'noviera.prasetyarini@music.yamaha.com']);
                    array_push($mng_mails, ['email' => 'hanin.hamidi@music.yamaha.com']);
                    array_push($mng_mails, ['email' => 'bakhtiar.muslim@music.yamaha.com']);
                }

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                $isimail = "select * from sakurentsus where sakurentsu_number = '" . $request->get("sk_number") . "'";

                $sakurentsuisi = db::select($isimail);

                Mail::to($mng_mails)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
            } else {
                $isimail = "select * from sakurentsus where sakurentsu_number = '" . $request->get("sk_number") . "'";

                $sakurentsuisi = db::select($isimail);

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                Mail::to('youichi.oyama@music.yamaha.com')->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
            }

            $response = array(
                'status' => true,
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

    public function save_tiga_em_translate(Request $request)
    {
        try {

            // $trans = EmployeeSync::where('employee_id', Auth::user()->username)->select('name')->first();

            if ($request->get('bom') != '') {
                SakurentsuThreeM::where('id', $request->get('id'))
                    ->update([
                        'title_jp' => $request->get('title_name'),
                        'product_name' => $request->get('product_name'),
                        'proccess_name' => $request->get('proccess_name'),
                        'unit' => $request->get('unit_name'),
                        'reason' => $request->get('isi'),
                        'benefit' => $request->get('keuntungan'),
                        'check_before' => $request->get('kualitas_before'),
                        'date_note' => $request->get('tgl_rencana_note'),
                        'special_items' => $request->get('item_khusus'),
                        'translator' => Auth::user()->username . '/' . Auth::user()->name,
                        'translate_date' => date('Y-m-d'),
                    ]);
            } else {
                SakurentsuThreeM::where('id', $request->get('id'))
                    ->update([
                        'title_jp' => $request->get('title_name'),
                        'product_name' => $request->get('product_name'),
                        'proccess_name' => $request->get('proccess_name'),
                        'unit' => $request->get('unit_name'),
                        'reason' => $request->get('isi'),
                        'benefit' => $request->get('keuntungan'),
                        'check_before' => $request->get('kualitas_before'),
                        'date_note' => $request->get('tgl_rencana_note'),
                        'special_items' => $request->get('item_khusus'),
                        'translator' => Auth::user()->username . '/' . Auth::user()->name,
                        'translate_date' => date('Y-m-d'),
                        'remark' => 2,
                    ]);
            }

            $tm = SakurentsuThreeM::where('id', $request->get('id'))->first();

            $tiga_em = SakurentsuThreeM::where('id', '=', $request->get('id'))
                ->update([
                    'remark' => '2',
                    'translate_date' => date('Y-m-d'),
                ]);

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('remark', '=', $tm->form_identity_number)
                ->leftJoin('translation_pics', 'translation_pics.employee_id', '=', 'translations.pic_id')
                ->first();

            if ($translation->pic_id == "") {
                $response = array(
                    'status' => false,
                    'message' => 'Request must be assigned first.',
                );
                return Response::json($response);
            }

            $translation_update = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $translation->translation_id)
                ->update([
                    'translation_result' => '<p>DONE</p>',
                    'finished_at' => date('Y-m-d'),
                    'status' => 'Finished',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $translation->translation_id,
                'status' => 'Finished',
                'remark' => 'Finished',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.form_identity_number', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'TRANSLATE',
                "remark" => $request->get('bom'),
                "subject" => '3M Application (3M申請書)',
            ];

            $mailtoo = SakurentsuThreeM::leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                ->where('sakurentsu_three_ms.id', $request->get('id'))
                ->select('users.email')
                ->first();

            $mailto = [];

            $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
            $mailtoocc = [];

            foreach ($mailcc as $mls) {
                array_push($mailtoocc, $mls->email);
            }

            array_push($mailto, $mailtoo->email);

            Mail::to($mailto)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            $filenames = array();
            $data2 = [
                'translation' => $translation,
                'filenames' => $filenames,
            ];

            Mail::to([$translation->requester_email])
                ->send(new SendEmail($data2, 'translation_result'));

            $response = array(
                'status' => true,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }

    }

    public function save_tiga_em_form(Request $request)
    {
        try {
            $sk_number = null;
            //form_number
            $tahun = date('y');
            $bulan = date('m');

            $query = "SELECT form_identity_number FROM `sakurentsu_three_ms` where DATE_FORMAT(created_at, '%y') = '$tahun' and month(created_at) = '$bulan' order by form_identity_number DESC LIMIT 1";
            $nomorurut = DB::select($query);

            if ($nomorurut != null) {
                $nomor = substr($nomorurut[0]->form_identity_number, -3);
                $nomor = $nomor + 1;
                $nomor = sprintf('%03d', $nomor);
            } else {
                $nomor = "001";
            }

            $result['tahun'] = $tahun;
            $result['bulan'] = $bulan;
            $result['no_urut'] = $nomor;

            $form_number = 'TM' . $result['tahun'] . $result['bulan'] . $result['no_urut'];

            if ($request->get("sakurentsu_number")) {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'status' => 'created',
                        'position' => 'PIC2',
                    ]);
                $sk_number = $request->get("sakurentsu_number");
            }

            if (count($request->file('file_datas')) > 0) {
                $num = 1;
                $files = $request->file('file_datas');
                foreach ($files as $filez) {
                    foreach ($filez as $file) {
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $file_name = $form_number . '_' . $num . '.' . $extension;

                        $file->move('uploads/sakurentsu/three_m/att/', $file_name);

                        $file_names[] = $file_name;
                        $num++;
                    }
                }
                $att = implode(',', $file_names);

            } else {
                $att = null;
            }

            $tiga_em = new SakurentsuThreeM;
            $tiga_em->form_identity_number = $form_number;
            $tiga_em->sakurentsu_number = $sk_number;
            $tiga_em->title = $request->get('title');
            $tiga_em->title_jp = $request->get('title_jp');
            $tiga_em->product_name = $request->get('product');
            $tiga_em->proccess_name = $request->get('proccess');
            $tiga_em->unit = $request->get('unit_name');
            $tiga_em->category = $request->get('category');
            $tiga_em->reason = $request->get('content');
            $tiga_em->benefit = $request->get('benefit');
            $tiga_em->check_before = $request->get('kualitas_before');
            $tiga_em->started_date = $request->get('planned_date');
            $tiga_em->date_note = $request->get('planned_date_note');
            $tiga_em->special_items = $request->get('special_item');
            $tiga_em->related_department = $request->get('related_department');
            $tiga_em->notif_date = $request->get('notif_date');
            $tiga_em->trial_id = $request->get('trial_id');
            $tiga_em->att = $att;
            $tiga_em->remark = 1;
            $tiga_em->created_by = Auth::user()->username;

            $tiga_em->save();

            if ($request->get('num_sp') > 0) {
                for ($i = 0; $i < $request->get('num_sp'); $i++) {
                    $appr_pic = new SakurentsuThreeMSpecial;
                    $appr_pic->form_number = $form_number;
                    $appr_pic->sakurentsu_number = $sk_number;
                    $appr_pic->identical_number = 'sp_' + ($i + 1);
                    $appr_pic->item_khusus = $request->get('sp_item_khusus_' . $i);
                    $appr_pic->target_change = $request->get('sp_target_' . $i);
                    $appr_pic->actual_change = $request->get('sp_actual_' . $i);
                    $appr_pic->pic = $request->get('sp_pic_' . $i);
                    $appr_pic->eviden_description = $request->get('eviden_' . $i);

                    $file_tmp = db::table('Sakurentsu_three_m_sp_temps')->where('temp_id', 'LIKE', ($i + 1) . "_" . strtoupper(Auth::user()->username) . '%')->get();

                    if (count($file_tmp) > 0) {
                        $file_evs = [];
                        foreach ($file_tmp as $temp) {
                            array_push($file_evs, $temp->renamed_file_name);
                        }

                        rename('uploads/sakurentsu/three_m/item_khusus/temp/' . $temp->renamed_file_name, 'uploads/sakurentsu/three_m/item_khusus/act/' . $temp->renamed_file_name);

                        $appr_pic->eviden_att = implode(',', $file_evs);
                        $appr_pic->status = 'Close';
                    } else {
                        $appr_pic->status = 'Open';
                    }

                    $appr_pic->status = $request->get('stat_' . $i);
                    $appr_pic->created_by = Auth::user()->username;
                    $appr_pic->save();

                    $del = db::table('Sakurentsu_three_m_sp_temps')->where('temp_id', 'LIKE', ($i + 1) . "_" . strtoupper(Auth::user()->username) . '%')->delete();
                }
            }

            if ($request->get('trial_id') != '') {
                SakurentsuTrialRequest::where('form_number', '=', $request->get('trial_id'))
                    ->update(['status' => '3M Created']);
            }

            $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

            $emp_pic = db::select('SELECT UPPER(username) as emp_id, users.name, send_emails.remark as department, employee_syncs.position from send_emails LEFT JOIN users on send_emails.email = users.email LEFT JOIN employee_syncs on users.username = employee_syncs.employee_id where remark = "' . $dept->department . '"');

            $appr_pic = new sakurentsuThreeMApproval;
            $appr_pic->form_id = $tiga_em->id;
            $appr_pic->approver_id = $emp_pic[0]->emp_id;
            $appr_pic->approver_name = $emp_pic[0]->name;
            $appr_pic->approver_department = $emp_pic[0]->department;
            $appr_pic->position = $emp_pic[0]->position;
            $appr_pic->status = 'pic';
            $appr_pic->created_by = Auth::user()->username;
            $appr_pic->save();

            $mails = "select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where section = 'Secretary Admin Section' and employee_id != 'PI9704001'";
            $mailtoo = DB::select($mails);

            // -------------- INSERT TRANSLATION --------------

            $dept = EmployeeSync::leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')
                ->where('employee_id', '=', Auth::user()->username)
                ->select('employee_syncs.department', 'department_shortname')
                ->first();

            $code_generator = CodeGenerator::where('note', '=', 'translation')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $translation_id = $code_generator->prefix . $number;
            $filename = null;

            $std_time = 0;
            $load_time = 0;

            $std_time = 50;
            $page_number = 2;

            $load_time = $std_time * $page_number;
            $status = "Assigned";

            $translation = db::connection('ympimis_2')->table('translations')->insert([
                'translation_id' => $translation_id,
                'category' => 'translation',
                'document_type' => 'Khusus',
                'title' => 'Form 3M ' . $form_number,
                'number_page' => $page_number,
                'request_date' => date('Y-m-d'),
                'std_time' => $std_time,
                'load_time' => $load_time,
                'requester_id' => Auth::user()->username,
                'requester_name' => Auth::user()->name,
                'requester_email' => Auth::user()->email,
                'pic_id' => $request->input('pic_id'),
                'pic_name' => $request->input('pic_name'),
                'department_name' => $dept->department,
                'department_shortname' => $dept->department_shortname,
                'translation_request' => '<p>' . url('index/sakurentsu/3m/translate') . '/' . $tiga_em->id . '</p>',
                'status' => $status,
                'remark' => $form_number,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $translation_id,
                'status' => $status,
                'remark' => 'Created',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $filenames = array();

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $translation_id)
                ->first();

            $data = [
                'translation' => $translation,
                'filenames' => $filenames,
            ];

            if (!str_contains(Auth::user()->role_code, 'S-INT')) {

                $translation_pics = db::connection('ympimis_2')->table('translation_pics')
                    ->whereNull('deleted_at')
                    ->get();

                $translators = array();

                foreach ($translation_pics as $translation_pic) {
                    array_push($translators, $translation_pic->email);
                }

                Mail::to($translators)
                    ->send(new SendEmail($data, 'translation_request'));

            }

            $response = array(
                'status' => true,
                'title' => $request->get('title'),
            );
            return Response::json($response);
        } catch (Exception $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage() . ' On Line ' . $e->getLine(),
            );
            return Response::json($response);
        }
    }

    public function fetch_tiga_em(Request $request)
    {
        DB::connection()->enableQueryLog();

        $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

        $department = $dept->department;

        $sakurentsu_req = Sakurentsu::leftJoin('sakurentsu_three_ms', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')
            ->where('sakurentsus.status', '=', 'determined')
            ->where('sakurentsus.category', '=', "3M");

        if ($department == 'Procurement Department' || $department == 'Purchasing Control Department') {
            $sakurentsu_req = $sakurentsu_req->whereRaw('sakurentsus.pic in ("Procurement Department", "Purchasing Control Department")');
        } else {
            $sakurentsu_req = $sakurentsu_req->where('sakurentsus.pic', '=', $dept->department);
        }

        $sakurentsu_req = $sakurentsu_req->select('sakurentsus.sakurentsu_number', 'sakurentsus.title', 'applicant', 'file_translate', 'upload_date', 'target_date', 'sakurentsus.status', 'pic')
            ->get();

        $three_m_list = SakurentsuThreeM::whereNotNull('sakurentsu_three_ms.reason')
            ->leftJoin(db::raw('(select * from processes where remark = "3M") as prcs'), 'prcs.process_code', '=', 'sakurentsu_three_ms.remark')
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_ms.created_by')
            ->select('sakurentsu_three_ms.id', 'sakurentsu_number', 'title', 'product_name', 'proccess_name', 'category', 'sakurentsu_three_ms.remark', 'process_name', 'employee_syncs.department', 'started_date', 'date_note', db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at, "%Y-%m-%d") as create_at'))->get();

        $response = array(
            'status' => true,
            'requested' => $sakurentsu_req,
            'three_m_list' => $three_m_list,
            'dept' => $dept,
            'query' => DB::getQueryLog(),
        );
        return Response::json($response);
    }

    public function fetch_tiga_em_document(Request $request)
    {
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_desc'))->get();

        $con = $docs->count();

        if ($con > 0) {
            $response = array(
                'status' => true,
                'docs' => $docs,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function upload_tiga_em_document(Request $request)
    {
        $file = $request->file('doc_upload');
        $id = $request->get('id_doc_upload');
        $doc_name = $request->get('text_doc_upload');

        // if($request->file('doc_upload'))
        // {
        // $num = 1;
        // foreach ($files as $file)
        // {
        $file_name = str_replace('/', '_', $id) . '_' . date('Y-m-d His') . '.' . $file->getClientOriginalExtension();

        $file->move(base_path('public/uploads/sakurentsu/three_m/doc'), $file_name);

        $three_m = SakurentsuThreeM::where('id', '=', $id)->first();

        $docs = new SakurentsuThreeMDocument;
        $docs->sakurentsu_number = $three_m->sakurentsu_number;
        $docs->form_id = $id;
        $docs->document_name = $doc_name;
        $docs->file_name = $file_name;
        $docs->created_by = Auth::user()->username;
        $docs->save();
        //     $num++;
        // }

        // }

        return redirect()->back()->with(array('alert' => 'Success', 'doc_name' => $doc_name));
    }

    public function post_tiga_em_premeeting(Request $request)
    {
        try {
            $files = array();

            if (count($request->file('file')) > 0) {
                $files = $request->file('file');
                $num = 1;
                foreach ($files as $filez) {
                    foreach ($filez as $file) {
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $file_name = $filename . '_' . date('YmdHis') . $num . '.' . $extension;

                        $file->move('uploads/sakurentsu/three_m/att/', $file_name);
                        $data[] = $file_name;
                        $num++;
                        $new_filename = '|' . implode(',', $data);

                    }

                }

            } else {
                $new_filename = null;
            }

            // $related_department = implode(',', $request->get('related_department'));

            $threem = SakurentsuThreeM::find($request->get('id'));
            if ($new_filename != null) {
                $att = $threem->att . '|' . $new_filename;
            } else {
                $att = $threem->att;
            }

            $need_name = [];

            for ($i = 1; $i <= 18; $i++) {
                if ($request->get('doc_' . $i) == 'NEED') {
                    $e_docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_name_' . $i))->get();

                    if (count($e_docs) > 0) {
                        $e_docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_name_' . $i))->update([
                            'document_description' => $request->get('doc_note_' . $i),
                            'target_date' => $request->get('doc_target_' . $i),
                            'finish_date' => $request->get('doc_finish_' . $i),
                            'pic' => $request->get('doc_pic_' . $i),
                            'created_by' => Auth::user()->username,
                        ]);
                    } else {
                        $docs = new SakurentsuThreeMDocument;
                        $docs->form_id = $request->get('id');
                        $docs->sakurentsu_number = $threem->sakurentsu_number;
                        $docs->document_name = $request->get('doc_name_' . $i);
                        $docs->document_description = $request->get('doc_note_' . $i);
                        $docs->target_date = $request->get('doc_target_' . $i);
                        $docs->finish_date = $request->get('doc_finish_' . $i);
                        $docs->pic = $request->get('doc_pic_' . $i);
                        $docs->created_by = Auth::user()->username;
                        $docs->save();
                    }

                    array_push($need_name, $request->get('doc_name_' . $i));
                }

            }

            //item khusus

            if ($request->get('num_sp') > 0) {
                for ($i = 0; $i < $request->get('num_sp'); $i++) {
                    $appr_pic = SakurentsuThreeMSpecial::firstOrNew(array('form_number' => $threem->form_identity_number, 'identical_number' => 'sp_' + ($i + 1)));
                    $appr_pic->form_number = $threem->form_identity_number;
                    $appr_pic->sakurentsu_number = $threem->sakurentsu_number;
                    $appr_pic->identical_number = 'sp_'.($i + 1);
                    $appr_pic->item_khusus = $request->get('sp_item_khusus_' . $i);
                    $appr_pic->target_change = $request->get('sp_target_' . $i);
                    $appr_pic->actual_change = $request->get('sp_actual_' . $i);
                    $appr_pic->pic = $request->get('sp_pic_' . $i);
                    $appr_pic->eviden_description = $request->get('eviden_' . $i);

                    $file_tmp = db::table('Sakurentsu_three_m_sp_temps')->where('temp_id', 'LIKE', ($i + 1) . "_" . strtoupper(Auth::user()->username) . '%')->get();

                    if (count($file_tmp) > 0) {
                        $file_evs = [];
                        foreach ($file_tmp as $temp) {
                            array_push($file_evs, $temp->renamed_file_name);
                            rename('uploads/sakurentsu/three_m/item_khusus/temp/' . $temp->renamed_file_name, 'uploads/sakurentsu/three_m/item_khusus/act/' . $temp->renamed_file_name);
                        }


                        $appr_pic->eviden_att = implode(',', $file_evs);
                        $appr_pic->status = 'Close';
                    } else {
                        $appr_pic->status = 'Open';
                    }

                    $appr_pic->status = $request->get('stat_' . $i);
                    $appr_pic->created_by = Auth::user()->username;
                    $appr_pic->save();

                    $del = db::table('Sakurentsu_three_m_sp_temps')->where('temp_id', 'LIKE', ($i + 1) . "_" . strtoupper(Auth::user()->username) . '%')->delete();
                }
            }

            $doc_count = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->whereNull('file_name')->count();

            if ($doc_count > 0) {
                $stat = 3;
            } else {
                $stat = 4;
            }

            SakurentsuThreeM::where('id', $request->get('id'))
                ->update([
                    'title' => $request->get('title'),
                    'title_jp' => $request->get('title_jp'),
                    'product_name' => $request->get('product'),
                    'proccess_name' => $request->get('proccess'),
                    'unit' => $request->get('unit_name'),
                    'category' => $request->get('category'),
                    'reason' => $request->get('content'),
                    'benefit' => $request->get('benefit'),
                    'check_before' => $request->get('kualitas_before'),
                    'started_date' => $request->get('planned_date'),
                    'date_note' => $request->get('planned_date_note'),
                    'special_items' => $request->get('special_item'),
                    'bom_change' => $request->get('bom_change'),
                    'related_department' => $request->get('related_department'),
                    'remark' => $stat,
                    'att' => $att,
                ]);

            SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->whereNotIn('document_name', $need_name)->forceDelete();

            // Add approval user
            $department = SakurentsuThreeM::where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('related_department')->first();
            $dept_arr = explode(',', $department->related_department);

            // $emp = EmployeeSync::whereIn('department', $dept_arr)
            // ->whereIn('position', ['Chief'])
            // ->whereNull('end_date')
            // ->select('employee_id', 'name', 'department', 'position')
            // ->get();

            $mngr = Mails::leftJoin('users', 'users.email', '=', 'send_emails.email')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
                ->whereIn('remark', $dept_arr)
                ->select('employee_id', 'employee_syncs.name', 'remark', 'position')
                ->whereNull('end_date')
                ->get();

            $cek_app = SakurentsuThreeMApproval::where('form_id', $request->get('id'))->where('status', '<>', 'pic')->get()->count();

            if ($cek_app == 0) {

                // foreach ($emp as $app) {
                //     $appr = new SakurentsuThreeMApproval;
                //     $appr->form_id = $request->get('id');
                //     $appr->approver_id = $app->employee_id;
                //     $appr->approver_name = $app->name;
                //     $appr->approver_department = $app->department;
                //     $appr->position = $app->position;
                //     $appr->status = 'approve';
                //     $appr->approve_at = null;
                //     $appr->created_by = Auth::user()->username;
                //     $appr->save();
                // }

                foreach ($mngr as $app_m) {
                    $appr = new SakurentsuThreeMApproval;
                    $appr->form_id = $request->get('id');
                    $appr->approver_id = $app_m->employee_id;
                    $appr->approver_name = $app_m->name;
                    $appr->approver_department = $app_m->remark;
                    $appr->position = $app_m->position;
                    $appr->status = 'approve';
                    $appr->approve_at = null;
                    $appr->created_by = Auth::user()->username;
                    $appr->save();
                }

                $gm = EmployeeSync::whereNull('end_date')
                    ->where(function ($q) {
                        // TMP
                        $q->where('division', '=', 'Production Division')
                            ->orWhere('division', '=', 'Production Support Division')
                            ->orWhere('division', '=', null);
                    })
                    ->where(function ($z) {
                        $z->where('position', '=', 'General Manager')
                            ->orWhere('position', '=', 'President Director');
                    })
                    ->select('employee_id', 'name', 'department', 'division', 'position')
                    ->get();

                foreach ($gm as $mgnt) {
                    $appr = new SakurentsuThreeMApproval;
                    $appr->form_id = $request->get('id');
                    $appr->approver_id = $mgnt->employee_id;
                    $appr->approver_name = $mgnt->name;
                    $appr->approver_department = $mgnt->department;
                    $appr->approver_division = $mgnt->division;
                    $appr->position = $mgnt->position;
                    $appr->status = 'approve';
                    $appr->approve_at = null;
                    $appr->created_by = Auth::user()->username;
                    $appr->save();
                }

                // Add DGM Prod
                $appr = new SakurentsuThreeMApproval;
                $appr->form_id = $request->get('id');
                $appr->approver_id = $this->dgm[0];
                $appr->approver_name = 'Budhi Apriyanto';
                $appr->approver_department = null;
                $appr->approver_division = 'Production Division';
                $appr->position = 'Deputy General Manager';
                $appr->status = 'approve';
                $appr->approve_at = null;
                $appr->created_by = Auth::user()->username;
                $appr->save();

                // Add DGM Prod
                $appr = new SakurentsuThreeMApproval;
                $appr->form_id = $request->get('id');
                $appr->approver_id = $this->dgm2[0];
                $appr->approver_name = 'Mei Rahayu';
                $appr->approver_department = null;
                $appr->approver_division = 'Production Support Division';
                $appr->position = 'Deputy General Manager';
                $appr->status = 'approve';
                $appr->approve_at = null;
                $appr->created_by = Auth::user()->username;
                $appr->save();
                // email to Interpreter

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp', 'sakurentsu_three_ms.translator')->first();

                $translator = explode('/', $data_tiga_em->translator)[0];

                $mail = user::where('username', '=', $translator)->select('email')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'INTERPRETER2',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                //  $pics = SakurentsuThreeMApproval::leftJoin('users', 'sakurentsu_three_m_approvals.approver_id', '=', 'users.username')
                //     ->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
                //     ->where('status', '=', 'pic')
                //     ->where('form_id', '=', $request->get('id'))
                //     ->select('users.email', 'department')
                //     ->first();

                //     $section = EmployeeSync::select('section')->where('department', $pics->department)->get()->toArray();

                //     $chf_foreman = Mails::whereIn('remark', $section)->get();

                //     $mailtoo = [];

                //     foreach ($pics as $dept) {
                //         array_push($mailtoo, $dept->email);
                //     }

                //     foreach ($chf_foreman as $chf_frm) {
                //         array_push($mailtoo, $chf_frm->email);
                //     }

                // $data = [
                //     "datas" => $data_tiga_em,
                //     "position" => 'PIC APPROVAL'
                // ];

                // Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

            //email pic

            if ($stat == 4) {
                // email to approver dept

                SakurentsuThreeM::find($request->get('id'))->update(['remark' => '5']);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'PIC APPROVAL',
                    "subject" => '3M Application (3M申請書)',
                ];

                $pics = SakurentsuThreeMApproval::leftJoin('users', 'sakurentsu_three_m_approvals.approver_id', '=', 'users.username')
                    ->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('status', '=', 'pic')
                    ->where('form_id', '=', $request->get('id'))
                    ->select('users.email', db::raw('sakurentsu_three_m_approvals.approver_department as department'))
                    ->first();

                $section = EmployeeSync::select('section')->where('department', $pics->department)->get()->toArray();


                $chf_foreman = Mails::whereIn('remark', $section)->get();

                $mailtoo = [];

                // foreach ($pics as $dept) {
                array_push($mailtoo, $pics->email);
                // }

                // foreach ($chf_foreman as $chf_frm) {
                //     array_push($mailtoo, $chf_frm->email);
                // }

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                Mail::to($mailtoo)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            }

            // email to item khusus

            $tm_special = SakurentsuThreeMSpecial::leftJoin('employee_syncs', 'sakurentsu_three_m_specials.pic', '=', 'employee_syncs.employee_id')
            ->leftJoin('users', 'users.username', '=', 'sakurentsu_three_m_specials.pic')
            ->where('form_number', '=', $threem->form_identity_number)
            ->whereNull('actual_change')
            ->select(db::raw('GROUP_CONCAT(item_khusus separator "|") as item_khusus'), 'pic', 'email')
            ->groupBy('pic', 'email')
            ->get();

            if(count($tm_special) > 0) {
                $email_sp = [];
                foreach ($tm_special as $key => $sp) {
                    array_push($email_sp, $sp->email);
                }

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                $data_specials = SakurentsuThreeM::leftJoin('sakurentsu_three_m_specials', 'sakurentsu_three_ms.form_identity_number', '=', 'sakurentsu_three_m_specials.form_number')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_specials.pic')
                ->leftJoin(db::raw('employee_syncs es'), 'es.employee_id', '=', 'sakurentsu_three_ms.created_by')
                ->where('sakurentsu_three_m_specials.form_number', '=', $threem->form_identity_number)
                ->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.sakurentsu_number', 'form_identity_number', 'sakurentsu_three_ms.title', 'sakurentsu_three_ms.title_jp', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.started_date', 'sakurentsu_three_ms.date_note', 'es.name', 'sakurentsu_three_m_specials.identical_number', 'item_khusus', 'target_change', 'actual_change', 'employee_syncs.name', 'eviden_description', 'eviden_att', 'sakurentsu_three_m_specials.status', db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%d %b %Y") as create_date'))
                ->get();

                $data_sp = [
                    "datas" => $data_specials
                ];

                Mail::to($email_sp)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data_sp, '3m_specials'));
            }

            $response = array(
                'status' => true,
                'message' => 'Sakurentsu 3M has been Updated & Meeting already done',
                'files' => $new_filename,
                'doc' => $need_name,
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

    public function retarget_3m(Request $request)
    {
        $response = array(
            'status' => true
        );
        return Response::json($response);
    }

    public function approve_tiga_em_pic($id_tiga_em, $position)
    {
        $appr = EmployeeSync::where('employee_id', Auth::user()->username)->first();

        $cek_data = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
            ->where('status', '=', 'pic')
            ->select('approve_at')
            ->first();

        // dd($cek_data);

        if ($cek_data->approve_at != '') {
            $title = 'Approval 3M Application';
            $title_jp = '3M変更申請の承認';

            $data2 = sakurentsuThreeM::where('id', '=', $id_tiga_em)->first();

            return view(
                'sakurentsu.report.index_approval',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'data' => $data2,
                    'status' => '3M Request Has Been Approved',
                )
            )->with('page', 'Sakurentsu')
                ->with('head', 'Sakurentsu');
        }

        sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
            ->where('status', '=', 'pic')
            ->update([
                'approver_id' => $appr->employee_id,
                'approver_name' => $appr->name,
                // 'approver_department' => $appr->department,
                'position' => $appr->position,
                'approve_at' => date('Y-m-d H:i:s'),
            ]);

        $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

        $data = [
            "datas" => $data_tiga_em,
            "position" => 'DEPT APPROVAL',
            "subject" => '3M Application (3M申請書)',
        ];

        $dept_all = SakurentsuThreeMApproval::leftJoin('users', 'sakurentsu_three_m_approvals.approver_id', '=', 'users.username')
            ->whereNull('approve_at')
            ->where('status', '=', 'approve')
            ->whereNotNull('approver_department')
            ->where('form_id', '=', $id_tiga_em)
            ->select('users.email')
            ->get();

        $mailtoo = [];

        foreach ($dept_all as $dept) {
            array_push($mailtoo, $dept->email);
        }

        $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
        $mailtoocc = [];

        foreach ($mailcc as $mls) {
            array_push($mailtoocc, $mls->email);
        }

        Mail::to($mailtoo)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

        $title = 'Approval 3M Application';
        $title_jp = '3M変更申請の承認';

        $data2 = sakurentsuThreeM::where('id', '=', $id_tiga_em)->first();

        return view(
            'sakurentsu.report.index_approval',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data2,
                'status' => '3M Request successfully approved',
            )
        )->with('page', 'Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function post_tiga_em_finalmeeting(Request $request)
    {
        try {
            $files = array();

            if (count($request->file('file')) > 0) {
                $files = $request->file('file');
                $num = 1;
                foreach ($files as $filez) {
                    foreach ($filez as $file) {
                        $nama = $file->getClientOriginalName();

                        $filename = pathinfo($nama, PATHINFO_FILENAME);
                        $extension = pathinfo($nama, PATHINFO_EXTENSION);

                        $file_name = $filename . '_' . date('YmdHis') . $num . '.' . $extension;

                        $file->move('uploads/sakurentsu/three_m/att/', $file_name);
                        $data[] = $file_name;
                        $num++;
                        $new_filename = '|' . implode(',', $data);

                    }

                }

            } else {
                $new_filename = null;
            }

            $threem = SakurentsuThreeM::find($request->get('id'));
            if ($new_filename != null) {
                $att = $threem->att . '|' . $new_filename;
            } else {
                $att = $threem->att;
            }

            $threem = SakurentsuThreeM::find($request->get('id'));

            SakurentsuThreeM::where('id', $request->get('id'))
                ->update([
                    'title' => $request->get('title'),
                    'title_jp' => $request->get('title_jp'),
                    'product_name' => $request->get('product'),
                    'proccess_name' => $request->get('proccess'),
                    'unit' => $request->get('unit_name'),
                    'category' => $request->get('category'),
                    'reason' => $request->get('content'),
                    'benefit' => $request->get('benefit'),
                    'check_before' => $request->get('kualitas_before'),
                    'started_date' => $request->get('planned_date'),
                    'date_note' => $request->get('planned_date_note'),
                    'special_items' => $request->get('special_item'),
                    'bom_change' => $request->get('bom_change'),
                    'related_department' => $request->get('related_department'),
                    'remark' => $request->get('stat'),
                    'att' => $att,
                ]);

            $need_name = [];

            for ($i = 1; $i <= 18; $i++) {
                if ($request->get('doc_' . $i) == 'NEED') {
                    $e_docs = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_name_' . $i))->update([
                        'document_description' => $request->get('doc_note_' . $i),
                        'target_date' => $request->get('doc_target_' . $i),
                        'finish_date' => $request->get('doc_finish_' . $i),
                        'pic' => $request->get('doc_pic_' . $i),
                    ]);

                    array_push($need_name, $request->get('doc_name_' . $i));
                }
            }

            // SIGN PIC 3M
            $pics = SakurentsuThreeM::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_ms.created_by')
                ->leftJoin('send_emails', 'employee_syncs.department', '=', 'send_emails.remark')
                ->leftJoin('users', 'users.email', '=', 'send_emails.email')
                ->where('sakurentsu_three_ms.id', '=', $request->get('id'))
                ->select('username', 'users.name', 'employee_syncs.department')
                ->first();

            $app_pic = new sakurentsuThreeMApproval;
            $app_pic->form_id = $request->get('id');
            $app_pic->approver_id = $pics->username;
            $app_pic->approver_name = $pics->name;
            $app_pic->approver_department = $pics->department;
            $app_pic->status = 'pic';
            $app_pic->approve_at = date('Y-m-d H:i:s');
            $app_pic->created_by = Auth::user()->username;
            $app_pic->save();

            $response = array(
                'status' => true,
                'message' => 'Berhasil Input Data',
                'files' => $new_filename,
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

    public function mail_tiga_em_document(Request $request)
    {
        try {
            $doc_list = SakurentsuThreeMDocument::leftJoin('employee_syncs', 'sakurentsu_three_m_documents.pic', '=', 'employee_syncs.employee_id')
                ->where('form_id', '=', $request->get('three_m_id'))
                ->select('document_name', 'document_description', db::raw('DATE_FORMAT(target_date, "%d %M %Y") as target'), db::raw('DATE_FORMAT(finish_date, "%d %M %Y") as finish'), 'pic', 'name', 'file_name')
                ->get();

            // $arr_pos = ['Manager', 'Foreman','Chief', 'Coordinator', 'Staff', 'Senior Staff'];
            $arr_pos = ['Chief', 'Coordinator'];

            $isi = SakurentsuThreeM::where('id', '=', $request->get('three_m_id'))->first();

            $arr_doc_dept = [];
            $arr_name_dept = [];

            foreach ($doc_list as $docs) {
                if ($docs->file_name == null) {
                    if (count($arr_doc_dept) > 0) {
                        if (!in_array($docs->pic, $arr_doc_dept, true)) {
                            array_push($arr_doc_dept, $docs->pic);
                        }
                    } else {
                        array_push($arr_doc_dept, $docs->pic);
                    }
                }
            }

            $email_user_list = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('employee_id', $arr_doc_dept)
                // ->whereIn('position', $arr_pos)
                ->whereNull('end_date')
                ->select('email')
                ->get();

            $dept = EmployeeSync::whereIn('employee_id', $arr_doc_dept)->select('department', 'name')->get();

            $arr_dept = [];
            foreach ($dept as $dpt) {
                array_push($arr_dept, $dpt->department);
                array_push($arr_name_dept, $docs->name);
            }

            $arr_dept = array_unique($arr_dept);
            $arr_name_dept = array_unique($arr_name_dept);

            $email_list = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('department', $arr_dept)
                ->whereIn('position', $arr_pos)
                ->whereNull('end_date')
                ->select('email')
                ->get();

            // $email_mng = Mails::whereIn('remark', $arr_dept)
            // ->select('email')
            // ->get();

            $sign = array_merge($email_list->toArray(), $email_user_list->toArray());
            // $sign = array_merge($sign, $email_user_list->toArray());

            $datas = [
                'documents' => $doc_list,
                'departments' => $arr_name_dept,
                'tiga_m' => $isi,
                'form_id' => $request->get('three_m_id'),
            ];

            Mail::to($sign)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($datas, '3m_document'));

            $response = array(
                'status' => true,
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

    public function upload_tiga_em_upload(Request $request)
    {
        try {
            $files = array();

            if ($request->file('file') != null) {
                if ($files = $request->file('file')) {
                    $num = 1;
                    foreach ($files as $file) {
                        // $nama = $file->getClientOriginalName();
                        $nama = $request->get('id') . '_' . $request->get('doc_name') . '_' . $num . '.' . $file->getClientOriginalExtension();

                        $file->move('uploads/sakurentsu/three_m/doc', $nama);
                        $data[] = $nama;

                        $e_doc = new SakurentsuThreeMDocument;
                        $e_doc->form_id = $request->get('id');
                        $e_doc->sakurentsu_number = $request->get('sk_num');
                        $e_doc->document_name = $request->get('doc_name');
                        $e_doc->document_description = $request->get('doc_desc');
                        $e_doc->target_date = $request->get('doc_target');
                        $e_doc->file_name = $nama;
                        $e_doc->finish_date = date('Y-m-d');
                        $e_doc->pic = $request->get('doc_pic');
                        $e_doc->created_by = Auth::user()->username;
                        $e_doc->save();

                        $num++;
                    }
                }

                $filename = json_encode($data);
            } else {
                $filename = null;
            }

            SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->where('document_name', '=', $request->get('doc_name'))->whereNull('finish_date')->forceDelete();

            // IF DOKUMEN LENGKAP

            $cek_doc = SakurentsuThreeMDocument::where('form_id', '=', $request->get('id'))->whereNull('finish_date')->get()->count();

            if ($cek_doc <= 0) {
                SakurentsuThreeM::where('id', $request->get('id'))->update(['remark' => '4']);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'DOCUMENT',
                    "subject" => '3M Application (3M申請書)',
                ];

                $mailtoo = SakurentsuThreeM::where('sakurentsu_three_ms.id', $request->get('id'))
                    ->leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                    ->select('users.email')
                    ->first();

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                Mail::to($mailtoo->email)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                // email to approver dept

                SakurentsuThreeM::where('id', $request->get('id'))->update(['remark' => '5']);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'PIC APPROVAL',
                    "subject" => '3M Application (3M申請書)',
                ];

                $pics = SakurentsuThreeMApproval::leftJoin('users', 'sakurentsu_three_m_approvals.approver_id', '=', 'users.username')
                    ->leftJoin('employee_syncs', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('status', '=', 'pic')
                    ->where('sakurentsu_three_m_approvals.form_id', '=', $request->get('id'))
                    ->select('users.email', 'department')
                    ->first();

                // $section = EmployeeSync::select('section')->where('department', $pics->department)->get()->toArray();

                // $chf_foreman = Mails::whereIn('remark', $section)->get();

                // $mailtoo = [];

                // foreach ($pics as $dept) {
                //     array_push($mailtoo, $dept->email);
                // }

                // foreach ($chf_foreman as $chf_frm) {
                //     array_push($mailtoo, $chf_frm->email);
                // }

                $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
                $mailtoocc = [];

                foreach ($mailcc as $mls) {
                    array_push($mailtoocc, $mls->email);
                }

                Mail::to($pics->email)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

            $response = array(
                'status' => true,
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

    public function fetch_tiga_em_document_by_id($id)
    {
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $id)
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_documents.pic')
            ->select('document_name', 'document_description', 'target_date', 'finish_date', db::raw('name as pic'), 'file_name', 'employee_syncs.department')
            ->get();

        $response = array(
            'status' => true,
            'docs' => $docs,
        );

        return Response::json($response);
    }

    public function generate_tiga_em_pdf($id_tiga_em)
    {
        $data = SakurentsuThreeM::where('id', '=', $id_tiga_em)->first();
        $docs = SakurentsuThreeMDocument::where('form_id', '=', $id_tiga_em)->get();

        $pdf = \App::make('dompdf.wrapper');
        $pdf->getDomPDF()->set_option("enable_php", true);
        $pdf->setPaper('A4', 'potrait');
        $pdf->setOptions(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);

        $pdf->loadView(
            'sakurentsu.report.pdf_3m_stream',
            array(
                'data' => $data,
                'docs' => $docs,
            )
        );

        return $pdf->stream("3M Request.pdf");

        // return view('sakurentsu.report.pdf_3m_stream', array(

        // ))->with('page', 'sakure')->with('head', 'Sakurentsu');
    }

    public function get_employee_sign(Request $request)
    {
        // $emp = Employee::where('tag', '=', $request->get("employee_tag"))->whereIn()->first();
        $dept = implode('","', $request->get('dept_list'));
        // $dept = '"'.$dept.'"';
        // dd($dept);

        $emp = db::select("select employees.employee_id, employees.`name`, employee_syncs.department , position from employees
                        left join users on employees.employee_id = users.username
                        left join employee_syncs on employees.employee_id = employee_syncs.employee_id
                        where tag = '" . $request->get('employee_tag') . "' and (users.email in (select email from send_emails where remark in (\"" . $dept . "\")) OR (employee_syncs.department in (\"" . $dept . "\") AND employee_syncs.position in ('Chief', 'Foreman')))

                        union all
                        select employees.employee_id, employees.`name`, employee_syncs.department, employee_syncs.position from employees
                        left join employee_syncs on employees.employee_id = employee_syncs.employee_id
                        where tag = '" . $request->get('employee_tag') . "' and position in ('Deputy General Manager','General Manager')");

        $relate_dept = SakurentsuThreeM::where('id', '=', $request->get('form_id'))->select('related_department')->first();
        $signing_user = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))->select('approver_department', 'approver_id')->get();

        // DB::connection()->enableQueryLog();

        if (count($emp) > 0) {
            $stat = 1;
            // dd($relate_dept->toArray());

            if (in_array($emp[0]->position, ['Deputy General Manager', 'General Manager'])) {
                $rel_dept = $relate_dept->toArray();
                // dd($rel_dept['related_department']);

                $rd = explode(',', $rel_dept['related_department']);

                if (count($signing_user) > 0) {
                    foreach ($signing_user->toArray() as $sg_u) {
                        $approver_dept[] = $sg_u['approver_department'];
                    }

                    foreach ($rd as $rel_d) {
                        if (in_array($rel_d, $approver_dept)) {
                        } else {
                            $stat = 0;
                        }
                    }

                } else {
                    $stat = 0;
                }

                if ($stat == 0) {
                    $response = array(
                        'status' => false,
                        'message' => 'All Department Must to sign first',
                        'data' => $emp,
                    );
                    return Response::json($response);
                }
            }

            $response = array(
                'status' => true,
                'data' => $emp,
            );

            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
                'message' => 'Employee Not Registered',
                'data' => $emp,
            );

            return Response::json($response);
        }
    }

    public function mail_unsigned_tiga_em(Request $request)
    {
        $sak_tiga_em = SakurentsuThreeM::where('id', '=', $request->get('form_id'))->select('related_department')->first();

        $rel_dept = explode(",", $sak_tiga_em->related_department);

        $dpt_apr = sakurentsuThreeMApproval::select('approver_department')->whereNotNull('approver_department')->where('form_id', '=', $request->get('form_id'))->where('status', '=', 'approve')->groupBy('approver_department')->get()->toArray();

        $gm_apr = SakurentsuThreeMApproval::select('approver_id')->whereNull('approver_department')->where('form_id', '=', $request->get('form_id'))->get()->toArray();

        if (count($rel_dept) > count($dpt_apr)) {
            //email ke semua dept
            $dept_min = array_diff($rel_dept, $dpt_apr);

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'SIGNING',
                "subject" => '3M Application (3M申請書)',
            ];

            $mailto_c = EmployeeSync::leftJoin('users', 'employee_syncs.employee_id', '=', 'users.username')
                ->whereIn('department', $dept_min)
                ->whereIn('position', ['Chief, Foreman'])
                ->select('email')
                ->get();

            $mailto_m = Mails::whereIn('remark', $dept_min)->select('email')->get();

            $mailto = array_merge($mailto_c->toArray(), $mailto_m->toArray());

            Mail::to($mailto)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

        } else if (count($rel_dept) == count($dpt_apr)) {
            // Pak budhi blm
            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'SIGNING DGM',
                "subject" => '3M Application (3M申請書)',
            ];

            $dept_min = 'SIGNING DGM';

            for ($i = 1; $i <= 2; $i++) {
                Mail::to($this->dgm[$i + 1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

        } else if (count($rel_dept) == count($dpt_apr) && !in_array("PI1206001", $gm_apr)) {
            // Pak Hayakawa blm
            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'SIGNING GM',
                "subject" => '3M Application (3M申請書)',
            ];

            $dept_min = 'SIGNING GM';

            Mail::to($this->gm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
        } else if (count($rel_dept) == count($dpt_apr) && !in_array("PI2111044", $gm_apr)) {
            // Pak ura blm

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'PRESDIR',
                "subject" => '3M Application (3M申請書)',
            ];

            $dept_min = 'PRESDIR';

            Mail::to('hiromichi.ichimura@music.yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
        }

        $response = array(
            'status' => true,
            'min' => $dept_min,
        );

        return $response;
    }

    public function signing_tiga_em(Request $request)
    {
        $approver = $request->get('sign');

        if (!$request->get('position')) {
            // JIKA MELALUI MEETING LANGSUNG

            $mngr_pic = SakurentsuThreeM::where('sakurentsu_three_ms.id', '=', $request->get('form_id'))
                ->join('employee_syncs', 'sakurentsu_three_ms.created_by', '=', 'employee_syncs.employee_id')
                ->join('send_emails', 'employee_syncs.department', '=', 'send_emails.remark')
                ->join('users', 'users.email', '=', 'send_emails.email')
                ->select('username', 'users.name', 'employee_syncs.department')
                ->first();

            $app = sakurentsuThreeMApproval::firstOrNew(array('form_id' => $request->get('form_id'), 'approver_id' => $mngr_pic->username, 'approver_department' => $mngr_pic->department));
            $app->approver_name = $mngr_pic->name;
            $app->approver_department = $mngr_pic->department;
            $app->status = 'pic';
            $app->approve_at = date('Y-m-d H:i:s');
            $app->created_by = $mngr_pic->username;
            $app->save();

            foreach ($approver as $appr) {
                $emp = EmployeeSync::where('employee_id', '=', $appr)->first();

                $app = sakurentsuThreeMApproval::firstOrNew(array('form_id' => $request->get('form_id'), 'approver_id' => $emp->employee_id, 'approver_department' => $emp->department));
                $app->approver_name = $emp->name;
                $app->approver_department = $emp->department;
                $app->status = 'approve';
                $app->approve_at = date('Y-m-d H:i:s');
                $app->position = $emp->position;
                $app->created_by = $emp->employee_id;
                $app->save();
            }

            SakurentsuThreeM::where('id', '=', $request->get('form_id'))->update(['remark' => 5]);

            $tiga_em = SakurentsuThreeM::where('id', '=', $request->get('form_id'))->select('related_department')->first();

            $rel_dept_arr = explode(',', $tiga_em->related_department);

            $sum_sign = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))->whereIn('approver_department', $rel_dept_arr)
                ->where('status', '=', 'approve')
                ->select('approver_department')
                ->groupBy('approver_department')
                ->get()
                ->toArray();

            //TTD yang kurang

            if (count($rel_dept_arr) == count($sum_sign)) {
                // Jika Sudah semua dept kurang DGM TAP

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING DGM',
                    "subject" => '3M Application (3M申請書)',
                ];

                for ($i = 1; $i <= 2; $i++) {
                    Mail::to($this->dgm[$i + 1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
                }

            } else if (count($rel_dept_arr) > count($sum_sign)) {
                $signs = [];
                foreach ($sum_sign as $sign) {
                    array_push($signs, $sign['approver_department']);
                }

                //Jika dept blm sign semua TAP
                $res = array_diff($rel_dept_arr, $signs);

                $mngr = Mails::whereIn('remark', $res)->select('email');

                $all = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->whereIn('position', ['Chief', 'Foreman'])
                    ->whereIn('department', $res)
                    ->select('email')
                    ->union($mngr)
                    ->get()
                    ->toArray();

                $must_signs = [];
                foreach ($all as $sign2) {
                    array_push($must_signs, $sign2['email']);
                }

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($must_signs)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            } else {
                //jika sudah sampai GM ttd  dan DGM SUDAH TAP
                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING GM',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($this->gm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

            $gm = EmployeeSync::where('position', 'LIKE', '%General Manager%')->whereNull('end_date')->select('employee_id')->get();
            $signing_user = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))->select('approver_id')->get();

            foreach ($signing_user->toArray() as $sg_u) {
                $approver_id[] = $sg_u['approver_id'];
            }

            foreach ($gm->toArray() as $gms) {
                $gm_arr[] = $gms['employee_id'];
            }

            // JIKA APPROVE LEWAT EMAIL
            if (count(array_intersect($gm_arr, $approver_id)) == count($gm_arr)) {
                try {
                    $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                    $data = [
                        "datas" => $data_tiga_em,
                        "position" => 'PRESDIR',
                        "subject" => '3M Application (3M申請書)',
                    ];

                    Mail::to('hiromichi.ichimura@music.yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                    $response = array(
                        'status' => true,
                        'message' => 'approval emailed',
                    );

                    return Response::json($response);
                } catch (QueryException $e) {
                    $response = array(
                        'status' => false,
                        'message' => $e->getMessage(),
                    );
                }

            }

        } else if ($request->get('position') && $request->get('position') == 'presdir') {
            //JIKA PAK URA / PRESDIR SIGN DIGITAL
            try {
                $emp3 = EmployeeSync::where('employee_id', '=', 'PI2111044')->first();

                $app3 = sakurentsuThreeMApproval::firstOrNew(array('form_id' => $request->get('form_id'), 'approver_id' => $emp3->employee_id, 'approver_department' => $emp3->department));
                $app3->approve_at = date('Y-m-d H:i:s');
                $app3->save();

                SakurentsuThreeMDistribution::where('form_id', '=', $request->get('form_id'))->forceDelete();

                $distribution = $request->get('distribusi');

                foreach ($distribution as $dstrb) {
                    $distrib = new SakurentsuThreeMDistribution();
                    $distrib->form_id = $request->get('form_id');
                    $distrib->distribution_to = $dstrb['name'];
                    $distrib->distribute_status = $dstrb['value'];
                    $distrib->created_by = Auth::user()->username;

                    $distrib->save();
                }

                // $no = SakurentsuThreeM::whereNotNull('form_number')
                // ->select('form_number')
                // ->orderBy('form_number', 'desc')
                // ->first();

                SakurentsuThreeM::where('id', '=', $request->get('form_id'))
                    ->update([
                        // 'form_number' => $no->form_number+1,
                        'date' => date('Y-m-d H:i:s'),
                        'remark' => 6,
                    ]);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'form_number', 'date', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.created_by', 'sakurentsu_three_ms.reason', 'started_date', 'sakurentsu_three_ms.title_jp', 'date_note')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'STD',
                    'presdir_app_date' => date('Y-m-d H:i:s'),
                    "subject" => '3M Approval (3M変更承認)',
                ];

                Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                // $dept = SakurentsuThreeM::leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                // ->where('sakurentsu_three_ms.id', $request->get('form_id'))
                // ->select( 'related_department', 'users.email')
                // ->first();

                // $dept_arr = explode(',', $dept->related_department);

                // $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                // ->whereIn('department', $dept_arr)
                // ->whereIn('position', ['Manager','Chief','Foreman'])
                // ->select('users.email')
                // ->get()
                // ->toArray();

                // foreach ($mail_dept as $ml_dept) {
                //     $mailtoo[] = $ml_dept['email'];
                // }

                // if (in_array("Production Engineering Department", $dept_arr)) {
                //     array_push($mailtoo, 'susilo.basri@music.yamaha.com');
                // } else if(in_array("Purchasing Control Department", $dept_arr)) {
                //     array_push($mailtoo, 'imron.faizal@music.yamaha.com');
                // } else if(in_array("General Affairs Department", $dept_arr)) {
                //     array_push($mailtoo, 'prawoto@music.yamaha.com');
                // } else if(in_array("Woodwind Instrument - Key Parts Process (WI-KPP) Department", $dept_arr)) {
                //     array_push($mailtoo, 'khoirul.umam@music.yamaha.com');
                // } else if(in_array("Woodwind Instrument - Body Parts Process (WI-BPP) Department", $dept_arr)) {
                //     array_push($mailtoo, 'fatchur.rozi@music.yamaha.com');
                // }

                // array_push($mailtoo, $dept->email);

                // $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id','form_number','date','sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.created_by', 'sakurentsu_three_ms.reason', 'started_date', 'sakurentsu_three_ms.title_jp','date_note')->first();

                // $data = [
                //     "datas" => $data_tiga_em,
                //     "position" => 'ALL'
                // ];

                // $proposer = db::select('SELECT name, remark from send_emails left join users on send_emails.email = users.email where remark = (SELECT department from employee_syncs where employee_id = "'.$data_tiga_em->created_by.'")');

                // $name_prop = $proposer[0]->name;

                // if ($proposer[0]->remark == "Production Engineering Department") {
                //     $name_prop = "Susilo Basri Prasetyo";
                // } else if($proposer[0]->remark == "Purchasing Control Department") {
                //     $name_prop = "Imron Faizal";
                // } else if($proposer[0]->remark == "General Affairs Department") {
                //     $name_prop = "Prawoto";
                // } else if($proposer[0]->remark == "Woodwind Instrument - Key Parts Process (WI-KPP) Department") {
                //     $name_prop = "Khoirul Umam";
                // } else if($proposer[0]->remark == "Woodwind Instrument - Body Parts Process (WI-BPP) Department") {
                //     $name_prop = "Fatchur Rozi";
                // }

                // Mail::to($mailtoo)->cc('evi.nur.cholifah@yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                // $app = SakurentsuThreeMImplementation::updateOrCreate(
                //     ['form_id' => $request->get('form_id'), 'form_number' => $data_tiga_em->form_number],
                //     [
                //         'form_date' => $data_tiga_em->date,
                //         'section' => $proposer[0]->remark,
                //         'name' =>  $name_prop,
                //         'title' => $data_tiga_em->title,
                //         'reason' => $data_tiga_em->reason,
                //         'started_date' => $data_tiga_em->started_date." ".$data_tiga_em->date_note,
                //         'created_by' => 'PI0904001'
                //     ]
                // );

                // $app->save();

            } catch (QueryException $e) {
                $response = array(
                    'status' => false,
                    'message' => $e->getMessage(),
                );

                return Response::json($response);
            }
        } else if ($request->get('position') && $request->get('position') == 'department') {
            //JIKA DEPARTEMEN TERKAIT SIGN DIGITAL
            $tiga_em = SakurentsuThreeM::where('id', '=', $request->get('form_id'))->select('related_department')->first();

            $rel_dept_arr = explode(',', $tiga_em->related_department);

            $emp_dept = EmployeeSync::where('employee_id', '=', $approver[0])->select('department', 'name', 'employee_id', 'position')->first();

            if (!in_array($emp_dept->position, ['Chief', 'Foreman'])) {

                if ($emp_dept->department == "Maintenance Department") {
                    $manag = ['Maintenance Department', 'Production Engineering Department'];
                }

                //Jika Pch / Proc maka pak imron
                else if ($emp_dept->department == "Procurement Department") {
                    $manag = ['Procurement Department', 'Purchasing Control Department'];
                }

                //Jika GA pak arief
                else if ($emp_dept->department == "Human Resources Department") {
                    $manag = ['Human Resources Department', 'General Affairs Department'];
                }

                //Jika KP maka EI
                else if ($emp_dept->department == "Educational Instrument (EI) Department") {
                    $manag = ['Educational Instrument (EI) Department', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department'];
                } else {
                    // Get Manager
                    $manag = [$emp_dept->department];
                }

                if (!empty(array_intersect($rel_dept_arr, $manag))) {
                    $in = array_intersect($rel_dept_arr, $manag);
                    foreach ($in as $val) {
                        $sign_dept = sakurentsuThreeMApproval::firstOrNew(array('form_id' => $request->get('form_id'), 'approver_id' => $approver[0], 'approver_department' => $val));
                        $sign_dept->form_id = $request->get('form_id');
                        $sign_dept->approver_department = $val;
                        $sign_dept->approver_name = $emp_dept->name;
                        $sign_dept->status = 'approve';
                        $sign_dept->approve_at = date('Y-m-d H:i:s');
                        $sign_dept->position = $emp_dept->position;
                        $sign_dept->created_by = $emp_dept->employee_id;
                        $sign_dept->save();
                    }
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Employee Not Registered',
                    );

                    return Response::json($response);
                }

            } else {
                if (in_array($emp_dept->department, $rel_dept_arr)) {
                    $sign_dept = sakurentsuThreeMImpApproval::firstOrNew(array('form_id' => $request->get('form_id'), 'approver_id' => $approver[0], 'approver_department' => $emp_dept->department));
                    $sign_dept->implement_id = $imp->id;
                    $sign_dept->approver_department = $emp_dept->department;
                    $sign_dept->approver_name = $emp_dept->name;
                    $sign_dept->status = 'approve';
                    $sign_dept->approve_at = date('Y-m-d H:i:s');
                    $sign_dept->position = $emp_dept->position;
                    $sign_dept->created_by = $emp_dept->employee_id;
                    $sign_dept->save();
                } else {
                    $response = array(
                        'status' => false,
                        'message' => 'Employee Not Registered',
                    );

                    return Response::json($response);
                }

            }

            $sum_sign2 = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))->whereIn('approver_department', $rel_dept_arr)
                ->where('status', '=', 'approve')
                ->select('approver_department')
                ->groupBy('approver_department')
                ->get()
                ->toArray();

            if (count($rel_dept_arr) == count($sum_sign2)) {
                //JIKA JUMLAH DEPT TTD SUDAH SEMUA

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING DGM',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($this->dgm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

        } else if ($request->get('position') && $request->get('position') == 'gm') {
            // GM SIGN DIGITAL
            $emp5 = EmployeeSync::where('employee_id', '=', $request->get('sign')[0])->first();

            if ($emp5->position != 'General Manager') {
                $response = array(
                    'status' => false,
                    'message' => 'Approver Not Allowed',
                );

                return Response::json($response);
            }

            $app4 = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))
                ->where('approver_id', '=', $emp5->employee_id)
                ->update([
                    'approve_at' => date('Y-m-d H:i:s'),
                ]);

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'PRESDIR',
                "subject" => '3M Application (3M申請書)',
            ];

            Mail::to('hiromichi.ichimura@music.yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

        } else if ($request->get('position') && $request->get('position') == 'dgm') {
            //DGM SIGN DIGITAL

            $emp4 = EmployeeSync::where('employee_id', '=', $request->get('sign')[0])->first();
            //JIKA BUKAN DGM SIGN
            if (strpos($emp4->position, 'General Manager') === false) {
                $response = array(
                    'status' => false,
                    'message' => 'Approver Not Allowed',
                );

                return Response::json($response);
            }

            $app4 = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))
                ->where('approver_id', '=', $emp4->employee_id)
                ->update([
                    'approve_at' => date('Y-m-d H:i:s'),
                ]);

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'SIGNING GM',
                "subject" => '3M Application (3M申請書)',
            ];

            $data_dgm = SakurentsuThreeMApproval::where('sakurentsu_three_m_approvals.form_id', '=', $request->get('form_id'))
                ->where('sakurentsu_three_m_approvals.position', '=', 'Deputy General Manager')
                ->whereNull('sakurentsu_three_m_approvals.approve_at')
                ->get();

            // dd(count($data_dgm))

            if (count($data_dgm) == 0) {
                // Mail::to($this->gm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
                Mail::to($this->gm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

        }

        $response = array(
            'status' => true,
            'message' => 'sukses',
        );

        return Response::json($response);
    }

    public function approve_tiga_em($id_tiga_em, $position)
    {
        $title = 'Approval 3M Application';
        $title_jp = '3M変更申請の承認';

        $data2 = sakurentsuThreeM::where('id', '=', $id_tiga_em)->first();

        if ($position == 'dgm') {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('approver_id', '=', $this->dgm[0])
                ->where('status', '=', 'approve')
                ->select('approve_at')
                ->first();

            if ($appr->approve_at == null) {
                sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                    ->where('approver_id', '=', $this->dgm[0])
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING GM',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($this->gm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

                // TMP

                // $data = [
                //     "datas" => $data_tiga_em,
                //     "position" => 'SIGNING DGM 2',
                //     "subject" => '3M Application (3M申請書)'
                // ];
                // Mail::to($this->dgm[3])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

        } else if ($position == 'gm') {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('approver_id', '=', $this->gm[0])
                ->select('approve_at')
                ->first();

            if ($appr->approve_at == null) {
                sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                    ->where('approver_id', '=', $this->gm[0])
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'SIGNING DGM 2',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($this->dgm[3])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }
        } else if ($position == 'dgm2') {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('approver_id', '=', $this->dgm[2])
                ->select('approve_at')
                ->first();

            if ($appr->approve_at == null) {
                sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                    ->where('approver_id', '=', $this->dgm[2])
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'PRESDIR',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to('hiromichi.ichimura@music.yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }
        } else if ($position == 'presdir') {
            // sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
            // ->where('position', '=', 'President Director')
            // ->update([
            //     'approve_at' => date('Y-m-d H:i:s')
            // ]);

            // sakurentsuThreeM::where('id', '=', $id_tiga_em)->update([
            //     'remark' => 6
            // ]);

        } else if ($position == 'std') {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('position', '=', 'President Director')
                ->select(db::raw('DATE_FORMAT(approve_at, "%Y-%m-%d") as dt'))
                ->first();

            $no = CodeGenerator::where('note', '3M')->select('index')->first();
            $no_new = $no->index + 1;

            SakurentsuThreeM::where('id', '=', $id_tiga_em)
                ->update([
                    'form_number' => $no_new,
                    'date' => $appr->dt,
                    'remark' => 7,
                ]);

            CodeGenerator::where('note', '=', '3M')
                ->update([
                    'index' => $no_new,
                ]);

            $dept = SakurentsuThreeM::leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                ->where('sakurentsu_three_ms.id', $id_tiga_em)
                ->select('related_department', 'users.email')
                ->first();

            $dept_arr = explode(',', $dept->related_department);

            $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('department', $dept_arr)
                ->whereIn('position', ['Manager', 'Chief', 'Foreman', 'Staff', 'Senior Staff'])
                ->whereNull('end_date')
                ->select('users.email')
                ->get()
                ->toArray();

            foreach ($mail_dept as $ml_dept) {
                $mailtoo[] = $ml_dept['email'];
            }

            if (in_array("Production Engineering Department", $dept_arr)) {
                array_push($mailtoo, 'susilo.basri@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Production Engineering Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }

            } else if (in_array("Purchasing Control Department", $dept_arr)) {
                array_push($mailtoo, 'imron.faizal@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Purchasing Control Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("General Affairs Department", $dept_arr)) {
                array_push($mailtoo, 'prawoto@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'General Affairs Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("Woodwind Instrument - Key Parts Process (WI-KPP) Department", $dept_arr)) {
                array_push($mailtoo, 'khoirul.umam@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            }

            array_push($mailtoo, $dept->email);
            array_push($mailtoo, 'youichi.oyama@music.yamaha.com');

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'form_number', 'date', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.created_by', 'sakurentsu_three_ms.reason', 'started_date', 'sakurentsu_three_ms.title_jp', 'date_note', 'date')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'ALL 1',
                "subject" => '3M Approval (3M変更承認)',
            ];

            $proposer = db::select('SELECT name, remark from send_emails left join users on send_emails.email = users.email where remark = (SELECT department from employee_syncs where employee_id = "' . $data_tiga_em->created_by . '")');

            $name_prop = $proposer[0]->name;

            if ($proposer[0]->remark == "Production Engineering Department") {
                $name_prop = "Susilo Basri Prasetyo";
            } else if ($proposer[0]->remark == "Purchasing Control Department") {
                $name_prop = "Imron Faizal";
            } else if ($proposer[0]->remark == "General Affairs Department") {
                $name_prop = "Prawoto";
            } else if ($proposer[0]->remark == "Woodwind Instrument - Key Parts Process (WI-KPP) Department") {
                $name_prop = "Khoirul Umam";
            }

            $mailto2 = SakurentsuThreeM::leftJoin('users', 'sakurentsu_three_ms.created_by', '=', 'users.username')
                ->where('sakurentsu_three_ms.id', '=', $id_tiga_em)
                ->select('email')
                ->first();

            Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'ALL 2',
                "subject" => '3M Approval (3M変更承認)',
            ];

            Mail::to($mailto2->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            $app = SakurentsuThreeMImplementation::updateOrCreate(
                ['form_id' => $id_tiga_em, 'form_number' => $data_tiga_em->form_number],
                [
                    'form_date' => $data_tiga_em->date,
                    'section' => $proposer[0]->remark,
                    'name' => $name_prop,
                    'title' => $data_tiga_em->title,
                    'reason' => $data_tiga_em->reason,
                    'started_date' => $data_tiga_em->started_date,
                    'date_note' => $data_tiga_em->date_note,
                    'created_by' => 'PI0904007',
                ]
            );

            $app->save();

        } else if ($position == 'dept') {
            $approver = SakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('status', '=', 'approve')
                ->select(db::raw('UPPER(approver_id) as emp_id'))
                ->get();

            $app_arr = [];
            foreach ($approver as $apr) {
                array_push($app_arr, $apr->emp_id);
            }

            if (in_array(strtoupper(Auth::user()->username), $app_arr)) {
                $cek_appr = sakurentsuThreeMApproval::whereNotNull('approver_department')
                    ->where('status', '=', 'approve')
                    ->where('form_id', '=', $id_tiga_em)
                    ->select('approver_department', db::raw('SUM(IF(approve_at is not null, 1,0)) as sign'))
                    ->groupBy('approver_department')
                    ->havingRaw('sign = 0')
                    ->get()
                    ->count();

                if ($cek_appr == 1) {
                    $stat_appr = 1;
                } else {
                    $stat_appr = 0;
                }

                sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                    ->where('approver_id', '=', Auth::user()->username)
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $not_appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                    ->whereNotNull('approver_department')
                    ->where('status', '=', 'approve')
                    ->select('approver_department', db::raw('GROUP_CONCAT(approve_at) as app_date'))
                    ->groupBy('approver_department')
                    ->havingRaw('app_date is null')
                    ->get()
                    ->count();

                $docs = SakurentsuThreeMDocument::where('form_id', $id_tiga_em)
                    ->whereNull('file_name')
                    ->get();

                if ($not_appr == 0 && $stat_appr == 1 && count($docs) == 0) {
                    $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                    $data = [
                        "datas" => $data_tiga_em,
                        "position" => 'SIGNING DGM',
                        "subject" => '3M Application (3M申請書)',
                    ];

                    Mail::to($this->dgm[1])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
                }
            } else {
                return view(
                    'sakurentsu.report.index_approval',
                    array(
                        'title' => $title,
                        'title_jp' => $title_jp,
                        'data' => $data2,
                        'status' => 'Sorry, You Don`t Have Access To Approval',
                    )
                )->with('page', 'Sakurentsu')
                    ->with('head', 'Sakurentsu');
            }
        }

        return view(
            'sakurentsu.report.index_approval',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data2,
                'status' => '3M Request successfully approved',
            )
        )->with('page', 'Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function receive_tiga_em(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', '3M')->first();

            $appr = sakurentsuThreeMApproval::where('form_id', '=', $request->get('form_id'))
                ->where('position', '=', 'President Director')
                ->select(db::raw('DATE_FORMAT(approve_at, "%Y-%m-%d") as dt'))
                ->first();

            SakurentsuThreeM::where('id', $request->get('form_id'))
                ->update(['remark' => 7, 'form_number' => $code_generator->index + 1, 'date' => $appr->dt]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $dept = SakurentsuThreeM::leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                ->where('sakurentsu_three_ms.id', $request->get('form_id'))
                ->select('related_department', 'users.email')
                ->first();

            $dept_arr = explode(',', $dept->related_department);

            $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('department', $dept_arr)
                ->whereIn('position', ['Manager', 'Chief', 'Foreman', 'Staff', 'Senior Staff'])
                ->whereNull('end_date')
                ->select('users.email')
                ->get()
                ->toArray();

            foreach ($mail_dept as $ml_dept) {
                $mailtoo[] = $ml_dept['email'];
            }

            if (in_array("Production Engineering Department", $dept_arr)) {
                array_push($mailtoo, 'susilo.basri@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Production Engineering Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("Purchasing Control Department", $dept_arr)) {
                array_push($mailtoo, 'imron.faizal@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Purchasing Control Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("General Affairs Department", $dept_arr)) {
                array_push($mailtoo, 'prawoto@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'General Affairs Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("Woodwind Instrument - Key Parts Process (WI-KPP) Department", $dept_arr)) {
                array_push($mailtoo, 'khoirul.umam@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            }

            array_push($mailtoo, $dept->email);

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('form_id'))->select('sakurentsu_three_ms.id', 'form_number', 'date', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.created_by', 'sakurentsu_three_ms.reason', 'started_date', 'sakurentsu_three_ms.title_jp', 'date_note')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'ALL',
                "subject" => '3M Application (3M申請書)',
            ];

            $proposer = db::select('SELECT name, remark from send_emails left join users on send_emails.email = users.email where remark = (SELECT department from employee_syncs where employee_id = "' . $data_tiga_em->created_by . '")');

            $name_prop = $proposer[0]->name;

            if ($proposer[0]->remark == "Production Engineering Department") {
                $name_prop = "Susilo Basri Prasetyo";
            } else if ($proposer[0]->remark == "Purchasing Control Department") {
                $name_prop = "Imron Faizal";
            } else if ($proposer[0]->remark == "General Affairs Department") {
                $name_prop = "Prawoto";
            } else if ($proposer[0]->remark == "Woodwind Instrument - Key Parts Process (WI-KPP) Department") {
                $name_prop = "Khoirul Umam";
            }

            Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            $app = SakurentsuThreeMImplementation::updateOrCreate(
                ['form_id' => $request->get('form_id'), 'form_number' => $data_tiga_em->form_number],
                [
                    'form_date' => $data_tiga_em->date,
                    'section' => $proposer[0]->remark,
                    'name' => $name_prop,
                    'title' => $data_tiga_em->title,
                    'reason' => $data_tiga_em->reason,
                    'started_date' => $data_tiga_em->started_date,
                    'date_note' => $data_tiga_em->date_note,
                    'created_by' => 'PI0904007'
                ]
            );

            $app->save();

            $response = array(
                'status' => true,
                'message' => 'sukses',
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

    public function post_tiga_em_implement(Request $request)
    {
        try {
            $files = array();

            if ($request->file('file') != null) {
                if ($files = $request->file('file')) {
                    foreach ($files as $file) {
                        $nama = $request->get('id') . '_' . date('YmdHis') . '.' . $file[0]->getClientOriginalExtension();

                        $file[0]->move('uploads/sakurentsu/three_m/att', $nama);
                        $data[] = $nama;
                    }
                }

                $filename = json_encode($data);
            } else {
                $filename = null;
            }

            $checker_arr = explode(',', $request->get('checker'));
            $checker = [];

            foreach ($checker_arr as $ca) {
                array_push($checker, explode(' - ', $ca)[1]);

            }

            $update = SakurentsuThreeMImplementation::where('form_id', $request->get('id'))
                ->update([
                    'actual_date' => $request->get('actual_date'),
                    'check_date' => $request->get('check_date'),
                    'serial_number' => $request->get('no_seri'),
                    'checker' => implode(',', $checker),
                    'att' => implode(',', (array) $filename),
                    'created_by' => Auth::user()->username,
                ]);

            SakurentsuThreeM::where('id', $request->get('id'))
                ->update([
                    'remark' => 8,
                ]);

            $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

            $mailtoo = Mails::where('send_emails.remark', '=', $dept->department)
                ->leftJoin('users', 'send_emails.email', '=', 'users.email')
                ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
                ->select('send_emails.email', 'employee_id', 'employee_syncs.name', 'department', 'position')
                ->first();

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp', 'related_department')->first();

            $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
                ->where('sakurentsu_three_m_implementations.form_id', '=', $request->get('id'))
                ->select('sakurentsu_three_m_implementations.id', 'sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'employee_syncs.division', 'serial_number')
                ->first();

            // Proposer

            $imp_appr = new SakurentsuThreeMImpApproval();
            $imp_appr->form_id = $request->get('id');
            $imp_appr->implement_id = $data_imp->id;
            $imp_appr->approver_id = $mailtoo->employee_id;
            $imp_appr->approver_name = $mailtoo->name;
            $imp_appr->approver_department = $mailtoo->department;
            $imp_appr->position = $mailtoo->position;
            $imp_appr->remark = 'pic';
            $imp_appr->created_by = Auth::user()->username;

            $imp_appr->save();

            $dpts = explode(',', $data_tiga_em->related_department);

            $chf = Approver::whereRaw('id in (SELECT min(id)
                                   FROM approvers
                                   WHERE remark in ("Foreman", "Chief")
                                   GROUP BY department)')
                ->whereIn('department', $dpts)
                ->whereIn('remark', ['Foreman', 'Chief'])
                ->select('approver_id', 'approver_name', 'department', 'position', 'approver_email')
                ->get();

            foreach ($chf as $chief) {
                $imp_appr = new SakurentsuThreeMImpApproval();
                $imp_appr->form_id = $request->get('id');
                $imp_appr->implement_id = $data_imp->id;
                $imp_appr->approver_id = $chief->approver_id;
                $imp_appr->approver_name = $chief->approver_name;
                $imp_appr->approver_department = $chief->department;
                $imp_appr->position = $chief->position;
                $imp_appr->remark = 'approve';
                $imp_appr->created_by = Auth::user()->username;

                $imp_appr->save();
            }

            // $imp_appr = new SakurentsuThreeMImpApproval();
            // $imp_appr->form_id = $request->get('id');
            // $imp_appr->implement_id = $data_imp->id;
            // $imp_appr->approver_id = $mailtoo->employee_id;
            // $imp_appr->approver_name = $mailtoo->name;
            // $imp_appr->approver_department = $mailtoo->department;
            // $imp_appr->position = $mailtoo->position;
            // $imp_appr->remark = 'approve';
            // $imp_appr->created_by = Auth::user()->username;

            // $imp_appr->save();

            // foreach ($appr as $app) {
            //     $imp_appr = new SakurentsuThreeMImpApproval();
            //     $imp_appr->form_id = $request->get('id');
            //     $imp_appr->implement_id = $data_imp->id;
            //     $imp_appr->approver_id = $app[0];
            //     $imp_appr->approver_name = $app[1];
            //     $imp_appr->approver_department = $app[2];
            //     $imp_appr->position = $app[3];
            //     $imp_appr->remark = 'approve';
            //     $imp_appr->created_by = Auth::user()->username;

            //     $imp_appr->save();
            // }

            // if ($data_imp->division == 'Production Division') {
            //     $dgm_id = $this->dgm[0];
            //     $dgm_name = 'Budhi Apriyanto';

            //     $gm_id = $this->gm[0];
            //     $gm_name = 'Yukitaka Hayakawa';

            //     // TMP
            //     // $gm_id = $this->dgm[0];
            //     // $gm_name = 'Budhi Apriyanto';
            // } else {
            //     $dgm_id = $this->dgm[2];
            //     $dgm_name = 'Mei Rahayu';

            //     $gm_id = $this->dgm[0];
            //     $gm_name = 'Budhi Apriyanto';
            // }

            // //DGM
            // $imp_appr = new SakurentsuThreeMImpApproval();
            // $imp_appr->form_id = $request->get('id');
            // $imp_appr->implement_id = $data_imp->id;
            // $imp_appr->approver_id = $dgm_id;
            // $imp_appr->approver_name = $dgm_name;
            // $imp_appr->position = 'Deputy General Manager';
            // $imp_appr->remark = 'approve';
            // $imp_appr->created_by = Auth::user()->username;
            // $imp_appr->save();

            // //GM
            // $imp_appr = new SakurentsuThreeMImpApproval();
            // $imp_appr->form_id = $request->get('id');
            // $imp_appr->implement_id = $data_imp->id;
            // $imp_appr->approver_id = $gm_id;
            // $imp_appr->approver_name = $gm_name;
            // $imp_appr->position = 'General Manager';
            // $imp_appr->remark = 'approve';
            // $imp_appr->created_by = Auth::user()->username;
            // $imp_appr->save();

            //STD
            $imp_appr = new SakurentsuThreeMImpApproval();
            $imp_appr->form_id = $request->get('id');
            $imp_appr->implement_id = $data_imp->id;
            $imp_appr->approver_id = 'PI0904007';
            $imp_appr->approver_name = 'Rani Nurdiyana Sari';
            $imp_appr->remark = 'std';
            $imp_appr->created_by = Auth::user()->username;
            $imp_appr->save();

            $data = [
                "datas" => $data_tiga_em,
                "implement" => $data_imp,
                "position" => 'IMPLEMENT',
                "subject" => '3M Application (3M申請書)',
            ];

            Mail::to($mailtoo->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            $response = array(
                'status' => true,
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

    public function signing_implement_tiga_em($id_tiga_em, $category)
    {
        $title = 'Approval 3M Implementation';
        $title_jp = '3M変更 運用の承認';

        $data2 = sakurentsuThreeM::where('id', '=', $id_tiga_em)->first();

        if ($category == 'proposer') {
            $app = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->where('remark', '=', 'pic')
                ->select('approve_at')
                ->first();

            if ($app->approve_at == null) {
                SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                    ->where('remark', '=', 'pic')
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $mailtoo = SakurentsuThreeMImpApproval::leftJoin('users', 'users.username', '=', 'sakurentsu_three_m_imp_approvals.approver_id')
                    ->where('form_id', '=', $id_tiga_em)
                    ->whereNotNull('approver_department')
                    ->where('sakurentsu_three_m_imp_approvals.remark', '<>', 'pic')
                    ->select('email')
                    ->get();

                $mailto = [];

                foreach ($mailtoo as $mlto) {
                    $mailto[] = $mlto['email'];
                }

                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
                    ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
                    ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
                    ->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "implement" => $data_imp,
                    "position" => 'IMPLEMENT DEPT',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($mailto)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

            return view(
                'sakurentsu.report.index_approval',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'data' => $data2,
                    'status' => '3M Implementation Form successfully approved',
                )
            )->with('page', 'Sakurentsu')
                ->with('head', 'Sakurentsu');
        } else if ($category == 'dept') {
            $approver = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->whereIn('remark', ['approve', 'pic'])
                ->select(db::raw('UPPER(approver_id) as emp_id'))->get();

            $app_arr = [];
            foreach ($approver as $apr) {
                array_push($app_arr, $apr->emp_id);
            }

            if (in_array(strtoupper(Auth::user()->username), $app_arr)) {
                $cek_appr = SakurentsuThreeMImpApproval::whereNotNull('approver_department')
                    ->where('remark', '=', 'approve')
                    ->where('form_id', '=', $id_tiga_em)
                    ->whereNull('approve_at')
                    ->select('approver_id', 'approver_name', 'approve_at')
                    ->get()
                    ->count();

                if ($cek_appr == 1) {
                    $stat_appr = 1;
                } else {
                    $stat_appr = 0;
                }

                SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                    ->where('approver_id', '=', Auth::user()->username)
                    ->update([
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                $not_appr = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                    ->whereNotNull('approver_department')
                    ->where('remark', '=', 'approve')
                    ->whereNull('approve_at')
                    ->select('approver_id', 'approver_name', 'approve_at')
                    ->get()
                    ->count();

                if ($not_appr == 0 && $stat_appr == 1) {
                    SakurentsuThreeM::where('id', '=', $id_tiga_em)
                        ->update([
                            'remark' => 9,
                        ]);

                    $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                    $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
                        ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
                        ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
                        ->first();

                    $data = [
                        "datas" => $data_tiga_em,
                        "implement" => $data_imp,
                        "position" => 'IMPLEMENT STD',
                        "subject" => '3M Application (3M申請書)',
                    ];

                    Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
                }

                return view(
                    'sakurentsu.report.index_approval',
                    array(
                        'title' => $title,
                        'title_jp' => $title_jp,
                        'data' => $data2,
                        'status' => '3M Implementation Form successfully approved',
                    )
                )->with('page', 'Sakurentsu')
                    ->with('head', 'Sakurentsu');
            } else {
                // Bukan yang approve

                return view(
                    'sakurentsu.report.index_approval',
                    array(
                        'title' => $title,
                        'title_jp' => $title_jp,
                        'data' => $data2,
                        'status' => 'Sorry, You Don`t Have Access To Approval',
                    )
                )->with('page', 'Sakurentsu')
                    ->with('head', 'Sakurentsu');
            }
        }
        // else if ($category == 'dgm') {
        //                 $app = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
        //                 ->where('position', '=', 'Deputy General Manager')
        //                 ->select('approve_at')
        //                 ->first();

        //                 if ($app->approve_at == null) {
        //                     SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
        //                     ->where('position', '=', 'Deputy General Manager')
        //                     ->update([
        //                         'approve_at' => date('Y-m-d H:i:s'),
        //                     ]);

        //                     $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

        //                     $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
        //                     ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
        //                     ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
        //                     ->first();

        //                     $data = [
        //                         "datas" => $data_tiga_em,
        //                         "implement" => $data_imp,
        //                         "position" => 'IMPLEMENT GM',
        //                         "subject" => '3M Application (3M申請書)',
        //                     ];

        // //mailto
        //                     $get_gm = SakurentsuThreeMImpApproval::leftJoin('users', 'sakurentsu_three_m_imp_approvals.approver_id', '=', 'users.username')
        //                     ->where('form_id', '=', $id_tiga_em)
        //                     ->where('position', '=', 'General Manager')
        //                     ->select('email')
        //                     ->first();

        //                     Mail::to($get_gm->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
        //                 }
        //                 return view('sakurentsu.report.index_approval', array(
        //                     'title' => $title,
        //                     'title_jp' => $title_jp,
        //                     'data' => $data2,
        //                     'status' => '3M Implementation Form successfully approved',
        //                 ))->with('page', 'Sakurentsu')
        //                 ->with('head', 'Sakurentsu');
        //             } else if ($category == 'gm') {
        //                 $app = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
        //                 ->where('position', '=', 'General Manager')
        //                 ->select('approve_at')
        //                 ->first();

        //                 if ($app->approve_at == null) {
        //                     SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
        //                     ->where('position', '=', 'General Manager')
        //                     ->update([
        //                         'approve_at' => date('Y-m-d H:i:s'),
        //                     ]);

        //                     SakurentsuThreeM::where('id', '=', $id_tiga_em)
        //                     ->update([
        //                         'remark' => 9,
        //                     ]);

        //                     $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

        //                     $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
        //                     ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
        //                     ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
        //                     ->first();

        //                     $data = [
        //                         "datas" => $data_tiga_em,
        //                         "implement" => $data_imp,
        //                         "position" => 'IMPLEMENT STD',
        //                         "subject" => '3M Application (3M申請書)',
        //                     ];

        //                     Mail::to(['rani.nurdiyana.sari@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
        //                 }

        //                 return view('sakurentsu.report.index_approval', array(
        //                     'title' => $title,
        //                     'title_jp' => $title_jp,
        //                     'data' => $data2,
        //                     'status' => '3M Implementation Form successfully approved',
        //                 ))->with('page', 'Sakurentsu')
        //                 ->with('head', 'Sakurentsu');
        //             }
        else if ($category == 'std') {
            $app = SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->where('remark', '=', 'std')
                ->select('approve_at')
                ->first();

            if ($app->approve_at == null) {
                SakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                    ->where('remark', '=', 'std')
                    ->update([
                        'approver_id' => strtoupper(Auth::user()->username),
                        'approver_name' => Auth::user()->name,
                        'approve_at' => date('Y-m-d H:i:s'),
                    ]);

                SakurentsuThreeM::where('id', '=', $id_tiga_em)
                    ->update([
                        'remark' => 10,
                    ]);

                $sk = Sakurentsu::where('sakurentsu_number', '=', $data2->sakurentsu_number)
                    ->first();

                if (count($sk) > 0) {
                    $sk->update([
                        'status' => 'close',
                    ]);
                }
            }

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
                ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
                ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
                ->first();

            $data2 = $data_tiga_em;

            $data = [
                "datas" => $data_tiga_em,
                "implement" => $data_imp,
                "position" => 'IMPLEMENT INFORMATION',
                "subject" => '3M Application (3M申請書)',
            ];

            $dept = SakurentsuThreeM::leftJoin('users', 'users.username', '=', 'sakurentsu_three_ms.created_by')
                ->where('sakurentsu_three_ms.id', $id_tiga_em)
                ->select('related_department', 'users.email')
                ->first();

            $dept_arr = explode(',', $dept->related_department);

            $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                ->whereIn('department', $dept_arr)
                ->whereIn('position', ['Manager', 'Chief', 'Foreman', 'Staff', 'Senior Staff'])
                ->whereNull('end_date')
                ->select('users.email')
                ->get()
                ->toArray();

            foreach ($mail_dept as $ml_dept) {
                $mailtoo[] = $ml_dept['email'];
            }

            if (in_array("Production Engineering Department", $dept_arr)) {
                array_push($mailtoo, 'susilo.basri@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Production Engineering Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }

            } else if (in_array("Purchasing Control Department", $dept_arr)) {
                array_push($mailtoo, 'imron.faizal@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Purchasing Control Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("General Affairs Department", $dept_arr)) {
                array_push($mailtoo, 'prawoto@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'General Affairs Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            } else if (in_array("Woodwind Instrument - Key Parts Process (WI-KPP) Department", $dept_arr)) {
                array_push($mailtoo, 'khoirul.umam@music.yamaha.com');

                $mail_dept = EmployeeSync::leftJoin('users', 'users.username', '=', 'employee_syncs.employee_id')
                    ->where('department', 'Woodwind Instrument - Key Parts Process (WI-KPP) Department')
                    ->whereIn('position', ['Chief', 'Foreman', 'Staff', 'Senior Staff'])
                    ->whereNull('end_date')
                    ->select('users.email')
                    ->get()
                    ->toArray();

                foreach ($mail_dept as $ml_dept) {
                    array_push($mailtoo, $ml_dept['email']);
                }
            }

            array_push($mailtoo, $dept->email);

            Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

            return view(
                'sakurentsu.report.index_approval',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'data' => $data2,
                    'status' => '3M Implementation Form successfully approved',
                )
            )->with('page', 'Sakurentsu')
                ->with('head', 'Sakurentsu');
        }
    }

    public function fetch_tiga_em_monitoring(Request $request)
    {
        $where = '';
        $where2 = '';
        $where3 = '';

        if ($request->get('sakurentsu_number')) {
            $where = ' AND s.sakurentsu_number = "' . $request->get('sakurentsu_number') . '"';
            $where2 = ' AND form_identity_number = "' . $request->get('sakurentsu_number') . '"';
            $where3 = ' AND form_number = "' . $request->get('sakurentsu_number') . '"';
        }

        $user = strtoupper(Auth::user()->username);

        if ($request->get('posisi')) {
            if ($request->get('posisi') == '3M Implementation') {
                if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {
                    $app = db::select('SELECT form_identity_number, sakurentsu_number, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_id) as total from
                        (SELECT sakurentsu_three_ms.id ,sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_id, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                            left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                            where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark = "approve" and approver_department is not null
                            GROUP BY sakurentsu_three_ms.id, sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_id) appr
                        JOIN (SELECT form_id from sakurentsu_three_m_imp_approvals where approver_id = "' . $user . '" and approve_at is null
                            GROUP BY form_id
                            ) as emp on appr.id = emp.form_id AND form_id is not null
                            GROUP BY form_identity_number, sakurentsu_number
                            having sudah >= total');
                } else {
                    $app = db::select('SELECT * from
                        (SELECT sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_id, approve_at as app from sakurentsu_three_ms
                            left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                            where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark in ( "approve", "pic" )
                            ) appr
                        where app is null and approver_id  = "' . $user . '"');

                    // dd($app);
                }

                if (count($app) > 0) {
                    $app_arr = [];
                    foreach ($app as $ap) {
                        if ($ap->sakurentsu_number) {
                            array_push($app_arr, "'" . $ap->sakurentsu_number . "'");
                        } else {
                            array_push($app_arr, "'" . $ap->form_identity_number . "'");
                        }
                    }

                    $app_string = implode(',', $app_arr);

                    $where = ' AND s.sakurentsu_number in (' . $app_string . ')';
                    $where2 = ' AND form_identity_number in (' . $app_string . ')';
                    $where3 = ' AND form_number in (' . $app_string . ')';
                }
            } else if ($request->get('posisi') == 'Approval 3M Application') {
                if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {
                    $app = db::select('SELECT form_identity_number, sakurentsu_number, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_department) as total from
                        (SELECT sakurentsu_three_ms.id, sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                            left join sakurentsu_three_m_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_approvals.form_id
                            where sakurentsu_three_ms.remark = 5 and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is null and position <> "President Director"
                            GROUP BY sakurentsu_three_ms.id, sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_department) appr
                        JOIN (SELECT form_id from sakurentsu_three_m_approvals where approver_id = "' . $user . '" and approve_at is null and approver_division is not null
                            GROUP BY form_id
                            ) as emp on appr.id = emp.form_id
                            GROUP BY form_identity_number, sakurentsu_number
                            having sudah >= total');
                } else {
                    $app = db::select('SELECT * from
                        (SELECT sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                            left join sakurentsu_three_m_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_approvals.form_id
                            where sakurentsu_three_ms.remark = 5 and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is null and position <> "President Director"
                            GROUP BY sakurentsu_three_ms.form_identity_number, sakurentsu_number, approver_department) appr
                        where app is null and approver_department in (SELECT department from approvers where approver_id = "' . $user . '" )');
                }

                if (count($app) > 0) {
                    $app_arr = [];
                    foreach ($app as $ap) {
                        if ($ap->sakurentsu_number) {
                            array_push($app_arr, "'" . $ap->sakurentsu_number . "'");
                        } else {
                            array_push($app_arr, "'" . $ap->form_identity_number . "'");
                        }
                    }

                    $app_string = implode(',', $app_arr);

                    $where = ' AND s.sakurentsu_number in (' . $app_string . ')';
                    $where2 = ' AND form_identity_number in (' . $app_string . ')';
                    $where3 = ' AND form_number in (' . $app_string . ')';
                }
            } else if ($request->get('posisi') == 'Trial Request Issue') {
                $chief = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and chief LIKE '" . $user . "%' and chief_date is null and deleted_at is null");

                $manager = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and manager LIKE '" . $user . "%' and manager_date is null and chief_date is not null and deleted_at is null");

                $manager_mecha = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and manager_mechanical LIKE '" . $user . "%' and manager_mechanical_date is null and manager_date is not null and deleted_at is null");

                $dgm = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and dgm LIKE '" . $user . "%' and dgm_date is null and (manager_mechanical is null OR manager_mechanical_date is not null) and manager_date is not null and deleted_at is null");

                $gm = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and gm LIKE '" . $user . "%' and gm_date is null and dgm_date is not null and deleted_at is null");

                $dgm2 = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and dgm2 LIKE '" . $user . "%' and dgm_date2 is null and gm_date is not null and deleted_at is null");

                $gm2 = db::SELECT("SELECT form_number, sakurentsu_number from sakurentsu_trial_requests where `status` = 'approval' and gm2 LIKE '" . $user . "%' and gm_date2 is null and deleted_at is null");

                $app = [];

                if (count($chief) > 0) {
                    $app = $chief;
                } else if (count($manager) > 0) {
                    $app = $manager;
                } else if (count($manager_mecha) > 0) {
                    $app = $manager_mecha;
                } else if (count($dgm) > 0) {
                    $app = $dgm;
                } else if (count($gm) > 0) {
                    $app = $gm;
                } else if (count($dgm2) > 0) {
                    $app = $dgm2;
                } else if (count($gm2) > 0) {
                    $app = $gm2;
                }

                $app_arr = [];
                if (count($app)) {
                    foreach ($app as $ap) {
                        if ($ap->sakurentsu_number) {
                            array_push($app_arr, "'" . $ap->sakurentsu_number . "'");
                        } else {
                            array_push($app_arr, "'" . $ap->form_number . "'");
                        }
                    }

                    $app_string = implode(',', $app_arr);

                    $where = ' AND s.sakurentsu_number in (' . $app_string . ')';
                    $where2 = ' AND form_identity_number in (' . $app_string . ')';
                    $where3 = ' AND form_number in (' . $app_string . ')';
                }
            } else if ($request->get('posisi') == 'Trial Request Receive') {
                $app = db::select("SELECT sakurentsu_trial_request_receives.trial_id, sakurentsu_trial_requests.sakurentsu_number from sakurentsu_trial_requests
                    left join sakurentsu_trial_request_receives on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_receives.trial_id
                    where sakurentsu_trial_requests.status = 'receiving'
                    AND ((sakurentsu_trial_request_receives.chief LIKE '" . $user . "%' and sakurentsu_trial_request_receives.chief_date is null and sakurentsu_trial_request_receives.manager_date is not null) OR (sakurentsu_trial_request_receives.manager LIKE '" . $user . "%' and sakurentsu_trial_request_receives.manager_date is null))");

                if (count($app) > 0) {
                    $app_arr = [];
                    foreach ($app as $ap) {
                        if ($ap->sakurentsu_number) {
                            array_push($app_arr, "'" . $ap->sakurentsu_number . "'");
                        } else {
                            array_push($app_arr, "'" . $ap->trial_id . "'");
                        }
                    }

                    $app_string = implode(',', $app_arr);

                    $where = ' AND s.sakurentsu_number in (' . $app_string . ')';
                    $where2 = ' AND form_identity_number in (' . $app_string . ')';
                    $where3 = ' AND form_number in (' . $app_string . ')';
                }
            }
        }

        $data_sk = db::select("SELECT s.sakurentsu_number, sm.id as id_tiga_em, s.title_jp, s.title, DATE_FORMAT(s.target_date,'%d %b %y') as target_dt, DATE_FORMAT(sm.started_date,'%d %b %y') as target_real_dt, s.translator as trans_sk, users.name as manager_dept,s.pic, s.category, s.additional_file, sm.created_by, sm.translator as trans_m, sm.remark, simp.checker, es.name, sm.related_department, s.position, s.remark as remark_s, sm.title as title_tiga_em, sts.trial_file, sts.remark as trial_remark, sts.pss_desc, sts.pss_file, sts.form_number from sakurentsus s
            left join sakurentsu_three_ms sm on s.sakurentsu_number = sm.sakurentsu_number
            left join sakurentsu_three_m_implementations simp on simp.form_id = sm.id
            left join employee_syncs es on es.employee_id = sm.created_by
            left join send_emails on send_emails.remark = s.pic
            left join users on users.email = send_emails.email
            left join sakurentsu_trial_statuses sts on s.sakurentsu_number = sts.sakurentsu_number
            where s.status <> 'close' AND s.deleted_at is null AND sm.deleted_at is null " . $where . "
            order by s.target_date asc");

        $docs = db::select("SELECT form_id, count(document_name) doc_all, SUM(IF(finish_date is null, 0, 1)) as doc_uploaded from (
            select form_id, document_name, GROUP_CONCAT(finish_date) as finish_date from sakurentsu_three_m_documents
            group by form_id, document_name) as doc1
        group by form_id");

        $sign_appr = db::select("SELECT form_id, SUM(dpt) as dpt, SUM(dpt_app) as dpt_app, SUM(dgm) as dgm, SUM(dgm_app) as dgm_app, SUM(gm) as gm, SUM(gm_app) as gm_app, SUM(presdir) as presdir, GROUP_CONCAT(approve_dt) as dt from
            (select dpt_data.form_id, SUM(dpt_app) as dpt, count(appr.approver_department) as dpt_app, 0 as dgm, 0 as dgm_app, 0 as gm, 0 as gm_app, 0 as presdir, '' as approve_dt from
                (select form_id, approver_department from sakurentsu_three_m_approvals
                    where approver_department is not null and status <> 'pic' and approve_at is not null
                    group by form_id, approver_department) as appr
                right join (
                    select form_id, approver_department, 1 as dpt_app from sakurentsu_three_m_approvals
                    where approver_department is not null and status <> 'pic'
                    group by form_id, approver_department
                    ) as dpt_data on appr.form_id = dpt_data.form_id and appr.approver_department = dpt_data.approver_department
                group by dpt_data.form_id
                union all
                select form_id,
                0 as dpt,
                0 as dpt_app,
                SUM(IF(position = 'Deputy General Manager', 1, 0)) as dgm,
                SUM(IF(position = 'Deputy General Manager' and approve_at is not null,1,0)) as dgm_app,
                SUM(IF(position = 'General Manager', 1, 0)) as gm,
                SUM(IF(position = 'General Manager' and approve_at is not null,1,0)) as gm_app,
                SUM(IF(position = 'President Director', 1, 0)) as presdir,
                GROUP_CONCAT(IF(position = 'President Director' and approve_at is not null,DATE_FORMAT(created_at,'%d %b %y'),'')) as approve_dt
                from sakurentsu_three_m_approvals
                group by form_id) appr_all
            group by form_id");

        $sign_imp = db::select("SELECT form_id, SUM(dpt) as dpt, SUM(dpt_app) as dpt_app, SUM(dgm) as dgm, SUM(dgm_app) as dgm_app, SUM(gm) as gm, SUM(gm_app) as gm_app from
            (
                SELECT dpt_data.form_id, SUM(dpt_app) as dpt, count(appr.approver_id) as dpt_app, 0 as dgm, 0 as dgm_app, 0 as gm, 0 as gm_app from
                (select form_id, approver_id, 1 as dpt_app from sakurentsu_three_m_imp_approvals
                    where approver_department is not null) dpt_data
                left join
                (select form_id, approver_id from sakurentsu_three_m_imp_approvals
                    where approver_department is not null and approve_at is not null) as appr on dpt_data.form_id = appr.form_id and dpt_data.approver_id = appr.approver_id
                group by dpt_data.form_id
                UNION ALL
                select form_id,
                0 as dpt,
                0 as dpt_app,
                SUM(IF(position = 'Deputy General Manager', 1, 0)) as dgm,
                SUM(IF(position = 'Deputy General Manager' and approve_at is not null,1,0)) as dgm_app,
                SUM(IF(position = 'General Manager', 1, 0)) as gm,
                SUM(IF(position = 'General Manager' and approve_at is not null,1,0)) as gm_app
                from sakurentsu_three_m_imp_approvals
                group by form_id
                ) appr_all
            group by form_id");

        $data_trial = db::select("SELECT sakurentsu_trial_requests.form_number, submission_date, proposer, sakurentsu_trial_requests.sakurentsu_number, date_format(trial_date, '%d %b %y') as trial_dt, sakurentsu_trial_requests.status as form_status, qc_report_file, qc_report_status, sakurentsu_trial_statuses.remark, sakurentsu_trial_statuses.status as status_trial, sakurentsu_trial_statuses.remark, sakurentsu_trial_requests.three_m_status from sakurentsu_trial_statuses
            left join sakurentsu_trial_requests on sakurentsu_trial_requests.form_number = sakurentsu_trial_statuses.form_number");

        $data_trial_receive = db::select("SELECT trial_id, sakurentsu_trial_request_receives.sakurentsu_number, SUM(1) as belum, SUM(IF(sakurentsu_trial_request_receives.chief_date is not null, 1,0)) as sudah, sakurentsu_trial_requests.status FROM `sakurentsu_trial_request_receives`
            LEFT JOIN sakurentsu_trial_requests on sakurentsu_trial_request_receives.trial_id = sakurentsu_trial_requests.form_number
            GROUP BY trial_id, sakurentsu_trial_request_receives.sakurentsu_number, sakurentsu_trial_requests.status");

        $data_trial_result = db::select('SELECT trial_id, sakurentsu_number, count(trial_id) as must_fill, SUM(IF(trial_method is not null, 1, 0)) as fill FROM `sakurentsu_trial_request_results`
            group by trial_id, sakurentsu_number');

        $data_chart = db::select("SELECT
            date_format( target_date, '%b %Y' ) AS mon,
            SUM( IF ( category = 'Sakurentsu' AND stts <> 'Close', 1, 0 ) ) AS sk_open,
            SUM( IF ( category = 'Sakurentsu' AND stts = 'Close', 1, 0 ) ) AS sk_close,
            SUM( IF ( category = '3M' AND stts <> 'Close', 1, 0 ) ) AS tiga_open,
            SUM( IF ( category = '3M' AND stts = 'Close', 1, 0 ) ) AS tiga_close,
            SUM( IF ( category = 'Trial' AND stts <> 'Close', 1, 0 ) ) AS trial_open,
            SUM( IF ( category = 'Trial' AND stts = 'Close', 1, 0 ) ) AS trial_close,
            SUM( IF ( category = 'Information' AND stts <> 'Close', 1, 0 ) ) AS info_open,
            SUM( IF ( category = 'Information' AND stts = 'Close', 1, 0 ) ) AS info_close
            FROM
            (
                SELECT
                sakurentsu_number,
                'Sakurentsu' AS category,
                target_date,
                IF
                ( `status` IN ( 'created', 'Close' ), 'Close', 'Open' ) AS stts
                FROM
                sakurentsus
                WHERE
                sakurentsus.deleted_at IS NULL UNION ALL
                SELECT
                sakurentsus.sakurentsu_number,
                sakurentsus.category,
                sakurentsu_three_ms.started_date AS target_date,
                IF
                ( sakurentsu_three_ms.remark = 10, 'Close', 'Open' ) AS stts
                FROM
                sakurentsus
                LEFT JOIN sakurentsu_three_ms ON sakurentsu_three_ms.sakurentsu_number = sakurentsus.sakurentsu_number
                WHERE
                sakurentsus.deleted_at IS NULL
                AND sakurentsus.category = '3M'
                AND sakurentsu_three_ms.deleted_at IS NULL
                AND sakurentsus.`status` IN ( 'created', 'Close' )
                UNION ALL
                SELECT
                sakurentsus.sakurentsu_number,
                sakurentsus.category,
                IFNULL(trial_date,sakurentsus.target_date) AS target_date,
                IF
                ( sakurentsus.STATUS = 'close', 'Close', 'Open' ) AS stts
                FROM
                sakurentsus
                LEFT JOIN sakurentsu_trial_requests ON sakurentsu_trial_requests.sakurentsu_number = sakurentsus.sakurentsu_number
                WHERE
                sakurentsus.deleted_at IS NULL
                AND sakurentsus.category = 'Trial'
                AND sakurentsus.`status` <> 'determined'
                UNION ALL
                SELECT
                sakurentsus.sakurentsu_number,
                sakurentsus.category,
                sakurentsus.target_date,
                IF
                ( sakurentsus.STATUS = 'close', 'Close', 'Open' ) AS stts
                FROM
                sakurentsus
                WHERE
                sakurentsus.deleted_at IS NULL
                AND sakurentsus.category = 'Information'
                AND sakurentsus.`status` IN ( 'created', 'Close' )
                ) mstr
            GROUP BY
            date_format( target_date, '%b %Y' )
            ORDER BY
            target_date ASC");

        $appr_info = db::select("SELECT sakurentsus.sakurentsu_number, count(sakurentsu_informations.department) as appr_dept from sakurentsus
            left join sakurentsu_informations on sakurentsus.sakurentsu_number = sakurentsu_informations.sakurentsu_number
            where category = 'Information' AND `status` <> 'close' AND pic IS NOT NULL
            group by sakurentsu_number");

        $appr_info = db::select("SELECT sakurentsus.sakurentsu_number, count(sakurentsu_informations.department) as appr_dept from sakurentsus
            left join sakurentsu_informations on sakurentsus.sakurentsu_number = sakurentsu_informations.sakurentsu_number
            where category = 'Information' AND `status` <> 'close' AND pic IS NOT NULL
            group by sakurentsu_number");

        $appr_trial_issue = db::select("SELECT form_number, sakurentsu_number, chief+manager+manager_mc+dgm+gm+gm2 as approval, manager_appr+manager_mc_appr+dgm_appr+gm_appr+gm2_appr+chief_appr as approved from (
            SELECT form_number, sakurentsu_number, IF(chief is null, 0, 1) as chief, IF(manager is null, 0, 1) as manager, IF(manager_mechanical is null, 0, 1) as manager_mc, IF(dgm is null, 0, 1) as dgm, IF(gm is null, 0, 1) as gm, IF(gm2 is null, 0, 1) as gm2, IF(chief_date is null, 0, 1) as chief_appr, IF(manager_date is null, 0, 1) as manager_appr, IF(manager_mechanical_date is null, 0, 1) as manager_mc_appr, IF(dgm_date is null, 0, 1) as dgm_appr, IF(gm_date is null, 0, 1) as gm_appr, IF(gm_date2 is null, 0, 1) as gm2_appr from sakurentsu_trial_requests where `status` in ('approval','received','receiving','resulting','reporting', 'approval final', '3m_need', '3m', '3m created')) as trial_request");

        $tiga_em_trial = db::select("SELECT id as id_tiga_em,form_identity_number, sakurentsu_number, remark, `status`, trial_id, translator, name, related_department FROM sakurentsu_three_ms
            LEFT JOIN employee_syncs on employee_syncs.employee_id = sakurentsu_three_ms.created_by
            where remark <= 10 and trial_id is not null and sakurentsu_three_ms.deleted_at is null");

        // ----------------  INTERNAL 3M  -----------------
        $internal = db::select("SELECT tiga_em.id as ids, form_identity_number as form_number, tiga_em.title, tiga_em.title_jp, DATE_FORMAT(tiga_em.started_date,'%d %b %y') as target_real_dt, null as target_dt, employee_syncs.name, related_department, translator as trans_m, tiga_em.remark, '3M' as kategori, CONCAT(appr.approver_id,'/',appr.approver_name) as manager, `status` as form_status, '-' as qc_report_status from sakurentsu_three_ms as tiga_em
            left join employee_syncs on employee_syncs.employee_id = tiga_em.created_by
            left join (SELECT * from approvers where remark = 'Manager') as appr on employee_syncs.department = appr.department
            left join sakurentsu_three_m_implementations simp on simp.form_id = tiga_em.id
            where sakurentsu_number is null and tiga_em.remark <= 10 AND tiga_em.deleted_at is null " . $where2 . "
            union all
            select id as ids, form_number, `subject` as title, '-' as title_jp, DATE_FORMAT(trial_date,'%d %b %y') as target_real_dt, null as target_dt, requester_name as `name`,'-' as related_department, '-' as trans_m, three_m_status as remark, 'Trial' as kategori, manager, `status` as form_status, qc_report_status from sakurentsu_trial_requests where (sakurentsu_number in ('-', '') OR sakurentsu_number is null OR sakurentsu_number not in (SELECT sakurentsu_number from sakurentsus)) AND reject is null AND `status` <> 'close' and sakurentsu_trial_requests.deleted_at is null " . $where3);

        $response = array(
            'status' => true,
            'data_sakurentsu' => $data_sk,
            'data_approve' => $sign_appr,
            'data_doc' => $docs,
            'data_sign_imp' => $sign_imp,
            'data_trial' => $data_trial,
            'sign_trial_issue' => $appr_trial_issue,
            'sign_trial_receive' => $data_trial_receive,
            'sign_trial_result' => $data_trial_result,
            'data_info' => $appr_info,
            'data_chart' => $data_chart,
            'data_tiga_em_trial' => $tiga_em_trial,
            'internal' => $internal,
        );
        return Response::json($response);
    }

    public function fetch_department_sign($id_tiga_em, $stat)
    {
        $rel_dpt = SakurentsuThreeM::select('related_department')->where('id', '=', $id_tiga_em)->first()->toArray();

        $rd = explode(',', $rel_dpt['related_department']);
        $before = [];
        $dept_appr = [];

        if ($stat == "approval") {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->whereIn('approver_department', $rd)
                ->where('status', '=', 'approve')
                ->select(db::raw('GROUP_CONCAT(approver_name) as approver_name'), 'approver_department', db::raw('COUNT(approve_at) as app'))
                ->groupBy('approver_department')
                ->get();
        } else if ($stat == "imp_approval") {
            $appr = sakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->whereNotNull('approver_department')
                ->select('approver_name', 'approver_department', db::raw('IF(approve_at is not null,1,0) as app'))
                ->orderBy('remark', 'desc')
                ->get();
        } else if ($stat == 'approval_gm') {
            $appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->whereNull('approver_department')
                ->where('position', '<>', 'President Director')
                ->select('approver_id', 'approver_name', 'position', 'approve_at')
                ->orderBy('position', 'asc')
                ->get();

            $dept_appr = sakurentsuThreeMApproval::where('form_id', '=', $id_tiga_em)
                ->where('status', '=', 'approve')
                ->whereNull('approver_division')
                ->where('position', '<>', 'President Director')
                ->select('approver_department', db::raw('GROUP_CONCAT(approve_at) as app'))
                ->groupBy('approver_department')
                ->get();
        } else if ($stat == 'imp_approval_up') {
            $appr = sakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->whereNull('approver_department')
                ->where('remark', '=', 'approve')
                ->select('approver_id', 'approver_name', 'position', 'approve_at')
                ->orderBy('position', 'asc')
                ->get();

            $dept_appr = sakurentsuThreeMImpApproval::where('form_id', '=', $id_tiga_em)
                ->whereNotNull('approver_department')
                ->select('approver_name', 'approver_department', db::raw('IF(approve_at is not null,1,0) as app'))
                ->get();
        }

        $response = array(
            'status' => true,
            'related_department' => $rd,
            'data_approve' => $appr,
            'dept_approve' => $dept_appr,
            'remark' => $stat,
        );
        return Response::json($response);
    }

    public function post_trial_request($sk_number)
    {
        try {
            $usr = Sakurentsu::leftJoin('send_emails', 'send_emails.remark', '=', 'sakurentsus.pic')
                ->leftJoin('users', 'users.email', '=', 'send_emails.email')
                ->where('sakurentsu_number', '=', $sk_number)->select('pic', 'name', 'username')->first();

            $trial_stat = SakurentsuTrialStatus::firstOrNew(array('sakurentsu_number' => $sk_number));
            $trial_stat->sakurentsu_number = $sk_number;
            $trial_stat->proposer = $usr->name;
            $trial_stat->department = $usr->pic;
            $trial_stat->remark = 'Trial InProgress';
            $trial_stat->save();

            $message = "Sakurentsu Trial Request Nomor " . $sk_number;
            $message2 = "Berhasil diterima";
            return view(
                'sakurentsu.trial_request.trial_message',
                array(
                    'sk_number' => $sk_number,
                    'message' => $message,
                    'message2' => $message2,
                )
            )->with('page', 'Approval');
        } catch (QueryException $e) {
            $message = "";
            return view(
                'sakurentsu.trial_request.trial_message',
                array(
                    'sk_number' => $sk_number,
                    'message' => 'Error',
                    'message2' => $e->getMessage(),
                )
            )->with('page', 'Approval');
        }
    }

    public function fetch_trial_request2(Request $request)
    {
        $sakurentsu_req = Sakurentsu::leftJoin('sakurentsu_trial_statuses', 'sakurentsu_trial_statuses.sakurentsu_number', '=', 'sakurentsus.sakurentsu_number')
            ->where('category', '=', "Trial")
            ->where('sakurentsus.status', '=', 'determined')
            ->whereNull('department')
            ->select('sakurentsus.sakurentsu_number', 'title', 'applicant', 'file_translate', 'upload_date', 'target_date', 'sakurentsus.status', 'pic')
            ->get();

        $dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

        $trial_list = SakurentsuTrialStatus::select('id', 'sakurentsu_number', 'subject', db::raw('date_format(created_at,"%d %b %y") as dt'), 'trial_file', 'remark', 'department')->get();

        $response = array(
            'status' => true,
            'requested' => $sakurentsu_req,
            'trial_list' => $trial_list,
            'dept' => $dept,
        );
        return Response::json($response);
    }

    public function upload_trial_request(Request $request)
    {
        $files = array();

        if ($request->file('file') != null) {
            if ($files = $request->file('file')) {
                $num = 1;
                foreach ($files as $file) {
                    $nama = $request->get('sakurentsu_number') . '_TRIAL_' . $num . '.' . $file->getClientOriginalExtension();

                    $file->move('uploads/sakurentsu/trial_req', $nama);
                    $data[] = $nama;

                    $num++;
                }
            }

            $filename = json_encode($data);
        } else {
            $filename = null;
        }

        $sk = Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->first();

        if ($sk->remark == 'PSS') {
            SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                ->update([
                    'trial_file' => $filename,
                    'status' => $request->get('tiga_em_need'),
                    'remark' => 'Wait PSS',
                ]);

            $isimail = "SELECT sakurentsus.*, sakurentsu_trial_statuses.trial_file from sakurentsus
        left join sakurentsu_trial_statuses on sakurentsus.sakurentsu_number = sakurentsu_trial_statuses.sakurentsu_number
        where sakurentsu_trial_statuses.sakurentsu_number = '" . $request->get("sakurentsu_number") . "'";

            $sakurentsuisi = db::select($isimail);

            Mail::to('adianto.heru@music.yamaha.com')->cc('imron.faizal@music.yamaha.com')->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
        }

        if ($request->get('tiga_em_need') == "Need") {
            Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                ->update([
                    'position' => '3M',
                ]);

            if ($sk->remark != 'PSS') {
                SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'trial_file' => $filename,
                        'status' => $request->get('tiga_em_need'),
                        'remark' => 'Trial Done',
                    ]);

                $trial = SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->first();

            }

            $tiga_em = sakurentsuThreeM::firstOrNew(array('sakurentsu_number' => $request->get('sakurentsu_number')));
            $tiga_em->sakurentsu_number = $request->get('sakurentsu_number');
            $tiga_em->created_by = $trial->created_by;

            $tiga_em->save();

            $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $tiga_em->id)->select(db::raw('sakurentsu_three_ms.id'), 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

            $data = [
                "datas" => $data_tiga_em,
                "position" => 'INTERPRETER',
                "subject" => '3M Application (3M申請書)',
            ];

            Mail::to($mailtoo)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
        } else {
            if ($sk->remark != 'PSS') {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'status' => 'close',
                    ]);

                SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'trial_file' => $filename,
                        'status' => $request->get('tiga_em_need'),
                        'remark' => 'Trial Done',
                    ]);
            }
        }

        $response = array(
            'status' => true,
            'remark' => $sk->remark,
        );
        return Response::json($response);
    }

    public function assign_to_staff(Request $request)
    {
        try {
            if ($request->get('category') == '3M') {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'position' => 'PIC2',
                    ]);

                $assign = sakurentsuThreeM::firstOrNew(array('sakurentsu_number' => $request->get('sakurentsu_number')));
                $assign->sakurentsu_number = $request->get('sakurentsu_number');
                $assign->created_by = $request->get('pic');

                $assign->save();
            } else if ($request->get('category') == 'Trial') {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'position' => 'PIC2',
                    ]);

                $sk = Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->first();

                $mngr = db::select("SELECT users.name from send_emails
                left join users on send_emails.email = users.email where remark = '" . $sk->pic . "'");

                $emp = EmployeeSync::select('employee_id', 'name')->where('employee_id', '=', $request->get('pic'))->first();

                $assign = SakurentsuTrialStatus::firstOrNew(array('sakurentsu_number' => $request->get('sakurentsu_number')));
                $assign->sakurentsu_number = $request->get('sakurentsu_number');
                $assign->proposer = $emp->employee_id . '/' . $emp->name;
                $assign->department = $sk->pic;
                $assign->created_by = $request->get('pic');

                $assign->save();
            } else if ($request->get('category') == 'Interpreter') {
                $emp = EmployeeSync::select('name')->where('employee_id', '=', $request->get('pic'))->first();

                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'translator' => $emp->name,
                        'position' => 'interpreter2',
                    ]);
            } else if ($request->get('category') == "Interpreter 3M") {
                $emp = EmployeeSync::select('name')->where('employee_id', '=', $request->get('pic'))->first();

                SakurentsuThreeM::where('id', '=', $request->get('sakurentsu_number'))
                    ->update([
                        'translator' => $request->get('pic') . '/' . $emp->name,
                    ]);
            }

            $mail = User::where('username', '=', $request->get('pic'))->select('email')->first();

            if ($request->get('category') != "Interpreter 3M") {
                $isimail = "select * from sakurentsus where sakurentsu_number = '" . $request->get("sakurentsu_number") . "'";

                $sakurentsuisi = db::select($isimail);

                Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
            } else if ($request->get('category') == "Trial") {
                $isimail = "select * from sakurentsus where sakurentsu_number = '" . $request->get("sakurentsu_number") . "'";

                $sakurentsuisi = db::select($isimail);

                Mail::to($mail->email)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
            } else {
                $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('sakurentsu_number'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

                $data = [
                    "datas" => $data_tiga_em,
                    "position" => 'INTERPRETER2',
                    "subject" => '3M Application (3M申請書)',
                ];

                Mail::to($mail)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));
            }

            $response = array(
                'status' => true,
                'emp' => $mail,
            );
            return Response::json($response);
        } catch (QueryException $e) {
            $response = array(
                'status' => false,
                'message' => $e->getMessage(),
            );
            return Response::json($response);
        }
    }

    public function index_reminder_tiga_em($id_tiga_em)
    {
        $title = 'Add Reminder 3M';
        $title_jp = '';

        $data2 = sakurentsuThreeM::where('id', '=', $id_tiga_em)->first();

        return view(
            'sakurentsu.report.index_approval',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data2,
                'status' => 'Add New Reminder 3M',
            )
        )->with('page', 'Sakurentsu')
            ->with('head', 'Sakurentsu');
    }

    public function reminder_tiga_em(Request $request)
    {
        try {
            SakurentsuThreeM::where('id', '=', $request->get('form_number'))
                ->update([
                    'notif_date' => $request->get('reminder_date'),
                ]);

            $response = array(
                'status' => true,
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

    public function save_pss_desc(Request $request)
    {
        SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
            ->update([
                'pss_desc' => $request->get('pss_desc'),
            ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function upload_pss(Request $request)
    {
        SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
            ->update([
                'pss_desc' => $request->get('pss_desc'),
            ]);

        $response = array(
            'status' => true,
        );
        return Response::json($response);
    }

    public function upload_pss_doc(Request $request)
    {
        $sk_trial = SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->first();
        $pss_desc = explode(',', $sk_trial->pss_desc);

        $files = [];
        for ($i = 1; $i < $request->get('tot'); $i++) {
            if ($request->file('file_' . $i) != null) {
                $file = $request->file('file_' . $i);

                $nama = $request->get('sakurentsu_number') . '_' . $pss_desc[$i - 1] . '.' . $file->getClientOriginalExtension();

                $file->move('uploads/sakurentsu/trial_req/pss', $nama);
                array_push($files, $nama);
            } else {
                array_push($files, "");
            }
        }

        SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))
            ->update([
                'pss_file' => json_encode($files),
            ]);

        $tot_pss = explode(',', $sk_trial->pss_desc);

        $pss_upload = $files;

        $pss_files = array_filter($pss_upload);

        if (count($pss_files) == count($tot_pss)) {
            if ($sk_trial->status == "No Need") {
                Sakurentsu::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->update([
                    'status' => 'close',
                ]);
            }

            SakurentsuTrialStatus::where('sakurentsu_number', '=', $request->get('sakurentsu_number'))->update([
                'status' => 'Trial Done',
            ]);
        }

        return view(
            'sakurentsu.trial_request.trial_message',
            array(
                'sk_number' => $request->get('sakurentsu_number'),
                'message' => 'Success',
                'message2' => 'PSS File has been uploaded',
            )
        )->with('page', 'Approval');

    }

    public function fetch_monitoring_detail(Request $request)
    {
        $cat = $request->get('category');
        $cat = explode(' ', $cat);

        $dt = $request->get('date');

        //Sakurentsu
        if ($cat[0] == 'Sakurentsu') {
            $details = Sakurentsu::select('sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'applicant', 'sakurentsus.file', 'file_translate', 'upload_date', 'target_date', 'pic', 'sakurentsus.status', 'sakurentsus.category', 'sakurentsus.translator')
                ->whereRaw('DATE_FORMAT(target_date,"%b %Y") = "' . $dt . '"');

            if ($cat[1] == 'Close') {
                $details = $details->whereIn('status', ['created', 'Close']);
            } else {
                $details = $details->whereNotIn('status', ['created', 'Close']);
            }

            $details = $details->get();
        }
        // 3M
        else if ($cat[0] == '3M') {
            $details = Sakurentsu::where('sakurentsus.category', '=', $cat[0])
                ->whereIn('sakurentsus.status', ['created', 'Close'])
                ->whereRaw('DATE_FORMAT(started_date,"%b %Y") = "' . $dt . '"')
                ->whereNull('sakurentsu_three_ms.deleted_at');

            if ($cat[1] == 'Close') {
                $details = $details->where('sakurentsu_three_ms.remark', '=', 10);
            } else {
                $details = $details->where('sakurentsu_three_ms.remark', '<', 10);
            }

            $details = $details->rightJoin('sakurentsu_three_ms', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')
                ->select('sakurentsu_three_ms.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'applicant', 'sakurentsus.file', 'file_translate', 'upload_date', db::raw('sakurentsu_three_ms.started_date AS target_date'), 'pic', 'sakurentsus.status', 'sakurentsus.category', 'sakurentsus.translator')
                ->get();
        }

        // Trial
        else if ($cat[0] == 'Trial') {
            $details = Sakurentsu::where('sakurentsus.category', '=', $cat[0])
                ->whereIn('sakurentsus.status', ['created', 'Close'])
                ->whereRaw('DATE_FORMAT(trial_date,"%b %Y") = "' . $dt . '"');

            if ($cat[1] == 'Close') {
                $details = $details->where('sakurentsu_trial_requests.status', '=', 'close');
            } else {
                $details = $details->where('sakurentsu_trial_requests.status', '<>', 'close');
            }

            $details = $details->rightJoin('sakurentsu_trial_requests', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_trial_requests.sakurentsu_number')
                ->select('sakurentsu_trial_requests.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'applicant', 'sakurentsus.file', 'file_translate', 'upload_date', db::raw('trial_date AS target_date'), 'pic', 'sakurentsus.status', 'sakurentsus.category', 'sakurentsus.translator')
                ->get();
        } else if ($cat[0] == 'Information') {
            $details = Sakurentsu::select('sakurentsus.sakurentsu_number', 'sakurentsus.title_jp', 'sakurentsus.title', 'applicant', 'sakurentsus.file', 'file_translate', 'upload_date', 'target_date', 'pic', 'sakurentsus.status', 'sakurentsus.category', 'sakurentsus.translator')
                ->where('sakurentsus.category', '=', $cat[0])
                ->whereRaw('DATE_FORMAT(target_date,"%b %Y") = "' . $dt . '"');
            if ($cat[1] == 'Close') {
                $details = $details->where('status', '=', 'Close');
            } else {
                $details = $details->where('status', '<>', 'Close');
            }

            $details = $details->get();
        }

        $response = array(
            'status' => true,
            'details' => $details,
        );
        return Response::json($response);
    }

    public function post_receive_information(Request $request)
    {
        $dpt = Mails::where('email', '=', Auth::user()->email)->get();
        $sk = Sakurentsu::where('sakurentsu_number', '=', $request->get('sk_num'))->select('pic')->first();
        $dpt_sk = explode(',', $sk->pic);

        foreach ($dpt as $d) {
            if (in_array($d->remark, $dpt_sk)) {
                $info = SakurentsuInformation::firstOrNew(array('sakurentsu_number' => $request->get('sk_num'), 'approver_id' => Auth::user()->username));
                $info->sakurentsu_number = $request->get('sk_num');
                $info->approver_id = Auth::user()->username;
                $info->approver_name = Auth::user()->name;
                $info->department = $d->remark;
                $info->remark = 'received';
                $info->created_by = Auth::user()->username;
                $info->save();
            }
        }

        $appr = SakurentsuInformation::where('sakurentsu_number', '=', $request->get('sk_num'))
            ->select('department')->groupBy('department')->get();

        if (count($dpt_sk) == $appr->count()) {
            Sakurentsu::where('sakurentsu_number', '=', $request->get('sk_num'))->update([
                'status' => 'close',
            ]);
        }

        $response = array(
            'status' => true,
            'employee_id' => $dpt,
            'dpt' => $dpt_sk,
        );
        return Response::json($response);
    }

    public function detail_receive_information($sk_num)
    {
        $sk = Sakurentsu::where('sakurentsu_number', '=', $sk_num)->first();

        $sk_info = SakurentsuInformation::where('sakurentsu_number', '=', $sk_num)->get();

        $response = array(
            'status' => true,
            'sign' => $sk,
            'tot_sign' => $sk_info,
        );
        return Response::json($response);
    }

    public function receive_trial_pc($sk_id)
    {
        $sk = Sakurentsu::where('id', '=', $sk_id)->update([
            'position' => "PIC2",
        ]);

        return view(
            'sakurentsu.report.index_receive_info',
            array(
                'sk_num' => $sk->sakurentsu_number,
                'title' => 'Receive Sakurentsu Trial Request',
            )
        )->with('page', 'Approval');
    }

    public function upload_trial_notulen(Request $request)
    {
        $names = "";

        if (count($request->file('file_notulen')) > 0) {
            $files = $request->file('file_notulen');

            $fl = $files->getClientOriginalName();
            $extension = pathinfo($fl, PATHINFO_EXTENSION);
            $nama = $request->get('sk_num') . '.' . $extension;

            $files->move('uploads/sakurentsu/trial_req/notulen', $nama);
            $names = $nama;
        }

        $notulen = Sakurentsu::where('sakurentsu_number', '=', $request->get('sk_num'))->update([
            'additional_file' => $names,
            'pic' => $request->get('pic'),
            'position' => 'PIC',
        ]);

        $section = EmployeeSync::where('department', '=', $request->get('pic'))->whereNotNull('section')->select('section')->groupBy('section')->get();

        $arr_section = [];

        foreach ($section as $sec) {
            array_push($arr_section, $sec->section);
        }

        array_push($arr_section, $request->get('pic'));

        $mng_mails = Mails::leftJoin('users', 'users.email', '=', 'send_emails.email')
            ->leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'users.username')
            ->WhereIn('remark', $arr_section)
            ->Where('position', '<>', 'Foreman')
            ->select('send_emails.email')
            ->get()
            ->toArray();

        $mailcc = DB::select("select distinct email from employee_syncs join users on employee_syncs.employee_id = users.username where employee_id in ('PI0812002','PI1506003')");
        $mailtoocc = [];

        foreach ($mailcc as $mls) {
            array_push($mailtoocc, $mls->email);
        }

        // $isimail = "select * FROM sakurentsus where sakurentsus.sakurentsu_number = '".$request->get("sakurentsu_number")."'";
        $isimail = "select * from sakurentsus where sakurentsu_number = '" . $request->get("sk_num") . "'";

        $sakurentsuisi = db::select($isimail);

        Mail::to($mng_mails)->cc($mailtoocc)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));

        $response = array(
            'status' => true,
            'upload' => 'success',
        );
        return Response::json($response);
    }

    public function fetch_material()
    {
        $datas = ExtraOrderMaterial::leftJoin('extra_orders', 'extra_orders.eo_number', '=', 'extra_order_materials.eo_number')
            ->leftJoin('extra_order_prices', 'extra_order_prices.material_number', '=', 'extra_order_materials.material_number')
            ->select('extra_order_materials.id', 'extra_order_materials.material_number', 'material_number_buyer', 'description', 'uom', 'storage_location', 'extra_order_materials.remark', 'extra_order_materials.status', 'extra_order_materials.created_by', 'extra_order_materials.eo_number', 'extra_order_prices.sales_price', db::raw('DATE_FORMAT(extra_order_prices.valid_date, "%d %b %Y") as valid_date'), 'extra_orders.attachment', 'extra_orders.attention', 'extra_order_materials.reference_form_number', db::raw('extra_order_prices.status as status_price'), db::raw('extra_order_prices.attachment as att_price'), db::raw('IF(extra_order_materials.material_number = "NEW",2,IF(extra_order_prices.sales_price is null,1,0)) as notif'))
            ->orderBy('notif', 'DESC')
            ->get();

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function index_approval_tiga_em($id_tiga_em, $category)
    {
        $title = "Approval 3M";
        $title_jp = "3M承認";

        $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.form_identity_number', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

        $data = [
            'position' => $category,
            'datas' => $data_tiga_em,
        ];

        return view(
            'sakurentsu.report.approval_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
            )
        );
    }

    public function index_approval_implement_tiga_em($id_tiga_em, $category)
    {
        $title = "Approval 3M";
        $title_jp = "3M承認";

        $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $id_tiga_em)->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.form_identity_number', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

        $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
            ->where('sakurentsu_three_m_implementations.form_id', '=', $id_tiga_em)
            ->select('sakurentsu_three_m_implementations.id', 'sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'employee_syncs.division', 'serial_number')
            ->first();

        $data = [
            "datas" => $data_tiga_em,
            "implement" => $data_imp,
            "position" => $category,
            "subject" => '3M Application (3M申請書)',
        ];

        return view(
            'sakurentsu.report.approval_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'data' => $data,
            )
        );
    }

    public function getNotifThreeM()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {

                $add = '';
                if ($user == 'PI9905001') {
                    $add = 'AND form_id in (SELECT form_id from sakurentsu_three_m_approvals where (approver_id = "PI0109004" and approve_at is not null) and (approver_id = "PI1206001" and approve_at is not null))';
                } else if ($user == 'PI1206001') {
                    $add = 'AND form_id in (SELECT form_id from sakurentsu_three_m_approvals where approver_id = "PI0109004" and approve_at is not null)';
                }

                $notif_tiga = db::select('SELECT id, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_department) as total, form_id from
                (SELECT sakurentsu_three_ms.id, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                    left join sakurentsu_three_m_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_approvals.form_id
                    where sakurentsu_three_ms.remark = 5 and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is null and position <> "President Director"
                    GROUP BY sakurentsu_three_ms.id, approver_department) appr
                LEFT JOIN (SELECT form_id from sakurentsu_three_m_approvals where approver_id = "' . $user . '" and approve_at is null and approver_division is not null
                    GROUP BY form_id
                    ) as emp on appr.id = emp.form_id
                    GROUP BY id, form_id
                    having sudah >= total AND form_id is not null ' . $add);
            } else {
                $notif_tiga = db::select('SELECT * from
                    (SELECT sakurentsu_three_ms.id, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                        left join sakurentsu_three_m_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_approvals.form_id
                        where sakurentsu_three_ms.remark = 5 and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is null and position <> "President Director"
                        GROUP BY sakurentsu_three_ms.id, approver_department) appr
                    where app is null and approver_department in (SELECT department from approvers where approver_id = "' . $user . '")');
            }

            $notif = count($notif_tiga);
            return $notif;
        }
    }

    public function getNotifThreeMImp()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {
                $notif_tiga = db::select('SELECT id, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_department) as total, form_id from
                        (SELECT sakurentsu_three_ms.id, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                            left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                            where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark = "approve"
                            GROUP BY sakurentsu_three_ms.id, approver_department) appr
                        LEFT JOIN (SELECT form_id from sakurentsu_three_m_imp_approvals where approver_id = "' . $user . '" and approve_at is null
                            GROUP BY form_id
                            ) as emp on appr.id = emp.form_id
                            GROUP BY id, form_id
                            having sudah >= total AND form_id is not null');
            } else {
                $notif_tiga = db::select('SELECT * from
                            (SELECT sakurentsu_three_ms.id, approver_id, approve_at as app from sakurentsu_three_ms
                                left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                                where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark in ("approve", "pic" )) appr
                            where app is null and approver_id = "' . $user . '"');
            }

            $notif = count($notif_tiga);
            return $notif;
        }
    }

    public function getNotifSakurentsuDetermine()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if (Auth::user()->role_code == 'S-PC' || Auth::user()->role_code == 'C-PC') {

                $notif_sk = db::select("SELECT sakurentsu_number FROM `sakurentsus` where `status` = 'approval'");

                $notif = count($notif_sk);
            }

            return $notif;
        }
    }

    public function getNotifSakurentsuThreeM()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            $dpt = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

            if (count($dpt) > 0) {
                $dep = $dpt->department;
                if ($dep == 'Procurement Department' || $dep == 'Purchasing Control Department') {
                    $dep = '"Procurement Department","Purchasing Control Department"';
                } else {
                    $dep = '"' . $dep . '"';
                }

                $notif_tiga = db::select("SELECT sakurentsu_number FROM `sakurentsus` where pic in (" . $dep . ") and `status` = 'determined' and category = '3M'");

                $notif = count($notif_tiga);
            }

            return $notif;
        }
    }

    public function getNotifSakurentsuTrialReq()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            $dpt = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();

            if (count($dpt) > 0) {
                $dep = $dpt->department;

                if ($dep == 'Procurement Department' || $dep == 'Purchasing Control Department') {
                    $dep = '"Procurement Department","Purchasing Control Department"';
                } else {
                    $dep = '"' . $dep . '"';
                }

                $notif_tiga = db::select("SELECT sakurentsu_number FROM `sakurentsus` where pic in (" . $dep . ") and `status` = 'determined' and category = '3M'");

                $notif = count($notif_tiga);
            }
            return $notif;
        }
    }

    public function getNotifSakurentsuTrial()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {
                $notif_tiga = db::select('SELECT id, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_department) as total, form_id from
                            (SELECT sakurentsu_three_ms.id, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                                left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                                where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark = "approve"
                                GROUP BY sakurentsu_three_ms.id, approver_department) appr
                            LEFT JOIN (SELECT form_id from sakurentsu_three_m_imp_approvals where approver_id = "' . $user . '" and approve_at is null
                                GROUP BY form_id
                                ) as emp on appr.id = emp.form_id
                                GROUP BY id, form_id
                                having sudah >= total AND form_id is not null');
            } else {
                $notif_tiga = db::select('SELECT * from
                                (SELECT sakurentsu_three_ms.id, approver_department, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                                    left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                                    where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark = "approve"
                                    GROUP BY sakurentsu_three_ms.id, approver_department) appr
                                where app is null and approver_department in (SELECT department from approvers where approver_id = "' . $user . '")');
            }

            $notif = count($notif_tiga);
            return $notif;
        }
    }

    public function message($message)
    {
        return view(
            'sakurentsu.report.error_message',
            array(
                'title' => $message,
            )
        );
    }

    public function resend_three_m(Request $request)
    {
        try {
           $data_tiga_em = SakurentsuThreeM::leftJoin('sakurentsus', 'sakurentsus.sakurentsu_number', '=', 'sakurentsu_three_ms.sakurentsu_number')->where('sakurentsu_three_ms.id', '=', $request->get('tiga_em_id'))->select('sakurentsu_three_ms.id', 'sakurentsu_three_ms.title', 'product_name', 'proccess_name', 'unit', 'sakurentsu_three_ms.category', 'sakurentsu_three_ms.created_at', 'sakurentsu_three_ms.sakurentsu_number', db::raw('sakurentsus.title as title_sakurentsu'), db::raw('sakurentsus.title_jp as title_sakurentsu_jp'), 'sakurentsus.applicant', 'upload_date', 'target_date', 'sakurentsu_three_ms.title_jp')->first();

           if ($request->get('remark') == '5') {
            $appr = db::select('SELECT form_id, approver_department, GROUP_CONCAT(approve_at) as app from  sakurentsu_three_m_approvals
                where form_id = "' . $request->get('tiga_em_id') . '" and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is null and position <> "President Director"
                GROUP BY form_id, approver_department');

            $stat_dpt = 'sudah';
            $dpt_belum = [];

            foreach ($appr as $aps) {
                if (!$aps->app) {
                    $stat_dpt = 'belum';
                    array_push($dpt_belum, $aps->approver_department);
                }
            }

            if ($stat_dpt == 'belum') {
                $pos = 'DEPT APPROVAL';
                $mail = Mails::whereIn('remark', $dpt_belum)->select('email')->get();
            } else {
                $appr2 = db::select('SELECT form_id, approver_id, position, approve_at from sakurentsu_three_m_approvals where form_id = "' . $request->get('tiga_em_id') . '" and sakurentsu_three_m_approvals.`status` = "approve" and approver_division is not null order by approver_division ASC, position ASC');

                $stat_gm = 'sudah';
                $gm_belum = [];

                foreach ($appr2 as $aps2) {
                    if (!$aps2->approve_at) {
                        $stat_gm = $aps2->position;
                        $gm_belum = [$aps2->approver_id];
                        break;
                    }
                }

                if ($stat_gm != 'sudah') {
                    if ($stat_gm == 'Deputy General Manager') {
                        if (in_array('PI9905001', $gm_belum)) {
                            $pos = 'SIGNING DGM 2';
                        } else {
                            $pos = 'SIGNING DGM';
                        }
                    } else {
                        $pos = 'SIGNING GM';
                    }

                    $mail = db::select('SELECT email from users where username = "' . $gm_belum[0] . '"');
                } else {
                    $pos = 'PRESDIR';
                    $mail = db::select('SELECT email from users left join employee_syncs on users.username = employee_syncs.employee_id where position="President Director" and end_date is null');
                }

            }

            $data = [
                "datas" => $data_tiga_em,
                "position" => $pos,
                "subject" => '3M Application (3M申請書)',
            ];
        } else if ($request->get('remark') == '8') {
            $data_imp = SakurentsuThreeMImplementation::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_m_implementations.created_by')
            ->where('sakurentsu_three_m_implementations.form_id', '=', $request->get('tiga_em_id'))
            ->select('sakurentsu_three_m_implementations.form_number', db::raw('DATE_FORMAT(form_date,"%d %M %Y") as frm_date'), 'sakurentsu_three_m_implementations.section', 'sakurentsu_three_m_implementations.name', 'title', 'reason', 'started_date', db::raw('DATE_FORMAT(actual_date,"%d %M %Y") as act_date'), db::raw('DATE_FORMAT(check_date,"%d %M %Y") as ck_date'), 'checker', 'serial_number')
            ->first();

            $appr = db::select('SELECT form_id, approver_id, approve_at as app from sakurentsu_three_m_imp_approvals where form_id = "' . $request->get('tiga_em_id') . '" and sakurentsu_three_m_imp_approvals.`remark` = "approve" AND approver_department is not null');

            $stat_dpt = 'sudah';
            $dpt_belum = [];

            foreach ($appr as $aps) {
                if (!$aps->app) {
                    $stat_dpt = 'belum';
                    array_push($dpt_belum, $aps->approver_id);
                }
            }

            if ($stat_dpt == 'belum') {
                $pos = 'IMPLEMENT DEPT';
                $mail = User::whereIn('username', $dpt_belum)->select('email')->get();
            } else {
                $appr2 = db::select('SELECT form_id, approver_id, position, approve_at as app from sakurentsu_three_m_imp_approvals
                    where form_id = "' . $request->get('tiga_em_id') . '" and sakurentsu_three_m_imp_approvals.`remark` = "approve" and approver_department is null
                    ORDER BY position ASC');

                $stat_gm = 'sudah';
                $gm_belum = [];

                foreach ($appr2 as $aps2) {
                    if (!$aps2->app) {
                        $stat_gm = $aps2->position;
                        $gm_belum = [$aps2->approver_id];
                        break;
                    }
                }

                if ($stat_gm != 'sudah') {
                    if ($stat_gm == 'Deputy General Manager') {
                        $pos = 'IMPLEMENT DGM';
                    } else {
                        $pos = 'IMPLEMENT GM';
                    }

                    $mail = db::select('SELECT email from users where username = "' . $gm_belum[0] . '"');
                } else {
                    $pos = 'IMPLEMENT STD';
                    $mails = db::select('SELECT email from users where username in ("PI0904001", "PI0904007")');

                    $mail = [];

                    foreach ($mails as $mls) {
                        array_push($mail, $mls->email);
                    }
                }
            }

            $data = [
                "datas" => $data_tiga_em,
                "implement" => $data_imp,
                "position" => $pos,
                "subject" => '3M Application (3M申請書)',
            ];
        }

        Mail::to($mail)->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($data, '3m_approval'));

        $response = array(
            'status' => true,
            'datas' => $data,
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

    public function index_summary()
    {
        $title = '3M/Trial/Information Summary';
        $title_jp = '??';

        return view(
            'sakurentsu.master.index_summary',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Sakurentsu Summary')
            ->with('head', 'Sakurentsu');
    }

    public function index_summary_all($category)
    {
        if ($category == '3m') {
            $title = '3M MIRAI Summary';
            $title_jp = 'MIRAIの3Mまとめ';
            $dept = ['Production Control Department', 'Procurement Department', 'Purchasing Control Department', 'Production Engineering Department'];

            $emp = EmployeeSync::whereIn('department', $dept)
                ->whereNull('end_date')
                ->whereNull('group')
                ->select('employee_id', 'name')
                ->orderBy('name')
                ->get();

            return view(
                'sakurentsu.report.3m_summary',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'department' => $dept,
                    'employee' => $emp,
                )
            )->with('page', '3M Summary')->with('head', 'Sakurentsu');
        } else if ($category == 'trial') {
            $title = 'Trial Request MIRAI Summary';
            $title_jp = '??';

            $dept = EmployeeSync::whereIn('division', ['Production Division', 'Production Support Division'])
                ->whereNotNull('department')
                ->select('department')
                ->groupBy('department')
                ->orderBy('department')
                ->get();

            $emp = EmployeeSync::whereIn('department', $dept)
                ->whereNull('end_date')
                ->whereNull('group')
                ->select('employee_id', 'name')
                ->orderBy('name')
                ->get();

            return view(
                'sakurentsu.report.trial_summary',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'department' => $dept,
                    'employee' => $emp,
                )
            )->with('page', 'Trial Request Summary')
                ->with('head', 'Sakurentsu');
        } else if ($category == 'information') {
            $title = 'Information MIRAI Summary';
            $title_jp = '??';

            $dept = EmployeeSync::whereIn('division', ['Production Division', 'Production Support Division'])
                ->whereNotNull('department')
                ->select('department')
                ->groupBy('department')
                ->orderBy('department')
                ->get();

            $emp = EmployeeSync::whereIn('department', $dept)
                ->whereNull('end_date')
                ->whereNull('group')
                ->select('employee_id', 'name')
                ->orderBy('name')
                ->get();

            return view(
                'sakurentsu.report.info_summary',
                array(
                    'title' => $title,
                    'title_jp' => $title_jp,
                    'department' => $dept,
                    'employee' => $emp,
                )
            )->with('page', 'Information Summary')
                ->with('head', 'Sakurentsu');
        }
    }

    public function fetch_summary_all($category, Request $request)
    {

        if ($category == '3m') {
            $data = SakurentsuThreeM::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_three_ms.created_by')
                ->leftJoin('sakurentsu_three_m_implementations', 'sakurentsu_three_m_implementations.form_id', '=', 'sakurentsu_three_ms.id')
                ->leftJoin(db::raw('(SELECT process_code, process_name from processes where remark = "3M") prc'), 'prc.process_code', '=', 'sakurentsu_three_ms.remark');

            if ($request->get('datefrom') != '' && $request->get('dateto') != '') {
                $data = $data->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            } else if ($request->get('datefrom') != '') {
                $data = $data->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '<=', $request->get('datefrom'));
            } else if ($request->get('dateto') != '') {
                $data = $data->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '>=', $request->get('dateto'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            }

            if ($request->get('category') != '') {
                $data = $data->where('category', '=', $request->get('category'));
            }

            if ($request->get('sakurentsu_number') != '') {
                $data = $data->where('sakurentsu_number', '=', $request->get('sakurentsu_number'));
            }

            if ($request->get('tiga_em_number') != '') {
                $data = $data->where('sakurentsu_three_ms.form_number', '=', $request->get('tiga_em_number'));
            }

            if ($request->get('tiga_em_title') != '') {
                $data = $data->where('sakurentsu_three_ms.title', 'LIKE', '%' . $request->get('tiga_em_title') . '%');
            }

            if ($request->get('department') != '') {
                if ($request->get('department') == 'Purchasing Control Department' || $request->get('department') == 'Procurement Department') {
                    $dpt = ['Purchasing Control Department', 'Procurement Department'];
                } else {
                    $dpt = [$request->get('department')];
                }

                $data = $data->whereIn('employee_syncs.department', $dpt);
            }

            if ($request->get('pic') != '') {
                $data = $data->where('sakurentsu_three_ms.created_by', '=', $request->get('pic'));
            }

            $data = $data->select('sakurentsu_three_ms.id', 'sakurentsu_number', 'form_identity_number', 'sakurentsu_three_ms.form_number', 'sakurentsu_three_ms.category', db::raw('DATE_FORMAT(sakurentsu_three_ms.created_at,"%d %b %Y") as issue_date'), 'employee_syncs.department', 'employee_syncs.name', 'sakurentsu_three_ms.title', 'prc.process_name', db::raw('DATE_FORMAT(sakurentsu_three_m_implementations.actual_date,"%d %b %Y") as act_date'))
                ->get();

            $datas2 = db::table('sakurentsu_three_m_summaries')->get();

            $datas = [
                'datas' => $data,
                'old_datas' => $datas2,
            ];

        } else if ($category == 'trial') {
            $datas = SakurentsuTrialRequest::leftJoin('employee_syncs', 'employee_syncs.employee_id', '=', 'sakurentsu_trial_requests.requester')->where('category', '=', 'Trial');

            if ($request->get('datefrom') != '' && $request->get('dateto') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            } else if ($request->get('datefrom') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '<=', $request->get('datefrom'));
            } else if ($request->get('dateto') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '>=', $request->get('dateto'))
                    ->where(db::raw('DATE_FORMAT(sakurentsu_trial_requests.created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            }

            if ($request->get('sakurentsu_number') != '') {
                $datas = $datas->where('sakurentsu_number', '=', $request->get('sakurentsu_number'));
            }

            if ($request->get('trial_number') != '') {
                $datas = $datas->where('sakurentsu_trial_requests.form_number', '=', $request->get('trial_number'));
            }

            if ($request->get('subject_trial') != '') {
                $datas = $datas->where('sakurentsu_trial_requests.subject', 'LIKE', '%' . $request->get('subject_trial') . '%');
            }

            if ($request->get('department') != '') {
                if ($request->get('department') == 'Purchasing Control Department' || $request->get('department') == 'Procurement Department') {
                    $dpt = ['Purchasing Control Department', 'Procurement Department'];
                } else {
                    $dpt = [$request->get('department')];
                }

                $datas = $datas->whereIn('sakurentsu_trial_requests.department', $dpt);
            }

            if ($request->get('pic') != '') {
                $datas = $datas->where('sakurentsu_trial_requests.created_by', '=', $request->get('pic'));
            }

            $datas = $datas->select('id', 'form_number', 'sakurentsu_number', 'subject', db::raw('DATE_FORMAT(submission_date,"%d %b %Y") as issue_date'), 'requester', 'sakurentsu_trial_requests.department', 'status', 'employee_syncs.name');
            // $datas = $datas->where() ;
            $datas = $datas->get();

        } else if ($category == 'information') {
            $datas = Sakurentsu::where('category', '=', 'Information');

            if ($request->get('datefrom') != '' && $request->get('dateto') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            } else if ($request->get('datefrom') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '>=', $request->get('datefrom'))
                    ->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '<=', $request->get('datefrom'));
            } else if ($request->get('dateto') != '') {
                $datas = $datas->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '>=', $request->get('dateto'))
                    ->where(db::raw('DATE_FORMAT(created_at,"%Y-%m-%d")'), '<=', $request->get('dateto'));
            }

            if ($request->get('sakurentsu_number') != '') {
                $datas = $datas->where('sakurentsu_number', '=', $request->get('sakurentsu_number'));
            }

            if ($request->get('department') != '') {
                $datas = $datas->where('pic', '=', $request->get('department'));
            }

            $datas = $datas->select('id', 'sakurentsu_number', db::raw('DATE_FORMAT(updated_at,"%d %b %Y") as issue_date'), 'pic', 'title')->get();
        }

        $response = array(
            'status' => true,
            'datas' => $datas,
        );
        return Response::json($response);
    }

    public function index3MDisplay()
    {
        $title = 'Outstanding 3M Monitoring';
        $title_jp = '';

        return view(
            'sakurentsu.monitoring.outstanding_3m',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
            )
        )->with('page', 'Outstanding 3M Monitoring')
            ->with('head', 'Sakurentsu');
    }

    public function fetch3MDisplay(Request $request)
    {
        $datas = db::select("SELECT sk.sakurentsu_number, sk.title, sk.category, sakurentsu_three_ms.started_date, sakurentsu_three_ms.product_name, sakurentsu_three_ms.proccess_name, related_department, sk.reason from
            (SELECT * FROM sakurentsus where category = '3M' and `status` = 'created' and deleted_at is null) as sk
            left join `sakurentsu_three_ms` on sk.sakurentsu_number = sakurentsu_three_ms.sakurentsu_number
            where sakurentsu_three_ms.remark = 7
            order by started_date ASC");
        $dpt = db::select("SELECT department_name, department_shortname_2 from departments");

        $response = array(
            'status' => true,
            'datas' => $datas,
            'depts' => $dpt,
        );
        return Response::json($response);
    }

    public function fetch_special_item_tmp_files(Request $request)
    {
        $docs = db::table('sakurentsu_three_m_sp_temps')
            ->where('temp_id', 'LIKE', $request->get('id') . '_' . strtoupper(Auth::user()->username) . '%')
            ->get();

        $con = $docs->count();

        if ($con > 0) {
            $response = array(
                'status' => true,
                'docs' => $docs,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function fetch_special_item_files(Request $request)
    {
        $docs = db::table('sakurentsu_three_m_specials')
            ->where('identical_number', 'LIKE', "%" . $request->get('id'))
            ->where('form_number', '=', $request->get('form_number'))
            ->get();

        $docs = db::select('SELECT eviden_att, "act" as remark from sakurentsu_three_m_specials where identical_number LIKE "%'. $request->get("id") .'" and form_number = "'.$request->get('form_number').'" UNION ALL SELECT renamed_file_name as eviden_att, "tmp" as remark from sakurentsu_three_m_sp_temps where temp_id LIKE "'. $request->get('id') .'_'. strtoupper(Auth::user()->username).'%"');
        $con = count($docs);

        if ($con > 0) {
            $response = array(
                'status' => true,
                'docs' => $docs,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

    public function upload_special_item_files(Request $request)
    {
        $file = $request->file('doc_upload2');
        $id = $request->get('ido2');

        $nama = $file->getClientOriginalName();
        $file_name = md5($id . '_' . strtoupper(Auth::user()->username) . '_' . date('His')) . '.' . $file->getClientOriginalExtension();

        $file->move(base_path('public/uploads/sakurentsu/three_m/item_khusus/temp'), $file_name);

        $three_m = SakurentsuThreeM::where('id', '=', $id)->first();

        // $docs = new SakurentsuThreeMSpTemp;
        // $docs->temp_id = $id . '_' . strtoupper(Auth::user()->username) . '_' . date('His');
        // $docs->origin_file_name = $nama;
        // $docs->renamed_file_name = $file_name;
        // $docs->uploaded_at = date('Y-m-d H:i:s');
        // $docs->uploaded_by = Auth::user()->username;
        // $docs->created_by = Auth::user()->username;
        // $docs->save();

        DB::table('sakurentsu_three_m_sp_temps')->insert([
            [
                'temp_id' => $id . '_' . strtoupper(Auth::user()->username) . '_' . date('His'), 
                'origin_file_name' => $nama,
                'renamed_file_name' => $file_name,
                'uploaded_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'uploaded_by' => Auth::user()->username,
                'created_by' => Auth::user()->username,
            ],
        ]);

        return redirect()->back()->with(array('alert2' => 'Success', 'doc_name' => $id));
    }

    public function index_upload_special_item($form_number)
    {
        $title = 'Update 3M Special Items';
        $title_jp = '??';

        $data_tiga_em = SakurentsuThreeM::where('form_identity_number', '=', $form_number)->first();
        $emp_dept = EmployeeSync::where('employee_id', '=', Auth::user()->username)->select('department')->first();
        $item_khusus = SakurentsuThreeMSpecial::leftJoin('employee_syncs', 'pic', '=', 'employee_syncs.employee_id')
        ->where('form_number', '=', $form_number);

        if (!str_contains(Auth::user()->role_code, 'MIS')) {
            $item_khusus = $item_khusus->where('department', '=', $emp_dept->department);
        }

        $item_khusus = $item_khusus->select('form_number', 'sakurentsu_number', 'identical_number', 'item_khusus', 'target_change', 'actual_change', 'pic', 'eviden_description', 'eviden_att', 'name')
        ->get();

        return view(
            'sakurentsu.report.upload_3m_special_items',
            array(
                'title' => $title,
                'title_jp' => $title_jp,
                'tiga_m' => $data_tiga_em,
                'item_khusus' => $item_khusus,
            )
        )->with('page', '3M Update Special Items')
        ->with('head', 'Sakurentsu');
    }

    public function update_special_items(Request $request)
    {
        $docs = db::table('sakurentsu_three_m_specials')
        ->where('identical_number', 'LIKE', "%" . $request->get('id'))
        ->where('form_number', '=', $request->get('form_number'))
        ->get();

        $docs = db::select('SELECT eviden_att, "act" as remark from sakurentsu_three_m_specials where identical_number LIKE "%'. $request->get("id") .'" and form_number = "'.$request->get('form_number').'" UNION ALL SELECT renamed_file_name as eviden_att, "tmp" as remark from sakurentsu_three_m_sp_temps where temp_id LIKE "'. $request->get('id') .'_'. strtoupper(Auth::user()->username).'%"');
        $con = count($docs);

        if ($con > 0) {
            $response = array(
                'status' => true,
                'docs' => $docs,
            );
            return Response::json($response);
        } else {
            $response = array(
                'status' => false,
            );
            return Response::json($response);
        }
    }

}