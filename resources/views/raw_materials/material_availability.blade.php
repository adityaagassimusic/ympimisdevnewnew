@extends('layouts.display')
@section('stylesheets')
    <link href="{{ url('css/jquery.gritter.css') }}" rel="stylesheet">
    <style type="text/css">
        thead input {
            width: 100%;
            padding: 3px;
            box-sizing: border-box;
        }

        thead>tr>th {
            padding-right: 3px;
            padding-left: 3px;
        }

        tbody>tr>td {
            padding-right: 3px;
            padding-left: 3px;
        }

        #loading,
        #error {
            display: none;
        }
    </style>
@stop
@section('header')
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
            <div id="period_title" class="col-xs-9" style="background-color: rgba(248,161,63,0.9);">
                <center><span style="color: black; font-size: 2vw; font-weight: bold;" id="title_text"></span></center>
            </div>
            <div class="col-xs-3">
                <div class="input-group date">
                    <div class="input-group-addon" style="background-color: rgba(248,161,63,0.9);">
                        <i class="fa fa-calendar"></i>
                    </div>
                    <input type="text" class="form-control pull-right" id="datepicker" name="datepicker"
                        onchange="fetchData()">
                </div>
            </div>
            <div class="col-xs-6" style="margin-top: 15px;">
                <div id="over_direct" style="height: 30vw;">
                </div>
            </div>
            <div class="col-xs-6" style="margin-top: 15px;">
                <div id="over_indirect" style="height: 30vw;">
                </div>
            </div>
            <div class="col-xs-12">
                <div class="row" id="monitoring">
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="modalDetail" data-keyboard="false">
        <div class="modal-dialog modal-lg" style="width: 95%;">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                    <div style="padding: 10px 10px 10px 10px;">
                        <table id="tableDetail"
                            style="border-color: black; width: 100%; border-collapse: collapse; border: 1px solid black; font-size: 12px;">
                            <thead>
                                <tr>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modalReason" data-keyboard="false">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <div class="modal-body table-responsive no-padding" style="min-height: 100px; padding-bottom: 5px;">
                        <span style="font-weight: bold;">Reason singkat:</span>
                        <input type="hidden" id="idReason">
                        <textarea id="editReason" rows="2" style="width: 100%;"></textarea>
                        <button class="btn btn-success pull-right" onclick="updateReason()">Save</button>
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
    <script src="{{ url('js/bootstrap-toggle.min.js') }}"></script>
    <script src="{{ url('js/highcharts.js') }}"></script>
    <script src="{{ url('js/pareto.js') }}"></script>
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        jQuery(document).ready(function() {
            $('#datepicker').datepicker({
                autoclose: true,
                format: "yyyy-mm-dd",
                todayHighlight: true
            });
            fetchData();
            setInterval(fetchData, 1000 * 60 * 60);
        });

        var material_controls = [];
        var usages = [];
        var stocks = [];
        var charts = [];
        var reasons = [];
        var over_direct_excludes = ['ZQ25360', 'ZQ25380', 'ZQ25390', 'ZQ25460', 'ZQ25470', 'ZQ25130', 'ZQ25140', 'ZQ25150',
            'ZQ18560', 'VCX6250', 'VCX6260', 'VDZ0450', 'VDZ0460', 'ZQ25160', 'ZQ25170', 'ZQ25210', 'ZQ25220',
            'ZQ25250', 'ZQ25260', '2336380', 'ZQ25270', 'ZQ25300', 'ZQ25310', 'ZQ25320', 'ZQ25330', 'ZQ25350',
            'ZQ25400', 'ZQ25410', 'ZQ25420', 'ZQ25430', 'ZQ25440', 'ZQ25450', 'ZQ25480', 'ZQ25490', 'ZQ25500',
            '2503833', 'ZQ25510', 'ZQ64890', 'ZQ64910', 'ZQ64920', 'ZQ64930', 'ZQ64940', 'ZQ66850', 'ZQ66860',
            'ZR52470', 'ZS03190', 'ZS03200', 'ZS03210', 'ZS03230', 'ZS03250', 'ZS03260', 'ZS03270', 'ZS03280',
            'ZS03290', 'ZS03300', 'ZS05680', 'ZS05690', 'ZS36610', 'ZT04940', 'ZT04950', 'ZT04960', 'ZT04980',
            'ZT05000', 'ZT05030', 'ZT05040', 'ZT05050', '0010150', '0010219', '0010220', '0010221', '0010222',
            '0010233', '0010234', '0010235', '2333760', '2333770', '2333840', '2333860', '2333870', '2333900',
            '2334210', '2334450', '2374720', '2503603', '2503604', '2503605', '2503606', '2503607', '2503609',
            '2504037', '2504038', '2504039', '2504040', '2504041', '2504042', '2504043', '2504044', '2504045',
            '2504046', '2504047', '2504048', '2505267', '2505268', '2505269', '2505270', '2505272', '2505274',
            '2505275', '2505277', '2505456', 'VAA2440', 'ZR42310', 'ZS64350', 'ZX67840', 'VFN1580',
        ];

        function modalDetail(cat, ch, name) {
            var type = ch.split('_')[0];

            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            var tableDetail = "";
            $('#tableDetail').html("");
            var cnt = 0;

            tableDetail += '<thead style="background-color: #2a2628; height: 40px;">';
            tableDetail += '<tr style="color: #f39c12;">';
            tableDetail += '<th style="width: 0.1%; border:1px solid black; text-align: center;">#</th>';
            tableDetail += '<th style="width: 1%; border:1px solid black; text-align: left;">Material </th>';
            tableDetail += '<th style="width: 4%; border:1px solid black; text-align: left;">Description </th>';
            tableDetail += '<th style="width: 1%; border:1px solid black; text-align: left;">Buyer</th>';
            tableDetail += '<th style="width: 3%; border:1px solid black; text-align: left;">Vendor</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>MOQ</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Days<br>Policy</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Policy (A)</th>';
            if (type == 'INDIRECT') {
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WH (B)</th>';
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Percentage<br>(B/A)</th> ';
            } else {
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WH (B)</th>';
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WIP (C)</th>';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Quantity Avg<br>Usage Per Day (D)</th> ';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Days<br>Availability<br>((B+C)/D)</th> ';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Percentage<br>((B+C)/A)</th> ';
            }
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">USD<br>Amount Over</th>';
            tableDetail +=
                '<th style="width: 3%; border:1px solid black; text-align: right;">Delivery Plan</th>';
            tableDetail += '</tr>';
            tableDetail += '</thead> ';
            tableDetail += '<tbody id="tableDetailBody">';

            $.each(charts, function(key, value) {
                if (value.controlling_group == ch.split('_')[0] && value.material_category == ch.split('_')[1] &&
                    value.categories_name == cat && value.buyer == name) {
                    cnt += 1;
                    tableDetail += '<tr>';
                    tableDetail += '<td style="width: 0.1%; border:1px solid black; text-align: center;">' +
                        cnt + '</td>';
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' + value
                        .material_number + '</td>';
                    tableDetail += '<td style="width: 4%; border:1px solid black; text-align: left;">' + value
                        .material_description + '</td>';
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' + value
                        .buyer + '</td>';
                    tableDetail += '<td style="width: 3%; border:1px solid black; text-align: left;">' + value
                        .vendor_code + '<br>' + value.vendor_name + '</td>';
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                        value.minimum_order + '</td>';
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                        value.policy_day + '</td>';
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                        value.stock_policy + '</td>';
                    if (type == 'INDIRECT') {
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.stock_wh + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            (value.stock_condition * 100).toFixed(1) + '%</td>';
                    } else {
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.stock_wh + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.stock_wip + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.policy + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.availability_days + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            (value.stock_condition * 100).toFixed(1) + '%</td>';
                    }
                    tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                        value.over_amount + '</td>';
                    tableDetail += '<td style="width: 3%; border:1px solid black; text-align: right;">' +
                        value.delivery + '</td>';
                    tableDetail += '</tr>';
                }
            });

            tableDetail += '</tbody>';

            $('#tableDetail').append(tableDetail);

            table = $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, 100, -1],
                    ['25 rows', '50 rows', '100 rows', 'Show all']
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
                "searching": true,
                "bJQueryUI": true,
                "bAutoWidth": true,
                "processing": true,
                "ordering": true,
                "paging": true,
            });

            $('#modalDetail').modal('show');

        }

        function editReason(a) {
            $('#editReason').val("");
            $('#idReason').val("");
            $.each(reasons, function(key, value) {
                if (value.controlling_group + '_' + value.material_category == a) {
                    $('#editReason').val(value.reason);
                }
            });
            $('#idReason').val(a);
            $('#modalReason').modal('show');
        }

        function updateReason() {
            var text = $('#editReason').val();
            var id = $('#idReason').val();
            var tanggal = $('#datepicker').val();

            var data = {
                text: text,
                id: id,
                tanggal: tanggal
            }
            $.post('{{ url('update/material/material_monitoring/availability_reason') }}', data, function(result, status,
                xhr) {
                if (result.status) {
                    openSuccessGritter('Success!', result.message);
                    $('#modalReason').modal('hide');
                    fetchData();
                } else {
                    openErrorGritter('Error!', result.message);
                    return false;
                }
            });

        }

        function modalDetailMaterial(type, material_number) {
            $('#tableDetail').DataTable().clear();
            $('#tableDetail').DataTable().destroy();
            var tableDetail = "";
            $('#tableDetail').html("");
            var cnt = 0;

            tableDetail += '<thead style="background-color: #2a2628; height: 40px;">';
            tableDetail += '<tr style="color: #f39c12;">';
            tableDetail += '<th style="width: 0.1%; border:1px solid black; text-align: center;">#</th>';
            tableDetail += '<th style="width: 1%; border:1px solid black; text-align: left;">Material </th>';
            tableDetail += '<th style="width: 4%; border:1px solid black; text-align: left;">Description </th>';
            tableDetail += '<th style="width: 1%; border:1px solid black; text-align: left;">Buyer</th>';
            tableDetail += '<th style="width: 3%; border:1px solid black; text-align: left;">Vendor</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>MOQ</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Days<br>Policy</th>';
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Policy (A)</th>';
            if (type == 'INDIRECT' || type == 'INDIRECT_TOP') {
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WH (B)</th>';
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Percentage<br>(B/A)</th> ';
            } else {
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WH (B)</th>';
                tableDetail +=
                    '<th style="width: 1%; border:1px solid black; text-align: right;">Quantity<br>Stock WIP (C)</th>';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Quantity Avg<br>Usage Per Day (D)</th> ';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Days<br>Availability<br>((B+C)/D)</th> ';
                tableDetail +=
                    '<th style = "width: 1%; border:1px solid black; text-align: right;">Percentage<br>((B+C)/A)</th> ';
            }
            tableDetail +=
                '<th style="width: 1%; border:1px solid black; text-align: right;">USD<br>Amount Over</th>';
            tableDetail +=
                '<th style="width: 3%; border:1px solid black; text-align: right;">Delivery Plan</th>';
            tableDetail += '</tr>';
            tableDetail += '</thead> ';
            tableDetail += '<tbody id="tableDetailBody">';

            $.each(charts, function(key, value) {
                if (type == 'INDIRECT' || type == 'DIRECT') {
                    if (value.material_number == material_number) {
                        cnt += 1;
                        tableDetail += '<tr>';
                        tableDetail += '<td style="width: 0.1%; border:1px solid black; text-align: center;">' +
                            cnt + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' + value
                            .material_number + '</td>';
                        tableDetail += '<td style="width: 4%; border:1px solid black; text-align: left;">' + value
                            .material_description + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' + value
                            .buyer + '</td>';
                        tableDetail += '<td style="width: 3%; border:1px solid black; text-align: left;">' + value
                            .vendor_code + '<br>' + value.vendor_name + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.minimum_order + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.policy_day + '</td>';
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.stock_policy + '</td>';
                        if (type == 'INDIRECT') {
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.stock_wh + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                (value.stock_condition * 100).toFixed(1) + '%</td>';
                        } else {
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.stock_wh + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.stock_wip + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.policy + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.availability_days + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                (value.stock_condition * 100).toFixed(1) + '%</td>';
                        }
                        tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                            value.over_amount + '</td>';
                        tableDetail += '<td style="width: 3%; border:1px solid black; text-align: right;">' +
                            value.delivery + '</td>';
                        tableDetail += '</tr>';
                    }
                }
                if (type == 'INDIRECT_TOP' || type == 'DIRECT_TOP') {
                    if (jQuery.inArray(value.material_number, over_direct_excludes) == -1) {
                        if (value.remark == material_number) {
                            cnt += 1;
                            tableDetail += '<tr>';
                            tableDetail += '<td style="width: 0.1%; border:1px solid black; text-align: center;">' +
                                cnt + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' +
                                value
                                .material_number + '</td>';
                            tableDetail += '<td style="width: 4%; border:1px solid black; text-align: left;">' +
                                value
                                .material_description + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: left;">' +
                                value
                                .buyer + '</td>';
                            tableDetail += '<td style="width: 3%; border:1px solid black; text-align: left;">' +
                                value
                                .vendor_code + '<br>' + value.vendor_name + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.minimum_order + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.policy_day + '</td>';
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.stock_policy + '</td>';
                            if (type == 'INDIRECT_TOP') {
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    value.stock_wh + '</td>';
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    (value.stock_condition * 100).toFixed(1) + '%</td>';
                            } else {
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    value.stock_wh + '</td>';
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    value.stock_wip + '</td>';
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    value.policy + '</td>';
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    value.availability_days + '</td>';
                                tableDetail +=
                                    '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                    (value.stock_condition * 100).toFixed(1) + '%</td>';
                            }
                            tableDetail += '<td style="width: 1%; border:1px solid black; text-align: right;">' +
                                value.over_amount + '</td>';
                            tableDetail += '<td style="width: 3%; border:1px solid black; text-align: right;">' +
                                value.delivery + '</td>';
                            tableDetail += '</tr>';
                        }
                    }
                }
            });

            tableDetail += '</tbody>';

            $('#tableDetail').append(tableDetail);

            table = $('#tableDetail').DataTable({
                'dom': 'Bfrtip',
                'responsive': true,
                'lengthMenu': [
                    [25, 50, 100, -1],
                    ['25 rows', '50 rows', '100 rows', 'Show all']
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
                "searching": true,
                "bJQueryUI": true,
                "bAutoWidth": true,
                "processing": true,
                "ordering": true,
                "paging": true,
            });

            $('#modalDetail').modal('show');
        }

        function fetchData() {
            var tanggal = $('#datepicker').val();
            var data = {
                date: tanggal
            }
            $.get('{{ url('fetch/material/material_monitoring_availability') }}', data, function(result, status, xhr) {
                if (result.status) {
                    $('#title_text').text('Availability Stock (' + result.now + ')');
                    var h = $('#period_title').height();
                    $('#datepicker').css('height', h);
                    material_controls = result.material_controls;
                    usages = result.usages;
                    stocks = result.stocks;
                    charts = result.charts;
                    reasons = result.reasons;

                    var div_monitoring = [];

                    $.each(material_controls, function(key, value) {
                        if (jQuery.inArray(value.controlling_group + '_' + value.material_category,
                                div_monitoring) === -1) {
                            div_monitoring.push(value.controlling_group + '_' + value.material_category);
                        }
                    });

                    var div = '';
                    $('#monitoring').html("");
                    charts.sort(function(a, b) {
                        return a['stock_condition'] - b['stock_condition']
                    });

                    var results = [];

                    charts.reduce(function(res, value) {
                        if (!res[value.categories_name + '_' + value.controlling_group + '_' + value
                                .material_category + '_' + value.buyer]) {
                            res[value.categories_name + '_' + value.controlling_group + '_' + value
                                .material_category + '_' + value.buyer] = {
                                categories_name: value.categories_name,
                                controlling_group: value.controlling_group,
                                material_category: value.material_category,
                                buyer: value.buyer,
                                count_item: 0
                            };
                            results.push(res[value.categories_name + '_' + value.controlling_group + '_' +
                                value.material_category + '_' + value.buyer])
                        }
                        res[value.categories_name + '_' + value.controlling_group + '_' + value
                            .material_category + '_' + value.buyer].count_item += 1;
                        return res;
                    }, {});

                    var results2 = [];

                    $.each(result.masters, function(key, value) {
                        var controlling_group = value.controlling_group;
                        var material_category = value.material_category;
                        var buyer = value.buyer;
                        var categories_name = value.categories_name;
                        var count_item = 0;

                        for (var i = 0; i < results.length; i++) {
                            if (results[i].controlling_group == controlling_group && results[i]
                                .material_category == material_category && results[i].buyer == buyer &&
                                results[i].categories_name == categories_name) {
                                count_item = results[i].count_item;
                            }
                        }

                        results2.push({
                            'controlling_group': controlling_group,
                            'material_category': material_category,
                            'buyer': buyer,
                            'categories_name': categories_name,
                            'count_item': count_item
                        });
                    });

                    $.each(div_monitoring, function(key, value) {
                        div = '<div class="col-xs-4" style="height: 30vw; margin-top: 15px;" id="' + value +
                            '">' + value + '</div>';
                        $('#monitoring').append(div);
                        var reason = "";

                        for (var i = 0; i < result.reasons.length; i++) {
                            if (result.reasons[i].controlling_group + '_' + result.reasons[i]
                                .material_category == value) {
                                reason = 'Note: ' + result.reasons[i].reason;
                            }
                        }

                        var categories = [];
                        var names = [];
                        var series = [];
                        var pb_to_unsafe = 0;
                        var pb_from_unsafe = -1;
                        var pb_name_unsafe = '';
                        var pb_to_safe = 0;
                        var pb_from_safe = 0;
                        var pb_name_safe = '';
                        var pb_to_over = 0;
                        var pb_from_over = 0;
                        var pb_name_over = '';
                        var over_cat = 0;
                        var lt_cat = 0;

                        for (var i = 0; i < results2.length; i++) {
                            if (results2[i].controlling_group + '_' + results2[i].material_category ==
                                value) {
                                if (jQuery.inArray(results2[i].categories_name, categories) === -1) {
                                    categories.push(results2[i].categories_name);
                                }
                                if (names.indexOf(results2[i].buyer) !== -1) {
                                    series[names.indexOf(results2[i].buyer)].data.push(
                                        results2[i].count_item
                                    )
                                } else {
                                    names.push(results2[i].buyer)
                                    series.push({
                                        name: results2[i].buyer,
                                        data: [results2[i].count_item]
                                    })
                                }
                            }
                        }

                        Highcharts.chart(value, {
                            chart: {
                                type: 'column'
                            },
                            title: {
                                useHTML: true,
                                text: value.split('_')[0] + ' (' + value.split('_')[1] + ')'
                            },
                            subtitle: {
                                text: reason,
                                align: 'left',
                                y: 32,
                                style: {
                                    color: 'red',
                                    fontSize: '9px'
                                }
                            },
                            xAxis: {
                                categories: categories,
                                plotBands: [{
                                    from: -0.5,
                                    to: 3.5,
                                    color: 'RGB(255,204,255)',
                                    label: {
                                        text: '<em>Not Safe</em>',
                                        style: {
                                            color: 'red'
                                        },
                                        y: -5
                                    },
                                    events: {
                                        click: function() {
                                            editReason(value);
                                        }
                                    }
                                }, {
                                    from: 3.5,
                                    to: 4.5,
                                    color: 'RGB(204,255,255)',
                                    label: {
                                        text: '<em>Safe</em>',
                                        style: {
                                            color: 'green'
                                        },
                                        y: -5
                                    }
                                }, {
                                    from: 4.5,
                                    to: 9.5,
                                    color: '#FFAB85',
                                    label: {
                                        text: '<em>Over</em>',
                                        style: {
                                            color: '#7A2700'
                                        },
                                        y: -5
                                    }
                                }]
                            },
                            credits: {
                                enabled: false
                            },
                            yAxis: {
                                min: 0,
                                title: {
                                    text: null
                                },
                                stackLabels: {
                                    enabled: true
                                },
                                labels: {
                                    enabled: false
                                }
                            },
                            legend: {
                                enabled: true,
                                borderWidth: 1,
                                backgroundColor: Highcharts.defaultOptions.legend.backgroundColor ||
                                    '#FFFFFF',
                                shadow: true,
                                itemStyle: {
                                    font: '8pt Trebuchet MS, Verdana, sans-serif',
                                },
                            },
                            tooltip: {
                                headerFormat: '<b>{point.x}</b><br/>',
                                pointFormat: '{series.name}: {point.y}<br/>Total: {point.stackTotal}'
                            },
                            plotOptions: {
                                column: {
                                    stacking: 'normal',
                                    pointPadding: 0.93,
                                    groupPadding: 0.93,
                                    borderWidth: 0.8,
                                    borderColor: '#212121',
                                    dataLabels: {
                                        enabled: false
                                    }
                                },
                                series: {
                                    minPointLength: 0,
                                    cursor: 'pointer',
                                    point: {
                                        events: {
                                            click: function() {
                                                modalDetail(this.category, value, this.series
                                                    .name);
                                            }
                                        }
                                    },
                                }
                            },
                            series: series
                        });
                    });

                    var overs = [];
                    overs = result.charts;

                    function SortByAmount(a, b) {
                        var aAmount = a.over_amount;
                        var bAmount = b.over_amount;
                        return ((aAmount > bAmount) ? -1 : ((aAmount < bAmount) ? 1 : 0));
                    }
                    overs.sort(SortByAmount);

                    var over_direct_categories = [];
                    var over_direct_series = [];

                    var over_indirect_categories = [];
                    var over_indirect_series = [];

                    $.each(overs, function(key, value) {
                        if (over_direct_categories.length < 20) {
                            if (value.controlling_group == 'DIRECT' && value.stock_condition >= 2) {
                                if (jQuery.inArray(value.material_number, over_direct_excludes) == -1) {
                                    over_direct_categories.push(value.remark);
                                    over_direct_series.push(value.over_amount);
                                }
                            }
                        }
                        if (over_indirect_categories.length < 20) {
                            if (value.controlling_group == 'INDIRECT' && value.stock_condition >= 2) {
                                over_indirect_categories.push(value.remark);
                                over_indirect_series.push(value.over_amount);
                            }
                        }
                    });

                    Highcharts.chart('over_direct', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: 'Top 20 Direct Material Over Stock',
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
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDetailMaterial('DIRECT_TOP', this.category);
                                        }
                                    }
                                },
                            },
                            pareto: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:,.0f}%',
                                },
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: over_direct_categories,
                            labels: {
                                format: "{value}",
                            }
                        },
                        yAxis: [{
                            title: {
                                text: 'Amount'
                            },
                        }, {
                            title: {
                                text: null,
                            },
                            labels: {
                                format: "{value}%",
                            },
                            opposite: true,
                            max: 100,
                            min: 0,
                        }, ],
                        series: [{
                            type: 'pareto',
                            name: 'Pareto',
                            yAxis: 1,
                            baseSeries: 1,
                            tooltip: {
                                valueDecimals: 1,
                                valueSuffix: '%'
                            },
                            color: '#8085e9',
                        }, {
                            name: 'Over Amount',
                            type: 'column',
                            data: over_direct_series,
                            color: '#ffab85',
                        }, ]
                    });

                    Highcharts.chart('over_indirect', {
                        chart: {
                            type: 'column'
                        },
                        title: {
                            useHTML: true,
                            text: 'Top 20 Indirect Material Over Stock',
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
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            modalDetailMaterial('INDIRECT_TOP', this.category);
                                        }
                                    }
                                },
                            },
                            pareto: {
                                dataLabels: {
                                    enabled: true,
                                    format: '{point.y:,.0f}%',
                                },
                            },
                        },
                        credits: {
                            enabled: false
                        },
                        xAxis: {
                            categories: over_indirect_categories,
                            labels: {
                                format: "{value}",
                            }
                        },
                        yAxis: [{
                            title: {
                                text: 'Amount'
                            },
                        }, {
                            title: {
                                text: null,
                            },
                            labels: {
                                format: "{value}%",
                            },
                            opposite: true,
                            max: 100,
                            min: 0,
                        }, ],
                        series: [{
                            type: 'pareto',
                            name: 'Pareto',
                            yAxis: 1,
                            baseSeries: 1,
                            tooltip: {
                                valueDecimals: 1,
                                valueSuffix: '%'
                            },
                            color: '#8085e9',
                        }, {
                            name: 'Over Amount',
                            type: 'column',
                            data: over_indirect_series,
                            color: '#ffab85',
                        }, ]
                    });




                } else {
                    alert('Attempt to retrieve data failed.');
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
@endsection
