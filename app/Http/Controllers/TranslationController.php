<?php

namespace App\Http\Controllers;

use App\CodeGenerator;
use App\Http\Controllers\Controller;
use App\Mail\SendEmail;
use App\Sakurentsu;
use App\SakurentsuThreeM;
use App\User;
use Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Response;

class TranslationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function indexResume()
    {
        $title = "Translation Resume";
        $title_jp = "まとめ";
        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $pics = db::connection('ympimis_2')->table('translation_pics')->whereNull('deleted_at')->get();

        return view('translation.translation_resume', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'pics' => $pics,
        ))->with('page', 'Translation')->with('head', 'Translation Resume');
    }

    public function indexTranslation()
    {
        $title = "TRACING (Translation Control & Monitoring System)";
        $title_jp = "翻訳管理システム";

        $departments = db::table('departments')->orderBy('department_name', 'ASC')->get();
        $pics = db::connection('ympimis_2')->table('translation_pics')->whereNull('deleted_at')->get();
        $user = User::where('id', '=', Auth::id())->first();
        
        $employee_sync = db::table('employee_syncs')->leftJoin('departments', 'departments.department_name', '=', 'employee_syncs.department')->select('departments.department_name', 'departments.department_shortname')->where('employee_id', '=', Auth::user()->username)->first();

        if (!$employee_sync) {
            $employee_sync = array();
        }

        return view('translation.translation_index', array(
            'title' => $title,
            'title_jp' => $title_jp,
            'departments' => $departments,
            'pics' => $pics,
            'user' => $user,
            'employee_sync' => $employee_sync,
        ))->with('page', 'Translation')->with('head', 'Translation Request');
    }

    public function fetchTranslation(Request $request)
    {
        $translation_translates = db::connection('ympimis_2')->table('translations')
            ->where('category', '=', 'translation')
            ->whereNull('deleted_at');

        $translation_meetings = db::connection('ympimis_2')->table('translations')
            ->where('category', '=', 'meeting')
            ->whereNull('deleted_at');

        $first = date('Y-m-01');
        $last = date('Y-m-t');

        if ($request->get('cat') == 'resume') {
            if ($request->get('date_from') != "") {
                $first = date('Y-m-d', strtotime($request->get('date_from')));
                $last = date('Y-m-d', strtotime($request->get('date_to')));
            }

            $translation_translates = $translation_translates->where('status', '=', 'Finished')
                ->where('finished_at', '>=', $first)
                ->where('finished_at', '<=', $last);

            $translation_meetings = $translation_meetings->where('status', '=', 'Finished')
                ->where('finished_at', '>=', $first)
                ->where('finished_at', '<=', $last);
        }

        if (!str_contains(Auth::user()->role_code, 'INT') || !str_contains(Auth::user()->role_code, 'MIS')) {
            // $employee = db::table('employee_syncs')->where('employee_id', '=', Auth::user()->username)
            // ->first();

            // $translation_translates = $translation_translates->where('department_name', '=', $employee->department)
            // ->orWhere('requester_id', '=', $employee->employee_id);

            // $translation_meetings = $translation_meetings->where('department_name', '=', $employee->department);
        }

        $translation_translates = $translation_translates->orderBy('translation_id', 'DESC')->get();
        $translation_meetings = $translation_meetings->orderBy('translation_id', 'DESC')->get();

        $translations = array();

        foreach ($translation_translates as $translation_translate) {
            array_push($translations, [
                'id' => $translation_translate->id,
                'translation_id' => $translation_translate->translation_id,
                'category' => $translation_translate->category,
                'document_type' => $translation_translate->document_type,
                'title' => $translation_translate->title,
                'number_page' => $translation_translate->number_page,
                'request_date' => $translation_translate->request_date,
                'finished_at' => $translation_translate->finished_at,
                'request_date_from' => $translation_translate->request_date,
                'request_date_to' => $translation_translate->request_date,
                'request_time_from' => $translation_translate->request_date,
                'request_time_to' => $translation_translate->request_date,
                'std_time' => $translation_translate->std_time,
                'load_time' => $translation_translate->load_time,
                'requester_id' => $translation_translate->requester_id,
                'requester_name' => $translation_translate->requester_name,
                'requester_email' => $translation_translate->requester_email,
                'department_name' => $translation_translate->department_name,
                'department_shortname' => $translation_translate->department_shortname,
                'translation_request' => $translation_translate->translation_request,
                'translation_result' => $translation_translate->translation_result,
                'pic_id' => $translation_translate->pic_id,
                'pic_name' => $translation_translate->pic_name,
                'status' => $translation_translate->status,
                'remark' => $translation_translate->remark,
                'deleted_at' => $translation_translate->deleted_at,
                'created_at' => $translation_translate->created_at,
                'updated_at' => $translation_translate->updated_at,
            ]);
        }

        foreach ($translation_meetings as $translation_meeting) {
            array_push($translations, [
                'id' => $translation_meeting->id,
                'translation_id' => $translation_meeting->translation_id,
                'category' => $translation_meeting->category,
                'document_type' => $translation_meeting->document_type,
                'title' => $translation_meeting->title,
                'number_page' => $translation_meeting->number_page,
                'request_date' => $translation_meeting->request_date,
                'finished_at' => $translation_meeting->finished_at,
                'request_date_from' => date('Y-m-d H:i', strtotime($translation_meeting->request_date_from)),
                'request_date_to' => date('Y-m-d H:i', strtotime($translation_meeting->request_date_to)),
                'request_time_from' => date('H:i', strtotime($translation_meeting->request_date_from)),
                'request_time_to' => date('H:i', strtotime($translation_meeting->request_date_to)),
                'std_time' => $translation_meeting->std_time,
                'load_time' => $translation_meeting->load_time,
                'requester_id' => $translation_meeting->requester_id,
                'requester_name' => $translation_meeting->requester_name,
                'requester_email' => $translation_meeting->requester_email,
                'department_name' => $translation_meeting->department_name,
                'department_shortname' => $translation_meeting->department_shortname,
                'translation_request' => $translation_meeting->translation_request,
                'translation_result' => $translation_meeting->translation_result,
                'pic_id' => $translation_meeting->pic_id,
                'pic_name' => $translation_meeting->pic_name,
                'status' => $translation_meeting->status,
                'remark' => $translation_meeting->remark,
                'deleted_at' => $translation_meeting->deleted_at,
                'created_at' => $translation_meeting->created_at,
                'updated_at' => $translation_meeting->updated_at,
            ]);
        }

        $translation_attachments = db::connection('ympimis_2')->table('translation_attachments')
            ->whereNull('deleted_at')
            ->get();

        $translation_logs = db::connection('ympimis_2')->table('translation_logs')
            ->whereNull('deleted_at')
            ->get();

        $weekly_calendars = db::table('weekly_calendars')->where('week_date', '>=', $first)
            ->where('week_date', '<=', $last)
            ->select('week_date', db::raw('date_format(week_date, "%d") as day_date'))
            ->orderBy('week_date', 'ASC')
            ->get();

        $response = array(
            'status' => true,
            'weekly_calendars' => $weekly_calendars,
            'translations' => $translations,
            'translation_attachments' => $translation_attachments,
            'translation_logs' => $translation_logs,
        );
        return Response::json($response);
    }

    public function fetchLoad()
    {
        $now = date('Y-m-d');

        $translation_pics = db::connection('ympimis_2')
            ->select("SELECT *
            FROM
            translation_pics
            WHERE
            deleted_at IS NULL");

        $translations = db::connection('ympimis_2')
            ->select("SELECT
            category,
            pic_id,
            pic_name,
            sum( load_time ) AS load_time
            FROM
            translations
            WHERE
            deleted_at IS NULL
            AND STATUS = 'Assigned'
            AND category = 'translation'
            GROUP BY
            category,
            pic_id,
            pic_name
            ");

        $meetings = db::connection('ympimis_2')
            ->select("SELECT
            category,
            pic_id,
            pic_name,
            sum( load_time ) AS load_time
            FROM
            translations
            WHERE
            deleted_at IS NULL
            AND category = 'meeting'
            AND finished_at = '" . $now . "'
            GROUP BY
            category,
            pic_id,
            pic_name");

        $loads = array();

        foreach ($translation_pics as $translation_pic) {
            $load_time_translation = 0;
            $load_time_meeting = 0;

            foreach ($translations as $translation) {
                if ($translation->pic_id == $translation_pic->employee_id) {
                    $load_time_translation += $translation->load_time;
                }
            }

            foreach ($meetings as $meeting) {
                if ($meeting->pic_id == $translation_pic->employee_id) {
                    $load_time_meeting += $meeting->load_time;
                }
            }

            array_push($loads, [
                'pic_id' => $translation_pic->employee_id,
                'pic_name' => $translation_pic->employee_name,
                'load_time_translation' => $load_time_translation,
                'load_time_meeting' => $load_time_meeting,
                'load_time_total' => $load_time_translation + $load_time_meeting,
            ]);
        }

        $response = array(
            'status' => true,
            'loads' => $loads,
        );
        return Response::json($response);
    }

    public function deleteTranslation(Request $request)
    {
        try {
            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_attachments = db::connection('ympimis_2')->table('translation_attachments')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_logs = db::connection('ympimis_2')->table('translation_logs')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'deleted_at' => date('Y-m-d H:i:s'),
                ]);

            $response = array(
                'status' => true,
                'message' => 'Translation request has been deleted.',
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

    public function editTranslation(Request $request)
    {
        try {
            if ($request->input('document_type') == 'Biasa') {
                $std_time = 40;
            } else if ($request->input('document_type') == 'Khusus') {
                $std_time = 50;
            } else if ($request->input('document_type') == 'Rahasia') {
                $std_time = 60;
            }

            $load_time = $std_time * $request->input('number_page');

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'category' => $request->input('category'),
                    'document_type' => $request->input('document_type'),
                    'title' => $request->input('title'),
                    'number_page' => $request->input('number_page'),
                    'request_date' => $request->input('request_date'),
                    'std_time' => $std_time,
                    'load_time' => $load_time,
                    'pic_id' => $request->input('pic_id'),
                    'pic_name' => $request->input('pic_name'),
                    'department_name' => $request->input('department_name'),
                    'department_shortname' => $request->input('department_shortname'),
                    'translation_request' => $request->input('translation_request'),
                    'status' => $request->input('status'),
                    'remark' => $request->input('remark'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $request->get('translation_id'),
                'status' => $request->input('status'),
                'remark' => 'Edited',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Translation request has been edited.',
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

    public function editMeeting(Request $request)
    {
        try {
            $request_date = explode(' - ', $request->get('request_date'));
            $load_time = round(abs(strtotime($request_date[1]) - strtotime($request_date[0])) / 60, 2);

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'category' => $request->get('category'),
                    'document_type' => $request->get('document_type'),
                    'load_time' => $load_time,
                    'request_date' => date('Y-m-d', strtotime($request_date[0])),
                    'finished_at' => date('Y-m-d', strtotime($request_date[0])),
                    'request_date_from' => $request_date[0],
                    'request_date_to' => $request_date[1],
                    'department_name' => $request->get('department_name'),
                    'department_shortname' => $request->get('department_shortname'),
                    'pic_id' => $request->get('pic_id'),
                    'pic_name' => $request->get('pic_name'),
                    'status' => 'Finished',
                    'remark' => $request->get('remark'),
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $request->get('translation_id'),
                'status' => 'Finished',
                'remark' => 'Edited',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $response = array(
                'status' => true,
                'message' => 'Meeting has been edited.',
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

    public function inputMeeting(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'translation')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $translation_id = $code_generator->prefix . $number;

            $request_date = explode(' - ', $request->get('request_date'));
            $date = date('Y-m-d', strtotime($request_date[0]));
            $date_from = date('Y-m-d H:i:s', strtotime($request_date[0]));
            $date_to = date('Y-m-d H:i:s', strtotime($request_date[1]));

            $load_time = round(abs(strtotime($date_to) - strtotime($date_from)) / 60, 2);

            $translation = db::connection('ympimis_2')->table('translations')->insert([
                'translation_id' => $translation_id,
                'category' => $request->get('category'),
                'document_type' => $request->get('document_type'),
                'load_time' => $load_time,
                'request_date' => $date,
                'finished_at' => $date,
                'request_date_from' => $date_from,
                'request_date_to' => $date_to,
                'department_name' => $request->get('department_name'),
                'department_shortname' => $request->get('department_shortname'),
                'pic_id' => $request->get('pic_id'),
                'pic_name' => $request->get('pic_name'),
                'status' => 'Finished',
                'remark' => $request->get('remark'),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $translation_id,
                'status' => 'Finished',
                'remark' => 'Created',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $response = array(
                'status' => true,
                'message' => 'Meeting has been added.',
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

    public function inputPIC(Request $request)
    {
        try {
            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->update([
                    'pic_id' => $request->get('pic_id'),
                    'pic_name' => $request->get('pic_name'),
                    'status' => 'Assigned',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $request->get('translation_id'),
                'status' => 'Assigned',
                'remark' => 'Assigned',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $translation_mail = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->first();

            $mail_to = db::connection('ympimis_2')->table('translation_pics')
                ->where('employee_id', '=', $translation_mail->pic_id)
                ->first();

            // --- CEK SAKURENTSU --
            $sakurentsu = Sakurentsu::where('position', '=', 'interpreter')
                ->select('sakurentsu_number')
                ->get();

            foreach ($sakurentsu as $sk) {
                if ($translation_mail->remark == $sk->sakurentsu_number) {
                    Sakurentsu::where('sakurentsu_number', '=', $sk->sakurentsu_number)
                        ->update([
                            'translator' => $request->get('pic_name'),
                            'position' => 'interpreter2',
                        ]);
                }
            }

            // --- CEK 3M ---
            $three_m = SakurentsuThreeM::where('remark', '=', '1')
                ->select('form_identity_number')
                ->get();

            foreach ($three_m as $t_m) {
                if ($translation_mail->remark == $t_m->form_identity_number) {
                    SakurentsuThreeM::where('form_identity_number', '=', $t_m->form_identity_number)
                        ->update([
                            'translator' => $request->get('pic_id') . '/' . $request->get('pic_name'),
                        ]);
                }
            }

            $data = [
                'translation' => $translation_mail,
            ];

            Mail::to([$mail_to->email])
            // ->bcc(['aditya.agassi@music.yamaha.com'])
                ->send(new SendEmail($data, 'translation_assignment'));

            $response = array(
                'status' => true,
                'pic_id' => $request->get('pic_id'),
                'pic_name' => $request->get('pic_name'),
                'message' => 'PIC has been assigned. Email has been sent to assignee.',
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

    public function approvalTranslation(Request $request)
    {
        if ($request->get('status') == 'Approved') {

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->get('translation_id'))
                ->first();

            return view('translation.translation_notification', array(
                'title' => 'Translation Assignment Process',
                'title_jp' => '',
                'translation' => $translation,
                'code' => 1,
            ))->with('page', 'MIS Ticket')->with('head', 'Ticket Confirmation');
        } else {
            exit;
        }
    }

    public function inputResult(Request $request)
    {
        try {
            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $request->input('translation_id'))
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
                ->where('translation_id', '=', $request->input('translation_id'))
                ->update([
                    'translation_request' => $request->input('translation_request'),
                    'translation_result' => $request->input('translation_result'),
                    'finished_at' => date('Y-m-d'),
                    'status' => 'Finished',
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);

            $translation_log = db::connection('ympimis_2')->table('translation_logs')->insert([
                'translation_id' => $request->input('translation_id'),
                'status' => 'Finished',
                'remark' => 'Finished',
                'updated_by' => Auth::user()->username,
                'updated_by_name' => Auth::user()->name,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            $filenames = array();
            $file_destination = 'files/translation';
            $attachments = [];
            if (count($request->input('attachments')) > 0) {
                $attachments = explode(',', $request->input('attachments'));
            }

            $file_decode = [];

            for ($i = 0; $i < count($attachments); $i++) {
                $translation_attachment = db::connection('ympimis_2')->table('translation_attachments')
                    ->where('id', '=', $attachments[$i])
                    ->first();

                $translation_filename = explode('.', $translation_attachment->file_name);

                $file = $request->file('attachment_' . $attachments[$i]);
                $nama = $file->getClientOriginalName();

                $extension = pathinfo($nama, PATHINFO_EXTENSION);

                $filename = $translation_filename[0] . '_jp.' . $extension;
                $file->move($file_destination, $filename);
                array_push($filenames, $filename);

                $update_attachment = db::connection('ympimis_2')->table('translation_attachments')
                    ->where('id', '=', $attachments[$i])
                    ->update([
                        'file_name_result' => $filename,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }

            // --- CEK SAKURENTSU --
            $sakurentsu = Sakurentsu::where('position', '=', 'interpreter2')
                ->select('sakurentsu_number')
                ->get();

            $sk_stat = '';

            foreach ($sakurentsu as $sk) {
                if ($translation->remark == $sk->sakurentsu_number) {
                    $file_decode = json_encode($filenames);
                    if (count($file_decode) == 0) {
                        $file_decode = null;
                    }

                    $str = str_replace('<p>', '', $request->input('translation_result'));
                    $str = str_replace('</p>', '', $str);

                    Sakurentsu::where('sakurentsu_number', '=', $sk->sakurentsu_number)
                        ->update([
                            'title' => $str,
                            'translator' => Auth::user()->name,
                            'position' => 'PC1',
                            'file_translate' => $file_decode,
                            'translate_date' => date('Y-m-d'),
                            'status' => 'approval',
                        ]);

                    $sk_stat = $sk->sakurentsu_number;
                }
            }

            if ($sk_stat != '') {
                $isimail = "select * FROM sakurentsus where sakurentsus.sakurentsu_number = '" . $sk_stat . "'";
                $sakurentsuisi = db::select($isimail);

                Mail::to(['mamluatul.atiyah@music.yamaha.com', 'farizca.nurma@music.yamaha.com'])->bcc(['nasiqul.ibat@music.yamaha.com'])->send(new SendEmail($sakurentsuisi, 'sakurentsu'));
            }

            $data = [
                'translation' => $translation,
                'filenames' => $filenames,
            ];

            Mail::to([$translation->requester_email])
            // ->bcc(['aditya.agassi@music.yamaha.com'])
                ->send(new SendEmail($data, 'translation_result'));

            $response = array(
                'status' => true,
                'message' => 'Translation result has been updated',
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

    public function inputTranslation(Request $request)
    {
        try {
            $code_generator = CodeGenerator::where('note', '=', 'translation')->first();
            $number = sprintf("%'.0" . $code_generator->length . "d", $code_generator->index + 1);
            $translation_id = $code_generator->prefix . $number;
            $filename = null;

            $std_time = 0;
            $load_time = 0;

            if ($request->input('document_type') == 'Biasa') {
                $std_time = 40;
            } else if ($request->input('document_type') == 'Khusus') {
                $std_time = 50;
            } else if ($request->input('document_type') == 'Rahasia') {
                $std_time = 60;
            }

            $load_time = $std_time * $request->input('number_page');

            $status = "Waiting";

            if (strlen($request->input('pic_id')) > 0) {
                $status = "Assigned";
            }

            $translation = db::connection('ympimis_2')->table('translations')->insert([
                'translation_id' => $translation_id,
                'category' => $request->input('category'),
                'document_type' => $request->input('document_type'),
                'title' => $request->input('title'),
                'number_page' => $request->input('number_page'),
                'request_date' => $request->input('request_date'),
                'std_time' => $std_time,
                'load_time' => $load_time,
                'requester_id' => Auth::user()->username,
                'requester_name' => Auth::user()->name,
                'requester_email' => Auth::user()->email,
                'pic_id' => $request->input('pic_id'),
                'pic_name' => $request->input('pic_name'),
                'department_name' => $request->input('department_name'),
                'department_shortname' => $request->input('department_shortname'),
                'translation_request' => $request->input('translation_request'),
                'status' => $status,
                'remark' => $request->input('remark'),
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

            $file_destination = 'files/translation';
            $filenames = array();

            $count_file = 0;
            for ($i = 0; $i < $request->input('count_attachment'); $i++) {
                $count_file++;
                $file = $request->file('attachment_' . $i);
                // $filename = $translation_id.'_'.$request->input('file_name_'.$i).'.'.$request->input('extension_'.$i);
                $filename = $translation_id . '_Document-' . $count_file . '.' . $request->input('extension_' . $i);
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

            $code_generator->index = $code_generator->index + 1;
            $code_generator->save();

            $translation = db::connection('ympimis_2')->table('translations')
                ->where('translation_id', '=', $translation_id)
                ->first();

            $data = [
                'translation' => $translation,
                'filenames' => $filenames,
            ];

            if (!str_contains(Auth::user()->role_code, 'INT')) {

                $translation_pics = db::connection('ympimis_2')->table('translation_pics')
                    ->whereNull('deleted_at')
                    ->get();

                $translators = array();

                foreach ($translation_pics as $translation_pic) {
                    array_push($translators, $translation_pic->email);
                }

                Mail::to($translators)
                // ->bcc(['aditya.agassi@music.yamaha.com'])
                    ->send(new SendEmail($data, 'translation_request'));

            }

            $response = array(
                'status' => true,
                'message' => 'Request has been created.',
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

    public function getNotifTranslation()
    {
        if (Auth::user() !== null) {
            $user = strtoupper(Auth::user()->username);
            $name = Auth::user()->name;
            $role = Auth::user()->role_code;

            $translation_translates = db::connection('ympimis_2')
                ->table('translations')
                ->where('category', '=', 'translation')
                ->where('status', '=', 'Waiting')
                ->whereNull('deleted_at');

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
}
