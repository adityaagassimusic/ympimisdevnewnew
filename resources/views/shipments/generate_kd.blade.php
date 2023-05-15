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
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid rgb(211, 211, 211);
            padding-top: 0;
            padding-bottom: 0;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid rgb(211, 211, 211);
        }

        #loading,
        #error {
            display: none;
        }

        td {
            white-space: nowrap;
            padding-top: 0px !important;
            padding-bottom: 0px !important;
        }

        th {
            min-width: 20px !important;
        }

        div.dataTables_wrapper {
            width: 100%;
            margin: 0 auto;
        }
    </style>
@endsection

@section('header')
    <section class="content-header">
        <h1>
            {{ $page }}
        </h1>
        <ol class="breadcrumb">
            <li>
                @if (str_contains(Auth::user()->role_code, 'MIS') || str_contains(Auth::user()->role_code, 'PC'))
                    <a data-toggle="modal" data-target="#shipmentModal" class="btn btn-success btn-sm" style="color:white">
                        &nbsp;<i class="fa fa-refresh"></i>&nbsp;Generate&nbsp;&nbsp;
                    </a>
                @endif
                <a data-toggle="modal" data-target="#info" class="btn btn-info btn-sm" style="color:white">
                    &nbsp;&nbsp;&nbsp;&nbsp;<i class="fa fa-info-circle"></i>&nbsp;Roles&nbsp;&nbsp;&nbsp;&nbsp;
                </a>
            </li>
        </ol>
    </section>
@endsection


