@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="{{ url('plugins/timepicker/bootstrap-timepicker.min.css') }}">
    <link type='text/css' rel="stylesheet" href="{{ url('css/bootstrap-datetimepicker.min.css') }}">
    <style type="text/css">
        input {
            line-height: 22px;
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

        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #main-body {
            overflow: auto;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}<span class="text-purple"> {{ $title_jp }}</span>

        </h1>
    </section>
@stop
@section('content')
    <input type="hidden" id="green">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <section class="content" style="padding-top: 0px;">
        <div class="row">
            <div id="main-body">
                <div class="temp-widget text-center" id="msg-sept-2022"
                    style="color:black; background-color: #e7e7e7; position: absolute; top: 51%; left: 85%; z-index: 1000; border: 1px dashed black; padding: 0.25%; border-radius: 10px; font-weight: bold;">
                    <div style="padding: 0px 4px;"><i>CLFG & SYNTHETIC REED ONLY</i></div>
                </div>
                <div class="col-xs-12" style="padding-bottom: 10px; padding-left: 0px;">
                    <div class="col-xs-2">
                        <div class="input-group date pull-right" style="text-align: center;">
                            <div class="input-group-addon bg-green">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control monthpicker" name="period" id="period"
                                placeholder="Select Period">
                        </div>
                    </div>

                    <div class="col-xs-2" style="padding: 0px;">
                        <button id="search" onclick="fillTable()" class="btn btn-primary">Search</button>
                    </div>

                    <div class="col-xs-4 pull-right" style="padding-right: 0px;">
                        <div class="col-xs-4 pull-right" style="padding: 0px;">
                            <a href="{{ url('/index/shipping_agency') }}" class="btn btn-info"
                                style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Shipping
                                Line</a>
                        </div>
                        <div class="col-xs-4 pull-right" style="margin-right: 10px; padding: 0px;">
                            <a href="{{ url('/index/shipping_order') }}" class="btn btn-info"
                                style="width: 100%; font-weight: bold; font-size: 1vw;"><i class="fa fa-list"></i> Booking
                                List</a>
                        </div>
                    </div>
                </div>


                <div class="col-xs-3" style="padding-bottom: 0px;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <table id="tableResume" class="table table-bordered"
                                style="width: 100%; font-size: 14px; border: 3px solid black;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 10%; font-size: 18px; font-weight: bold;">SUBJECT<br><span
                                                style="color: purple;">件名</span></th>
                                        <th style="width: 7%; font-size: 18px; font-weight: bold;">PLAN<br><span
                                                style="color: purple;">計画</span></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyResume">
                                    <tr style="height: 60px">
                                        <td id="teus_subject">TEU<br>&nbsp;</td>
                                        <td id="teus_plan" style="font-weight: bold; font-size: 20px;"></td>
                                    </tr>
                                    <tr style="height: 60px">
                                        <td id="or_subject">ORDINARY<br><span style="color: purple;">通常のコンテナ</span></td>
                                        <td id="or_plan" style="font-weight: bold; font-size: 20px;"></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-xs-7" style="padding-bottom: 0px; padding-left: 0px;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <div class="col-xs-8" style="padding: 0px;">
                                <table id="tableResume" class="table table-bordered"
                                    style="width: 100%; font-size: 14px; margin-bottom: 0px;">
                                    <thead style="background-color: rgba(126,86,134,.7);">
                                        <tr>
                                            <th
                                                style="width: 7%; font-size: 18px; font-weight: bold; border-left: 3px solid black;  border-right: 3px solid black; border-top: 3px solid black;">
                                                CONFIRMED<br><span style="color: purple;">確保済み</span></th>
                                            {{-- <th style="width: 6%; background-color: rgba(216, 151, 230, .7);">NOT STUFFING YET<br><span style="color: purple;">未スタッフィング</span></th> --}}
                                            <th style="width: 7%; background-color: rgba(216, 151, 230, .7);">FACTORY
                                                UNDEPARTED<br><span style="color: purple;"> 工場未出発 </span></th>
                                            <th style="width: 7%; background-color: rgba(216, 151, 230, .7);">FACTORY
                                                DEPARTED<br><span style="color: purple;"> 工場出発済み </span></th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyResume">
                                        <tr style="height: 60px">
                                            <td id="teus_confirmed"
                                                style="font-weight: bold; font-size: 20px; border-left: 3px solid black; border-right: 3px solid black;">
                                            </td>
                                            {{-- <td id="teus_not_yet_stuffing" style="font-weight: bold; font-size: 20px;"></td> --}}
                                            <td id="teus_unympi" style="font-weight: bold; font-size: 20px;"></td>
                                            <td id="teus_ympi" style="font-weight: bold; font-size: 20px;"></td>
                                        </tr>
                                        <tr style="height: 60px">
                                            <td id="or_confirmed"
                                                style="font-weight: bold; font-size: 20px; border-left: 3px solid black;  border-right: 3px solid black; border-bottom: 3px solid black;">
                                            </td>
                                            {{-- <td id="or_not_yet_stuffing" style="font-weight: bold; font-size: 20px;"></td> --}}
                                            <td id="or_unympi" style="font-weight: bold; font-size: 20px;"></td>
                                            <td id="or_ympi" style="font-weight: bold; font-size: 20px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="col-xs-4" style="padding: 0px; margin-top: 13px;">
                                <table id="tableResume" class="table table-bordered"
                                    style="width: 100%; font-size: 14px; margin-bottom: 0px;">
                                    <thead style="background-color: rgba(224, 146, 240, .7);">
                                        <tr>
                                            <th style="width: 50%;">HOLD AT PORT<br><span
                                                    style="color: purple;">港留め置き中</span>
                                            </th>
                                            <th style="width: 50%;">PORT DEPARTED<br><span
                                                    style="color: purple;">出港済み</span>
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody id="tableBodyResume">
                                        <tr style="height: 60px">
                                            <td id="teus_port" style="font-weight: bold; font-size: 20px;"></td>
                                            <td id="teus_etd" style="font-weight: bold; font-size: 20px;"></td>
                                        </tr>
                                        <tr style="height: 60px; padding-bottom: 10px;">
                                            <td id="or_port" style="font-weight: bold; font-size: 20px;"></td>
                                            <td id="or_etd" style="font-weight: bold; font-size: 20px;"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-xs-2" style="padding-bottom: 0px; padding-left: 0px;">
                    <div class="box box-solid" style="margin-bottom: 0px;">
                        <div class="box-body">
                            <table id="tableResume" class="table table-bordered"
                                style="width: 100%; font-size: 14px; border: 3px solid black;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 7%; font-size: 18px; font-weight: bold;">NOT CONFIRMED<br><span
                                                style="color: purple;">未確保</span></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyResume">
                                    <tr style="height: 60px">
                                        <td id="teus_not_confirmed" style="font-weight: bold; font-size: 20px;"></td>
                                    </tr>
                                    <tr style="height: 60px">
                                        <td id="or_not_confirmed" style="font-weight: bold; font-size: 20px;"></td>
                                    </tr>

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xs-12" style="padding-top: 0px; padding: 15px;">
                    {{-- <center>
                        <div class="col-xs-12">
                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon"
                                        style="background-color: #605ca8; color: white; height: 75px;"><i
                                            class="glyphicon glyphicon-tasks"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">PLAN <span
                                                style="color: rgba(96, 92, 168);">計画</span></span>
                                        <span class="info-box-number" style="font-size: 1vw;" id="total_plan"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon bg-green" style="height: 75px;"><i
                                            class="fa fa-ship"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">ETD SUB <span style="color: rgba(96, 92, 168);"> 出荷
                                            </span></span>
                                        <span class="info-box-number" style="font-size: 1vw;" id="total_etd"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon"
                                        style="background-color: #455DFF; color: white; height: 75px;"><i
                                            class="fa fa-truck"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">AT PORT <span style="color: rgba(96, 92, 168);"> 発送
                                            </span></span>
                                        <span class="info-box-number" style="font-size: 1vw;" id="total_on_board"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon"
                                        style="background-color: #CCFFFF; color: #212121; height: 75px;"><i
                                            class="glyphicon glyphicon-ok"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">NOT YET STUFFING <span
                                                style="color: rgba(96, 92, 168);">確保済み</span></span>
                                        <span class="info-box-number" style="font-size: 1vw;"
                                            id="total_confirmed"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon bg-red" style="height: 75px;"><i
                                            class="glyphicon glyphicon-remove"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">NOT CONFIRMED <span
                                                style="color: rgba(96, 92, 168);">??</span></span>
                                        <span class="info-box-number" style="font-size: 1vw;"
                                            id="total_not_confirmed"></span>
                                    </div>
                                </div>
                            </div>

                            <div style="width: 16%; display: inline-block; vertical-align: bottom;">
                                <div class="info-box" style="min-height: 75px;">
                                    <span class="info-box-icon bg-red" style="height: 75px;"><i
                                            class="glyphicon glyphicon-remove"></i></span>

                                    <div class="info-box-content">
                                        <span class="info-box-text">NOT CONFIRMED <span
                                                style="color: rgba(96, 92, 168);">??</span></span>
                                        <span class="info-box-number" style="font-size: 1vw;"
                                            id="total_not_confirmed"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </center> --}}

                    <div class="col-xs-12" style="padding: 0px;">
                        <div id="container1" style="height: 525px;"></div>
                    </div>
                </div>

                <div class="col-xs-6">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table id="tableList" class="table table-bordered" style="width: 100%; font-size: 16px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 1%">DESTINATION<br><span style="color: purple;">仕向け地</span></th>
                                        <th style="width: 1%">PLAN<br><span style="color: purple;">計画</span></th>
                                        <th style="width: 1%">ETD SUB<br><span style="color: purple;">出荷</span></th>
                                        <th style="width: 1%">ON BOARD<br><span style="color: purple;">発送</span></th>
                                        <th style="width: 1%">CONFIRMED<br><span style="color: purple;">確保済み</span></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyList">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xs-3" style="padding-left: 0px;">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table id="tableService" class="table table-bordered" style="width: 100%; font-size: 16px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 3%">SERVICE<br><span style="color: purple;">サービス</span></th>
                                        <th style="width: 3%">QTY<br><span style="color: purple;">数量</span></th>
                                        <th style="width: 1%">%<br><br></th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyService">
                                    <tr>
                                        <td style="width: 1%">MAIN</td>
                                        <td style="width: 1%">
                                            <span id="main_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="main_percen">0%</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 1%">SUB</td>
                                        <td style="width: 1%">
                                            <span id="sub_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="sub_percen">0%</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 1%">BACK-UP</td>
                                        <td style="width: 1%">
                                            <span id="backup_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="backup_percen">0%</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 1%">OTHER</td>
                                        <td style="width: 1%">
                                            <span id="other_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="other_percen">0%</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background-color: rgb(252, 248, 227);">
                                    <tr>
                                        <th style="width: 1%">TOTAL</th>
                                        <th style="width: 1%">
                                            <span id="total_service_qty">0</span>
                                        </th>
                                        <th style="width: 1%">
                                            <span id="total_service_percen">100%</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-xs-3" style="padding-left: 0px;">
                    <div class="box box-solid">
                        <div class="box-body">
                            <table id="tableRateService" class="table table-bordered"
                                style="width: 100%; font-size: 16px;">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 1%">
                                            RATE SERVICE<br><span style="color: purple;">レートサービス</span>
                                        </th>
                                        <th style="width: 1%">
                                            QTY<br><span style="color: purple;">数量</span>
                                        </th>
                                        <th style="width: 1%">
                                            %<br><br>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyRateService">
                                    <tr>
                                        <td style="width: 1%">CONTRACTED RATE</td>
                                        <td style="width: 1%">
                                            <span id="contracted_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="contracted_percen">0%</span>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td style="width: 1%">SPOT/EXTRA RATE</td>
                                        <td style="width: 1%">
                                            <span id="spot_qty">0</span>
                                        </td>
                                        <td style="width: 1%">
                                            <span id="spot_percen">0%</span>
                                        </td>
                                    </tr>
                                </tbody>
                                <tfoot style="background-color: rgb(252, 248, 227);">
                                    <tr>
                                        <th style="width: 1%">TOTAL</th>
                                        <th style="width: 1%">
                                            <span id="total_rate_qty"></span>
                                        </th>
                                        <th style="width: 1%">
                                            <span id="total_rate_percen">100%</span>
                                        </th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>



            </div>
        </div>

        </div>
    </section>

    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog modal-lg" style="width: 90%;">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <span id="title_modal" style="font-weight: bold; font-size: 1.5vw;"></span>
                    </center>
                    <hr>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <div class="col-xs-8 col-xs-offset-2" style="padding-bottom: 5px;">
                            <table class="table table-hover table-bordered table-striped" id="tableDetail">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 2%; vertical-align: middle;" colspan="9">RESUME</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 2%; vertical-align: middle;">YCJ Ref No.</th>
                                        <th style="width: 1%; vertical-align: middle;">Shipper</th>
                                        <th style="width: 2%; vertical-align: middle;">Port Loading</th>
                                        <th style="width: 4%; vertical-align: middle;">Port of Delivery</th>
                                        <th style="width: 4%; vertical-align: middle;">Country</th>
                                        <th style="width: 4%; vertical-align: middle;">Plan (TEU)</th>
                                        <th style="width: 4%; vertical-align: middle;">Plan (Ordinary)</th>
                                        <th style="width: 25%; vertical-align: middle;">Invoice</th>
                                        <th style="width: 4%; vertical-align: middle;">ATD</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDetailBody">
                                </tbody>
                            </table>
                        </div>
                        <div class="col-xs-12" style="padding-bottom: 5px;">
                            <table class="table table-hover table-bordered table-striped" id="tableDetailRef">
                                <thead style="background-color: rgba(126,86,134,.7);">
                                    <tr>
                                        <th style="width: 2%; vertical-align: middle;" colspan="15">BOOKING DETAILS</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">YCJ Ref No.</th>
                                        <th style="width: 1%; vertical-align: middle;" rowspan="2">Shipper</th>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">Port Loading</th>
                                        <th style="width: 4%; vertical-align: middle;" rowspan="2">Port of Delivery
                                        </th>
                                        <th style="width: 4%; vertical-align: middle;" rowspan="2">Country</th>
                                        <th style="width: 4%; vertical-align: middle;" rowspan="2">Plan<br>(TEU)</th>
                                        <th style="width: 4%; vertical-align: middle;" rowspan="2">Plan<br>(Ordinary)
                                        </th>
                                        <th style="width: 2%; vertical-align: middle;" colspan="3">Container Size</th>
                                        <th style="width: 4%; vertical-align: middle;" rowspan="2">Booking No. or B/L
                                            No.</th>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">Carier</th>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">Nomination</th>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">Application Rate
                                        </th>
                                        <th style="width: 2%; vertical-align: middle;" rowspan="2">Status</th>
                                    </tr>
                                    <tr>
                                        <th style="width: 1%;">40HC</th>
                                        <th style="width: 1%;">40'</th>
                                        <th style="width: 1%;">20'</th>
                                    </tr>
                                </thead>
                                <tbody id="tableDetailRefBody">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
