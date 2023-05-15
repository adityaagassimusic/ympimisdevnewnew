@extends('layouts.master')
@section('stylesheets')
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            text-align: center;
        }

        tbody>tr>td {
            text-align: center;
        }

        tfoot>tr>th {
            text-align: center;
        }

        td:hover {
            overflow: visible;
        }

        table.table-bordered {
            border: 1px solid black;
            margin-bottom: 5px;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            margin: 0;
            padding: 0;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(180, 180, 180);
            font-size: 12px;
            background-color: rgb(240, 240, 240);
            padding-top: 2px;
            padding-bottom: 2px;
            padding-left: 3px;
            padding-right: 3px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #loading,
        #error {
            display: none;
        }

        .marquee {
            width: 100%;
            overflow: hidden;
            margin: 0px;
            padding: 0px;
            text-align: center;
            height: 35px;
        }
    </style>
@stop
@section('header')
    <section class="content-header" style="padding: 0; margin:0;">
        <div class="marquee">
            <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span
                    style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span
                    style="font-size:22px;"><b>A</b></span>cquisition of <span
                    style="font-size:22px;"><b>I</b></span>nformation</span>
            <br>
            <b><span style="font-size: 20px;" class="text-purple">
                    <img src="{{ url('images/logo_mirai_bundar.png') }}" height="24px">
                    製 造 の リ ア ル タ イ ム 情 報
                    <img src="{{ url('images/logo_mirai_bundar.png') }}" height="24px">
                </span></b>
        </div>
    </section>
@endsection

