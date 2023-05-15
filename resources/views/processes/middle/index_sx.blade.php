@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        .tab-master {
            font-size: 17px;
            border-color: black;
            color: black;
        }

        .tab-master-block {
            font-size: 17px;
            border-color: black;
            color: white;
            background-color: #808080;
        }

        .tab-master:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: black;
        }

        .tab-master-block:hover {
            font-weight: bold;
            color: black;
            background-color: #bfbfbf;
            border-color: black;
            color: white;
        }

        .tab-process {
            font-size: 17px;
            border-color: green;
            color: black;
        }

        .tab-process-body {
            font-size: 17px;
            border-color: green;
            color: black;
            background-color: #ccff90;
        }

        .highlight-process {
            font-size: 20px;
            font-weight: bold;
            color: green;
            text-shadow: 1px 1px 5px #ccff90;
        }

        .tab-process:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: green;
        }

        .tab-process-body:hover {
            font-weight: bold;
            color: black;
            background-color: #e1ffbd;
            border-color: green;
        }

        .tab-display {
            font-size: 17px;
            border-color: red;
            color: black;
        }

        .tab-display-body {
            font-size: 17px;
            border-color: red;
            color: black;
            background-color: #ff5757;
        }

        .tab-display:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: purple;
        }

        .tab-display-body:hover {
            font-weight: bold;
            color: black;
            background-color: #ff9999;
            border-color: red;
        }

        .highlight-display {
            font-size: 20px;
            font-weight: bold;
            color: #C51C5A;
            text-shadow: 1px 1px 5px #ff9999;
        }

        .tab-report {
            font-size: 17px;
            border-color: purple;
            color: black;
        }

        .tab-report-body {
            font-size: 17px;
            border-color: purple;
            color: black;
            background-color: #a6a3f0;
        }

        .tab-report:hover {
            font-weight: bold;
            color: black;
            background-color: #E7E7E7;
            border-color: purple;
        }

        .tab-report-body:hover {
            font-weight: bold;
            color: black;
            background-color: #c1bff2;
            border-color: purple;
        }

        .highlight-report {
            font-size: 20px;
            font-weight: bold;
            color: purple;
            text-shadow: 1px 1px 5px #c1bff2;
        }

        .highlight-black {
            font-size: 20px;
            font-weight: bold;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            Saxophone Surface Treatment<span class="text-purple"> 表面処理</span>
            <small>WIP Control <span class="text-purple"> 仕掛品管理</span></small>
        </h1>
    </section>
@stop
@section('content')
    <section class="content">
        @foreach (Auth::user()->role->permissions as $perm)
            @php
                $navs[] = $perm->navigation_code;
            @endphp
        @endforeach


        <div class="row" style="margin-top: 1%;">

            @if (in_array('A9', $navs))
                <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                    <span style="font-size: 22px; color: black;"><i class="fa fa-angle-double-down"></i> Master Buffing <i
                            class="fa fa-angle-double-down"></i></span>
                    <a href="" class="btn btn-default btn-block tab-master">Kanban Edar</a>
                    <a href="{{ url('/index/middle/buffing_operator', 'bff-sx') }}"
                        class="btn btn-default btn-block tab-master">Buffing Operator</a>
                    <a href="{{ url('/index/middle/buffing_kanban', 'SX51') }}"
                        class="btn btn-default btn-block tab-master">Buffing Kanban</a>
                    <a href="{{ url('/index/middle/buffing_target', 'bff') }}"
                        class="btn btn-default btn-block tab-master">Buffing Target</a>

                    <span style="font-size: 22px; color: black;"><i class="fa fa-angle-double-down"></i> Master Lacquering
                        <i class="fa fa-angle-double-down"></i></span>
                    <a href="{{ url('/index/middle/buffing_target', 'lcq') }}"
                        class="btn btn-default btn-block tab-master">Lacquering Target</a>

                    <span style="font-size: 22px; color: black;"><i class="fa fa-angle-double-down"></i> Master Plating
                        <i class="fa fa-angle-double-down"></i></span>
                    <a href="{{ url('/index/middle/buffing_target', 'plt') }}"
                        class="btn btn-default btn-block tab-master">Plating Target</a>
                </div>
            @endif


            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: green;"><i class="fa fa-angle-double-down"></i> Process Buffing <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/request/043?filter=') }}" class="btn btn-default btn-block tab-process"><span
                        class="highlight-process">Key</span> Request</a>
                <a href="{{ url('index/middle/buffing_work_order', 'bff-sx') }}"
                    class="btn btn-default btn-block tab-process"><span class="highlight-process">Key</span> Work Order</a>
                @if (in_array('A9', $navs))
                    <a href="{{ url('/index/middle/buffing_canceled') }}"
                        class="btn btn-default btn-block tab-process">Cancel <span class="highlight-process">Key</span>
                        Buffing Job</a>
                @endif
                <a href="{{ url('index/process_buffing_kensa', 'bff-kensa-sx') }}"
                    class="btn btn-default btn-block tab-process">Kensa Buffing <span
                        class="highlight-process">Key</span></a>
                <a href="{{ url('index/body/kensa/bff-kensa-body-sx') }}"
                    class="btn btn-default btn-block tab-process-body">Kensa Buffing <span
                        class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Process Barrel <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/process_buffing_inout') }}" class="btn btn-default btn-block tab-process">In / Out
                    Store <span class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_barrel', 'barrel-sx-lcq') }}"
                    class="btn btn-default btn-block tab-process">Lacquering <span class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_barrel', 'barrel-sx-plt') }}"
                    class="btn btn-default btn-block tab-process">Plating <span class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_barrel', 'barrel-sx-flanel') }}"
                    class="btn btn-default btn-block tab-process">Flanel <span class="highlight-process">Key</span></a>
                <a href="{{ url('index/body/kensa/barrel-kensa-body-sx') }}"
                    class="btn btn-default btn-block tab-process-body">Barrel Kensa <span
                        class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Process Lacquering <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/process_middle_kensa', 'lcq-incoming') }}"
                    class="btn btn-default btn-block tab-process">Incoming Check <span
                        class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_kensa', 'lcq-kensa') }}"
                    class="btn btn-default btn-block tab-process">Kensa <span class="highlight-process">Key</span></a>
                {{-- <a href="{{ url("index/process_middle_kensa", "lcq-incoming2") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">Incoming Check (After Treatment)</a> --}}
                {{-- <a href="{{ url("index/process_middle_kensa", "incoming-lcq-body") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: green;">IC LCQ Body</a> --}}
                <a href="{{ url('index/body/kensa/lcq-incoming-body-sx') }}"
                    class="btn btn-default btn-block tab-process-body">Incoming Check <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/kensa/lcq-kensa-body-sx') }}"
                    class="btn btn-default btn-block tab-process-body">Kensa <span class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Process Plating <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/process_middle_kensa', 'plt-incoming-sx') }}"
                    class="btn btn-default btn-block tab-process">Incoming Check <span
                        class="highlight-process">Key</span></a>
                <a href="{{ url('index/process_middle_kensa', 'plt-kensa-sx') }}"
                    class="btn btn-default btn-block tab-process">Kensa <span class="highlight-process">Key</span></a>
                <a href="{{ url('index/body/kensa/plt-incoming-body-sx') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Incoming Check <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/enthol/plt-enthol-body-sx') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Cuci Enthol <span
                        class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/kensa/plt-kensa-body-sx') }}"
                    class="btn btn-default btn-block tab-process tab-process-body">Kensa <span
                        class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Repair <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/process_middle_return', 'buffing') }}"
                    class="btn btn-default btn-block tab-process">Repair/Return <span class="highlight-process">Key</span>
                    to Buffing</a>
                <a href="{{ url('index/process_middle_return/body', 'buffing') }}"
                    class="btn btn-default btn-block tab-process-body">Repair/Return <span
                        class="highlight-black">Body</span> to Buffing</a>

                <!-- <br>
                                                       <span style="font-size: 24px; color: green;"><i class="fa fa-angle-double-down"></i> Audit <i class="fa fa-angle-double-down"></i></span>
                                                       <a href="{{ url('index/middle/audit', 'clean-room-lcq') }}" class="btn btn-default btn-block tab-process">Audit <span class="highlight-process">Clean Room</span> Lacquering</a> -->
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 24px; color: red;"><i class="fa fa-angle-double-down"></i> Display <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/op_analysis?dateFrom=&dateTo=') }}"
                    class="btn btn-default btn-block tab-display">OP Analysis</a>
                <a href="{{ url('index/middle/display_monitoring?location=') }}"
                    class="btn btn-default btn-block tab-display">Kanban WIP Monitoring</a>
                <a href="{{ url('index/middle/display_kensa_time?tanggal=&location=') }}"
                    class="btn btn-default btn-block tab-display">Operator Kensa ΣTime</a>
                <a href="{{ url('index/middle/display_production_result?tanggal=&location=') }}"
                    class="btn btn-default btn-block tab-display">Production Result</a>
                <a href="{{ url('index/middle/request/display/043?filter=') }}"
                    class="btn btn-default btn-block tab-display">Material Request Soldering</a>

                <br>
                <span style="font-size: 22px; color: red;"><i class="fa fa-angle-double-down"></i> Display Buffing <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/buffing_board/buffing-sx?page=') }}"
                    class="btn btn-default btn-block tab-display">Buffing <span class="highlight-display">Key</span>
                    Board</a>
                <a href="{{ url('index/body/display/board/bff-sax-1') }}"
                    class="btn btn-default btn-block tab-display-body">Buffing <span class="highlight-black">Body</span>
                    Board 1</a>
                <a href="{{ url('index/body/display/board/bff-sax-2') }}"
                    class="btn btn-default btn-block tab-display-body">Buffing <span class="highlight-black">Body</span>
                    Board 2</a>
                {{-- <a href="{{ url("index/middle/buffing_daily_ng_rate") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily NG Rate</a> --}}
                <a href="{{ url('index/middle/buffing_ng') }}" class="btn btn-default btn-block tab-display">NG Rate</a>
                <a href="{{ url('index/middle/buffing_op_ranking?bulan=&target=') }}"
                    class="btn btn-default btn-block tab-display">Resume NG Rate & Productivity</a>
                {{-- <a href="{{ url("index/middle/buffing_trend_op_eff") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily Operator Trends</a> --}}
                <a href="{{ url('index/middle/buffing_op_ng?tanggal=&group=') }}"
                    class="btn btn-default btn-block tab-display">NG Rate by Operator</a>
                <a href="{{ url('index/middle/buffing_op_eff?tanggal=&group=') }}"
                    class="btn btn-default btn-block tab-display">Operator Overall Efficiency</a>
                <a href="{{ url('index/middle/buffing_resume_konseling') }}"
                    class="btn btn-default btn-block tab-display">Resume Operator Counseling</a>
                <a href="{{ url('index/middle/buffing_group_achievement') }}"
                    class="btn btn-default btn-block tab-display">Group Achievement</a>
                <a href="{{ url('index/middle/buffing_group_balance') }}"
                    class="btn btn-default btn-block tab-display">Group Work Balance</a>
                <a href="{{ url('index/middle/buffing_operator_assesment') }}"
                    class="btn btn-default btn-block tab-display">Operator Evaluation</a>
                {{-- <a href="{{ url("index/middle/buffing_daily_op_ng_rate") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Daily NG Rate by Operator</a> --}}
                {{-- <a href="{{ url("index/middle/muzusumashi") }}" class="btn btn-default btn-block" style="font-size: 24px; border-color: red;">Mizusumashi Monitoring</a> --}}
                <a href="{{ url('index/middle/buffing_ic_atokotei') }}"
                    class="btn btn-default btn-block tab-display">Incoming Check Lacquering</a>

                <br>
                <span style="font-size: 22px; color: red;"><i class="fa fa-angle-double-down"></i> Display Barrel <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/barrel_board/barrel-sx') }}"
                    class="btn btn-default btn-block tab-display">Barrel <span class="highlight-display">Key</span>
                    Board</a>
                <a href="{{ url('index/middle/barrel_machine') }}" class="btn btn-default btn-block tab-display">Machine
                    Activity</a>

                <br>
                <span style="font-size: 22px; color: red;"><i class="fa fa-angle-double-down"></i> Display Lacquering <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/ic_atokotei_subassy?loc=lacquering&date=&key=') }}"
                    class="btn btn-default btn-block tab-display">Incoming Check Subassy</a>
                <a href="{{ url('index/middle/ic_atokotei_subassy_op') }}"
                    class="btn btn-default btn-block tab-display">Incoming Check Subassy by Operator</a>

                <br>
                <span style="font-size: 22px; color: red;"><i class="fa fa-angle-double-down"></i> Display Plating <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/ic_atokotei_subassy?loc=plating&date=&key=') }}"
                    class="btn btn-default btn-block tab-display">Incoming Check Subassy</a>
                <a href="{{ url('index/body/resume_ng/sx') }}"
                    class="btn btn-default btn-block tab-display tab-display-body">Production Result <span
                        class="highlight-black">Body</span></a>
            </div>

            <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3" style="text-align: center;">
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_ng') }}" class="btn btn-default btn-block tab-report">Not Good</a>
                <a href="{{ url('index/middle/report_production_result') }}"
                    class="btn btn-default btn-block tab-report">Total Check</a>
                <a href="{{ url('index/body/report_ng') }}" class="btn btn-default btn-block tab-report-body">Not Good
                    <span class="highlight-black">Body</span></a>
                <a href="{{ url('index/body/prod_result') }}"
                    class="btn btn-default btn-block tab-report-body">Production Result <span
                        class="highlight-black">Body</span></a>

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Buffing <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_buffing_ng?bulan=&fy=') }}"
                    class="btn btn-default btn-block tab-report">Resume <span class="highlight-report">Key</span></a>
                <!-- <a href="" class="btn btn-default btn-block tab-report-body">Resume <span class="highlight-black">Body</span></a> -->
                <a href="{{ url('index/middle/report_buffing_operator_time') }}"
                    class="btn btn-default btn-block tab-report">Operator Time</a>
                <a href="{{ url('index/middle/report_buffing_traing_ng_operator') }}"
                    class="btn btn-default btn-block tab-report">Training NG Operator</a>
                <a href="{{ url('index/middle/report_buffing_traing_eff_operator') }}"
                    class="btn btn-default btn-block tab-report">Training Efficiency Operator</a>
                <a href="{{ url('index/middle/report_buffing_canceled_log') }}"
                    class="btn btn-default btn-block tab-report">Buffing Canceled Log</a>

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Barrel<i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/report_middle', 'slip-fulfillment') }}"
                    class="btn btn-default btn-block tab-report">ID Slip Fulfillment</a>
                <a href="{{ url('index/middle/barrel_log') }}" class="btn btn-default btn-block tab-report">Barrel
                    Log</a>
                <a href="{{ url('index/middle/stock_monitoring') }}" class="btn btn-default btn-block tab-report">Stock
                    Monitoring</a>

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Lacquering <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_lcq_ng?bulan=&fy=') }}"
                    class="btn btn-default btn-block tab-report">Resume <span class="highlight-report">Key</span></a>
                <a href="{{ url('index/middle/report_hourly_lcq') }}" class="btn btn-default btn-block tab-report">Hourly
                    <span class="highlight-report">Key</span> Report</a>
                <!-- <a href="{{ url('index/body/resume/lcq-sx') }}" class="btn btn-default btn-block tab-report-body">Resume <span class="highlight-black">Body</span></a> -->

                <br>
                <span style="font-size: 22px; color: purple;"><i class="fa fa-angle-double-down"></i> Report Plating <i
                        class="fa fa-angle-double-down"></i></span>
                <a href="{{ url('index/middle/report_plt_ng?bulan=&fy=', 'sax') }}"
                    class="btn btn-default btn-block tab-report">Resume <span class="highlight-report">Key</span></a>
                <!-- <a href="index/body/resume/plt-sx" class="btn btn-default btn-block tab-report-body">Resume <span class="highlight-black">Body</span></a> -->

            </div>
        </div>

    </section>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");
        });
    </script>
@endsection
