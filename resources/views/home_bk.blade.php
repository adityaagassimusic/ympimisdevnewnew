@extends('layouts.master')
@section('stylesheets')
<style type="text/css">
    thead input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }
    thead>tr>th{
        text-align:center;
    }
    tbody>tr>td{
        text-align:center;
    }
    tfoot>tr>th{
        text-align:center;
    }
    td:hover {
        overflow: visible;
    }
    table.table-bordered{
        border:1px solid black;
        margin-bottom: 5px;
    }
    table.table-bordered > thead > tr > th{
        border:1px solid black;
        margin:0;
        padding:0;
    }
    table.table-bordered > tbody > tr > td{
        border:1px solid rgb(180,180,180);
        font-size: 12px;
        background-color: rgb(240,240,240);
        padding-top: 2px;
        padding-bottom: 2px;
        padding-left: 3px;
        padding-right: 3px;
    }
    table.table-bordered > tfoot > tr > th{
        border:1px solid rgb(211,211,211);
    }
    #loading, #error { display: none; }
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
        <span style="font-size: 16px;" class="text-purple"><span style="font-size:22px;"><b>M</b></span>anufactur<span style="font-size:23px;"><b>i</b></span>ng <span style="font-size:22px;"><b>R</b></span>ealtime <span style="font-size:22px;"><b>A</b></span>cquisition of <span style="font-size:22px;"><b>I</b></span>nformation</span>
        <br>
        <b><span style="font-size: 20px;" class="text-purple">
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
            製 造 の リ ア ル タ イ ム 情 報
            <img src="{{ url("images/logo_mirai_bundar.png")}}" height="24px">
        </span></b>
    </div>
</section>
@endsection

@section('content')

