@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        table.table-bordered {
            border: 1px solid black;
        }

        table.table-bordered>thead>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tbody>tr>td {
            border: 1px solid black;
            vertical-align: middle;
        }

        table.table-bordered>tfoot>tr>th {
            border: 1px solid black;
            vertical-align: middle;
        }

        img {
            max-width: 100%
        }

        #loading,
        #error {
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
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-8">
                                        <span style="font-weight: bold; font-size: 30px;">REPAIR ROOM (WELDING)</span>
                                    </div>
                                    <div class="col-xs-4">
                                        <a class="btn btn-warning pull-right" style="width: 100%;"
                                            href="{{ url('index/transaction/repair_room?location=Welding') }}">Input
                                            Welding</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="containerWelding"></div>
                            </div>
                            <div class="col-xs-4">
                                <div class="small-box bg-aqua" style="border: 1px solid black;">
                                    <div class="inner">
                                        <h3 id="totalWelding">PCs</h3>
                                        <p>Total Quantity</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-cube"></i>
                                    </div>
                                    <a href="javascript:void(0)" onclick="modalDetail('Stock','Welding')"
                                        class="small-box-footer">Stock info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                                <div class="small-box bg-green" style="border: 1px solid black;">
                                    <div class="inner">
                                        <h3 id="amountWelding">$</h3>
                                        <p>Total Amount</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-social-usd"></i>
                                    </div>
                                    <a href="javascript:void(0)" onclick="modalDetail('Log','Welding')"
                                        class="small-box-footer">Log info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerWelding3"></div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerWelding2" style="height: 500px;"></div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerWelding4" style="height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-6">
                <div class="box box-solid">
                    <div class="box-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="row">
                                    <div class="col-xs-8">
                                        <span style="font-weight: bold; font-size: 30px;">REPAIR ROOM (MIDDLE)</span>
                                    </div>
                                    <div class="col-xs-4">
                                        <a class="btn btn-warning pull-right" style="width: 100%;"
                                            href="{{ url('index/transaction/repair_room?location=Plating') }}">Input Surface
                                            Middle</a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-xs-8">
                                <div id="containerMiddle"></div>
                            </div>
                            <div class="col-xs-4">
                                <div class="small-box bg-aqua" style="border: 1px solid black;">
                                    <div class="inner">
                                        <h3 id="totalMiddle">PCs</h3>
                                        <p>Total Quantity</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-cube"></i>
                                    </div>
                                    <a href="javascript:void(0)" onclick="modalDetail('Stock','Plating')"
                                        class="small-box-footer">Stock info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                                <div class="small-box bg-green" style="border: 1px solid black;">
                                    <div class="inner">
                                        <h3 id="amountMiddle">$</h3>
                                        <p>Total Amount</p>
                                    </div>
                                    <div class="icon">
                                        <i class="ion ion-social-usd"></i>
                                    </div>
                                    <a href="javascript:void(0)" onclick="modalDetail('Log','Plating')"
                                        class="small-box-footer">Log info <i class="fa fa-arrow-circle-right"></i></a>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerMiddle3"></div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerMiddle2" style="height: 500px;"></div>
                            </div>
                            <div class="col-xs-12">
                                <div id="containerMiddle4" style="height: 500px;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalStock">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <table id="tableStockDetail" class="table table-bordered table-hover">
                        <thead style="background-color: #00c0ef;">
                            <tr>
                                <th style="width: 0.1%; text-align: center;">HPL</th>
                                <th style="width: 0.1%; text-align: center;">Material</th>
                                <th style="width: 6%; text-align: left;">Description</th>
                                <th style="width: 0.1%; text-align: right;">Quantity<br>(Pcs)</th>
                                <th style="width: 0.1%; text-align: right;">Price<br>(USD)</th>
                                <th style="width: 0.1%; text-align: right;">Amount<br>(USD)</th>
                            </tr>
                        </thead>
                        <tbody id="tableStockDetailBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalBalance">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <table id="tableBalance" class="table table-bordered table-hover">
                        <thead style="background-color: #00c0ef;">
                            <tr>
                                <th style="width: 0.1%; text-align: center;">HPL</th>
                                <th style="width: 0.1%; text-align: right;">Quantity In<br>(Pcs)</th>
                                <th style="width: 0.1%; text-align: right;">Quantity Out<br>(Pcs)</th>
                                <th style="width: 0.1%; text-align: right;">Amount In<br>(USD)</th>
                                <th style="width: 0.1%; text-align: right;">Amount Out<br>(USD)</th>
                            </tr>
                        </thead>
                        <tbody id="tableBalanceBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalLog">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-body">
                    <div class="col-md-6 col-md-offset-3">
                        <div class="form-group">
                            <label for="exampleInputEmail1" class="col-sm-2 control-label">Date</label>
                            <div class="col-xs-10">
                                <input type="text" class="form-control datepicker" id="filterDate" name="filterDate"
                                    placeholder="Select Date">
                            </div>
                        </div>
                        <div class="col-xs-12" style="margin-top: 15px; margin-bottom: 15px;">
                            <button class="btn btn-success pull-right" onclick="fetchLog()">Search</button>
                        </div>
                    </div>
                    <table id="tableLogDetail" class="table table-bordered table-hover">
                        <thead style="">
                            <tr>
                                <th style="width: 1%; text-align: center;">Material</th>
                                <th style="width: 3%; text-align: left;">Description</th>
                                <th style="width: 1%; text-align: right;">Quantity</th>
                                <th style="width: 1%; text-align: right;">Price<br>(USD)</th>
                                <th style="width: 1%; text-align: right;">Amount<br>(USD)</th>
                                <th style="width: 2%; text-align: left;">PIC</th>
                                <th style="width: 1%; text-align: center;">Remark</th>
                                <th style="width: 1%; text-align: center;">Date</th>
                            </tr>
                        </thead>
                        <tbody id="tableLogDetailBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script src="{{ url('js/jquery.gritter.min.js') }}"></script>
    <script src="{{ url('bower_components/moment/min/moment.min.js') }}"></script>
    <script src="{{ url('bower_components/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script src="{{ url('js/dataTables.buttons.min.js') }}"></script>
    <script src="{{ url('js/buttons.flash.min.js') }}"></script>
    <script src="{{ url('js/jszip.min.js') }}"></script>
    <script src="{{ url('js/vfs_fonts.js') }}"></script>
    <script src="{{ url('js/buttons.html5.min.js') }}"></script>
    <script src="{{ url('js/buttons.print.min.js') }}"></script>
    <script src="{{ url('js/highstock.js') }}"></script>
    {{-- <script src="{{ url('js/pareto.js') }}"></script> --}}
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            fetchData();
            $('#filterDate').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        });

        var audio_error = new Audio('{{ url('sounds/error.mp3') }}');
        var audio_ok = new Audio('{{ url('sounds/sukses.mp3') }}');
        var inventories = [];
        var logs = [];
        var all_logs = [];
        var records = [];
        var sum_logs = [];
        var sum_inventories = [];
        var resume_logs = [];

        function modalBalance(loc, cat, name) {
            var stock_date = cat;

            var hpl = "";
            if (name == 'Stock SX') {
                hpl = 'SX';
            } else if (name == 'Stock CL') {
                hpl = 'CL';
            } else if (name == 'Stock FL') {
                hpl = 'FL';
            }

            var location = "";
            if (loc == 'Welding') {
                location = 'Welding';
            } else if (loc == 'Middle') {
                location = 'Plating';
            }

            var array = all_logs;
            var result = [];

            array.reduce(function(res, value) {
                if (!res[value.category + value.location + value.hpl]) {
                    res[value.category + value.location + value.hpl] = {
                        category: value.category,
                        location: value.location,
                        hpl: value.hpl,
                        quantity_in: 0,
                        quantity_out: 0,
                        amount_in: 0,
                        amount_out: 0
                    };
                    result.push(res[value.category + value.location + value.hpl])
                }
                if (value.quantity > 0) {
                    res[value.category + value.location + value.hpl].quantity_in += value.quantity;
                    res[value.category + value.location + value.hpl].amount_in += value.amount;
                }
                if (value.quantity < 0) {
                    res[value.category + value.location + value.hpl].quantity_out += value.quantity;
                    res[value.category + value.location + value.hpl].amount_out += value.amount;
                }
                return res;
            }, {});

            $('#tableBalanceBody').html("");
            var tableBalance = "";

            $.each(result, function(key, value) {
                if (value.location == location) {
                    if (value.category == stock_date) {
                        tableBalance += '<tr>';
                        tableBalance += '<td style="width: 0.1%; text-align: center;">' + value.hpl + '</td>';
                        tableBalance += '<td style="width: 0.1%; text-align: right;">' + value.quantity_in +
                            '</td>';
                        tableBalance += '<td style="width: 0.1%; text-align: right;">' + value.quantity_out +
                            '</td>';
                        tableBalance += '<td style="width: 0.1%; text-align: right;">' + value.amount_in.toFixed(
                            2) + '</td>';
                        tableBalance += '<td style="width: 0.1%; text-align: right;">' + value.amount_out.toFixed(
                            2) + '</td>';
                        tableBalance += '</tr>';
                    }
                }
            });

            $('#tableBalanceBody').append(tableBalance);

            $('#modalBalance').modal('show');
        }

        function modalStock(loc, cat, name) {
            var hpl = "";
            if (cat == 'Saxophone') {
                hpl = 'SX';
            } else if (cat == 'Clarinet') {
                hpl = 'CL';
            } else if (cat == 'Flute') {
                hpl = 'FL';
            }

            var location = "";
            if (loc == 'Welding') {
                location = 'Welding';
            } else if (loc == 'Middle') {
                location = 'Plating';
            }

            $('#tableStockDetail').DataTable().clear();
            $('#tableStockDetail').DataTable().destroy();
            $('#tableStockDetailBody').html("");
            var tableStockDetail = "";
            $.each(inventories, function(key, value) {
                if (value.location == location) {
                    if (value.hpl == hpl) {
                        tableStockDetail += '<tr>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: center;">' + value.hpl + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: center;">' + value
                            .material_number + '</td>';
                        tableStockDetail += '<td style="width: 6%; text-align: left;">' + value
                            .material_description + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.quantity +
                            '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.price + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.amount + '</td>';
                        tableStockDetail += '</tr>';
                    }
                }
            });
            $('#tableStockDetailBody').append(tableStockDetail);
            $('#tableStockDetail').DataTable({
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
                'paging': false,
                'lengthChange': true,
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

            $('#modalStock').modal('show')
        }

        function fetchLog() {
            $('#loading').show();
            var filterDate = $('#filterDate').val();
            var category = "log";

            var data = {
                filterDate: filterDate,
                category: category
            }
            $.get('{{ url('fetch/transaction/repair_room_log') }}', data, function(result, status, xhr) {
                logs = result.logs;
                $('#tableLogDetail').DataTable().clear();
                $('#tableLogDetail').DataTable().destroy();
                $('#tableLogDetailBody').html("");
                var tableLogDetail = "";
                $.each(logs, function(key, value) {
                    tableLogDetail += '<tr>';
                    tableLogDetail += '<td style="width: 1%; text-align: center;">' + value
                        .material_number + '</td>';
                    tableLogDetail += '<td style="width: 3%; text-align: left;">' + value
                        .material_description + '</td>';
                    tableLogDetail += '<td style="width: 1%; text-align: right;">' + value.quantity +
                        '</td>';
                    tableLogDetail += '<td style="width: 1%; text-align: right;">' + value.price + '</td>';
                    tableLogDetail += '<td style="width: 1%; text-align: right;">' + value.amount + '</td>';
                    tableLogDetail += '<td style="width: 2%; text-align: left;">' + value.created_by_name +
                        '</td>';
                    if (value.remark != null) {
                        tableLogDetail += '<td style="width: 1%; text-align: center;">' + value.remark +
                            '</td>';
                    } else {
                        tableLogDetail += '<td style="width: 1%; text-align: center;"></td>';
                    }
                    tableLogDetail += '<td style="width: 1%; text-align: center;">' + value.created_at +
                        '</td>';
                    tableLogDetail += '</tr>';
                });
                $('#tableLogDetailBody').append(tableLogDetail);
                $('#tableLogDetail').DataTable({
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
                    'paging': false,
                    'lengthChange': true,
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
            });
        }

        function modalDetail(cat, location) {
            if (cat == 'Log') {
                $('#filterDate').daterangepicker({
                    locale: {
                        format: 'YYYY-MM-DD'
                    },
                    startDate: '{{ date('Y-m-01') }}',
                    endDate: '{{ date('Y-m-t') }}'
                });
                $('#modalLog').modal('show');
            }
            if (cat == 'Stock') {
                $('#tableStockDetail').DataTable().clear();
                $('#tableStockDetail').DataTable().destroy();
                $('#tableStockDetailBody').html("");
                var tableStockDetail = "";
                $.each(inventories, function(key, value) {
                    if (value.location == location) {
                        tableStockDetail += '<tr>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: center;">' + value.hpl + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: center;">' + value
                            .material_number + '</td>';
                        tableStockDetail += '<td style="width: 6%; text-align: left;">' + value
                            .material_description + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.quantity +
                            '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.price + '</td>';
                        tableStockDetail += '<td style="width: 0.1%; text-align: right;">' + value.amount + '</td>';
                        tableStockDetail += '</tr>';
                    }
                });
                $('#tableStockDetailBody').append(tableStockDetail);
                $('#tableStockDetail').DataTable({
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
                    'paging': false,
                    'lengthChange': true,
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
                $('#modalStock').modal('show');
            }
        }

        function fetchData() {
            $('#loading').show();
            var data = {

            }

            $.get('{{ url('fetch/transaction/repair_room_monitoring') }}', data, function(result, status, xhr) {
                if (result.status) {
                    var array2 = result.logs;
                    var array3 = result.records;
                    var array4 = result.record_mons;
                    inventories = result.inventories;
                    all_logs = result.logs;
                    records = result.records;
                    resume_logs = result.resume_logs;

                    var amount_welding = 0;
                    var total_welding = 0;
                    var amount_middle = 0;
                    var total_middle = 0;

                    for (var i = 0; i < inventories.length; i++) {
                        if (inventories[i].location == 'Welding') {
                            amount_welding += inventories[i].amount;
                            total_welding += inventories[i].quantity;
                        }
                        if (inventories[i].location == 'Plating') {
                            amount_middle += inventories[i].amount;
                            total_middle += inventories[i].quantity;
                        }
                    }

                    var array = result.inventories;
                    var result = [];
                    array.reduce(function(res, value) {
                        if (!res[value.hpl + value.location]) {
                            res[value.hpl + value.location] = {
                                hpl: value.hpl,
                                location: value.location,
                                quantity: 0,
                                amount: 0
                            };
                            result.push(res[value.hpl + value.location])
                        }
                        res[value.hpl + value.location].quantity += value.quantity;
                        res[value.hpl + value.location].amount += value.amount;
                        return res;
                    }, {});

                    var categories_welding = [];
                    var series_quantity_welding = [];
                    var series_amount_welding = [];

                    var categories_middle = [];
                    var series_quantity_middle = [];
                    var series_amount_middle = [];

                    $.each(result, function(key, value) {
                        var hpl = "";
                        if (value.hpl == 'CL') {
                            hpl = 'Clarinet';
                        }
                        if (value.hpl == 'FL') {
                            hpl = 'Flute';
                        }
                        if (value.hpl == 'SX') {
                            hpl = 'Saxophone';
                        }
                        if (value.location == 'Welding') {
                            categories_welding.push(hpl);
                            series_quantity_welding.push(value.quantity);
                            series_amount_welding.push(value.amount);
                        }

                        if (value.location == 'Plating') {
                            categories_middle.push(hpl);
                            series_quantity_middle.push(value.quantity);
                            series_amount_middle.push(value.amount);
                        }
                    });

                    $('#totalWelding').text(total_welding.toLocaleString() + ' PCs');
                    $('#amountWelding').text('$ ' + amount_welding.toLocaleString(1));
                    $('#totalMiddle').text(total_middle.toLocaleString() + ' PCs');
                    $('#amountMiddle').text('$ ' + amount_middle.toLocaleString(1));

                    var result2 = [];
                    array2.reduce(function(res, value) {
                        if (!res[value.category + value.location]) {
                            res[value.category + value.location] = {
                                category: value.category,
                                location: value.location,
                                material_in: 0,
                                material_out: 0
                            };
                            result2.push(res[value.category + value.location])
                        }
                        if (value.quantity > 0) {
                            res[value.category + value.location].material_in += value.quantity;
                        }
                        if (value.quantity < 0) {
                            res[value.category + value.location].material_out += value.quantity;
                        }
                        return res;
                    }, {});

                    var series_welding_in = [];
                    var series_welding_out = [];
                    var series_middle_in = [];
                    var series_middle_out = [];

                    $.each(result2, function(key, value) {
                        if (value.location == 'Welding') {
                            series_welding_in.push([Date.parse(value.category), Math.abs(value
                                .material_in)]);
                            series_welding_out.push([Date.parse(value.category), Math.abs(value
                                .material_out)]);
                        }
                        if (value.location == 'Plating') {
                            series_middle_in.push([Date.parse(value.category), Math.abs(value
                                .material_in)]);
                            series_middle_out.push([Date.parse(value.category), Math.abs(value
                                .material_out)]);
                        }
                    });

                    var result3 = [];
                    array3.reduce(function(res, value) {
                        if (!res[value.stock_date + value.location]) {
                            res[value.stock_date + value.location] = {
                                stock_date: value.stock_date,
                                location: value.location,
                                stock_cl: 0,
                                stock_fl: 0,
                                stock_sx: 0,
                                stock: 0,
                                amount_cl: 0,
                                amount_fl: 0,
                                amount_sx: 0,
                                amount: 0
                            };
                            result3.push(res[value.stock_date + value.location])
                        }
                        if (value.hpl == 'CL') {
                            res[value.stock_date + value.location].stock_cl += value.quantity;
                            res[value.stock_date + value.location].amount_cl += value.amount;
                        }
                        if (value.hpl == 'FL') {
                            res[value.stock_date + value.location].stock_fl += value.quantity;
                            res[value.stock_date + value.location].amount_fl += value.amount;
                        }
                        if (value.hpl == 'SX') {
                            res[value.stock_date + value.location].stock_sx += value.quantity;
                            res[value.stock_date + value.location].amount_sx += value.amount;
                        }
                        res[value.stock_date + value.location].stock += value.quantity;
                        res[value.stock_date + value.location].amount += value.amount;
                        return res;
                    }, {});

                    var series_welding_stock_cl = [];
                    var series_welding_stock_fl = [];
                    var series_welding_stock_sx = [];
                    var series_middle_stock_cl = [];
                    var series_middle_stock_fl = [];
                    var series_middle_stock_sx = [];

                    var series_welding_amount_cl = [];
                    var series_welding_amount_fl = [];
                    var series_welding_amount_sx = [];
                    var series_middle_amount_cl = [];
                    var series_middle_amount_fl = [];
                    var series_middle_amount_sx = [];

                    var series_welding_amount_total = [];
                    var series_middle_amount_total = [];

                    $.each(result3, function(key, value) {
                        if (value.location == 'Welding') {
                            series_welding_stock_cl.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_cl)]);
                            series_welding_stock_fl.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_fl)]);
                            series_welding_stock_sx.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_sx)]);

                            series_welding_amount_cl.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_cl)]);
                            series_welding_amount_fl.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_fl)]);
                            series_welding_amount_sx.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_sx)]);

                            series_welding_amount_total.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_sx + value.amount_fl + value.amount_cl)]);
                        }
                        if (value.location == 'Plating') {
                            series_middle_stock_cl.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_cl)]);
                            series_middle_stock_fl.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_fl)]);
                            series_middle_stock_sx.push([Date.parse(value.stock_date), Math.abs(value
                                .stock_sx)]);

                            series_middle_amount_cl.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_cl)]);
                            series_middle_amount_fl.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_fl)]);
                            series_middle_amount_sx.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_sx)]);

                            series_middle_amount_total.push([Date.parse(value.stock_date), Math.abs(value
                                .amount_sx + value.amount_fl + value.amount_cl)]);
                        }
                    });

                    var result4 = [];
                    array4.reduce(function(res, value) {
                        if (!res[value.stock_date + value.location]) {
                            res[value.stock_date + value.location] = {
                                stock_date: value.stock_date,
                                location: value.location,
                                stock_cl: 0,
                                stock_fl: 0,
                                stock_sx: 0,
                                stock: 0,
                                amount_cl: 0,
                                amount_fl: 0,
                                amount_sx: 0,
                                amount: 0
                            };
                            result4.push(res[value.stock_date + value.location])
                        }
                        if (value.hpl == 'CL') {
                            res[value.stock_date + value.location].stock_cl += value.quantity;
                            res[value.stock_date + value.location].amount_cl += value.amount;
                        }
                        if (value.hpl == 'FL') {
                            res[value.stock_date + value.location].stock_fl += value.quantity;
                            res[value.stock_date + value.location].amount_fl += value.amount;
                        }
                        if (value.hpl == 'SX') {
                            res[value.stock_date + value.location].stock_sx += value.quantity;
                            res[value.stock_date + value.location].amount_sx += value.amount;
                        }
                        res[value.stock_date + value.location].stock += value.quantity;
                        res[value.stock_date + value.location].amount += value.amount;
                        return res;
                    }, {});

                    var series_welding_stock_mon_cl = [];
                    var series_welding_stock_mon_fl = [];
                    var series_welding_stock_mon_sx = [];
                    var series_middle_stock_mon_cl = [];
                    var series_middle_stock_mon_fl = [];
                    var series_middle_stock_mon_sx = [];

                    var series_welding_amount_mon_cl = [];
                    var series_welding_amount_mon_fl = [];
                    var series_welding_amount_mon_sx = [];
                    var series_middle_amount_mon_cl = [];
                    var series_middle_amount_mon_fl = [];
                    var series_middle_amount_mon_sx = [];

                    var series_welding_amount_mon_total = [];
                    var series_middle_amount_mon_total = [];

                    var categories_welding_mon = [];
                    var categories_middle_mon = [];

                    $.each(result4, function(key, value) {
                        if (value.location == 'Welding') {
                            categories_welding_mon.push(value.stock_date);
                            series_welding_stock_mon_cl.push(Math.abs(value.stock_cl));
                            series_welding_stock_mon_fl.push(Math.abs(value.stock_fl));
                            series_welding_stock_mon_sx.push(Math.abs(value.stock_sx));

                            series_welding_amount_mon_cl.push(Math.abs(value.amount_cl));
                            series_welding_amount_mon_fl.push(Math.abs(value.amount_fl));
                            series_welding_amount_mon_sx.push(Math.abs(value.amount_sx));

                            series_welding_amount_mon_total.push(Math.abs(value.amount_sx + value
                                .amount_fl + value.amount_cl));
                        }
                        if (value.location == 'Plating') {
                            categories_middle_mon.push(value.stock_date);
                            series_middle_stock_mon_cl.push(Math.abs(value.stock_cl));
                            series_middle_stock_mon_fl.push(Math.abs(value.stock_fl));
                            series_middle_stock_mon_sx.push(Math.abs(value.stock_sx));

                            series_middle_amount_mon_cl.push(Math.abs(value.amount_cl));
                            series_middle_amount_mon_fl.push(Math.abs(value.amount_fl));
                            series_middle_amount_mon_sx.push(Math.abs(value.amount_sx));

                            series_middle_amount_mon_total.push(Math.abs(value.amount_sx + value.amount_fl +
                                value.amount_cl));
                        }
                    });

                    Highcharts.chart('containerWelding', {
                        chart: {
                            type: 'column',
                            height: '300px'
                        },
                        title: {
                            text: 'Realtime Stock'
                        },
                        xAxis: {
                            categories: categories_welding
                        },
                        yAxis: [{
                            title: {
                                text: 'Quantity (PCs)'
                            },
                            labels: {
                                formatter: function() {
                                    if (this.value > 1000) return Highcharts.numberFormat(this
                                        .value / 1000, 1) + "K";
                                    return Highcharts.numberFormat(this.value, 0);
                                }
                            }
                        }, {
                            title: {
                                text: 'Amount (USD)'
                            },
                            opposite: true
                        }],
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalStock('Welding', this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Quantity',
                            data: series_quantity_welding,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    if (this.y > 1000) return Highcharts.numberFormat(this.y /
                                        1000, 1) + "K";
                                    return Highcharts.numberFormat(this.y, 0);
                                }
                            },
                            color: '#00add7'
                        }, {
                            name: 'Amount',
                            yAxis: 1,
                            data: series_amount_welding,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    if (this.y > 1000) return "$" + Highcharts.numberFormat(this
                                        .y / 1000, 1) + "K";
                                    return "$" + Highcharts.numberFormat(this.y, 0);
                                }
                            },
                            color: '#009551'
                        }]
                    });

                    Highcharts.chart('containerMiddle', {
                        chart: {
                            type: 'column',
                            height: '300px'
                        },
                        title: {
                            text: 'Realtime Stock'
                        },
                        xAxis: {
                            categories: categories_middle
                        },
                        yAxis: [{
                            title: {
                                text: 'Quantity (PCs)'
                            },
                            labels: {
                                formatter: function() {
                                    if (this.value > 1000) return Highcharts.numberFormat(this
                                        .value / 1000, 1) + "K";
                                    return Highcharts.numberFormat(this.value, 0);
                                }
                            }
                        }, {
                            title: {
                                text: 'Amount (USD)'
                            },
                            opposite: true
                        }],
                        legend: {
                            enabled: false
                        },
                        credits: {
                            enabled: false
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalStock('Middle', this.category, this.series.name);
                                        }
                                    }
                                }
                            }
                        },
                        series: [{
                            name: 'Quantity',
                            data: series_quantity_middle,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    if (this.y > 1000) return Highcharts.numberFormat(this.y /
                                        1000, 1) + "K";
                                    return Highcharts.numberFormat(this.y, 0);
                                }
                            },
                            color: '#00add7'
                        }, {
                            name: 'Amount',
                            yAxis: 1,
                            data: series_amount_middle,
                            dataLabels: {
                                enabled: true,
                                formatter: function() {
                                    if (this.y > 1000) return "$" + Highcharts.numberFormat(this
                                        .y / 1000, 1) + "K";
                                    return "$" + Highcharts.numberFormat(this.y, 0);
                                }
                            },
                            color: '#009551'
                        }]
                    });

                    Highcharts.stockChart('containerWelding2', {
                        rangeSelector: {
                            selected: 0
                        },
                        title: {
                            text: 'Daily Trend Stock'
                        },
                        xAxis: {
                            type: 'datetime',
                            tickInterval: 24 * 3600 * 1000
                        },
                        yAxis: {
                            title: {
                                text: 'Balance'
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalBalance('Welding', $.date(this.category), this.series
                                                .name);
                                        }
                                    }
                                }
                            },
                            column: {
                                stacking: 'normal'
                            }
                        },
                        legend: {
                            enabled: true
                        },
                        credits: {
                            enabled: false
                        },
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true
                        },
                        series: [{
                            name: 'Stock CL',
                            data: series_welding_stock_cl,
                            color: '#db3069',
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock FL',
                            data: series_welding_stock_fl,
                            color: '#1446a0',
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock SX',
                            data: series_welding_stock_sx,
                            color: '#f5d547',
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Amount CL',
                            data: series_welding_amount_cl,
                            color: '#db3069',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount FL',
                            data: series_welding_amount_fl,
                            color: '#1446a0',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount SX',
                            data: series_welding_amount_sx,
                            color: '#f5d547',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount Total',
                            data: series_welding_amount_total,
                            color: 'black',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }],
                    });

                    Highcharts.stockChart('containerMiddle2', {
                        rangeSelector: {
                            selected: 0
                        },
                        title: {
                            text: 'Daily Trend Stock'
                        },
                        xAxis: {
                            type: 'datetime',
                            tickInterval: 24 * 3600 * 1000
                        },
                        yAxis: {
                            title: {
                                text: 'Balance'
                            }
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalBalance('Middle', $.date(this.category), this.series
                                                .name);
                                        }
                                    }
                                }
                            },
                            column: {
                                stacking: 'normal'
                            }
                        },
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true,
                            pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                        },
                        legend: {
                            enabled: true
                        },
                        credits: {
                            enabled: false
                        },
                        series: [{
                            name: 'Stock CL',
                            data: series_middle_stock_cl,
                            color: '#db3069',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock FL',
                            data: series_middle_stock_fl,
                            color: '#1446a0',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock SX',
                            data: series_middle_stock_sx,
                            color: '#f5d547',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            lineWidth: 2,
                            type: 'column',
                            stack: 'stock',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Amount CL',
                            data: series_middle_amount_cl,
                            color: '#db3069',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount FL',
                            data: series_middle_amount_fl,
                            color: '#1446a0',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount SX',
                            data: series_middle_amount_sx,
                            color: '#f5d547',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount Total',
                            data: series_middle_amount_total,
                            color: 'black',
                            marker: {
                                enabled: true,
                                radius: 3
                            },
                            dashStyle: 'dash',
                            lineWidth: 2,
                            type: 'spline',
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }],
                    });

                    Highcharts.chart('containerWelding3', {
                        title: {
                            text: 'End Month Trend Stock',
                        },
                        xAxis: [{
                            categories: categories_welding_mon,
                            crosshair: true
                        }],
                        yAxis: [{
                            labels: {
                                format: '{value} Pcs'
                            },
                            title: {
                                text: 'Stock'
                            }
                        }, {
                            title: {
                                text: 'Amount'
                            },
                            labels: {
                                format: '${value}'
                            },
                            opposite: true
                        }],
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalBalance('Welding', $.date(this.category), this.series
                                                .name);
                                        }
                                    }
                                }
                            },
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Stock CL',
                            type: 'column',
                            color: '#db3069',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_welding_stock_mon_cl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }

                        }, {
                            name: 'Stock FL',
                            type: 'column',
                            color: '#1446a0',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_welding_stock_mon_fl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock SX',
                            type: 'column',
                            color: '#f5d547',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_welding_stock_mon_sx,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Amount CL',
                            type: 'spline',
                            color: '#db3069',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_welding_amount_mon_cl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount FL',
                            type: 'spline',
                            color: '#1446a0',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_welding_amount_mon_fl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount SX',
                            type: 'spline',
                            color: '#f5d547',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_welding_amount_mon_sx,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount Total',
                            type: 'spline',
                            color: 'black',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_welding_amount_mon_total,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }]
                    });

                    Highcharts.chart('containerMiddle3', {
                        title: {
                            text: 'End Month Trend Stock',
                        },
                        xAxis: [{
                            categories: categories_middle_mon,
                            crosshair: true
                        }],
                        yAxis: [{
                            labels: {
                                format: '{value} Pcs'
                            },
                            title: {
                                text: 'Stock'
                            }
                        }, {
                            title: {
                                text: 'Amount'
                            },
                            labels: {
                                format: '${value}'
                            },
                            opposite: true
                        }],
                        tooltip: {
                            enabled: true,
                            split: false,
                            shared: true
                        },
                        credits: {
                            enabled: false
                        },
                        legend: {
                            enabled: true
                        },
                        plotOptions: {
                            series: {
                                borderWidth: 1,
                                borderColor: '#212121',
                                showInNavigator: true,
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalBalance('Welding', $.date(this.category), this.series
                                                .name);
                                        }
                                    }
                                }
                            },
                            column: {
                                stacking: 'normal',
                                dataLabels: {
                                    enabled: true
                                }
                            }
                        },
                        series: [{
                            name: 'Stock CL',
                            type: 'column',
                            color: '#db3069',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_middle_stock_mon_cl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }

                        }, {
                            name: 'Stock FL',
                            type: 'column',
                            color: '#1446a0',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_middle_stock_mon_fl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Stock SX',
                            type: 'column',
                            color: '#f5d547',
                            lineWidth: 2,
                            stack: 'stock',
                            yAxis: 0,
                            data: series_middle_stock_mon_sx,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>{point.y} Pcs</b><br/>',
                            }
                        }, {
                            name: 'Amount CL',
                            type: 'spline',
                            color: '#db3069',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_middle_amount_mon_cl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount FL',
                            type: 'spline',
                            color: '#1446a0',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_middle_amount_mon_fl,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount SX',
                            type: 'spline',
                            color: '#f5d547',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_middle_amount_mon_sx,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }, {
                            name: 'Amount Total',
                            type: 'spline',
                            color: 'black',
                            dashStyle: 'dash',
                            yAxis: 1,
                            lineWidth: 2,
                            data: series_middle_amount_mon_total,
                            tooltip: {
                                pointFormat: '<span style="color:{series.color}">{series.name}</span>: <b>${point.y:.1f}</b><br/>',
                            }
                        }]
                    });

                    var top_welding_categories = [];
                    var top_welding_series = [];
                    var top_middle_categories = [];
                    var top_middle_series = [];

                    $.each(resume_logs, function(key, value) {
                        if (top_welding_categories.length < 20) {
                            if (value.location == 'Welding') {
                                top_welding_categories.push(value.material_description);
                                top_welding_series.push(value.amount);
                            }
                        }
                        if (top_middle_categories.length < 20) {
                            if (value.location == 'Plating') {
                                top_middle_categories.push(value.material_description);
                                top_middle_series.push(value.amount);
                            }
                        }
                    });

                    Highcharts.chart('containerWelding4', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: 'Top 20 Welding',
                        },
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            enabled: false,
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    rotation: -90,
                                    align: 'left'
                                }
                            },
                            series: {
                                minPointLength: 0,
                                // cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             modalDetailMaterial('DIRECT_TOP', this.category);
                                //         }
                                //     }
                                // },
                            },
                            // pareto: {
                            //     dataLabels: {
                            //         enabled: true,
                            //         format: '{point.y:,.0f}%',
                            //     },
                            // },
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: top_welding_categories,
                            labels: {
                                format: "{value}",
                                formatter: function() {
                                    return this.value.substring(0, 10);
                                }
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Amount'
                            },
                        },
                        series: [
                            //     {
                            //     type: 'pareto',
                            //     name: 'Pareto',
                            //     yAxis: 1,
                            //     baseSeries: 1,
                            //     tooltip: {
                            //         valueDecimals: 1,
                            //         valueSuffix: '%'
                            //     },
                            //     color: '#8085e9',
                            // }, 
                            {
                                name: 'Over Amount',
                                type: 'column',
                                data: top_welding_series,
                                color: '#ffab85',
                            },
                        ]
                    });

                    Highcharts.chart('containerMiddle4', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: 'Top 20 Middle',
                        },
                        tooltip: {
                            shared: true
                        },
                        legend: {
                            enabled: false,
                        },
                        plotOptions: {
                            column: {
                                pointPadding: 0.93,
                                groupPadding: 0.93,
                                borderWidth: 0.8,
                                borderColor: '#212121',
                                dataLabels: {
                                    enabled: true,
                                    rotation: -90,
                                    align: 'left'
                                }
                            },
                            series: {
                                minPointLength: 0,
                                // cursor: 'pointer',
                                // point: {
                                //     events: {
                                //         click: function() {
                                //             modalDetailMaterial('DIRECT_TOP', this.category);
                                //         }
                                //     }
                                // },
                            },
                            // pareto: {
                            //     dataLabels: {
                            //         enabled: true,
                            //         format: '{point.y:,.0f}%',
                            //     },
                            // },
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: top_middle_categories,
                            labels: {
                                format: "{value}",
                                formatter: function() {
                                    return this.value.substring(0, 10);
                                },
                            }
                        },
                        yAxis: {
                            title: {
                                text: 'Amount'
                            },
                        },
                        series: [
                            //     {
                            //     type: 'pareto',
                            //     name: 'Pareto',
                            //     yAxis: 1,
                            //     baseSeries: 1,
                            //     tooltip: {
                            //         valueDecimals: 1,
                            //         valueSuffix: '%'
                            //     },
                            //     color: '#8085e9',
                            // }, 
                            {
                                name: 'Over Amount',
                                type: 'column',
                                data: top_middle_series,
                                color: '#ffab85',
                            },
                        ]
                    });

                    $('#loading').hide();
                } else {
                    $('#loading').hide();
                    alert('Gagal memuat data.');
                }
            });
        }

        $.date = function(dateObject) {
            var d = new Date(dateObject);
            var day = d.getDate();
            var month = d.getMonth() + 1;
            var year = d.getFullYear();
            if (day < 10) {
                day = "0" + day;
            }
            if (month < 10) {
                month = "0" + month;
            }
            var date = year + "-" + month + "-" + day;

            return date;
        };

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