@section('content')

    <section class="content" style="padding-top: 0;">
        <div class="row">
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Human Resources Department<br />人事課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('files/Authorization Matrix.pdf') }}">
                                    <i class="fa fa-caret-right"></i> Authorization Matrix (権限マトリックス)
                                </a>
                                <br>
                                <a href="{{ url('index/general/online_transportation') }}">
                                    <i class="fa fa-caret-right"></i> Attendance & Transport (出社・移動費)
                                </a>
                                <br>
                                <a href="{{ url('index/general/surat_dokter') }}">
                                    <i class="fa fa-caret-right"></i> Surat Dokter (診断書)
                                </a>
                                <br>
                                <a href="{{ url('index/human_resource/leave_request') }}">
                                    <i class="fa fa-caret-right"></i> Surat Izin Keluar (外出申請書)
                                </a>
                                <br>
                                <a href="{{ url('human_resource') }}">
                                    <i class="fa fa-caret-right"></i> Permohonan Tunjangan (アローワンス申請)
                                </a>
                                <br>
                                <a href="{{ url('dashboard/mutasi') }}">
                                    <i class="fa fa-caret-right"></i> Mutasi Satu Dept. (部門内部署移動)
                                </a>
                                <br>
                                <a href="{{ url('dashboard_ant/mutasi') }}">
                                    <i class="fa fa-caret-right"></i> Mutasi Antar Dept. (部門跨ぐ部署移動)
                                </a>
                                <br>
                                <a href="{{ url('index/general/agreement') }}">
                                    <i class="fa fa-caret-right"></i> Control Law & Agreement (法規制及び契約書の管理)
                                </a>
                                <br>
                                <a href="{{ url('index/checklist_container_security') }}">
                                    <i class="fa fa-caret-right"></i> Checklist Pengecekan Kontainer (コンテナーチェックのチェックリスト)
                                </a>
                                <br>
                                <a href="{{ url('index/filosofi') }}">
                                    <i class="fa fa-caret-right"></i> Training Yamaha Philosophy (教育に対するモニタリング)
                                </a>
                                <br>
                                <a href="{{ url('index/live/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> LIVE (従業員の声) の表示
                                </a>
                                <br>
                                <a href="{{ url('index/qnaHR/resume') }}">
                                    <i class="fa fa-caret-right"></i> HR Question Monitoring (HR 質問監視)
                                </a>
                                <br>
                                <a href="{{ url('index/human_resource/let') }}">
                                    <i class="fa fa-caret-right"></i> Leader Training Evaluation (職長教育の評価)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Overtime Information (残業の情報)</span>
                                {{-- <br>
                                <a href="{{ url('index/overtime/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Overtime Monitoring (残業管理)
                                </a> --}}
                                <br>
                                <a href="{{ url('index/report/overtime_monthly_fq') }}">
                                    <i class="fa fa-caret-right"></i> Overtime vs Forecast (残業対予報)
                                </a>
                                <br>
                                <a href="{{ url('index/report/overtime_monthly_bdg') }}">
                                    <i class="fa fa-caret-right"></i> Overtime vs Budget (残業対予算)
                                </a>
                                <br>
                                <a href="{{ url('index/report/overtime_section') }}">
                                    <i class="fa fa-caret-right"></i> Overtime By CC (コストセンター別の残業)
                                </a>
                                <br>
                                <a href="{{ url('index/report/overtime_data') }}">
                                    <i class="fa fa-caret-right"></i> Overtime Data (残業データ)
                                </a>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Manpower Information (人工の情報)</span>
                                <br>
                                <a href="{{ url('index/manpower/information') }}">
                                    <i class="fa fa-caret-right"></i> Manpower Information (従業員の情報)
                                </a>
                                <br>
                                <a href="{{ url('index/manpower/information_management') }}">
                                    <i class="fa fa-caret-right"></i> Manpower Information (Management) (従業員の情報)
                                </a>
                                <br>
                                <a href="{{ url('index/report/total_meeting') }}">
                                    <i class="fa fa-caret-right"></i> Total Meeting (トータルミーティング)
                                </a>
                                <br>
                                <a href="{{ url('index/report/employee_appraisal') }}">
                                    <i class="fa fa-caret-right"></i> Performance Appraisal
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Presence Information (出勤情報)</span>
                                <br>
                                <a href="{{ url('index/report/employee_resume') }}">
                                    <i class="fa fa-caret-right"></i> Employee Resume (従業員のまとめ)
                                </a>
                                <br>
                                <a href="{{ url('index/report/absence') }}">
                                    <i class="fa fa-caret-right"></i> Daily Attendance (YMPI日常出勤まと)
                                </a>
                                <br>
                                <a href="{{ url('index/report/absence_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Daily Absence Monitoring (日次出席監視)
                                </a>
                                <br>
                                <a href="{{ url('index/report/attendance_data') }}">
                                    <i class="fa fa-caret-right"></i> Attendance Data (出席データ)
                                </a>
                                <br>
                                <a href="{{ url('index/report/checklog_data') }}">
                                    <i class="fa fa-caret-right"></i> Checklog Data (出退勤登録データ)
                                </a>
                            </td>
                        </tr>

                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>General Affairs<br />総務課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('index/translation') }}">
                                    <i class="fa fa-caret-right"></i> Translation Request (翻訳管理システム)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/bento') }}">
                                    <i class="fa fa-caret-right"></i> Japanese Food Order (和食弁当の予約)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/live_cooking') }}">
                                    <i class="fa fa-caret-right"></i> Live Cooking Order (ライブクッキングの予約)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/driver') }}">
                                    <i class="fa fa-caret-right"></i> Driver Request (ドライバー管理システム)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/driver_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Driver Monitoring (ドライバー管理)
                                </a>
                                {{-- <br>
                            }
                            <a href="{{ url("index/ga_control/bento") }}">
                                <i class="fa fa-caret-right"></i> Japanese Food Order <img src="{{ asset('images/flag/id.png') }}" style="height: 14px; border: 1px solid black;"> (和食弁当の予約)
                            </a> --}}
                                <br>
                                <a href="{{ secure_url('index/ga_control/locker') }}">
                                    <i class="fa fa-caret-right"></i> Locker Room Control (ロッカー室の管理)
                                </a>
                                <br>
                                <a href="{{ url('index/license/document') }}">
                                    <i class="fa fa-caret-right"></i> Dokumen Perizinan (許可申請書)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_secretary/president_director/approval') }}">
                                    <i class="fa fa-caret-right"></i> Pengajuan Approval Presiden Direktur (社長承認申請)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/mcu') }}">
                                    <i class="fa fa-caret-right"></i> Medical Check Up (MCU) (健康診断)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/uniform/stock') }}">
                                    <i class="fa fa-caret-right"></i> Uniform Control (制服管理)
                                </a>
                                <br>
                                <a href="{{ url('index/process_gs') }}">
                                    <i class="fa fa-caret-right"></i> GS Control (GS管理)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_report/order/makan') }}">
                                    <i class="fa fa-caret-right"></i> Order Food Overtime (産業用の食事発注申請)
                                </a>
                                <br>
                                <a href="{{ url('index/ga_control/gym') }}">
                                    <i class="fa fa-caret-right"></i> GYM Reservation (スポーツジムの予約)
                                </a>

                                {{-- <br>
                                <a href="{{ url('index/ga_control/mcu/queue') }}">
                                    <i class="fa fa-caret-right"></i> Medical Check Up Queue (健康診断待ち行列)
                                </a> --}}
                                {{-- <br>
                                    <a href="{{ url('index/ga_report/order/makan') }}">
                                        <i class="fa fa-caret-right"></i> Order Makan Ramadhan (断食月の昼食注文)
                                    </a> --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Standardization<br>標準化課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">QA Process Control (品質保証　管理セクション)</span>
                                <br>
                                <a href="{{ url('index/qc_report/grafik_cpar') }}">
                                    <i class="fa fa-caret-right"></i> CPAR CAR Monitoring (是正予防策・是正策監視)
                                </a>
                                <br>
                                <a href="{{ url('index/qa_cpar') }}">
                                    <i class="fa fa-caret-right"></i> CPAR CAR Data (是正予防策リポートと是正策データ)
                                </a>
                                <br>
                                <a href="{{ url('index/qa_ymmj_index') }}">
                                    <i class="fa fa-caret-right"></i> YMMJ Report (YMMJ品保の報告データ)
                                </a>
                                <br>
                                <a href="{{ url('index/qa') }}">
                                    <i class="fa fa-caret-right"></i> Incoming Check (受入検査)
                                </a>
                                <br>
                                <!-- <a href="http://vendor.ympi.co.id" target="_blank">
                                                                                                                                                                                                                                                                                                                                                                                                                                                <i class="fa fa-caret-right"></i> Vendor Final Inspect (ベンダーファイナル検査)
                                                                                                                                                                                                                                                                                                                                                                                                                                            </a>
                                                                                                                                                                                                                                                                                                                                                                                                                                            <br> -->
                                <a href="{{ url('index/qa/audit_ng_jelas') }}">
                                    <i class="fa fa-caret-right"></i> Audit NG Jelas (品保の明らか不良検査)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/certificate') }}">
                                    <i class="fa fa-caret-right"></i> Kensa Certification (品質保証検査認定)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/special_process') }}">
                                    <i class="fa fa-caret-right"></i> Audit Proses Khusus & Kensa Proses (特殊工程の監査)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/packing') }}">
                                    <i class="fa fa-caret-right"></i> Audit Packing (梱包監査)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/audit_fg') }}">
                                    <i class="fa fa-caret-right"></i> Audit FG / KD (監査 完成品・KD部品)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/cpar_car') }}">
                                    <i class="fa fa-caret-right"></i> Audit CPAR & CAR (是正予防策・是正策監視 監査)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/feeling') }}">
                                    <i class="fa fa-caret-right"></i> Penyamaan Feeling Kensa FG (QA完成品検査の認識すり合わせ)
                                </a>
                                <br>
                                <a href="{{ url('index/qa/ik') }}">
                                    <i class="fa fa-caret-right"></i> Audit IK (QA職長による作業手順書の監査)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Standardization Process Control (標準化課　管理セクション)</span>
                                <br>
                                <a href="{{ url('index/standardization/document_index') }}">
                                    <i class="fa fa-caret-right"></i> IK DM DL Control (IK・DM・DLの管理)
                                </a>
                                <br>
                                <a href="{{ url('index/standardization/calibration') }}">
                                    <i class="fa fa-caret-right"></i> Calibration Control (管理)
                                </a>
                                <br>
                                <a href="{{ url('index/license/equipment') }}">
                                    <i class="fa fa-caret-right"></i> Equipment License Control (設備ライセンス管理)
                                </a>
                                <br>
                                <a href="{{ url('index/license/operator') }}">
                                    <i class="fa fa-caret-right"></i> Operator License Control (作業者ライセンス管理)
                                </a>
                                <br>
                                <a href="{{ url('index/std_control/safety_shoes') }}">
                                    <i class="fa fa-caret-right"></i> Safety Shoes Control (安全靴管理システム)
                                </a>
                                <br>
                                <a href="{{ url('index/kecelakaan') }}">
                                    <i class="fa fa-caret-right"></i> Informasi Kecelakaan Yamaha (ヤマハの事故情報)
                                </a>
                                <br>
                                <a href="{{ url('index/sga/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Small Group Activity (SGA) (小グループ活動)
                                </a>
                                <br>
                                <a href="{{ url('index/safety_check/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Holiday Safety Check (休日安全確認)
                                </a>
                                <br>
                                <a href="{{ url('index/ky_hh') }}">
                                    <i class="fa fa-caret-right"></i> Kiken Yochi & Hiyari Hatto (KYT) (危険予知とヒヤリハット)
                                </a>
                                <br>
                                <a href="{{ url('index/standardization/vehicle_menu') }}">
                                    <i class="fa fa-caret-right"></i> Pemeriksaan Kendaraan (車両検査)
                                </a>
                                <!-- <br>
                                                                                                                                                                                                                                                                                                                        <a href="{{ url('index/slogan') }}">
                                                                                                                                                                                                                                                                                                                            <i class="fa fa-caret-right"></i> Slogan Kebijakan Mutu (YMPIの品質スローガン)
                                                                                                                                                                                                                                                                                                                        </a> -->
                                <br>
                                <a href="{{ url('index/standardization/emergency') }}">
                                    <i class="fa fa-caret-right"></i> Emergency Simulation ()
                                </a>
                                <br>
                                <a href="{{ url('index/standardization/risk_assessment') }}">
                                    <i class="fa fa-caret-right"></i> Audit Risk Assessment ()
                                </a>
                                <br>
                                <a href="{{ url('index/standardization/ypm') }}">
                                    <i class="fa fa-caret-right"></i> YPM Evaluation (YPM評価)
                                </a>
                            </td>
                        </tr>

                        <!-- <tr>
                                                                                                                                                                                                                                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                                                                                                                                                                                                                            <span style="font-weight: bold;">WWT & Chemical</span>
                                                                                                                                                                                                                                                                                                                                                                                                                            <br>
                                                                                                                                                                                                                                                                                                                                                                                                                            <a href="{{ url('index/standardization/document_index') }}">
                                                                                                                                                                                                                                                                                                                                                                                                                            <i class="fa fa-caret-right"></i> Create SPK WWT (廃水処理所に作業依頼書を作成)
                                                                                                                                                                                                                                                                                                                                                                                                                            </a>
                                                                                                                                                                                                                                                                                                                                                                                                                            </td>
                                                                                                                                                                                                                                                                                                                                                                                                                        </tr> -->
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Accounting<br>経理課 </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('/index/billing_menu') }}">
                                    <i class="fa fa-caret-right"></i> E-Billing (E-ビリング)
                                </a>
                                <br>
                                <a href="{{ url('index/fixed_asset') }}">
                                    <i class="fa fa-caret-right"></i> Fixed Asset (固定資産)
                                </a>
                                <br>
                                <a href="{{ url('investment/control') }}">
                                    <i class="fa fa-caret-right"></i> Investment Monitoring & Control (投資管理)
                                </a>
                                <br>
                                <a href="{{ url('budget/info') }}">
                                    <i class="fa fa-caret-right"></i> Budget Information (予算情報)
                                </a>
                                <br>
                                <a href="{{ url('/index/resume_pajak') }}">
                                    <i class="fa fa-caret-right"></i> Data NPWP (納税義務者番号データ)
                                </a>
                                <br>
                                <a href="{{ url('/index/filemanager') }}">
                                    <i class="fa fa-caret-right"></i> Document Controlling System (書類管理システム)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Production Control<br>生産管理課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('/index/stocktaking/menu') }}">
                                    <i class="fa fa-caret-right"></i> Monthly Stock Taking (月次棚卸)
                                </a>
                                <br>
                                <a href="{{ url('/index/stocktaking/check_flo_kdo') }}">
                                    <i class="fa fa-caret-right"></i> Check FLO & KDO (FLOとKDOを確認)
                                </a>
                                <br>
                                <a href="{{ url('/index/stocktaking/silver_report') }}">
                                    <i class="fa fa-caret-right"></i> Silver Stock Taking Report (銀材棚卸し報告)
                                </a>
                                <br>
                                <a href="{{ url('/index/stocktaking/daily_report') }}">
                                    <i class="fa fa-caret-right"></i> Daily Stock Taking Report (日次棚卸し報告)
                                </a>
                                <br>
                                <a href="{{ url('/index/stocktaking/survey_report') }}">
                                    <i class="fa fa-caret-right"></i> Survey Stocktaking (棚卸のサーベイ)
                                </a>
                                <br>
                                <a href="{{ url('index/sakurentsu/monitoring/3m') }}">
                                    <i class="fa fa-caret-right"></i> Sakurentsu/3M/Trial Request Monitoring
                                    (作連通/３M/試作依頼監視)
                                </a>

                                <br>
                                <a href="{{ url('index/sakurentsu/display/monitoring/ALL') }}">
                                    <i class="fa fa-caret-right"></i> 3M Outstanding Monitoring
                                    ()
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Others<br />他の情報</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('/index/form_ketidaksesuaian/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Form Ketidaksesuaian (不適合報告フォーム)
                                </a>
                                <br>
                                <a href="{{ url('/index/form_experience') }}">
                                    <i class="fa fa-caret-right"></i> Form Kegagalan (問題・失敗のフォーム)
                                </a>
                                <br>
                                <a href="{{ url('/index/warehouse/operatoraktual') }}">
                                    <i class="fa fa-caret-right"></i> WH Internal Productivity (倉庫内の生産性)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Audit & Patrol Internal<br>内部監査・パトロール </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('/index/patrol') }}">
                                    <i class="fa fa-caret-right"></i> YMPI Internal Patrol (内部パトロール)
                                </a>
                                <br>
                                <a href="{{ url('/index/audit_internal') }}">
                                    <i class="fa fa-caret-right"></i> YMPI Internal Audit (内部監査)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Purchasing Control & Procurement<br />購買管理課 と 調達課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ secure_url('/index/material/check_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Incoming Material Check (材料受入検査)
                                </a>
                                <br>
                                <a href="{{ url('/index/trade_agreement') }}">
                                    <i class="fa fa-caret-right"></i> MTA (取引基本契約書)
                                </a>
                                <br>
                                <a href="{{ url('/index/raw_material_dashboard') }}">
                                    <i class="fa fa-caret-right"></i> Raw Material Dashboard (素材ダッシュボード)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/material_monitoring/direct') }}">
                                    <i class="fa fa-caret-right"></i> Direct Mat. Monitoring (素材監視「直材」)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/material_monitoring/indirect') }}">
                                    <i class="fa fa-caret-right"></i> Indirect Mat. Monitoring (素材監視「間材」)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/material_monitoring/subcont') }}">
                                    <i class="fa fa-caret-right"></i> Subcont Mat. Monitoring (素材監視「サブコン」)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/material_monitoring_availability') }}">
                                    <i class="fa fa-caret-right"></i> Material Availability (素材有無)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/shortage_material_availability') }}">
                                    <i class="fa fa-caret-right"></i> Shortage of Materials Availability (材料不足)
                                </a>
                                <br>
                                <a href="{{ url('/index/material/control_delivery') }}">
                                    <i class="fa fa-caret-right"></i> Delivery Control (納期管理の監視)
                                </a>
                                <br>
                                <a href="{{ url('index/license/raw_material') }}">
                                    <i class="fa fa-caret-right"></i> Material License Control (材料ライセンス管理)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Logistic<br>物流</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Warehouse (倉庫)</span>
                                <br>
                                <a href="{{ url('index/warehouse/temporary_storage') }}">
                                    <i class="fa fa-caret-right"></i> Kontrol Penyimpanan Sementara ()
                                </a>
                                <br>
                                <a href="{{ url('index/barang_modal') }}">
                                    <i class="fa fa-caret-right"></i> Kontrol Barang Modal (試算・資本金管理)
                                </a>
                                <br>
                                <a href="{{ url('index/indirect_material_stock') }}">
                                    <i class="fa fa-caret-right"></i> Indirect Material (間材)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Other Information<br />他の情報</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Mirai Mobile Report (モバイルMIRAIの記録)</span>
                                <br>
                                <a href="{{ url('index/mirai_mobile/index') }}">
                                    <i class="fa fa-caret-right"></i> Mirai Mobile Data (MIRAIモバイルデータ)
                                </a>
                                <br>
                                <a href="{{ url('index/survey_covid') }}">
                                    <i class="fa fa-caret-right"></i> Monitoring Pengisian Survey Covid (コロナ調査)
                                </a>
                                <br>
                                <a href="{{ url('index/survey') }}">
                                    <i class="fa fa-caret-right"></i> Emergency Survey (エマージェンシーサーベイ)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Health Monitoring (健康監視)</span>
                                <br>
                                <a href="{{ url('index/temperature') }}">
                                    <i class="fa fa-caret-right"></i> Body Temperature (体温)
                                </a>
                                <br>
                                <a href="{{ url('index/display/clinic_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Clinic Monitoring (クリニック監視)
                                </a>
                                <br>
                                <a href="{{ url('index/display/clinic_visit?datefrom=&dateto=') }}">
                                    <i class="fa fa-caret-right"></i> Clinic Visit (クリニック訪問)
                                </a>
                                <br>
                                <a href="{{ url('index/general/oxymeter') }}">
                                    <i class="fa fa-caret-right"></i> Oximeter Check (オキシメーター検査)
                                </a>
                                <br>
                                <a href="{{ url('index/display/clinic_disease?month=') }}">
                                    <i class="fa fa-caret-right"></i> Clinic Diagnostic Data (クリニック見立てデータ)
                                </a>
                                <br>
                                <a href="{{ url('index/general/oxymeter/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Oximeter Monitoring (オキシメーターモニター)
                                </a>
                                <br>
                                <a href="{{ url('index/general/airvisual') }}">
                                    <i class="fa fa-caret-right"></i> CO<sub>2</sub> Monitor (二酸化炭素モニター)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Visitor Monitoring (来客の管理)</span>
                                <br>
                                <a href="{{ url('visitor_display') }}">
                                    <i class="fa fa-caret-right"></i> Visitor Data (ビジターデータ)
                                </a>
                                <br>
                                <a href="{{ url('visitor_index') }}">
                                    <i class="fa fa-caret-right"></i> Visitor Control Security (ビジターコントロール)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Other Monitoring (その他の監視)</span>
                                <br>
                                {{-- <a href="{{ secure_url('index/std_control/safety_shoes') }}">
                                    <i class="fa fa-caret-right"></i> Safety Shoes Control (安全靴管理システム)
                                </a>
                                <br> --}}
                                <a href="{{ url('index/meeting') }}">
                                    <i class="fa fa-caret-right"></i> Meeting List (会議リスト)
                                </a>
                                {{-- <br>
                            <a href="{{ url('index/temperature/room_temperature') }}">
                                <i class="fa fa-caret-right"></i> Room Temperature (室内温度)
                            </a> --}}
                                <br>
                                <a href="{{ url('visitor_confirmation') }}">
                                    <i class="fa fa-caret-right"></i> Telephone List (電話帳)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>HRqu & e-Kaizen<br>Hrqu&e-改善</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <?php $tahun = date('Y'); ?>
                                <a href="{{ route('emp_service', ['id' => '1', 'tahun' => $tahun]) }}">
                                    <i class="fa fa-caret-right"></i> HRqu (従業員の情報サービス)
                                </a>
                                <br>
                                <a href="{{ route('emp_service') }}">
                                    <i class="fa fa-caret-right"></i> e-Kaizen (e-改善)
                                </a>
                                <br>
                                <a href="{{ url('/index/kaizen_teian') }}">
                                    <i class="fa fa-caret-right"></i> e-Kaizen Monitoring (E改善の監視)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                {{-- <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Logistic<br>物流課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url('/index/warehouse/incoming_check/monitoring') }}">
                                <i class="fa fa-caret-right"></i> Incoming Material Check (材料受入検査)
                            </a>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table> --}}
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Production Engineering<br>生産技術課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">PE Control (生産技術　管理)</span>
                                {{-- <br>
                                <a href="{{ url('winds_mpro') }}">
                                    <i class="fa fa-caret-right"></i> WINDS MPRO
                                </a> --}}
                                <br>
                                <a href="{{ url('index/ejor/monitoring?filter=') }}">
                                    <i class="fa fa-caret-right"></i> EJOR (技術的作業依頼書)
                                </a>
                                <br>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">PE Field (生産技術　現場)</span>
                                <br>
                                <a href="{{ url('index/workshop/create_wjo') }}">
                                    <i class="fa fa-caret-right"></i> Create WJO (作業依頼書の作成)
                                </a>
                                <br>
                                <a href="{{ url('index/workshop') }}">
                                    <i class="fa fa-caret-right"></i> WJO Monitoring (作業依頼書の監視)
                                </a>
                                <br>
                                <a href="{{ url('index/workshop/check_molding_vendor') }}">
                                    <i class="fa fa-caret-right"></i> Molding Check Vendor ()
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Maintenance<br>保全課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('index/maintenance/list/user') }}">
                                    <i class="fa fa-caret-right"></i> Create SPK (作業依頼書を作成)
                                </a>
                                <br>
                                <a href="{{ url('index/maintenance/tpm/dashboard') }}">
                                    <i class="fa fa-caret-right"></i> Smart TPM Monitoring (スマートTPMの監視)
                                </a>
                                {{-- <br>
                                    <a href="{{ url('index/maintenance/spk_monitoring') }}">
                                        <i class="fa fa-caret-right"></i> SPK Monitoring (作業依頼書の管理)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/maintenance/machine_monitoring') }}">
                                        <i class="fa fa-caret-right"></i> Machine Monitoring (マシン監視)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/maintenance/planned_monitoring') }}">
                                        <i class="fa fa-caret-right"></i> Planned Maintenance (予定保全)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/temperature/room_temperature') }}">
                                        <i class="fa fa-caret-right"></i> Room Temperature (室内温度)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/maintenance/electricity/daily_consumption_ratio') }}">
                                        <i class="fa fa-caret-right"></i> Electricity Consumption (日次電気消費率)
                                    </a>
                                    <br>
                                    <a href="#">
                                        <i class="fa fa-caret-right"></i> Electricity Consumption VS Sales (電気消費量 対 売上)
                                    </a> --}}
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Management Information System<br>情報システム課</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('/index/ticket/monitoring/mis') }}">
                                    <i class="fa fa-caret-right"></i> Ticket (MISチケット依頼)
                                </a>
                                <br>
                                <a href="{{ url('/index/mis/form') }}">
                                    <i class="fa fa-caret-right"></i> MIS Form (MIS票)
                                </a>
                                <br>
                                <a href="{{ url('/index/ticket_log') }}">
                                    <i class="fa fa-caret-right"></i> System Update Logs (システム更新履歴)
                                </a>
                                <br>
                                <a href="{{ url('files/IT_POLICY.pdf') }}" target="_blank">
                                    <i class="fa fa-caret-right"></i> IT Policy & Others (IT方針とその他)
                                </a>
                                <br>
                                <a href="{{ url('index/mis/stocktaking_account') }}">
                                    <i class="fa fa-caret-right"></i> Accounts & Roles Stocktaking (アカウントと業務内容の棚卸)
                                </a>
                                <br>
                                <a href="{{ url('index/license/software') }}">
                                    <i class="fa fa-caret-right"></i> License Control (ソフトライセンス管理)
                                </a>
                                <br>
                                <a href="{{ url('index/inventory_mis') }}">
                                    <i class="fa fa-caret-right"></i> Asset (資産)
                                </a>
                                <br>
                                <a href="{{ url('index/display/ip?location=server') }}">
                                    <i class="fa fa-caret-right"></i> Ping Status (IP管理)
                                </a>
                                <br>
                                <a href="{{ url('index/server_room') }}">
                                    <i class="fa fa-caret-right"></i> Server Room (サーバールームモニタリング)
                                </a>
                                <br>
                                <a href="{{ url('index/mis/complaint/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Wifi Complaint (Wi-Fi に関する苦情)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>YMPI Supporting System<br> YMPI サポートシステム</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="http://10.109.33.33/kitto/public">
                                    <i class="fa fa-caret-right"></i> KITTO (きっと)
                                </a>
                                <br>
                                <a href="http://10.109.52.8/sf6/" target="_blank">
                                    <i class="fa fa-caret-right"></i> Sunfish (サンフィッシュ)
                                </a>
                                <br>
                                <a href="https://a01.yamaha.co.jp/fw/dfw/CERT/Portal.php" target="_blank">
                                    <i class="fa fa-caret-right"></i> IDM Portal;
                                </a>
                                <a href="https://yamahagroup.sharepoint.com/sites/prj00220"
                                    target="_blank">Sharepoint;</a>
                                <a href="https://adagio.infosys.yamaha.com/imart/login" target="_blank">Adagio;</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Work In Process<br />仕掛品</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Educational Instrument (教育楽器課)</span>
                                <br>
                                <a href="{{ url('/index/Pianica') }}">
                                    <i class="fa fa-caret-right"></i> Pianica Process (ピアニカ加工セクション)
                                </a>
                                <br>
                                <a href="{{ url('index/recorder_process') }}">
                                    <i class="fa fa-caret-right"></i> Recorder Process (リコーダー加工セクション)
                                </a>
                                <br>
                                <a href="{{ url('/index/injeksi') }}">
                                    <i class="fa fa-caret-right"></i> Injection Recorder (RC成形)
                                </a>
                                <br>
                                <a href="{{ url('/index/reed') }}">
                                    <i class="fa fa-caret-right"></i> Injection Reed Synthetic (樹脂リード成形)
                                </a>
                                <br>
                                <a href="{{ url('index/final/reed_synthetic') }}">
                                    <i class="fa fa-caret-right"></i> Reed Synthetic (樹脂リード)
                                </a>
                                <br>
                                <a href="{{ url('index/mouthpiece_process') }}">
                                    <i class="fa fa-caret-right"></i> Mouthpiece(唄口)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Key Parts Process (木管 カギ部品加工課)</span>
                                <br>
                                <a href="{{ url('/index/initial', 'material_process') }}">
                                    <i class="fa fa-caret-right"></i> Material Process ()
                                </a>
                                <br>
                                <a href="{{ url('/index/initial', 'press') }}">
                                    <i class="fa fa-caret-right"></i> Press (プレス)
                                </a>
                                <br>
                                <a href="{{ url('/index/initial', 'sanding') }}">
                                    <i class="fa fa-caret-right"></i> Sanding (サンディング)
                                </a>
                                <br>
                                <a href="{{ url('/index/initial', 'lotting') }}">
                                    <i class="fa fa-caret-right"></i> Lotting (ロッティング)
                                </a>
                                <br>
                                <a href="{{ url('winds') }}">
                                    <i class="fa fa-caret-right"></i> WINDS Z-PRO (作業指示デジタルシステム)
                                </a>
                                <!-- <br>
                                                                                                                                                                                                                                                                                                                            <a href="{{ url('/index/press/monitoring') }}"><i class="fa fa-caret-right"></i> Press Machine Monitoring (プレス機管理) </a>
                                                                                                                                                                                                                                                                                                                        -->
                                <br>
                                <a href="{{ url('/index/initial/stock_monitoring', 'mpro') }}">
                                    <i class="fa fa-caret-right"></i> Stock Monitoring (部品加工の仕掛品監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/initial/stock_trend', 'mpro') }}">
                                    <i class="fa fa-caret-right"></i> Stock Trend (部品加工の在庫トレンド)
                                </a>
                                <br>
                                <a href="http://10.109.52.7/tpro/" target="_blank">
                                    <i class="fa fa-caret-right"></i> Kanban Monitoring (プロかんばんの監視)
                                </a>
                                <br>
                                <a href="{{ url('/kanagata/control') }}">
                                    <i class="fa fa-caret-right"></i> Kanagata Control (金型の報告管理)
                                </a>
                            </td>
                        </tr>
                        <!-- <tr>
                                                                                                                                                                                                            <td>
                                                                                                                                                                                                                <span style="font-weight: bold;">Body Parts Process (木管 管体部品加工課)</span>
                                                                                                                                                                                                                <br>
                                                                                                                                                                                                                <a href="{{ url('/index/initial', 'bpro_cl') }}">
                                                                                                                                                                                                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                <br>
                                                                                                                                                                                                                <a href="{{ url('/index/initial', 'bpro_fl') }}">
                                                                                                                                                                                                                    <i class="fa fa-caret-right"></i> Flute (フルート)
                                                                                                                                                                                                                </a>
                                                                                                                                                                                                                <br>
                                                                                                                                                                                                                <a href="{{ url('/index/initial', 'bpro_sx') }}">
                                                                                                                                                                                                                    <i class="fa fa-caret-right"></i> Saxophone (サックス)
                                                                                                                                                                                                                </a>
                                                                                                                                                                                                            </td>
                                                                                                                                                                                                        </tr> -->
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Body Parts Process (木管 管体部品加工課)</span>
                                <br>
                                <a href="{{ route('bodyPartsProcessFLIndex') }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート)
                                </a>
                                <br>
                                <a href="{{ route('bodyPartsProcessSXIndex') }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Welding Process (木管 溶接課)</span>
                                <br>
                                <a href="{{ url('/index/process_welding_cl') }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_welding_fl') }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート溶接)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_welding_sx') }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス溶接)
                                </a>
                                <br>
                                <a href="{{ url('/index/welding_jig') }}">
                                    <i class="fa fa-caret-right"></i> Digital Jig Handling (冶具デジタル管理)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/sub_assy/welding_fl?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Flute Picking (フルートのピッキング監視)
                                </a>
                                <br>
                                <a
                                    href="{{ url('/index/display/sub_assy/welding_sax?date=&surface2=&key2=&model2=&hpl2=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Saxophone Picking (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/sub_assy/welding_cl?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking (クラリネットピッキング監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Surface Treatment (木管 表面処理課)</span>
                                <br>
                                <a href="{{ url('/index/process_middle_cl') }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_middle_fl') }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート表面処理)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_middle_sx') }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス表面処理)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_middle_acc') }}">
                                    <i class="fa fa-caret-right"></i> Accessories (付属部品の表面処理)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/stockroom_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Stockroom Monitoring (ストックルームの監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/middle/stock_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Middle Stock Monitoring (中間工程の仕掛品監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/lifetime/jig/plating') }}">
                                    <i class="fa fa-caret-right"></i> Plating Jig Lifetime (メッキ治具の寿命)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Assembly (木管 組立課)</span>
                                <br>
                                <a href="{{ url('/index/process_stamp_cl') }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                </a>
                                <br>
                                <a href="{{ url('/index/process_assy_fl') }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート仮組~組立)
                                </a>
                                <br>
                                <a href="{{ url('index/process_stamp_sx_assy') }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス仮組～組立)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/sub_assy/assy_fl?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Flute Key Picking (フルートのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/body/fl_body?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Flute Body Picking (フルートのピッキング監視)
                                </a>
                                <br>
                                <a
                                    href="{{ url('/index/display/sub_assy/assy_sax?date=&surface2=&key2=&model2=&hpl2=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Sax Key Picking (サックスのピッキング監視)
                                </a>
                                <br>
                                <a
                                    href="{{ url('/index/display/body/sax_body?date=&surface2=&key2=&model2=&hpl2=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Sax Body Picking (サックスのピッキング監視)
                                </a>
                                <br>
                                <a
                                    href="{{ url('/index/display/picking/body/sax_body?date=&surface2=&key2=&model2=&hpl2=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Sax Body Picking 2 (サックスのピッキング監視 2)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/sub_assy/assy_cl?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking (クラリネットピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/sub_assy/assy_acc?date=&order2=') }}">
                                    <i class="fa fa-caret-right"></i> Accessories Picking (アクセサリピッキングモニター)
                                </a>
                                <br>
                                <a href="{{ url('/index/process/tanpo_stock_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Tanpo Stock Monitor (タンポ在庫モニター)
                                </a>
                                <br>
                                <a href="{{ url('index/packing_documentation') }}">
                                    <i class="fa fa-caret-right"></i> Packing Documentation (梱包作業の文書化)
                                </a>
                                <br>
                                <a href="{{ url('index/case/menu') }}">
                                    <i class="fa fa-caret-right"></i> Case Control Final Assy (組立職場のケース管理)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Others (他の情報)</span>
                                <br>
                                <a href="{{ url('/index/efficiency/dashboard') }}">
                                    <i class="fa fa-caret-right"></i> Efficiency Control (効率制御)
                                </a>
                                <br>
                                <a href="{{ url('/index/efficiency/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Efficiency Monitoring (効率の監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/transaction/repair_room_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Repair Room (リペアールーム)
                                </a>
                                <br>
                                <a href="{{ url('/index/audit_ng_jelas_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Audit NG Jelas (明らか不良監査の監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/tools') }}">
                                    <i class="fa fa-caret-right"></i> Digital Order & Control Stock Tools
                                    (デジタルオーダー及び在庫管理ツール)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Leader Control (職長管理)</span>
                                <br>
                                <a href="{{ url('/index/efficiency/leader') }}">
                                    <i class="fa fa-caret-right"></i> Efficiency (次効率)
                                </a>
                                <br>
                                <a href="{{ url('/index/audit_ik_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Audit IK Monitoring (作業手順書監査表示)
                                </a>
                                <br>
                                <a href="{{ url('/index/daily_check_mesin') }}">
                                    <i class="fa fa-caret-right"></i> Daily Check Mesin Monitoring (機械 日常点検)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/20') }}">
                                    <i class="fa fa-caret-right"></i> WI-PP Report (主要部品加工)
                                </a>
                                <br>
                                <!-- <a href="{{ url('/index/production_report/index/17') }}">
                                                            <i class="fa fa-caret-right"></i> WI-BPP Report (ボディパーツ加工)
                                                        </a>
                                                        <br> -->
                                <a href="{{ url('/index/production_report/index/15') }}">
                                    <i class="fa fa-caret-right"></i> WI-WP Report (溶接プロセスリポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/18') }}">
                                    <i class="fa fa-caret-right"></i> WI-ST Report (表面処理レポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/8') }}">
                                    <i class="fa fa-caret-right"></i> WI-FA Report (アセンブリ（WI-FA）レポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/10') }}">
                                    <i class="fa fa-caret-right"></i> Maintenance Report (メンテナンスリポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/13') }}">
                                    <i class="fa fa-caret-right"></i> PE Field Report (PEフィールドレポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/5') }}">
                                    <i class="fa fa-caret-right"></i> Logistic Report (兵站学レポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/9') }}">
                                    <i class="fa fa-caret-right"></i> EI Report (教育楽器レポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/14') }}">
                                    <i class="fa fa-caret-right"></i> STD Report (品保レポート)
                                </a>
                                <br>
                                <a href="{{ url('/index/production_report/index/19') }}">
                                    <i class="fa fa-caret-right"></i> GPC Report (総合工程の管理報告)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
                @if (isset(Auth::user()->employee_sync->name) &&
                        (str_contains(Auth::user()->role_code, 'MIS') || Auth::user()->employee_sync->department == ''))
                    <table class="table table-bordered">
                        <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                            <tr>
                                <th>Mr. Ichimura Link<br>市村社長専用のリンク</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>
                                    <a href="{{ url('index/shipping_production_amount') }}">
                                        <i class="fa fa-caret-right"></i> Ship. & Prod. Amount (日次出荷と生産「金額」)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/fg_production') }}">
                                        <i class="fa fa-caret-right"></i> FLO Monthly Summary (FLO月次まとめ)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/resume_shipping_order') }}">
                                        <i class="fa fa-caret-right"></i> Shipping BML (船便予約管理リスト)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/report/overtime_monthly_fq') }}">
                                        <i class="fa fa-caret-right"></i> Overtime Progress (残業まとめの進捗)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/ch_daily_production_result') }}">
                                        <i class="fa fa-caret-right"></i> Production Progress (FG生産まとめ)
                                    </a>
                                    {{-- <br>
                                    <a href="{{ url('index/display/efficiency_monitoring_monthly') }}">
                                        <i class="fa fa-caret-right"></i> Monthly Efficiency Monitoring (月次効率の監視)
                                    </a> --}}
                                    <br>
                                    <a href="{{ url('/index/efficiency/monitoring') }}">
                                        <i class="fa fa-caret-right"></i> Efficiency Monitoring (効率の監視)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/report/absence') }}">
                                        <i class="fa fa-caret-right"></i> Daily Attendance (YMPI日常出勤まと)
                                    </a>
                                    <br>
                                    <a href="{{ url('index/production_resume') }}">
                                        <i class="fa fa-caret-right"></i> Production Shortage Summary (生産の不足まとめ)
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endif

                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>Finished Goods & KD Parts<br />完成品・KD部品</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Finished Goods Control (完成品管理)</span>
                                <br>
                                <a href="{{ url('/index/fg_production_schedule') }}">
                                    <i class="fa fa-caret-right"></i> Prod. Schedule Data (生産スケジュールデータ)
                                </a>
                                <br>
                                <a href="{{ url('/index/dp_production_result') }}">
                                    <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                                </a>
                                <br>
                                <a href="{{ url('/index/dp_fg_accuracy') }}">
                                    <i class="fa fa-caret-right"></i> FG Accuracy (FG週次出荷)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_production') }}">
                                    <i class="fa fa-caret-right"></i> Production Result (生産実績)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_stock') }}">
                                    <i class="fa fa-caret-right"></i> Finished Goods Stock (完成品在庫)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_traceability') }}">
                                    <i class="fa fa-caret-right"></i> FG Traceability (FG完成品追跡)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">KD Parts Control (KD部品管理)</span>
                                <br>
                                <a href="{{ url('/index/kd_stock') }}">
                                    <i class="fa fa-caret-right"></i> KD Parts Stock (KD部品在庫)
                                </a>
                                <br>
                                <a href="{{ url('/index/kd_traceability') }}">
                                    <i class="fa fa-caret-right"></i> KD Traceability (KD完成品追跡)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Extra Order Control (エキストラオーダー管理)</span>
                                <br>
                                <a href="{{ url('index/extra_order') }}">
                                    <i class="fa fa-caret-right"></i> EO Monitoring (EOモニタリング)
                                </a>
                                <br>
                                <a
                                    href="{{ url('index/extra_order/approval_monitoring?submit_from=&submit_to=&approver_id=') }}">
                                    <i class="fa fa-caret-right"></i> EOC Monitoring (EOC承認申請監視)
                                </a>
                                <br>
                                <a href="{{ url('index/extra_order/shortage_monitoring?area=') }}">
                                    <i class="fa fa-caret-right"></i> EO Shortage (EOの不足監視)
                                </a>
                                <br>
                                <a href="{{ url('index/extra_order/data') }}">
                                    <i class="fa fa-caret-right"></i> EO Data (EOーデータ)
                                </a>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Shipment Control (出荷管理)</span>
                                <br>
                                <a href="{{ url('/index/display/all_stock') }}">
                                    <i class="fa fa-caret-right"></i> All Stock (全在庫)
                                </a>
                                <br>
                                <a href="{{ url('index/resume_shipping_order') }}">
                                    <i class="fa fa-caret-right"></i> Shipping BML (船便予約管理リスト)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_shipment_schedule') }}">
                                    <i class="fa fa-caret-right"></i> Ship. Schedule Data (出荷スケジュールデータ)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_shipment_result') }}">
                                    <i class="fa fa-caret-right"></i> Shipment Result (出荷結果)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/shipment_progress_all') }}">
                                    <i class="fa fa-caret-right"></i> Shipment Progress (出荷結果)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/shipment_report') }}">
                                    <i class="fa fa-caret-right"></i> Weekly Ship. SUB (週次出荷　スラバヤ着荷)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_container_departure') }}">
                                    <i class="fa fa-caret-right"></i> Container Departure (コンテナー出発)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Shipment Performance (出荷管理)</span>
                                <br>
                                <a href="{{ url('/index/display/stuffing_monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Stuffing Monitoring (荷積み監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_weekly_summary') }}">
                                    <i class="fa fa-caret-right"></i> Weekly Summary (週次まとめ)
                                </a>
                                <br>
                                <a href="{{ url('/index/fg_monthly_summary') }}">
                                    <i class="fa fa-caret-right"></i> Monthly Summary (月次まとめ)
                                </a>
                                <br>
                                <a href="{{ url('/index/budget_vs_actual_sales') }}">
                                    <i class="fa fa-caret-right"></i> Budget VS Actual Sales (売上予算対売り上げ実績)
                                </a>
                                <br>
                                <a href="{{ url('/index/shipping_amount') }}">
                                    <i class="fa fa-caret-right"></i> Shipping Amount (日次出荷「金額」)
                                </a>
                                <br>
                                <a href="{{ url('/index/shipping_production_amount') }}">
                                    <i class="fa fa-caret-right"></i> Ship. & Prod. Amount (日次出荷と生産「金額」)
                                </a>
                                <br>
                                <a href="{{ url('/index/sales_by_destination') }}">
                                    <i class="fa fa-caret-right"></i> Sales By Destination (仕向けによる売り上げ)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Chorei (朝礼)</span>
                                <br>
                                <a href="{{ url('index/production_resume') }}">
                                    <i class="fa fa-caret-right"></i> Production Shortage Summary (生産の不足まとめ)
                                </a>
                                <br>
                                <a href="{{ url('index/production_warehouse') }}">
                                    <i class="fa fa-caret-right"></i> Production To FSTK ()
                                </a>
                                <br>
                                <a href="{{ url('/index/ch_daily_production_result') }}">
                                    <i class="fa fa-caret-right"></i> FG Production Summary (FG生産まとめ)
                                </a>
                                <br>
                                <a href="{{ url('/index/ch_daily_production_result_kd') }}">
                                    <i class="fa fa-caret-right"></i> KD Production Summary (KD生産まとめ)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/eff_scrap') }}">
                                    <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/general/pointing_call/japanese') }}">
                                    <i class="fa fa-caret-right"></i> Japanese Pointing Call (駐在員指差し呼称)
                                </a>
                                <br>
                                <a href="{{ url('/index/general/pointing_call/national') }}">
                                    <i class="fa fa-caret-right"></i> NS Pointing Call (ナショナル・スタッフ用の指差し呼称)
                                </a>
                                <br>
                                <a href="{{ url('/index/general/pointing_call/aturan_keselamatan') }}">
                                    <i class="fa fa-caret-right"></i> YMPI Safety Rules (YMPI安全掟)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Others (他の情報)</span>
                                {{-- <br>
                                <a href="{{ url('/index/display/efficiency_monitoring_monthly') }}">
                                    <i class="fa fa-caret-right"></i> Monthly Efficiency Monitoring (月次効率の監視)
                                </a> --}}
                                <br>
                                <a href="{{ url('/index/efficiency/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> Efficiency Monitoring (効率の監視)
                                </a>
                                <br>
                                <a href="{{ url('/index/display/eff_scrap') }}">
                                    <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <table class="table table-bordered">
                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                        <tr>
                            <th>PR, PO, Investment & Budget Control<br /> 購入依頼書・投資申請</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <a href="{{ url('purchase_requisition/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> PR Monitoring & Control (PR監視・管理)
                                </a>
                                <br>
                                <a href="{{ url('investment/control') }}">
                                    <i class="fa fa-caret-right"></i> Investment Monitoring & Control (投資管理)
                                </a>
                                <br>
                                <a href="{{ url('purchase_order/monitoring') }}">
                                    <i class="fa fa-caret-right"></i> PO Monitoring & Control (PO管理)
                                </a>
                                <br>
                                <a href="{{ url('budget/info') }}">
                                    <i class="fa fa-caret-right"></i> Budget Information (予算情報)
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </section>

@stop
@section('scripts')
    <script src="{{ url('js/jquery.marquee.min.js') }}"></script>
    <script>
        jQuery(document).ready(function() {
            $('.marquee').marquee({
                duration: 4000,
                gap: 1,
                delayBeforeStart: 0,
                direction: 'up',
                duplicated: true
            });
        });
    </script>
@endsection