<section class="content" style="padding-top: 0;">
    <div class="row">
        <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
            <?php $tahun = date('Y'); ?>
            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Audit / Patrol Internal<br>内部監査・パトロール </th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("/index/patrol") }}">
                                <i class="fa fa-caret-right"></i> Internal Patrol (内部パトロール)
                            </a>  
                            <br> 
                            <a href="{{ url("/index/audit_internal") }}">
                                <i class="fa fa-caret-right"></i> Internal Audit (内部監査)
                            </a>
                            <br> 
                            <a href="{{ url('/index/interview/pointing_call') }}">
                                <i class="fa fa-caret-right"></i> Interview Pointing Call (指差し呼称の面接)
                            </a>
                        </td>                     
                    </tr>                    
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Other Form<br>物流課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("/index/form_experience") }}">
                                <i class="fa fa-caret-right"></i> (One For All) Form Permasalahan & Kegagalan (問題・失敗のフォーム)
                            </a>
                        </td>                 
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Other Information<br/>他の情報</th>
                    </tr>
                </thead>
                <tbody>
                 <tr>
                    <td>
                        <span style="font-weight: bold;">Overtime Information (残業の情報)</span>
                        <br>
                        <a href="{{ url("index/report/overtime_monthly_fq") }}">
                            <i class="fa fa-caret-right"></i> OT Monitor Forecast (コストセンターによる残業管理)
                        </a>
                        <br>
                        <a href="{{ url("index/report/overtime_monthly_bdg") }}">
                            <i class="fa fa-caret-right"></i> OT Monitor Budget (コストセンターによる残業管理)
                        </a>
                        <br>
                        <a href="{{ url("index/report/overtime_yearly") }}">
                            <i class="fa fa-caret-right"></i> Overtime Resume (年度残業まとめ)
                        </a>
                        <br>
                        <a href="{{ url("index/report/overtime_section")}}">
                            <i class="fa fa-caret-right"></i> OT By Cost Center (コストセンター別の残業)
                        </a>
                        <br>
                        <a href="{{ url("index/report/overtime_data") }}">
                            <i class="fa fa-caret-right"></i> OT Data (残業データ)
                        </a>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Manpower Information (人工の情報)</span>
                        <br>
                        <a href="{{ url("index/report/manpower") }}">
                            <i class="fa fa-caret-right"></i> Manpower Information (人工の情報)
                        </a>
                        <br>
                        <a href="{{ url("index/report/total_meeting") }}">
                            <i class="fa fa-caret-right"></i> Total Meeting (トータルミーティング)
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Presence Information (出勤情報)</span>
                        <br>
                        <a href="{{ url("index/report/employee_resume") }}">
                            <i class="fa fa-caret-right"></i> Employee Resume (従業員のまとめ)
                        </a>
                        <br>
                        <a href="{{ url("index/report/absence") }}">
                            <i class="fa fa-caret-right"></i> Absence (欠勤)
                        </a>
                        <br>
                        <a href="{{ url("index/report/absence_monitoring") }}">
                            <i class="fa fa-caret-right"></i>Daily Absence Monitoring (日次出席監視)
                        </a>
                        <br>
                        <a href="{{ url("index/report/attendance_data")}}">
                            <i class="fa fa-caret-right"></i> Attendance Data (出席データ)
                        </a>
                        <br>
                        <a href="{{ url("index/report/checklog_data")}}">
                            <i class="fa fa-caret-right"></i> Checklog Data (出退勤登録データ)
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>

                        <span style="font-weight: bold;">Mirai Mobile Report (モバイルMIRAIの記録)</span>
                        <br>
                        <a href="{{ url("index/mirai_mobile/index") }}">
                            <i class="fa fa-caret-right"></i> Mirai Mobile Data (MIRAIモバイルデータ)
                        </a>
                        <br>
                        <a href="{{ url("index/survey_covid") }}">
                            <i class="fa fa-caret-right"></i> Monitoring Pengisian Survey Covid
                        </a>
                        <br>
                        <a href="{{ url("index/survey") }}">
                            <i class="fa fa-caret-right"></i> Emergency Survey
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Health Monitoring (健康監視)</span>
                        <br>
                        <a href="{{ url("index/temperature") }}">
                            <i class="fa fa-caret-right"></i> Body Temperature (体温)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_monitoring") }}">
                            <i class="fa fa-caret-right"></i> Clinic Monitoring (クリニック監視)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_visit?datefrom=&dateto=") }}">
                            <i class="fa fa-caret-right"></i> Clinic Visit (クリニック訪問)
                        </a>
                        <br>
                        <a href="{{ url('index/general/oxymeter') }}">
                            <i class="fa fa-caret-right"></i> Oximeter Check (オキシメーター検査)
                        </a>
                        <br>
                        <a href="{{ url("index/display/clinic_disease?month=") }}">
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
                        <br>
                        <a href="{{ url('index/general/queue','mcu') }}">
                            <i class="fa fa-caret-right"></i> Medical Check Up Queue (健康診断待ち行列)
                        </a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Visitor Monitoring (来客の管理)</span>
                        <br>
                        <a href="{{ url("visitor_display") }}">
                            <i class="fa fa-caret-right"></i> Visitor Data (ビジターデータ)
                        </a>
                        <br>
                        <a href="{{ url("visitor_index") }}">
                            <i class="fa fa-caret-right"></i> Visitor Control Security (ビジターコントロール)
                        </a>
                            <!-- <br>
                            <a href="{{ url("index/display/pantry_visit?tanggal=") }}">
                                <i class="fa fa-caret-right"></i> Pantry Visitor Monitoring (給湯室の来室者監視)
                            </a> -->
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Other Monitoring (その他の監視)</span>
                            <br>
                            <a href="{{ url("index/meeting") }}">
                                <i class="fa fa-caret-right"></i> Meeting List (会議リスト)
                            </a>
                            <!-- <br>
                            <a href="{{ url("index/toilet") }}">
                                <i class="fa fa-caret-right"></i> Toilet Availability (トイレステイタス(空席・使用中))
                            </a> -->
                            <br>
                            <a href="{{ url("index/temperature/room_temperature") }}">
                                <i class="fa fa-caret-right"></i> Room Temperature (室内温度)
                            </a>
                            <!-- <a href="{{ url("visitor_confirmation_manager") }}">
                                <i class="fa fa-caret-right"></i> Visitor Confirmation (来客の確認)
                            </a>
                            <br> -->
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>e-Kaizen<br>e-改善</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <!-- <a href="{{ url("/index/resume_pajak") }}">
                                <i class="fa fa-caret-right"></i> Resume Pengisian NPWP (納税義務者番号データのまとめ)
                            </a>
                            <br> -->

                            <a href="{{ route('emp_service', ['id' => '2', 'tahun' => $tahun]) }}">
                                <i class="fa fa-caret-right"></i> e-Kaizen (E-改善)
                            </a>
                            <br>
                            <a href="{{ url("/index/kaizen/aproval/resume") }}">
                                <i class="fa fa-caret-right"></i> Resume e-Kaizen Progress (E-改善進捗のまとめ)
                            </a>
                            <br>
                            <a href="{{ url("/index/kaizen") }}">
                                <i class="fa fa-caret-right"></i> List Unverified e-Kaizen (未承認E-改善のリスト)
                            </a>
                            <br>
                            {{-- <a href="#">
                                <i class="fa fa-caret-right"></i> List Verified e-Kaizen (承認済E-改善のリスト)
                            </a>
                            <br> --}}
                            {{-- <a href="{{ url("/index/kaizen/applied") }}">
                                <i class="fa fa-caret-right"></i> List Applied e-Kaizen (適用済E-改善のリスト)
                            </a>
                            <br> --}}
                            <a href="{{ url("/index/kaizen2/resume") }}">
                                <i class="fa fa-caret-right"></i> Report All Kaizen (全改善のリポート)
                            </a>
                            <br>
                            <a href="{{ url("index/kaizen2/report") }}">
                                <i class="fa fa-caret-right"></i> Report Kaizen Excellent (エクセレント改善のリポート)
                            </a>
                            <br>
                            <a href="{{ url("index/kaizen2/value") }}">
                                <i class="fa fa-caret-right"></i> Report Kaizen Reward (改善リポートのリワード)
                            </a>
                            
                        </td>
                    </tr>
                    {{--  <tr>
                        <td>
                            <a href="{{ url("index/upload_kaizen") }}">
                                <i class="fa fa-caret-right"></i> Upload Kaizen Images (改善写真のアップロード)
                            </a>
                            <br>
                        </td>
                    </tr> --}}
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
                            <a href="http://10.109.52.8/sf6/" target="_blank">
                                <i class="fa fa-caret-right"></i> Sunfish (サンフィッシュ)
                            </a>
                            <br>
                            <a href="https://app.greatdayhr.com/features/home/home-web" target="_blank">
                                <i class="fa fa-caret-right"></i> GreatDay HR
                            </a>
                            <br>
                            <a href="https://a01.yamaha.co.jp/fw/dfw/CERT/Portal.php" target="_blank">
                                <i class="fa fa-caret-right"></i> IDM Portal (IDMポータル)
                            </a>
                            <br>
                            <a href="https://yamahagroup.sharepoint.com/sites/prj00220" target="_blank">
                                <i class="fa fa-caret-right"></i> Sharepoint;
                            </a>
                            <a href="https://a01.yamaha.co.jp/fw/dfw/SAP2/Citrix/XenApp/site/default.aspx" target="_blank">
                             SAP;
                         </a>
                         <a href="https://adagio.yamaha.co.jp/imart/default.portal" target="_blank">
                             Adagio;
                         </a>
                         <a href="https://a01.yamaha.co.jp/fw/dfw/MA5/ma5/EntranceServlet" target="_blank">
                             MA5;
                         </a>
                     </td>     
                 </tr>                                                                             
             </tbody>
         </table>
         {{-- <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Workshop<br>ワークショップ</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ url("index/workshop/create_wjo") }}">
                            <i class="fa fa-caret-right"></i> Create WJO (作業依頼書の作成)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/wjo_monitoring") }}">
                            <i class="fa fa-caret-right"></i> WJO Monitoring (作業依頼書の監視)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/productivity") }}">
                            <i class="fa fa-caret-right"></i> Workshop Productivity (作業依頼書の実現力)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/workload") }}">
                            <i class="fa fa-caret-right"></i> Workshop Workload (作業依頼書一覧)
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/workload/machine") }}">
                            <i class="fa fa-caret-right"></i> Workshop Machine Workload ()
                        </a>
                        <br>
                        <a href="{{ url("index/workshop/operatorload") }}">
                            <i class="fa fa-caret-right"></i> Workshop Operator Work Schedule (ワークショップ作業者の作業予定)
                        </a>
                        <br>
                    </td>                        
                </tr>
            </tbody>
        </table> --}}
    </div>
    <div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">

        <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Accounting<br>経理課</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ url("investment/control") }}">
                            <i class="fa fa-caret-right"></i> Investment Monitoring & Control (投資管理)
                        </a>
                        <br>
                        <a href="{{ url("budget/info") }}">
                            <i class="fa fa-caret-right"></i> Budget Information (予算情報)
                        </a>
                        <br>
                        <a href="{{ url("index/fixed_asset") }}">
                            <i class="fa fa-caret-right"></i> Fixed Asset (固定資産)
                        </a>  
                        <!-- <br> 
                        <a href="{{ url("index/fixed_asset/audit") }}">
                            <i class="fa fa-caret-right"></i> Fixed Asset Audit (??)
                        </a> -->
                    </td>                     
                </tr>                    
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>General Affair<br>総務課</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ url("index/ga_control/driver") }}">
                            <i class="fa fa-caret-right"></i> Request Driver (ドライバー管理システム)
                        </a>
                        <br>
                        <a href="{{ url("index/ga_control/driver_monitoring") }}">
                            <i class="fa fa-caret-right"></i> Driver Monitoring (ドライバー管理)
                        </a>
                        <br>
                        <a href="{{ url("index/ga_control/bento") }}">
                            <i class="fa fa-caret-right"></i> Japanese Food Order <img src="{{ asset('images/flag/id.png') }}" style="height: 14px; border: 1px solid black;"> (和食弁当の予約)
                        </a>
                        <br>
                        <a href="{{ url("index/ga_control/bento_japanese/".date('F Y')) }}">
                            <i class="fa fa-caret-right"></i> Japanese Food Order <img src="{{ asset('images/flag/jp.png') }}" style="height: 14px; border: 1px solid black;"> (和食弁当の予約)
                        </a>
                        <br>
                        <a href="{{ url('index/ga_control/live_cooking') }}">
                            <i class="fa fa-caret-right"></i> Live Cooking Order (ライブクッキングの予約)
                        </a>
                    </td>                 
                </tr>
            </tbody>
        </table>

        <table class="table table-bordered">
            <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                <tr>
                    <th>Human Resources<br>人事課</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a href="{{ route('emp_service', ['id' =>'1', 'tahun' => $tahun]) }}">
                            <i class="fa fa-caret-right"></i> HRqu (従業員の情報サービス)
                        </a>
                        <br>
                        <a href="{{ url("index/general/online_transportation") }}">
                            <i class="fa fa-caret-right"></i> Attendance & Transportation Report (出社・移動費のオンライン報告)
                        </a>
                        <br>
                        <a href="{{ url("index/general/surat_dokter") }}">
                            <i class="fa fa-caret-right"></i> Dropbox Surat Dokter (診断書のドロップボックス)
                        </a>
                        <br>
                        <a href="{{ url("index/general/agreement") }}">
                            <i class="fa fa-caret-right"></i> Company Agreement List (会社の契約書)
                        </a>
                        <br>
                        <a href="{{ url("dashboard/mutasi") }}">
                            <i class="fa fa-caret-right"></i> Mutasi Satu Department (部門内部署移動)
                        </a>
                        <br>
                        <a href="{{ url("dashboard_ant/mutasi") }}">
                            <i class="fa fa-caret-right"></i> Mutasi Antar Department (部門跨ぐ部署移動)
                        </a>
                            <!-- <br>
                            <a href="{{ url("index/hr/request_manpower") }}">
                                <i class="fa fa-caret-right"></i> Request Manpower (マンパワーを要求する)
                            </a> -->
                            <br>
                            <a href="{{ url("index/human_resource/leave_request") }}">
                                <i class="fa fa-caret-right"></i> Surat Izin Keluar (有休申請書)
                            </a>
                            {{-- <br>
                            <a href="{{ url("human_resource") }}">
                                <i class="fa fa-caret-right"></i> Pengajuan Tunjangan & Simpati (許可と金銭の共感の適用)
                            </a> --}}
                        </td>                 
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Logistic<br>物流課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("/index/warehouse/operatoraktual") }}">
                                <i class="fa fa-caret-right"></i> Warehouse Internal Productivity (-)
                            </a>
                            <br>
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
                            <a href="{{ url("/index/ticket/monitoring/mis") }}">
                                <i class="fa fa-caret-right"></i> MIS Ticketing System (MISチケット依頼)
                            </a>
                            <br>
                            <a href="{{ url("index/server_room") }}">
                                <i class="fa fa-caret-right"></i> Server Room Monitoring (サーバールームモニタリング)
                            </a>
                            <br>
                            <a href="{{ url("index/display/ip?location=server") }}">
                                <i class="fa fa-caret-right"></i> Ping Status Monitoring (IP管理)
                            </a>
                        </td>                 
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Procurement<br>調達課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("/index/material/material_monitoring/direct") }}">
                                <i class="fa fa-caret-right"></i> Raw Material Monitoring (Direct) (素材監視「直材」)
                            </a>
                            <br>
                            <a href="{{ url("/index/material/material_monitoring/indirect") }}">
                                <i class="fa fa-caret-right"></i> Raw Material Monitoring (Indirect) (素材監視「間材」)
                            </a>
                            <br>
                        </td>                 
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Production<br>生産部</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("/index/form_ketidaksesuaian") }}">
                                <i class="fa fa-caret-right"></i> Create Form Ketidaksesuaian (不適合報告フォームを作成)
                            </a>
                            <br>
                            <a href="{{ url("/index/form_ketidaksesuaian/monitoring") }}">
                                <i class="fa fa-caret-right"></i> Monitoring Form Ketidaksesuaian (不適合報告フォームの管理)
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
                            <a href="{{ url("index/sakurentsu/monitoring/3m") }}">
                                <i class="fa fa-caret-right"></i> Sakurentsu Monitoring (作連通監視)
                            </a>
                            <br>
                            <a href="{{ url("/index/stocktaking/menu") }}">
                                <i class="fa fa-caret-right"></i> Monthly Stock Taking (月次棚卸)
                            </a>
                            <br>
                            <a href="{{ url("/index/stocktaking/silver_report") }}">
                                <i class="fa fa-caret-right"></i> Silver Stock Taking Report (銀材棚卸し報告)
                            </a>
                            <br>
                            <a href="{{ url("/index/stocktaking/daily_report") }}">
                                <i class="fa fa-caret-right"></i> Daily Stock Taking Report (日次棚卸し報告)
                            </a>
                            <br>
                            <a href="{{ url("/index/stocktaking/video_tutorial") }}">
                                <i class="fa fa-caret-right"></i> Video Tutorial (棚卸チュートリアル動画)
                            </a>
                            <br>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Production Engineering<br>生産技術課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("winds") }}">
                                <i class="fa fa-caret-right"></i> WINDS
                            </a>
                            <br>
                            <a href="{{ url("winds") }}">
                                <i class="fa fa-caret-right"></i> EJOR & Project Monitoring
                            </a>
                            <br>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <a href="{{ url("index/workshop/create_wjo") }}">
                                <i class="fa fa-caret-right"></i> Create WJO (作業依頼書の作成)
                            </a>
                            <br>
                            <a href="{{ url("index/workshop/wjo_monitoring") }}">
                                <i class="fa fa-caret-right"></i> WJO Monitoring (作業依頼書の監視)
                            </a>
                            <br>
                            <a href="{{ url("index/workshop/productivity") }}">
                                <i class="fa fa-caret-right"></i> Workshop Productivity (作業依頼書の実現力)
                            </a>
                            <br>
                            <a href="{{ url("index/workshop/workload") }}">
                                <i class="fa fa-caret-right"></i> Workshop Workload (作業依頼書一覧)
                            </a>
                            <br>
                            <a href="{{ url("index/workshop/workload/machine") }}">
                                <i class="fa fa-caret-right"></i> Workshop Machine Workload ()
                            </a>
                            <br>
                            <a href="{{ url("index/workshop/operatorload") }}">
                                <i class="fa fa-caret-right"></i> Workshop Operator Work Schedule (ワークショップ作業者の作業予定)
                            </a>
                            <br>
                        </td>                        
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Purchasing Control<br>購買管理課</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("purchase_requisition/monitoring") }}">
                                <i class="fa fa-caret-right"></i> PR Monitoring & Control (PR監視・管理)
                            </a>
                            <br>
                            <a href="{{ url("purchase_order/monitoring") }}">
                                <i class="fa fa-caret-right"></i> PO Monitoring & Control (PO管理)
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>

            <table class="table table-bordered">
                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                    <tr>
                        <th>Quality Assurance<br>品保</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>
                            <a href="{{ url("index/qc_report/grafik_cpar") }}">
                                <i class="fa fa-caret-right"></i> CPAR & CAR Monitoring (是正予防策・是正策監視)
                            </a>
                            <br>
                            <a href="{{ url("index/qa_cpar") }}">
                                <i class="fa fa-caret-right"></i> CPAR & CAR Data (品保是正予防策リポートと是正策データ)
                            </a>
                            <br>
                            <a href="{{ url("index/qa_ymmj_index") }}">
                                <i class="fa fa-caret-right"></i> YMMJ Report (YMMJ品保の報告データ)
                            </a>
                            <br>
                            <a href="{{ url("index/qa") }}">
                                <i class="fa fa-caret-right"></i> Incoming Check (受入検査)
                            </a>
                            <br>
                            <a href="{{ url("index/qa/audit_ng_jelas") }}">
                                <i class="fa fa-caret-right"></i> Audit NG Jelas (品保の明らか不良検査)
                            </a>
                        </td>                 
                    </tr>
                    <tr>
                        <td>
                            <a href="{{ url("index/license/equipment") }}">
                                <i class="fa fa-caret-right"></i> Equipment License Control ()
                            </a>
                            <br>
                            <a href="{{ url("index/license/operator") }}">
                                <i class="fa fa-caret-right"></i> Operator License Control ()
                            </a>
                            <br>
                            <a href="{{ secure_url("index/std_control/safety_shoes") }}">
                                <i class="fa fa-caret-right"></i> Safety Shoes Control (安全靴管理システム)
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
                            <span style="font-weight: bold;">Smart TPM (??)</span>
                            <br>
                            <a href="{{ url("index/maintenance/tpm/dashboard") }}">
                                <i class="fa fa-caret-right"></i> Smart TPM (??)
                            </a>
                        </td>                     
                    </tr>
                    <tr>
                        <td>
                            <span style="font-weight: bold;">Machine Monitoring (マシン監視)</span>
                            <br>
                            <a href="{{ url('index/maintenance/machine/part_list') }}">
                                <i class="fa fa-caret-right"></i> Machine Part List
                            </a>
                            <br>
                            <a href="{{ url('index/maintenance/machine/part_graph') }}">
                                <i class="fa fa-caret-right"></i> Machine Part Graph
                            </a>
                            <br>
                            <a href="http://10.109.52.7/zed/dashboard/awal" target="_blank">
                                <i class="fa fa-caret-right"></i> Overall Equipment Efficiency (稼働率) (OEE)
                            </a>
                            <br>
                            <a href="{{ url("/machinery_monitoring?mesin=") }}">
                                <i class="fa fa-caret-right"></i> Machinery Monitoring (機械監視)
                            </a>
                            <br>
                            <a href="http://10.109.52.7/mtnc/login/log" target="_blank">
                                <i class="fa fa-caret-right"></i> Planned Maintenance Activity Finding
                            </a>
                            <br>
                            <a href="{{ url("index/maintenance/machine_report/list") }}">
                                <i class="fa fa-caret-right"></i> Machine Breakdown Report
                            </a>
                            <br> 
                            <a href="{{ url("index/maintenance/machine_report/graph") }}">
                                <i class="fa fa-caret-right"></i> Machine Breakdown Graph
                            </a>
                            <br>
                        <!-- <a href="{{ url("index/maintenance/mttr/list") }}">
                            <i class="fa fa-caret-right"></i> MTTR (??)
                        </a>
                        <br> -->
                        <a href="{{ url("index/maintenance/operator/position") }}">
                            <i class="fa fa-caret-right"></i> Maintenance Operator Location (保全班作業者の位置)
                        </a>
                        <!-- <br>
                        <a href="{{ url("index/maintenance/operator/workload") }}">
                            <i class="fa fa-caret-right"></i> Maintenance Operator Workload (??)
                        </a> -->

                        <br>
                        <a href="{{ url("index/maintenance/machine_report/report") }}">
                            <i class="fa fa-caret-right"></i> Maintenance Trouble Report
                        </a>
                    </td>                     
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">SPK (メンテナンス作業依頼書)</span>
                        <br>
                        <a href="{{ url("index/maintenance/list/user") }}">
                            <i class="fa fa-caret-right"></i> Create SPK (作業依頼書を作成)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/spk/grafik") }}">
                            <i class="fa fa-caret-right"></i> SPK Monitoring (作業依頼書の管理)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/spk/workload") }}">
                            <i class="fa fa-caret-right"></i> SPK Workload
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/operator/workload") }}">
                            <i class="fa fa-caret-right"></i> Operator Workload
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/spk/weekly") }}">
                            <i class="fa fa-caret-right"></i> SPK Weekly Report
                        </a>
                        <!-- <br>
                         <a href="{{ url("index/report/urgent_monitoring") }}">
                            <i class="fa fa-caret-right"></i> Urgent SPK Monitoring (??)
                        </a>
                        <br>  -->

                    </td>
                </tr>
                <tr>
                    <td>
                        <span style="font-weight: bold;">Utility (ユーティリティー)</span>
                        <br>
                        <a href="{{ url("index/maintenance/apar") }}">
                            <i class="fa fa-caret-right"></i> APAR Check Schedule (消火器・消火栓の点検日程)
                        </a>
                        <br>
                        <a href="{{ secure_url("/index/maintenance/apar/expire") }}">
                            <i class="fa fa-caret-right"></i> APAR Expired List 消火器・消火栓の使用期限一覧)
                        </a>
                        <br>
                        <a href="{{ url("index/maintenance/apar/resume") }}">
                            <i class="fa fa-caret-right"></i> APAR Resume (消火栓・消火器の点検進捗のまとめ)
                        </a>
                        <!-- <br>
                        <a href="{{ url("index/maintenance/pic/Utility") }}">
                            <i class="fa fa-caret-right"></i> PIC List (担当者リスト)
                        </a> -->
                        <!-- <br>
                        <a href="{{ url('/index/production_report/index/13') }}">
                            <i class="fa fa-caret-right"></i> PE Field Report (??)
                        </a>
                    </td>
                </tr>
            -->
            <tr>
                <td>
                    <span style="font-weight: bold;">Planned Maintenance (予定保全)</span>
                    <br>
                    <!-- <a href="{{ url("index/maintenance/pm/schedule") }}">
                        <i class="fa fa-caret-right"></i> Plan Maintenance Schedule (予定保全の計画)
                    </a>
                    <br> -->
                    <a href="{{ url("index/maintenance/pm/monitoring") }}">
                        <i class="fa fa-caret-right"></i> Plan Maintenance Monitoring (保全計画の監視)
                    </a>
                    <br>
                    <a href="{{ url("index/maintenance/pm/trendline") }}">
                        <i class="fa fa-caret-right"></i> Plan Maintenance Trendline Graph
                    </a>
                    <br>
                    <!-- <a href="{{ url("index/maintenance/apar") }}">
                        <i class="fa fa-caret-right"></i> PIC List (担当者リスト)
                    </a>
                    <br> -->
                </td>
            </tr> 
        </tbody>
    </table>