@section('content')

    <section class="content" style="font-size: 0.8vw;">
        <div id="loading"
            style="margin: 0px; padding: 0px; position: fixed; right: 0px; top: 0px; width: 100%; height: 100%; background-color: rgb(0,191,255); z-index: 30001; opacity: 0.8; display: none">
            <p style="position: absolute; color: white; top: 45%; left: 45%;">
                <span style="font-size: 5vw;"><i class="fa fa-spin fa-circle-o-notch"></i></span>
            </p>
        </div>

        <input type="text" name="category" id="category" value="{{ $category }}" hidden>

        <div class="row">
            <div class="col-xs-12" style="margin-bottom: 1%;">
                <div class="col-xs-2" style="padding: 0px;">
                    <div class="input-group date pull-right" style="text-align: center;">
                        <div class="input-group-addon bg-green">
                            <i class="fa fa-calendar"></i>
                        </div>
                        <input type="text" class="form-control monthpicker" name="month" id="month"
                            placeholder="Select Month" onchange="fillTableShipment()">
                    </div>
                </div>
            </div>

            <div class="col-xs-12" style="margin-bottom: 1%;">
                <div class="box box-solid" style="margin-bottom: 0px;">
                    <div class="box-body" style="overflow-x: auto;">
                        <table id="tableShip" class="table table-bordered" style="width: 100%; font-size: 12px;"
                            style="width: 100%;">
                            <thead id="headShipSch" style="background-color: rgba(126,86,134,.7);">
                                <th></th>
                            </thead>
                            <tbody id="bodyShipSch" style="vertical-align: middle;">
                                <th></th>
                            </tbody>
                            <tfoot id="footShipSch" style="vertical-align: middle;">
                                <th></th>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>

    </section>

    <div class="modal fade" id="info" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Shipment Roles</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <h2>YMMJ</h2>
                            YMMJ : Selasa - Kamis export Jumat<br>
                            YMMJ : Jumat - Senin export Selasa
                            <h2>XY</h2>
                            XY : Rabu - Jumat export Senin<br>
                            XY : Senin - Selasa export Rabu
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="shipmentModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Generate Shipment Schedule</h4>
                    Generate Shipment Schedule akan mengahapus schedule yang telah ada<br>
                    Dan akan diganti dengan hasil generate dari Production Schedule yang terbaru
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-xs-6 col-xs-offset-3">
                            <div class="col-xs-12">
                                <label>Select Month</label>
                                <div class="input-group date pull-right" style="text-align: center;">
                                    <div class="input-group-addon bg-green">
                                        <i class="fa fa-calendar"></i>
                                    </div>
                                    <input type="text" class="form-control monthpicker" name="shipment_month"
                                        id="shipment_month" placeholder="Select Month">
                                </div>

                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Work Center</label>
                                <select class="form-control select2" multiple="multiple" id='shipment_hpl'
                                    id='shipment_hpl' data-placeholder="Select Work Center" style="width: 100%;">
                                    @foreach ($locations as $location)
                                        <option value="{{ $location->hpl }}">KD - {{ $location->hpl }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="shipment()" class="btn btn-primary">Generate </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
            $('#adjustment_date').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });

            $('.monthpicker').datepicker({
                format: "yyyy-mm",
                startView: "months",
                minViewMode: "months",
                autoclose: true,
                todayHighlight: true
            });

            $('body').toggleClass("sidebar-collapse");

            $('.select2').select2();

            fillTableShipment();

        });

        $('#shipmentModal').on('hidden.bs.modal', function() {
            $('#shipment_month').val('');
            $("#shipment_hpl").val("");
            $("#shipment_hpl").trigger("change");
        });

        function fillTableShipment() {

            var data = {
                month: $('#month').val(),
                category: $('#category').val(),
            }

            $('#loading').show();

            $.get('{{ url('fetch/shipment_schedule_kd') }}', data, function(result, status, xhr) {

                if (result.month != $('#month').val()) {
                    $('#month').val(result.month);
                }

                $('#month_ship').text(result.month);
                $('#tableShip').DataTable().clear();
                $('#tableShip').DataTable().destroy();

                $('#headShipSch').html("");
                var ps = [];
                var tableHead = '<tr style=>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">SO</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DESC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">WORK CENTER</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DEST.</th>';
                for (var i = 0; i < result.dates.length; i++) {
                    tableHead += '<th style="vertical-align: middle; text-align: center;">' + result.dates[i]
                        .week_date.slice(8) + '</th>';
                    ps.push({
                        'date': result.dates[i].week_date,
                        'quantity': 0
                    });
                }
                tableHead += '<th style="vertical-align: middle; text-align: center;">PLAN SHIPMENT</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">REQUEST</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DIFF</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">STATUS</th>';
                tableHead += '</tr>';
                $('#headShipSch').append(tableHead);


                $('#footShipSch').html("");
                var tableFoot = '<tr style=>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                for (var i = 0; i < result.dates.length; i++) {
                    tableFoot += '<th></th>';
                }
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '</tr>';
                $('#footShipSch').append(tableFoot);


                $('#bodyShipSch').html("");
                var tableBody = '';

                for (var i = 0; i < result.sales_orders.length; i++) {

                    tableBody += '<tr>';
                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.sales_orders[i].sales_order;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.sales_orders[i].material_number;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: left;">';
                    tableBody += result.sales_orders[i].material_description;
                    tableBody += '</td>';


                    tableBody += '<td style="vertical-align: middle; text-align: left;">';
                    tableBody += result.sales_orders[i].hpl;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.sales_orders[i].destination_shortname;
                    tableBody += '</td>';


                    var sum_row = 0;
                    var diff = 0;

                    for (var j = 0; j < result.dates.length; j++) {
                        var inserted = false;

                        var css = 'padding: 0px !important; ';
                        if (result.dates[j].remark == 'H') {
                            css = 'background-color: gainsboro; color: #a32b1c;';
                        } else {
                            css = 'background-color: #ccffff;';
                        }

                        for (var k = 0; k < result.shipments.length; k++) {

                            if ((result.shipments[k].destination_code == result.sales_orders[i].destination_code) &&
                                (result.shipments[k].material_number == result.sales_orders[i].material_number) &&
                                (result.shipments[k].st_date == result.dates[j].week_date)) {

                                tableBody += '<td style="text-align: center; vertical-align: middle; ' + css + '">';
                                tableBody += result.shipments[k].quantity;
                                tableBody += '</td>';

                                sum_row += result.shipments[k].quantity;
                                inserted = true;
                            }
                        }

                        if (!inserted) {
                            var css = '';
                            if (result.dates[j].remark == 'H') {
                                css = 'background-color: gainsboro; color: #a32b1c;';
                            }

                            tableBody += '<td style="text-align: center; vertical-align: middle; ' + css + '">';
                            tableBody += '0';
                            tableBody += '</td>';
                        }

                    }
                    tableBody += '<td style="text-align: right; vertical-align: middle;">';
                    tableBody += sum_row;
                    tableBody += '</td>';


                    tableBody += '<td style="text-align: right; vertical-align: middle;">';
                    tableBody += result.sales_orders[i].quantity;
                    tableBody += '</td>';

                    diff = sum_row - result.sales_orders[i].quantity;

                    var css = '';
                    var status = '';
                    if (diff == 0) {
                        css += 'text-align: center; vertical-align: middle;';
                        status = 'OK';
                    } else {
                        css += 'text-align: center; vertical-align: middle; background-color: #ffccff;';
                        status = 'NG';
                    }
                    tableBody += '<td style="' + css + '">' + diff + '</td>';
                    tableBody += '<td style="' + css + '">' + status + '</td>';

                    tableBody += '</tr>';
                }
                $('#bodyShipSch').append(tableBody);


                var col = 0;
                $('#tableShip tfoot th').each(function() {
                    if ([0, 1, 2].includes(col)) {
                        var title = $(this).text();
                        $(this).html('<input style="text-align: center;" type="text" placeholder="Search ' +
                            title + '" size="4"/>');
                    }
                    col++

                });

                var table = $('#tableShip').DataTable({
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
                            },
                            {
                                extend: 'copy',
                                className: 'btn btn-success',
                                text: '<i class="fa fa-copy"></i> Copy',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            },
                            {
                                extend: 'excel',
                                className: 'btn btn-info',
                                text: '<i class="fa fa-file-excel-o"></i> Excel',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            },
                            {
                                extend: 'print',
                                className: 'btn btn-warning',
                                text: '<i class="fa fa-print"></i> Print',
                                exportOptions: {
                                    columns: ':not(.notexport)'
                                }
                            },
                        ]
                    },
                    'paging': true,
                    'lengthChange': true,
                    'searching': true,
                    'ordering': false,
                    'info': true,
                    'autoWidth': true,
                    'sPaginationType': 'full_numbers',
                    'bJQueryUI': true,
                    'bAutoWidth': false,
                    'processing': true,
                    'scrollCollapse': true,
                    initComplete: function() {
                        this.api()
                            .columns([3, 4, (result.dates.length + 9 - 1)])
                            .every(function(dd) {
                                var column = this;
                                var theadname = $("#example1 th").eq([dd]).text();
                                var select = $(
                                        '<select><option value="" style="font-size:11px;">All</option></select>'
                                    )
                                    .appendTo($(column.footer()).empty())
                                    .on('change', function() {
                                        var val = $.fn.dataTable.util.escapeRegex($(this)
                                            .val());

                                        column.search(val ? '^' + val + '$' : '', true, false)
                                            .draw();
                                    });
                                column
                                    .data()
                                    .unique()
                                    .sort()
                                    .each(function(d, j) {
                                        var vals = d;
                                        if ($("#example1 th").eq([dd]).text() == 'Category') {
                                            vals = d.split(' ')[0];
                                        }
                                        select.append(
                                            '<option style="font-size:11px;" value="' +
                                            d + '">' + vals + '</option>');
                                    });
                            });
                    }
                });

                table.columns().every(function() {
                    var that = this;

                    $('input', this.footer()).on('keyup change', function() {
                        if (that.search() !== this.value) {
                            that
                                .search(this.value)
                                .draw();
                        }
                    });
                });

                $('#tableShip tfoot tr').appendTo('#tableShip thead');

                $('#loading').hide();

            });
        }

        function shipment() {
            var month = $('#shipment_month').val();
            var hpl = $('#shipment_hpl').val();

            var data = {
                month: month,
                hpl: hpl,
            }

            if (hpl.length < 1 || month == '') {
                openErrorGritter("Error", "Select Month & Work Center");
                return false;
            }
            $('#loading').show();

            $.post('{{ url('generate/shipment_schedule') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#shipment_month').val('');
                    $("#shipment_hpl").val("");
                    $("#shipment_hpl").trigger("change");

                    $('#shipmentModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter("Success", "Generate shipment schedule success");

                } else {
                    $('#loading').hide();
                    openErrorGritter("Error", "Generate shipment schedule failed");
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
