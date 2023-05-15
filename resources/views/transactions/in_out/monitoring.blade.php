@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table {
            border: 1px solid black !important;
        }

        thead>tr>th {
            vertical-align: middle !important;
            border: 1px solid black !important;
        }

        tbody>tr>td {
            border: 1px solid black !important;
            padding: 3px 3px 3px 3px !important;
        }

        tfoot>tr>th {
            border: 1px solid black !important;
            padding: 3px 3px 3px 3px !important;
        }

        #tableKanbanResumeDetail>tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #tableReturnResumeDetail>tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #tableRepairResumeDetail>tbody>tr:hover {
            cursor: pointer;
            background-color: #7dfa8c !important;
        }

        #loading {
            display: none;
        }
    </style>
@stop
@section('header')
    <section class="content-header">
    </section>
@endsection
@section('content')
    <section class="content" style="padding-top: 0;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>
        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 10px;">
                <div class="row">
                    <div class="col-xs-2" style="margin-top: 0; margin-bottom: 0;">
                        <div class="input-group date">
                            <div class="input-group-addon">
                                <i class="fa fa-calendar"></i>
                            </div>
                            <input type="text" class="form-control pull-right" id="filterDate" onchange="fetchData()">
                        </div>
                    </div>
                    <div id="chart_title" class="col-xs-10" style="background-color: rgb(96, 92, 168);">
                        <center>
                            <span style="color: white; font-size: 2vw; font-weight: bold;" id="title_text"></span>
                        </center>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" style="padding-bottom: 5px;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <center>
                            <span style="font-weight: bold; font-size: 1.2vw;">KANBAN</span>
                        </center>
                        <div id="container_kanban" style="height: 200px;"></div>
                        <span style="font-weight: bold;">Resume Table</span>
                        <table id="tableKanbanResume" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: rgb(96, 92, 168); color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 1%; text-align: right;">Total Out</th>
                                    <th style="width: 1%; text-align: right;">Total Transaksi</th>
                                    <th style="width: 1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableKanbanResumeBody">
                            </tbody>
                        </table>
                        <span style="font-weight: bold;">Resume Table Detail</span>
                        <table id="tableKanbanResumeDetail" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: rgb(96, 92, 168); color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 0.1%; text-align: center;">Material</th>
                                    <th style="width: 3%; text-align: left;">Deskripsi</th>
                                    <th style="width: 0.1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableKanbanResumeDetailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" style="padding-bottom: 5px;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <center>
                            <span style="font-weight: bold; font-size: 1.2vw;">RETURN</span>
                        </center>
                        <div id="container_return" style="height: 200px;"></div>
                        <span style="font-weight: bold;">Resume Table</span>
                        <table id="tableReturnResume" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #00a65a; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 1%; text-align: right;">Total Out</th>
                                    <th style="width: 1%; text-align: right;">Total Transaksi</th>
                                    <th style="width: 1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableReturnResumeBody">
                            </tbody>
                        </table>
                        <span style="font-weight: bold;">Resume Table Detail</span>
                        <table id="tableReturnResumeDetail" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #00a65a; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 0.1%; text-align: center;">Material</th>
                                    <th style="width: 3%; text-align: left;">Deskripsi</th>
                                    <th style="width: 0.1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableReturnResumeDetailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-xs-4" style="padding-bottom: 5px;">
                <div class="box box-solid" style="border: 1px solid grey;">
                    <div class="box-body">
                        <center>
                            <span style="font-weight: bold; font-size: 1.2vw;">REPAIR</span>
                        </center>
                        <div id="container_repair" style="height: 200px;"></div>
                        <span style="font-weight: bold;">Resume Table</span>
                        <table id="tableRepairResume" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #f39c12; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 1%; text-align: right;">Total Out</th>
                                    <th style="width: 1%; text-align: right;">Total Transaksi</th>
                                    <th style="width: 1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableRepairResumeBody">
                            </tbody>
                        </table>
                        <span style="font-weight: bold;">Resume Table Detail</span>
                        <table id="tableRepairResumeDetail" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #f39c12; color: white;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">Lokasi</th>
                                    <th style="width: 0.1%; text-align: center;">Material</th>
                                    <th style="width: 3%; text-align: left;">Deskripsi</th>
                                    <th style="width: 0.1%; text-align: right;">Total Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableRepairResumeDetailBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <center>
                        <h3 id="modalDetailTitle">Title</h3>
                    </center>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <span style="font-weight: bold;">Resume</span>
                        <table id="tableDetail" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #7dfa8c;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 0.5%; text-align: center;">Tag</th>
                                    <th style="width: 0.5%; text-align: center;">Material</th>
                                    <th style="width: 4%; text-align: left;">Deskripsi</th>
                                    <th style="width: 0.8%; text-align: right;">In - Out</th>
                                    <th style="width: 0.8%; text-align: right;">Transaksi</th>
                                    <th style="width: 0.8%; text-align: right;">Diff</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailBody">
                            </tbody>
                        </table>
                        <span style="font-weight: bold;">Detail In-Out</span>
                        <table id="tableDetailInout" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #7dfa8c;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 0.5%; text-align: center;">Tag</th>
                                    <th style="width: 0.5%; text-align: center;">Material</th>
                                    <th style="width: 4%; text-align: left;">Deskripsi</th>
                                    <th style="width: 1%; text-align: center;">Lokasi</th>
                                    <th style="width: 0.8%; text-align: center;">Quantity</th>
                                    <th style="width: 0.8%; text-align: left;">PIC</th>
                                    <th style="width: 0.8%; text-align: center;">Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailInoutBody">
                            </tbody>
                        </table>
                        <span style="font-weight: bold;">Detail Transaksi</span>

                        <table id="tableDetailTransaction" class="table table-bordered table-striped table-hover">
                            <thead style="background-color: #7dfa8c;">
                                <tr>
                                    <th style="width: 0.1%; text-align: center;">#</th>
                                    <th style="width: 0.5%; text-align: center;">Tag</th>
                                    <th style="width: 0.5%; text-align: center;">Material</th>
                                    <th style="width: 4%; text-align: left;">Deskripsi</th>
                                    <th style="width: 1%; text-align: center;">Lokasi</th>
                                    <th style="width: 0.8%; text-align: center;">Quantity</th>
                                    <th style="width: 0.8%; text-align: left;">PIC</th>
                                    <th style="width: 0.8%; text-align: center;">Waktu</th>
                                </tr>
                            </thead>
                            <tbody id="tableDetailTransactionBody">
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('#filterDate').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            fetchData();
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var data_1 = [];
        var datas_2 = [];
        var datas = [];
        var resumes = [];

        function fetchDetail(category, location, material_number) {
            var cnt_1 = 0;
            var total_inout_1 = 0;
            var total_transaction_1 = 0;
            var total_diff_1 = 0;
            var tags = [];

            var tableDetailBody = "";
            $('#tableDetailBody').html("");

            $.each(datas_2, function(key, value) {
                if (value.category == category && value.location == location && value.material_number ==
                    material_number && value.quantity_diff != 0) {
                    cnt_1 += 1;
                    tableDetailBody += '<tr>';
                    tableDetailBody += '<td style="width: 0.1%; text-align: center;">' + cnt_1 + '</td>';
                    tableDetailBody += '<td style="width: 0.5%; text-align: center;">' + value.tag + '</td>';
                    tableDetailBody += '<td style="width: 0.5%; text-align: center;">' + value.material_number +
                        '</td>';
                    tableDetailBody += '<td style="width: 4%; text-align: left;">' + value
                        .material_description + '</td>';
                    tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + value.quantity_inout +
                        '</td>';
                    tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + value
                        .quantity_transaction + '</td>';
                    tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + (value.quantity_inout -
                        value.quantity_transaction) + '</td>';
                    tableDetailBody += '</tr>';

                    total_inout_1 += value.quantity_inout;
                    total_transaction_1 += value.quantity_transaction;
                    total_diff_1 += value.quantity_inout - value.quantity_transaction;

                    tags.push(value.tag);
                }
            });

            tableDetailBody += '<tr style="background-color: #fcf8e3;">';
            tableDetailBody += '<td style="width: 0.1%; text-align: center; font-weight: bold;" colspan="4">Total</td>';
            tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + total_inout_1 +
                '</td>';
            tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + total_transaction_1 + '</td>';
            tableDetailBody += '<td style="width: 0.8%; text-align: right;">' + total_diff_1 +
                '</td>';
            tableDetailBody += '</tr>';

            $('#tableDetailBody').append(tableDetailBody);

            var cnt_2 = 0;
            var tableDetailInoutBody = "";
            $('#tableDetailInoutBody').html("");
            var cnt_3 = 0;
            var tableDetailTransactionBody = "";
            $('#tableDetailTransactionBody').html("");

            $.each(data_1, function(key, value) {
                if (jQuery.inArray(value.tag, tags) != -1 && value.category == category && value.location ==
                    location) {
                    if (value.quantity_inout != 0) {
                        cnt_2 += 1;
                        tableDetailInoutBody += '<tr>';
                        tableDetailInoutBody += '<td style="width: 0.1%; text-align: center;">' + cnt_2 + '</td>';
                        tableDetailInoutBody += '<td style="width: 0.5%; text-align: center;">' + value.tag +
                            '</td>';
                        tableDetailInoutBody += '<td style="width: 0.5%; text-align: center;">' + value
                            .material_number + '</td>';
                        tableDetailInoutBody += '<td style="width: 4%; text-align: left;">' + value
                            .material_description + '</td>';
                        tableDetailInoutBody += '<td style="width: 1%; text-align: center;">' + value
                            .issue_location + ' - ' + value.receive_location + '</td>';
                        tableDetailInoutBody += '<td style="width: 0.8%; text-align: center;">' + value
                            .quantity_inout + '</td>';
                        tableDetailInoutBody += '<td style="width: 0.8%; text-align: left;">' + value
                            .created_by + '<br>' + callName(value.created_by_name) + '</td>';
                        tableDetailInoutBody += '<td style="width: 0.8%; text-align: center;">' + value
                            .created_at + '</td>';
                        tableDetailInoutBody += '</tr>';
                    }
                    if (value.quantity_transaction != 0) {
                        cnt_3 += 1;
                        tableDetailTransactionBody += '<tr>';
                        tableDetailTransactionBody += '<td style="width: 0.1%; text-align: center;">' + cnt_3 +
                            '</td>';
                        tableDetailTransactionBody += '<td style="width: 0.5%; text-align: center;">' + value.tag +
                            '</td>';
                        tableDetailTransactionBody += '<td style="width: 0.5%; text-align: center;">' + value
                            .material_number + '</td>';
                        tableDetailTransactionBody += '<td style="width: 4%; text-align: left;">' + value
                            .material_description + '</td>';
                        tableDetailTransactionBody += '<td style="width: 1%; text-align: center;">' + value
                            .issue_location + ' - ' + value.receive_location + '</td>';
                        tableDetailTransactionBody += '<td style="width: 0.8%; text-align: center;">' + value
                            .quantity_transaction + '</td>';
                        tableDetailTransactionBody += '<td style="width: 0.8%; text-align: left;">' + value
                            .created_by + '<br>' + callName(value.created_by_name) + '</td>';
                        tableDetailTransactionBody += '<td style="width: 0.8%; text-align: center;">' + value
                            .created_at + '</td>';
                        tableDetailTransactionBody += '</tr>';
                    }
                }
            });

            $('#tableDetailInoutBody').append(tableDetailInoutBody);
            $('#tableDetailTransactionBody').append(tableDetailTransactionBody);

            $('#modalDetailTitle').text(category + " - " + location);
            $('#modalDetail').modal('show');
        }

        function fetchData() {
            // $('#loading').show();
            date = $('#filterDate').val();
            var data = {
                date: date,
            }
            $.get('{{ url('fetch/in_out_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#title_text').text('PERIODE ' + result.periode.toUpperCase());
                    var h = $('#chart_title').height();
                    $('#filterDate').css('height', h);

                    data_1 = result.data_1;
                    datas_2 = result.datas_2;
                    datas = result.datas;
                    resumes = result.resumes;

                    var total_kanban_inout_bpp = 0;
                    var total_kanban_transaction_bpp = 0;
                    var total_kanban_diff_bpp = 0;
                    var total_kanban_inout_wld = 0;
                    var total_kanban_transaction_wld = 0;
                    var total_kanban_diff_wld = 0;
                    var total_kanban_inout_bff = 0;
                    var total_kanban_transaction_bff = 0;
                    var total_kanban_diff_bff = 0;
                    // var total_kanban_inout_plt = 0;
                    // var total_kanban_transaction_plt = 0;
                    // var total_kanban_diff_plt = 0;

                    var total_kanban_inout = 0;
                    var total_kanban_transaction = 0;
                    var total_kanban_diff = 0;

                    var total_return_inout_bpp = 0;
                    var total_return_transaction_bpp = 0;
                    var total_return_diff_bpp = 0;
                    var total_return_inout_wld = 0;
                    var total_return_transaction_wld = 0;
                    var total_return_diff_wld = 0;
                    var total_return_inout_bff = 0;
                    var total_return_transaction_bff = 0;
                    var total_return_diff_bff = 0;
                    var total_return_inout_lcq = 0;
                    var total_return_transaction_lcq = 0;
                    var total_return_diff_lcq = 0;
                    var total_return_inout_plt = 0;
                    var total_return_transaction_plt = 0;
                    var total_return_diff_plt = 0;

                    var total_return_inout = 0;
                    var total_return_transaction = 0;
                    var total_return_diff = 0;

                    var total_repair_inout_bpp = 0;
                    var total_repair_transaction_bpp = 0;
                    var total_repair_diff_bpp = 0;
                    var total_repair_inout_wld = 0;
                    var total_repair_transaction_wld = 0;
                    var total_repair_diff_wld = 0;
                    var total_repair_inout_bff = 0;
                    var total_repair_transaction_bff = 0;
                    var total_repair_diff_bff = 0;
                    var total_repair_inout_lcq = 0;
                    var total_repair_transaction_lcq = 0;
                    var total_repair_diff_lcq = 0;
                    var total_repair_inout_plt = 0;
                    var total_repair_transaction_plt = 0;
                    var total_repair_diff_plt = 0;

                    var total_repair_inout = 0;
                    var total_repair_transaction = 0;
                    var total_repair_diff = 0;

                    var tableKanbanResumeBody = "";
                    $('#tableKanbanResumeBody').html("");

                    var tableReturnResumeBody = "";
                    $('#tableReturnResumeBody').html("");

                    var tableRepairResumeBody = "";
                    $('#tableRepairResumeBody').html("");

                    $.each(resumes, function(key, value) {
                        if (value.location == 'BPP' && value.category == 'KANBAN') {
                            total_kanban_inout_bpp += value.quantity_inout;
                            total_kanban_transaction_bpp += value.quantity_transaction;
                            total_kanban_diff_bpp += value.quantity_diff;
                        }
                        if (value.location == 'WLD' && value.category == 'KANBAN') {
                            total_kanban_inout_wld += value.quantity_inout;
                            total_kanban_transaction_wld += value.quantity_transaction;
                            total_kanban_diff_wld += value.quantity_diff;
                        }
                        if (value.location == 'BFF' && value.category == 'KANBAN') {
                            total_kanban_inout_bff += value.quantity_inout;
                            total_kanban_transaction_bff += value.quantity_transaction;
                            total_kanban_diff_bff += value.quantity_diff;
                        }
                        // if (value.location == 'PLT' && value.category == 'KANBAN') {
                        //     total_kanban_inout_plt += value.quantity_inout;
                        //     total_kanban_transaction_plt += value.quantity_transaction;
                        //     total_kanban_diff_plt += value.quantity_diff;
                        // }
                        if (value.category == 'KANBAN' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF'
                            ]) != -1) {
                            tableKanbanResumeBody += '<tr>';
                            tableKanbanResumeBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .location +
                                '</td>';
                            tableKanbanResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_inout + '</td>';
                            tableKanbanResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_transaction + '</td>';
                            tableKanbanResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_diff + '</td>';
                            tableKanbanResumeBody += '</tr>';

                            total_kanban_inout += value.quantity_inout;
                            total_kanban_transaction += value.quantity_transaction;
                            total_kanban_diff += value.quantity_diff;
                        }

                        if (value.location == 'BPP' && value.category == 'RETURN') {
                            total_return_inout_bpp += value.quantity_inout;
                            total_return_transaction_bpp += value.quantity_transaction;
                            total_return_diff_bpp += value.quantity_diff;
                        }
                        if (value.location == 'WLD' && value.category == 'RETURN') {
                            total_return_inout_wld += value.quantity_inout;
                            total_return_transaction_wld += value.quantity_transaction;
                            total_return_diff_wld += value.quantity_diff;
                        }
                        if (value.location == 'BFF' && value.category == 'RETURN') {
                            total_return_inout_bff += value.quantity_inout;
                            total_return_transaction_bff += value.quantity_transaction;
                            total_return_diff_bff += value.quantity_diff;
                        }
                        if (value.location == 'LCQ' && value.category == 'RETURN') {
                            total_return_inout_lcq += value.quantity_inout;
                            total_return_transaction_lcq += value.quantity_transaction;
                            total_return_diff_lcq += value.quantity_diff;
                        }
                        if (value.location == 'PLT' && value.category == 'RETURN') {
                            total_return_inout_plt += value.quantity_inout;
                            total_return_transaction_plt += value.quantity_transaction;
                            total_return_diff_plt += value.quantity_diff;
                        }
                        if (value.category == 'RETURN' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF', 'PLT', 'LCQ'
                            ]) != -1) {
                            tableReturnResumeBody += '<tr>';
                            tableReturnResumeBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .location +
                                '</td>';
                            tableReturnResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_inout + '</td>';
                            tableReturnResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_transaction + '</td>';
                            tableReturnResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_diff + '</td>';
                            tableReturnResumeBody += '</tr>';

                            total_return_inout += value.quantity_inout;
                            total_return_transaction += value.quantity_transaction;
                            total_return_diff += value.quantity_diff;
                        }

                        if (value.location == 'BPP' && value.category == 'REPAIR') {
                            total_repair_inout_bpp += value.quantity_inout;
                            total_repair_transaction_bpp += value.quantity_transaction;
                            total_repair_diff_bpp += value.quantity_diff;
                        }
                        if (value.location == 'WLD' && value.category == 'REPAIR') {
                            total_repair_inout_wld += value.quantity_inout;
                            total_repair_transaction_wld += value.quantity_transaction;
                            total_repair_diff_wld += value.quantity_diff;
                        }
                        if (value.location == 'BFF' && value.category == 'REPAIR') {
                            total_repair_inout_bff += value.quantity_inout;
                            total_repair_transaction_bff += value.quantity_transaction;
                            total_repair_diff_bff += value.quantity_diff;
                        }
                        if (value.location == 'LCQ' && value.category == 'REPAIR') {
                            total_repair_inout_lcq += value.quantity_inout;
                            total_repair_transaction_lcq += value.quantity_transaction;
                            total_repair_diff_lcq += value.quantity_diff;
                        }
                        if (value.location == 'PLT' && value.category == 'REPAIR') {
                            total_repair_inout_plt += value.quantity_inout;
                            total_repair_transaction_plt += value.quantity_transaction;
                            total_repair_diff_plt += value.quantity_diff;
                        }
                        if (value.category == 'REPAIR' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF', 'PLT', 'LCQ'
                            ]) != -1) {
                            tableRepairResumeBody += '<tr>';
                            tableRepairResumeBody += '<td style="width: 0.1%; text-align: center;">' + value
                                .location +
                                '</td>';
                            tableRepairResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_inout + '</td>';
                            tableRepairResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_transaction + '</td>';
                            tableRepairResumeBody += '<td style="width: 1%; text-align: center;">' + value
                                .quantity_diff + '</td>';
                            tableRepairResumeBody += '</tr>';


                            total_repair_inout += value.quantity_inout;
                            total_repair_transaction += value.quantity_transaction;
                            total_repair_diff += value.quantity_diff;
                        }
                    });

                    tableKanbanResumeBody += '<tr style="background-color: #fcf8e3;">';
                    tableKanbanResumeBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;">Total</td>';
                    tableKanbanResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_kanban_inout +
                        '</td>';
                    tableKanbanResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_kanban_transaction +
                        '</td>';
                    tableKanbanResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_kanban_diff +
                        '</td>';
                    tableKanbanResumeBody += '</tr>';

                    tableReturnResumeBody += '<tr style="background-color: #fcf8e3;">';
                    tableReturnResumeBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;">Total</td>';
                    tableReturnResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_return_inout +
                        '</td>';
                    tableReturnResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_return_transaction +
                        '</td>';
                    tableReturnResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_return_diff +
                        '</td>';
                    tableReturnResumeBody += '</tr>';

                    tableRepairResumeBody += '<tr style="background-color: #fcf8e3;">';
                    tableRepairResumeBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;">Total</td>';
                    tableRepairResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_repair_inout +
                        '</td>';
                    tableRepairResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_repair_transaction +
                        '</td>';
                    tableRepairResumeBody += '<td style="width: 1%; text-align: center; font-weight: bold;">' +
                        total_repair_diff +
                        '</td>';
                    tableRepairResumeBody += '</tr>';


                    $('#tableKanbanResumeBody').append(tableKanbanResumeBody);
                    $('#tableReturnResumeBody').append(tableReturnResumeBody);
                    $('#tableRepairResumeBody').append(tableRepairResumeBody);

                    var tableKanbanResumeDetailBody = "";
                    $('#tableKanbanResumeDetailBody').html("");

                    var tableReturnResumeDetailBody = "";
                    $('#tableReturnResumeDetailBody').html("");

                    var tableRepairResumeDetailBody = "";
                    $('#tableRepairResumeDetailBody').html("");

                    var total_kanban_detail = 0;
                    var total_return_detail = 0;
                    var total_repair_detail = 0;

                    $.each(datas, function(key, value) {
                        if (value.category == 'KANBAN' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF'
                            ]) != -1 && value.quantity_diff != 0) {
                            tableKanbanResumeDetailBody += '<tr onclick="fetchDetail(\'' + value.category +
                                '\',\'' + value.location + '\',\'' + value.material_number + '\')">';
                            tableKanbanResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .location +
                                '</td>';
                            tableKanbanResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .material_number + '</td>';
                            tableKanbanResumeDetailBody += '<td style="width: 3%; text-align: left;">' +
                                value
                                .material_description + '</td>';
                            tableKanbanResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .quantity_diff + '</td>';
                            tableKanbanResumeDetailBody += '</tr>';

                            total_kanban_detail += value.quantity_diff;
                        }

                        if (value.category == 'RETURN' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF', 'PLT', 'LCQ'
                            ]) != -1 && value.quantity_diff != 0) {
                            tableReturnResumeDetailBody += '<tr onclick="fetchDetail(\'' + value.category +
                                '\',\'' + value.location + '\',\'' + value.material_number + '\')">';
                            tableReturnResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .location +
                                '</td>';
                            tableReturnResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .material_number + '</td>';
                            tableReturnResumeDetailBody += '<td style="width: 3%; text-align: left;">' +
                                value
                                .material_description + '</td>';
                            tableReturnResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .quantity_diff + '</td>';
                            tableReturnResumeDetailBody += '</tr>';

                            total_return_detail += value.quantity_diff;
                        }

                        if (value.category == 'REPAIR' && jQuery.inArray(value.location, ['BPP', 'WLD',
                                'BFF', 'PLT', 'LCQ'
                            ]) != -1 && value.quantity_diff != 0) {
                            tableRepairResumeDetailBody += '<tr onclick="fetchDetail(\'' + value.category +
                                '\',\'' + value.location + '\',\'' + value.material_number + '\')">';
                            tableRepairResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .location +
                                '</td>';
                            tableRepairResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .material_number + '</td>';
                            tableRepairResumeDetailBody += '<td style="width: 3%; text-align: left;">' +
                                value
                                .material_description + '</td>';
                            tableRepairResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                                value
                                .quantity_diff + '</td>';
                            tableRepairResumeDetailBody += '</tr>';

                            total_repair_detail += value.quantity_diff;
                        }
                    });


                    tableKanbanResumeDetailBody += '<tr style="background-color: #fcf8e3;">';
                    tableKanbanResumeDetailBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;" colspan="3">Total</td>';
                    tableKanbanResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                        total_kanban_detail + '</td>';
                    tableKanbanResumeDetailBody += '</tr>';

                    tableReturnResumeDetailBody += '<tr style="background-color: #fcf8e3;">';
                    tableReturnResumeDetailBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;" colspan="3">Total</td>';
                    tableReturnResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                        total_return_detail + '</td>';
                    tableReturnResumeDetailBody += '</tr>';

                    tableRepairResumeDetailBody += '<tr style="background-color: #fcf8e3;">';
                    tableRepairResumeDetailBody +=
                        '<td style="width: 0.1%; text-align: center; font-weight: bold;" colspan="3">Total</td>';
                    tableRepairResumeDetailBody += '<td style="width: 0.1%; text-align: center;">' +
                        total_repair_detail + '</td>';
                    tableRepairResumeDetailBody += '</tr>';

                    $('#tableKanbanResumeDetailBody').append(tableKanbanResumeDetailBody);
                    $('#tableReturnResumeDetailBody').append(tableReturnResumeDetailBody);
                    $('#tableRepairResumeDetailBody').append(tableRepairResumeDetailBody);

                    Highcharts.chart('container_kanban', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: null
                        },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: ['BPP', 'WLD', 'BFF']
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y} Pc(s)',
                                },
                                animation: {
                                    duration: 0
                                },
                                // cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             fetchDetail('KANBAN', this.category, '');
                                //         }
                                //     }
                                // },
                            }
                        },
                        series: [{
                            name: 'Different',
                            color: '#605ca8',
                            data: [total_kanban_diff_bpp, total_kanban_diff_wld,
                                total_kanban_diff_bff
                            ]
                        }]
                    });

                    Highcharts.chart('container_return', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: null
                        },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: ['BPP', 'WLD', 'BFF', 'LCQ', 'PLT']
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y} Pc(s)',
                                },
                                animation: {
                                    duration: 0
                                },
                                // cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             fetchDetail('RETURN', this.category, '');
                                //         }
                                //     }
                                // },
                            }
                        },
                        series: [{
                            name: 'Different',
                            color: '#605ca8',
                            data: [total_return_diff_bpp, total_return_diff_wld,
                                total_return_diff_bff, total_return_diff_lcq,
                                total_return_diff_plt
                            ]
                        }]
                    });

                    Highcharts.chart('container_repair', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            text: null
                        },
                        yAxis: {
                            title: {
                                text: null
                            }
                        },
                        xAxis: {
                            categories: ['BPP', 'WLD', 'BFF', 'LCQ', 'PLT']
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: false
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y} Pc(s)',
                                },
                                animation: {
                                    duration: 0
                                },
                                // cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             fetchDetail('REPAIR', this.category, '');
                                //         }
                                //     }
                                // },
                            }
                        },
                        series: [{
                            name: 'Different',
                            color: '#00a65a',
                            data: [total_repair_diff_bpp, total_repair_diff_wld,
                                total_repair_diff_bff, total_repair_diff_lcq,
                                total_repair_diff_plt
                            ]
                        }]
                    });

                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    audio_error.play();
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function callName(name) {
            var new_name = '';
            var blok_m = [
                'M.',
                'Mas',
                'Moch',
                'Moch.',
                'Mochamad',
                'Mochammad',
                'Moh.',
                'Mohamad',
                'Mokhamad',
                'Much.',
                'Muchammad',
                'Muhamad',
                'Muhammaad',
                'Muhammad',
                'Mukammad',
                'Mukhamad',
                'Mukhammad'
            ];
            if (name != null) {
                if (name.includes(' ')) {
                    name = name.split(' ');
                    if (blok_m.includes(name[0])) {
                        new_name = 'M.';
                        for (i = 1; i < name.length; i++) {
                            if (i == 1) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    } else {
                        for (i = 0; i < name.length; i++) {
                            if (i == 0) {
                                new_name += ' ';
                                new_name += name[i];
                            } else {
                                new_name += ' ';
                                new_name += name[i].substr(0, 1) + '.';
                            }
                        }
                    }
                } else {
                    new_name = name;
                }
            } else {
                new_name = '-';
            }
            return new_name;
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
