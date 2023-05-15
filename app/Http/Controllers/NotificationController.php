<?php

namespace App\Http\Controllers;

use App\HrQuestionLog;
use App\Http\Controllers\Controller;
use Auth;
use Illuminate\Support\Facades\DB;
use Response;

class NotificationController extends Controller
{
    public function fetchNotification()
    {
        try {
            $notifications = array();
            if (Auth::check()) {
                array_push($notifications,
                    [
                        'title' => 'MIS Ticket',
                        'title_jp' => 'MISチケット依頼',
                        'url' => 'index/ticket/monitoring/mis',
                        'count' => self::notif_mis_ticket(),
                    ],
                    [
                        'title' => 'MIS Form',
                        'title_jp' => 'MIS票',
                        'url' => 'index/mis/form',
                        'count' => self::notif_mis_form(),
                    ],
                    [
                        'title' => 'Surat Izin Keluar',
                        'title_jp' => '外出申請書',
                        'url' => 'index/human_resource/leave_request',
                        'count' => self::notif_leave_request(),
                    ],
                    [
                        'title' => 'Pelaporan Kanagata Retak',
                        'title_jp' => '金型故障報告',
                        'url' => 'kanagata/control',
                        'count' => self::notif_kanagata(),
                    ],
                    [
                        'title' => 'Purchase Requisition (PR)',
                        'title_jp' => '購入申請',
                        'url' => 'purchase_requisition/monitoring',
                        'count' => self::notif_pr(),
                    ],
                    [
                        'title' => 'Purchase Requisition Canteen (PR)',
                        'title_jp' => '購入申請',
                        'url' => 'canteen/purchase_requisition/monitoring',
                        'count' => self::notif_pr_canteen(),
                    ],
                    [
                        'title' => 'Investment',
                        'title_jp' => '投資申請',
                        'url' => 'investment/control',
                        'count' => self::notif_inv(),
                    ],
                    [
                        'title' => 'Purchase Order (PO)',
                        'title_jp' => '発注依頼',
                        'url' => 'purchase_order/monitoring',
                        'count' => self::notif_po(),
                    ],
                    [
                        'title' => 'Purchase Order Canteen (PO)',
                        'title_jp' => '発注依頼',
                        'url' => 'canteen/purchase_order/monitoring',
                        'count' => self::notif_po_canteen(),
                    ],
                    [
                        'title' => 'Mirai Approval',
                        'title_jp' => 'MIRAI承認システム',
                        'url' => 'index/mirai/approval',
                        'count' => self::notif_mirai_approval(),
                    ],
                    [
                        'title' => 'Mutasi Satu Department',
                        'title_jp' => '部門内部署移動',
                        'url' => 'dashboard/mutasi',
                        'count' => self::notif_mutation(),
                    ],
                    [
                        'title' => 'Mutasi Antar Department',
                        'title_jp' => '部門跨ぐ部署移動',
                        'url' => 'dashboard_ant/mutasi',
                        'count' => self::notif_mutation_department(),
                    ],
                    [
                        'title' => 'Extra Order',
                        'title_jp' => 'エキストラオーダー',
                        'url' => 'index/extra_order/approval_monitoring?submit_from=&submit_to=&approver_id=' . strtoupper(Auth::user()->username),
                        'count' => self::notif_eo(),
                    ],
                    [
                        'title' => 'EO Sending Application',
                        'title_jp' => 'エキストラオーダー',
                        'url' => 'index/extra_order/sending_application',
                        'count' => self::notif_send_app(),
                    ],
                    [
                        'title' => 'Translation Request',
                        'title_jp' => '翻訳管理システム',
                        'url' => 'index/translation',
                        'count' => self::notif_translation(),
                    ],
                    [
                        'title' => 'Small Group Activity (SGA)',
                        'title_jp' => 'スモールグループ活動',
                        'url' => 'index/sga/monitoring',
                        'count' => self::notif_sga(),
                    ],
                    [
                        'title' => 'YPM Contest',
                        'title_jp' => 'YMPI生産性管理評価',
                        'url' => 'index/standardization/ypm',
                        'count' => self::notif_ypm(),
                    ],
                    [
                        'title' => 'QA Certificate FG/KD',
                        'title_jp' => '品質保証検査認定',
                        'url' => 'index/qa/certificate/code',
                        'count' => self::notif_certificate(),
                    ],
                    [
                        'title' => 'QA Certificate Inprocess',
                        'title_jp' => '工程内検査認証',
                        'url' => 'index/qa/certificate/code/inprocess',
                        'count' => self::notif_certificate_process(),
                    ],
                    [
                        'title' => 'New QA Certificate',
                        'title_jp' => '検査認定証新規申請承認',
                        'url' => 'index/submission/qa/certificate',
                        'count' => self::notif_certificate_submit(),
                    ],
                    [
                        'title' => 'Non-Active Kensa Certificate',
                        'title_jp' => '検査認定証無効化',
                        'url' => 'index/submission/qa/certificat',
                        'count' => self::notif_certificate_inactive(),
                    ],
                    [
                        'title' => '3M Application',
                        'title_jp' => '3M変更申請の承認',
                        'url' => 'index/sakurentsu/monitoring/3m/Approval 3M Application',
                        'count' => self::notif_3m(),
                    ],
                    [
                        'title' => '3M Implementation',
                        'title_jp' => '3M変更操作',
                        'url' => 'index/sakurentsu/monitoring/3m/3M Implementation',
                        'count' => self::notif_3m_implementation(),
                    ],
                    [
                        'title' => 'Trial Request Issue',
                        'title_jp' => '試作依頼',
                        'url' => 'index/sakurentsu/monitoring/3m/Trial Request Issue',
                        'count' => self::notif_trial(),
                    ],
                    [
                        'title' => 'Receive Trial Request',
                        'title_jp' => '試作依頼書の受理',
                        'url' => 'index/sakurentsu/monitoring/3m/Trial Request Receive',
                        'count' => self::notif_trial_receive(),
                    ],
                    [
                        'title' => 'Result Trial Result',
                        'title_jp' => '試作依頼の結果',
                        'url' => 'index/trial_request_leader',
                        'count' => self::notif_trial_result(),
                    ],
                    [
                        'title' => 'Sakurentsu 3M',
                        'title_jp' => '3Mの作連通',
                        'url' => 'index/sakurentsu/list_3m',
                        'count' => self::notif_sakurentsu_3m(),
                    ],
                    [
                        'title' => 'Sakurentsu Trial Request',
                        'title_jp' => '試作依頼の作連通',
                        'url' => 'index/trial_request',
                        'count' => self::notif_sakurentsu_trial(),
                    ],
                    [
                        'title' => 'Visitor Confirmation',
                        'title_jp' => '来客の確認',
                        'url' => 'visitor_confirmation_manager',
                        'count' => self::notif_visitor(),
                    ],
                    [
                        'title' => 'HR Q&A',
                        'title_jp' => '人事部質疑応答',
                        'url' => 'index/qnaHR',
                        'count' => self::notif_q_a_hr(),
                    ],
                    [
                        'title' => 'Tunjangan & Simpati',
                        'title_jp' => '(????)',
                        'url' => 'human_resource',
                        'count' => self::notif_tunjangan(),
                    ],
                    [
                        'title' => 'Approval Disposal WWT',
                        'title_jp' => '(????)',
                        'url' => 'index/confirmation/limbah',
                        'count' => self::notif_chemical_wwt(),
                    ],
                    [
                        'title' => 'Payment Request',
                        'title_jp' => '支払リクエスト',
                        'url' => 'index/payment_request/monitoring',
                        'count' => self::notif_payment_request(),
                    ],
                    [
                        'title' => 'Engineering Job Request',
                        'title_jp' => '技術的作業の依頼',
                        'url' => 'index/ejor/monitoring?filter=',
                        'count' => self::notif_ejor(),
                    ],
                    [
                        'title' => 'Fixed Asset Registration',
                        'title_jp' => '固定資産登録',
                        'url' => 'index/fixed_asset/monitoring_approval/registration',
                        'count' => self::notif_fa_registrasi(),
                    ],
                    [
                        'title' => 'Fixed Asset Disposal',
                        'title_jp' => '固定資産の処分',
                        'url' => 'index/fixed_asset/monitoring_approval/disposal',
                        'count' => self::notif_fa_disposal(),
                    ],
                    [
                        'title' => 'Fixed Asset Receive Label',
                        'title_jp' => '',
                        'url' => 'index/fixed_asset/monitoring_approval',
                        'count' => self::notif_receive_label_fa(),
                    ],
                    [
                        'title' => 'CVM Sanding',
                        'title_jp' => '',
                        'url' => 'index/monitoring/material_check/sanding',
                        'count' => self::notif_cvm(),
                    ],
                    [
                        'title' => 'EJOR Evidence Verification',
                        'title_jp' => '',
                        'url' => 'index/ejor/monitoring?filter=verifying',
                        'count' => self::notif_ejor_ev(),
                    ]

                );
            }

            $response = array(
                'status' => true,
                'notifications' => $notifications,
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

    public function notif_q_a_hr()
    {
        if (Auth::user() !== null) {
            if (str_contains(Auth::user()->role_code, 'C-HR') || str_contains(Auth::user()->role_code, 'S-HR')) {
                $ntf = HrQuestionLog::select(db::raw("SUM(remark) as ntf"))->first();
                return (int) $ntf->ntf;
            } else {
                return 0;
            }
        }
    }

    public function notif_visitor()
    {
        $lists = null;
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
                        AND
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
                }
            }

            $notif = 0;

            if ($lists != null) {
                $notif = count($lists);
            }
            return $notif;
        }
    }

