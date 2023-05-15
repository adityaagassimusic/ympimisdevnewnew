@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            padding: 4px;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
        }

        .label-status {
            color: black;
            font-size: 12px;
            border-radius: 4px;
            padding: 1px 5px 2px 5px;
            border: 1px solid black;
            min-width: 120px;
            vertical-align: middle;
        }

        .non-active {
            font-weight: bold;
        }

        #loading,
        #error {
            display: none;
        }

        #container1 {
            height: 70vh;
        }

        .highcharts-figure,
        .highcharts-data-table table {
            min-width: 310px;
            max-width: 800px;
            margin: 1em auto;
        }

        .highcharts-data-table table {
            font-family: Verdana, sans-serif;
            border-collapse: collapse;
            border: 1px solid #ebebeb;
            margin: 10px auto;
            text-align: center;
            width: 100%;
            max-width: 500px;
        }

        .highcharts-data-table caption {
            padding: 1em 0;
            font-size: 1.2em;
            color: #555;
        }

        .highcharts-data-table th {
            font-weight: 600;
            padding: 0.5em;
        }

        .highcharts-data-table td,
        .highcharts-data-table th,
        .highcharts-data-table caption {
            padding: 0.5em;
        }

        .highcharts-data-table thead tr,
        .highcharts-data-table tr:nth-child(even) {
            background: #f8f8f8;
        }

        .highcharts-data-table tr:hover {
            background: #f1f7ff;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $title }}
            <small><span class="text-purple">{{ $title_jp }}</span></small>
        </h1>
    </section>
@stop

@section('content')
    <section class="content" style="font-size: 0.8vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8;">
            <p style="position: absolute; color: White; top: 45%; left: 40%;">
                <span style="font-size: 40px">Please Wait <i class="fa fa-spin fa-refresh"></i></span>
            </p>
        </div>

        <div class="box box-solid">
            <div class="row">
                <div class="col-xs-12">


                    <div class="col-xs-4">
                        <div class="col-xs-12" style="padding: 10px;">
                            <div class="form-group" style="margin: 0px;">
                                <button onClick="drawChart(id)" class="btn btn-chart btn-xs btn-default non-active"
                                    id="6month">&nbsp;&nbsp;&nbsp;6 Month&nbsp;&nbsp;&nbsp;</button>
                                <button onClick="drawChart(id)" class="btn btn-chart btn-xs btn-default non-active"
                                    id="3month">&nbsp;&nbsp;&nbsp;3 Month&nbsp;&nbsp;&nbsp;</button>
                                <button onClick="drawChart(id)" class="btn btn-chart btn-xs btn-default non-active"
                                    id="1month">&nbsp;&nbsp;&nbsp;1 Month&nbsp;&nbsp;&nbsp;</button>
                                <button onClick="drawChart(id)" class="btn btn-chart btn-xs btn-default" id="outstanding"
                                    disabled>Outstanding</button>
                            </div>
                        </div>
                        <div class="col-xs-12" style="padding: 10px;">
                            <div id="container1"></div>
                        </div>
                    </div>

                    <div class="col-xs-8">
                        <form method="GET" action="{{ action('ExtraOrderController@indexApprovalMonitoring') }}">
                            <div class="col-xs-8 col-xs-offset-2">
                                <div class="box box-primary box-solid" style="margin-top: 2%; margin-bottom: 1%;">
                                    <div class="box-body" style="padding-bottom: 0px;">
                                        <div class="col-xs-3" style="padding-left: 0px; padding-right: 2.5px;">
                                            <div class="form-group">
                                                <label>Submit From</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select Submit Date"
                                                        class="form-control datepicker pull-right" id="submit_from"
                                                        name="submit_from" style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-3" style="padding-left: 2.5px; padding-right: 0px;">
                                            <div class="form-group">
                                                <label>Submit To</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <input type="text" placeholder="Select Submit Date"
                                                        class="form-control datepicker pull-right" id="submit_to"
                                                        name="submit_to" style="font-size: 0.8vw;">
                                                </div>
                                            </div>
                                        </div>
                                        {{-- <div class="col-xs-3" style="padding-left: 5px; padding-right: 0px;">
                                        <div class="form-group">
                                            <label>Approval Progress</label>
                                            <div class="input-group date" style="width: 100%;">
                                                <select style="width: 100%;" class="form-control select2" id="approval_status_select" name="approval_status_select" data-placeholder="Select Progress" onchange="changeApprovalStatus()">
                                                    <option value="">&nbsp;</option>
                                                    <option value="0">Waiting for approval</option>
                                                    <option value="1">Fully approved</option>
                                                </select>
                                                <input type="text" name="approval_status" id="approval_status" hidden>
                                            </div>
                                        </div>
                                    </div> --}}
                                        <div class="col-xs-6" style="padding-left: 5px; padding-right: 0px;">
                                            <div class="form-group">
                                                <label>Approver In Progress</label>
                                                <div class="input-group date" style="width: 100%;">
                                                    <select style="width: 100%;" class="form-control select2"
                                                        id="approver_id_select" name="approver_id_select"
                                                        data-placeholder="Select PIC" onchange="changeApproverId()">
                                                        <option value="">&nbsp;</option>
                                                        @foreach ($approvers as $approver)
                                                            <option value="{{ $approver->approver_id }}">
                                                                {{ $approver->approver_name }}</option>
                                                        @endforeach
                                                    </select>
                                                    <input type="text" name="approver_id" id="approver_id" hidden>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8 col-xs-offset-2">
                                <div class="form-group pull-right" style="margin: 0px;">
                                    <button onClick="clearConfirmation()"
                                        class="btn btn-danger">&nbsp;&nbsp;&nbsp;&nbsp;Clear&nbsp;&nbsp;&nbsp;&nbsp;</button>
                                    <button type="submit" class="btn btn-primary"><span class="fa fa-search"></span>
                                        Search</button>
                                </div>
                            </div>
                        </form>

                        <div class="col-xs-12" style="margin-top: 10px;">
                            <table class="table table-hover table-striped table-bordered" id="tableResume">
                                <thead style="background-color: rgb(126,86,134); color: white">
                                    <tr>
                                        <th style="width: 10%; text-align: center">EO Data</th>
                                        <th style="width: 18%; text-align: center">Buyer</th>
                                        <th style="width: 18%; text-align: center">Foreman & Chief</th>
                                        <th style="width: 18%; text-align: center">Manager</th>
                                        <th style="width: 18%; text-align: center">DGM</th>
                                        <th style="width: 18%; text-align: center">GM</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBodyResume">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div class="modal fade" id="modalDetail">
        <div class="modal-dialog" style="width: 60%;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="modalDetailTitle"></h4>
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px">
                        <table class="table table-hover table-bordered table-striped" id="tableDetail">
                            <thead style="background-color: rgba(126,86,134,.7);">
                                <tr>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;">EO Number</th>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;">Receipt</th>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;">Destination</th>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;">Status</th>
                                    <th style="width: 20%; text-align: center; vertical-align: middle;">Outstanding
                                        Approver</th>
                                </tr>
                            </thead>
                            <tbody id="bodyDetail">
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/jquery.tagsinput.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/highcharts-3d.js') }}"></script>

    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {

            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2({
                allowClear: true
            });

            $('.datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true,
                autoclose: true,
            });

            $("#approver_id_select").val("{{ $_GET['approver_id'] }}").trigger('change.select2');

            showTable();
            drawChart('outstanding');

        });

        function changeApproverId() {
            $("#approver_id").val($("#approver_id_select").val());
        }

        function ShowModal(month, series) {
            $('#loading').show();
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            $('#bodyDetail').html("");
            var tableData = "";

            for (var i = 0; i < approval_data.length; i++) {

                if (convert(approval_data[i].submit_month) == month) {
                    if ((approval_data[i].status == series) && (approval_data[i].status == 'Fully approved')) {
                        tableData += '<tr>';
                        tableData += '<td style="width:10%; text-align: center; cursor: pointer;">';
                        tableData +=
                            '<span style="font-weight: bold; cursor: pointer; color: #3c8dbc;" onclick="detailExtraOrder(\'' +
                            approval_data[i].eo_number + '\')">' + approval_data[i].eo_number + '</span><br>';
                        tableData += '</td>';
                        tableData += '<td style="width:10%;">' + approval_data[i].attention + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i]
                            .destination_shortname + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i].status + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">-</td>';
                        tableData += '</tr>';
                    } else if ((approval_data[i].status == series) && (approval_data[i].status == 'Waiting for approval')) {
                        tableData += '<tr>';
                        tableData += '<td style="width:10%; text-align: center; cursor: pointer;">';
                        tableData +=
                            '<span style="font-weight: bold; cursor: pointer; color: #3c8dbc;" onclick="detailExtraOrder(\'' +
                            approval_data[i].eo_number + '\')">' + approval_data[i].eo_number + '</span><br>';
                        tableData += '</td>';
                        tableData += '<td style="width:10%;">' + approval_data[i].attention + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i]
                            .destination_shortname + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i].status + '</td>';
                        tableData += '<td style="width:10%; text-align: left;">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var j = 0; j < approval_detail.length; j++) {
                            if (approval_detail[j].eo_number == approval_data[i].eo_number) {
                                if (approval_detail[j].approval_status == 1) {
                                    tableData += '<li>';
                                    tableData +=
                                        '<a style="font-weight: bold; cursor: pointer; color: red;" onclick="detailApproval(id)" id="' +
                                        approval_detail[j].id + '">' + approval_detail[j].approver_name + '</a>';
                                    tableData += '</li>';
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';
                        tableData += '</tr>';
                    } else if ((approval_data[i].status == series) && (approval_data[i].status == 'Not submitted yet')) {
                        tableData += '<tr>';
                        tableData += '<td style="width:10%; text-align: center; cursor: pointer;">';
                        tableData +=
                            '<span style="font-weight: bold; cursor: pointer; color: #3c8dbc;" onclick="detailExtraOrder(\'' +
                            approval_data[i].eo_number + '\')">' + approval_data[i].eo_number + '</span><br>';
                        tableData += '</td>';
                        tableData += '<td style="width:10%;">' + approval_data[i].attention + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i]
                            .destination_shortname + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">' + approval_data[i].status + '</td>';
                        tableData += '<td style="width:10%; text-align: center;">-</td>';
                        tableData += '</tr>';
                    }
                }

            }


            $('#bodyDetail').append(tableData);
            $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [-1],
                    ['Show all']
                ],
                'buttons': {
                    buttons: [{
                        extend: 'pageLength',
                        className: 'btn btn-default',
                    }]
                },
                'paging': true,
                'lengthChange': true,
                'searching': true,
                'ordering': false,
                'info': true,
                'autoWidth': true,
                "sPaginationType": "full_numbers",
                "bJQueryUI": true,
                "bAutoWidth": false,
                "processing": true
            });

            var title = '<center><b>EOC APPROVAL ON ' + month.toUpperCase() + ' WITH STATUS ' + series.toUpperCase() +
                '</b></center>';
            $('#modalDetailTitle').html(title);
            $('#modalDetail').modal('show');
            $('#loading').hide();

        }

        function clearConfirmation() {
            location.reload(true);
        }

        function convert(param) {
            var txt = ['Jan', 'Feb', 'Mar', 'Apr', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return txt[parseInt(param.split('-')[1]) - 1] + ' ' + param.split('-')[0].substr(2, 2);
        }

        var approval_data = [];
        var approval_detail = [];

        function approvalStatus(status) {
            var message = '';

            if (status == 3) {
                message += '<label class="label-sm label-status" style="background-color: #aee571; margin: 0px;">';
                message += '<p style="font-size: 0.75vw; margin: 0px;">Fully Approved</p>';
                message += '</label>';
            } else {
                message +=
                    '<label class="label-sm label-status" style="background-color: #f25450; margin: 0px; color: #f8ef00;">';
                message += '<p style="font-size: 0.75vw; margin: 0px;">Waiting for Approval</p>';
                message += '</label>';
            }

            return message;
        }

        function detailApproval(approval_id) {
            window.open('{{ url('index/extra_order/view_approval') }}' + '/' + approval_id, '_self');
        }

        function dateFormatter(date) {
            var txt = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            return date.split('-')[2] + '-' + txt[parseInt(date.split('-')[1]) - 1] + '-' + date.split('-')[0];
        }

        function datetimeFormatter(datetime) {
            var txt = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
            var date = datetime.split(' ')[0];
            var time = datetime.split(' ')[1];

            var new_date = date.split('-')[2] + '-' + txt[parseInt(date.split('-')[1]) - 1] + '-' + date.split('-')[0];
            var new_time = time.split(':')[0] + ':' + time.split(':')[1];

            return new_date + ' ' + new_time;
        }

        function approvalList(approval) {
            var message = '';
            if (approval.status == null) {
                message += '<li>';
                message += '<a style="font-weight: bold; cursor: pointer; color: red;" onclick="detailApproval(id)" id="' +
                    approval.id + '">' + approval.approver_name + '<br>(Waiting)</a>';
                message += '</li>';
            } else if (approval.status == 'Hold & Comment') {
                message += '<li>';
                message +=
                    '<a style="font-weight: bold; cursor: pointer; color: #367fa9;" onclick="detailApproval(id)" id="' +
                    approval.id + '">' + approval.approver_name + '<br>(Hold & Comment)</a>';
                message += '</li>';
            } else if (approval.status == 'Rejected') {
                message += '<li>';
                message += '<a style="font-weight: bold; color: red;" id="' + approval.id + '">' + approval.approver_name +
                    '<br>(' + datetimeFormatter(approval.approved_at) + ')</a>';
                message += '</li>';
            } else {
                message += '<li>';
                message += '<a style="font-weight: bold; color: green;" id="' + approval.id + '">' + approval
                    .approver_name + '<br>(' + datetimeFormatter(approval.approved_at) + ')</a>';
                message += '</li>';
            }
            return message;
        }

        function detailExtraOrder(eo_number) {
            window.open('{{ url('index/extra_order/detail') }}' + '/' + eo_number, '_blank');
        }

        function extraOrderData(eo) {
            var message = '';
            message +=
                '<span style="font-weight: bold; cursor: pointer; font-size: 1.1vw; color: #3c8dbc;" onclick="detailExtraOrder(\'' +
                eo.eo_number + '\')">' + eo.eo_number + '</span><br>';
            message += '<span>' + eo.attention + '</span><br>';
            message += '<span>' + eo.destination_shortname + '</span><br><br>';
            message += '<span>Submit Date :<br>' + dateFormatter(eo.submit_date) + '</span><br><br>';

            return message;

        }

        function showTable() {

            var data = {
                submit_from: "{{ $_GET['submit_from'] }}",
                submit_to: "{{ $_GET['submit_to'] }}",
                approver_id: "{{ $_GET['approver_id'] }}",
            }

            $('#loading').show();

            $.get('{{ url('fetch/extra_order/approval_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#tableResume').DataTable().clear();
                    $('#tableResume').DataTable().destroy();
                    $('#tableBodyResume').html("");
                    $('#tableBodyResume').empty();
                    var tableData = '';

                    for (var x = 0; x < result.approval.length; x++) {

                        tableData += '<tr>';
                        tableData += '<td style="width: 10%; text-align: left">' + extraOrderData(result.approval[
                            x]) + approvalStatus(result.approval[x].approval_status) + '</td>';

                        tableData += '<td style="width: 18%; text-align: left">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var i = 0; i < result.approval_detail.length; i++) {
                            if (result.approval_detail[i].eo_number == result.approval[x].eo_number) {
                                if (result.approval_detail[i].approval_order == 1) {
                                    tableData += approvalList(result.approval_detail[i]);
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';


                        tableData += '<td style="width: 18%; text-align: left">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var i = 0; i < result.approval_detail.length; i++) {
                            if (result.approval_detail[i].eo_number == result.approval[x].eo_number) {
                                if (result.approval_detail[i].approval_order == 2) {
                                    tableData += approvalList(result.approval_detail[i]);
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';

                        tableData += '<td style="width: 18%; text-align: left">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var i = 0; i < result.approval_detail.length; i++) {
                            if (result.approval_detail[i].eo_number == result.approval[x].eo_number) {
                                if (result.approval_detail[i].approval_order == 3) {
                                    tableData += approvalList(result.approval_detail[i]);
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';


                        tableData += '<td style="width: 18%; text-align: left">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var i = 0; i < result.approval_detail.length; i++) {
                            if (result.approval_detail[i].eo_number == result.approval[x].eo_number) {
                                if (result.approval_detail[i].approval_order == 4) {
                                    tableData += approvalList(result.approval_detail[i]);
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';


                        tableData += '<td style="width: 18%; text-align: left">';
                        tableData += '<ul style="padding-left: 10%;">';
                        for (var i = 0; i < result.approval_detail.length; i++) {
                            if (result.approval_detail[i].eo_number == result.approval[x].eo_number) {
                                if (result.approval_detail[i].approval_order == 5) {
                                    tableData += approvalList(result.approval_detail[i]);
                                }
                            }
                        }
                        tableData += '</ul>';
                        tableData += '</td>';


                        // tableData += '<td style="width: 5%; text-align: center;">';
                        // tableData += '<button onclick="downloadEoc(\''+ result.approval[x].eo_number +'\')" class="btn btn-xs btn-primary" id="btn-download" style="color: white; width: 60%;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;Download EOC</button>';
                        // tableData += '<button onclick="downloadEoc(\''+ result.approval[x].eo_number +'\')" class="btn btn-xs btn-primary" id="btn-download" style="color: white; width: 60%;"><i class="fa fa-file-pdf-o"></i>&nbsp;&nbsp;&nbsp;Detail EOC</button>';
                        // tableData += '</td>';

                        tableData += '</tr>';

                    }

                    $('#tableBodyResume').append(tableData);

                    var tableResume = $('#tableResume').DataTable({
                        'dom': 'Bfrtip',
                        'responsive': true,
                        'lengthMenu': [
                            [10, 25, 50, -1],
                            ['10 rows', '25 rows', '50 rows', 'Show all']
                        ],
                        'buttons': {
                            buttons: [{
                                extend: 'pageLength',
                                className: 'btn btn-default',
                            }]
                        },
                        'paging': true,
                        'lengthChange': true,
                        'pageLength': 10,
                        'searching': true,
                        'ordering': true,
                        'order': [],
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true
                    });

                    $('#loading').hide();


                } else {
                    openErrorGritter('Error!', result.message);
                }
            });
        }

        function drawChart(id) {
            var data = {
                condition: id
            }

            $('#loading').show();

            $.get('{{ url('fetch/extra_order/approval_chart') }}', data, function(result, status, xhr) {
                if (result.status) {

                    approval_data = [];
                    approval_detail = result.approval_detail;

                    $(".btn-chart").prop('disabled', false);
                    $(".btn-chart").addClass('non-active');

                    $("#" + id).prop('disabled', true);
                    $("#" + id).removeClass('non-active');

                    var grouping = [];
                    for (var x = 0; x < result.approval.length; x++) {
                        var status = '';

                        if (!grouping[result.approval[x].submit_month]) {
                            grouping[result.approval[x].submit_month] = {
                                'full_approved': 0,
                                'waiting': 0,
                                'not_submitted': 0,
                            };
                        }

                        if (parseInt(result.approval[x].total) == parseInt(result.approval[x].approve)) {
                            if (parseInt(result.approval[x].total) > 0) {
                                grouping[result.approval[x].submit_month].full_approved += 1;
                                status = 'Fully approved';
                            } else {
                                grouping[result.approval[x].submit_month].not_submitted += 1;
                                status = 'Not submitted yet';
                            }
                        } else {
                            grouping[result.approval[x].submit_month].waiting += 1;
                            status = 'Waiting for approval';
                        }

                        approval_data.push({
                            'submit_month': result.approval[x].submit_month,
                            'eo_number': result.approval[x].eo_number,
                            'attention': result.approval[x].attention,
                            'destination_shortname': result.approval[x].destination_shortname,
                            'status': status
                        });
                    }

                    var categories = [];
                    var full_approved = [];
                    var not_submitted = [];
                    var waiting = [];

                    for (var key in grouping) {
                        categories.push(convert(key));
                        full_approved.push(parseInt(grouping[key].full_approved));
                        not_submitted.push(parseInt(grouping[key].not_submitted));
                        waiting.push(parseInt(grouping[key].waiting));
                    }



                    Highcharts.chart('container1', {
                        chart: {
                            type: 'column',
                        },
                        title: {
                            text: ''
                        },
                        xAxis: {
                            categories: categories,
                            type: 'category',
                            gridLineWidth: 1,
                            gridLineColor: 'RGB(204,255,255)',
                            lineWidth: 2,
                            lineColor: '#9e9e9e',
                            labels: {
                                style: {
                                    fontSize: '13px'
                                }
                            },
                        },
                        yAxis: {
                            title: {
                                text: 'Count of Extra Order',
                                style: {
                                    color: '#000',
                                    fontSize: '15px',
                                    fontWeight: 'bold',
                                    fill: '#6d869f'
                                }
                            },
                            stackLabels: {
                                enabled: true,
                                style: {
                                    fontWeight: 'bold',
                                    color: 'white',
                                    fontSize: '1vw'
                                }
                            },
                            opposite: true
                        },
                        tooltip: {
                            headerFormat: '<span>{series.name}</span>',
                            pointFormat: '<span style="color:{point.color};font-weight: bold;">{point.name} </span>: <b>{point.y}</b><br/>',
                        },
                        legend: {
                            layout: 'horizontal',
                            backgroundColor: Highcharts.defaultOptions.legend.backgroundColor || '#ffffff',
                            itemStyle: {
                                fontSize: '10px',
                            },
                            enabled: true,
                            reversed: true
                        },
                        plotOptions: {
                            column: {
                                stacking: 'normal',
                            },
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            ShowModal(this.category, this.series.name);
                                        }
                                    }
                                },
                                animation: false,
                                dataLabels: {
                                    enabled: true,
                                    formatter: function() {
                                        return (this.y != 0) ? this.y : "";
                                    },
                                    style: {
                                        fontSize: '1vw'
                                    }
                                },
                                animation: false,
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.93,
                                cursor: 'pointer'
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Fully approved',
                            data: full_approved,
                            color: '#4bc16b'
                        }, {
                            name: 'Waiting for approval',
                            data: waiting,
                            color: '#f25450',
                        }, {
                            name: 'Not submitted yet',
                            data: not_submitted,
                            color: '#babec2',
                        }]
                    });

                    $('#loading').hide();


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
                time: '4000'
            });
        }

        function openErrorGritter(title, message) {
            jQuery.gritter.add({
                title: title,
                text: message,
                class_name: 'growl-danger',
                image: '{{ url('images/image-stop.png') }}',
                sticky: false,
                time: '4000'
            });
        }
    </script>

@endsection