</div>
<div class="col-md-3" style="padding-left: 3px; padding-right: 3px;">
    <table class="table table-bordered">
        <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
            <tr>
                <th>Work In Process<br/>仕掛品</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <span style="font-weight: bold;">KITTO (きっと)</span>
                    <br>
                    <a href="http://10.109.52.4/kitto/public">
                        <i class="fa fa-caret-right"></i> Kanban Monitoring (かんばん監視)
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-weight: bold;">INJECTION Process (成形プロセス)</span>

                    <br>
                    <a href="{{ url("/index/injeksi") }}">
                        <i class="fa fa-caret-right"></i> Injection Recorder (RC成形)
                    </a>
                    <br>
                    <a href="{{ url("/index/reed") }}">
                        <i class="fa fa-caret-right"></i> Injection Reed Synthetic (樹脂リード成形)
                    </a>
                </td>
            </tr>
            <tr>
                <td>
                    <span style="font-weight: bold;">MATERIAL Process (イニシアル工程)</span>
                    <br>
                    <a href="{{ url("/index/initial", "press") }}">
                        <i class="fa fa-caret-right"></i> Press (プレス)
                    </a>
                    <br>
                    <a href="{{ url("/index/initial", "lotting") }}">
                        <i class="fa fa-caret-right"></i> Lotting (ロッティング)
                    </a>
                    <br>
                    <a href="{{ url("/index/press/monitoring") }}">
                        <i class="fa fa-caret-right"></i> Press Machine Monitoring (プレス機管理)
                    </a>
                    <br>
                    <a href="{{ url("/index/initial/stock_monitoring", "mpro") }}">
                        <i class="fa fa-caret-right"></i> M-PRO Stock Monitoring (部品加工の仕掛品監視)
                    </a>
                    <br>
                    <a href="{{ url("/index/initial/stock_trend", "mpro") }}">
                        <i class="fa fa-caret-right"></i> M-PRO Stock Trend (部品加工の在庫トレンド)
                    </a>
                    <br>
                    <a href="http://10.109.52.7/tpro/" target="_blank">
                        <i class="fa fa-caret-right"></i> M-Pro Kanban Monitoring (Mプロかんばんの監視)
                    </a>
                                <!-- <br>
                                <a href="{{ url("/index/tools") }}">
                                    <i class="fa fa-caret-right"></i> Digital Order & Control Stock Tools ()
                                </a> -->
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">BODY Process (イニシアル工程)</span>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_cl") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (ロッティング)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート)
                                </a>
                                <br>
                                <a href="{{ url("/index/initial", "bpro_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">WELDING Process (溶接工程)</span>
                                <br>
                                <a href="{{ url("/index/process_welding_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート溶接)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_stamp_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス溶接)
                                </a>
                                <br>
                                <a href="{{ url("/index/welding_jig") }}">
                                    <i class="fa fa-caret-right"></i> Digital Jig Handling (冶具デジタル管理)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/welding_fl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Flute Picking Monitor (フルートのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/welding_sax?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/welding_cl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking Monitor (クラリネットピッキング監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">MIDDLE Process (中間工程)</span>
                                <br>
                                <a href="{{ url("/index/process_middle_cl") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet (クラリネット)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_middle_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート表面処理)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_middle_sx") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス表面処理)
                                </a>
                                <br>
                                <a href="{{ url("/index/process_middle_acc") }}">
                                    <i class="fa fa-caret-right"></i> Accessories (付属部品の表面処理)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/stockroom_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Stockroom Monitoring (ストックルームの監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/middle/stock_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Middle Stock Monitoring (中間工程の仕掛品監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">FINAL Process (最終工程)</span>
                                <br>
                                <a href="{{ url("/index/process_assy_fl") }}">
                                    <i class="fa fa-caret-right"></i> Flute (フルート仮組~組立)
                                </a>
                                <br>
                                <a href="{{ url("index/process_stamp_sx_assy") }}">
                                    <i class="fa fa-caret-right"></i> Saxophone (サックス仮組～組立)
                                </a>
                                <br>
                                <a href="{{ url("/index/Pianica") }}">
                                    <i class="fa fa-caret-right"></i> Pianica (ピアニカ組立)
                                </a>
                                <br>                                
                                <a href="{{ url("index/recorder_process") }}">
                                    <i class="fa fa-caret-right"></i> Recorder (リコーダー)
                                </a>
                                <br>
                                <a href="{{ url("index/final/reed_synthetic") }}">
                                    <i class="fa fa-caret-right"></i> Reed Synthetic (樹脂リード)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/assy_fl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Flute Key Picking Monitor (フルートのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/body/fl_body?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Flute Body Picking Monitor (フルートのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/assy_sax?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Sax Key Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/body/sax_body?date=&surface2=&key2=&model2=&hpl2=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Sax Body Picking Monitor (サックスのピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/display/sub_assy/assy_cl?date=&order2=") }}">
                                    <i class="fa fa-caret-right"></i> Clarinet Picking Monitor (クラリネットピッキング監視)
                                </a>
                                <br>
                                <a href="{{ url("/index/process/tanpo_stock_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Tanpo Stock Monitor (タンポ在庫モニター)
                                </a>                                
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Check Material Dimensions (寸法測定結果)</span>
                                <br>
                                <a href="http://10.109.52.11/digital-ik-cdm/">
                                    <i class="fa fa-caret-right"></i> Work Instruction Digital System (作業手順書デジタル化)
                                </a>
                                <br>
                                <a href="http://10.109.52.11/cdm-new/">
                                    <i class="fa fa-caret-right"></i> T-Pro CDM Charts (T-ProのCDMチャート)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">NG Jelas (明らか不良)</span>
                                <br>
                                <a href="{{ url("/index/audit_ng_jelas_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Audit NG Jelas Monitoring (明らか不良監査の監視)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Audit IK (作業手順書監査)</span>
                                <br>
                                <a href="{{ url("/index/audit_ik_monitoring") }}">
                                    <i class="fa fa-caret-right"></i> Audit IK Monitoring (作業手順書監査表示)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">LEADER Control (職長管理)</span>
                                <br>
                                <a href="{{ url("/index/efficiency/leader") }}">
                                    <i class="fa fa-caret-right"></i> Efficiency (次効率)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/8") }}">
                                    <i class="fa fa-caret-right"></i> Assembly (WI-A) Report (アセンブリ（WI-A）レポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/17") }}">
                                    <i class="fa fa-caret-right"></i> Body Parts Process (WI-BPP) Report (ボディパーツ加工)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/12") }}">
                                    <i class="fa fa-caret-right"></i> Key Parts Process (WI-KPP) Report (主要部品加工)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/15") }}">
                                    <i class="fa fa-caret-right"></i> Welding Process Report (溶接プロセスリポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/18") }}">
                                    <i class="fa fa-caret-right"></i> Surface-Treatment Report (表面処理レポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/10") }}">
                                    <i class="fa fa-caret-right"></i> Maintenance Report (メンテナンスリポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/13") }}">
                                    <i class="fa fa-caret-right"></i> PE Field Report (PEフィールドレポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/5") }}">
                                    <i class="fa fa-caret-right"></i> Logistic Report (兵站学レポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/9") }}">
                                    <i class="fa fa-caret-right"></i> EI Report (教育楽器レポート)
                                </a>
                                <br>
                                <a href="{{ url("/index/production_report/index/14") }}">
                                    <i class="fa fa-caret-right"></i> QA Report (品保レポート)
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Tools</span>
                                <br>
                                <a href="{{ url("/index/tools") }}">
                                    <i class="fa fa-caret-right"></i> Digital Order & Control Stock Tools ()
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
                            <th>Finished Goods & KD Parts<br/>完成品・KD部品</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                <span style="font-weight: bold;">Finished Goods Control (完成品管理)</span>
                                <br>
                                <a href="{{ url("/index/fg_production_schedule") }}">
                                    <i class="fa fa-caret-right"></i> Production Schedule Data (生産スケジュールデータ)
                                </a>
                                {{-- <br>
                                    <a href="{{ url("/index/fg_production_monitoring") }}">
                                        <i class="fa fa-caret-right"></i> Production Schedule Monitoring (生産予定監視)
                                    </a> --}}
                                    <br>
                                    <a href="{{ url("/index/dp_production_result") }}">
                                        <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/dp_fg_accuracy") }}">
                                        <i class="fa fa-caret-right"></i> FG Accuracy (FG週次出荷)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/fg_production") }}">
                                        <i class="fa fa-caret-right"></i> Production Result (生産実績)
                                    </a>
                                    <br>
                                    <a href="{{ url("/index/fg_stock") }}">
                                        <i class="fa fa-caret-right"></i> Finished Goods Stock (完成品在庫)
                                    </a>
                                    {{--  <br>
                                        <a href="{{ url("/index/display/shipment_progress") }}">
                                            <i class="fa fa-caret-right"></i> FG Shipment Progress (出荷結果)
                                        </a> --}}
                                        <br>
                                        <a href="{{ url("/index/fg_traceability") }}">
                                            <i class="fa fa-caret-right"></i> FG Traceability (FG完成品追跡)
                                        </a>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <span style="font-weight: bold;">KD Parts Control (KD部品管理)</span>
                                        {{-- <br>
                                            <a href="{{ url("/index/kd_production_schedule_data") }}">
                                                <i class="fa fa-caret-right"></i> Production Schedule Data (生産スケジュールデータ)
                                            </a> --}}
                                            {{--   <br>
                                                <a href="{{ url("/index/kd_daily_production_result") }}">
                                                    <i class="fa fa-caret-right"></i> Daily Production Result (日常生産実績)
                                                </a> --}}
                                                <br>
                                                <a href="{{ url("/index/kd_stock") }}">
                                                    <i class="fa fa-caret-right"></i> KD Parts Stock (KD部品在庫)
                                                </a>
                                                <br>
                                                <a href="{{ url("/index/kd_traceability") }}">
                                                    <i class="fa fa-caret-right"></i> KD Traceability (KD完成品追跡)
                                                </a>
                                                {{--  <br>
                                                    <a href="{{ url("/index/kd_shipment_progress") }}">
                                                        <i class="fa fa-caret-right"></i> KD Shipment Progress (出荷結果)
                                                    </a> --}}
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Shipment Control (出荷管理)</span>
                                                    <br>
                                                    <a href="{{ url("/index/display/all_stock") }}">
                                                        <i class="fa fa-caret-right"></i> All Stock (全在庫)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("index/resume_shipping_order") }}">
                                                        <i class="fa fa-caret-right"></i> Shipping Booking Management List (船便予約管理リスト)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_shipment_schedule") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Schedule Data (出荷スケジュールデータ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_shipment_result") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Result (出荷結果)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/display/shipment_progress_all") }}">
                                                        <i class="fa fa-caret-right"></i> Shipment Progress (出荷結果)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/display/shipment_report") }}">
                                                        <i class="fa fa-caret-right"></i> Weekly Shipment ETD SUB (週次出荷　スラバヤ着荷)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_container_departure") }}">
                                                        <i class="fa fa-caret-right"></i> Container Departure (コンテナー出発)
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Shipment Performance (出荷管理)</span>
                                                    <br>
                                                    <a href="{{ url("/index/display/stuffing_monitoring") }}">
                                                        <i class="fa fa-caret-right"></i> Stuffing Monitoring (荷積み監視)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_weekly_summary") }}">
                                                        <i class="fa fa-caret-right"></i> Weekly Summary (週次まとめ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/fg_monthly_summary") }}">
                                                        <i class="fa fa-caret-right"></i> Monthly Summary (月次まとめ)
                                                    </a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>
                                                    <span style="font-weight: bold;">Chorei (朝礼)</span>
                                                    <br>
                                                    <a href="{{ url("/index/ch_daily_production_result") }}">
                                                        <i class="fa fa-caret-right"></i> FG Production Summary (FG生産まとめ)
                                                    </a>
                                                    <br>
                                                    <a href="{{ url("/index/ch_daily_production_result_kd") }}">
                                                        <i class="fa fa-caret-right"></i> KD Production Summary (KD生産まとめ)
                                                    </a>
                                                    {{--  <br>
                                                        <a href="{{ url("/index/display/efficiency_monitoring") }}">
                                                            <i class="fa fa-caret-right"></i> Daily Efficiency Monitoring (日次効率の監視)
                                                        </a> --}}
                                                        <br>
                                                        <a href="{{ url("/index/display/efficiency_monitoring_monthly") }}">
                                                            <i class="fa fa-caret-right"></i> Monthly Efficiency Monitoring (月次効率の監視)
                                                        </a>
                                                        <br>
                                                        <a href="{{ url("/index/display/eff_scrap") }}">
                                                            <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                                        </a>
                                                        <br>
                                                        <a href="{{ url("/index/general/pointing_call/japanese") }}">
                                                            <i class="fa fa-caret-right"></i> Japanese Pointing Call (駐在員指差し呼称)
                                                        </a>
                                                        <br>
                                                        <a href="{{ url("/index/general/pointing_call/national") }}">
                                                            <i class="fa fa-caret-right"></i> NS Pointing Call (ナショナル・スタッフ用の指差し呼称)
                                                        </a>
                                                        {{-- <br>
                                                            <a href="{{ url("/index/production_achievement") }}">
                                                                <i class="fa fa-caret-right"></i> Production Achievement ()
                                                            </a> --}}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>
                                                            <span style="font-weight: bold;">Others (他の情報)</span>
                                                            <br>
                                                          {{--   <a href="{{ url("/index/display/efficiency_monitoring_monthly") }}">
                                                                <i class="fa fa-caret-right"></i> YMPI Efficiency ()
                                                            </a>
                                                            <br> --}}
                                                            <a href="{{ url("/index/display/efficiency_monitoring_monthly") }}">
                                                                <i class="fa fa-caret-right"></i> Monthly Efficiency Monitoring (月次効率の監視)
                                                            </a>
                                                            <br>
                                                            <a href="{{ url("/index/display/eff_scrap") }}">
                                                                <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                                            </a>
                                                        </tr>
                                                        {{-- <tr>
                                                            <td>
                                                                <span style="font-weight: bold;">Additional</span>
                                                                <br>
                                                                <a href="{{ url("flute_repair") }}">
                                                                    <i class="fa fa-caret-right"></i> Flute Repair
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("recorder_repair") }}">
                                                                    <i class="fa fa-caret-right"></i> Recorder Repair
                                                                </a>
                                                            </td>
                                                        </tr> --}}
                                                        {{-- <tr>
                                                            <td>
                                                                <span style="font-weight: bold;">Display (表示)</span>
                                                                <br>
                                                                <a href="{{ url("/index/display/stuffing_progress") }}">
                                                                    <i class="fa fa-caret-right"></i> Stuffing Progress (荷積み進捗)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("/index/display/stuffing_time") }}">
                                                                    <i class="fa fa-caret-right"></i> Stuffing Time (荷積み時間)
                                                                </a>
                                                            </td>
                                                        </tr> --}}
                                                    </tbody>
                                                </table>

                                                {{--       <table class="table table-bordered">
                                                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                        <tr>
                                                            <th>Chorei<br/> 朝礼</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="{{ url("/index/ch_daily_production_result") }}">
                                                                    <i class="fa fa-caret-right"></i> Production Summary (生産まとめ)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("/index/display/eff_scrap") }}">
                                                                    <i class="fa fa-caret-right"></i> Scrap Monitoring (スクラップの監視)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("/index/general/pointing_call/japanese") }}">
                                                                    <i class="fa fa-caret-right"></i> Japanese Pointing Call (駐在員指差し呼称)
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table> --}}


                                                <table class="table table-bordered">
                                                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                        <tr>
                                                            <th>PR, PO, Investment & Budget Control<br/> 購入依頼書・投資申請</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="{{ url("purchase_requisition/monitoring") }}">
                                                                    <i class="fa fa-caret-right"></i> PR Monitoring & Control (PR監視・管理)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("investment/control") }}">
                                                                    <i class="fa fa-caret-right"></i> Investment Monitoring & Control (投資管理)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("purchase_order/monitoring") }}">
                                                                    <i class="fa fa-caret-right"></i> PO Monitoring & Control (PO管理)
                                                                </a>
                                                                <br>
                                                                <a href="{{ url("budget/info") }}">
                                                                    <i class="fa fa-caret-right"></i> Budget Information (予算情報)
                                                                </a>
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table>

                                                {{-- <table class="table table-bordered">
                                                    <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                                        <tr>
                                                            <th>
                                                                <span style="font-weight: bold;">Internet of Things<br>モノのインターネット</span>
                                                            </th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <tr>
                                                            <td>
                                                                <a href="{{ url("index/driver_manager") }}">
                                                                    <i class="fa fa-caret-right"></i> Report Driver IN / OUT <span style="color:red"> *Beacon </span>
                                                                </a>                           
                                                            </td>
                                                        </tr>
                                                    </tbody>
                                                </table> --}}

                           <!--  <table class="table table-bordered">
                                <thead style="background-color: rgba(126,86,134,.7); font-size: 14px;">
                                    <tr>
                                        <th>Sakurentsu<br/></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <a href="{{ url("index/sakurentsu/monitoring") }}">
                                                <i class="fa fa-caret-right"></i> Sakurentsu Monitoring ()
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table> -->
                        </div>
                    </div>
                </section>

                @stop
                @section('scripts')
                <script src="{{ url("js/jquery.marquee.min.js")}}"></script>
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