    public function notif_sakurentsu_trial()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            $dpt = db::table('employee_syncs')->where('employee_id', '=', Auth::user()->username)->whereNotIn('position', ['chief', 'manager', 'Specialist'])->select('department')->first();

            if ($dpt != null) {
                $dep = $dpt->department;

                if ($dep == 'Procurement Department' || $dep == 'Purchasing Control Department') {
                    $dep = '"Procurement Department","Purchasing Control Department"';
                } else {
                    $dep = '"' . $dep . '"';
                }

                $notif_tiga = db::select("SELECT sakurentsu_number FROM `sakurentsus` where pic in (" . $dep . ") and `status` = 'determined' and category = 'Trial'");

                $notif = count($notif_tiga);
            }
            return $notif;
        }
    }

    public function notif_sakurentsu_3m()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;
            $dpt = db::table('employee_syncs')->where('employee_id', '=', Auth::user()->username)->whereNotIn('position', ['chief', 'manager', 'Specialist'])->select('department')->first();

            if ($dpt != null) {
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

    public function notif_trial_result()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $notif = 0;

            $trial_result = db::select("SELECT sakurentsu_trial_request_results.* from sakurentsu_trial_requests
            left join sakurentsu_trial_request_results on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_results.trial_id
            where sakurentsu_trial_requests.status = 'resulting'
            and reject is null
            and fill_by LIKE '" . $user . "%' and trial_method is null");

            if (count($trial_result) > 0) {
                $notif = count($trial_result);
            }

            return $notif;
        }
    }

    public function notif_trial_receive()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $notif = 0;

            $trial_receive = db::select("SELECT sakurentsu_trial_request_receives.trial_id from sakurentsu_trial_requests
            left join sakurentsu_trial_request_receives on sakurentsu_trial_requests.form_number = sakurentsu_trial_request_receives.trial_id
            where sakurentsu_trial_requests.status = 'receiving'
            and reject is null
            AND ((sakurentsu_trial_request_receives.chief LIKE '" . $user . "%' and sakurentsu_trial_request_receives.chief_date is null and sakurentsu_trial_request_receives.manager_date is not null) OR (sakurentsu_trial_request_receives.manager LIKE '" . $user . "%' and sakurentsu_trial_request_receives.manager_date is null))");

            if (count($trial_receive) > 0) {
                $notif = count($trial_receive);
            }

            return $notif;
        }
    }

    public function notif_trial()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $notif = 0;

            $chief = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and chief LIKE '" . $user . "%' and chief_date is null and reject is null and deleted_at is null");

            $manager = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and manager LIKE '" . $user . "%' and manager_date is null and chief_date is not null and reject is null and deleted_at is null");

            $manager_mecha = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and manager_mechanical LIKE '" . $user . "%' and manager_mechanical_date is null and manager_date is not null and reject is null and deleted_at is null");

            $dgm = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and dgm LIKE '" . $user . "%' and dgm_date is null and (manager_mechanical is null OR manager_mechanical_date is not null) and manager_date is not null and reject is null and deleted_at is null");

            $gm = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and gm LIKE '" . $user . "%' and gm_date is null and dgm_date is not null and reject is null and deleted_at is null");

            $dgm2 = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and dgm2 LIKE '" . $user . "%' and dgm_date2 is null and gm_date is not null and reject is null and deleted_at is null");

            $gm2 = db::SELECT("SELECT * from sakurentsu_trial_requests where `status` = 'approval' and gm2 LIKE '" . $user . "%' and dgm_date2 is not null and gm_date2 is null and reject is null and deleted_at is null");

            $notif = count($chief) + count($manager) + count($manager_mecha) + count($dgm) + count($gm) + count($dgm2) + count($gm2);

            return $notif;
        }
    }

    public function notif_3m_implementation()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {

                $add = '';

                if ($user == 'PI0109004') {
                    $add = 'and (approver_id = "PI9905001" and approve_at is not null)';
                } else if ($user == 'PI1206001') {
                    $add = 'and (approver_id = "PI0109004" and approve_at is not null)';
                }

                $notif_tiga = db::select('SELECT id, SUM(IF(app is not null, 1, 0)) as sudah, COUNT(approver_id) as total, form_id from
                (SELECT sakurentsu_three_ms.id, approver_id, GROUP_CONCAT(approve_at) as app from sakurentsu_three_ms
                left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark = "approve"
                and sakurentsu_three_ms.deleted_at is null and approver_department is not null
                GROUP BY sakurentsu_three_ms.id, approver_id) appr
                JOIN (SELECT form_id from sakurentsu_three_m_imp_approvals where approver_id = "' . $user . '" and approve_at is null ' . $add . '
                GROUP BY form_id
                ) as emp on appr.id = emp.form_id
                GROUP BY id, form_id
                having sudah >= total AND form_id is not null');
            } else {
                $notif_tiga = db::select('SELECT * from
                (SELECT sakurentsu_three_ms.id, approver_id, approve_at as app from sakurentsu_three_ms
                left join sakurentsu_three_m_imp_approvals on sakurentsu_three_ms.id = sakurentsu_three_m_imp_approvals.form_id
                where sakurentsu_three_ms.remark = 8 and sakurentsu_three_m_imp_approvals.remark in ("approve", "pic" ) and sakurentsu_three_ms.deleted_at is null) appr
                where app is null and approver_id = "' . $user . '"');
            }

            $notif = count($notif_tiga);
            return $notif;
        }
    }

    public function notif_3m()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $notif = 0;

            if ($user == 'PI0109004' || $user == 'PI9905001' || $user == 'PI1206001') {

                $add = '';
                if ($user == 'PI9905001') {
                    $add = 'AND form_id in (SELECT form_id from sakurentsu_three_m_approvals where (approver_id = "PI0109004" and approve_at is not null) and form_id in (SELECT form_id from sakurentsu_three_m_approvals where approver_id = "PI1206001" and approve_at is not null))';
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

    public function notif_certificate_inactive()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $staff_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( staff_qa )
      FROM
      qa_certificate_submissions
      WHERE
      staff_qa LIKE '%" . $user . "%'
      AND SPLIT_STRING ( applicant, '_', 3 ) != ''
      AND SPLIT_STRING ( staff_qa, '_', 3 ) = ''
      AND request_type = 'deactivate'
      AND deleted_at IS NULL");

            $notif = 0;

            if (count($staff_qa) > 0) {
                $notif = count($staff_qa);
            }
            return $notif;
        }
    }

    public function notif_certificate_submit()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $foreman_prod = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( foreman_prod )
      FROM
      qa_certificate_submissions
      WHERE
      foreman_prod LIKE '%" . $user . "%'
      AND SPLIT_STRING ( applicant, '_', 3 ) != ''
      AND SPLIT_STRING ( foreman_prod, '_', 3 ) = ''
      AND request_type = 'new'
      AND deleted_at IS NULL");

            $staff_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( staff_qa )
      FROM
      qa_certificate_submissions
      WHERE
      staff_qa LIKE '%" . $user . "%'
      AND SPLIT_STRING ( foreman_prod, '_', 3 ) != ''
      AND SPLIT_STRING ( staff_qa, '_', 3 ) = ''
      AND request_type = 'new'
      AND deleted_at IS NULL");

            $foreman_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( foreman_qa )
      FROM
      qa_certificate_submissions
      WHERE
      foreman_qa LIKE '%" . $user . "%'
      AND SPLIT_STRING ( staff_qa, '_', 3 ) != ''
      AND SPLIT_STRING ( foreman_qa, '_', 3 ) = ''
      AND request_type = 'new'
      AND deleted_at IS NULL");

            $leader_qa = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
      ( leader_qa )
      FROM
      qa_certificate_submissions
      WHERE
      leader_qa LIKE '%" . $user . "%'
      AND SPLIT_STRING ( foreman_qa, '_', 3 ) != ''
      AND SPLIT_STRING ( leader_qa, '_', 3 ) = ''
      AND request_type = 'new'
      AND deleted_at IS NULL");

            $notif = 0;

            if (count($foreman_prod) > 0 || count($staff_qa) > 0 || count($foreman_qa) > 0 || count($leader_qa) > 0) {
                $notif = count($foreman_prod) + count($staff_qa) + count($foreman_qa) + count($leader_qa);
            }
            return $notif;
        }
    }

    public function notif_certificate_process()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;
            $jumlah_tanggungan = 0;

            $cer_approval = DB::connection('ympimis_2')
                ->select("SELECT
      approver_id
      FROM
      `qa_certificate_approvals`
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id
      WHERE
      priority = 1
      AND approver_id = '" . $user . "'
      AND `code` != 'I'");

            if (count($cer_approval) > 0) {
                $notif = count($cer_approval);
            }

            return $notif;
        }
    }

    public function notif_certificate()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;
            $jumlah_tanggungan = 0;

            $cer_approval = DB::connection('ympimis_2')
                ->select("SELECT
      approver_id
      FROM
      `qa_certificate_approvals`
      JOIN qa_certificate_codes ON qa_certificate_codes.certificate_id = qa_certificate_approvals.certificate_id
      WHERE
      priority = 1
      AND approver_id = '" . $user . "'
      AND `code` = 'I'");

            if (count($cer_approval) > 0) {
                $notif = count($cer_approval);
            }

            return $notif;
        }
    }

    public function notif_sga()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $manager = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
            ( manager_qa_approver_id )
            FROM
            sga_teams
            WHERE
            manager_qa_approver_id = '" . $user . "'
            AND secretariat_approver_status IS NOT NULL
            AND manager_qa_approver_status IS NULL
            AND deleted_at IS NULL");

            $dgm = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
            ( dgm_approver_id )
            FROM
            sga_teams
            WHERE
            dgm_approver_id = '" . $user . "'
            AND manager_qa_approver_status IS NOT NULL
            AND dgm_approver_status IS NULL
            AND deleted_at IS NULL");

            $gm = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
            ( gm_approver_id )
            FROM
            sga_teams
            WHERE
            gm_approver_id = '" . $user . "'
            AND dgm_approver_status IS NOT NULL
            AND gm_approver_status IS NULL
            AND deleted_at IS NULL");

            $presdir = DB::connection('ympimis_2')->SELECT("SELECT DISTINCT
            ( presdir_approver_id )
            FROM
            sga_teams
            WHERE
            presdir_approver_id = '" . $user . "'
            AND gm_approver_status IS NOT NULL
            AND presdir_approver_status IS NULL
            AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($dgm) > 0 || count($gm) > 0 || count($presdir) > 0) {
                $notif = count($manager) + count($dgm) + count($gm) + count($presdir);
            }
            return $notif;
        }
    }

    public function notif_ypm()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);

            $ypm = DB::connection('ympimis_2')->SELECT("SELECT
                    std_ypm_judges.judges_id,
                    std_ypm_judges.judges_approval 
                FROM
                    std_ypm_judges
                    JOIN ( SELECT periode, std_approval FROM std_ypm_teams GROUP BY periode, std_approval ORDER BY periode DESC LIMIT 1 ) AS teams ON teams.periode = std_ypm_judges.periode 
                WHERE
                    std_ypm_judges.judges_approval IS NULL 
                    AND teams.std_approval IS NOT NULL 
                    AND std_ypm_judges.judges_id = '".$user."' 
                    AND std_ypm_judges.judges_priority = 1 
                    LIMIT 1");

            $notif = 0;

            if (count($ypm) > 0) {
                $notif = count($ypm);
            }
            return $notif;
        }
    }

    public function notif_translation()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $translation_translates = db::connection('ympimis_2')
                ->table('translations')
                ->where('category', '=', 'translation')
                ->where('status', '=', 'Waiting')
                ->whereNull('deleted_at')->get();

            $notif = 0;
            $translate = 0;

            if (count($translation_translates) > 0 && ($user == "PI1212001" || $user == "PI1504002" || $user == "PI1402005" || $user == "PI1802029" || $user == "PI1410007")) {
                $translate = count($translation_translates);
            }

            if ($translate > 0) {
                $notif = $translate;
            }
            return $notif;
        }
    }

    public function notif_send_app()
    {
        if (Auth::user() !== null) {
            $send_app = [];
            if (in_array(Auth::user()->role_code, ['S-LOG'])) {
                $send_app = db::table('sending_applications')
                    ->whereIn('status', [3, 5])
                    ->whereNull('deleted_at')
                    ->get();

            } elseif (in_array(Auth::user()->role_code, ['C-LOG', 'OP-LOG', 'SL-LOG', 'L-LOG', 'L-WH', 'OP-WH-Exim'])) {
                $send_app = db::table('sending_applications')
                    ->where('status', 1)
                    ->whereNull('deleted_at')
                    ->get();

            }

            return count($send_app);
        } else {
            return 0;
        }

    }

    public function notif_eo()
    {

        if (Auth::user() !== null && str_contains(strtoupper(Auth::user()->username), 'PI')) {

            $outstanding = db::select("SELECT * FROM `extra_order_approvals`
            WHERE approver_id = '" . strtoupper(Auth::user()->username) . "'
            AND approval_status = 1");

            return count($outstanding);
        } else {
            return 0;
        }
    }

    public function notif_mutation_department()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $chief_asal = DB::SELECT('select nama_chief_asal from mutasi_ant_depts where posisi = "chf_asal" and nama_chief_asal = "' . $name . '" and remark = "2" and status is null');
            $manager_asal = DB::SELECT('select nama_manager_asal from mutasi_ant_depts where posisi = "mgr_asal" and nama_manager_asal = "' . $name . '" and remark = "2" and status is null');
            $dgm_asal = DB::SELECT('select nama_dgm_asal from mutasi_ant_depts where posisi = "dgm_asal" and nama_dgm_asal = "' . $name . '" and remark = "2" and status is null');
            $gm_asal = DB::SELECT('select nama_gm_asal from mutasi_ant_depts where posisi = "gm_asal" and nama_gm_asal = "' . $name . '" and remark = "2" and status is null');

            $chief_tujuan = DB::SELECT('select nama_chief_tujuan from mutasi_ant_depts where posisi = "chf_tujuan" and nama_chief_tujuan = "' . $name . '" and remark = "2" and status is null');
            $manager_tujuan = DB::SELECT('select nama_manager_tujuan from mutasi_ant_depts where posisi = "mgr_tujuan" and nama_manager_tujuan = "' . $name . '" and remark = "2" and status is null');
            $dgm_tujuan = DB::SELECT('select nama_dgm_tujuan from mutasi_ant_depts where posisi = "dgm_tujuan" and nama_dgm_tujuan = "' . $name . '" and remark = "2" and status is null');
            $gm_tujuan = DB::SELECT('select nama_gm_tujuan from mutasi_ant_depts where posisi = "gm_tujuan" and nama_gm_tujuan = "' . $name . '" and remark = "2" and status is null');

            $manager_hr = DB::SELECT('select nama_manager from mutasi_ant_depts where posisi = "mgr_hrga" and nama_manager = "' . $name . '" and remark = "2" and status is null');
            $direktur_hr = DB::SELECT('select nama_direktur_hr from mutasi_ant_depts where posisi = "dir_hr" and nama_direktur_hr = "' . $name . '" and remark = "2" and status is null');

            $notif = 0;

            if (count($chief_asal) > 0 || count($manager_asal) > 0 || count($dgm_asal) > 0 || count($gm_asal) > 0 || count($chief_tujuan) > 0 || count($manager_tujuan) > 0 || count($dgm_tujuan) > 0 || count($gm_tujuan) > 0 || count($manager_hr) > 0 || count($direktur_hr) > 0) {

                $notif = count($chief_asal) + count($manager_asal) + count($dgm_asal) + count($gm_asal) + count($chief_tujuan) + count($manager_tujuan) + count($dgm_tujuan) + count($gm_tujuan) + count($manager_hr) + count($direktur_hr);
            }

            return $notif;
        }
    }

    public function notif_mutation()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $chief_asal = DB::SELECT('select nama_chief_asal from mutasi_depts where posisi = "chf_asal" and nama_chief_asal = "' . $name . '" and remark = "2" and status is null');
            $chief_tujuan = DB::SELECT('select nama_chief_tujuan from mutasi_depts where posisi = "chf_tujuan" and nama_chief_tujuan = "' . $name . '" and remark = "2" and status is null');
            $manager = DB::SELECT('select nama_manager_tujuan from mutasi_depts where posisi = "mgr" and nama_manager_tujuan = "' . $name . '" and remark = "2" and status is null');
            $hr = DB::SELECT('select nama_manager from mutasi_depts where posisi = "hr" and nama_manager = "' . $name . '" and remark = "2" and status is null');

            $notif = 0;

            if (count($chief_asal) > 0 || count($chief_tujuan) > 0 || count($manager) > 0 || count($hr) > 0) {

                $notif = count($chief_asal) + count($chief_tujuan) + count($manager) + count($hr);
            }

            return $notif;
        }
    }

    public function notif_mirai_approval()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;
            $notif = 0;

            $tanggungan_notif = db::select('SELECT DISTINCT
        a.no_transaction,
        (
        SELECT COALESCE
        ( b.approver_name )
        FROM
        appr_approvals b
        WHERE
        b.request_id = a.no_transaction
        AND STATUS IS NULL
        ORDER BY
        id ASC
        LIMIT 1
        ) AS tanggungan
        FROM
        (
        SELECT DISTINCT
        no_transaction
        FROM
        appr_sends
        LEFT JOIN appr_approvals ON appr_sends.no_transaction = appr_approvals.request_id
        LEFT JOIN departments ON appr_sends.department = departments.department_name
        LEFT JOIN users ON appr_sends.created_by = users.id
        WHERE
        appr_sends.remark = "Open" UNION ALL
        SELECT
        request_id
        FROM
        appr_approvals
        LEFT JOIN appr_sends ON appr_approvals.request_id = appr_sends.no_transaction
        LEFT JOIN departments ON appr_sends.department = departments.department_name
        LEFT JOIN users ON appr_sends.created_by = users.id
        WHERE
        `status` IS NULL  and appr_sends.remark = "Open"
        GROUP BY
        request_id
        ) AS a
        WHERE
        ( SELECT COALESCE ( b.approver_id ) FROM appr_approvals b WHERE b.request_id = a.no_transaction AND b.`status` IS NULL LIMIT 1 ) = "' . $user . '"
        GROUP BY
        a.no_transaction');

            $notif = count($tanggungan_notif);
            return $notif;
        }
    }

    public function notif_po_canteen()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $manager = DB::SELECT("SELECT
            authorized2
            FROM
            canteen_purchase_orders
            WHERE
            authorized2 = '" . $user . "'
            AND posisi = 'manager_pch'
            AND approval_authorized2 IS NULL
            AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0) {
                $notif = count($manager);
            }
            return $notif;
        }
    }

    public function notif_pr_canteen()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $manager = DB::SELECT("SELECT manager from canteen_purchase_requisitions where manager = '" . $user . "' AND posisi = 'manager' AND approvalm IS NULL AND deleted_at IS NULL");

            $gm = DB::SELECT("SELECT gm from canteen_purchase_requisitions where gm = '" . $user . "' AND posisi = 'gm' AND approvalgm IS NULL AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($gm) > 0) {
                $notif = count($manager) + count($gm);
            }
            return $notif;
        }
    }

    public function notif_po()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $manager = DB::SELECT("SELECT authorized2 from acc_purchase_orders where authorized2 = '" . $user . "' AND posisi = 'manager_pch' AND approval_authorized2 IS NULL AND deleted_at IS NULL");

            $gm = DB::SELECT("SELECT authorized3 from acc_purchase_orders where authorized3 = '" . $user . "' AND posisi = 'dgm_pch' AND approval_authorized3 IS NULL AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($gm) > 0) {
                $notif = count($manager) + count($gm);
            }
            return $notif;
        }
    }

    public function notif_inv()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $acc_budget = DB::SELECT("SELECT approval_acc_budget FROM acc_investments WHERE posisi = 'acc_budget' AND deleted_at IS NULL");

            $acc_pajak = DB::SELECT("SELECT approval_acc_pajak FROM acc_investments WHERE posisi = 'acc_pajak' AND deleted_at IS NULL");

            $manager = DB::SELECT("SELECT approval_manager FROM acc_investments WHERE posisi = 'manager' AND SPLIT_STRING(approval_manager, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $dgm = DB::SELECT("SELECT approval_dgm FROM acc_investments WHERE posisi = 'dgm' AND SPLIT_STRING(approval_dgm, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $gm = DB::SELECT("SELECT approval_gm FROM acc_investments WHERE posisi = 'gm' AND SPLIT_STRING(approval_gm, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $manager_acc = DB::SELECT("SELECT approval_manager_acc FROM acc_investments WHERE posisi = 'manager_acc' AND SPLIT_STRING(approval_manager_acc, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $direktur_acc = DB::SELECT("SELECT approval_dir_acc FROM acc_investments WHERE posisi = 'direktur_acc' AND SPLIT_STRING(approval_dir_acc, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $presdir = DB::SELECT("SELECT approval_presdir FROM acc_investments WHERE posisi = 'presdir' AND SPLIT_STRING(approval_presdir, '/', 1) = '" . $user . "' AND deleted_at IS NULL");

            $notif = 0;

            $budget = 0;
            $pajak = 0;

            if (count($acc_budget) > 0 && $user == "PI0902001") {
                $budget = count($acc_budget);
            }
            if (count($acc_pajak) > 0 && $user == "PI9802001") {
                $pajak = count($acc_pajak);
            }

            if ($budget > 0 || $pajak > 0 || count($manager) > 0 || count($dgm) > 0 || count($gm) > 0 || count($manager_acc) > 0 || count($direktur_acc) > 0 || count($presdir) > 0) {

                $notif = $budget + $pajak + count($manager) + count($dgm) + count($gm) + count($manager_acc) + count($direktur_acc) + count($presdir);
            }

            return $notif;
        }
    }

    public function notif_pr()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $manager = DB::SELECT("SELECT manager from acc_purchase_requisitions where manager = '" . $user . "' AND posisi = 'manager' AND approvalm IS NULL AND deleted_at IS NULL");

            $dgm = DB::SELECT("SELECT dgm from acc_purchase_requisitions where dgm = '" . $user . "' AND posisi = 'dgm' AND approvaldgm IS NULL AND deleted_at IS NULL");

            $gm = DB::SELECT("SELECT gm from acc_purchase_requisitions where gm = '" . $user . "' AND posisi = 'gm' AND approvalgm IS NULL AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($dgm) > 0 || count($gm) > 0) {
                $notif = count($manager) + count($dgm) + count($gm);
            }
            return $notif;
        }
    }

    public function notif_kanagata()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
            SELECT DISTINCT
            pelaporan_kanagata_requests.request_id
            FROM
            pelaporan_kanagata_requests
            JOIN pelaporan_kanagata_approvals ON pelaporan_kanagata_requests.request_id = pelaporan_kanagata_approvals.request_id
            WHERE
            CONCAT( pelaporan_kanagata_approvals.`status` ) IS NULL
            AND pelaporan_kanagata_requests.remark != 'Rejected'
            ");

            $kanagata_request_id = [];
            foreach ($tanggungan as $tag) {
                array_push($kanagata_request_id, $tag->request_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($kanagata_request_id); $i++) {
                $tanggungan_user = db::select("
                SELECT
                ( SELECT approver_id FROM pelaporan_kanagata_approvals a WHERE a.id = ( pelaporan_kanagata_approvals.id ) ) next
                FROM
                pelaporan_kanagata_approvals
                WHERE
                `status` IS NULL
                AND request_id = '" . $kanagata_request_id[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_leave_request()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
            SELECT DISTINCT
            hr_leave_requests.request_id
            FROM
            hr_leave_requests
            JOIN hr_leave_request_approvals ON hr_leave_requests.request_id = hr_leave_request_approvals.request_id
            WHERE
            CONCAT( hr_leave_request_approvals.`status` ) IS NULL
            AND hr_leave_requests.remark != 'Rejected'
            ");

            $leave_request_id = [];
            foreach ($tanggungan as $tag) {
                array_push($leave_request_id, $tag->request_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($leave_request_id); $i++) {
                $tanggungan_user = db::select("
                SELECT
                ( SELECT approver_id FROM hr_leave_request_approvals a WHERE a.id = ( hr_leave_request_approvals.id ) ) next
                FROM
                hr_leave_request_approvals
                WHERE
                `status` IS NULL
                AND request_id = '" . $leave_request_id[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_mis_ticket()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
            SELECT DISTINCT
            tickets.ticket_id
            FROM
            tickets
            JOIN ticket_approvers ON tickets.ticket_id = ticket_approvers.ticket_id
            WHERE
            CONCAT( ticket_approvers.`status` ) IS NULL
            and tickets.deleted_at is null
            and tickets.`status` != 'Rejected'
            ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->ticket_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                $tanggungan_user = db::select("
                SELECT
                ( SELECT approver_id FROM ticket_approvers a WHERE a.id = ( ticket_approvers.id ) ) next
                FROM
                ticket_approvers
                WHERE
                `status` IS NULL
                AND ticket_id = '" . $ticket[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_mis_form()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
            SELECT DISTINCT
            ticket_forms.form_id
            FROM
            ticket_forms
            JOIN ticket_form_approvers ON ticket_forms.form_id = ticket_form_approvers.form_id
            WHERE
            ticket_form_approvers.`status` = 'Waiting'
            and ticket_forms.deleted_at is null
            and ticket_forms.`status` != 'Rejected'
            ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->form_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                $tanggungan_user = db::select("
                SELECT
                ( SELECT approver_id FROM ticket_form_approvers a WHERE a.id = ( ticket_form_approvers.id ) ) next
                FROM
                ticket_form_approvers
                WHERE
                (`status` = 'Waiting' or `status` IS NULL)
                AND form_id = '" . $ticket[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_tunjangan()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::select("
            SELECT DISTINCT
            uang_simpatis.request_id
            FROM
            uang_simpatis
            JOIN hr_approvals ON uang_simpatis.request_id = hr_approvals.request_id
            WHERE
            uang_simpatis.remark = 'Open'
            AND uang_simpatis.deleted_at IS NULL
            AND uang_simpatis.remark != 'Rejected'
            UNION ALL
            SELECT DISTINCT
            uang_keluargas.request_id
            FROM
            uang_keluargas
            JOIN hr_approvals ON uang_keluargas.request_id = hr_approvals.request_id
            WHERE
            uang_keluargas.remark = 'Open'
            AND uang_keluargas.deleted_at IS NULL
            AND uang_keluargas.remark != 'Rejected'
            ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->request_id);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                $tanggungan_user = db::select("
                SELECT
                ( SELECT approver_id FROM hr_approvals a WHERE a.id = ( hr_approvals.id ) ) next
                FROM
                hr_approvals
                WHERE
                ( `status` = 'Waiting' OR `status` IS NULL )
                AND request_id = '" . $ticket[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_chemical_wwt()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $notif = 0;

            $tanggungan = db::connection('ympimis_2')->select("
            SELECT DISTINCT
            slip_disposal
            FROM
            waste_details
            WHERE
            slip_disposal IS NOT NULL
            ");

            $ticket = [];
            foreach ($tanggungan as $tag) {
                array_push($ticket, $tag->slip_disposal);
            }

            $jumlah_tanggungan = 0;

            for ($i = 0; $i < count($ticket); $i++) {
                $tanggungan_user = db::connection('ympimis_2')->select("
                SELECT
                ( SELECT approver_id FROM waste_approvals a WHERE a.id = ( waste_approvals.id ) ) next
                FROM
                waste_approvals
                WHERE
                ( `status` = 'Waiting' OR `status` IS NULL )
                AND slip_disposal = '" . $ticket[$i] . "'
                ORDER BY
                id ASC
                LIMIT 1
                ");

                if (count($tanggungan_user) > 0 && $tanggungan_user[0]->next == $user) {
                    $jumlah_tanggungan += 1;
                }
            }

            $notif = $jumlah_tanggungan;

            return $notif;
        }
    }

    public function notif_payment_request()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $manager = DB::SELECT("SELECT manager from acc_payment_requests where manager = '" . $user . "' AND posisi = 'manager' AND status_manager IS NULL AND deleted_at IS NULL");

            $gm = DB::SELECT("SELECT gm from acc_payment_requests where gm = '" . $user . "' AND posisi = 'gm' AND status_gm IS NULL AND deleted_at IS NULL");

            $notif = 0;

            if (count($manager) > 0 || count($gm) > 0) {
                $notif = count($manager) + count($gm);
            }
            return $notif;
        }
    }

    public function notif_ejor()
    {
        $appr = DB::SELECT("SELECT count(ejor_forms.form_id) as jml from ejor_forms
        left join ejor_form_approvers on ejor_forms.form_id = ejor_form_approvers.form_id
        where ejor_forms.status = 'Approval'
        and ejor_form_approvers.approve_at is null and ejor_form_approvers.id in (
        SELECT min(id)
        FROM ejor_form_approvers
        WHERE approve_at is null
        GROUP BY form_id
    ) and approver_id = '" . Auth::user()->username . "'");

        $notif = 0;

        if (count($appr) > 0) {
            $notif = $appr[0]->jml;
        }

        return $notif;
    }

    public function notif_fa_registrasi()
    {
        $notif = 0;
        $reg_fa = db::select("SELECT fixed_asset_invoices.form_id, fixed_asset_registrations.form_number  FROM `fixed_asset_invoices`
        left join fixed_asset_registrations on fixed_asset_invoices.form_id = fixed_asset_registrations.form_number
        where fixed_asset_registrations.form_number is null and created_for = '" . Auth::user()->username . "' and fixed_asset_invoices.deleted_at is null");

        $app_fa = [];

        if (Auth::user()->username == 'PI0905001') {
            $app_fa = db::select("SELECT form_number from fixed_asset_registrations where update_fa_at is null and deleted_at is null");
        }

        $manager_app = db::select("SELECT form_number from fixed_asset_registrations where manager_app LIKE '" . Auth::user()->username . "%' and manager_app_date is null and update_fa_at is not null and deleted_at is null");

        $manager_acc = db::select("SELECT form_number from fixed_asset_registrations where manager_acc LIKE '" . Auth::user()->username . "%' and manager_acc_date is null and manager_app_date is not null and deleted_at is null");

        if (count($reg_fa) > 0 || count($app_fa) > 0 || count($manager_app) > 0 || count($manager_acc) > 0) {
            $notif = count($reg_fa) + count($app_fa) + count($manager_app) + count($manager_acc);
        }

        return $notif;
    }

    public function notif_fa_disposal()
    {
        $notif = 0;
        $app_fa = [];

        if (strtoupper(Auth::user()->username) == 'PI0905001') {
            $app_fa = db::select("SELECT form_number from fixed_asset_disposals where fa_app_date is null and pic_app_date is not null and deleted_at is null");

            $notif += count($app_fa);
        } else {
            $manager = db::select('SELECT form_number FROM fixed_asset_disposals where manager_app LIKE  "' . Auth::user()->username . '%" and manager_app_date is null and fa_app_date is not null and deleted_at is null');

            $manager_dispo = db::select('SELECT form_number FROM fixed_asset_disposals where manager_disposal_app LIKE  "' . Auth::user()->username . '%" and manager_disposal_app_date is null and manager_app_date is not null and deleted_at is null');

            $dgm = db::select('SELECT form_number FROM fixed_asset_disposals where dgm_app LIKE  "' . Auth::user()->username . '%" and dgm_app_date is null and manager_disposal_app_date is not null and deleted_at is null');

            $gm = db::select('SELECT form_number FROM fixed_asset_disposals where gm_app LIKE  "' . Auth::user()->username . '%" and gm_app_date is null and dgm_app_date is not null and deleted_at is null');

            $manger_acc = db::select('SELECT form_number FROM fixed_asset_disposals where manager_acc_app LIKE  "' . Auth::user()->username . '%" and manager_acc_app_date is null and gm_app_date is not null and deleted_at is null');

            $dir_fin = db::select('SELECT form_number FROM fixed_asset_disposals where director_fin_app LIKE  "' . Auth::user()->username . '%" and director_fin_app_date is null and manager_acc_app_date is not null and deleted_at is null');

            $dir = db::select('SELECT form_number FROM fixed_asset_disposals where presdir_app LIKE  "' . Auth::user()->username . '%" and presdir_app_date is null and director_fin_app_date is not null and deleted_at is null');

            $new_pic = db::select('SELECT form_number FROM fixed_asset_disposals where new_pic_app LIKE  "' . Auth::user()->username . '%" and new_pic_app_date is null and presdir_app_date is not null and deleted_at is null');

            $notif += count($manager) + count($manager_dispo) + count($dgm) + count($gm) + count($manger_acc) + count($dir_fin) + count($dir) + count($new_pic);
        }

        return $notif;
    }

    public function notif_receive_label_fa()
    {
        $notif = 0;

        $receive_label = db::select("SELECT fixed_asset_registrations.form_number, fixed_asset_labels.status, fixed_asset_labels.remark FROM fixed_asset_registrations
            left join fixed_asset_labels on fixed_asset_registrations.form_number = fixed_asset_labels.remark
            left join fixed_asset_invoices on fixed_asset_invoices.form_id = fixed_asset_registrations.form_number
            where fixed_asset_labels.status = 'printed' and fixed_asset_invoices.created_for = '" . Auth::user()->username . "'");

        $receive_label2 = db::select("SELECT fixed_asset_disposals.form_number, fixed_asset_labels.status, fixed_asset_labels.remark FROM fixed_asset_disposals
            left join fixed_asset_labels on fixed_asset_disposals.form_number = fixed_asset_labels.remark
            where fixed_asset_labels.status = 'printed' and fixed_asset_disposals.created_by = '" . Auth::user()->username . "'");

        $receive_label3 = db::select("SELECT fixed_asset_transfers.form_number, fixed_asset_labels.status, fixed_asset_labels.remark FROM fixed_asset_transfers
            left join fixed_asset_labels on fixed_asset_transfers.form_number = fixed_asset_labels.remark
            where fixed_asset_labels.status = 'printed' and fixed_asset_transfers.created_by = '" . Auth::user()->username . "'");

        $notif = count($receive_label) + count($receive_label2) + count($receive_label3);

        return $notif;
    }

    public function notif_cvm()
    {
        $notif = 0;

        $notif_press = db::select("SELECT material_number, material_description, GROUP_CONCAT(point SEPARATOR '|') as pn, GROUP_CONCAT(point_description SEPARATOR '|') des, max(check_point) cp, max(check_time) as check_time , min(remark) as stat, min(fu_number) as form_number
        FROM sanding_checks
        WHERE id IN (
        SELECT min(id)
        FROM sanding_checks
        where `status` = 'NG' and (remark is null OR remark = 'Rejected' OR remark = 'Waiting')
        GROUP BY material_number, material_description, point, point_description
        )
        GROUP BY material_number, material_description
        ORDER BY check_time asc, check_point asc");

        if (count($notif_press) > 0 && strtoupper(Auth::user()->username) == 'PI0704009') {
            $notif = count($notif_press);
        }

        $notif_sanding = db::select("SELECT material_number, material_description, GROUP_CONCAT(point SEPARATOR '|') as pn, GROUP_CONCAT(point_description SEPARATOR '|') des, max(check_point) cp, max(check_time) as check_time , min(remark) as stat, min(fu_number) as form_number
        FROM sanding_checks
        WHERE id IN (
        SELECT min(id)
        FROM sanding_checks
        where `status` = 'NG' and remark = 'Waiting'
        GROUP BY material_number, material_description, point, point_description
        )
        GROUP BY material_number, material_description
        ORDER BY check_time asc, check_point asc");

        if (count($notif_sanding) > 0 && (strtoupper(Auth::user()->username) == 'PI9811006' || strtoupper(Auth::user()->username) == 'PI0005016')) {
            $notif = count($notif_sanding);
        }

        return $notif;
    }

    public function notif_ejor_ev()
    {
        $notif = 0;

        $ev_ejor = db::select("SELECT * from ejor_forms where `status` = 'Verifying' and deleted_at is null");

        if (count($ev_ejor) > 0 && strtoupper(Auth::user()->username) == 'PI1106001') {
            $notif = count($ev_ejor);
        }

        return $notif;
    }

}