@section('scripts')
    <script src="{{ url('js/moment.min.js') }}"></script>
    <script src="{{ url('js/bootstrap-datetimepicker.min.js') }}"></script>
    <script src="{{ url('plugins/timepicker/bootstrap-timepicker.min.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('body').toggleClass("sidebar-collapse");

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });
            fillTable();
            setInterval(fillTable, 10 * 60 * 1000);


        });

        function clearConfirmation() {
            location.reload(true);
        }

        function fillTable() {

            var period = $('#period').val();

            var data = {
                period: period,
            }

            $.get('{{ url('fetch/resume_shipping_order') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableBodyList').html("");

                    var tableData = "";



                    for (var i = 0; i < result.data.length; i++) {

                        tableData += '<tr>';

                        tableData += '<td>' + result.data[i].port_of_delivery + '</td>';
                        tableData += '<td>' + result.data[i].plan + '</td>';

                        if (result.data[i].departed == result.data[i].plan) {
                            tableData += '<td style="background-color: rgb(204, 255, 255);">' + result.data[i]
                                .departed + '</td>';
                        } else {
                            tableData += '<td style="background-color: rgb(255, 204, 255);">' + result.data[i]
                                .departed + '</td>';
                        }


                        tableData += '<td>' + result.data[i].on_board + '</td>';

                        tableData += '<td>' + result.data[i].confirmed + '</td>';

                        tableData += '</tr>';
                    }

                    $('#tableBodyList').append(tableData);


                    var total_or_plan = 0;
                    var total_or_etd_sub = 0;
                    var total_or_at_port = 0;
                    var total_or_not_yet_stuffing = 0;
                    var total_or_confirmed = 0;
                    var total_or_not_confirmed = 0;

                    var total_teus_plan = 0;
                    var total_teus_etd_sub = 0;
                    var total_teus_at_port = 0;
                    var total_teus_not_yet_stuffing = 0;
                    var total_teus_confirmed = 0;
                    var total_teus_not_confirmed = 0;


                    var date = [];
                    var plan = [];
                    var on_board = [];
                    var stuffing = [];
                    var confirmed = [];
                    var not_confirmed = [];

                    var departed = [];
                    var ship_confirmed = [];


                    var monthNames = ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov",
                        "Dec"
                    ];


                    for (var i = 0; i < result.ship_by_dates.length; i++) {
                        var d = new Date(result.ship_by_dates[i].week_date)
                        date.push(d.getDate() + '-' + monthNames[d.getMonth()]);

                        plan.push(parseInt(result.ship_by_dates[i].plan));

                        confirmed.push(parseInt(result.ship_by_dates[i].confirmed) + parseInt(result.ship_by_dates[
                            i].stuffing));
                        on_board.push(parseInt(result.ship_by_dates[i].on_board) + parseInt(result.ship_by_dates[i]
                            .departed))
                        // stuffing.push(parseInt(result.ship_by_dates[i].stuffing));

                        var not_conf = parseInt(result.ship_by_dates[i].plan) - parseInt(result.ship_by_dates[i]
                            .confirmed) - parseInt(result.ship_by_dates[i].stuffing) - parseInt(result
                            .ship_by_dates[i].on_board) - parseInt(result.ship_by_dates[i].departed);
                        not_confirmed.push(parseInt(not_conf));


                        var ship_conf = parseInt(result.ship_by_dates[i].confirmed) + parseInt(result.ship_by_dates[
                            i].stuffing) + on_board[i] - parseInt(result.ship_by_dates[i].departed);

                        ship_confirmed.push(parseInt(ship_conf));
                        departed.push(parseInt(result.ship_by_dates[i].departed));


                        total_or_plan += parseInt(result.ship_by_dates[i].plan);
                        total_or_etd_sub += parseInt(result.ship_by_dates[i].departed);
                        total_or_at_port += parseInt(result.ship_by_dates[i].on_board);
                        total_or_confirmed += parseInt(result.ship_by_dates[i].confirmed) + parseInt(result
                            .ship_by_dates[i].stuffing) + parseInt(result.ship_by_dates[i].on_board) + parseInt(
                            result.ship_by_dates[i].departed);
                        total_or_not_confirmed += parseInt(not_conf);
                        total_or_not_yet_stuffing += parseInt(result.ship_by_dates[i].confirmed);



                        var teus_not_conf = parseInt(result.teus[i].plan_teus) - parseInt(result.teus[i]
                                .confirmed) - parseInt(result.teus[i].stuffing) - parseInt(result.teus[i]
                                .on_board) -
                            parseInt(result.teus[i].departed);


                        total_teus_plan += parseInt(result.teus[i].plan_teus);
                        total_teus_etd_sub += parseInt(result.teus[i].departed);
                        total_teus_at_port += parseInt(result.teus[i].on_board);
                        total_teus_confirmed += parseInt(result.teus[i].confirmed) + parseInt(result.teus[i]
                            .stuffing) + parseInt(result.teus[i].on_board) + parseInt(result.teus[i].departed);
                        total_teus_not_confirmed += parseInt(teus_not_conf);
                        total_teus_not_yet_stuffing += parseInt(result.teus[i].confirmed);

                    }

                    var css = 'style="font-weight: normal; font-style: italic;"';


                    var persen_confirm = Math.round(total_or_confirmed / total_or_plan * 100);
                    var persen_not_confirm = 100 - persen_confirm;

                    var persen_not_yet_stuffing = Math.round(total_or_not_yet_stuffing / total_or_plan * 100);
                    var persen_ympi = Math.round((total_or_at_port + total_or_etd_sub) / total_or_plan * 100);
                    var persen_port = Math.round(total_or_at_port / total_or_plan * 100);
                    var persen_etd = Math.round(total_or_etd_sub / total_or_plan * 100);





                    // var persen_teus_confirm = Math.ceil(total_teus_confirmed/total_teus_plan*100);
                    // var persen_teus_not_confirm = 100-persen_teus_confirm;

                    // var persen_teus_port = Math.round(total_teus_at_port/total_teus_confirmed*100);
                    // var persen_teus_etd = Math.round(total_teus_etd_sub/total_teus_confirmed*100);
                    // var persen_teus_not_yet = Math.round(total_teus_etd_sub/total_teus_confirmed*100) 100-persen_teus_port-persen_teus_etd;


                    // var fix_port = Math.round((total_teus_at_port/total_teus_confirmed) * 100);
                    // var fix_etd_sub = parseInt(persen_teus_port + persen_teus_etd) - fix_port;



                    $('#or_plan').html(total_or_plan);
                    $('#or_confirmed').html(total_or_confirmed + ' <small ' + css + '>(' + persen_confirm +
                        '%)</small>');
                    $('#or_not_yet_stuffing').html(total_or_not_yet_stuffing + ' <small ' + css + '>(' +
                        persen_not_yet_stuffing + '%)</small>');
                    $('#or_ympi').html(parseInt(total_or_at_port + total_or_etd_sub) + ' <small ' + css + '>(' +
                        persen_ympi + '%)</small>');
                    $('#or_unympi').html(parseInt(total_or_plan - (total_or_at_port + total_or_etd_sub)) +
                        ' <small ' + css + '>(' + (100 - persen_ympi) + '%)</small>');
                    $('#or_port').html(total_or_at_port + ' <small ' + css + '>(' + persen_port + '%)</small>');
                    $('#or_etd').html(total_or_etd_sub + ' <small ' + css + '>(' + persen_etd + '%)</small>');
                    $('#or_not_confirmed').html(total_or_not_confirmed + ' <small ' + css + '>(' +
                        persen_not_confirm + '%)</small>');


                    $('#teus_plan').html(total_teus_plan);
                    $('#teus_confirmed').html(total_teus_confirmed + ' <small ' + css + '>(' + persen_confirm +
                        '%)</small>');
                    $('#teus_not_yet_stuffing').html(total_teus_not_yet_stuffing + ' <small ' + css + '>(' +
                        persen_not_yet_stuffing + '%)</small>');
                    $('#teus_ympi').html(parseInt(total_teus_at_port + total_teus_etd_sub) + ' <small ' + css +
                        '>(' + persen_ympi + '%)</small>');
                    $('#teus_unympi').html(parseInt(total_teus_plan - (total_teus_at_port + total_teus_etd_sub)) +
                        ' <small ' + css + '>(' + (100 - persen_ympi) + '%)</small>');
                    $('#teus_port').html(total_teus_at_port + ' <small ' + css + '>(' + persen_port + '%)</small>');
                    $('#teus_etd').html(total_teus_etd_sub + ' <small ' + css + '>(' + persen_etd + '%)</small>');
                    $('#teus_not_confirmed').html(total_teus_not_confirmed + ' <small ' + css + '>(' +
                        persen_not_confirm + '%)</small>');

                    if (result.month == 'September 2022') {
                        $('#msg-sept-2022').css({
                            "display": "block"
                        });

                    } else {
                        $('#msg-sept-2022').css({
                            "display": "none"
                        });
                    }




                    for (let i = 0; i < result.application_rate.length; i++) {
                        if (result.application_rate[i].application_rate == 'CONTRACTED RATE') {
                            $('#contracted_qty').html(result.application_rate[i].qty);
                            var percen = result.application_rate[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#contracted_percen').html(percen + '%');

                        } else if (result.application_rate[i].application_rate == 'SPOT/EXTRA RATE') {
                            $('#spot_qty').html(result.application_rate[i].qty);
                            var percen = result.application_rate[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#spot_percen').html(percen + '%');
                        }

                    }
                    $('#total_rate_qty').html(total_or_confirmed);


                    for (let i = 0; i < result.nomination.length; i++) {
                        if (result.nomination[i].nomination == 'MAIN') {
                            $('#main_qty').html(result.nomination[i].qty);
                            var percen = result.nomination[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#main_percen').html(percen + '%');

                        } else if (result.nomination[i].nomination == 'SUB') {
                            $('#sub_qty').html(result.nomination[i].qty);
                            var percen = result.nomination[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#sub_percen').html(percen + '%');

                        } else if (result.nomination[i].nomination == 'BACK UP') {
                            $('#backup_qty').html(result.nomination[i].qty);
                            var percen = result.nomination[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#backup_percen').html(percen + '%');

                        } else if (result.nomination[i].nomination == 'OTHER') {
                            $('#other_qty').html(result.nomination[i].qty);
                            var percen = result.nomination[i].qty / total_or_confirmed * 100;
                            if (percen != 100) {
                                percen = percen.toFixed(1);
                            }
                            $('#other_percen').html(percen + '%');

                        }

                    }
                    $('#total_service_qty').html(total_or_confirmed);





                    Highcharts.chart('container1', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: 'Shipping Booking Management List (' + result.month +
                                ')<br><span style="color: rgba(96, 92, 168);">船便予約管理リスト 「' + result.year +
                                '年 ' + result.mon + '月」</span>'
                        },
                        xAxis: {
                            categories: date
                        },
                        yAxis: {
                            enabled: true,
                            title: {
                                enabled: true,
                                text: "Quantity Container<br>(コンテナ台数)"
                            },
                            tickInterval: 1
                        },
                        exporting: {
                            enabled: false
                        },
                        tooltip: {
                            headerFormat: '<b>{point.x}</b><br/>',
                            pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                        },
                        credits: {
                            enabled: false
                        },
                        annotations: [{
                            labels: [{
                                point: {
                                    x: 25,
                                    y: 2
                                },
                                text: 'Col de la Joux'
                            }]
                        }],
                        legend: {
                            enabled: false
                        },
                        legend: {
                            layout: 'vertical',
                            align: 'right',
                            verticalAlign: 'top',
                            x: 1,
                            y: 0,
                            floating: true,
                            borderWidth: 1,
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
                            shadow: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    style: {
                                        textOutline: false
                                    },
                                    formatter: function() {
                                        if (this.y != 0) {
                                            return this.y;
                                        } else {
                                            return null;
                                        }
                                    }
                                },
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function(event) {
                                            showDetail(event.point.category);
                                        }
                                    }
                                },
                            },
                        },
                        series: [{
                                name: 'Ship Confirmed (船確保済み)',
                                data: ship_confirmed,
                                stack: 'shipment',
                                color: '#d897e6'
                            }, {
                                name: 'Port Departed (出港済み)',
                                data: departed,
                                stack: 'shipment',
                                color: '#00a65a'
                            }, {
                                name: 'Factory Departed (工場出発済み)',
                                data: on_board,
                                stack: 'container',
                                color: '#455DFF'
                            },
                            // {
                            // 	name: 'Stuffing (荷積み)',
                            // 	data: stuffing,
                            // 	stack: 'container',
                            // 	color: '#FFFF54'
                            // },
                            {
                                name: 'Container Confirmed (コンテナ確保済み)',
                                data: confirmed,
                                stack: 'container',
                                color: '#a8daf7'
                            }, {
                                name: 'Container Not Confirmed (コンテナ未確保)',
                                data: not_confirmed,
                                stack: 'container',
                                color: '#d50000'
                            }
                        ]
                    });
                }
            });
        }

        function showDetail(category) {
            var period = $('#period').val();
            var date = category;

            var data = {
                period: period,
                date: date
            }

            $.get('{{ url('fetch/resume_shipping_order_detail') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#tableDetailBody').html("");
                    $('#tableDetailRefBody').html("");

                    $('#title_modal').text('Shipping Booking Management Booking Details on ' + result.st_date);

                    var detail = '';
                    var concat = '';
                    $.each(result.detail, function(key, value) {
                        var color = '';
                        var check = 'BOOKING CONFIRMED';
                        var status = value.status;

                        if (status.includes(check)) {
                            concat += value.ycj_ref_number;
                            color = 'style="background-color: rgb(204, 255, 255);"';
                        }

                        detail += '<tr>';
                        detail += '<td ' + color + '>' + value.ycj_ref_number + '</td>';
                        detail += '<td ' + color + '>' + value.shipper + '</td>';
                        detail += '<td ' + color + '>' + value.port_loading + '</td>';
                        detail += '<td ' + color + '>' + value.port_of_delivery + '</td>';
                        detail += '<td ' + color + '>' + value.country + '</td>';
                        detail += '<td ' + color + '>' + value.plan_teus + '</td>';
                        detail += '<td ' + color + '>' + value.plan + '</td>';
                        detail += '<td ' + color + '>' + (value.fortyhc || '') + '</td>';
                        detail += '<td ' + color + '>' + (value.forty || '') + '</td>';
                        detail += '<td ' + color + '>' + (value.twenty || '') + '</td>';
                        detail += '<td ' + color + '>' + (value.booking_number || '') + '</td>';
                        detail += '<td ' + color + '>' + value.carier + '</td>';
                        detail += '<td ' + color + '>' + value.nomination + '</td>';
                        detail += '<td ' + color + '>' + value.application_rate + '</td>';
                        detail += '<td ' + color + '>' + value.status + '</td>';
                        detail += '</tr>';
                    });
                    $('#tableDetailRefBody').append(detail);


                    var detail = '';
                    $.each(result.resume, function(key, value) {
                        var color = '';
                        if (concat.includes(value.ycj_ref_number)) {
                            color = 'style="background-color: rgb(204, 255, 255);"';
                        } else {
                            color = 'style="background-color: rgb(255, 204, 255);"';
                        }

                        detail += '<tr>';
                        detail += '<td ' + color + '>' + value.ycj_ref_number + '</td>';
                        detail += '<td ' + color + '>' + value.shipper + '</td>';
                        detail += '<td ' + color + '>' + value.port_loading + '</td>';
                        detail += '<td ' + color + '>' + value.port_of_delivery + '</td>';
                        detail += '<td ' + color + '>' + value.country + '</td>';
                        detail += '<td ' + color + '>' + value.plan_teus + '</td>';
                        detail += '<td ' + color + '>' + value.plan + '</td>';
                        detail += '<td ' + color + '>' + (value.invoice || '-') + '</td>';
                        detail += '<td ' + color + '>' + (value.actual_departed || '-') + '</td>';
                        detail += '</tr>';
                    });
                    $('#tableDetailBody').append(detail);


                    $('#modalDetail').modal('show');
                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function openSuccessGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-success',
                image: '{{ url('images/image-screen.png') }}',
                sticky: false,
                time: '3000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '3000'
            });
        }
    </script>
@endsection
