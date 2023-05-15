@extends('layouts.master')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
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
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
            vertical-align: middle;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $page }}
        </h1>

    </section>
@endsection


@section('content')

    <section class="content">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: White; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-thumbs-o-up"></i> Success!</h4>
                {{ session('status') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger alert-dismissible">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <h4><i class="icon fa fa-ban"></i> Error!</h4>
                {{ session('error') }}
            </div>
        @endif

        <div class="row">
            <div class="col-xs-12" style="padding: 0px;">
                <div class="col-xs-2" style="padding-right: 0px;">
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="filter_month" id="filter_month"
                            placeholder="Select Month">
                    </div>
                </div>
                <div class="col-xs-2" style="padding-right: 0px;">
                    <button onclick="fetchTable()" class="btn btn-primary">Search</button>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="nav-tabs-custom" style="margin-top: 1%;">
                    <ul class="nav nav-tabs" style="font-weight: bold; font-size: 15px">
                        <li class="vendor-tab active">
                            <a href="#tab_0" data-toggle="tab" id="tab_header_0">Material Volume</a>
                        </li>
                        <li class="vendor-tab">
                            <a href="#tab_1" data-toggle="tab" id="tab_header_1">Request VS Sales Order</a>
                        </li>
                        <li class="vendor-tab">
                            <a href="#tab_15" data-toggle="tab" id="tab_header_15">Sales Order VS Draft Shipment</a>
                        </li>
                        <li class="vendor-tab">
                            <a href="#tab_2" data-toggle="tab" id="tab_header_2">Sales Order VS Ship. Sch.</a>
                        </li>
                    </ul>

                    <div class="tab-content">
                        <div class="tab-pane active" id="tab_0" style="overflow-x: auto;">
                            <table id="table_0" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="head_0"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_0">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_0">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_1" style="overflow-x: auto;">
                            <table id="table_1" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="head_1"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_1">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_1">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_15" style="overflow-x: auto;">
                            <table id="table_15" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="head_15"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_15">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_15">
                                    <th></th>
                                </tfoot>
                            </table>
                        </div>
                        <div class="tab-pane" id="tab_2" style="overflow-x: auto;">
                            <table id="table_2" class="table table-bordered table-hover" style="width: 100%;">
                                <thead id="head_2"
                                    style="background-color: rgba(126,86,134,.7); vertical-align: middle;">
                                    <th></th>
                                </thead>
                                <tbody id="body_2">
                                    <td></td>
                                </tbody>
                                <tfoot id="foot_2">
                                    <td></td>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@stop

@section('scripts')
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
            $('body').toggleClass("sidebar-collapse");

            $('.select3').select2({
                allowClear: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            // fetchTable();

        });

        $('#uploadModal').on('hidden.bs.modal', function() {
            $('#upload_month').val('');
            $('#upload').val('');
        });


        function fetchTable() {

            var month = $('#filter_month').val();

            var data = {
                month: month
            }

            $('#loading').show();
            $.get('{{ url('fetch/shipment_unmatch') }}', data, function(result, status, xhr) {
                if (result.status) {


                    // START TABLE MATERIAL VOLUME
                    $('#table_0').DataTable().clear();
                    $('#table_0').DataTable().destroy();
                    $('#head_0').html("");
                    var head_0 = '<tr>';
                    head_0 += '<th rowspan="2" style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_0 += '<th rowspan="2" style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_0 += '<th rowspan="2" style="vertical-align: middle; text-align: center;">Category</th>';
                    head_0 += '<th rowspan="2" style="vertical-align: middle; text-align: center;">HPL</th>';
                    head_0 += '<th style="background-color: #b4c6e7;" colspan="5">Pallet</th>';
                    head_0 += '<th style="background-color: #ffd34e;" colspan="5">Carton</th>';
                    head_0 += '<th rowspan="2" style="vertical-align: middle; text-align: center;">status</th>';
                    head_0 += '</tr>';
                    head_0 += '<tr>';
                    head_0 += '<th style="background-color: #b4c6e7;">Lot</th>';
                    head_0 += '<th style="background-color: #b4c6e7;">L</th>';
                    head_0 += '<th style="background-color: #b4c6e7;">W</th>';
                    head_0 += '<th style="background-color: #b4c6e7;">H</th>';
                    head_0 += '<th style="background-color: #b4c6e7;">Volume</th>';
                    head_0 += '<th style="background-color: #ffd34e;">Lot</th>';
                    head_0 += '<th style="background-color: #ffd34e;">L</th>';
                    head_0 += '<th style="background-color: #ffd34e;">W</th>';
                    head_0 += '<th style="background-color: #ffd34e;">H</th>';
                    head_0 += '<th style="background-color: #ffd34e;">Volume</th>';
                    head_0 += '</tr>';
                    $('#head_0').append(head_0);

                    $('#foot_0').html("");
                    var foot_0 = '';
                    foot_0 += '<tr>'
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '<th></th>';
                    foot_0 += '</tr>';
                    $('#foot_0').append(foot_0);

                    $('#body_0').html("");
                    var body_0 = '';
                    $.each(result.resume_request_so, function(key, value) {
                        body_0 += '<tr>';
                        body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        body_0 += value.material_number;
                        body_0 += '</td>';
                        body_0 += '<td style="vertical-align: middle; text-align: left;">';
                        body_0 += value.material_description;
                        body_0 += '</td>';
                        body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        body_0 += value.category;
                        body_0 += '</td>';
                        body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        body_0 += value.hpl;
                        body_0 += '</td>';

                        var pallet_lot = 0;
                        var pallet_l = 0;
                        var pallet_w = 0;
                        var pallet_h = 0;
                        var pallet_volume = 0;
                        var carton_lot = 0;
                        var carton_l = 0;
                        var carton_w = 0;
                        var carton_h = 0;
                        var carton_volume = 0;

                        for (let i = 0; i < result.material_volume.length; i++) {
                            if (result.material_volume[i].material_number = value.material_number) {
                                pallet_lot = result.material_volume[i].lot_pallet;
                                pallet_l = result.material_volume[i].length_pallet;
                                pallet_w = result.material_volume[i].width_pallet;
                                pallet_h = result.material_volume[i].height_pallet;
                                pallet_volume = result.material_volume[i].cubic_meter_pallet;
                                carton_lot = result.material_volume[i].lot_carton;
                                carton_l = result.material_volume[i].length;
                                carton_w = result.material_volume[i].width;
                                carton_h = result.material_volume[i].height;
                                carton_volume = result.material_volume[i].cubic_meter;
                                break;
                            }
                        }


                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += pallet_lot;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += pallet_l;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += pallet_w;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += pallet_h;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += pallet_volume;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += carton_lot;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += carton_l;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += carton_w;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += carton_h;
                        body_0 += '</td>';

                        if (pallet_lot == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                        }
                        body_0 += carton_volume;
                        body_0 += '</td>';


                        if (pallet_lot == 0 || pallet_l == 0 || pallet_w == 0 || pallet_h == 0 ||
                            pallet_volume == 0 || carton_lot == 0 || carton_l == 0 || carton_w == 0 ||
                            carton_h == 0 || carton_volume == 0) {
                            body_0 +=
                                '<td style="background-color: #ffccff; vertical-align: middle; text-align: center;">';
                            body_0 += 'NG';
                            body_0 += '</td>';
                        } else {
                            body_0 += '<td style="vertical-align: middle; text-align: center;">';
                            body_0 += 'OK';
                            body_0 += '</td>';
                        }

                        body_0 += '</tr>';
                    });
                    $('#body_0').append(body_0);

                    $('#table_0 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table1 = $('#table_0').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        "columnDefs": [{
                            "targets": [1],
                            "className": "text-left"
                        }],
                        "ordering": false,
                        "ordering": false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([2, 3, 14])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#table_0 th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#table_0 th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table1.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_0 tfoot tr').appendTo('#table_0 thead');
                    // END TABLE MATERIAL VOLUME




                    // START TABLE REQUEST VS SALES ORDER
                    $('#table_1').DataTable().clear();
                    $('#table_1').DataTable().destroy();
                    $('#head_1').html("");
                    var head_1 = '<tr>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">HPL</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Destination</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Request</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">SO</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">Diff</th>';
                    head_1 += '<th style="vertical-align: middle; text-align: center;">status</th>';
                    head_1 += '</tr>';
                    $('#head_1').append(head_1);

                    $('#foot_1').html("");
                    var foot_1 = '';
                    foot_1 += '<tr>'
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '<th></th>';
                    foot_1 += '</tr>';
                    $('#foot_1').append(foot_1);

                    $('#body_1').html("");
                    var body_1 = '';
                    $.each(result.resume_request_so, function(key, value) {
                        body_1 += '<tr>';
                        body_1 += '<td style="vertical-align: middle; text-align: center;">';
                        body_1 += value.material_number;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: left;">';
                        body_1 += value.material_description;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: center;">';
                        body_1 += value.category;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: center;">';
                        body_1 += value.hpl;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: center;">';
                        body_1 += value.destination_shortname;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: right;">';
                        body_1 += value.request;
                        body_1 += '</td>';
                        body_1 += '<td style="vertical-align: middle; text-align: right;">';
                        body_1 += value.so_qty;
                        body_1 += '</td>';

                        var bg_color = '';
                        var status = '';
                        if (value.diff != 0) {
                            bg_color = 'background-color : #ffccff;';
                            status = 'NG';
                        } else {
                            bg_color = 'background-color : #ccffff;';
                            status = 'OK';
                        }

                        var value_diff = value.diff;
                        if (value_diff > 0) {
                            value_diff = '+' + value_diff;
                        }

                        body_1 += '<td style="' + bg_color + 'vertical-align: middle; text-align: right;">';
                        body_1 += value_diff;
                        body_1 += '</td>';
                        body_1 += '<td style="' + bg_color +
                            'vertical-align: middle; text-align: center;">';
                        body_1 += status;
                        body_1 += '</td>';
                        body_1 += '</tr>';
                    });
                    $('#body_1').append(body_1);

                    $('#table_1 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table1 = $('#table_1').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        "columnDefs": [{
                            "targets": [1],
                            "className": "text-left"
                        }],
                        "ordering": false,
                        "ordering": false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([2, 3, 4, 8])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#table_1 th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#table_1 th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table1.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_1 tfoot tr').appendTo('#table_1 thead');
                    // END TABLE REQUEST VS SALES ORDER


                    // START TABLE SALES ORDER VS DRAFT SHIPMENT
                    $('#table_15').DataTable().clear();
                    $('#table_15').DataTable().destroy();
                    $('#head_15').html("");
                    var head_15 = '<tr>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">HPL</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Destination</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">SO</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Draft</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Diff</th>';
                    head_15 += '<th style="vertical-align: middle; text-align: center;">Status</th>';
                    head_15 += '</tr>';
                    $('#head_15').append(head_15);

                    $('#foot_15').html("");
                    var foot_15 = '';
                    foot_15 += '<tr>'
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '<th></th>';
                    foot_15 += '</tr>';
                    $('#foot_15').append(foot_15);

                    $('#body_15').html("");
                    var body_15 = '';
                    $.each(result.resume_so_draft, function(key, value) {
                        body_15 += '<tr>';
                        body_15 += '<td style="vertical-align: middle; text-align: center;">';
                        body_15 += value.material_number;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: left;">';
                        body_15 += value.material_description;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: center;">';
                        body_15 += value.category;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: center;">';
                        body_15 += value.hpl;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: center;">';
                        body_15 += value.destination_shortname;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: right;">';
                        body_15 += value.so_qty;
                        body_15 += '</td>';
                        body_15 += '<td style="vertical-align: middle; text-align: right;">';
                        body_15 += value.shipment;
                        body_15 += '</td>';

                        var bg_color = '';
                        var status = '';
                        if (value.diff != 0) {
                            bg_color = 'background-color : #ffccff;';
                            status = 'NG';
                        } else {
                            bg_color = 'background-color : #ccffff;';
                            status = 'OK';
                        }

                        var value_diff = value.shipment - value.so_qty;
                        if (value_diff > 0) {
                            value_diff = '+' + value_diff;
                        }

                        body_15 += '<td style="' + bg_color +
                            'vertical-align: middle; text-align: right;">';
                        body_15 += value_diff;
                        body_15 += '</td>';
                        body_15 += '<td style="' + bg_color +
                            'vertical-align: middle; text-align: center;">';
                        body_15 += status;
                        body_15 += '</td>';
                        body_15 += '</tr>';
                    });
                    $('#body_15').append(body_15);

                    $('#table_15 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table1 = $('#table_15').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        "columnDefs": [{
                            "targets": [1],
                            "className": "text-left"
                        }],
                        "ordering": false,
                        "ordering": false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([2, 3, 4, 8])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#table_15 th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#table_15 th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table1.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_15 tfoot tr').appendTo('#table_15 thead');
                    // END TABLE SALES ORDER VS DRAFT SHIPMENT


                    // START TABLE SALES ORDER VS SHIPMENT
                    $('#table_2').DataTable().clear();
                    $('#table_2').DataTable().destroy();
                    $('#head_2').html("");
                    var head_2 = '<tr>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Desc.</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Category</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">HPL</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Destination</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">SO</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Schedule</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Diff</th>';
                    head_2 += '<th style="vertical-align: middle; text-align: center;">Status</th>';
                    head_2 += '</tr>';
                    $('#head_2').append(head_2);

                    $('#foot_2').html("");
                    var foot_2 = '';
                    foot_2 += '<tr>'
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '<th></th>';
                    foot_2 += '</tr>';
                    $('#foot_2').append(foot_2);

                    $('#body_2').html("");
                    var body_2 = '';
                    $.each(result.resume_so_shipment, function(key, value) {
                        body_2 += '<tr>';
                        body_2 += '<td style="vertical-align: middle; text-align: center;">';
                        body_2 += value.material_number;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: left;">';
                        body_2 += value.material_description;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: center;">';
                        body_2 += value.category;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: center;">';
                        body_2 += value.hpl;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: center;">';
                        body_2 += value.destination_shortname;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: right;">';
                        body_2 += value.so_qty;
                        body_2 += '</td>';
                        body_2 += '<td style="vertical-align: middle; text-align: right;">';
                        body_2 += value.shipment;
                        body_2 += '</td>';

                        var bg_color = '';
                        var status = '';
                        if (value.diff != 0) {
                            bg_color = 'background-color : #ffccff;';
                            status = 'NG';
                        } else {
                            bg_color = 'background-color : #ccffff;';
                            status = 'OK';
                        }

                        var value_diff = value.shipment - value.so_qty;
                        if (value_diff > 0) {
                            value_diff = '+' + value_diff;
                        }

                        body_2 += '<td style="' + bg_color + 'vertical-align: middle; text-align: right;">';
                        body_2 += value_diff;
                        body_2 += '</td>';
                        body_2 += '<td style="' + bg_color +
                            'vertical-align: middle; text-align: center;">';
                        body_2 += status;
                        body_2 += '</td>';
                        body_2 += '</tr>';
                    });
                    $('#body_2').append(body_2);

                    $('#table_2 tfoot th').each(function() {
                        var title = $(this).text();
                        $(this).html(
                            '<input class="tb_search" style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="3"/>');
                    });

                    var table1 = $('#table_2').DataTable({
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
                            }, {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }, {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            }]
                        },
                        "columnDefs": [{
                            "targets": [1],
                            "className": "text-left"
                        }],
                        "ordering": false,
                        "ordering": false,
                        'paging': true,
                        'lengthChange': true,
                        'searching': true,
                        'info': true,
                        'autoWidth': true,
                        "sPaginationType": "full_numbers",
                        "bJQueryUI": true,
                        "bAutoWidth": false,
                        "processing": true,
                        initComplete: function() {
                            this.api()
                                .columns([2, 3, 4, 8])
                                .every(function(dd) {
                                    var column = this;
                                    var theadname = $("#table_2 th").eq([dd]).text();
                                    var select = $(
                                            '<select><option value="" style="font-size:11px;">All</option></select>'
                                        )
                                        .appendTo($(column.footer()).empty())
                                        .on('change', function() {
                                            var val = $.fn.dataTable.util.escapeRegex($(this)
                                                .val());

                                            column.search(val ? '^' + val + '$' : '', true,
                                                    false)
                                                .draw();
                                        });
                                    column
                                        .data()
                                        .unique()
                                        .sort()
                                        .each(function(d, j) {
                                            var vals = d;
                                            if ($("#table_2 th").eq([dd]).text() ==
                                                'Category') {
                                                vals = d.split(' ')[0];
                                            }
                                            select.append(
                                                '<option style="font-size:11px;" value="' +
                                                d + '">' + vals + '</option>');
                                        });
                                });
                        },
                    });

                    table1.columns().every(function() {
                        var that = this;
                        $('.tb_search', this.footer()).on('keyup change', function() {
                            if (that.search() !== this.value) {
                                that
                                    .search(this.value)
                                    .draw();
                            }
                        });
                    });
                    $('#table_2 tfoot tr').appendTo('#table_2 thead');
                    // END TABLE SALES ORDER VS SHIPMENT





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

@stop
