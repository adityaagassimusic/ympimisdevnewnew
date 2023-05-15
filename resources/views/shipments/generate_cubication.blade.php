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
                        &nbsp;<i class="fa fa-refresh"></i>&nbsp;Calculate&nbsp;&nbsp;
                    </a>
                    <a data-toggle="modal" data-target="#exportModal" class="btn btn-success btn-sm" style="color:white">
                        &nbsp;<i class="fa fa-upload"></i>&nbsp;Export Draft&nbsp;&nbsp;
                    </a>
                @endif
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
                        <div id="container1" style="height: 40vh;"></div>
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
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="calculate()" class="btn btn-primary">Calculate </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="exportModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel"
        aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <input type="hidden" value="{{ csrf_token() }}" name="_token" />
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                    <h4 class="modal-title" id="myModalLabel">Export Draft to Actual Shipment Schedule</h4>
                    Draft shipment yang sudah diekspor tidak bisa diekspor kembali
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
                                    <input type="text" class="form-control monthpicker" name="export_month"
                                        id="export_month" placeholder="Select Month">
                                </div>
                            </div>
                            <div class="col-xs-12" style="margin-top: 3%;">
                                <label>Select Destination</label>
                                <select class="form-control select2" multiple="multiple" id='export_destination'
                                    id='export_destination' data-placeholder="Select Destination" style="width: 100%;">
                                    @foreach ($destinations as $destination)
                                        <option value="{{ $destination->destination_code }}">
                                            {{ $destination->destination_shortname }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="row" style="margin-top: 7%; margin-right: 2%;">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button onclick="exportDraft()" class="btn btn-primary">Export Draft </button>
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
    <script src="{{ url('js/highcharts.js') }}"></script>
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
        });

        $('#exportModal').on('hidden.bs.modal', function() {
            $('#export_month').val('');
            $("#export_destination").val("");
            $("#export_destination").trigger("change");
        });

        function exportDraft(params) {

            if ($('#export_month').val() == '') {
                openErrorGritter("Error", "Select Month");
                return false;
            }

            if ($('#export_destination').val() == '') {
                openErrorGritter("Error", "Select Destination");
                return false;
            }

            var data = {
                month: $('#export_month').val(),
                destination_code: $('#export_destination').val(),
            }

            $('#loading').show();

            $.post('{{ url('export/shipment_cubication') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#exportModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter('Success!', result.message);
                } else {
                    $('#loading').hide();
                    openErrorGritter('Error!', result.message);
                }
            });

        }

        function fillTableShipment() {

            var data = {
                month: $('#month').val(),
                category: $('#category').val(),
            }

            $('#loading').show();

            $.get('{{ url('fetch/generate_shipment_cubication') }}', data, function(result, status, xhr) {

                if (result.month != $('#month').val()) {
                    $('#month').val(result.month);
                }
                $('#month_ship').text(result.month);


                // START DATATABLE
                $('#tableShip').DataTable().clear();
                $('#tableShip').DataTable().destroy();

                $('#headShipSch').html("");
                var ps = [];
                var tableHead = '<tr>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">SO</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DEST.</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">REMARK</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">GMC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">DESC</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">WORK CENTER</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">ST DATE</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">PLAN BL</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">QUANTITY</th>';
                tableHead += '<th style="vertical-align: middle; text-align: center;">VOLUME (m<sup>3</sup>)</th>';
                tableHead += '</tr>';
                $('#headShipSch').append(tableHead);


                $('#footShipSch').html("");
                var tableFoot = '<tr>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '<th></th>';
                tableFoot += '</tr>';
                $('#footShipSch').append(tableFoot);


                $('#bodyShipSch').html("");
                var tableBody = '';

                for (var i = 0; i < result.shipments.length; i++) {

                    tableBody += '<tr>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].sales_order;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].destination_shortname;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].remark;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].material_number;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: left;">';
                    tableBody += result.shipments[i].material_description;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: left;">';
                    tableBody += result.shipments[i].hpl;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].st_date;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: center;">';
                    tableBody += result.shipments[i].bl_date;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: right;">';
                    tableBody += result.shipments[i].quantity;
                    tableBody += '</td>';

                    tableBody += '<td style="vertical-align: middle; text-align: right;">';
                    tableBody += result.shipments[i].volume;
                    tableBody += '</td>';

                    tableBody += '</tr>';
                }
                $('#bodyShipSch').append(tableBody);


                var col = 0;
                $('#tableShip tfoot th').each(function() {
                    if ([0, 3, 4].includes(col)) {
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
                            .columns([1, 2, 5, 6, 7])
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
                // END DATATABLE


                // START HIGHCHART
                var xCategories = [];

                var xy = [];
                var itm_lh = [];
                var itm_hb = [];
                var yemi = [];
                var ymid = [];
                var ycj_ymj = [];
                var ymmj = [];
                var jh = [];
                var itm_korea = [];
                var itm_siam = [];
                var itm_yca = [];

                for (let i = 0; i < result.dates.length; i++) {
                    xCategories.push(result.dates[i].week_date);

                    var val_xy = null;
                    var val_itm_lh = null;
                    var val_itm_hb = null;
                    var val_yemi = null;
                    var val_ymid = null;
                    var val_ycj_ymj = null;
                    var val_ymmj = null;
                    var val_jh = null;
                    var val_itm_korea = null;
                    var val_itm_siam = null;
                    var val_itm_yca = null;

                    for (let j = 0; j < result.shipments.length; j++) {
                        if (result.dates[i].week_date == result.shipments[j].st_date) {
                            if (result.shipments[j].remark == 'XY') {
                                val_xy += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'ITM-LH') {
                                val_itm_lh += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'ITM-HB') {
                                val_itm_hb += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'YEMI') {
                                val_yemi += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'YMID') {
                                val_ymid += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'YCJ & YMJ') {
                                val_ycj_ymj += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'YMMJ') {
                                val_ymmj += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'JH') {
                                val_jh += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'ITM-KOREA') {
                                val_itm_korea += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'ITM-SIAM') {
                                val_itm_siam += result.shipments[j].volume;
                            } else if (result.shipments[j].remark == 'ITM-YCA') {
                                val_itm_yca += result.shipments[j].volume;
                            }
                        }
                    }

                    xy.push(val_xy);
                    itm_lh.push(val_itm_lh);
                    itm_hb.push(val_itm_hb);
                    yemi.push(val_yemi);
                    ymid.push(val_ymid);
                    ycj_ymj.push(val_ycj_ymj);
                    ymmj.push(val_ymmj);
                    jh.push(val_jh);
                    itm_korea.push(val_itm_korea);
                    itm_siam.push(val_itm_siam);
                    itm_yca.push(val_itm_yca);
                }

                Highcharts.chart('container1', {
                    chart: {
                        type: 'column',
                        options3d: {
                            enabled: true,
                            alpha: 0,
                            beta: 0,
                            viewDistance: 20,
                            depth: 80
                        },
                        backgroundColor: null
                    },
                    title: {
                        text: '',
                        style: {
                            fontSize: '26px',
                            fontWeight: 'bold'
                        }
                    },
                    subtitle: {
                        text: '',
                        style: {
                            fontSize: '1vw',
                            fontWeight: 'bold'
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    legend: {
                        enabled: true,
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },
                    xAxis: {
                        categories: xCategories,
                    },
                    yAxis: {
                        title: {
                            text: 'Volume (m&sup3)'
                        },
                        stackLabels: {
                            enabled: true,
                            style: {
                                color: (Highcharts.theme && Highcharts.theme.textColor) || 'black'
                            },
                            formatter: function() {
                                return this.total.toFixed(2);
                            }
                        },
                    },
                    tooltip: {
                        headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                        pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                            '<td style="padding:0"><b>{point.y:.2f} m<sup>3</sup></b></td></tr>',
                        footerFormat: '</table>',
                        shared: true,
                        useHTML: true
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.05,
                            groupPadding: 0.1,
                            borderWidth: 0
                        },
                        series: {
                            borderColor: '#303030',
                            borderWidth: 2,
                            cursor: 'pointer',
                            stacking: 'normal',
                        },
                    },
                    series: [{
                        name: 'XY',
                        data: xy,
                        color: '#c8ffa0'
                    }, {
                        name: 'ITM-LH',
                        data: itm_lh,
                        color: '#5e76ff'
                    }, {
                        name: 'ITM-HB',
                        data: itm_hb,
                        color: '#ffff54'
                    }, {
                        name: 'YEMI',
                        data: yemi,
                        color: '#9f9f9f'
                    }, {
                        name: 'YMID',
                        data: ymid,
                        color: '#ff2f2f'
                    }, {
                        name: 'YCJ & YMJ',
                        data: ycj_ymj,
                        color: '#ffa24a'
                    }, {
                        name: 'YMMJ',
                        data: ymmj,
                        color: '#6fe8e7'
                    }, {
                        name: 'JH',
                        data: jh,
                        color: '#ccaa35'
                    }, {
                        name: 'ITM-KOREA',
                        data: itm_korea,
                        color: '#44a9a8'
                    }, {
                        name: 'ITM-SIAM',
                        data: itm_siam,
                        color: '#a488aa'
                    }, {
                        name: 'ITM-YCA',
                        data: itm_yca,
                        color: '#ffa7d5'
                    }]
                });


                // END HIGHCHART


                $('#loading').hide();

            });
        }

        function calculate() {
            var month = $('#shipment_month').val();

            var data = {
                month: month,
            }

            if (month == '') {
                openErrorGritter("Error", "Select Month");
                return false;
            }
            $('#loading').show();

            $.post('{{ url('generate/shipment_cubication') }}', data, function(result, status, xhr) {
                if (result.status) {

                    $('#shipment_month').val('');

                    $('#shipmentModal').modal('hide');
                    $('#loading').hide();
                    openSuccessGritter("Success", "Generate shipment schedule success");

                } else {
                    $('#loading').hide();
                    openErrorGritter("Error", result.message);
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
